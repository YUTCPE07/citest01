<?php

header('Content-Type:text/html; charset=UTF-8');

//========================================//

ini_set("display_errors",1);
error_reporting(1);

//========================================//

include('../../include/common.php');
include('../../lib/function_normal.php');
include('../../include/common_check.php');
require_once('../../include/connect.php');

//========================================//

$oTmp = new TemplateEngine();
$oDB = new DBI();

if ($bDebug) {

	$oErr = new Tracker();
	$oDB->SetTracker($oErr);
}

//========================================//

if ($_SESSION['role_action']['privilege_customer']['view'] != 1) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");
$Act = $_REQUEST['act'];
$path_upload_member = $_SESSION['path_upload_member'];



# SHOW

$where_show = "";

$show_status = $_REQUEST['show_status'];

if ($show_status == "500" || !$show_status) { 

	$where_show = ' LIMIT 500';
} 
elseif ($show_status == "1000") { 

	$where_show = ' LIMIT 1000'; 
} 
elseif ($show_status == "2000") { 

	$where_show = ' LIMIT 1000'; 
}
elseif ($show_status == "All") { 

	$where_show = ''; 
}



# WHERE

$where_view = "";
$where_brand = "";

if($_SESSION['user_type_id_ses']>1){

	$where_view = ' AND mi_branch.brand_id = "'.$_SESSION['user_brand_id'].'"';
	$where_brand = ' AND mi_brand.brand_id = "'.$_SESSION['user_brand_id'].'"';
}

if($_SESSION['user_branch_id']){

	$where_view .= ' AND mi_branch.branch_id = "'.$_SESSION['user_branch_id'].'"';
	$where_brand = ' AND mi_branch.branch_id = "'.$_SESSION['user_branch_id'].'"';
}

$data_table = "";
$n = "1";


$sql_transaction = 'SELECT
						mi_brand.name AS brand_name,
						mi_brand.logo_image,
						mi_brand.path_logo,
						mi_brand.otp_pc,
						mi_brand.brand_id,
						mi_card.card_id,
						mi_card.name AS card_name,
						mi_card.image AS card_image,
						mi_card.path_image,
						mb_member.firstname,
						mb_member.lastname,
						mb_member.facebook_name,
						mb_member.email AS member_email,
						mb_member.member_image AS member_image,
						mb_member.member_id AS member_id,
						mb_member.facebook_id AS facebook_id,
						mi_branch.name AS branch_name,
						member_transaction_h.meth_MemberTransactionHID AS id_use,
						member_transaction_h.meth_MemberTransactionID AS code_use,
						member_transaction_h.meth_UpdatedDate,
						member_transaction_h.meth_CreatedDate AS create_date,
						member_transaction_h.meth_Deleted AS status_del,
						member_transaction_h.meth_Reason

						FROM member_transaction_h

						LEFT JOIN mb_member
						ON  member_transaction_h.memb_MemberID = mb_member.member_id

						LEFT JOIN mi_branch
						ON member_transaction_h.brnc_BranchID = mi_branch.branch_id

						LEFT JOIN mi_brand
						ON mi_branch.brand_id = mi_brand.brand_id

						LEFT JOIN mi_card
						ON member_transaction_h.card_CardID = mi_card.card_id

						WHERE member_transaction_h.meth_Platform = "Use"
						AND member_transaction_h.meth_UpdatedDate >= "'.$six_month.'"
						'.$where_brand.'

					ORDER BY create_date DESC
					'.$where_show;

$rs_trans = $oDB->Query($sql_transaction);

if (!$rs_trans) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_trans->FetchRow(DBI_ASSOC)) {
		
		# COUNT USE

		$sql_priv = 'SELECT COUNT(mepe_MemberPrivlegeID)
						FROM member_privilege_trans
						WHERE meth_MemberTransactionHID="'.$axRow['id_use'].'"';
		$count_priv = $oDB->QueryOne($sql_priv);

		$sql_coup = 'SELECT COUNT(meco_MemberCouponID)
						FROM member_coupon_trans
						WHERE meth_MemberTransactionHID="'.$axRow['id_use'].'"';
		$count_coup = $oDB->QueryOne($sql_coup);

		$sql_acti = 'SELECT COUNT(meac_MemberActivityID)
						FROM member_activity_trans
						WHERE meth_MemberTransactionHID="'.$axRow['id_use'].'"';
		$count_acti = $oDB->QueryOne($sql_acti);

		$count_use = $count_priv+$count_coup+$count_acti;


		# MEMBER BRAND ID

		$sql_brand = 'SELECT member_brand_code
						FROM mb_member_register
						WHERE bran_BrandID="'.$axRow['brand_id'].'"
						AND member_id="'.$axRow['member_id'].'"';
		$brand_code = $oDB->QueryOne($sql_brand);


		# MEMBER CARD ID

		$sql_card = 'SELECT member_card_code
						FROM mb_member_register
						WHERE card_id="'.$axRow['card_id'].'"
						AND member_id="'.$axRow['member_id'].'"';
		$card_code = $oDB->QueryOne($sql_card);


		# MEMBER

		$member_name = '';

		if ($axRow['firstname'].' '.$axRow['lastname']) {

			if ($axRow['member_email']) {

				if ($axRow['member_mobile']) {

					if ($card_code) {

						if ($brand_code) {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>'.$axRow['member_mobile'].'<br>Member Card : '.$card_code.'<br>Member Brand : '.$brand_code;

						} else {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>'.$axRow['member_mobile'].'<br>Member Card : '.$card_code;
						}

					} else {

						if ($brand_code) {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>'.$axRow['member_mobile'].'<br>Member Brand : '.$brand_code;
						} else {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>'.$axRow['member_mobile'];
						}
					}

				} else {

					if ($card_code) {

						if ($brand_code) {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>Member Card : '.$card_code.'<br>Member Brand : '.$brand_code;

						} else {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>Member Card : '.$card_code;
						}

					} else {

						if ($brand_code) {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>Member Brand : '.$brand_code;
						} else {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'];
						}
					}
				}

			} else {

				if ($axRow['member_mobile']) {

					if ($card_code) {

						if ($brand_code) {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_mobile'].'<br>Member Card : '.$card_code.'<br>Member Brand : '.$brand_code;

						} else {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_mobile'].'<br>Member Card : '.$card_code;
						}

					} else {

						if ($brand_code) {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_mobile'].'<br>Member Brand : '.$brand_code;
						} else {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_mobile'];
						}
					}
				
				} else { 

					if ($card_code) {

						if ($brand_code) {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>Member Card : '.$card_code.'<br>Member Brand : '.$brand_code;

						} else {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>Member Card : '.$card_code;
						}

					} else {

						if ($brand_code) {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>Member Brand : '.$brand_code;
						} else {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'];
						}
					}
				}
			}

		} else {

			if ($axRow['member_email']) {

				if ($axRow['member_mobile']) { $member_name = $axRow['member_email'].'<br>'.$axRow['member_mobile']; } 
				
				else { $member_name = $axRow['member_email']; }

			} else {

				if ($axRow['member_mobile']) { $member_name = $axRow['member_mobile']; } 
				
				else { $member_name = ''; }
			}
		}


		
		if($axRow['member_image']!='' && $axRow['member_image']!='user.png'){

			$member_image = '<img src="'.$path_upload_member.$axRow['member_image'].'" height="100" width="100" class="img-circle image_border"/>';

			$axRow['member_image'] = '<img src="'.$path_upload_member.$axRow['member_image'].'" width="50" height="50" class="img-circle image_border"/>';	

		} else if ($axRow['facebook_id']!='') {

			$member_image = '<img src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=large" width="100" height="100" class="img-circle image_border"/>';

			$axRow['member_image'] = '<img src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=large" width="50" height="50" class="img-circle image_border"/>';

		} else {

			$member_image = '<img src="../../images/user.png" height="100" width="100" class="img-circle image_border"/>';

			$axRow['member_image'] = '<img src="../../images/user.png" width="50" height="50" class="img-circle image_border"/>';
		}


		# CARD

		if($axRow['card_image']!='') {

			$card_image = '<img src="../../upload/'.$axRow['path_image'].$axRow['card_image'].'" height="100" class="img-rounded image_border"/>';

			$axRow['card_image'] = '<img src="../../upload/'.$axRow['path_image'].$axRow['card_image'].'" height="50" class="img-rounded image_border"/>';

		} else {

			$card_image = '<img src="../../images/card_privilege.jpg" height="100" class="img-rounded image_border"/>';	

			$axRow['card_image'] = '<img src="../../images/card_privilege.jpg" height="50" class="img-rounded image_border"/>';	
		}


		# LOGO

		if($axRow['logo_image']!=''){

			$axRow['logo_image'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="50" height="50"/>';

		} else {

			$axRow['logo_image'] = '<img src="../../images/400x400.png" class="image_border" width="50" height="50"/>';
		}


		# OTP STATUS

		$otp_status = '';

		if ($axRow['mepe_OTP']=="") {

			if ($axRow['otp_pc']=="T") {

				$otp_status = '<span class="glyphicon glyphicon-ok-circle" style="color:#5cb85c;font-size:20px"></span>';

			} else {

				$otp_status = '<span class="glyphicon glyphicon-minus" style="color:#BBBBBB;font-size:20px"></span>';
			}
			
		} else {

			$otp_status = '<a href="privilege_otp.php?member_id='.$axRow['member_id'].'&use_id='.$axRow['mepe_MemberPrivlegeID'].'&type=p"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-phone" aria-hidden="true"></span></button></a>';
		}


		# STASUS

		if ($axRow['status_del']=='T') { 

			$status_del = 'Deleted<hr>'.$axRow['meth_Reason'];
			$button_del = 'glyphicon glyphicon-eye-open';

		} else { 

			$status_del = 'Active';
			$button_del = 'glyphicon glyphicon-trash';
		}


		# VIEW

		$sql_view = 'SELECT
						COUNT(member_privilege_trans.mepe_MemberPrivlegeID) AS count_use,
						member_privilege_trans.mepe_Status AS status,
						member_privilege_trans.mepe_Deleted AS status_del,
						member_privilege_trans.mepe_CreatedDate AS create_date,
						privilege.priv_Name AS name,
						privilege.priv_Image AS image,
						privilege.priv_ImagePath AS path,
						privilege.priv_PrivilegeID AS privilege_id,
						"Privilege" AS type

						FROM member_privilege_trans

						LEFT JOIN privilege
						ON member_privilege_trans.priv_PrivilegeID = privilege.priv_PrivilegeID

						LEFT JOIN mi_branch
						ON member_privilege_trans.brnc_BranchID = mi_branch.branch_id

						WHERE member_privilege_trans.meth_MemberTransactionHID = "'.$axRow['id_use'].'"
						'.$where_view.'
						GROUP BY member_privilege_trans.priv_PrivilegeID

					UNION

					SELECT
						COUNT(member_coupon_trans.meco_MemberCouponID) AS count_use,
						member_coupon_trans.meco_Status AS status,
						member_coupon_trans.meco_Deleted AS status_del,
						member_coupon_trans.meco_CreatedDate AS create_date,
						coupon.coup_Name AS name,
						coupon.coup_Image AS image,
						coupon.coup_ImagePath AS path,
						coupon.coup_CouponID AS privilege_id,
						"Coupon" AS type

						FROM member_coupon_trans

						LEFT JOIN coupon
						ON member_coupon_trans.coup_CouponID = coupon.coup_CouponID

						LEFT JOIN mi_branch
						ON member_coupon_trans.brnc_BranchID = mi_branch.branch_id

						WHERE member_coupon_trans.meth_MemberTransactionHID = "'.$axRow['id_use'].'"
						AND coupon.coup_Birthday = ""
						'.$where_view.'
						GROUP BY member_coupon_trans.coup_CouponID

					UNION

					SELECT
						COUNT(member_coupon_trans.meco_MemberCouponID) AS count_use,
						member_coupon_trans.meco_Status AS status,
						member_coupon_trans.meco_Deleted AS status_del,
						member_coupon_trans.meco_CreatedDate AS create_date,
						coupon.coup_Name AS name,
						coupon.coup_Image AS image,
						coupon.coup_ImagePath AS path,
						coupon.coup_CouponID AS privilege_id,
						"Brithday Coupon" AS type

						FROM member_coupon_trans

						LEFT JOIN coupon
						ON member_coupon_trans.coup_CouponID = coupon.coup_couponID

						LEFT JOIN mi_branch
						ON member_coupon_trans.brnc_BranchID = mi_branch.branch_id

						WHERE member_coupon_trans.meth_MemberTransactionHID = "'.$axRow['id_use'].'"
						AND coupon.coup_Birthday = "T"
						'.$where_view.'
						GROUP BY member_coupon_trans.coup_CouponID

					UNION

					SELECT
						COUNT(member_activity_trans.meac_MemberActivityID) AS count_use,
						member_activity_trans.meac_Status AS status,
						member_activity_trans.meac_Deleted AS status_del,
						member_activity_trans.meac_CreatedDate AS create_date,
						activity.acti_Name AS privilege_name,
						activity.acti_Image AS privilege_image,
						activity.acti_ImagePath AS privilege_path,
						activity.acti_ActivityID AS privilege_id,
						"Activity" AS type

						FROM member_activity_trans

						LEFT JOIN activity
						ON member_activity_trans.acti_ActivityID = activity.acti_ActivityID

						LEFT JOIN mi_branch
						ON member_activity_trans.brnc_BranchID = mi_branch.branch_id

						WHERE member_activity_trans.meth_MemberTransactionHID = "'.$axRow['id_use'].'"
						'.$where_view.'
						GROUP BY member_activity_trans.acti_ActivityID';

		$data_view = "";
		$rs_view = $oDB->Query($sql_view);

		$status_motivation = 'T';

		while($view = $rs_view->FetchRow(DBI_ASSOC)) {


			# PRIVILEGE IMAGE

			if($view['image']!=''){

				$privilege_image = '<img src="../../upload/'.$view['path'].$view['image'].'" class="image_border" height="50"/>';

			} else {

				$privilege_image = '<img src="../../images/card_privilege.jpg" height="50"/>';
			}


			# DELETED

				# POINT

				$sql_point = 'SELECT memp_LastQty
								FROM member_motivation_point_trans 
								WHERE mepe_MemberPrivlegeID="'.$check['code_use'].'"';
				$point = $oDB->QueryOne($sql_point);

				if ($point != 0) { $status_motivation = 'F'; }

				# STAMP

				$sql_stamp = 'SELECT memps_LastQty
								FROM member_motivation_stamp_trans 
								WHERE mepe_MemberPrivlegeID="'.$check['code_use'].'"';
				$stamp = $oDB->QueryOne($sql_stamp);

				if ($stamp != 0) { $status_motivation = 'F'; }


			# DETAIL

			$privilege_data = '<table style="width:100%">
									<tr>
										<td style="text-align:right;width:80px">
											Name<br>
											Type';

			if ($axRow['status_del']=='T') {

				$privilege_data .= '<br>Delete Date';
			}
			
			$privilege_data .= '		</td>
										<td style="text-align:center;width:20px">
											:<br>
											:';

			if ($axRow['status_del']=='T') {

				$privilege_data .= '<br>:';
			}
			
			$privilege_data .= '		</td>
										<td>
											'.$view['name'].'<br>
											'.$view['type'].'';

			if ($axRow['status_del']=='T') {

				$privilege_data .= '<br>'.DateTime($axRow['meth_UpdatedDate']);
			}
			
			$privilege_data .= '		</td>
									</tr>
								</table>';

			$data_view .= '<tr>
								<td style="text-align:center">'.$privilege_image.'</td>
								<td>'.$privilege_data.'</td>
								<td style="text-align:center">'.$view['count_use'].'</td>
							</tr>';
		}
				
		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$n.'"><span class="'.$button_del.'" aria-hidden="true"></span></button>

				<div class="modal fade" id="View'.$n.'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px;">
						        	'.$axRow['code_use'].'
						        	<div style="float:right">'.$axRow['brand_name'].'</div>
						        </span>
						        <hr>
							    <table width="80%" class="myPopup" align="center">
							        <tr><td width="150px" style="text-align:center">'.$member_image.'</td>
							        	<td width="200px">'.$card_image.'</td>
							        	<td width="200px" align="center">'.$member_name.'
							        	</td>
							        </tr>
							    </table>
							    <hr>
								<table width="100%" class="table table-striped table-bordered myPopup">
									<thead>
										<tr class="th_table">
											<th style="width:20%;text-align:ceter"><b>Privilege</b></th>
											<th style="text-align:ceter"><b>Detail</b></th>
											<th style="width:20%;text-align:ceter"><b>No. of Use</b></th>
										</tr>
									</thead>
									<tbody>
										'.$data_view.'
									</tbody>
								</table>
							</div>
							<div class="modal-footer">';

		if ($_SESSION['role_action']['privilege_customer']['delete'] == 1 && $axRow['status_del']=='' && $status_motivation=='T') {		    
			
			$view .= '			<a href="customer_delete.php?act=edit&id='.$axRow['id_use'].'">
								<button type="button" class="btn btn-default btn-sm">Delete</button></a>';
		}
		
		$view .= '      		<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
							</div>
						</div>
					</div>
				</div>';


		# TABLE

		$data_table .= '<tr>
							<td>'.number_format($n++).'</td>
							<td style="text-align:center">'.$axRow['code_use'].'</td>
							<td style="text-align:center">'.$axRow['member_image'].'</td>
							<td>'.$member_name.'</td>
							<td style="text-align:center">'.$count_use.'
								<hr>
								'.$view.'</td>
							<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['logo_image'].'</a><br>
								<span style="font-size:11px;">'.$axRow['brand_name'].'</span></td>
							<td style="text-align:center"><a href="../card/card.php">'.$axRow['card_image'].'</a><br>
								<span style="font-size:11px;">'.$axRow['card_name'].'</span></td>
							<td>'.$axRow['branch_name'].'</td>
							<td style="text-align:center">'.$otp_status.'</td>
							<td style="text-align:center">'.DateTime($axRow['create_date']).'</td>
							<td style="text-align:center">'.$status_del.'</td>
						</tr>' ;
	}
}



#  select_show dropdownlist

$select_show = '';

$select_show .= '<option value="500"';

	if ($show_status == "500" || !$show_status) {	$select_show .= ' selected';	}

$select_show .= '>500</option>';

$select_show .=	'<option value="1000"';

	if ($show_status == "1000") {	$select_show .= ' selected';	}

$select_show .= '>1,000</option>';

$select_show .=	'<option value="2000"';

	if ($show_status == "2000") {	$select_show .= ' selected';	}

$select_show .= '>2,000</option>';

$select_show .=	'<option value="All"';

	if ($show_status == "All") {	$select_show .= ' selected';	}

$select_show .= '>All</option>';

$oTmp->assign('select_show', $select_show);



$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_transaction');

$oTmp->assign('content_file','transaction/customer.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
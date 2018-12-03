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

if ($_SESSION['role_action']['register_trans']['view'] != 1) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$path_upload_member = $_SESSION['path_upload_member'];
$data_register = "";
$regis_n = "1";
$time_insert = date("Y-m-d H:i:s");
$Act = $_REQUEST['act'];
$id = $_REQUEST['id'];


if($_SESSION['user_type_id_ses']>1){

	if ($_SESSION['user_type_id_ses']==3) {

		$where_brand = ' AND a.brnc_BranchID = "'.$_SESSION['user_branch_id'].'" AND a.flag_del=""';

	} else {

		$where_brand = ' AND b.brand_id = "'.$_SESSION['user_brand_id'].'" AND a.flag_del=""';
	}
}


# SEARCH

$brand_id = "";

$where_search = "";

for($k=0 ; $k<count($_POST["brand_id"]) ; $k++){
	
	if(trim($_POST["brand_id"][$k]) != ""){

		if ($_POST["brand_id"][$k]==0) {
			
			$brand_id = 0;

		} else {
		
			if ($k==count($_POST["brand_id"])-1) {	$brand_id .= $_POST["brand_id"][$k];	} 

			else {	$brand_id .= $_POST["brand_id"][$k].",";	}
		}
	}
}

if ($brand_id=="" || $brand_id==0) {	$where_search = "";	} 
else {	$where_search = " AND c.brand_id IN (".$brand_id.")";	}



$sql_register ='SELECT DISTINCT
				a.member_register_id,
				a.member_card_code,
				a.member_brand_code,
				a.card_id,
				a.date_start,
				a.date_create,
				b.name AS card_name,
				b.path_image,
				b.image AS card_image,
				b.image_newupload AS card_image_new,
				d.member_image,
				b.member_fee,
				d.firstname,
				d.lastname,
				d.email AS member_email,
				d.date_birth,
				d.facebook_id,
				d.facebook_name,
				d.mobile AS member_mobile,
				d.member_id,
				c.logo_image,
				c.path_logo,
				c.name AS brand_name,
	  			t.name AS user_type,
	  			e.name AS branch_name,
	  			(SELECT COUNT(member_privilege_trans.mepe_MemberPrivlegeID)
					FROM member_privilege_trans
					WHERE member_privilege_trans.memb_MemberID=a.member_id
					AND member_privilege_trans.card_CardID=a.card_id) AS count_priv,
				(SELECT COUNT(member_coupon_trans.meco_MemberCouponID)
					FROM member_coupon_trans
					WHERE member_coupon_trans.memb_MemberID=a.member_id
					AND member_coupon_trans.card_CardID=a.card_id) AS count_coup,
				(SELECT COUNT(member_activity_trans.meac_MemberActivityID)
					FROM member_activity_trans
					WHERE member_activity_trans.memb_MemberID=a.member_id
					AND member_activity_trans.card_CardID=a.card_id) AS count_acti

				FROM mb_member_register AS a

				LEFT JOIN mi_card AS b
				ON a.card_id = b.card_id

				LEFT JOIN mb_member AS d
				ON a.member_id = d.member_id

				LEFT JOIN mi_brand AS c
				ON b.brand_id = c.brand_id

				LEFT JOIN mi_user AS u
				ON u.user_id = a.payr_CreatedBy 

				LEFT JOIN mi_user_type AS t
				ON u.user_type_id = t.user_type_id 

				LEFT JOIN mi_branch AS e
				ON e.branch_id = a.brnc_BranchID 

				WHERE a.platform = "Insert"

				'.$where_brand.'
				'.$where_search.'

				GROUP BY a.member_register_id
				ORDER BY a.date_create DESC';



if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT flag_del FROM mb_member_register WHERE member_register_id ="'.$id.'"';

	$oRes = $oDB->Query($sql);
	$axRow = $oRes->FetchRow(DBI_ASSOC);
		
	if($axRow['flag_del']=="T") {
 				
 		$do_sql_user = "UPDATE mb_member_register 
 							SET flag_del='', 
 							payr_UpdatedDate='".$time_insert."' 
 							WHERE member_register_id='".$id."'";
 	}

 	$oDB->QueryOne($do_sql_user);
 			
 	echo '<script>window.location.href="register.php";</script>';
} 



$rs_regis = $oDB->Query($sql_register);

if (!$rs_regis) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_regis->FetchRow(DBI_ASSOC)) {

		# MEMBER

		$member_name = '';

		if ($axRow['firstname'].' '.$axRow['lastname']) {

			if ($axRow['member_email']) {

				if ($axRow['member_mobile']) {

					if ($axRow['member_card_code']) {

						if ($axRow['member_brand_code']) {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>'.$axRow['member_mobile'].'<br>Member Card : '.$axRow['member_card_code'].'<br>Member Brand : '.$axRow['member_brand_code'];

						} else {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>'.$axRow['member_mobile'].'<br>Member Card : '.$axRow['member_card_code'];
						}

					} else {

						if ($axRow['member_brand_code']) {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>'.$axRow['member_mobile'].'<br>Member Brand : '.$axRow['member_brand_code'];
						} else {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>'.$axRow['member_mobile'];
						}
					}

				} else {

					if ($axRow['member_card_code']) {

						if ($axRow['member_brand_code']) {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>Member Card : '.$axRow['member_card_code'].'<br>Member Brand : '.$axRow['member_brand_code'];

						} else {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>Member Card : '.$axRow['member_card_code'];
						}

					} else {

						if ($axRow['member_brand_code']) {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>Member Brand : '.$axRow['member_brand_code'];
						} else {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'];
						}
					}
				}

			} else {

				if ($axRow['member_mobile']) {

					if ($axRow['member_card_code']) {

						if ($axRow['member_brand_code']) {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_mobile'].'<br>Member Card : '.$axRow['member_card_code'].'<br>Member Brand : '.$axRow['member_brand_code'];

						} else {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_mobile'].'<br>Member Card : '.$axRow['member_card_code'];
						}

					} else {

						if ($axRow['member_brand_code']) {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_mobile'].'<br>Member Brand : '.$axRow['member_brand_code'];
						} else {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_mobile'];
						}
					}
				
				} else { 

					if ($axRow['member_card_code']) {

						if ($axRow['member_brand_code']) {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>Member Card : '.$axRow['member_card_code'].'<br>Member Brand : '.$axRow['member_brand_code'];

						} else {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>Member Card : '.$axRow['member_card_code'];
						}

					} else {

						if ($axRow['member_brand_code']) {
						
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>Member Brand : '.$axRow['member_brand_code'];
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

		if($axRow['member_image']!='' && $axRow['member_image']!='https://www.memberin.com/images/user.png'){

			$member_image = '<img src="'.$path_upload_member.$axRow['member_image'].'" width="100" height="100" class="img-circle image_border"/>';	

			$axRow['member_image'] = '<img src="'.$path_upload_member.$axRow['member_image'].'" width="50" height="50" class="img-circle image_border"/>';	

		} else if ($axRow['facebook_id']!='') {

			$axRow['member_image'] = '<img src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=square" width="50" height="50" class="img-circle image_border"/>';

			$member_image = '<img src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=square" width="100" height="100" class="img-circle image_border"/>';

		} else {

			$axRow['member_image'] = '<img src="../../images/user.png" width="50" height="50" class="img-circle image_border"/>';

			$member_image = '<img src="../../images/user.png" width="100" height="100" class="img-circle image_border"/>';
		}


		# CARD IMAGE

		if($axRow['card_image']!=''){

			$card_image = '<img src="../../upload/'.$axRow['path_image'].$axRow['card_image'].'" class="img-rounded image_border" height="100px">';

			$axRow['card_image'] = '<img src="../../upload/'.$axRow['path_image'].$axRow['card_image'].'" class="img-rounded image_border" height="50px">';

		} else if ($axRow['image_newupload']!='') {

			$card_image = '<img src="../../upload/'.$axRow['path_image'].$axRow['image_newupload'].'" class="img-rounded image_border" height="100px">';

			$axRow['card_image'] = '<img src="../../upload/'.$axRow['path_image'].$axRow['image_newupload'].'" class="img-rounded image_border" height="50px">';

		} else {

			$card_image = '<img src="../../images/card_privilege.jpg" class="img-rounded image_border" height="100px">';

			$axRow['card_image'] = '<img src="../../images/card_privilege.jpg" class="img-rounded image_border" height="50px">';
		}


		# LOGO

		if($axRow['logo_image']!=''){

			$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="50" height="50"/>';

		} else {

			$logo_brand = '<img src="../../images/400x400.png" class="image_border" width="50" height="50"/>';
		}


		# BRANCH

		if ($axRow['branch_name']=='') { $axRow['branch_name'] = '-'; }


		# OTP STATUS

		$otp_status = '';

		if ($axRow['otp_status']=="success") { 

			$otp_status = '<span class="glyphicon glyphicon-ok-sign" style="color:#5cb85c;font-size:20px"></span>'; 
		
		} else {

			$otp_status = '<a href="register_otp.php?id='.$axRow['member_id'].'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-phone" aria-hidden="true"></span></button></a>';
		}


		# STATUS DELETE

		$status_del = 0;

		$count_priv = $axRow['count_priv'];
		if ($count_priv!=0) { $status_del = $count_priv; }

		$count_coup = $axRow['count_coup'];
		if ($count_coup!=0) { $status_del = $count_coup; }

		$count_acti = $axRow['count_acti'];
		if ($count_acti!=0) { $status_del = $count_acti; }


		// # DELETED

		// $status = '';

		// if ($status_del == 0) {

		// 	if($axRow['flag_del']=="") {

		// 		if ($axRow['del_reason']) { $reason = nl2br($axRow['del_reason']).'<hr>'; }
		// 		else { $reason = 'Active<hr>'; }

		// 		$deleted = $reason.'<a href="register_delete.php?act=edit&id='.$axRow['member_register_id'].'">
		// 				        <button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button></a>';
			
		// 	} else if ($axRow['flag_del']=='T') {

		// 		if ($axRow['del_reason']) { $reason = nl2br($axRow['del_reason']).'<hr>'; }
		// 		else { $reason = 'Delete<hr>'; }

		// 		$deleted = $reason.'<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['member_register_id'].'"><span class="glyphicon glyphicon-repeat" aria-hidden="true"></span></button>

		// 			<div class="modal fade" id="Deleted'.$axRow['member_register_id'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
		// 				<div class="modal-dialog" role="document">
		// 					<div class="modal-content">
		// 					    <div class="modal-body" align="left">
		// 					        <span style="font-size:16px"><b>Please confirm your choice</b></span>
		// 					        <hr>
		// 					        <center>
		// 					        <table width="80%" class="myPopup">
		// 					        	<tr><td width="120px" style="text-align:center">'.$member_image.'</td>
		// 					        		<td width="170px">'.$card_image.'</td>
		// 					        		<td>'.$member_name.'</td></tr>
		// 					        	<tr><td colspan="3"><br>
		// 							        <p style="font-size:12px;padding-left:130px;">
		// 							            By clicking the <b>"Active"</b> button to:<br>
		// 							            &nbsp; &nbsp;- Active this member card
		// 							        </p></td>
		// 					        	</tr>
		// 					        </table>
		// 					        </center>
		// 					    </div>
		// 					    <div class="modal-footer">
		// 					    	<a href="register.php?act=delete&id='.$axRow['member_register_id'].'">
		// 					        <button type="button" class="btn btn-default btn-sm">Active</button></a>
		// 					        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
		// 					    </div>
		// 					</div>
		// 				</div>
		// 			</div>';
		// 	}

		// } else {

		// 	if ($axRow['del_reason']) { $reason = nl2br($axRow['del_reason']).'<hr>'; }
		// 	else { $reason = ''; }
					
		// 	$deleted = $reason.'-';
		// }



		// # VIEW	

		// $edit_status = 'F';

		// $sql_member = 'SELECT * FROM mb_member WHERE member_id="'.$axRow['member_id'].'"';

		// $oRes = $oDB->Query($sql_member);
		// $member = $oRes->FetchRow(DBI_ASSOC);

		// $view_table = '<table style="width:90%" class="table table-striped table-bordered myPopup">';

		// $topic = array("Profile", "Home Address", "Work Address", "Work", "Contact");

		// for ($i=0; $i <5 ; $i++) { 

		// 	$sql_field = 'SELECT a.*,b.*,c.*,

		// 					a.mafi_MasterFieldID AS master_field_id,
		// 					b.refo_Target,
		// 					d.fity_Name AS field_type

		// 					FROM master_field AS a

		// 					LEFT JOIN register_form AS b
		// 					ON b.mafi_MasterFieldID = a.mafi_MasterFieldID

		// 					LEFT JOIN mi_card AS c
		// 					ON b.card_CardID = c.card_id

		// 					LEFT JOIN field_type AS d
		// 					ON a.mafi_FieldType = d.fity_FieldTypeID

		// 					WHERE a.mafi_Position = "'.$topic[$i].'"
		// 					AND a.mafi_Deleted != "T"
		// 					AND c.card_id = "'.$axRow['card_id'].'"
		// 					AND b.refo_FillIn = "Y"

		// 					GROUP BY a.mafi_FieldName
		// 					ORDER BY a.mafi_FieldOrder';

		// 	$oRes = $oDB->Query($sql_field);
		// 	$check_field = $oDB->QueryOne($sql_field);

		// 	if ($check_field) {

		// 		$view_table .= '<tr class="th_table"><td colspan="3" style="text-align:center;background-color:#003369"><b>'.$topic[$i].'</b></td></tr>';

		// 		while ($field = $oRes->FetchRow(DBI_ASSOC)){

		// 			$view_table .= '<tr>
		// 								<td style="text-align:right"><b>'.$field['mafi_NameEn'].'</b></td>
		// 								<td width="10%" style="text-align:center"> : </td>';

		// 			if ($field['field_type']=='Text') {

		// 				# MEMBER BRAND CODE & MEMBER CARD COE

		// 				if ($field['master_field_id']=='48') { # CARD

		// 					$member[$field['mafi_FieldName']] = $axRow['member_card_code'];
							
		// 				} elseif ($field['master_field_id']=='49') { # BRAND

		// 					$member[$field['mafi_FieldName']] = $axRow['member_brand_code'];
		// 				}

		// 				if ($member[$field['mafi_FieldName']]=="") { 

		// 					$member[$field['mafi_FieldName']] = "-";
		// 					$edit_status = "T"; 
		// 				}

		// 				$view_table .= '<td>'.$member[$field['mafi_FieldName']];
						
		// 			} else if ($field['field_type']=='Number') {

		// 				if ($member[$field['mafi_FieldName']]=="0") { 

		// 					$member[$field['mafi_FieldName']] = "-";
		// 					$edit_status = "T"; 
		// 				}

		// 				$view_table .= '<td>'.$member[$field['mafi_FieldName']];
						
		// 			} else if ($field['field_type']=='Date') {

		// 				if ($member[$field['mafi_FieldName']] != '0000-00-00') { 

		// 					$view_table .= '<td>'.DateOnly($member[$field['mafi_FieldName']]); 

		// 				} else { 

		// 					$view_table .= '<td>-';
		// 					$edit_status = "T"; 
		// 				}

		// 			} else if ($field['field_type']=='Radio') {

		// 				$x = 0;

		// 				$data = $member[$field['mafi_FieldName']];

		// 				$view_table .= '<td><span class="form-inline">';

		// 				$sql_target = 'SELECT *
		// 								FROM master_target
		// 								WHERE mafi_MasterFieldID = "'.$field['master_field_id'].'"';
		// 				$oRes_target = $oDB->Query($sql_target);
		// 				while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

		// 					if ($data != 0) {

		// 						if ($data == $target['mata_MasterTargetID']) {

		// 							if ($x==0) {

		// 								$view_table .= '<span class="glyphicon glyphicon-check"></span> '.$target['mata_NameEn'].'<label>';

		// 							} else {

		// 								$view_table .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-check"></span> '.$target['mata_NameEn'].'<label>';
		// 							}

		// 						} else {

		// 							if ($x==0) {

		// 								$view_table .= '<span class="glyphicon glyphicon-unchecked"></span> '.$target['mata_NameEn'].'<label>';

		// 							} else {

		// 								$view_table .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-unchecked"></span> '.$target['mata_NameEn'].'<label>';
		// 							}
		// 						}

		// 					} else {

		// 						$edit_status = "T";

		// 						$view_table .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-unchecked"></span> '.$target['mata_NameEn'].'<label>';
		// 					}

		// 					$x++;
		// 				}

		// 				$view_table .= '</span>';

		// 			} else if ($field['field_type']=='Checkbox') {

		// 				$x = 0;

		// 				$data = $member[$field['mafi_FieldName']];

		// 				$view_table .= '<td><span class="form-inline"><label>';

		// 				$sql_target = 'SELECT *
		// 								FROM master_target
		// 								WHERE mafi_MasterFieldID = "'.$field['master_field_id'].'"';
		// 				$oRes_target = $oDB->Query($sql_target);
		// 				while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

		// 					if ($data != 0) {

		// 						if ($data == $target['mata_MasterTargetID']) {

		// 							if ($x==0) {

		// 								$view_table .= '<span class="glyphicon glyphicon-check"></span> '.$target['mata_NameEn'].'<label>';

		// 							} else {

		// 								$view_table .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-check"></span> '.$target['mata_NameEn'].'<label>';
		// 							}

		// 						} else {

		// 							if ($x==0) {

		// 								$view_table .= '<span class="glyphicon glyphicon-unchecked"></span> '.$target['mata_NameEn'].'<label>';

		// 							} else {

		// 								$view_table .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-unchecked"></span> '.$target['mata_NameEn'].'<label>';
		// 							}
		// 						}

		// 					} else {

		// 						$edit_status = "T";

		// 						$view_table .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-unchecked"></span> '.$target['mata_NameEn'].'<label>';
		// 					}

		// 					$x++;
		// 				}

		// 				$view_table .= '</label></span>';

		// 			} else if ($field['field_type']=='Selection') {

		// 				$data = $member[$field['mafi_FieldName']];

		// 				if ($field['master_field_id'] == 33 || $field['master_field_id'] == 45) {

		// 					$sql_target = 'SELECT prov_Name FROM province WHERE prov_ProvinceID = "'.$data.'"';

		// 				} elseif ($field['master_field_id'] == 34 || $field['master_field_id'] == 46) {

		// 					$sql_target = 'SELECT coun_NiceName FROM country WHERE coun_CountryID = "'.$data.'"';

		// 				} else {

		// 					$sql_target = 'SELECT mata_NameEn FROM master_target WHERE mata_MasterTargetID = "'.$data.'"';
		// 				}
						
		// 				$data = $oDB->QueryOne($sql_target);

		// 				if ($data=="") { $edit_status = "T"; $data = "-"; }

		// 				$view_table .= '<td>'.$data;

		// 			} else if ($field['field_type']=='Tel') {

		// 				$data = $member[$field['mafi_FieldName']];

		// 				if ($data=="") { $edit_status = "T"; $data = "-"; }

		// 				$view_table .= '<td>'.$data;
		// 			}

		// 			$view_table .= '</td></tr>';
		// 		}
		// 	}
		// }

		// $sql_custom = 'SELECT custom_field.*,
		// 				custom_form.cufo_Require,
		// 				field_type.fity_Name AS field_type
		// 				FROM custom_field
		// 				LEFT JOIN custom_form
		// 				ON custom_form.cufi_CustomFieldID = custom_field.cufi_CustomFieldID
		// 				LEFT JOIN field_type
		// 				ON custom_field.fity_FieldTypeID = field_type.fity_FieldTypeID
		// 				WHERE custom_form.card_CardID = "'.$axRow['card_id'].'"
		// 				AND custom_form.cufo_FillIn = "Y"
		// 				ORDER BY custom_field.cufi_FieldOrder';

		// $oRes = $oDB->Query($sql_custom);
		// $check_field = $oDB->QueryOne($sql_custom);

		// if ($check_field) {

		// 	$view_table .= '<tr class="th_table"><td colspan="3" style="text-align:center;background-color:#003369"><b>Custom</b></td></tr>';

		// 	while ($field = $oRes->FetchRow(DBI_ASSOC)){

		// 		$sql_member_custom = 'SELECT reda_Value
		// 								FROM custom_register_data 
		// 								WHERE mebe_MemberID="'.$axRow['member_id'].'"
		// 								AND card_CardID="'.$axRow['card_id'].'"
		// 								AND cufi_CustomFieldID="'.$field['cufi_CustomFieldID'].'"';
		// 		$data = $oDB->QueryOne($sql_member_custom);

		// 		$view_table .= '<tr>
		// 							<td style="text-align:right"><b>'.$field['cufi_Name'].'</b></td>
		// 							<td width="10px" style="text-align:center">:</td>';

		// 		if ($field['field_type']=='Text') {

		// 			if ($data=="") { $data = "-"; $edit_status = "T"; }

		// 			$view_table .= '<td>'.$data;
						
		// 		} else if ($field['field_type']=='Number') {

		// 			if ($data=="0") { $data = "-"; $edit_status = "T"; }

		// 			$view_table .= '<td>'.$data;
						
		// 		} else if ($field['field_type']=='Date') {

		// 			if ($data != '0000-00-00') { 

		// 				$view_table .= '<td>'.DateOnly($data); 

		// 			} else { $view_table .= '<td>-'; $edit_status = "T"; }
						
		// 		} else if ($field['field_type']=='Radio') {

		// 			$x = 0;

		// 			$view_table .= '<td><span class="form-inline"><label>';

		// 			$sql_target = 'SELECT *
		// 							FROM custom_list_value
		// 							WHERE cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
		// 			$oRes_target = $oDB->Query($sql_target);
		// 			while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

		// 				if ($data != 0) {

		// 					if ($data == $target['clva_CustomListValueID']) {

		// 						if ($x==0) {

		// 							$view_table .= '<span class="glyphicon glyphicon-check"></span> '.$target['clva_Name'].'<label>';

		// 						} else {

		// 							$view_table .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-check"></span> '.$target['clva_Name'].'<label>';
		// 						}

		// 					} else {

		// 						if ($x==0) {

		// 							$view_table .= '<span class="glyphicon glyphicon-unchecked"></span> '.$target['clva_Name'].'<label>';

		// 						} else {

		// 							$view_table .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-unchecked"></span> '.$target['clva_Name'].'<label>';
		// 						}
		// 					}

		// 				} else {

		// 					$edit_status = "T";

		// 					$view_table .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-unchecked"></span> '.$target['clva_Name'].'<label>';
		// 				}

		// 				$x++;
		// 			}

		// 			$view_table .= '</span>';

		// 		} else if ($field['field_type']=='Checkbox') {

		// 			$x = 0;

		// 			$view_table .= '<td><span class="form-inline"><label>';

		// 			$sql_target = 'SELECT *
		// 							FROM custom_list_value
		// 							WHERE cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
		// 			$oRes_target = $oDB->Query($sql_target);
		// 			while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

		// 				if ($data != 0) {

		// 					if ($data == $target['clva_CustomListValueID']) {

		// 						if ($x==0) {

		// 							$view_table .= '<span class="glyphicon glyphicon-check"></span> '.$target['clva_Name'].'<label>';

		// 						} else {

		// 							$view_table .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-check"></span> '.$target['clva_Name'].'<label>';
		// 						}

		// 					} else {

		// 						if ($x==0) {

		// 							$view_table .= '<span class="glyphicon glyphicon-unchecked"></span> '.$target['clva_Name'].'<label>';

		// 						} else {

		// 							$view_table .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-unchecked"></span> '.$target['clva_Name'].'<label>';
		// 						}
		// 					}

		// 				} else {

		// 					$edit_status = "T";

		// 					$view_table .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-unchecked"></span> '.$target['clva_Name'].'<label>';
		// 				}

		// 				$x++;
		// 			}

		// 			$view_table .= '</label></span>';

		// 		} else if ($field['field_type']=='Selection') {

		// 			$sql_target = 'SELECT clva_NameEn FROM custom_list_value WHERE clva_CustomListValueID = "'.$data.'"';
		// 			$data = $oDB->QueryOne($sql_target);

		// 			if ($data=="") { $data = "-"; $edit_status = "T"; }

		// 			$view_table .= '<td>'.$data;

		// 		} else if ($field['field_type']=='Tel') {

		// 			if ($data=="") { $data = "-"; $edit_status = "T"; }

		// 			$view_table .= '<td>'.$data;
		// 		}

		// 		$view_table .= '</td></tr>';
		// 	}
		// }

		// $view_table .= '</table>';

		// $view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Profile'.$axRow['member_register_id'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>

		// 			<div class="modal fade" id="Profile'.$axRow['member_register_id'].'" tabindex="-1" role="dialog" aria-labelledby="ProfileDataLabel">
		// 				<div class="modal-dialog" role="document">
		// 					<div class="modal-content">
		// 					    <div class="modal-body">
		// 					        <center><br>
		// 					        	'.$member_image.'&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-plus" style="font-size:20px"></span>&nbsp;&nbsp;&nbsp;'.$card_image.'<br><br>
		// 					        	'.$view_table.'
		// 					        </center>';

		// // if ($edit_status == 'T') {

		// 	$view .= '		    	<a href="edit_data.php?id='.$axRow['member_register_id'].'">
		// 					        <button type="button" class="btn btn-default btn-sm">Edit Data</button></a>';
		// // }

		// $view .= '			    </div>
		// 					</div>
		// 				</div>
		// 			</div>';



		# TABLE

		  $data_register .= '<tr>
								<td>'.$regis_n++.'</td>
								<td style="text-align:center">'.$axRow['member_image'].'</td>
								<td >'.$member_name.'</td>
								<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
									<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
								<td style="text-align:center"><a href="../card/card.php">'.$axRow['card_image'].'</a><br>
									<span style="font-size:11px">'.$axRow['card_name'].'</span></td>
								<td style="text-align:center">'.$axRow['branch_name'].'</span></td>
								<td style="text-align:right">'.number_format($axRow['member_fee'],2).' ฿</td>
								<td style="text-align:center">'.DateOnly($axRow['date_start']).'</td>
								<td style="text-align:center">'.DateTime($axRow['date_create']).'<hr>'.$axRow['user_type'].'</td>
							</tr>' ;
	}
}





#  brand dropdownlist

$sql_brand ='SELECT brand_id, name FROM mi_brand WHERE flag_del!=1 ORDER BY name';
$oRes_brand = $oDB->Query($sql_brand);

$select_brand = '';
$selected = "";

if ($brand_id==0) {	$selected = "selected";	}
else {	$selected = "";	}

$select_brand .= '<option value="0" '.$selected.'>All</option>';

$selected = "";

while ($axRow = $oRes_brand->FetchRow(DBI_ASSOC)){

	for($j=0 ; $j<count($_POST["brand_id"]) ; $j++){
	
		if ($axRow['brand_id']==$_POST["brand_id"][$j]) {	$selected = "selected";	}
	}

	$select_brand .= '<option value="'.$axRow['brand_id'].'" '.$selected.'>'.$axRow['name'].'</option>';

	$selected = "";
}

$oTmp->assign('select_brand', $select_brand);




$oTmp->assign('data_register', $data_register);

$oTmp->assign('is_menu', 'is_transaction');

$oTmp->assign('content_file','transaction/register.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());

}

//========================================//


?>
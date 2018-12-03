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

if ($_SESSION['role_action']['redeem_trans']['view'] != 1) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");
$Act = $_REQUEST['act'];
$path_upload_member = $_SESSION['path_upload_member'];
$path_upload_collection = $_SESSION['path_upload_collection'];



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

	$where_brand = ' AND mi_brand.brand_id = "'.$_SESSION['user_brand_id'].'"';
}

if($_SESSION['user_branch_id']){

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
						reward.rewa_RewardID,
						reward.rewa_Name AS reward_name,
						reward.rewa_Image AS reward_image,
						reward.rewa_ImagePath,
						reward.rewa_Type,
						reward.card_CardID,
						mb_member.firstname,
						mb_member.lastname,
						mb_member.facebook_name,
						mb_member.email AS member_email,
						mb_member.member_image AS member_image,
						mb_member.member_id AS member_id,
						mb_member.facebook_id AS facebook_id,
						mi_branch.name AS branch_name,
						mi_user_type.name AS user_type,
						reward_redeem_trans.coty_CollectionTypeID AS coty_id,
						reward_redeem_trans.rera_RewardQty_Point AS point_qty,
						reward_redeem_trans.rera_RewardQty_Stamp AS stamp_qty,
						reward_redeem_trans.retr_RedeemQty AS redeem_qty,
						reward_redeem_trans.rede_AutoRedeem AS auto_redeem,
						reward_redeem_trans.retr_RewardRedeemTransID AS code_use,
						reward_redeem_trans.retr_RedeemDate AS redeem_date,
						reward_redeem_trans.retr_CreatedDate AS create_date,
						reward_redeem_trans.retr_Deleted AS status_del

						FROM reward_redeem_trans

						LEFT JOIN mb_member
						ON  reward_redeem_trans.memb_MemberID = mb_member.member_id

						LEFT JOIN mi_branch
						ON reward_redeem_trans.brnc_BranchID = mi_branch.branch_id

						LEFT JOIN mi_brand
						ON mi_branch.brand_id = mi_brand.brand_id

						LEFT JOIN reward_redeem
						ON reward_redeem_trans.rede_RewardRedeemID = reward_redeem.rede_RewardRedeemID

						LEFT JOIN reward
						ON reward_redeem.rewa_RewardID = reward.rewa_RewardID

						LEFT JOIN mi_user
						ON mi_user.user_id = reward_redeem_trans.retr_UpdatedBy 

						LEFT JOIN mi_user_type
						ON mi_user.user_type_id = mi_user_type.user_type_id 

						WHERE reward_redeem_trans.retr_Platform = "Insert"
						'.$where_brand.'

					ORDER BY retr_UpdatedDate DESC
					'.$where_show;

$rs_trans = $oDB->Query($sql_transaction);

if (!$rs_trans) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_trans->FetchRow(DBI_ASSOC)) {


		# MEMBER BRAND ID

		$sql_brand = 'SELECT member_brand_code
						FROM mb_member_register
						WHERE bran_BrandID="'.$axRow['brand_id'].'"
						AND member_id="'.$axRow['member_id'].'"';
		$brand_code = $oDB->QueryOne($sql_brand);


		# MEMBER

		$member_name = '';

		if ($axRow['firstname'].' '.$axRow['lastname']) {

			if ($axRow['member_email']) {

				if ($axRow['member_mobile']) {

					if ($brand_code) {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>'.$axRow['member_mobile'].'<br>Member Brand : '.$brand_code;

					} else {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>'.$axRow['member_mobile'];
					}

				} else {

					if ($brand_code) {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>Member Brand : '.$brand_code;

					} else {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'];
					}
				}

			} else {

				if ($axRow['member_mobile']) {

					if ($brand_code) {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_mobile'].'<br>Member Brand : '.$brand_code;

					} else {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_mobile'];
					}
				
				} else { 

					if ($brand_code) {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>Member Brand : '.$brand_code;

					} else {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'];
					}
				}
			}

		} else {

			if ($axRow['member_email']) {

				if ($axRow['member_mobile']) { 

					if ($brand_code) {
						
						$member_name = $axRow['member_email'].'<br>'.$axRow['member_mobile'].'<br>Member Brand : '.$brand_code;

					} else {
						
						$member_name = $axRow['member_email'].'<br>'.$axRow['member_mobile'];
					}
				
				} else { 

					if ($brand_code) {
						
						$member_name = $axRow['member_email'].'<br>Member Brand : '.$brand_code;

					} else {
						
						$member_name = $axRow['member_email'];
					}
				}

			} else {

				if ($axRow['member_mobile']) { 

					if ($brand_code) {
						
						$member_name = $axRow['member_mobile'].'<br>Member Brand : '.$brand_code;

					} else {
						
						$member_name = $axRow['member_mobile'];
					}

				} else { 

					if ($brand_code) {
						
						$member_name = 'Member Brand : '.$brand_code;

					} else {
						
						$member_name = '';
					}
				}
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


		# REWARD

		if ($axRow['rewa_Type'] == 'Card') {

			$sql_card = 'SELECT image, image_newupload,path_image FROM mi_card WHERE card_id="'.$axRow['card_CardID'].'"';
			$oRes_card = $oDB->Query($sql_card);
			$axRow_card = $oRes_card->FetchRow(DBI_ASSOC);

			# REWARDS IMAGE

			if($axRow_card['image']!=''){

				$reward_image = '<img src="../../upload/'.$axRow_card['path_image'].$axRow_card['image'].'" class="img-rounded image_border" height="100"/>';

				$axRow['reward_image'] = '<img src="../../upload/'.$axRow_card['path_image'].$axRow_card['image'].'" class="img-rounded image_border" height="50"/>';

			} else if($axRow_card['image_newupload']!=''){

				$reward_image = '<img src="../../upload/'.$axRow_card['path_image'].$axRow_card['image_newupload'].'" class="img-rounded image_border" height="100"/>';

				$axRow['reward_image'] = '<img src="../../upload/'.$axRow_card['path_image'].$axRow_card['image_newupload'].'" class="img-rounded image_border" height="50"/>';

			} else {

				$reward_image = '<img src="../../images/400x400.png" class="img-rounded image_border" height="100"/>';

				$axRow['reward_image'] = '<img src="../../images/400x400.png" class="img-rounded image_border" height="50"/>';
			}

		} else {

			# REWARDS IMAGE

			if($axRow['reward_image']!=''){

				$reward_image = '<img src="../../upload/'.$axRow['rewa_ImagePath'].$axRow['reward_image'].'" class="image_border" width="100" height="100"/>';

				$axRow['reward_image'] = '<img src="../../upload/'.$axRow['rewa_ImagePath'].$axRow['reward_image'].'" class="image_border" width="50" height="50"/>';

			} else {

				$reward_image = '<img src="../../images/400x400.png" class="image_border" width="100" height="100"/>';

				$axRow['reward_image'] = '<img src="../../images/400x400.png" class="image_border" width="50" height="50"/>';
			}
		}


		# LOGO

		if($axRow['logo_image']!=''){

			$axRow['logo_image'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="50" height="50"/>';

		} else {

			$axRow['logo_image'] = '<img src="../../images/400x400.png" class="image_border" width="50" height="50"/>';
		}



		# STASUS

		if ($axRow['status_del']=='T') { 

			$status_del = 'Deleted';

		} else { 

			$status_del = 'Active';
		}


		
		# RATIO

		$ratio = '';

		$coty_Image = '';

		if ($axRow['coty_id'] && $axRow['stamp_qty']) {

			$sql_image = 'SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID="'.$axRow['coty_id'].'"';
	 		$coty_Image = $oDB->QueryOne($sql_image);

			$coty_Image = '<img src="'.$path_upload_collection.$coty_Image.'" style="margin-bottom:5px" width="12" height="12"/>';

			$ratio .= $coty_Image.' &nbsp; '.number_format($axRow['stamp_qty']).' / '.$axRow['redeem_qty'];
		}


		if ($axRow['point_qty']) {

			$sql_image = 'SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID="3"';
	 		$coty_Image = $oDB->QueryOne($sql_image);

			$coty_Image = '<img src="'.$path_upload_collection.$coty_Image.'" style="margin-bottom:5px" width="12" height="12"/>';

			$ratio .= $coty_Image.' &nbsp; '.number_format($axRow['point_qty']).' / '.$axRow['redeem_qty'];
		}



		# AUTO REDEEM

		if ($axRow['auto_redeem']=='T') { $ratio = 'Auto Redeem'; }



		# TABLE

		$data_table .= '<tr>
							<td>'.number_format($n++).'</td>
							<td style="text-align:center">'.$axRow['code_use'].'</td>
							<td style="text-align:center">'.$axRow['member_image'].'</td>
							<td>'.$member_name.'</td>
							<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['logo_image'].'</a><br>
								<span style="font-size:11px;">'.$axRow['brand_name'].'</span></td>
							<td style="text-align:center"><a href="../redeem/redeem.php">'.$axRow['reward_image'].'</a><br>
								<span style="font-size:11px;">'.$axRow['reward_name'].'</span></td>
							<td>'.$ratio.'</td>
							<td>'.$axRow['branch_name'].'</td>
							<td style="text-align:center">'.DateOnly($axRow['redeem_date']).'</td>
							<td style="text-align:center">'.DateTime($axRow['create_date']).'
								<hr>'.$axRow['user_type'].'</td>
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

$oTmp->assign('content_file','transaction/redeem.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
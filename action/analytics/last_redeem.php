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


$id = $_REQUEST['id'];

$path_upload_member = $_SESSION['path_upload_member'];

$path_upload_collection = $_SESSION['path_upload_collection'];

if($_SESSION['user_branch_id']){

	$where_branch .= ' AND mi_branch.branch_id = "'.$_SESSION['user_branch_id'].'"';
}


$sql = 'SELECT

		reward.*,
		mi_brand.name as brand_name,
		mi_tg_activity.activity_name as category_name,
		reward_ratio.rera_RewardQty as reward_qty,
		collection_type.coty_Image as collection_type,
		reward_ratio.rera_RewardQty_Point as point_qty,
		reward_ratio.rera_RewardQty_Stamp as stamp_qty,
		reward_ratio.coty_CollectionTypeID as collection_id

	  	FROM reward

		LEFT JOIN mi_brand
		ON mi_brand.brand_id = reward.bran_BrandID 

		LEFT JOIN mi_tg_activity
		ON mi_tg_activity.id_activity = reward.rewa_Category

		LEFT JOIN reward_redeem
		ON reward_redeem.rewa_RewardID = reward.rewa_RewardID

		LEFT JOIN reward_ratio
		ON reward_redeem.rede_RewardRedeemID = reward_ratio.rede_RewardRedeemID

		LEFT JOIN collection_type
		ON collection_type.coty_CollectionTypeID = reward_ratio.coty_CollectionTypeID

		WHERE reward_redeem.rede_RewardRedeemID = "'.$id.'"';

$oRes = $oDB->Query($sql)or die(mysql_error());

$asData = array();

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	if ($axRow['rewa_Description']=="") { $axRow['rewa_Description']="-"; }

	if ($axRow['rewa_UOM']=="") { $axRow['rewa_UOM']="-"; }

	if ($axRow['rewa_Limit']=="F") { $axRow['rewa_Qty']="Unlimit"; }

	if ($axRow['collection_id']=="0") {	

		$axRow['collection_id'] = $axRow['reward_qty']." / ".$axRow['point_qty']." Points";

	} else {

		$axRow['collection_id'] = $axRow['reward_qty']." / ".$axRow['stamp_qty']." <img src='".$path_upload_collection.$axRow['collection_type']."' style='margin-bottom:8px' width='20' height='20'/>";
	}

	$asData = $axRow;
}


$sql_redeem = "SELECT 
					reward_redeem_trans.retr_CreatedDate AS date_use,
					reward_redeem_trans.retr_RewardRedeemTransID AS code_use,
					reward_redeem_trans.coty_CollectionTypeID AS collect_type,
					reward_redeem_trans.rera_RewardQty_Point AS point,
					reward_redeem_trans.rera_RewardQty_Stamp AS stamp,
					reward_redeem_trans.retr_RedeemQty AS qty,
					mi_brand.brand_id,
					mi_branch.name AS branch_name,
					mb_member.firstname, 
					mb_member.lastname, 
					mb_member.facebook_id, 
					mb_member.facebook_name, 
					mb_member.member_id, 
					mb_member.member_image,
					mb_member.email,
					mb_member.mobile,
					collection_type.coty_Image AS collect_img

					FROM reward_redeem_trans

					LEFT JOIN mb_member
					ON reward_redeem_trans.memb_MemberID = mb_member.member_id

					LEFT JOIN mi_branch
					ON reward_redeem_trans.brnc_BranchID = mi_branch.branch_id

					LEFT JOIN mi_brand
					ON mi_brand.brand_id = mi_branch.brand_id

					LEFT JOIN collection_type
					ON collection_type.coty_CollectionTypeID = reward_redeem_trans.coty_CollectionTypeID 

					WHERE reward_redeem_trans.rede_RewardRedeemID = ".$id."
					".$where_branch."

					ORDER BY date_use DESC";

$oRes_redeem = $oDB->Query($sql_redeem)or die(mysql_error());
$check_redeem = $oDB->QueryOne($sql_redeem);

if ($check_redeem) {

	$table_redeem = "<table id='example' class='table table-bordered' style='background-color:white;'>
						<thead><tr class='th_table'>
							<th style='text-align:center;'>Redeem Date</th>
							<th style='text-align:center;'>Code Redeem</th>
							<th style='text-align:center;'>Member</th>
							<th style='text-align:center;'>Profile</th>
							<th style='text-align:center;'>Branch</th>
							<th style='text-align:center;'>Reward Qty</th>
							<th style='text-align:center;'>Redeem Ratio</th>
						</tr></thead><tbody>";

	while ($axRow = $oRes_redeem->FetchRow(DBI_ASSOC)) {


		# MEMBER BRAND ID

		$sql_brand = 'SELECT member_brand_code
						FROM mb_member_register
						WHERE bran_BrandID="'.$axRow['brand_id'].'"
						AND member_id="'.$axRow['member_id'].'"';
		$brand_code = $oDB->QueryOne($sql_brand);


		# MEMBER

		if($axRow['member_image']!='' && $axRow['member_image']!='user.png') {

			$axRow['member_image'] = '<img class="img-circle image_border" src="'.$path_upload_member.$axRow['member_image'].'"width="65" height="65"/>';

		} else if ($axRow['facebook_id']!='') {

			$axRow['member_image'] = '<img class="img-circle image_border" src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=square" width="65" height="65" />';

		} else {

			$axRow['member_image'] = '<img src="../../images/user.png" width="65" height="65" class="img-circle image_border" />';
		}

		$member_name = '';

		if ($axRow['firstname'] || $axRow['lastname']) {

			if ($axRow['email']) {

				if ($axRow['mobile']) {

					if ($brand_code) {
									
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'].'<br>Member Brand : '.$brand_code;

					} else {
									
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'];
					}

				} else {

					if ($brand_code) {
									
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>Member Brand : '.$brand_code;
								
					} else {
									
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'];
					}
				}

			} else {

				if ($axRow['mobile']) {

					if ($brand_code) {
									
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile'].'<br>Member Brand : '.$brand_code;

					} else {
									
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile'];
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

			if ($axRow['email']) {

				if ($axRow['mobile']) { $member_name = $axRow['email'].'<br>'.$axRow['mobile'];

				} else { $member_name = $axRow['email']; }

			} else {

				if ($axRow['mobile']) { $member_name = $axRow['mobile'];

				} else { $member_name = ''; }
			}
		}



		# RATIO IMAGE

		if($axRow['collect_type']!='0'){

			$ratio = $axRow['stamp'].' &nbsp; <img src="'.$path_upload_collection.$axRow['collect_img'].'" height="20px">';

		} else {

			$ratio = $axRow['point'].' Points';
		}


		# CODE USE

		if (is_numeric($axRow['code_use'])) { $axRow['code_use'] = '-'; }


		$table_redeem .= "<tr>
							<td style='text-align:center'>".DateTime($axRow['date_use'])."</td>
							<td style='text-align:center'>".$axRow['code_use']."</td>
							<td style='text-align:center'>".$axRow['member_image']."</td>
							<td>".$member_name."</td>
							<td style='text-align:center'>".$axRow['branch_name']."</td>
							<td style='text-align:center'>".$axRow['qty']."</td>
							<td style='text-align:center'>".$ratio."</td>
						</tr>";
	}

	$table_redeem .= "</tbody></table>";
}




$oTmp->assign('table_redeem', $table_redeem);

$oTmp->assign('data', $asData);

$oTmp->assign('is_menu', 'is_analytics');

$oTmp->assign('content_file', 'analytics/last_redeem.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
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

if($_SESSION['user_branch_id']){

	$where_branch = ' AND mi_branch.branch_id = "'.$_SESSION['user_branch_id'].'"';
}



$sql = 'SELECT hilight_coupon.*,
			mi_brand.name AS brand_name

			FROM hilight_coupon

			LEFT JOIN mi_brand
			ON mi_brand.brand_id = hilight_coupon.bran_BrandID

			WHERE hilight_coupon.coup_CouponID = "'.$id.'"';

$oRes = $oDB->Query($sql)or die(mysql_error());

$asData = array();

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	if ($axRow['coup_Description']=="") { $axRow['coup_Description']="-"; }
	else { $axRow['coup_Description'] = nl2br($axRow['coup_Description']); }

	$asData = $axRow;
}



$sql_earn = "SELECT 
				hilight_coupon_trans.hico_CreatedDate AS date_use,
				hilight_coupon_trans.hico_HilightCouponID AS code_use,
				mi_branch.name AS branch_name,
				mb_member.firstname, 
				mb_member.lastname, 
				mb_member.facebook_id, 
				mb_member.facebook_name, 
				mb_member.member_id, 
				mb_member.member_image,
				mb_member.mobile,
				mb_member.email

				FROM hilight_coupon_trans

				LEFT JOIN mb_member
				ON hilight_coupon_trans.memb_MemberID = mb_member.member_id

				LEFT JOIN mi_branch
				ON hilight_coupon_trans.brnc_BranchID = mi_branch.branch_id

				LEFT JOIN mi_brand
				ON mi_brand.brand_id = mi_branch.brand_id

				WHERE hilight_coupon_trans.coup_CouponID = ".$id."
				".$where_branch."

				ORDER BY date_use DESC";

$oRes_earn = $oDB->Query($sql_earn)or die(mysql_error());

$check_earn = $oDB->QueryOne($sql_earn);

if ($check_earn) {

	$table_earn = "<table id='example' class='table table-bordered' style='background-color:white;'>
						<thead><tr class='th_table'>
							<th style='text-align:center;'>Use Date</th>
							<th style='text-align:center;'>Code Use</th>
							<th style='text-align:center;'>Customer</th>
							<th style='text-align:center;'>Profile</th>
							<th style='text-align:center;'>Branch</th>
						</tr></thead><tbody>";

	while ($axRow = $oRes_earn->FetchRow(DBI_ASSOC)) {

		# MEMBER

		if($axRow['member_image']!='' && $axRow['member_image']!='user.png') {

			$axRow['member_image'] = '<img class="img-circle image_border" src="'.$path_upload_member.$axRow['member_image'].'"width="60" height="60"/>';

		} else if ($axRow['facebook_id']!='') {

			$axRow['member_image'] = '<img class="img-circle image_border" src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=square" width="60" height="60" />';

		} else {

			$axRow['member_image'] = '<img src="../../images/user.png" width="60" height="60" class="img-circle image_border" />';
		}

		$member_name = '';

		if ($axRow['firstname'] || $axRow['lastname']) {

			if ($axRow['email']) {

				if ($axRow['mobile']) {
							
					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'];

				} else { $member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email']; }

			} else {

				if ($axRow['mobile']) {
							
					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile'];

				} else { $member_name = $axRow['firstname'].' '.$axRow['lastname']; }
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



		$table_earn .= "<tr>
							<td style='text-align:center'>".DateTime($axRow['date_use'])."</td>
							<td style='text-align:center'>".$axRow['code_use']."</td>
							<td style='text-align:center'>".$axRow['member_image']."</td>
							<td>".$member_name."</td>
							<td style='text-align:center'>".$axRow['branch_name']."</td>
						</tr>";
	}

	$table_earn .= "</tbody></table>";
}




$oTmp->assign('table_earn', $table_earn);

$oTmp->assign('data', $asData);

$oTmp->assign('is_menu', 'is_analytics');

$oTmp->assign('content_file', 'analytics/last_earn.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
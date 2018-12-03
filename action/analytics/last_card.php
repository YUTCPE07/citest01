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



$sql = 'SELECT DISTINCT	mi_card.*,
			mi_brand.name AS brand_name,
			mi_card_type.name AS card_type_name

			FROM mi_card

			LEFT JOIN mi_brand
			ON mi_brand.brand_id = mi_card.brand_id

			LEFT JOIN mi_card_type
			ON mi_card_type.card_type_id = mi_card.card_type_id

			WHERE mi_card.card_id = "'.$id.'"';

$oRes = $oDB->Query($sql)or die(mysql_error());

$asData = array();

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$axRow['date_expired'] = DateOnly($axRow['date_expired']);

	if ($axRow['period_type']==2) { $axRow['period_type_other'] = $axRow['period_type_other'].' Months'; }
	if ($axRow['period_type']==3) { $axRow['period_type_other'] = $axRow['period_type_other'].' Years'; }
	if ($axRow['period_type']==4) { $axRow['period_type_other'] = 'Member Life Time'; }
	if ($axRow['description']=="" || !$axRow['description']) { $axRow['description']="-"; }

	if ($axRow['description']=="" || !$axRow['description']) { $axRow['description']="-"; }
	else { $axRow['description'] = nl2br($axRow['description']); }

	$asData = $axRow;
}


if($_SESSION['user_brand_id']){

	$where_brand = 'AND mi_card.brand_id = "'.$_SESSION['user_brand_id'].'"';
}

if($_SESSION['user_branch_id']){

	$where_branch = ' AND mi_branch.branch_id = "'.$_SESSION['user_branch_id'].'"';
}


$sql_card = "SELECT * FROM (

				SELECT member_privilege_trans.mepe_CreatedDate AS date_use,
					member_privilege_trans.mepe_MemberPrivlegeID AS code_use,
					mi_card.card_id,
					privilege.priv_Name AS privilege_name,
					privilege.priv_Image AS privilege_image,
					privilege.priv_ImageNew AS privilege_new,
					privilege.priv_ImagePath AS path_image,
					'Privilege' AS privilege_type,
					mi_branch.name AS branch_name,
					mb_member.firstname, 
					mb_member.lastname, 
					mb_member.facebook_id, 
					mb_member.facebook_name, 
					mb_member.member_id, 
					mb_member.member_image,
					mb_member.mobile,
					mb_member.email

					FROM member_privilege_trans

					LEFT JOIN mb_member
					ON member_privilege_trans.memb_MemberID = mb_member.member_id

					LEFT JOIN mi_card
					ON member_privilege_trans.card_CardID = mi_card.card_id

					LEFT JOIN privilege
					ON member_privilege_trans.priv_PrivilegeID = privilege.priv_PrivilegeID

					LEFT JOIN mi_branch
					ON member_privilege_trans.brnc_BranchID = mi_branch.branch_id

					LEFT JOIN mi_brand
					ON mi_brand.brand_id = mi_branch.brand_id

					WHERE member_privilege_trans.card_CardID = ".$id."
					".$where_brand."
					".$where_branch."

				UNION

				SELECT member_coupon_trans.meco_CreatedDate AS date_use,
					member_coupon_trans.meco_MemberCouponID AS code_use,
					mi_card.card_id,
					coupon.coup_Name AS privilege_name,
					coupon.coup_Image AS privilege_image,
					coupon.coup_ImageNew AS privilege_new,
					coupon.coup_ImagePath AS path_image,
					'Coupon' AS privilege_type,
					mi_branch.name AS branch_name,
					mb_member.firstname, 
					mb_member.lastname, 
					mb_member.facebook_id, 
					mb_member.facebook_name, 
					mb_member.member_id, 
					mb_member.member_image,
					mb_member.mobile,
					mb_member.email

					FROM member_coupon_trans

					LEFT JOIN mb_member
					ON member_coupon_trans.memb_MemberID = mb_member.member_id

					LEFT JOIN mi_card
					ON member_coupon_trans.card_CardID = mi_card.card_id

					LEFT JOIN coupon
					ON member_coupon_trans.coup_CouponID = coupon.coup_CouponID

					LEFT JOIN mi_branch
					ON member_coupon_trans.brnc_BranchID = mi_branch.branch_id

					LEFT JOIN mi_brand
					ON mi_brand.brand_id = mi_branch.brand_id

					WHERE member_coupon_trans.card_CardID = ".$id."
					AND coupon.coup_Birthday != 'T'
					".$where_brand."
					".$where_branch."

				UNION

				SELECT member_coupon_trans.meco_CreatedDate AS date_use,
					member_coupon_trans.meco_MemberCouponID AS code_use,
					mi_card.card_id,
					coupon.coup_Name AS privilege_name,
					coupon.coup_Image AS privilege_image,
					coupon.coup_ImageNew AS privilege_new,
					coupon.coup_ImagePath AS path_image,
					'Birthday Coupon' AS privilege_type,
					mi_branch.name AS branch_name,
					mb_member.firstname, 
					mb_member.lastname, 
					mb_member.facebook_id, 
					mb_member.facebook_name, 
					mb_member.member_id, 
					mb_member.member_image,
					mb_member.mobile,
					mb_member.email

					FROM member_coupon_trans

					LEFT JOIN mb_member
					ON member_coupon_trans.memb_MemberID = mb_member.member_id

					LEFT JOIN mi_card
					ON member_coupon_trans.card_CardID = mi_card.card_id

					LEFT JOIN coupon
					ON member_coupon_trans.coup_CouponID = coupon.coup_CouponID

					LEFT JOIN mi_branch
					ON member_coupon_trans.brnc_BranchID = mi_branch.branch_id

					LEFT JOIN mi_brand
					ON mi_brand.brand_id = mi_branch.brand_id

					WHERE member_coupon_trans.card_CardID = ".$id."
					AND coupon.coup_Birthday = 'T'
					".$where_brand."
					".$where_branch."

				UNION

				SELECT member_activity_trans.meac_CreatedDate AS date_use,
					member_activity_trans.meac_MemberActivityID AS code_use,
					mi_card.card_id,
					activity.acti_Name AS privilege_name,
					activity.acti_Image AS privilege_image,
					activity.acti_ImageNew AS privilege_new,
					activity.acti_ImagePath AS path_image,
					'Activity' AS privilege_type,
					mi_branch.name AS branch_name,
					mb_member.firstname, 
					mb_member.lastname, 
					mb_member.facebook_id, 
					mb_member.facebook_name, 
					mb_member.member_id, 
					mb_member.member_image,
					mb_member.mobile,
					mb_member.email

					FROM member_activity_trans

					LEFT JOIN mb_member
					ON member_activity_trans.memb_MemberID = mb_member.member_id

					LEFT JOIN mi_card
					ON member_activity_trans.card_CardID = mi_card.card_id

					LEFT JOIN activity
					ON member_activity_trans.acti_ActivityID = activity.acti_ActivityID

					LEFT JOIN mi_branch
					ON member_activity_trans.brnc_BranchID = mi_branch.branch_id

					LEFT JOIN mi_brand
					ON mi_brand.brand_id = mi_branch.brand_id

					WHERE member_activity_trans.card_CardID = ".$id."
					".$where_brand."
					".$where_branch."

				

				UNION

				SELECT member_identify.mein_CreatedDate AS date_use,
					member_identify.mein_MemberIndentifyID AS code_use,
					mi_card.card_id,
					mi_card.name AS privilege_name,
					mi_card.image AS privilege_image,
					mi_card.image_newupload AS privilege_new,
					mi_card.path_image AS path_image,
					'Check In' AS privilege_type,
					mi_branch.name AS branch_name,
					mb_member.firstname, 
					mb_member.lastname, 
					mb_member.facebook_id, 
					mb_member.facebook_name, 
					mb_member.member_id, 
					mb_member.member_image,
					mb_member.mobile,
					mb_member.email

					FROM member_identify

					LEFT JOIN mb_member
					ON member_identify.memb_MemberID = mb_member.member_id

					LEFT JOIN mi_card
					ON member_identify.card_CardID = mi_card.card_id

					LEFT JOIN mi_branch
					ON member_identify.brnc_BranchID = mi_branch.branch_id

					LEFT JOIN mi_brand
					ON mi_brand.brand_id = mi_branch.brand_id

					WHERE member_identify.card_CardID = ".$id."
					".$where_brand."
					".$where_branch."

				) member_trans

				ORDER BY date_use DESC";


$table_card = "<table id='example' class='table table-bordered' style='background-color:white;'>
					<thead><tr class='th_table'>
						<th style='text-align:center;'>Use Date</th>
						<th style='text-align:center;'>Code Use</th>
						<th style='text-align:center;'>Member</th>
						<th style='text-align:center;'>Profile</th>
						<th style='text-align:center;'>Privilege</th>
						<th style='text-align:center;'>Type</th>
						<th style='text-align:center;'>Branch</th>
						</tr></thead><tbody>";

$oRes_card = $oDB->Query($sql_card)or die(mysql_error());

while ($axRow = $oRes_card->FetchRow(DBI_ASSOC)) {

	# MEMBER CARD ID

	$sql_card = 'SELECT member_card_code
					FROM mb_member_register
					WHERE card_id="'.$axRow['card_id'].'"
					AND member_id="'.$axRow['member_id'].'"';
	$card_code = $oDB->QueryOne($sql_card);

	# MEMBER

	if($axRow['member_image']!='' && $axRow['member_image']!='user.png') {

		$axRow['member_image'] = '<img class="img-circle image_border" src="'.$path_upload_member.$axRow['member_image'].'"width="50" height="50"/>';

	} else if ($axRow['facebook_id']!='') {

		$axRow['member_image'] = '<img class="img-circle image_border" src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=square" width="50" height="50" />';

	} else {

		$axRow['member_image'] = '<img src="../../images/user.png" width="50" height="50" class="img-circle image_border" />';
	}

	$member_name = '';

	if ($axRow['firstname'] || $axRow['lastname']) {

		if ($axRow['email']) {

			if ($axRow['mobile']) {

				if ($card_code) {
								
					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'].'<br>Member Card : '.$card_code;

				} else {
								
					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'];
				}

			} else {

				if ($card_code) {
								
					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>Member Card : '.$card_code;
							
				} else {
								
					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'];
				}
			}

		} else {

			if ($axRow['mobile']) {

				if ($card_code) {
								
					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile'].'<br>Member Card : '.$card_code;

				} else {
								
					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile'];
				}

			} else {

				if ($card_code) {
								
					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>Member Card : '.$card_code;

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


	# TYPE CHECK IN

	if ($axRow['privilege_type']=='Check In') { $class_img = 'class="img-rounded image_border"'; } 
	else { $class_img = 'class="image_border"'; }


	# PRIVILEGE IMAGE

	if($axRow['privilege_new']!=''){

		$privilege_img = '<img src="../../upload/'.$axRow['path_image'].$axRow['priv_ImageNew'].'" '.$class_img.' height="50px">';

	} else if ($axRow['privilege_image']!='') {

		$privilege_img = '<img src="../../upload/'.$axRow['path_image'].$axRow['privilege_image'].'" '.$class_img.' height="50px">';

	} else {

		$privilege_img = '<img src="../../images/card_privilege.png" '.$class_img.' height="50px">';
	}


	$table_card .= "<tr>
						<td style='text-align:center'>".DateTime($axRow['date_use'])."</td>
						<td style='text-align:center'>".$axRow['code_use']."</td>
						<td style='text-align:center'>".$axRow['member_image']."</td>
						<td>".$member_name."</td>";

	if ($axRow['privilege_type']=="Privilege") {

		$table_card .= "<td style='text-align:center'>
							<a href='../privilege/privilege.php'>".$privilege_img."</a>
							<br><span style='font-size:11px'>".$axRow['privilege_name']."</span>
						</td>";

	} elseif ($axRow['privilege_type']=="Coupon" || $axRow['privilege_type']=="Birthday Coupon") {

		$table_card .= "<td style='text-align:center'>
							<a href='../coupon/coupon.php'>".$privilege_img."</a>
							<br><span style='font-size:11px'>".$axRow['privilege_name']."</span>
						</td>";

	} elseif ($axRow['privilege_type']=="Activity") {

		$table_card .= "<td style='text-align:center'>
							<a href='../activity/activity.php'>".$privilege_img."</a>
							<br><span style='font-size:11px'>".$axRow['privilege_name']."</span>
						</td>";
	} else {

		$table_card .= "<td style='text-align:center'>
							<a href='../card/card.php'>".$privilege_img."</a>
							<br><span style='font-size:11px'>".$axRow['privilege_name']."</span>
						</td>";
	}

	$table_card .= "	<td style='text-align:center'>".$axRow['privilege_type']."</td>
						<td style='text-align:center'>".$axRow['branch_name']."</td>
					</tr>";
}

$table_card .= "</tbody></table>";



$oTmp->assign('path_upload_card', $path_upload_card);

$oTmp->assign('data', $asData);

$oTmp->assign('table_card', $table_card);

$oTmp->assign('is_menu', 'is_analytics');

$oTmp->assign('content_file', 'analytics/last_card.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
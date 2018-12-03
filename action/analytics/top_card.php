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



$path_upload_member = $_SESSION['path_upload_member'];
$card_id = $_REQUEST['id'];



if($_SESSION['user_branch_id']){

	$where_branch .= ' AND mi_branch.branch_id = "'.$_SESSION['user_branch_id'].'"';
}



$sql = 'SELECT DISTINCT	mi_card.*,
			mi_brand.name AS brand_name,
			mi_card_type.name AS card_type_name
			FROM mi_card
			LEFT JOIN mi_brand
			ON mi_brand.brand_id = mi_card.brand_id
			LEFT JOIN mi_card_type
			ON mi_card_type.card_type_id = mi_card.card_type_id
			WHERE mi_card.card_id = "'.$card_id.'"';

$oRes = $oDB->Query($sql)or die(mysql_error());

$asData = array();

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$axRow['date_expired'] = DateOnly($axRow['date_expired']);

	if ($axRow['period_type']==2) { $axRow['period_type_other'] = $axRow['period_type_other'].' Months'; }
	if ($axRow['period_type']==3) { $axRow['period_type_other'] = $axRow['period_type_other'].' Years'; }
	if ($axRow['period_type']==4) { $axRow['period_type_other'] = 'Member Life Time'; }

	if ($axRow['description']=="" || !$axRow['description']) { $axRow['description']="-"; }
	else { $axRow['description'] = nl2br($axRow['description']); }

	$asData = $axRow;
}


	# BRAND

	$sql_brand_id = 'SELECT brand_id 
					FROM mi_card 
					WHERE card_id = "'.$card_id.'"';

	$brand_id = $oDB->QueryOne($sql_brand_id);

	# BRANCH

	$sql_branch = 'SELECT 
					name as txt,
					branch_id as id 
					FROM mi_branch 
					WHERE brand_id = "'.$brand_id.'" '.$where_branch.'';

	# PRIVILEGE

	$sql_privilege = 'SELECT DISTINCT
						privilege.priv_Name as txt,
						privilege.priv_PrivilegeID as id
						FROM privilege 
						LEFT JOIN mi_card_register
						ON mi_card_register.privilege_id = privilege.priv_PrivilegeID
						LEFT JOIN mi_card
						ON mi_card.card_id = mi_card_register.card_id
						WHERE privilege.bran_BrandID = "'.$brand_id.'" 
						AND mi_card_register.status=1 
						AND mi_card.card_id ="'.$card_id.'"';

	if ($_SESSION['user_type_id_ses']==3) {
		
		$sql_privilege .= ' AND mi_card_register.branch_id = "'.$_SESSION['user_branch_id'].'"';
	}

	# COUPON

	$sql_coupon = 'SELECT DISTINCT
					coupon.coup_Name as txt,
					coupon.coup_CouponID as id
					FROM coupon 
					LEFT JOIN mi_card_register
					ON mi_card_register.coupon_id = coupon.coup_CouponID
					LEFT JOIN mi_card
					ON mi_card.card_id = mi_card_register.card_id
					WHERE coupon.bran_BrandID = "'.$brand_id.'" 
					AND mi_card_register.status=1 
					AND mi_card.card_id ="'.$card_id.'"
					AND coupon.coup_Birthday!="T"';

	if ($_SESSION['user_type_id_ses']==3) {
		
		$sql_coupon .= ' AND mi_card_register.branch_id = "'.$_SESSION['user_branch_id'].'"';
	}

	# HBD COUPON

	$sql_hbd = 'SELECT DISTINCT
					coupon.coup_Name as txt,
					coupon.coup_CouponID as id
					FROM coupon 
					LEFT JOIN mi_card_register
					ON mi_card_register.coupon_id = coupon.coup_CouponID
					LEFT JOIN mi_card
					ON mi_card.card_id = mi_card_register.card_id
					WHERE coupon.bran_BrandID = "'.$brand_id.'" 
					AND mi_card_register.status=1 
					AND mi_card.card_id ="'.$card_id.'"
					AND coupon.coup_Birthday="T"';

	if ($_SESSION['user_type_id_ses']==3) {
		
		$sql_hbd .= ' AND mi_card_register.branch_id = "'.$_SESSION['user_branch_id'].'"';
	}

	# ACTIVITY

	$sql_activity = 'SELECT DISTINCT
						activity.acti_Name as txt,
						activity.acti_ActivityID as id
						FROM activity 
						LEFT JOIN mi_card_register
						ON mi_card_register.activity_id = activity.acti_ActivityID
						LEFT JOIN mi_card
						ON mi_card.card_id = mi_card_register.card_id
						WHERE activity.bran_BrandID = "'.$brand_id.'" 
						AND mi_card_register.status=1 
						AND mi_card.card_id ="'.$card_id.'"';

	if ($_SESSION['user_type_id_ses']==3) {
		
		$sql_activity .= ' AND mi_card_register.branch_id = "'.$_SESSION['user_branch_id'].'"';
	}

	# MEMBER REGISTER

	$sql_register = 'SELECT DISTINCT
						mb_member.member_id,
						mb_member.facebook_id,
						mb_member.facebook_name,
						mb_member.firstname,
						mb_member.lastname,
						mb_member.member_image,
						mb_member.email,
						mb_member.mobile,
						mb_member_register.member_card_code,

						((SELECT COUNT(mepe_MemberPrivlegeID) FROM member_privilege_trans WHERE memb_MemberID=mb_member.member_id AND card_CardID="'.$card_id.'")+
						(SELECT COUNT(meco_MemberCouponID) FROM member_coupon_trans WHERE memb_MemberID=mb_member.member_id AND card_CardID="'.$card_id.'")+
						(SELECT COUNT(meac_MemberActivityID) FROM member_activity_trans WHERE memb_MemberID=mb_member.member_id AND card_CardID="'.$card_id.'")) AS total

						FROM mb_member_register
						LEFT JOIN mb_member
						ON mb_member_register.member_id = mb_member.member_id
						WHERE mb_member_register.card_id = "'.$card_id.'"
						AND mb_member.member_id != 0
						ORDER BY total DESC';

	$oRes_register = $oDB->Query($sql_register)or die(mysql_error());
	$check_regis = $oDB->QueryOne($sql_register);


	$i=0;
		
	$privilege_id = array();

	$coupon_id = array();

	$hbd_id = array();

	$activity_id = array();




	# LEADER BOARD

	$table_card = "<table id='example' class='table table-bordered' style='background-color:white;'>
					<thead>
					<tr class='th_table' style='text-align:center'>
						<td colspan='2' rowspan='2' class='total_td'>
							Leader Board<span style='float:right'>Total Privilege Use&nbsp;</span>
						</td>";

	$total_use = 0;

	# PRIVILEGE

	$a = 0;

	$oRes_privilege = $oDB->Query($sql_privilege)or die(mysql_error());

	while ($axRow_privilege = $oRes_privilege->FetchRow(DBI_ASSOC)) {

		$a++;
	}

	if ($a != 0) { $table_card .= "<td colspan='".$a."'><b>Privilege</b></td>"; }


	# COUPON

	$a = 0;

	$oRes_coupon = $oDB->Query($sql_coupon)or die(mysql_error());

	while ($axRow_coupon = $oRes_coupon->FetchRow(DBI_ASSOC)) {

		$a++;
	}

	if ($a != 0) { $table_card .= "<td colspan='".$a."'><b>Coupon</b></td>"; }


	# HBD

	$a = 0;

	$oRes_hbd = $oDB->Query($sql_hbd)or die(mysql_error());

	while ($axRow_hbd = $oRes_hbd->FetchRow(DBI_ASSOC)) {

		$a++;
	}

	if ($a != 0) { $table_card .= "<td colspan='".$a."'><b>Birthday Coupon</b></td>"; }


	# ACTIVITY

	$a = 0;

	$oRes_activity = $oDB->Query($sql_activity)or die(mysql_error());

	while ($axRow_activity = $oRes_activity->FetchRow(DBI_ASSOC)) {

		$a++;
	}

	if ($a != 0) { $table_card .= "<td colspan='".$a."'><b>Activity</b></td>"; }


	$table_card .= "<td rowspan='2'><b>รวม</b></td>
					</tr>
					<tr class='th_table'>";


	# PRIVILEGE

	$a = 0;

	$oRes_privilege = $oDB->Query($sql_privilege)or die(mysql_error());

	while ($axRow_privilege = $oRes_privilege->FetchRow(DBI_ASSOC)) {
			
		$table_card .= "<td style='text-align:center;'>".$axRow_privilege['txt']." </td>";

		$privilege_id[$a]  = $axRow_privilege['id'];

		$a++;
	}


	# COUPON

	$a = 0;

	$oRes_coupon = $oDB->Query($sql_coupon)or die(mysql_error());

	while ($axRow_coupon = $oRes_coupon->FetchRow(DBI_ASSOC)) {
			
		$table_card .= "<td style='text-align:center;'>".$axRow_coupon['txt']." </td>";

		$coupon_id[$a]  = $axRow_coupon['id'];

		$a++;
	}


	# HBD

	$a = 0;

	$oRes_hbd = $oDB->Query($sql_hbd)or die(mysql_error());

	while ($axRow_hbd = $oRes_hbd->FetchRow(DBI_ASSOC)) {
			
		$table_card .= "<td style='text-align:center;'>".$axRow_hbd['txt']." </td>";

		$hbd_id[$a]  = $axRow_hbd['id'];

		$a++;
	}


	# ACTIVITY

	$a = 0;

	$oRes_activity = $oDB->Query($sql_activity)or die(mysql_error());

	while ($axRow_activity = $oRes_activity->FetchRow(DBI_ASSOC)) {
			
		$table_card .= "<td style='text-align:center;'>".$axRow_activity['txt']." </td>";

		$activity_id[$a]  = $axRow_activity['id'];

		$a++;
	}


	$table_card .= "</tr>
					<tr style='text-align:center;background-color:#EEE'>
						<td class='th_table'>Member</td>
						<td class='th_table'>Profile</td>";


	# PRIVILEGE

	$a = 0;

	$oRes_privilege = $oDB->Query($sql_privilege)or die(mysql_error());

	while ($axRow_privilege = $oRes_privilege->FetchRow(DBI_ASSOC)) {

		$total_priv = get_total_privilege_use($axRow_privilege['id'],"",$card_id,"","");

		if ($total_priv=="") {	$total_priv = 0;	}

		$total_use += $total_priv;
			
		$table_card .= "<td><b>".number_format($total_priv)."</b></td>";

		$privilege_id[$a]  = $axRow_privilege['id'];

		$a++;
	}


	# COUPON

	$a = 0;

	$oRes_coupon = $oDB->Query($sql_coupon)or die(mysql_error());

	while ($axRow_coupon = $oRes_coupon->FetchRow(DBI_ASSOC)) {

		$total_coup = get_total_coupon_use($axRow_coupon['id'],"",$card_id,"","");

		if ($total_coup=="") {	$total_coup = 0;	}

		$total_use += $total_coup;
			
		$table_card .= "<td><b>".number_format($total_coup)."</b></td>";

		$coupon_id[$a]  = $axRow_coupon['id'];

		$a++;
	}


	# HBD

	$a = 0;

	$oRes_hbd = $oDB->Query($sql_hbd)or die(mysql_error());

	while ($axRow_hbd = $oRes_hbd->FetchRow(DBI_ASSOC)) {

		$total_coup = get_total_coupon_use($axRow_hbd['id'],"",$card_id,"","");

		if ($total_coup=="") {	$total_coup = 0;	}

		$total_use += $total_coup;
			
		$table_card .= "<td><b>".number_format($total_coup)."</b></td>";

		$hbd_id[$a]  = $axRow_hbd['id'];

		$a++;
	}


	# ACTIVITY

	$a = 0;

	$oRes_activity = $oDB->Query($sql_activity)or die(mysql_error());

	while ($axRow_activity = $oRes_activity->FetchRow(DBI_ASSOC)) {

		$total_acti = get_total_activity_use($axRow_activity['id'],"",$card_id,"","");

		if ($total_acti=="") {	$total_acti = 0;	}

		$total_use += $total_acti;
			
		$table_card .= "<td><b>".number_format($total_acti)."</b></td>";

		$activity_id[$a]  = $axRow_activity['id'];

		$a++;
	}


	$table_card .= "<td style='text-align:center;'>".number_format($total_use)."</td>
					</tr>
					</thead>
					<tbody>";

	if ($check_regis) {

		while ($axRow = $oRes_register->FetchRow(DBI_ASSOC)) {

			$total_member = 0;

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

						if ($axRow['member_card_code']) {
								
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'].'<br>Member Card : '.$axRow['member_card_code'];

						} else {
								
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'];
						}

					} else {

						if ($axRow['member_card_code']) {
								
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>Member Card : '.$axRow['member_card_code'];
							
						} else {
								
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'];
						}
					}

				} else {

					if ($axRow['mobile']) {

						if ($axRow['member_card_code']) {
								
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile'].'<br>Member Card : '.$axRow['member_card_code'];

						} else {
								
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile'];
						}

					} else {

						if ($axRow['member_card_code']) {
								
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>Member Card : '.$axRow['member_card_code'];

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

			$table_card .= "<tr>
							<td style='text-align:center'>".$axRow['member_image']."</td>
							<td>".$member_name."</td>";

			for ($i=0; $i < count($privilege_id); $i++) {

				$member_priv = get_total_privilege_use($privilege_id[$i],"",$card_id,$axRow['member_id'],"");

				$total_member += $member_priv;

				$table_card .= "<td style='text-align:center'>".number_format($member_priv)."</td>";
			}

			for ($i=0; $i < count($coupon_id); $i++) {

				$member_coup = get_total_coupon_use($coupon_id[$i],"",$card_id,$axRow['member_id'],"");

				$total_member += $member_coup;

				$table_card .= "<td style='text-align:center'>".number_format($member_coup)."</td>";
			}

			for ($i=0; $i < count($hbd_id); $i++) {

				$member_hbd = get_total_coupon_use($hbd_id[$i],"",$card_id,$axRow['member_id'],"");

				$total_member += $member_hbd;

				$table_card .= "<td style='text-align:center'>".number_format($member_hbd)."</td>";
			}

			for ($i=0; $i < count($activity_id); $i++) {

				$member_acti = get_total_activity_use($activity_id[$i],"",$card_id,$axRow['member_id'],"");

				$total_member += $member_acti;

				$table_card .= "<td style='text-align:center'>".number_format($member_acti)."</td>";
			}

			$table_card .= "<td style='text-align:center;background-color:#F2F2F2'><b>".number_format($total_member)."</b></td></tr>";
		}
	}

	$table_card .= "</tbody>
					</table>";



$oTmp->assign('data', $asData);

$oTmp->assign('table_card', $table_card);

$oTmp->assign('path_upload_card', $path_upload_card);

$oTmp->assign('is_menu', 'is_analytics');

$oTmp->assign('content_file', 'analytics/top_card.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
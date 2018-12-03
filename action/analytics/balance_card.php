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

$path_upload_card = $_SESSION['path_upload_card'];

$path_upload_member = $_SESSION['path_upload_member'];

$oTmp = new TemplateEngine();

$oDB = new DBI();

if ($bDebug) {

	$oErr = new Tracker();

	$oDB->SetTracker($oErr);
}

$card_id = $_REQUEST['card_id'];

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
						privilege.priv_PrivilegeID as id,
						privilege.*
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
					coupon.coup_CouponID as id,
					coupon.*
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
					coupon.coup_CouponID as id,
					coupon.*
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
						activity.acti_ActivityID as id,
						activity.*
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


	# TABLE REPORT

	$table_card = "<table id='example' class='table table-striped table-bordered'>
					<thead>
					<tr class='th_table'>
						<td colspan='3'>Privilege</td>
						<td>Status</td>
						<td>Total</td>
						<td>Use</td>
						<td>Balance</td>
						<td>View</td>
					</tr>
					</thead>
					<tbody>";

	$total_use = 0;

	# PRIVILEGE

	$oRes_privilege = $oDB->Query($sql_privilege)or die(mysql_error());

	while ($privilege = $oRes_privilege->FetchRow(DBI_ASSOC)) {

		$total_priv = get_total_privilege_use($privilege['id'],"",$card_id,"","T");

		if ($total_priv=="") {	$total_priv = 0; }

		if ($privilege['priv_OneTimePer'] == '') { $privilege['priv_OneTimePer'] = '-'; }
		else { 

			$privilege['priv_OneTimePer'] = '1 Times Per '.$privilege['priv_OneTimePer'];
		}

		$period = '';

		if ($privilege['priv_StartDateSpecial'] != '0000-00-00' && $privilege['priv_EndDateSpecial'] != '0000-00-00') { 

			$period = DateOnly($privilege['priv_StartDateSpecial']).' - '.DateOnly($privilege['priv_EndDateSpecial']); 

		} else { $period = '-'; }
			
		$table_card .= "<tr>
							<td style='text-align:center' width='180px'><a href='../privilege/privilege.php'>
								<img src='../../upload/".$privilege['priv_ImagePath'].$privilege['priv_Image']."' class='image_border' height='100'/></a></td>
							<td style='text-align:right;line-height:170%' width='90px'>
						        <b>Name<br>
						        Type<br>
						        Limited Use<br>
						        Period</b></td>
						    <td style='line-height:170%'>
						        ".$privilege['txt']."<br>
						        Privilege<br>
						        ".$privilege['priv_OneTimePer']."<br>
						        ".$period."</td>
							<td style='text-align:center'>".$privilege['priv_Status']."</td>
							<td style='text-align:center'>-</td>
							<td style='text-align:center'>".$total_priv."</td>
							<td style='text-align:center'>-</td>
							<td style='text-align:center'><a href='balance_privilege.php?card_id=".$card_id."&id=".$privilege['id']."&type=p'><button type='button' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-eye-open' aria-hidden='true'></span></button></a></td>
						</tr>";
	}

	# COUPON

	$oRes_coupon = $oDB->Query($sql_coupon)or die(mysql_error());

	while ($coupon = $oRes_coupon->FetchRow(DBI_ASSOC)) {

		$total_coup = get_total_coupon_use($coupon['id'],"",$card_id,"","T");

		if ($total_coup=="") { $total_coup = 0; }

		$period = '';

		if ($coupon['coup_StartDateSpecial']!='0000-00-00' && $coupon['coup_EndDateSpecial']!='0000-00-00'){
								
			$period = DateOnly($coupon['coup_StartDateSpecial']).' - '.DateOnly($coupon['coup_EndDateSpecial']);
							
		} else if ($coupon['coup_Method']!='Dpd' && $coupon['coup_StartDate']!='0000-00-00' && $coupon['coup_EndDate']!='0000-00-00') {
								
			$period = DateOnly($coupon['coup_StartDate']).' - '.DateOnly($coupon['coup_EndDate']);

		} else { $period = '-'; }

		$remaining = '';

		$time_all = 0;

		if ($coupon['coup_Repetition']=='T') {

			$remaining = $coupon['coup_Qty'].' Per '.$coupon['coup_QtyPer'];
			$time_all = $coupon['coup_Qty'];

		} else { $remaining = '-'; }

		$time_member = 0;

		if ($coupon['coup_RepetitionMember']=='T') {

			if ($coupon['coup_QtyPerMember'] == 'Not Specific') {

				$per_member = 'Person';

			} else { $per_member = $coupon['coup_QtyPerMember']; }

			$total = $coupon['coup_QtyMember'].' Times Per '.$per_member;
			$time_member = $coupon['coup_QtyMember'];

		} else { $total = '-'; }

		if (($coupon['coup_Repetition']=='T' || $coupon['coup_RepetitionMember']=='T') && ($coupon['coup_QtyPer']=='Not Specific' || $coupon['coup_QtyPerMember']=='Not Specific')) {

			$last_total = 0;

			if ($time_all!=0 && $time_member!=0) {

				if ($time_member>$time_all) { $last_total = $time_all; }
				else { $last_total = $time_member; }

			} else {

				if ($time_all==0 && $time_member==0) { $last_total = "-"; }
				else if ($time_member==0) { $last_total = $time_all; $status_qty = "T"; }
				else { $last_total = $time_member; $status_qty = "T"; }
			}

			$total_use = 0;

			if ($last_total != 0) {
				
				$sql_count = 'SELECT COUNT(member_register_id)
								FROM mb_member_register
								WHERE card_id="'.$card_id.'"
								AND flag_del=""
								AND date_start<="'.date("Y-m-d").'"
								AND date_expire>"'.date("Y-m-d").'"';
				$count_card = $oDB->QueryOne($sql_count);

				if ($time_all!=0) {

					$total_use = $time_all;
					
				} else {

					$total_use = $count_card*$last_total;
				}
			}

			$total_balance = 0;

			if ($total_coup != 0 && $total_use != 0) {
				
				$total_balance = $total_use - $total_coup;
			}

		} else {

			$total_use = '-';
			$total_balance = '-';
		}
			
		$table_card .= "<tr>
							<td style='text-align:center' width='180px'><a href='../coupon/coupon.php'>
								<img src='../../upload/".$coupon['coup_ImagePath'].$coupon['coup_Image']."' class='image_border' height='100'></a></td>
							<td style='text-align:right;line-height:170%' width='90px'>
						        <b>Name<br>
						        Type<br>
						        Remaining<br>
						        Total Limited<br>
						        Period</b></td>
						    <td style='line-height:170%'>
						        ".$coupon['txt']."<br>
						        Coupon<br>
						        ".$remaining."<br>
						        ".$total."<br>
						        ".$period."</td>
							<td style='text-align:center'>".$coupon['coup_Status']."</td>
							<td style='text-align:center'>".$total_use."</td>
							<td style='text-align:center'>".$total_coup."</td>
							<td style='text-align:center'>".$total_balance."</td>
							<td style='text-align:center'><a href='balance_privilege.php?card_id=".$card_id."&id=".$coupon['id']."&type=c'><button type='button' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-eye-open' aria-hidden='true'></span></button></a></td>
						</tr>";
	}

	# HBD

	$oRes_hbd = $oDB->Query($sql_hbd)or die(mysql_error());

	while ($hbd = $oRes_hbd->FetchRow(DBI_ASSOC)) {

		$total_coup = get_total_coupon_use($hbd['id'],"",$card_id,"","T");

		if ($total_coup=="") { $total_coup = 0; }
			
		$table_card .= "<tr>
							<td style='text-align:center' width='180px'><a href='../coupon/coupon.php'>
								<img src='../../upload/".$hbd['coup_ImagePath'].$hbd['coup_Image']."' class='image_border' height='100'></a></td>
							<td style='text-align:right;line-height:170%' width='90px'>
						        <b>Name<br>
						        Type<br>
						        Period</b></td>
						    <td style='line-height:170%'>
						        ".$hbd['txt']."<br>
						        Birthday Coupon<br>
						        1 Times Per ".$hbd['coup_Method']."</td>
							<td style='text-align:center'>".$hbd['coup_Status']."</td>
							<td style='text-align:center'>-</td>
							<td style='text-align:center'>".$total_coup."</td>
							<td style='text-align:center'>-</td>
							<td style='text-align:center'><a href='balance_privilege.php?card_id=".$card_id."&id=".$coupon['id']."&type=c'><button type='button' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-eye-open' aria-hidden='true'></span></button></a></td>
						</tr>";
	}

	# ACTIVITY

	$oRes_activity = $oDB->Query($sql_activity)or die(mysql_error());

	while ($activity = $oRes_activity->FetchRow(DBI_ASSOC)) {

		$total_acti = get_total_activity_use($activity['id'],"",$card_id,"","T");

		if ($total_acti=="") { $total_acti = 0; }

		$activity_date = '';

		if ($activity['acti_StartDate']!='0000-00-00' && $activity['acti_EndDate']!='0000-00-00') {
								
			$activity_date = DateOnly($activity['acti_StartDate']).' - '.DateOnly($activity['acti_EndDate']);
							
		} else { $activity = '-'; }

		$time = '';

		if ($activity['acti_StartTime']!='00:00:00' && $activity['acti_EndTime']!='00:00:00') {
								
			$time = TimeOnly($activity['acti_StartTime']).' - '.TimeOnly($activity['acti_EndTime']);
							
		} else { $time = '-'; }

		$reservation = '';

		if ($activity['acti_StartDateReservation']!='0000-00-00' && $activity['acti_EndDateReservation']!='0000-00-00') {
								
			$reservation = DateOnly($activity['acti_StartDateReservation']).' - '.DateOnly($activity['acti_EndDateReservation']);
							
		} else { $reservation = '-'; }
			
		$table_card .= "<tr>
							<td style='text-align:center' width='180px'><a href='../activity/activity.php'>
								<img src='../../upload/".$activity['acti_ImagePath'].$activity['acti_Image']."' class='image_border' height='100'/></a></td>
						    <td style='text-align:right;line-height:170%' width='90px'>
						        <b>Name</b><br>
						        <b>Type</b><br>
						        <b>Activity</b><br>
						        <b>Time</b>
						        <b>Reservation</b><br></td>
						    <td style='line-height:170%'>
						        ".$activity['txt']."<br>
						        Activity<br>
						        ".$activity_date."<br>
						        ".$time."<br>
						        ".$reservation."</td>
							<td style='text-align:center'>".$activity['acti_Status']."</td>
							<td style='text-align:center'>-</td>
							<td style='text-align:center'>".$total_acti."</td>
							<td style='text-align:center'>-</td>
							<td style='text-align:center'><a href='balance_privilege.php?card_id=".$card_id."&id=".$activity['id']."&type=a'><button type='button' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-eye-open' aria-hidden='true'></span></button></a></td>
						</tr>";
	}

	$table_card .= "</tbody></table>";



$oTmp->assign('data', $asData);

$oTmp->assign('table_card', $table_card);

$oTmp->assign('path_upload_card', $path_upload_card);

$oTmp->assign('is_menu', 'is_analytics');

$oTmp->assign('content_file', 'analytics/balance_card.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());

}

//========================================//

?>

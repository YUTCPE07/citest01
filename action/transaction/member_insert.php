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

if (($_SESSION['role_action']['member_insert']['add'] != 1) || ($_SESSION['role_action']['member_insert']['edit'] != 1)) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];
$time_insert = date("Y-m-d H:i:s");


if ($Act == 'save') {

	$branch_id = trim_txt($_REQUEST['branch_id']);
	$card_id = trim_txt($_REQUEST['card_id']);
	$member_id = trim_txt($_REQUEST['member_id']);
	$register_id = trim_txt($_REQUEST['register_id']);

	$sql_brand = 'SELECT brand_id FROM mi_branch WHERE branch_id = "'.$branch_id.'"';
	$brand_id = $oDB->QueryOne($sql_brand);

	$sms_date = array();

	$u = 0;


	# HEAD CODE

	$sql_code = 'SELECT pre_code FROM mi_brand WHERE brand_id = "'.$brand_id.'"';
	$pre_code = $oDB->QueryOne($sql_code);

	if ($pre_code) {

		$length = 6;
	 	$randomString = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
	 	$head_code = $pre_code.$randomString;

	} else {

		$length = 8;
	 	$randomString = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
	 	$head_code = 'M'.$randomString;
	}

 	$x = 0;

 	while($x < 1) {

    	$member_use_privilege_id = "SELECT meth_MemberTransactionID FROM member_transaction_h
    								WHERE meth_MemberTransactionID = ".$head_code." LIMIT 1 ";

		$oRes = $oDB->Query($member_use_privilege_id);
		$rowCount = mysql_num_rows($oRes);

 		if($rowCount == 0) {

 	  		$x++;
 	  
 		} else {

			if ($pre_code) {

			 	$randomString = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
			 	$head_code = $pre_code.$randomString;

			} else {

			 	$randomString = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
			 	$head_code = 'M'.$randomString;
			}
 		}
 	}


	# HEAD ID
 
 	$strsqlInsertTransaction = "INSERT INTO 
					 			member_transaction_h(
					 			meth_MemberTransactionID,
					 			memb_MemberID,
					 			card_CardID,
					 			brnc_BranchID,
					 			meth_Platform,
					 			meth_Like,
					 			meth_Comment,
					 			meth_OTP,
					 			meth_CreatedDate,
					 			meth_UpdatedDate,
					 			meth_CreatedBy,
					 			meth_UpdatedBy)
					 			VALUES (
					 			'".$head_code."',
					 			'".$member_id."',
					 			'".$card_id."',
					 			'".$branch_id."',
					 			'Insert',
					 			'',
					 			'',
					 			'".$OTP."',
					 			'".$time_insert."',
					 			'".$time_insert."',
					 			'".$_SESSION['UID']."',
					 			'".$_SESSION['UID']."')";

	if ($oDB->QueryOne($strsqlInsertTransaction)) { $head_id = $oDB->meth_MemberTransactionHID; }

	$date_head = "0000-00-00 00:00:00";


 	# BRAND CARD NAME

 	$sql_name = 'SELECT mi_card.name AS card_name,
 						mi_brand.name AS brand_name
 					FROM mi_card
 					LEFT JOIN mi_brand
 					ON mi_brand.brand_id = mi_card.brand_id
 					WHERE mi_card.card_id="'.$card_id.'"';
 	$oRes_name = $oDB->Query($sql_name);
	$name = $oRes_name->FetchRow(DBI_ASSOC);


 	# OTP

 	$sql_otp = 'SELECT otp_pc
 					FROM mi_brand
 					LEFT JOIN mi_card
 					ON mi_card.brand_id = mi_brand.brand_id
 					WHERE mi_card.card_id="'.$card_id.'"';
 	$OTP = $oDB->QueryOne($sql_otp);

 	if ($OTP == 'T') { $OTP = mt_rand(1000,9999); } 
 	else { $OTP = ''; }


 	# REGISTER ID

 	if ($register_id) {

		$token = strtok($register_id,",");
		$register = array();
		$r = 0;
		
		while ($token !== false) {
		    			
		    $register[$r] =  $token;
		    $token = strtok(",");

		    $r++;
		}

		$count_card = $r;
	}


	# ALERT MESSAGE

	$alert_check = 0;


	# NORMAL

	foreach ($_POST['np'] as $privilege_id) {

		$time = $_REQUEST['nptime_'.$privilege_id];

		$member_date = $_REQUEST['date_'.$privilege_id];
		$member_month = $_REQUEST['month_'.$privilege_id];
		$member_year = $_REQUEST['year_'.$privilege_id];
		$member_hour = $_REQUEST['hour_'.$privilege_id];
		$member_min = $_REQUEST['min_'.$privilege_id];

		$date_use = $member_year.'-'.$member_month.'-'.$member_date.' '.$member_hour.':'.$member_min.':00';
		$date_check = $member_year.'-'.$member_month.'-'.$member_date;

		if ($date_head <= $date_use) { $date_head = $date_use; }

		$s = 'T';

		foreach ($sms_date[$u] as $date) {

			if ($date == $date_use) { $s = 'F'; }
		}

		if ($s == 'T') {

			$sms_date[$u] = $date_use;
			$u++;
		}

		if ($date_use < $time_insert) {

			# PRIVILEGE SQL

			$sql_priv = "SELECT DISTINCT privilege.priv_Name AS name, 
										privilege.priv_Image AS image, 
										privilege.priv_PrivilegeID AS id,
										privilege.priv_LimitUse,
										privilege.priv_OneTimePer,
										privilege.priv_StartDateSpecial,
										privilege.priv_EndDateSpecial,
										IF(privilege.priv_StartDateSpecial = '0000-00-00' 
										OR privilege.priv_EndDateSpecial = '0000-00-00', '-',
										CONCAT(DATE_FORMAT(privilege.priv_StartDateSpecial,'%d/%m/%Y'),' - ',
										DATE_FORMAT(privilege.priv_EndDateSpecial,'%d/%m/%Y'))) AS TextDate
						FROM mi_card_register
						LEFT JOIN privilege
						ON mi_card_register.privilege_id = privilege.priv_PrivilegeID
						WHERE mi_card_register.card_id=".$card_id."
						AND mi_card_register.status='0'
						AND mi_card_register.privilege_id!='0'
						AND privilege.priv_PrivilegeID=".$privilege_id;

			$oRes_priv = $oDB->Query($sql_priv);
			$axRow = $oRes_priv->FetchRow(DBI_ASSOC);

			if ($time!=0) {

				$r = 0;

				for ($i=0 ; $i<$time ; $i++) {

					$regis_id = $register[$r];

					# CHECK PRIVILEGE USE

					if ($regis_id) {

						if ($axRow['priv_LimitUse'] == "T") {

							switch ($axRow['priv_OneTimePer']) {
								case 'Daily' :
									$status_limit = priv_Daily($regis_id,$member_id,$privilege_id,$card_id,$date_check);
									break;
								case 'Weekly' :
									$status_limit = priv_Weekly($regis_id,$member_id,$privilege_id,$card_id,$date_check);
									break;
								default :
									$status_limit = priv_Monthly($regis_id,$member_id,$privilege_id,$card_id,$date_check);
									break;
							}

						} else { $status_limit = 'true'; }
									
						if ($axRow["TextDate"] == '-' ) {

							$status_use = "true";

						} else {

							$status_use = check_SpecialDate($axRow["priv_StartDateSpecial"],$axRow["priv_EndDateSpecial"],$date_check);
						}

						if ($status_use == "true" && $status_limit == "true") { 
								
							$id_use = UsePCAB($pre_code,$head_id,$regis_id,$member_id,$card_id,$branch_id,$date_use,$time_insert,$privilege_id,"MP","",$OTP);

							$alert_check++;
						
						} else { $r++; $i--;  }
					}
				}
			}
		}
	}


	foreach ($_POST['nc'] as $coupon_id) {

		$time = $_REQUEST['nctime_'.$coupon_id];

		$member_date = $_REQUEST['date_'.$coupon_id];
		$member_month = $_REQUEST['month_'.$coupon_id];
		$member_year = $_REQUEST['year_'.$coupon_id];
		$member_hour = $_REQUEST['hour_'.$coupon_id];
		$member_min = $_REQUEST['min_'.$coupon_id];

		$date_use = $member_year.'-'.$member_month.'-'.$member_date.' '.$member_hour.':'.$member_min.':00';
		$date_check = $member_year.'-'.$member_month.'-'.$member_date;

		if ($date_head <= $date_use) { $date_head = $date_use; }

		$s = 'T';

		foreach ($sms_date[$u] as $date) {

			if ($date == $date_use) { $s = 'F'; }
		}

		if ($s == 'T') {

			$sms_date[$u] = $date_use;
			$u++;
		}

		if ($date_use < $time_insert) {

			# COUPON SQL

			$sql_coup = "SELECT DISTINCT coupon.coup_Name AS name, 
										coupon.coup_Image AS image, 
										coupon.coup_CouponID AS id,
										coupon.coup_QtyPerMember,
										coupon.coup_RepetitionMember,
										coupon.coup_QtyMember,
										coupon.coup_QtyPerMemberData,
										coupon.coup_SpecialPeriodType,
										coupon.coup_QtyPer,
										coupon.coup_Repetition,
										coupon.coup_Qty,
										coupon.coup_QtyPerData,
										coupon.coup_TotalQty,
										coupon.coup_Method,
										coupon.coup_StartDate,
										coupon.coup_EndDate,
										coupon.coup_StartDateSpecial,
										coupon.coup_EndDateSpecial,
										IF(coupon.coup_StartDate = '0000-00-00' OR coupon.coup_EndDate = '0000-00-00', 
										'-', CONCAT(DATE_FORMAT(coupon.coup_StartDate,'%d/%m/%Y'),' - ',
										DATE_FORMAT(coupon.coup_EndDate,'%d/%m/%Y'))) as TextDate,
										IF(coupon.coup_StartDateSpecial = '0000-00-00' 
										OR coupon.coup_EndDateSpecial = '0000-00-00', 
										'-', CONCAT(DATE_FORMAT(coupon.coup_StartDateSpecial,'%d/%m/%Y'),' - ',
										DATE_FORMAT(coupon.coup_EndDateSpecial,'%d/%m/%Y'))) as TextDateSpecial
						FROM mi_card_register
						LEFT JOIN coupon
						ON mi_card_register.coupon_id = coupon.coup_CouponID
						WHERE mi_card_register.card_id='".$card_id."'
						AND mi_card_register.status='0'
						AND mi_card_register.coupon_id!='0'
						AND coupon.coup_Birthday!='T'
						AND coupon.coup_CouponID='".$coupon_id."'";

			$oRes_coup = $oDB->Query($sql_coup);
			$axRow = $oRes_coup->FetchRow(DBI_ASSOC);

			# CHECK USE

			if ($time!=0) {

				$r = 0;

				for ($i=0 ; $i<$time ; $i++) {

					$regis_id = $register[$r];

					if ($regis_id) {

						if ($axRow['coup_Repetition'] == "T") {

							switch ($axRow['coup_QtyPer']) {
									
								case 'Daily' :
									// $total = CoupRepetitionDaily('', $coupon_id, $axRow['coup_Qty'], $card_id, $date_use);
									// if ($total == "0") { $status_Repetition = "false"; }
									$status_Repetition = "true";
									break;
								case 'Weekly' :
									$arrayName = CoupRepetitionWeekly('','',$coupon_id,$axRow['coup_Qty'],$card_id,$axRow['coup_QtyPerData'], $date_check);
									// $total = $arrayName["total"];
									// if ($total == "0") { $status_Repetition = "false"; }
									$status_Repetition = $arrayName["status"];
									break;
								case 'Monthly' :
									$arrayName = CoupRepetitionMonth('','',$coupon_id,$axRow['coup_Qty'],$card_id,$axRow['coup_QtyPerData'],$date_check);
									// $total = $arrayName["total"];
									// if ($total == "0") { $status_Repetition = "false"; }
									$status_Repetition = $arrayName["status"];
									break;
								case 'Not' :
									$total = CoupRepetitionNotSpecific('', $coupon_id, $axRow['coup_Qty'], $card_id);
									if ($total == "0") { $status_Repetition = "false"; }
									else { $status_Repetition = "true"; }
									
									break;
								default :
									break;
							}			
						}

						$status_all = "true";

						if ($axRow['coup_RepetitionMember'] == "T") {

							switch ($axRow['coup_QtyPerMember']) {

								case 'Daily' :
									$totalMember = CoupRepetitionDaily($regis_id,$member_id,$coupon_id,$axRow['coup_QtyMember'],$card_id,$date_check);
									if ($totalMember == "0") { $status_all = "false"; }
									break;
								case 'Weekly' :
									$arrayName = CoupRepetitionWeekly($regis_id,$member_id,$coupon_id,$axRow['coup_QtyMember'],$card_id,$axRow['coup_QtyPerMemberData'],$date_check);
									$totalMember = $arrayName["total"];
									if ($totalMember == "0") { $status_all = "false"; } 
									else { $status_all = $arrayName["status"]; }
									break;
								case 'Monthly' :
									$arrayName = CoupRepetitionMonth($regis_id,$member_id,$coupon_id,$axRow['coup_QtyMember'],$card_id,$axRow['coup_QtyPerMemberData'],$date_check);
									$totalMember = $arrayName["total"];
									if ($totalMember == "0") { $status_all = "false"; }
									else { $status_all = $arrayName["status"]; }
									break;
								case 'Not Specific' :
									$totalMember = CoupRepetitionNotSpecific($regis_id,$member_id,$coupon_id,$axRow['coup_QtyMember'],$card_id);
									if ($totalMember == "0") { $status_all = "false"; }
									break;
								default :
									break;
							}
						}

						if ($axRow['coup_RepetitionMember'] != "T" && $axRow['coup_Repetition'] != "T") {
								
							$check_use = check_UseCoupon($regis_id, $member_id, $coupon_id, $card_id);
							if ($check_use == "false") { $status_all = "true"; } 
							else { $status_all = "false"; }
							
						} else {

							if ($status_Repetition == "false" || $status_all == "false") { $status_all = "false"; }
						}
						
						if ($axRow["TextDate"] == "-") {
								
						} else {
								
							if ($axRow['coup_StartDate'] < $date_check && $axRow['coup_EndDate'] > $date_check && $status_all == "true") {
									
								$status_all = "true";
							
							} else { $status_all = "false"; }
						}
							
						if ($axRow["TextDateSpecial"] != "-") {
								
							if ($axRow['coup_StartDateSpecial'] < $date_check && $axRow['coup_EndDateSpecial'] > $date_check && $status_all == "true") {
									
								$status_all = "true";
							
							} else { $status_all = "false"; }
						}

						if ($status_all == "true") { 
										
							$id_use = UsePCAB($pre_code,$head_id,$regis_id,$member_id,$card_id,$branch_id,$date_use,$time_insert,$coupon_id,"MC","",$OTP);

							$alert_check++;
						
						} else { $r++; $i--;  }
					}
				}
			}
		}
	}




	foreach ($_POST['nh'] as $hbd_id) {

		$time = $_REQUEST['nhtime_'.$coupon_id];

		if ($time == '') { $time = 1; }

		$member_date = $_REQUEST['date_'.$hbd_id];
		$member_month = $_REQUEST['month_'.$hbd_id];
		$member_year = $_REQUEST['year_'.$hbd_id];
		$member_hour = $_REQUEST['hour_'.$hbd_id];
		$member_min = $_REQUEST['min_'.$hbd_id];

		$date_use = $member_year.'-'.$member_month.'-'.$member_date.' '.$member_hour.':'.$member_min.':00';
		$date_check = $member_year.'-'.$member_month.'-'.$member_date;

		if ($date_head <= $date_use) { $date_head = $date_use; }

		$s = 'T';

		foreach ($sms_date[$u] as $date) {

			if ($date == $date_use) { $s = 'F'; }
		}

		if ($s == 'T') {

			$sms_date[$u] = $date_use;
			$u++;
		}

		if ($date_use < $time_insert) {

			# HBD SQL

			$sql_hbd = "SELECT DISTINCT coupon.coup_Name AS name, 
										coupon.coup_Image AS image, 
										coupon.coup_CouponID AS id,
										coupon.coup_Method
						FROM mi_card_register
						LEFT JOIN coupon
						ON mi_card_register.coupon_id = coupon.coup_CouponID
						WHERE mi_card_register.card_id='".$card_id."'
						AND mi_card_register.status='0'
						AND mi_card_register.coupon_id!='0'
						AND coupon.coup_Birthday='T'
						AND coupon.coup_CouponID='".$hbd_id."'";

			$oRes_hbd = $oDB->Query($sql_hbd);
			$axRow = $oRes_hbd->FetchRow(DBI_ASSOC);

			# CHECK HBD

			if ($time != 0) {

				$r = 0;

				for ($i=0 ; $i<$time ; $i++) {

					$regis_id = $register[$r];

					if ($regis_id) {

						$check_use = check_YearBirthday($regis_id,$member_id,$hbd_id,$card_id,$member_year);

						if ($check_use == "false") $status_all = "true";
						else $status_all = "false";

						if ($status_all == "true") {
							
							$birthday = birthday($member_id);

							if ($birthday != "") {
										
								switch ($axRow['coup_Method']) {
									case 'Day' :
										$status_all = birthdayToday($birthday,$member_year,$date_check);
										break;
									case 'Week' :
										$status_all = birthdayWeek($birthday,$member_year,$date_check);
										break;
									case 'Month' :
										$status_all = birthdayMonth($birthday,$date_check);
										break;
									default :
										$status_all = "false";
										break;
								}

							} else { $status_all = "false"; }
						}

						if ($status_all == "true") {

							$id_use = UsePCAB($pre_code,$head_id,$regis_id,$member_id,$card_id,$branch_id,$date_use,$time_insert,$hbd_id,"MC","",$OTP);

							$alert_check++;

						} else { $r++; $i--; }
					}
				}
			}
		}
	}



	

	foreach ($_POST['na'] as $activity_id) {

		$member_date = $_REQUEST['date_'.$activity_id];
		$member_month = $_REQUEST['month_'.$activity_id];
		$member_year = $_REQUEST['year_'.$activity_id];
		$member_hour = $_REQUEST['hour_'.$activity_id];
		$member_min = $_REQUEST['min_'.$activity_id];

		$date_use = $member_year.'-'.$member_month.'-'.$member_date.' '.$member_hour.':'.$member_min.':00';
		$date_check = $member_year.'-'.$member_month.'-'.$member_date;

		if ($date_head <= $date_use) { $date_head = $date_use; }

		$s = 'T';

		foreach ($sms_date[$u] as $date) {

			if ($date == $date_use) { $s = 'F'; }
		}

		if ($s == 'T') {

			$sms_date[$u] = $date_use;
			$u++;
		}

		if ($date_use < $time_insert) {

			# ACTIVITY SQL

			$sql_acti = "SELECT DISTINCT activity.acti_Name AS name, 
										activity.acti_Image AS image, 
										activity.acti_ActivityID AS id,
										activity.acti_SpecialPeriodType,
										IF(activity.acti_StartDate = '0000-00-00' OR activity.acti_EndDate = '0000-00-00', '-',CONCAT(DATE_FORMAT(activity.acti_StartDate,'%d/%m/%Y'),' - ',DATE_FORMAT(activity.acti_EndDate,'%d/%m/%Y'))) as TextDate,
										activity.acti_StartDateSpecial, 
										activity.acti_EndDateSpecial, 
										activity.acti_StartDate,
										activity.acti_EndDate,
										activity.acti_Method,
										activity.acti_StartTime,
										activity.acti_EndTime,
										activity.acti_QtyPerMember,
										activity.acti_QtyMember,
										activity.acti_RepetitionMember,
										activity.acti_QtyPerMemberData,
										activity.acti_QtyPer,
										activity.acti_Qty,
										activity.acti_MaxQty,
										activity.acti_Repetition,
										activity.acti_QtyPerData,
										activity.acti_TotalQty,
										activity.acti_Reservation,
										activity.acti_StartDateReservation,
										activity.acti_EndDateReservation,
										activity.acti_StartTimeReservation,
										activity.acti_EndTimeReservation
						FROM mi_card_register
						LEFT JOIN activity
						ON mi_card_register.activity_id = activity.acti_ActivityID
						WHERE mi_card_register.card_id='".$card_id."'
						AND mi_card_register.status='0'
						AND mi_card_register.activity_id!='0'
						AND activity.acti_ActivityID='".$activity_id."'";

			$oRes_acti = $oDB->Query($sql_acti);
			$axRow = $oRes_acti->FetchRow(DBI_ASSOC);

			# CHECK ACTIVITY USE

			$r = 0;

			for ($i=0 ; $i<$time ; $i++) {

				$regis_id = $register[$r];

				if ($regis_id) {

					if ($axRow['acti_Repetition'] == "T") {

						switch ($acti_QtyPer) {
								
							case 'Daily' :
								// $total = ActiRepetitionDaily('', $activity_id, $axRow['acti_Qty'], $card_id, $date_use);	
								// if ($total == "0") { $status_Repetition = "false"; }
								$status_Repetition = "true";
								break;
							case 'Weekly' :
								$arrayName = ActiRepetitionWeekly('','',$activity_id,$axRow['acti_Qty'],$card_id,$axRow['acti_QtyPerData'],$date_check);
								// $total = $arrayName["total"];
								// if ($total == "0") { $status_Repetition = "false"; }
								$status_Repetition = $arrayName["status"];	
								break;
							case 'Monthly' :
								$arrayName = ActiRepetitionMonth('','',$activity_id,$axRow['acti_Qty'],$card_id,$axRow['acti_QtyPerData'],$date_check);
								// $total = $arrayName["total"];
								// if ($total == "0") { $status_Repetition = "false"; }
								$status_Repetition = $arrayName["status"];
								break;
							case '' :
								// $total = ActiRepetitionNotSpecific('', $activity_id, $axRow['acti_Qty'], $card_id, $axRow['acti_QtyPerData'], $date_use);
								// if ($total == "0") { $status_Repetition = "false"; }
								$status_Repetition = "true";		
								break;
							default :
								break;
						}
					}

					switch ($axRow['acti_QtyPerMember']) {

						case 'Daily' :
							$totalMember = ActiRepetitionDaily($regis_id,$member_id,$activity_id,$axRow['acti_QtyMember'],$card_id,$date_check);
							if ($totalMember == "0") { $status_all = "false"; }	
							break;
						case 'Weekly' :
							$arrayName = ActiRepetitionWeekly($regis_id,$member_id,$activity_id,$axRow['acti_QtyMember'],$card_id,$axRow['acti_QtyPerMemberData'],$date_check);
							$totalMember = $arrayName["total"];
							if ($totalMember == "0") { $status_all = "false"; }	
							else { $status_all = $arrayName["status"]; }
							break;
						case 'Monthly' :
							$arrayName = ActiRepetitionMonth($regis_id,$member_id,$activity_id,$axRow['acti_QtyMember'],$card_id,$axRow['acti_QtyPerMemberData'],$date_check);
							$totalMember = $arrayName["total"];
							if ($totalMember == "0") { $status_all = "false"; }	
							else { $status_all = $arrayName["status"]; }
							break;
						case 'Not Specific' :
							$totalMember = ActiRepetitionNotSpecific($regis_id,$member_id,$activity_id,$axRow['acti_QtyMember'],$card_id,$axRow['acti_QtyPerMemberData'],$date_check);
							if ($totalMember == "0") { $status_all = "false"; }	
							break;
						default :
							$check_use = check_UseActivity($regis_id,$member_id,$activity_id,$card_id);
							if ($check_use == "false") { $status_all = "true"; } 
							else { $status_all = "false"; }
							break;
					}

					if ($status_Repetition == "false" || $status_all == "false") { $status_all = "false"; }


					# RESERVATION
								
					if ($status_all == "true") {
								
						if ($acti_Reservation == "T") {
									
							$status_Reservation = check_DateActivity($axRow['acti_StartDateReservation'],$axRow['acti_EndDateReservation'],$date_check);

							if ($status_Reservation == "true") {

								# RESERVATION

								$id_use = UsePCAB($pre_code,$head_id,$regis_id,$member_id,$card_id,$branch_id,$date_use,$time_insert,$activity_id,"MA","",$OTP);

								$alert_check++;

							} else { $r++; $i--; }

						} else {

							# USE
									
							$status_Use = check_DateActivity($axRow['acti_StartDate'],$axRow['acti_EndDate'],$date_check);

							if ($status_Use == 'true') {

								$id_use = UsePCAB($pre_code,$head_id,$regis_id,$member_id,$card_id,$branch_id,$date_use,$time_insert,$activity_id,"MA","Complete",$OTP);

								$alert_check++;

							} else { $r++; $i--; }
						}

					} else {
									
						$reservation = CheckReservation($regis_id,$acti_ActivityID,$member_id,$card_id);
								
						if ($reservation["meac_MemberActivityID"] != "" && $reservation["meac_Status"] != "Complete") {

							# USE
									
							$status_Use = check_DateActivity($axRow['acti_StartDate'],$axRow['acti_EndDate'],$date_check);

							if ($status_Use == 'true') {

								$id_use = UseActivity($reservation["meac_MemberActivityID"],$date_use,$time_insert);

								$alert_check++;

							} else { $r++; $i--;  }
						}
					}
				}
			}
		}
	}




	# POINT

	foreach ($_POST['pp'] as $privilege_id) {

		$member_date = $_REQUEST['date_'.$privilege_id];
		$member_month = $_REQUEST['month_'.$privilege_id];
		$member_year = $_REQUEST['year_'.$privilege_id];
		$member_hour = $_REQUEST['hour_'.$privilege_id];
		$member_min = $_REQUEST['min_'.$privilege_id];

		$date_use = $member_year.'-'.$member_month.'-'.$member_date.' '.$member_hour.':'.$member_min.':00';
		$date_check = $member_year.'-'.$member_month.'-'.$member_date;

		if ($date_head <= $date_use) { $date_head = $date_use; }

		$s = 'T';

		foreach ($sms_date[$u] as $date) {

			if ($date == $date_use) { $s = 'F'; }
		}

		if ($s == 'T') {

			$sms_date[$u] = $date_use;
			$u++;
		}

		$recieve = $_REQUEST['precieve_'.$privilege_id];
		$amount = $_REQUEST['pamount_'.$privilege_id];

		if ($date_use < $time_insert) {

			# PRIVILEGE SQL

			$sql_priv = "SELECT DISTINCT privilege.priv_Name AS name, 
										privilege.priv_Image AS image, 
										privilege.priv_PrivilegeID AS id,
										privilege.priv_LimitUse,
										privilege.priv_OneTimePer,
										privilege.priv_StartDateSpecial,
										privilege.priv_EndDateSpecial,
										IF(privilege.priv_StartDateSpecial = '0000-00-00' 
										OR privilege.priv_EndDateSpecial = '0000-00-00', '-',
										CONCAT(DATE_FORMAT(privilege.priv_StartDateSpecial,'%d/%m/%Y'),' - ',
										DATE_FORMAT(privilege.priv_EndDateSpecial,'%d/%m/%Y'))) AS TextDate,
										privilege.priv_MotivationID
						FROM mi_card_register
						LEFT JOIN privilege
						ON mi_card_register.privilege_id = privilege.priv_PrivilegeID
						WHERE mi_card_register.card_id=".$card_id."
						AND mi_card_register.status='0'
						AND mi_card_register.privilege_id!='0'
						AND privilege.priv_PrivilegeID=".$privilege_id;

			$oRes_priv = $oDB->Query($sql_priv);
			$axRow = $oRes_priv->FetchRow(DBI_ASSOC);

			# CHECK PRIVILEGE USE

			$time = 1;

			$r = 0;

			for ($i=0 ; $i<$time ; $i++) {

				$regis_id = $register[$r];

				if ($regis_id) {

					if ($axRow['priv_LimitUse'] == "T") {

						switch ($axRow['priv_OneTimePer']) {
							case 'Daily' :
								$status_limit = priv_Daily($regis_id,$member_id,$privilege_id,$card_id,$date_check);
								break;
							case 'Weekly' :
								$status_limit = priv_Weekly($regis_id,$member_id,$privilege_id,$card_id,$date_check);
								break;
							default :
								$status_limit = priv_Monthly($regis_id,$member_id,$privilege_id,$card_id,$date_check);
								break;
						}

					} else { $status_limit = 'true'; }
										
					if ($axRow["TextDate"] == '-' ) {

						$status_use = "true";

					} else {

						$status_use = check_SpecialDate($axRow["priv_StartDateSpecial"],$axRow["priv_EndDateSpecial"],$date_check);
					}

					if ($status_use == "true" && $status_limit == "true") {

						$id_use = UsePCAB($pre_code,$head_id,$regis_id,$member_id,$card_id,$branch_id,$date_use,$time_insert,$privilege_id,"MP","",$OTP);

						insert_point('p',$privilege_id,$id_use,$branch_id,$card_id,$regis_id,$recieve,$amount,$date_use,$time_insert,$axRow['priv_MotivationID']);

						$alert_check++;

					} else { $r++; $i--;  }
				}
			}
		}
	}



	foreach ($_POST['pc'] as $coupon_id) {

		$member_date = $_REQUEST['date_'.$coupon_id];
		$member_month = $_REQUEST['month_'.$coupon_id];
		$member_year = $_REQUEST['year_'.$coupon_id];
		$member_hour = $_REQUEST['hour_'.$coupon_id];
		$member_min = $_REQUEST['min_'.$coupon_id];

		$date_use = $member_year.'-'.$member_month.'-'.$member_date.' '.$member_hour.':'.$member_min.':00';
		$date_check = $member_year.'-'.$member_month.'-'.$member_date;

		if ($date_head <= $date_use) { $date_head = $date_use; }

		$s = 'T';

		foreach ($sms_date[$u] as $date) {

			if ($date == $date_use) { $s = 'F'; }
		}

		if ($s == 'T') {

			$sms_date[$u] = $date_use;
			$u++;
		}

		$recieve = $_REQUEST['crecieve_'.$coupon_id];
		$amount = $_REQUEST['camount_'.$coupon_id];

		if ($date_use < $time_insert) {

			# COUPON SQL

			$sql_coup = "SELECT DISTINCT coupon.coup_Name AS name, 
										coupon.coup_Image AS image, 
										coupon.coup_CouponID AS id,
										coupon.coup_QtyPerMember,
										coupon.coup_RepetitionMember,
										coupon.coup_QtyMember,
										coupon.coup_QtyPerMemberData,
										coupon.coup_SpecialPeriodType,
										coupon.coup_QtyPer,
										coupon.coup_Repetition,
										coupon.coup_Qty,
										coupon.coup_QtyPerData,
										coupon.coup_TotalQty,
										coupon.coup_Method,
										coupon.coup_StartDate,
										coupon.coup_EndDate,
										coupon.coup_StartDateSpecial,
										coupon.coup_EndDateSpecial,
										IF(coupon.coup_StartDate = '0000-00-00' OR coupon.coup_EndDate = '0000-00-00', 
										'-', CONCAT(DATE_FORMAT(coupon.coup_StartDate,'%d/%m/%Y'),' - ',
										DATE_FORMAT(coupon.coup_EndDate,'%d/%m/%Y'))) as TextDate,
										IF(coupon.coup_StartDateSpecial = '0000-00-00' 
										OR coupon.coup_EndDateSpecial = '0000-00-00', 
										'-', CONCAT(DATE_FORMAT(coupon.coup_StartDateSpecial,'%d/%m/%Y'),' - ',
										DATE_FORMAT(coupon.coup_EndDateSpecial,'%d/%m/%Y'))) as TextDateSpecial,
										coupon.coup_MotivationID
						FROM mi_card_register
						LEFT JOIN coupon
						ON mi_card_register.coupon_id = coupon.coup_CouponID
						WHERE mi_card_register.card_id='".$card_id."'
						AND mi_card_register.status='0'
						AND mi_card_register.coupon_id!='0'
						AND coupon.coup_Birthday!='T'
						AND coupon.coup_CouponID='".$coupon_id."'";

			$oRes_coup = $oDB->Query($sql_coup);
			$axRow = $oRes_coup->FetchRow(DBI_ASSOC);

			# CHECK USE

			$time = 1;

			$r = 0;

			for ($i=0 ; $i<$time ; $i++) {

				$regis_id = $register[$r];

				if ($regis_id) {

					$total = "0";
					$totalMember = "0";

					if ($axRow['coup_Repetition'] == "T") {

						switch ($axRow['coup_QtyPer']) {
										
							case 'Daily' :
								// $total = CoupRepetitionDaily('', $coupon_id, $axRow['coup_Qty'], $card_id, $date_use);
								// if ($total == "0") { $status_Repetition = "false"; }
								$status_Repetition = "true";
								break;
							case 'Weekly' :
								$arrayName = CoupRepetitionWeekly('','',$coupon_id,$axRow['coup_Qty'],$card_id,$axRow['coup_QtyPerData'],$date_check);
								// $total = $arrayName["total"];
								// if ($total == "0") { $status_Repetition = "false"; }
								$status_Repetition = $arrayName["status"];
								break;
							case 'Monthly' :
								$arrayName = CoupRepetitionMonth('','',$coupon_id,$axRow['coup_Qty'],$card_id,$axRow['coup_QtyPerData'],$date_check);
								// $total = $arrayName["total"];
								// if ($total == "0") { $status_Repetition = "false"; }
								$status_Repetition = $arrayName["status"];
								break;
							case 'Not' :
								$total = CoupRepetitionNotSpecific('', $coupon_id, $axRow['coup_Qty'], $card_id);
								if ($total == "0") { $status_Repetition = "false"; }
								$status_Repetition = "true";
								break;
							default :
								break;
						}			
					}

					if ($axRow['coup_RepetitionMember'] == "T") {

						switch ($axRow['coup_QtyPerMember']) {

							case 'Daily' :
								$totalMember = CoupRepetitionDaily($regis_id,$member_id,$coupon_id,$axRow['coup_QtyMember'],$card_id,$date_check);
								if ($totalMember == "0") { $status_all = "false"; }
								break;
							case 'Weekly' :
								$arrayName = CoupRepetitionWeekly($regis_id,$member_id,$coupon_id,$axRow['coup_QtyMember'], $card_id, $axRow['coup_QtyPerMemberData'], $date_check);
								$totalMember = $arrayName["total"];
								if ($totalMember == "0") { $status_all = "false"; } 
								else { $status_all = $arrayName["status"]; }
								break;
							case 'Monthly' :
								$arrayName = CoupRepetitionMonth($regis_id,$member_id,$coupon_id,$axRow['coup_QtyMember'],$card_id,$axRow['coup_QtyPerMemberData'],$date_check);
								$totalMember = $arrayName["total"];
								if ($totalMember == "0") { $status_all = "false"; }
								else { $status_all = $arrayName["status"]; }
								break;
							case 'Not Specific' :
								$totalMember = CoupRepetitionNotSpecific($regis_id,$member_id,$coupon_id,$axRow['coup_QtyMember'],$card_id);
								if ($totalMember == "0") { $status_all = "false"; }
								break;
							default :
								$check_use = check_UseCoupon($regis_id,$member_id,$coupon_id,$card_id);
								if ($check_use == "false" && $status_all == "true") { $status_all = "true"; } 
								else { $status_all = "false"; }
								break;
						}
					}

					if ($axRow['coup_RepetitionMember'] != "T" && $axRow['coup_Repetition'] != "T") {
									
						$check_use = check_UseCoupon($member_id, $coupon_id, $card_id);
						if ($check_use == "false") { $status_all = "true"; } 
						else { $status_all = "false"; }
								
					} else {

						if ($status_Repetition == "false" || $status_all == "false") { $status_all = "false"; }
					}
							
					if ($axRow["TextDate"] == "-") {
									
					} else {
									
						if ($axRow['coup_StartDate'] < $date_check && $axRow['coup_EndDate'] > $date_check && $status_all == "true") {
										
							$status_all = "true";
						}
					}
								
					if ($axRow["TextDateSpecial"] == "-") {
									
					} else {
									
						if ($axRow['coup_StartDateSpecial'] < $date_check && $axRow['coup_EndDateSpecial'] > $date_check &&status_all == "true") {
										
							$status_all = "true";
						}
					}

					if ($status_all == "true") { 

						$id_use = UsePCAB($pre_code,$head_id,$regis_id, $member_id,$card_id,$branch_id,$date_use,$time_insert,$coupon_id,"MC","",$OTP);

						insert_point('c',$coupon_id,$id_use,$branch_id,$card_id,$regis_id,$recieve,$amount,$date_use,$time_insert,$axRow['coup_MotivationID']);

						$alert_check++;

					} else { $r++; $i--;  }
				}
			}
		}
	}
	



	foreach ($_POST['ph'] as $hbd_id) {

		$recieve = $_REQUEST['hrecieve_'.$hbd_id];
		$amount = $_REQUEST['hamount_'.$hbd_id];

		$member_date = $_REQUEST['date_'.$hbd_id];
		$member_month = $_REQUEST['month_'.$hbd_id];
		$member_year = $_REQUEST['year_'.$hbd_id];
		$member_hour = $_REQUEST['hour_'.$hbd_id];
		$member_min = $_REQUEST['min_'.$hbd_id];

		$date_use = $member_year.'-'.$member_month.'-'.$member_date.' '.$member_hour.':'.$member_min.':00';
		$date_check = $member_year.'-'.$member_month.'-'.$member_date;

		if ($date_head <= $date_use) { $date_head = $date_use; }

		$s = 'T';

		foreach ($sms_date[$u] as $date) {

			if ($date == $date_use) { $s = 'F'; }
		}

		if ($s == 'T') {

			$sms_date[$u] = $date_use;
			$u++;
		}

		if ($date_use < $time_insert) {

			# HBD SQL

			$sql_hbd = "SELECT DISTINCT coupon.coup_Name AS name, 
										coupon.coup_Image AS image, 
										coupon.coup_CouponID AS id,
										coupon.coup_Method,
										coupon.coup_MotivationID
						FROM mi_card_register
						LEFT JOIN coupon
						ON mi_card_register.coupon_id = coupon.coup_CouponID
						WHERE mi_card_register.card_id='".$card_id."'
						AND mi_card_register.status='0'
						AND mi_card_register.coupon_id!='0'
						AND coupon.coup_Birthday='T'
						AND coupon.coup_CouponID='".$hbd_id."'";

			$oRes_hbd = $oDB->Query($sql_hbd);
			$axRow = $oRes_hbd->FetchRow(DBI_ASSOC);

			# CHECK HBD

			$r = 0;

			for ($i=0 ; $i<$time ; $i++) {

				$regis_id = $register[$r];

				if ($regis_id) {

					$check_use = check_YearBirthday($regis_id,$member_id,$hbd_id,$card_id,$member_year);

					if ($check_use == "false") $status_all = "true";
					else $status_all = "false";

					if ($status_all == "true") {
						
						$birthday = birthday($member_id);

						if ($birthday != "") {
									
							switch ($axRow['coup_Method']) {
										
								case 'Day' :
									$status_all = birthdayToday($birthday,$member_year,$date_check);
									break;
								case 'Week' :
									$status_all = birthdayWeek($birthday,$member_year,$date_check);
									break;
								case 'Month' :
									$status_all = birthdayMonth($birthday,$member_month);
									break;
								default :
									$status_all = "false";
									break;
							}

						} else { $status_all = "false"; }
					}

					if ($status_all == "true") {

						$id_use = UsePCAB($pre_code,$head_id,$regis_id,$member_id,$card_id,$branch_id,$date_use,$time_insert,$hbd_id,"MC","",$OTP);

						insert_point('h',$hbd_id,$id_use,$branch_id,$card_id,$regis_id,$recieve,$amount,$date_use,$time_insert,$axRow['coup_MotivationID']);

						$alert_check++;

					} else { $r++; $i--;  }
				}
			}
		}
	}



	foreach ($_POST['pa'] as $activity_id) {

		$member_date = $_REQUEST['date_'.$activity_id];
		$member_month = $_REQUEST['month_'.$activity_id];
		$member_year = $_REQUEST['year_'.$activity_id];
		$member_hour = $_REQUEST['hour_'.$activity_id];
		$member_min = $_REQUEST['min_'.$activity_id];

		$date_use = $member_year.'-'.$member_month.'-'.$member_date.' '.$member_hour.':'.$member_min.':00';
		$date_check = $member_year.'-'.$member_month.'-'.$member_date;

		if ($date_head <= $date_use) { $date_head = $date_use; }

		$s = 'T';

		foreach ($sms_date[$u] as $date) {

			if ($date == $date_use) { $s = 'F'; }
		}

		if ($s == 'T') {

			$sms_date[$u] = $date_use;
			$u++;
		}

		$recieve = $_REQUEST['arecieve_'.$activity_id];
		$amount = $_REQUEST['aamount_'.$activity_id];

		if ($date_use < $time_insert) {

			# ACTIVITY SQL

			$sql_acti = "SELECT DISTINCT activity.acti_Name AS name, 
										activity.acti_Image AS image, 
										activity.acti_ActivityID AS id,
										activity.acti_SpecialPeriodType,
										IF(activity.acti_StartDate = '0000-00-00' OR activity.acti_EndDate = '0000-00-00', '-',CONCAT(DATE_FORMAT(activity.acti_StartDate,'%d/%m/%Y'),' - ',DATE_FORMAT(activity.acti_EndDate,'%d/%m/%Y'))) as TextDate,
										activity.acti_StartDateSpecial, 
										activity.acti_EndDateSpecial, 
										activity.acti_StartDate,
										activity.acti_EndDate,
										activity.acti_Method,
										activity.acti_StartTime,
										activity.acti_EndTime,
										activity.acti_QtyPerMember,
										activity.acti_QtyMember,
										activity.acti_RepetitionMember,
										activity.acti_QtyPerMemberData,
										activity.acti_QtyPer,
										activity.acti_Qty,
										activity.acti_MaxQty,
										activity.acti_Repetition,
										activity.acti_QtyPerData,
										activity.acti_TotalQty,
										activity.acti_Reservation,
										activity.acti_StartDateReservation,
										activity.acti_EndDateReservation,
										activity.acti_StartTimeReservation,
										activity.acti_EndTimeReservation,
										activity.acti_MotivationID
						FROM mi_card_register
						LEFT JOIN activity
						ON mi_card_register.activity_id = activity.acti_ActivityID
						WHERE mi_card_register.card_id='".$card_id."'
						AND mi_card_register.status='0'
						AND mi_card_register.activity_id!='0'
						AND activity.acti_ActivityID='".$activity_id."'";

			$oRes_acti = $oDB->Query($sql_acti);
			$axRow = $oRes_acti->FetchRow(DBI_ASSOC);

			# CHECK ACTIVITY USE

			$time = 1;

			$r = 0;

			for ($i=0 ; $i<$time ; $i++) {

				$regis_id = $register[$r];

				if ($regis_id) {

					if ($axRow['acti_Repetition'] == "T") {

						switch ($acti_QtyPer) {
								
							case 'Daily' :
								// $total = ActiRepetitionDaily('', $activity_id, $axRow['acti_Qty'], $card_id, $date_use);	
								// if ($total == "0") { $status_Repetition = "false"; }
								$status_Repetition = "true";
								break;
							case 'Weekly' :
								$arrayName = ActiRepetitionWeekly('','',$activity_id,$axRow['acti_Qty'],$card_id,$axRow['acti_QtyPerData'],$date_check);
								// $total = $arrayName["total"];
								// if ($total == "0") { $status_Repetition = "false"; }
								$status_Repetition = $arrayName["status"];	
								break;
							case 'Monthly' :
								$arrayName = ActiRepetitionMonth('','',$activity_id,$axRow['acti_Qty'],$card_id,$axRow['acti_QtyPerData'],$date_check);
								// $total = $arrayName["total"];
								// if ($total == "0") { $status_Repetition = "false"; }
								$status_Repetition = $arrayName["status"];
								break;
							case '' :
								// $total = ActiRepetitionNotSpecific('', $activity_id, $axRow['acti_Qty'], $card_id, $axRow['acti_QtyPerData'], $date_use);
								// if ($total == "0") { $status_Repetition = "false"; }
								$status_Repetition = "true";		
								break;
							default :
								break;
						}
					}

					switch ($axRow['acti_QtyPerMember']) {

						case 'Daily' :
							$totalMember = ActiRepetitionDaily($regis_id,$member_id,$activity_id,$axRow['acti_QtyMember'],$card_id,$date_check);
							if ($totalMember == "0") { $status_all = "false"; }	
							break;
						case 'Weekly' :
							$arrayName = ActiRepetitionWeekly($regis_id,$member_id,$activity_id,$axRow['acti_QtyMember'],$card_id,$axRow['acti_QtyPerMemberData'], $date_check);
							$totalMember = $arrayName["total"];
							if ($totalMember == "0") { $status_all = "false"; }	
							else { $status_all = $arrayName["status"]; }
							break;
						case 'Monthly' :
							$arrayName = ActiRepetitionMonth($regis_id,$member_id,$activity_id,$axRow['acti_QtyMember'],$card_id,$axRow['acti_QtyPerMemberData'],$date_check);
							$totalMember = $arrayName["total"];
							if ($totalMember == "0") { $status_all = "false"; }	
							else { $status_all = $arrayName["status"]; }
							break;
						case 'Not Specific' :
							$totalMember = ActiRepetitionNotSpecific($regis_id,$member_id,$activity_id,$axRow['acti_QtyMember'], $card_id, $axRow['acti_QtyPerMemberData'], $date_check);
							if ($totalMember == "0") { $status_all = "false"; }	
							break;
						default :
							$check_use = check_UseActivity($regis_id,$member_id,$activity_id,$card_id);
							if ($check_use == "false") { $status_all = "true"; } 
							else { $status_all = "false"; }
							break;
					}

					if ($status_Repetition == "false" || $status_all == "false") { $status_all = "false"; }


					# RESERVATION
								
					if ($status_all == "true") {
								
						if ($acti_Reservation == "T") {
									
							$status_Reservation = check_DateActivity($axRow['acti_StartDateReservation'],$axRow['acti_EndDateReservation'],$date_check);

							if ($status_Reservation == "true") {

								# RESERVATION

								$id_use = UsePCAB($pre_code,$head_id,$regis_id,$member_id,$card_id,$branch_id,$date_use,$time_insert,$activity_id,"MA","",$OTP);

								$time++;
							}

						} else {

							# USE
									
							$status_Use = check_DateActivity($axRow['acti_StartDate'],$axRow['acti_EndDate'],$date_check);

							if ($status_Use == 'true') {

								$id_use = UsePCAB($pre_code,$head_id,$regis_id,$member_id,$card_id,$branch_id,$date_use,$time_insert,$activity_id,"MA","Complete");

								insert_point('a',$activity_id,$id_use,$branch_id,$card_id,$regis_id,$recieve,$amount,$date_use,$time_insert,$axRow['acti_MotivationID']);

								$alert_check++;

							} else { $r++; $i--; }
						}

					} else {
									
						$reservation = CheckReservation($regis_id,$acti_ActivityID,$member_id,$card_id);
									
						if ($reservation["meac_MemberActivityID"] != "" && $reservation["meac_Status"] != "Complete") {

							# USE
									
							$status_Use = check_DateActivity($axRow['acti_StartDate'],$axRow['acti_EndDate'],$date_check);

							if ($status_Use == 'true') {

								$id_use = UseActivity($reservation["meac_MemberActivityID"],$date_use,$time_insert);

								insert_point('a',$activity_id,$id_use,$branch_id,$card_id,$regis_id,$recieve,$amount,$date_use,$time_insert,$axRow['acti_MotivationID']);	

								$alert_check++;

							} else { $r++; $i--;  }
						}
					}
				}
			}
		}
	}



	# STAMP

	foreach ($_POST['sp'] as $privilege_id) {

		$time = $_REQUEST['sptime_'.$privilege_id];

		$member_date = $_REQUEST['date_'.$privilege_id];
		$member_month = $_REQUEST['month_'.$privilege_id];
		$member_year = $_REQUEST['year_'.$privilege_id];
		$member_hour = $_REQUEST['hour_'.$privilege_id];
		$member_min = $_REQUEST['min_'.$privilege_id];

		$date_use = $member_year.'-'.$member_month.'-'.$member_date.' '.$member_hour.':'.$member_min.':00';
		$date_check = $member_year.'-'.$member_month.'-'.$member_date;

		if ($date_head <= $date_use) { $date_head = $date_use; }

		$s = 'T';

		foreach ($sms_date[$u] as $date) {

			if ($date == $date_use) { $s = 'F'; }
		}

		if ($s == 'T') {

			$sms_date[$u] = $date_use;
			$u++;
		}

		if ($date_use < $time_insert) {

			# PRIVILEGE SQL

			$sql_priv = "SELECT DISTINCT privilege.priv_Name AS name, 
										privilege.priv_Image AS image, 
										privilege.priv_PrivilegeID AS id,
										privilege.priv_LimitUse,
										privilege.priv_OneTimePer,
										privilege.priv_StartDateSpecial,
										privilege.priv_EndDateSpecial,
										IF(privilege.priv_StartDateSpecial = '0000-00-00' 
										OR privilege.priv_EndDateSpecial = '0000-00-00', '-',
										CONCAT(DATE_FORMAT(privilege.priv_StartDateSpecial,'%d/%m/%Y'),' - ',
										DATE_FORMAT(privilege.priv_EndDateSpecial,'%d/%m/%Y'))) AS TextDate,
										privilege.priv_MotivationID
						FROM mi_card_register
						LEFT JOIN privilege
						ON mi_card_register.privilege_id = privilege.priv_PrivilegeID
						WHERE mi_card_register.card_id=".$card_id."
						AND mi_card_register.status='0'
						AND mi_card_register.privilege_id!='0'
						AND privilege.priv_PrivilegeID=".$privilege_id;

			$oRes_priv = $oDB->Query($sql_priv);
			$axRow = $oRes_priv->FetchRow(DBI_ASSOC);

			# CHECK PRIVILEGE USE
		
			if ($time!=0) {

				$r = 0;

				for ($i=0 ; $i<$time ; $i++) {

					$regis_id = $register[$r];

					if ($regis_id) {

						if ($axRow['priv_LimitUse'] == "T") {

							switch ($axRow['priv_OneTimePer']) {
								case 'Daily' :
									$status_limit = priv_Daily($regis_id,$member_id,$privilege_id,$card_id,$date_check);
									break;
								case 'Weekly' :
									$status_limit = priv_Weekly($regis_id,$member_id,$privilege_id,$card_id,$date_check);
									break;
								default :
									$status_limit = priv_Monthly($regis_id,$member_id,$privilege_id,$card_id,$date_check);
									break;
							}

						} else { $status_limit = 'true'; }
									
						if ($axRow["TextDate"] == '-' ) {

							$status_use = "true";

						} else {

							$status_use = check_SpecialDate($axRow["priv_StartDateSpecial"],$axRow["priv_EndDateSpecial"],$date_check);
						}

						if ($status_use == "true" && $status_limit == "true") { 
								
							$id_use = UsePCAB($pre_code,$head_id,$regis_id,$member_id,$card_id,$branch_id,$date_use,$time_insert,$privilege_id,"MP","",$OTP);

							insert_stamp('p',$privilege_id,$branch_id,$card_id,$regis_id,$id_use,$date_use,$time_insert,$axRow['priv_MotivationID']);

							$alert_check++;

						} else { $r++; $i--;  }
					}
				}
			}
		}
	}



	foreach ($_POST['sc'] as $coupon_id) {

		$time = $_REQUEST['sctime_'.$coupon_id];

		$member_date = $_REQUEST['date_'.$coupon_id];
		$member_month = $_REQUEST['month_'.$coupon_id];
		$member_year = $_REQUEST['year_'.$coupon_id];
		$member_hour = $_REQUEST['hour_'.$coupon_id];
		$member_min = $_REQUEST['min_'.$coupon_id];

		$date_use = $member_year.'-'.$member_month.'-'.$member_date.' '.$member_hour.':'.$member_min.':00';
		$date_check = $member_year.'-'.$member_month.'-'.$member_date;

		if ($date_head <= $date_use) { $date_head = $date_use; }

		$s = 'T';

		foreach ($sms_date[$u] as $date) {

			if ($date == $date_use) { $s = 'F'; }
		}

		if ($s == 'T') {

			$sms_date[$u] = $date_use;
			$u++;
		}

		if ($date_use < $time_insert) {

			# COUPON SQL

			$sql_coup = "SELECT DISTINCT coupon.coup_Name AS name, 
										coupon.coup_Image AS image, 
										coupon.coup_CouponID AS id,
										coupon.coup_QtyPerMember,
										coupon.coup_RepetitionMember,
										coupon.coup_QtyMember,
										coupon.coup_QtyPerMemberData,
										coupon.coup_SpecialPeriodType,
										coupon.coup_QtyPer,
										coupon.coup_Repetition,
										coupon.coup_Qty,
										coupon.coup_QtyPerData,
										coupon.coup_TotalQty,
										coupon.coup_Method,
										coupon.coup_StartDate,
										coupon.coup_EndDate,
										coupon.coup_StartDateSpecial,
										coupon.coup_EndDateSpecial,
										IF(coupon.coup_StartDate = '0000-00-00' OR coupon.coup_EndDate = '0000-00-00', 
										'-', CONCAT(DATE_FORMAT(coupon.coup_StartDate,'%d/%m/%Y'),' - ',
										DATE_FORMAT(coupon.coup_EndDate,'%d/%m/%Y'))) as TextDate,
										IF(coupon.coup_StartDateSpecial = '0000-00-00' 
										OR coupon.coup_EndDateSpecial = '0000-00-00', 
										'-', CONCAT(DATE_FORMAT(coupon.coup_StartDateSpecial,'%d/%m/%Y'),' - ',
										DATE_FORMAT(coupon.coup_EndDateSpecial,'%d/%m/%Y'))) as TextDateSpecial,
										coupon.coup_MotivationID
						FROM mi_card_register
						LEFT JOIN coupon
						ON mi_card_register.coupon_id = coupon.coup_CouponID
						WHERE mi_card_register.card_id='".$card_id."'
						AND mi_card_register.status='0'
						AND mi_card_register.coupon_id!='0'
						AND coupon.coup_Birthday!='T'
						AND coupon.coup_CouponID='".$coupon_id."'";

			$oRes_coup = $oDB->Query($sql_coup);
			$axRow = $oRes_coup->FetchRow(DBI_ASSOC);

			# CHECK USE

			if ($time!=0) {

				$r = 0;

				for ($i=0 ; $i<$time ; $i++) {

					$regis_id = $register[$r];

					if ($regis_id) {

						$total = "0";
						$totalMember = "0";

						if ($axRow['coup_Repetition'] == "T") {

							switch ($axRow['coup_QtyPer']) {
									
								case 'Daily' :
									// $total = CoupRepetitionDaily('', $coupon_id, $axRow['coup_Qty'], $card_id, $date_use);
									// if ($total == "0") { $status_Repetition = "false"; }
									$status_Repetition = "true";
									break;
								case 'Weekly' :
									$arrayName = CoupRepetitionWeekly('','',$coupon_id,$axRow['coup_Qty'],$card_id,$axRow['coup_QtyPerData'],$date_check);
									// $total = $arrayName["total"];
									// if ($total == "0") { $status_Repetition = "false"; }
									$status_Repetition = $arrayName["status"];
									break;
								case 'Monthly' :
									$arrayName = CoupRepetitionMonth('','',$coupon_id,$axRow['coup_Qty'],$card_id,$axRow['coup_QtyPerData'],$date_check);
									// $total = $arrayName["total"];
									// if ($total == "0") { $status_Repetition = "false"; }
									$status_Repetition = $arrayName["status"];
									break;
								case 'Not' :
									$total = CoupRepetitionNotSpecific('', $coupon_id, $axRow['coup_Qty'], $card_id);
									if ($total == "0") { $status_Repetition = "false"; }
									$status_Repetition = "true";
									break;
								default :
									break;
							}			
						}

						if ($axRow['coup_RepetitionMember'] == "T") {

							switch ($axRow['coup_QtyPerMember']) {

								case 'Daily' :
									$totalMember = CoupRepetitionDaily($regis_id,$member_id,$coupon_id,$axRow['coup_QtyMember'],$card_id,$date_check);
									if ($totalMember == "0") { $status_all = "false"; }
									break;
								case 'Weekly' :
									$arrayName = CoupRepetitionWeekly($regis_id,$member_id,$coupon_id,$axRow['coup_QtyMember'],$card_id,$axRow['coup_QtyPerMemberData'],$date_check);
									$totalMember = $arrayName["total"];
									if ($totalMember == "0") { $status_all = "false"; } 
									else { $status_all = $arrayName["status"]; }
									break;
								case 'Monthly' :
									$arrayName = CoupRepetitionMonth($regis_id,$member_id, $coupon_id, $axRow['coup_QtyMember'], $card_id, $axRow['coup_QtyPerMemberData'], $date_check);
									$totalMember = $arrayName["total"];
									if ($totalMember == "0") { $status_all = "false"; }
									else { $status_all = $arrayName["status"]; }
									break;
								case 'Not Specific' :
									$totalMember = CoupRepetitionNotSpecific($regis_id,$member_id, $coupon_id, $axRow['coup_QtyMember'], $card_id);
									if ($totalMember == "0") { $status_all = "false"; }
									break;
								default :
									break;
							}
						}

						if ($axRow['coup_RepetitionMember'] != "T" && $axRow['coup_Repetition'] != "T") {
								
							$check_use = check_UseCoupon($member_id, $coupon_id, $card_id);
							if ($check_use == "false") { $status_all = "true"; } 
							else { $status_all = "false"; }
							
						} else {

							if ($status_Repetition == "false" || $status_all == "false") { $status_all = "false"; }
						}
						
						if ($axRow["TextDate"] == "-") {
								
						} else {
								
							if ($axRow['coup_StartDate'] < $date_check && $axRow['coup_EndDate'] > $date_check && $status_all == "true") {
									
								$status_all = "true";
							}
						}
							
						if ($axRow["TextDateSpecial"] == "-") {
								
						} else {
								
							if ($axRow['coup_StartDateSpecial'] < $date_check && $axRow['coup_EndDateSpecial'] > $date_check && $status_all == "true") {
									
								$status_all = "true";
							}
						}

						if ($status_all == "true") { 
										
							$id_use = UsePCAB($pre_code,$head_id,$regis_id,$member_id,$card_id,$branch_id,$date_use,$time_insert,$coupon_id,"MC","",$OTP);

							insert_stamp('c',$coupon_id,$branch_id,$card_id,$regis_id,$id_use,$date_use,$time_insert,$axRow['coup_MotivationID']);

							$alert_check++;

						} else { $r++; $i--;  }
					}
				}
			}
		}
	}



	foreach ($_POST['sh'] as $hbd_id) {

		$time = $_REQUEST['shtime_'.$coupon_id];

		if ($time == '') { $time = 1; }

		$member_date = $_REQUEST['date_'.$hbd_id];
		$member_month = $_REQUEST['month_'.$hbd_id];
		$member_year = $_REQUEST['year_'.$hbd_id];
		$member_hour = $_REQUEST['hour_'.$hbd_id];
		$member_min = $_REQUEST['min_'.$hbd_id];

		$date_use = $member_year.'-'.$member_month.'-'.$member_date.' '.$member_hour.':'.$member_min.':00';
		$date_check = $member_year.'-'.$member_month.'-'.$member_date;

		if ($date_head <= $date_use) { $date_head = $date_use; }

		$s = 'T';

		foreach ($sms_date[$u] as $date) {

			if ($date == $date_use) { $s = 'F'; }
		}

		if ($s == 'T') {

			$sms_date[$u] = $date_use;
			$u++;
		}

		if ($date_use < $time_insert) {

			# HBD SQL

			$sql_hbd = "SELECT DISTINCT coupon.coup_Name AS name, 
										coupon.coup_Image AS image, 
										coupon.coup_CouponID AS id,
										coupon.coup_Method,
										coupon.coup_MotivationID
						FROM mi_card_register
						LEFT JOIN coupon
						ON mi_card_register.coupon_id = coupon.coup_CouponID
						WHERE mi_card_register.card_id='".$card_id."'
						AND mi_card_register.status='0'
						AND mi_card_register.coupon_id!='0'
						AND coupon.coup_Birthday='T'
						AND coupon.coup_CouponID='".$hbd_id."'";

			$oRes_hbd = $oDB->Query($sql_hbd);
			$axRow = $oRes_hbd->FetchRow(DBI_ASSOC);

			# CHECK HBD

			if ($time != 0) {

				$r = 0;

				for ($i=0 ; $i<$time ; $i++) {

					$regis_id = $register[$r];

					if ($regis_id) {

						$check_use = check_YearBirthday($regis_id,$member_id,$hbd_id,$card_id,$member_year);

						if ($check_use == "false") $status_all = "true";
						else $status_all = "false";

						if ($status_all == "true") {
							
							$birthday = birthday($member_id);

							if ($birthday != "") {
										
								switch ($axRow['coup_Method']) {
											
									case 'Day' :
										$status_all = birthdayToday($birthday,$member_year,$date_check);
										break;
									case 'Week' :
										$status_all = birthdayWeek($birthday,$member_year,$date_check);
										break;
									case 'Month' :
										$status_all = birthdayMonth($birthday,$member_month);
										break;
									default :
										$status_all = "false";
										break;
								}

							} else { $status_all = "false"; }
						}

						# CHECK HBD

						if ($status_all == "true") {

							$id_use = UsePCAB($pre_code,$head_id,$regis_id,$member_id,$card_id,$branch_id,$time_insert,$hbd_id,"MC","",$OTP);

							insert_stamp('c',$hbd_id,$branch_id,$card_id,$regis_id,$id_use,$date_use,$time_insert,$axRow['coup_MotivationID']);

							$alert_check++;

						} else { $r++; $i--;  }
					}
				}
			}
		}
	}



	foreach ($_POST['sa'] as $activity_id) {

		$member_date = $_REQUEST['date_'.$activity_id];
		$member_month = $_REQUEST['month_'.$activity_id];
		$member_year = $_REQUEST['year_'.$activity_id];
		$member_hour = $_REQUEST['hour_'.$activity_id];
		$member_min = $_REQUEST['min_'.$activity_id];

		$date_use = $member_year.'-'.$member_month.'-'.$member_date.' '.$member_hour.':'.$member_min.':00';
		$date_check = $member_year.'-'.$member_month.'-'.$member_date;

		if ($date_head <= $date_use) { $date_head = $date_use; }

		$s = 'T';

		foreach ($sms_date[$u] as $date) {

			if ($date == $date_use) { $s = 'F'; }
		}

		if ($s == 'T') {

			$sms_date[$u] = $date_use;
			$u++;
		}

		if ($date_use < $time_insert) {

			# ACTIVITY SQL

			$sql_acti = "SELECT DISTINCT activity.acti_Name AS name, 
										activity.acti_Image AS image, 
										activity.acti_ActivityID AS id,
										activity.acti_SpecialPeriodType,
										IF(activity.acti_StartDate = '0000-00-00' OR activity.acti_EndDate = '0000-00-00', '-',CONCAT(DATE_FORMAT(activity.acti_StartDate,'%d/%m/%Y'),' - ',DATE_FORMAT(activity.acti_EndDate,'%d/%m/%Y'))) as TextDate,
										activity.acti_StartDateSpecial, 
										activity.acti_EndDateSpecial, 
										activity.acti_StartDate,
										activity.acti_EndDate,
										activity.acti_Method,
										activity.acti_StartTime,
										activity.acti_EndTime,
										activity.acti_QtyPerMember,
										activity.acti_QtyMember,
										activity.acti_RepetitionMember,
										activity.acti_QtyPerMemberData,
										activity.acti_QtyPer,
										activity.acti_Qty,
										activity.acti_MaxQty,
										activity.acti_Repetition,
										activity.acti_QtyPerData,
										activity.acti_TotalQty,
										activity.acti_Reservation,
										activity.acti_StartDateReservation,
										activity.acti_EndDateReservation,
										activity.acti_StartTimeReservation,
										activity.acti_EndTimeReservation,
										activity.acti_MotivationID,
						FROM mi_card_register
						LEFT JOIN activity
						ON mi_card_register.activity_id = activity.acti_ActivityID
						WHERE mi_card_register.card_id='".$card_id."'
						AND mi_card_register.status='0'
						AND mi_card_register.activity_id!='0'
						AND activity.acti_ActivityID='".$activity_id."'";

			$oRes_acti = $oDB->Query($sql_acti);
			$axRow = $oRes_acti->FetchRow(DBI_ASSOC);

			# CHECK ACTIVITY USE

			$time = 1;

			$r = 0;

			for ($i=0 ; $i<$time ; $i++) {

				$regis_id = $register[$r];

				if ($regis_id) {

					if ($axRow['acti_Repetition'] == "T") {

						switch ($acti_QtyPer) {
								
							case 'Daily' :
								// $total = ActiRepetitionDaily('', $activity_id, $axRow['acti_Qty'], $card_id, $date_use);	
								// if ($total == "0") { $status_Repetition = "false"; }
								$status_Repetition = "true";
								break;
							case 'Weekly' :
								$arrayName = ActiRepetitionWeekly('','',$activity_id,$axRow['acti_Qty'],$card_id,$axRow['acti_QtyPerData'],$date_check);
								// $total = $arrayName["total"];
								// if ($total == "0") { $status_Repetition = "false"; }
								$status_Repetition = $arrayName["status"];	
								break;
							case 'Monthly' :
								$arrayName = ActiRepetitionMonth('','',$activity_id,$axRow['acti_Qty'],$card_id,$axRow['acti_QtyPerData'],$date_check);
								// $total = $arrayName["total"];
								// if ($total == "0") { $status_Repetition = "false"; }
								$status_Repetition = $arrayName["status"];
								break;
							case '' :
								// $total = ActiRepetitionNotSpecific('', $activity_id, $axRow['acti_Qty'], $card_id, $axRow['acti_QtyPerData'], $date_use);
								// if ($total == "0") { $status_Repetition = "false"; }
								$status_Repetition = "true";		
								break;
							default :
								break;
						}
					}

					switch ($axRow['acti_QtyPerMember']) {

						case 'Daily' :
							$totalMember = ActiRepetitionDaily($regis_id,$member_id,$activity_id,$axRow['acti_QtyMember'],$card_id,$date_check);
							if ($totalMember == "0") { $status_all = "false"; }	
							break;
						case 'Weekly' :
							$arrayName = ActiRepetitionWeekly($regis_id,$member_id,$activity_id,$axRow['acti_QtyMember'], $card_id,$axRow['acti_QtyPerMemberData'],$date_check);
							$totalMember = $arrayName["total"];
							if ($totalMember == "0") { $status_all = "false"; }	
							else { $status_all = $arrayName["status"]; }
							break;
						case 'Monthly' :
							$arrayName = ActiRepetitionMonth($regis_id,$member_id,$activity_id,$axRow['acti_QtyMember'], $card_id,$axRow['acti_QtyPerMemberData'],$date_check);
							$totalMember = $arrayName["total"];
							if ($totalMember == "0") { $status_all = "false"; }	
							else { $status_all = $arrayName["status"]; }
							break;
						case 'Not Specific' :
							$totalMember = ActiRepetitionNotSpecific($regis_id,$member_id,$activity_id,$axRow['acti_QtyMember'],$card_id,$axRow['acti_QtyPerMemberData'],$date_check);
							if ($totalMember == "0") { $status_all = "false"; }	
							break;
						default :
							$check_use = check_UseActivity($regis_id,$member_id,$activity_id,$card_id);
							if ($check_use == "false") { $status_all = "true"; } 
							else { $status_all = "false"; }
							break;
					}

					if ($status_Repetition == "false" || $status_all == "false") { $status_all = "false"; }


					# RESERVATION
								
					if ($status_all == "true") {
								
						if ($acti_Reservation == "T") {
									
							$status_Reservation = check_DateActivity($axRow['acti_StartDateReservation'],$axRow['acti_EndDateReservation'],$date_check);

							if ($status_Reservation == "true") {

								# RESERVATION

								$id_use = UsePCAB($pre_code,$head_id,$regis_id,$member_id,$card_id,$branch_id,$date_use,$time_insert,$activity_id,"MA","");

								$alert_check++;

							} else { $r++; $i--; }

						} else {

							# USE
									
							$status_Use = check_DateActivity($axRow['acti_StartDate'],$axRow['acti_EndDate'],$date_check);

							if ($status_Use == 'true') {

								$id_use = UsePCAB($pre_code,$head_id,$regis_id,$member_id,$card_id,$branch_id,$date_use,$time_insert,$activity_id,"MA","Complete",$OTP);

								insert_stamp('a',$activity_id,$branch_id,$card_id,$regis_id,$id_use,$date_use,$time_insert,$axRow['acti_MotivationID']);

								$alert_check++;

							} else { $r++; $i--; }
						}

					} else {
									
						$reservation = CheckReservation($regis_id,$acti_ActivityID,$member_id,$card_id);
									
						if ($reservation["meac_MemberActivityID"] != "" && $reservation["meac_Status"] != "Complete") {

							# USE
									
							$status_Use = check_DateActivity($axRow['acti_StartDate'],$axRow['acti_EndDate'],$date_check);

							if ($status_Use == 'true') {

								$id_use = UseActivity($reservation["meac_MemberActivityID"],$date_use,$time_insert);

								insert_stamp('a',$activity_id,$branch_id,$card_id,$regis_id,$id_use,$date_use,$time_insert,$axRow['acti_MotivationID']);

								$alert_check++;

							} else { $r++; $i--;  }
						}
					}
				}
			}
		}
	}


	# MOBILE

	$sql_mobile = 'SELECT mobile FROM mb_member WHERE member_id="'.$member_id.'"';
	$mobile = $oDB->QueryOne($sql_mobile);

	# SMS

	$sql_sms = 'SELECT flag_sms FROM mi_brand WHERE brand_id="'.$brand_id.'"';
	$sms = $oDB->QueryOne($sql_sms);

	if ($mobile && $sms=="T") {

		foreach ($sms_date as $s_date) {

			$date = date('d M o', strtotime($s_date));
			$time = date('H:i', strtotime($s_date));

	       	if ($OTP) { 

	       		$text = 'ท่านมีการใช้ '.$name['card_name'].' ของ '.$name['brand_name'].' เมื่อ '.$date; 

				if ($time != '00:00') { $text .= ' เวลา '.date('H:i'); }

	       		$text .= ' กรุณายืนยันการใช้สิทธิด้วยหมายเลข OTP ที่ได้รับ [OTP:'.$OTP.']'; 

	       	} else {

	       		$text = 'ท่านมีการใช้ '.$name['card_name'].' ของ '.$name['brand_name'].' เมื่อ '.$date; 

				if ($time != '00:00') { $text .= ' เวลา '.date('H:i'); }

	       		$text .= ' สามารถเข้าไปตรวจสอบได้ที่ goo.gl/5vYxXD หากไม่ใช่หรือไม่ถูกต้อง ติดต่อ '.$name['brand_name'].' ทันทีป้องกันคนอื่นแอบอ้างใช้สิทธิ'; 
	       	}

			$message = new stdClass();
	      	$message->from = 'iHealthy';
	       	$message->to = $mobile;
	       	$message->text = $text;
	        
	       	$username = 'Jirarak';
	       	$password = 'memberin2017';
	       	$auth = base64_encode("$username:$password");       
			    
	       	$curl = curl_init('api.infobip.com/sms/1/text/single');
			    
	       	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	       	curl_setopt($curl, CURLOPT_HTTPHEADER, [
	           	"Authorization: Basic $auth",
	           	"Content-Type: application/json"
	       	]);

			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($message));
						    
			$curl_response = curl_exec($curl);
						    
			$http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			$decoded = json_decode($curl_response);
						    
			if ($http_status_code != 200) {
						    
			    error_log("SendMessage|".$decoded->requestError->serviceException->messageId.":".$decoded->requestError->serviceException->text."|".date('Y-m-d H:i:s')."\n", 3, 'error.log');
			}
		}
	}


	# UPDATE HEAD
 
 	$sqlUpdateTransaction = "UPDATE member_transaction_h 
 							SET meth_CreatedDate = '".$date_head."'
 							WHERE meth_MemberTransactionHID = '".$head_id."'";

	$oDB->QueryOne($sqlUpdateTransaction);


	# ALERT_check

	if ($alert_check == 0) {

		$msg = 'มีรายการการใช้งานที่ไม่สามารถบันทึกการใช้งานได้ กรุณาตรวจเงื่อนไขการใช้งานของสิทธิ์';
		echo '<script type="text/javascript">alert("'.$msg.'")</script>';


		# DELETED HEAD
	 
	 	$sqlUpdateTransaction = "DELETE FROM member_transaction_h WHERE meth_MemberTransactionHID = '".$head_id."'";

		$oDB->QueryOne($sqlUpdateTransaction);
	}

	echo '<script>window.location.href="privilege.php";</script>';
	exit();
}







function get_member_registerID($card_id,$member_id) {

	$oDB = new DBI();

	$sql = "SELECT member_register_id FROM mb_member_register WHERE card_id=".$card_id." AND member_id=".$member_id;

	$member_register_id = $oDB->QueryOne($sql);

	return $member_register_id;
}


function priv_Daily($register_id,$member_id,$priv_PrivilegeID,$card_id,$date_use) {

	$oDB = new DBI();

	$sql = "SELECT mepe_MemberPrivlegeID
			FROM member_privilege_trans
			WHERE member_privilege_trans.priv_PrivilegeID = '".$priv_PrivilegeID."'
			AND member_privilege_trans.mere_MemberRegisterID = '".$register_id."'
			AND member_privilege_trans.mepe_CreatedDate LIKE '".$date_use."%'
			AND member_privilege_trans.mepe_Deleted=''";

	$row_result = $oDB->QueryOne($sql);

	if ($row_result) { return "false"; } 
	else { return "true"; }
}


function priv_Weekly($register_id, $member_id, $priv_PrivilegeID, $card_id, $date_use) {

	$oDB = new DBI();

	$date_use = strtotime($date_use);

	$monday = date("Y-m-d", strtotime("monday this week - 1 day",$date_use));
	$sunday = date("Y-m-d", strtotime("sunday this week",$date_use));

	$sql = "SELECT mepe_MemberPrivlegeID
			FROM member_privilege_trans
			WHERE member_privilege_trans.priv_PrivilegeID = '".$priv_PrivilegeID."'
			AND member_privilege_trans.mere_MemberRegisterID = '".$register_id."'
			AND member_privilege_trans.mepe_CreatedDate BETWEEN '".$monday."%' AND '".$sunday."%'
			AND member_privilege_trans.mepe_Deleted=''";

	$row_result = $oDB->QueryOne($sql);

	if ($row_result) { return "false"; } 
	else { return "true"; }
}


function priv_Monthly($register_id, $member_id, $priv_PrivilegeID, $card_id, $date_use) {

	$oDB = new DBI();

	$date_use = strtotime($date_use);

	$firstday = date("Y-m-d", strtotime("first day of this month",$date_use));
	$lastday = date("Y-m-d", strtotime("last day of this month",$date_use));

	$sql = "SELECT mepe_MemberPrivlegeID
			FROM member_privilege_trans
			WHERE member_privilege_trans.priv_PrivilegeID = '".$priv_PrivilegeID."'
			AND member_privilege_trans.mere_MemberRegisterID = '".$register_id."'
			AND member_privilege_trans.mepe_CreatedDate BETWEEN '".$firstday."%' AND '".$lastday."%'
			AND member_privilege_trans.mepe_Deleted=''";

	$row_result = $oDB->QueryOne($sql);

	if ($row_result) { return "false"; } 
	else { return "true"; }
}


function check_SpecialDate($date_start, $date_end, $date_use) {

	$date = date('Y-m-d', strtotime($date_use));
	$date_start = date('Y-m-d', strtotime($date_start));
	$date_end = date('Y-m-d', strtotime($date_end));

	if ($date_start <= $date && $date <= $date_end)
		return "true";
	else
		return "false";
}


function check_UseCoupon($register_id,$member_id,$coup_CouponID,$card_id) {

	$oDB = new DBI();

    $sql = "SELECT meco_MemberCouponID
			FROM member_coupon_trans
			WHERE mere_MemberRegisterID = ".$register_id."
			AND coup_CouponID = ".$coup_CouponID."
			AND meco_Deleted=''";

    $row_result = $oDB->QueryOne($sql);

    if ($row_result) { return "true"; } 
    else { return "false"; }
}


function check_UseActivity($register_id,$member_id,$acti_ActivityID,$card_id) {

	$oDB = new DBI();

    $sql = "SELECT meac_MemberActivityID
			FROM member_activity_trans
			WHERE mere_MemberRegisterID = ".$register_id."
			AND acti_ActivityID = ".$acti_ActivityID."
			AND meac_Deleted=''";

    $row_result = $oDB->QueryOne($sql);

    if ($row_result) { return "true"; } 
    else { return "false"; }
}


function check_DateActivity($start_date,$end_date,$date_use) {
			
	if ($start_date <= $date_use && $end_date >= $date_use) { return "true"; } 
	else { return "false"; }
}


function CheckReservation($register_id, $acti_ActivityID,$member_id,$card_id) {

	$oDB = new DBI();
	
	$sql = "SELECT meac_MemberActivityID,meac_Status
			FROM member_activity_trans
			WHERE acti_ActivityID = ".$acti_ActivityID." 
			AND mere_MemberRegisterID = ".$register_id."
			AND meac_Deleted=''";

    $row_result = $oDB->QueryOne($sql);
	
	return $row_result;
}


function UseActivity($id,$date) {

	$oDB = new DBI();

	$time_only = date("H:i:s");

    $sql = "UPDATE member_activity_trans
			SET meac_Status = 'Complete',
			meac_UpdatedBy ='".$_SESSION['UID']."',
			meac_UpdatedDate = '".$date." ".$time_only."'
			WHERE meac_MemberActivityID = '".$id."'
			AND meac_Deleted=''";

    $row_result = $oDB->QueryOne($sql);
}


function CoupRepetitionDaily($register_id,$member_id,$coup_CouponID,$coup_Qty,$card_id,$date_use) {

	$oDB = new DBI();

	$date = $date_use;
	$date .= '%';

	if ($register_id==0) {

		$sql = "SELECT COUNT(meco_MemberCouponID) AS ToTalUse
				FROM member_coupon_trans
				WHERE card_CardID = ".$card_id."
				AND coup_CouponID = ".$coup_CouponID." AND meco_CreatedDate LIKE '".$date."'
				AND meco_Deleted=''";
	} else {

		$sql = "SELECT COUNT(meco_MemberCouponID) AS ToTalUse
				FROM member_coupon_trans
				WHERE mere_MemberRegisterID = ".$register_id."
				AND coup_CouponID = ".$coup_CouponID."
				AND meco_CreatedDate LIKE '".$date."'
				AND meco_Deleted=''";
	}
	
	$total_use = $oDB->QueryOne($sql);	

	if ($total_use < $coup_Qty) { return $coup_Qty-$total_use; } 
	else { return 0; }
}


function CoupRepetitionWeekly($register_id,$member_id,$coup_CouponID,$coup_Qty,$card_id,$coup_QtyPerData,$date_use) {

	$oDB = new DBI();
	
	$status = "false";

	$date_use = strtotime($date_use);

	$date_week = explode(',', $coup_QtyPerData);

	if (count($date_week) > 0) {
		
		foreach ($date_week as $date_select) {

			switch ($date_select) {

				case 'Sun' :
					$date_check = date("Y-m-d", strtotime("monday this week - 1 day",$date_use));
					break;
				case 'Mon' :
					$date_check = date("Y-m-d", strtotime("monday this week",$date_use));
					break;
				case 'Tue' :
					$date_check = date('Y-m-d', strtotime('tuesday this week',$date_use));
					break;
				case 'Wed' :
					$date_check = date('Y-m-d', strtotime('wednesday this week',$date_use));
					break;
				case 'Thu' :
					$date_check = date('Y-m-d', strtotime('thursday this week',$date_use));
					break;
				case 'Fri' :
					$date_check = date('Y-m-d', strtotime('friday this week',$date_use));
					break;
				case 'Sat' :
					$date_check = date('Y-m-d', strtotime('saturday this week',$date_use));
					break;
				default :
					$status = "false";
					$date_check = $date_use;
					break;
			}

			if ($date_check == $date) $status = "true";
		}
	}

	$monday = date("Y-m-d", strtotime("monday this week - 1 day",$date_use));
	$monday .='%'; 
	$sunday = date("Y-m-d", strtotime("sunday this week",$date_use));
	$sunday .='%'; 

	if ($register_id == 0) {

		$sql = "SELECT COUNT(meco_MemberCouponID) AS ToTalUse
						  FROM member_coupon_trans
						  WHERE card_CardID = ".$card_id."
						  AND coup_CouponID = ".$coup_CouponID."
							AND meco_Deleted=''
						  AND  meco_CreatedDate 
						  BETWEEN '".$monday."'
						  AND '".$sunday."'";
	} else {

		$sql = "SELECT COUNT(meco_MemberCouponID) AS ToTalUse
						  FROM member_coupon_trans
						  WHERE mere_MemberRegisterID = ".$register_id."
						  AND coup_CouponID = ".$coup_CouponID."
							AND meco_Deleted=''
						  AND  meco_CreatedDate 
						  BETWEEN '".$monday."'
						  AND '".$sunday."'";
	}

	$total_use = $oDB->QueryOne($sql);

	if ($total_use < $coup_Qty) { $total = $coup_Qty-$total_use; } 
	else { $total = 0; }

	$arrayName = array('total' => $total, 'status' => $status);

	return $arrayName;
}


function CoupRepetitionMonth($register_id,$member_id,$coup_CouponID,$coup_Qty,$card_id,$coup_QtyPerData,$date_use) {

	$oDB = new DBI();

	$status = "false";

	$date = date('d', $date_use);

	$date_week = explode(',', $coup_QtyPerData);

	if (count($date_week) > 0) {
		
		foreach ($date_week as $date_select) {

			if ($date_select == $date || $date_select == "") { $status = "true"; }
		}
	}

	$firstday = date('Y-m-d', strtotime('first day of this month',$date_use));
	$firstday .='%'; 
	$lastday = date('Y-m-d', strtotime('last day of this month',$date_use));
	$lastday .='%'; 

	if ($register_id == 0) {

		$sql = "SELECT COUNT(meco_MemberCouponID) AS ToTalUse
				FROM member_coupon_trans
				WHERE card_CardID = ".$card_id."
				AND coup_CouponID = ".$coup_CouponID." 
				AND meco_Deleted=''
				AND  meco_CreatedDate 
				BETWEEN '".$firstday."' 
				AND '".$lastday."'";
	} else {

		$sql = "SELECT COUNT(meco_MemberCouponID) AS ToTalUse
				FROM member_coupon_trans
				WHERE mere_MemberRegisterID = ".$register_id."
				AND coup_CouponID = ".$coup_CouponID." 
				AND meco_Deleted=''
				AND  meco_CreatedDate 
				BETWEEN '".$firstday."' 
				AND '".$lastday."'";
	}

	$total_use = $oDB->QueryOne($sql);	
	
	if ($total_use < $coup_QtyMember) { $total = $coup_QtyMember-$total_use; } 
	else { $total = 0; }

	$arrayName = array('total' => $total, 'status' => $status);

	return $arrayName;
}


function CoupRepetitionNotSpecific($register_id,$member_id,$coup_CouponID,$coup_Qty,$card_id) {

	$oDB = new DBI();

	if ($register_id == 0) {

		$sql = "SELECT COUNT(meco_MemberCouponID) AS ToTalUse
				FROM member_coupon_trans
				WHERE card_CardID = ".$card_id."
				AND coup_CouponID = ".$coup_CouponID."
				AND meco_Deleted=''";
	} else {

		$sql = "SELECT COUNT(meco_MemberCouponID) AS ToTalUse
				FROM member_coupon_trans
				WHERE mere_MemberRegisterID = ".$register_id."
				AND coup_CouponID = ".$coup_CouponID."
				AND meco_Deleted=''";
	}

	$total_use = $oDB->QueryOne($sql);

	if ($total_use < $coup_Qty) { $total = $coup_Qty - $total_use; } 
	else { $total = 0; }

	return $total;
}


function birthday($member_id) {

	$oDB = new DBI();

	$sql = "SELECT IFNULL(date_birth,'') birthday 
			FROM mb_member 
			WHERE member_id = ".$member_id."";

	$date = $oDB->QueryOne($sql);

	return $date;
}


function check_YearBirthday($register_id,$member_id,$coup_CouponID,$card_id,$year_use) {

	$oDB = new DBI();

    $sql = "SELECT meco_MemberCouponID
			FROM member_coupon_trans
			WHERE mere_MemberRegisterID = ".$register_id."
			AND coup_CouponID = ".$coup_CouponID."
			AND meco_CreatedDate LIKE '%".$year_use."%'
			AND meco_Deleted=''";

	$row_result = $oDB->QueryOne($sql);

    if ($row_result) { return "true"; } 
    else { return "false"; }
}


function birthdayToday($birthday,$year_use,$date_use) {

	$birthday = substr_replace($birthday, $year_use, 0, 4);
	$birthday = date("Y-m-d", strtotime(date("Y-m-d", strtotime($birthday))));

	if ($date_use == $birthday) { return "true"; } 
	else { return "false"; }
}


function birthdayWeek($birthday,$year_use,$date_use) {

	$birthday = substr_replace($birthday, $year_use, 0, 4);
	$birthday = date("Y-m-d", strtotime(date("Y-m-d", strtotime($birthday))));

	$monday = date('Y-m-d', strtotime('monday this week - 1 day',$birthday));
	$sunday = date('Y-m-d', strtotime('sunday this week',$birthday));

	if ($monday <= $date_use && $sunday > $date_use) { return "true"; } 
	else { return "false"; }
}


function birthdayMonth($birthday,$date_use) {

	$month_use = substr($birthday,5,2);
	$date_use = date("m", strtotime(date("Y-m-d", strtotime($date_use))));

	if ($month_use == $date_use) { return "true"; } 
	else { return "false"; }
}


function ActivityAllUse($acti_TotalQty,$acti_ActivityID,$card_id) {

	$oDB = new DBI();

	$sql = "SELECT COUNT(meac_MemberActivityID) AS ToTalUse
					FROM member_activity_trans
					WHERE acti_ActivityID = ".$acti_ActivityID."
					AND card_CardID = ".$card_id."
					AND meac_Deleted=''";

	$total_use = $oDB->QueryOne($sql);

	return $total_use;
}


function ActiRepetitionDaily($register_id,$member_id,$acti_ActivityID,$acti_Qty,$card_id,$date_use) {

	$oDB = new DBI();

	$date = $date_use;
	$date .= "%";

	if ($register_id!=0) {

		$sql = "SELECT COUNT(meac_MemberActivityID) AS ToTalUse
				FROM member_activity_trans
				WHERE acti_ActivityID = ".$acti_ActivityID."
				AND mere_MemberRegisterID = ".$register_id."
				AND meac_CreatedDate LIKE '".$date."'
				AND meac_Deleted=''";
	} else {

		$sql = "SELECT COUNT(meac_MemberActivityID) AS ToTalUse
				FROM member_activity_trans
				WHERE acti_ActivityID = ".$acti_ActivityID."
				AND card_CardID = ".$card_id."
				AND meac_CreatedDate LIKE '".$date."'
				AND meac_Deleted=''";
	}

	$total_use = $oDB->QueryOne($sql);
	
	if ($total_use < $acti_Qty) { return $acti_Qty - $total_use; } 
	else { return 0; }
}


function ActiRepetitionWeekly($register_id,$member_id,$acti_ActivityID,$acti_Qty,$card_id,$acti_QtyPerData,$date_use) {

	$oDB = new DBI();
	
	$status = "false";

	$date_use = strtotime($date_use);

	$date_week = explode(',', $acti_QtyPerData);

	if (count($date_week) > 0) {
		
		foreach ($date_week as $date_select) {

			switch ($date_select) {
				
				case 'Sun' :
					$date_check = date("Y-m-d", strtotime("monday this week - 1 day",$date_use));
					break;
				case 'Mon' :
					$date_check = date("Y-m-d", strtotime("monday this week",$date_use));
					break;
				case 'Tue' :
					$date_check = date('Y-m-d', strtotime('tuesday this week',$date_use));
					break;
				case 'Wed' :
					$date_check = date('Y-m-d', strtotime('wednesday this week',$date_use));
					break;
				case 'Thu' :
					$date_check = date('Y-m-d', strtotime('thursday this week',$date_use));
					break;
				case 'Fri' :
					$date_check = date('Y-m-d', strtotime('friday this week',$date_use));
					break;
				case 'Sat' :
					$date_check = date('Y-m-d', strtotime('saturday this week',$date_use));
					break;
				default :
					$status = "false";
					$date_check = $date_use;
					break;
			}

			if ($date_check == $date) $status = "true";
		}
	}

	$monday = date("Y-m-d", strtotime("monday this week - 1 day",$date_use));
	$monday .='%'; 
	$sunday = date("Y-m-d", strtotime("sunday this week",$date_use));
	$sunday .='%'; 

	if ($register_id == 0) {

		$sql = "SELECT COUNT(meac_MemberActivityID) AS ToTalUse
						  FROM member_activity_trans
						  WHERE card_CardID = ".$card_id."
						  AND acti_ActivityID = ".$acti_ActivityID."
						  AND meac_Deleted=''
						  AND  meac_CreatedDate 
						  BETWEEN '".$monday."'
						  AND '".$sunday."'";
	} else {

		$sql = "SELECT COUNT(meac_MemberActivityID) AS ToTalUse
						  FROM member_activity_trans
						  WHERE mere_MemberRegisterID = ".$register_id."
						  AND acti_ActivityID = ".$acti_ActivityID."
						  AND meac_Deleted=''
						  AND  meac_CreatedDate 
						  BETWEEN '".$monday."'
						  AND '".$sunday."'";
	}

	$total_use = $oDB->QueryOne($sql);

	if ($total_use < $acti_Qty) { $total = $acti_Qty-$total_use; } 
	else { $total = 0; }

	$arrayName = array('total' => $total, 'status' => $status);

	return $arrayName;
}


function ActiRepetitionMonth($register_id,$member_id,$acti_ActivityID,$acti_Qty,$card_id,$acti_QtyPerData,$date_use) {

	$oDB = new DBI();

	$status = "false";

	$date = date('d', $date_use);

	$date_week = explode(',', $acti_QtyPerData);

	if (count($date_week) > 0) {
		
		foreach ($date_week as $date_select) {

			if ($date_select == $date || $date_select == "") { $status = "true"; }
		}
	}

	$firstday = date('Y-m-d', strtotime('first day of this month',$date_use));
	$firstday .='%'; 
	$lastday = date('Y-m-d', strtotime('last day of this month',$date_use));
	$lastday .='%'; 

	if ($register_id == 0) {

		$sql = "SELECT COUNT(meac_MemberActivityID) AS ToTalUse
				FROM member_activity_trans
				WHERE card_CardID = ".$card_id."
				AND acti_ActivityID = ".$acti_ActivityID." 
				AND meac_Deleted=''
				AND  meac_CreatedDate 
				BETWEEN '".$firstday."' 
				AND '".$lastday."'";
	} else {

		$sql = "SELECT COUNT(meac_MemberActivityID) AS ToTalUse
				FROM member_activity_trans
				WHERE mere_MemberRegisterID = ".$register_id."
				AND acti_ActivityID = ".$acti_ActivityID." 
				AND meac_Deleted=''
				AND  meac_CreatedDate 
				BETWEEN '".$firstday."' 
				AND '".$lastday."'";
	}

	$total_use = $oDB->QueryOne($sql);	
	
	if ($total_use < $acti_QtyMember) { $total = $acti_QtyMember-$total_use; } 
	else { $total = 0; }

	$arrayName = array('total' => $total, 'status' => $status);

	return $arrayName;
}


function ActiRepetitionNotSpecific($register_id,$member_id,$acti_ActivityID,$acti_Qty,$card_id,$acti_QtyPerData,$date_use) {

	$oDB = new DBI();

	if ($register_id == 0) {

		$sql = "SELECT COUNT(meac_MemberActivityID) AS ToTalUse
				FROM member_activity_trans
				WHERE card_CardID = ".$card_id."
				AND meac_Deleted=''
				AND acti_ActivityID = ".$acti_ActivityID."";
	} else {

		$sql = "SELECT COUNT(meac_MemberActivityID) AS ToTalUse
				FROM member_activity_trans
				WHERE mere_MemberRegisterID = ".$register_id."
				AND meac_Deleted=''
				AND acti_ActivityID = ".$acti_ActivityID."";
	}

	$total_use = $oDB->QueryOne($sql);

	if ($total_use < $acti_Qty) { $total = $acti_Qty - $total_use; } 
	else { $total = 0; }

	return $total;
}


function UsePCAB($pre_code,$head_id,$register_id,$member_id,$card_id,$branch_id,$member_time,$time_insert,$privilege_id,$type,$status,$OTP) {

	$oDB = new DBI();

	$time_only = date("H:i:s");
		
	switch ($type) {
		case 'MP' :
			$priv_PrivilegeID 		= "priv_PrivilegeID";
			$mepe_MemberPrivlegeID 	= "mepe_MemberPrivlegeID";
			$table 			  		= "privilege";
			$name			  		= "mepe";
			break;
		case 'MC' :
			$priv_PrivilegeID 		= "coup_CouponID";
			$mepe_MemberPrivlegeID 	= "meco_MemberCouponID";
			$table 			  		= "coupon";
			$name			  		= "meco";
			break;
		default :
			$priv_PrivilegeID 		= "acti_ActivityID";
			$mepe_MemberPrivlegeID 	= "meac_MemberActivityID";
			$table 			  		= "activity";
			$name			  		= "meac";
			break;
	}

	if ($pre_code) {
		
		$length = 6;
	 	$randomString = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
	 	$id_use = $pre_code.$randomString;

	} else {
		
		$length = 7;
	 	$randomString = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
	 	$id_use = $type.$randomString;
	}

 	$x = 0;

 	while($x < 1) {

    	$member_use_privilege_id = "SELECT ".$mepe_MemberPrivlegeID." FROM member_".$table."_trans
    								WHERE ".$mepe_MemberPrivlegeID." = ".$id_use." LIMIT 1 ";

		$oRes = $oDB->Query($member_use_privilege_id);
		$rowCount = mysql_num_rows($oRes);

 		if($rowCount == 0) {

 	  		$x++;
 	  
 		} else {

			if ($pre_code) {
				
			 	$randomString = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
			 	$id_use = $pre_code.$randomString;

			} else {
				
			 	$randomString = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
			 	$id_use = $type.$randomString;
			}
 		}

 	}// while

 	if ($OTP) { $status = 'Pending'; }
 	else { $status = 'Active'; }
 

 	$strsqlInsertUsePrivilege = "INSERT INTO 
					 			member_".$table."_trans(
					 			mere_MemberRegisterID,
					 			meth_MemberTransactionHID,
					 			memb_MemberID,
					 			card_CardID,
					 			brnc_BranchID,
					 			".$mepe_MemberPrivlegeID.",
					 			".$name."_CreatedDate,
					 			".$name."_UpdatedDate,
					 			".$name."_CreatedBy,
					 			".$name."_UpdatedBy,
					 			".$priv_PrivilegeID.",
					 			".$name."_Status,
					 			".$name."_Platform,
					 			".$name."_OTP)
					 			VALUES (
					 			'".$register_id."',
					 			'".$head_id."',
					 			'".$member_id."',
					 			'".$card_id."',
					 			'".$branch_id."',
					 			'".$id_use."',
					 			'".$member_time."',
					 			'".$time_insert."',
					 			'".$_SESSION['UID']."',
					 			'".$_SESSION['UID']."',
					 			'".$privilege_id."',
					 			'".$status."',
					 			'Insert',
					 			'".$OTP."')";

	$oDB->QueryOne($strsqlInsertUsePrivilege);
		
	return $id_use;
}


function insert_point($type,$privilege_id,$PrivlegeCode,$branch_id,$card_id,$member_register_id,$recieve_no,$amount,$member_time,$time_insert,$motivation_id) {

	$oDB = new DBI();

	// $sql = 'SELECT mopp_MotivationPointID ';
	// $sql .= 'FROM motivation_plan_point mop ';
	// $sql .= 'WHERE mop.mopp_Status = "T" ';
	// $sql .= 'AND mop.mopp_Deleted != "T" ';

	// if ($type == "p") {
		
	// 	$sql .= 'AND mop.mopp_PrivilegeID = "'.$privilege_id.'"';
	// 	$sql .= 'AND mop.mopp_PrivilegeType = "Privilege"';
	// 	$type = 'priv_PrivilegeID';
	
	// } else if ($type == "c" || $type == "h") {
		
	// 	$sql .= 'AND mop.mopp_PrivilegeID = "'.$privilege_id.'"';
	// 	$sql .= 'AND mop.mopp_PrivilegeType = "Coupon"';
	// 	$type = 'coup_CouponID';

	// } else {
		
	// 	$sql .= 'AND mop.mopp_PrivilegeID = "'.$privilege_id.'"';
	// 	$sql .= 'AND mop.mopp_PrivilegeType = "Activity"';
	// 	$type = 'acti_ActivityID';
	// }

	// $motivation_id = $oDB->QueryOne($sql);

 	
 	if ($motivation_id) {

 		$motivation_sql = 'SELECT *
 							FROM motivation_plan_point mp
 							WHERE mp.mopp_MotivationPointID = '.$motivation_id;

		$oRes = $oDB->Query($motivation_sql);
		$motivation = $oRes->FetchRow(DBI_ASSOC);

		$moph_Method = $motivation["mopp_Method"];
		$moph_CollectionMethod = $motivation["mopp_CollectionMethod"];
		$moph_PeriodTime = $motivation["mopp_PeriodTime"];
		$moph_PeriodType = $motivation["mopp_PeriodType"];
		$moph_PeriodTypeEnd = $motivation["mopp_PeriodTypeEnd"];
		$mopo_UseAmount = $motivation["mopp_UseAmount"];
		$mopo_MultipleStartDate = $motivation["mopp_MultipleStartDate"];
		$mopo_MultipleEndDate = $motivation["mopp_MultipleEndDate"];
		$StartDate = $motivation["mopp_StartDate"];
		$EndDate = $motivation["mopp_EndDate"];
		$mopo_PointQty = $motivation["mopp_PointQty"];
		$mopo_Multiple = $motivation["mopp_Multiple"];

		$StartDate = date("Y-m-d", strtotime($StartDate));
		$EndDate = date("Y-m-d", strtotime($EndDate));
		$pointRecive = 0;

		$date_create = date("Y-m-d");
		$date_expire = "";

		if ($moph_CollectionMethod == "Fix") {
				
			$date_expire = $EndDate; 

		} else if ($moph_CollectionMethod == "No") {
				
			$date_expire = "";

		} else if ($moph_CollectionMethod == "Exp") {

			if ($moph_PeriodType == "M") {

				$time = "+".$moph_PeriodTime." months";
				$date_expire = date('Y-m-t', strtotime($time, strtotime($date_create)));

			} else if ($moph_PeriodType == "Y") {

				if ($moph_PeriodTypeEnd == "M") {

					$time = "+".$moph_PeriodTime." years";
					$date_expire = date('Y-m-t', strtotime($time, strtotime($date_create)));

				} else if ($moph_PeriodTypeEnd == "Y") {

					$time = "+".$moph_PeriodTime." years";
					$date_expire = date('Y-12-t', strtotime($time, strtotime($date_create)));
						
				}	
			}
		}

		if ($amount >= $mopo_UseAmount) {
				
			$pointRecive = check_type_motivation($moph_Method, $mopo_UseAmount, $amount, $mopo_PointQty);

			if ($mopo_Multiple > 0 && $mopo_MultipleStartDate != "0000-00-00" && $mopo_MultipleEndDate != "0000-00-00") {
					
				$MultipleStartDate = date("Y-m-d", strtotime($mopo_MultipleStartDate));
				$MultipleEndDate = date("Y-m-d", strtotime($mopo_MultipleEndDate));
				$date_create = date("Y-m-d", strtotime($date_create));

				if ($date_create >= $mopo_MultipleStartDate && $date_create <= $mopo_MultipleEndDate && $mopo_Multiple > 0 ) {
						
					$pointRecive = $pointRecive * $mopo_Multiple;
				}
			}

			# OTP

		 	$sql_otp = 'SELECT otp_pc
		 					FROM mi_brand
		 					LEFT JOIN mi_card
		 					ON mi_card.brand_id = mi_brand.brand_id
		 					WHERE mi_card.card_id="'.$card_id.'"';
		 	$OTP = $oDB->Query($sql_otp);

		 	if ($OTP == 'T') { $status = "Pending"; } 
		 	else { $status = 'Active'; }

			# POINT

			$sql =	"INSERT INTO member_motivation_point_trans(
				 					".$type.",
									mere_MemberRegisterID,
									memp_CreatedDate,
									memp_UpdatedDate,
									memp_CollectedDate,
									mepe_MemberPrivlegeID,
									brnc_BranchID,
									mopo_MotivationPointID,
									memp_Status,
									memp_ReceiptNo,
									memp_PointQty,
									memp_ReceiptAmount,
									memp_CreatedBy,
									memp_UpdatedBy,
									memp_CollectedBy,
									memp_Platform,
									memp_ExpiredDate
									)
									VALUES (
									'".$privilege_id."',
									'".$member_register_id."',
									'".$member_time."',
									'".$time_insert."',
									'".$time_insert."',
									'".$PrivlegeCode."',
									'".$branch_id."',
									'".$motivation_id."',
									'".$status."',
									'".$recieve_no."',
									'".$pointRecive."',
									'".$amount."',
									'".$_SESSION['UID']."',
									'".$_SESSION['UID']."',
									'".$_SESSION['UID']."',
									'Insert',
									'".$date_expire."'
									)";

			$oDB->QueryOne($sql);
		}
 	}
}


function insert_stamp($type,$privilege_id="0",$branch_id,$card_id,$member_register_id,$PrivlegeCode,$member_time,$time_insert,$motivation_id) {
	
	$oDB = new DBI();

	$time_only = date("H:i:s");

    $response = array();
    $multiple = 0;


 //    $sql = 'SELECT *';
 //    $sql .= 'FROM motivation_plan_stamp ms ';
 //    $sql .= 'WHERE ms.mops_Status = "T" AND ms.mops_Deleted != "T" ';


	// if ($type == "p") {
		
	// 	$sql .= 'AND ms.mops_PrivilegeID = "'.$privilege_id.'" 
	// 			AND ms.mops_PrivilegeType = "Privilege"';
	// 	$type = 'priv_PrivilegeID';
	
	// } else if ($type == "c" || $type == "h") {
		
	// 	$sql .= 'AND ms.mops_PrivilegeID = "'.$privilege_id.'" 
	// 			AND ms.mops_PrivilegeType = "Coupon"';
	// 	$type = 'coup_CouponID';

	// } else {
		
	// 	$sql .= 'AND ms.mops_PrivilegeID = "'.$privilege_id.'" 
	// 			AND ms.mops_PrivilegeType = "Activity"';
	// 	$type = 'acti_ActivityID';
	// }

   
	// $oRes = $oDB->Query($sql);
	// $motivation = $oRes->FetchRow(DBI_ASSOC);

     if($motivation_id){

        $most_MotivationStampID = $motivation['mops_MotivationStampID'];
        $most_Multiple = $motivation['mops_Multiple'];
        $most_MultipleStartDate = $motivation['mops_MultipleStartDate'];
        $most_MultipleEndDate = $motivation['mops_MultipleEndDate'];
        $mosh_StampQty = $motivation['mops_StampQty'];
        $mosh_CollectionMethod = $motivation['mops_CollectionMethod'];
        $mosh_PeriodTime = $motivation['mops_PeriodTime'];
     	$mosh_PeriodType = $motivation['mops_PeriodType'];
     	$mosh_PeriodTypeEnd = $motivation['mops_PeriodTypeEnd'];
        $mosh_StartDate = $motivation['mops_StartDate'];
        $mosh_EndDate = $motivation['mops_EndDate'];
        $coty_CollectionTypeID = $motivation['mops_CollectionTypeID'];
        $member_register_id = $member_register_id;

		$StartDate = date("Y-m-d", strtotime($mosh_StartDate));
		$EndDate = date("Y-m-d", strtotime($mosh_EndDate));
		$stampTotal = 0;

		$date_create = date("Y-m-d");
		$date_expire = "";


        if ($mosh_CollectionMethod == "Fix") {
				
			$date_expire = $EndDate; 

		} else if ($mosh_CollectionMethod == "No") {
				
			$date_expire = "";

		} else if ($mosh_CollectionMethod == "Exp") {

			if ($mosh_PeriodType == "M") {

				$time = "+".$mosh_PeriodTime." months";
				$date_expire = date('Y-m-t', strtotime($time, strtotime($date_create)));

			} else if ($mosh_PeriodType == "Y") {

				if ($mosh_PeriodTypeEnd == "M") {

					$time = "+".$moph_PeriodTime." years";
					$date_expire = date('Y-m-t', strtotime($time, strtotime($date_create)));

				} else if ($mosh_PeriodTypeEnd == "Y") {

					$time = "+".$mosh_PeriodTime." years";
					$date_expire = date('Y-12-t', strtotime($time, strtotime($date_create)));
				}
			}
		}
                    

        if($most_Multiple > 0){
                        
            $dateOut = checkOutDate($most_MultipleStartDate, $most_MultipleEndDate);
            
            ($dateOut) ? 

            $multiple = $most_Multiple * $mosh_StampQty : $multiple = $mosh_StampQty;
                    
        } else { $multiple = $mosh_StampQty; }

        
        $stampTotal =  $multiple;



		# OTP
			
		 $sql_otp = 'SELECT otp_pc
		 				FROM mi_brand
		 				LEFT JOIN mi_card
		 				ON mi_card.brand_id = mi_brand.brand_id
		 				WHERE mi_card.card_id="'.$card_id.'"';
		 $OTP = $oDB->Query($sql_otp);

		 if ($OTP == 'T') { $status = "Pending"; } 
		 else { $status = 'Active'; }


        # STAMP

        if ($member_time <= $time_insert) {

	        $sql = 'INSERT INTO member_motivation_stamp_trans(
	        			mepe_MemberPrivlegeID, 
	        			most_MotivationStampID,
	        			priv_PrivilegeID, 
	        			brnc_BranchID, 
	        			mere_MemberRegisterID, 
	        			mems_StampQty, 
	        			coty_CollectionTypeID,
	        			mems_CreatedBy, 
	        			mems_CreatedDate, 
	        			mems_UpdatedBy, 
	        			mems_UpdatedDate, 
	        			mems_CollectedBy, 
	        			mems_CollectedDate, 
	        			mems_Status, 
	        			mems_Platform,
	        			mems_ExpiredDate) 

	        			VALUES("'.$PrivlegeCode.'",
	        			"'.$most_MotivationStampID.'", 
	        			"'.$privilege_id.'",
	        			"'.$branch_id.'", 
	        			"'.$member_register_id.'", 
	        			"'.$stampTotal.'",
	        			"'.$coty_CollectionTypeID.'",
	        			"'.$_SESSION['UID'].'", 
	        			"'.$member_time.'",
	        			 "'.$_SESSION['UID'].'",
	        			 "'.$time_insert.'", 
	        			"'.$_SESSION['UID'].'", 
	        			"'.$time_insert.'",
	        			"'.$status.'", 
	        			"Insert",
	        			"'.$date_expire.'")';

	        $oDB->Query($sql);
        }	
    } 
}


function check_type_motivation($moph_Method,$mopo_UseAmount,$amount,$mopo_PointQty) {
	
	if ($moph_Method == "One Time") { return $mopo_PointQty; }
	else {
		
		$totalPoint = $amount / $mopo_UseAmount;
		$totalPoint = intval($totalPoint);

		return $totalPoint;
	}
}






#  branch dropdownlist

$where_branch = '';

if($_SESSION['user_type_id_ses']>1){

	if($_SESSION['user_type_id_ses']>2){

		$where_branch = ' branch_id="'.$_SESSION['user_branch_id'].'" ';
	
	} else {

		$where_branch = ' brand_id="'.$_SESSION['user_brand_id'].'" ';
	}

}

$as_branch_id = dropdownlist_from_table($oDB,'mi_branch','branch_id','name',$where_branch,' ORDER BY name ASC');

$oTmp->assign('branch', $as_branch_id);




$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_transaction');

$oTmp->assign('content_file', 'transaction/member_insert.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());

}

//========================================//

?>

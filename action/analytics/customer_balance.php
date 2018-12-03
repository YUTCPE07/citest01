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

if ($_SESSION['role_action']['customer_balance']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$id = $_REQUEST['id'];
$path_upload_member = $_SESSION['path_upload_member'];

//========================================//


$sql ='SELECT * FROM mb_member WHERE member_id = "'.$id.'"';
$oRes = $oDB->Query($sql);
$asData = array();

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	if ($axRow['date_birth']=="0000-00-00") { $axRow['date_birth'] = "-"; } 
	else { $axRow['date_birth'] = DateOnly($axRow['date_birth']); }

	if ($axRow['nickname']=="") { $axRow['nickname']="-"; }
	if ($axRow['firstname']=="") { $axRow['firstname']="-"; }
	if ($axRow['lastname']=="") { $axRow['lastname']="-"; }

	$asData = $axRow;
}


if($_SESSION['user_type_id_ses']==2){ $where_brand = 'and mi_brand.brand_id = "'.$_SESSION['user_brand_id'].'"'; }

$branch_id = "";

if($_SESSION['user_type_id_ses']==3){ $branch_id = $_SESSION['user_branch_id']; }


$sql_card_member = 'SELECT DISTINCT
						mi_card.card_id,
						mi_card.image AS card_image,
						mi_card.image_newupload,
						mi_card.path_image,
						mi_card.name AS card_name,
						mi_card.flag_multiple AS card_multiple,
						mi_card.period_type,
						mi_card.period_type_other,
						mi_card.member_fee,
						mi_card_type.name AS card_type_name,
						mi_brand.name AS brand_name,
						mb_member_register.date_create,
						mb_member_register.date_expire
						FROM mb_member_register
						INNER JOIN mi_card
						ON mb_member_register.card_id = mi_card.card_id
						LEFT JOIN mi_card_type
						ON mi_card_type.card_type_id = mi_card.card_type_id
						INNER JOIN mi_brand
						ON mi_brand.brand_id = mi_card.brand_id
						WHERE mb_member_register.member_id = '.$id.'
						AND mb_member_register.flag_del = ""
						'.$where_brand.'
						GROUP BY mb_member_register.card_id';

$data_table = '';

$oRes_card_member = $oDB->Query($sql_card_member);

$table_member = "";

if($_SESSION['user_branch_id']){

	$where_branch .= ' AND mi_branch.branch_id = "'.$_SESSION['user_branch_id'].'"';
}


$x = 0;

while ($axRow_card_member = $oRes_card_member->FetchRow(DBI_ASSOC)){

	if ($x != 0) { $table_member .= '<hr>'; }
	else { $table_member .= '<br>'; }

	$card_id = $axRow_card_member["card_id"];

	$x++;


	# CARD IMAGE

	if($axRow_card_member['card_image']!=''){

		$axRow_card_member['card_image'] = '<img src="../../upload/'.$axRow_card_member['path_image'].$axRow_card_member['card_image'].'" class="image_border img-rounded" height="100px"/>';

	} else if ($axRow_card_member['image_newupload']!='') {

		$axRow_card_member['card_image'] = '<img src="../../upload/'.$axRow_card_member['path_image'].$axRow_card_member['image_newupload'].'" class="image_border img-rounded" height="100px"/>';

	} else {

		$axRow_card_member['card_image'] = '<img src="../../images/card_privilege.jpg" class="image_border img-rounded" height="100px"/>';
	}


	# CARD PERIOD

	if ($axRow_card_member['period_type'] == '1') { 

		$axRow_card_member['period_type'] = DateOnly($axRow_card_member['date_expire']);	

	} else if ($axRow_card_member['period_type'] == '2') { 

		$axRow_card_member['period_type'] = $axRow_card_member['period_type_other'].' Months';	

	} else if ($axRow_card_member['period_type'] == '3') { 

		$axRow_card_member['period_type'] = $axRow_card_member['period_type_other'].' Years';	

	} else if ($axRow_card_member['period_type'] == '4') { 

		$axRow_card_member['period_type'] = 'Member Life Time';	
	}


	# BRAND

	$sql_brand_id = 'SELECT brand_id 
						FROM mi_card 
						WHERE card_id = "'.$card_id.'"';

	$brand_id = $oDB->QueryOne($sql_brand_id);

	# BRANCH

	$sql_branch = 'SELECT name as txt,
						branch_id as id 
						FROM mi_branch 
						WHERE name LIKE "%'.$search_branch.'%" AND brand_id = "'.$brand_id.'" '.$where_branch.'';

	# MEMBER CARD

	$sql_card = 'SELECT member_card_code
						FROM mb_member_register
						WHERE card_id="'.$card_id.'"
						AND member_id="'.$id.'"';
	$card_code = $oDB->QueryOne($sql_card);

	# PRIVILEGE

	$sql_privilege = 'SELECT DISTINCT
						privilege.priv_Name as txt,
						privilege.priv_PrivilegeID as id,
						privilege.priv_Status,
						privilege.priv_MotivationID,
						privilege.priv_Motivation

						FROM privilege 
							
						LEFT JOIN mi_card_register
						ON mi_card_register.privilege_id = privilege.priv_PrivilegeID

						LEFT JOIN mi_card
						ON mi_card.card_id = mi_card_register.card_id

						WHERE privilege.bran_BrandID = "'.$brand_id.'" 
						AND mi_card_register.status="0" 
						AND mi_card_register.card_id ="'.$card_id.'"';

	# COUPON

	$sql_coupon = 'SELECT DISTINCT
						coupon.coup_Name as txt,
						coupon.coup_CouponID as id,
						coupon.coup_Status,
						coupon.coup_MotivationID,
						coupon.coup_Motivation

						FROM coupon 
							
						LEFT JOIN mi_card_register
						ON mi_card_register.coupon_id = coupon.coup_CouponID

						LEFT JOIN mi_card
						ON mi_card.card_id = mi_card_register.card_id

						WHERE coupon.bran_BrandID = "'.$brand_id.'" 
							AND mi_card_register.status="0" 
							AND mi_card_register.card_id ="'.$card_id.'"
							AND coupon.coup_Birthday!="T"';

	# HBD COUPON

	$sql_hbd = 'SELECT DISTINCT
					coupon.coup_Name as txt,
					coupon.coup_CouponID as id,
					coupon.coup_Status,
					coupon.coup_MotivationID,
					coupon.coup_Motivation

					FROM coupon 
							
					LEFT JOIN mi_card_register
					ON mi_card_register.coupon_id = coupon.coup_CouponID

					LEFT JOIN mi_card
					ON mi_card.card_id = mi_card_register.card_id

					WHERE coupon.bran_BrandID = "'.$brand_id.'" 
					AND mi_card_register.status="0" 
					AND mi_card_register.card_id ="'.$card_id.'"
					AND coupon.coup_Birthday="T"';

	# ACTIVITY

	$sql_activity = 'SELECT DISTINCT
						activity.acti_Name as txt,
						activity.acti_ActivityID as id,
						activity.acti_Status,
						activity.acti_MotivationID,
						activity.acti_Motivation

						FROM activity 
							
						LEFT JOIN mi_card_register
						ON mi_card_register.activity_id = activity.acti_ActivityID

						LEFT JOIN mi_card
						ON mi_card.card_id = mi_card_register.card_id

						WHERE activity.bran_BrandID = "'.$brand_id.'" 
						AND mi_card_register.status="0" 
						AND mi_card_register.card_id ="'.$card_id.'"';

	$i = 0;

	$oRes_privilege = $oDB->Query($sql_privilege)or die(mysql_error());

	$check_priv = $oDB->QueryOne($sql_privilege);
		
	$privilege_id = array();


	$oRes_coupon = $oDB->Query($sql_coupon)or die(mysql_error());

	$check_coup = $oDB->QueryOne($sql_coupon);
		
	$coupon_id = array();


	$oRes_hbd = $oDB->Query($sql_hbd)or die(mysql_error());

	$check_hbd = $oDB->QueryOne($sql_hbd);
		
	$hbd_id = array();


	$oRes_activity = $oDB->Query($sql_activity)or die(mysql_error());

	$check_acti = $oDB->QueryOne($sql_activity);
		
	$activity_id = array();


	$table_member .= '<table>
						<tr>
							<td rowspan="6" width="200px" valign="top" style="text-align:center;">
							'.$axRow_card_member['card_image'].'</td>
							<td style="text-align:right;width:100px;"><b>Brand</b></td>
							<td style="text-align:center;width:30px;"><b>:</b></td>
							<td><b>'.$axRow_card_member['brand_name'].'</b></td>
						</tr>
						<tr>
							<td style="text-align:right"><b>Card</b></td>
							<td style="text-align:center;"><b>:</b></td>
							<td><b>'.$axRow_card_member['card_name'].'</b></td></tr>
						<tr>
							<td style="text-align:right"><b>Member Card</b></td>
							<td style="text-align:center;"><b>:</b></td>
							<td><b>'.$card_code.'</b></td></tr>
						<tr>
							<td style="text-align:right"><b>Type</b></td>
							<td style="text-align:center;"><b>:</b></td>
							<td><b>'.$axRow_card_member['card_type_name'].'</b></td></tr>
						<tr>
							<td style="text-align:right"><b>Period</b></td>
							<td style="text-align:center;"><b>:</b></td>
							<td><b>'.$axRow_card_member['period_type'].'</b></td></tr>
						<tr>
							<td style="text-align:right"><b>Member Fee</b></td>
							<td style="text-align:center;"><b>:</b></td>
							<td><b>'.number_format($axRow_card_member['member_fee'],2).' ฿</b></td></tr>
						</table>
					<br>';


	## TABLE USE ##

	$a=0; // privilege
	$table_priv = '';

	$b=0; // coupon
	$table_coup = '';

	$c=0; // activity
	$table_acti = '';

	$d=0; // hbd
	$table_hbd = '';

	$total = 0;

	$oRes_branch = $oDB->Query($sql_branch)or die(mysql_error());

	$table_member .= "<div class='table-responsive'>
						<table class='table table-bordered' style='background-color:white;'>
							<tr style='background-color:#003369;color:white;text-align:center'>
								<td rowspan='3'><b>No.</b></td>
								<td rowspan='2' colspan='2'><b>Period</b></td>";

	# PRIVILEGE

	while ($axRow_privilege = $oRes_privilege->FetchRow(DBI_ASSOC)) {

		if ($axRow_privilege['priv_Status'] == "Pending") { $status_priv = "#f0ad4e"; } 
		else { $status_priv = "#5cb85c"; }

		$table_priv .= "<td colspan='2' style='background-color:#CCC'><b>".$axRow_privilege['txt']."</b> 
							<span class='glyphicon glyphicon-certificate' style='color:".$status_priv.";'></span>
						</td>";

		$privilege_id[$a] = $axRow_privilege['id'];

		$a++;
	}

	if ($a != 0) { $table_member .= "<td colspan='".($a*2)."'><b>Privilege</b></td>"; }

	
	# COUPON

	while ($axRow_coupon = $oRes_coupon->FetchRow(DBI_ASSOC)) {

		if ($axRow_coupon['coup_Status'] == "Pending") { $status_priv = "#f0ad4e"; } 
		else { $status_priv = "#5cb85c"; }

		$table_coup .= "<td colspan='2' style='background-color:#CCC'><b>".$axRow_coupon['txt']."</b> 
							<span class='glyphicon glyphicon-certificate' style='color:".$status_priv.";'></span>
						</td>";

		$coupon_id[$b] = $axRow_coupon['id'];

		$b++;
	}

	if ($b != 0) { $table_member .= "<td colspan='".($b*2)."'><b>Coupon</b></td>"; }

	
	# BIRTHDAY

	while ($axRow_hbd = $oRes_hbd->FetchRow(DBI_ASSOC)) {

		if ($axRow_hbd['coup_Status'] == "Pending") { $status_priv = "#f0ad4e"; } 
		else { $status_priv = "#5cb85c"; }

		$table_hbd .= "<td colspan='2' style='background-color:#CCC'><b>".$axRow_hbd['txt']."</b> 
							<span class='glyphicon glyphicon-certificate' style='color:".$status_priv.";'></span>
						</td>";

		$hbd_id[$d] = $axRow_hbd['id'];

		$d++;
	}

	if ($d != 0) { $table_member .= "<td colspan='".($d*2)."'><b>Birthday Coupon</b></td>"; }

	
	# ACTIVITY

	while ($axRow_activity = $oRes_activity->FetchRow(DBI_ASSOC)) {

		if ($axRow_activity['acti_Status'] == "Pending") { $status_priv = "#f0ad4e"; } 
		else { $status_priv = "#5cb85c"; }

		$table_acti .= "<td colspan='2' style='background-color:#CCC'><b>".$axRow_activity['txt']."</b> 
							<span class='glyphicon glyphicon-certificate' style='color:".$status_priv.";'></span>
						</td>";

		$activity_id[$c] = $axRow_activity['id'];

		$c++;
	}	

	if ($c != 0) { $table_member .= "<td colspan='".($c*2)."'><b>Activity</b></td>";	}

	
	$table_member .= "</tr>
					<tr style='background-color:#EEEEEE;text-align:center'>
						".$table_priv."
						".$table_coup."
						".$table_hbd."
						".$table_acti."
					</tr>
					<tr style='background-color:#EEEEEE;text-align:center'>
						<td>Start Date</td>
						<td>End Date</td>";

	$total_td = $a+$b+$c+$d;

	for ($x=0; $x<$total_td ; $x++) { 

		$table_member .= "<td>Total</td><td>Balance</td>";
	}

	$table_member .= "</tr>";

	$sql_register = "SELECT mb_member_register.*
						FROM mb_member_register
						WHERE member_id='".$id."'
						AND card_id='".$card_id."'
						AND flag_del!='T'
						ORDER BY member_register_id";

	$oRes_regis = $oDB->Query($sql_register)or die(mysql_error());
	$r = 0;

	$table_total = "";

	$use_coup = array();
	$balance_coup = array();

	while ($axRow_regis = $oRes_regis->FetchRow(DBI_ASSOC)) {

		$r++;

		# START DATE

		if ($axRow_regis['period_type']=='4') { $end_date = '-'; }
		else { $end_date = DateOnly($axRow_regis['date_expire']); }

		if ($axRow_regis['date_start']=='0000-00-00') { $start_date = DateOnly($axRow_regis['date_create']); }
		else { $start_date = DateOnly($axRow_regis['date_start']); }

		$table_member .= "<tr>
							<td>".$r.".</td>
							<td style='text-align:center'>".$start_date."</td>
							<td style='text-align:center'>".$end_date."</td>";

		# PRIVILEGE

		for ($i=0; $i < $a; $i++) {

			$number_use = get_total_privilege_use($privilege_id[$i],$branch_id,$card_id,$id,'',$axRow_regis['member_register_id']);

			$table_member .= "<td style='text-align:center'>-</td>
								<td style='text-align:center'>-</td>";
		}

		# COUPON

		for ($i=0; $i < $b; $i++) {

			$sql_coupon_detail = 'SELECT coupon.*,
									mi_privilege_type.name AS privilege_type_name,
									mi_brand.name AS brand_name
									FROM coupon
									LEFT JOIN mi_privilege_type
									ON coupon.prty_PrivilegeTypeID = mi_privilege_type.privilege_type_id
									LEFT JOIN mi_brand
									ON mi_brand.brand_id = coupon.bran_BrandID
									WHERE coupon.coup_CouponID = "'.$coupon_id[$i].'"';

			$oRes_coup = $oDB->Query($sql_coupon_detail);
			$coupon = $oRes_coup->FetchRow(DBI_ASSOC);


			# COUPON IMAGE

			if($coupon['coup_Image']!=''){

				$coup_image = '<img src="../../upload/'.$coupon['coup_ImagePath'].$coupon['coup_Image'].'" height="150px" class="img-rounded image_border"/>';

			} else {

				$coup_image = '<img src="../../images/card_privilege.jpg" height="150px" class="img-rounded image_border"/>';
			}


			# REPRPTITION

			$time_member = 0;

			if ($coupon['coup_RepetitionMember']=='T') { $time_member = $coupon['coup_QtyMember']; }

			if ($coupon['coup_RepetitionMember']=='T' && $coupon['coup_QtyPerMember']=='Not Specific') {

				$total_use = $time_member;

			} else { $total_use = '-'; }

			$number_use = get_total_coupon_use($coupon_id[$i],"",$card_id,$id,'',$axRow_regis['member_register_id']);

			$total_balance = 0;

			if ($total_use != '-') { 

				$use_coup[$i] += $total_use;

				$total_balance = number_format($total_use-$number_use); 
				$total_use = number_format($total_use); 

			} else { $total_balance = '-'; }

			$view = '';

			if ($total_balance!='-') {

				$balance_coup[$i] += $total_balance;

				if ($total_balance == 0) { 
					$total_balance = '<span class="glyphicon glyphicon-minus" aria-hidden="true" style="color:rgba(119, 27, 19, 0.8)"></span>'; 
				}

				$view = "<br><a href='customer_privilege.php?regis_id=".$axRow_regis['member_register_id']."&id=".$coupon_id[$i]."&type=c'><button type='button' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-eye-open' aria-hidden='true'></span></button></a>";
			}

			$table_member .= "<td style='text-align:center'>".$total_use."</td>
								<td style='text-align:center'>".$total_balance.$view."</td>";
		}

		# BIRTHDAY COUPON

		for ($i=0; $i < $d; $i++) {

			$number_use = get_total_coupon_use($hbd_id[$i],"",$card_id,$id,'',$axRow_regis['member_register_id']);

			$table_member .= "<td style='text-align:center'>-</td>
								<td style='text-align:center'>-</td>";
		}

		# ACTIVITY

		for ($i=0; $i < $c; $i++) {

			$number_use = get_total_activity_use($acti_id[$i],"",$card_id,$id,'',$axRow_regis['member_register_id']);

			$table_member .= "<td style='text-align:center'>-</td>
								<td style='text-align:center'>-</td>";
		}

		$table_member .= "</tr>";
	}

	$table_member .= "<tr style='background-color:#EEE'>
						<td colspan='3' style='text-align:center;'>Total</td>";

	# PRIVILEGE

	for ($i=0; $i < $a; $i++) {

		$table_member .= "<td style='text-align:center'>-</td>
								<td style='text-align:center'>-</td>";
	}

	# COUPON

	for ($i=0; $i < $b; $i++) {

		# TOTAL

		if ($use_coup[$i] == '') { 

			$use_coup[$i] = '-'; 

		} elseif ($use_coup[$i] == '0') { 

			$use_coup[$i] = '<span class="glyphicon glyphicon-minus" aria-hidden="true" style="color:rgba(119, 27, 19, 0.8)"></span>'; 

		} else { $use_coup[$i] = number_format($use_coup[$i]); }

		# BALANCE

		if ($balance_coup[$i] == '') { 

			$balance_coup[$i] = '-'; 

		} elseif ($balance_coup[$i] == '0') { 

			$balance_coup[$i] = '<span class="glyphicon glyphicon-minus" aria-hidden="true" style="color:rgba(119, 27, 19, 0.8)"></span>'; 

		} else { $balance_coup[$i] = number_format($balance_coup[$i]); }


		$table_member .= "<td style='text-align:center'>".$use_coup[$i]."</td>
								<td style='text-align:center'>".$balance_coup[$i]."</td>";
	}

	# BIRTHDAY

	for ($i=0; $i < $d; $i++) {

		$table_member .= "<td style='text-align:center'>-</td>
								<td style='text-align:center'>-</td>";
	}

	# ACTIVITY

	for ($i=0; $i < $c; $i++) {

		$table_member .= "<td style='text-align:center'>-</td>
								<td style='text-align:center'>-</td>";
	}

	$table_member .= "</tr></table></div>";
}


$as_name_title_type = list_type_master_value($oDB,'name_title_type',$axRow['name_title_type']);
if ($as_name_title_type=="") { $as_name_title_type = "-"; }
$oTmp->assign('name_title_type', $as_name_title_type);


$oTmp->assign('data', $asData);
$oTmp->assign('table_member', $table_member);
$oTmp->assign('is_menu', 'is_analytics');
$oTmp->assign('content_file', 'analytics/customer_balance.htm');
$oTmp->display('layout/template.html');

//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
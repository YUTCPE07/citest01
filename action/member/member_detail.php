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


$sql ='SELECT * FROM mb_member WHERE member_id = "'.$id.'"';

$oRes = $oDB->Query($sql);

$asData = array();

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$i++;

	if ($axRow['date_birth']=="0000-00-00") { $axRow['date_birth'] = "-"; } 
	else { $axRow['date_birth'] = DateOnly($axRow['date_birth']); }

	if ($axRow['nickname']=="") { $axRow['nickname']="-"; }
	if ($axRow['firstname']=="") { $axRow['firstname']="-"; }
	if ($axRow['lastname']=="") { $axRow['lastname']="-"; }

	$asData = $axRow;

}

if($_SESSION['user_type_id_ses']>1){

	$where_brand = 'and mi_brand.brand_id = "'.$_SESSION['user_brand_id'].'"';
}



# CARD

$sql_card_member = 'SELECT DISTINCT
						COUNT(mb_member_register.member_register_id) AS count_card,
						mi_card.card_id,
						mi_card.image AS card_image,
						mi_card.image_newupload,
						mi_card.path_image,
						mi_card.name AS card_name,
						mi_card.flag_multiple AS card_multiple,
						mi_card.period_type,
						mi_card.period_type_other,
						mi_brand.name AS brand_name,
						mb_member_register.date_create,
						mb_member_register.date_expire
						FROM mb_member_register
						INNER JOIN mi_card
						ON mb_member_register.card_id = mi_card.card_id
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

	if ($x > 1) { $table_member .= '<hr>'; }
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
						WHERE  name LIKE "%'.$search_branch.'%" AND brand_id = "'.$brand_id.'" '.$where_branch.'';

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


	$table_member .= '<table class="myPopup">
						<tr>
							<td rowspan="4" width="200px" valign="top" style="text-align:center;">
							'.$axRow_card_member['card_image'].'</td>
							<td style="text-align:right;width:100px;"><b>Brand</b></td>
							<td style="text-align:center;width:30px;"><b>:</b></td>
							<td><b>'.$axRow_card_member['brand_name'].'</b></td>
						</tr>
						<tr>
							<td style="text-align:right"><b>Card</b></td>
							<td style="text-align:center;"><b>:</b></td>
							<td><b>'.$axRow_card_member['card_name'].'</b></td></tr>';

	if ($axRow_card_member['card_multiple']=='Yes') {

		$table_member .= '<tr>
							<td style="text-align:right"><b>Card Qty</b></td>
							<td style="text-align:center;"><b>:</b></td>
							<td><b>'.$axRow_card_member['count_card'].'</b></td></tr>
						<tr>
							<td style="text-align:right"><b>Period</b></td>
							<td style="text-align:center;"><b>:</b></td>
							<td><b>'.$axRow_card_member['period_type'].'</b></td></tr>
					</table>
					<br>';
	} else {

		$axRow_card_member['card_multiple'] = '';

		$table_member .= '<tr>
							<td style="text-align:right"><b>Registed Date</b></td>
							<td style="text-align:center;"><b>:</b></td>
							<td><b>'.DateOnly($axRow_card_member['date_create']).'</b></td></tr>
						<tr>
							<td style="text-align:right"><b>Expried Date</b></td>
							<td style="text-align:center;"><b>:</b></td>
							<td><b>'.DateOnly($axRow_card_member['date_expire']).'</b></td></tr>
					</table>
					<br>';
	}


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

	$table_member .= " &nbsp; <b>Privileges Use</b></span><br>
					<div class='table-responsive'>
						<table id='myTable' class='table table-bordered' style='background-color:white;'>
							<tr style='background-color:#003369;color:white;text-align:center'>
								<td rowspan='2' width='150px'><b>Branch \ Privilege</b></td>";

	# PRIVILEGE

	while ($axRow_privilege = $oRes_privilege->FetchRow(DBI_ASSOC)) {

		if ($axRow_privilege['priv_Status'] == "Pending") { $status_priv = "#f0ad4e"; } 
		else { $status_priv = "#5cb85c"; }

		$table_priv .= "<td><b>".$axRow_privilege['txt']."</b> 
							<span class='glyphicon glyphicon-certificate' style='color:".$status_priv.";'></span>
						</td>";

		$privilege_id[$a] = $axRow_privilege['id'];

		$a++;
	}

	if ($a != 0) { $table_member .= "<td colspan='".$a."'><b>Privilege</b></td>"; }

	
	# COUPON

	while ($axRow_coupon = $oRes_coupon->FetchRow(DBI_ASSOC)) {

		if ($axRow_coupon['coup_Status'] == "Pending") { $status_priv = "#f0ad4e"; } 
		else { $status_priv = "#5cb85c"; }

		$table_coup .= "<td><b>".$axRow_coupon['txt']."</b> 
							<span class='glyphicon glyphicon-certificate' style='color:".$status_priv.";'></span>
						</td>";

		$coupon_id[$b] = $axRow_coupon['id'];

		$b++;
	}

	if ($b != 0) { $table_member .= "<td colspan='".$b."'><b>Coupon</b></td>"; }

	
	# BIRTHDAY

	while ($axRow_hbd = $oRes_hbd->FetchRow(DBI_ASSOC)) {

		if ($axRow_hbd['coup_Status'] == "Pending") { $status_priv = "#f0ad4e"; } 
		else { $status_priv = "#5cb85c"; }

		$table_hbd .= "<td><b>".$axRow_hbd['txt']."</b> 
							<span class='glyphicon glyphicon-certificate' style='color:".$status_priv.";'></span>
						</td>";

		$hbd_id[$d] = $axRow_hbd['id'];

		$d++;
	}

	if ($d != 0) { $table_member .= "<td colspan='".$d."'><b>Birthday Coupon</b></td>"; }

	
	# ACTIVITY

	while ($axRow_activity = $oRes_activity->FetchRow(DBI_ASSOC)) {

		if ($axRow_activity['acti_Status'] == "Pending") { $status_priv = "#f0ad4e"; } 
		else { $status_priv = "#5cb85c"; }

		$table_acti .= "<td><b>".$axRow_activity['txt']."</b> 
							<span class='glyphicon glyphicon-certificate' style='color:".$status_priv.";'></span>
						</td>";

		$activity_id[$c] = $axRow_activity['id'];

		$c++;
	}	

	if ($c != 0) { $table_member .= "<td colspan='".$c."'><b>Activity</b></td>";	}

	
	$table_member .= "<td rowspan='2'><b>รวม</b></td>
					</tr>
					<tr style='background-color:#EEEEEE;text-align:center'>
						".$table_priv."
						".$table_coup."
						".$table_hbd."
						".$table_acti."
					</tr>
					<tr>";

	$all_use = 0;

	while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)) {

		$total_branch = 0;

		$table_member .= "<tr><td class='td_head'>".$axRow_branch['txt']."</td>";

		for ($i=0; $i < $a; $i++) {

			$number_branch = get_total_privilege_use($privilege_id[$i],$axRow_branch['id'],$card_id,$id,$axRow_card_member['card_multiple']);

			$table_member .= "<td style='text-align:center'>".number_format($number_branch)."</td>";

			$total_branch += $number_branch;
		}

		for ($i=0; $i < $b; $i++) {

			$number_branch = get_total_coupon_use($coupon_id[$i],$axRow_branch['id'],$card_id,$id,$axRow_card_member['card_multiple']);

			$table_member .= "<td style='text-align:center'>".number_format($number_branch)."</td>";

			$total_branch += $number_branch;
		}

		for ($i=0; $i < $d; $i++) {

			$number_branch = get_total_coupon_use($hbd_id[$i],$axRow_branch['id'],$card_id,$id,$axRow_card_member['card_multiple']);

			$table_member .= "<td style='text-align:center'>".number_format($number_branch)."</td>";

			$total_branch += $number_branch;
		}

		for ($i=0; $i < $c; $i++) {

			$number_branch = get_total_activity_use($activity_id[$i],$axRow_branch['id'],$card_id,$id,$axRow_card_member['card_multiple']);

			$table_member .= "<td style='text-align:center'>".number_format($number_branch)."</td>";

			$total_branch += $number_branch;
		}

		$all_use += $total_branch;

		$table_member .= "<td style='text-align:center;background-color:#EEEEEE'><b>".number_format($total_branch)."</b></td>";

		$table_member .= "</tr>";
			
	}

	if(!$_SESSION['user_branch_id']) {
														
		$table_member .= "<tr>";
									
		$table_member .= "<td style='background-color:#003369;color:white;text-align:center'><b>Total Use</b></td>";
				
		for ($i=0; $i < $a; $i++) {

			$number_privilege =	get_total_privilege_use($privilege_id[$i],"",$card_id,$id,$axRow_card_member['card_multiple']);

			$table_member .= "<td style='text-align:center;background-color:#EEEEEE'><b>".number_format($number_privilege)."</b></td>";						
		}

		for ($i=0; $i < $b; $i++) {

			$number_branch = get_total_coupon_use($coupon_id[$i],"",$card_id,$id,$axRow_card_member['card_multiple']);

			$table_member .= "<td style='text-align:center;background-color:#EEEEEE'><b>".number_format($number_branch)."</b></td>";
		}

		for ($i=0; $i < $d; $i++) {

			$number_branch = get_total_coupon_use($hbd_id[$i],"",$card_id,$id,$axRow_card_member['card_multiple']);

			$table_member .= "<td style='text-align:center;background-color:#EEEEEE'><b>".number_format($number_branch)."</b></td>";
		}

		for ($i=0; $i < $c; $i++) {

			$number_branch = get_total_activity_use($activity_id[$i],"",$card_id,$id,$axRow_card_member['card_multiple']);

			$table_member .= "<td style='text-align:center;background-color:#EEEEEE'><b>".number_format($number_branch)."</b></td>";
		}
									
		$table_member .= "<td style='background-color:#003369;color:white;text-align:center'><b>".number_format($all_use)."</b></td>";
									
		$table_member .= "</tr>";	
	}
							
	$table_member .="</tr></table>";

	$table_member .= "</div>";



	# POINT #

	$table_point = '';

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


	$a = 0;
	$b = 0;
	$c = 0;
	$d = 0;

	$oRes_branch = $oDB->Query($sql_branch)or die(mysql_error());


	$icon_sql = "SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID = 3";
	$icon = $oDB->QueryOne($icon_sql);
	$icon ='<img src="../../upload/collection_upload/'.$icon.'" width="12px" height="12px" style="margin-bottom:3px">';

	$table_point .= " &nbsp; <b>Point Collects</b><br>
						<div class='table-responsive'>
							<table id='myTable' class='table table-bordered' style='background-color:white;' >
								<tr style='background-color:#003369;color:white;text-align:center'>
									<td width='150px'><b>Branch \ Privilege</b></td>";

	# PRIVILEGE

	while ($axRow_privilege = $oRes_privilege->FetchRow(DBI_ASSOC)) {

		if ($axRow_privilege['priv_MotivationID'] != 0 && $axRow_privilege['priv_Motivation'] == 'Point') {

			if ($axRow_privilege['priv_Status'] == "Pending") { $status_priv = "#f0ad4e"; } 
			else { $status_priv = "#5cb85c"; }

			$plan_sql = "SELECT mopp_Name, mopp_PointQty, mopp_UseAmount FROM motivation_plan_point WHERE mopp_MotivationPointID='".$axRow_privilege['priv_MotivationID']."'";
			$get_point = $oDB->Query($plan_sql);
			$point = $get_point->FetchRow(DBI_ASSOC);

			$motivation_plan = '<br>'.$point['mopp_Name'].'<br>('.number_format($point['mopp_UseAmount'],2).' ฿ / '.$point['mopp_PointQty'].' '.$icon.')';

			$table_point .= "<td><b>".$axRow_privilege['txt']."</b> <span class='glyphicon glyphicon-certificate' style='color:".$status_priv.";'></span>".$motivation_plan."</td>";

			$privilege_id[$a] = $axRow_privilege['id'];

			$a++;
		}
	}

	
	# COUPON

	while ($axRow_coupon = $oRes_coupon->FetchRow(DBI_ASSOC)) {

		if ($axRow_coupon['coup_MotivationID'] != 0 && $axRow_coupon['coup_Motivation'] == 'Point') {

			if ($axRow_coupon['coup_Status'] == "Pending") { $status_priv = "#f0ad4e"; } 
			else { $status_priv = "#5cb85c"; }

			$plan_sql = "SELECT mopp_Name, mopp_PointQty, mopp_UseAmount FROM motivation_plan_point WHERE mopp_MotivationPointID='".$axRow_coupon['coup_MotivationID']."'";
			$get_point = $oDB->Query($plan_sql);
			$point = $get_point->FetchRow(DBI_ASSOC);

			$motivation_plan = '<br>'.$point['mopp_Name'].'<br>('.number_format($point['mopp_UseAmount'],2).' ฿ / '.$point['mopp_PointQty'].' '.$icon.')';

			$table_point .= "<td><b>".$axRow_coupon['txt']."</b> <span class='glyphicon glyphicon-certificate' style='color:".$status_priv.";'></span>".$motivation_plan."</td>";

			$coupon_id[$b] = $axRow_coupon['id'];

			$b++;
		}
	}

	
	# HBD

	while ($axRow_hbd = $oRes_hbd->FetchRow(DBI_ASSOC)) {

		if ($axRow_hbd['coup_MotivationID'] != 0 && $axRow_coupon['coup_Motivation'] == 'Point') {

			if ($axRow_coupon['coup_Status'] == "Pending") { $status_priv = "#f0ad4e"; } 
			else { $status_priv = "#5cb85c"; }

			$plan_sql = "SELECT mopp_Name, mopp_PointQty, mopp_UseAmount FROM motivation_plan_point WHERE mopp_MotivationPointID='".$axRow_hbd['coup_MotivationID']."'";
			$get_point = $oDB->Query($plan_sql);
			$point = $get_point->FetchRow(DBI_ASSOC);

			$motivation_plan = '<br>'.$point['mopp_Name'].'<br>('.number_format($point['mopp_UseAmount'],2).' ฿ / '.$point['mopp_PointQty'].' '.$icon.')';

			$table_point .= "<td><b>".$axRow_hbd['txt']."</b> <span class='glyphicon glyphicon-certificate' style='color:".$status_priv.";'></span>".$motivation_plan."</td>";

			$hbd_id[$d] = $axRow_hbd['id'];

			$d++;
		}
	}

	
	# ACTIVITY

	while ($axRow_activity = $oRes_activity->FetchRow(DBI_ASSOC)) {

		if ($axRow_activity['acti_MotivationID'] != 0 && $axRow_activity['acti_Motivation'] == 'Point') {

			if ($axRow_activity['acti_Status'] == "Pending") { $status_priv = "#f0ad4e"; } 
			else { $status_priv = "#5cb85c"; }

			$plan_sql = "SELECT mopp_Name, mopp_PointQty, mopp_UseAmount FROM motivation_plan_point WHERE mopp_MotivationPointID='".$axRow_activity['acti_MotivationID']."'";
			$get_point = $oDB->Query($plan_sql);
			$point = $get_point->FetchRow(DBI_ASSOC);

			$motivation_plan = '<br>'.$point['mopp_Name'].'<br>('.number_format($point['mopp_UseAmount'],2).' ฿ / '.$point['mopp_PointQty'].' '.$icon.')';

			$table_point .= "<td><b>".$axRow_activity['txt']."</b> <span class='glyphicon glyphicon-certificate' style='color:".$status_priv.";'></span>".$motivation_plan."</td>";

			$activity_id[$c] = $axRow_activity['id'];

			$c++;
		}
	}


	$table_point .= "<td style='background-color:#003369;color:white;text-align:center'><b>รวม</b></td>";					
	$table_point .= "</tr><tbody>";

	$all_use = 0;

	while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)) {

		$total_branch = 0;

		$table_point .= "<tr><td class='td_head'>".$axRow_branch['txt']."</td>";

		for ($i=0; $i < $a; $i++) {

			$number_branch = get_point_priv($privilege_id[$i],$axRow_branch['id'],$card_id,$id);

			$table_point .= "<td style='text-align:center'>".number_format($number_branch)."</td>";

			$total_branch += $number_branch;
		}

		for ($i=0; $i < $b; $i++) {

			$number_branch = get_point_coup($coupon_id[$i],$axRow_branch['id'],$card_id,$id);

			$table_point .= "<td style='text-align:center'>".number_format($number_branch)."</td>";

			$total_branch += $number_branch;
		}

		for ($i=0; $i < $d; $i++) {

			$number_branch = get_point_coup($hbd_id[$i],$axRow_branch['id'],$card_id,$id);

			$table_point .= "<td style='text-align:center'>".number_format($number_branch)."</td>";

			$total_branch += $number_branch;
		}

		for ($i=0; $i < $c; $i++) {

			$number_branch = get_point_acti($activity_id[$i],$axRow_branch['id'],$card_id,$id);

			$table_point .= "<td style='text-align:center'>".number_format($number_branch)."</td>";

			$total_branch += $number_branch;
		}

		$all_use += $total_branch;

		$table_point .= "<td style='text-align:center;background-color:#EEEEEE'><b>".number_format($total_branch)."</b></td>";

		$table_point .= "</tr>";		
	}

	
	if(!$_SESSION['user_branch_id']) {
															
		$table_point .= "<tr style='background-color:#EEE;;text-align:center'>";
										
		$table_point .= "<td style='background-color:#003369;color:white'><b>Total Collects</b></td>";
					
		for ($i=0; $i < $a; $i++) {

			$number_privilege =	get_point_priv($privilege_id[$i],"",$card_id,$id);

			$table_point .= "<td><b>".number_format($number_privilege)."</b></td>";		
		}

		for ($i=0; $i < $b; $i++) {

			$number_branch = get_point_coup($coupon_id[$i],"",$card_id,$id);

			$table_point .= "<td><b>".number_format($number_branch)."</b></td>";
		}

		for ($i=0; $i < $d; $i++) {

			$number_branch = get_point_coup($hbd_id[$i],"",$card_id,$id);

			$table_point .= "<td><b>".number_format($number_branch)."</b></td>";
		}

		for ($i=0; $i < $c; $i++) {

			$number_branch = get_point_acti($activity_id[$i],"",$card_id,$id);

			$table_point .= "<td><b>".number_format($number_branch)."</b></td>";
		}
										
		$table_point .= "<td style='background-color:#003369;color:white;text-align:center'><b>".number_format($all_use)."</b></td>";
										
		$table_point .= "</tr>";
	}
								
	$table_point .="</tbody></table>";

	$table_point .= "</div>";


	if ($a!=0 || $b!=0 || $c!=0 || $d!=0) { $table_member .= $table_point; }



	# STAMP #

	$table_stamp = '';

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


	$a = 0;
	$b = 0;
	$c = 0;
	$d = 0;

	$oRes_branch = $oDB->Query($sql_branch)or die(mysql_error());


	$icon_sql = "SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID = 3";
	$icon = $oDB->QueryOne($icon_sql);
	$icon ='<img src="../../upload/collection_upload/'.$icon.'" width="12px" height="12px" style="margin-bottom:3px">';


	$table_stamp .= " &nbsp; <b>Stamp Collects</b><br>
						<div class='table-responsive'>
							<table id='myTable' class='table table-bordered' style='background-color:white;' >
								<tr style='background-color:#003369;color:white;text-align:center'>
									<td width='150px'><b>Branch \ Privilege</b></td>";

	# PRIVILEGE

	while ($axRow_privilege = $oRes_privilege->FetchRow(DBI_ASSOC)) {

		if ($axRow_privilege['priv_MotivationID'] != 0 && $axRow_privilege['priv_Motivation'] == 'Stamp') {

			if ($axRow_privilege['priv_Status'] == "Pending") { $status_priv = "#f0ad4e"; } 
			else { $status_priv = "#5cb85c"; }

			$plan_sql = "SELECT mops_Name, mops_StampQty, mops_CollectionTypeID FROM motivation_plan_stamp WHERE mops_MotivationStampID='".$axRow_privilege['priv_MotivationID']."'";
			$get_stamp = $oDB->Query($plan_sql);
			$stamp = $get_stamp->FetchRow(DBI_ASSOC);

			$icon_sql = "SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID = ".$stamp['mops_CollectionTypeID'];
			$icon = $oDB->QueryOne($icon_sql);
			$icon ='<img src="../../upload/collection_upload/'.$icon.'" width="12px" height="12px" style="margin-bottom:3px">';

			$motivation_plan = '<br>'.$stamp['mops_Name'].'<br>(1 Times / '.$stamp['mops_StampQty'].' '.$icon.')';

			$table_stamp .= "<td><b>".$axRow_privilege['txt']."</b> <span class='glyphicon glyphicon-certificate' style='color:".$status_priv.";'></span>".$motivation_plan."</td>";

			$privilege_id[$a] = $axRow_privilege['id'];

			$a++;
		}
	}

		
	# COUPON

	while ($axRow_coupon = $oRes_coupon->FetchRow(DBI_ASSOC)) {

		if ($axRow_coupon['coup_MotivationID'] != 0 && $axRow_coupon['coup_Motivation'] == 'Point') {

			if ($axRow_coupon['coup_Status'] == "Pending") { $status_priv = "#f0ad4e"; } 
			else { $status_priv = "#5cb85c"; }

			$plan_sql = "SELECT mops_Name, mops_StampQty, mops_CollectionTypeID FROM motivation_plan_stamp WHERE mops_MotivationStampID='".$axRow_coupon['coup_MotivationID']."'";
			$get_stamp = $oDB->Query($plan_sql);
			$stamp = $get_stamp->FetchRow(DBI_ASSOC);

			$icon_sql = "SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID = ".$stamp['mops_CollectionTypeID'];
			$icon = $oDB->QueryOne($icon_sql);
			$icon ='<img src="../../upload/collection_upload/'.$icon.'" width="12px" height="12px" style="margin-bottom:3px">';

			$motivation_plan = '<br>'.$stamp['mops_Name'].'<br>(1 Times / '.$stamp['mops_StampQty'].' '.$icon.')';

			$table_stamp .= "<td><b>".$axRow_coupon['txt']."</b> <span class='glyphicon glyphicon-certificate' style='color:".$status_priv.";'></span>".$motivation_plan."</td>";

			$coupon_id[$b] = $axRow_coupon['id'];

			$b++;
		}
	}

		
	# HBD

	while ($axRow_hbd = $oRes_hbd->FetchRow(DBI_ASSOC)) {

		if ($axRow_hbd['coup_MotivationID'] != 0 && $axRow_coupon['coup_Motivation'] == 'Point') {

			if ($axRow_coupon['coup_Status'] == "Pending") { $status_priv = "#f0ad4e"; } 
			else { $status_priv = "#5cb85c"; }

			$plan_sql = "SELECT mops_Name, mops_StampQty, mops_CollectionTypeID FROM motivation_plan_stamp WHERE mops_MotivationStampID='".$axRow_coupon['coup_MotivationID']."'";
			$get_stamp = $oDB->Query($plan_sql);
			$stamp = $get_stamp->FetchRow(DBI_ASSOC);

			$icon_sql = "SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID = ".$stamp['mops_CollectionTypeID'];
			$icon = $oDB->QueryOne($icon_sql);
			$icon ='<img src="../../upload/collection_upload/'.$icon.'" width="12px" height="12px" style="margin-bottom:3px">';

			$motivation_plan = '<br>'.$stamp['mops_Name'].'<br>(1 Times / '.$stamp['mops_StampQty'].' '.$icon.')';

			$table_stamp .= "<td><b>".$axRow_hbd['txt']."</b> <span class='glyphicon glyphicon-certificate' style='color:".$status_priv.";'></span>".$motivation_plan."</td>";

			$hbd_id[$d] = $axRow_hbd['id'];

			$d++;
		}
	}

		
	# ACTIVITY

	while ($axRow_activity = $oRes_activity->FetchRow(DBI_ASSOC)) {

		if ($axRow_activity['acti_MotivationID'] != 0 && $axRow_activity['acti_Motivation'] == 'Point') {

			if ($axRow_activity['acti_Status'] == "Pending") { $status_priv = "#f0ad4e"; } 
			else { $status_priv = "#5cb85c"; }

			$plan_sql = "SELECT mops_Name, mops_StampQty, mops_CollectionTypeID FROM motivation_plan_stamp WHERE mops_MotivationStampID='".$axRow_activity['acti_MotivationID']."'";
			$get_stamp = $oDB->Query($plan_sql);
			$stamp = $get_stamp->FetchRow(DBI_ASSOC);

			$icon_sql = "SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID = ".$stamp['mops_CollectionTypeID'];
			$icon = $oDB->QueryOne($icon_sql);
			$icon ='<img src="../../upload/collection_upload/'.$icon.'" width="12px" height="12px" style="margin-bottom:3px">';

			$motivation_plan = '<br>'.$stamp['mops_Name'].'<br>(1 Times / '.$stamp['mops_StampQty'].' '.$icon.')';

			$table_stamp .= "<td><b>".$axRow_activity['txt']."</b> <span class='glyphicon glyphicon-certificate' style='color:".$status_priv.";'></span>".$motivation_plan."</td>";

			$activity_id[$c] = $axRow_activity['id'];

			$c++;
		}
	}


	$table_stamp .= "<td style='background-color:#003369;color:white;text-align:center'><b>รวม</b></td>";					
	$table_stamp .= "</tr><tbody>";

	$all_use = 0;

	while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)) {

		$total_branch = 0;

		$table_stamp .= "<tr><td class='td_head'>".$axRow_branch['txt']."</td>";

		for ($i=0; $i < $a; $i++) {

			$number_branch = get_point_priv($privilege_id[$i],$axRow_branch['id'],$card_id,$id);

			$table_stamp .= "<td style='text-align:center'>".number_format($number_branch)."</td>";

			$total_branch += $number_branch;
		}

		for ($i=0; $i < $b; $i++) {

			$number_branch = get_point_coup($coupon_id[$i],$axRow_branch['id'],$card_id,$id);

			$table_stamp .= "<td style='text-align:center'>".number_format($number_branch)."</td>";

			$total_branch += $number_branch;
		}

		for ($i=0; $i < $d; $i++) {

			$number_branch = get_point_coup($hbd_id[$i],$axRow_branch['id'],$card_id,$id);
					
			$table_stamp .= "<td style='text-align:center'>".number_format($number_branch)."</td>";

			$total_branch += $number_branch;
		}

		for ($i=0; $i < $c; $i++) {

			$number_branch = get_point_acti($activity_id[$i],$axRow_branch['id'],$card_id,$id);

			$table_stamp .= "<td style='text-align:center'>".number_format($number_branch)."</td>";

			$total_branch += $number_branch;
		}

		$all_use += $total_branch;

		$table_stamp .= "<td style='text-align:center;background-color:#EEEEEE'><b>".number_format($total_branch)."</b></td>";

		$table_stamp .= "</tr>";		
	}

	if(!$_SESSION['user_branch_id']) {
															
		$table_stamp .= "<tr style='background-color:#EEE;;text-align:center'>";
										
		$table_stamp .= "<td style='background-color:#003369;color:white'><b>Total Collects</b></td>";
					
		for ($i=0; $i < $a; $i++) {

			$number_privilege =	get_point_priv($privilege_id[$i],"",$card_id,$id);

			$table_stamp .= "<td><b>".number_format($number_privilege)."</b></td>";		
		}

		for ($i=0; $i < $b; $i++) {

			$number_branch = get_point_coup($coupon_id[$i],"",$card_id,$id);

			$table_stamp .= "<td><b>".number_format($number_branch)."</b></td>";
		}

		for ($i=0; $i < $d; $i++) {

			$number_branch = get_point_coup($hbd_id[$i],"",$card_id,$id);

			$table_stamp .= "<td><b>".number_format($number_branch)."</b></td>";
		}

		for ($i=0; $i < $c; $i++) {

			$number_branch = get_point_acti($activity_id[$i],"",$card_id,$id);

			$table_stamp .= "<td><b>".number_format($number_branch)."</b></td>";
		}
										
		$table_stamp .= "<td style='background-color:#003369;color:white;text-align:center'><b>".number_format($all_use)."</b></td>";
										
		$table_stamp .= "</tr>";
	}
								
	$table_stamp .="</tbody></table>";

	$table_stamp .= "</div>";


	if ($a!=0 || $b!=0 || $c!=0 || $d!=0) { $table_member .= $table_stamp; }

}





# EARN

$sql_earn = "SELECT 
				hilight_coupon_trans.hico_CreatedDate AS date_use,
				hilight_coupon_trans.hico_HilightCouponID AS code_use,
				hilight_coupon.coup_Name,
				hilight_coupon.coup_Image,
				hilight_coupon.coup_ImageNew,
				hilight_coupon.coup_ImagePath,
				hilight_coupon.coup_Type,
				mi_branch.name AS branch_name,
				mb_member.firstname, 
				mb_member.lastname, 
				mb_member.facebook_id, 
				mb_member.facebook_name, 
				mb_member.member_id, 
				mb_member.member_image,
				mb_member.mobile,
				mb_member.email,
				mi_brand.name AS brand_name,
				mi_brand.path_logo AS path_logo,
				mi_brand.logo_image AS brand_logo

				FROM hilight_coupon_trans

				LEFT JOIN hilight_coupon
				ON hilight_coupon_trans.coup_CouponID = hilight_coupon.coup_CouponID

				LEFT JOIN mb_member
				ON hilight_coupon_trans.memb_MemberID = mb_member.member_id

				LEFT JOIN mi_branch
				ON hilight_coupon_trans.brnc_BranchID = mi_branch.branch_id

				LEFT JOIN mi_brand
				ON mi_brand.brand_id = mi_branch.brand_id

				WHERE mb_member.member_id = ".$id."
				".$where_brand."

			UNION

			SELECT 
				hilight_coupon_buy.hcbu_CreatedDate AS date_use,
				hilight_coupon_buy.hcbu_HilightCouponBuyID AS code_use,
				hilight_coupon.coup_Name,
				hilight_coupon.coup_Image,
				hilight_coupon.coup_ImageNew,
				hilight_coupon.coup_ImagePath,
				hilight_coupon.coup_Type,
				mi_branch.name AS branch_name,
				mb_member.firstname, 
				mb_member.lastname, 
				mb_member.facebook_id, 
				mb_member.facebook_name, 
				mb_member.member_id, 
				mb_member.member_image,
				mb_member.mobile,
				mb_member.email,
				mi_brand.name AS brand_name,
				mi_brand.path_logo AS path_logo,
				mi_brand.logo_image AS brand_logo

				FROM hilight_coupon_buy

				LEFT JOIN hilight_coupon
				ON hilight_coupon_buy.hico_HilightCouponID = hilight_coupon.coup_CouponID

				LEFT JOIN mb_member
				ON hilight_coupon_buy.memb_MemberID = mb_member.member_id

				LEFT JOIN mi_branch
				ON hilight_coupon_buy.brnc_BranchID = mi_branch.branch_id

				LEFT JOIN mi_brand
				ON mi_brand.brand_id = mi_branch.brand_id

				WHERE mb_member.member_id = ".$id."
				".$where_brand."

				ORDER BY date_use DESC";

$oRes_earn = $oDB->Query($sql_earn)or die(mysql_error());

$check_earn = $oDB->QueryOne($sql_earn);

if ($check_earn) {

	$table_earn = "<table id='example' class='table table-bordered' style='background-color:white;'>
						<thead><tr class='th_table'>
							<th style='text-align:center;'>Use Date</th>
							<th style='text-align:center;'>Code Use</th>
							<th style='text-align:center;'>Earn Attention</th>
							<th style='text-align:center;'>Detail</th>
							<th style='text-align:center;'>Brand</th>
							<th style='text-align:center;'>Branch</th>
						</tr></thead><tbody>";

	while ($axRow = $oRes_earn->FetchRow(DBI_ASSOC)) {

		# LOGO IMAGE

		if($axRow['brand_logo']!=''){

			$axRow['brand_logo'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" width="50" height="50"/>';

		} else {

			$axRow['brand_logo'] = '<img src="../../images/400x400.png" width="50" height="50"/>';
		}


		# EARN IMAGE

		if($axRow['coup_ImageNew']!=''){

			$privilege_img = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_ImageNew'].'" height="50px" class="image_border">';

		} else if ($axRow['coup_Image']!='') {

			$privilege_img = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_Image'].'" height="50px" class="image_border">';

		} else {

			$privilege_img = '<img src="../../images/card_privilege.jpg" height="50px" class="image_border">';
		}

		$table_earn .= "<tr>
							<td style='text-align:center;width:15%'>".DateTime($axRow['date_use'])."</td>
							<td style='text-align:center;width:10%'>".$axRow['code_use']."</td>
							<td style='text-align:center;width:15%'>".$privilege_img."</td>
							<td style='text-align:left'>
								<table class='myTable' style='width:100%'>
									<tr>
										<td style='text-align:right;width:10%'>
											Name<br>
											Type</td>
										<td style='text-align:center;width:10%'>
											:<br>
											:</td>
										<td>
											".$axRow['coup_Name']."<br>
											".$axRow['coup_Type']."</td>
									</tr>
								</table>
							</td>
							<td style='text-align:center;width:10%'>".$axRow['brand_logo']."<br>
								<span style='font-size:11px'>".$axRow['brand_name']."</span></td>
							<td style='text-align:center'>".$axRow['branch_name']."</td>
						</tr>";
	}

	$table_earn .= "</tbody></table>";
}





$as_name_title_type = list_type_master_value($oDB,'name_title_type',$axRow['name_title_type']);

if ($as_name_title_type=="") { $as_name_title_type = "-"; }

$oTmp->assign('name_title_type', $as_name_title_type);



$oTmp->assign('data', $asData);

$oTmp->assign('table_member', $table_member);

$oTmp->assign('table_earn', $table_earn);

$oTmp->assign('is_menu', 'is_analytics');

$oTmp->assign('content_file', 'member/member_detail.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());

}

//========================================//

?>

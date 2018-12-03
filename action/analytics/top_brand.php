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

$sql ='SELECT mi_brand.*,
			mi_category_brand.name AS category_name

			FROM mi_brand
			INNER JOIN mi_category_brand
			ON mi_brand.category_brand = mi_category_brand.category_brand_id
			WHERE brand_id = "'.$id.'"

			ORDER BY brand_id DESC LIMIT 1';

$oRes = $oDB->Query($sql);

$axRow = $oRes->FetchRow(DBI_ASSOC);


$sql_card = 'SELECT DISTINCT
				mi_card.*,
				mi_card.card_id AS card_id,
				mi_card.image AS card_image,
				mi_card.image_newupload AS card_imagenew,
				mi_card.path_image,
				mi_card.name AS card_name
				FROM mi_card_register
				INNER JOIN mi_card
				ON mi_card_register.card_id = mi_card.card_id
				WHERE mi_card_register.brand_id="'.$id.'"
				AND mi_card.flag_del!="1"';

$oRes_card = $oDB->Query($sql_card);

$table_brand = "";

$x = 1;

while ($axRow_card = $oRes_card->FetchRow(DBI_ASSOC)){

	if ($x > 1) { $table_brand .= '<hr>'; }
	else { $table_brand .= '<br>'; }
	
	$x++;

	$card_id = $axRow_card["card_id"];


	# CARD IMAGE

	if($axRow_card['image_newupload']!=''){

		$axRow_card['card_image'] = '<img src="../../upload/'.$axRow_card['path_image'].$axRow_card['image_newupload'].'" class="img-rounded image_border" height="100"/>';

	} else if($axRow_card['card_image']!=''){

		$axRow_card['card_image'] = '<img src="../../upload/'.$axRow_card['path_image'].$axRow_card['card_image'].'" class="img-rounded image_border" height="100" />';

	} else {

		$axRow_card['card_image'] = '<img src="../../image/card_privilege.jpg" class="img-rounded image_border" height="100" />';
	}


	# CARD PERIOD

	if ($axRow_card['period_type'] == '1') { 

		$axRow_card['period_type'] = DateOnly($axRow_card['date_expire']);	

	} else if ($axRow_card['period_type'] == '2') { 

		$axRow_card['period_type'] = $axRow_card['period_type_other'].' Months';	

	} else if ($axRow_card['period_type'] == '3') { 

		$axRow_card['period_type'] = $axRow_card['period_type_other'].' Years';	

	} else if ($axRow_card['period_type'] == '4') { 

		$axRow_card['period_type'] = 'Member Life Time';	
	}


	# BRANCH

	$sql_branch = 'SELECT 
					name as txt,
					branch_id as id 
					FROM mi_branch 
					WHERE brand_id = "'.$id.'"';


	# PRIVILEGE

	$sql_privilege = 'SELECT DISTINCT
						privilege.priv_Name as txt,
						privilege.priv_PrivilegeID as id,
						privilege.priv_MotivationID,
						privilege.priv_Motivation
						FROM privilege 
						LEFT JOIN mi_card_register
						ON mi_card_register.privilege_id = privilege.priv_PrivilegeID
						LEFT JOIN mi_card
						ON mi_card.card_id = mi_card_register.card_id
						WHERE privilege.bran_BrandID = "'.$id.'" 
						AND mi_card_register.status=1 
						AND mi_card.card_id ="'.$card_id.'"';

	# COUPON

	$sql_coupon = 'SELECT DISTINCT
					coupon.coup_Name as txt,
					coupon.coup_CouponID as id,
					coupon.coup_MotivationID,
					coupon.coup_Motivation
					FROM coupon 
					LEFT JOIN mi_card_register
					ON mi_card_register.coupon_id = coupon.coup_CouponID
					LEFT JOIN mi_card
					ON mi_card.card_id = mi_card_register.card_id
					WHERE coupon.bran_BrandID = "'.$id.'" 
					AND mi_card_register.status=1 
					AND mi_card.card_id ="'.$card_id.'"
					AND coupon.coup_Birthday!="T"';

	# HBD COUPON

	$sql_hbd = 'SELECT DISTINCT
					coupon.coup_Name as txt,
					coupon.coup_CouponID as id,
					coupon.coup_MotivationID,
					coupon.coup_Motivation
					FROM coupon 
					LEFT JOIN mi_card_register
					ON mi_card_register.coupon_id = coupon.coup_CouponID
					LEFT JOIN mi_card
					ON mi_card.card_id = mi_card_register.card_id
					WHERE coupon.bran_BrandID = "'.$id.'" 
					AND mi_card_register.status=1 
					AND mi_card.card_id ="'.$card_id.'"
					AND coupon.coup_Birthday="T"';

	# ACTIVITY

	$sql_activity = 'SELECT DISTINCT
						activity.acti_Name as txt,
						activity.acti_ActivityID as id,
						activity.acti_MotivationID,
						activity.acti_Motivation
						FROM activity 
						LEFT JOIN mi_card_register
						ON mi_card_register.activity_id = activity.acti_ActivityID
						LEFT JOIN mi_card
						ON mi_card.card_id = mi_card_register.card_id
						WHERE activity.bran_BrandID = "'.$id.'" 
						AND mi_card_register.status=1 
						AND mi_card.card_id ="'.$card_id.'"';

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


	$total_priv = get_total_privilege_use("","",$card_id,"");
	$total_coup = get_total_coupon_use("","",$card_id,"");
	$total_acti = get_total_activity_use("","",$card_id,"");
	$total_use = $total_priv+$total_coup+$total_acti;


	$table_brand .= '<table class="myPopup">
						<tr>
							<td rowspan="4" width="200px" valign="top" style="text-align:center;">
								'.$axRow_card['card_image'].'</td>
							<td style="text-align:right;width:100px;"><b>Brand</b></td>
							<td style="text-align:center;width:30px;"><b>:</b></td>
							<td><b>'.$axRow['name'].'</b></td>
						</tr>
						<tr>
							<td style="text-align:right"><b>Card</b></td>
							<td style="text-align:center;"><b>:</b></td>
							<td><b>'.$axRow_card['card_name'].'</b></td></tr>
						<tr>
							<td style="text-align:right"><b>Card Multiple</b></td>
							<td style="text-align:center;"><b>:</b></td>
							<td><b>'.$axRow_card['flag_multiple'].'</b></td></tr>
						<tr>
							<td style="text-align:right"><b>Period</b></td>
							<td style="text-align:center;"><b>:</b></td>
							<td><b>'.$axRow_card['period_type'].'</b></td></tr>
					</table>
					<br>
					<center>
	 					<span style="font-size:16px"><b>Total Use &nbsp; : &nbsp; '.number_format($total_use).' &nbsp; Times</span></b>
	 				</center>
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

	$table_brand .= " &nbsp; <b>Privileges Use</b></span><br>
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

	if ($a != 0) { $table_brand .= "<td colspan='".$a."'><b>Privilege</b></td>"; }

	
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

	if ($b != 0) { $table_brand .= "<td colspan='".$b."'><b>Coupon</b></td>"; }

	
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

	if ($d != 0) { $table_brand .= "<td colspan='".$d."'><b>Birthday Coupon</b></td>"; }

	
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

	if ($c != 0) { $table_brand .= "<td colspan='".$c."'><b>Activity</b></td>";	}

	
	$table_brand .= "<td rowspan='2'><b>รวม</b></td>
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

		$table_brand .= "<tr><td class='td_head'>".$axRow_branch['txt']."</td>";

		for ($i=0; $i < $a; $i++) {

			$number_branch = get_total_privilege_use($privilege_id[$i],$axRow_branch['id'],$card_id,"",$axRow_card['flag_multiple']);

			$table_brand .= "<td style='text-align:center'>".number_format($number_branch)."</td>";

			$total_branch += $number_branch;
		}

		for ($i=0; $i < $b; $i++) {

			$number_branch = get_total_coupon_use($coupon_id[$i],$axRow_branch['id'],$card_id,"",$axRow_card['flag_multiple']);

			$table_brand .= "<td style='text-align:center'>".number_format($number_branch)."</td>";

			$total_branch += $number_branch;
		}

		for ($i=0; $i < $d; $i++) {

			$number_branch = get_total_coupon_use($hbd_id[$i],$axRow_branch['id'],$card_id,"",$axRow_card['flag_multiple']);

			$table_brand .= "<td style='text-align:center'>".number_format($number_branch)."</td>";

			$total_branch += $number_branch;
		}

		for ($i=0; $i < $c; $i++) {

			$number_branch = get_total_activity_use($activity_id[$i],$axRow_branch['id'],$card_id,"",$axRow_card['flag_multiple']);

			$table_brand .= "<td style='text-align:center'>".number_format($number_branch)."</td>";

			$total_branch += $number_branch;
		}

		$all_use += $total_branch;

		$table_brand .= "<td style='text-align:center;background-color:#EEEEEE'><b>".number_format($total_branch)."</b></td>";

		$table_brand .= "</tr>";
			
	}

	if(!$_SESSION['user_branch_id']) {
														
		$table_brand .= "<tr>";
									
		$table_brand .= "<td style='background-color:#003369;color:white;text-align:center'><b>Total Use</b></td>";
				
		for ($i=0; $i < $a; $i++) {

			$number_privilege =	get_total_privilege_use($privilege_id[$i],"",$card_id,"",$axRow_card['flag_multiple']);

			$table_brand .= "<td style='text-align:center;background-color:#EEEEEE'><b>".$number_privilege."</b></td>";						
		}

		for ($i=0; $i < $b; $i++) {

			$number_branch = get_total_coupon_use($coupon_id[$i],"",$card_id,"",$axRow_card['flag_multiple']);

			$table_brand .= "<td style='text-align:center;background-color:#EEEEEE'><b>".$number_branch."</b></td>";
		}

		for ($i=0; $i < $d; $i++) {

			$number_branch = get_total_coupon_use($hbd_id[$i],"",$card_id,"",$axRow_card['flag_multiple']);

			$table_brand .= "<td style='text-align:center;background-color:#EEEEEE'><b>".$number_branch."</b></td>";
		}

		for ($i=0; $i < $c; $i++) {

			$number_branch = get_total_activity_use($activity_id[$i],"",$card_id,"",$axRow_card['flag_multiple']);

			$table_brand .= "<td style='text-align:center;background-color:#EEEEEE'><b>".$number_branch."</b></td>";
		}
									
		$table_brand .= "<td style='background-color:#003369;color:white;text-align:center'><b>".$all_use."</b></td>";
									
		$table_brand .= "</tr>";	
	}
							
	$table_brand .="</tr></table>";

	$table_brand .= "</div>";



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

			$number_branch = get_point_priv($privilege_id[$i],$axRow_branch['id'],$card_id,"");

			$table_point .= "<td style='text-align:center'>".number_format($number_branch)."</td>";

			$total_branch += $number_branch;
		}

		for ($i=0; $i < $b; $i++) {

			$number_branch = get_point_coup($coupon_id[$i],$axRow_branch['id'],$card_id,"");

			$table_point .= "<td style='text-align:center'>".number_format($number_branch)."</td>";

			$total_branch += $number_branch;
		}

		for ($i=0; $i < $d; $i++) {

			$number_branch = get_point_coup($hbd_id[$i],$axRow_branch['id'],$card_id,"");

			$table_point .= "<td style='text-align:center'>".number_format($number_branch)."</td>";

			$total_branch += $number_branch;
		}

		for ($i=0; $i < $c; $i++) {

			$number_branch = get_point_acti($activity_id[$i],$axRow_branch['id'],$card_id,"");

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

			$number_privilege =	get_point_priv($privilege_id[$i],"",$card_id,"");

			$table_point .= "<td><b>".number_format($number_privilege)."</b></td>";		
		}

		for ($i=0; $i < $b; $i++) {

			$number_branch = get_point_coup($coupon_id[$i],"",$card_id,"");

			$table_point .= "<td><b>".number_format($number_branch)."</b></td>";
		}

		for ($i=0; $i < $d; $i++) {

			$number_branch = get_point_coup($hbd_id[$i],"",$card_id,"");

			$table_point .= "<td><b>".number_format($number_branch)."</b></td>";
		}

		for ($i=0; $i < $c; $i++) {

			$number_branch = get_point_acti($activity_id[$i],"",$card_id,"");

			$table_point .= "<td><b>".number_format($number_branch)."</b></td>";
		}
										
		$table_point .= "<td style='background-color:#003369;color:white;text-align:center'><b>".number_format($all_use)."</b></td>";
										
		$table_point .= "</tr>";
	}
								
	$table_point .="</tbody></table>";

	$table_point .= "</div>";


	if ($a!=0 || $b!=0 || $c!=0 || $d!=0) { $table_brand .= $table_point; }



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

			$number_branch = get_point_priv($privilege_id[$i],$axRow_branch['id'],$card_id,"");

			$table_stamp .= "<td style='text-align:center'>".number_format($number_branch)."</td>";

			$total_branch += $number_branch;
		}

		for ($i=0; $i < $b; $i++) {

			$number_branch = get_point_coup($coupon_id[$i],$axRow_branch['id'],$card_id,"");

			$table_stamp .= "<td style='text-align:center'>".number_format($number_branch)."</td>";

			$total_branch += $number_branch;
		}

		for ($i=0; $i < $d; $i++) {

			$number_branch = get_point_coup($hbd_id[$i],$axRow_branch['id'],$card_id,"");
					
			$table_stamp .= "<td style='text-align:center'>".number_format($number_branch)."</td>";

			$total_branch += $number_branch;
		}

		for ($i=0; $i < $c; $i++) {

			$number_branch = get_point_acti($activity_id[$i],$axRow_branch['id'],$card_id,"");

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

			$number_privilege =	get_point_priv($privilege_id[$i],"",$card_id,"");

			$table_stamp .= "<td><b>".number_format($number_privilege)."</b></td>";		
		}

		for ($i=0; $i < $b; $i++) {

			$number_branch = get_point_coup($coupon_id[$i],"",$card_id,"");

			$table_stamp .= "<td><b>".number_format($number_branch)."</b></td>";
		}

		for ($i=0; $i < $d; $i++) {

			$number_branch = get_point_coup($hbd_id[$i],"",$card_id,"");

			$table_stamp .= "<td><b>".number_format($number_branch)."</b></td>";
		}

		for ($i=0; $i < $c; $i++) {

			$number_branch = get_point_acti($activity_id[$i],"",$card_id,"");

			$table_stamp .= "<td><b>".number_format($number_branch)."</b></td>";
		}
										
		$table_stamp .= "<td style='background-color:#003369;color:white;text-align:center'><b>".number_format($all_use)."</b></td>";
										
		$table_stamp .= "</tr>";
	}
								
	$table_stamp .="</tbody></table>";

	$table_stamp .= "</div>";


	if ($a!=0 || $b!=0 || $c!=0 || $d!=0) { $table_brand .= $table_stamp; }

	$x++;
}




$oTmp->assign('data', $axRow);

$oTmp->assign('table_brand', $table_brand);

$oTmp->assign('is_menu', 'is_analytics');

$oTmp->assign('content_file', 'analytics/top_brand.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>

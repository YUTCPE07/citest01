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

$sql ='SELECT mi_branch.*,
			mi_brand.logo_image,
			mi_brand.path_logo,
			mi_brand.company_name,
			mi_brand.name AS brand_name,
			mi_branch.name AS branch_name
			FROM mi_branch
			INNER JOIN mi_brand
			ON mi_brand.brand_id = mi_branch.brand_id
			WHERE branch_id = "'.$id.'"';

$oRes = $oDB->Query($sql)or die(mysql_error());

$axRow = $oRes->FetchRow(DBI_ASSOC);


$sql_card = 'SELECT DISTINCT
				mi_card.card_id,
				mi_card.image AS card_image,
				mi_card.image_newupload AS card_imagenew,
				mi_card.path_image,
				mi_card.name AS card_name
				FROM mi_card_register
				INNER JOIN mi_card
				ON mi_card_register.card_id = mi_card.card_id
				INNER JOIN mi_branch
				ON mi_branch.branch_id = mi_card_register.branch_id
				INNER JOIN mi_brand
				ON mi_branch.brand_id = mi_brand.brand_id
				WHERE mi_card_register.branch_id = '.$id.'
				AND mi_card_register.status=1';
	
$oRes_card = $oDB->Query($sql_card)or die(mysql_error());

$table_branch = "";

while ($axRow_card = $oRes_card->FetchRow(DBI_ASSOC)){

	$card_id = $axRow_card["card_id"];


	# CARD

	if($axRow_card['card_imagenew']!=''){
		
		$axRow_card['card_image'] = '<img src="../../upload/'.$axRow_card['path_image'].$axRow_card['card_imagenew'].'" class="img-responsive" width="240px" style="border-radius:10px"/>';

	} else if($axRow_card['card_image']!=''){
		
		$axRow_card['card_image'] = '<img src="../../upload/'.$axRow_card['path_image'].$axRow_card['card_image'].'" class="img-responsive" width="240px" style="border-radius:10px" />';
	
	} else {
		
		$axRow_card['card_image'] = '<img src="../../images/card_privilege.jpg" class="img-responsive" />';
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
					WHERE branch_id = "'.$id.'"';


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


		
	$privilege_id = array();

	$coupon_id = array();

	$hbd_id = array();

	$activity_id = array();

	$total_priv = get_total_privilege_use("",$id,$card_id,"");

	$total_coup = get_total_coupon_use("",$id,$card_id,"");

	$total_acti = get_total_activity_use("",$id,$card_id,"");

	$total_use = $total_priv+$total_coup+$total_acti;

	$table_branch .= "<center>
						".$axRow_card['card_image']."
						<br><span style='font-size:18px'><b>Total &nbsp; : &nbsp; ".$total_use."</span></b>
						<br><br></center>";
	
	$table_branch .= "<div class='table-responsive'>";

	$table_branch .= "<table id='myTable' class='table table-bordered' style='background-color:white;'>
						<thead><td>Branch \ Privilege</td>";


	# PRIVILEGE

	$a = 0;

	$oRes_privilege = $oDB->Query($sql_privilege)or die(mysql_error());

	while ($axRow_privilege = $oRes_privilege->FetchRow(DBI_ASSOC)) {
			
		$table_branch .= "<td style='background-color:#B5D334;text-align:center'>".$axRow_privilege['txt']."</td>";

		$privilege_id[$a]  = $axRow_privilege['id'];

		$a++;

	}


	# COUPON

	$a = 0;

	$oRes_coupon = $oDB->Query($sql_coupon)or die(mysql_error());

	while ($axRow_coupon = $oRes_coupon->FetchRow(DBI_ASSOC)) {
			
		$table_branch .= "<td style='background-color:#FF2795;text-align:center'>".$axRow_coupon['txt']."</td>";

		$coupon_id[$a]  = $axRow_coupon['id'];

		$a++;

	}


	# ACTIVITY

	$a = 0;

	$oRes_activity = $oDB->Query($sql_activity)or die(mysql_error());

	while ($axRow_activity = $oRes_activity->FetchRow(DBI_ASSOC)) {
			
		$table_branch .= "<td style='background-color:#00A3D7;text-align:center'>".$axRow_activity['txt']."</td>";

		$activity_id[$a]  = $axRow_activity['id'];

		$a++;

	}


	# HBD

	$a = 0;

	$oRes_hbd = $oDB->Query($sql_hbd)or die(mysql_error());

	while ($axRow_hbd = $oRes_hbd->FetchRow(DBI_ASSOC)) {
			
		$table_branch .= "<td style='background-color:#FF7507;text-align:center'>".$axRow_hbd['txt']."</td>";

		$hbd_id[$a]  = $axRow_hbd['id'];

		$a++;

	}
				
	$table_branch .= "<td style='text-align:center'>Total Branch</td>";
	
	$table_branch .= "</thead><tbody>";

	$oRes_branch = $oDB->Query($sql_branch)or die(mysql_error());

	while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)) {

		$table_branch .= "<tr><td class='td_head'>".$axRow_branch['txt']."</td>";

		$total_branch = 0;

		for ($i=0; $i < count($privilege_id); $i++) {

			$priv_use = get_total_privilege_use($privilege_id[$i],$id,$card_id,"");

			$total_branch += $priv_use;

			$table_branch .= "<td style='text-align:center'>".$priv_use."</td>";

		}

		for ($i=0; $i < count($coupon_id); $i++) {

			$coup_use = get_total_coupon_use($coupon_id[$i],$id,$card_id,"");

			$total_branch += $coup_use;

			$table_branch .= "<td style='text-align:center'>".$coup_use."</td>";

		}

		for ($i=0; $i < count($activity_id); $i++) {

			$acti_use = get_total_activity_use($activity_id[$i],$id,$card_id,"");

			$total_branch += $acti_use;

			$table_branch .= "<td style='text-align:center'>".$acti_use."</td>";

		}

		for ($i=0; $i < count($hbd_id); $i++) {

			$hbd_use = get_total_coupon_use($hbd_id[$i],$id,$card_id,"");

			$total_branch += $hbd_use;

			$table_branch .= "<td style='text-align:center'>".$hbd_use."</td>";

		}
		
		$table_branch .= "<td style='text-align:center'>".$total_branch."</td>";
		
		$table_branch .= "</tr>";
	
	}


	$table_branch .="</table>";
										
	$table_branch .= "</div>";

}



$oTmp->assign('data', $axRow);

$oTmp->assign('table_branch', $table_branch);

$oTmp->assign('is_menu', 'is_analytics');

$oTmp->assign('content_file', 'analytics/your_branch.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());

}

//========================================//

?>

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

if ($_SESSION['role_action']['nearby_distance']['edit'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];


$sql = 'SELECT * FROM mi_setting';

$oRes = $oDB->Query($sql);

$asData = array();

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$asData = $axRow;
}


if($Act == 'save') {

	$sql_distance = "";


	$radius_distance = trim_txt($_REQUEST['radius_distance']);
	$first_card = trim_txt($_REQUEST['first_card']);
	$second_card = trim_txt($_REQUEST['second_card']);
	$redeem = trim_txt($_REQUEST['redeem']);
	$coupon = trim_txt($_REQUEST['coupon']);
	$birthday_coupon = trim_txt($_REQUEST['birthday_coupon']);
	$activity = trim_txt($_REQUEST['activity']);
	$point = trim_txt($_REQUEST['point']);
	$stamp = trim_txt($_REQUEST['stamp']);


	$sql_distance .= 'radius_distance="'.$radius_distance.'"';
	$sql_distance .= ',first_card="'.$first_card.'"'; 
	$sql_distance .= ',second_card="'.$second_card.'"'; 
	$sql_distance .= ',redeem="'.$redeem.'"'; 
	$sql_distance .= ',coupon="'.$coupon.'"'; 
	$sql_distance .= ',birthday_coupon="'.$birthday_coupon.'"'; 
	$sql_distance .= ',activity="'.$activity.'"'; 
	$sql_distance .= ',point="'.$point.'"'; 
	$sql_distance .= ',stamp="'.$stamp.'"'; 
	$sql_distance .= ',distance_updateddate="'.$time_insert.'"';   
	$sql_distance .= ',distance_updatedby="'.$_SESSION['UID'].'"';	



	# UPDATE

	$do_sql_distance = "UPDATE mi_setting SET ".$sql_distance."";

	$oDB->QueryOne($do_sql_distance);
	

	echo '<script type="text/javascript"> window.location.href="general.php"; </script>';

	exit;

}


$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_setting');

$oTmp->assign('content_file', 'setting/general_create.htm');

$oTmp->display('layout/template.html');


//========================================//


$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
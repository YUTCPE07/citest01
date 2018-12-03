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

if ($_SESSION['role_action']['ma_setting']['edit'] != 1) {

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

	$sql_setting = "";


	$card_charge = trim_txt($_REQUEST['card_charge']);
	$card_service_fee = trim_txt($_REQUEST['card_service_fee']);
	$promotion_charge = trim_txt($_REQUEST['promotion_charge']);
	$promotion_service_fee = trim_txt($_REQUEST['promotion_service_fee']);
	$transaction_charge = trim_txt($_REQUEST['transaction_charge']);
	$transaction_service_fee = trim_txt($_REQUEST['transaction_service_fee']);
	$pama_PackageMasterID = trim_txt($_REQUEST['pama_PackageMasterID']);


	$sql_setting .= 'card_charge="'.$card_charge.'"';
	$sql_setting .= ',card_service_fee="'.$card_service_fee.'"'; 
	$sql_setting .= ',promotion_charge="'.$promotion_charge.'"'; 
	$sql_setting .= ',promotion_service_fee="'.$promotion_service_fee.'"'; 
	$sql_setting .= ',transaction_charge="'.$transaction_charge.'"'; 
	$sql_setting .= ',transaction_service_fee="'.$transaction_service_fee.'"'; 	
	$sql_setting .= ',pama_PackageMasterID="'.$pama_PackageMasterID.'"'; 	



	# UPDATE

	$do_sql_setting = "UPDATE mi_setting SET ".$sql_setting."";

	$oDB->QueryOne($do_sql_setting);
	

	echo '<script type="text/javascript"> window.location.href="setting.php"; </script>';

	exit;

}



#  package dropdownlist

$as_package_id = dropdownlist_from_table($oDB,'package_master','pama_PackageMasterID','pama_Name','',' ORDER BY pama_Name ASC');

$oTmp->assign('package_master', $as_package_id);



$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_setting');

$oTmp->assign('content_file', 'setting/setting_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
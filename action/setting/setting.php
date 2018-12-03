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

if ($_SESSION['role_action']['ma_setting']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];


$sql = 'SELECT mi_setting.*,
			package_master.pama_Name
		FROM mi_setting
		LEFT JOIN package_master
		ON package_master.pama_PackageMasterID = mi_setting.pama_PackageMasterID';

$oRes = $oDB->Query($sql);

$asData = array();

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$asData = $axRow;
}




$oTmp->assign('data', $asData);
$oTmp->assign('is_menu', 'is_setting');
$oTmp->assign('content_file', 'setting/setting.htm');
$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
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

if ($_SESSION['role_action']['brand_sms']['edit'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];


if ($Act == 'edit' && $id != '' ){

	$sql = 'SELECT flag_sms FROM mi_brand WHERE brand_id = "'.$id.'" ';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

	$asData = array();

	$asData = $axRow;

} else if($Act == 'save') {

	$sql_brand = "";

	$flag_sms = trim_txt($_REQUEST['flag_sms']);



	$sql_brand .= 'flag_sms="'.$flag_sms.'"';  

	$sql_brand .= ',sms_update_date="'.$time_insert.'"';   

	$sql_brand .= ',sms_update_by="'.$_SESSION['UID'].'"';	




	if ($id) {

		# UPDATE

		$do_sql_brand = "UPDATE mi_brand SET ".$sql_brand." WHERE brand_id= '".$id."'";

		$oDB->QueryOne($do_sql_brand);
	}	

	echo '<script type="text/javascript"> window.location.href="brand_sms.php"; </script>';

	exit;
}



$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('data_table', $data_table);

$oTmp->assign('content_file', 'brand/brand_sms_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
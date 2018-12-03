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

if ($_SESSION['role_action']['approve_otp']['edit'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");
$Act = $_REQUEST['act'];
$id = $_REQUEST['id'];


if ($Act == 'edit' && $id != '' ){

	$sql = 'SELECT pre_code, flag_numberic FROM mi_brand WHERE brand_id = "'.$id.'" ';
	$oRes = $oDB->Query($sql);
	$axRow = $oRes->FetchRow(DBI_ASSOC);

	$asData = array();

	$axRow['pre_1'] = substr($axRow['pre_code'], 0, 1);
	$axRow['pre_2'] = substr($axRow['pre_code'], 1, 1);
	$axRow['pre_3'] = substr($axRow['pre_code'], 2, 1);

	$asData = $axRow;

} else if($Act == 'save') {

	$sql_brand = "";

	$pre_1 = trim_txt($_REQUEST['pre_1']);
	$pre_2 = trim_txt($_REQUEST['pre_2']);
	$pre_3 = trim_txt($_REQUEST['pre_3']);
	$flag_numberic = trim_txt($_REQUEST['flag_numberic']);

	$sql_brand = 'pre_code="'.$pre_1.$pre_2.$pre_3.'"';  
	$sql_brand .= ',flag_numberic="'.$flag_numberic.'"'; 


	# CHECK CODE

	$sql_check = "SELECT pre_code FROM mi_brand WHERE pre_code='".$pre_1.$pre_2.$pre_3."' AND brand_id!='".$id."'";
	$check_code = $oDB->QueryOne($sql_check);

	if ($check_code) {

		echo '<script type="text/javascript">alert("Code ซ้ำ กรุณากรอกใหม่")</script>';
		echo '<script type="text/javascript"> window.location.href="gen_code_create.php?act=edit&id='.$id.'"; </script>';
		exit;
	}

	if ($id) {

		# UPDATE

		$do_sql_brand = "UPDATE mi_brand SET ".$sql_brand." WHERE brand_id= '".$id."'";
		$oDB->QueryOne($do_sql_brand);
	}	

	echo '<script type="text/javascript"> window.location.href="gen_code.php"; </script>';
	exit;
}



$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_setting');

$oTmp->assign('content_file', 'setting/gen_code_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
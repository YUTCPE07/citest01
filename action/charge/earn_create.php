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

if (($_SESSION['role_action']['charge_earn']['edit'] != 1)) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];
$id = $_REQUEST['id'];
$time_insert = date("Y-m-d H:i:s");



if( $Act == 'edit' && $id != '' ){

	# EDIT

	$sql = 'SELECT DISTINCT	hilight_coupon.*,
				mi_brand.name AS brand_name
				FROM hilight_coupon
				LEFT JOIN mi_brand
				ON mi_brand.brand_id = hilight_coupon.bran_BrandID
				WHERE hilight_coupon.coup_CouponID = "'.$id.'"';

	$oRes = $oDB->Query($sql);

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$asData = $axRow;
	}

} else if( $Act == 'save' ) {

	$do_sql_earn = "";

	$id = trim_txt($_REQUEST['id']);

	$coup_ChargePercent = trim_txt($_REQUEST['coup_ChargePercent']);

	$coup_ExpenseFee = trim_txt($_REQUEST['coup_ExpenseFee']);



	$sql_earn = '';

	$table_earn = 'hilight_coupon';



	$sql_earn .= 'coup_ChargePercent="'.$coup_ChargePercent.'"'; 

	$sql_earn .= ',coup_ExpenseFee="'.$coup_ExpenseFee.'"';



	if($id){

		# UPDATE

		$do_sql_earn = "UPDATE ".$table_earn." SET ".$sql_earn." WHERE coup_CouponID= '".$id."'";
	}

	$oDB->QueryOne($do_sql_earn);	

	echo '<script>window.location.href="earn_attention.php";</script>';

	exit;
}




$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_charge_condition');

$oTmp->assign('content_file', 'charge/earn_create.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
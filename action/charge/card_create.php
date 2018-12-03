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

if (($_SESSION['role_action']['charge_card']['edit'] != 1)) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$time_insert = date("Y-m-d H:i:s");



if( $Act == 'edit' && $id != '' ){

	# EDIT

	$sql = 'SELECT DISTINCT	mi_card.*,
				mi_brand.name AS brand_name
				FROM mi_card
				LEFT JOIN mi_brand
				ON mi_brand.brand_id = mi_card.brand_id
				WHERE mi_card.card_id = "'.$id.'"';

	$oRes = $oDB->Query($sql);

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$asData = $axRow;
	}

} else if( $Act == 'save' ) {

	$do_sql_card = "";

	$id = trim_txt($_REQUEST['id']);

	$charge_percent = trim_txt($_REQUEST['charge_percent']);

	$expense_fee = trim_txt($_REQUEST['expense_fee']);



	$sql_card = '';

	$table_card = 'mi_card';



	$sql_card .= 'charge_percent="'.$charge_percent.'"';  

	$sql_card .= ',expense_fee="'.$expense_fee.'"';  



	if($id){

		# UPDATE

		$do_sql_card = "UPDATE ".$table_card." SET ".$sql_card." WHERE card_id= '".$id."'";
	}

	$oDB->QueryOne($do_sql_card);	

	echo '<script>window.location.href="card.php";</script>';

	exit;
}




$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_charge_condition');

$oTmp->assign('content_file', 'charge/card_create.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
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

if ($_SESSION['role_action']['expiry']['edit'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];



if( $Act == 'edit' && $id != '' ){

	# EDIT

	$sql = 'SELECT messages.mess_Notification,
					messages.mess_UpdatedDate,
					mi_brand.name

			FROM messages

			LEFT JOIN mi_brand
			ON mi_brand.brand_id = messages.bran_BrandID

			WHERE messages.bran_BrandID ="'.$id.'"
			AND messages.mess_Type = "Expiry"';

	$oRes = $oDB->Query($sql);

	$i=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$asData = $axRow;
	}


} else if( $Act == 'save' ){

	# SAVE

	$mess_NearBy = trim_txt($_REQUEST['mess_NearBy']);



	$sql_near = '';

	$table_near = 'messages';



	$sql_near .= 'mess_Notification="'.$mess_Expiry.'"'; 

	$sql_near .= ',mess_UpdatedDate="'.$time_insert.'"';   

	$sql_near .= ',mess_UpdatedBy="'.$_SESSION['UID'].'"';  



	if ($id) {

		# UPDATE

		$do_sql_near = 'UPDATE '.$table_near.' SET '.$sql_near.' WHERE bran_BrandID="'.$id.'" WHERE mess_Type="Expiry"';
	}


	$oDB->QueryOne($do_sql_near);

	echo '<script> window.location.href="expiry.php"; </script>';

	exit;
}







$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_expiry');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_messages', 'in');

$oTmp->assign('content_file', 'message/expiry_create.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
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

if ($_SESSION['role_action']['greeting_messages']['edit'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$Act = $_REQUEST['act'];
$id = $_REQUEST['id'];
$time_insert = date("Y-m-d H:i:s");

if( $Act == 'edit' && $id != '' ){

	# EDIT

	$sql = '';

	$sql .= 'SELECT
				mi_card.*,
				mi_brand.name AS brand_name,
				mi_card_type.name AS card_type_name

				FROM mi_card

				LEFT JOIN mi_brand
				ON mi_brand.brand_id = mi_card.brand_id

				LEFT JOIN mi_card_type
				ON mi_card_type.card_type_id = mi_card.card_type_id

				WHERE mi_card.card_id = "'.$id.'"';

	$oRes = $oDB->Query($sql);
	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$axRow['date_expired'] = DateOnly($axRow['date_expired']);

		if ($axRow['period_type']==2) { $axRow['period_type_other'] = $axRow['period_type_other'].' Months'; }
		if ($axRow['period_type']==3) { $axRow['period_type_other'] = $axRow['period_type_other'].' Years'; }
		if ($axRow['period_type']==4) { $axRow['period_type_other'] = 'Member Life Time'; }

		if ($axRow['description']=="" || !$axRow['description']) { $axRow['description']="-"; }
		else { $axRow['description'] = nl2br($axRow['description']); }

		$axRow['member_fee'] = number_format($axRow['member_fee'],2).' ฿';

		$asData = $axRow;
	}

} else if( $Act == 'save' ){

	# SAVE

	$do_sql_card = "";
	$id = trim_txt($_REQUEST['id']);
	$greeting_massages = trim_txt($_REQUEST['greeting_messages']);
	$greeting_messages_ckedit = base64_encode(trim_txt(htmlspecialchars($_REQUEST['greeting_messages_ckedit'])));
	$greeting_type = trim_txt($_REQUEST['greeting_type']);
	$greeting_accept = trim_txt($_REQUEST['greeting_accept']);

	$sql_card = '';

	$table_card = 'mi_card';


	# Action with card table  

	$sql_card .= 'greeting_messages="'.$greeting_massages.'"';
	$sql_card .= ',greeting_messages_ckedit="'.$greeting_messages_ckedit.'"';
	$sql_card .= ',greeting_type="'.$greeting_type.'"';

	if ($greeting_accept) {

		$sql_card .= ',greeting_accept="'.$greeting_accept.'"';

	} else {

		$sql_card .= ',greeting_accept="No"';
	}

	$sql_card .= ',greeting_updateddate="'.$time_insert.'"';
	$sql_card .= ',greeting_updatedby="'.$_SESSION['UID'].'"';


	if($id){

		# UPDATE

		$do_sql_card = "UPDATE ".$table_card." SET ".$sql_card." WHERE card_id= '".$id."' ";
		$oDB->QueryOne($do_sql_card);
	}

	echo '<script>window.location.href="greeting_messages.php";</script>';
	exit;
}






$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_greeting_messages');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_messages', 'in');

$oTmp->assign('content_file', 'message/greeting_messages_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
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

if ($_SESSION['role_action']['mi_topic']['edit'] != 1 && $_SESSION['role_action']['ma_topic']['edit'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$time_insert = date("Y-m-d H:i:s");
$Act = $_REQUEST['act'];
$id = $_REQUEST['id'];


# SEARCH MAX TOPIC_ID

	$sql_get_last_ins = 'SELECT max(coto_TopicID) FROM communication_topic';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################


if ($Act == 'edit' && $id != '' ){

	$sql = 'SELECT communication_topic.*
  			FROM communication_topic
			WHERE coto_TopicID = "'.$id.'" ';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

	$asData = array();

	$asData = $axRow;


} else if($Act == 'save') {

	$sql_topic = "";

	$coto_Name = trim_txt($_REQUEST['coto_Name']);

	$coto_Email = trim_txt($_REQUEST['coto_Email']);

	$coto_Mobile = trim_txt($_REQUEST['coto_Mobile']);

	$coto_Branch = trim_txt($_REQUEST['coto_Branch']);

	$coto_Anywhere = trim_txt($_REQUEST['coto_Anywhere']);



	$sql_topic .= 'coto_Name="'.$coto_Name.'"'; 

	$sql_topic .= ',coto_Email="'.$coto_Email.'"';

	$sql_topic .= ',coto_Mobile="'.$coto_Mobile.'"';   

	$sql_topic .= ',coto_Branch="'.$coto_Branch.'"';   

	$sql_topic .= ',coto_Anywhere="'.$coto_Anywhere.'"'; 

	$sql_topic .= ',coto_UpdatedDate="'.$time_insert.'"';   

	$sql_topic .= ',coto_UpdatedBy="'.$_SESSION['UID'].'"';




	if ($id) {

		# UPDATE

		$do_sql_topic = "UPDATE communication_topic SET ".$sql_topic." WHERE coto_TopicID= '".$id."'";

		$oDB->QueryOne($do_sql_topic);

	} else if (!$id) {

		# INSERT

		$sql_topic .= ',coto_TopicID="'.$id_new.'"';  

		$sql_topic .= ',coto_CreatedDate="'.$time_insert.'"';  

		$sql_topic .= ',coto_CreatedBy="'.$_SESSION['UID'].'"'; 

		$do_sql_topic = "INSERT INTO communication_topic SET ".$sql_topic;

		$oDB->QueryOne($do_sql_topic);
	}

	echo '<script type="text/javascript">window.location.href="topic.php";</script>';

	exit;
}


$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_communication');

$oTmp->assign('content_file', 'communication/topic_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
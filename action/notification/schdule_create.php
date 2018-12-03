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

if (($_SESSION['role_action']['push_notification']['add'] != 1) || ($_SESSION['role_action']['push_notification']['edit'] != 1)) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");
$time_pic = date("Ymd_His");
$Act = $_REQUEST['act'];
$id = $_REQUEST['id'];


# SEARCH MAX PUSH ID

	$sql_get_last_ins = 'SELECT max(scno_SchduleNotificationID) FROM schdule_notification';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################


if( $Act == 'edit' && $id != '' ){

	# EDIT

	$sql = 'SELECT * FROM schdule_notification WHERE scno_SchduleNotificationID ='.$id;

	$oRes = $oDB->Query($sql);

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$axRow['hour_start'] = substr($axRow['scno_StartTime'], 0, 2);
		if ($axRow['hour_start'] == "00") { $axRow['hour_start'] = ''; }

		$axRow['minute_start'] = substr($axRow['scno_StartTime'], 3, 2);
		// if ($axRow['minute_start'] == "00") { $axRow['minute_start'] = ''; }

		$axRow['hour_end'] = substr($axRow['scno_EndTime'], 0, 2);
		if ($axRow['hour_end'] == "00") { $axRow['hour_end'] = ''; }

		$axRow['minute_end'] = substr($axRow['scno_EndTime'], 3, 2);
		// if ($axRow['minute_end'] == "00") { $axRow['minute_end'] = ''; }

		$asData = $axRow;
	}

} else if( $Act == 'save' ){

	# SAVE

	$bran_BrandID = trim_txt($_REQUEST['bran_BrandID']);
	$scno_Name = trim_txt($_REQUEST['scno_Name']);
	$puno_PushNotificationID = trim_txt($_REQUEST['puno_PushNotificationID']);
	$puno_Type = trim_txt($_REQUEST['puno_Type']);
	$tali_TargetListID = trim_txt($_REQUEST['tali_TargetListID']);
	$scno_StartDate = trim_txt($_REQUEST['StartDate']);
	$scno_EndDate = trim_txt($_REQUEST['EndDate']);
	$hour_start = trim_txt($_REQUEST['hour_start']);
	$minute_start = trim_txt($_REQUEST['minute_start']);
	$hour_end = trim_txt($_REQUEST['hour_end']);
	$minute_end = trim_txt($_REQUEST['minute_end']);



	$sql_schdule = '';

	$table_schdule = 'schdule_notification';




	$sql_schdule .= 'bran_BrandID="'.$bran_BrandID.'"'; 
	
	if($scno_Name){	$sql_schdule .= ',scno_Name="'.$scno_Name.'"';   }
	
	if($puno_PushNotificationID){	$sql_schdule .= ',puno_PushNotificationID="'.$puno_PushNotificationID.'"';   }
	
	if($puno_Type){	$sql_schdule .= ',puno_Type="'.$puno_Type.'"';   }
	
	if($tali_TargetListID){	$sql_schdule .= ',tali_TargetListID="'.$tali_TargetListID.'"';   }
	
	$sql_schdule .= ',scno_StartDate="'.$scno_StartDate.'"';
	
	$sql_schdule .= ',scno_EndDate="'.$scno_EndDate.'"';

	$sql_schdule .= ',scno_StartTime="'.$hour_start.':'.$minute_start.':00"';  

	$sql_schdule .= ',scno_EndTime="'.$hour_end.':'.$minute_end.':00"';  

	$sql_schdule .= ',scno_UpdatedDate="'.$time_insert.'"';   

	$sql_schdule .= ',scno_UpdatedBy="'.$_SESSION['UID'].'"';  






	if ($id) {

		# UPDATE

		$do_sql_schdule = 'UPDATE '.$table_schdule.' SET '.$sql_schdule.' WHERE scno_SchduleNotificationID="'.$id.'"';

	} else {

		# INSERT

		$sql_schdule .= ',scno_CreatedDate="'.$time_insert.'"';   

		$sql_schdule .= ',scno_CreatedBy="'.$_SESSION['UID'].'"';  

		if($id_new){	$sql_schdule .= ',scno_SchduleNotificationID="'.$id_new.'"';   }  

		$do_sql_schdule = 'INSERT INTO '.$table_schdule.' SET '.$sql_schdule;
	}

	$oDB->QueryOne($do_sql_schdule);	

	echo '<script> window.location.href="schdule_page.php"; </script>';

	exit;
}




#  brand dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' brand_id="'.$_SESSION['user_brand_id'].'" ';
}

$as_brand = dropdownlist_from_table($oDB,'mi_brand','brand_id','name',$where_brand,' ORDER BY name ASC');

$oTmp->assign('brand', $as_brand);



#  brand dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' bran_BrandID="'.$_SESSION['user_brand_id'].'" ';
}

$as_target = dropdownlist_from_table($oDB,'target_list','tali_TargetListID','tali_Name',$where_brand,' ORDER BY tali_Name ASC');

$oTmp->assign('target_list', $as_target);



$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('content_file', 'notification/schdule_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
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


# SEARCH NAME IMAGE

	$sql_get_old_img = 'SELECT puno_Image FROM push_notification WHERE puno_PushNotificationID='.$id;
	$get_old_img = $oDB->QueryOne($sql_get_old_img);
	$old_image = $get_old_img;

#######################################

# SEARCH MAX PUSH ID

	$sql_get_last_ins = 'SELECT max(puno_PushNotificationID) FROM push_notification';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################


if( $Act == 'edit' && $id != '' ){

	# EDIT

	$sql = 'SELECT * FROM push_notification WHERE puno_PushNotificationID ='.$id;

	$oRes = $oDB->Query($sql);

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$axRow['hour_start'] = substr($axRow['puno_StartTime'], 0, 2);
		if ($axRow['hour_start'] == "00") { $axRow['hour_start'] = ''; }

		$axRow['minute_start'] = substr($axRow['puno_StartTime'], 3, 2);
		// if ($axRow['minute_start'] == "00") { $axRow['minute_start'] = ''; }

		$axRow['hour_end'] = substr($axRow['puno_EndTime'], 0, 2);
		if ($axRow['hour_end'] == "00") { $axRow['hour_end'] = ''; }

		$axRow['minute_end'] = substr($axRow['puno_EndTime'], 3, 2);
		// if ($axRow['minute_end'] == "00") { $axRow['minute_end'] = ''; }

		$asData = $axRow;
	}

} else if( $Act == 'save' ){

	# SAVE

	$bran_BrandID = trim_txt($_REQUEST['bran_BrandID']);
	$puno_Header = trim_txt($_REQUEST['puno_Header']);
	$puno_Detail = trim_txt($_REQUEST['puno_Detail']);
	$puno_Url = trim_txt($_REQUEST['puno_Url']);
	$puno_StartDate = trim_txt($_REQUEST['StartDate']);
	$puno_EndDate = trim_txt($_REQUEST['EndDate']);
	$hour_start = trim_txt($_REQUEST['hour_start']);
	$minute_start = trim_txt($_REQUEST['minute_start']);
	$hour_end = trim_txt($_REQUEST['hour_end']);
	$minute_end = trim_txt($_REQUEST['minute_end']);
	$puno_Description = base64_encode(trim_txt(htmlspecialchars($_REQUEST['puno_Description'])));
	$puno_Type = trim_txt($_REQUEST['puno_Type']);



	$sql_push = '';

	$table_push = 'push_notification';



	
	$sql_push .= 'bran_BrandID="'.$bran_BrandID.'"'; 
	
	if($puno_Type){	$sql_push .= ',puno_Type="'.$puno_Type.'"';   }
	
	if($puno_Header){	$sql_push .= ',puno_Header="'.$puno_Header.'"';   }
	
	$sql_push .= ',puno_Detail="'.$puno_Detail.'"'; 
	
	$sql_push .= ',puno_Url="'.$puno_Url.'"'; 
	
	$sql_push .= ',puno_Description="'.$puno_Description.'"'; 

	$sql_push .= ',puno_ImagePath="'.$bran_BrandID.'/push_notification_upload/"';

	$sql_push .= ',puno_UpdatedDate="'.$time_insert.'"';   

	$sql_push .= ',puno_UpdatedBy="'.$_SESSION['UID'].'"';  

	if($_FILES["notification_image_upload"]["name"] != ""){

		$new_img_name = upload_img('notification_image_upload','image_'.$time_pic,'../../upload/'.$bran_BrandID.'/push_notification_upload/',640,400);

		$sql_push .= ',puno_Image="'.$new_img_name.'"';
	}






	if ($id) {

		# UPDATE

		if($new_img_name){	

			unlink_file($oDB,'push_notification','puno_Image','puno_PushNotificationID',$id,'../../upload/'.$brand_id.'/push_notification_upload/',$old_image);
		}

		$do_sql_push = 'UPDATE '.$table_push.' SET '.$sql_push.' WHERE puno_PushNotificationID="'.$id.'"';

	} else {

		# INSERT

		$sql_push .= ',puno_CreatedDate="'.$time_insert.'"';   

		$sql_push .= ',puno_CreatedBy="'.$_SESSION['UID'].'"';  

		if($id_new){	$sql_push .= ',puno_PushNotificationID="'.$id_new.'"';   }  

		$do_sql_push = 'INSERT INTO '.$table_push.' SET '.$sql_push;
	}

	$oDB->QueryOne($do_sql_push);	

	echo '<script> window.location.href="push_notification.php"; </script>';

	exit;
}




#  brand dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' brand_id="'.$_SESSION['user_brand_id'].'" ';
}

$as_brand = dropdownlist_from_table($oDB,'mi_brand','brand_id','name',$where_brand,' ORDER BY name ASC');

$oTmp->assign('brand_opt', $as_brand);



$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('type_file_upload', '<span class="text-rq">Type file : .jpg , .png , .gif only</span>');

$oTmp->assign('content_file', 'notification/push_notification_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
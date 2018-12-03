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

if (($_SESSION['role_action']['stamp']['add'] != 1) || ($_SESSION['role_action']['stamp']['edit'] != 1)) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//



$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$time_insert = date("Y-m-d H:i:s");

$path_upload_collection = $_SESSION['path_upload_collection'];



if( $Act == 'edit' && $id ){

	$sql = '';

	$sql .= 'SELECT

				motivation_plan_stamp.*,
				mi_brand.name AS brand_name,
				mi_brand.path_logo,
				collection_type.coty_Image

				FROM motivation_plan_stamp 

				LEFT JOIN collection_type
				ON motivation_plan_stamp.mops_CollectionTypeID = collection_type.coty_CollectionTypeID

				LEFT JOIN mi_brand
				ON motivation_plan_stamp.bran_BrandID = mi_brand.brand_id

				WHERE motivation_plan_stamp.mops_MotivationStampID = "'.$id.'"';

	$oRes = $oDB->Query($sql);

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$axRow['mops_EndDate'] = DateOnly($axRow['mops_EndDate']);

		$asData = $axRow;
	}

	$sql_status .= 'SELECT mops_Status FROM motivation_plan_stamp WHERE mops_MotivationStampHID = "'.$id.'"';

	$status = $oDB->QueryOne($sql_status);

	if ($status == 'F') {

		echo "<script> history.back(); </script>";

		exit();
	}


} else if( $Act == 'save' ){

	$id = trim_txt($_REQUEST['id']);

	$mops_Multiple = trim_txt($_REQUEST['mops_Multiple']);

	$StartDate_other = trim_txt($_REQUEST['StartDate_other']);

	$EndDate_other = trim_txt($_REQUEST['EndDate_other']);

	$mops_Name = trim_txt($_REQUEST['mops_Name']);


	$sql_stamp = '';

	$table_stamp = 'motivation_plan_stamp';




	# ACTION POINT

	if($mops_Multiple){	$sql_stamp .= 'mops_Multiple="'.$mops_Multiple.'"';   }

	if ($StartDate_other && $EndDate_other) {

		if($StartDate_other){	$sql_stamp .= ',mops_MultipleStartDate="'.$StartDate_other.'"';   }

		if($EndDate_other){	$sql_stamp .= ',mops_MultipleEndDate="'.$EndDate_other.'"';   }
	}

	if($_SESSION['UID']){	$sql_stamp .= ',mops_UpdatedBy="'.$_SESSION['UID'].'"';   }

	if($time_insert){	$sql_stamp .= ',mops_UpdatedDate="'.$time_insert.'"';   }

	if($mops_Name){	$sql_point .= ',mops_Name="'.$mops_Name.'"';   }



	if ($id){

		#	UPDATE

		$do_sql_stamp = "UPDATE ".$table_stamp." SET ".$sql_stamp." WHERE mops_MotivationStampID= '".$id."'";

		$oDB->QueryOne($do_sql_stamp);

	} else {

		echo "<script> history.back(); </script>";

		exit();
	}

	echo '<script>window.location.href = "stamp.php";</script>';

	exit;
}




$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_stamp');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_motivation', 'in');

$oTmp->assign('inner_motivation', 'in');

$oTmp->assign('content_file', 'motivation/stamp_edit.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
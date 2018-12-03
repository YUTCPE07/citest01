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

if (($_SESSION['role_action']['point']['add'] != 1) || ($_SESSION['role_action']['point']['edit'] != 1)) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$time_insert = date("Y-m-d H:i:s");



if( $Act == 'edit' && $id ){

	$sql = 'SELECT

				motivation_plan_point.*,
				mi_brand.name AS brand_name

				FROM motivation_plan_point 

				LEFT JOIN mi_brand
				ON motivation_plan_point.bran_BrandID = mi_brand.brand_id

				WHERE mopp_MotivationPointID = "'.$id.'"';

	$oRes = $oDB->Query($sql);

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$axRow['mopp_EndDate'] = DateOnly($axRow['mopp_EndDate']);

		$asData = $axRow;
	}


	$sql_status .= 'SELECT mopp_Status FROM motivation_plan_point WHERE mopp_MotivationPointID = "'.$id.'"';

	$status = $oDB->QueryOne($sql_status);

	if ($status == 'F') {

		echo "<script> history.back(); </script>";
		exit();
	}


} else if( $Act == 'save' ){


	$id = trim_txt($_REQUEST['id']);

	$mopp_Multiple = trim_txt($_REQUEST['mopp_Multiple']);

	$StartDate_other = trim_txt($_REQUEST['StartDate_other']);

	$EndDate_other = trim_txt($_REQUEST['EndDate_other']);

	$mopp_Name = trim_txt($_REQUEST['mopp_Name']);


	$sql_point = '';

	$table_point = 'motivation_plan_point';



	# ACTION POINT

	if($mopp_Multiple){	$sql_point .= 'mopp_Multiple="'.$mopp_Multiple.'"';   }

	if ($StartDate_other && $EndDate_other) {

		if($StartDate_other){	$sql_point .= ',mopp_MultipleStartDate="'.$StartDate_other.'"';   }

		if($EndDate_other){	$sql_point .= ',mopp_MultipleEndDate="'.$EndDate_other.'"';   }
	}

	if($_SESSION['UID']){	$sql_point .= ',mopp_UpdatedBy="'.$_SESSION['UID'].'"';   }

	if($time_insert){	$sql_point .= ',mopp_UpdatedDate="'.$time_insert.'"';   }

	if($mopp_Name){	$sql_point .= ',mopp_Name="'.$mopp_Name.'"';   }




	if ($id){

		#	UPDATE

		$do_sql_point = "UPDATE ".$table_point." SET ".$sql_point." WHERE mopp_MotivationPointID= '".$id."'";

		$oDB->QueryOne($do_sql_point);

	} else {

		echo "<script> history.back(); </script>";

		exit();
	}

	echo '<script>window.location.href = "point.php";</script>';

	exit;
}





$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('cancel', 'cancel');

$oTmp->assign('is_menu', 'is_point');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_motivation', 'in');

$oTmp->assign('inner_motivation', 'in');

$oTmp->assign('content_file', 'motivation/point_edit.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
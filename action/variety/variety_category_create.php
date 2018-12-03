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

if ($_SESSION['role_action']['variety_category']['add'] != 1 || $_SESSION['role_action']['variety_category']['add'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];


# SEARCH MAX VARIETY_CATEGORY_ID

	$sql_get_last_ins = 'SELECT max(vaca_VarietyCategoryID) FROM variety_category';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################



if ($Act == 'edit' && $id != '' ){


	$sql = '';

	$sql = 'SELECT variety_category.*
  			FROM variety_category
			WHERE vaca_VarietyCategoryID = "'.$id.'" ';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

	$asData = array();

	$asData = $axRow;


} else if($Act == 'save') {

	
	$sql_VarietyCategory = "";

	$vaca_Name = trim_txt($_REQUEST['vaca_Name']);

	$vaca_NameEn = trim_txt($_REQUEST['vaca_NameEn']);

	$vaca_Status = trim_txt($_REQUEST['vaca_Status']);

	$vaca_Type = trim_txt($_REQUEST['vaca_Type']);

	foreach ($_POST['vaca_MainCategoryBrandID'] as $category_id) {

		$vaca_MainCategoryBrandID .= $category_id.",";
	}

	$str_category = strlen($vaca_MainCategoryBrandID);

	$vaca_MainCategoryBrandID = substr($vaca_MainCategoryBrandID,0,$str_category-1);

	if ($vaca_MainCategoryBrandID=='') { $vaca_MainCategoryBrandID = '0'; }



	if($vaca_Name){	$sql_VarietyCategory .= 'vaca_Name="'.$vaca_Name.'"';   }

	if($vaca_NameEn){	$sql_VarietyCategory .= ',vaca_NameEn="'.$vaca_NameEn.'"';   }

	if($vaca_Type){	$sql_VarietyCategory .= ',vaca_Type="'.$vaca_Type.'"';   }

	if($vaca_Status){	$sql_VarietyCategory .= ',vaca_Status="'.$vaca_Status.'"';   }

	if($vaca_MainCategoryBrandID){	$sql_VarietyCategory .= ',vaca_MainCategoryBrandID="'.$vaca_MainCategoryBrandID.'"';   }

	if($time_insert){	$sql_VarietyCategory .= ',vaca_UpdatedDate="'.$time_insert.'"';   }



	if ($id!='' && $id>0) {

		#	update

		$do_sql_variety =  "UPDATE variety_category SET ".$sql_VarietyCategory." WHERE vaca_VarietyCategoryID= '".$id."'";

		$oDB->QueryOne($do_sql_variety);

	} else if ($id=='') {

		#	insert

		if($id_new){	$sql_VarietyCategory .= ',vaca_VarietyCategoryID="'.$id_new.'"';   }

		if($time_insert){	$sql_VarietyCategory .= ',vaca_CreatedDate="'.$time_insert.'"';	}

		$do_sql_variety = "INSERT INTO variety_category SET ".$sql_VarietyCategory;

		$oDB->QueryOne($do_sql_variety);
	}

	echo '<script type="text/javascript">window.location.href = "variety_category.php";</script>';

	exit;
}



$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_variety_category');

$oTmp->assign('content_file', 'variety/variety_category_create.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
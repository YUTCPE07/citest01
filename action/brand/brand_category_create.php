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

if (($_SESSION['role_action']['brand_category']['add'] != 1) || ($_SESSION['role_action']['brand_category']['edit'] != 1)) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];
$id = $_REQUEST['id'];


# SEARCH MAX ID

	$sql_get_last_ins = 'SELECT max(category_brand_id) FROM mi_category_brand';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################


if( $Act == 'edit' && $id != '' ){

	# edit page

	$sql = 'SELECT * FROM mi_category_brand WHERE category_brand_id = "'.$id.'"';

	$oRes = $oDB->Query($sql);

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$asData = $axRow;
	}

} else if( $Act == 'save' ){

	$do_sql_card = "";

	$bran_BrandID = trim_txt($_REQUEST['bran_BrandID']);

	$brand_name = trim_txt($_REQUEST['brand_name']);

	$brand_name_en = trim_txt($_REQUEST['brand_name_en']);

	$main_category_brand_id = trim_txt($_REQUEST['main_category_brand_id']);


	$time_insert = date("Y-m-d H:i:s");


	$sql_category = '';

	$table_category = 'mi_category_brand';



	# CATEGORY

	if($brand_name){	$sql_category .= 'name="'.$brand_name.'"';   }

	if($brand_name_en){	$sql_category .= ',name_en="'.$brand_name_en.'"';   }

	if($main_category_brand_id){	$sql_category .= ',main_category_id="'.$main_category_brand_id.'"';   }

	if($time_insert){	$sql_category .= ',update_date="'.$time_insert.'"';   }


	if ($id) {

		$do_sql_category = "UPDATE ".$table_category." SET ".$sql_category." WHERE category_brand_id=".$id."";

	} else {

		if($time_insert){	$sql_category .= ',create_date="'.$time_insert.'"';   }

		$sql_category .= ',category_brand_id="'.$id_new.'"';

		$do_sql_category = 'INSERT INTO '.$table_category.' SET '.$sql_category;
	}

	$oDB->QueryOne($do_sql_category);

	echo '<script>window.location.href="brand_category.php";</script>';

	exit;
}




#  main_category_brand dropdownlist

$as_main = dropdownlist_from_table($oDB,'mi_main_category_brand','main_category_brand_id','name_en');

$oTmp->assign('main_category_brand_id_opt', $as_main);



$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_register_form');

$oTmp->assign('content_file', 'brand/brand_category_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
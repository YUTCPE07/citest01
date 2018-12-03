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

if ($_SESSION['role_action']['news_category']['add'] != 1 || $_SESSION['role_action']['news_category']['add'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];



# SEARCH MAX VARIETY_CATEGORY_ID

	$sql_get_last_ins = 'SELECT max(neca_NewsCategoryID) FROM news_category';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################



if ($Act == 'edit' && $id != '' ){

	$sql = '';

	$sql = 'SELECT news_category.*
  			FROM news_category
			WHERE neca_NewsCategoryID = "'.$id.'" ';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

	$asData = array();

	$asData = $axRow;


} else if($Act == 'save') {

	$sql_NewsCategory = "";

	$neca_Name = trim_txt($_REQUEST['neca_Name']);

	$neca_NameEn = trim_txt($_REQUEST['neca_NameEn']);

	$neca_Status = trim_txt($_REQUEST['neca_Status']);



	if($neca_Name){	$sql_NewsCategory .= 'neca_Name="'.$neca_Name.'"';   }

	if($neca_NameEn){	$sql_NewsCategory .= ',neca_NameEn="'.$neca_NameEn.'"';   }

	if($neca_Status){	$sql_NewsCategory .= ',neca_Status="'.$neca_Status.'"';   }

	if($time_insert){	$sql_NewsCategory .= ',neca_UpdatedDate="'.$time_insert.'"';   }



	if ($id!='' && $id>0) {

		#	update

		$do_sql_news =  "UPDATE news_category SET ".$sql_NewsCategory." WHERE neca_NewsCategoryID= '".$id."'";

		$oDB->QueryOne($do_sql_news);

	} else if ($id=='') {

		#	insert

		if($id_new){	$sql_NewsCategory .= ',neca_NewsCategoryID="'.$id_new.'"';   }

		if($time_insert){	$sql_NewsCategory .= ',neca_CreatedDate="'.$time_insert.'"';	}

		$do_sql_news = "INSERT INTO news_category SET ".$sql_NewsCategory;

		$oDB->QueryOne($do_sql_news);
	}


	echo '<script type="text/javascript">window.location.href = "news_category.php";</script>';

	exit;
}





$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_news_category');

$oTmp->assign('content_file', 'news/news_category_create.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
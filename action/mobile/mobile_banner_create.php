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

if ($_SESSION['role_action']['mobile_banner']['edit'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//



$time_insert = date("Y-m-d H:i:s");

$time_pic = date("Ymd_His");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];



# SEARCH MAX CARD_ID

	$sql_get_last_ins = 'SELECT max(banner_id) FROM mi_banner';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################


if ($Act == 'edit' && $id != '' ){

	$sql = 'SELECT a.*
  			FROM mi_banner AS a
 			LEFT JOIN mi_brand AS b
			ON a.brand_id = b.brand_id
			WHERE banner_id = "'.$id.'" ';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

	$asData = array();

	$asData = $axRow;


} else if($Act == 'save') {

	$sql_banner = "";

	$table_banner = "mi_banner";



	$brand_id = trim_txt($_REQUEST['brand_id']);

	$description = trim_txt($_REQUEST['description']);



	if($brand_id){	$sql_banner .= 'brand_id="'.$brand_id.'"';   }

	if($time_insert){	$sql_banner .= ',date_update="'.$time_insert.'"';   }



	$sql_banner .= ',description="'.$description.'"';  

	$sql_banner .= ',path_image="'.$brand_id.'/mobile_banner_upload/"'; 

	if( $_FILES["banner_image_upload"]["name"] != ""){

		$new_img_name = upload_img('banner_image_upload','banner_'.$time_pic,'../../upload/'.$brand_id.'/mobile_banner_upload/',640,250);

		unlink_file($oDB,$table_banner,'image','banner_id',$id,'../../upload/'.$brand_id.'/mobile_banner_upload/',$new_img_name);
	}

	if($new_img_name){	$sql_banner .= ',image="'.$new_img_name.'"';	}



	if ($id!='' && $id>0) {

		# UPDATE

		$do_sql_banner = "UPDATE mi_banner SET ".$sql_banner." WHERE banner_id= '".$id."'";

		$oDB->QueryOne($do_sql_banner);

	} else if ($id=='') {

		# INSERT

		if($id_new){	$sql_banner .= ',banner_id="'.$id_new.'"';   }

		$do_sql_banner = "INSERT INTO mi_banner SET ".$sql_banner;

		$oDB->QueryOne($do_sql_banner);
	}

	echo '<script type="text/javascript"> window.location.href = "mobile_banner.php"; </script>';

	exit;
}




# brand dropdown

$as_brand_name = dropdownlist_from_table($oDB,'mi_brand','brand_id','name','',' ORDER BY name');

$oTmp->assign('brand_name_opt', $as_brand_name);



$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('type_file_upload', '<span class="text-rq">Type file : .jpg , .png , .gif only</span>');

$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_mobile_banner');

$oTmp->assign('content_file', 'mobile/mobile_banner_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
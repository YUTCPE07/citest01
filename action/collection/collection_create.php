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

if ($_SESSION['role_action']['collection']['edit'] != 1 || $_SESSION['role_action']['collection']['add'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$time_insert = date("Y-m-d H:i:s");

$time_pic = date("Ymd_His");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$path_upload_collection = $_SESSION['path_upload_collection'];



# SEARCH MAX COLLECTION ID

	$sql_get_last_ins = 'SELECT max(coty_CollectionTypeID) FROM collection_type';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$collection_id_new = $id_last_ins+1;

#######################################

# SEARCH NAME OLD IMAGE

	$sql_get_old_img = 'SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID='.$id;
	$get_old_img = $oDB->QueryOne($sql_get_old_img);
	$old_image = $get_old_img;

#######################################



if ($Act == 'edit' && $id != '' ){

	$sql = 'SELECT *
  			FROM collection_type
			WHERE coty_CollectionTypeID = "'.$id.'" ';

	$oRes = $oDB->Query($sql);

	$i=0;

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$asData = $axRow;
	}

} else if($Act == 'save') {

	$coty_Name = trim_txt($_REQUEST['coty_Name']);

	$coty_Status = trim_txt($_REQUEST['coty_Status']);

	$coty_Type = trim_txt($_REQUEST['coty_Type']);



	$sql_collection = '';

	$table_collection = 'collection_type';



	if($coty_Name){	$sql_collection .= 'coty_Name="'.$coty_Name.'"';   }

	if($coty_Status){	$sql_collection .= ',coty_Status="'.$coty_Status.'"';   }

	if($coty_Type){	$sql_collection .= ',coty_Type="'.$coty_Type.'"';   }

	if($time_insert){	$sql_collection .= ',coty_UpdatedDate="'.$time_insert.'"';   }

	if($_SESSION['UID']){	$sql_collection .= ',coty_UpdatedBy="'.$_SESSION['UID'].'"';   }

	if( $_FILES["collection_image_upload"]["name"] != ""){

		$new_img_name = upload_img('collection_image_upload','collection_'.$time_pic,$path_upload_collection,200,200);

		unlink_file($oDB,$table_collection,'coty_Image','coty_CollectionTypeID',$id,$path_upload_collection,$old_image);
	}

	if($new_img_name){	$sql_collection .= ',coty_Image="'.$new_img_name.'"';	}




	if ($id) {

		# UPDATE

		$do_sql_collection =  "UPDATE ".$table_collection." SET ".$sql_collection." WHERE coty_CollectionTypeID= '".$id."'";

		$oDB->QueryOne($do_sql_collection);

	} else {

		# INSERT

		if($collection_id_new){	$sql_collection .= ',coty_CollectionTypeID="'.$collection_id_new.'"';   }

		if($time_insert){	$sql_collection .= ',coty_CreatedDate="'.$time_insert.'"';   }

		if($_SESSION['UID']){	$sql_collection .= ',coty_CreatedBy="'.$_SESSION['UID'].'"';   }

		$do_sql_collection = "INSERT INTO ".$table_collection." SET ".$sql_collection;

		$oDB->QueryOne($do_sql_collection);
	}

	echo '<script type="text/javascript">window.location.href="collection.php";</script>';

	exit;
}



$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('type_file_upload', '<span class="text-rq">Type file : .jpg , .png , .gif only</span>');

$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_collection');

$oTmp->assign('content_file', 'collection/collection_create.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
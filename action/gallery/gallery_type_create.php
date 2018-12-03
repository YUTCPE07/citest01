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

if (($_SESSION['role_action']['gallery']['add'] != 1) || ($_SESSION['role_action']['gallery']['edit'] != 1)) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$time_pic = date("Ymd_His");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];



# SEARCH NAME IMAGE

	$sql_get_old_img = 'SELECT image FROM gallery WHERE gall_GalleryID='.$id;
	$get_old_img = $oDB->QueryOne($sql_get_old_img);
	$old_image = $get_old_img;

#######################################

# SEARCH MAX GALLERY_ID

	$sql_get_last_ins = 'SELECT max(gall_GalleryID) FROM gallery';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################



if( $Act == 'edit' && $id != '' ){

	# EDIT

	$sql='	SELECT a.*,
					b.name AS brand_name
				FROM gallery AS a

				LEFT JOIN mi_brand AS b
				ON a.bran_BrandID = b.brand_id

				WHERE gall_GalleryID ='.$id;

	$oRes = $oDB->Query($sql);

	$i=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$asData = $axRow;
	}

} else if( $Act == 'save' ){

	# SAVE

	$bran_BrandID = trim_txt($_REQUEST['bran_BrandID']);

	$gall_Name = trim_txt($_REQUEST['gall_Name']);

	$gall_Status = trim_txt($_REQUEST['gall_Status']);

	$gall_Description = trim_txt($_REQUEST['gall_Description']);



	$sql_gallery = '';

	$table_gallery = 'gallery';



	$sql_gallery .= 'gall_Name="'.$gall_Name.'"';   

	$sql_gallery .= ',gall_Status="'.$gall_Status.'"';   

	if ($bran_BrandID) { $sql_gallery .= ',bran_BrandID="'.$bran_BrandID.'"'; }

	$sql_gallery .= ',gall_UpdatedDate="'.$time_insert.'"';   

	$sql_gallery .= ',gall_Description="'.$gall_Description.'"';

	$sql_gallery .= ',gall_UpdatedBy="'.$_SESSION['UID'].'"';   



	if ($id) {

		# UPDATE

		$do_sql_gallery = 'UPDATE '.$table_gallery.' SET '.$sql_gallery.' WHERE gall_GalleryID="'.$id.'"';

	} else {

		# INSERT

		$sql_gallery .= ',gall_CreatedBy="'.$_SESSION['UID'].'"';    

		$sql_gallery .= ',gall_CreatedDate="'.$time_insert.'"';   

		$sql_gallery .= ',gall_GalleryID="'.$id_new.'"';   

		$do_sql_gallery = 'INSERT INTO '.$table_gallery.' SET '.$sql_gallery;
	}


	$oDB->QueryOne($do_sql_gallery);	

	echo '<script> window.location.href="gallery_type.php"; </script>';

	exit;
}





#  brand dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' brand_id="'.$_SESSION['user_brand_id'].'" ';
}

$as_brand = dropdownlist_from_table($oDB,'mi_brand','brand_id','name',$where_brand,' ORDER BY name ASC');

$oTmp->assign('brand', $as_brand);




$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_gallery');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_setting', 'in');

$oTmp->assign('content_file', 'gallery/gallery_type_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
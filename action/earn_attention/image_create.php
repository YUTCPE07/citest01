<?php

header('Content-Type:text/html; charset=UTF-8');

//========================================//

ini_set("display_errors",1);
error_reporting(1);

//========================================//

include('../../include/common.php');
include('../../lib/function_normal.php');
include('../../include/common_check.php');
include('../../lib/phpqrcode/qrlib.php');
require_once('../../include/connect.php');

//========================================//

$oTmp = new TemplateEngine();
$oDB = new DBI();

if ($bDebug) {
	$oErr = new Tracker();
	$oDB->SetTracker($oErr);
}

//========================================//

if (($_SESSION['role_action']['earn_attention']['add'] != 1) || ($_SESSION['role_action']['earn_attention']['edit'] != 1)) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];
$id = $_REQUEST['id'];
$time_insert = date("Y-m-d H:i:s");
$time_insert_pic = date("Ymd_His");


# SEARCH MAX COUPON ID

	$sql_get_last_ins = 'SELECT max(hcim_HilightCouponImageID) FROM hilight_coupon_image';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################


# SEARCH NAME OLD IMAGE

	$sql_get_old_img = 'SELECT hcim_Image FROM hilight_coupon_image WHERE hcim_HilightCouponImageID='.$id;
	$get_old_img = $oDB->QueryOne($sql_get_old_img);
	$old_image = $get_old_img;

#######################################



if( $Act == 'edit' && $id != '' ) {

	# EDIT

	$sql = 'SELECT * FROM hilight_coupon_image WHERE hcim_HilightCouponImageID = "'.$id.'" ';

	$oRes = $oDB->Query($sql);

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$asData = $axRow;
	}


} else if( $Act == 'save' ){

	$id = trim_txt($_REQUEST['id']);

	$bran_BrandID = trim_txt($_REQUEST['bran_BrandID']);

	$hico_HilightCouponID = trim_txt($_REQUEST['hico_HilightCouponID']);

	$hcim_Type = trim_txt($_REQUEST['hcim_Type']);


	# IMAGE

	$total = count($_FILES['coupon_image_upload']['name']); 

	for( $i=0 ; $i < $total ; $i++ ) { 

		$sql_coupon = 'hcim_ImagePath="'.$bran_BrandID.'/earn_attention_upload/"';

		$sql_coupon .= ',bran_BrandID="'.$bran_BrandID.'"';

		$sql_coupon .= ',hico_HilightCouponID="'.$hico_HilightCouponID.'"';

		$sql_coupon .= ',hcim_Type="'.$hcim_Type.'"';

		$sql_coupon .= ',hcim_UpdatedDate="'.$time_insert.'"';   

		$sql_coupon .= ',hcim_UpdatedBy="'.$_SESSION['UID'].'"'; 

		$filename = $_FILES['coupon_image_upload']['name'][$i];
		$images = $_FILES['coupon_image_upload']["tmp_name"][$i];
		$ext = pathinfo($filename, PATHINFO_EXTENSION);

		if ($i==0 && $id) {

			if ($filename!="") {
				
				$new_img_name = 'coupon_'.$id.'_'.$time_insert_pic.'.'.$ext;
				$full_path = '../../upload/'.$bran_BrandID.'/earn_attention_upload/'.$new_img_name;

				if( move_uploaded_file($images,$full_path) ){

					$resize = new ResizeImage($full_path);
					$resize->resizeTo(640, 400, 'exact');
					$resize->saveImage($full_path);

					$sql_coupon .= ',hcim_Image="'.$new_img_name.'"';
				}

				unlink_file($oDB,'hilight_coupon_image','hcim_Image','hcim_HilightCouponImageID',$id,'../../upload/'.$bran_BrandID.'/earn_attention_upload/',$old_image);

				unlink('../../upload/'.$bran_BrandID.'/earn_attention_upload/'.$old_image);
			}

			$do_sql_coupon = "UPDATE hilight_coupon_image SET ".$sql_coupon." WHERE hcim_HilightCouponImageID= '".$id."'";

			$x++;

		} else {

			$sql_coupon .= ',hcim_HilightCouponImageID="'.$id_new.'"';  

			$sql_coupon .= ',hcim_CreatedDate="'.$time_insert.'"';   

			$sql_coupon .= ',hcim_CreatedBy="'.$_SESSION['UID'].'"'; 

			$new_img_name = 'coupon_'.$id_new.'_'.$time_insert_pic.'.'.$ext;
			$full_path = '../../upload/'.$bran_BrandID.'/earn_attention_upload/'.$new_img_name;

			if( move_uploaded_file($images,$full_path) ){

				$resize = new ResizeImage($full_path);
				$resize->resizeTo(640, 400, 'exact');
				$resize->saveImage($full_path);

				$sql_coupon .= ',hcim_Image="'.$new_img_name.'"';
			}

			$do_sql_coupon = "INSERT INTO hilight_coupon_image SET ".$sql_coupon;

			$id_new++;
		}

		$oDB->QueryOne($do_sql_coupon);
	}

	echo '<script>window.location.href = "image.php";</script>';

	exit;
}





#  brand dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' brand_id="'.$_SESSION['user_brand_id'].'" ';
}

$as_brand_id = dropdownlist_from_table($oDB,'mi_brand','brand_id','name',$where_brand,' ORDER BY name ASC');

$oTmp->assign('brand', $as_brand_id);




$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('type_file_upload', '<span class="text-rq">Type file : .jpg , .png , .gif only</span>');

$oTmp->assign('data_table', $data_table);

$oTmp->assign('content_file', 'earn_attention/image_create.htm');

$oTmp->display('layout/template.html');


//========================================//


$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>


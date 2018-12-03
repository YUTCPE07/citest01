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

if (($_SESSION['role_action']['product']['add'] != 1) || ($_SESSION['role_action']['product']['edit'] != 1)) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$time_pic = date("Ymd_His");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];



# SEARCH NAME IMAGE

	$sql_get_old_img = 'SELECT image FROM mi_products WHERE products_id='.$id;
	$get_old_img = $oDB->QueryOne($sql_get_old_img);
	$old_image = $get_old_img;

#######################################

# SEARCH MAX PRODUCTS_ID

	$sql_get_last_ins = 'SELECT max(products_id) FROM mi_products';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################



if( $Act == 'edit' && $id != '' ){

	# EDIT

	$sql = 'SELECT a.*,
					b.name AS brand_name

			FROM mi_products AS a

			LEFT JOIN mi_brand AS b
			ON a.brand_id = b.brand_id

			WHERE products_id ='.$id;

	$oRes = $oDB->Query($sql);

	$i=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$asData = $axRow;
	}

} else if( $Act == 'save' ){

	# SAVE

	$brand_id = trim_txt($_REQUEST['brand']);

	$products_name = trim_txt($_REQUEST['products_name']);

	$gallery_id = trim_txt($_REQUEST['gallery_id']);

	$flag_status = trim_txt($_REQUEST['flag_status']);

	$hilight_status = trim_txt($_REQUEST['hilight_status']);

	$description = trim_txt($_REQUEST['description']);

	$feature_type = trim_txt($_REQUEST['feature_type']);

	$product_type = trim_txt($_REQUEST['product_type']);



	$sql_products = '';

	$table_products = 'mi_products';






	if ($product_type == 'Image') {

		# IMAGE

		$total = count($_FILES['products_image_upload']['name']); 

		for( $i=0 ; $i < $total ; $i++ ) { 

			if($products_name){	$sql_products = 'name="'.$products_name.'"';   }

			if($flag_status){	$sql_products .= ',flag_status="'.$flag_status.'"';   }

			if($gallery_id){	$sql_products .= ',gallery_id="'.$gallery_id.'"';   }

			if($hilight_status){	$sql_products .= ',hilight_status="'.$hilight_status.'"';   }

			if($brand_id){	$sql_products .= ',brand_id="'.$brand_id.'"';   }

			if($time_insert){	$sql_products .= ',date_update="'.$time_insert.'"';   }

			if($feature_type){	$sql_products .= ',feature_type="'.$feature_type.'"';   }

			$sql_products .= ',description="'.$description.'"';

			if($product_type){	$sql_products .= ',product_type="'.$product_type.'"';   }

			$sql_products .= ',video_link=""';

			$sql_products .= ',path_image="'.$brand_id.'/product_upload/"'; 

			$filename = $_FILES['products_image_upload']['name'][$i];
			$images = $_FILES['products_image_upload']["tmp_name"][$i];
			$ext = pathinfo($filename, PATHINFO_EXTENSION);

			if ($i==0 && $id) {

				if ($filename!="") {

					$new_img_name = 'products_'.$id.'_'.$time_pic.'.'.$ext;
					$full_path = '../../upload/'.$brand_id.'/product_upload/'.$new_img_name;

					if( move_uploaded_file($images,$full_path) ){

						$resize = new ResizeImage($full_path);
						$resize->resizeTo(400, 400, 'exact');
						$resize->saveImage($full_path);

						$sql_products .= ',image="'.$new_img_name.'"';
					}

					unlink_file($oDB,'mi_products','image','products_id',$id,'../../upload/'.$brand_id.'/product_upload/',$old_image);

					unlink('../../upload/'.$brand_id.'/product_upload/'.$old_image);
				}

				$do_sql_products = 'UPDATE '.$table_products.' SET '.$sql_products.' WHERE products_id="'.$id.'"';

				$x++;

			} else {

				if($time_insert){	$sql_products .= ',date_create="'.$time_insert.'"';   }

				if($products_id){	$sql_products .= ',products_id="'.$id_new.'"';   } 

				$new_img_name = 'products_'.$id_new.'_'.$time_pic.'.'.$ext;
				$full_path = '../../upload/'.$brand_id.'/product_upload/'.$new_img_name;

				if( move_uploaded_file($images,$full_path) ){

					$resize = new ResizeImage($full_path);
					$resize->resizeTo(400, 400, 'exact');
					$resize->saveImage($full_path);

					$sql_products .= ',image="'.$new_img_name.'"';
				} 

				$do_sql_products = 'INSERT INTO '.$table_products.' SET '.$sql_products;

				$id_new++;
			}

			$oDB->QueryOne($do_sql_products);	
		}

	} else {

		# VIDEO

		$x = 1;


		for($i=0;$i<count($_POST["video_link"]);$i++) {

			if(trim($_POST["video_link"][$i]) != ""){

				if($products_name){	$sql_products = 'name="'.$products_name.'"';   }

				if($flag_status){	$sql_products .= ',flag_status="'.$flag_status.'"';   }

				if($gallery_id){	$sql_products .= ',gallery_id="'.$gallery_id.'"';   }

				if($hilight_status){	$sql_products .= ',hilight_status="'.$hilight_status.'"';   }

				if($brand_id){	$sql_products .= ',brand_id="'.$brand_id.'"';   }

				if($time_insert){	$sql_products .= ',date_update="'.$time_insert.'"';   }

				if($feature_type){	$sql_products .= ',feature_type="'.$feature_type.'"';   }

				$sql_products .= ',description="'.$description.'"';

				if($product_type){	$sql_products .= ',product_type="'.$product_type.'"';   }

				$sql_products .= ',path_image=""'; 
				
				$sql_products .= ',image=""';

				$sql_products .= ',video_link="'.$_POST["video_link"][$i].'"';

				if ($id) {

					if ($x == 1) {

						$do_sql_products = 'UPDATE '.$table_products.' SET '.$sql_products.' WHERE products_id="'.$id.'"';
						$x++;

					} else {

						if($time_insert){	$sql_products .= ',date_create="'.$time_insert.'"';   }

						if($products_id){	$sql_products .= ',products_id="'.$id_new.'"';   } 

						$do_sql_products = 'INSERT INTO '.$table_products.' SET '.$sql_products;

						$id_new++;
					}

				} else {

					if($time_insert){	$sql_products .= ',date_create="'.$time_insert.'"';   }

					if($products_id){	$sql_products .= ',products_id="'.$id_new.'"';   } 

					$do_sql_products = 'INSERT INTO '.$table_products.' SET '.$sql_products;

					$id_new++;
				}

				$oDB->QueryOne($do_sql_products);
			}
		}
	}

	echo '<script> window.location.href="gallery.php"; </script>';

	exit;
}






#  brand dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' brand_id="'.$_SESSION['user_brand_id'].'" ';
}

$as_brand = dropdownlist_from_table($oDB,'mi_brand','brand_id','name',$where_brand,' ORDER BY name ASC');

$oTmp->assign('brand_opt', $as_brand);




#  gallery dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' and bran_BrandID IN ('.$_SESSION['user_brand_id'].',0) ';
}

$as_gallery = dropdownlist_from_table($oDB,'gallery','gall_GalleryID','gall_Name','gall_Deleted=""'.$where_brand,' ORDER BY gall_Name ASC');

$oTmp->assign('gallery_opt', $as_gallery);




$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('type_file_upload', '<span class="text-rq">Type file : .jpg , .png , .gif only</span>');

$oTmp->assign('is_menu', 'is_product');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_setting', 'in');

$oTmp->assign('content_file', 'gallery/gallery_create.html');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
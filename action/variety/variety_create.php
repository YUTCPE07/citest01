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

if ($_SESSION['role_action']['variety']['add'] != 1 || $_SESSION['role_action']['variety']['add'] != 1) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$time_pic = date("Ymd_His");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];


	$sql_get_last_ins = 'SELECT max(vari_VarietyID) FROM variety';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

	$sql_get_old_img = 'SELECT vari_Image FROM variety WHERE vari_VarietyID='.$id;
	$get_old_img = $oDB->QueryOne($sql_get_old_img);
	$old_image = $get_old_img;

	if ($Act == 'edit' && $id != '' ){
		$sql = 'SELECT variety.*, vaca_Name ';
		$sql .= 'FROM variety ';
		$sql .= 'LEFT JOIN variety_category ON vari_VarietyCategoryID = vaca_VarietyCategoryID ';
		$sql .= 'WHERE vari_VarietyID = "'.$id.'" ';

		$oRes = $oDB->Query($sql);
		$axRow = $oRes->FetchRow(DBI_ASSOC);
		$asData = array();
		$asData = $axRow;
	}
	else if($Act == 'save') {

		$sql_Variety = "";
		$vari_Title = trim_txt($_REQUEST['vari_Title']);
		$vari_Status = trim_txt($_REQUEST['vari_Status']);
		$vari_Description = base64_encode(trim_txt(htmlspecialchars($_REQUEST['vari_Description'])));
		// $vari_BrandID = trim_txt($_REQUEST['vari_BrandID']);
		$vari_VarietyCategoryID = trim_txt($_REQUEST['vari_VarietyCategoryID']);

		if($vari_Title != '')				$sql_Variety .= 'vari_Title="'.$vari_Title.'"';
		if($vari_Status != '')				$sql_Variety .= ',vari_Status="'.$vari_Status.'"';
		$sql_Variety .= ',vari_Description="'.$vari_Description.'"';
		// if($vari_BrandID != '')				$sql_Variety .= ',vari_BrandID="'.$vari_BrandID.'"';
		if($vari_VarietyCategoryID != '')	$sql_Variety .= ',vari_VarietyCategoryID="'.$vari_VarietyCategoryID.'"';
		if($time_insert != '')				$sql_Variety .= ',vari_UpdatedDate="'.$time_insert.'"';
		$sql_Variety .= ',vari_ImagePath="variety_upload/"';

		if( $_FILES["vari_Image"]["name"] != "" ){
			$new_img_name = upload_img('vari_Image','variety_'. $time_pic,'../../upload/'.$vari_BrandID.'/variety_upload/', 400, 400);

			if($new_img_name){
				$sql_Variety .= ',vari_Image="'.$new_img_name.'"';
			}
		}

		if ($id!='' && $id > 0) {
			$do_sql_variety = "UPDATE variety SET ".$sql_Variety." WHERE vari_VarietyID= '".$id."'";
			$oDB->QueryOne($do_sql_variety);
		}
		else if ($id=='') {
			if($id_new){	$sql_Variety .= ',vari_VarietyID="'.$id_new.'"';   }
			if($time_insert){	$sql_Variety .= ',vari_CreatedDate="'.$time_insert.'"';	}

			$do_sql_variety = "INSERT INTO variety SET ".$sql_Variety;
			$oDB->QueryOne($do_sql_variety);
		}

		if ($old_image) {
			unlink_file($oDB,'variety','vari_Image','vari_VarietyID',$id,'../../upload/'.$vari_BrandID.'/variety_upload/',$old_image);
		}

		echo '<script type="text/javascript">window.location.href = "variety.php";</script>';
		exit;
	}

	# VARIETY CATEORY

	$as_VarietyCategoryID = '';

	$oRes_cate = $oDB->Query("SELECT vaca_VarietyCategoryID AS id, 
								CONCAT(vaca_Name,' | ',vaca_Type) AS name,
								vaca_MainCategoryBrandID AS main_id
						FROM variety_category
						WHERE vaca_Status=1 
						AND vaca_Deleted=0");

	if ($oRes_cate) {
		
		while ($axRow_cate = $oRes_cate->FetchRow(DBI_ASSOC)) {

			if ($asData['vari_VarietyCategoryID']==$axRow_cate['id']) { $select = 'selected'; }
			else { $select = ''; }

			$as_VarietyCategoryID .= '<option value="'.$axRow_cate['id'].'" '.$select.'>'.$axRow_cate["name"].'</option> ';

			# MAIN CATEGORY BRAND

			$sql_category = 'SELECT name
								FROM mi_main_category_brand 
								WHERE main_category_brand_id IN ('.$axRow_cate["main_id"].')';

			$oRes_category = $oDB->Query($sql_category);

			while ($category = $oRes_category->FetchRow(DBI_ASSOC)){

				$as_VarietyCategoryID .= '<option disabled>&nbsp;&nbsp;- '.$category["name"].'</option>';
			}
		}
	}

	$oTmp->assign('vaca_VarietyCategoryID', $as_VarietyCategoryID);



	$oTmp->assign('type_file_upload', '<span class="text-rq">Type file : .jpg , .png , .gif only</span>');
	$oTmp->assign('data', $asData);
	$oTmp->assign('act', 'save');
	$oTmp->assign('is_menu', 'is_variety');
	$oTmp->assign('content_file', 'variety/variety_create.htm');

	$oTmp->display('layout/template.html');

	$oDB->Close();

	if ($bDebug) {
		echo($oErr->GetAll());
	}
?>
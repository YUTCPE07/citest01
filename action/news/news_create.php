<?php

header('Content-Type:text/html; charset=UTF-8');

//========================================//

ini_set("display_errors",1);
error_reporting(1);
define('API_ACCESS_KEY','AIzaSyAmkjFhHjVXbBwVTft0RkQH_IgJMTCjQ20'); //API

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

if (($_SESSION['role_action']['news']['add'] != 1) || ($_SESSION['role_action']['news']['edit'] != 1)) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$time_insert = date("Y-m-d H:i:s");

$time_pic = date("Ymd_His");



# SEARCH NAME IMAGE

	$sql_get_old_img = 'SELECT news_Image FROM news WHERE news_NewsID='.$id;
	$get_old_img = $oDB->QueryOne($sql_get_old_img);
	$old_image = $get_old_img;

#######################################

# SEARCH MAX NEWS_ID

	$sql_get_last_ins = 'SELECT max(news_NewsID) FROM news';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################



if( $Act == 'edit' && $id != '' ){

	# EDIT

	$sql = 'SELECT news.* FROM news WHERE news_NewsID ='.$id;

	$oRes = $oDB->Query($sql);

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$asData = $axRow;
	}


} else if( $Act == 'save' ){

	$bran_BrandID = trim_txt($_REQUEST['bran_BrandID']);

	$news_Title = trim_txt($_REQUEST['news_Title']);

	$neca_NewsCategory = trim_txt($_REQUEST['neca_NewsCategory']);

	$news_PostedDate = trim_txt($_REQUEST['PostDate']);

	$news_Status = trim_txt($_REQUEST['news_Status']);

	$news_ShortDescription = trim_txt($_REQUEST['news_ShortDescription']);

	$news_Description = base64_encode(trim_txt(htmlspecialchars($_REQUEST['news_Description'])));

	foreach ($_POST['card_CardID'] as $card_id)

		$card_data .= $card_id.",";

	$str_card = strlen($card_data);

	$card_data = substr($card_data,0,$str_card-1);



	$sql_news = '';

	$table_news = 'news';



	if($bran_BrandID){	$sql_news .= 'bran_BrandID="'.$bran_BrandID.'"';   }

	if($news_Title){	$sql_news .= ',news_Title="'.$news_Title.'"';   }

	if($neca_NewsCategory){	$sql_news .= ',neca_NewsCategoryID="'.$neca_NewsCategory.'"';   }

	if($news_PostedDate){	$sql_news .= ',news_PostedDate="'.$news_PostedDate.'"';   }

	if($news_Status){	$sql_news .= ',news_Status="'.$news_Status.'"';   }

	if($card_data){	$sql_news .= ',card_CardID="'.$card_data.'"';   }

	$sql_news .= ',news_Description="'.$news_Description.'"';  

	$sql_news .= ',news_ShortDescription="'.$news_ShortDescription.'"';

	if($time_insert){	$sql_news .= ',news_UpdatedDate="'.$time_insert.'"';   }

	if($_SESSION['UID']){	$sql_news .= ',news_UpdatedBy="'.$_SESSION['UID'].'"';   }

	$sql_news .= ',news_ImagePath="'.$bran_BrandID.'/news_upload/"';  

	if( $_FILES["news_image_upload"]["name"] != ""){

		$new_img_name = upload_img('news_image_upload','news_'.$time_pic,'../../upload/'.$bran_BrandID.'/news_upload/',640,400);

		if($new_img_name){

			$sql_news .= ',news_Image="'.$new_img_name.'"';
		}

		if ($old_image) {

			unlink_file($oDB,'news','news_Image','news_NewsID',$id,'../../upload/news_upload/',$old_image);
		}
	}

	if ($old_image!="") {

		$sql_products .= ',flag_status="'.$flag_status.'"';  
	}




	if($id!='' && $id>0){

		# UPDATE

		$do_sql_news =  'UPDATE '.$table_news.' SET '.$sql_news.' WHERE news_NewsID="'.$id.'"';

	} else {

		# INSERT

		$sql_name = 'SELECT news_Title FROM news WHERE news_NewsID !='.$id_new.' AND bran_BrandID='.$bran_BrandID;

		$oRes = $oDB->Query($sql_name);

		while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

			$string1 = strtolower($axRow['news_Title']);

			$string2 = strtolower($news_Title);

			if ($string1 == $string2) {

				echo "<script>alert('Title Dupplicate.'); history.back();</script>";

				exit;
			}
		}


		if($time_insert){	$sql_news .= ',news_CreatedDate="'.$time_insert.'"';   }

		if($_SESSION['UID']){	$sql_news .= ',news_CreatedBy="'.$_SESSION['UID'].'"';   }

		if($id_new){	$sql_news .= ',news_NewsID="'.$id_new.'"';   }

		$do_sql_news = 'INSERT INTO '.$table_news.' SET '.$sql_news;
	}

	$oDB->QueryOne($do_sql_news);	

	echo '<script>window.location.href="news.php";</script>';

	exit;
}





#  brand dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' brand_id="'.$_SESSION['user_brand_id'].'" ';
}

$as_brand = dropdownlist_from_table($oDB,'mi_brand','brand_id','name',$where_brand,' ORDER BY name ASC');

$oTmp->assign('brand', $as_brand);




#  card dropdownlist

$as_card = dropdownlist_from_table($oDB,'mi_card','card_id','name',$where_brand,' ORDER BY name ASC');

$oTmp->assign('card', $as_card);




#  news category dropdownlist

$as_news = dropdownlist_from_table($oDB,'news_category','neca_NewsCategoryID','neca_Name',' neca_Deleted!="T"',' ORDER BY neca_Name ASC');

$oTmp->assign('news_category', $as_news);





$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('type_file_upload', '<span class="text-rq">Type file : .jpg , .png , .gif only</span>');

$oTmp->assign('is_menu', 'is_news');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_messages', 'in');

$oTmp->assign('content_file', 'news/news_create.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
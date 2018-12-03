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

if ($_SESSION['role_action']['mobile_caption']['edit'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$time_insert = date("Y-m-d H:i:s");



# SEARCH MAX MOBILE_CAPTION

	$sql_get_last_ins = 'SELECT max(moca_MobileCaptionID) FROM mobile_caption_v2';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_caption = $id_last_ins+1;

#######################################

# SEARCH MAX MOBILE_CAPTION_LANGUAGE

	$sql_get_last_ins = 'SELECT max(mobl_MobileCaptionLanguageID) FROM mobile_caption_language_v2';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_language = $id_last_ins+1;

#######################################


if ($Act == 'edit' && $id != '' ){

	$sql = 'SELECT *
  			FROM mobile_caption_v2
			WHERE moca_MobileCaptionID = "'.$id.'" ';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

	$asData = array();

	$asData = $axRow;


	$sql_th = 'SELECT mobl_Text
	  			FROM mobile_caption_language_v2
				WHERE moca_MobileCaptionID = "'.$id.'" AND lang_LanguageID=1';

	$mobl_TextTH = $oDB->QueryOne($sql_th);

	$oTmp->assign('mobl_TextTH', $mobl_TextTH);

	$sql_en = 'SELECT mobl_Text
	  			FROM mobile_caption_language_v2
				WHERE moca_MobileCaptionID = "'.$id.'" AND lang_LanguageID=2';

	$mobl_TextEN = $oDB->QueryOne($sql_en);

	$oTmp->assign('mobl_TextEN', $mobl_TextEN);


} else if($Act == 'save') {

	$moca_Name = trim_txt($_REQUEST['moca_Name']);

	$moca_Description = trim_txt($_REQUEST['moca_Description']);

	$mobl_TextTH = trim_txt($_REQUEST['mobl_TextTH']);

	$mobl_TextEN = trim_txt($_REQUEST['mobl_TextEN']);



	# MOBILE_CAPTION

	$sql_caption = "";

	$table_caption = "mobile_caption_v2";


	if($moca_Name){	$sql_caption .= 'moca_Name="'.$moca_Name.'"';   }

	if($moca_Description){	$sql_caption .= ',moca_Description="'.$moca_Description.'"';   }

	if($time_insert){	$sql_caption .= ',moca_UpdatedDate="'.$time_insert.'"';   }

	if($_SESSION['UID']){	$sql_caption .= ',moca_UpdatedBy="'.$_SESSION['UID'].'"';   }



	# MOBILE_CAPTION_LANGUAGE_TH

	$sql_th = "";

	$table_th = "mobile_caption_language_v2";

	$sql_th .= 'mobl_Text="'.$mobl_TextTH.'"'; 

	$sql_th .= ',lang_LanguageID="1"';

	$sql_th .= ',mobl_UpdatedDate="'.$time_insert.'"';   

	$sql_th .= ',mobl_UpdatedBy="'.$_SESSION['UID'].'"';   




	# MOBILE_CAPTION_LANGUAGE_EN

	$sql_en = "";

	$table_en = "mobile_caption_language_v2";

	$sql_en .= 'mobl_Text="'.$mobl_TextEN.'"';  

	$sql_en .= ',lang_LanguageID="2"';

	$sql_en .= ',mobl_UpdatedDate="'.$time_insert.'"';   

	$sql_en .= ',mobl_UpdatedBy="'.$_SESSION['UID'].'"';   




	if ($id) {

		# UPDATE

		$do_sql_caption = "UPDATE mobile_caption_v2 SET ".$sql_caption." WHERE moca_MobileCaptionID= '".$id."'";

		$oDB->QueryOne($do_sql_caption);

		$do_sql_th = "UPDATE mobile_caption_language_v2 SET ".$sql_th." WHERE moca_MobileCaptionID= '".$id."' AND lang_LanguageID=1";

		$oDB->QueryOne($do_sql_th);

		$do_sql_en = "UPDATE mobile_caption_language_v2 SET ".$sql_en." WHERE moca_MobileCaptionID= '".$id."' AND lang_LanguageID=2";

		$oDB->QueryOne($do_sql_en);

	} else {

		# INSERT

		$sql_caption .= ',moca_MobileCaptionID="'.$id_caption.'"';   

		$sql_caption .= ',moca_CreatedDate="'.$time_insert.'"';   

		$sql_caption .= ',moca_CreatedBy="'.$_SESSION['UID'].'"';   

		$do_sql_caption = "INSERT INTO mobile_caption_v2 SET ".$sql_caption;

		$oDB->QueryOne($do_sql_caption);


		$sql_th .= ',mobl_MobileCaptionLanguageID="'.$id_language.'"';   

		$sql_th .= ',moca_MobileCaptionID="'.$id_caption.'"';   

		$sql_th .= ',mobl_CreatedDate="'.$time_insert.'"';   

		$sql_th .= ',mobl_CreatedBy="'.$_SESSION['UID'].'"';   

		$do_sql_th = "INSERT INTO mobile_caption_language_v2 SET ".$sql_th;

		$oDB->QueryOne($do_sql_th);


		$id_language++;


		$sql_en .= ',mobl_MobileCaptionLanguageID="'.$id_language.'"';   

		$sql_en .= ',moca_MobileCaptionID="'.$id_caption.'"';   

		$sql_en .= ',mobl_CreatedDate="'.$time_insert.'"';   

		$sql_en .= ',mobl_CreatedBy="'.$_SESSION['UID'].'"';   

		$do_sql_en = "INSERT INTO mobile_caption_language_v2 SET ".$sql_en;

		$oDB->QueryOne($do_sql_en);
	}

	echo '<script type="text/javascript">window.location.href="mobile_caption.php";</script>';

	exit;
}



$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_mobile_caption');

$oTmp->assign('content_file', 'mobile/mobile_caption_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
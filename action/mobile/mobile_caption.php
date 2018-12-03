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

if ($_SESSION['role_action']['mobile_caption']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$sql = 'SELECT 
		mobile_caption_v2.*
  		FROM mobile_caption_v2
		ORDER BY moca_UpdatedDate DESC';

$oRes = $oDB->Query($sql) or die(mysql_error($sql));

$data_table = '';

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$i++;

	$sql_TH = 'SELECT mobl_Text 
					FROM mobile_caption_language_v2 
					WHERE moca_MobileCaptionID="'.$axRow['moca_MobileCaptionID'].'"
					AND lang_LanguageID=1';

	$text_TH = $oDB->QueryOne($sql_TH);

	$sql_EN = 'SELECT mobl_Text 
					FROM mobile_caption_language_v2 
					WHERE moca_MobileCaptionID="'.$axRow['moca_MobileCaptionID'].'"
					AND lang_LanguageID=2';

	$text_EN = $oDB->QueryOne($sql_EN);

	$data_table .= '<tr >
					<td >'.$i.'</td>
					<td >'.$axRow['moca_Name'].'</td>
					<td >'.$text_TH.'</td>
					<td >'.$text_EN.'</td>
					<td >'.$axRow['moca_Description'].'</td>
					<td >'.DateTime($axRow['moca_UpdatedDate']).'</td>';

	if ($_SESSION['role_action']['mobile_caption']['edit'] == 1) {

		$data_table .= '<td ><span style="cursor:pointer" onclick="'."window.location.href='mobile_caption_create.php?act=edit&id=".$axRow['moca_MobileCaptionID']."'".'">
				<button type="button" class="btn btn-default btn-sm">
				<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></span>
			</td>';
	}

	$data_table .= '</tr>';

	$asData[] = $axRow;
}




$oTmp->assign('data_table', $data_table);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_mobile_caption');

$oTmp->assign('content_file', 'mobile/mobile_caption.htm');

$oTmp->display('layout/template.html');


//========================================//


$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
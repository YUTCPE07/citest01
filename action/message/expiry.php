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

if ($_SESSION['role_action']['expiry']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$path_upload_logo = $_SESSION['path_upload_logo'];



$where_brand = '';

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND messages.bran_BrandID = "'.$_SESSION['user_brand_id'].'"';
}


$sql = 'SELECT

		mi_brand.logo_image,
		mi_brand.name,
		mi_brand.path_logo,
		messages.mess_UpdatedDate,
		messages.mess_Notification,
		messages.bran_BrandID,
	  	mi_user_type.name AS user_type

	  	FROM messages

	  	LEFT JOIN mi_brand
	  	ON mi_brand.brand_id = messages.bran_BrandID

		LEFT JOIN mi_user
		ON mi_user.user_id = messages.mess_UpdatedBy 

		LEFT JOIN mi_user_type
		ON mi_user.user_type_id = mi_user_type.user_type_id 

		WHERE messages.mess_Type = "Expiry"
		'.$where_brand.' 

		ORDER BY messages.mess_UpdatedDate DESC';

$oRes = $oDB->Query($sql);

$i=0;

$asData = array();

$data_table = '';

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$i++;


	# LOGO BRAND

	if($axRow['logo_image']!=''){

		$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';

	} else {

		$logo_brand = '<img src="../../images/400x400.png" width="60" height="60"/>';
	}



	# UPDATE

	if ($axRow['mess_UpdatedDate'] == '0000-00-00 00:00:00') {

		$axRow['mess_UpdatedDate'] = '';

	} else {

		$axRow['mess_UpdatedDate'] = DateTime($axRow['mess_UpdatedDate']);
	}



	# DATA TABLE

	$data_table .= '<tr >
						<td >'.$i.'</td>
						<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
							<span style="font-size:11px;">'.$axRow['name'].'</span>
						</td>
						<td >'.nl2br($axRow['mess_Notification']).'</td>
						<td style="text-align:center">'.$axRow['mess_UpdatedDate'].'<hr>
							'.$axRow['user_type'].'</td>';

	if ($_SESSION['role_action']['expiry']['edit'] == 1) {

		$data_table .= '<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href='expiry_create.php?act=edit&id=".$axRow['bran_BrandID']."'".'">
				<button type="button" class="btn btn-default btn-sm">
				<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></span></td>';
	}

	$data_table .= '</tr>';

	$asData[] = $axRow;
}



$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_expiry');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_messages', 'in');

$oTmp->assign('content_file', 'message/expiry.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
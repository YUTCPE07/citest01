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

if ($_SESSION['role_action']['approve_otp']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];



$where_brand = '';

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' WHERE mi_brand.brand_id = "'.$_SESSION['user_brand_id'].'"';
}




$sql = 'SELECT path_logo,
		logo_image,
		name,
		IF(pre_code="","",CONCAT(pre_code,"XXXXXX")) AS pre_code,
		flag_numberic,
		brand_id
	  	FROM mi_brand'
	  	.$where_brand;

$oRes = $oDB->Query($sql);

$data_table = '';

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$i++;


	# LOGO

	if($axRow['logo_image']!=''){

		$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';

	} else {

		$logo_brand = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';
	}


	# NUMBERIC

	if ($axRow['flag_numberic'] == 'T') { $axRow['flag_numberic'] = '<span class="glyphicon glyphicon-ok"></span>'; }
	else { $axRow['flag_numberic'] = '<span class="glyphicon glyphicon-remove"></span>'; }



	# DATA TABLE

	$data_table .= '<tr>
						<td >'.$i.'</td>
						<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
							<span style="font-size:11px;">'.$axRow['name'].'</span>
						</td>
						<td style="text-align:center">'.$axRow['pre_code'].'</td>
						<td style="text-align:center">'.$axRow['flag_numberic'].'</td>
						<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href='gen_code_create.php?act=edit&id=".$axRow['brand_id']."'".'">
				<button type="button" class="btn btn-default btn-sm">
				<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></span></td>
					</tr>';
}



$oTmp->assign('data_table', $data_table);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_setting');

$oTmp->assign('content_file', 'setting/gen_code.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
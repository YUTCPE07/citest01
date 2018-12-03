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

if ($_SESSION['role_action']['brand_sms']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$where_brand = '';

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND mi_brand.brand_id = "'.$_SESSION['user_brand_id'].'"';
}



$sql = 'SELECT mi_brand.*,
	  			t.name AS user_type 

	  	FROM mi_brand

		LEFT JOIN mi_user AS u
		ON u.user_id = mi_brand.sms_update_by 

		LEFT JOIN mi_user_type AS t
		ON u.user_type_id = t.user_type_id  

	  	WHERE mi_brand.flag_del="0" 
	  	'.$where_brand.' 

	  	ORDER BY sms_update_date DESC';

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



	# SMS

	if ($axRow['flag_sms']=='T') {

		$flag_sms = '<span class="glyphicon glyphicon-check"></span>';

	} else {

		$flag_sms = '<span class="glyphicon glyphicon-unchecked"></span>';
	}




	# DATA TABLE

	$data_table .= '<tr >
						<td >'.$i.'</td>
						<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
							<span style="font-size:11px;">'.$axRow['name'].'</span>
						</td>
						<td style="text-align:center">'.$flag_sms.'</td>
						<td style="text-align:center">'.DateTime($axRow['sms_update_date']).'<hr>
							'.$axRow['user_type'].'</td>';

	if ($_SESSION['role_action']['brand_sms']['edit'] == 1) {

		$data_table .=	'<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href='brand_sms_create.php?act=edit&id=".$axRow['brand_id']."'".'">
				<button type="button" class="btn btn-default btn-sm">
				<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></span></td>';
	}
		
	$data_table .=	'</tr>';
}



$oTmp->assign('data_table', $data_table);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_setting');

$oTmp->assign('content_file', 'brand/brand_sms.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
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

if ($_SESSION['role_action']['communication']['view'] != 1) {

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



$sql = 'SELECT
		mi_brand.*,
		MAX(communication.comm_UpdatedDate) AS max_update,
	  	mi_user_type.name AS user_type

	  	FROM mi_brand

		LEFT JOIN communication 
		ON communication.bran_BrandID = mi_brand.brand_id 

		LEFT JOIN mi_user
		ON mi_user.user_id = communication.comm_UpdatedBy 

		LEFT JOIN mi_user_type
		ON mi_user.user_type_id = mi_user_type.user_type_id 

		'.$where_brand.' 

		GROUP BY mi_brand.brand_id
		ORDER BY communication.comm_UpdatedDate DESC';


$oRes = $oDB->Query($sql);

$i=0;

$asData = array();

$data_table = '';

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$i++;

	# LOGO BRAND

	if($axRow['logo_image']!=''){

		$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="70" height="70"/>';

		$logo_view = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="150" height="150"/>';

	} else {

		$logo_brand = '<img src="../../images/400x400.png" width="70" height="70"/>';

		$logo_view = '<img src="../../images/400x400.png" width="150" height="150"/>';
	}




	# USER

	$user_data = '';

	$email_data = '';

	$mobile_data = '';

	$sql_user = 'SELECT mi_user.email,
					mi_contact.mobile,
					mi_contact.firstname,
					mi_contact.lastname

				FROM mi_user 

				LEFT JOIN mi_contact
				ON mi_user.user_id = mi_contact.user_id

				WHERE mi_user.flag_del!="1"
				AND mi_user.role_RoleID="4"
				AND mi_user.brand_id="'.$axRow['brand_id'].'"';

	$oRes_user = $oDB->Query($sql_user);

	while ($user = $oRes_user->FetchRow(DBI_ASSOC)){

		if ($user["mobile"] == '+66') { $user["mobile"] = ''; }

		$user_data .= $user["firstname"].' '.$user["lastname"].'<br>';
		$email_data .= $user["email"].'<br>';
		$mobile_data .= $user["mobile"].'<br>';
	}


	# DATA TABLE

	if ($axRow['max_update'] == '') { $axRow['max_update'] = ''; }
	else { $axRow['max_update'] = DateTime($axRow['max_update']); }


	$data_table .= '<tr >
						<td >'.$i.'</td>
						<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
							<span style="font-size:11px">'.$axRow['name'].'</sapn></td>
						<td >'.$user_data.'</td>
						<td >'.$email_data.'</td>
						<td >'.$mobile_data.'</td>
						<td style="text-align:center">'.$axRow['max_update'].'<hr>'.$axRow['user_type'].'</td>';

	if ($_SESSION['role_action']['communication']['view'] == 1) {

		$data_table .= '<td style="text-align:center"><a href="communication_edit.php?act=edit&id='.$axRow['brand_id'].'">
							<button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></a></td>';
	}

	$data_table .= '</tr>';

	$asData[] = $axRow;
}




$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_communication');

$oTmp->assign('content_file', 'communication/communication.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
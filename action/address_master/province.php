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

$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];


if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT prov_Deleted FROM province WHERE prov_ProvinceID ="'.$id.'"';
	$oRes = $oDB->Query($sql);
	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($axRow['prov_Deleted']=='') {

 		$do_sql_coun = "UPDATE province
 							SET prov_Deleted='T', 
 							prov_UpdatedDate='".$time_insert."' 
 							WHERE prov_ProvinceID='".$id."'";

 	} else if ($axRow['prov_Deleted']=='T') {

		$do_sql_coun = "UPDATE province
 							SET prov_Deleted='', 
 							prov_UpdatedDate='".$time_insert."' 
 							WHERE prov_ProvinceID='".$id."'";
	}

 	$oDB->QueryOne($do_sql_coun);

 	echo '<script>window.location.href="province.php";</script>';
} 


# SQL

$sql = 'SELECT province.*, 
			country.coun_Nicename
		FROM province 
		LEFT JOIN country 
		ON country.coun_CountryID = province.coun_CountryID
		ORDER BY prov_UpdatedDate DESC';

$oRes = $oDB->Query($sql);

$i=0;

$data_table = '';

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$i++;


	# DELETED

	if($axRow['prov_Deleted']=='') {

		$deleted = '<a href="province.php?act=delete&id='.$axRow['prov_ProvinceID'].'">
						<button type="button" class="btn btn-default btn-sm">
						<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button></a>';

	} else if ($axRow['prov_Deleted']=='T') {

		$deleted = '<a href="province.php?act=delete&id='.$axRow['prov_ProvinceID'].'">
						<button type="button" class="btn btn-default btn-sm">
						<span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button></a>';
	}


	# DATA TABLE

	$data_table .= '<tr >
						<td >'.$i.'</td>
						<td >'.$axRow['prov_Name'].'</td>
						<td >'.$axRow['prov_NameEn'].'</td>
						<td >'.$axRow['coun_Nicename'].'</td>
						<td >'.DateTime($axRow['prov_UpdatedDate']).'</td>
						<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href='province_create.php?act=edit&id=".$axRow['prov_ProvinceID']."'".'">
							<button type="button" class="btn btn-default btn-sm">
								<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
							</button></span></td>
						<td style="text-align:center">'.$deleted.'</td>
					</tr>';
}




$oTmp->assign('data_table', $data_table);

$oTmp->assign('content_file', 'address_master/province.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
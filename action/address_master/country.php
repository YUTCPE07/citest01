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

	$sql = 'SELECT coun_Deleted FROM country WHERE coun_CountryID ="'.$id.'"';
	$oRes = $oDB->Query($sql);
	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($axRow['coun_Deleted']=='') {

 		$do_sql_coun = "UPDATE country
 							SET coun_Deleted='T', 
 							coun_UpdatedDate='".$time_insert."' 
 							WHERE coun_CountryID='".$id."'";

 	} else if ($axRow['coun_Deleted']=='T') {

		$do_sql_coun = "UPDATE country
 							SET coun_Deleted='', 
 							coun_UpdatedDate='".$time_insert."' 
 							WHERE coun_CountryID='".$id."'";
	}

 	$oDB->QueryOne($do_sql_coun);

 	echo '<script>window.location.href="country.php";</script>';
} 


# SQL

$sql = 'SELECT * 
		FROM country 
		ORDER BY coun_UpdatedDate DESC';

$oRes = $oDB->Query($sql);

$i=0;

$data_table = '';

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$i++;


	# DELETED

	if($axRow['coun_Deleted']=='') {

		$deleted = '<a href="country.php?act=delete&id='.$axRow['coun_CountryID'].'">
						<button type="button" class="btn btn-default btn-sm">
						<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button></a>';

	} else if ($axRow['coun_Deleted']=='T') {

		$deleted = '<a href="country.php?act=delete&id='.$axRow['coun_CountryID'].'">
						<button type="button" class="btn btn-default btn-sm">
						<span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button></a>';
	}


	# DATA TABLE

	$data_table .= '<tr >
						<td >'.$i.'</td>
						<td >'.$axRow['coun_Nicename'].'</td>
						<td >'.$axRow['coun_NameTH'].'</td>
						<td >'.$axRow['coun_ISO'].'</td>
						<td >'.$axRow['coun_ISO3'].'</td>
						<td >'.$axRow['coun_NumCode'].'</td>
						<td >'.$axRow['coun_PhoneCode'].'</td>
						<td >'.DateTime($axRow['coun_UpdatedDate']).'</td>
						<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href='country_create.php?act=edit&id=".$axRow['coun_CountryID']."'".'">
							<button type="button" class="btn btn-default btn-sm">
								<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
							</button></span></td>
						<td style="text-align:center">'.$deleted.'</td>
					</tr>';
}




$oTmp->assign('data_table', $data_table);

$oTmp->assign('content_file', 'address_master/country.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
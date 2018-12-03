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

	$sql = 'SELECT dist_Deleted FROM district WHERE dist_DistrictID ="'.$id.'"';
	$oRes = $oDB->Query($sql);
	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($axRow['dist_Deleted']=='') {

 		$do_sql_coun = "UPDATE district
 							SET dist_Deleted='T', 
 							dist_UpdatedDate='".$time_insert."' 
 							WHERE dist_DistrictID='".$id."'";

 	} else if ($axRow['dist_Deleted']=='T') {

		$do_sql_coun = "UPDATE district
 							SET dist_Deleted='', 
 							dist_UpdatedDate='".$time_insert."' 
 							WHERE dist_DistrictID='".$id."'";
	}

 	$oDB->QueryOne($do_sql_coun);

 	echo '<script>window.location.href="district.php";</script>';
} 


# SQL

$sql = 'SELECT district.dist_Name,
			district.dist_NameEn,
			district.dist_DistrictID,
			district.dist_UpdatedDate,
			district.dist_Deleted,
			province.prov_Name,
			country.coun_Nicename
		FROM district 
		LEFT JOIN province 
		ON district.dist_ProvinceID = province.prov_ProvinceID
		LEFT JOIN country 
		ON country.coun_CountryID = province.coun_CountryID
		ORDER BY dist_UpdatedDate DESC';

$oRes = $oDB->Query($sql);

$i=0;

$data_table = '';

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$i++;


	# DELETED

	if($axRow['dist_Deleted']=='') {

		$deleted = '<a href="district.php?act=delete&id='.$axRow['dist_DistrictID'].'">
						<button type="button" class="btn btn-default btn-sm">
						<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button></a>';

	} else if ($axRow['dist_Deleted']=='T') {

		$deleted = '<a href="district.php?act=delete&id='.$axRow['dist_DistrictID'].'">
						<button type="button" class="btn btn-default btn-sm">
						<span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button></a>';
	}


	# DATA TABLE

	$data_table .= '<tr >
						<td >'.$i.'</td>
						<td >'.$axRow['dist_Name'].'</td>
						<td >'.$axRow['dist_NameEn'].'</td>
						<td >'.$axRow['prov_Name'].'</td>
						<td >'.$axRow['coun_Nicename'].'</td>
						<td >'.DateTime($axRow['dist_UpdatedDate']).'</td>
						<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href='district_create.php?act=edit&id=".$axRow['dist_DistrictID']."'".'">
							<button type="button" class="btn btn-default btn-sm">
								<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
							</button></span></td>
						<td style="text-align:center">'.$deleted.'</td>
					</tr>';
}




$oTmp->assign('data_table', $data_table);

$oTmp->assign('content_file', 'address_master/district.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
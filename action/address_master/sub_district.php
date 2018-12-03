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

	$sql = 'SELECT sudi_Deleted FROM sub_district WHERE sudi_SubDistrictID ="'.$id.'"';
	$oRes = $oDB->Query($sql);
	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($axRow['sudi_Deleted']=='') {

 		$do_sql_coun = "UPDATE sub_district
 							SET sudi_Deleted='T', 
 							sudi_UpdatedDate='".$time_insert."' 
 							WHERE sudi_SubDistrictID='".$id."'";

 	} else if ($axRow['sudi_Deleted']=='T') {

		$do_sql_coun = "UPDATE sub_district
 							SET sudi_Deleted='', 
 							sudi_UpdatedDate='".$time_insert."' 
 							WHERE sudi_SubDistrictID='".$id."'";
	}

 	$oDB->QueryOne($do_sql_coun);

 	echo '<script>window.location.href="sub_district.php";</script>';
} 


# SQL

$sql = 'SELECT sub_district.sudi_Name,
			sub_district.sudi_NameEn,
			sub_district.sudi_SubDistrictID, 
			sub_district.sudi_Deleted, 
			sub_district.sudi_UpdatedDate, 
			district.dist_Name,
			province.prov_Name,
			country.coun_Nicename
		FROM sub_district 
		LEFT JOIN district 
		ON district.dist_DistrictID = sub_district.sudi_DistrictID
		LEFT JOIN province 
		ON district.dist_ProvinceID = province.prov_ProvinceID
		LEFT JOIN country 
		ON country.coun_CountryID = province.coun_CountryID
		ORDER BY sudi_UpdatedDate DESC';

$oRes = $oDB->Query($sql);

$i=0;

$data_table = '';

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$i++;


	# DELETED

	if($axRow['sudi_Deleted']=='') {

		$deleted = '<a href="sub_district.php?act=delete&id='.$axRow['sudi_SubDistrictID'].'">
						<button type="button" class="btn btn-default btn-sm">
						<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button></a>';

	} else if ($axRow['sudi_Deleted']=='T') {

		$deleted = '<a href="sub_district.php?act=delete&id='.$axRow['sudi_SubDistrictID'].'">
						<button type="button" class="btn btn-default btn-sm">
						<span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button></a>';
	}


	# DATA TABLE

	$data_table .= '<tr >
						<td >'.$i.'</td>
						<td >'.$axRow['sudi_Name'].'</td>
						<td >'.$axRow['sudi_NameEn'].'</td>
						<td >'.$axRow['dist_Name'].'</td>
						<td >'.$axRow['prov_Name'].'</td>
						<td >'.$axRow['coun_Nicename'].'</td>
						<td >'.DateTime($axRow['sudi_UpdatedDate']).'</td>
						<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href='sub_district_create.php?act=edit&id=".$axRow['sudi_SubDistrictID']."'".'">
							<button type="button" class="btn btn-default btn-sm">
								<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
							</button></span></td>
						<td style="text-align:center">'.$deleted.'</td>
					</tr>';
}




$oTmp->assign('data_table', $data_table);

$oTmp->assign('content_file', 'address_master/sub_district.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
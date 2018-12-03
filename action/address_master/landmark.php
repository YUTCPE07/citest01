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


# SQL

$sql = 'SELECT landmark.*,
			coun_NiceName,
			prov_Name,
			dist_Name,
			sudi_Name 
		FROM landmark 
		LEFT JOIN country 
		ON country.coun_CountryID = landmark.coun_CountryID
		LEFT JOIN province 
		ON province.prov_ProvinceID = landmark.prov_ProvinceID
		LEFT JOIN district 
		ON district.dist_DistrictID = landmark.dist_DistrictID
		LEFT JOIN sub_district 
		ON sub_district.sudi_SubDistrictID = landmark.sudi_SubDistrictID
		ORDER BY landmark.land_UpdatedDate DESC';

$oRes = $oDB->Query($sql);

$i=0;

$data_table = '';

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$i++;


	# ADDRESS

	$address_landmark = "";

	if ($axRow['land_AddressNo']!="") { $address_landmark .= $axRow['land_AddressNo']." "; }
	if ($axRow['land_Moo']!="") { $address_landmark .= "หมู่ ".$axRow['land_Moo']." "; }
	if ($axRow['land_Junction']!="") { $address_landmark .= "แยก ".$axRow['land_Junction']." "; }
	if ($axRow['land_Soi']!="") { $address_landmark .= "ซอย ".$axRow['land_Soi']." "; }
	if ($axRow['land_Road']!="") { $address_landmark .= "ถนน ".$axRow['land_Road']." "; }

	if ($axRow['land_Floor']=="0") { $axRow['land_Floor'] = "-"; }



	# DATA TABLE

	$data_table .= '<tr >
						<td >'.$i.'</td>
						<td >'.$axRow['land_Name'].'</td>
						<td >'.$axRow['land_NameEn'].'</td>
						<td >'.$axRow['land_Type'].'</td>
						<td >'.$axRow['land_Floor'].'</td>
						<td >'.$address_landmark.'</td>
						<td >'.$axRow['sudi_Name'].'</td>
						<td >'.$axRow['dist_Name'].'</td>
						<td >'.$axRow['prov_Name'].'</td>
						<td >'.$axRow['coun_NiceName'].'</td>
						<td >'.$axRow['land_Postcode'].'</td>
						<td >'.DateTime($axRow['land_UpdatedDate']).'</td>
						<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href='landmark_create.php?act=edit&id=".$axRow['land_LandmarkID']."'".'">
							<button type="button" class="btn btn-default btn-sm">
								<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
							</button></span></td>
					</tr>';
}




$oTmp->assign('data_table', $data_table);

$oTmp->assign('content_file', 'address_master/landmark.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
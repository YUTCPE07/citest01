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


$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$time_insert = date("Y-m-d H:i:s");



if( $Act == 'edit' && $id != '' ){

	# EDIT

	$sql = 'SELECT * FROM landmark WHERE land_LandmarkID ='.$id;

	$oRes = $oDB->Query($sql);

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		if ($axRow['land_Floor']==0) { $axRow['land_Floor']=""; }
		$asData = $axRow;
	}

} else if( $Act == 'save' ){

	# SAVE

	$land_Name = trim_txt($_REQUEST['land_Name']);

	$land_NameEn = trim_txt($_REQUEST['land_NameEn']);

	$land_AddressNo = trim_txt($_REQUEST['land_AddressNo']);

	$land_Moo = trim_txt($_REQUEST['land_Moo']);

	$land_Junction = trim_txt($_REQUEST['land_Junction']);

	$land_Soi = trim_txt($_REQUEST['land_Soi']);

	$land_Road = trim_txt($_REQUEST['land_Road']);

	$coun_CountryID = trim_txt($_REQUEST['coun_CountryID']);

	$dist_ProvinceID = trim_txt($_REQUEST['dist_ProvinceID']);

	$district = trim_txt($_REQUEST['district']);

	$sub_district = trim_txt($_REQUEST['sub_district']);

	$land_Postcode = trim_txt($_REQUEST['land_Postcode']);

	$land_Latitude = trim_txt($_REQUEST['land_Latitude']);

	$land_Longitude = trim_txt($_REQUEST['land_Longitude']);

	$land_Type = trim_txt($_REQUEST['land_Type']);

	$land_Floor = trim_txt($_REQUEST['land_Floor']);



	$sql_landmark = '';

	$table_landmark = 'landmark';



	if($land_Name){	$sql_landmark .= 'land_Name="'.$land_Name.'"';   }

	if($land_NameEn){	$sql_landmark .= ',land_NameEn="'.$land_NameEn.'"';   }

	if($land_AddressNo){	$sql_landmark .= ',land_AddressNo="'.$land_AddressNo.'"';   }

	if($land_Moo){	$sql_landmark .= ',land_Moo="'.$land_Moo.'"';   }

	if($land_Junction){	$sql_landmark .= ',land_Junction="'.$land_Junction.'"';   }

	if($land_Soi){	$sql_landmark .= ',land_Soi="'.$land_Soi.'"';   }

	if($land_Road){	$sql_landmark .= ',land_Road="'.$land_Road.'"';   }

	if($coun_CountryID){	$sql_landmark .= ',coun_CountryID="'.$coun_CountryID.'"';   }

	if($dist_ProvinceID){	$sql_landmark .= ',prov_ProvinceID="'.$dist_ProvinceID.'"';   }

	if($district){	$sql_landmark .= ',dist_DistrictID="'.$district.'"';   }

	if($sub_district){	$sql_landmark .= ',sudi_SubDistrictID="'.$sub_district.'"';   }

	if($land_Postcode){	$sql_landmark .= ',land_Postcode="'.$land_Postcode.'"';   }

	if($land_Latitude){	$sql_landmark .= ',land_Latitude="'.$land_Latitude.'"';   }

	if($land_Longitude){	$sql_landmark .= ',land_Longitude="'.$land_Longitude.'"';   }

	if($land_Type){	$sql_landmark .= ',land_Type="'.$land_Type.'"';   }

	if($land_Floor){	$sql_landmark .= ',land_Floor="'.$land_Floor.'"';   }

	if($time_insert){	$sql_landmark .= ',land_UpdatedDate="'.$time_insert.'"';   }

	if($_SESSION['UID']){	$sql_landmark .= ',land_UpdatedBy="'.$_SESSION['UID'].'"';   }





	if ($id) {

		# UPDATE

		$do_sql_landmark = 'UPDATE '.$table_landmark.' SET '.$sql_landmark.' WHERE land_LandmarkID="'.$id.'"';

	} else {

		# INSERT 

		if($time_insert){	$sql_landmark .= ',land_CreatedDate="'.$time_insert.'"';   }

		if($_SESSION['UID']){	$sql_landmark .= ',land_CreatedBy="'.$_SESSION['UID'].'"';   }

		$do_sql_landmark = 'INSERT INTO '.$table_landmark.' SET '.$sql_landmark;
	}

	$oDB->QueryOne($do_sql_landmark);	

	echo '<script> window.location.href="landmark.php"; </script>';

	exit;
}



#  country dropdownlist

$as_country = dropdownlist_from_table($oDB,'country','coun_CountryID','coun_Nicename','coun_Deleted!="T"',' ORDER BY coun_Nicename ASC');

$oTmp->assign('country_opt', $as_country);

if ($asData['coun_CountryID']==0) { 

	$asData['coun_CountryID'] = 211; 
	$asData['prov_ProvinceID'] = 1; 
}



$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('content_file', 'address_master/landmark_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
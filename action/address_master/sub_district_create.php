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

	$sql = 'SELECT sub_district.*,
					sub_district.sudi_DistrictID,
					province.prov_ProvinceID,
					province.coun_CountryID
			FROM sub_district 
			LEFT JOIN district 
			ON district.dist_DistrictID = sub_district.sudi_DistrictID
			LEFT JOIN province 
			ON province.prov_ProvinceID = district.dist_ProvinceID
			WHERE sudi_SubDistrictID ='.$id;

	$oRes = $oDB->Query($sql);

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$asData = $axRow;
	}

} else if( $Act == 'save' ){

	# SAVE

	$sudi_NameEn = trim_txt($_REQUEST['sudi_NameEn']);

	$sudi_Name = trim_txt($_REQUEST['sudi_Name']);

	$district = trim_txt($_REQUEST['district']);



	$sql_sub_district = '';

	$table_sub_district = 'sub_district';



	if($district){	$sql_sub_district .= 'sudi_DistrictID="'.$district.'"';   }

	if($sudi_Name){	$sql_sub_district .= ',sudi_Name="'.$sudi_Name.'"';   }

	if($sudi_NameEn){	$sql_sub_district .= ',sudi_NameEn="'.$sudi_NameEn.'"';   }

	if($time_insert){	$sql_sub_district .= ',sudi_UpdatedDate="'.$time_insert.'"';   }

	if($_SESSION['UID']){	$sql_sub_district .= ',sudi_UpdatedBy="'.$_SESSION['UID'].'"';   }





	if ($id) {

		# UPDATE

		$do_sql_sub_district = 'UPDATE '.$table_sub_district.' SET '.$sql_sub_district.' WHERE sudi_SubDistrictID="'.$id.'"';

	} else {

		# INSERT 

		if($time_insert){	$sql_sub_district .= ',sudi_CreatedDate="'.$time_insert.'"';   }

		if($_SESSION['UID']){	$sql_sub_district .= ',sudi_CreatedBy="'.$_SESSION['UID'].'"';   }

		$do_sql_sub_district = 'INSERT INTO '.$table_sub_district.' SET '.$sql_sub_district;
	}

	$oDB->QueryOne($do_sql_sub_district);
	
	echo '<script> window.location.href="sub_district.php"; </script>';

	exit;
}



#  country dropdownlist

$as_country = dropdownlist_from_table($oDB,'country','coun_CountryID','coun_Nicename','',' ORDER BY coun_Nicename ASC');

$oTmp->assign('country_opt', $as_country);

if ($asData['coun_CountryID']==0) { 

	$asData['coun_CountryID'] = 211; 
	$asData['prov_ProvinceID'] = 1; 
}



$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('content_file', 'address_master/sub_district_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
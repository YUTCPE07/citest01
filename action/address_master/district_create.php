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

	$sql = 'SELECT district.*,
					province.coun_CountryID
			FROM district 
			LEFT JOIN province 
			ON province.prov_ProvinceID = district.dist_ProvinceID
			WHERE dist_DistrictID ='.$id;

	$oRes = $oDB->Query($sql);

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$asData = $axRow;
	}

} else if( $Act == 'save' ){

	# SAVE

	$dist_NameEn = trim_txt($_REQUEST['dist_NameEn']);

	$dist_Name = trim_txt($_REQUEST['dist_Name']);

	$dist_ProvinceID = trim_txt($_REQUEST['province_id']);



	$sql_district = '';

	$table_district = 'district';



	if($dist_ProvinceID){	$sql_district .= 'dist_ProvinceID="'.$dist_ProvinceID.'"';   }

	if($dist_Name){	$sql_district .= ',dist_Name="'.$dist_Name.'"';   }

	if($dist_NameEn){	$sql_district .= ',dist_NameEn="'.$dist_NameEn.'"';   }

	if($time_insert){	$sql_district .= ',dist_UpdatedDate="'.$time_insert.'"';   }

	if($_SESSION['UID']){	$sql_district .= ',dist_UpdatedBy="'.$_SESSION['UID'].'"';   }





	if ($id) {

		# UPDATE

		$do_sql_district = 'UPDATE '.$table_district.' SET '.$sql_district.' WHERE dist_DistrictID="'.$id.'"';

	} else {

		# INSERT 

		if($time_insert){	$sql_district .= ',dist_CreatedDate="'.$time_insert.'"';   }

		if($_SESSION['UID']){	$sql_district .= ',dist_CreatedBy="'.$_SESSION['UID'].'"';   }

		$do_sql_district = 'INSERT INTO '.$table_district.' SET '.$sql_district;
	}

	$oDB->QueryOne($do_sql_district);

	echo '<script> window.location.href="district.php"; </script>';

	exit;
}



#  country dropdownlist

$as_country = dropdownlist_from_table($oDB,'country','coun_CountryID','coun_Nicename','',' ORDER BY coun_Nicename ASC');

$oTmp->assign('country_opt', $as_country);

if ($asData['coun_CountryID']==0) { 

	$asData['coun_CountryID'] = 211; 
	$asData['dist_ProvinceID'] = 1; 
}



$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('content_file', 'address_master/district_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
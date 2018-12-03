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

	$sql = 'SELECT * FROM province WHERE prov_ProvinceID ='.$id;

	$oRes = $oDB->Query($sql);

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$asData = $axRow;
	}

} else if( $Act == 'save' ){

	# SAVE

	$prov_NameEn = trim_txt($_REQUEST['prov_NameEn']);

	$prov_Name = trim_txt($_REQUEST['prov_Name']);

	$coun_CountryID = trim_txt($_REQUEST['coun_CountryID']);

	$prov_RegionID = trim_txt($_REQUEST['prov_RegionID']);



	$sql_province = '';

	$table_province = 'province';



	if($coun_CountryID){	$sql_province .= 'coun_CountryID="'.$coun_CountryID.'"';   }

	if($prov_Name){	$sql_province .= ',prov_Name="'.$prov_Name.'"';   }

	if($prov_NameEn){	$sql_province .= ',prov_NameEn="'.$prov_NameEn.'"';   }

	if($prov_RegionID){	$sql_province .= ',prov_RegionID="'.$prov_RegionID.'"';   }

	$sql_province .= ',mafi_MasterFieldID="33"';

	if($time_insert){	$sql_province .= ',prov_UpdatedDate="'.$time_insert.'"';   }

	if($_SESSION['UID']){	$sql_province .= ',prov_UpdatedBy="'.$_SESSION['UID'].'"';   }





	if ($id) {

		# UPDATE

		$do_sql_province = 'UPDATE '.$table_province.' SET '.$sql_province.' WHERE prov_ProvinceID="'.$id.'"';

	} else {

		# INSERT 

		if($time_insert){	$sql_province .= ',prov_CreatedDate="'.$time_insert.'"';   }

		if($_SESSION['UID']){	$sql_province .= ',prov_CreatedBy="'.$_SESSION['UID'].'"';   }

		$do_sql_province = 'INSERT INTO '.$table_province.' SET '.$sql_province;
	}

	$oDB->QueryOne($do_sql_province);	

	echo '<script> window.location.href="province.php"; </script>';

	exit;
}



#  country dropdownlist

$as_country = dropdownlist_from_table($oDB,'country','coun_CountryID','coun_Nicename','',' ORDER BY coun_Nicename ASC');

$oTmp->assign('country_opt', $as_country);

if ($axRow['coun_CountryID']==0) { $asData['coun_CountryID'] = 211; }



#  region dropdownlist

$as_region = dropdownlist_from_table($oDB,'region','regi_RegionID','regi_Name','',' ORDER BY regi_Name ASC');

$oTmp->assign('region_opt', $as_region);



$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('content_file', 'address_master/province_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
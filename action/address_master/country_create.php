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

	$sql = 'SELECT * FROM country WHERE coun_CountryID ='.$id;

	$oRes = $oDB->Query($sql);

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$axRow['iso3_1'] = substr($axRow['coun_ISO3'], 0, 1);
		$axRow['iso3_2'] = substr($axRow['coun_ISO3'], 1, 1);
		$axRow['iso3_3'] = substr($axRow['coun_ISO3'], 2, 1);

		$axRow['iso_1'] = substr($axRow['coun_ISO'], 0, 1);
		$axRow['iso_2'] = substr($axRow['coun_ISO'], 1, 1);

		$asData = $axRow;
	}

} else if( $Act == 'save' ){

	# SAVE

	$coun_NameTH = trim_txt($_REQUEST['coun_NameTH']);

	$coun_Nicename = trim_txt($_REQUEST['coun_Nicename']);

	$coun_NumCode = trim_txt($_REQUEST['coun_NumCode']);

	$coun_PhoneCode = trim_txt($_REQUEST['coun_PhoneCode']);

	$iso3_1 = trim_txt($_REQUEST['iso3_1']);
	$iso3_2 = trim_txt($_REQUEST['iso3_2']);
	$iso3_3 = trim_txt($_REQUEST['iso3_3']);

	$iso_1 = trim_txt($_REQUEST['iso_1']);
	$iso_2 = trim_txt($_REQUEST['iso_2']);



	$sql_country = '';

	$table_country = 'country';



	if($coun_Nicename){	$sql_country .= 'coun_Nicename="'.$coun_Nicename.'"';   }

	if($coun_Nicename){	$sql_country .= ',coun_Name="'.strtoupper($coun_Nicename).'"';   }

	if($coun_NameTH){	$sql_country .= ',coun_NameTH="'.$coun_NameTH.'"';   }

	if($iso_1 && $iso_2){	$sql_country .= ',coun_ISO="'.$iso_1.$iso_2.'"';   }

	if($iso3_1 && $iso3_2 && $iso3_3){	$sql_country .= ',coun_ISO3="'.$iso3_1.$iso3_2.$iso3_3.'"';   }

	if($coun_NumCode){	$sql_country .= ',coun_NumCode="'.$coun_NumCode.'"';   }

	if($coun_PhoneCode){	$sql_country .= ',coun_PhoneCode="'.$coun_PhoneCode.'"';   }

	if($time_insert){	$sql_country .= ',coun_UpdatedDate="'.$time_insert.'"';   }

	if($_SESSION['UID']){	$sql_country .= ',coun_UpdatedBy="'.$_SESSION['UID'].'"';   }





	if ($id) {

		# UPDATE

		$do_sql_country = 'UPDATE '.$table_country.' SET '.$sql_country.' WHERE coun_CountryID="'.$id.'"';

	} else {

		# INSERT 

		if($time_insert){	$sql_country .= ',coun_CreatedDate="'.$time_insert.'"';   }

		if($_SESSION['UID']){	$sql_country .= ',coun_CreatedBy="'.$_SESSION['UID'].'"';   }

		$do_sql_country = 'INSERT INTO '.$table_country.' SET '.$sql_country;
	}

	$oDB->QueryOne($do_sql_country);	

	echo '<script> window.location.href="country.php"; </script>';

	exit;
}




$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('content_file', 'address_master/country_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
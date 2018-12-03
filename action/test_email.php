<?php

header('Content-Type:text/html; charset=UTF-8');

//========================================//

ini_set("display_errors",1);
error_reporting(1);

//========================================//

include('../include/common_login.php');
include('../lib/pagination_class.php');
include('../lib/function_normal.php');
include('../include/common_check.php');

//========================================//

$oTmp = new TemplateEngine();
$oDB = new DBI();

if ($bDebug) {

  $oErr = new Tracker();
  $oDB->SetTracker($oErr);
}

$sql_branch = 'SELECT branch_id,
                      district,
                      sub_district
                FROM mi_branch';

$oRes = $oDB->Query($sql_branch);
while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

  $sql_district = 'SELECT dist_DistrictID
                    FROM district 
                    WHERE dist_Name="'.$axRow['district'].'"';
  $district_id = $oDB->QueryOne($sql_district);

  $sql_sub_district = 'SELECT sudi_SubDistrictID
                      FROM sub_district 
                      WHERE sudi_Name="'.$axRow['sub_district'].'"';
  $sub_district_id = $oDB->QueryOne($sql_sub_district);

  $update = 'UPDATE mi_branch 
              SET district_id="'.$district_id.'", 
              sub_district_id="'.$sub_district_id.'"
              WHERE branch_id="'.$axRow['branch_id'].'"';
  $oDB->QueryOne($update);

  echo $update.'<br><br>';
}


//========================================//

$oDB->Close();

if ($bDebug) {

  echo($oErr->GetAll());
}

//========================================//
    
?>
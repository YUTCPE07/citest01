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

if (($_SESSION['role_action']['point']['add'] != 1) || ($_SESSION['role_action']['point']['edit'] != 1)) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//



$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$time_insert = date("Y-m-d H:i:s");



# SEARCH MAX MOTIVATION_POINT ID

	$sql_get_last_ins = 'SELECT max(mopp_MotivationPointID) FROM motivation_plan_point';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_point_new = $id_last_ins+1;

#######################################


if( $Act == 'edit' && $id ){

	$sql = '';

	$sql .= 'SELECT a.*,
					b.name AS brand_name
				FROM motivation_plan_point a
				LEFT JOIN mi_brand b
				ON a.bran_BrandID = b.brand_id
				WHERE a.mopp_MotivationPointID = "'.$id.'"';

	$oRes = $oDB->Query($sql);

	$i=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$asData = $axRow;
	}

	$sql_status .= 'SELECT mopp_Status FROM motivation_plan_point WHERE mopp_MotivationPointID = "'.$id.'"';

	$status = $oDB->QueryOne($sql_status);

	if ($status == 'T') {

		echo "<script> history.back(); </script>";
		exit();
	}

} else if( $Act == 'save' ){

	$id = trim_txt($_REQUEST['id']);

	$mopp_Objective = trim_txt($_REQUEST['mopp_Objective']);

	$mopp_Description = trim_txt($_REQUEST['mopp_Description']);

	$mopp_Status = trim_txt($_REQUEST['mopp_Status']);

	$mopp_EndDate = trim_txt($_REQUEST['EndDate']);

	$mopp_PointQty = trim_txt($_REQUEST['mopp_PointQty']);

	$mopp_CollectionMethod = trim_txt($_REQUEST['mopp_CollectionMethod']);

	$mopp_PeriodTime = trim_txt($_REQUEST['mopp_PeriodTime']);

	$mopp_PeriodType = trim_txt($_REQUEST['mopp_PeriodType']);

	$mopp_PeriodTypeEnd = trim_txt($_REQUEST['mopp_PeriodTypeEnd']);

	$mopp_RequestReceiptNo = trim_txt($_REQUEST['mopp_RequestReceiptNo']);

	$mopp_RequestReceiptAmount = trim_txt($_REQUEST['mopp_RequestReceiptAmount']);

	$mopp_MaxPointPerDay = trim_txt($_REQUEST['mopp_MaxPointPerDay']);

	$mopp_Method = trim_txt($_REQUEST['mopp_Method']);

	$mopp_DateStatus = trim_txt($_REQUEST['AutoDate']);

	$bran_BrandID = trim_txt($_REQUEST['bran_BrandID']);

	$mopp_Name = trim_txt($_REQUEST['mopp_Name']);

	$mopp_UseAmount = trim_txt($_REQUEST['mopp_UseAmount']);




	$sql_point = '';

	$table_point = 'motivation_plan_point';




	# ACTION POINT HEADER

	if($mopp_Name){	$sql_point .= 'mopp_Name="'.$mopp_Name.'"';   } 

	$sql_point .= ',mopp_Objective="'.$mopp_Objective.'"';   

	$sql_point .= ',mopp_Description="'.$mopp_Description.'"';   

	if($mopp_Method){	$sql_point .= ',mopp_Method="'.$mopp_Method.'"';   }

	if($mopp_PointQty){	$sql_point .= ',mopp_PointQty="'.$mopp_PointQty.'"';   }

	if($mopp_UseAmount){	$sql_point .= ',mopp_UseAmount="'.$mopp_UseAmount.'"';   }

	$sql_point .= ',mopp_RequestReceiptNo="'.$mopp_RequestReceiptNo.'"';

	$sql_point .= ',mopp_RequestReceiptAmount="'.$mopp_RequestReceiptAmount.'"';

	$sql_point .= ',mopp_MaxPointPerDay="'.$mopp_MaxPointPerDay.'"';   

	if($bran_BrandID){	$sql_point .= ',bran_BrandID="'.$bran_BrandID.'"';   }

	if($_SESSION['UID']){	$sql_point .= ',mopp_UpdatedBy="'.$_SESSION['UID'].'"';   }

	if($time_insert){	$sql_point .= ',mopp_UpdatedDate="'.$time_insert.'"';   }

	if($mopp_CollectionMethod){	$sql_point .= ',mopp_CollectionMethod="'.$mopp_CollectionMethod.'"';   }

	$sql_point .= ',mopp_DateStatus="'.$mopp_DateStatus.'"';

	$sql_point .= ',mopp_Status="'.$mopp_Status.'"';

	if($mopp_CollectionMethod == "Fix") {

		if($mopp_EndDate){	$sql_point .= ',mopp_EndDate="'.$mopp_EndDate.'"';   }

		$sql_point .= ',mopp_PeriodTime=""';

		$sql_point .= ',mopp_PeriodType=""'; 

		$sql_point .= ',mopp_PeriodTypeEnd=""';

	} else if ($mopp_CollectionMethod == "No") {

		$sql_point .= ',mopp_EndDate=""';

		$sql_point .= ',mopp_PeriodTime=""';

		$sql_point .= ',mopp_PeriodType=""'; 

		$sql_point .= ',mopp_PeriodTypeEnd=""';  

	} else if ($mopp_CollectionMethod == "Exp") {

		$sql_point .= ',mopp_EndDate=""';

		$sql_point .= ',mopp_PeriodTime="'.$mopp_PeriodTime.'"';

		$sql_point .= ',mopp_PeriodType="'.$mopp_PeriodType.'"'; 

		if ($mopp_PeriodType == "M") { $sql_point .= ',mopp_PeriodTypeEnd="M"'; } 
		else { $sql_point .= ',mopp_PeriodTypeEnd="'.$mopp_PeriodTypeEnd.'"';  } 
	}



	# CHECK PLAN NAME

	if ($id) {

		$sql_name = 'SELECT mopp_Name FROM motivation_plan_point WHERE bran_BrandID='.$bran_BrandID.' AND mopp_MotivationPointID!='.$id;

	} else {

		$sql_name = 'SELECT mopp_Name FROM motivation_plan_point WHERE bran_BrandID='.$bran_BrandID;
	}

	$oRes = $oDB->Query($sql_name);

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$string1 = strtolower($axRow['mopp_Name']);

		$string2 = strtolower($mopp_Name);

		if ($string1 == $string2) {

			echo "<script>alert('Plan Name Dupplicate.');
				history.back();</script>";

			exit;
		}
	}




	if ($id){

		# UPDATE

		$do_sql_point = "UPDATE ".$table_point." SET ".$sql_point." WHERE mopp_MotivationPointID= '".$id."'";

		$oDB->QueryOne($do_sql_point);

	} else {

		# INSERT HEADER

		if($_SESSION['UID']){	$sql_point .= ',mopp_CreatedBy="'.$_SESSION['UID'].'"';   }

		if($time_insert){	$sql_point .= ',mopp_CreatedDate="'.$time_insert.'"';	}

		if($id_point_new){	$sql_point .= ',mopp_MotivationPointID="'.$id_point_new.'"';   }

		$do_sql_point = 'INSERT INTO '.$table_point.' SET '.$sql_point;

		$oDB->QueryOne($do_sql_point);
	}

	echo '<script>window.location.href = "point.php";</script>';

	exit;
}





#  brand dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' brand_id="'.$_SESSION['user_brand_id'].'" ';
}

$as_brand = dropdownlist_from_table($oDB,'mi_brand','brand_id','name',$where_brand,' ORDER BY name ASC');

$oTmp->assign('brand', $as_brand);




$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_point');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_motivation', 'in');

$oTmp->assign('inner_motivation', 'in');

$oTmp->assign('content_file', 'motivation/point_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
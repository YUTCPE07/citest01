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

if (($_SESSION['role_action']['stamp']['add'] != 1) || ($_SESSION['role_action']['stamp']['edit'] != 1)) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$time_insert = date("Y-m-d H:i:s");


# SEARCH MAX MOTIVATION_STAMP ID

	$sql_get_last_ins = 'SELECT max(mops_MotivationStampID) FROM motivation_plan_stamp';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$stamp_id_new = $id_last_ins+1;

#######################################



if( $Act == 'edit' && $id ){

	$sql = '';

	$sql .= 'SELECT motivation_stamp_header.* FROM motivation_plan_stamp WHERE mops_MotivationStampID = "'.$id.'"';

	$oRes = $oDB->Query($sql);

	$i=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$asData = $axRow;
	}

	$sql_status = 'SELECT mops_Status FROM motivation_plan_stamp WHERE mops_MotivationStampID = "'.$id.'"';

	$status = $oDB->QueryOne($sql_status);

	if ($status == 'T') {

		echo "<script> history.back(); </script>";

		exit();
	}


} else if( $Act == 'save' ){


	$id = trim_txt($_REQUEST['id']);

	$mops_Objective = trim_txt($_REQUEST['mops_Objective']);

	$mops_Description = trim_txt($_REQUEST['mops_Description']);

	$mops_Status = trim_txt($_REQUEST['mops_Status']);

	$mops_Name = trim_txt($_REQUEST['mops_Name']);

	$mops_EndDate = trim_txt($_REQUEST['EndDate']);

	$mops_CollectionMethod = trim_txt($_REQUEST['mops_CollectionMethod']);

	$mops_PeriodTime = trim_txt($_REQUEST['mops_PeriodTime']);

	$mops_PeriodType = trim_txt($_REQUEST['mops_PeriodType']);

	$mops_PeriodTypeEnd = trim_txt($_REQUEST['mops_PeriodTypeEnd']);

	$mops_MaxStampPerDay = trim_txt($_REQUEST['mops_MaxStampPerDay']);

	$mops_CollectionTypeID = trim_txt($_REQUEST['mops_CollectionTypeID']);

	$mops_StampQty = trim_txt($_REQUEST['mops_StampQty']);

	$bran_BrandID = trim_txt($_REQUEST['bran_BrandID']);

	$mops_DateStatus = trim_txt($_REQUEST['AutoDate']);



	$sql_stamp = '';

	$table_stamp = 'motivation_plan_stamp';



	# ACTION POINT HEADER

	if($mops_Name){	$sql_stamp .= 'mops_Name="'.$mops_Name.'"';   }

	$sql_stamp .= ',mops_Objective="'.$mops_Objective.'"';  

	$sql_stamp .= ',mops_Description="'.$mops_Description.'"';  

	if($mops_EndDate){	$sql_stamp .= ',mops_EndDate="'.$mops_EndDate.'"';   }

	if($mops_StampQty){	$sql_stamp .= ',mops_StampQty="'.$mops_StampQty.'"';   }

	$sql_stamp .= ',mops_MaxStampPerDay="'.$mops_MaxStampPerDay.'"';  

	if($bran_BrandID){	$sql_stamp .= ',bran_BrandID="'.$bran_BrandID.'"';   }

	if($mops_CollectionTypeID){	$sql_stamp .= ',mops_CollectionTypeID="'.$mops_CollectionTypeID.'"';   }

	if($_SESSION['UID']){	$sql_stamp .= ',mops_UpdatedBy="'.$_SESSION['UID'].'"';   }

	if($time_insert){	$sql_stamp .= ',mops_UpdatedDate="'.$time_insert.'"';   }

	if($mops_CollectionMethod){	$sql_stamp .= ',mops_CollectionMethod="'.$mops_CollectionMethod.'"';   }

	$sql_stamp .= ',mops_DateStatus="'.$mops_DateStatus.'"';

	$sql_stamp .= ',mops_Status="'.$mops_Status.'"';

	if($mops_CollectionMethod == "Fix") {

		if($mops_EndDate){	$sql_stamp .= ',mops_EndDate="'.$mops_EndDate.'"';   }

		$sql_stamp .= ',mops_PeriodTime=""';

		$sql_stamp .= ',mops_PeriodType=""'; 

		$sql_stamp .= ',mops_PeriodTypeEnd=""';

	} else if ($mops_CollectionMethod == "No") {

		$sql_stamp .= ',mops_EndDate=""';

		$sql_stamp .= ',mops_PeriodTime=""';

		$sql_stamp .= ',mops_PeriodType=""'; 

		$sql_stamp .= ',mops_PeriodTypeEnd=""';  

	} else if ($mops_CollectionMethod == "Exp") {

		$sql_stamp .= ',mops_EndDate=""';

		$sql_stamp .= ',mops_PeriodTime="'.$mops_PeriodTime.'"';

		$sql_stamp .= ',mops_PeriodType="'.$mops_PeriodType.'"'; 

		if ($mops_PeriodType == "M") { $sql_stamp .= ',mops_PeriodTypeEnd="M"'; } 
		else { $sql_stamp .= ',mops_PeriodTypeEnd="'.$mops_PeriodTypeEnd.'"';  } 
	}



	# CHECK PLAN NAME

	if ($id) {

		$sql_name = 'SELECT mops_Name FROM motivation_plan_stamp WHERE bran_BrandID='.$bran_BrandID.' AND mops_MotivationStampID!='.$id;

	} else {

		$sql_name = 'SELECT mops_Name FROM motivation_plan_stamp WHERE bran_BrandID='.$bran_BrandID;
	}

	$oRes = $oDB->Query($sql_name);

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$string1 = strtolower($axRow['mops_Name']);

		$string2 = strtolower($mops_Name);

		if ($string1 == $string2) {

			echo "<script>alert('Plan Name Dupplicate.');
				history.back();</script>";

			exit;
		}
	}



	if ($id){

		#	UPDATE

		$do_sql_stamp = "UPDATE ".$table_stamp." SET ".$sql_stamp." WHERE mops_MotivationStampID= '".$id."'";

		$oDB->QueryOne($do_sql_stamp);

	} else {

		# INSERT HEADER

		if($_SESSION['UID']){	$sql_stamp .= ',mops_CreatedBy="'.$_SESSION['UID'].'"';   }

		if($time_insert){	$sql_stamp .= ',mops_CreatedDate="'.$time_insert.'"';	}

		if($stamp_id_new){	$sql_stamp .= ',mops_MotivationStampID="'.$stamp_id_new.'"';   }

		$do_sql_stamp = 'INSERT INTO '.$table_stamp.' SET '.$sql_stamp;

		$oDB->QueryOne($do_sql_stamp);
	}

	echo '<script>window.location.href = "stamp.php";</script>';

	exit;
}




#  brand dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' brand_id="'.$_SESSION['user_brand_id'].'" ';
}

$as_brand = dropdownlist_from_table($oDB,'mi_brand','brand_id','name',$where_brand,' ORDER BY name ASC');

$oTmp->assign('brand', $as_brand);



#  icon dropdownlist

$as_icon = dropdownlist_from_table($oDB,'collection_type','coty_CollectionTypeID','coty_Name',' coty_Type="Brand"');

$oTmp->assign('collection_type', $as_icon);





$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_stamp');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_motivation', 'in');

$oTmp->assign('inner_motivation', 'in');

$oTmp->assign('content_file', 'motivation/stamp_create.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
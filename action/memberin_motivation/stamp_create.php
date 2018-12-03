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

if (($_SESSION['role_action']['memberin_stamp']['add'] != 1) || ($_SESSION['role_action']['memberin_stamp']['edit'] != 1)) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$time_insert = date("Y-m-d H:i:s");


# SEARCH MAX MEMBERIN_POINT ID

	$sql_get_last_ins = 'SELECT max(mist_MemberinStampID) FROM memberin_stamp';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################


if( $Act == 'edit' && $id ){

	$sql = '';

	$sql .= 'SELECT
				memberin_stamp.*

				FROM memberin_stamp 

				WHERE mist_MemberinStampID = "'.$id.'"';


	$oRes = $oDB->Query($sql);

	$i=0;

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$asData = $axRow;
	}

} else if( $Act == 'save' ){

	$id = trim_txt($_REQUEST['id']);

	$mist_TimeQty = trim_txt($_REQUEST['mist_TimeQty']);

	$mist_Description = trim_txt($_REQUEST['mist_Description']);

	$mist_Status = trim_txt($_REQUEST['mist_Status']);

	$mist_StartDate = trim_txt($_REQUEST['StartDate']);

	$mist_EndDate = trim_txt($_REQUEST['EndDate']);

	$mist_CollectionMethod = trim_txt($_REQUEST['mist_CollectionMethod']);

	$mist_PeriodTime = trim_txt($_REQUEST['mist_PeriodTime']);

	$mist_PeriodType = trim_txt($_REQUEST['mist_PeriodType']);

	$mist_PeriodTypeEnd = trim_txt($_REQUEST['mist_PeriodTypeEnd']);

	$mist_MaxStampPerDay = trim_txt($_REQUEST['mist_MaxStampPerDay']);

	$mist_CollectionTypeID = trim_txt($_REQUEST['mist_CollectionTypeID']);

	$mist_StampQty = trim_txt($_REQUEST['mist_StampQty']);



	$sql_stamp = '';

	$table_stamp = 'memberin_stamp';




	# ACTION POINT HEADER

	$sql_stamp .= 'mist_Description="'.$mist_Description.'"';  

	if($mist_Status){	$sql_stamp .= ',mist_Status="'.$mist_Status.'"';   }

	if($mist_TimeQty){	$sql_stamp .= ',mist_TimeQty="'.$mist_TimeQty.'"';   }

	if($mist_StartDate){	$sql_stamp .= ',mist_StartDate="'.$mist_StartDate.'"';   }

	if($mist_StampQty){	$sql_stamp .= ',mist_StampQty="'.$mist_StampQty.'"';   }

	if($mist_MaxStampPerDay){	$sql_stamp .= ',mist_MaxStampPerDay="'.$mist_MaxStampPerDay.'"';   }

	if($mist_CollectionTypeID){	$sql_stamp .= ',mist_CollectionTypeID="'.$mist_CollectionTypeID.'"';   }

	if($_SESSION['UID']){	$sql_stamp .= ',mist_UpdatedBy="'.$_SESSION['UID'].'"';   }

	if($time_insert){	$sql_stamp .= ',mist_UpdatedDate="'.$time_insert.'"';   }

	if($mist_CollectionMethod){	$sql_stamp .= ',mist_CollectionMethod="'.$mist_CollectionMethod.'"';   }

	if($mist_CollectionMethod == "Fix") {

		if($mist_StartDate){	$sql_stamp .= ',mist_StartDate="'.$mist_StartDate.'"';   }

		if($mist_EndDate){	$sql_stamp .= ',mist_EndDate="'.$mist_EndDate.'"';   }

		$sql_stamp .= ',mist_PeriodTime=""';

		$sql_stamp .= ',mist_PeriodType=""'; 

		$sql_stamp .= ',mist_PeriodTypeEnd=""';

	} else if ($mist_CollectionMethod == "No") {

		$sql_stamp .= ',mist_StartDate=""';   

		$sql_stamp .= ',mist_EndDate=""';

		$sql_stamp .= ',mist_PeriodTime=""';

		$sql_stamp .= ',mist_PeriodType=""'; 

		$sql_stamp .= ',mist_PeriodTypeEnd=""';  

	} else if ($mist_CollectionMethod == "Exp") {

		$sql_stamp .= ',mist_StartDate=""';   

		$sql_stamp .= ',mist_EndDate=""';

		$sql_stamp .= ',mist_PeriodTime="'.$mist_PeriodTime.'"';

		$sql_stamp .= ',mist_PeriodType="'.$mist_PeriodType.'"'; 


		if ($mist_PeriodType == "M") { $sql_stamp .= ',mist_PeriodTypeEnd="M"'; } 
		else { $sql_stamp .= ',mist_PeriodTypeEnd="'.$mist_PeriodTypeEnd.'"';  } 
	}


	if ($id){

		#	UPDATE

		$do_table_stamp = "UPDATE ".$table_stamp." SET ".$sql_stamp." WHERE mist_MemberinStampID= '".$id."'";

		$oDB->QueryOne($do_table_stamp);

	} else {

		# INSERT HEADER

		if($_SESSION['UID']){	$sql_stamp .= ',mist_CreatedBy="'.$_SESSION['UID'].'"';   }

		if($time_insert){	$sql_stamp .= ',mist_CreatedDate="'.$time_insert.'"';	}

		if($id_new){	$sql_stamp .= ',mist_MemberinStampID="'.$id_new.'"';   }


		$do_table_stamp = 'INSERT INTO '.$table_stamp.' SET '.$sql_stamp;

		$oDB->QueryOne($do_table_stamp);
	}

	echo '<script>window.location.href = "stamp.php";</script>';

	exit;
}




#  icon dropdownlist

$as_icon = dropdownlist_from_table($oDB,'collection_type','coty_CollectionTypeID','coty_Name','coty_Type="MemberIn"');

$oTmp->assign('collection_type', $as_icon);



#  memberin_action dropdownlist

$as_action = dropdownlist_from_table($oDB,'memberin_action','miac_MemberinActionID','miac_Name','miac_Type="Stamp"',' ORDER BY miac_Name ASC');

$oTmp->assign('memberin_action', $as_action);





$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_memberin_stamp');

$oTmp->assign('content_file', 'memberin_motivation/stamp_create.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
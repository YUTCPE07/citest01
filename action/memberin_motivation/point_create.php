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

if (($_SESSION['role_action']['memberin_point']['add'] != 1) || ($_SESSION['role_action']['memberin_point']['edit'] != 1)) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$time_insert = date("Y-m-d H:i:s");


# SEARCH MAX MEMBERIN_POINT ID

	$sql_get_last_ins = 'SELECT max(mipo_MemberinPointID) FROM memberin_point';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################


if( $Act == 'edit' && $id ){

	$sql = '';

	$sql .= 'SELECT
				memberin_point.*
				FROM memberin_point
				WHERE mipo_MemberinPointID = "'.$id.'"';

	$oRes = $oDB->Query($sql);

	$i=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$asData = $axRow;
	}


} else if( $Act == 'save' ){

	$id = trim_txt($_REQUEST['id']);

	$mipo_Description = trim_txt($_REQUEST['mipo_Description']);

	$mipo_Status = trim_txt($_REQUEST['mipo_Status']);

	$mipo_StartDate = trim_txt($_REQUEST['StartDate']);

	$mipo_EndDate = trim_txt($_REQUEST['EndDate']);

	$mipo_PointQty = trim_txt($_REQUEST['mipo_PointQty']);

	$mipo_CollectionTypeID = trim_txt($_REQUEST['mipo_CollectionTypeID']);

	$mipo_CollectionMethod = trim_txt($_REQUEST['mipo_CollectionMethod']);

	$mipo_PeriodTime = trim_txt($_REQUEST['mipo_PeriodTime']);

	$mipo_PeriodType = trim_txt($_REQUEST['mipo_PeriodType']);

	$mipo_PeriodTypeEnd = trim_txt($_REQUEST['mipo_PeriodTypeEnd']);

	$mipo_RequestReceiptNo = trim_txt($_REQUEST['mipo_RequestReceiptNo']);

	$mipo_RequestReceiptAmount = trim_txt($_REQUEST['mipo_RequestReceiptAmount']);

	$mipo_Method = trim_txt($_REQUEST['mipo_Method']);

	$mipo_UseAmount = trim_txt($_REQUEST['mipo_UseAmount']);

	$mipo_Multiple = trim_txt($_REQUEST['mipo_Multiple']);

	$StartDate_other = trim_txt($_REQUEST['StartDate_other']);

	$EndDate_other = trim_txt($_REQUEST['EndDate_other']);




	$sql_point = '';

	$table_point = 'memberin_point';



	# ACTION POINT HEADER

	$sql_point .= 'mipo_Description="'.$mipo_Description.'"';  

	if($mipo_UseAmount){	$sql_point .= ',mipo_UseAmount="'.$mipo_UseAmount.'"';   } 

	if($mipo_Status){	$sql_point .= ',mipo_Status="'.$mipo_Status.'"';   } 

	if($mipo_Method){	$sql_point .= ',mipo_Method="'.$mipo_Method.'"';   }

	if($mipo_PointQty){	$sql_point .= ',mipo_PointQty="'.$mipo_PointQty.'"';   }

	if($mipo_CollectionTypeID){	$sql_point .= ',mipo_CollectionTypeID="'.$mipo_CollectionTypeID.'"';   }

	$sql_point .= ',mipo_RequestReceiptNo="'.$mipo_RequestReceiptNo.'"';

	$sql_point .= ',mipo_RequestReceiptAmount="'.$mipo_RequestReceiptAmount.'"';

	if($_SESSION['UID']){	$sql_point .= ',mipo_UpdatedBy="'.$_SESSION['UID'].'"';   }

	if($time_insert){	$sql_point .= ',mipo_UpdatedDate="'.$time_insert.'"';   }

	if($mipo_CollectionMethod){	$sql_point .= ',mipo_CollectionMethod="'.$mipo_CollectionMethod.'"';   }

	if($mipo_CollectionMethod == "Fix") {

		if($mipo_StartDate){	$sql_point .= ',mipo_StartDate="'.$mipo_StartDate.'"';   }

		if($mipo_EndDate){	$sql_point .= ',mipo_EndDate="'.$mipo_EndDate.'"';   }

		$sql_point .= ',mipo_PeriodTime=""';

		$sql_point .= ',mipo_PeriodType=""'; 

		$sql_point .= ',mipo_PeriodTypeEnd=""';

	} else if ($mipo_CollectionMethod == "No") {

		$sql_point .= ',mipo_StartDate=""';   

		$sql_point .= ',mipo_EndDate=""';

		$sql_point .= ',mipo_PeriodTime=""';

		$sql_point .= ',mipo_PeriodType=""'; 

		$sql_point .= ',mipo_PeriodTypeEnd=""';  

	} else if ($mipo_CollectionMethod == "Exp") {

		$sql_point .= ',mipo_StartDate=""';   

		$sql_point .= ',mipo_EndDate=""';
	
		$sql_point .= ',mipo_PeriodTime="'.$mipo_PeriodTime.'"';

		$sql_point .= ',mipo_PeriodType="'.$mipo_PeriodType.'"'; 

		if ($mipo_PeriodType == "M") { $sql_point .= ',mipo_PeriodTypeEnd="M"'; } 

		else { $sql_point .= ',mipo_PeriodTypeEnd="'.$mipo_PeriodTypeEnd.'"';  } 
	}

	if($mipo_Multiple){	$sql_point .= ',mipo_Multiple="'.$mipo_Multiple.'"';   }

	if($mipo_Multiple){	

		$sql_point .= ',mipo_MultipleStartDate="'.$StartDate_other.'"';  

		$sql_point .= ',mipo_MultipleEndDate="'.$EndDate_other.'"';  

	} else {

		$sql_point .= ',mipo_MultipleStartDate=""';  

		$sql_point .= ',mipo_MultipleEndDate=""';  
	}




	if ($id){

		#	UPDATE

		$do_sql_point = "UPDATE ".$table_point." SET ".$sql_point." WHERE mipo_MemberinPointID= '".$id."'";

		$oDB->QueryOne($do_sql_point);

	} else {

		# INSERT HEADER

		if($_SESSION['UID']){	$sql_point .= ',mipo_CreatedBy="'.$_SESSION['UID'].'"';   }

		if($time_insert){	$sql_point .= ',mipo_CreatedDate="'.$time_insert.'"';	}

		if($id_new){	$sql_point .= ',mipo_MemberinPointID="'.$id_new.'"';   }


		$do_sql_point = 'INSERT INTO '.$table_point.' SET '.$sql_point;

		$oDB->QueryOne($do_sql_point);
	}

	echo '<script>window.location.href = "point.php";</script>';

	exit;
}




#  icon dropdownlist

$as_icon = dropdownlist_from_table($oDB,'collection_type','coty_CollectionTypeID','coty_Name','coty_Type="MemberIn"');

$oTmp->assign('collection_type', $as_icon);



#  memberin_action dropdownlist

$as_action = dropdownlist_from_table($oDB,'memberin_action','miac_MemberinActionID','miac_Name','miac_Type="Point"',' ORDER BY miac_Name ASC');

$oTmp->assign('memberin_action', $as_action);



$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_memberin_point');

$oTmp->assign('content_file', 'memberin_motivation/point_create.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
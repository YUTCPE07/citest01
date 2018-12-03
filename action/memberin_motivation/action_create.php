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

if (($_SESSION['role_action']['motivation_action']['add'] != 1) || ($_SESSION['role_action']['motivation_action']['edit'] != 1)) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


# SEARCH MAX ID

	$sql_get_last_ins = 'SELECT max(miac_MemberinActionID) FROM memberin_action';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################



$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$time_insert = date("Y-m-d H:i:s");



	if( $Act == 'edit' && $id != '' ){

		# EDIT

		$sql = '';

		$sql .= 'SELECT * FROM memberin_action WHERE miac_MemberinActionID = "'.$id.'"';

		$oRes = $oDB->Query($sql);

		$asData = array();

		while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

			$i++;

			$asData = $axRow;

		}


	} else if( $Act == 'save' ) {

		$do_sql_action = "";

		$id = trim_txt($_REQUEST['id']);

		$miac_Name = trim_txt($_REQUEST['miac_Name']);

		$miac_Type = trim_txt($_REQUEST['miac_Type']);

		$miac_Description = trim_txt($_REQUEST['miac_Description']);

		$miac_Status = trim_txt($_REQUEST['miac_Status']);





		$sql_action = '';

		$table_action = 'memberin_action';



		if($miac_Name){	$sql_action .= 'miac_Name="'.$miac_Name.'"';   }

		$sql_action .= ',miac_Description="'.$miac_Description.'"';   

		if($miac_Type){	$sql_action .= ',miac_Type="'.$miac_Type.'"';   }

		if($miac_Status){	$sql_action .= ',miac_Status="'.$miac_Status.'"';   }

		if($time_insert){	$sql_action .= ',miac_UpdatedDate="'.$time_insert.'"';   }

		$sql_action .= ',miac_UpdatedBy="'.$_SESSION['UID'].'"';




		if($id){

			# UPDATE


			# CHECK ACTION NAME

			$sql_name = 'SELECT miac_Name FROM memberin_action WHERE miac_MemberinActionID!="'.$id.'"';

			$oRes = $oDB->Query($sql_name);

			while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

				$string1 = strtolower($axRow['miac_Name']);
				$string2 = strtolower($miac_Name);

				if ($string1 == $string2) {

					echo "<script>alert('Name Dupplicate');
						history.back();</script>";

					exit;
				}
			}

			$do_sql_action = "UPDATE ".$table_action." SET ".$sql_action." WHERE miac_MemberinActionID= '".$id."'";


		} else {

			# INSERT

			if($time_insert){	$sql_action .= ',miac_CreatedDate="'.$time_insert.'"';   }

			$sql_action .= ',miac_CreatedBy="'.$_SESSION['UID'].'"';

			if($id_new){	$sql_action .= ',miac_MemberinActionID="'.$id_new.'"';   }

			$do_sql_action = 'INSERT INTO '.$table_action.' SET '.$sql_action;

		}


		$oDB->QueryOne($do_sql_action);	

		echo '<script>window.location.href="action.php";</script>';

		exit;

	}






$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_memberin_action');

$oTmp->assign('content_file', 'memberin_motivation/action_create.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
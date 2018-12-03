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

if (($_SESSION['role_action']['card_type']['add'] != 1) || ($_SESSION['role_action']['card_type']['edit'] != 1)) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


# SEARCH MAX ID

	$sql_get_last_ins = 'SELECT max(card_type_id) FROM mi_card_type';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################


$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$time_insert = date("Y-m-d H:i:s");



if( $Act == 'edit' && $id != '' ){

	# EDIT

	$sql = '';

	$sql .= 'SELECT * FROM mi_card_type WHERE card_type_id = "'.$id.'"';

	$oRes = $oDB->Query($sql);

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;

		$asData = $axRow;
	}

} else if( $Act == 'save' ) {

	$do_sql_card = "";

	$id = trim_txt($_REQUEST['id']);

	$brand_id = trim_txt($_REQUEST['brand_id']);

	$type_name = trim_txt($_REQUEST['type_name']);

	$description = trim_txt($_REQUEST['description']);

	$flag_status = trim_txt($_REQUEST['flag_status']);



	$sql_card = '';

	$table_brand = 'mi_card_type';



	if($type_name){	$sql_card .= 'name="'.$type_name.'"';   }	

	if($brand_id){	$sql_card .= ',brand_id="'.$brand_id.'"';   }

	$sql_card .= ',description="'.$description.'"';   

	if($flag_status){	$sql_card .= ',flag_status="'.$flag_status.'"';   }

	if($time_insert){	$sql_card .= ',date_update="'.$time_insert.'"';   }

	$sql_card .= ',update_by="'.$_SESSION['UID'].'"';



	# CHECK TYPE NAME

	$sql_name = 'SELECT name FROM mi_card_type WHERE card_type_id!="'.$id.'" AND card_type_id!=0 AND flag_del!=1';

	$oRes = $oDB->Query($sql_name);

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$string1 = strtolower($axRow['name']);

		$string2 = strtolower($type_name);

		if ($string1 == $string2) {

			echo "<script>alert('Name Dupplicate');
					history.back();</script>"ว
			exit;
		}
	}


	if($id){

		# UPDATE

		$do_sql_card = "UPDATE ".$table_brand." SET ".$sql_card." WHERE card_type_id= '".$id."'";

	} else {

		# INSERT

		if($time_insert){	$sql_card .= ',date_create="'.$time_insert.'"';   }

		$sql_card .= ',create_by="'.$_SESSION['UID'].'"';

		if($id_new){	$sql_card .= ',card_type_id="'.$id_new.'"';   }

		$do_sql_card = 'INSERT INTO '.$table_brand.' SET '.$sql_card;
	}

	$oDB->QueryOne($do_sql_card);	

	echo '<script>window.location.href="card_type.php";</script>';

	exit;
}




#  brand dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' and brand_id="'.$_SESSION['user_brand_id'].'" ';
}

$as_brand_id = dropdownlist_from_table($oDB,'mi_brand','brand_id','name','brand_id>0'.$where_brand,' ORDER BY name ASC');

$oTmp->assign('brand_id_opt', $as_brand_id);



$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_card_type');

$oTmp->assign('content_file', 'card/card_type_create.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
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

if ($_SESSION['role_action']['communication']['add'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$brand_id = $_REQUEST['brand_id'];

$user_id = $_REQUEST['user_id'];



# SEARCH MAX ID

	$sql_get_last_ins = 'SELECT max(user_id) FROM mi_user';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################


if( $Act == 'edit' && $user_id != '' ){

	# EDIT

	$sql = '';
	$sql .= 'SELECT mi_user.email,
			mi_contact.firstname,
			mi_contact.lastname,
			mi_contact.mobile

			FROM mi_user

  			LEFT JOIN mi_contact
    		ON mi_user.user_id = mi_contact.user_id

			WHERE mi_user.user_id = "'.$user_id.'" ';

	$oRes = $oDB->Query($sql);

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$axRow['mobile'] = substr($axRow['mobile'],3);
		$asData = $axRow;
	}


} else if($Act == 'save') {

	# USER 

	$email = trim_txt($_REQUEST['email']);


	# CONTACT

	$firstname = trim_txt($_REQUEST['firstname']);

	$lastname = trim_txt($_REQUEST['lastname']);

	$mobile = trim_txt($_REQUEST['mobile']);


	$sql_user = '';

	$sql_contact = '';

	$table_user = 'mi_user';

	$table_contact = 'mi_contact';



	# ACTION MI_USER TABLE  

	$sql_user .= 'email="'.$email.'"';   

	$sql_user .= ',flag_status="1"';    

	$sql_user .= ',date_update="'.$time_insert.'"'; 

	$sql_user .= ',brand_id="'.$brand_id.'"';

	$sql_user .= ',role_RoleID="4"';

	$sql_user .= ',branch_id="0"'; 

	$sql_user .= ',flag_trial="No"'; 



	# ACTION MI_CONTACT TABLE

	$sql_contact .= 'firstname="'.$firstname.'"';   

	$sql_contact .= ',lastname="'.$lastname.'"';   

	$sql_contact .= ',mobile="+66'.$mobile.'"';   



	# CHECK MOBILE EMAIL

	if (!$user_id) {

		# EMAIL

		// $sql_email = 'SELECT email FROM mi_user WHERE role_RoleID="4"';

		// $oRes = $oDB->Query($sql_email);

		// while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		// 	$string1 = strtolower($axRow['email']);

		// 	$string2 = strtolower($email);

		// 	if ($string1 == $string2) {

		// 		echo "<script>alert('Email Dupplicate.');
		// 		history.back();</script>";

		// 		exit;
		// 	}
		// }


		# MOBILE

		// $sql_mobile = 'SELECT mi_contact.mobile 
		// 				FROM mi_contact
		// 				LEFT JOIN mi_user
		// 				ON mi_user.user_id = mi_contact.user_id
		// 				WHERE mi_user.role_RoleID="4"';

		// $oRes = $oDB->Query($sql_mobile);

		// while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		// 	$string1 = strtolower($axRow['mobile']);

		// 	$string2 = strtolower($mobile);

		// 	if ($string1 == $string2) {

		// 		echo "<script>alert('Mobile Dupplicate.');
		// 		history.back();</script>";

		// 		exit;
		// 	}
		// }

	} else {

		# EMAIL

		// $sql_email = 'SELECT email FROM mi_user WHERE user_id!="'.$user_id.'" AND role_RoleID="4"';

		// $oRes = $oDB->Query($sql_email);

		// while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		// 	$string1 = strtolower($axRow['email']);

		// 	$string2 = strtolower($username);

		// 	if ($string1 == $string2) {

		// 		echo "<script>alert('Email Dupplicate.');
		// 		history.back();</script>";

		// 		exit;
		// 	}
		// }

		# MOBILE

		// $sql_mobile = 'SELECT mi_contact.mobile 
		// 				FROM mi_contact
		// 				LEFT JOIN mi_user
		// 				ON mi_user.user_id = mi_contact.user_id
		// 				WHERE mi_user.role_RoleID="4"
		// 				AND mi_user.user_id!="'.$user_id.'"';

		// $oRes = $oDB->Query($sql_mobile);

		// while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		// 	$string1 = strtolower($axRow['mobile']);

		// 	$string2 = strtolower($username);

		// 	if ($string1 == $string2) {

		// 		echo "<script>alert('Mobile Dupplicate.');
		// 		history.back();</script>";

		// 		exit;
		// 	}
		// }
	}



	if($user_id){

		# UPDATE

		$do_sql_user = "UPDATE ".$table_user." SET ".$sql_user." WHERE user_id= '".$user_id."'";

		$do_sql_contact = 'UPDATE '.$table_contact.' SET '.$sql_contact.' WHERE user_id= "'.$user_id.'" ';

	} else {

		# INSERT

		if($time_insert){	$sql_user .= ',date_create="'.$time_insert.'"';   }

		if($id_new){	$sql_user .= ',user_id="'.$id_new.'"';   }

		$do_sql_user = 'INSERT INTO '.$table_user.' SET '.$sql_user;

		$user_id = $id_new;


		if($user_id){	$sql_contact .= ',user_id="'.$user_id.'"';   }

		$do_sql_contact = 'INSERT INTO '.$table_contact.' SET '.$sql_contact;
	}

	$oDB->QueryOne($do_sql_user);

	$oDB->QueryOne($do_sql_contact);

	echo '<script>window.location.href="communication_edit.php?act=edit&id='.$brand_id.'";</script>';

	exit;
}




$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_communication');

$oTmp->assign('content_file', 'communication/user_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
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

if ($_SESSION['role_action']['register_trans']['delete'] != 1) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");
$Act = $_REQUEST['act'];
$id = $_REQUEST['id'];
$path_upload_member = $_SESSION['path_upload_member'];


if($Act=='save' && $id!='') {

	# UPDATE DELETED

	$del_reason = $_REQUEST['del_reason'];
		
	$do_sql_del = "UPDATE mb_member_register 
 					SET flag_del='T',
 						del_reason='".$del_reason."',
 						payr_UpdatedDate='".$time_insert."' 
 					WHERE member_register_id='".$id."'";

 	$oDB->QueryOne($do_sql_del);
 			
 	echo '<script>window.location.href="register.php";</script>';
}

$sql_register ='SELECT DISTINCT
				a.*,
				b.name AS card_name,
				b.path_image,
				b.image AS card_image,
				b.image_newupload AS card_image_new,
				d.member_image,
				b.member_fee,
				d.firstname,
				d.lastname,
				d.email AS member_email,
				d.date_birth,
				d.facebook_id,
				d.nickname,
				d.mobile AS member_mobile,
				d.email AS member_email,
				d.member_id,
				c.logo_image,
				c.path_logo,
				c.name AS brand_name

				FROM mb_member_register AS a

				LEFT JOIN mi_card AS b
				ON a.card_id = b.card_id

				LEFT JOIN mb_member AS d
				ON a.member_id = d.member_id

				LEFT JOIN mi_brand AS c
				ON b.brand_id = c.brand_id

				WHERE a.member_register_id = "'.$id.'"';

$rs_regis = $oDB->Query($sql_register);

$asData = array();

while($axRow = $rs_regis->FetchRow(DBI_ASSOC)) {

	if ($axRow['date_birth']=="0000-00-00") { $axRow['date_birth'] = "-"; } 
	else { $axRow['date_birth'] = DateOnly($axRow['date_birth']); }

	if ($axRow['nickname']=="") { $axRow['nickname']="-"; }
	if ($axRow['firstname']=="") { $axRow['firstname']="-"; }
	if ($axRow['lastname']=="") { $axRow['lastname']="-"; }
	if ($axRow['member_email']=="") { $axRow['member_email']="-"; }
	if ($axRow['member_mobile']=="") { $axRow['member_mobile']="-"; }

	$detail_data = $axRow;
}






$oTmp->assign('data',$detail_data);

$oTmp->assign('is_menu', 'is_transaction');

$oTmp->assign('content_file','transaction/register_delete.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
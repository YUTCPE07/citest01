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

if (($_SESSION['role_action']['privilege_trans']['add'] != 1) || ($_SESSION['role_action']['privilege_trans']['edit'] != 1)) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$member_id = $_REQUEST['member_id'];
$use_id = $_REQUEST['use_id'];
$type = $_REQUEST['type'];
$Act = $_REQUEST['act'];

$time_insert = date("Y-m-d H:i:s");


if ($member_id) {

	$sql_member ='SELECT * FROM mb_member WHERE member_id="'.$member_id.'"';
	$member = $oDB->Query($sql_member);
	$axRow = $member->FetchRow(DBI_ASSOC);

	# MEMBER

	$member_name = $axRow['firstname'].' '.$axRow['lastname'];

	if($axRow['member_image']!='' && $axRow['member_image']!='https://www.memberin.com/images/user.png'){

		$axRow['member_image'] = '<img src="'.$path_upload_member.$axRow['member_image'].'" width="100" height="100" class="img-circle image_border"/>';	

	} else if ($axRow['facebook_id']!='') {

		$axRow['member_image'] = '<img src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=square" width="100" height="100" class="img-circle image_border"/>';

	} else {

		$axRow['member_image'] = '<img src="../../images/user.png" width="100" height="100" class="img-circle image_border"/>';
	}

	$data_member = '<center><br>
					<table class="myPopup">
						<tr>
							<td width="120px" style="text-align:center">'.$axRow['member_image'].'</td>
							<td>
								<p style="font-size:14px;padding-left:10px;">
									'.$member_name.'<br>
									'.$axRow['email'].'<br>
									'.$axRow['mobile'].'
								</p>
							</td>
						</tr>
					</table>
					</center>';

	$oTmp->assign('data_member', $data_member);
}


if ($Act == 'save' && $member_id && $use_id && $type) {

	$otp = trim_txt($_REQUEST['otp']);

	# CHECK OTP

	if ($type=='p') {

		$sql_check = 'SELECT mepe_UpdatedDate 
						FROM member_privilege_trans 
						WHERE mepe_OTP="'.$otp.'"
						AND memb_MemberID="'.$member_id.'"
						AND mepe_MemberPrivlegeID="'.$use_id.'"
						AND mepe_Platform="Insert"';

	} else if ($type=='c') {

		$sql_check = 'SELECT meco_UpdatedDate 
						FROM member_coupon_trans 
						WHERE meco_OTP = "'.$otp.'"
						AND memb_MemberID="'.$member_id.'"
						AND meco_MemberCouponID="'.$use_id.'"
						AND meco_Platform="Insert"';
						
	} else if ($type=='a') {

		$sql_check = 'SELECT meac_UpdatedDate 
						FROM member_activity_trans 
						WHERE meac_OTP = "'.$otp.'"
						AND memb_MemberID="'.$member_id.'"
						AND meac_MemberActivityID="'.$use_id.'"
						AND meac_Platform="Insert"';	
	}

	$check_date = $oDB->QueryOne($sql_check);

	if ($check_date) {

		$sql_update = 'UPDATE member_privilege_trans 
						SET mepe_OTP="", 
							mepe_Status="Active", 
							mepe_UpdatedDate="'.$time_insert.'" 
						WHERE memb_MemberID = "'.$member_id.'"
							AND mepe_UpdatedDate="'.$check_date.'"
							AND mepe_Platform="Insert"
							AND mepe_OTP="'.$otp.'"';
		$oDB->QueryOne($sql_update);

		$sql_update = 'UPDATE member_coupon_trans 
						SET meco_OTP="", 
							meco_Status="Active", 
							meco_UpdatedDate="'.$time_insert.'" 
						WHERE memb_MemberID = "'.$member_id.'"
							AND meco_UpdatedDate="'.$check_date.'"
							AND meco_Platform="Insert"
							AND meco_OTP="'.$otp.'"';
		$oDB->QueryOne($sql_update);

		$sql_update = 'UPDATE member_activity_trans 
						SET meac_OTP="", 
							meac_Status="Active", 
							meac_UpdatedDate="'.$time_insert.'" 
						WHERE memb_MemberID = "'.$member_id.'"
							AND meac_UpdatedDate="'.$check_date.'"
							AND meac_Platform="Insert"
							AND meac_OTP="'.$otp.'"';
		$oDB->QueryOne($sql_update);
	}

	echo '<script>window.location.href="privilege.php";</script>';
	exit();
}




$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_transaction');

$oTmp->assign('content_file', 'transaction/privilege_otp.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());

}

//========================================//

?>

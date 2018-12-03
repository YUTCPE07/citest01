<?php

header('Content-Type:text/html; charset=UTF-8');

//========================================//

ini_set("display_errors",1);
error_reporting(1);

//========================================//

include('../../include/common.php');
include('../../lib/function_normal.php');
include('../../include/common_check.php');
include("../../lib/phpmailer/class.phpmailer.php"); 
require_once ( "../../lib/phpmailer/PHPMailerAutoload.php" );
require_once('../../include/connect.php');

//========================================//

$oTmp = new TemplateEngine();
$oDB = new DBI();

if ($bDebug) {

	$oErr = new Tracker();
	$oDB->SetTracker($oErr);
}

//========================================//

if ($_SESSION['role_action']['upload_member']['add'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];

$time_insert = date("Y-m-d H:i:s");



$where_brand = '';

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND a.brand_id = "'.$_SESSION['user_brand_id'].'"';
}

#######################################


if($Act == 'save') {

	foreach($_REQUEST['email_id'] as $id) {

		$do_email = "SELECT send_email FROM mb_member_brand WHERE member_brand_id='".$id."'";

		$email = $oDB->QueryOne($do_email);

		$email++;

		$data_email = "SELECT email,member_token,brand_id,card_id 
							FROM mb_member_brand 
							WHERE member_brand_id='".$id."'";

		$oRes = $oDB->Query($data_email);
		$axRow = $oRes->FetchRow(DBI_ASSOC);



		$data_brand = "SELECT name,logo_image,code_color,text_color FROM mi_brand WHERE brand_id='".$axRow['brand_id']."'";

		$oRes_brand = $oDB->Query($data_brand);
		$brand = $oRes_brand->FetchRow(DBI_ASSOC);



		$data_card = "SELECT name, image FROM mi_card WHERE card_id='".$axRow['card_id']."'";

		$oRes_card = $oDB->Query($data_card);
		$card = $oRes_card->FetchRow(DBI_ASSOC);



		# SEND EMAIL

		$mail = new PHPMailer();

		$mail = new PHPMailer;

		$HTML = '<table style="margin-bottom:120px;width:700px;" >
					<tr>
						<td colspan="3" style="background:#'.$brand['code_color'].';color:'.$brand['text_color'].';padding:10px 0px 10px 30px;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;">
						    MemberIn &nbsp; | &nbsp; Join Card
						</td>
					</tr>
					<tr>
						<td></td>
						<td rowspan="3" style="text-align:center" width="330px">
						    <img src="http://www.memberin.com/upload/logo_card_upload/'.$card['image'].'" width="250" style="margin-top:30px;border-radius:10px;border: 1px solid #E6E6E6;"/>
						    <br>
						    <table style="width:300px;">
						    	<tr>
						    		<td style="text-align:center;width:100px"><img src="http://www.memberin.com/upload/logo_brand_upload/'.$brand['logo_image'].'" width="100" style="border: 1px solid #E6E6E6;margin-left:50px"></td>
						    		<td style="font-size:14px;font-family:Helvetica Neue,Helvetica,Arial, sans-serif;letter-spacing:1px;line-height:25px;text-align:center">
						    			'.$card['name'].'<br>'.$brand['name'].'</td>
						    	</tr>
						    </table>
						</td>
						<td style="font-size:30px;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;padding-bottom:45px;padding-top:55px;" >
						    Hi '.$axRow['email'].',
						</td>
					</tr>
					<tr>
						<td></td>
						<td style="font-size:16px;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;padding-bottom:45px; letter-spacing: 1px;line-height:25px;" >
						    You get "'.$card['name'].'" from "'.$brand['name'].'", all you need to get now is "Join Card" below
						</td>
					</tr>
					<tr>
						<td></td>
						<td><a href="www.memberin.com/demo/action/join_card.php?token_id='.$axRow['member_token'].'" style="font-size:13px;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;text-decoration:none;background:#'.$brand['code_color'].';color:'.$brand['text_color'].';padding:7px 30px 7px 30px;border-radius:7px;" > Join Card</a>
						 </td>
					</tr>
				</table>';

		$mail->Debugoutput = 'html';
		$mail->Host = 'mail.memberin.com';
		$mail->SMTPSecure = '25';
		$mail->SMTPAuth = true;
		$mail->Username = "noreply@memberin.com";
		$mail->Password = "m3mb3rIN@2016";
		$mail->CharSet = 'UTF-8';
		$mail->isSendmail();
		$mail->setFrom('noreply@memberin.com', 'MemberIn');
		$mail->addAddress($axRow['email']);
		$mail->Subject = 'MemberIn | Join Card';
		$mail->msgHTML($HTML);
		$mail->send();


		# UPDATE

		$do_sql = "UPDATE mb_member_brand SET send_email='".$email."', date_update='".$time_insert."' WHERE member_brand_id='".$id."'";

		$oDB->QueryOne($do_sql);
	}



	foreach($_REQUEST['sms_id'] as $id) {

		$do_sms = "SELECT send_sms FROM mb_member_brand WHERE member_brand_id='".$id."'";

		$sms = $oDB->QueryOne($do_sms);

		$sms++;


		$data_mobile = "SELECT mobile,member_token,brand_id,card_id 
							FROM mb_member_brand 
							WHERE member_brand_id='".$id."'";

		$oRes = $oDB->Query($data_mobile);
		$axRow = $oRes->FetchRow(DBI_ASSOC);


		$data_brand = "SELECT name FROM mi_brand WHERE brand_id='".$axRow['brand_id']."'";
		$brand = $oDB->QueryOne($data_brand);


		$data_card = "SELECT name FROM mi_card WHERE card_id='".$axRow['card_id']."'";
		$card = $oDB->QueryOne($data_card);


		$brand = str_replace(" ", "%20", $brand);
		$card = str_replace(" ", "%20", $card);


		$strlen = strlen($mobile);


		if ($strlen == 10) { $mobile = substr($axRow['mobile'],1,9); }


		$title = "MemberIn";

		$token = 'www.memberin.com/demo/action/join_card.php?token_id='.$axRow['member_token'];

		$urlSMS = "http://api.infobip.com/api/v3/sendsms/plain?user=jirarak&password=uW7eyLvN&sender=".$title."&SMSText=You%20get%20'".$card."'%20from%20'".$brand."',%20all%20you%20need%20to%20get%20now%20below%20".$token."&GSM=+66".$mobile."&type=longSMS";

		file_get_contents($urlSMS);

		$do_sql = "UPDATE mb_member_brand SET send_sms='".$sms."', date_update='".$time_insert."' WHERE member_brand_id='".$id."'";

		$oDB->QueryOne($do_sql);
	}

	echo '<script>window.location.href="upload_member.php";</script>';
}





#  brand dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' and brand_id="'.$_SESSION['user_brand_id'].'" ';
}

$as_brand_id = dropdownlist_from_table($oDB,'mi_brand','brand_id','name','brand_id>0'.$where_brand,' ORDER BY name ASC');

$oTmp->assign('brand_id_opt', $as_brand_id);



# card dropdownlist

$as_card = dropdownlist_from_table($oDB,'mi_card','card_id','name','flag_status="1"'.$where_brand,' ORDER BY name ASC');

$oTmp->assign('card_opt', $as_card);



$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('type_file_upload', '<span class="text-rq">Type file : .xls , .xlsx only</span>');

$oTmp->assign('is_menu', 'is_upload_member');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_membership', 'in');

$oTmp->assign('content_file', 'card/send_member.htm');

$oTmp->display('layout/template.html');



//========================================//


$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
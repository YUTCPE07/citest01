<?php 

ini_set("display_errors",0);

error_reporting(0);

//=====================================================//

include('../include/common_login.php');

include_once('../include/funcphp_smail.php');

require("../lib/phpmailer/class.phpmailer.php");

require_once ( '../lib/phpmailer/PHPMailerAutoload.php' );

//=====================================================//

$sACT =(isset($_POST['LANGUAGE'])) ? $_POST['LANGUAGE'] : "";

//=====================================================//

if($sACT=="EN" and $_POST['email']!="" ){



$oDB = new DBI();

if ($bDebug) {

	$oErr = new Tracker();

	$oDB->SetTracker($oErr);

}

	

	$user_up = $_POST['email'];

	$UN = $oDB->QueryOne("SELECT COUNT(*) 

							FROM mi_user  

							WHERE email ='".$user_up."'");

	$newpass = randomToken(10);

	

	if($UN==1){



		$arr_id = $oDB->QueryOne("SELECT user_id

							FROM mi_user

							WHERE email ='".$user_up."'");


		$sql .= "SELECT mi_user.username, 

							mi_contact.firstname,

							mi_contact.lastname

							FROM mi_user

							LEFT JOIN mi_contact

							ON mi_user.user_id = mi_contact.user_id

							WHERE mi_user.user_id ='".$arr_id."'";

		$oRes = $oDB->Query($sql);
		$axRow = $oRes->FetchRow(DBI_ASSOC);


		$user_fullname = $axRow['firstname']." ".$axRow['lastname'];
	


				# UPDATE

			$newpass_md5 = md5($newpass);
	
			$oRes_login_log = $oDB->QueryOne("Update mi_user set date_update='".date("Y-m-d H:i:s")."', password='".$newpass_md5."' WHERE email ='".$user_up."' ");	

		


				

		$data=array();

		$data[] = $user_fullname;

		$data[] = $newpass; 		

				

				# send mail

				if($sACT=='TH'){

				}else{

					// smail_phpmailer('forgot','EN','D','',$user_up,'', '',$data);

					$mail = new PHPMailer();



					$HTML = "Dear ".$user_fullname."<br>
								As you requested to reset password for your account, here is your new password.<br><br>
								<table border='0' width='250px'><tr>
									<td width='35%' style='background-color:#0d93c7;color:white' align='right'>Username&nbsp; &nbsp;</td>
									<td style='background-color:#e6e6e6'>&nbsp; &nbsp;".$axRow['username']."</td></tr>
									<tr>
									<td style='background-color:#0d93c7;color:white' align='right'>Password&nbsp; &nbsp;</td>
									<td style='background-color:#e6e6e6'>&nbsp; &nbsp;".$newpass."</td>
									</tr></table><br>
								Please change the password after you login.";



					$mail->Debugoutput = 'html';
					$mail->Host = 'mail.memberin.com';
					$mail->SMTPSecure = '25';
					$mail->SMTPAuth = true;
					$mail->Username = "noreply@memberin.com";
					$mail->Password = "m3mb3rIN@2016";
					$mail->CharSet = 'UTF-8';
					$mail->isSendmail();
					$mail->setFrom('noreply@memberin.com', 'MemberIn');
					$mail->addAddress($user_up);
					$mail->Subject = 'MemberIn | Reset Password';
					$mail->msgHTML($HTML);
					

					if(!$mail->Send()) {
    					echo "Mailer Error: " . $mail->ErrorInfo;
					} else {
    					// echo "Message sent!";
					}

				}

				#exit;

				#== SHOW MSG

				echo "<script language=\"JavaScript\"> alert('Reset Password Completed!, Please Check Email.'); 
					window.location.href = 'index.php';</script>";

				//=====================================================//	

		// 	}

		// }

	}else{

		#== SHOW MSG

		echo "<script language=\"JavaScript\"> alert('Error USERNAME, Please contact admin.');
			window.location.href = 'forget.php'; </script>";	

		exit();

	}

	

	$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());

}	

}


?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=windows-874" />

<title>.:: MemberIn ::.</title>

<link rel="shortcut icon" href="../../favicon.ico">

<link href="../css/screen.css" rel="stylesheet" type="text/css" />

<link href="../css/memberin.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" type="text/css" href="../lib/bootstrap/css/bootstrap.css">

  <script src="../lib/bootstrap/js/jquery-1.11.3.min.js"></script>

  <script src="../lib/bootstrap/js/bootstrap.js"></script>

</head>

<style type="text/css">

A:link {text-decoration: none}

A:visited {text-decoration: none}

A:active {

	text-decoration: none;

	font-size: 12;

}



.padding {
	padding-left:4.4cm;
}

.padding1 {
	padding-left:4.4cm;
	padding-top:2px;
}

</style>



</head>

<script type="text/javascript">



function keypressed(){

	if(event.keyCode=='13'){
		document.form.submit();
	}

}



function username_clear(){

	

	if(document.form.user.value =='Username'){

		document.form.user.value = "";

	}


}



function text_notclear(){

	if(document.form.user.value == ""){

		document.form.user.value ="";

	}

}

function text(){

        var s = document.form.user.value;

        var re = /[\!$%^&*(){}<>:;'\"]/; 

        var result = s.match(re);

		if(result!=null){

            document.form.user.value="";

            return false ;

        }

}

function CheckTXT(){  

        if ((event.keyCode == 32)||(event.keyCode >= 48 && event.keyCode <= 57)||(event.keyCode >= 65 && event.keyCode <= 90)||(event.keyCode >= 97 && event.keyCode <= 122)){event.returnValue = true; }

        else{event.returnValue = false;  }

} 

				

				

function part1(info) {

	var user = document.getElementById('user').value;

	

	if (user == '' || user == 'Username'){

		alert("Please fill Email.");

		document.getElementById('user').focus();  return false;

	}else{		

			var agree = confirm('Confrim ?');

			if (agree) {

				document.form.submit();

			} 

		

	}

}				

				

				

</script>



<body id="login-bg" style="padding-top:1px">

<div id="login-holder">

  <div id="loginbox">

  <form id="form" name="form"  method="post" action="">

  <div id="login_center_blue" class="login_center_blue_master login_center_blue" align="center">



 <div id="text-alert" style="font-size:12px;"><?=$message?></div> 

 <table width="330" align="center" id="acc-details">

 <tr>  

  <td class="login_logo">

  <!--Member<font color="#00CCFF"> In</font>-->

  <!--<img src="../images/logo_memberin.png" height="50" />-->

 </td>

  </tr>

  <tr>  

  <td class="forgot_logo">

  <span style="font-size:15px;padding-top:15px">Forgot / Reset  Password</span>

 </td>

  </tr>

  <tr>

<!-- <td width="70" height="130" rowspan="3" align="center" valign="top"><span style="height:165px;"></span></td>

-->

 <td align="left" valign="bottom"><input name="email"

  id="email"

  type="text" 

  class="form-control" 

   size="25" 

   value=""

  maxlength="50"

	onkeyup="text(this.value);" 

	onkeydown="javascript:if(event.keyCode==13)part1(this.form);"

  onclick="username_clear();"

  onblur="text_notclear();"

  onfocus="username_clear();"

  autocomplete="off"  

  placeholder="Email" style="height:40px;"

  required autofocus

  /></td>

  

  <tr>

  <td class="submit_cancel">    

 

						<button class="btn btn-primary btn-xs" type="submit" onclick="part1(this.form)" style="height:25px;width:80px;">SUBMIT</button>

                        &nbsp;&nbsp;&nbsp;

						<button class="btn btn-primary btn-xs" type="button" onClick="window.location.href = 'index.php'" style="height:25px;width:80px;">BACK</button>

  

  

   <input type="hidden" name="LANGUAGE" value="EN">

   

  </td>

  

  </tr>

 <!-- <tr>

  <td   width="180" onClick=" window.open ('forget.php','','menubar=no,toolbar=no,location=no,directories=no,status=no,scrollbars=auto,resizable=no,dependent,width=400,height=200');" style="cursor:pointer; color: #00C; font-size: 14px;"><div><u></br>Forgot / Reset  password</u>

  </div> 

 </td>

  <td></td>

  </tr>-->

  </table>

   

  </div>

   

 </form> 

 

  </div>



 </div>

 </div>

<!-- <div align="center" class="text1" >

&#169; COPYRIGHT 2015

</div>-->





</body>

</html>














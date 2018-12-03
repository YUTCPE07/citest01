<?php

session_start();

//========================================//

ini_set("display_errors",1);

error_reporting(1);

//========================================//

include('../include/common_login.php');

$sACT =(isset($_POST['act'])) ? $_POST['act'] : "";

//========================================//

$oTmp = new TemplateEngine();

$oDB = new DBI();

if ($bDebug) {

	$oErr = new Tracker();

	$oDB->SetTracker($oErr);

}

//========================================//

$time_insert = date("Y-m-d H:i:s");


if($sACT=="change"){


	# UPDATER

	$pass_old_text = md5($_POST['PASSWORD']);

	$pass_new_md5 = md5($_POST['NEWPASSWORD']);

	$pass_con_md5 = md5($_POST['CONFIRMPASSWORD']);

	


	echo $_SESSION['UID'];
	
	$password_user = $oDB->QueryOne("

							SELECT password 

							FROM mi_user

							WHERE user_id='".$_SESSION['UID']."'");


	if(trim($pass_old_text)!=trim($password_user)){

		echo "<script>
				 alert('Old password Incorrect!.');
				 history.back();
				</script>";		

		exit();

	} else if( trim($password_user) === trim($pass_new_md5) ){		

		echo "<script>

				alert('Please change your password!.');
		 		history.back();

			</script>";		

		exit();

	} else if( trim($pass_con_md5) != trim($pass_new_md5) ){		

		echo "<script>

				alert('New Password Not Match!.');
		 		history.back();

			</script>";		

		exit();

	} else {

		$asData = " password='".$pass_new_md5."', date_update='".$time_insert."'";

		$oRes_expire = $oDB->QueryOne("Update mi_user set ".$asData."  WHERE user_id=".$_SESSION['UID']);		

	}
	

					
	if( $_SESSION['DEP_PKID']!=''  ){ 

		$oRes_login_log = $oDB->Query("Update gmllp_users set last_login='".date("Y-m-d")."' , user_activated='Y'  WHERE user_pkid = '".$_SESSION["UID"]."' ");	

		Redirect($index_depart); 

	} else {

		$oTmp->assign('message', '<font color="red">Login incorrect, please try again.</font>');

		Redirect('index.php');

	}

}




$oTmp->display("change.htm");



$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());

}

?>
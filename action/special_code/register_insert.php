<?php

header('Content-Type:text/html; charset=UTF-8');

//========================================//

ini_set("display_errors",1);

error_reporting(1);

ini_set('memory_limit', '128M');

//========================================//

include('../../include/common.php');
include('../../lib/function_normal.php');
include('../../include/common_check.php');
include("../../lib/phpmailer/class.phpmailer.php"); 
require_once("../../lib/phpmailer/PHPMailerAutoload.php");
require_once('../../include/connect.php');

//========================================//


$oTmp = new TemplateEngine();

$oDB = new DBI();

if ($bDebug) {

	$oErr = new Tracker();

	$oDB->SetTracker($oErr);

}

//========================================//

if (($_SESSION['role_action']['register_trans']['add'] != 1) || ($_SESSION['role_action']['register_trans']['edit'] != 1)) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];

$time_insert = date("Y-m-d H:i:s");


if ($Act == 'save') {

	$card_CardID = trim_txt($_REQUEST['card_CardID']);
	$member_id = trim_txt($_REQUEST['member_id']);

	# CHECK MEMBER

	$member_email = strtolower(trim_txt($_REQUEST['email']));
	$member_mobile = trim_txt($_REQUEST['mobile']);

	# MOBILE

	if (strlen($member_mobile)==10) { 

		$member_mobile_1 = $member_mobile;
		$member_mobile_2 = "+66".substr($member_mobile,1);

	} else {

		if (strlen($member_mobile)<10) {

			$member_mobile_2 = "+66".$member_mobile;
			$member_mobile_1 = "0".$member_mobile;

		} else {

			$member_mobile_2 = $member_mobile;
			$member_mobile_1 = "0".substr($member_mobile,3);
		}
	}

	$sql_check = 'SELECT member_id
					FROM mb_member
					WHERE';

	if ($member_email != "") {

		$sql_check .= ' email="'.$member_email.'"';

		if ($member_mobile_1 != "" && $member_mobile_2 != "") {

			$sql_check .= ' OR mobile="'.$member_mobile_1.'" OR mobile="'.$member_mobile_2.'"';
		}
					
	} else {

		if ($member_mobile != "") {

			$sql_check .= ' mobile="'.$member_mobile_1.'" OR mobile="'.$member_mobile_2.'"';
		}
	}

	$check_member = $oDB->QueryOne($sql_check);

	if (!$member_id || $member_id=="") {

		if ($check_member) {

			$member_id = $check_member;

			register_member($member_id,$card_CardID,$member_mobile_2,$member_email);

		} else {

			register_member("",$card_CardID,$member_mobile_2,$member_email);
		}

	} else {

		if ($check_member) {

			$member_id = $check_member;

			register_member($member_id,$card_CardID,$member_mobile_2,$member_email);

		} else {

			register_member("",$card_CardID,$member_mobile_2,$member_email);
		}
	}

	echo '<script>window.location.href="register.php";</script>';

	exit();
}



function get_id() {

	$oDB = new DBI();

	$min_id = array();
	$i = 0;
	$sql_id = 'SELECT member_id FROM mb_member';
	$oRes_id = $oDB->Query($sql_id);

	while ($id = $oRes_id->FetchRow(DBI_ASSOC)) {
		$min_id[$i] = $id['member_id'];
		$i++;
	}

	$missing = array();
	for ($i = 1; $i < max($min_id); $i++) {
	    if (!in_array($i, $min_id)) $missing[] = $i;
	}

	if ($missing[0] != 0) { return $missing[0]; } 
	else { return max($min_id)+1; }

	exit();
}



function get_registerid() {

	$oDB = new DBI();

	$min_id = array();
	$i = 0;
	$sql_id = 'SELECT member_register_id FROM mb_member_register';
	$oRes_id = $oDB->Query($sql_id);

	while ($id = $oRes_id->FetchRow(DBI_ASSOC)) {
		$min_id[$i] = $id['member_register_id'];
		$i++;
	}

	$missing = array();
	for ($i = 1; $i < max($min_id); $i++) {
	    if (!in_array($i, $min_id)) $missing[] = $i;
	}

	if ($missing[0] != 0) { return $missing[0]; } 
	else { return max($min_id)+1; }

	exit();
}



function register_member($member_id,$card_CardID,$member_mobile,$member_email){

	$oDB = new DBI();

	$time_insert = date("Y-m-d H:i:s");

	if ($member_id == "") { 

		$member_id = get_id();
		$status_member = "new";

	} else { $status_member = "old"; }

	# CHECK MEMBER BRAND

	$brand_member = 'UPDATE mb_member_brand 
						SET member_id="'.$member_id.'",
							date_update="'.$time_insert.'"
						WHERE';

	if ($member_mobile) {

		$brand_member .= ' mobile="'.$member_mobile.'"';

		if ($member_email) {

			$brand_member .= ' OR email="'.$member_email.'"';
		}
	
	} else {

		if ($member_email) {

			$brand_member .= ' email="'.$member_email.'"';
		}
	}

	$oDB->QueryOne($brand_member);

	$data_member = '';
	$data_custom = '';


	// # CHECK PASSWORD

	// $pass1 = trim_txt($_REQUEST['pass1']);
	// $pass2 = trim_txt($_REQUEST['pass2']);

	// if ($pass1 != $pass2) {
				
	// 	echo "<script>alert('Password && Confirm Password Not Match.');
	// 			history.back();</script>";
	// 	exit;

	// } else if ($pass1 != "" && $pass2 != "") { $data_member .= ',password="'.md5($pass1).'"'; }

	$status_form = "true"; 

	$sql_field = 'SELECT a.*,b.*,

					a.mafi_MasterFieldID AS master_field_id,
					b.refo_Target

					FROM master_field AS a

					LEFT JOIN register_form AS b
					ON b.mafi_MasterFieldID = a.mafi_MasterFieldID

					WHERE a.mafi_Deleted != "T"
					AND b.card_CardID = "'.$card_CardID.'"

					GROUP BY a.mafi_FieldName
					ORDER BY a.mafi_FieldOrder';

	$oRes = $oDB->Query($sql_field);

	$d = 1;

	while ($field = $oRes->FetchRow(DBI_ASSOC)){

		# BIRTHDAY

		if ($field['master_field_id'] == 6) {

			$year = trim_txt($_REQUEST[$field['mafi_FieldName'].'_year']);
			$month = trim_txt($_REQUEST[$field['mafi_FieldName'].'_month']);
			$date = trim_txt($_REQUEST[$field['mafi_FieldName'].'_date']);

			if ($year != "" && $month != "" && $date != "") { $birthday = $year."-".$month."-".$date; }
			else { $birthday = ""; }

			# AGE

 			$age = (date("md", date("U", mktime(0, 0, 0, $month, $date, $year))) > date("md")
		    ? ((date("Y") - $year) - 1)
		    : (date("Y") - $year));

			if ($field['refo_Target']) {

	            $token = strtok($field['refo_Target'] , ",");
				$target = array();
				$i = 0;

				while ($token !== false) {

					$sql_target = 'SELECT mata_NameEn
									FROM master_target
									WHERE mata_MasterTargetID="'.$token.'"';
		 			$target[$i] = $oDB->QueryOne($sql_target);
					$token = strtok(",");
					$i++;
				}

				if ($target[0] <= $age && $age <= $target[1]) {

					if ($d == 1) {

						$data_member .= $field['mafi_FieldName'].'="'.$birthday.'"';
						$d++;

					} else {

						$data_member .= ','.$field['mafi_FieldName'].'="'.$birthday.'"';
						$d++;
					}

				} else { $status_form = "false"; }
			}
		
		} else if (trim_txt($_REQUEST[$field['mafi_FieldName']])) {

			if ($field['refo_Target']) {

				if ($field['refo_Target'] != trim_txt($_REQUEST[$field['mafi_FieldName']])) {

					$status_form = "false";

				} else {

					if ($d == 1) {

						$data_member .= $field['mafi_FieldName'].'="'.trim_txt($_REQUEST[$field['mafi_FieldName']]).'"'; 
						$d++;

					} else {

						$data_member .= ','.$field['mafi_FieldName'].'="'.trim_txt($_REQUEST[$field['mafi_FieldName']]).'"'; 
						$d++;
					}
				}

			} else {

				# MOBILE

				if ($field['master_field_id'] == 20) {

					$mobile = "+66".$_REQUEST[$field['mafi_FieldName']];

					if ($d == 1) {

						$data_member .= $field['mafi_FieldName'].'="'.$mobile.'"';
						$d++;

					} else {

						$data_member .= ','.$field['mafi_FieldName'].'="'.$mobile.'"';
						$d++;
					}

				} else {

					if ($d == 1) {

						$data_member .= $field['mafi_FieldName'].'="'.trim_txt($_REQUEST[$field['mafi_FieldName']]).'"';
						$d++;

					} else {

						$data_member .= ','.$field['mafi_FieldName'].'="'.trim_txt($_REQUEST[$field['mafi_FieldName']]).'"';
						$d++;
					}
				} 
			}
		}
	}

	if ($status_form == "true") {

		if ($status_member == "new") {

			$do_member = 'INSERT INTO mb_member SET '.$data_member;
			$do_member .= ',member_id="'.$member_id.'"'; 
			$do_member .= ',platform="Insert"'; 
			$do_member .= ',status_member="otp"'; 
			$do_member .= ',create_by="'.$_SESSION['UID'].'"'; 
			$do_member .= ',date_create="'.$time_insert.'"'; 
			$do_member .= ',update_by="'.$_SESSION['UID'].'"'; 
			$do_member .= ',date_update="'.$time_insert.'"; '; 
		
		} else {

			$do_member = 'UPDATE mb_member SET '.$data_member;
			$do_member .= ',update_by="'.$_SESSION['UID'].'"'; 
			$do_member .= ',date_update="'.$time_insert.'" '; 
			$do_member .= 'WHERE member_id="'.$member_id.'"; '; 

		}
	}

	$status_custom = "true";

	$sql_custom = 'SELECT custom_field.*,
					custom_form.cufo_Target,
					field_type.fity_Name AS field_type
					FROM custom_field
					LEFT JOIN custom_form
					ON custom_form.cufi_CustomFieldID = custom_field.cufi_CustomFieldID
					LEFT JOIN field_type
					ON custom_field.fity_FieldTypeID = field_type.fity_FieldTypeID
					WHERE custom_form.card_CardID = "'.$card_CardID.'"
					AND custom_form.cufo_FillIn = "Y"
					ORDER BY custom_field.cufi_FieldOrder';

	$oRes = $oDB->Query($sql_custom);
	while ($field = $oRes->FetchRow(DBI_ASSOC)){

		if (trim_txt($_REQUEST[$field['cufi_FieldName']])) {

			if ($field['cufo_Target']) {

				if ($field['cufo_Target'] != trim_txt($_REQUEST[$field['culi_FieldName']])) {

					$status_custom = "false";

				} else {

					$sql_check = 'SELECT reda_Value
									FROM custom_register_data
									WHERE mebe_MemberID = "'.$member_id.'"
									AND card_CardID = "'.$card_CardID.'"
									AND cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
					$check = $oDB->Query($sql_check);

					if ($check) {

						$do_custom .= 'UPDATE custom_register_data SET ';
						$do_custom .= 'reda_Value="'.trim_txt($_REQUEST[$field['culi_FieldName']]).'"';
						$do_custom .= ',reda_UpdatedBy="'.$_SESSION['UID'].'"'; 
						$do_custom .= ',reda_UpdatedDate="'.$time_insert.'"'; 
						$do_custom .= ' WHERE cufi_CustomFieldID="'.$field['cufi_CustomFieldID'].'"'; 
						$do_custom .= ' AND mebe_MemberID="'.$member_id.'"'; 
						$do_custom .= ' AND card_CardID="'.$card_CardID.'"; '; 

					} else {

						$do_custom .= 'INSERT INTO custom_register_data SET ';
						$do_custom .= 'cufi_CustomFieldID="'.$field['cufi_CustomFieldID'].'"'; 
						$do_custom .= ',mebe_MemberID="'.$member_id.'"'; 
						$do_custom .= ',card_CardID="'.$card_CardID.'"'; 
						$do_custom .= ',reda_Value="'.trim_txt($_REQUEST[$field['culi_FieldName']]).'"'; 
						$do_custom .= ',reda_CreatedBy="'.$_SESSION['UID'].'"'; 
						$do_custom .= ',reda_CreatedDate="'.$time_insert.'"'; 
						$do_custom .= ',reda_UpdatedBy="'.$_SESSION['UID'].'"'; 
						$do_custom .= ',reda_UpdatedDate="'.$time_insert.'"; '; 
					}
				}

			} else {

				if ($status_custom == "true" && $status_form == "true") {

					$sql_check = 'SELECT reda_Value
									FROM custom_register_data
									WHERE mebe_MemberID = "'.$member_id.'"
									AND card_CardID = "'.$card_CardID.'"
									AND cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
					$check = $oDB->QueryOne($sql_check);

					if ($check) {

						$do_custom .= 'UPDATE custom_register_data SET ';
						$do_custom .= 'reda_Value="'.trim_txt($_REQUEST[$field['culi_FieldName']]).'"';
						$do_custom .= ',reda_UpdatedBy="'.$_SESSION['UID'].'"'; 
						$do_custom .= ',reda_UpdatedDate="'.$time_insert.'"'; 
						$do_custom .= ' WHERE cufi_CustomFieldID="'.$field['cufi_CustomFieldID'].'"'; 
						$do_custom .= ' AND mebe_MemberID="'.$member_id.'"'; 
						$do_custom .= ' AND card_CardID="'.$card_CardID.'"; '; 

					} else {

						$do_custom .= 'INSERT INTO custom_register_data SET ';
						$do_custom .= 'cufi_CustomFieldID="'.$field['cufi_CustomFieldID'].'"'; 
						$do_custom .= ',mebe_MemberID="'.$member_id.'"'; 
						$do_custom .= ',card_CardID="'.$card_CardID.'"'; 
						$do_custom .= ',reda_Value="'.trim_txt($_REQUEST[$field['culi_FieldName']]).'"'; 
						$do_custom .= ',reda_CreatedBy="'.$_SESSION['UID'].'"'; 
						$do_custom .= ',reda_CreatedDate="'.$time_insert.'"'; 
						$do_custom .= ',reda_UpdatedBy="'.$_SESSION['UID'].'"'; 
						$do_custom .= ',reda_UpdatedDate="'.$time_insert.'"; '; 
					}
				}
			}
		}
	}

	if ($status_form == "true" && $status_custom == "true") {
		
		$oDB->QueryOne($do_member);
		
		$oDB->QueryOne($do_custom);

		$sql_card = 'SELECT period_type, period_type_other, date_expired
						FROM mi_card
						WHERE card_id = "'.$card_CardID.'"';
		$card = $oDB->Query($sql_card);
		$card_data = $card->FetchRow(DBI_ASSOC);

		$member_register_id = get_registerid();

		switch ($card_data['period_type']) {
		    case '1':
		      	$date_expire = $card_data['date_expired'];
		      	break;
		    case '2':
		      	$date_expire = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($time_insert)) . " + ".$period_type_other." Month"));
		     	break;
		    case '3':
		     	$date_expire = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($time_insert)) . " + 1 Year"));
		      	break;
		    case '4':
		      	$date_expire = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($time_insert))));
		      	break;
		    default:
		      	return false;
		}

		# MEMBER REGISTER

		$do_register .= 'INSERT INTO mb_member_register SET ';
		$do_register .= 'member_register_id="'.$member_register_id.'"'; 
		$do_register .= ',bran_BrandID="'.$_REQUEST['bran_BrandID'].'"'; 
		$do_register .= ',member_id="'.$member_id.'"'; 
		$do_register .= ',card_id="'.$card_CardID.'"'; 
		$do_register .= ',status="Complete"'; 
		$do_register .= ',payr_TransferStatus="Yes"'; 
		$do_register .= ',email="'.$member_email.'"'; 
		$do_register .= ',tel="'.$mobile.'"'; 
		$do_register .= ',platform="Insert"'; 
		$do_register .= ',period_type="'.$card_data['period_type'].'"'; 
		$do_register .= ',period_type_other="'.$card_data['period_type_other'].'"'; 
		$do_register .= ',date_expire="'.$date_expire.'"';
		$do_register .= ',payr_CreatedBy="'.$_SESSION['UID'].'"'; 
		$do_register .= ',date_create="'.$time_insert.'"'; 
		$do_register .= ',payr_UpdatedBy="'.$_SESSION['UID'].'"'; 
		$do_register .= ',payr_UpdatedDate="'.$time_insert.'"; '; 

		$oDB->QueryOne($do_register);

		echo "www ".$status_member;
		exit();

		if ($status_member == "new") {

			echo $member_email;
			exit();

			if ($member_email) {

				# VERIFY EMAIL

				$token_member = md5($member_email);

				$HTML = '<table style="margin-bottom:120px;width:800px;" >
						<tr>
		      				<td colspan="3" style="background:#50A9D5;color:#FFFFFF;padding:10px 0px 10px 30px;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;">
		        				E-mail verification
		      				</td>
					    </tr>
					    <tr>
					      <td>
					      </td>
					      <td rowspan= "3" >
					        <img src="http://www.memberin.com/images/Logo-mail.png"  width="250" style="padding:45px 70px 45px 30px;" />
					      </td>
					      <td style="font-size:30px;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;padding-bottom:45px;padding-top:55px;" >
					          Hi '.$member_email.',
					      </td>
					    </tr>
					    <tr>
					      </td>
					      <td>
					      </td>
					      <td style="font-size:16px;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;padding-bottom:45px; letter-spacing: 1px;line-height:25px;" >
					       You account has been successfully created, all you need to do now is verify your Email address below
					      </td>
					    </tr>
					    <tr>

					      <td>
					      </td>
					      <td>
							<input name="act" type="hidden" id="act" value="save" />
					      <a href="'.HOME_PATH_Location.'/token.php?token_id='.$token_member.'" style="font-size:13px;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;text-decoration:none;color:#FFFFFF;background-color:#50A9D5;padding:7px 30px 7px 30px;border-radius:7px;" > Verify Email Address</a>
					      </td>
					    </tr>
					</table>';

				echo $HTML;
				exit();

		        $mail = new PHPMailer();
				$mail = new PHPMailer;


		        $mail->Debugoutput = 'html';
				$mail->Host = 'mail.memberin.com';
				$mail->SMTPSecure = '25';
				$mail->SMTPAuth = true;
				$mail->Username = "noreply@memberin.com";
				$mail->Password = "m3mb3rIN@2016";
				$mail->CharSet = 'UTF-8';
				$mail->isSendmail();
				$mail->setFrom('noreply@memberin.com', 'MemberIn');
				$mail->addAddress($member_email);
				$mail->Subject = 'MemberIn | Mail Activeted';
				$mail->msgHTML($HTML);
		        $mail->Send();

				$do_token = 'UPDATE mb_member SET member_token="'.$token_member.'" WHERE member_id="'.$member_id.'"; '; 
				echo $do_token;
				exit();
				$oDB->QueryOne($do_token);
			}

			if ($mobile) {

				# VERIFY MOBILE

				$otp = mt_rand(1000,9999);

				$strSQL .= 'INSERT INTO member_otp_tel 
							SET memb_MemberID="'.$member_id.'",
							mot_Tel="'.$member_mobile.'",
							mot_OTP="'.$otp.'",
							mot_platform="Insert",
							mot_status="pending",
							date_create="'.$time_insert.'",
							date_update="'.$time_insert.'"';
				$oDB->QueryOne($strSQL);

				$urlOTP = "http://api.infobip.com/api/v3/sendsms/plain?user=jirarak&password=uW7eyLvN&sender=MemberIn&SMSText=OTP:".$otp."&GSM=".$member_mobile;
				file_get_contents($urlOTP);
			}
		}
	}
}


#  card dropdownlist

$where_brand = '';

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' brand_id="'.$_SESSION['user_brand_id'].'" ';
}

$as_card_id = dropdownlist_from_table($oDB,'mi_card','card_id','name',$where_brand,' ORDER BY name ASC');

$oTmp->assign('card', $as_card_id);


#  brand dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' brand_id="'.$_SESSION['user_brand_id'].'" ';
}

$as_brand_id = dropdownlist_from_table($oDB,'mi_brand','brand_id','name',$where_brand,' ORDER BY name ASC');

$oTmp->assign('brand', $as_brand_id);




$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_transaction');

$oTmp->assign('content_file', 'transaction/register_insert.htm');

$oTmp->display('layout/layout.htm');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());

}

//========================================//

?>

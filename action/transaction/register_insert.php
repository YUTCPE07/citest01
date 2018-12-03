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

if (($_SESSION['role_action']['register_trans']['add'] != 1) || ($_SESSION['role_action']['register_trans']['edit'] != 1)) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];

$time_insert = date("Y-m-d H:i:s");


if ($Act == 'save') {

	$card_CardID = trim_txt($_REQUEST['card_CardID']);
	$brnc_BranchID = trim_txt($_REQUEST['brnc_BranchID']);
	$member_id = trim_txt($_REQUEST['member_id']);
	$member_card_code = trim_txt($_REQUEST['member_card_code']);
	$member_brand_code = trim_txt($_REQUEST['member_brand_code']);
	$multiple_card = trim_txt($_REQUEST['multiple_card']);
	$start_date = trim_txt($_REQUEST['start_date']);
	$start_month = trim_txt($_REQUEST['start_month']);
	$start_year = trim_txt($_REQUEST['start_year']);

	$date_start = $start_year.'-'.$start_month.'-'.$start_date;

	if ($multiple_card=='') { $multiple_card = 1; }

	# BRAND CARD DATA

	$sql_brand = "SELECT mi_brand.logo_image, 
						mi_brand.path_logo,
						mi_card.date_expired,
						mi_card.name AS card_name, 
						mi_card.image AS card_image, 
						mi_card.path_image AS card_path_image,
						mi_card.greeting_messages_ckedit
					FROM mi_card 
					LEFT JOIN mi_brand
					ON mi_brand.brand_id = mi_card.brand_id 
					WHERE mi_card.card_id='".$card_CardID."'";
	$brand = $oDB->Query($sql_brand);
	$brand_data = $brand->FetchRow(DBI_ASSOC);

	# CARD EXPIRED

	$card_expired = $brand_data['date_expired'];

	# CARD DATA

	$card_name = $brand_data['card_name'];
	$card_image = $brand_data['card_image'];
	$card_path_image = $brand_data['card_path_image'];
	$message = $brand_data['greeting_messages_ckedit'];

	// $sql_card = 'SELECT name, image, path_image
	// 				FROM mi_card
	// 				WHERE card_id = "'.$card_CardID.'"';
	// $card = $oDB->Query($sql_card);
	// $card_data = $card->FetchRow(DBI_ASSOC);

	if ($card_expired == '0000-00-00' || $date_start<$card_expired) {	

		# CHECK MEMBER

		$member_email = strtolower(trim_txt($_REQUEST['email']));
		$member_mobile = trim_txt($_REQUEST['mobile']);
		$code_mobile = trim_txt($_REQUEST['code_mobile']);

		# MOBILE

		$member_mobile_1 = "0".$member_mobile;
		$member_mobile_2 = $code_mobile.$member_mobile;

		for ($i=1; $i <= $multiple_card; $i++) { 

			$check_member = "";

			if ($member_email == "" && $member_mobile == "") {

			} else {

				$sql_check = 'SELECT member_id
								FROM mb_member
								WHERE 1';

				if ($member_email != "") {

					$sql_check .= ' AND email="'.$member_email.'"';

					if ($member_mobile_1 != "" && $member_mobile_2 != "") {

						$sql_check .= ' OR mobile="'.$member_mobile_1.'" OR mobile="'.$member_mobile_2.'"';
					}
								
				} else {

					if ($member_mobile != "") {

						$sql_check .= ' AND mobile="'.$member_mobile_1.'" OR mobile="'.$member_mobile_2.'"';
					}
				}

				$sql_check .= ' ORDER BY member_id DESC LIMIT 1 ';

				$check_member = $oDB->QueryOne($sql_check);
			}

			if (!$member_id || $member_id=="") {

				if ($check_member) {

					$member_id = $check_member;

					register_member($member_card_code,$member_brand_code,$member_id,$card_CardID,$member_mobile_1,$member_mobile_2,$member_email,$date_start,$brnc_BranchID);

				} else {

					register_member($member_card_code,$member_brand_code,"",$card_CardID,$member_mobile_1,$member_mobile_2,$member_email,$date_start,$brnc_BranchID);
				}

			} else {

				register_member($member_card_code,$member_brand_code,$member_id,$card_CardID,$member_mobile_1,$member_mobile_2,$member_email,$date_start,$brnc_BranchID);
			}
		}

		if ($member_email) {

			# UNSUBSCRIBE

			if ($check_member) {

				$sql_un = 'SELECT unsubscribe FROM mb_member WHERE member_id="'.$check_member.'"';
				$check_un = $oDB->QueryOne($sql_un);
			
			} else { $check_un = ''; }

			# WELCOME MESSAGES

			// $sql_wel = 'SELECT greeting_messages_ckedit FROM mi_card WHERE card_id="'.$card_CardID.'"';
			// $message = $oDB->QueryOne($sql_wel);

			if ($message) {

				$HTML = '<table style="width:720px;background-color:#FFF;" cellspacing="0" cellpadding="0">
							<tr height="100px">
								<td style="text-align:center">
									<img src="http://www.memberin.com/images/LOGO.png" height="70px">
								</td>
							</tr>
							<tr><td style="text-align:center"><center>
								<br><img src="http://www.memberin.com/upload/'.$brand_data['path_logo'].$brand_data['logo_image'].'" style="border: 1px solid #E6E6E6" width="100" height="100"/><br>
									<span style="font-size:16px"><br><b>ยินดีต้อนรับ ด้วยสิทธิพิเศษ จาก<br>
									Welcome with Privilege from<br>
									'.$card_name.'</b></span>
								<br><br><img src="http://www.memberin.com/upload/'.$card_path_image.$card_image.'" style="border: 1px solid #E6E6E6;border-radius:10%" width="160" height="100"/><br><br>
									<div align="left" style="width:80%;font-weight:normal;">
										<span style="font-size:14px"><b>เรียน ท่านผู้มีเกียรติ</b><br><br>
											ยินดีต้อนรับ ด้วยสิทธิพิเศษ จาก '.$card_name.'<br><br>
											ท่านจะได้รับสิทธิพิเศษต่างๆ อัพเดทข่าวสาร โปรโมชั่น สิทธิพิเศษอื่นๆ หวังอย่างยิ่งว่าท่านจะได้รับความคุ้มค่า ทุกครั้งที่ได้เข้ามาใช้บริการ
											<br><br><hr><br>
											<b>Dear Value Customer</b><br><br>
											We pround to inform you that you will receive privileges and news information.<br><br>
											My pressure to serving you.
										</span>
									</div>
									<table width="70%" class="myPopup">
										<tr><td width="140px" valign="top">
										<br>'.htmlspecialchars_decode(htmlspecialchars_decode(base64_decode($message))).'<br>
										</td></tr>
									</table>
									<br><hr style="width:80%"><br>
									<div align="left" style="width:80%;font-weight:normal;">
										<span style="font-size:14px">
											MemberIn | เมมเบออิน<br>
											T. +66 (0) 2061 1169<br>
											E. contact@memberin.com<br>
											<a href="www.memberin.com">www.memberin.com</a>
										</span>
									</div>
								</center>
							</td></tr>
						</table>';

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
					$mail->Subject = 'MemberIn | Welcome to '.$card_name;
					$mail->msgHTML($HTML);

					if ($check_un=='') { $mail->Send(); }
			}
		}

		echo '<script>window.location.href="register.php";</script>';

	} else {

		echo '<script style="text/javascript">
				alert("Start Date เกินวัน Expired Date");
				window.history.back();</script>';
	}
}



function get_id() {

	$oDB = new DBI();

	$min_id = array();
	$i = 0;
	$sql_id = 'SELECT MAX(member_id) FROM mb_member';
	$member_id = $oDB->QueryOne($sql_id);

	$member_id = $member_id+10;

	return $member_id;

	exit();
}



function get_registerid() {

	$oDB = new DBI();

	$min_id = array();
	$i = 0;
	$sql_id = 'SELECT MAX(member_register_id) FROM mb_member_register';
	$member_register_id = $oDB->QueryOne($sql_id);

	$member_register_id = $member_register_id+10;

	return $member_register_id;

	exit();
}



function register_member($member_card_code,$member_brand_code,$member_id,$card_CardID,$member_mobile_1,$member_mobile_2,$member_email,$date_start,$brnc_BranchID){

	$oDB = new DBI();

	$time_insert = date("Y-m-d H:i:s");

	if ($member_id == "") { 

		// $member_id = get_id();
		$status_member = "new";

	} else { $status_member = "old"; }

	# CHECK MEMBER BIRTHDAY

	$birth_member = '';

	if ($status_member == "old") {

		$sql_birth = 'SELECT date_birth FROM member_id="'.$member_id.'"';
		$birth_member = $oDB->QueryOne($sql_birth);
	}

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
					AND a.mafi_MasterFieldID NOT IN (48,49)

					GROUP BY a.mafi_FieldName
					ORDER BY a.mafi_FieldOrder';

	$oRes = $oDB->Query($sql_field);

	$d = 1;

	while ($field = $oRes->FetchRow(DBI_ASSOC)){

		# BIRTHDAY

		if ($field['master_field_id'] == 6) {

			if ($birth_member == '0000-00-00' || $birth_member == '') {

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

						if ($birthday) {

							if ($d == 1) {

								$data_member .= $field['mafi_FieldName'].'="'.$birthday.'"';
								$d++;

							} else {

								$data_member .= ','.$field['mafi_FieldName'].'="'.$birthday.'"';
								$d++;
							}
						}

					} else { $status_form = "false"; }
				
				} else {

					if ($birthday) {

						if ($d == 1) {

							$data_member .= $field['mafi_FieldName'].'="'.$birthday.'"';
							$d++;

						} else {

							$data_member .= ','.$field['mafi_FieldName'].'="'.$birthday.'"';
							$d++;
						}
					}
				}
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

					$mobile_code = $_REQUEST['code_'.$field['mafi_FieldName']];
					$mobile = $mobile_code.$_REQUEST[$field['mafi_FieldName']];

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

	# PASSWORD

	$digits_needed = 8;
	$random_number = '';
	$count = 0;

	while ($count < $digits_needed) {
		
		$random_digit = mt_rand(0, 9);
		$random_number .= $random_digit;
		$count++;
	}


	# MEMBER

	if ($status_form == "true") {

		if ($status_member == "new") {

			$do_member = 'INSERT INTO mb_member SET '.$data_member;
			// $do_member .= ',member_id="'.$member_id.'"'; 
			$do_member .= ',password="'.md5($random_number).'"'; 
			$do_member .= ',platform="Insert"'; 
			$do_member .= ',status_member="success"'; 
			$do_member .= ',create_by="'.$_SESSION['UID'].'"'; 
			$do_member .= ',date_create="'.$time_insert.'"'; 
			$do_member .= ',update_by="'.$_SESSION['UID'].'"'; 
			$do_member .= ',date_update="'.$time_insert.'"; '; 

			if ($oDB->QueryOne($do_member)) { $member_id = $oDB->member_id; }
		
		} else {

			$do_member = 'UPDATE mb_member SET '.$data_member;
			$do_member .= ',update_by="'.$_SESSION['UID'].'"'; 
			$do_member .= ',date_update="'.$time_insert.'" '; 
			$do_member .= 'WHERE member_id="'.$member_id.'"; '; 

			$oDB->QueryOne($do_member);
		}
	}


	# CUSTOM

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

		$do_custom = '';

		if (trim_txt($_REQUEST[$field['cufi_FieldName']])) {

			$data_custom = trim_txt($_REQUEST['code_'.$field['cufi_FieldName']]).trim_txt($_REQUEST[$field['cufi_FieldName']]);

			if ($field['cufo_Target']) {

				if ($field['cufo_Target'] != $data_custom) {

					$status_custom = "false";

				} else {

					$sql_check = 'SELECT reda_Value
									FROM custom_register_data
									WHERE mebe_MemberID = "'.$member_id.'"
									AND card_CardID = "'.$card_CardID.'"
									AND cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
					$check = $oDB->Query($sql_check);

					if ($check) {

						$do_custom = 'UPDATE custom_register_data SET ';
						$do_custom .= 'reda_Value="'.$data_custom.'"';
						$do_custom .= ',reda_UpdatedBy="'.$_SESSION['UID'].'"'; 
						$do_custom .= ',reda_UpdatedDate="'.$time_insert.'"'; 
						$do_custom .= ' WHERE cufi_CustomFieldID="'.$field['cufi_CustomFieldID'].'"'; 
						$do_custom .= ' AND mebe_MemberID="'.$member_id.'"'; 
						$do_custom .= ' AND card_CardID="'.$card_CardID.'"; '; 

					} else {

						$do_custom = 'INSERT INTO custom_register_data SET ';
						$do_custom .= 'cufi_CustomFieldID="'.$field['cufi_CustomFieldID'].'"'; 
						$do_custom .= ',mebe_MemberID="'.$member_id.'"'; 
						$do_custom .= ',card_CardID="'.$card_CardID.'"'; 
						$do_custom .= ',reda_Value="'.$data_custom.'"'; 
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

						$do_custom = 'UPDATE custom_register_data SET ';
						$do_custom .= 'reda_Value="'.$data_custom.'"';
						$do_custom .= ',reda_UpdatedBy="'.$_SESSION['UID'].'"'; 
						$do_custom .= ',reda_UpdatedDate="'.$time_insert.'"'; 
						$do_custom .= ' WHERE cufi_CustomFieldID="'.$field['cufi_CustomFieldID'].'"'; 
						$do_custom .= ' AND mebe_MemberID="'.$member_id.'"'; 
						$do_custom .= ' AND card_CardID="'.$card_CardID.'"; '; 

					} else {

						$do_custom = 'INSERT INTO custom_register_data SET ';
						$do_custom .= 'cufi_CustomFieldID="'.$field['cufi_CustomFieldID'].'"'; 
						$do_custom .= ',mebe_MemberID="'.$member_id.'"'; 
						$do_custom .= ',card_CardID="'.$card_CardID.'"'; 
						$do_custom .= ',reda_Value="'.$data_custom.'"'; 
						$do_custom .= ',reda_CreatedBy="'.$_SESSION['UID'].'"'; 
						$do_custom .= ',reda_CreatedDate="'.$time_insert.'"'; 
						$do_custom .= ',reda_UpdatedBy="'.$_SESSION['UID'].'"'; 
						$do_custom .= ',reda_UpdatedDate="'.$time_insert.'"; '; 
					}
				}
			}
			
			$oDB->QueryOne($do_custom);
		}
	}


	# MEMBER BRAND CODE

	$status_brand = "true";

	// if ($member_brand_code) {

	// 	$sql_brand_code = 'SELECT member_id 
	// 						FROM mb_member_register 
	// 						WHERE bran_BrandID="'.$_REQUEST['bran_BrandID'].'"
	// 						AND member_id!="'.$member_id.'"
	// 						AND member_brand_code="'.$member_brand_code.'"';
	// 	$member_brand_id = $oDB->QueryOne($sql_brand_code);

	// 	if ($member_brand_id) { $status_brand = "false"; }
	// }

	# MEMBER CARD CODE

	$status_card = "true";

	// if ($member_card_code) {

	// 	$sql_card_code = 'SELECT member_id 
	// 						FROM mb_member_register 
	// 						WHERE card_CardID="'.$card_CardID.'"
	// 						AND member_id!="'.$member_id.'"
	// 						AND member_card_code="'.$member_card_code.'"';
	// 	$member_card_id = $oDB->QueryOne($sql_card_code);

	// 	if ($member_card_id) { $status_card = "false"; }
	// }



	# MEMBER REGISTER

	if ($status_form == "true" && $status_custom == "true" && $status_card == "true" && $status_brand == "true") {

		// if ($member_email || $member_mobile_1 || $member_mobile_2 || $member_card_code || $member_brand_code) {

		// 	# CHECK MEMBER BRAND

		// 	$j = 0;

		// 	$brand_member = 'UPDATE mb_member_brand 
		// 						SET member_id="'.$member_id.'",
		// 							date_update="'.$time_insert.'"
		// 						WHERE ';

		// 	if ($member_email!="") {

		// 		if ($j == 0) { $brand_member .= ' email="'.$member_email.'"';	$j++; }
		// 		else { $brand_member .= ' OR email="'.$member_email.'"'; }
		// 	}

		// 	if ($member_mobile_1!="") {

		// 		if ($j == 0) { $brand_member .= ' mobile="'.$member_mobile_1.'"';	$j++; }
		// 		else { $brand_member .= ' OR mobile="'.$member_mobile_1.'"'; }
		// 	}

		// 	if ($member_mobile_2!="") {

		// 		if ($j == 0) { $brand_member .= ' mobile="'.$member_mobile_2.'"';	$j++; }
		// 		else { $brand_member .= ' OR mobile="'.$member_mobile_2.'"'; }
		// 	}

		// 	if ($member_card_code!="") {

		// 		if ($j == 0) { $brand_member .= ' member_card_code="'.$member_card_code.'"';	$j++; }
		// 		else { $brand_member .= ' OR member_card_code="'.$member_card_code.'"'; }
		// 	}

		// 	if ($member_brand_code!="") {

		// 		if ($j == 0) { $brand_member .= ' member_brand_code="'.$member_brand_code.'"';	$j++; }
		// 		else { $brand_member .= ' OR member_brand_code="'.$member_brand_code.'"'; }
		// 	}

		// 	$oDB->QueryOne($brand_member);
		// }

		# MOBILE SMS

		$mobile_sms = '';

		if ($mobile) { $mobile_sms = "0".substr($mobile,3); }

		# BRAND NAME

		$sql_brand = "SELECT mi_brand.name,
						mi_card.flag_multiple,
						mi_card.date_last_register,
						mi_card.period_type, 
						mi_card.period_type_other, 
						mi_card.date_expired
						FROM mi_card 
						LEFT JOIN mi_brand
						ON mi_brand.brand_id = mi_card.brand_id 
						WHERE mi_card.card_id='".$card_CardID."'";
		$oRes_data = $oDB->Query($sql_brand);
		$card_data = $oRes_data->FetchRow(DBI_ASSOC);

		$brand_name = $card_data['name'];
		$multiple = $card_data['flag_multiple'];
		$last_date = $card_data['date_last_register'];

		// # CHECK MULTIPLE CARD

		// $sql_multi = "SELECT flag_multiple FROM mi_card WHERE card_id='".$card_CardID."'";
		// $multiple = $oDB->QueryOne($sql_multi);

		# CHECK REGISTER

		$sql_regis = "SELECT member_register_id 
						FROM mb_member_register
						WHERE card_id=".$card_CardID."
						AND member_id=".$member_id."
						AND flag_del=''";
		$register_id = $oDB->QueryOne($sql_regis);

		// # CHECK LAST REGISTER DATE

		// $sql_last = "SELECT date_last_register 
		// 				FROM mi_card
		// 				WHERE card_id=".$card_CardID."";
		// $last_date = $oDB->QueryOne($sql_last);

		if ($last_date!='0000-00-00' && ($last_date < $date_start)) {

			echo "<script type='text/javascript'>
				alert('เลยช่วงวันที่สามารถสมัครบัตร');
				window.history.back();</script>";

		} else {

			if (!$register_id || $multiple=="Yes") {
			
				// $oDB->QueryOne($do_member);
				// $oDB->QueryOne($do_custom);

				// $sql_card = 'SELECT period_type, period_type_other, date_expired
				// 				FROM mi_card
				// 				WHERE card_id = "'.$card_CardID.'"';
				// $card = $oDB->Query($sql_card);
				// $card_data = $card->FetchRow(DBI_ASSOC);

				// $member_register_id = get_registerid();

				switch ($card_data['period_type']) {
				    case '1':
				      	$date_expire = $card_data['date_expired'];
				      	break;
				    case '2':
				      	$date_expire = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($date_start)) . " + ".$card_data['period_type_other']." Month"));
				     	break;
				    case '3':
				     	$date_expire = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($date_start)) . " + ".$card_data['period_type_other']." Year"));
				      	break;
				    case '4':
				      	$date_expire = $time_insert;
				      	break;
				    default:
				      	return false;
				}

				# MEMBER REGISTER

				$do_register = 'INSERT INTO mb_member_register SET ';
				// $do_register .= 'member_register_id="'.$member_register_id.'"'; 
				$do_register .= 'member_card_code="'.$member_card_code.'"'; 
				$do_register .= ',member_brand_code="'.$member_brand_code.'"';
				$do_register .= ',bran_BrandID="'.$_REQUEST['bran_BrandID'].'"'; 
				$do_register .= ',member_id="'.$member_id.'"'; 
				$do_register .= ',card_id="'.$card_CardID.'"'; 
				$do_register .= ',brnc_BranchID="'.$brnc_BranchID.'"'; 
				$do_register .= ',status="Complete"'; 
				$do_register .= ',payr_TransferStatus="Yes"'; 
				$do_register .= ',email="'.$member_email.'"'; 
				$do_register .= ',tel="'.$mobile.'"'; 
				$do_register .= ',platform="Insert"'; 
				$do_register .= ',period_type="'.$card_data['period_type'].'"'; 
				$do_register .= ',period_type_other="'.$card_data['period_type_other'].'"'; 
				$do_register .= ',date_start="'.$date_start.'"';
				$do_register .= ',date_expire="'.$date_expire.'"';
				$do_register .= ',payr_CreatedBy="'.$_SESSION['UID'].'"'; 
				$do_register .= ',date_create="'.$time_insert.'"'; 
				$do_register .= ',payr_UpdatedBy="'.$_SESSION['UID'].'"'; 
				$do_register .= ',payr_UpdatedDate="'.$time_insert.'";';

				if ($oDB->QueryOne($do_register)) { $member_register_id = $oDB->member_register_id; }

				
				# UPDATE MEMBER BRAND

				if ($member_card_code) {

					$brand_member = 'UPDATE mb_member_brand 
										SET member_register_id="'.$member_register_id.'",
											member_id="'.$member_id.'",
											date_update="'.$time_insert.'"
										WHERE card_id="'.$card_CardID.'"
										AND member_card_code="'.$member_card_code.'"';

					$oDB->QueryOne($brand_member);
				}


				if ($status_member == "new") {

					if ($member_email) {

						# VERIFY EMAIL

						$token_member = md5($member_email);

						$HTML = '<table style="width:720px;background-color:#FFF;" cellspacing="0" cellpadding="0">
									<tr height="100px">
										<td style="text-align:center">
											<img src="http://www.memberin.com/images/LOGO.png" height="70px">
										</td>
									</tr>
									<tr>
										<td><center>
											<div align="left" style="width:80%;font-weight:normal;">
												<b>ยินดีต้อนรับ '.$member_email.',</b>
												<br><br>
												ขอบพระคุณเป็นอย่างสูงที่ท่านให้ความไว้วางใจสมัครเข้าร่วมกับเรา<br>
												เพื่อการส่งข้อมูลข่าวสารให้ถูกต้อง ขอความกรุณาท่านกดยืนยันอีเมล์ ที่ปุ่มด้านล่าง เพื่อให้ขบวนการลงทะเบียนเสร็จสมบูรณ์
												<br><br><hr><br>
												<b>Hi '.$member_email.',</b>
												<br><br>
												You account has been successfully created, all you need to do now is verify your Email address below.
												<br><br><hr><br>
											</div>
											<table style="border:1px black solid;border-collapse:collapse;">
												<tr height="30px" style="border:1px black solid;">
													<td style="text-align:right;width:80px;background-color:#50A9D5;color:#FFFFFF;">&nbsp;&nbsp;User&nbsp;&nbsp;</td>
													<td>&nbsp;&nbsp;'.$member_email.'&nbsp;&nbsp;</td>
												</tr>
												<tr height="30px">
													<td style="text-align:right;background-color:#50A9D5;color:#FFFFFF;">&nbsp;&nbsp;Password&nbsp;&nbsp;</td>
													<td>&nbsp;&nbsp;'.$random_number.'&nbsp;&nbsp;</td>
												</tr>
											</table>
											<br><br>
											<a href="www.memberin.com/token.php?token_id='.$token_member.'" style="font-size:13px;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;text-decoration:none;color:#FFFFFF;background-color:#50A9D5;padding:7px 30px 7px 30px;border-radius:7px;" > Verify Email Address</a>
											<br><br><br>
											<a href="https://goo.gl/tE1DtI" target="_blank">
												<img src="http://www.memberin.com/images/download/appstore.png" height="30px">
											</a>
											<a href="https://goo.gl/WSFRjP" target="_blank">
												<img src="http://www.memberin.com/images/download/googleplay.png" height="30px">
											</a>
											<br><br>
											<hr style="width:80%;">
											<div align="left" style="width:80%;font-weight:normal;">
												<br>
												<span style="font-size:14px">
												ขอแสดงความนับถือ<br>
												ฝ่ายติดตั้งระบบและบริการ
												<br><br>
												MemberIn | เมมเบออิน<br>
												T. +66 (0) 2061 1169<br>
												E. contact@memberin.com<br>
												<a href="www.memberin.com">www.memberin.com</a>
												</span>
											</div>
										</center></td>
									</tr>
								</table>';

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
						$mail->Subject = 'MemberIn | Mail Activated';
						$mail->msgHTML($HTML);
				        $mail->Send();

						$do_token = 'UPDATE mb_member SET member_token="'.$token_member.'" WHERE member_id="'.$member_id.'"; ';
						$oDB->QueryOne($do_token);
					}

					if ($mobile && $mobile_code == '+66') {

						$message = new stdClass();
		            	$message->from = 'iHealthy';
		            	$message->to = $mobile;
			            $message->text = "กรุณาเข้าระบบ MemberIn ด้วยเบอร์โทรศัพท์ของท่าน (".$mobile_sms.") และ Password: ".$random_number." เท่านั้น เพื่อรับสิทธิ์จาก ".$brand_name." [Download MemberIn app. : goo.gl/5vYxXD]";
		        
			        	$username = 'Jirarak';
			        	$password = 'memberin2017';
			        	$auth = base64_encode("$username:$password");       
			    
			        	$curl = curl_init('api.infobip.com/sms/1/text/single');
			    
			        	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			        	curl_setopt($curl, CURLOPT_HTTPHEADER, [
			            	"Authorization: Basic $auth",
			            	"Content-Type: application/json"
			        	]);

						curl_setopt($curl, CURLOPT_POST, true);
						curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($message));
						    
						$curl_response = curl_exec($curl);
						    
						$http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
						curl_close($curl);
						$decoded = json_decode($curl_response);
						    
						if ($http_status_code != 200) {
						    
						    error_log("SendMessage|".$decoded->requestError->serviceException->messageId.":".$decoded->requestError->serviceException->text."|".date('Y-m-d H:i:s')."\n", 3, 'error.log');
						}
					}
				
				} else {

					if ($mobile && $mobile_code == '+66') {

						# CHECK VERIFY

						// $check_otp = 'SELECT status_member FROM mb_member WHERE memb_MemberID="'.$member_id.'"';
						// $otp = $oDB->QueryOne($check_otp);

						// if ($otp == 'otp') {

							# VERIFY MOBILE

							// $otp = mt_rand(1000,9999);

							// $strSQL .= 'INSERT INTO member_otp_tel 
							// 			SET memb_MemberID="'.$member_id.'",
							// 			mot_Tel="'.$mobile.'",
							// 			mot_OTP="'.$otp.'",
							// 			mot_platform="Insert",
							// 			mot_status="pending",
							// 			date_create="'.$time_insert.'",
							// 			date_update="'.$time_insert.'"';
							// $oDB->QueryOne($strSQL);

							$message = new stdClass();
			            	$message->from = 'iHealthy';
			            	$message->to = $mobile;
		            		$message->text = "กรุณาเข้าระบบ MemberIn ด้วยเบอร์โทรศัพท์ของท่าน (".$mobile_sms.") เพื่อรับสิทธิ์จาก ".$brand_name." [Download MemberIn app. : goo.gl/5vYxXD]";
			        
				        	$username = 'Jirarak';
				        	$password = 'memberin2017';
				        	$auth = base64_encode("$username:$password");       
				    
				        	$curl = curl_init('api.infobip.com/sms/1/text/single');
				    
				        	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				        	curl_setopt($curl, CURLOPT_HTTPHEADER, [
				            	"Authorization: Basic $auth",
				            	"Content-Type: application/json"
				        	]);

							curl_setopt($curl, CURLOPT_POST, true);
							curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($message));
							    
							$curl_response = curl_exec($curl);
							    
							$http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
							curl_close($curl);
							$decoded = json_decode($curl_response);
							    
							if ($http_status_code != 200) {
							    
							    error_log("SendMessage|".$decoded->requestError->serviceException->messageId.":".$decoded->requestError->serviceException->text."|".date('Y-m-d H:i:s')."\n", 3, 'error.log');
							}
						// }
					}
				}
			}
		}
	
	} elseif ($status_card != "true" || $status_brand != "true") {

		echo "<script type='text/javascript'>
				alert('Member Card ID หรือ Member Brand ID นี้มีคนใช้แล้ว');
				window.history.back();</script>";
	}
}


#  card dropdownlist

// $where_brand = '';

// if($_SESSION['user_type_id_ses']>1){

// 	$where_brand = ' brand_id="'.$_SESSION['user_brand_id'].'" AND card_type_id!="6" AND flag_status="1"';
// }

// $as_card_id = dropdownlist_from_table($oDB,'mi_card','card_id','name',$where_brand,' ORDER BY name ASC');

// $oTmp->assign('card', $as_card_id);


#  branch dropdownlist

$where_brand = '';

if($_SESSION['user_type_id_ses']>1){

	if ($_SESSION['user_type_id_ses']==3) {

		$where_brand = ' branch_id="'.$_SESSION['user_branch_id'].'"';

	} else {

		$where_brand = ' brand_id="'.$_SESSION['user_brand_id'].'" AND flag_status="1"';
	}
}

$as_branch_id = dropdownlist_from_table($oDB,'mi_branch','branch_id','name',$where_brand,' ORDER BY name ASC');

$oTmp->assign('branch', $as_branch_id);


#  brand dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND brand_id="'.$_SESSION['user_brand_id'].'" ';
}

$as_brand_id = dropdownlist_from_table($oDB,'mi_brand','brand_id','name','flag_del=0 '.$where_brand,' ORDER BY name ASC');

$oTmp->assign('brand', $as_brand_id);




$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_transaction');

$oTmp->assign('content_file', 'transaction/register_insert.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());

}

//========================================//

?>

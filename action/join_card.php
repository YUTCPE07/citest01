<html>
	<head>
		<title>.:: MemberIn ::.</title>
		<link rel="shortcut icon" href="www.memberin.com/favicon.ico">

		<script type="text/javascript">

			function part1(info) {
								
				var pass1 = document.form1.pass1.value;
				var pass2 = document.form1.pass2.value;
								
				if (pass1 == ''){

					alert("Please fill in Password.");
					document.getElementById('pass1').focus();  
					return false;

				} else if (pass2 == ''){

					alert("Please fill in Confirm password.");
					document.getElementById('pass2').focus(); 
					return false;

				} else {
												
					if(checkText('Password',pass2) == false){ return false; };								
										
					var agree = confirm('Confrim ?');
									
					if (agree) {
										
						document.form1.submit();
									
					} else {
										
						return false ;
					}
				}
			}


			function checkText() {	
								
				var title = 'Password';
				var text = document.getElementById('pass2').value; 
										
				if((text.length < 8 || text.length > 12)) {
												
					alert('Your '+title+' must be between 8 to 12 chractors');
					return false;
				}	
									
				if ( !text.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/) ) {
									
					alert('Your '+title+' must contain uppercase letter and lowercase letter. (a-Z, A-Z)');
					return false;
				}
									
				if (!text.match(/([0-9])/)) {
									
					alert('Your '+title+' must contain a number. (0-9)');
					return false;
				}	
									
				if(!text.match(/^([a-z0-9\_])+$/i) ) {
									
					alert(title+" ¡ÃÍ¡ä´éà©¾ÒÐ a-Z, A-Z, 0-9 áÅÐ _ (underscore) à·èÒ¹Ñé¹");
					return false;
				}
									
				return true;					
			}	

		</script>

	</head>
	<body>
		<center>
					
<?php

header('Content-Type:text/html; charset=UTF-8');

//========================================//

ini_set("display_errors",1);
error_reporting(1);

//========================================//

include('../include/common_login.php');

//========================================//

$oTmp = new TemplateEngine();

$oDB = new DBI();

if ($bDebug) {

	$oErr = new Tracker();

	$oDB->SetTracker($oErr);

}


# SEARCH MAX MEMBER_ID

	$sql_get_last_ins = 'SELECT max(member_id) FROM mb_member';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################


# SEARCH MAX REGISTER_ID

	$sql_get_last_regis = 'SELECT max(member_register_id) FROM mb_member_register';
	$id_last_regis = $oDB->QueryOne($sql_get_last_regis);
	$id_regis_new = $id_last_regis+1;

#######################################


$token_id = $_REQUEST['token_id'];

$time_insert = date("Y-m-d H:i:s");

$act = $_REQUEST['act'];

if ($token_id) {


	$data_member = "SELECT * FROM mb_member_brand WHERE member_token='".$token_id."'";
	$oRes = $oDB->Query($data_member);
	$axRow = $oRes->FetchRow(DBI_ASSOC);

	$mi_member = "SELECT member_id FROM mb_member WHERE email='".$axRow['email']."' OR mobile='".$axRow['mobile']."'";
	$memberin = $oDB->QueryOne($mi_member);

	$data_brand = "SELECT name,logo_image,code_color,text_color FROM mi_brand WHERE brand_id='".$axRow['brand_id']."'";
	$oRes_brand = $oDB->Query($data_brand);
	$brand = $oRes_brand->FetchRow(DBI_ASSOC);

	$data_card = "SELECT name, image FROM mi_card WHERE card_id='".$axRow['card_id']."'";
	$oRes_card = $oDB->Query($data_card);
	$card = $oRes_card->FetchRow(DBI_ASSOC);


	# CHECK MEMBERIN

	if ($memberin) {

		$sql_mb = "UPDATE mb_member_brand SET member_id='".$memberin."', date_update='".$time_insert."' WHERE member_token='".$token_id."'";
		$oDB->QueryOne($sql_mb);

		$sql_mb = "UPDATE mb_member SET ";

		if ($axRow['name_title_type']) { $sql_mb .= "name_title_type='".$axRow['name_title_type']."',"; }
		if ($axRow['firstname']) { $sql_mb .= "firstname='".$axRow['firstname']."',"; }
		if ($axRow['lastname']) { $sql_mb .= "lastname='".$axRow['lastname']."',"; }
		if ($axRow['nickname']) { $sql_mb .= "nickname='".$axRow['nickname']."',"; }
		if ($axRow['flag_gender']) { $sql_mb .= "flag_gender='".$axRow['flag_gender']."',"; }
		if ($axRow['date_birth']) { $sql_mb .= "date_birth='".$axRow['date_birth']."',"; }
		if ($axRow['flag_marital']) { $sql_mb .= "flag_marital='".$axRow['flag_marital']."',"; }
		if ($axRow['no_of_children']) { $sql_mb .= "no_of_children='".$axRow['no_of_children']."',"; }
		if ($axRow['nationality']) { $sql_mb .= "nationality='".$axRow['nationality']."',"; }
		if ($axRow['idcard_no']) { $sql_mb .= "idcard_no='".$axRow['idcard_no']."',"; }
		if ($axRow['passport_no']) { $sql_mb .= "passport_no='".$axRow['passport_no']."',"; }
		if ($axRow['educate_type']) { $sql_mb .= "educate_type='".$axRow['educate_type']."',"; }
		if ($axRow['interest_activity_type']) { $sql_mb .= "interest_activity_type='".$axRow['interest_activity_type']."',"; }
		if ($axRow['employment_type']) { $sql_mb .= "employment_type='".$axRow['employment_type']."',"; }
		if ($axRow['industry_current_work_type']) { $sql_mb .= "industry_current_work_type='".$axRow['industry_current_work_type']."',"; }
		if ($axRow['primary_work_role_type']) { $sql_mb .= "primary_work_role_type='".$axRow['primary_work_role_type']."',"; }
		if ($axRow['area_work_type']) { $sql_mb .= "area_work_type='".$axRow['area_work_type']."',"; }
		if ($axRow['monthly_personal_income_type']) { $sql_mb .= "monthly_personal_income_type='".$axRow['monthly_personal_income_type']."',"; }
		if ($axRow['monthly_household_income_type']) { $sql_mb .= "monthly_household_income_type='".$axRow['monthly_household_income_type']."',"; }
		if ($axRow['mobile']) { $sql_mb .= "mobile='".$axRow['mobile']."',"; }
		if ($axRow['home_phone']) { $sql_mb .= "home_phone='".$axRow['home_phone']."',"; }
		if ($axRow['work_phone']) { $sql_mb .= "work_phone='".$axRow['work_phone']."',"; }
		if ($axRow['email']) { $sql_mb .= "email='".$axRow['email']."',"; }
		if ($axRow['home_address']) { $sql_mb .= "home_address='".$axRow['home_address']."',"; }
		if ($axRow['home_area']) { $sql_mb .= "home_area='".$axRow['home_area']."',"; }
		if ($axRow['home_room_no']) { $sql_mb .= "home_room_no='".$axRow['home_room_no']."',"; }
		if ($axRow['home_moo']) { $sql_mb .= "home_moo='".$axRow['home_moo']."',"; }
		if ($axRow['home_junction']) { $sql_mb .= "home_junction='".$axRow['home_junction']."',"; }
		if ($axRow['home_soi']) { $sql_mb .= "home_soi='".$axRow['home_soi']."',"; }
		if ($axRow['home_road']) { $sql_mb .= "home_road='".$axRow['home_road']."',"; }
		if ($axRow['home_sub_district']) { $sql_mb .= "home_sub_district='".$axRow['home_sub_district']."',"; }
		if ($axRow['home_district']) { $sql_mb .= "home_district='".$axRow['home_district']."',"; }
		if ($axRow['home_province']) { $sql_mb .= "home_province='".$axRow['home_province']."',"; }
		if ($axRow['home_country']) { $sql_mb .= "home_country='".$axRow['home_country']."',"; }
		if ($axRow['home_postcode']) { $sql_mb .= "home_postcode='".$axRow['home_postcode']."',"; }
		if ($axRow['work_address']) { $sql_mb .= "work_address='".$axRow['work_address']."',"; }
		if ($axRow['work_area']) { $sql_mb .= "work_area='".$axRow['work_area']."',"; }
		if ($axRow['work_room_no']) { $sql_mb .= "work_room_no='".$axRow['work_room_no']."',"; }
		if ($axRow['work_moo']) { $sql_mb .= "work_moo='".$axRow['work_moo']."',"; }
		if ($axRow['work_junction']) { $sql_mb .= "work_junction='".$axRow['work_junction']."',"; }
		if ($axRow['work_soi']) { $sql_mb .= "work_soi='".$axRow['work_soi']."',"; }
		if ($axRow['work_road']) { $sql_mb .= "work_road='".$axRow['work_road']."',"; }
		if ($axRow['work_sub_district']) { $sql_mb .= "work_sub_district='".$axRow['work_sub_district']."',"; }
		if ($axRow['work_district']) { $sql_mb .= "work_district='".$axRow['work_district']."',"; }
		if ($axRow['work_province']) { $sql_mb .= "work_province='".$axRow['work_province']."',"; }
		if ($axRow['work_country']) { $sql_mb .= "work_country='".$axRow['work_country']."',"; }
		if ($axRow['work_postcode']) { $sql_mb .= "work_postcode='".$axRow['work_postcode']."',"; }
		if ($time_insert) { $sql_mb .= "date_update='".$time_insert."',"; }
		if ($time_insert) { $sql_mb .= "date_create='".$time_insert."',"; }

		$oDB->QueryOne($sql_mb);

		$mi_register = "SELECT member_register_id, date_expire
						FROM mb_member_register WHERE member_id='".$memberin."' AND card_id='".$axRow['card_id']."'";

		$oRes_register = $oDB->Query($mi_register);
		$register = $oRes_register->FetchRow(DBI_ASSOC);

		$check_register = $oDB->QueryOne($mi_register);

		if ($check_register) {

			if (strtotime($register['date_exprie']) > strtotime($axRow['date_expried'])) {
				
				$date_expried = $register['date_exprie'];

			} else {
				
				$date_expried = $axRow['date_expried'];
			}

			$sql_mb = "UPDATE mb_member_brand SET member_register_id='".$register['member_register_id']."', date_update='".$time_insert."' WHERE member_token='".$token_id."'";
			$oDB->QueryOne($sql_mb);

			$sql_mr = "UPDATE mb_member_register SET date_exprie='".$date_expried."', date_update='".$time_insert."' WHERE member_register_id='".$register['member_register_id']."'";
			$oDB->QueryOne($sql_mr);
		
		} else {

			member_register($id_regis_new,$memberin,$axRow['card_id'],$axRow['date_expried'],$axRow['platform']);

			$sql_mb = "UPDATE mb_member_brand SET member_register_id='".$id_regis_new."', date_update='".$time_insert."' WHERE member_token='".$token_id."'";

			$oDB->QueryOne($sql_mb);
		}
			
		$HTML = '<table style="margin-bottom:120px;width:800px;" >
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
						<td style="font-size:30px;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;padding-top:55px;" >
							Success
						</td>
					</tr>
					<tr>
						<td></td>
						<td style="font-size:16px;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;padding-bottom:45px; letter-spacing: 1px;line-height:25px;" >
							You get "'.$card['name'].'" from "'.$brand['name'].'"
						</td>
					</tr>
					<tr>
						<td></td>
						<td><a href="../" style="font-size:13px;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;text-decoration:none;background:#'.$brand['code_color'].';color:'.$brand['text_color'].';padding:7px 45px 7px 50px;border-radius:7px;margin-bottom:40px" > MemberIn </a>
								<input name="act" type="hidden" id="act" value="save" />
						</td>
					</tr>
				</table>';

			echo $HTML;
			exit();
	
	} else {


		if ($act == 'save') {

			$pass_1 = md5($_POST['pass1']);
			$pass_2 = md5($_POST['pass2']);

			if(trim($pass_1) != trim($pass_2)){		

				echo "<script>

						alert('Password Not Match!.');
				 		history.back();

					</script>";		

				exit();

			} else {


				$sql_mb = "INSERT INTO mb_member 
							SET member_id='".$id_new."',
							name_title_type='".$axRow['name_title_type']."',
							firstname='".$axRow['firstname']."',
							lastname='".$axRow['lastname']."',
							nickname='".$axRow['nickname']."',
							flag_gender='".$axRow['flag_gender']."',
							date_birth='".$axRow['date_birth']."',
							flag_marital='".$axRow['flag_marital']."',
							no_of_children='".$axRow['no_of_children']."',
							nationality='".$axRow['nationality']."',
							idcard_no='".$axRow['idcard_no']."',
							passport_no='".$axRow['passport_no']."',
							educate_type='".$axRow['educate_type']."',
							interest_activity_type='".$axRow['interest_activity_type']."',
							employment_type='".$axRow['employment_type']."',
							industry_current_work_type='".$axRow['industry_current_work_type']."',
							primary_work_role_type='".$axRow['primary_work_role_type']."',
							area_work_type='".$axRow['area_work_type']."',
							monthly_personal_income_type='".$axRow['monthly_personal_income_type']."',
							monthly_household_income_type='".$axRow['monthly_household_income_type']."',
							mobile='".$axRow['mobile']."',
							home_phone='".$axRow['home_phone']."',
							work_phone='".$axRow['work_phone']."',
							email='".$axRow['email']."',
							home_address='".$axRow['home_address']."',
							home_area='".$axRow['home_area']."',
							home_room_no='".$axRow['home_room_no']."',
							home_moo='".$axRow['home_moo']."',
							home_junction='".$axRow['home_junction']."',
							home_soi='".$axRow['home_soi']."',
							home_road='".$axRow['home_road']."',
							home_sub_district='".$axRow['home_sub_district']."',
							home_district='".$axRow['home_district']."',
							home_province='".$axRow['home_province']."',
							home_country='".$axRow['home_country']."',
							home_postcode='".$axRow['home_postcode']."',
							work_address='".$axRow['work_address']."',
							work_area='".$axRow['work_area']."',
							work_room_no='".$axRow['work_room_no']."',
							work_moo='".$axRow['work_moo']."',
							work_junction='".$axRow['work_junction']."',
							work_soi='".$axRow['work_soi']."',
							work_road='".$axRow['work_road']."',
							work_sub_district='".$axRow['work_sub_district']."',
							work_district='".$axRow['work_district']."',
							work_province='".$axRow['work_province']."',
							work_country='".$axRow['work_country']."',
							work_postcode='".$axRow['work_postcode']."',
							date_update='".$time_insert."',
							date_create='".$time_insert."',
							status='1'";

				$oDB->QueryOne($sql_mb);

				member_register($id_regis_new,$id_new,$axRow['card_id'],$axRow['date_expried'],$axRow['platform']);

				$sql_mb = "UPDATE mb_member_brand SET member_id='".$id_new."', member_register_id='".$id_regis_new."', date_update='".$time_insert."' WHERE member_token='".$token_id."'";


				$oDB->QueryOne($sql_mb);
			


				$HTML = '<html>
						<head>
						<title>.:: MemberIn ::.</title>
						<link rel="shortcut icon" href="www.memberin.com/favicon.ico">
						</head>
						<body>
						<center>
						<table style="margin-bottom:120px;width:800px;" >
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
								<td style="font-size:30px;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;padding-top:55px;" >
									Success
								</td>
							</tr>
							<tr>
								<td></td>
								<td style="font-size:16px;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;padding-bottom:45px; letter-spacing: 1px;line-height:25px;" >
									You get "'.$card['name'].'" from "'.$brand['name'].'"
								</td>
							</tr>
							<tr>
								<td></td>
								<td><a href="../" style="font-size:13px;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;text-decoration:none;background:#'.$brand['code_color'].';color:'.$brand['text_color'].';padding:7px 45px 7px 50px;border-radius:7px;margin-bottom:40px" > MemberIn </a>
								</td>
							</tr>
						</table>
						</center>
						</body>
						</html>';

						echo $HTML;
						exit();
			}
		
		} else {

			# FORM

			$HTML = '<form id="form1" name="form1" method="post" action="">
					<table style="margin-bottom:120px;width:800px;" >
						<tr>
							<td colspan="4" style="background:#'.$brand['code_color'].';color:'.$brand['text_color'].';padding:10px 0px 10px 30px;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;">
								        MemberIn &nbsp; | &nbsp; Join Card
							</td>
						</tr>
						<tr>
							<td></td>
							<td rowspan="4" style="text-align:center" width="330px">
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
							<td style="font-size:17px;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;padding-top:30px;letter-spacing:1px;line-height:25px;" >
								E-mail<br>
		  						<input value="'.$axRow['email'].'" class="form-control text-md" style="height:40px;width:250px;font-size:16px;" disabled/>
							</td>
						</tr>
						<tr>
							<td></td>
							<td style="font-size:17px;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;letter-spacing: 1px;line-height:25px;" >
								Password<br>
		  						<input type="password" maxlength="12" name="pass1" id="pass1" value="" class="form-control text-md" style="height:40px;width:250px;font-size:16px;" required autofocus/>
							</td>
						</tr>
						<tr>
							<td></td>
							<td style="font-size:17px;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;letter-spacing: 1px;line-height:25px;" >
								Confirm Password<br>
		  						<input type="password" maxlength="12" name="pass2" id="pass2" value="" class="form-control text-md" style="height:40px;width:250px;font-size:16px;" required autofocus/>
							</td>
						</tr>
						<tr>
							<td></td>
							<td style="padding-top:30px">
								<input name="act" type="hidden" id="act" value="save" />
								<input name="save" type="button" class="btn btn-submit btn-xs" value="Register" onclick="part1(this.form);" style="font-size:13px;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;text-decoration:none;background:#'.$brand['code_color'].';color:'.$brand['text_color'].';padding:7px 45px 7px 50px;border-radius:7px;margin-bottom:40px" />
							</td>
						</tr>
					</table>';

			echo $HTML;
			exit();
		}
	}

} else {

	echo 'error';
	exit();
}



function member_register($register_id,$member_id,$card_id,$date_expried,$platform) {
	
	$oDB = new DBI();

	$time_insert = date("Y-m-d H:i:s");
	
	$card_sql = "SELECT brand_id, period_type, period_type_other, date_expired 
				FROM mi_card WHERE card_id ='".$card_id."' AND flag_status='1' AND flag_del='0'";

	$oRes_card = $oDB->Query($card_sql);
	$axRow_card = $oRes_card->FetchRow(DBI_ASSOC);

	if ($platform == 'New Member') { $platform = 'import (new member)';
	} else { $platform = 'import (existing member)'; }
	
	if ($axRow_card['period_type']) {
                    
        $period_type = $axRow_card["period_type"];
        $period_type_other = $axRow_card["period_type_other"];
        $date_create = $time_insert;
        $card_expired = $axRow_card["date_expired"];
        $brand_id = $axRow_card["brand_id"];

        switch ($period_type) {
            case '1':
                $card_expired = DateTime::createFromFormat('Y-m-d', $card_expired)->format('Y-m-d');
            	break;
            case '2':
                $card_expired = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($date_create)) . " + ".$period_type_other." Month")); // date + 1 Month
                break;
            case '3':
                $card_expired = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($date_create)) . " + 1 Year")); // date + 1 Year
                break;
            case '4':
                $card_expired = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($date_create)) . " + 1 Year")); // date + 1 Month
                break;
            default:
                exit();
                break;
        }

		if ($period_type=='4') {

			$strSQL = "INSERT INTO mb_member_register(	
							member_register_id,member_id,card_id,
							date_create,date_expire,token_type_id,
							platform,status,bran_BrandID)
						VALUES ('".$register_id."', '".$member_id."', '".$card_id."', '".$date_create."', '".$card_expired."','0', '".$platform."', 'Complete', '".$brand_id."')";

			$sql_mb = "UPDATE mb_member_brand SET date_expired='".$card_expired."' WHERE member_id='".$member_id."' AND card_id='".$card_id."'";

		} else {

			if ($date_expired == '0000-00-00') {

				$strSQL = "INSERT INTO mb_member_register(	
								member_register_id,member_id,card_id,
								date_create,date_expire,token_type_id,
								platform,status,bran_BrandID)
							VALUES ('".$register_id."', '".$member_id."', '".$card_id."', '".$date_create."', '".$card_expired."','0', '".$platform."', 'Complete', '".$brand_id."')";

				$sql_mb = "UPDATE mb_member_brand SET date_expired='".$card_expired."' WHERE member_id='".$member_id."' AND card_id='".$card_id."'";

			} else {

				$strSQL = "INSERT INTO mb_member_register(	
								member_register_id,member_id,card_id,
								date_create,date_expire,token_type_id,
								platform,status,bran_BrandID)
							VALUES ('".$register_id."', '".$member_id."', '".$card_id."', '".$date_create."', '".$date_expired."','0', '".$platform."', 'Complete', '".$brand_id."')";

				$sql_mb = "UPDATE mb_member_brand SET date_expired='".$date_expired."' WHERE member_id='".$member_id."' AND card_id='".$card_id."'";
			}

		}

		$oDB->Query($strSQL);
		$oDB->QueryOne($sql_mb);
	}

	return true;
}



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());

}

//========================================//

?>


		</center>
	</body>
</html>
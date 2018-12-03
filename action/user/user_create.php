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

// if ($_SESSION['role_action']['user']['add'] != 1 || $_SESSION['role_action']['user']['add'] != 1) {
// 	echo "<script> history.back(); </script>";
// 	exit();
// }

//========================================//

# SEARCH MAX ID

	$sql_get_last_ins = 'SELECT max(user_id) FROM mi_user';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################



$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$id_card_format = array('1','4','5','1','2');

$mobile_contact_format = array('3','7');

$time_insert = date("Y-m-d H:i:s");


# CHECK LOGIN

if ($_SESSION['user_type_id_ses'] != 1) {

	if (!$id) {

		echo "<script>history.back();</script>";
		exit();
	}
}


##########################################



	if( $Act == 'edit' && $id != '' ){

		# EDIT

		$sql = '';

		$sql .= 'SELECT a.*, b.*,
				a.user_id AS id_user

				FROM mi_user AS a

  				LEFT JOIN mi_contact AS b

    			ON a.user_id = b.user_id

				WHERE a.user_id = "'.$id.'" ';


		$oRes = $oDB->Query($sql);

		$i=0;

		$asData = array();


		while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

			$i++;

			$axRow['date_expried'] = DateOnly($axRow['date_expried']);


			$last_val_1 = 0;

			for($d=0;$d<count($id_card_format);$d++){

				$axRow['idcard_no_'.($d+1)] = substr($axRow['idcard_no'],$last_val_1,$id_card_format[$d]);

				$last_val_1 += $id_card_format[$d];

			}

			$axRow['mobile'] = substr($axRow['mobile'],3);

			$asData = $axRow;

		}



	} else if($Act == 'save') {

		# USER 

		$user_type_id = trim_txt($_REQUEST['user_type_id']);

		$role_admin = trim_txt($_REQUEST['role_admin']);

		$role_brand = trim_txt($_REQUEST['role_brand']);

		$role_branch = trim_txt($_REQUEST['role_branch']);

		$username = trim_txt($_REQUEST['username']);

		$password = trim_txt($_REQUEST['password']);

		$confirm_password = trim_txt($_REQUEST['confirm_password']);

		$email = trim_txt($_REQUEST['email']);

		$brand_id = trim_txt($_REQUEST['brand_id']);

		$branch_id = trim_txt($_REQUEST['branch_id']);

		$flag_status = trim_txt($_REQUEST['flag_status']);

		$flag_api = trim_txt($_REQUEST['flag_api']);

		$flag_trial = trim_txt($_REQUEST['flag_trial']);

		$ExpiredDate = trim_txt($_REQUEST['ExpiredDate']);

		


		# CONTACT

		$name_title_type = trim_txt($_REQUEST['name_title_type']);

		$firstname = trim_txt($_REQUEST['firstname']);

		$lastname = trim_txt($_REQUEST['lastname']);

		$nickname = trim_txt($_REQUEST['nickname']);

		$gender = trim_txt($_REQUEST['gender']);

		$mobile = trim_txt($_REQUEST['mobile']);


		if($_REQUEST['nationality_sl']==1){

			for($d=1;$d<=count($id_card_format);$d++){

				$idcard_no .= trim_txt($_REQUEST['idcard_no_'.$d]);

			}	

		} else {

			$idcard_no = trim_txt($_REQUEST['id_card_no_txt']);

		}



		$passport_no = trim_txt($_REQUEST['passport_no']);

		$nationality = trim_txt($_REQUEST['nationality_sl']);






		$sql_user = '';

		$sql_contact = '';

		$table_user = 'mi_user';

		$table_contact = 'mi_contact';





		# ACTION MI_USER TABLE


		$sql_user .= 'user_type_id="'.$user_type_id.'"';  

		$sql_user .= ',username="'.$username.'"';   

		$sql_user .= ',email="'.$email.'"';   

		$sql_user .= ',flag_status="'.$flag_status.'"';   

		$sql_user .= ',flag_api="'.$flag_api.'"';  

		$sql_user .= ',date_update="'.$time_insert.'"'; 

		if($user_type_id==1){	

			$brand_id = '';  
			$branch_id='';

			$sql_user .= ',role_RoleID="'.$role_admin.'"';

		} else if($user_type_id==2){

			$sql_user .= ',brand_id="'.$brand_id.'"';
			$sql_user .= ',role_RoleID="'.$role_brand.'"';

			$branch_id='';   

		} else {

			$sql_user .= ',brand_id="'.$brand_id.'"';
			$sql_user .= ',branch_id="'.$branch_id.'"';
			$sql_user .= ',role_RoleID="'.$role_branch.'"';

		}
		

		# TRIAL

		$sql_user .= ',flag_trial="'.$flag_trial.'"';  

		if($flag_trial=="15"){	

			$sql_user .= ',date_expried="'.date("Y-m-d",strtotime("+15 day",strtotime($time_insert))).'"'; 	

		} else if($flag_trial=="30"){	

			$sql_user .= ',date_expried="'.date("Y-m-d",strtotime("+30 day",strtotime($time_insert))).'"';	

		} else if($flag_trial=="Specific"){	

			$sql_user .= ',date_expried="'.$ExpiredDate.'"';	

		} else {	$sql_user .= ',date_expried=""';	}



		# ACTION MI_CONTACT TABLE

		if($name_title_type){	$sql_contact .= 'name_title_type="'.$name_title_type.'"';   }

		if($firstname){	$sql_contact .= ',firstname="'.$firstname.'"';   }

		if($lastname){	$sql_contact .= ',lastname="'.$lastname.'"';   }

		if($nickname){	$sql_contact .= ',nickname="'.$nickname.'"';   }

		if($gender){	$sql_contact .= ',gender="'.$gender.'"';   }

		if($mobile){	$sql_contact .= ',mobile="+66'.$mobile.'"';	}

		if($idcard_no){	$sql_contact .= ',idcard_no="'.$idcard_no.'"';   }

		if($passport_no){	$sql_contact .= ',passport_no="'.$passport_no.'"';   }

		if($nationality){	$sql_contact .= ',nationality="'.$nationality.'"';   }




		# CHECK USER NAME

		if (!$id) {

			# INSERT

			$sql_name = 'SELECT username FROM mi_user WHERE user_id !='.$id_new;

			$oRes = $oDB->Query($sql_name);

			while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

				$string1 = strtolower($axRow['username']);
				$string2 = strtolower($username);

				if ($string1 == $string2) {

					echo "<script>alert('Username Dupplicate.');
					history.back();</script>";
					exit;
				}

			}
		}







		if($id){

			# UPDATE

			$do_sql_user = "UPDATE ".$table_user." SET ".$sql_user." WHERE user_id= '".$id."'";

			$do_sql_contact = 'UPDATE '.$table_contact.' SET '.$sql_contact.' WHERE user_id= "'.$id.'" ';

		} else {

			# INSERT

			# CHECK PASSWORD

			if ($confirm_password != $password) {
				
				echo "<script>alert('Password && Confirm Password Not Match.');
						history.back();</script>";
				exit;

			} else if ($password=='' && $confirm_password==''){

				echo '<script>alert("Please Fill Password.");history.back();</script>';

			} else {

				$sql_user .= ',password="'.md5($password).'"';
			}



			if($time_insert){	$sql_user .= ',date_create="'.$time_insert.'"';   }

			if($id_new){	$sql_user .= ',user_id="'.$id_new.'"';   }

			$do_sql_user = 'INSERT INTO '.$table_user.' SET '.$sql_user;

			$id = $id_new;



			if($id_new){	$sql_contact .= ',user_id="'.$id_new.'"';   }

			$do_sql_contact = 'INSERT INTO '.$table_contact.' SET '.$sql_contact;


		}
		
		$oDB->QueryOne($do_sql_user);

		$oDB->QueryOne($do_sql_contact);

		echo '<script>window.location.href="user.php";</script>';

		exit;

	}




#  user_type dropdownlist

if($id=='' and $_SESSION['user_type_id_ses']==0){

	$as_user_type = dropdownlist_from_table($oDB,'mi_user_type','user_type_id','name','no_priority >"'.$_SESSION['no_priority'].'" ');

} else {

	if($_SESSION['user_type_id_ses']==2 ){

		if($_SESSION['UID']==$id || $asData['user_type_id']==2){

			$as_user_type = dropdownlist_from_table($oDB,'mi_user_type','user_type_id','name','user_type_id=2');

		}

		else{

			$as_user_type = dropdownlist_from_table($oDB,'mi_user_type','user_type_id','name','user_type_id >= 2');

		}

	} else if($_SESSION['user_type_id_ses']==3){

		$as_user_type = dropdownlist_from_table($oDB,'mi_user_type','user_type_id','name','user_type_id=3');

	} else {

		$as_user_type = dropdownlist_from_table($oDB,'mi_user_type','user_type_id','name','');

	}
}



$oTmp->assign('user_type_opt', $as_user_type);



#  brand dropdownlist


if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' brand_id="'.$_SESSION['user_brand_id'].'" ';

}


$as_brand_id = dropdownlist_from_table($oDB,'mi_brand','brand_id','name',$where_brand,' ORDER BY name ASC');

$oTmp->assign('brand_id_opt', $as_brand_id);



#  branch dropdownlist

$as_branch_id = dropdownlist_from_table($oDB,'mi_branch','branch_id','name','branch_id>0 and branch_id="'.$_SESSION['user_brand_id'].'" and brand_id="'.$_SESSION['user_brand_id'].'" ORDER BY name ASC');

$oTmp->assign('branch_id_opt', $as_branch_id);



#  name_title_type dropdownlist

$as_name_title_type = dropdownlist_type_master($oDB,'name_title_type');

$oTmp->assign('name_title_type_opt', $as_name_title_type);



#  role_admin dropdownlist

$as_role_admin = dropdownlist_from_table($oDB,'role','role_RoleID','role_Name','role_Type="Admin"');

$oTmp->assign('role_admin', $as_role_admin);



#  role_brand dropdownlist

$as_role_brand = dropdownlist_from_table($oDB,'role','role_RoleID','role_Name','role_Type="Brand" AND role_RoleID!="4"');

$oTmp->assign('role_brand', $as_role_brand);



#  role_branch dropdownlist

$as_role_branch = dropdownlist_from_table($oDB,'role','role_RoleID','role_Name','role_Type="Branch"');

$oTmp->assign('role_branch', $as_role_branch);





$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_user');

$oTmp->assign('content_file', 'user/user_create.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
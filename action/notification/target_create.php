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

if (($_SESSION['role_action']['push_notification']['add'] != 1) || ($_SESSION['role_action']['push_notification']['edit'] != 1)) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");
$time_pic = date("Ymd_His");
$Act = $_REQUEST['act'];
$id = $_REQUEST['id'];

# SEARCH MAX TARGET ID

	$sql_get_last_ins = 'SELECT max(tali_TargetListID) FROM target_list';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################

# SEARCH MAX TARGET VIEW ID

	$sql_get_last_ins = 'SELECT max(tavi_TargetViewID) FROM target_view';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_view = $id_last_ins+1;

#######################################


if( $Act == 'edit' && $id != '' ){

	# EDIT

	$sql = 'SELECT * FROM target_list WHERE tali_TargetListID ='.$id;
	$oRes = $oDB->Query($sql);
	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		if ($axRow['bran_BrandID']==0) { $axRow['bran_BrandID'] = 'All'; }
		
		$axRow['age_1'] = "";
		$axRow['age_2'] = "";

		if ($axRow['tali_SQLView']=='member_profile_view') {

			$sql = 'SELECT tavi_ID 
					FROM target_view 
					WHERE tali_TargetListID ="'.$id.'"
					AND tavi_Type="Age"';
			$member_age = $oDB->QueryOne($sql);

			if ($member_age == 'All') {

				$axRow['age_1'] = "All";

			} elseif ($member_age) {

		        $age_basic = $member_age;
		        $token = strtok($age_basic,",");
		     	$axRow['age_1'] = $token;
		     	$token = strtok (",");
		     	$axRow['age_2'] = $token;
			}
		}

		$asData = $axRow;
	}

} else if( $Act == 'save' ){

	# SAVE

	$bran_BrandID = trim_txt($_REQUEST['bran_BrandID']);
	$tali_Name = trim_txt($_REQUEST['tali_Name']);
	$tali_Description = trim_txt($_REQUEST['tali_Description']);
	$tali_SQLView = trim_txt($_REQUEST['tali_SQLView']);



	$sql_target = '';

	$table_target = 'target_list';


	
	if($bran_BrandID){	

		if ($bran_BrandID == 'All') { $bran_BrandID = 0; }

		$sql_target .= 'bran_BrandID="'.$bran_BrandID.'"';   
	}
	
	if($tali_Name){	$sql_target .= ',tali_Name="'.$tali_Name.'"';   }
	
	if($tali_SQLView){	$sql_target .= ',tali_SQLView="'.$tali_SQLView.'"';   }
	
	$sql_target .= ',tali_Description="'.$tali_Description.'"'; 

	$sql_target .= ',tali_UpdatedDate="'.$time_insert.'"';   

	$sql_target .= ',tali_UpdatedBy="'.$_SESSION['UID'].'"'; 


	# TARGET LIST

	if ($id) {

		# UPDATE

		$do_sql_target = 'UPDATE '.$table_target.' SET '.$sql_target.' WHERE tali_TargetListID="'.$id.'"';

	} else {

		# INSERT

		$sql_target .= ',tali_CreatedDate="'.$time_insert.'"';   
		$sql_target .= ',tali_CreatedBy="'.$_SESSION['UID'].'"';  
		if($id_new){	$sql_target .= ',tali_TargetListID="'.$id_new.'"';   }  

		$do_sql_target = 'INSERT INTO '.$table_target.' SET '.$sql_target;

		$id = $id_new;
	}

	$oDB->QueryOne($do_sql_target);	



	# TARGET VIEW

	$targer_data = "";
	$privilege_data = "";
	$coupon_data = "";
	$birthday_data = "";
	$activity_data = "";
	$x = 0;
	$c = 0;
	$a = 0;

	if ($tali_SQLView == 'promotion_view') {

		if ($bran_BrandID == 0) { $where_brand = ''; } 
		else { $where_brand = 'AND bran_BrandID="'.$bran_BrandID.'"';}

		$sql_promotion = 'SELECT coup_CouponID AS id FROM hilight_coupon WHERE coup_Deleted="" '.$where_brand;
		$oRes_pro = $oDB->Query($sql_promotion);

		while ($axRow_pro = $oRes_pro->FetchRow(DBI_ASSOC)) {

			if ($_POST[$axRow_pro['id']]) { 

				$status = ""; 

				if ($x == 0) { $target_data .= $axRow_pro['id']; } 
				else { $target_data .= ','.$axRow_pro['id']; }

				$x++;

			} else { $status = "T"; }

			$sql_check = 'SELECT tavi_TargetViewID 
							FROM target_view 
							WHERE tali_TargetListID="'.$id.'"
							AND tavi_ID="'.$axRow_pro['id'].'"
							AND tavi_Type="Promotion"';
			$target_id = $oDB->QueryOne($sql_check);

			if ($target_id) {

				$do_sql_view = 'UPDATE target_view 
								SET tavi_ID="'.$axRow_pro['id'].'",
								tavi_Type="Promotion",
								tavi_Deleted="'.$status.'"
								WHERE tavi_TargetViewID="'.$target_id.'"';

			} else {

				$do_sql_view = 'INSERT INTO target_view 
								SET tavi_ID="'.$axRow_pro['id'].'",
								tavi_Type="Promotion",
								tali_TargetListID="'.$id.'",
								tavi_Deleted="'.$status.'",
								tavi_TargetViewID="'.$id_view.'"';

				$id_view++;
			}
			
			$oDB->QueryOne($do_sql_view);
		}

		if ($target_data) {

			$sql_data = 'SELECT DISTINCT mobile_token, member_id 
							FROM promotion_view 
							WHERE hico_HilightCouponID IN ('.$target_data.')';
		}

	} elseif ($tali_SQLView == 'checkin_view') {

		if ($bran_BrandID == 0) { $where_brand = ''; } 
		else { $where_brand = 'AND brand_id="'.$bran_BrandID.'"';}

		$sql_card = 'SELECT card_id AS id FROM mi_card WHERE flag_del="" '.$where_brand;
		$oRes_card = $oDB->Query($sql_card);

		while ($axRow_card = $oRes_card->FetchRow(DBI_ASSOC)) {

			if ($_POST[$axRow_card['id']]) { 

				$status = ""; 

				if ($x == 0) { $target_data .= $axRow_card['id']; } 
				else { $target_data .= ','.$axRow_card['id']; }

				$x++;

			} else { $status = "T"; }

			$sql_check = 'SELECT tavi_TargetViewID 
							FROM target_view 
							WHERE tali_TargetListID="'.$id.'"
							AND tavi_ID="'.$axRow_card['id'].'"
							AND tavi_Type="Card"';
			$target_id = $oDB->QueryOne($sql_check);

			if ($target_id) {

				$do_sql_view = 'UPDATE target_view 
								SET tavi_ID="'.$axRow_card['id'].'",
								tavi_Type="Card",
								tavi_Deleted="'.$status.'"
								WHERE tavi_TargetViewID="'.$target_id.'"';

			} else {

				$do_sql_view = 'INSERT INTO target_view 
								SET tavi_ID="'.$axRow_card['id'].'",
								tavi_Type="Card",
								tali_TargetListID="'.$id.'",
								tavi_Deleted="'.$status.'",
								tavi_TargetViewID="'.$id_view.'"';

				$id_view++;
			}
			
			$oDB->QueryOne($do_sql_view);
		}

		if ($target_data) {

			$sql_data = 'SELECT DISTINCT mobile_token, member_id 
							FROM checkin_view 
							WHERE card_CardID IN ('.$target_data.')';
		}

	} elseif ($tali_SQLView == 'privilege_view') {

		if ($bran_BrandID == 0) { $where_brand = ''; } 
		else { $where_brand = 'AND bran_BrandID="'.$bran_BrandID.'"';}

		$sql_privilege = 'SELECT priv_PrivilegeID AS id FROM privilege WHERE priv_Deleted="" '.$where_brand;
		$oRes_priv = $oDB->Query($sql_privilege);

		$sql_coupon = 'SELECT coup_CouponID AS id FROM coupon WHERE coup_Deleted="" AND coup_Birthday="" '.$where_brand;
		$oRes_coup = $oDB->Query($sql_coupon);

		$sql_hbd = 'SELECT coup_CouponID AS id FROM coupon WHERE coup_Deleted="" AND coup_Birthday="T" '.$where_brand;
		$oRes_hbd = $oDB->Query($sql_hbd);

		$sql_activity = 'SELECT acti_ActivityID AS id FROM activity WHERE acti_Deleted="" '.$where_brand;
		$oRes_acti = $oDB->Query($sql_activity);

		# PRIVILEGE

		while ($axRow_priv = $oRes_priv->FetchRow(DBI_ASSOC)) {

			if ($_POST['p'.$axRow_priv['id']]) { 

				$status = ""; 

				if ($p == 0) { $privilege_data .= $axRow_priv['id']; } 
				else { $privilege_data .= ','.$axRow_priv['id']; }

				$p++;

			} else { $status = "T"; }

			$sql_check = 'SELECT tavi_TargetViewID 
							FROM target_view 
							WHERE tali_TargetListID="'.$id.'"
							AND tavi_ID="'.$axRow_priv['id'].'"
							AND tavi_Type="Privilege"';
			$target_id = $oDB->QueryOne($sql_check);

			if ($target_id) {

				$do_sql_view = 'UPDATE target_view 
								SET tavi_ID="'.$axRow_priv['id'].'",
								tavi_Type="Privilege",
								tavi_Deleted="'.$status.'"
								WHERE tavi_TargetViewID="'.$target_id.'"';

			} else {

				$do_sql_view = 'INSERT INTO target_view 
								SET tavi_ID="'.$axRow_priv['id'].'",
								tavi_Type="Privilege",
								tali_TargetListID="'.$id.'",
								tavi_Deleted="'.$status.'",
								tavi_TargetViewID="'.$id_view.'"';

				$id_view++;
			}
			
			$oDB->QueryOne($do_sql_view);
		}

		# COUPON

		while ($axRow_coup = $oRes_coup->FetchRow(DBI_ASSOC)) {

			if ($_POST['c'.$axRow_coup['id']]) { 

				$status = ""; 

				if ($c == 0) { $coupon_data .= $axRow_coup['id']; } 
				else { $coupon_data .= ','.$axRow_coup['id']; }

				$c++;

			} else { $status = "T"; }

			$sql_check = 'SELECT tavi_TargetViewID 
							FROM target_view 
							WHERE tali_TargetListID="'.$id.'"
							AND tavi_ID="'.$axRow_coup['id'].'"
							AND tavi_Type="Coupon"';
			$target_id = $oDB->QueryOne($sql_check);

			if ($target_id) {

				$do_sql_view = 'UPDATE target_view 
								SET tavi_ID="'.$axRow_coup['id'].'",
								tavi_Type="Coupon",
								tavi_Deleted="'.$status.'"
								WHERE tavi_TargetViewID="'.$target_id.'"';

			} else {

				$do_sql_view = 'INSERT INTO target_view 
								SET tavi_ID="'.$axRow_coup['id'].'",
								tavi_Type="Coupon",
								tali_TargetListID="'.$id.'",
								tavi_Deleted="'.$status.'",
								tavi_TargetViewID="'.$id_view.'"';

				$id_view++;
			}
			
			$oDB->QueryOne($do_sql_view);
		}

		# HBD

		while ($axRow_hbd = $oRes_hbd->FetchRow(DBI_ASSOC)) {

			if ($_POST['b'.$axRow_hbd['id']]) { 

				$status = ""; 

				if ($c == 0) { $coupon_data .= $axRow_hbd['id']; } 
				else { $coupon_data .= ','.$axRow_hbd['id']; }

				$c++;

			} else { $status = "T"; }

			$sql_check = 'SELECT tavi_TargetViewID 
							FROM target_view 
							WHERE tali_TargetListID="'.$id.'"
							AND tavi_ID="'.$axRow_hbd['id'].'"
							AND tavi_Type="Birthday"';
			$target_id = $oDB->QueryOne($sql_check);

			if ($target_id) {

				$do_sql_view = 'UPDATE target_view 
								SET tavi_ID="'.$axRow_hbd['id'].'",
								tavi_Type="Birthday",
								tavi_Deleted="'.$status.'"
								WHERE tavi_TargetViewID="'.$target_id.'"';

			} else {

				$do_sql_view = 'INSERT INTO target_view 
								SET tavi_ID="'.$axRow_hbd['id'].'",
								tavi_Type="Birthday",
								tali_TargetListID="'.$id.'",
								tavi_Deleted="'.$status.'",
								tavi_TargetViewID="'.$id_view.'"';

				$id_view++;
			}
			
			$oDB->QueryOne($do_sql_view);
		}

		# ACTIVITY

		while ($axRow_acti = $oRes_acti->FetchRow(DBI_ASSOC)) {

			if ($_POST['a'.$axRow_acti['id']]) { 

				$status = ""; 

				if ($a == 0) { $activity_data .= $axRow_acti['id']; } 
				else { $activity_data .= ','.$axRow_acti['id']; }

				$a++;

			} else { $status = "T"; }

			$sql_check = 'SELECT tavi_TargetViewID 
							FROM target_view 
							WHERE tali_TargetListID="'.$id.'"
							AND tavi_ID="'.$axRow_acti['id'].'"
							AND tavi_Type="Activity"';
			$target_id = $oDB->QueryOne($sql_check);

			if ($target_id) {

				$do_sql_view = 'UPDATE target_view 
								SET tavi_ID="'.$axRow_acti['id'].'",
								tavi_Type="Activity",
								tavi_Deleted="'.$status.'"
								WHERE tavi_TargetViewID="'.$target_id.'"';

			} else {

				$do_sql_view = 'INSERT INTO target_view 
								SET tavi_ID="'.$axRow_acti['id'].'",
								tavi_Type="Activity",
								tali_TargetListID="'.$id.'",
								tavi_Deleted="'.$status.'",
								tavi_TargetViewID="'.$id_view.'"';

				$id_view++;
			}
			
			$oDB->QueryOne($do_sql_view);
		}

		$j = 0;
		$where_all = '';

		if ($privilege_data || $coupon_data || $birthday_data || $activity_data) {

			if ($privilege_data) {

				if ($j==0) {

					$where_all = "(privilege_id IN (".$privilege_data.") AND type='Privilege')";


				} else {

					$where_all = " AND (privilege_id IN (".$privilege_data.") AND type='Privilege')";
				}

				$j++;
			}

			if ($coupon_data) { 

				if ($j==0) {

					$where_all = "(privilege_id IN (".$coupon_data.") AND type='Coupon')";


				} else {

					$where_all = " AND (privilege_id IN (".$coupon_data.") AND type='Coupon')";
				}

				$j++;
			}

			if ($activity_data) { 

				if ($j==0) {

					$where_all = "(privilege_id IN (".$coupon_data.") AND type='Activity')";


				} else {

					$where_all = " AND (privilege_id IN (".$coupon_data.") AND type='Activity')";
				}

				$j++;
			}

			$sql_data = 'SELECT DISTINCT mobile_token, member_id 
							FROM privilege_view 
							WHERE '.$where_all;
		}

	} elseif ($tali_SQLView == 'redeem_view') {

		if ($bran_BrandID == 0) { $where_brand = ''; } 
		else { $where_brand = 'AND bran_BrandID="'.$bran_BrandID.'"';}

		$sql_reward = 'SELECT rewa_RewardID AS id FROM reward WHERE rewa_Deleted="" '.$where_brand;
		$oRes_reward = $oDB->Query($sql_reward);

		while ($axRow_reward = $oRes_reward->FetchRow(DBI_ASSOC)) {

			if ($_POST[$axRow_reward['id']]) { 

				$status = ""; 

				if ($x == 0) { $target_data .= $axRow_reward['id']; } 
				else { $target_data .= ','.$axRow_reward['id']; }

				$x++;

			} else { $status = "T"; }

			$sql_check = 'SELECT tavi_TargetViewID 
							FROM target_view 
							WHERE tali_TargetListID="'.$id.'"
							AND tavi_ID="'.$axRow_reward['id'].'"
							AND tavi_Type="Reward"';
			$target_id = $oDB->QueryOne($sql_check);

			if ($target_id) {

				$do_sql_view = 'UPDATE target_view 
								SET tavi_ID="'.$axRow_reward['id'].'",
								tavi_Type="Reward",
								tavi_Deleted="'.$status.'"
								WHERE tavi_TargetViewID="'.$target_id.'"';

			} else {

				$do_sql_view = 'INSERT INTO target_view 
								SET tavi_ID="'.$axRow_reward['id'].'",
								tavi_Type="Reward",
								tali_TargetListID="'.$id.'",
								tavi_Deleted="'.$status.'",
								tavi_TargetViewID="'.$id_view.'"';

				$id_view++;
			}
			
			$oDB->QueryOne($do_sql_view);
		}

		if ($target_data) {

			$sql_data = 'SELECT DISTINCT mobile_token, member_id 
							FROM redeem_view 
							WHERE reward_id IN ('.$target_data.')';
		}

	} elseif ($tali_SQLView == 'member_profile_view') {

		# BRAND
		
		$where_brand = "";

		if ($bran_BrandID != 0) { $where_brand = "brand_id=".$bran_BrandID; }

		# GENDER

		$target_gender = trim_txt($_REQUEST['target_gender']);

		if ($target_gender == 'All') { $where_gender = ""; } 
		elseif ($target_gender == 'Male') { $where_gender = "member_gender='Male'"; } 
		elseif ($target_gender == 'Female') { $where_gender = "member_gender='Female'"; }

		$sql_check = 'SELECT tavi_TargetViewID 
						FROM target_view 
						WHERE tali_TargetListID="'.$id.'"
						AND tavi_Type="Gender"';
		$target_id = $oDB->QueryOne($sql_check);

		if ($target_id) {

			$do_sql_view = 'UPDATE target_view 
							SET tavi_ID="'.$target_gender.'",
							tavi_Type="Gender",
							tavi_Deleted=""
							WHERE tavi_TargetViewID="'.$target_id.'"';
		} else {

			$do_sql_view = 'INSERT INTO target_view 
							SET tavi_ID="'.$target_gender.'",
							tavi_Type="Gender",
							tali_TargetListID="'.$id.'",
							tavi_Deleted="",
							tavi_TargetViewID="'.$id_view.'"';
			$id_view++;
		}
			
		$oDB->QueryOne($do_sql_view);

		# AGE

		$age_basic1 = trim_txt($_REQUEST['age_basic1']);
		$age_basic2 = trim_txt($_REQUEST['age_basic2']);

		$target_age = $age_basic1.','.$age_basic2;

		if ($age_basic1 == 'All') { 

			$where_age = '';
			$target_age = 'All';

		} else { 

			$sql_age_basic1 = 'SELECT mata_NameEn FROM master_target WHERE mata_MasterTargetID="'.$age_basic1.'"';
			$age_basic1 = $oDB->QueryOne($sql_age_basic1);

			$sql_age_basic2 = 'SELECT mata_NameEn FROM master_target WHERE mata_MasterTargetID="'.$age_basic2.'"';
			$age_basic2 = $oDB->QueryOne($sql_age_basic2);

			if ($age_basic1 == 'Under 5') { 

				if ($age_basic2) { 

					$where_age = 'member_age BETWEEN 0 AND '.$age_basic2; 

				} else { $where_age = 'member_age < 5'; }
			
			} elseif ($age_basic1 == 'Over 85') {

				$where_age = 'member_age > 85';
			
			} elseif ($age_basic1 && $age_basic2) {

				$where_age = 'member_age BETWEEN '.$age_basic1.' AND '.$age_basic2;
			
			} elseif ($age_basic1) {

				$where_age = 'member_age = '.$age_basic1;
			}
		}

		$sql_check = 'SELECT tavi_TargetViewID 
						FROM target_view 
						WHERE tali_TargetListID="'.$id.'"
						AND tavi_Type="Age"';
		$target_id = $oDB->QueryOne($sql_check);

		if ($target_id) {

			$do_sql_view = 'UPDATE target_view 
							SET tavi_ID="'.$target_age.'",
							tavi_Type="Age",
							tavi_Deleted=""
							WHERE tavi_TargetViewID="'.$target_id.'"';
		} else {

			$do_sql_view = 'INSERT INTO target_view 
							SET tavi_ID="'.$target_age.'",
							tavi_Type="Age",
							tali_TargetListID="'.$id.'",
							tavi_Deleted="",
							tavi_TargetViewID="'.$id_view.'"';
			$id_view++;
		}
			
		$oDB->QueryOne($do_sql_view);

		$sql_data = 'SELECT DISTINCT mobile_token, member_id 
						FROM member_profile_view 
						WHERE 1 ';

		if ($where_brand) { $sql_data .= 'AND '.$where_brand.' '; }
		if ($where_age) { $sql_data .= 'AND '.$where_age.' '; }
		if ($where_gender) { $sql_data .= 'AND '.$where_gender.' '; }
	}

	$sql_data .= " AND mobile_token!=''";

	# UPDATE SQL TEXT

	$do_sqlview = 'UPDATE target_list SET tali_SQLText = "'.$sql_data.'" WHERE tali_TargetListID = "'.$id.'"';
	$oDB->QueryOne($do_sqlview);

	echo '<script> window.location.href="target_page.php"; </script>';

	exit;
}




#  brand dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' brand_id="'.$_SESSION['user_brand_id'].'" ';
}

$as_brand = dropdownlist_from_table($oDB,'mi_brand','brand_id','name',$where_brand,' ORDER BY name ASC');

$oTmp->assign('brand_opt', $as_brand);



$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('type_file_upload', '<span class="text-rq">Type file : .jpg , .png , .gif only</span>');

$oTmp->assign('content_file', 'notification/target_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
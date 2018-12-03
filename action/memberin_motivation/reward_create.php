<?php

header('Content-Type:text/html; charset=UTF-8');

//========================================//

ini_set("display_errors",1);
error_reporting(1);

//========================================//

include('../../include/common.php');
include('../../lib/function_normal.php');
include('../../include/common_check.php');
include('../../lib/phpqrcode/qrlib.php');
require_once('../../include/connect.php');

//========================================//

$oTmp = new TemplateEngine();
$oDB = new DBI();

if ($bDebug) {

	$oErr = new Tracker();
	$oDB->SetTracker($oErr);
}

//========================================//

if (($_SESSION['role_action']['rewards']['add'] != 1) || ($_SESSION['role_action']['rewards']['edit'] != 1)) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$time_insert = date("Y-m-d H:i:s");

$time_pic = date("Ymd_His");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];


# SEARCH MAX REWARD ID

	$sql_get_last_ins = 'SELECT max(rewa_RewardID) FROM reward';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$reward_id_new = $id_last_ins+1;

#######################################

# SEARCH NAME IMAGE

	$sql_get_old_img = 'SELECT rewa_Image FROM reward WHERE rewa_RewardID='.$id;
	$get_old_img = $oDB->QueryOne($sql_get_old_img);
	$old_image = $get_old_img;

#######################################


if ($Act == 'edit' && $id != '' ){

	$sql = 'SELECT *
  			FROM reward

  			LEFT JOIN mi_tg_activity
  			ON rewa_Category = id_activity

			WHERE rewa_RewardID = "'.$id.'" ';

	$oRes = $oDB->Query($sql);

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

        $data_age = $axRow['rewa_Age'];
        $token = strtok($data_age,",");
     	$axRow['age_1'] = $token;
     	$token = strtok (",");
     	$axRow['age_2'] = $token;
		$asData = $axRow;


		# LOCATION

		$data_branch = "";

		if ($axRow['brnc_BranchID']) {

			$token = strtok($axRow['brnc_BranchID'] , ",");
			$branch = array();
			$i = 0;
			while ($token !== false) {

	    		$branch[$i] =  $token;
	    		$token = strtok(",");
	    		$i++;
			}

			$arrlength = count($branch);

			for($x = 0; $x < $arrlength; $x++) {

				$sql_branch = 'SELECT name FROM mi_branch WHERE branch_id = "'.$branch[$x].'"';
				$name = $oDB->QueryOne($sql_branch);

				$data_branch .= '<tr>
									<td style="text-align:center">'.$name.'</td>
									<td style="text-align:center"><img src="../../upload/'.$axRow['rewa_QrPath'].'QRW-'.str_pad($id,4,"0",STR_PAD_LEFT).'-'.str_pad($branch[$x],4,"0",STR_PAD_LEFT).'.png" width="50" height="50" class="image_border"/></td>
									<td style="text-align:center"><a target="_blank" href="reward_qrcode.php?id='.$id.'&branch='.$branch[$x].'">QRCode Link</td>
									</tr>';
			}

		} else {

			$data_branch = '<tr><td colspan="3" style="text-align:center">No Branch Data</td></tr>';
		}

		$oTmp->assign('branch_data', $data_branch);



		# STATUS

		$sql_rede = 'SELECT rede_RewardRedeemID 
						FROM reward_redeem 
						WHERE rewa_RewardID="'.$axRow['rewa_RewardID'].'"
						AND rede_Status="Active"';

		$check_rede = $oDB->QueryOne($sql_rede);

		$status= 'true';

		if ($check_rede) { $status = 'false'; }
		else { $status = 'true'; }

		$oTmp->assign('status_check', $status);

	}

} else if($Act == 'save') {

	$rewa_Name = trim_txt($_REQUEST['rewa_Name']);

	$bran_BrandID = trim_txt($_REQUEST['bran_BrandID']);

	$rewa_DisplayData = trim_txt($_REQUEST['rewa_DisplayData']);

	$vaca_VarietyCategoryID = trim_txt($_REQUEST['vaca_VarietyCategoryID']);

	$rewa_Status = trim_txt($_REQUEST['rewa_Status']);

	$rewa_Description = trim_txt($_REQUEST['rewa_Description']);

	$rewa_Price = trim_txt($_REQUEST['rewa_Price']);

	$rewa_Cost = trim_txt($_REQUEST['rewa_Cost']);

	$rewa_Category = trim_txt($_REQUEST['rewa_Category']);

	$rewa_Type = trim_txt($_REQUEST['rewa_Type']);

	$rewa_DiscountType = trim_txt($_REQUEST['rewa_DiscountType']);

	$rewa_Discount = trim_txt($_REQUEST['rewa_Discount']);

	$rewa_MinPay = trim_txt($_REQUEST['rewa_MinPay']);

	$rewa_MaxPay = trim_txt($_REQUEST['rewa_MaxPay']);

	$card_CardID = trim_txt($_REQUEST['card_CardID']);

	$rewa_AutoReward = trim_txt($_REQUEST['rewa_AutoReward']);

	$rewa_UOM = trim_txt($_REQUEST['rewa_UOM']);

	$rewa_Limit = trim_txt($_REQUEST['rewa_Limit']);

	$rewa_Qty = trim_txt($_REQUEST['rewa_Qty']);

	$Gender = trim_txt($_REQUEST['Gender']);

	$rewa_Gender = trim_txt($_REQUEST['rewa_Gender']);

	$Marital = trim_txt($_REQUEST['Marital']);

	$rewa_Marital = trim_txt($_REQUEST['rewa_Marital']);

	$Education = trim_txt($_REQUEST['Education']);

	$rewa_Education = trim_txt($_REQUEST['rewa_Education']);

	$Activity = trim_txt($_REQUEST['Activity']);

	$rewa_Activity = trim_txt($_REQUEST['rewa_Activity']);

	$MonthlyPersonalIncome = trim_txt($_REQUEST['MonthlyPersonalIncome']);

	$rewa_MonthlyPersonalIncome = trim_txt($_REQUEST['rewa_MonthlyPersonalIncome']);

	$Province = trim_txt($_REQUEST['Province']);

	$rewa_Province = trim_txt($_REQUEST['rewa_Province']);

	$Age = trim_txt($_REQUEST['Age']);

	$age1 = $_REQUEST['age_dp1'];

	$age2 = $_REQUEST['age_dp2'];

	$rewa_Age = $age1.",".$age2;


	// $branch_data = "";

	// foreach ($_POST['brnc_BranchID'] as $branch_id) {

	// 	$branch_data .= $branch_id.",";

	// 	if ($id) { 

	// 		$qrcode .= 'QRW-'.str_pad($id,4,"0",STR_PAD_LEFT).'-'.str_pad($branch_id,4,"0",STR_PAD_LEFT).",";

	// 	} else { 

	// 		$qrcode .= 'QRW-'.str_pad($id_new,4,"0",STR_PAD_LEFT).'-'.str_pad($branch_id,4,"0",STR_PAD_LEFT).","; 
	// 	}
	// }

	// $str_branch = strlen($branch_data);

	// $branch_data = substr($branch_data,0,$str_branch-1);

	// $qrcode = substr($qrcode,0,$str_branch-1);



	$sql_reward = '';

	$table_reward = 'reward';



	if($rewa_Name){	$sql_reward .= 'rewa_Name="'.$rewa_Name.'"';   }

	if($rewa_Status){	$sql_reward .= ',rewa_Status="'.$rewa_Status.'"';   }

	if($bran_BrandID){	$sql_reward .= ',bran_BrandID="'.$bran_BrandID.'"';   }

	if($vaca_VarietyCategoryID){	$sql_reward .= ',vaca_VarietyCategoryID="'.$vaca_VarietyCategoryID.'"';   }

	if($rewa_DisplayData){	$sql_reward .= ',rewa_DisplayData="'.$rewa_DisplayData.'"';   }

	// if($branch_data){	$sql_reward .= ',brnc_BranchID="'.$branch_data.'"';   }

	if($rewa_Category){	$sql_reward .= ',rewa_Category="'.$rewa_Category.'"';   }

	if($time_insert){	$sql_reward .= ',rewa_UpdatedDate="'.$time_insert.'"';   }

	if($_SESSION['UID']){	$sql_reward .= ',rewa_UpdatedBy="'.$_SESSION['UID'].'"';   }

	$sql_reward .= ',rewa_Description="'.$rewa_Description.'"';  

	if($rewa_Price){	$sql_reward .= ',rewa_Price="'.$rewa_Price.'"';   }

	if($rewa_Cost){	$sql_reward .= ',rewa_Cost="'.$rewa_Cost.'"';   }

	if($rewa_Type == 'Card'){	

		$sql_reward .= ',rewa_Type="'.$rewa_Type.'"';

		$sql_reward .= ',card_CardID="'.$card_CardID.'"';

		$sql_reward .= ',rewa_AutoReward="'.$rewa_AutoReward.'"';

		$sql_reward .= ',rewa_DiscountType=""';

		$sql_reward .= ',rewa_Discount=""';

		$sql_reward .= ',rewa_MinPay=""';

		$sql_reward .= ',rewa_MaxPay=""';

	} else if($rewa_Type == 'Discount'){	

		$sql_reward .= ',rewa_Type="'.$rewa_Type.'"';

		$sql_reward .= ',rewa_DiscountType="'.$rewa_DiscountType.'"';

		$sql_reward .= ',rewa_Discount="'.$rewa_Discount.'"';

		$sql_reward .= ',rewa_MinPay="'.$rewa_MinPay.'"';

		$sql_reward .= ',rewa_MaxPay="'.$rewa_MaxPay.'"';

	} else {

		$sql_reward .= ',rewa_Type="'.$rewa_Type.'"';

		$sql_reward .= ',rewa_DiscountType=""';

		$sql_reward .= ',rewa_Discount=""';

		$sql_reward .= ',rewa_MinPay=""';

		$sql_reward .= ',rewa_MaxPay=""';
	}

	if($rewa_UOM){	$sql_reward .= ',rewa_UOM="'.$rewa_UOM.'"';   }

	if($rewa_Limit){	$sql_reward .= ',rewa_Limit="'.$rewa_Limit.'"';   }

	if($rewa_Qty){	$sql_reward .= ',rewa_Qty="'.$rewa_Qty.'"';   }

	$sql_reward .= ',rewa_ImagePath="'.$bran_BrandID.'/reward_upload/"'; 

	$sql_reward .= ',rewa_QrPath="'.$bran_BrandID.'/qr_reward_upload/"';

	if( $_FILES["reward_image_upload"]["name"] != ""){

		$new_img_name = upload_img('reward_image_upload','rewrd_'.$time_pic,'../../upload/'.$bran_BrandID.'/reward_upload/',400,400);

		if($new_img_name){	

			$sql_reward .= ',rewa_Image="'.$new_img_name.'"';

			unlink_file($oDB,reward,'rewa_Image','rewa_RewardID',$id,'../../upload/'.$bran_BrandID.'/reward_upload/',$old_image);
		}
	}

	if($Age){	$sql_reward .= ',rewa_Age="'.$rewa_Age.'"';   }
	else {	$sql_reward .= ',rewa_Age=""';	}

	if($Gender){	$sql_reward .= ',rewa_Gender="'.$rewa_Gender.'"';   }
	else {	$sql_reward .= ',rewa_Gender=""';	}

	if($Marital){	$sql_reward .= ',rewa_Marital="'.$rewa_Marital.'"';   }
	else {	$sql_reward .= ',rewa_Marital=""';	}

	if($Education){	$sql_reward .= ',rewa_Education="'.$rewa_Education.'"';   }
	else {	$sql_reward .= ',rewa_Education=""';	}

	if($Activity){	$sql_reward .= ',rewa_Activity="'.$rewa_Activity.'"';   }
	else {	$sql_reward .= ',rewa_Activity=""';	}

	if($MonthlyPersonalIncome){	$sql_reward .= ',rewa_MonthlyPersonalIncome="'.$rewa_MonthlyPersonalIncome.'"';   }
	else {	$sql_reward .= ',rewa_MonthlyPersonalIncome=""';	}

	if($Province){	$sql_reward .= ',rewa_Province="'.$rewa_Province.'"';   }
	else {	$sql_reward .= ',rewa_Province=""';	}


	if ($id) {

		# UPDATE

		$do_sql_reward = "UPDATE ".$table_reward." SET ".$sql_reward." WHERE rewa_RewardID= '".$id."'";

		$oDB->QueryOne($do_sql_reward);

	} else {

		# INSERT

		if($reward_id_new){	$sql_reward .= ',rewa_RewardID="'.$reward_id_new.'"';   }

		if($time_insert){	$sql_reward .= ',rewa_CreatedDate="'.$time_insert.'"';   }

		if($_SESSION['UID']){	$sql_reward .= ',rewa_CreatedBy="'.$_SESSION['UID'].'"';   }

		$do_sql_reward = "INSERT INTO ".$table_reward." SET ".$sql_reward;

		$oDB->QueryOne($do_sql_reward);

		$id = $reward_id_new;
	}



	# CUSTOM TARGET

	$sql_custom = 'SELECT * FROM custom_field WHERE bran_BrandID="'.$bran_BrandID.'" AND cufi_Deleted="" AND fity_FieldTypeID IN (3,4,5)';

	$oRes_custom = $oDB->Query($sql_custom);

	while ($custom = $oRes_custom->FetchRow(DBI_ASSOC)){

		$sql_target = '';

		$check = trim_txt($_REQUEST[$custom['cufi_FieldName'].'_check']);

		$data = trim_txt($_REQUEST[$custom['cufi_FieldName']]);

		# CHECK

		$sql_check = 'SELECT reta_RewardTargetID FROM reward_target WHERE rewa_RewardID="'.$id.'" AND cufi_CustomFieldID="'.$custom['cufi_CustomFieldID'].'"';

		$check_id = $oDB->QueryOne($sql_check);

		if ($check_id) {

			if($check && $data){	

				$sql_target = 'reta_Target="'.$data.'"';  

				$sql_target .= ',reta_Deleted=""';  

				$sql_target .= ',reta_UpdatedDate="'.$time_insert.'"';  

				$sql_target .= ',reta_UpdatedBy="'.$_SESSION['UID'].'"';   

			} else {

				$sql_target = 'reta_Deleted="T"';  

				$sql_target .= ',reta_UpdatedDate="'.$time_insert.'"'; 

				$sql_target .= ',reta_UpdatedBy="'.$_SESSION['UID'].'"'; 
			}

			# UPDATE

			$do_sql_target = "UPDATE reward_target SET ".$sql_target." WHERE reta_RewardTargetID= '".$check_id."'";

		} else {

			if($check && $data){	

				# TARGET ID

				$sql_id = 'SELECT MAX(reta_RewardTargetID) FROM reward_target';

				$new_id = $oDB->QueryOne($sql_id);

				$new_id++;

				$sql_target = 'reta_RewardTargetID="'.$new_id.'"';

				$sql_target .= ',cufi_CustomFieldID="'.$custom['cufi_CustomFieldID'].'"'; 

				$sql_target .= ',reta_Target="'.$data.'"';  

				$sql_target .= ',rewa_RewardID="'.$id.'"';  

				$sql_target .= ',reta_Deleted=""';  

				$sql_target .= ',reta_UpdatedDate="'.$time_insert.'"';   

				$sql_target .= ',reta_UpdatedBy="'.$_SESSION['UID'].'"';

				$sql_target .= ',reta_CreatedDate="'.$time_insert.'"';   

				$sql_target .= ',reta_CreatedBy="'.$_SESSION['UID'].'"';
			}

			$do_sql_target = "INSERT INTO reward_target SET ".$sql_target;
		}

		$oDB->QueryOne($do_sql_target);
	}



	# QRCODE

	foreach ($_POST['brnc_BranchID'] as $branch_id) {

		$qrcode_privileges_text = "QRW-".str_pad($id,4,"0",STR_PAD_LEFT)."-"
										.str_pad($branch_id,4,"0",STR_PAD_LEFT)."";

		$file_full_path = '../../upload/'.$bran_BrandID.'/qr_reward_upload/'.$qrcode_privileges_text.".png";

		$qrcode_url = $qrcode_privileges_text.".png";

		$errorCorrectionLevel = 'H'; 

		$matrixPointSize = 10;	

		QRcode::png($qrcode_privileges_text, $file_full_path, $errorCorrectionLevel, $matrixPointSize, 2); 
	}


	echo '<script type="text/javascript">window.location.href="reward.php";</script>';

	exit;
}





#  variety_category dropdownlist

$as_variety_category = dropdownlist_from_table($oDB,'variety,variety_category','vari_VarietyID','vari_Title','variety.vari_VarietyCategoryID = variety_category.vaca_VarietyCategoryID AND variety_category.vaca_Type="Reward" 
						AND variety.vari_Status="1"');

$oTmp->assign('variety_category_opt', $as_variety_category);



#  category dropdownlist

$as_mi_tg_activity = dropdownlist_from_table($oDB,'mi_tg_activity','id_activity','activity_name');

$oTmp->assign('mi_tg_activity', $as_mi_tg_activity);



#  uom dropdownlist

$as_uom = dropdownlist_from_table($oDB,'uom','uom_UOMID','uom_Name');

$oTmp->assign('uom', $as_uom);



#  age dropdownlist

$as_age = dropdownlist_from_table($oDB,'mi_master_target','target_id','name','master_field_id="6"');

$oTmp->assign('age', $as_age);



#  gender dropdownlist

$as_gender = dropdownlist_from_table($oDB,'mi_master_target','target_id','name','master_field_id="5"');

$oTmp->assign('gender', $as_gender);



#  marital dropdownlist

$as_marital = dropdownlist_from_table($oDB,'mi_master_target','target_id','name','master_field_id="7"');

$oTmp->assign('marital', $as_marital);



#  education dropdownlist

$as_education = dropdownlist_from_table($oDB,'mi_master_target','target_id','name','master_field_id="23"');

$oTmp->assign('education', $as_education);



#  activity dropdownlist

$as_activity = dropdownlist_from_table($oDB,'mi_master_target','target_id','name','master_field_id="30"');

$oTmp->assign('activity', $as_activity);



#  monthly income dropdownlist

$as_income = dropdownlist_from_table($oDB,'mi_master_target','target_id','name','master_field_id="28"');

$oTmp->assign('income', $as_income);



#  province income dropdownlist

$as_province = dropdownlist_from_table($oDB,'province','prov_ProvinceID','prov_Name');

$oTmp->assign('province', $as_province);




$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('type_file_upload', '<span class="text-rq">Type file : .jpg , .png , .gif only</span>');

$oTmp->assign('is_menu', 'is_rewards');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_motivation', 'in');

$oTmp->assign('content_file', 'memberin_motivation/reward_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
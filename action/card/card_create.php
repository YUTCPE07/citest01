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
include('../../lib/phpqrcode/qrlib.php');

//========================================//


$oTmp = new TemplateEngine();
$oDB = new DBI();

if ($bDebug) {

	$oErr = new Tracker();
	$oDB->SetTracker($oErr);
}

//========================================//

if (($_SESSION['role_action']['card']['add'] != 1) || ($_SESSION['role_action']['card']['edit'] != 1)) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$approve = $_REQUEST['approve'];

$time_insert = date("Y-m-d H:i:s");

$time_pic = date("Ymd");

$time_insert_pic = date("Ymd_His");



$where_brand = '';

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND a.brand_id = "'.$_SESSION['user_brand_id'].'"';
}


# SEARCH MAX CARD_ID

	$sql_get_last_ins = 'SELECT max(card_id) FROM mi_card';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################


# SEARCH MAX REGISTER_ID

	$sql_get_last_regis = 'SELECT max(refo_RegisterFormID) FROM register_form';
	$id_last_regis = $oDB->QueryOne($sql_get_last_regis);
	$id_regis_new = $id_last_regis+1;

#######################################


# SEARCH NAME OLD IMAGE

	$sql_get_old_img = 'SELECT image FROM mi_card WHERE card_id='.$id;
	$get_old_img = $oDB->QueryOne($sql_get_old_img);
	$old_image = $get_old_img;

#######################################


# COUNT FIELD MEMBER

	$sql_get_count_field = 'SELECT COUNT(*) FROM master_field';
	$get_count_field = $oDB->QueryOne($sql_get_count_field);
	$count_field = $get_count_field;

#######################################


# SEARCH TAX_TYPE BRAND

	$sql_get_tax = 'SELECT tax_type FROM mi_brand WHERE brand_id='.$brand_id;
	$get_tax = $oDB->QueryOne($sql_get_tax);

#######################################


# SEARCH TAX_VAT BRAND

	$sql_get_vat = 'SELECT tax_vat FROM mi_brand WHERE brand_id='.$brand_id;
	$get_vat = $oDB->QueryOne($sql_get_vat);

#######################################


# SEARCH CHARGE

	$sql_card_charge = 'SELECT card_charge FROM mi_setting';
	$card_charge = $oDB->QueryOne($sql_card_charge);

#######################################


# SEARCH SERVICE FEE

	$sql_card_service_fee = 'SELECT card_service_fee FROM mi_setting';
	$card_service_fee = $oDB->QueryOne($sql_card_service_fee);

#######################################



if($Act == 'approve' && $id != '') {

	# APPROVE IMAGE

	$sql = 'SELECT image_newupload, image, brand_id FROM mi_card WHERE card_id ="'.$id.'"';

	$oRes = $oDB->Query($sql);
	
	$axRow = $oRes->FetchRow(DBI_ASSOC);
		
	if($approve == 'unapprove') {

 		# UNAPPROVE

		if ($axRow['image']!="") {

			unlink_file($oDB,'mi_card','image_newupload','card_id',$id,'../../upload/'.$axRow['brand_id'].'/card_upload/',$axRow['image_newupload']);

			$do_sql_upload = "UPDATE mi_card SET image_newupload='',flag_status='2',date_update='".$time_insert."' WHERE card_id='".$id."' ";

		} else if ($axRow['image_newupload']!=""){

			unlink_file($oDB,'mi_card','image','card_id',$id,'../../upload/'.$axRow['brand_id'].'/card_upload/',$axRow['image']);
				
			$do_sql_upload = "UPDATE mi_card SET image='',flag_status='2',date_update='".$time_insert."' WHERE card_id='".$id."' ";
		}
 			
 		$oDB->QueryOne($do_sql_upload);
 	}
		
	if ($approve == 'approve') {

		# APPROVE

		if ($axRow['image']!="") {

			unlink_file($oDB,'mi_card','image','card_id',$id,'../../upload/'.$axRow['brand_id'].'/card_upload/',$axRow['image']);

			$do_sql_upload = "UPDATE mi_card 
								SET image='".$axRow['image_newupload']."', 
								image_newupload='',
								date_update='".$time_insert."' 
								WHERE card_id='".$id."'";
		} else {

			$do_sql_upload = "UPDATE mi_card 
								SET image='".$axRow['image_newupload']."', 
								image_newupload='',
								flag_status='2',
								date_update='".$time_insert."' 
								WHERE card_id='".$id."'";
		}

	 	$oDB->QueryOne($do_sql_upload);
	}

	echo '<script> window.location.href="card_create.php?act=edit&id='.$id.'"; </script>';

} else if( $Act == 'edit' && $id != '' ){

	# EDIT

	$sql = '';

	$sql .= 'SELECT

				a.*,
				b.tax_type,
				b.tax_vat,
				b.name AS brand_name

				FROM mi_card AS a

				LEFT JOIN mi_brand AS b
				ON b.brand_id = a.brand_id

				WHERE a.card_id = "'.$id.'" '.$where_brand.'';

	$oRes = $oDB->Query($sql);

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;

		if ($axRow['limit_privilege']==0) { $axRow['limit_privilege']=""; }

		if ($axRow['limit_member']==0) { $axRow['limit_member']=""; }

		if ($axRow['member_fee']==0) { $axRow['member_fee']=""; }

		$asData = $axRow;

		
		$data_branch = "";

		if ($axRow['branch_id']) {

			$token = strtok($axRow['branch_id'] , ",");

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
									<td style="text-align:center"><img src="../../upload/'.$axRow['path_qr'].'MAC-'.str_pad($id,4,"0",STR_PAD_LEFT).'-'.str_pad($branch[$x],4,"0",STR_PAD_LEFT).'.png" width="50" height="50" class="image_border"/></td>
									<td style="text-align:center"><a target="_blank" href="card_qrcode.php?id='.$id.'&branch='.$branch[$x].'">QRCode Link</td>
									</tr>';
			}

		} else {

			$data_branch = '<tr><td colspan="3" style="text-align:center">No Branch Data</td></tr>';
		}

		$oTmp->assign('branch_data', $data_branch);
	}

} else if( $Act == 'save' ){

	# SAVE

	$do_sql_card = "";

	$id = trim_txt($_REQUEST['id']);

	$card_type_id = trim_txt($_REQUEST['card_type_id']);

	$variety_id = trim_txt($_REQUEST['variety_id']);

	$display_data = trim_txt($_REQUEST['display_data']);

	$brand_id = trim_txt($_REQUEST['brand_id']);

	$card_name = trim_txt($_REQUEST['card_name']);

	$description = trim_txt($_REQUEST['description']);

	$purpose = trim_txt($_REQUEST['purpose']);

	$price_type = trim_txt($_REQUEST['price_type']);

	$member_fee = trim_txt($_REQUEST['member_fee']);

	$original_fee = trim_txt($_REQUEST['original_fee']);

	$card_percent = trim_txt($_REQUEST['card_percent']);

	// $charge_percent = trim_txt($_REQUEST['charge_percent']);

	$vat_type = trim_txt($_REQUEST['vat_type']);

	$note = trim_txt($_REQUEST['note']);

	$target_member_type = trim_txt($_REQUEST['target_member_type']);

	$period_type = trim_txt($_REQUEST['period_type']);

	$period_type_year = trim_txt($_REQUEST['period_type_year']);

	$period_type_month = trim_txt($_REQUEST['period_type_month']);

	$limit_member = trim_txt($_REQUEST['limit_member']);

	$flag_status = trim_txt($_REQUEST['flag_status']);

	$last_register_type = trim_txt($_REQUEST['last_register_type']);

	$date_last_register = trim_txt($_REQUEST['RegisterDate']);

	$date_expired = trim_txt($_REQUEST['ExpiredDate']);

	$choose_upload_default = trim_txt($_REQUEST['choose_upload_default']);

	$condition_card = trim_txt($_REQUEST['condition_card']);

	$exception = trim_txt($_REQUEST['exception']);

	$date_status = trim_txt($_REQUEST['AutoDate']);

	$special_code = trim_txt($_REQUEST['special_code']);

	$flag_hidden = trim_txt($_REQUEST['flag_hidden']);

	$flag_multiple = trim_txt($_REQUEST['flag_multiple']);

	$flag_autorenew = trim_txt($_REQUEST['flag_autorenew']);

	$flag_existing = trim_txt($_REQUEST['flag_existing']);

	$register_condition = trim_txt($_REQUEST['register_condition']); 
	$how_to_activate = trim_txt($_REQUEST['how_to_activate']); 
	$birthday_privileges = trim_txt($_REQUEST['birthday_privileges']); 
	$how_to_use = trim_txt($_REQUEST['how_to_use']); 
	$collection_data = trim_txt($_REQUEST['collection_data']); 
	$re_new = trim_txt($_REQUEST['re_new']); 
	$upgrade_data = trim_txt($_REQUEST['upgrade_data']); 
	$where_to_use = trim_txt($_REQUEST['where_to_use']);
	$source_information = trim_txt($_REQUEST['source_information']);

	$privileges_1 = trim_txt($_REQUEST['privileges_1']); 
	$privileges_2 = trim_txt($_REQUEST['privileges_2']); 
	$privileges_3 = trim_txt($_REQUEST['privileges_3']); 
	$privileges_4 = trim_txt($_REQUEST['privileges_4']); 
	$privileges_5 = trim_txt($_REQUEST['privileges_5']); 
	$privileges_6 = trim_txt($_REQUEST['privileges_6']); 
	$privileges_7 = trim_txt($_REQUEST['privileges_7']); 
	$privileges_8 = trim_txt($_REQUEST['privileges_8']); 
	$privileges_9 = trim_txt($_REQUEST['privileges_9']); 
	$privileges_10 = trim_txt($_REQUEST['privileges_10']);  

	$reward_1 = trim_txt($_REQUEST['reward_1']); 
	$reward_2 = trim_txt($_REQUEST['reward_2']); 
	$reward_3 = trim_txt($_REQUEST['reward_3']); 
	$reward_4 = trim_txt($_REQUEST['reward_4']); 
	$reward_5 = trim_txt($_REQUEST['reward_5']); 
 


	$branch_data = "";

	foreach ($_POST['brnc_BranchID'] as $branch_id) {
		$branch_data .= $branch_id.",";

		if ($id) { 
			$qrcode .= 'MAC-'.str_pad($id,4,"0",STR_PAD_LEFT).'-'.str_pad($branch_id,4,"0",STR_PAD_LEFT).",";
		} else { 
			$qrcode .= 'MAC-'.str_pad($id_new,4,"0",STR_PAD_LEFT).'-'.str_pad($branch_id,4,"0",STR_PAD_LEFT).","; 
		}
	}

	$str_branch = strlen($branch_data);
	$branch_data = substr($branch_data,0,$str_branch-1);




	$sql_card = '';

	$table_card = 'mi_card';



	# Action with card table

	if($card_name){	$sql_card .= 'name="'.$card_name.'"';   }	###

	if($card_type_id){	$sql_card .= ',card_type_id="'.$card_type_id.'"';   }

	if($variety_id){	$sql_card .= ',variety_id="'.$variety_id.'"';   }

	if($display_data){	$sql_card .= ',display_data="'.$display_data.'"';   }

	$sql_card .= ',branch_id="'.$branch_data.'"';   

	$sql_card .= ',description="'.$description.'"';  

	$sql_card .= ',condition_card="'.$condition_card.'"';   

	$sql_card .= ',exception="'.$exception.'"';   

	$sql_card .= ',purpose="'.$purpose.'"';   

	$sql_card .= ',note="'.$note.'"';

	$sql_card .= ',special_code="'.$special_code.'"';

	if($price_type){	$sql_card .= ',price_type="'.$price_type.'"';   }

	if ($price_type == "Free Card") {

		$sql_card .= ',member_fee=0';

		$sql_card .= ',original_fee=0';

	} else if ($price_type == "Not Free Card" || $price_type == "Info. Card") {

		$sql_card .= ',member_fee="'.$member_fee.'"';

		$sql_card .= ',original_fee="'.$original_fee.'"';
	}

	if($vat_type){	$sql_card .= ',vat_type="'.$vat_type.'"';   } 
	
	else {	$sql_card .= ',vat_type="1"';	}
		
	if ($get_tax==1) {

		$member_vat = $member_fee*($get_vat/100);
		
		$sql_card .= ',member_vat="'.$member_vat.'"'; 

		if ($vat_type==1) {

			$member_amount = $member_fee-($member_fee*($get_vat/100));
			
			$sql_card .= ',member_amount="'.$member_amount.'"'; 

		} else { 

			$member_amount = $member_fee;
			
			$sql_card .= ',member_amount="'.$member_fee.'"'; 
		}

	} else {

		$member_vat = 0.00;
		
		$member_amount = $member_fee;
		
		$sql_card .= ',member_vat=0.00';
		
		$sql_card .= ',member_amount="'.$member_amount.'"'; 
	}

	$sql_card .= ',member_price="'.($member_amount+$member_vat).'"'; 

	if($limit_member){	$sql_card .= ',limit_member="'.$limit_member.'"';   }

	if ($last_register_type == 1) { $sql_card .= ',date_last_register=""'; } 

	else if ($last_register_type == 2) { $sql_card .= ',date_last_register="'.$date_last_register.'"'; }

	if ($period_type == 1) {

		$sql_card.=',period_type_other=""'		;
		
		if($date_expired){	$sql_card .= ',date_expired="'.$date_expired.'"';   }

	} else if ($period_type == 2) {
			
		if($period_type_month){	$sql_card .= ',period_type_other="'.$period_type_month.'"';   }
		
		$sql_card .= ',date_expired=""';
		
	} else if ($period_type == 3) {
			
		if($period_type_year){	$sql_card .= ',period_type_other="'.$period_type_year.'"';   }
		
		$sql_card .= ',date_expired=""';
		
	} else {

		$sql_card .= ',period_type_other=""';
		
		$sql_card .= ',date_expired=""';
	}

	if($period_type){	$sql_card .= ',period_type="'.$period_type.'"';   }

	if($time_insert){	$sql_card .= ',date_update="'.$time_insert.'"';   }

	if($brand_id){	$sql_card .= ',brand_id="'.$brand_id.'"';   }

	$sql_card .= ',date_status="'.$date_status.'"';

	if ($flag_hidden=='') { $sql_card .= ',flag_hidden="No"'; } 
	else { $sql_card .= ',flag_hidden="'.$flag_hidden.'"';}

	if ($flag_multiple=='') { $sql_card .= ',flag_multiple="No"'; } 
	else { $sql_card .= ',flag_multiple="'.$flag_multiple.'"';}

	if ($flag_autorenew=='') { $sql_card .= ',flag_autorenew="No"'; } 
	else { $sql_card .= ',flag_autorenew="'.$flag_autorenew.'"';}

	if ($flag_existing=='') { $sql_card .= ',flag_existing="No"'; } 
	else { $sql_card .= ',flag_existing="'.$flag_existing.'"';}

	if( $_FILES["card_image_upload"]["name"] != "" && $choose_upload_default==1){

		$new_img_name = upload_img('card_image_upload','card_'.$time_insert_pic,'../../upload/'.$brand_id.'/card_upload/',640,400);

		if($new_img_name){

			// $sql_card .= ',image_newupload="'.$new_img_name.'"';

			// if ($old_image!="") {	$sql_card .= ',flag_status="'.$flag_status.'"';	} 
			
			// else {	$sql_card .= ',flag_status="2"';	}
			
			$sql_card .= ',image="'.$new_img_name.'"';

			$sql_card .= ',flag_status="'.$flag_status.'"';

			$sql_card .= ',flag_approve=""';

			if ($old_image!="") {

				unlink_file($oDB,'mi_card','image','card_id',$id,'../../upload/'.$brand_id.'/card_upload/',$old_image);
			}
		}

	} else if($choose_upload_default==2){

		$new_img_name = 'card_'.$brand_id.'.jpg';

		$exp_name = explode('.','../../upload/'.$brand_id.'/card_upload/'.$new_img_name);

		$i = count($exp_name)-1;

		$type = $exp_name[$i];

		$img_name = 'card_'.$time_insert_pic.'.'.$type;

		copy('../../upload/'.$brand_id.'/card_upload/'.$new_img_name,'../../upload/'.$brand_id.'/card_upload/'.$img_name);

		unlink_file($oDB,'mi_card','image','card_id',$id,'../../upload/'.$brand_id.'/card_upload/',$old_image);

		unlink('../../upload/'.$brand_id.'/card_upload/'.$new_img_name);

		unlink_file($oDB,'mi_card','image_newupload','card_id',$id,'../../upload/'.$brand_id.'/card_upload/',$image_newupload);

		unlink('../../upload/'.$brand_id.'/card_upload/'.$image_newupload);

		$sql_card .= ',image="'.$img_name.'"';

		$sql_card .= ',image_newupload=""';

		$sql_card .= ',flag_status="'.$flag_status.'"';

		$sql_card .= ',flag_approve="T"';

	} else { $sql_card .= ',flag_status="2"'; }

	if ($old_image!="") { $sql_card .= ',flag_status="'.$flag_status.'"'; }


	$sql_card .= ',register_condition="'.$register_condition.'"'; 
	$sql_card .= ',how_to_activate="'.$how_to_activate.'"'; 
	$sql_card .= ',birthday_privileges="'.$birthday_privileges.'"'; 
	$sql_card .= ',how_to_use="'.$how_to_use.'"'; 
	$sql_card .= ',collection_data="'.$collection_data.'"'; 
	$sql_card .= ',re_new="'.$re_new.'"'; 
	$sql_card .= ',upgrade_data="'.$upgrade_data.'"'; 
	$sql_card .= ',where_to_use="'.$where_to_use.'"'; 
	$sql_card .= ',source_information="'.$source_information.'"'; 

	$sql_card .= ',privileges_1="'.$privileges_1.'"'; 
	$sql_card .= ',privileges_2="'.$privileges_2.'"'; 
	$sql_card .= ',privileges_3="'.$privileges_3.'"'; 
	$sql_card .= ',privileges_4="'.$privileges_4.'"'; 
	$sql_card .= ',privileges_5="'.$privileges_5.'"'; 
	$sql_card .= ',privileges_6="'.$privileges_6.'"'; 
	$sql_card .= ',privileges_7="'.$privileges_7.'"'; 
	$sql_card .= ',privileges_8="'.$privileges_8.'"'; 
	$sql_card .= ',privileges_9="'.$privileges_9.'"'; 
	$sql_card .= ',privileges_10="'.$privileges_10.'"'; 

	$sql_card .= ',reward_1="'.$reward_1.'"'; 
	$sql_card .= ',reward_2="'.$reward_2.'"'; 
	$sql_card .= ',reward_3="'.$reward_3.'"'; 
	$sql_card .= ',reward_4="'.$reward_4.'"'; 
	$sql_card .= ',reward_5="'.$reward_5.'"'; 



	
	if($id){

		# UPDATE

		$do_sql_card = "UPDATE ".$table_card." SET ".$sql_card." WHERE card_id= '".$id."' ";
		
		$oDB->QueryOne($do_sql_card);

		# QR CODE

		$sql_get_qr_code_text = 'SELECT qr_code_image FROM '.$table_card.' WHERE card_id = "'.$id.'" ';

		$QR_DB = $oDB->QueryOne($sql_get_qr_code_text);


		if($QR_DB==''){

			$qr_code_text = "MBC-".str_pad($id,4,"0",STR_PAD_LEFT);

			$errorCorrectionLevel = 'H';

			$matrixPointSize = 10;		

			$qr_code_image = $qr_code_text.'.png';

			$file_full_path = '../../upload/'.$brand_id.'/qr_card_upload/'.$qr_code_image;

			QRcode::png($qr_code_text, $file_full_path, $errorCorrectionLevel, $matrixPointSize, 2); 

			$sql_card = 'qr_code_text="'.$qr_code_text.'",qr_code_image="'.$qr_code_image.'"';

			$do_sql_card = "UPDATE ".$table_card." SET ".$sql_card." WHERE card_id= '".$id."'";

			$oDB->QueryOne($do_sql_card);
		}

	} else {

		# INSERT
		
		if($time_insert){	$sql_card .= ',date_create="'.$time_insert.'"';   }
		
		if($id_new){	$sql_card .= ',card_id="'.$id_new.'"';   }
		
		if($id_new){	$sql_card .= ',qr_code_text="MBC-'.$id_new.'"';   }
		
		$sql_card .= ',path_qr="'.$brand_id.'/qr_card_upload/"';
		
		$sql_card .= ',path_image="'.$brand_id.'/card_upload/"';

		if ($price_type == "Not Free Card" || $price_type == "Info. Card") { 

			$sql_card .= ',charge_percent="'.$card_charge.'"'; 
			$sql_card .= ',expense_fee="'.$card_service_fee.'"'; 
		}

		$do_sql_card = 'INSERT INTO '.$table_card.' SET '.$sql_card;
		
		$oDB->QueryOne($do_sql_card);


		# REGISTER FORM

		for ($i=1; $i <= $count_field; $i++) {

			$do_sql_card_register = "INSERT INTO register_form SET card_CardID='".$id_new."',mafi_MasterFieldID='".$i."',refo_RegisterFormID='".$id_regis_new."',refo_CreatedBy='".$_SESSION['UID']."',refo_UpdatedBy='".$_SESSION['UID']."',refo_CreatedDate='".$time_insert."',refo_UpdatedDate='".$time_insert."'";

			$oDB->QueryOne($do_sql_card_register);

			$id_regis_new++;
		}

		// $do_sql_check = "UPDATE register_form 
		// 					SET refo_FillIn='Y', refo_Require='Y' 
		// 					WHERE mafi_MasterFieldID IN (2,3,5,6)";

		// $oDB->QueryOne($do_sql_check);


		# QR CODE

		$qr_code_text = "MBC-".str_pad($id_new,4,"0",STR_PAD_LEFT);

		$errorCorrectionLevel = 'H'; 

		$matrixPointSize = 10;		

		$qr_code_image = $qr_code_text.'.png';

		$file_full_path = '../../upload/'.$brand_id.'/qr_card_upload/'.$qr_code_image;

		QRcode::png($qr_code_text, $file_full_path, $errorCorrectionLevel, $matrixPointSize, 2);

		$sql_card = 'qr_code_text="'.$qr_code_text.'",qr_code_image="'.$qr_code_image.'"';

		$do_sql_card = "UPDATE ".$table_card." SET ".$sql_card." WHERE card_id= '".$id_new."'";

		$oDB->QueryOne($do_sql_card);


		$id = $id_new;

	}	




	# QRCODE

	foreach ($_POST['brnc_BranchID'] as $branch_id) {

		$qrcode_privileges_text = "MAC-".str_pad($id,4,"0",STR_PAD_LEFT)."-"
										.str_pad($branch_id,4,"0",STR_PAD_LEFT)."";

		$file_full_path = '../../upload/'.$brand_id.'/qr_card_upload/'.$qrcode_privileges_text.".png";

		$qrcode_url = $qrcode_privileges_text.".png";

		$errorCorrectionLevel = 'H'; 

		$matrixPointSize = 10;	

		QRcode::png($qrcode_privileges_text, $file_full_path, $errorCorrectionLevel, $matrixPointSize, 2); 
	}


	echo '<script>window.location.href="card.php";</script>';

	exit;

}





#  variety_category dropdownlist

$as_variety_category = dropdownlist_from_table($oDB,'variety,variety_category','vari_VarietyID','vari_Title','variety.vari_VarietyCategoryID = variety_category.vaca_VarietyCategoryID AND variety_category.vaca_Type="Card" 
						AND variety.vari_Status="1"');

$oTmp->assign('variety_category_opt', $as_variety_category);



#  period_type dropdownlist

$as_period_type = dropdownlist_type_master($oDB,'period_type');

$oTmp->assign('period_type_opt', $as_period_type);



#  card_type dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_type = ' and brand_id IN (0,'.$_SESSION['user_brand_id'].')';
}

$as_province = dropdownlist_from_table($oDB,'mi_card_type','card_type_id','name','flag_status="1"'.$where_type,' ORDER BY name ASC');

$oTmp->assign('card_type_opt', $as_province);



#  brand dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' and brand_id="'.$_SESSION['user_brand_id'].'" ';
}

$as_brand_id = dropdownlist_from_table($oDB,'mi_brand','brand_id','name','brand_id>0'.$where_brand,' ORDER BY name ASC');

$oTmp->assign('brand_id_opt', $as_brand_id);




$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('type_file_upload', '<span class="text-rq">Type file : .jpg , .png , .gif only</span>');

$oTmp->assign('is_menu', 'is_card');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_membership', 'in');

$oTmp->assign('content_file', 'card/card_create.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
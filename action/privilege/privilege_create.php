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

if (($_SESSION['role_action']['privilege']['add'] != 1) || ($_SESSION['role_action']['privilege']['edit'] != 1)) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$approve = $_REQUEST['approve'];

$time_insert = date("Y-m-d H:i:s");

$time_insert_pic = date("Ymd_His");

$time_pic = date("Ymd");



$where_brand = '';

if ($_SESSION['user_type_id_ses']>1 ) {

	$where_brand = ' AND bran_BrandID = "'.$_SESSION['user_brand_id'].'"';
}



# SEARCH MAX PRIVILEGE ID

	$sql_get_last_ins = 'SELECT max(priv_PrivilegeID) FROM privilege';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################


# SEARCH MAX PRIVILEGE LINK ID

	$sql_get_last_ins = 'SELECT max(prli_PrivilegeLinkID) FROM privilege_link';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$link_id_new = $id_last_ins+1;

#######################################


# SEARCH NAME OLD IMAGE

	$sql_get_old_img = 'SELECT priv_Image FROM privilege WHERE priv_PrivilegeID='.$id;
	$get_old_img = $oDB->QueryOne($sql_get_old_img);
	$old_image = $get_old_img;

#######################################


# SEARCH MAX CUSTOM PRIVILEGE ID

	$sql_get_last_ins = 'SELECT max(cufo_CustomFormID) FROM custom_form_privilege';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$custom_new = $id_last_ins+1;

#######################################




if($Act == 'approve' && $id != '') {

	# APPROVE IMAGE

	$sql = '';

	$sql .= 'SELECT priv_ImageNew, priv_Image, bran_BrandID FROM privilege WHERE priv_PrivilegeID ="'.$id.'"';

	$oRes = $oDB->Query($sql);
	
	$axRow = $oRes->FetchRow(DBI_ASSOC);
		
	if($approve == 'unapprove') {

 		# UNAPPROVE

		// if ($axRow['priv_ImageNew']!="") {

		// 	unlink_file($oDB,'privilege','priv_ImageNew','priv_PrivilegeID',$id,'../../upload/'.$axRow['bran_BrandID'].'/privilege_upload/',$axRow['priv_ImageNew']);

		// 	$do_sql_upload = "UPDATE privilege SET priv_ImageNew='',priv_Status='Pending', priv_UpdatedDate='".$time_insert."' WHERE priv_PrivilegeID='".$id."' ";

		// } else if ($axRow['priv_Image']!=""){

		// 	unlink_file($oDB,'privilege','priv_Image','priv_PrivilegeID',$id,'../../upload/'.$axRow['bran_BrandID'].'/privilege_upload/',$axRow['priv_Image']);
				
		// 	$do_sql_upload = "UPDATE privilege SET priv_Image='',priv_Status='Pending', priv_UpdatedDate='".$time_insert."' WHERE priv_PrivilegeID='".$id."' ";
		// }

		unlink_file($oDB,'privilege','priv_Image','priv_PrivilegeID',$id,'../../upload/'.$axRow['bran_BrandID'].'/privilege_upload/',$axRow['priv_Image']);
				
		$do_sql_upload = "UPDATE privilege 
							SET priv_Image='',
							priv_Approve='',
							priv_UpdatedDate='".$time_insert."',
							priv_UpdatedBy='".$_SESSION['UID']."'  
							WHERE priv_PrivilegeID='".$id."' ";
 			
 		$oDB->QueryOne($do_sql_upload);
 	}
		
	if ($approve == 'approve') {

		# APPROVE

		// if ($axRow['priv_Image']!="") {

		// 	unlink_file($oDB,'privilege','priv_Image','priv_PrivilegeID',$id,'../../upload/'.$axRow['bran_BrandID'].'/privilege_upload/',$axRow['priv_Image']);

		// 	$do_sql_upload = "UPDATE privilege SET priv_Image='".$axRow['priv_ImageNew']."', priv_ImageNew='', priv_UpdatedDate='".$time_insert."' WHERE priv_PrivilegeID='".$id."'";

		// } else {

		// 	$do_sql_upload = "UPDATE privilege SET priv_Image='".$axRow['priv_ImageNew']."', priv_ImageNew='', priv_Status='Pending', priv_UpdatedDate='".$time_insert."' WHERE priv_PrivilegeID='".$id."'";
		// }
				
		$do_sql_upload = "UPDATE privilege 
							SET priv_Approve='T',
							priv_UpdatedDate='".$time_insert."',
							priv_UpdatedBy='".$_SESSION['UID']."'
							WHERE priv_PrivilegeID='".$id."' ";

	 	$oDB->QueryOne($do_sql_upload);
	}

	echo '<script> window.location.href="privilege_create.php?act=edit&id='.$id.'"; </script>';

} else if( $Act == 'edit' && $id != '' ){

	# EDIT

	$sql = '';

	$sql .= 'SELECT a.*,
					b.name AS brand_name 
				FROM privilege a
				LEFT JOIN mi_brand b
				ON a.bran_BrandID = b.brand_id
				WHERE priv_PrivilegeID = "'.$id.'" '.$where_brand.' ';

	$oRes = $oDB->Query($sql);

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		if ($axRow['priv_Motivation']=='Point') { $axRow['priv_Moti'] = 'p'; }
		if ($axRow['priv_Motivation']=='Stamp') { $axRow['priv_Moti'] = 's'; }
		if ($axRow['priv_Motivation']=='None') { $axRow['priv_Moti'] = 'n'; }

		$asData = $axRow;


		# motivation plan

		$option = '';

		$sql = "SELECT mopp_Name, mopp_MotivationPointID, mopp_UseAmount, mopp_PointQty FROM motivation_plan_point WHERE bran_BrandID='".$_SESSION['user_brand_id']."' AND mopp_Status='T' OR mopp_PrivilegeID=".$id."";

		$check_point = $oDB->QueryOne($sql);
		$get_point = $oDB->Query($sql);

		if ($check_point) {

			$option .= '<optgroup label="Point">';

			while ($point = $get_point->FetchRow(DBI_ASSOC)) {

				$select = "";

				if ($axRow['priv_Motivation']=="Point") {
						
					if ($axRow['priv_MotivationID'] == $point['mopp_MotivationPointID']) { $select = 'selected'; }
					else { $select = ''; }
				}
	    			
	    		$option .= '<option value="p'.$point['mopp_MotivationPointID'].'" '.$select.'>'.$point['mopp_Name'].'&nbsp; ('.number_format($point['mopp_UseAmount'],2).' ฿ / '.$point['mopp_PointQty'].' Point)</option>';
			}

			$option .= '</optgroup>';
		}

		$sql = "SELECT mops_Name, mops_MotivationStampID, mops_StampQty, mops_CollectionTypeID FROM motivation_plan_stamp WHERE bran_BrandID='".$_SESSION['user_brand_id']."' AND mops_Status='T' OR mops_PrivilegeID=".$id."";

		$check_stamp = $oDB->QueryOne($sql);
		$get_stamp = $oDB->Query($sql);

		if ($check_stamp) {

			$option .= '<optgroup label="Stamp">';

			while ($stamp = $get_stamp->FetchRow(DBI_ASSOC)) {

				$icon = "SELECT coty_Name FROM collection_type WHERE coty_CollectionTypeID=".$stamp['mops_CollectionTypeID'];
				$icon_name = $oDB->QueryOne($icon);

				$select = "";

				if ($axRow['priv_Motivation']=="Stamp") {
						
					if ($axRow['priv_MotivationID'] == $stamp['mops_MotivationStampID']) { $select = 'selected'; }
					else { $select = ''; }
				}
	    			
	    		$option .= '<option value="s'.$stamp['mops_MotivationStampID'].'" '.$select.'>'.$stamp['mops_Name'].'&nbsp; (1 Times / '.$stamp['mops_StampQty'].' '.$icon_name.')</option>';
			}

			$option .= '</optgroup>';
		}

		$oTmp->assign('motivation_plan', $option);

	}

} else if( $Act == 'save' ){

	$id = trim_txt($_REQUEST['id']);

	$priv_Name = trim_txt($_REQUEST['priv_Name']);

	$bran_BrandID = trim_txt($_REQUEST['bran_BrandID']);

	$priv_Status = trim_txt($_REQUEST['priv_Status']);

	$priv_Point = trim_txt($_REQUEST['priv_Point']);

	$priv_Stamp = trim_txt($_REQUEST['priv_Stamp']);

	$priv_StartDate = trim_txt($_REQUEST['StartDate']);

	$priv_EndDate = trim_txt($_REQUEST['EndDate']);

	$priv_Description = trim_txt($_REQUEST['priv_Description']);

	$prty_PrivilegeTypeID = trim_txt($_REQUEST['prty_PrivilegeTypeID']);

	$prca_ProductCategoryID = trim_txt($_REQUEST['prca_ProductCategoryID']);

	$priv_SpecialPeriodType = trim_txt($_REQUEST['priv_SpecialPeriodType']);

	$priv_StartDateSpecial = trim_txt($_REQUEST['SStartDate']);

	$priv_EndDateSpecial = trim_txt($_REQUEST['SEndDate']);

	$priv_LimitUse = trim_txt($_REQUEST['priv_LimitUse']);

	$priv_OneTimePer = trim_txt($_REQUEST['priv_OneTimePer']);

	$priv_TrackLike = trim_txt($_REQUEST['priv_TrackLike']);

	$priv_TrackReview = trim_txt($_REQUEST['priv_TrackReview']);

	$priv_TrackRequest = trim_txt($_REQUEST['priv_TrackRequest']);

	$priv_TrackShare = trim_txt($_REQUEST['priv_TrackShare']);

	$priv_Cost = trim_txt($_REQUEST['priv_Cost']);

	$priv_Condition = trim_txt($_REQUEST['priv_Condition']);

	$priv_Exception = trim_txt($_REQUEST['priv_Exception']);

	$priv_HowToUse = trim_txt($_REQUEST['priv_HowToUse']);

	$priv_Note = trim_txt($_REQUEST['priv_Note']);

	$prod_ProductID = trim_txt($_REQUEST['prod_ProductID']);

	$choose_upload_default = trim_txt($_REQUEST['choose_upload_default']);

	$priv_DateStatus = trim_txt($_REQUEST['AutoDate']);

	$priv_Motivation = trim_txt($_REQUEST['priv_Motivation']);

	$priv_Hidden = trim_txt($_REQUEST['priv_Hidden']);

		

	$sql_privilege = '';

	$table_privilege = 'privilege';

	$sql_privilege_link = '';

	$table_privilege_link = 'privilege_link';



	if($priv_Name){	$sql_privilege .= 'priv_Name="'.$priv_Name.'"';   }

	if($bran_BrandID){	$sql_privilege .= ',bran_BrandID="'.$bran_BrandID.'"';   }

	$sql_privilege .= ',priv_CardRegister="Yes"';

	if($priv_StartDate){	$sql_privilege .= ',priv_StartDate="'.$priv_StartDate.'"';   }

	if($priv_EndDate){	$sql_privilege .= ',priv_EndDate="'.$priv_EndDate.'"';   }

	if($prty_PrivilegeTypeID){	$sql_privilege .= ',prty_PrivilegeTypeID="'.$prty_PrivilegeTypeID.'"';   }

	if($priv_SpecialPeriodType){	
			
		$sql_privilege .= ',priv_SpecialPeriodType="'.$priv_SpecialPeriodType.'"';  

		if($priv_StartDateSpecial){	$sql_privilege .= ',priv_StartDateSpecial="'.$priv_StartDateSpecial.'"';   }

		if($priv_EndDateSpecial){	$sql_privilege .= ',priv_EndDateSpecial="'.$priv_EndDateSpecial.'"';   } 

	} else {	

		$sql_privilege .= ',priv_SpecialPeriodType=""';

		$sql_privilege .= ',priv_StartDateSpecial=""';

		$sql_privilege .= ',priv_EndDateSpecial=""'; 
	}

	if($priv_LimitUse){	

		$sql_privilege .= ',priv_LimitUse="'.$priv_LimitUse.'"';
		$sql_privilege .= ',priv_OneTimePer="'.$priv_OneTimePer.'"';   

	} else {

		$sql_privilege .= ',priv_LimitUse=""';
		$sql_privilege .= ',priv_OneTimePer=""';
	}

	if($priv_TrackLike){	$sql_privilege .= ',priv_TrackLike="'.$priv_TrackLike.'"';   }

	if($priv_TrackReview){	$sql_privilege .= ',priv_TrackReview="'.$priv_TrackReview.'"';   }

	if($priv_TrackRequest){	$sql_privilege .= ',priv_TrackRequest="'.$priv_TrackRequest.'"';   }

	if($priv_TrackShare){	$sql_privilege .= ',priv_TrackShare="'.$priv_TrackShare.'"';   }

	if($priv_Cost){	$sql_privilege .= ',priv_Cost="'.$priv_Cost.'"';   }

	if($prod_ProductID){

		$sql_privilege .= ',prod_ProductID="'.$prod_ProductID.'"';
		
		$sql_privilege .= ',prca_ProductCategoryID="'.$prca_ProductCategoryID.'"';
			
	} else {	$sql_privilege .= ',prca_ProductCategoryID="0"';	}

	if($time_insert){	$sql_privilege .= ',priv_UpdatedDate="'.$time_insert.'"';   }

	if($_SESSION['UID']){	$sql_privilege .= ',priv_UpdatedBy="'.$_SESSION['UID'].'"';   }

	$sql_privilege .= ',priv_Condition="'.$priv_Condition.'"';   

	$sql_privilege .= ',priv_Exception="'.$priv_Exception.'"';   

	$sql_privilege .= ',priv_HowToUse="'.$priv_HowToUse.'"';   

	$sql_privilege .= ',priv_Note="'.$priv_Note.'"';   

	$sql_privilege .= ',priv_Description="'.$priv_Description.'"';   

	if ($priv_Hidden=='Yes') { $sql_privilege .= ',priv_Hidden="'.$priv_Hidden.'"'; }
	else { $sql_privilege .= ',priv_Hidden="No"'; } 
			
	$sql_privilege .= ',priv_ImagePath="'.$bran_BrandID.'/privilege_upload/"';

	if( $_FILES["privilege_image_upload"]["name"] != "" && $choose_upload_default==1){

		$new_img_name = upload_img('privilege_image_upload','privilege_'.$time_insert_pic,'../../upload/'.$bran_BrandID.'/privilege_upload/',640,400);

		if($new_img_name){

			// $sql_privilege .= ',priv_ImageNew="'.$new_img_name.'"';

			// if ($old_image!="") {	$sql_privilege .= ',priv_Status="'.$priv_Status.'"';	} 
			
			// else {	$sql_privilege .= ',priv_Status="Pending"';	}
			
			$sql_privilege .= ',priv_Image="'.$new_img_name.'"';

			$sql_privilege .= ',priv_Status="'.$priv_Status.'"';

			$sql_privilege .= ',priv_Approve=""';

			if ($old_image!="") {

				unlink_file($oDB,'privilege','priv_Image','priv_PrivilegeID',$id,'../../upload/'.$bran_BrandID.'/privilege_upload/',$old_image);
			}
		}

	} else if($choose_upload_default==2){

		$new_img_name = 'privilege_'.$bran_BrandID.'.jpg';

		$exp_name = explode('.','../../upload/'.$bran_BrandID.'/privilege_upload/'.$new_img_name);

		$i = count($exp_name)-1;

		$type = $exp_name[$i];

		$img_name = 'privilege_'.$time_insert_pic.'.'.$type;

		copy('../../upload/'.$bran_BrandID.'/privilege_upload/'.$new_img_name,'../../upload/'.$bran_BrandID.'/privilege_upload/'.$img_name);

		unlink_file($oDB,'privilege','priv_Image','priv_PrivilegeID',$id,'../../upload/'.$bran_BrandID.'/privilege_upload/',$old_image);

		unlink('../../upload/'.$bran_BrandID.'/privilege_upload/'.$new_img_name);

		unlink_file($oDB,'privilege','priv_ImageNew','priv_PrivilegeID',$id,'../../upload/'.$bran_BrandID.'/privilege_upload/',$priv_ImageNew);

		unlink('../../upload/'.$bran_BrandID.'/privilege_upload/'.$priv_ImageNew);

		$sql_privilege .= ',priv_Image="'.$img_name.'"';

		$sql_privilege .= ',priv_ImageNew=""';

		$sql_privilege .= ',priv_Status="'.$priv_Status.'"';

		$sql_privilege .= ',priv_Approve="T"';

		$sql_privilege .= ',priv_DateStatus="'.$priv_DateStatus.'"';

	} else { $sql_privilege .= ',priv_Status="Pending"'; }

	if ($old_image!="") { $sql_privilege .= ',priv_Status="'.$priv_Status.'"'; }



	# MOTIVATION PLAN

	if ($priv_Motivation == 'None') {

		$sql_privilege .= ',priv_Motivation="None"';
		$sql_privilege .= ',priv_MotivationID="0"';

	} else {

		$type = substr($priv_Motivation,0,1);
		$id_plan = substr($priv_Motivation,1);

		if ($type == 'p') {

			$sql_privilege .= ',priv_Motivation="Point"';
			$sql_privilege .= ',priv_MotivationID="'.$id_plan.'"';

		} else {

			$sql_privilege .= ',priv_Motivation="Stamp"';
			$sql_privilege .= ',priv_MotivationID="'.$id_plan.'"';
		}
	}




	# PRIVILEGE LINK

	if($time_insert){	$sql_privilege_link = 'prli_UpdatedDate="'.$time_insert.'"';   }

	if($_SESSION['UID']){	$sql_privilege_link .= ',prli_UpdatedBy="'.$_SESSION['UID'].'"';   }

	if($priv_Status){	$sql_privilege_link .= ',prli_Status="'.$priv_Status.'"';   }







	if($id){

		$do_sql_privilege = "UPDATE ".$table_privilege." SET ".$sql_privilege." WHERE priv_PrivilegeID= '".$id."'";

		$do_sql_privilege_link = "UPDATE ".$table_privilege_link." SET ".$sql_privilege_link." WHERE priv_PrivilegeID= '".$id."'";

	} else {

		if($time_insert){	$sql_privilege .= ',priv_CreatedDate="'.$time_insert.'"';   }

		if($_SESSION['UID']){	$sql_privilege .= ',priv_CreatedBy="'.$_SESSION['UID'].'"';   }
			
		if($id_new){	$sql_privilege .= ',priv_PrivilegeID="'.$id_new.'"';   }

		$do_sql_privilege = 'INSERT INTO '.$table_privilege.' SET '.$sql_privilege;

		if($time_insert){	$sql_privilege_link .= ',prli_CreatedDate="'.$time_insert.'"';   }

		if($_SESSION['UID']){	$sql_privilege_link .= ',prli_CreatedBy="'.$_SESSION['UID'].'"';   }
			
		if($id_new){	$sql_privilege_link .= ',priv_PrivilegeID="'.$id_new.'"';   }
			
		if($link_id_new){	$sql_privilege_link .= ',prli_PrivilegeLinkID="'.$link_id_new.'"';   }

		$do_sql_privilege_link = 'INSERT INTO '.$table_privilege_link.' SET '.$sql_privilege_link;

		$id = $id_new;
	}

	$oDB->QueryOne($do_sql_privilege);

	$oDB->QueryOne($do_sql_privilege_link);





	// # MOTIVATION PLAN

	// if ($priv_Motivation == 'None') {

	// 	$do_sql_point = "UPDATE motivation_plan_point SET mopp_PrivilegeType='None', mopp_PrivilegeID=0 WHERE mopp_PrivilegeType='Privilege' AND mopp_PrivilegeID='".$id."'";
	// 	$oDB->QueryOne($do_sql_point);

	// 	$do_sql_stamp = "UPDATE motivation_plan_stamp SET mops_PrivilegeType='None', mops_PrivilegeID=0 WHERE mops_PrivilegeType='Privilege' AND mops_PrivilegeID='".$id."'";
	// 	$oDB->QueryOne($do_sql_stamp);

	// } else {

	// 	if ($type == 'p') {

	// 		$do_sql_point = "UPDATE motivation_plan_point SET mopp_PrivilegeType='Privilege', mopp_PrivilegeID=".$id." WHERE mopp_MotivationPointID='".$id_plan."'";
	// 		$oDB->QueryOne($do_sql_point);

	// 		$do_sql_point = "UPDATE motivation_plan_point SET mopp_PrivilegeType='None', mopp_PrivilegeID='0' WHERE mopp_MotivationPointID!='".$id_plan."' AND mopp_PrivilegeID='".$id."'";
	// 		$oDB->QueryOne($do_sql_point);

	// 	} else {

	// 		$do_sql_stamp = "UPDATE motivation_plan_stamp SET mops_PrivilegeType='Privilege', mops_PrivilegeID=".$id." WHERE mops_MotivationStampID='".$id_plan."'";
	// 		$oDB->QueryOne($do_sql_stamp);

	// 		$do_sql_stamp = "UPDATE motivation_plan_stamp SET mops_PrivilegeType='None', mops_PrivilegeID='0' WHERE mops_MotivationStampID!='".$id_plan."' AND mopp_PrivilegeID='".$id."'";
	// 		$oDB->QueryOne($do_sql_stamp);
	// 	}
	// }





	# CUSTOM

	$sql_custom = 'SELECT * FROM custom_field WHERE bran_BrandID="'.$bran_BrandID.'" AND cufi_Type="Privilege" AND cufi_Deleted=""';
	$oRes_custom = $oDB->Query($sql_custom);
	while ($custom = $oRes_custom->FetchRow(DBI_ASSOC)){

		$custom_field = trim_txt($_REQUEST[$custom['cufi_FieldName'].'_check']);

		if ($custom_field) { $sql_custom_privilege = "cufo_Deleted=''"; } 
		else { $sql_custom_privilege = "cufo_Deleted='T'"; }

		$sql_custom_privilege .= ",cufo_UpdatedBy='".$_SESSION['UID']."'";
		$sql_custom_privilege .= ",cufo_UpdatedDate='".$time_insert."'";

		# CHECK

		$sql_check = 'SELECT cufo_CustomFormID FROM custom_form_privilege WHERE cufo_PrivilegeID="'.$id.'" AND cufo_Type="Privilege" AND cufi_CustomFieldID="'.$custom['cufi_CustomFieldID'].'"';

		$cufo_CustomFormID = $oDB->QueryOne($sql_check);

		if ($cufo_CustomFormID) {

			$do_sql_custom = "UPDATE custom_form_privilege SET ".$sql_custom_privilege." WHERE cufo_CustomFormID= '".$cufo_CustomFormID."'";

		} else {

			$sql_custom_privilege .= ",cufo_CreatedBy='".$_SESSION['UID']."'";
			$sql_custom_privilege .= ",cufo_CreatedDate='".$time_insert."'";
			$sql_custom_privilege .= ",cufo_Type='Privilege'";
			$sql_custom_privilege .= ",cufo_CustomFormID='".$custom_new."'";
			$sql_custom_privilege .= ",cufi_CustomFieldID='".$custom['cufi_CustomFieldID']."'";
			$sql_custom_privilege .= ",cufo_PrivilegeID='".$id."'";

			$do_sql_custom = 'INSERT INTO custom_form_privilege SET '.$sql_custom_privilege;
		}
			
		$oDB->QueryOne($do_sql_custom);

		$custom_new++;
	}



	echo '<script>window.location.href="privilege.php";</script>';

	exit;

}





#  special_period_type dropdownlist

$as_special_period_type = dropdownlist_type_master($oDB,'special_period_type');

$oTmp->assign('special_period_type_opt', $as_special_period_type);



#  period_member_type dropdownlist

$as_period_member_type = dropdownlist_type_master($oDB,'period_member_type');

$oTmp->assign('period_member_type_opt', $as_period_member_type);



#  period_privilege_type dropdownlist

$as_period_privilege_type = dropdownlist_type_master($oDB,'period_privilege_type');

$oTmp->assign('period_privilege_type_opt', $as_period_privilege_type);



#  privilege_activity_type dropdownlist

$as_privilege_activity_type = dropdownlist_type_master($oDB,'privilege_activity_type');

$oTmp->assign('privilege_activity_type_opt', $as_privilege_activity_type);



#  privilege_type_id dropdownlist

$as_privilege_type_id = dropdownlist_from_table($oDB,'mi_privilege_type','privilege_type_id','name','prty_Type!="Activity"');

$oTmp->assign('privilege_type_id_opt', $as_privilege_type_id);



#  category_id dropdownlist

$as_category_id = dropdownlist_from_table($oDB,'mi_products_category','category_id','name');

$oTmp->assign('category_id_opt', $as_category_id);



#  brand dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' brand_id="'.$_SESSION['user_brand_id'].'" ';

}

$as_brand_id = dropdownlist_from_table($oDB,'mi_brand','brand_id','name',$where_brand,' ORDER BY name ASC');

$oTmp->assign('brand', $as_brand_id);



#  motivation dropdownlist

if($_SESSION['user_type_id_ses']>1){

	if (!$id) {

		$option = '';

		$sql = "SELECT mopp_Name, mopp_MotivationPointID, mopp_UseAmount, mopp_PointQty FROM motivation_plan_point WHERE bran_BrandID='".$_SESSION['user_brand_id']."' AND mopp_Status='T'";

		$check_point = $oDB->QueryOne($sql);
		$get_point = $oDB->Query($sql);

		if ($check_point) {

			$option .= '<optgroup label="Point">';

			while ($point = $get_point->FetchRow(DBI_ASSOC)) {
	    			
	    		$option .= '<option value="p'.$point['mopp_MotivationPointID'].'">'.$point['mopp_Name'].'&nbsp; ('.number_format($point['mopp_UseAmount'],2).' ฿ / '.$point['mopp_PointQty'].' Point)</option>';
			}

			$option .= '</optgroup>';
		}

		$sql = "SELECT mops_Name, mops_MotivationStampID, mops_StampQty, mops_CollectionTypeID FROM motivation_plan_stamp WHERE bran_BrandID='".$_SESSION['user_brand_id']."' AND mops_Status='T'";

		$check_stamp = $oDB->QueryOne($sql);
		$get_stamp = $oDB->Query($sql);

		if ($check_stamp) {

			$option .= '<optgroup label="Stamp">';

			while ($stamp = $get_stamp->FetchRow(DBI_ASSOC)) {
	    			
	    		$option .= '<option value="s'.$stamp['mops_MotivationStampID'].'">'.$stamp['mops_Name'].'&nbsp; (1 Times / '.$stamp['mops_StampQty'].' '.$icon_name.')</option>';
			}

			$option .= '</optgroup>';
		}

		$oTmp->assign('motivation_plan', $option);
	}
}




$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('type_file_upload', '<span class="text-rq">Type file : .jpg , .png , .gif only</span>');

$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_privilege');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_privilege', 'in');

$oTmp->assign('content_file', 'privilege/privilege_create.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>

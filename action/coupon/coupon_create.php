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

if (($_SESSION['role_action']['coupon']['add'] != 1) || ($_SESSION['role_action']['coupon']['edit'] != 1)) {

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

	$where_brand = ' AND a.bran_BrandID = "'.$_SESSION['user_brand_id'].'"';
}



# SEARCH MAX COUPON ID

	$sql_get_last_ins = 'SELECT max(coup_CouponID) FROM coupon';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################

# SEARCH MAX PRIVILEGE LINK ID

	$sql_get_last_ins = 'SELECT max(prli_PrivilegeLinkID) FROM privilege_link';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$link_id_new = $id_last_ins+1;

#######################################

# SEARCH NAME OLD IMAGE

	$sql_get_old_img = 'SELECT coup_Image FROM coupon WHERE coup_CouponID='.$id;
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

	$sql .= 'SELECT coup_ImageNew, coup_Image, bran_BrandID FROM coupon WHERE coup_CouponID ="'.$id.'"';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($approve == 'unapprove') {

 		# UNAPPROVE

		// if ($axRow['coup_ImageNew']!="") {

		// 	unlink_file($oDB,'coupon','coup_ImageNew','coup_CouponID',$id,'../../upload/'.$axRow['bran_BrandID'].'/coupon_upload/',$axRow['coup_ImageNew']);

		// 	$do_sql_upload=  "UPDATE coupon SET coup_ImageNew='',coup_Status='Pending' WHERE coup_CouponID='".$id."' ";

	
		// } else if ($axRow['coup_Image']!=""){

			unlink_file($oDB,'coupon','coup_Image','coup_CouponID',$id,'../../upload/'.$axRow['bran_BrandID'].'/coupon_upload/',$axRow['coup_Image']);

			$do_sql_upload = "UPDATE coupon 
								SET coup_Image='',
									coup_Status='Pending',
									coup_Approve='',
									coup_UpdatedDate='".$time_insert."',
									coup_UpdatedBy='".$_SESSION['UID']."'
								WHERE coup_CouponID='".$id."' ";
		// }

 		$oDB->QueryOne($do_sql_upload);
 	}

	if ($approve == 'approve') {

		# APPROVE

		// if ($axRow['coup_Image']!="") {

		// 	unlink_file($oDB,'coupon','coup_Image','coup_CouponID',$id,'../../upload/'.$axRow['bran_BrandID'].'/coupon_upload/',$axRow['coup_Image']);

		// 	$do_sql_upload = "UPDATE coupon 

		// 						SET coup_Image='".$axRow['coup_ImageNew']."', 

		// 						coup_ImageNew='',

		// 						coup_UpdatedDate='".$time_insert."' 

		// 						WHERE coup_CouponID='".$id."'";

		// } else {

			$do_sql_upload = "UPDATE coupon 
								SET coup_Approve='T',
								coup_UpdatedDate='".$time_insert."',
								coup_UpdatedBy='".$_SESSION['UID']."' 
								WHERE coup_CouponID='".$id."'";
		// }

	 	$oDB->QueryOne($do_sql_upload);
	}

	echo '<script>window.location.href = "coupon_create.php?act=edit&id='.$id.'";</script>';

} else if( $Act == 'edit' && $id != '' ) {

	# EDIT

	$sql = '';

	$sql .= 'SELECT a.*,
					b.name AS brand_name 
				FROM coupon a
				LEFT JOIN mi_brand b
				ON a.bran_BrandID = b.brand_id 
				WHERE a.coup_CouponID = "'.$id.'" '.$where_brand.' ';

	$oRes = $oDB->Query($sql);

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;

		$axRow['hour_start'] = substr($axRow['coup_StartTime'], 0, 2);

		if ($axRow['hour_start'] == "00") { $axRow['hour_start'] = ''; }

		$axRow['minute_start'] = substr($axRow['coup_StartTime'], 3, 2);

		$axRow['hour_end'] = substr($axRow['coup_EndTime'], 0, 2);

		if ($axRow['hour_end'] == "00") { $axRow['hour_end'] = ''; }

		$axRow['minute_end'] = substr($axRow['coup_EndTime'], 3, 2);

		$asData = $axRow;
	}

} else if( $Act == 'save' ){

	$id = trim_txt($_REQUEST['id']);

	$coup_Name = trim_txt($_REQUEST['coup_Name']);

	$bran_BrandID = trim_txt($_REQUEST['bran_BrandID']);

	$coup_Status = trim_txt($_REQUEST['coup_Status']);

	$coup_Method = trim_txt($_REQUEST['coup_Method']);

	$coup_StartDate = trim_txt($_REQUEST['StartDate']);

	$coup_EndDate = trim_txt($_REQUEST['EndDate']);

	$coup_Time = trim_txt($_REQUEST['coup_Time']);

	$hour_start = trim_txt($_REQUEST['hour_start']);

	$minute_start = trim_txt($_REQUEST['minute_start']);

	$hour_end = trim_txt($_REQUEST['hour_end']);

	$minute_end = trim_txt($_REQUEST['minute_end']);

	$coup_Description = trim_txt($_REQUEST['coup_Description']);

	$prty_PrivilegeTypeID = trim_txt($_REQUEST['prty_PrivilegeTypeID']);

	$prca_ProductCategoryID = trim_txt($_REQUEST['prca_ProductCategoryID']);

	$prod_ProductID = trim_txt($_REQUEST['prod_ProductID']);

	$coup_Qty = trim_txt($_REQUEST['coup_Qty']);

	$coup_QtyPer = trim_txt($_REQUEST['QtyPer']);

	$coup_TotalQty = trim_txt($_REQUEST['coup_TotalQty']);

	$coup_Transfer = trim_txt($_REQUEST['coup_Transfer']);

	$coup_Repetition = trim_txt($_REQUEST['coup_Repetition']);

	$coup_SpecialPeriodType = trim_txt($_REQUEST['coup_SpecialPeriodType']);

	$coup_StartDateSpecial = trim_txt($_REQUEST['SStartDate']);

	$coup_EndDateSpecial = trim_txt($_REQUEST['SEndDate']);

	$coup_RepetitionMember = trim_txt($_REQUEST['coup_RepetitionMember']);

	$coup_QtyMember = trim_txt($_REQUEST['coup_QtyMember']);

	$coup_QtyPerMember = trim_txt($_REQUEST['QtyPerMember']);

	$coup_MaxQty = trim_txt($_REQUEST['coup_MaxQty']);

	$coup_TrackLike = trim_txt($_REQUEST['coup_TrackLike']);

	$coup_TrackReview = trim_txt($_REQUEST['coup_TrackReview']);

	$coup_TrackRequest = trim_txt($_REQUEST['coup_TrackRequest']);

	$coup_TrackShare = trim_txt($_REQUEST['coup_TrackShare']);

	$coup_Latitude = trim_txt($_REQUEST['coup_Latitude']);

	$coup_Longitude = trim_txt($_REQUEST['coup_Longitude']);

	$coup_Location = trim_txt($_REQUEST['coup_Location']);

	$coup_StartDateSell = trim_txt($_REQUEST['StartDateSell']);

	$coup_EndDateSell = trim_txt($_REQUEST['EndDateSell']);

	$coup_Price = trim_txt($_REQUEST['coup_Price']);

	$coup_Cost = trim_txt($_REQUEST['coup_Cost']);

	$coup_Payment = trim_txt($_REQUEST['coup_Payment']);

	$coup_Condition = trim_txt($_REQUEST['coup_Condition']);

	$coup_Exception = trim_txt($_REQUEST['coup_Exception']);

	$coup_HowToUse = trim_txt($_REQUEST['coup_HowToUse']);

	$coup_Note = trim_txt($_REQUEST['coup_Note']);

	$coup_DateStatus = trim_txt($_REQUEST['AutoDate']);

	$coup_Motivation = trim_txt($_REQUEST['coup_Motivation']);

	$coup_Hidden = trim_txt($_REQUEST['coup_Hidden']);

	$choose_upload_default = trim_txt($_REQUEST['choose_upload_default']);

	$data = "";

	foreach ($_POST['QtyPerData'] as $coup_QtyPerData)

		$data .= $coup_QtyPerData.",";

	$str_data = strlen($data);

	$coup_QtyPerData = substr($data,0,$str_data-1);

	$data_member = "";

	foreach ($_POST['QtyPerMemberData'] as $coup_QtyPerMemberData)

		$data_member .= $coup_QtyPerMemberData.",";

	$str_data = strlen($data_member);

	$coup_QtyPerMemberData = substr($data_member,0,$str_data-1);



	$sql_coupon = '';

	$table_coupon = 'coupon';

	$sql_privilege_link = '';

	$table_privilege_link = 'privilege_link';



	if($coup_Name){	$sql_coupon .= 'coup_Name="'.$coup_Name.'"';   }

	if($bran_BrandID){	$sql_coupon .= ',bran_BrandID="'.$bran_BrandID.'"';   }

	$sql_coupon .= ',coup_CardRegister="Yes"'; 

	if($coup_Method){	$sql_coupon .= ',coup_Method="'.$coup_Method.'"';   }

	if ($coup_Method == 'Fix') {

		$sql_coupon .= ',coup_StartDate="'.$coup_StartDate.'"';  

		$sql_coupon .= ',coup_EndDate="'.$coup_EndDate.'"';  

	} else {

		$sql_coupon .= ',coup_StartDate=""';  

		$sql_coupon .= ',coup_EndDate=""';  
	}

	if($coup_Time=="Fix"){

		$sql_coupon .= ',coup_Time="Fix"';

		$coup_StartTime = $hour_start.":".$minute_start.":00";

		$sql_coupon .= ',coup_StartTime="'.$coup_StartTime.'"'; 

		$coup_EndTime = $hour_end.":".$minute_end.":00";

		$sql_coupon .= ',coup_EndTime="'.$coup_EndTime.'"';  

	} else {

		$sql_coupon .= ',coup_Time="All"';

		$sql_coupon .= ',coup_StartTime=""';

		$sql_coupon .= ',coup_EndTime=""';
	}

	if($prty_PrivilegeTypeID){	$sql_coupon .= ',prty_PrivilegeTypeID="'.$prty_PrivilegeTypeID.'"';   }

	if($prod_ProductID){

		$sql_coupon .= ',prod_ProductID="'.$prod_ProductID.'"';

		$sql_coupon .= ',prca_ProductCategoryID="'.$prca_ProductCategoryID.'"';

	} else {	$sql_coupon .= ',prca_ProductCategoryID="0"';	}

	if($coup_Repetition!='' && $coup_Qty!='0' && $coup_QtyPer!=''){

		$sql_coupon .= ',coup_Qty="'.$coup_Qty.'"';

		$sql_coupon .= ',coup_QtyPer="'.$coup_QtyPer.'"';

		$sql_coupon .= ',coup_Repetition="'.$coup_Repetition.'"';

		$sql_coupon .= ',coup_QtyPerData="'.$coup_QtyPerData.'"';

	} else {

		$sql_coupon .= ',coup_Qty="0"';

		$sql_coupon .= ',coup_Repetition=""';

		$sql_coupon .= ',coup_QtyPer=""';

		$sql_coupon .= ',coup_QtyPerData=""';
	}

	$sql_coupon .= ',coup_TotalQty="'.$coup_TotalQty.'"'; 

	if($coup_Transfer){	$sql_coupon .= ',coup_Transfer="'.$coup_Transfer.'"';   }

	if($coup_SpecialPeriodType){	

		$sql_coupon .= ',coup_SpecialPeriodType="'.$coup_SpecialPeriodType.'"';  

		if($coup_StartDateSpecial){	$sql_coupon .= ',coup_StartDateSpecial="'.$coup_StartDateSpecial.'"';   }

		if($coup_EndDateSpecial){	$sql_coupon .= ',coup_EndDateSpecial="'.$coup_EndDateSpecial.'"';   } 

	} else {	

		$sql_coupon .= ',coup_SpecialPeriodType=""';

		$sql_coupon .= ',coup_StartDateSpecial=""';

		$sql_coupon .= ',coup_EndDateSpecial=""'; 
	}

	if($coup_RepetitionMember!='' && $coup_QtyMember!='0' && $coup_QtyPerMember!=''){

		$sql_coupon .= ',coup_QtyMember="'.$coup_QtyMember.'"';

		$sql_coupon .= ',coup_QtyPerMember="'.$coup_QtyPerMember.'"';

		$sql_coupon .= ',coup_RepetitionMember="'.$coup_RepetitionMember.'"';

		$sql_coupon .= ',coup_QtyPerMemberData="'.$coup_QtyPerMemberData.'"';

	} else {

		$sql_coupon .= ',coup_QtyMember="0"';

		$sql_coupon .= ',coup_RepetitionMember=""';

		$sql_coupon .= ',coup_QtyPerMember=""';

		$sql_coupon .= ',coup_QtyPerMemberData=""';
	}

	if($coup_MaxQty){	$sql_coupon .= ',coup_MaxQty="'.$coup_MaxQty.'"';   }

	if($coup_TrackLike){	$sql_coupon .= ',coup_TrackLike="'.$coup_TrackLike.'"';   }

	if($coup_TrackReview){	$sql_coupon .= ',coup_TrackReview="'.$coup_TrackReview.'"';   }

	if($coup_TrackRequest){	$sql_coupon .= ',coup_TrackRequest="'.$coup_TrackRequest.'"';   }

	if($coup_TrackShare){	$sql_coupon .= ',coup_TrackShare="'.$coup_TrackShare.'"';   }

	if($coup_Latitude){	$sql_coupon .= ',coup_Latitude="'.$coup_Latitude.'"';   }

	if($coup_Longitude){	$sql_coupon .= ',coup_Longitude="'.$coup_Longitude.'"';   }

	if($coup_StartDateSell){	$sql_coupon .= ',coup_StartDateSell="'.$coup_StartDateSell.'"';   }

	if($coup_EndDateSell){	$sql_coupon .= ',coup_EndDateSell="'.$coup_EndDateSell.'"';   }

	if($coup_Price){	$sql_coupon .= ',coup_Price="'.$coup_Price.'"';   }

	if($coup_Cost){	$sql_coupon .= ',coup_Cost="'.$coup_Cost.'"';   }

	if($coup_Payment){	$sql_coupon .= ',coup_Payment="'.$coup_Payment.'"';   }

	$sql_coupon .= ',coup_Description="'.$coup_Description.'"';   

	$sql_coupon .= ',coup_Location="'.$coup_Location.'"';   

	$sql_coupon .= ',coup_Condition="'.$coup_Condition.'"';   

	$sql_coupon .= ',coup_Exception="'.$coup_Exception.'"';   

	$sql_coupon .= ',coup_HowToUse="'.$coup_HowToUse.'"';   

	$sql_coupon .= ',coup_Note="'.$coup_Note.'"';  

	$sql_coupon .= ',coup_DateStatus="'.$coup_DateStatus.'"'; 

	if ($coup_Hidden=='Yes') { $sql_coupon .= ',coup_Hidden="'.$coup_Hidden.'"'; }
	else { $sql_coupon .= ',coup_Hidden="No"'; }  

	$sql_coupon .= ',coup_UpdatedDate="'.$time_insert.'"';  

	$sql_coupon .= ',coup_UpdatedBy="'.$_SESSION['UID'].'"';  

	if( $_FILES["coupon_image_upload"]["name"] != "" && $choose_upload_default==1){

		$new_img_name = upload_img('coupon_image_upload','coupon_'.$time_insert_pic,'../../upload/'.$bran_BrandID.'/coupon_upload/',640,400);

		if($new_img_name){

			// $sql_coupon .= ',coup_ImageNew="'.$new_img_name.'"';

			// if ($old_image!="") {	$sql_coupon .= ',coup_Status="'.$coup_Status.'"';	} 

			// else {	$sql_coupon .= ',coup_Status="Pending"';	}

			$sql_coupon .= ',coup_Image="'.$new_img_name.'"';

			$sql_coupon .= ',coup_Status="'.$coup_Status.'"';

			$sql_coupon .= ',coup_Approve=""';

			if ($old_image!="") {

				unlink_file($oDB,'coupon','coup_Image','coup_CouponID',$id,'../../upload/'.$bran_BrandID.'/coupon_upload/',$old_image);
			}
		}

	} else if($choose_upload_default==2){

		$new_img_name = 'coupon_'.$bran_BrandID.'.jpg';

		$exp_name = explode('.','../../upload/'.$bran_BrandID.'/coupon_upload/'.$new_img_name);

		$i = count($exp_name)-1;

		$type = $exp_name[$i];

		$img_name = 'coupon_'.$time_insert_pic.'.'.$type;

		copy('../../upload/'.$bran_BrandID.'/coupon_upload/'.$new_img_name,'../../upload/'.$bran_BrandID.'/coupon_upload/'.$img_name);

		unlink_file($oDB,'coupon','coup_Image','coup_CouponID',$id,'../../upload/'.$bran_BrandID.'/coupon_upload/',$old_image);

		unlink('../../upload/'.$bran_BrandID.'/coupon_upload/'.$new_img_name);

		unlink_file($oDB,'coupon','coup_ImageNew','coup_CouponID',$id,'../../upload/'.$bran_BrandID.'/coupon_upload/',$coup_ImageNew);

		unlink('../../upload/'.$bran_BrandID.'/coupon_upload/'.$coup_ImageNew);

		$sql_coupon .= ',coup_Image="'.$img_name.'"';

		$sql_coupon .= ',coup_ImageNew=""';

		$sql_coupon .= ',coup_Status="'.$coup_Status.'"';

		$sql_coupon .= ',coup_Approve="T"';

	} else { $sql_coupon .= ',coup_Status="Pending"'; }

	if ($old_image!="") { $sql_coupon .= ',coup_Status="'.$coup_Status.'"'; }



	# MOTIVATION PLAN

	// if ($coup_Motivation == 'None') {

	// 	$sql_coupon .= ',coup_Motivation="None"';

	// 	$sql_coupon .= ',coup_MotivationID="0"';

	// } else {

	// 	$type = substr($coup_Motivation,0,1);

	// 	$id_plan = substr($coup_Motivation,1);

	// 	if ($type == 'p') {

	// 		$sql_coupon .= ',coup_Motivation="Point"';

	// 		$sql_coupon .= ',coup_MotivationID="'.$id_plan.'"';

	// 	} else {

	// 		$sql_coupon .= ',coup_Motivation="Stamp"';

	// 		$sql_coupon .= ',coup_MotivationID="'.$id_plan.'"';
	// 	}
	// }



	# PRIVILEGE LINK

	if($time_insert){	$sql_privilege_link = 'prli_UpdatedDate="'.$time_insert.'"';   }

	if($_SESSION['UID']){	$sql_privilege_link .= ',prli_UpdatedBy="'.$_SESSION['UID'].'"';   }

	if($coup_Status){	$sql_privilege_link .= ',prli_Status="'.$coup_Status.'"';   }



	if($id){

		$do_sql_coupon = "UPDATE ".$table_coupon." SET ".$sql_coupon." WHERE coup_CouponID= '".$id."'";

		$do_sql_privilege_link = "UPDATE ".$table_privilege_link." SET ".$sql_privilege_link." WHERE coup_CouponID= '".$id."'";

		if ($coup_Status=="Active") {	$motivation_status = "T";	}

		if ($coup_Status=="Pending") {	$motivation_status = "F";	}

		$do_sql_point = "UPDATE motivation_point SET mopo_Status='".$motivation_status."' WHERE coup_CouponID= '".$id."'";

		$oDB->QueryOne($do_sql_point);

		$do_sql_stamp = "UPDATE motivation_stamp SET most_Status='".$motivation_status."' WHERE coup_CouponID= '".$id."'";

		$oDB->QueryOne($do_sql_stamp);

	} else {

		if($time_insert){	$sql_coupon .= ',coup_CreatedDate="'.$time_insert.'"';   }

		if($_SESSION['UID']){	$sql_coupon .= ',coup_CreatedBy="'.$_SESSION['UID'].'"';   }

		if($id_new){	$sql_coupon .= ',coup_CouponID="'.$id_new.'"';   }

		$sql_coupon .= ',coup_ImagePath="'.$bran_BrandID.'/coupon_upload/"';

		$do_sql_coupon = 'INSERT INTO '.$table_coupon.' SET '.$sql_coupon;

		if($time_insert){	$sql_privilege_link .= ',prli_CreatedDate="'.$time_insert.'"';   }

		if($_SESSION['UID']){	$sql_privilege_link .= ',prli_CreatedBy="'.$_SESSION['UID'].'"';   }

		if($id_new){	$sql_privilege_link .= ',coup_CouponID="'.$id_new.'"';   }

		if($link_id_new){	$sql_privilege_link .= ',prli_PrivilegeLinkID="'.$link_id_new.'"';   }

		$do_sql_privilege_link = 'INSERT INTO '.$table_privilege_link.' SET '.$sql_privilege_link;

		$id = $id_new;
	}

	$oDB->QueryOne($do_sql_coupon);

	$oDB->QueryOne($do_sql_privilege_link);




	# MOTIVATION PLAN

	// if ($coup_Motivation == 'None') {

	// 	$do_sql_point = "UPDATE motivation_plan_point SET mopp_PrivilegeType='None', mopp_PrivilegeID=0 WHERE mopp_PrivilegeType='Coupon' AND mopp_PrivilegeID='".$id."'";
	// 	$oDB->QueryOne($do_sql_point);

	// 	$do_sql_stamp = "UPDATE motivation_plan_stamp SET mops_PrivilegeType='None', mops_PrivilegeID=0 WHERE mops_PrivilegeType='Coupon' AND mops_PrivilegeID='".$id."'";
	// 	$oDB->QueryOne($do_sql_stamp);

	// } else {

	// 	if ($type == 'p') {

	// 		$do_sql_point = "UPDATE motivation_plan_point SET mopp_PrivilegeType='Coupon', mopp_PrivilegeID=".$id." WHERE mopp_MotivationPointID='".$id_plan."'";
	// 		$oDB->QueryOne($do_sql_point);

	// 		$do_sql_point = "UPDATE motivation_plan_point SET mopp_PrivilegeType='None', mopp_PrivilegeID='0' WHERE mopp_MotivationPointID!='".$id_plan."' AND mopp_PrivilegeID='".$id."'";
	// 		$oDB->QueryOne($do_sql_point);

	// 	} else {

	// 		$do_sql_stamp = "UPDATE motivation_plan_stamp SET mops_PrivilegeType='Coupon', mops_PrivilegeID=".$id." WHERE mops_MotivationStampID='".$id_plan."'";
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

		$sql_check = 'SELECT cufo_CustomFormID FROM custom_form_privilege WHERE cufo_PrivilegeID="'.$id.'" AND cufo_Type="Coupon" AND cufi_CustomFieldID="'.$custom['cufi_CustomFieldID'].'"';

		$cufo_CustomFormID = $oDB->QueryOne($sql_check);

		if ($cufo_CustomFormID) {

			$do_sql_custom = "UPDATE custom_form_privilege SET ".$sql_custom_privilege." WHERE cufo_CustomFormID= '".$cufo_CustomFormID."'";

		} else {

			$sql_custom_privilege .= ",cufo_CreatedBy='".$_SESSION['UID']."'";

			$sql_custom_privilege .= ",cufo_CreatedDate='".$time_insert."'";

			$sql_custom_privilege .= ",cufo_Type='Coupon'";

			$sql_custom_privilege .= ",cufo_CustomFormID='".$custom_new."'";

			$sql_custom_privilege .= ",cufi_CustomFieldID='".$custom['cufi_CustomFieldID']."'";

			$sql_custom_privilege .= ",cufo_PrivilegeID='".$id."'";

			$do_sql_custom = 'INSERT INTO custom_form_privilege SET '.$sql_custom_privilege;
		}

		$oDB->QueryOne($do_sql_custom);

		$custom_new++;
	}

	echo '<script>window.location.href = "coupon.php";</script>';
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




$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('type_file_upload', '<span class="text-rq">Type file : .jpg , .png , .gif only</span>');

$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_coupon');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_privilege', 'in');

$oTmp->assign('content_file', 'coupon/coupon_create.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
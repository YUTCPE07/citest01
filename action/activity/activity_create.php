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
require_once('../../include/connect.php');

//========================================//

$oTmp = new TemplateEngine();

$oDB = new DBI();

if ($bDebug) {

	$oErr = new Tracker();
	$oDB->SetTracker($oErr);
}

//========================================//

if (($_SESSION['role_action']['activity']['add'] != 1) || ($_SESSION['role_action']['activity']['edit'] != 1)) {

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



# SEARCH MAX ACTIVITY ID

	$sql_get_last_ins = 'SELECT max(acti_ActivityID) FROM activity';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################


# SEARCH MAX PRIVILEGE LINK ID

	$sql_get_last_ins = 'SELECT max(prli_PrivilegeLinkID) FROM privilege_link';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$link_id_new = $id_last_ins+1;

#######################################


# SEARCH NAME OLD IMAGE

	$sql_get_old_img = 'SELECT acti_Image FROM activity WHERE acti_ActivityID='.$id;
	$get_old_img = $oDB->QueryOne($sql_get_old_img);
	$old_image = $get_old_img;

#######################################


# SEARCH MAX CUSTOM PRIVILEGE ID

	$sql_get_last_ins = 'SELECT max(cufo_CustomFormID) FROM custom_form_privilege';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$custom_new = $id_last_ins+1;

#######################################


if($Act == 'approve' && $id != '') {

	$sql = '';

	$sql .= 'SELECT acti_ImageNew, acti_Image, bran_BrandID FROM activity WHERE acti_ActivityID ="'.$id.'"';
	$oRes = $oDB->Query($sql);
	$axRow = $oRes->FetchRow(DBI_ASSOC);


	if($approve == 'unapprove') {

 		# UNAPPROVE

		// if ($axRow['acti_ImageNew']!="") {

		// 	unlink_file($oDB,'activity','acti_ImageNew','acti_ActivityID',$id,'../../upload/'.$axRow['bran_BrandID'].'/activity_upload/',$axRow['acti_ImageNew']);

		// 	$do_sql_upload=  "UPDATE activity SET acti_ImageNew='',acti_Status='Pending' WHERE acti_ActivityID='".$id."' ";

 		// 	$oDB->QueryOne($do_sql_upload);

		// } else if ($axRow['acti_Image']!=""){

		unlink_file($oDB,'activity','acti_Image','acti_ActivityID',$id,'../../upload/'.$axRow['bran_BrandID'].'/activity_upload/',$axRow['acti_Image']);

		$do_sql_upload = "UPDATE activity 
							SET acti_Image='',
								acti_Status='Pending',
								acti_Approve='',
								acti_UpdatedDate='".$time_insert."',
								acti_UpdatedBy='".$_SESSION['UID']."' 
							WHERE acti_ActivityID='".$id."' ";

 		$oDB->QueryOne($do_sql_upload);

		// }
 	}

	if ($approve == 'approve') {

		# APPROVE

		// if ($axRow['acti_Image']!="") {

		// 	unlink_file($oDB,'activity','acti_Image','acti_ActivityID',$id,'../../upload/'.$axRow['bran_BrandID'].'/activity_upload/',$axRow['acti_Image']);

		// 	$do_sql_upload = "UPDATE activity 

		// 						SET acti_Image='".$axRow['acti_ImageNew']."', 

		// 						acti_ImageNew='',

		// 						acti_UpdatedDate='".$time_insert."' 

		// 						WHERE acti_ActivityID='".$id."'";

		// } else {

		$do_sql_upload = "UPDATE activity 
							SET acti_Approve='T',
								acti_UpdatedDate='".$time_insert."',
								acti_UpdatedBy='".$_SESSION['UID']."'
							WHERE acti_ActivityID='".$id."'";
		// }

 		$oDB->QueryOne($do_sql_upload);
	}

	echo '<script>window.location.href = "activity_create.php?act=edit&id='.$id.'";</script>';


} else if( $Act == 'edit' && $id != '' ) {

	# EDIT

	$sql = 'SELECT a.*,
					b.name AS brand_name
				FROM activity a
				LEFT JOIN mi_brand b
				ON a.bran_BrandID = b.brand_id 
				WHERE a.acti_ActivityID = "'.$id.'" '.$where_brand.' ';

	$oRes = $oDB->Query($sql);

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;

		$axRow['hour_start'] = substr($axRow['acti_StartTime'], 0, 2);

		if ($axRow['hour_start'] == "00") { $axRow['hour_start'] = ''; }

		$axRow['minute_start'] = substr($axRow['acti_StartTime'], 3, 2);

		$axRow['hour_end'] = substr($axRow['acti_EndTime'], 0, 2);

		if ($axRow['hour_end'] == "00") { $axRow['hour_end'] = ''; }

		$axRow['minute_end'] = substr($axRow['acti_EndTime'], 3, 2);

		$axRow['rhour_start'] = substr($axRow['acti_StartTimeReservation'], 0, 2);

		if ($axRow['rhour_start'] == "00") { $axRow['rhour_start'] = ''; }

		$axRow['rminute_start'] = substr($axRow['acti_StartTimeReservation'], 3, 2);

		$axRow['rhour_end'] = substr($axRow['acti_EndTimeReservation'], 0, 2);

		if ($axRow['rhour_end'] == "00") { $axRow['rhour_end'] = ''; }

		$axRow['rminute_end'] = substr($axRow['acti_EndTimeReservation'], 3, 2);

		$asData = $axRow;
	}


} else if( $Act == 'save' ) {

	$id = trim_txt($_REQUEST['id']);

	$acti_Name = trim_txt($_REQUEST['acti_Name']);

	$bran_BrandID = trim_txt($_REQUEST['bran_BrandID']);

	$acti_Status = trim_txt($_REQUEST['acti_Status']);

	$acti_CardRegister = trim_txt($_REQUEST['acti_CardRegister']);

	// $acti_Motivation = trim_txt($_REQUEST['acti_Motivation']);

	$acti_Method = trim_txt($_REQUEST['acti_Method']);

	$acti_StartDate = trim_txt($_REQUEST['StartDate']);

	$acti_EndDate = trim_txt($_REQUEST['EndDate']);

	$hour_start = trim_txt($_REQUEST['hour_start']);

	$minute_start = trim_txt($_REQUEST['minute_start']);

	$ap_start = trim_txt($_REQUEST['ap_start']);

	$hour_end = trim_txt($_REQUEST['hour_end']);

	$minute_end = trim_txt($_REQUEST['minute_end']);

	$ap_end = trim_txt($_REQUEST['ap_end']);

	$acti_Description = trim_txt($_REQUEST['acti_Description']);

	$prty_PrivilegeTypeID = trim_txt($_REQUEST['prty_PrivilegeTypeID']);

	$prca_ProductCategoryID = trim_txt($_REQUEST['prca_ProductCategoryID']);

	$prod_ProductID = trim_txt($_REQUEST['prod_ProductID']);

	$acti_Qty = trim_txt($_REQUEST['acti_Qty']);

	$acti_QtyPer = trim_txt($_REQUEST['QtyPer']);

	$acti_TotalQty = trim_txt($_REQUEST['acti_TotalQty']);

	$acti_Transfer = trim_txt($_REQUEST['acti_Transfer']);

	$acti_Repetition = trim_txt($_REQUEST['acti_Repetition']);

	$acti_SpecialPeriodType = trim_txt($_REQUEST['acti_SpecialPeriodType']);

	$acti_RepetitionMember = trim_txt($_REQUEST['acti_RepetitionMember']);

	$acti_QtyMember = trim_txt($_REQUEST['acti_QtyMember']);

	$acti_QtyPerMember = trim_txt($_REQUEST['QtyPerMember']);

	$acti_MaxQty = trim_txt($_REQUEST['acti_MaxQty']);

	$acti_Reservation = trim_txt($_REQUEST['acti_Reservation']);

	$acti_StartDateReservation = trim_txt($_REQUEST['RStartDate']);

	$acti_EndDateReservation = trim_txt($_REQUEST['REndDate']);

	$hour_rstart = trim_txt($_REQUEST['hour_rstart']);

	$minute_rstart = trim_txt($_REQUEST['minute_rstart']);

	$hour_rend = trim_txt($_REQUEST['hour_rend']);

	$minute_rend = trim_txt($_REQUEST['minute_rend']);

	$acti_TrackLike = trim_txt($_REQUEST['acti_TrackLike']);

	$acti_TrackReview = trim_txt($_REQUEST['acti_TrackReview']);

	$acti_TrackRequest = trim_txt($_REQUEST['acti_TrackRequest']);

	$acti_TrackShare = trim_txt($_REQUEST['acti_TrackShare']);

	$acti_Latitude = trim_txt($_REQUEST['acti_Latitude']);

	$acti_Longitude = trim_txt($_REQUEST['acti_Longitude']);

	$acti_Location = trim_txt($_REQUEST['acti_Location']);

	$acti_StartDateSell = trim_txt($_REQUEST['StartDateSell']);

	$acti_EndDateSell = trim_txt($_REQUEST['EndDateSell']);

	$acti_Price = trim_txt($_REQUEST['acti_Price']);

	$acti_Cost = trim_txt($_REQUEST['acti_Cost']);

	$acti_Payment = trim_txt($_REQUEST['acti_Payment']);

	$acti_Condition = trim_txt($_REQUEST['acti_Condition']);

	$acti_Exception = trim_txt($_REQUEST['acti_Exception']);

	$acti_HowToUse = trim_txt($_REQUEST['acti_HowToUse']);

	$acti_Note = trim_txt($_REQUEST['acti_Note']);

	$acti_DateStatus = trim_txt($_REQUEST['AutoDate']);

	$acti_Motivation = trim_txt($_REQUEST['acti_Motivation']);

	$acti_Hidden = trim_txt($_REQUEST['acti_Hidden']);

	$choose_upload_default = trim_txt($_REQUEST['choose_upload_default']);


	$data = "";

	foreach ($_POST['QtyPerData'] as $acti_QtyPerData)

		$data .= $acti_QtyPerData.",";

	$str_data = strlen($data);

	$acti_QtyPerData = substr($data,0,$str_data-1);


	$data_member = "";

	foreach ($_POST['QtyPerMemberData'] as $acti_QtyPerMemberData)

		$data_member .= $acti_QtyPerMemberData.",";

	$str_data = strlen($data_member);

	$acti_QtyPerMemberData = substr($data_member,0,$str_data-1);





	$sql_activity = '';

	$table_activity = 'activity';

	$sql_privilege_link = '';

	$table_privilege_link = 'privilege_link';




	if($acti_Name){	$sql_activity .= 'acti_Name="'.$acti_Name.'"';   }

	if($bran_BrandID){	$sql_activity .= ',bran_BrandID="'.$bran_BrandID.'"';   }

	$sql_activity .= ',acti_CardRegister="Yes"'; 

	if($acti_Method){	$sql_activity .= ',acti_Method="'.$acti_Method.'"';   }

	if($acti_StartDate){	$sql_activity .= ',acti_StartDate="'.$acti_StartDate.'"';   }

	if($acti_EndDate){	$sql_activity .= ',acti_EndDate="'.$acti_EndDate.'"';   }

	$acti_StartTime = $hour_start.":".$minute_start.":00";

	$sql_activity .= ',acti_StartTime="'.$acti_StartTime.'"';  

	$acti_EndTime = $hour_end.":".$minute_end.":00";

	$sql_activity .= ',acti_EndTime="'.$acti_EndTime.'"';  

	$sql_activity .= ',prty_PrivilegeTypeID="'.$prty_PrivilegeTypeID.'"';   

	if($prod_ProductID){

		$sql_activity .= ',prod_ProductID="'.$prod_ProductID.'"';

		$sql_activity .= ',prca_ProductCategoryID="'.$prca_ProductCategoryID.'"';

	} else {	$sql_activity .= ',prca_ProductCategoryID="0"';	}

	$sql_activity .= ',acti_TotalQty="'.$acti_TotalQty.'"';  

	$sql_activity .= ',acti_Transfer="'.$acti_Transfer.'"';   

	if($acti_Repetition && $acti_Qty && $acti_QtyPer && $acti_QtyPerData){

		$sql_activity .= ',acti_Qty="'.$acti_Qty.'"';

		$sql_activity .= ',acti_QtyPer="'.$acti_QtyPer.'"';

		$sql_activity .= ',acti_Repetition="'.$acti_Repetition.'"';

		$sql_activity .= ',acti_QtyPerData="'.$acti_QtyPerData.'"';

	} else {

		$sql_activity .= ',acti_Qty="0"';

		$sql_activity .= ',acti_Repetition=""';

		$sql_activity .= ',acti_QtyPer=""';

		$sql_activity .= ',acti_QtyPerData=""';
	}

	if($acti_RepetitionMember && $acti_QtyMember && $acti_QtyPerMember && $acti_QtyPerMemberData){

		$sql_activity .= ',acti_QtyMember="'.$acti_QtyMember.'"';

		$sql_activity .= ',acti_QtyPerMember="'.$acti_QtyPerMember.'"';

		$sql_activity .= ',acti_RepetitionMember="'.$acti_RepetitionMember.'"';

		$sql_activity .= ',acti_QtyPerMemberData="'.$acti_QtyPerMemberData.'"';

	} else {

		$sql_activity .= ',acti_QtyMember="0"';

		$sql_activity .= ',acti_RepetitionMember=""';

		$sql_activity .= ',acti_QtyPerMember=""';

		$sql_activity .= ',acti_QtyPerMemberData=""';
	}

	$sql_activity .= ',acti_SpecialPeriodType="'.$acti_SpecialPeriodType.'"'; 

	$sql_activity .= ',acti_MaxQty="'.$acti_MaxQty.'"';   

	$sql_activity .= ',acti_Reservation="'.$acti_Reservation.'"';   

	$sql_activity .= ',acti_StartDateReservation="'.$acti_StartDateReservation.'"';   

	$sql_activity .= ',acti_EndDateReservation="'.$acti_EndDateReservation.'"';   

	$acti_StartTimeReservation = $hour_rstart.":".$minute_rstart.":00";

	$sql_activity .= ',acti_StartTimeReservation="'.$acti_StartTimeReservation.'"';  

	$acti_EndTimeReservation = $hour_rend.":".$minute_rend.":00";

	$sql_activity .= ',acti_EndTimeReservation="'.$acti_EndTimeReservation.'"';  

	$sql_activity .= ',acti_TrackLike="'.$acti_TrackLike.'"';   

	$sql_activity .= ',acti_TrackReview="'.$acti_TrackReview.'"';   

	$sql_activity .= ',acti_TrackRequest="'.$acti_TrackRequest.'"';   

	$sql_activity .= ',acti_TrackShare="'.$acti_TrackShare.'"';   

	$sql_activity .= ',acti_Location="'.$acti_Location.'"';   

	$sql_activity .= ',acti_Latitude="'.$acti_Latitude.'"';   

	$sql_activity .= ',acti_Longitude="'.$acti_Longitude.'"';   

	$sql_activity .= ',acti_StartDateSell="'.$acti_StartDateSell.'"';   

	$sql_activity .= ',acti_EndDateSell="'.$acti_EndDateSell.'"';   

	$sql_activity .= ',acti_Price="'.$acti_Price.'"';   

	$sql_activity .= ',acti_Cost="'.$acti_Cost.'"';   

	$sql_activity .= ',acti_Payment="'.$acti_Payment.'"';  

	$sql_activity .= ',acti_Description="'.$acti_Description.'"';   

	$sql_activity .= ',acti_Condition="'.$acti_Condition.'"';   

	$sql_activity .= ',acti_Exception="'.$acti_Exception.'"';   

	$sql_activity .= ',acti_HowToUse="'.$acti_HowToUse.'"';   

	$sql_activity .= ',acti_Note="'.$acti_Note.'"';

	if ($acti_Hidden=='Yes') { $sql_activity .= ',acti_Hidden="'.$acti_Hidden.'"'; }
	else { $sql_activity .= ',acti_Hidden="No"'; } 

	$sql_activity .= ',acti_UpdatedDate="'.$time_insert.'"'; 

	$sql_activity .= ',acti_DateStatus="'.$acti_DateStatus.'"';   

	$sql_activity .= ',acti_UpdatedBy="'.$_SESSION['UID'].'"';   

	if( $_FILES["activity_image_upload"]["name"] != "" && $choose_upload_default==1){

		$new_img_name = upload_img('activity_image_upload','activity_'.$time_insert_pic,'../../upload/'.$bran_BrandID.'/activity_upload/',640,400);

		if($new_img_name){

			// $sql_activity .= ',acti_ImageNew="'.$new_img_name.'"';

			// if ($old_image!="") {	$sql_activity .= ',acti_Status="'.$acti_Status.'"';	}

			// else {	$sql_activity .= ',acti_Status="Pending"';	}

			$sql_activity .= ',acti_Image="'.$new_img_name.'"';

			$sql_activity .= ',acti_Status="'.$acti_Status.'"';

			$sql_activity .= ',acti_Approve=""';

			if ($old_image!="") {

				unlink_file($oDB,'activity','acti_Image','acti_ActivityID',$id,'../../upload/'.$bran_BrandID.'/activity_upload/',$old_image);
			}
		}

	} else if($choose_upload_default==2){

		$new_img_name = 'activity_'.$bran_BrandID.'.jpg';

		$exp_name = explode('.','../../upload/'.$bran_BrandID.'/activity_upload/'.$new_img_name);

		$i = count($exp_name)-1;

		$type = $exp_name[$i];

		$img_name = 'activity_'.$time_insert_pic.'.'.$type;

		copy($path_upload_activity.$new_img_name,'../../upload/'.$bran_BrandID.'/activity_upload/'.$img_name);

		unlink_file($oDB,'activity','acti_Image','acti_ActivityID',$id,'../../upload/'.$bran_BrandID.'/activity_upload/',$old_image);

		unlink('../../upload/'.$bran_BrandID.'/activity_upload/'.$new_img_name);

		unlink_file($oDB,'activity','acti_ImageNew','acti_ActivityID',$id,'../../upload/'.$bran_BrandID.'/activity_upload/',$acti_ImageNew);

		unlink('../../upload/'.$bran_BrandID.'/activity_upload/'.$acti_ImageNew);

		$sql_activity .= ',acti_Image="'.$img_name.'"';

		$sql_activity .= ',acti_ImageNew=""';

		$sql_activity .= ',acti_Status="'.$acti_Status.'"';

		$sql_activity .= ',acti_Approve="T"';

	} else {

		$sql_activity .= ',acti_Status="Pending"';
	}


	if ($old_image!="") {

		$sql_activity .= ',acti_Status="'.$acti_Status.'"';
	}




	# MOTIVATION PLAN

	// if ($acti_Motivation == 'None') {

	// 	$sql_activity .= ',acti_Motivation="None"';

	// 	$sql_activity .= ',acti_MotivationID="0"';

	// } else {

	// 	$type = substr($acti_Motivation,0,1);

	// 	$id_plan = substr($acti_Motivation,1);

	// 	if ($type == 'p') {

	// 		$sql_activity .= ',acti_Motivation="Point"';

	// 		$sql_activity .= ',acti_MotivationID="'.$id_plan.'"';

	// 	} else {

	// 		$sql_activity .= ',acti_Motivation="Stamp"';

	// 		$sql_activity .= ',acti_MotivationID="'.$id_plan.'"';
	// 	}
	// }


	# PRIVILEGE LINK

	if($time_insert){	$sql_privilege_link = 'prli_UpdatedDate="'.$time_insert.'"';   }

	if($_SESSION['UID']){	$sql_privilege_link .= ',prli_UpdatedBy="'.$_SESSION['UID'].'"';   }

	if($acti_Status){	$sql_privilege_link .= ',prli_Status="'.$acti_Status.'"';   }



	if($id){

		$do_sql_activity = "UPDATE ".$table_activity." SET ".$sql_activity." WHERE acti_ActivityID= '".$id."'";

		$do_sql_privilege_link = "UPDATE ".$table_privilege_link." SET ".$sql_privilege_link." WHERE acti_ActivityID= '".$id."'";

		if ($acti_Status=="Active") {	$motivation_status = "T";	}

		if ($acti_Status=="Pending") {	$motivation_status = "F";	}

		$do_sql_point = "UPDATE motivation_point SET mopo_Status='".$motivation_status."' WHERE acti_ActivityID= '".$id."'";

		$oDB->QueryOne($do_sql_point);

		$do_sql_stamp = "UPDATE motivation_stamp SET most_Status='".$motivation_status."' WHERE acti_ActivityID= '".$id."'";

		$oDB->QueryOne($do_sql_stamp);

	} else {

		if($time_insert){	$sql_activity .= ',acti_CreatedDate="'.$time_insert.'"';   }

		if($_SESSION['UID']){	$sql_activity .= ',acti_CreatedBy="'.$_SESSION['UID'].'"';   }

		if($id_new){	$sql_activity .= ',acti_ActivityID="'.$id_new.'"';   }

		$sql_activity .= ',acti_ImagePath="'.$bran_BrandID.'/activity_upload/"';

		$do_sql_activity = 'INSERT INTO '.$table_activity.' SET '.$sql_activity;

		if($time_insert){	$sql_privilege_link .= ',prli_CreatedDate="'.$time_insert.'"';   }

		if($_SESSION['UID']){	$sql_privilege_link .= ',prli_CreatedBy="'.$_SESSION['UID'].'"';   }

		if($id_new){	$sql_privilege_link .= ',acti_ActivityID="'.$id_new.'"';   }

		if($link_id_new){	$sql_privilege_link .= ',prli_PrivilegeLinkID="'.$link_id_new.'"';   }

		$do_sql_privilege_link = 'INSERT INTO '.$table_privilege_link.' SET '.$sql_privilege_link;

		$id = $id_new;
	}


	$oDB->QueryOne($do_sql_activity);

	$oDB->QueryOne($do_sql_privilege_link);



	# MOTIVATION PLAN

	// if ($priv_Motivation == 'None') {

	// 	$do_sql_point = "UPDATE motivation_plan_point SET mopp_PrivilegeType='None', mopp_PrivilegeID=0 WHERE mopp_PrivilegeType='Activity' AND mopp_PrivilegeID='".$id."'";
	// 	$oDB->QueryOne($do_sql_point);

	// 	$do_sql_stamp = "UPDATE motivation_plan_stamp SET mops_PrivilegeType='None', mops_PrivilegeID=0 WHERE mops_PrivilegeType='Activity' AND mops_PrivilegeID='".$id."'";
	// 	$oDB->QueryOne($do_sql_stamp);

	// } else {

	// 	if ($type == 'p') {

	// 		$do_sql_point = "UPDATE motivation_plan_point SET mopp_PrivilegeType='Activity', mopp_PrivilegeID=".$id." WHERE mopp_MotivationPointID='".$id_plan."'";
	// 		$oDB->QueryOne($do_sql_point);

	// 		$do_sql_point = "UPDATE motivation_plan_point SET mopp_PrivilegeType='None', mopp_PrivilegeID='0' WHERE mopp_MotivationPointID!='".$id_plan."' AND mopp_PrivilegeID='".$id."'";
	// 		$oDB->QueryOne($do_sql_point);

	// 	} else {

	// 		$do_sql_stamp = "UPDATE motivation_plan_stamp SET mops_PrivilegeType='Activity', mops_PrivilegeID=".$id." WHERE mops_MotivationStampID='".$id_plan."'";
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

		$sql_check = 'SELECT cufo_CustomFormID FROM custom_form_privilege WHERE cufo_PrivilegeID="'.$id.'" AND cufo_Type="Activity" AND cufi_CustomFieldID="'.$custom['cufi_CustomFieldID'].'"';

		$cufo_CustomFormID = $oDB->QueryOne($sql_check);

		if ($cufo_CustomFormID) {

			$do_sql_custom = "UPDATE custom_form_privilege SET ".$sql_custom_privilege." WHERE cufo_CustomFormID= '".$cufo_CustomFormID."'";

		} else {

			$sql_custom_privilege .= ",cufo_CreatedBy='".$_SESSION['UID']."'";

			$sql_custom_privilege .= ",cufo_CreatedDate='".$time_insert."'";

			$sql_custom_privilege .= ",cufo_Type='Activity'";

			$sql_custom_privilege .= ",cufo_CustomFormID='".$custom_new."'";

			$sql_custom_privilege .= ",cufi_CustomFieldID='".$custom['cufi_CustomFieldID']."'";

			$sql_custom_privilege .= ",cufo_PrivilegeID='".$id."'";

			$do_sql_custom = 'INSERT INTO custom_form_privilege SET '.$sql_custom_privilege;
		}

		$oDB->QueryOne($do_sql_custom);

		$custom_new++;
	}


	echo '<script>window.location.href = "activity.php";</script>';

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




#  privilege_type_id dropdownlist

$as_privilege_type_id = dropdownlist_from_table($oDB,'mi_privilege_type','privilege_type_id','name','prty_Type="Activity"');

$oTmp->assign('privilege_type_id_opt', $as_privilege_type_id);




#  category_id dropdownlist

$as_category_id = dropdownlist_from_table($oDB,'mi_products_category','category_id','name');

$oTmp->assign('category_id_opt', $as_category_id);




# category_product dropdownlist

$as_category_product = dropdownlist_from_table($oDB,'mi_products_category_tb','id','name','product_category_id','=1') ;

$oTmp->assign('category_product_opt', $as_category_product);

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' and brand_id="'.$_SESSION['user_brand_id'].'" ';
}




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

$oTmp->assign('is_menu', 'is_activity');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_privilege', 'in');

$oTmp->assign('content_file', 'activity/activity_create.htm');

$oTmp->display('layout/template.html');


//========================================//


$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
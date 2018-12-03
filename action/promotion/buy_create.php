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

if (($_SESSION['role_action']['promotion']['add'] != 1) || ($_SESSION['role_action']['promotion']['edit'] != 1)) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];
$id = $_REQUEST['id'];
$time_insert = date("Y-m-d H:i:s");
$time_insert_pic = date("Ymd_His");
$time_pic = date("Ymd");


$where_brand = '';


if ($_SESSION['user_type_id_ses']>1 ) {

	$where_brand = ' AND h.bran_BrandID = "'.$_SESSION['user_brand_id'].'"';
}


# SEARCH MAX COUPON ID

	$sql_get_last_ins = 'SELECT max(coup_CouponID) FROM hilight_coupon';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################


# SEARCH NAME OLD IMAGE

	$sql_get_old_img = 'SELECT coup_Image FROM hilight_coupon WHERE coup_CouponID='.$id;
	$get_old_img = $oDB->QueryOne($sql_get_old_img);
	$old_image = $get_old_img;

#######################################


# SEARCH CHARGE

	$sql_promotion_charge = 'SELECT promotion_charge FROM mi_setting';
	$promotion_charge = $oDB->QueryOne($sql_promotion_charge);

#######################################


# SEARCH SERVICE FEE

	$sql_promotion_service_fee = 'SELECT promotion_service_fee FROM mi_setting';
	$promotion_service_fee = $oDB->QueryOne($sql_promotion_service_fee);

#######################################



if( $Act == 'edit' && $id != '' ) {

	# EDIT

	$sql = '';

	$sql .= 'SELECT h.*,
					b.tax_type,
					b.tax_vat,
					b.name AS brand_name

				FROM hilight_coupon AS h

				LEFT JOIN mi_brand AS b
				ON b.brand_id = h.bran_BrandID

				WHERE h.coup_CouponID = "'.$id.'" 
				'.$where_brand.' ';

	$oRes = $oDB->Query($sql);

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;

		$axRow['hour_start'] = substr($axRow['coup_StartTime'],0,2);
		$axRow['minute_start'] = substr($axRow['coup_StartTime'],3,2);
		$axRow['hour_end'] = substr($axRow['coup_EndTime'],0,2);
		$axRow['minute_end'] = substr($axRow['coup_EndTime'],3,2);

		$axRow['coup_Website'] = substr($axRow['coup_Website'],7);
		$axRow['coup_Facebook'] = substr($axRow['coup_Facebook'],21);

		$asData = $axRow;

		
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
									<td style="text-align:center" rowspan="3">'.$name.'</td>
									<td style="text-align:center" rowspan="3"><img src="../../upload/'.$axRow['coup_QrPath'].'QHC-'.str_pad($id,4,"0",STR_PAD_LEFT).'-'.str_pad($branch[$x],4,"0",STR_PAD_LEFT).'.png" width="90" height="90" class="image_border"/><br><a target="_blank" href="earn_attention_qrcode.php?id='.$axRow['coup_CouponID'].'&branch='.$branch[$x].'">QRCode Link</a></td>
									<td style="text-align:center"><a target="_blank" href="earn_attention_a4.php?id='.$axRow['coup_CouponID'].'&branch='.$branch[$x].'">A4</a></td>
								</tr>
								<tr>
									<td style="text-align:center"><a target="_blank" href="earn_attention_a5.php?id='.$axRow['coup_CouponID'].'&branch='.$branch[$x].'">A5</a></td>
								</tr>
								<tr>
									<td style="text-align:center"><a target="_blank" href="earn_attention_a6.php?id='.$axRow['coup_CouponID'].'&branch='.$branch[$x].'">A6</a></td>
								</tr>';
			}

		} else {

			$data_branch = '<tr><td colspan="3" style="text-align:center">No Branch Data</td></tr>';
		}

		$oTmp->assign('branch_data', $data_branch);


		# QR CODE

        $data_qrcode = '<div class="adj_row">
							<label class="lable-form">QRCODE</label>
							<img src="../../upload/'.$axRow['coup_QrPath'].'QCH-'.str_pad($id,4,"0",STR_PAD_LEFT).'-'.str_pad($axRow['bran_BrandID'],4,"0",STR_PAD_LEFT).'.png" width="150" height="150" class="img_upload">
                		</div>';

		$oTmp->assign('data_qrcode', $data_qrcode);
	}

} else if( $Act == 'save' ){

	$id = trim_txt($_REQUEST['id']);

	$coup_Name = trim_txt($_REQUEST['coup_Name']);

	$bran_BrandID = trim_txt($_REQUEST['bran_BrandID']);

	$coup_Status = trim_txt($_REQUEST['coup_Status']);

	$coup_DisplayData = trim_txt($_REQUEST['coup_DisplayData']);

	$vari_VarietyID = trim_txt($_REQUEST['vari_VarietyID']);

	$coup_SendEmail = trim_txt($_REQUEST['coup_SendEmail']);

	$coup_Type = "Buy";

	$coup_Method = "Fix";

	$coup_MethodUse = trim_txt($_REQUEST['coup_MethodUse']);

	$coup_StartDateUse = trim_txt($_REQUEST['StartDateUse']);

	$coup_EndDateUse = trim_txt($_REQUEST['EndDateUse']);

	$coup_StartDate = trim_txt($_REQUEST['StartDate']);

	$coup_EndDate = trim_txt($_REQUEST['EndDate']);

	$coup_Time = trim_txt($_REQUEST['coup_Time']);

	$hour_start = trim_txt($_REQUEST['hour_start']);

	$minute_start = trim_txt($_REQUEST['minute_start']);

	$hour_end = trim_txt($_REQUEST['hour_end']);

	$minute_end = trim_txt($_REQUEST['minute_end']);

	$coup_Description = trim_txt($_REQUEST['coup_Description']);

	$prty_PrivilegeTypeID = trim_txt($_REQUEST['prty_PrivilegeTypeID']);

	$coup_Qty = trim_txt($_REQUEST['coup_Qty']);

	$coup_QtyPer = trim_txt($_REQUEST['QtyPer']);

	$coup_TotalQty = trim_txt($_REQUEST['coup_TotalQty']);

	$coup_Repetition = trim_txt($_REQUEST['coup_Repetition']);

	$coup_SpecialPeriodType = trim_txt($_REQUEST['coup_SpecialPeriodType']);

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

	$coup_Contact = trim_txt($_REQUEST['coup_Contact']);

	$coup_VatType = trim_txt($_REQUEST['vat_type']);

	$coup_Payment = trim_txt($_REQUEST['coup_Payment']);

	$coup_Condition = trim_txt($_REQUEST['coup_Condition']);

	$coup_Exception = trim_txt($_REQUEST['coup_Exception']);

	$coup_HowToUse = trim_txt($_REQUEST['coup_HowToUse']);

	$coup_Participation = trim_txt($_REQUEST['coup_Participation']);

	$coup_Note = trim_txt($_REQUEST['coup_Note']);

	$coup_DateStatus = trim_txt($_REQUEST['AutoDate']);

	$coup_Motivation = trim_txt($_REQUEST['coup_Motivation']);

	$coup_Hidden = trim_txt($_REQUEST['coup_Hidden']);

	$coup_Information = trim_txt($_REQUEST['coup_Information']);

	$choose_upload_default = trim_txt($_REQUEST['choose_upload_default']);

	$coup_Website = trim_txt($_REQUEST['coup_Website']);

	$coup_Facebook = trim_txt($_REQUEST['coup_Facebook']);

	$coup_Video = trim_txt($_REQUEST['coup_Video']);

	$coup_ActivityDuration = trim_txt($_REQUEST['coup_ActivityDuration']);


	$coup_Benefits1 = trim_txt($_REQUEST['coup_Benefits1']);
	$coup_Benefits2 = trim_txt($_REQUEST['coup_Benefits2']);
	$coup_Benefits3 = trim_txt($_REQUEST['coup_Benefits3']);
	$coup_Benefits4 = trim_txt($_REQUEST['coup_Benefits4']);
	$coup_Benefits5 = trim_txt($_REQUEST['coup_Benefits5']);

	$coup_Differences1 = trim_txt($_REQUEST['coup_Differences1']);
	$coup_Differences2 = trim_txt($_REQUEST['coup_Differences2']);
	$coup_Differences3 = trim_txt($_REQUEST['coup_Differences3']);
	$coup_Differences4 = trim_txt($_REQUEST['coup_Differences4']);
	$coup_Differences5 = trim_txt($_REQUEST['coup_Differences5']);


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


	foreach ($_POST['brnc_BranchID'] as $branch_id) {

		$branch_data .= $branch_id.",";

		if ($id) { 

			$qrcode .= 'QHC-'.str_pad($id,4,"0",STR_PAD_LEFT).'-'.str_pad($branch_id,4,"0",STR_PAD_LEFT).",";

		} else { 

			$qrcode .= 'QHC-'.str_pad($id_new,4,"0",STR_PAD_LEFT).'-'.str_pad($branch_id,4,"0",STR_PAD_LEFT).","; 
		}
	}



	$str_branch = strlen($branch_data);

	$branch_data = substr($branch_data,0,$str_branch-1);

	$qrcode = substr($qrcode,0,$str_branch-1);



	$sql_coupon = '';

	$table_coupon = 'hilight_coupon';

	if($coup_Name){	$sql_coupon .= 'coup_Name="'.$coup_Name.'"';   }

	if($bran_BrandID){	$sql_coupon .= ',bran_BrandID="'.$bran_BrandID.'"';   }

	if($coup_DisplayData){	$sql_coupon .= ',coup_DisplayData="'.$coup_DisplayData.'"';   }

	if($vari_VarietyID){	$sql_coupon .= ',vari_VarietyID="'.$vari_VarietyID.'"';   }

	if($coup_Method){	$sql_coupon .= ',coup_Method="Fix"';   }

	if($branch_data){	$sql_coupon .= ',brnc_BranchID="'.$branch_data.'"';   }

	if($coup_Type){	$sql_coupon .= ',coup_Type="'.$coup_Type.'"';   }

	if($coup_MethodUse){	$sql_coupon .= ',coup_MethodUse="'.$coup_MethodUse.'"';   }

	if ($coup_MethodUse == 'Fix') {

		$sql_coupon .= ',coup_StartDateUse="'.$coup_StartDateUse.'"';  

		$sql_coupon .= ',coup_EndDateUse="'.$coup_EndDateUse.'"';  

	} else {

		$sql_coupon .= ',coup_StartDateUse="0000-00-00"';  

		$sql_coupon .= ',coup_EndDateUse="0000-00-00"';  
	}

	if ($coup_Method == 'Fix') {

		$sql_coupon .= ',coup_StartDate="'.$coup_StartDate.'"';  

		$sql_coupon .= ',coup_EndDate="'.$coup_EndDate.'"';  

	} else {

		$sql_coupon .= ',coup_StartDate="0000-00-00"';  

		$sql_coupon .= ',coup_EndDate="0000-00-00"';  
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

	$sql_coupon .= ',coup_SpecialPeriodType="'.$coup_SpecialPeriodType.'"';  

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

	$sql_coupon .= ',coup_Description="'.$coup_Description.'"';   

	$sql_coupon .= ',coup_Location="'.$coup_Location.'"';  

	$sql_coupon .= ',coup_StartDateSell="'.$coup_StartDateSell.'"';   

	$sql_coupon .= ',coup_EndDateSell="'.$coup_EndDateSell.'"';   

	$sql_coupon .= ',coup_Price="'.$coup_Price.'"';   

	$sql_coupon .= ',coup_Cost="'.$coup_Cost.'"';

	$sql_coupon .= ',coup_Contact="'.$coup_Contact.'"';   

	$sql_coupon .= ',coup_Payment="'.$coup_Payment.'"'; 

	$sql_coupon .= ',coup_ChargePercent="'.$promotion_charge.'"'; 

	$sql_coupon .= ',coup_ExpenseFee="'.$promotion_service_fee.'"';

	if($coup_VatType){	$sql_coupon .= ',coup_VatType="'.$coup_VatType.'"';   } 
	else {	$sql_coupon .= ',coup_VatType="1"';	}


	# SEARCH TAX_TYPE BRAND

		$sql_get_tax = 'SELECT tax_type FROM mi_brand WHERE brand_id='.$bran_BrandID;
		$brand_tax = $oDB->QueryOne($sql_get_tax);

	#######################################


	# SEARCH TAX_VAT BRAND

		$sql_get_vat = 'SELECT tax_vat FROM mi_brand WHERE brand_id='.$bran_BrandID;
		$brand_vat = $oDB->QueryOne($sql_get_vat);

	#######################################
		
	if ($brand_tax == 1) {

		$coup_Vat = $coup_Price*($brand_vat/100);
		
		$sql_coupon .= ',coup_Vat="'.$coup_Vat.'"'; 

		if ($coup_VatType == 1) {

			$coup_Amount = $coup_Price-($coup_Price*($brand_vat/100));
			
			$sql_coupon .= ',coup_Amount="'.$coup_Amount.'"'; 

		} else { 

			$coup_Amount = $coup_Price;
			
			$sql_coupon .= ',coup_Amount="'.$coup_Price.'"'; 
		}

	} else {
		
		$sql_coupon .= ',coup_Vat=0.00';
		
		$sql_coupon .= ',coup_Amount="'.$coup_Price.'"'; 
	}

	$sql_coupon .= ',coup_Condition="'.$coup_Condition.'"';   

	$sql_coupon .= ',coup_Exception="'.$coup_Exception.'"';   

	$sql_coupon .= ',coup_HowToUse="'.$coup_HowToUse.'"';    

	$sql_coupon .= ',coup_Participation="'.$coup_Participation.'"'; 

	$sql_coupon .= ',coup_Note="'.$coup_Note.'"';  

	$sql_coupon .= ',coup_ActivityDuration="'.$coup_ActivityDuration.'"';   

	$sql_coupon .= ',coup_Status="'.$coup_Status.'"';

	$sql_coupon .= ',coup_DateStatus="'.$coup_DateStatus.'"'; 

	$sql_coupon .= ',coup_SendEmail="'.$coup_SendEmail.'"';

	if ($coup_Website) { $sql_coupon .= ',coup_Website="http://'.$coup_Website.'"'; } 
	else { $sql_coupon .= ',coup_Website=""'; }   

	if ($coup_Facebook) { $sql_coupon .= ',coup_Facebook="https://facebook.com/'.$coup_Facebook.'"'; } 
	else { $sql_coupon .= ',coup_Facebook=""'; } 

	if ($coup_Video) { $sql_coupon .= ',coup_Video="https://www.youtube.com/'.$coup_Video.'"'; } 
	else { $sql_coupon .= ',coup_Video=""'; } 

	if ($coup_Hidden=='Yes') { $sql_coupon .= ',coup_Hidden="'.$coup_Hidden.'"'; }
	else { $sql_coupon .= ',coup_Hidden="No"'; } 

	if ($coup_Information=='Yes') { $sql_coupon .= ',coup_Information="'.$coup_Information.'"'; }
	else { $sql_coupon .= ',coup_Information="No"'; } 

	$sql_coupon .= ',coup_ImagePath="'.$bran_BrandID.'/earn_attention_upload/"';

	$sql_coupon .= ',coup_QrPath="'.$bran_BrandID.'/qr_earn_attention_upload/"';

	if($time_insert){	$sql_coupon .= ',coup_UpdatedDate="'.$time_insert.'"';   }

	if($_SESSION['UID']){	$sql_coupon .= ',coup_UpdatedBy="'.$_SESSION['UID'].'"';   }

	if( $_FILES["coupon_image_upload"]["name"] != "" && $choose_upload_default==1){

		$new_img_name = upload_img('coupon_image_upload','coupon_'.$time_insert_pic,'../../upload/'.$bran_BrandID.'/earn_attention_upload/',640,400);

		if($new_img_name){ $sql_coupon .= ',coup_Image="'.$new_img_name.'"'; }
		
	} else if($choose_upload_default==2){

		$new_img_name = 'coupon_'.$bran_BrandID.'.jpg';

		$exp_name = explode('.','../../upload/'.$bran_BrandID.'/earn_attention_upload/'.$new_img_name);

		$i = count($exp_name)-1;

		$type = $exp_name[$i];

		$img_name = 'coupon_'.$time_insert_pic.'.'.$type;

		copy('../../upload/'.$bran_BrandID.'/earn_attention_upload/'.$new_img_name,'../../upload/'.$bran_BrandID.'/earn_attention_upload/'.$img_name);

		unlink_file($oDB,'coupon','coup_Image','coup_CouponID',$id,'../../upload/'.$bran_BrandID.'/earn_attention_upload/',$old_image);

		unlink('../../upload/'.$bran_BrandID.'/earn_attention_upload/'.$old_image);

		$sql_coupon .= ',coup_Image="'.$img_name.'"';
	}

	$sql_coupon .= ',coup_Benefits1="'.$coup_Benefits1.'"';
	$sql_coupon .= ',coup_Benefits2="'.$coup_Benefits2.'"';
	$sql_coupon .= ',coup_Benefits3="'.$coup_Benefits3.'"';
	$sql_coupon .= ',coup_Benefits4="'.$coup_Benefits4.'"';
	$sql_coupon .= ',coup_Benefits5="'.$coup_Benefits5.'"';

	$sql_coupon .= ',coup_Differences1="'.$coup_Differences1.'"';
	$sql_coupon .= ',coup_Differences2="'.$coup_Differences2.'"';
	$sql_coupon .= ',coup_Differences3="'.$coup_Differences3.'"';
	$sql_coupon .= ',coup_Differences4="'.$coup_Differences4.'"';
	$sql_coupon .= ',coup_Differences5="'.$coup_Differences5.'"';


	# MOTIVATION PLAN

	if ($coup_Motivation == 'None') {

		$sql_coupon .= ',coup_Motivation="None"';
		$sql_coupon .= ',coup_MotivationID="0"';

	} else {

		$type = substr($coup_Motivation,0,1);
		$id_plan = substr($coup_Motivation,1);

		if ($type == 'p') {

			$sql_coupon .= ',coup_Motivation="Point"';
			$sql_coupon .= ',coup_MotivationID="'.$id_plan.'"';

		} else {

			$sql_coupon .= ',coup_Motivation="Stamp"';
			$sql_coupon .= ',coup_MotivationID="'.$id_plan.'"';
		}
	}


	# TEMPLATE

	if ($id) {

		$sql_template = 'SELECT coup_A4Template, coup_A5Template, coup_A6Template
							FROM hilight_coupon 
							WHERE coup_CouponID="'.$id.'"';

		$oRes_tmp = $oDB->Query($sql_template);
		$tmp = $oRes_tmp->FetchRow(DBI_ASSOC);

		$a4_old = '';
		$a5_old = '';
		$a6_old = '';
			
		if ($tmp['coup_A4Template'] != '') { $a4_old = $tmp['coup_A4Template']; }
		if ($tmp['coup_A5Template'] != '') { $a5_old = $tmp['coup_A5Template']; }
		if ($tmp['coup_A6Template'] != '') { $a6_old = $tmp['coup_A6Template']; }

		$id_tmp = $id;

	} else {
			
		$a4_old = '';
		$a5_old = '';
		$a6_old = '';
		$id_tmp = $id_new;
	}

	if($_FILES["a4_upload"]["name"]!= ""){

		$a4_template = upload_img('a4_upload','A4_'.$id_tmp,'../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/',2480,3508);

		if($a4_template){ $sql_coupon .= ',coup_A4Template="'.$a4_template.'"'; }

		$exp_name = explode('.','../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/'.$a4_template);
		$i = count($exp_name)-1;
		$type_a4 = $exp_name[$i];

		$a4_qr_temp = '../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/A4_'.$id_tmp.'.'.$type_a4;

	} elseif ($a4_old != "") {

		$exp_name = explode('.','../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/'.$a4_old);
		$i = count($exp_name)-1;
		$type_a4 = $exp_name[$i];

		$a4_qr_temp = '../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/A4_'.$id_tmp.'.'.$type_a4;

	} else { 

		$a4_qr_temp = '../../images/tendcard/A4.jpg';
		$type_a4 = 'jpg'; 
	}

	if($_FILES["a5_upload"]["name"]!= ""){

		$a5_template = upload_img('a5_upload','A5_'.$id_tmp,'../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/',1748,2480);

		if($a5_template){ $sql_coupon .= ',coup_A5Template="'.$a5_template.'"'; }

		$exp_name = explode('.','../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/'.$a5_template);
		$i = count($exp_name)-1;
		$type_a5 = $exp_name[$i];

		$a5_qr_temp = '../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/A5_'.$id_tmp.'.'.$type_a5;

	} elseif ($a5_old != "") {

		$exp_name = explode('.','../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/'.$a5_old);
		$i = count($exp_name)-1;
		$type_a5 = $exp_name[$i];

		$a5_qr_temp = '../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/A5_'.$id_tmp.'.'.$type_a5;
		
	} else { 

		$a5_qr_temp = '../../images/tendcard/A5.jpg';
		$type_a5 = 'jpg'; 
	}

	if($_FILES["a6_upload"]["name"]!= ""){

		$a6_template = upload_img('a6_upload','A6_'.$id_tmp,'../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/',1240,1748);

		if($a6_template){ $sql_coupon .= ',coup_A6Template="'.$a6_template.'"'; }

		$exp_name = explode('.','../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/'.$a6_template);
		$i = count($exp_name)-1;
		$type_a6 = $exp_name[$i];

		$a6_qr_temp = '../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/A6_'.$id_tmp.'.'.$type_a6;

	} elseif ($a6_old != "") {

		$exp_name = explode('.','../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/'.$a6_old);
		$i = count($exp_name)-1;
		$type_a6 = $exp_name[$i];

		$a6_qr_temp = '../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/A6_'.$id_tmp.'.'.$type_a6;
		
	} else { 

		$a6_qr_temp = '../../images/tendcard/A6.jpg';
		$type_a6 = 'jpg'; 
	}

	# QRCODE

	// if (!$id) {

		foreach ($_POST['brnc_BranchID'] as $branch_id) {

			$sql_branch_name = 'SELECT name FROM mi_branch WHERE branch_id="'.$branch_id.'"';
			$branch_name = $oDB->QueryOne($sql_branch_name);

			$qrcode_privileges_text = "QHC-".str_pad($id_tmp,4,"0",STR_PAD_LEFT)."-"
												.str_pad($branch_id,4,"0",STR_PAD_LEFT)."";

			$file_full_path = '../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/'.$qrcode_privileges_text.'.png';
			$qrcode_url = $qrcode_privileges_text.".png";
			$errorCorrectionLevel = 'H'; 
			$matrixPointSize = 10;	

			QRcode::png($qrcode_privileges_text, $file_full_path, $errorCorrectionLevel, $matrixPointSize, 2); 

			# MERGE LOGO MI

			$qr_image = ImageCreateFromPng($file_full_path);
			$qr_logo = ImageCreateFromPng('../../images/qrcode_logo.png');

			$Logo_new = ImageCreateTrueColor(94, 94);
			ImageCopyResampled($Logo_new, $qr_logo, 0, 0, 0, 0, 94, 94, 94, 94);

			$black = imagecolorexact($Logo_new, 0, 0, 0);
			imagecolortransparent($Logo_new, $black);
									 
			imagealphablending($Logo_new, flase);
			imagesavealpha($Logo_new, flase);
				
			ImageCopyMerge($qr_image, $Logo_new, 98, 98, 0, 0, 94, 94, 100);

			imagesavealpha($qr_image, true);
			ImagePNG($qr_image,$file_full_path);

			ImageDestroy($qr_image);
			ImageDestroy($qr_logo);
			ImageDestroy($Logo_new);


			// =========================================================================================

			# A4

			if ($type_a4 == 'png' || $type_a4 == 'PNG' || $type_a4 == 'Png') {
				
				$qr_image = imagecreatefromPng($a4_qr_temp);

			} else if ($type_a4 == 'jpg' || $type_a4 == 'JPG' || $type_a4 == 'jpeg' || $type_a4 == 'JPEG') {
				
				$qr_image = imagecreatefromJpeg($a4_qr_temp);

			} else if ($type_a4 == 'gif' || $type_a4 == 'GIF') {
				
				$qr_image = imagecreatefromGif($a4_qr_temp);
			}

			$qr_logo = imagecreatefromPNG($file_full_path);

			$Logo_new = ImageCreateTrueColor(640, 640);
			ImageCopyResampled($Logo_new, $qr_logo, 0, 0, 0, 0, 640, 640, 290, 290);
							 
			ImageCopyMerge($qr_image, $Logo_new, 1763, 1485, 0, 0, 640, 640, 100);

			imagesavealpha($qr_image, true);
			ImageJPEG($qr_image,'../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/A4_'.
							str_pad($id_tmp,4,"0",STR_PAD_LEFT).'-'.
							str_pad($branch_id,4,"0",STR_PAD_LEFT).'.jpg');

			# TEXT TYPE

			$font_size = 90;
			$string_text = $branch_name;
			$font = 'RSU_BOLD.ttf';

			$images = imagecreatefromjpeg('../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/A4_'.
							str_pad($id_tmp,4,"0",STR_PAD_LEFT).'-'.
							str_pad($branch_id,4,"0",STR_PAD_LEFT).'.jpg');  
			$color_name = ImageColorAllocate($images, 0, 0, 0);

			while(1) {
				
				$box_text = imageTTFbbox($font_size, 0, $font, $string_text);
				$text_width = abs($box_text[2]);
				if ( $text_width < 640 ) break;
				$font_size--;
			}

			$text_width = (int) (2083-($text_width/2));

			ImagettfText($images, $font_size, 0, $text_width, 2245, $color_name, $font, $string_text);

			ImageJPEG($images,'../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/A4_'.
							str_pad($id_tmp,4,"0",STR_PAD_LEFT).'-'.
							str_pad($branch_id,4,"0",STR_PAD_LEFT).'.jpg');

			ImageDestroy($qr_image);
			ImageDestroy($images);
			ImageDestroy($qr_logo);
			ImageDestroy($Logo_new);


			// =========================================================================================

			# A5

			if ($type_a5 == 'png' || $type_a5 == 'PNG' || $type_a5 == 'Png') {
				
				$qr_image = imagecreatefromPng($a5_qr_temp);

			} else if ($type_a5 == 'jpg' || $type_a5 == 'JPG' || $type_a5 == 'jpeg' || $type_a5 == 'JPEG') {
				
				$qr_image = imagecreatefromJpeg($a5_qr_temp);

			} else if ($type_a5 == 'gif' || $type_a5 == 'GIF') {
				
				$qr_image = imagecreatefromGif($a5_qr_temp);
			}

			$qr_logo = imagecreatefromPNG($file_full_path);

			$Logo_new = ImageCreateTrueColor(450, 450);
			ImageCopyResampled($Logo_new, $qr_logo, 0, 0, 0, 0, 450, 450, 290, 290);
							 
			ImageCopyMerge($qr_image, $Logo_new, 1246, 1050, 0, 0, 450, 450, 100);

			imagesavealpha($qr_image, true);
			ImageJPEG($qr_image,'../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/A5_'.
							str_pad($id_tmp,4,"0",STR_PAD_LEFT).'-'.
							str_pad($branch_id,4,"0",STR_PAD_LEFT).'.jpg');

			# TEXT TYPE

			$font_size = 50;
			$string_text = $branch_name;
			$font = 'RSU_BOLD.ttf';

			$images = imagecreatefromjpeg('../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/A5_'.
							str_pad($id_tmp,4,"0",STR_PAD_LEFT).'-'.
							str_pad($branch_id,4,"0",STR_PAD_LEFT).'.jpg');  
			$color_name = ImageColorAllocate($images, 0, 0, 0);

			while(1) {
				
				$box_text = imageTTFbbox($font_size, 0, $font, $string_text);
				$text_width = abs($box_text[2]);
				if ( $text_width < 450 ) break;
				
				$font_size--;
			}

			$text_width = (int) (1471-($text_width/2));

			ImagettfText($images, $font_size, 0, $text_width, 1580, $color_name, $font, $string_text);

			ImageJPEG($images,'../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/A5_'.
							str_pad($id_tmp,4,"0",STR_PAD_LEFT).'-'.
							str_pad($branch_id,4,"0",STR_PAD_LEFT).'.jpg');

			ImageDestroy($qr_image);
			ImageDestroy($images);
			ImageDestroy($qr_logo);
			ImageDestroy($Logo_new);


			// =========================================================================================

			# A6 

			if ($type_a6 == 'png' || $type_a6 == 'PNG' || $type_a6 == 'Png') {
				
				$qr_image = imagecreatefromPng($a6_qr_temp);

			} else if ($type_a6 == 'jpg' || $type_a6 == 'JPG' || $type_a6 == 'jpeg' || $type_a6 == 'JPEG') {
				
				$qr_image = imagecreatefromJpeg($a6_qr_temp);

			} else if ($type_a6 == 'gif' || $type_a6 == 'GIF') {
				
				$qr_image = imagecreatefromGif($a6_qr_temp);
			}

			$qr_logo = imagecreatefromPNG($file_full_path);

			$Logo_new = ImageCreateTrueColor(318, 318);
			ImageCopyResampled($Logo_new, $qr_logo, 0, 0, 0, 0, 318, 318, 290, 290);
							 
			ImageCopyMerge($qr_image, $Logo_new, 880, 747, 0, 0, 318, 318, 100);

			imagesavealpha($qr_image, true);
			ImageJPEG($qr_image,'../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/A6_'.
							str_pad($id_tmp,4,"0",STR_PAD_LEFT).'-'.
							str_pad($branch_id,4,"0",STR_PAD_LEFT).'.jpg');

			# TEXT TYPE

			$font_size = 35;
			$string_text = $branch_name;
			$font = 'RSU_BOLD.ttf';

			$images = imagecreatefromjpeg('../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/A6_'.
							str_pad($id_tmp,4,"0",STR_PAD_LEFT).'-'.
							str_pad($branch_id,4,"0",STR_PAD_LEFT).'.jpg');  
			$color_name = ImageColorAllocate($images, 0, 0, 0);

			while(1) {
				
				$box_text = imageTTFbbox($font_size, 0, $font, $string_text);
				$text_width = abs($box_text[2]);
				if ( $text_width < 318 ) break;
				
				$font_size--;
			}

			$text_width = (int) (1039-($text_width/2));

			ImagettfText($images, $font_size, 0, $text_width, 1120, $color_name, $font, $string_text);

			ImageJPEG($images,'../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/A6_'.
							str_pad($id_tmp,4,"0",STR_PAD_LEFT).'-'.
							str_pad($branch_id,4,"0",STR_PAD_LEFT).'.jpg');

			ImageDestroy($qr_image);
			ImageDestroy($images);
			ImageDestroy($qr_logo);
			ImageDestroy($Logo_new);
		}
	// }



	if($id){

		$do_sql_coupon = "UPDATE ".$table_coupon." SET ".$sql_coupon." WHERE coup_CouponID= '".$id."'";

	} else {

		if($time_insert){	$sql_coupon .= ',coup_CreatedDate="'.$time_insert.'"';   }

		if($_SESSION['UID']){	$sql_coupon .= ',coup_CreatedBy="'.$_SESSION['UID'].'"';   }

		if($id_new){	$sql_coupon .= ',coup_CouponID="'.$id_new.'"';   }


		# QRCODE

		$qrcode_privileges_text = "QCH-".str_pad($id_new,4,"0",STR_PAD_LEFT)."-"
										.str_pad($bran_BrandID,4,"0",STR_PAD_LEFT)."";

		$file_full_path = '../../upload/'.$bran_BrandID.'/qr_earn_attention_upload/'.$qrcode_privileges_text.'.png';

		$qrcode_url = $qrcode_privileges_text.".png";

		$errorCorrectionLevel = 'H'; 

		$matrixPointSize = 10;	

		QRcode::png($qrcode_privileges_text, $file_full_path, $errorCorrectionLevel, $matrixPointSize, 2);

		$sql_coupon .= ',coup_QrCode="'.$qrcode_privileges_text.'.png"';

		# MERGE LOGO MI

		$qr_image = ImageCreateFromPng($file_full_path);
		$qr_logo = ImageCreateFromPng('../../images/qrcode_logo.png');

		$Logo_new = ImageCreateTrueColor(94, 94);
		ImageCopyResampled($Logo_new, $qr_logo, 0, 0, 0, 0, 94, 94, 94, 94);

		$black = imagecolorexact($Logo_new, 0, 0, 0);
		imagecolortransparent($Logo_new, $black);
							 
		imagealphablending($Logo_new, flase);
		imagesavealpha($Logo_new, flase);
		
		ImageCopyMerge($qr_image, $Logo_new, 98, 98, 0, 0, 94, 94, 100);

		imagesavealpha($qr_image, true);
		ImagePNG($qr_image,$file_full_path);
		ImageDestroy($qr_image);
		ImageDestroy($Logo_new);


		$do_sql_coupon = 'INSERT INTO '.$table_coupon.' SET '.$sql_coupon;

		$id = $id_new;
	}

	$oDB->QueryOne($do_sql_coupon);



	# MOTIVATION PLAN

	if ($coup_Motivation == 'None') {

		$do_sql_point = "UPDATE motivation_plan_point SET mopp_PrivilegeType='None', mopp_PrivilegeID=0 WHERE mopp_PrivilegeType='Coupon' AND mopp_PrivilegeID='".$id."'";

		$oDB->QueryOne($do_sql_point);

		$do_sql_stamp = "UPDATE motivation_plan_stamp SET mops_PrivilegeType='None', mops_PrivilegeID=0 WHERE mops_PrivilegeType='Coupon' AND mops_PrivilegeID='".$id."'";

		$oDB->QueryOne($do_sql_stamp);

	} else {

		if ($type == 'p') {

			$do_sql_point = "UPDATE motivation_plan_point SET mopp_PrivilegeType='HiCoupon', mopp_PrivilegeID=".$id." WHERE mopp_MotivationPointID='".$id_plan."'";

			$oDB->QueryOne($do_sql_point);

		} else {

			$do_sql_stamp = "UPDATE motivation_plan_stamp SET mops_PrivilegeType='HiCoupon', mops_PrivilegeID=".$id." WHERE mops_MotivationStampID='".$id_plan."'";

			$oDB->QueryOne($do_sql_stamp);
		}
	}



	# MASTER FIELD

	$field = 'SELECT * FROM master_field WHERE mafi_MasterFieldID IN (2,3,5,6,20,23)';

	$oRes_field = $oDB->Query($field);

	while ($master_field = $oRes_field->FetchRow(DBI_ASSOC)){

		$master_data = trim_txt($_REQUEST['mafi_'.$master_field['mafi_MasterFieldID']]);

		# CHECK DATA

		$data = 'SELECT hcre_HilightCouponRequestID FROM hilight_coupon_request 
					WHERE mafi_MasterFieldID="'.$master_field['mafi_MasterFieldID'].'" 
					AND coup_CouponID="'.$id.'"';

		$check_data = $oDB->QueryOne($data);

		if ($check_data != "") { 

			if ($master_data) {

				$do_request = 'UPDATE hilight_coupon_request SET
									hcre_Deleted="",
									hcre_UpdatedBy="'.$_SESSION['UID'].'",
									hcre_UpdatedDate="'.$time_insert.'"
									WHERE hcre_HilightCouponRequestID="'.$check_data.'"';
			} else {

				$do_request = 'UPDATE hilight_coupon_request SET
									hcre_Deleted="T",
									hcre_UpdatedBy="'.$_SESSION['UID'].'",
									hcre_UpdatedDate="'.$time_insert.'"
									WHERE hcre_HilightCouponRequestID="'.$check_data.'"';
			}

		} else {

			if ($master_data) {

				$do_request = 'INSERT INTO hilight_coupon_request SET 
									coup_CouponID="'.$id.'",
									mafi_MasterFieldID="'.$master_field['mafi_MasterFieldID'].'",
									hcre_Deleted="",
									hcre_UpdatedBy="'.$_SESSION['UID'].'",
									hcre_UpdatedDate="'.$time_insert.'",
									hcre_CreatedBy="'.$_SESSION['UID'].'",
									hcre_CreatedDate="'.$time_insert.'"';
			} else {

				$do_request = 'INSERT INTO hilight_coupon_request SET 
									coup_CouponID="'.$id.'",
									mafi_MasterFieldID="'.$master_field['mafi_MasterFieldID'].'",
									hcre_Deleted="T",
									hcre_UpdatedBy="'.$_SESSION['UID'].'",
									hcre_UpdatedDate="'.$time_insert.'",
									hcre_CreatedBy="'.$_SESSION['UID'].'",
									hcre_CreatedDate="'.$time_insert.'"';
			}
		}

		$oDB->QueryOne($do_request);
	}

	echo '<script>window.location.href = "buy.php";</script>';

	exit;
}





# INFORMATION REQUEST

	$information_request = '<table class="table table-bordered" cellspacing="0" style="background-color:white;text-align:center;valign:center;width:50%">';

	$topic = array("Profile", "Contact");

	for ($i=0; $i <2 ; $i++) { 

		# TOPIC

		$information_request .= '<tr class="th_table">
									<td style="text-align:center" colspan="2"><b>'.$topic[$i].'</b></td>
								</tr>';

		# MASTER FIELD

		$field = 'SELECT * FROM master_field 
					WHERE mafi_Position="'.$topic[$i].'" 
					AND mafi_MasterFieldID IN (2,3,5,6,20,23)';

		$oRes_field = $oDB->Query($field);

		while ($master_field = $oRes_field->FetchRow(DBI_ASSOC)){

			$check = "";

			if ($id) {

				# CHECK DATA

				$data = 'SELECT hcre_Deleted FROM hilight_coupon_request 
							WHERE mafi_MasterFieldID="'.$master_field['mafi_MasterFieldID'].'" 
							AND coup_CouponID="'.$id.'"';

				$check_data = $oDB->QueryOne($data);

				$id_data = 'SELECT hcre_HilightCouponRequestID FROM hilight_coupon_request 
						WHERE mafi_MasterFieldID="'.$master_field['mafi_MasterFieldID'].'" 
						AND coup_CouponID="'.$id.'"';

				$check_id = $oDB->QueryOne($id_data);

				if ($check_data == "T") { $check = ""; }

				else if ($check_id) { $check = "checked"; }

				else { $check = ""; }
			
			} else {

				if ($master_field['mafi_MasterFieldID'] == 20) { $check = "checked"; }
			}

			$information_request .= '<tr>
										<td style="text-align:center" width="20%">
											<input type="checkbox" name="mafi_'.$master_field['mafi_MasterFieldID'].'" value="1" '.$check.'></td>
										<td style="text-align:center"><b>'.$master_field['mafi_NameEn'].'</b></td>
									</tr>';
		}
	}

	$information_request .= '</table>';

$oTmp->assign('information_request', $information_request);


# VARIETY CATEORY
		
$as_variety_category = "";

$oRes_head = $oDB->Query("SELECT vc.vaca_VarietyCategoryID AS id, 
							vc.vaca_Name AS name
						FROM variety AS vr
						LEFT JOIN variety_category AS vc
						ON vr.vari_VarietyCategoryID = vc.vaca_VarietyCategoryID
						WHERE vc.vaca_Status=1 
						AND vc.vaca_Type='Shop'
						AND vr.vari_Status='1'
						GROUP BY vc.vaca_VarietyCategoryID");

if ($oRes_head) {
		
	while ($axRow_head = $oRes_head->FetchRow(DBI_ASSOC)) {

		$as_variety_category .= '<optgroup label="'.$axRow_head["name"].'"> ';

		$oRes_detail = $oDB->Query("SELECT vari_VarietyID AS id, 
									vari_Title AS name
									FROM variety
									WHERE vari_VarietyCategoryID='".$axRow_head["id"]."'
									AND vari_Status='1'");
		
		while ($axRow_detail = $oRes_detail->FetchRow(DBI_ASSOC)) {

			if ($asData['vari_VarietyID']==$axRow_detail['id']) { $select = 'selected'; }
			else { $select = ''; }

			$as_variety_category .= '<option value="'.$axRow_detail["id"].'" '.$select.'>'.$axRow_detail["name"].'</option> ';
		}

		$as_variety_category .= '</optgroup> ';
	}
}

$oTmp->assign('variety_category_opt', $as_variety_category);


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

$oTmp->assign('content_file', 'promotion/buy_create.htm');

$oTmp->display('layout/template.html');


//========================================//


$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>


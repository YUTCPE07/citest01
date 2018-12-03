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

if (($_SESSION['role_action']['brand']['add'] != 1) && ($_SESSION['role_action']['brand']['edit'] != 1)) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$Act = $_REQUEST['act'];
$id = $_REQUEST['id'];
$time_insert = date("Y-m-d H:i:s");
$time_pic = date("Ymd_His");
$mobile_format = array('3','7');
$phone_format = array('3','6');


# SEARCH MAX BRAND_ID

	$sql_get_last_ins = 'SELECT max(brand_id) FROM mi_brand';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################

# SEARCH MAX BRAND PACKAGE

	$sql_get_last_pack = 'SELECT max(brpa_BrandPackageID) FROM brand_package';
	$id_last_pack = $oDB->QueryOne($sql_get_last_pack);
	$pack_new = $id_last_pack+1;

#######################################

# SEARCH NAME OLD IMAGE

	$sql_get_old_img = 'SELECT logo_image FROM mi_brand WHERE brand_id='.$id;
	$get_old_img = $oDB->QueryOne($sql_get_old_img);
	$old_image = $get_old_img;

#######################################

# SEARCH NAME OLD IMAGE

	$sql_get_old_cover = 'SELECT cover FROM mi_brand WHERE brand_id='.$id;
	$get_old_cover = $oDB->QueryOne($sql_get_old_cover);
	$old_cover = $get_old_cover;

#######################################

# SEARCH PACKAGE

	$sql_get_package = 'SELECT pama_PackageMasterID FROM mi_setting';
	$get_package = $oDB->QueryOne($sql_get_package);

#######################################


if( $Act == 'edit' && $id != '' ){

	# EDIT

	$sql = '';

	$sql .= 'SELECT a.*,
					d.name                       AS category_brand_d,
					a.category_brand             AS category_brand_a

					FROM mi_brand AS a

					LEFT JOIN mi_category_brand AS d
					ON d.category_brand_id = a.category_brand

					WHERE a.brand_id = "'.$id.'"';

	$oRes = $oDB->Query($sql);

	$i=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++	;

		$last_val_1 = 0;

		for($d=0;$d<count($mobile_format);$d++){

			$axRow['mobile_'.($d+1)] = substr($axRow['mobile'],$last_val_1,$mobile_format[$d]);
			$last_val_1 += $mobile_format[$d];
		}

		$last_val_2 = 0;

		for($d=0;$d<count($phone_format);$d++){

			$axRow['phone_'.($d+1)] = substr($axRow['phone'],$last_val_2,$phone_format[$d]);

			$last_val_2 += $phone_format[$d];
		}

		$axRow['website'] = substr($axRow['website'],7);

		$axRow['facebook_url'] = substr($axRow['facebook_url'],21);


		if ($axRow['line_type']=='line') { $axRow['line_id'] = substr($axRow['line_id'],20); } 

		else { $axRow['line_id'] = substr($axRow['line_id'],22); }


		$axRow['instragram'] = substr($axRow['instragram'],22);

		$axRow['tweeter'] = substr($axRow['tweeter'],20);

		$asData = $axRow;



		$branch_data = "";

		$sql_branch = 'SELECT name, 
								qr_code_image AS qr_code, 
								path_qr AS path,
								branch_id AS id 
						FROM mi_branch 
						WHERE brand_id = "'.$axRow['brand_id'].'"
						AND flag_del = "0"
						AND flag_status = "1"';

		$oRes_brnc = $oDB->Query($sql_branch);
		$check_brnc = $oDB->QueryOne($sql_branch);
		while ($brnc = $oRes_brnc->FetchRow(DBI_ASSOC)){

			$branch_data .= '<tr>
								<td style="text-align:center">'.$brnc['name'].'</td>
								<td style="text-align:center"><img src="../../upload/'.$brnc['path'].$brnc['qr_code'].'" width="80" height="80" class="image_border"/></td>
								<td style="text-align:center"><a target="_blank" href="branch_qrcode.php?branch='.$brnc['id'].'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-print" aria-hidden="true"></span>&nbsp; Print QRCode</button></a></td>
							</tr>';
		} 

		if ($branch_data == "") {
				
			$branch_data = '<tr><td colspan="2" style="text-align:center">No Branch Data</td></tr>';
		}

		$oTmp->assign('branch_data', $branch_data);
	}

} else if( $Act == 'save' ){

	# CREATE FLODER

	if (!$id) {

		mkdir('../../upload/'.$id_new.'/', 0777, true);
		mkdir('../../upload/'.$id_new.'/activity_upload/', 0777, true);
		mkdir('../../upload/'.$id_new.'/card_upload/', 0777, true);
		mkdir('../../upload/'.$id_new.'/coupon_upload/', 0777, true);
		mkdir('../../upload/'.$id_new.'/cover_upload/', 0777, true);
		mkdir('../../upload/'.$id_new.'/earn_attention_upload/', 0777, true);
		mkdir('../../upload/'.$id_new.'/gallery_upload/', 0777, true);
		mkdir('../../upload/'.$id_new.'/logo_upload/', 0777, true);
		mkdir('../../upload/'.$id_new.'/mobile_banner_upload/', 0777, true);
		mkdir('../../upload/'.$id_new.'/news_upload/', 0777, true);
		mkdir('../../upload/'.$id_new.'/privilege_upload/', 0777, true);
		mkdir('../../upload/'.$id_new.'/product_upload/', 0777, true);
		mkdir('../../upload/'.$id_new.'/qr_card_register_upload/', 0777, true);
		mkdir('../../upload/'.$id_new.'/qr_card_upload/', 0777, true);
		mkdir('../../upload/'.$id_new.'/qr_earn_attention_upload/', 0777, true);
		mkdir('../../upload/'.$id_new.'/qr_redeem_upload/', 0777, true);
		mkdir('../../upload/'.$id_new.'/qr_reward_upload/', 0777, true);
		mkdir('../../upload/'.$id_new.'/reward_upload/', 0777, true);
		mkdir('../../upload/'.$id_new.'/variety_upload/', 0777, true);
		mkdir('../../upload/'.$id_new.'/qr_branch_upload/', 0777, true);
		mkdir('../../upload/'.$id_new.'/review_upload/', 0777, true);
	}


	# SAVE

	$do_sql_brand = "";

	$id = trim_txt($_REQUEST['id']);

	$brandname = trim_txt($_REQUEST['brandname']);

	$brand_category = trim_txt($_REQUEST['brand_category']);

	$brand_type = trim_txt($_REQUEST['brand_type']);

	$company_type = trim_txt($_REQUEST['company_type']);

	$companyTypeOther = trim_txt($_REQUEST['companyTypeOther']);

	$companyName = trim_txt($_REQUEST['companyName']);

	$slogan = trim_txt($_REQUEST['slogan']);

	$signature_info = trim_txt($_REQUEST['signature_info']);

	// $greeting_message = trim_txt($_REQUEST['greeting_message']);

	$tax_type = trim_txt($_REQUEST['tax_type']);

	$tax_vat = trim_txt($_REQUEST['tax_vat']);

	$taxNo = trim_txt($_REQUEST['taxNo']);

	$issueBy = trim_txt($_REQUEST['issueBy']);

	$tax_issue_date = trim_txt($_REQUEST['issueDate']);

	$price_range_type = trim_txt($_REQUEST['price_range_type']);

	$email = trim_txt($_REQUEST['email']);

	for($d=1;$d<=count($mobile_format);$d++){	$mobile .= trim_txt($_REQUEST['mobile_'.$d]);	}

	for($d=1;$d<=count($phone_format);$d++){	$phone .= trim_txt($_REQUEST['phone_'.$d]);	}

	$fax = trim_txt($_REQUEST['fax']);

	$website = trim_txt($_REQUEST['website']);

	$facebook_url = trim_txt($_REQUEST['facebook_url']);

	$line_type = trim_txt($_REQUEST['line_type']);

	$line_id = trim_txt($_REQUEST['line_id']);

	$instragram = trim_txt($_REQUEST['instragram']);

	$tweeter = trim_txt($_REQUEST['tweeter']);

	$flag_status = trim_txt($_REQUEST['flag_status']);

	$sub_category_brand = trim_txt($_REQUEST['sub_category_brand']);


	if($_REQUEST['group'] =='group'){	$special_for_group = trim_txt($_REQUEST['group']);	} 
	else {	$special_for_group ="";	}


	if($_REQUEST['children']=='children'){	$special_for_children = trim_txt($_REQUEST['children']);	}
	else {	$special_for_children ="";	}


	if($_REQUEST['other']=='other'){	$other = trim_txt($_REQUEST['other_txt']);	}
	else {	$other = "";	}

	
	$special_for_type = trim_txt($_REQUEST['special_for_type']);

	$check_color = trim_txt($_REQUEST['check_color']);

	$text_color = trim_txt($_REQUEST['text_color']);

	$code_color = trim_txt($_REQUEST['code_color']);

	$date_status = trim_txt($_REQUEST['AutoDate']);

	$flag_hidden = trim_txt($_REQUEST['flag_hidden']);

	$flag_recommend = trim_txt($_REQUEST['flag_recommend']);

	$open_brief = trim_txt($_REQUEST['open_brief']); 

	$open_description = trim_txt($_REQUEST['open_description']); 

	$promotion_reservation_brief = trim_txt($_REQUEST['promotion_reservation_brief']); 
	$promotion_reservation_description = trim_txt($_REQUEST['promotion_reservation_description']); 
	$promotion_howtouse_brief = trim_txt($_REQUEST['promotion_howtouse_brief']);
	$promotion_howtouse_description = trim_txt($_REQUEST['promotion_howtouse_description']);
	$promotion_cancellation_brief = trim_txt($_REQUEST['promotion_cancellation_brief']);
	$promotion_cancellation_description = trim_txt($_REQUEST['promotion_cancellation_description']);
	$promotion_condition = trim_txt($_REQUEST['promotion_condition']);
	$promotion_additional_info = trim_txt($_REQUEST['promotion_additional_info']);
	$promotion_exception = trim_txt($_REQUEST['promotion_exception']);
	$promotion_q1 = trim_txt($_REQUEST['promotion_q1']);
	$promotion_a1 = trim_txt($_REQUEST['promotion_a1']);
	$promotion_q2 = trim_txt($_REQUEST['promotion_q2']);
	$promotion_a2 = trim_txt($_REQUEST['promotion_a2']);
	$promotion_q3 = trim_txt($_REQUEST['promotion_q3']);
	$promotion_a3 = trim_txt($_REQUEST['promotion_a3']);
	$promotion_q4 = trim_txt($_REQUEST['promotion_q4']);
	$promotion_a4 = trim_txt($_REQUEST['promotion_a4']);
	$promotion_q5 = trim_txt($_REQUEST['promotion_q5']);
	$promotion_a5 = trim_txt($_REQUEST['promotion_a5']);

	$shop_reservation_brief = trim_txt($_REQUEST['shop_reservation_brief']); 
	$shop_reservation_description = trim_txt($_REQUEST['shop_reservation_description']); 
	$shop_howtouse_brief = trim_txt($_REQUEST['shop_howtouse_brief']);
	$shop_howtouse_description = trim_txt($_REQUEST['shop_howtouse_description']);
	$shop_cancellation_brief = trim_txt($_REQUEST['shop_cancellation_brief']);
	$shop_cancellation_description = trim_txt($_REQUEST['shop_cancellation_description']);
	$shop_condition = trim_txt($_REQUEST['shop_condition']);
	$shop_additional_info = trim_txt($_REQUEST['shop_additional_info']);
	$shop_exception = trim_txt($_REQUEST['shop_exception']);
	$shop_q1 = trim_txt($_REQUEST['shop_q1']);
	$shop_a1 = trim_txt($_REQUEST['shop_a1']);
	$shop_q2 = trim_txt($_REQUEST['shop_q2']);
	$shop_a2 = trim_txt($_REQUEST['shop_a2']);
	$shop_q3 = trim_txt($_REQUEST['shop_q3']);
	$shop_a3 = trim_txt($_REQUEST['shop_a3']);
	$shop_q4 = trim_txt($_REQUEST['shop_q4']);
	$shop_a4 = trim_txt($_REQUEST['shop_a4']);
	$shop_q5 = trim_txt($_REQUEST['shop_q5']);
	$shop_a5 = trim_txt($_REQUEST['shop_a5']);

	$member_reservation_brief = trim_txt($_REQUEST['member_reservation_brief']); 
	$member_reservation_description = trim_txt($_REQUEST['member_reservation_description']); 
	$member_howtouse_brief = trim_txt($_REQUEST['member_howtouse_brief']);
	$member_howtouse_description = trim_txt($_REQUEST['member_howtouse_description']);
	$member_cancellation_brief = trim_txt($_REQUEST['member_cancellation_brief']);
	$member_cancellation_description = trim_txt($_REQUEST['member_cancellation_description']);
	$member_condition = trim_txt($_REQUEST['member_condition']);
	$member_additional_info = trim_txt($_REQUEST['member_additional_info']);
	$member_exception = trim_txt($_REQUEST['member_exception']);
	$member_q1 = trim_txt($_REQUEST['member_q1']);
	$member_a1 = trim_txt($_REQUEST['member_a1']);
	$member_q2 = trim_txt($_REQUEST['member_q2']);
	$member_a2 = trim_txt($_REQUEST['member_a2']);
	$member_q3 = trim_txt($_REQUEST['member_q3']);
	$member_a3 = trim_txt($_REQUEST['member_a3']);
	$member_q4 = trim_txt($_REQUEST['member_q4']);
	$member_a4 = trim_txt($_REQUEST['member_a4']);
	$member_q5 = trim_txt($_REQUEST['member_q5']);
	$member_a5 = trim_txt($_REQUEST['member_a5']);


	$benefits_1 = trim_txt($_REQUEST['benefits_1']);
	$benefits_2 = trim_txt($_REQUEST['benefits_2']);
	$benefits_3 = trim_txt($_REQUEST['benefits_3']);
	$benefits_4 = trim_txt($_REQUEST['benefits_4']);
	$benefits_5 = trim_txt($_REQUEST['benefits_5']);
	$benefits_6 = trim_txt($_REQUEST['benefits_6']);
	$benefits_7 = trim_txt($_REQUEST['benefits_7']);
	$benefits_8 = trim_txt($_REQUEST['benefits_8']);
	$benefits_9 = trim_txt($_REQUEST['benefits_9']);
	$benefits_10 = trim_txt($_REQUEST['benefits_10']);

	$differences_1 = trim_txt($_REQUEST['differences_1']);
	$differences_2 = trim_txt($_REQUEST['differences_2']);
	$differences_3 = trim_txt($_REQUEST['differences_3']);
	$differences_4 = trim_txt($_REQUEST['differences_4']);
	$differences_5 = trim_txt($_REQUEST['differences_5']);
	$differences_6 = trim_txt($_REQUEST['differences_6']);
	$differences_7 = trim_txt($_REQUEST['differences_7']);
	$differences_8 = trim_txt($_REQUEST['differences_8']);
	$differences_9 = trim_txt($_REQUEST['differences_9']);
	$differences_10 = trim_txt($_REQUEST['differences_10']);



	# ACTION BRAND TABLE

	$sql_brand = '';

	$table_brand = 'mi_brand';

	$sql_brand .= 'name="'.$brandname.'"';   

	$sql_brand .= ',company_type="'.$company_type.'"';   

	$sql_brand .= ',company_name="'.$companyName.'"';   


	if($tax_type == 1){	

		$sql_brand .= ',tax_type="'.$tax_type.'"';

		$sql_brand .= ',tax_vat="'.$tax_vat.'"';  
	}


	if($tax_type == 2){	

		$sql_brand .= ',tax_type="'.$tax_type.'"';

		$sql_brand .= ',tax_vat="0"';   
	}


	$sql_brand .= ',tax_id="'.$taxNo.'"';   

	$sql_brand .= ',tax_issue_by="'.$issueBy.'"';   

	$sql_brand .= ',tax_issue_date="'.$tax_issue_date.'"';   

	$sql_brand .= ',type_brand="'.$brand_type.'"';

	$sql_brand .= ',category_brand="'.$brand_category.'"';

	$sql_brand .= ',phone="'.$phone.'"';   

	$sql_brand .= ',mobile="'.$mobile.'"';   

	$sql_brand .= ',fax="'.$fax.'"';   

	$sql_brand .= ',email="'.$email.'"'; 


	if ($website) { $sql_brand .= ',website="http://'.$website.'"'; } 
	else { $sql_brand .= ',website=""'; }   


	if ($facebook_url) { $sql_brand .= ',facebook_url="https://facebook.com/'.$facebook_url.'"'; } 
	else { $sql_brand .= ',facebook_url=""'; }  


	$sql_brand .= ',line_type="'.$line_type.'"'; 


	if ($line_type=="line") {

		if ($line_id) { $sql_brand .= ',line_id="http://line.me/ti/p/'.$line_id.'"'; } 
		else { $sql_brand .= ',line_id=""'; } 

	} else {

		if ($line_id) { $sql_brand .= ',line_id="http://line.me/R/ti/p/'.$line_id.'"'; } 
		else { $sql_brand .= ',line_id=""'; } 
	}   


	if ($instragram) { $sql_brand .= ',instragram="https://instagram.com/'.$instragram.'"'; } 
	else { $sql_brand .= ',instragram=""'; }    


	if ($tweeter) { $sql_brand .= ',tweeter="https://twitter.com/'.$tweeter.'"'; } 
	else { $sql_brand .= ',tweeter=""'; }   


	$sql_brand .= ',price_range_type="'.$price_range_type.'"';   

	$sql_brand .= ',date_update="'.$time_insert.'"';   

	$sql_brand .=',special_for_group="'.$special_for_group.'"';	

	$sql_brand.=',special_for_children="'.$special_for_children.'"';	

	$sql_brand.=',other="'.$other.'"';		

	$sql_brand .= ',flag_status="'.$flag_status.'"';   

	$sql_brand .= ',signature_info="'.$signature_info.'"';


	if($flag_recommend){	$sql_brand .= ',flag_recommend="'.$flag_recommend.'"';   }
	else {	$sql_brand .= ',flag_recommend="No"';	}


	if($flag_hidden){	$sql_brand .= ',flag_hidden="'.$flag_hidden.'"';   }
	else {	$sql_brand .= ',flag_hidden="No"';	}


	// $sql_brand .= ',greeting_message="'.$greeting_message.'"';


	$sql_brand .= ',slogan="'.$slogan.'"';

	if($_FILES["logo_image_upload"]["name"] != ""){

		if ($id) {

			$new_logo_name = upload_img('logo_image_upload','logo_'.$time_pic,'../../upload/'.$id.'/logo_upload/',400,400);

		} else {

			$new_logo_name = upload_img('logo_image_upload','logo_'.$time_pic,'../../upload/'.$id_new.'/logo_upload/',400,400);
		}

		$sql_brand .= ',logo_image="'.$new_logo_name.'"';
	}

	if($_FILES["cover_image_upload"]["name"] != ""){

		if ($id) {

			$new_cover_name = upload_img('cover_image_upload','cover_'.$time_pic,'../../upload/'.$id.'/cover_upload/',640,250);

		} else {

			$new_cover_name = upload_img('cover_image_upload','cover_'.$time_pic,'../../upload/'.$id_new.'/cover_upload/',640,250);
		}

		$sql_brand .= ',cover="'.$new_cover_name.'"';
	}

	if ($check_color=='default') {

		$sql_brand .= ',code_color="5CB2DA"';

		$sql_brand .= ',text_color="white"';

	} else {

		$sql_brand .= ',code_color="'.$code_color.'"';

		$sql_brand .= ',text_color="'.$text_color.'"';
	}

	$sql_brand .= ',update_by="'.$_SESSION['UID'].'"';

	$sql_brand .= ',date_status="'.$date_status.'"';

	$sql_brand .= ',open_brief="'.$open_brief.'"'; 
	$sql_brand .= ',open_description="'.$open_description.'"'; 

	$sql_brand .= ',promotion_reservation_brief="'.$promotion_reservation_brief.'"'; 
	$sql_brand .= ',promotion_reservation_description="'.$promotion_reservation_description.'"'; 
	$sql_brand .= ',promotion_howtouse_brief="'.$promotion_howtouse_brief.'"';
	$sql_brand .= ',promotion_howtouse_description="'.$promotion_howtouse_description.'"';
	$sql_brand .= ',promotion_cancellation_brief="'.$promotion_cancellation_brief.'"';
	$sql_brand .= ',promotion_cancellation_description="'.$promotion_cancellation_description.'"';
	$sql_brand .= ',promotion_condition="'.$promotion_condition.'"';
	$sql_brand .= ',promotion_additional_info="'.$promotion_additional_info.'"';
	$sql_brand .= ',promotion_exception="'.$promotion_exception.'"';
	$sql_brand .= ',promotion_q1="'.$promotion_q1.'"';
	$sql_brand .= ',promotion_a1="'.$promotion_a1.'"';
	$sql_brand .= ',promotion_q2="'.$promotion_q2.'"';
	$sql_brand .= ',promotion_a2="'.$promotion_a2.'"';
	$sql_brand .= ',promotion_q3="'.$promotion_q3.'"';
	$sql_brand .= ',promotion_a3="'.$promotion_a3.'"';
	$sql_brand .= ',promotion_q4="'.$promotion_q4.'"';
	$sql_brand .= ',promotion_a4="'.$promotion_a4.'"';
	$sql_brand .= ',promotion_q5="'.$promotion_q5.'"';
	$sql_brand .= ',promotion_a5="'.$promotion_a5.'"';

	$sql_brand .= ',shop_reservation_brief="'.$shop_reservation_brief.'"'; 
	$sql_brand .= ',shop_reservation_description="'.$shop_reservation_description.'"'; 
	$sql_brand .= ',shop_howtouse_brief="'.$shop_howtouse_brief.'"';
	$sql_brand .= ',shop_howtouse_description="'.$shop_howtouse_description.'"';
	$sql_brand .= ',shop_cancellation_brief="'.$shop_cancellation_brief.'"';
	$sql_brand .= ',shop_cancellation_description="'.$shop_cancellation_description.'"';
	$sql_brand .= ',shop_condition="'.$shop_condition.'"';
	$sql_brand .= ',shop_additional_info="'.$shop_additional_info.'"';
	$sql_brand .= ',shop_exception="'.$shop_exception.'"';
	$sql_brand .= ',shop_q1="'.$shop_q1.'"';
	$sql_brand .= ',shop_a1="'.$shop_a1.'"';
	$sql_brand .= ',shop_q2="'.$shop_q2.'"';
	$sql_brand .= ',shop_a2="'.$shop_a2.'"';
	$sql_brand .= ',shop_q3="'.$shop_q3.'"';
	$sql_brand .= ',shop_a3="'.$shop_a3.'"';
	$sql_brand .= ',shop_q4="'.$shop_q4.'"';
	$sql_brand .= ',shop_a4="'.$shop_a4.'"';
	$sql_brand .= ',shop_q5="'.$shop_q5.'"';
	$sql_brand .= ',shop_a5="'.$shop_a5.'"';

	$sql_brand .= ',member_reservation_brief="'.$member_reservation_brief.'"'; 
	$sql_brand .= ',member_reservation_description="'.$member_reservation_description.'"'; 
	$sql_brand .= ',member_howtouse_brief="'.$member_howtouse_brief.'"';
	$sql_brand .= ',member_howtouse_description="'.$member_howtouse_description.'"';
	$sql_brand .= ',member_cancellation_brief="'.$member_cancellation_brief.'"';
	$sql_brand .= ',member_cancellation_description="'.$member_cancellation_description.'"';
	$sql_brand .= ',member_condition="'.$member_condition.'"';
	$sql_brand .= ',member_additional_info="'.$member_additional_info.'"';
	$sql_brand .= ',member_exception="'.$member_exception.'"';
	$sql_brand .= ',member_q1="'.$member_q1.'"';
	$sql_brand .= ',member_a1="'.$member_a1.'"';
	$sql_brand .= ',member_q2="'.$member_q2.'"';
	$sql_brand .= ',member_a2="'.$member_a2.'"';
	$sql_brand .= ',member_q3="'.$member_q3.'"';
	$sql_brand .= ',member_a3="'.$member_a3.'"';
	$sql_brand .= ',member_q4="'.$member_q4.'"';
	$sql_brand .= ',member_a4="'.$member_a4.'"';
	$sql_brand .= ',member_q5="'.$member_q5.'"';
	$sql_brand .= ',member_a5="'.$member_a5.'"';

	$sql_brand .= ',benefits_1="'.$benefits_1.'"';
	$sql_brand .= ',benefits_2="'.$benefits_2.'"';
	$sql_brand .= ',benefits_3="'.$benefits_3.'"';
	$sql_brand .= ',benefits_4="'.$benefits_4.'"';
	$sql_brand .= ',benefits_5="'.$benefits_5.'"';
	$sql_brand .= ',benefits_6="'.$benefits_6.'"';
	$sql_brand .= ',benefits_7="'.$benefits_7.'"';
	$sql_brand .= ',benefits_8="'.$benefits_8.'"';
	$sql_brand .= ',benefits_9="'.$benefits_9.'"';
	$sql_brand .= ',benefits_10="'.$benefits_10.'"';

	$sql_brand .= ',differences_1="'.$differences_1.'"';
	$sql_brand .= ',differences_2="'.$differences_2.'"';
	$sql_brand .= ',differences_3="'.$differences_3.'"';
	$sql_brand .= ',differences_4="'.$differences_4.'"';
	$sql_brand .= ',differences_5="'.$differences_5.'"';
	$sql_brand .= ',differences_6="'.$differences_6.'"';
	$sql_brand .= ',differences_7="'.$differences_7.'"';
	$sql_brand .= ',differences_8="'.$differences_8.'"';
	$sql_brand .= ',differences_9="'.$differences_9.'"';
	$sql_brand .= ',differences_10="'.$differences_10.'"';





	if ($id) {

		# UPDATE

		if($new_logo_name){	

			unlink_file($oDB,'mi_brand','logo_image','brand_id',$id,'../../upload/'.$id.'/logo_upload/',$old_image);
		}

		if($new_cover_name){	

			unlink_file($oDB,'mi_brand','cover','brand_id',$id,'../../upload/'.$id.'/logo_upload/',$old_cover);
		}

		$do_sql_brand = 'UPDATE mi_brand SET '.$sql_brand.' WHERE brand_id="'.$id.'"';

		$oDB->QueryOne($do_sql_brand);
	
	} else {

		# INSERT BRAND

		if($time_insert){	$sql_brand .= ',date_create="'.$time_insert.'"';   }

		if($id_new){	$sql_brand .= ',brand_id="'.$id_new.'"';   }

		$sql_brand .= ',flag_approve="T"';

		$sql_brand .= ',create_by="'.$_SESSION['UID'].'"';

		$sql_brand .= ',path_logo="'.$id_new.'/logo_upload/"';

		$sql_brand .= ',path_cover="'.$id_new.'/cover_upload/"';

		$sql_brand .= ',otp_updateddate="'.$time_insert.'"';

		$sql_brand .= ',otp_updatedby="'.$_SESSION['UID'].'"';

		$sql_brand .= ',flag_sms="T"';

		$sql_brand .= ',sms_update_date="'.$time_insert.'"';

		$sql_brand .= ',sms_update_by="'.$_SESSION['UID'].'"';

		$do_sql_brand = 'INSERT INTO mi_brand SET '.$sql_brand;

		$oDB->QueryOne($do_sql_brand);


		# INSERT PACKAGE

		$do_sql_package = 'INSERT INTO brand_package SET 
								brpa_BrandPackageID = "'.$pack_new.'",
								bran_BrandID = "'.$id_new.'",
								pama_PackageMasterID = "'.$get_package.'",
								brpa_CreatedBy = "'.$_SESSION['UID'].'",
								brpa_CreatedDate = "'.$time_insert.'",
								brpa_UpdatedBy = "'.$_SESSION['UID'].'",
								brpa_UpdatedDate = "'.$time_insert.'"';

		$oDB->QueryOne($do_sql_package);


		# INSERT MESSAGES

			# SEARCH MAX MESSAGES_ID

			$sql_get_last_ins = 'SELECT max(mess_MessagesID) FROM messages';
			$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
			$message_new = $id_last_ins+1;

		$topic = array("NearBy", "Expiry", "Birthday", "Follow");

		for ($i=0; $i <4 ; $i++) { 

			$do_sql_messages = 'INSERT INTO messages SET
									mess_MessagesID = "'.$message_new.'",
									bran_BrandID = "'.$id_new.'",
									mess_Type = "'.$topic[$i].'",
									mess_CreatedBy = "'.$_SESSION['UID'].'",
									mess_CreatedDate = "'.$time_insert.'",
									mess_UpdatedBy = "'.$_SESSION['UID'].'",
									mess_UpdatedDate = "'.$time_insert.'"';

			$oDB->QueryOne($do_sql_messages);

			$message_new++;
		}
	}

	// echo $do_sql_brand;

	echo '<script>window.location.href = "brand.php";</script>';

	exit;
}




#  company_type dropdownlist

$as_company_type = dropdownlist_type_master($oDB,'company_type');

$oTmp->assign('company_type_opt', $as_company_type);



#  price_lange_type dropdownlist

$as_price_lange_type = dropdownlist_type_master($oDB,'price_lange_type');

$oTmp->assign('price_lange_type_opt', $as_price_lange_type);



#  category_brand dropdownlist

$as_category_brand_d = dropdownlist_from_table($oDB,'mi_category_brand','category_brand_id','name_en','flag_status="1" AND flag_del=""',' ORDER BY name_en ASC');

$oTmp->assign('category_brand_d_opt', $as_category_brand_d);



#  type_brand dropdownlist

$as_type_brand = dropdownlist_from_table($oDB,'mi_brand_type','brand_type_id','name_en','',' ORDER BY name_en ASC');

$oTmp->assign('type_brand_opt', $as_type_brand);




#  special_for_type dropdownlist

$as_special_for_type = dropdownlist_type_master($oDB,'special_for_type');

$oTmp->assign('special_for_type_opt', $as_special_for_type);




$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('type_file_upload', '<span class="text-rq">Type file : .jpg , .png , .gif only<br></span>');

$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_brand');

$oTmp->assign('content_file', 'brand/brand_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
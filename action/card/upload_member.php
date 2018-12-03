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

if ($_SESSION['role_action']['card']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$today = date("Y-m-d");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$platform = $_REQUEST['platform'];

$path_upload_member = $_SESSION['path_upload_member'];


$where_brand = '';

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND mb_member_brand.brand_id = "'.$_SESSION['user_brand_id'].'" ';
}



# SEARCH

$brand_id = "";

$where_search = "";

for($k=0 ; $k<count($_POST["brand_id"]) ; $k++){

	if(trim($_POST["brand_id"][$k]) != ""){

		if ($_POST["brand_id"][$k]==0) {

			$brand_id = 0;

		} else {

			if ($k==count($_POST["brand_id"])-1) {	$brand_id .= $_POST["brand_id"][$k];	} 
			else {	$brand_id .= $_POST["brand_id"][$k].",";	}
		}
	}
}

if ($brand_id=="" || $brand_id==0) {	$where_search = "";	} 
else {	$where_search = " AND mb_member_brand.brand_id IN (".$brand_id.")";	}



#  BRAND

$sql_brand ='SELECT brand_id, name FROM mi_brand WHERE flag_del!=1 ORDER BY name';
$oRes_brand = $oDB->Query($sql_brand);

$select_brand = "";

while ($axRow = $oRes_brand->FetchRow(DBI_ASSOC)){

	$select_brand .= '<option value="'.$axRow['brand_id'].'">'.$axRow['name'].'</option>';
}


#  CARD

$sql_card ='SELECT card_id, name FROM mi_card WHERE flag_del!=1 AND brand_id='.$_SESSION['user_brand_id'].' ORDER BY name';

$oRes_card = $oDB->Query($sql_card);

$select_card = "";

while ($axRow = $oRes_card->FetchRow(DBI_ASSOC)){

	$select_card .= '<option value="'.$axRow['card_id'].'">'.$axRow['name'].'</option>';
}


# TEMPLATE

$template = '<button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#Template">Download Template</button>
				<div class="modal fade" id="Template" tabindex="-1" role="dialog" aria-labelledby="TemplateDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Download Template</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">';

if ($_SESSION['user_type_id_ses']==1) {

	$template .= '				<tr>
						        	<td width="140px" style="text-align:right"><b>Select Brand</b></td>
						        	<td width="20px">&nbsp;</td>
						        	<td>
						        	<select class="form-control text-md" id="bran_BrandID" name="bran_BrandID" onchange="BrandSelect();" required autofocus>
						        		<option value="">Please Select ..</option>
						        		'.$select_brand.'
						        	</select>
								    </td>
						        	</tr>
						        	<tr><td><br></td><td></td><td><br></td></tr>
						        	<tr>
						        	<td width="140px" style="text-align:right"><b>Select Card</b></td>
						        	<td width="20px">&nbsp;</td>
						        	<td><span id="brand_select" class="fontBlack">
						        	<select class="form-control text-md" disabled>
						        		<option value="">Please Select ..</option>
						        	</select></span></td>
						        	</tr>';
} else {

	$template .= '		        <tr>
						        	<td width="140px" style="text-align:right"><b>Select Card</b></td>
						        	<td width="20px">&nbsp;</td>
						        	<td>
						        	<select class="form-control text-md" id="card_CardID" name="card_CardID" onchange="CardSelect();" required autofocus>
						        		<option value="">Please Select ..</option>
						        		'.$select_card.'
						        	</select>
								    </td>
						        	</tr>';
}

$template .= '		        	<tr><td><br></td><td></td><td><br></td></tr>
						        	<tr>
						        	<td width="140px" style="text-align:right"><b>Member Type</b></td>
						        	<td width="20px">&nbsp;</td>
						        	<td>
						        	<select class="form-control text-md" id="platform" name="platform" onchange="CardSelect();">
						        		<option value="new">Invite New Member</option>
						        		<option value="existing">Activate Existing Member</option>
						        	</select>
								    </td>
						        	</tr>
						        </table>
                    			<span id="form_select" style="font-size:12px" class="fontBlack"></span>
						        </center>
						    </div>
						    <div class="modal-footer">
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

$oTmp->assign('template', $template);


# SEND EMAIL & SMS

$send = '<button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#Send">Send Email & SMS</button>
			<div class="modal fade" id="Send" tabindex="-1" role="dialog" aria-labelledby="SendDataLabel">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-body" align="left">
						    <span style="font-size:16px"><b>Send Email & SMS</b><span>
						    <hr>
						    <center>
						    <table width="70%" class="myPopup">';

if ($_SESSION['user_type_id_ses']==1) {

	$send .= '				<tr>
						        <td width="140px" style="text-align:right"><b>Select Brand</b></td>
						        <td width="20px">&nbsp;</td>
						        <td>
						        	<select class="form-control text-md" id="bran_BrandID" name="bran_BrandID" onchange="BrandSelect();" required autofocus>
						        		<option value="">Please Select ..</option>
						        		'.$select_brand.'
						        	</select>
								</td>
						    </tr>
						    <tr><td><br></td><td></td><td><br></td></tr>
						    <tr>
						        <td width="140px" style="text-align:right"><b>Select Card</b></td>
						        <td width="20px">&nbsp;</td>
						        <td><span id="brand_select" class="fontBlack">
						        	<select class="form-control text-md" disabled>
						        		<option value="">Please Select ..</option>
						        	</select></span></td>
						        </tr>';
} else {

	$send .= '		        <tr>
						        <td width="140px" style="text-align:right"><b>Select Card</b></td>
						        <td width="20px">&nbsp;</td>
						        <td>
						        	<select class="form-control text-md" id="card_CardID" name="card_CardID" onchange="CardSelect();" required autofocus>
						        		<option value="">Please Select ..</option>
						        		'.$select_card.'
						        	</select>
								</td>
						    </tr>';
}

$send .= '		        </table>
                    	<span id="send_select" style="font-size:12px" class="fontBlack"></span>
						</center>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
					</div>
				</div>
			</div>
		</div>';

$oTmp->assign('send', $send);




if ($Act=='Template' && $id && $platform) {

	$sql_card ='SELECT name FROM mi_card WHERE card_id='.$id.'';
	$card_name = $oDB->QueryOne($sql_card);

	# EXCEL

	require_once '../../lib/PHPExcel/Classes/PHPExcel.php';

	$objPHPExcel = new PHPExcel();

	$char = 'A';

	$chars = $char;

	$row_start = 5;

	$reportName = $card_name;

	$objPHPExcel->setActiveSheetIndex(0);

	$topic = array("Profile", "Home Address", "Work Address", "Work", "Contact");

	$country_check = 0;
	$province_check = 0;

	# COMMENT

	$j = 1;

	for ($i=0; $i <5 ; $i++) { 

		$sql_form = 'SELECT
						b.mafi_NameEn,
						b.mafi_MasterFieldID
						FROM register_form AS a
						LEFT JOIN master_field AS b
						ON b.mafi_MasterFieldID = a.mafi_MasterFieldID
						WHERE b.mafi_Position = "'.$topic[$i].'"
						AND b.mafi_Deleted != "T"
						AND a.card_CardID = "'.$id.'"
						AND a.refo_FillIn = "Y"
						ORDER BY b.mafi_FieldOrder';

		$oRes_form = $oDB->Query($sql_form);

	 	while ($axRow = $oRes_form->FetchRow(DBI_ASSOC)) {

			$sql_target = 'SELECT
							mata_NameEn
							FROM master_target
							WHERE mafi_MasterFieldID = "'.$axRow['mafi_MasterFieldID'].'"';

			$count_target = 'SELECT
							COUNT(mata_NameEn)
							FROM master_target
							WHERE mafi_MasterFieldID = "'.$axRow['mafi_MasterFieldID'].'"';

			$oRes_trget = $oDB->Query($sql_target);

			$check_trget = $oDB->QueryOne($sql_target);

			$count_target = $oDB->QueryOne($count_target);

			$k = 1;

			if ($check_trget) {

		 		$target = '[';

		 		if ($axRow['mafi_MasterFieldID']=='6') {

			 		$target .= 'YYYY-MM-DD';

		 		} else {

			 		while ($axRow_target = $oRes_trget->FetchRow(DBI_ASSOC)) {

			 			$target .= $axRow_target['mata_NameEn'];

			 			if ($k < $count_target) { $target .= ', '; }

			 			$k++;
			 		}
		 		}

		 		$target .= ']';

				$objPHPExcel->getActiveSheet()->setCellValue('B'.$j, $axRow['mafi_NameEn']);

				$objPHPExcel->getActiveSheet()->setCellValue('C'.$j++, $target);
			
			} else {

				if ($axRow['mafi_MasterFieldID']=='34' || $axRow['mafi_MasterFieldID']=='46') { # COUNTRY

					if ($country_check == 0) {

						$country_check++;

				 		$target = '[';

				 		$sql_country = 'SELECT coun_Nicename FROM country WHERE coun_PhoneCode!=0 ORDER BY coun_Nicename';
				 		$oRes_trget = $oDB->Query($sql_country);

				 		$count_target = 'SELECT COUNT(coun_Nicename) FROM country WHERE coun_PhoneCode!=0';
						$count_target = $oDB->QueryOne($count_target);
						$k = 0;

					 	while ($axRow_target = $oRes_trget->FetchRow(DBI_ASSOC)) {

					 		$target .= $axRow_target['coun_Nicename'];

					 		if ($k < $count_target) { $target .= ', '; }

					 		$k++;
					 	}

				 		$target .= ']';

						$objPHPExcel->getActiveSheet()->setCellValue('B'.$j, $axRow['mafi_NameEn']);

						$objPHPExcel->getActiveSheet()->setCellValue('C'.$j++, $target);
					}
				}

				if ($axRow['mafi_MasterFieldID']=='33' || $axRow['mafi_MasterFieldID']=='45') { # PROVINCE

					if ($province_check == 0) {

						$province_check++;

				 		$target = '[';

				 		$sql_province = 'SELECT prov_Name FROM province WHERE prov_Deleted!="T" ORDER BY prov_Name';
				 		$oRes_trget = $oDB->Query($sql_province);

				 		$count_target = 'SELECT COUNT(prov_Name) FROM province WHERE prov_Deleted!="T"';
						$count_target = $oDB->QueryOne($count_target);
						$k = 0;

					 	while ($axRow_target = $oRes_trget->FetchRow(DBI_ASSOC)) {

					 		$target .= $axRow_target['prov_Name'];

					 		if ($k < $count_target) { $target .= ', '; }

					 		$k++;
					 	}

				 		$target .= ']';

						$objPHPExcel->getActiveSheet()->setCellValue('B'.$j, $axRow['mafi_NameEn']);

						$objPHPExcel->getActiveSheet()->setCellValue('C'.$j++, $target);
					}
				}
			}
		}
	}

	$objPHPExcel->getActiveSheet()->setCellValue('B'.$j, 'Mobile');

	$objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.$j++, '+66XXXXXXXXX', PHPExcel_Cell_DataType::TYPE_STRING);

	$sql_custom = 'SELECT
					b.cufi_Name,
					b.cufi_CustomFieldID
					FROM custom_form AS a
					LEFT JOIN custom_field AS b
					ON b.cufi_CustomFieldID = a.cufi_CustomFieldID
					WHERE b.cufi_Deleted != "T"
					AND a.card_CardID = "'.$id.'"
					AND a.cufo_FillIn = "Y"
					ORDER BY b.cufi_FieldOrder';

	$oRes_custom = $oDB->Query($sql_custom);

	while ($axRow = $oRes_custom->FetchRow(DBI_ASSOC)) {

		$sql_target = 'SELECT clva_NameEn
						FROM custom_list_value
						WHERE cufi_CustomFieldID = "'.$axRow['cufi_CustomFieldID'].'"
						AND clva_Deleted=""';

		$count_target = 'SELECT COUNT(clva_NameEn)
						FROM custom_list_value
						WHERE cufi_CustomFieldID = "'.$axRow['cufi_CustomFieldID'].'"
						AND clva_Deleted=""';

		$oRes_trget = $oDB->Query($sql_target);

		$check_trget = $oDB->QueryOne($sql_target);

		$count_target = $oDB->QueryOne($count_target);

		$k = 1;

		if ($check_trget) {

		 	$target = '[';

		 	while ($axRow_target = $oRes_trget->FetchRow(DBI_ASSOC)) {

			 	$target .= $axRow_target['clva_NameEn'];

			 	if ($k < $count_target) { $target .= ', '; }

			 	$k++;
		 	}

		 	$target .= ']';

			$objPHPExcel->getActiveSheet()->setCellValue('B'.$j, $axRow['cufi_Name']);

			$objPHPExcel->getActiveSheet()->setCellValue('C'.$j++, $target);
		}
	}

	if ($platform=='existing') {

		$objPHPExcel->getActiveSheet()->setCellValue('B'.$j, 'Expired Date');

		$objPHPExcel->getActiveSheet()->setCellValue('C'.$j++, '[YYYY-MM-DD]');
	}

	$objPHPExcel->getActiveSheet()->setCellValue('C'.$j++, '* จัดระเบียบรูปแบบเซลล์เป็นประเภทข้อความ');


	# LOOP FORM

	$f = $j;

	$f++;

	$blue = $f;

	for ($i=0; $i <5 ; $i++) { 

		$sql_form = 'SELECT a.member_register_id
						FROM register_form AS a
						LEFT JOIN master_field AS b
						ON b.mafi_MasterFieldID = a.mafi_MasterFieldID
						WHERE b.mafi_Position = "'.$topic[$i].'"
						AND b.mafi_Deleted != "T"
						AND a.card_CardID = "'.$id.'"
						AND a.refo_FillIn = "Y"
						ORDER BY b.mafi_FieldOrder';

		$oRes_form = $oDB->Query($sql_form);

	 	$check_form = $oDB->QueryOne($sql_form);

		if ($check_form) {

			$merge = $chars;

			$chars++;

			while ($oRes_form->FetchRow(DBI_ASSOC)) { $merge++; }

			$merge--;

			if ($platform=='existing' && $topic[$i]=='Profile') {

				$merge++;
			}

			// if ($topic[$i]=='Contact') {

			// 	$merge = $merge+2;

			// }

			$objPHPExcel->getActiveSheet()->setCellValue($chars.$f, $topic[$i]);

			$objPHPExcel->getActiveSheet()->mergeCells($chars++.$f.':'.$merge.$f);

			$chars = $merge;
		} 

		// else if ($topic[$i]=='Contact') {

		// 	$merge = $chars;

		// 	$chars++;

		// 		$merge++;

		// 		$merge++;

		// 	$objPHPExcel->getActiveSheet()->setCellValue($chars.$f, $topic[$i]);

		// 	$objPHPExcel->getActiveSheet()->mergeCells($chars++.$f.':'.$merge.$f);

		// 	$chars = $merge;

		// }
	}



	# CUSTOM

	$sql_custom = 'SELECT a.cufo_CustomFormID
					FROM custom_form AS a
					LEFT JOIN custom_field AS b
					ON b.cufi_CustomFieldID = a.cufi_CustomFieldID
					WHERE b.cufi_Deleted != "T"
					AND a.card_CardID = "'.$id.'"
					AND a.cufo_FillIn = "Y"
					ORDER BY b.cufi_FieldOrder';

	$oRes_custom = $oDB->Query($sql_custom);

 	$check_custom = $oDB->QueryOne($sql_custom);

	if ($check_custom) {

		$merge = $chars;

		$chars++;

		while ($oRes_custom->FetchRow(DBI_ASSOC)) { $merge++; }

		$merge--;

		$objPHPExcel->getActiveSheet()->setCellValue($chars.$f, 'Custom');

		$objPHPExcel->getActiveSheet()->mergeCells($chars.$f.':'.$merge.$f);

		$merge--;

		$chars = $merge;
	}

	$chars++;

	if ($platform=='existing') {

		$objPHPExcel->getActiveSheet()->setCellValue($chars.$f, 'Card Expired');

		$chars++;

		$objPHPExcel->getActiveSheet()->setCellValue($chars.$f, 'Collect');
	}

	$f++;

	$gray = $f;

	$chars = 'A';

	$objPHPExcel->getActiveSheet()->setCellValue($chars.$f, 'No.');

	if ($platform=='existing') {

		$chars++;

		$objPHPExcel->getActiveSheet()->setCellValue($chars.$f, 'ID Card No.');
	}

	for ($i=0; $i <5 ; $i++) { 

		$sql_form = 'SELECT
						b.mafi_NameEn
						FROM register_form AS a
						LEFT JOIN master_field AS b
						ON b.mafi_MasterFieldID = a.mafi_MasterFieldID
						WHERE b.mafi_Position = "'.$topic[$i].'"
						AND b.mafi_Deleted != "T"
						AND a.card_CardID = "'.$id.'"
						AND a.refo_FillIn = "Y"
						ORDER BY b.mafi_FieldOrder';

		$oRes_form = $oDB->Query($sql_form);

	 	$check_form = $oDB->QueryOne($sql_form);

		if ($check_form) {

			while ($axRow = $oRes_form->FetchRow(DBI_ASSOC)) {

				$chars++;

				$objPHPExcel->getActiveSheet()->setCellValue($chars.$f, $axRow['mafi_NameEn']);
			}
		} 

		// else if ($topic[$i] == "Contact") {

		// 	$chars++;

		// 	$objPHPExcel->getActiveSheet()->setCellValue($chars.$f, 'Mobile');

		// 	$chars++;

		// 	$objPHPExcel->getActiveSheet()->setCellValue($chars.$f, 'Email');

		// }
	}



	# CUSTOM

	$sql_custom = 'SELECT b.cufi_Name
					FROM custom_form AS a
					LEFT JOIN custom_field AS b
					ON b.cufi_CustomFieldID = a.cufi_CustomFieldID
					WHERE b.cufi_Deleted != "T"
					AND a.card_CardID = "'.$id.'"
					AND a.cufo_FillIn = "Y"
					ORDER BY b.cufi_FieldOrder';

	$oRes_custom = $oDB->Query($sql_custom);

 	$check_custom = $oDB->QueryOne($sql_custom);

	if ($check_custom) {

		while ($axRow = $oRes_custom->FetchRow(DBI_ASSOC)) { 

			$chars++;
			$objPHPExcel->getActiveSheet()->setCellValue($chars.$f, $axRow['cufi_Name']); 
		}
	}

	if ($platform=='existing') {

		$chars++;

		$objPHPExcel->getActiveSheet()->setCellValue($chars.$f, 'Expried Date'); 

		$chars++;

		$objPHPExcel->getActiveSheet()->setCellValue($chars.$f, 'Point');
	}

	for ($i=1; $i < $j ; $i++) { 

		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C'.$i.':'.$chars.$i);
	}


	$sharedStyle1 = new PHPExcel_Style();

	$sharedStyle1->applyFromArray(

		array(
			  'borders' => array(
								'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
								'right'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN)
								)
	));


	// $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A5:".$chars.($i-1));

	$objPHPExcel->getActiveSheet()->getStyle('A'.$blue.':'.$chars.$blue)->applyFromArray(

			array(
				'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
				'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
				'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
				'fill' => array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '5CB2DA'))
			)
	);

	$objPHPExcel->getActiveSheet()->getStyle('A'.$gray.':'.$chars.$gray)->applyFromArray(

			array(
				'borders' => array('right' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
				'fill' => array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'F2F2F2'))
			)
	);

	$p=0;

	for($p=0;$p <= get_character_number($chars);$p++){

		$objPHPExcel->getActiveSheet()->getColumnDimension($char++)->setAutoSize(true);
	}


	// Rename sheet

	$objPHPExcel->getActiveSheet()->setTitle($reportName);

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet

	$objPHPExcel->setActiveSheetIndex(0);

	$date = date('Y-m-d');

	$strFileName = $reportName." Template.xls";			# 2003


	//======================================
	//			download to Excel
	//======================================

	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

	header('Content-Disposition: attachment;filename="'.$strFileName.'"');

	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');		# 23

	$objWriter->save('php://output');

	exit;
}


###########################################################


$sql = 'SELECT 
		mb_member_brand.member_brand_id,
		mb_member_brand.member_card_code,
		mb_member_brand.member_brand_code,
		mb_member_brand.platform,
		mb_member_brand.member_id,
		mb_member_brand.member_register_id,
		mb_member_brand.member_type_id,
		mb_member_brand.firstname,
		mb_member_brand.lastname,
		mb_member_brand.nickname,
		mb_member_brand.name_title_type,
		mb_member_brand.flag_gender,
		mb_member_brand.date_birth,
		mb_member_brand.flag_marital,
		mb_member_brand.no_of_children,
		mb_member_brand.nationality,
		mb_member_brand.idcard_no,
		mb_member_brand.passport_no,
		mb_member_brand.educate_type,
		mb_member_brand.interest_activity_type,
		mb_member_brand.employment_type,
		mb_member_brand.industry_current_work_type,
		mb_member_brand.area_work_type,
		mb_member_brand.monthly_personal_income_type,
		mb_member_brand.monthly_household_income_type,
		mb_member_brand.mobile,
		mb_member_brand.home_phone,
		mb_member_brand.work_phone,
		mb_member_brand.email,
		mb_member_brand.home_address,
		mb_member_brand.home_area,
		mb_member_brand.home_room_no,
		mb_member_brand.home_moo,
		mb_member_brand.home_junction,
		mb_member_brand.home_soi,
		mb_member_brand.home_road,
		mb_member_brand.home_sub_district,
		mb_member_brand.home_district,
		mb_member_brand.home_province,
		mb_member_brand.home_country,
		mb_member_brand.home_postcode,
		mb_member_brand.work_address,
		mb_member_brand.work_area,
		mb_member_brand.work_room_no,
		mb_member_brand.work_moo,
		mb_member_brand.work_junction,
		mb_member_brand.work_soi,
		mb_member_brand.work_road,
		mb_member_brand.work_sub_district,
		mb_member_brand.work_province,
		mb_member_brand.work_country,
		mb_member_brand.work_postcode,
		mb_member_brand.point_collect,
		mb_member_brand.date_update,
		mb_member_brand.date_expried,
		mb_member_brand.flag_del,
		mb_member_brand.send_email,
		mb_member_brand.send_sms,
		mi_brand.name AS brand_name,
		mi_brand.logo_image AS brand_logo,
		mi_brand.path_logo,
		mi_card.card_id AS card_id,
		mi_card.name AS card_name,
		mi_card.image AS card_image,
		mi_card.image_newupload AS card_new,
		mi_card.path_image,
		mb_member.member_image AS member_image,
		mb_member.facebook_id AS facebook_id

		FROM mb_member_brand

		LEFT JOIN mb_member
		ON mb_member.member_id = mb_member_brand.member_id

		LEFT JOIN mi_brand
		ON mi_brand.brand_id = mb_member_brand.brand_id

		LEFT JOIN mi_card
		ON mi_card.card_id = mb_member_brand.card_id

		WHERE 1
		'.$where_search.'
		'.$where_brand.' 

		GROUP BY mb_member_brand.member_brand_id

		ORDER BY CASE 
			WHEN mb_member_brand.member_register_id != "0" THEN 1
	        WHEN mb_member_brand.flag_del = "T" THEN 2 END ASC, 
	        mb_member_brand.date_update DESC, 
	        mb_member_brand.member_brand_id ASC';

	$oRes = $oDB->Query($sql);

	$i=1;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		# MEMBER STATUS

		$status_send = '';

		if ($axRow['flag_del'] == 'T') {

			$member_status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

			$status_send = 'disabled';

		} else {

			if ($axRow['member_id']) {

				if ($axRow['member_register_id']) {

					$member_status = '<button style="width:100px;" class="form-control text-md status_active" name="active_status">Active</button>';

					$status_send = 'disabled';

				} else {

					$member_status = '<button style="width:100px;" class="form-control text-md status_pending" name="pending_status">Pending</button>';

					$status_send = '';
				}

			} else {

				$member_status = '<button style="width:100px;" class="form-control text-md status_pending" name="pending_status">Pending</button>';

				$status_send = '';
			}
		}


		# LOGO

		if($axRow['brand_logo']!=''){

			$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" width="60" height="60"/>';

			$logo_view = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" width="150" height="150"/>';

		} else {

			$logo_brand = '<img src="../../images/400x400.png" width="60" height="60"/>';

			$logo_view = '<img src="../../images/400x400.png" width="150" height="150"/>';
		}


		# MEMBER IMAGE

		if($axRow['member_image']!=''){

			$member_view = '<img src="'.$path_upload_member.$axRow['member_image'].'" width="100" height="100" class="img-circle image_border"/>';	

		} else if ($axRow['facebook_id']!='') {

			$member_view = '<img src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=large" width="100" height="100" class="img-circle image_border"/>';

		} else {

			$member_view = '<img src="../../images/user.png" width="100" height="100" class="img-circle image_border"/>';
		}


		# CARD IMAGE

		if($axRow['card_new']!=''){

			$card_image = '<img src="../../upload/'.$axRow['path_image'].$axRow['card_new'].'" class="img-rounded image_border" height="60"/>';

			$card_view = '<img src="../../upload/'.$axRow['path_image'].$axRow['card_new'].'" class="img-rounded image_border" height="100"/>';

		} else {

			if($axRow['card_image']!=''){

				$card_image = '<img src="../../upload/'.$axRow['path_image'].$axRow['card_image'].'" class="img-rounded image_border" height="60"/>';

				$card_view = '<img src="../../upload/'.$axRow['path_image'].$axRow['card_image'].'" class="img-rounded image_border" height="100"/>';

			} else {

				$card_image = '<img src="../../images/card_privilege.jpg" class="img-rounded image_border" height="60"/>';

				$card_view = '<img src="../../images/card_privilege.jpg" class="img-rounded image_border" height="100"/>';
			}
		}


		# VIEW

			# DATA

			$sql_card = "SELECT image,image_newupload,member_fee,period_type,period_type_other,flag_status,date_expired 
							FROM mi_card 
							WHERE card_id='".$axRow['card_id']."'";

			$oRes_card = $oDB->Query($sql_card);
			$axRow_card = $oRes_card->FetchRow(DBI_ASSOC);

			# STATUS

			if ($axRow_card['flag_status']==1) {	$status = 'Active';	}
			else {	$status = 'Pending';	}

			# PERIOD TYPE

			if ($axRow_card['period_type'] == '1') { 

				$axRow_card['period_type'] = DateOnly($axRow_card['date_expired']);	

			} else if ($axRow_card['period_type'] == '2') { 

				$axRow_card['period_type'] = $axRow_card['period_type_other'].' Months';	

			} else if ($axRow_card['period_type'] == '3') { 

				$axRow_card['period_type'] = $axRow_card['period_type_other'].' Years';	

			} else if ($axRow_card['period_type'] == '4') { 

				$axRow_card['period_type'] = 'Member Life Time';	
			}


			# EXPRIED DATE

			if ($axRow['date_expried'] != '0000-00-00') { $date_expried = DateOnly($axRow['date_expried']);	} 
			else { $date_expried = '-'; }

		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['member_brand_id'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
				<div class="modal fade" id="View'.$axRow['member_brand_id'].'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>'.$axRow['card_name'].'
						        <span style="float:right">'.$axRow['brand_name'].'</span></b></span>
						        <hr><center>
								<table style="width:90%" class="myPopup">
									<tr>
										<td width="300px" style="text-align:center" rowspan="4">
										'.$member_view.'&nbsp; &nbsp;'.$card_view.'</td>
										<td style="text-align:right">Status</td>
										<td width="20px" style="text-align:center">:</td>
										<td>'.$status.'</td>
									</tr>
									<tr>
										<td style="text-align:right">Member Fee</td>
										<td style="text-align:center">:</td>
										<td>'.number_format($axRow_card['member_fee'],2).' ฿</td>
									</tr>
									<tr>
										<td style="text-align:right">Period Type</td>
										<td style="text-align:center">:</td>
										<td>'.$axRow_card['period_type'].'</td>
									</tr>
									<tr>
										<td style="text-align:right">Member Type</td>
										<td style="text-align:center">:</td>
										<td>'.$axRow['platform'].'</td>
									</tr>
								</table>
								<br><br>
								<table style="width:95%" class="table table-striped table-bordered myPopup">
									<thead><tr class="th_table">
										<th style="text-align:center"><b>Target Member Type</b></th>
										<th style="text-align:center"><b>Member Data</b></th>
									</tr></thead>
									<tbody>';

		$topic = array("Profile", "Home Address", "Work Address", "Work", "Contact");

		for ($j=0; $j <5 ; $j++) { 

			$sql_form = 'SELECT
							b.mafi_NameEn,
							b.mafi_MasterFieldID,
							b.mafi_FieldName,
							b.mafi_FieldType

							FROM register_form AS a

							LEFT JOIN master_field AS b
							ON b.mafi_MasterFieldID = a.mafi_MasterFieldID

							WHERE b.mafi_Position = "'.$topic[$j].'"
							AND b.mafi_Deleted != "T"
							AND a.card_CardID = "'.$axRow['card_id'].'"
							AND a.refo_FillIn = "Y"

							ORDER BY b.mafi_FieldOrder';

			$oRes_form = $oDB->Query($sql_form);

	 		$check_form = $oDB->QueryOne($sql_form);

			if ($check_form) {

				$view .= '<tr>
							<td colspan="2" style="text-align:center;background-color:#F2F2F2"><b>'.$topic[$j].'</b></td>
							</tr>';

				while ($axRow_form = $oRes_form->FetchRow(DBI_ASSOC)){

					// $sql_data = 'SELECT '.$axRow_form['mafi_FieldName'].' 
					// 			FROM mb_member_brand 
					// 			WHERE member_brand_id="'.$axRow['member_brand_id'].'"';

					$data_form = $axRow[$axRow_form['mafi_FieldName']];

			 		// $data_form = $oDB->QueryOne($sql_data);

			 		if (!$data_form) { $data_form = '-'; }

			 		else {

			 			if ($axRow_form['mafi_FieldType'] == '6') { 

			 				if ($data_form == '0000-00-00') { $data_form = '-'; }
			 				else { $data_form = DateOnly($data_form); }

			 			} elseif ($axRow_form['mafi_FieldName']=='home_country' || $axRow_form['mafi_FieldName']=='work_country') {

							$sql_target = 'SELECT coun_Nicename 
											FROM country 
											WHERE coun_CountryID="'.$data_form.'"';

			 				$target_form = $oDB->QueryOne($sql_target);

			 				if ($target_form) { $data_form = $target_form; }

			 			} elseif ($axRow_form['mafi_FieldName']=='home_province' || $axRow_form['mafi_FieldName']=='work_province') {

							$sql_target = 'SELECT prov_Name 
											FROM province 
											WHERE prov_ProvinceID="'.$data_form.'"';

			 				$target_form = $oDB->QueryOne($sql_target);

			 				if ($target_form) { $data_form = $target_form; }

			 			} elseif ($axRow_form['mafi_FieldType'] == '3' || $axRow_form['mafi_FieldType'] == '4' || $axRow_form['mafi_FieldType'] == '5') {

							$sql_target = 'SELECT mata_NameEn 
											FROM master_target 
											WHERE mata_MasterTargetID="'.$data_form.'"';

			 				$target_form = $oDB->QueryOne($sql_target);

			 				if ($target_form) { $data_form = $target_form; }
			 			}
			 		}

					$view .= '<tr>
	                                <td style="text-align:center" width="50%"><b>'.$axRow_form['mafi_NameEn'].'</b></td>
									<td style="text-align:center">'.$data_form.'</td>
								</tr>';
				}
			} 
		}



		# CUSTOM

		$sql_custom = 'SELECT

						b.cufi_Name,
						b.cufi_CustomFieldID

						FROM custom_form AS a

						LEFT JOIN custom_field AS b
						ON b.cufi_CustomFieldID = a.cufi_CustomFieldID

						WHERE b.cufi_Deleted != "T"
						AND a.card_CardID = "'.$axRow['card_id'].'"
						AND a.cufo_FillIn = "Y"
						ORDER BY b.cufi_FieldOrder';

		$oRes_custom = $oDB->Query($sql_custom);

 		$check_custom = $oDB->QueryOne($sql_custom);

		if ($check_custom) {

			$view .= '<tr>
						<td colspan="2" style="text-align:center;background-color:#F2F2F2"><b>Custom</b></td>
						</tr>';

			while ($axRow_custom = $oRes_custom->FetchRow(DBI_ASSOC)){

				$sql_list = 'SELECT cufi_CustomFieldID 
								FROM custom_list_value
								WHERE cufi_CustomFieldID="'.$axRow_custom['cufi_CustomFieldID'].'"';

			 	$list_form = $oDB->QueryOne($sql_list);

				$sql_data = 'SELECT reda_Value 
								FROM mb_member_brand_custom
								WHERE cufi_CustomFieldID="'.$axRow_custom['cufi_CustomFieldID'].'"
								AND member_brand_id="'.$axRow['member_brand_id'].'"';

				$data_form = $oDB->QueryOne($sql_data);

			 	if (!$list_form) { if (!$data_form) { $data_form = '-'; }

			 	} else {

					$sql_target = 'SELECT clva_NameEn 
									FROM custom_list_value
									WHERE clva_Value="'.$data_form.'"
									AND cufi_CustomFieldID="'.$axRow_custom['cufi_CustomFieldID'].'"';

			 		$target_form = $oDB->QueryOne($sql_target);

			 		if ($target_form) { $data_form = $target_form; }

			 		else { $data_form = '-'; }
			 	}

				$view .= '<tr>
	                        <td style="text-align:center"><b>'.$axRow_custom['cufi_Name'].'</b></td>
	                        <td style="text-align:center">'.$data_form.'</td>
	                    </tr>';
	        }
	    }

		$view .= '<tr>
					<td colspan="2" style="text-align:center;background-color:#F2F2F2"><b>Card Register</b></td>
				</tr>
				<tr>
					<td style="text-align:center"><b>Expried Date</b></td>
	                <td style="text-align:center">'.$date_expried.'</td>
	            </tr>
	            <tr>
					<td colspan="2" style="text-align:center;background-color:#F2F2F2"><b>Point Collect</b></td>
				</tr>
				<tr>
					<td style="text-align:center"><b>Point</b></td>
	                <td style="text-align:center">'.$axRow['point_collect'].'</td>
	            </tr>';

		$view .= '</table>
					<table style="width:50%" class="table table-striped table-bordered myPopup">
						<thead><tr class="th_table">
							<th style="text-align:center"><b>Send Email</b></th>
							<th style="text-align:center"><b>Send SMS</b></th>
						</tr></thead>
						<tbody><tr>
							<th style="text-align:center"><b>'.$axRow['send_email'].'</b></th>
							<th style="text-align:center"><b>'.$axRow['send_sms'].'</b></th>
						</tr></tbody>
					</table>
				</center>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>';


	# MEMBER NAME

	$member_name = '';

	if ($axRow['firstname'].' '.$axRow['lastname']) {

		if ($axRow['email']) {

			if ($axRow['mobile']) {

				if ($axRow['member_card_code']) {

					if ($axRow['member_brand_code']) {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'].'<br>Member Card : '.$axRow['member_card_code'].'<br>Member Brand : '.$axRow['member_brand_code'];

					} else {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'].'<br>Member Card : '.$axRow['member_card_code'];
					}

				} else {

					if ($axRow['member_brand_code']) {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'].'<br>Member Brand : '.$axRow['member_brand_code'];
					} else {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'];
					}
				}

			} else {

				if ($axRow['member_card_code']) {

					if ($axRow['member_brand_code']) {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>Member Card : '.$axRow['member_card_code'].'<br>Member Brand : '.$axRow['member_brand_code'];

					} else {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>Member Card : '.$axRow['member_card_code'];
					}

				} else {

					if ($axRow['member_brand_code']) {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>Member Brand : '.$axRow['member_brand_code'];
					} else {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'];
					}
				}
			}

		} else {

			if ($axRow['mobile']) {

				if ($axRow['member_card_code']) {

					if ($axRow['member_brand_code']) {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile'].'<br>Member Card : '.$axRow['member_card_code'].'<br>Member Brand : '.$axRow['member_brand_code'];

					} else {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile'].'<br>Member Card : '.$axRow['member_card_code'];
					}

				} else {

					if ($axRow['member_brand_code']) {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile'].'<br>Member Brand : '.$axRow['member_brand_code'];
					} else {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile'];
					}
				}
				
			} else { 

				if ($axRow['member_card_code']) {

					if ($axRow['member_brand_code']) {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>Member Card : '.$axRow['member_card_code'].'<br>Member Brand : '.$axRow['member_brand_code'];

					} else {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>Member Card : '.$axRow['member_card_code'];
					}

				} else {

					if ($axRow['member_brand_code']) {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>Member Brand : '.$axRow['member_brand_code'];
					} else {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'];
					}
				}
			}
		}

	} else {

		if ($axRow['email']) {

			if ($axRow['mobile']) { $member_name = $axRow['email'].'<br>'.$axRow['mobile']; } 
				
			else { $member_name = $axRow['email']; }

		} else {

			if ($axRow['mobile']) { $member_name = $axRow['mobile']; } 
				
			else { $member_name = ''; }
		}
	}


	# DATA TABLE

	$data_table .= '<tr >
						<td >'.$i++.'</td>
						<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
							<span style="font-size:11px;">'.$axRow['brand_name'].'</span>
						</td>
						<td style="text-align:center"><a href="../card/card.php">'.$card_image.'</a><br>
							<span style="font-size:11px;">'.$axRow['card_name'].'</span></td>
						<td >'.$member_name.'</td>
						<td >'.DateTime($axRow['date_update']).'</td>
						<td style="text-align:center">'.$member_status.'</td>';

	if ($_SESSION['role_action']['upload_member']['view'] == 1) {

		$data_table .=	'<td style="text-align:center">'.$view.'</td>';
	}

	$data_table .=	'</tr>';
}



###########################################################



#  brand dropdownlist

$sql_brand ='SELECT brand_id, name FROM mi_brand WHERE flag_del!=1 ORDER BY name';

$oRes_brand = $oDB->Query($sql_brand);

$select_brand = '';

$selected = "";

if ($brand_id==0) {	$selected = "selected";	}

else {	$selected = "";	}

$select_brand .= '<option value="0" '.$selected.'>All</option>';

$selected = "";

while ($axRow = $oRes_brand->FetchRow(DBI_ASSOC)){

	for($j=0 ; $j<count($_POST["brand_id"]) ; $j++){

		if ($axRow['brand_id']==$_POST["brand_id"][$j]) {	$selected = "selected";	}
	}

	$select_brand .= '<option value="'.$axRow['brand_id'].'" '.$selected.'>'.$axRow['name'].'</option>';

	$selected = "";
}

$oTmp->assign('select_brand', $select_brand);



$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_upload_member');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_membership', 'in');

$oTmp->assign('content_file', 'card/upload_member.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
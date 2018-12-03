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

if ($_SESSION['role_action']['member_register']['view'] != 1) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$StartDate = $_REQUEST['StartDate'];

$EndDate = $_REQUEST['EndDate'];

$path_upload_member = $_SESSION['path_upload_member'];



if ($StartDate && $EndDate) {

	$date = $EndDate;
	$date1 = str_replace('-', '/', $date);
	$EndDate1 = date('Y-m-d',strtotime($date1 . "+1 days"));
	
	$where_date = ' AND a.date_create BETWEEN "'.$StartDate.'" AND "'.$EndDate1.'" ';
	
	$oTmp->assign('dataStartDate', $StartDate);
	$oTmp->assign('dataEndDate', $EndDate);

} else if ($StartDate) {
	
	$where_date = ' AND a.date_create >= "'.$StartDate.'" ';
	$oTmp->assign('dataStartDate', $StartDate);

} else if ($EndDate) {

	$date = $EndDate;
	$date1 = str_replace('-', '/', $date);
	$EndDate1 = date('Y-m-d',strtotime($date1 . "+1 days"));
	
	$where_date = ' AND a.date_create <= "'.$EndDate1.'" ';
	$oTmp->assign('dataEndDate', $EndDate);

} else {

	$where_date = '';
}


if($_SESSION['user_type_id_ses']>1){

	if ($_SESSION['user_type_id_ses']>2) {

		$where_brand = ' AND a.brnc_BranchID = "'.$_SESSION['user_branch_id'].'"';

	} else {

		$where_brand = ' AND b.brand_id = "'.$_SESSION['user_brand_id'].'"';
	}
}


$sql ='SELECT DISTINCT
		a.member_register_id,
		a.date_create,
		b.name AS card_name,
		b.image AS card_image,
		b.image_newupload AS card_image_new,
		b.path_image,
		d.member_image,
		d.firstname,
		d.lastname,
		d.email AS member_email,
		d.date_birth,
		d.facebook_id,
		d.facebook_name,
		d.mobile AS member_mobile,
		d.member_id,
		e.name AS token_name,
		c.logo_image,
		c.path_logo,
		c.name AS brand_name,
		f.name AS branch_name

		FROM mb_member_register AS a

		LEFT JOIN mi_card AS b
		ON a.card_id = b.card_id

		LEFT JOIN mb_member AS d
		ON a.member_id = d.member_id

		LEFT JOIN mi_brand AS c
		ON b.brand_id = c.brand_id

		LEFT JOIN mi_branch AS f
		ON a.brnc_BranchID = f.branch_id

		LEFT JOIN mi_token_type AS e
		ON a.token_type_id = e.token_type_id

		WHERE (a.date_start = "0000-00-00" || a.date_start <= "'.date("Y-m-d").'")

		'.$where_date.'
		'.$where_brand.'

		ORDER BY a.date_create DESC' ;



if($Act=='xls'){


	require_once '../../lib/PHPExcel/Classes/PHPExcel.php';

	$objPHPExcel = new PHPExcel();

	$char = 'A';

	$chars = $char;

	$row_start = 5;

	$reportName = 'Member Register Report';

	$objPHPExcel->setActiveSheetIndex(0)

				->setCellValue('A1', 'Topics this report : '.$reportName )

				->setCellValue('A2', 'Issued this report  : '.$_SESSION['UNAME'].' / '.$_SESSION['FULLNAME'])

				->setCellValue('A3', 'Check out this report : '.$time_insert)

				->setCellValue($chars++.$row_start, 'No.')

				->setCellValue($chars++.$row_start, 'Facebook')

				->setCellValue($chars++.$row_start, 'Name')

				->setCellValue($chars++.$row_start, 'Email')

				->setCellValue($chars++.$row_start, 'Phone')

				->setCellValue($chars++.$row_start, 'Birthday')

				->setCellValue($chars++.$row_start, 'Brand')

				->setCellValue($chars++.$row_start, 'Card')

				->setCellValue($chars++.$row_start, 'Branch')

				->setCellValue($chars++.$row_start, 'Member Fee')

				->setCellValue($chars++.$row_start, 'Platform')

				->setCellValue($chars.$row_start, 'Register Date');


	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:'.$chars.'1');

	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:'.$chars.'2');

	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A3:'.$chars.'3');

	$i = 6;

	$oRes = $oDB->Query($sql);

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)) {


		$chars = $char;

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $i, ($i-5));

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $i, $axRow['facebook_name']);

		$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars++ . $i, $axRow['firstname']." ".$axRow['lastname']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $i, $axRow['member_email']);

		$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars++ . $i, $axRow['member_mobile'], PHPExcel_Cell_DataType::TYPE_STRING);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $i, DateOnly($axRow['date_birth']));

		$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars++ . $i, $axRow['brand_name']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $i, $axRow['card_name']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $i, $axRow['branch_name']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $i, $axRow['total_amt']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $i, $axRow['platform']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars . $i, DateTime($axRow['date_create']));

		$i++;

	}


	$sharedStyle1 = new PHPExcel_Style();

	$sharedStyle1->applyFromArray(

		array(
			  'borders' => array(
								'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
								'right'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN)
								)
	));

	$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A5:".$chars.($i-1));

	$objPHPExcel->getActiveSheet()->getStyle('A5:'.$chars.'5')->applyFromArray(

			array(

				'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),

				'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),

				'borders' => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),

				 'fill' => array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '003369'))

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

	$strFileName = $reportName."_".$date.".xls";			# 2003

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




$oRes = $oDB->Query($sql);

$n=0;

$asData = array();

$data_table = '';

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$n++;


	# MEMBER

	if($axRow['member_image']!='' && $axRow['member_image']!='https://www.memberin.com/images/user.png'){

		$member_image = '<img src="'.$path_upload_member.$axRow['member_image'].'" width="100" height="100" class="img-circle image_border"/>';	

		$axRow['member_image'] = '<img src="'.$path_upload_member.$axRow['member_image'].'" width="50" height="50" class="img-circle image_border"/>';	

	} else if ($axRow['facebook_id']!='') {

		$axRow['member_image'] = '<img src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=square" width="50" height="50" class="img-circle image_border"/>';

		$member_image = '<img src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=square" width="100" height="100" class="img-circle image_border"/>';

	} else {

		$axRow['member_image'] = '<img src="../../images/user.png" width="50" height="50" class="img-circle image_border"/>';

		$member_image = '<img src="../../images/user.png" width="100" height="100" class="img-circle image_border"/>';
	}

	$member_name = '';

	if ($axRow['firstname'].' '.$axRow['lastname']) {

		if ($axRow['member_email']) {

			if ($axRow['member_mobile']) {

				if ($axRow['member_card_code']) {

					if ($axRow['member_brand_code']) {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>'.$axRow['member_mobile'].'<br>Member Card : '.$axRow['member_card_code'].'<br>Member Brand : '.$axRow['member_brand_code'];

					} else {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>'.$axRow['member_mobile'].'<br>Member Card : '.$axRow['member_card_code'];
					}

				} else {

					if ($axRow['member_brand_code']) {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>'.$axRow['member_mobile'].'<br>Member Brand : '.$axRow['member_brand_code'];
					
					} else {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>'.$axRow['member_mobile'];
					}
				}

			} else {

				if ($axRow['member_card_code']) {

					if ($axRow['member_brand_code']) {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>Member Card : '.$axRow['member_card_code'].'<br>Member Brand : '.$axRow['member_brand_code'];

					} else {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>Member Card : '.$axRow['member_card_code'];
					}

				} else {

					if ($axRow['member_brand_code']) {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>Member Brand : '.$axRow['member_brand_code'];
					
					} else {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'];
					}
				}
			}

		} else {

			if ($axRow['member_mobile']) {

				if ($axRow['member_card_code']) {

					if ($axRow['member_brand_code']) {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_mobile'].'<br>Member Card : '.$axRow['member_card_code'].'<br>Member Brand : '.$axRow['member_brand_code'];

					} else {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_mobile'].'<br>Member Card : '.$axRow['member_card_code'];
					}

				} else {

					if ($axRow['member_brand_code']) {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_mobile'].'<br>Member Brand : '.$axRow['member_brand_code'];
					
					} else {
						
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_mobile'];
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

		if ($axRow['member_email']) {

			if ($axRow['member_mobile']) { $member_name = $axRow['member_email'].'<br>'.$axRow['member_mobile']; } 
				
			else { $member_name = $axRow['member_email']; }

		} else {

			if ($axRow['member_mobile']) { $member_name = $axRow['member_mobile']; } 
				
			else { $member_name = ''; }
		}
	}

	
	# CARD IMAGE

	if($axRow['card_image']!=''){

		$card_image = '<img src="../../upload/'.$axRow['path_image'].$axRow['card_image'].'" class="img-rounded image_border" height="100px">';

		$axRow['card_image'] = '<img src="../../upload/'.$axRow['path_image'].$axRow['card_image'].'" class="img-rounded image_border" height="50px">';

	} else if ($axRow['image_newupload']!='') {

		$card_image = '<img src="../../upload/'.$axRow['path_image'].$axRow['image_newupload'].'" class="img-rounded image_border" height="100px">';

		$axRow['card_image'] = '<img src="../../upload/'.$axRow['path_image'].$axRow['image_newupload'].'" class="img-rounded image_border" height="50px">';

	} else {

		$card_image = '<img src="../../images/card_privilege.jpg" class="img-rounded image_border" height="100px">';

		$axRow['card_image'] = '<img src="../../images/card_privilege.jpg" class="img-rounded image_border" height="50px">';
	}


	# LOGO

	if($axRow['logo_image']!=''){

		$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="50" height="50"/>';

	} else {

		$logo_brand = '<img src="../../images/400x400.png" class="image_border" width="50" height="50"/>';
	}


	# TRANSFER

	if ($axRow['payr_TransferStatus']=="Cancel") {

		$axRow['payr_TransferStatus'] = "<span style='color:red'><b>Cancel</b></span>";

	} else if ($axRow['payr_TransferStatus']=="Yes") {

		$axRow['payr_TransferStatus'] = "<span style='color:green'><b>Yes</b></span>";

	} else if ($axRow['payr_TransferStatus']=="Request") {

		$axRow['payr_TransferStatus'] = "<span style='color:yellow'><b>Request</b></span>";

	} else if ($axRow['payr_TransferStatus']=="Wait") {

		$axRow['payr_TransferStatus'] = "<span style='color:orange'><b>Wait</b></span>";
	}

	if ($axRow['total_amt']==0) {
		
		$axRow['total_amt'] = "0.00";
		$axRow['token_name'] = "-";
		$axRow['payr_TransferStatus'] = "<span style='color:blue'><b>-</b></span>";
	}


	# BRANCH

	if ($axRow['branch_name']=='') { $axRow['branch_name'] = '-'; }


	# DELETE

	if ($axRow['flag_del']=='T') { $delete = 'Expired'; }
	else { $delete = 'Active'; }


	# VIEW	

	$edit_status = 'F';

		$sql_member = 'SELECT name_title_type,
							firstname,
							lastname,
							nickname,
							flag_gender,
							date_birth,
							flag_marital,
							no_of_children,
							nationality,
							idcard_no,
							passport_no,
							educate_type,
							interest_activity_type,
							employment_type,
							industry_current_work_type,
							area_work_type,
							monthly_personal_income_type,
							monthly_household_income_type,
							mobile,
							home_phone,
							work_phone,
							email,
							home_address,
							home_area,
							home_room_no,
							home_moo,
							home_junction,
							home_soi,
							home_road,
							home_sub_district,
							home_district,
							home_province,
							home_country,
							home_postcode,
							work_address,
							work_area,
							work_room_no,
							work_moo,
							work_junction,
							work_soi,
							work_road,
							work_sub_district,
							work_province,
							work_country,
							work_postcode
						FROM mb_member 
						WHERE member_id="'.$axRow['member_id'].'"';

		$oRes_member = $oDB->Query($sql_member);
		$member = $oRes_member->FetchRow(DBI_ASSOC);

		$view_table = '<table style="width:90%" class="table table-striped table-bordered myPopup">';

		$topic = array("Profile", "Home Address", "Work Address", "Work", "Contact");

		for ($i=0; $i <5 ; $i++) { 

			$sql_field = 'SELECT a.mafi_NameEn,
							a.mafi_MasterFieldID AS master_field_id,
							b.refo_Target,
							d.fity_Name AS field_type

							FROM master_field AS a

							LEFT JOIN register_form AS b
							ON b.mafi_MasterFieldID = a.mafi_MasterFieldID

							LEFT JOIN mi_card AS c
							ON b.card_CardID = c.card_id

							LEFT JOIN field_type AS d
							ON a.mafi_FieldType = d.fity_FieldTypeID

							WHERE a.mafi_Position = "'.$topic[$i].'"
							AND a.mafi_Deleted != "T"
							AND c.card_id = "'.$axRow['card_id'].'"
							AND b.refo_FillIn = "Y"

							GROUP BY a.mafi_FieldName
							ORDER BY a.mafi_FieldOrder';

			$oRes_field = $oDB->Query($sql_field);
			$check_field = $oDB->QueryOne($sql_field);

			if ($check_field) {

				$view_table .= '<tr class="th_table"><td colspan="3" style="text-align:center;background-color:#003369"><b>'.$topic[$i].'</b></td></tr>';

				while ($field = $oRes_field->FetchRow(DBI_ASSOC)){

					$view_table .= '<tr>
										<td style="text-align:right"><b>'.$field['mafi_NameEn'].'</b></td>
										<td width="10%" style="text-align:center"> : </td>';

					if ($field['field_type']=='Text') {

						# MEMBER BRAND CODE & MEMBER CARD COE

						if ($field['master_field_id']=='48') { # CARD

							$member[$field['mafi_FieldName']] = $axRow['member_card_code'];
							
						} elseif ($field['master_field_id']=='49') { # BRAND

							$member[$field['mafi_FieldName']] = $axRow['member_brand_code'];
						}

						if ($member[$field['mafi_FieldName']]=="") { 

							$member[$field['mafi_FieldName']] = "-";
							$edit_status = "T"; 
						}

						$view_table .= '<td>'.$member[$field['mafi_FieldName']];
						
					} else if ($field['field_type']=='Number') {

						if ($member[$field['mafi_FieldName']]=="0") { 

							$member[$field['mafi_FieldName']] = "-";
							$edit_status = "T"; 
						}

						$view_table .= '<td>'.$member[$field['mafi_FieldName']];
						
					} else if ($field['field_type']=='Date') {

						if ($member[$field['mafi_FieldName']] != '0000-00-00') { 

							$view_table .= '<td>'.DateOnly($member[$field['mafi_FieldName']]); 

						} else { 

							$view_table .= '<td>-';
							$edit_status = "T"; 
						}

					} else if ($field['field_type']=='Radio') {

						$x = 0;

						$data = $member[$field['mafi_FieldName']];

						if ($data=="0") { $edit_status = "T"; }

						$view_table .= '<td><span class="form-inline">';

						$sql_target = 'SELECT mata_MasterTargetID,
											mata_NameEn
										FROM master_target
										WHERE mafi_MasterFieldID = "'.$field['master_field_id'].'"';
						$oRes_target = $oDB->Query($sql_target);
						while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

							if ($data != 0) {

								if ($data == $target['mata_MasterTargetID']) {

									if ($x==0) {

										$view_table .= '<span class="glyphicon glyphicon-check"></span> '.$target['mata_NameEn'].'<label>';

									} else {

										$view_table .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-check"></span> '.$target['mata_NameEn'].'<label>';
									}

								} else {

									if ($x==0) {

										$view_table .= '<span class="glyphicon glyphicon-unchecked"></span> '.$target['mata_NameEn'].'<label>';

									} else {

										$view_table .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-unchecked"></span> '.$target['mata_NameEn'].'<label>';
									}
								}

							} else {

								$view_table .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-unchecked"></span> '.$target['mata_NameEn'].'<label>';
							}

							$x++;
						}

						$view_table .= '</span>';

					} else if ($field['field_type']=='Checkbox') {

						$x = 0;

						$data = $member[$field['mafi_FieldName']];

						if ($data=="") { $edit_status = "T"; }

						$view_table .= '<td><span class="form-inline"><label>';

						$sql_target = 'SELECT mata_MasterTargetID,
											mata_NameEn
										FROM master_target
										WHERE mafi_MasterFieldID = "'.$field['master_field_id'].'"';
						$oRes_target = $oDB->Query($sql_target);
						while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

							if ($data != 0) {

								if ($data == $target['mata_MasterTargetID']) {

									if ($x==0) {

										$view_table .= '<span class="glyphicon glyphicon-check"></span> '.$target['mata_NameEn'].'<label>';

									} else {

										$view_table .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-check"></span> '.$target['mata_NameEn'].'<label>';
									}

								} else {

									if ($x==0) {

										$view_table .= '<span class="glyphicon glyphicon-unchecked"></span> '.$target['mata_NameEn'].'<label>';

									} else {

										$view_table .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-unchecked"></span> '.$target['mata_NameEn'].'<label>';
									}
								}

							} else {

								$view_table .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-unchecked"></span> '.$target['mata_NameEn'].'<label>';
							}

							$x++;
						}

						$view_table .= '</label></span>';

					} else if ($field['field_type']=='Selection') {

						$data = $member[$field['mafi_FieldName']];

						if ($field['master_field_id'] == 33 || $field['master_field_id'] == 45) {

							$sql_target = 'SELECT prov_Name FROM province WHERE prov_ProvinceID = "'.$data.'"';

						} elseif ($field['master_field_id'] == 34 || $field['master_field_id'] == 46) {

							$sql_target = 'SELECT coun_NiceName FROM country WHERE coun_CountryID = "'.$data.'"';

						} else {

							$sql_target = 'SELECT mata_NameEn FROM master_target WHERE mata_MasterTargetID = "'.$data.'"';
						}
						
						$data = $oDB->QueryOne($sql_target);

						if ($data=="") { $edit_status = "T"; $data = "-"; }

						$view_table .= '<td>'.$data;

					} else if ($field['field_type']=='Tel') {

						$data = $member[$field['mafi_FieldName']];

						if ($data=="") { $edit_status = "T"; $data = "-"; }

						$view_table .= '<td>'.$data;
					}

					$view_table .= '</td></tr>';
				}
			}
		}

		$sql_custom = 'SELECT custom_field.cufi_CustomFieldID,
						custom_field.cufi_Name,
						custom_form.cufo_Require,
						field_type.fity_Name AS field_type
						FROM custom_field
						LEFT JOIN custom_form
						ON custom_form.cufi_CustomFieldID = custom_field.cufi_CustomFieldID
						LEFT JOIN field_type
						ON custom_field.fity_FieldTypeID = field_type.fity_FieldTypeID
						WHERE custom_form.card_CardID = "'.$axRow['card_id'].'"
						AND custom_form.cufo_FillIn = "Y"
						ORDER BY custom_field.cufi_FieldOrder';

		$oRes_custom = $oDB->Query($sql_custom);
		$check_field = $oDB->QueryOne($sql_custom);

		if ($check_field) {

			$view_table .= '<tr class="th_table"><td colspan="3" style="text-align:center;background-color:#003369"><b>Custom</b></td></tr>';

			while ($field = $oRes_custom->FetchRow(DBI_ASSOC)){

				$sql_member_custom = 'SELECT reda_Value
										FROM custom_register_data 
										WHERE mebe_MemberID="'.$axRow['member_id'].'"
										AND card_CardID="'.$axRow['card_id'].'"
										AND cufi_CustomFieldID="'.$field['cufi_CustomFieldID'].'"';
				$data = $oDB->QueryOne($sql_member_custom);

				$view_table .= '<tr>
									<td style="text-align:right"><b>'.$field['cufi_Name'].'</b></td>
									<td width="10px" style="text-align:center">:</td>';

				if ($field['field_type']=='Text') {

					if ($data=="") { $data = "-"; $edit_status = "T"; }

					$view_table .= '<td>'.$data;
						
				} else if ($field['field_type']=='Number') {

					if ($data=="0") { $data = "-"; $edit_status = "T"; }

					$view_table .= '<td>'.$data;
						
				} else if ($field['field_type']=='Date') {

					if ($data != '0000-00-00') { 

						$view_table .= '<td>'.DateOnly($data); 

					} else { $view_table .= '<td>-'; $edit_status = "T"; }
						
				} else if ($field['field_type']=='Radio') {

					$x = 0;

					$view_table .= '<td><span class="form-inline"><label>';

					$sql_target = 'SELECT clva_CustomListValueID,
										clva_Name
									FROM custom_list_value
									WHERE cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
					$oRes_target = $oDB->Query($sql_target);
					while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

						if ($data != 0) {

							if ($data == $target['clva_CustomListValueID']) {

								if ($x==0) {

									$view_table .= '<span class="glyphicon glyphicon-check"></span> '.$target['clva_Name'].'<label>';

								} else {

									$view_table .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-check"></span> '.$target['clva_Name'].'<label>';
								}

							} else {

								if ($x==0) {

									$view_table .= '<span class="glyphicon glyphicon-unchecked"></span> '.$target['clva_Name'].'<label>';

								} else {

									$view_table .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-unchecked"></span> '.$target['clva_Name'].'<label>';
								}
							}

						} else {

							$edit_status = "T";

							$view_table .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-unchecked"></span> '.$target['clva_Name'].'<label>';
						}

						$x++;
					}

					$view_table .= '</span>';

				} else if ($field['field_type']=='Checkbox') {

					$x = 0;

					$view_table .= '<td><span class="form-inline"><label>';

					$sql_target = 'SELECT clva_CustomListValueID,
										clva_Name
									FROM custom_list_value
									WHERE cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
					$oRes_target = $oDB->Query($sql_target);
					while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

						if ($data != 0) {

							if ($data == $target['clva_CustomListValueID']) {

								if ($x==0) {

									$view_table .= '<span class="glyphicon glyphicon-check"></span> '.$target['clva_Name'].'<label>';

								} else {

									$view_table .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-check"></span> '.$target['clva_Name'].'<label>';
								}

							} else {

								if ($x==0) {

									$view_table .= '<span class="glyphicon glyphicon-unchecked"></span> '.$target['clva_Name'].'<label>';

								} else {

									$view_table .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-unchecked"></span> '.$target['clva_Name'].'<label>';
								}
							}

						} else {

							$edit_status = "T";

							$view_table .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-unchecked"></span> '.$target['clva_Name'].'<label>';
						}

						$x++;
					}

					$view_table .= '</label></span>';

				} else if ($field['field_type']=='Selection') {

					$sql_target = 'SELECT clva_NameEn FROM custom_list_value WHERE clva_CustomListValueID = "'.$data.'"';
					$data = $oDB->QueryOne($sql_target);

					if ($data=="") { $data = "-"; $edit_status = "T"; }

					$view_table .= '<td>'.$data;

				} else if ($field['field_type']=='Tel') {

					if ($data=="") { $data = "-"; $edit_status = "T"; }

					$view_table .= '<td>'.$data;
				}

				$view_table .= '</td></tr>';
			}
		}

		$view_table .= '</table>';

		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Profile'.$axRow['member_register_id'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>

					<div class="modal fade" id="Profile'.$axRow['member_register_id'].'" tabindex="-1" role="dialog" aria-labelledby="ProfileDataLabel">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
							    <div class="modal-body">
							        <center><br>
							        	'.$member_image.'&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-plus" style="font-size:20px"></span>&nbsp;&nbsp;&nbsp;'.$card_image.'<br><br>
							        	'.$view_table.'
							        </center>';

		// if ($edit_status == 'T') {

			$view .= '		    	<a href="edit_data.php?id='.$axRow['member_register_id'].'">
							        <button type="button" class="btn btn-default btn-sm">Edit Data</button></a>';
		// }

		$view .= '			    </div>
							</div>
						</div>
					</div>';


	# DATA TABLE

	$data_table .= '<tr>
						<td>'.$n.'</td>
						<td style="text-align:center">'.$axRow['member_image'].'</td>
						<td >'.$member_name.'</td>
						<td style="text-align:center">'.$logo_brand.'<br>
							<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
						<td style="text-align:center">'.$axRow['card_image'].'<br>
							<span style="font-size:11px">'.$axRow['card_name'].'</span></td>
						<td style="text-align:center">'.$axRow['branch_name'].'</td>';

	if ($_SESSION['user_type_id_ses']==1) {

		$data_table .= '<td >'.$axRow['token_name'].'</td>';
	}

	$data_table .= '	<td style="text-align:right">'.number_format($axRow['total_amt'],2).' ฿</td>
						<td style="text-align:center">'.$axRow['platform'].'</td>
						<td >'.DateTime($axRow['date_create']).'</td>
						<td style="text-align:center">'.$delete.'<hr>'.$view.'</td>
					</tr>';
}




$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_member_insights');

$oTmp->assign('content_file','member_insights/member_register.html');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>

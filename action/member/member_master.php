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

if ($_SESSION['role_action']['member_new']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$time_insert = date("Y-m-d H:i:s");	
$Act = $_REQUEST['act'];
$id = $_REQUEST['id'];
$path_upload_member = $_SESSION['path_upload_member'];

# SEARCH BRAND

$brand_id = "";

if($_SESSION['user_type_id_ses']>1){

	$brand_id = $_SESSION['user_brand_id'];

} else {

	for($k=0 ; $k<count($_POST["brand_id"]) ; $k++){

		if(trim($_POST["brand_id"][$k]) != ""){

			if ($_POST["brand_id"][$k]==0) { $brand_id = 0; } 

			else {

				if ($k==count($_POST["brand_id"])-1) { $brand_id .= $_POST["brand_id"][$k];	} 
				else { $brand_id .= $_POST["brand_id"][$k].","; }
			}
		}
	}

	if ($brand_id=="" || $brand_id==0) { $brand_id = ""; } 
	else { $_SESSION['export_brand'] = $brand_id; }
}


# SEARCH DATE

$StartDate = $_REQUEST['StartDate'];
$EndDate = $_REQUEST['EndDate'];

if ($StartDate && $EndDate) {

	$where_date = ' AND (SELECT MIN(b.date_create) FROM mb_member_register b WHERE b.member_id=a.member_id GROUP BY b.member_id) BETWEEN "'.$StartDate.'" AND "'.$EndDate.'" ';

	$_SESSION['export_date'] = $where_date;

	$oTmp->assign('dataStartDate', $StartDate);
	$oTmp->assign('dataEndDate', $EndDate);

} else if ($StartDate) {

	$where_date = ' AND (SELECT MIN(b.date_create) FROM mb_member_register b WHERE b.member_id=a.member_id GROUP BY b.member_id) >= "'.$StartDate.'" ';

	$_SESSION['export_date'] = $where_date;
	$oTmp->assign('dataStartDate', $StartDate);

} else if ($EndDate) {

	$where_date = ' AND (SELECT MIN(b.date_create) FROM mb_member_register b WHERE b.member_id=a.member_id GROUP BY b.member_id) <= "'.$EndDate.'" ';
	$_SESSION['export_date'] = $where_date;
	$oTmp->assign('dataEndDate', $EndDate);

} else {

	$where_date = '';
}

if ($where_date=='' && $brand_id=='') {

	$sql ='SELECT *
			FROM mb_member
			ORDER BY mb_member.date_create DESC';

	if($Act=='xls'){

		require_once '../../lib/PHPExcel/Classes/PHPExcel.php';

		$objPHPExcel = new PHPExcel();
		$char = 'A';
		$chars = $char;
		$row_start = 1;
		$reportName = 'New Member Report';
		$objPHPExcel->setActiveSheetIndex(0);

		# MASTER FIELD

		$topic = array("Profile", "Home Address", "Work Address", "Work", "Contact");

		for ($i=0; $i <5 ; $i++) { 

			$sql_field = 'SELECT master_field.mafi_NameEn,
									master_field.mafi_FieldName,
									master_field.mafi_MasterFieldID
								FROM master_field
								WHERE master_field.mafi_Position = "'.$topic[$i].'"
								AND master_field.mafi_Deleted = ""
								AND master_field.mafi_MasterFieldID NOT IN (48,49)
								ORDER BY master_field.mafi_FieldOrder';

			$oRes_field = $oDB->Query($sql_field);

			while ($field = $oRes_field->FetchRow(DBI_ASSOC)){

				$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars++.$row_start, $field['mafi_NameEn']);
			}
		}

		$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars.$row_start, 'Loaded Date');

		# MEMBER

		$oRes_member = $oDB->Query($sql);
		$row_start++;

		$data_table = '';

		while ($member = $oRes_member->FetchRow(DBI_ASSOC)){

			$chars = $char;

			$topic = array("Profile", "Home Address", "Work Address", "Work", "Contact");

			for ($i=0; $i <5 ; $i++) { 

				$sql_field ='SELECT master_field.mafi_NameEn,
										master_field.mafi_FieldName,
										master_field.mafi_MasterFieldID
								FROM master_field
								WHERE master_field.mafi_Position = "'.$topic[$i].'"
								AND master_field.mafi_Deleted = ""
								AND master_field.mafi_MasterFieldID NOT IN (48,49)
								ORDER BY master_field.mafi_FieldOrder';

				$oRes_field = $oDB->Query($sql_field);

				while ($field = $oRes_field->FetchRow(DBI_ASSOC)){

					if ($field['mafi_FieldName'] == 'date_birth') {

						if ($member[$field['mafi_FieldName']] != '0000-00-00') {

							$member[$field['mafi_FieldName']] = DateOnly($member[$field['mafi_FieldName']]);

						} else {

							$member[$field['mafi_FieldName']] = '';
						}

					} else if ($field['mafi_FieldName'] == 'home_province' || $field['mafi_FieldName'] == 'work_province') {

						# PROVINCE

						$sql_province = 'SELECT prov_Name
										FROM province
										WHERE prov_ProvinceID = "'.$member[$field['mafi_FieldName']].'"';

						$province = $oDB->QueryOne($sql_province);

						if ($province) { $member[$field['mafi_FieldName']] = $province; }

						else { 

							if ($member[$field['mafi_FieldName']] == "0") { $member[$field['mafi_FieldName']] = ''; } 
						}

					} else {

						# TARGET

						$sql_target = 'SELECT mata_Name
										FROM master_target
										WHERE mafi_MasterFieldID = "'.$field['mafi_MasterFieldID'].'"
										AND mata_MasterTargetID = "'.$member[$field['mafi_FieldName']].'"';

						$target = $oDB->QueryOne($sql_target);

						if ($target) { $member[$field['mafi_FieldName']] = $target; }

						else { 

							if ($member[$field['mafi_FieldName']] == "0") { $member[$field['mafi_FieldName']] = ''; } 
						}
					}

					$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars++.$row_start, $member[$field['mafi_FieldName']], PHPExcel_Cell_DataType::TYPE_STRING);
				}
			}

			$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars.$row_start, DateTime($member['date_create()']));
			$row_start++;
		}

		$sharedStyle1 = new PHPExcel_Style();
		$sharedStyle1->applyFromArray(

			array(

				  'borders' => array(

									'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
									'right'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN)
									)
		));

		$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:".$chars.($row_start-1));
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.$chars.'1')->applyFromArray(

				array(

					'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
					'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
					'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
					'fill' => array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '5CB2DA'))
				)
		);

		$p=0;

		for ($p=0 ; $p <= get_character_number($chars) ; $p++) {

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

	} else {

		# MASTER FIELD

		$master_field = '';

		$master_field .= '<tr class="th_table">
					        <th rowspan="2" style="text-align:center">No.</th>
					        <th rowspan="2" style="text-align:center">Member</th>
					        <th colspan="2" style="text-align:center">Card</th>';

		$topic = array("Profile", "Home Address", "Work Address", "Work", "Contact");

		for ($i=0; $i <5 ; $i++) { 

			$sql_count = 'SELECT COUNT(DISTINCT master_field.mafi_MasterFieldID)
								FROM master_field
								WHERE master_field.mafi_Position = "'.$topic[$i].'"
								AND master_field.mafi_MasterFieldID NOT IN (48,49)
								AND master_field.mafi_Deleted = ""';

			$count = $oDB->QueryOne($sql_count);

			if ($count != 0) {

				if ($topic[$i] == 'Home Address') { $class = 'HomeA'; }
				else if ($topic[$i] == 'Work Address') { $class = 'WorkA'; }
				else { $class = $topic[$i]; }

				$master_field .= '<th colspan="'.$count.'" class="'.$class.'" style="text-align:center">'.$topic[$i].'</th>';
			}
		}

		# MASTER IFELD

		$master_field .= '<th></th>
							<th></th>
					    </tr>
					    <tr class="th_table">
					    	<th>Active</th>
					    	<th>Expired</th>';

		for ($i=0; $i <5 ; $i++) { 

			$sql_field ='SELECT master_field.mafi_NameEn,
								master_field.mafi_FieldName
							FROM master_field
							WHERE master_field.mafi_Position = "'.$topic[$i].'"
							AND master_field.mafi_Deleted = ""
							AND master_field.mafi_MasterFieldID NOT IN (48,49)
							ORDER BY master_field.mafi_FieldOrder';

			$oRes_field = $oDB->Query($sql_field);

			while ($field = $oRes_field->FetchRow(DBI_ASSOC)){

				if ($topic[$i] == 'Home Address') { $class = 'HomeA'; }
				else if ($topic[$i] == 'Work Address') { $class = 'WorkA'; }
				else { $class = $topic[$i]; }

				$master_field .= '<th class="'.$class.'">'.$field['mafi_NameEn'].'</th>';
			}
		}

		$master_field .= '<th style="text-align:center">Load Date</th>
							<th style="text-align:center">View</th>
					        </tr>';

		$oTmp->assign('master_field', $master_field);	

		# MEMBER

		$oRes_member = $oDB->Query($sql);

		$x = 1;

		$data_table = '';

		while ($member = $oRes_member->FetchRow(DBI_ASSOC)){

			# MEMBER IMAGE

			if($member['member_image']!='' && $member['member_image']!='user.png'){

				$member['member_image'] = '<img src="'.$path_upload_member.$member['member_image'].'" width="60" height="60" class="img-circle image_border"/>';	

			} else if ($member['facebook_id']!='') {

				$member['member_image'] = '<img src="http://graph.facebook.com/'.$member['facebook_id'].'/picture?type=square" width="60" height="60" class="img-circle image_border"/>';

			} else {

				$member['member_image'] = '<img src="../../images/user.png" width="60" height="60" class="img-circle image_border"/>';
			}

			# MEMBER STATUS

			if ($member['member_token']) { 

				$member_status = '<span class="glyphicon glyphicon-exclamation-sign" style="color:#f0ad4e;font-size:20px"></span>'; 
			}

			# CARD

			$today = date("Y-m-d H:i:s");

			$sql_active = 'SELECT COUNT(DISTINCT card_id)
							FROM mb_member_register
							WHERE member_id = "'.$member['member_id'].'"
							'.$brand_search.'
							AND (date_expire > "'.$today.'"
							OR date_expire = date_create)';

			$active = $oDB->QueryOne($sql_active);

			$sql_expired = 'SELECT COUNT(DISTINCT card_id)
							FROM mb_member_register
							WHERE member_id = "'.$member['member_id'].'"
							'.$brand_search.'
							AND date_expire <= "'.$today.'"
							AND date_expire != date_create';

			$expired = $oDB->QueryOne($sql_expired);

			# DATA TABLE

			$data_table .= '<tr>
							  	<td>'.$x++.'<br><center>'.$member_status.'</center></td>
							  	<td style="text-align:center">'.$member['member_image'].'</td>
							  	<td style="text-align:center">'.$active.'</td>
							  	<td style="text-align:center">'.$expired.'</td>';

			$topic = array("Profile", "Home Address", "Work Address", "Work", "Contact");

			for ($i=0; $i <5 ; $i++) { 

				$sql_field ='SELECT master_field.mafi_NameEn,
									master_field.mafi_FieldName,
									master_field.mafi_MasterFieldID
								FROM master_field
								WHERE master_field.mafi_Position = "'.$topic[$i].'"
								AND master_field.mafi_MasterFieldID NOT IN (48,49)
								AND master_field.mafi_Deleted = ""';

				$oRes_field = $oDB->Query($sql_field);

				while ($field = $oRes_field->FetchRow(DBI_ASSOC)){

					if ($field['mafi_FieldName'] == 'date_birth') {

						if ($member[$field['mafi_FieldName']] != '0000-00-00') {

							$member[$field['mafi_FieldName']] = DateOnly($member[$field['mafi_FieldName']]);

						} else {

							$member[$field['mafi_FieldName']] = '';
						}

					} else if ($field['mafi_FieldName'] == 'home_province' || $field['mafi_FieldName'] == 'work_province') {

						# PROVINCE

						$sql_province = 'SELECT prov_Name
										FROM province
										WHERE prov_ProvinceID = "'.$member[$field['mafi_FieldName']].'"';

						$province = $oDB->QueryOne($sql_province);

						if ($province) { $member[$field['mafi_FieldName']] = $province; }

						else { 

							if ($member[$field['mafi_FieldName']] == "0") { $member[$field['mafi_FieldName']] = ''; } 
						}

					} else {

						# TARGET

						$sql_target = 'SELECT mata_Name
										FROM master_target
										WHERE mafi_MasterFieldID = "'.$field['mafi_MasterFieldID'].'"
										AND mata_MasterTargetID = "'.$member[$field['mafi_FieldName']].'"';

						$target = $oDB->QueryOne($sql_target);

						if ($target) { $member[$field['mafi_FieldName']] = $target; }

						else { 

							if ($member[$field['mafi_FieldName']] == "0") { $member[$field['mafi_FieldName']] = ''; } 
						}
					}

					if ($topic[$i] == 'Home Address') { $class = 'HomeA'; }
					else if ($topic[$i] == 'Work Address') { $class = 'WorkA'; }
					else { $class = $topic[$i]; }

					$data_table .= '<td class="'.$class.'">'.$member[$field['mafi_FieldName']].'</td>';
				}
			}

			$data_table .= '<td>'.DateTime($member['date_create']).'</td>
							<td style="text-align:center" width="5%"><span style="cursor:pointer" onclick="'."window.location.href='member_detail.php?id=".$member['member_id']."'".'" target="_blank"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>';
			$data_table .= '</tr>';
		}

		$oTmp->assign('data_table', $data_table);
	}

} else {

	if($Act=='xls' || $Act=='csv'){

		$brand_id = $_SESSION['export_brand'];
		$card_id = $_SESSION['export_card'];
		$where_date = $_SESSION['export_date'];

		$objPHPExcel = new PHPExcel();

		$char = 'A';

		$chars = $char;

		$row_start = 1;

		$reportName = 'Member Master Report';

		$objPHPExcel->setActiveSheetIndex(0);

					// ->setCellValueExplicit('A1', 'Topics this report : '.$reportName )
					// ->setCellValueExplicit('A2', 'Issued this report  : '.$_SESSION['UNAME'].' / '.$_SESSION['FULLNAME'])
					// ->setCellValueExplicit('A3', 'Check out this report : '.$time_insert);

		# MASTER FIELD

		// $objPHPExcel->getActiveSheet()->setCellValueExplicit($chars.$row_start, '');

		$topic = array("Profile", "Home Address", "Work Address", "Work", "Contact");

		// for ($i=0; $i <5 ; $i++) { 

		// 	if ($brand_id) {

		// 		$sql_count ='SELECT COUNT(DISTINCT master_field.mafi_MasterFieldID)
		// 					FROM register_form
		// 					LEFT JOIN master_field
		// 					ON master_field.mafi_MasterFieldID = register_form.mafi_MasterFieldID
		// 					LEFT JOIN mi_card
		// 					ON mi_card.card_id = register_form.card_CardID
		// 					WHERE master_field.mafi_Position = "'.$topic[$i].'"
		// 					AND master_field.mafi_Deleted = ""
		// 					AND register_form.refo_Require = "Y"';

		// 		$sql_count .= ' AND mi_card.brand_id IN ('.$brand_id.')';

		// 		if ($card_id) {

		// 			$sql_count .= ' AND mi_card.card_id IN ('.$card_id.')';
		// 		}

		// 	} else {

		// 		$sql_count ='SELECT COUNT(DISTINCT master_field.mafi_MasterFieldID)
		// 					FROM master_field
		// 					WHERE master_field.mafi_Position = "'.$topic[$i].'"
		// 					AND master_field.mafi_Deleted = ""';
		// 	}

		// 	$oRes_count = $oDB->Query($sql_count);
		// 	$check_count = $oDB->QueryOne($sql_count);

		// 	if ($check_count) {

		// 		$merge = $chars;
		// 		$chars++;

		// 		while ($count = $oRes_count->FetchRow(DBI_ASSOC)){ $merge++; }

		// 		$merge--;

		// 		$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars.$row_start, $topic[$i]);
		// 		$objPHPExcel->getActiveSheet()->mergeCells($chars.$row_start.':'.$merge.$row_start);

		// 		$merge--;
		// 		$chars = $merge;
		// 	}
		// }

		# CUSTOM

		// if ($brand_id) {

		// 	$sql_count = 'SELECT DISTINCT custom_form.cufi_CustomFieldID AS count_field
		// 					FROM custom_form
		// 					LEFT JOIN custom_field
		// 					ON custom_field.cufi_CustomFieldID = custom_form.cufi_CustomFieldID
		// 					WHERE custom_field.cufi_Deleted = ""
		// 					AND custom_form.cufo_Require = "Y"';

		// 	$sql_count .= ' AND custom_field.bran_BrandID IN ('.$brand_id.')';

		// 	if ($card_id) {

		// 		$sql_count .= ' AND custom_form.card_CardID IN ('.$card_id.')';
		// 	}

		// 	$oRes_count = $oDB->Query($sql_count);
		// 	$check_count = $oDB->QueryOne($sql_count);

		// 	if ($check_count) {

		// 		$merge = $chars;
		// 		$chars++;

		// 		while ($count = $oRes_count->FetchRow(DBI_ASSOC)){ $merge++; }

		// 		$merge--;

		// 		$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars.$row_start, 'Custom');
		// 		$objPHPExcel->getActiveSheet()->mergeCells($chars.$row_start.':'.$merge.$row_start);

		// 		$merge--;

		// 		$chars = $merge;
		// 	}
		// }

		// $chars++;

		// $objPHPExcel->getActiveSheet()->setCellValueExplicit($chars.$row_start, '');

		// $row_start++;

		$chars = $char;

		// $objPHPExcel->getActiveSheet()->setCellValueExplicit($chars++.$row_start, 'No.');

		# MASTER FIELD

		$csv_n = 1;
		$csv_field = '';

		for ($i=0; $i <5 ; $i++) { 

			if ($brand_id) {

				$sql_field = 'SELECT DISTINCT master_field.mafi_NameEn,
									master_field.mafi_FieldName,
									master_field.mafi_MasterFieldID
								FROM register_form
								LEFT JOIN master_field
								ON master_field.mafi_MasterFieldID = register_form.mafi_MasterFieldID
								LEFT JOIN mi_card
								ON mi_card.card_id = register_form.card_CardID
								WHERE master_field.mafi_Position = "'.$topic[$i].'"
								AND master_field.mafi_Deleted = ""
								AND master_field.mafi_MasterFieldID NOT IN (48,49)
								AND register_form.refo_Require = "Y"';

				$sql_field .= ' AND mi_card.brand_id IN ('.$brand_id.')';

				if ($card_id) {

					$sql_field .= ' AND mi_card.card_id IN ('.$card_id.')';
				}

			} else {

				$sql_field = 'SELECT master_field.mafi_NameEn,
									master_field.mafi_FieldName,
									master_field.mafi_MasterFieldID
								FROM master_field
								WHERE master_field.mafi_Position = "'.$topic[$i].'"
								AND master_field.mafi_MasterFieldID NOT IN (48,49)
								AND master_field.mafi_Deleted = ""';
			}

			$sql_field .= 'ORDER BY master_field.mafi_FieldOrder';

			$oRes_field = $oDB->Query($sql_field);

			while ($field = $oRes_field->FetchRow(DBI_ASSOC)){

				$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars++.$row_start, $field['mafi_NameEn']);

				if ($csv_n == 1) { $csv_field .= $field['mafi_NameEn']; }
				else { $csv_field .= ','.$field['mafi_NameEn']; }

				$csv_n++;
			}
		}

		# CUSTOM

		if ($brand_id) {

			$sql_field = 'SELECT DISTINCT custom_field.cufi_CustomFieldID,
							custom_field.cufi_Name
							FROM custom_form
							LEFT JOIN custom_field
							ON custom_field.cufi_CustomFieldID = custom_form.cufi_CustomFieldID
							WHERE custom_field.cufi_Deleted = ""
							AND custom_form.cufo_Require = "Y"';

			$sql_field .= ' AND custom_field.bran_BrandID IN ('.$brand_id.')';

			if ($card_id) {

				$sql_field .= ' AND custom_form.card_CardID IN ('.$card_id.')';
			}

			$sql_field .= 'ORDER BY custom_field.cufi_CustomFieldID';

			$oRes_field = $oDB->Query($sql_field);

			while ($field = $oRes_field->FetchRow(DBI_ASSOC)){

				$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars++.$row_start, $field['cufi_Name']);

				if ($csv_n == 1) { $csv_field .= $field['cufi_Name']; }

				else { $csv_field .= ','.$field['cufi_Name']; }

				$csv_n++;
			}
		}

		$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars.$row_start, 'First Register Date');

		$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars.$row_start, 'Update Date');

		$csv_field .= ",First Register Date"; 

		$csv_field .= ",Update Date\n"; 

		# MEMBER

		$member_id = '';

		$sql_member = 'SELECT DISTINCT a.member_id,
						MIN(a.date_create)
						FROM mb_member_register a
						WHERE 1  
						'.$where_date.' ';

		if ($brand_id) {

			$sql_member .= ' AND a.bran_BrandID IN ('.$brand_id.')';
		}

		if ($card_id) {

			$sql_member .= ' AND a.card_id IN ('.$card_id.')';
		}

		$sql_member .= ' GROUP BY a.member_id';
		$oRes_member = $oDB->Query($sql_member);

		while ($member = $oRes_member->FetchRow(DBI_ASSOC)){

			$member_id .= $member['member_id'].',';
		}

		$member_id = substr($member_id,0,-1);

		$sql_member = 'SELECT mb_member.*
						FROM mb_member
						WHERE member_id IN ('.$member_id.')
						ORDER BY mb_member.date_update DESC';

		$oRes_member = $oDB->Query($sql_member);

		// $x = 1;

		$row_start++;

		$data_table = '';

		while ($member = $oRes_member->FetchRow(DBI_ASSOC)){

			$csv_n = 1;

			$chars = $char;

			// $objPHPExcel->getActiveSheet()->setCellValueExplicit($chars++ . $row_start, $x++);

			$topic = array("Profile", "Home Address", "Work Address", "Work", "Contact");

			for ($i=0; $i <5 ; $i++) { 

				if ($brand_id) {

					$sql_field ='SELECT DISTINCT master_field.mafi_NameEn,
										master_field.mafi_FieldName,
										master_field.mafi_MasterFieldID
								FROM register_form
								LEFT JOIN master_field
								ON master_field.mafi_MasterFieldID = register_form.mafi_MasterFieldID
								LEFT JOIN mi_card
								ON mi_card.card_id = register_form.card_CardID
								WHERE master_field.mafi_Position = "'.$topic[$i].'"
								AND master_field.mafi_Deleted = ""
								AND master_field.mafi_MasterFieldID NOT IN (48,49)
								AND register_form.refo_Require = "Y"';

					$sql_field .= ' AND mi_card.brand_id IN ('.$brand_id.')';

					if ($card_id) {

						$sql_field .= ' AND mi_card.card_id IN ('.$card_id.')';
					}

				} else {

					$sql_field ='SELECT master_field.mafi_NameEn,
										master_field.mafi_FieldName,
										master_field.mafi_MasterFieldID
								FROM master_field
								WHERE master_field.mafi_Position = "'.$topic[$i].'"
								AND master_field.mafi_MasterFieldID NOT IN (48,49)
								AND master_field.mafi_Deleted = ""';
				}

				$sql_field .= 'ORDER BY master_field.mafi_FieldOrder';

				$oRes_field = $oDB->Query($sql_field);

				while ($field = $oRes_field->FetchRow(DBI_ASSOC)){

					if ($field['mafi_FieldName'] == 'date_birth') {

						if ($member[$field['mafi_FieldName']] != '0000-00-00') {

							$member[$field['mafi_FieldName']] = DateOnly($member[$field['mafi_FieldName']]);

						} else {

							$member[$field['mafi_FieldName']] = '';
						}

					} else if ($field['mafi_FieldName'] == 'home_province' || $field['mafi_FieldName'] == 'work_province') {

						# PROVINCE

						$sql_province = 'SELECT prov_Name
										FROM province
										WHERE prov_ProvinceID = "'.$member[$field['mafi_FieldName']].'"';

						$province = $oDB->QueryOne($sql_province);

						if ($province) { $member[$field['mafi_FieldName']] = $province; }

						else { 

							if ($member[$field['mafi_FieldName']] == "0") { $member[$field['mafi_FieldName']] = ''; } 
						}

					} else {

						# TARGET

						$sql_target = 'SELECT mata_Name
										FROM master_target
										WHERE mafi_MasterFieldID = "'.$field['mafi_MasterFieldID'].'"
										AND mata_MasterTargetID = "'.$member[$field['mafi_FieldName']].'"';

						$target = $oDB->QueryOne($sql_target);

						if ($target) { $member[$field['mafi_FieldName']] = $target; }

						else { 

							if ($member[$field['mafi_FieldName']] == "0") { $member[$field['mafi_FieldName']] = ''; } 
						}
					}

					$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars++.$row_start, $member[$field['mafi_FieldName']], PHPExcel_Cell_DataType::TYPE_STRING);

					if ($csv_n == 1) { $csv_field .= $member[$field['mafi_FieldName']]; }
					else { $csv_field .= ','.$member[$field['mafi_FieldName']]; }

					$csv_n++;
				}
			}

			# CUSTOM

			if ($brand_id) {

				$sql_custom = 'SELECT custom_field.cufi_CustomFieldID
								FROM custom_form
								LEFT JOIN custom_field
								ON custom_field.cufi_CustomFieldID = custom_form.cufi_CustomFieldID
								WHERE custom_field.cufi_Deleted = ""
								AND custom_form.cufo_Require = "Y"';

				$sql_custom .= ' AND custom_field.bran_BrandID IN ('.$brand_id.')';

				if ($card_id) {

					$sql_custom .= ' AND custom_form.card_CardID IN ('.$card_id.')';
				}

				$sql_custom .= 'ORDER BY custom_field.cufi_CustomFieldID';

				$oRes_custom = $oDB->Query($sql_custom);

				while ($custom = $oRes_custom->FetchRow(DBI_ASSOC)){

					$sql_data = 'SELECT reda_Value
									FROM custom_register_data
									WHERE cufi_CustomFieldID = "'.$custom['cufi_CustomFieldID'].'"
									AND mebe_MemberID = "'.$member['member_id'].'"';

					$data = $oDB->QueryOne($sql_data);

					# TARGET

					$sql_target = 'SELECT clva_Name
									FROM custom_list_value
									WHERE cufi_CustomFieldID = "'.$custom['cufi_CustomFieldID'].'"
									AND clva_Value = "'.$data.'"';

					$target = $oDB->QueryOne($sql_target);

					if ($target) { $data = $target; }

					$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars++.$row_start, $data, PHPExcel_Cell_DataType::TYPE_STRING);

					if ($csv_n == 1) { $csv_field .= $data; }

					else { $csv_field .= ','.$data; }

					$csv_n++;
				}
			}

			# REGISTER DATE

			$sql_register = 'SELECT MIN(date_create) 
							FROM mb_member_register 
							WHERE member_id = "'.$member['member_id'].'"';

			if ($brand_id) {

				$sql_register .= ' AND bran_BrandID IN ('.$brand_id.')';

				if ($card_id) {

					$sql_register .= ' AND card_id IN ('.$card_id.')';
				}
			}

			$register_date = $oDB->QueryOne($sql_register);

			$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars.$row_start, DateTime($register_date));

			$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars.$row_start, DateTime($member['date_update']));

			$csv_field .= ",".DateTime($register_date)."\n";

			$csv_field .= ",".DateTime($member['date_update'])."\n";

			$row_start++;
		}

		// $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:'.$chars.'1');

		// $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:'.$chars.'2');

		// $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A3:'.$chars.'3');

		$sharedStyle1 = new PHPExcel_Style();

		$sharedStyle1->applyFromArray(

			array(
				  'borders' => array(

									'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
									'right'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN)
									)
		));

		$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:".$chars.($row_start-1));

		$objPHPExcel->getActiveSheet()->getStyle('A1:'.$chars.'1')->applyFromArray(

				array(

					'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
					'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
					'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
					 'fill' => array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '5CB2DA'))
				)
		);

		$p=0;

		for ($p=0 ; $p <= get_character_number($chars) ; $p++) {

			$objPHPExcel->getActiveSheet()->getColumnDimension($char++)->setAutoSize(true);
		}

		// Rename sheet

		$objPHPExcel->getActiveSheet()->setTitle($reportName);

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet

		$objPHPExcel->setActiveSheetIndex(0);

		$date = date('Y-m-d');			# 2003

		//======================================
		//			download to Excel
		//======================================

		if ($Act=='csv') {

			$strFileName = $reportName."_".$date.".csv";

			header('Content-Encoding: UTF-8');
			header('Content-Type:text/csv; charset=UTF-8');
			header('Content-Type: application/csv');
			header('Content-Disposition: attachement; filename="'.$strFileName.'"');

			echo $csv_field; 

		} else {

			$strFileName = $reportName."_".$date.".xls";

			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="'.$strFileName.'"');
			header('Cache-Control: max-age=0');

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');		# 23
			$objWriter->save('php://output');
		}

		exit;

	} else {

		# MASTER FIELD

		$master_field = '';

		$master_field .= '<tr class="th_table">
					        <th rowspan="2" style="text-align:center">No.</th>
					        <th rowspan="2" style="text-align:center">Member</th>';

		$topic = array("Profile", "Home Address", "Work Address", "Work", "Contact");

		for ($i=0; $i <5 ; $i++) { 

			if ($brand_id) {

				$sql_count = 'SELECT COUNT(DISTINCT master_field.mafi_MasterFieldID)
								FROM register_form
								LEFT JOIN master_field
								ON master_field.mafi_MasterFieldID = register_form.mafi_MasterFieldID
								LEFT JOIN mi_card
								ON mi_card.card_id = register_form.card_CardID
								WHERE master_field.mafi_Position = "'.$topic[$i].'"
								AND master_field.mafi_Deleted = ""
								AND master_field.mafi_MasterFieldID NOT IN (48,49)
								AND register_form.refo_Require = "Y"';

				$sql_count .= ' AND mi_card.brand_id IN ('.$brand_id.')';

				if ($card_id) {

					$sql_count .= ' AND mi_card.card_id IN ('.$card_id.')';
				}

			} else {

				$sql_count = 'SELECT COUNT(DISTINCT master_field.mafi_MasterFieldID)
								FROM master_field
								WHERE master_field.mafi_Position = "'.$topic[$i].'"
								AND master_field.mafi_MasterFieldID NOT IN (48,49)
								AND master_field.mafi_Deleted = ""';
			}

			$count = $oDB->QueryOne($sql_count);

			if ($count != 0) {

				if ($topic[$i] == 'Home Address') { $class = 'HomeA'; }
				else if ($topic[$i] == 'Work Address') { $class = 'WorkA'; }
				else { $class = $topic[$i]; }

				$master_field .= '<th colspan="'.$count.'" class="'.$class.'" style="text-align:center">'.$topic[$i].'</th>';
			}
		}

		# CUSTOM

		if ($brand_id) {

			$sql_count = 'SELECT COUNT(DISTINCT custom_form.cufi_CustomFieldID) AS count_field
							FROM custom_form
							LEFT JOIN custom_field
							ON custom_field.cufi_CustomFieldID = custom_form.cufi_CustomFieldID
							WHERE custom_field.cufi_Deleted = ""
							AND custom_form.cufo_Require = "Y"';

			$sql_count .= ' AND custom_field.bran_BrandID IN ('.$brand_id.')';

			if ($card_id) {

				$sql_count .= ' AND custom_form.card_CardID IN ('.$card_id.')';
			}

			$count = $oDB->QueryOne($sql_count);

			if ($count != 0) {

				$master_field .= '<th colspan="'.$count.'" style="text-align:center">Custom</th>';
			}
		}

		# MASTER IFELD

		$master_field .= '<th style="text-align:center"></th>
							<th style="text-align:center"></th>
							<th style="text-align:center"></th>
					    </tr>
					    <tr class="th_table">';

		for ($i=0; $i <5 ; $i++) { 

			if ($brand_id) {

				$sql_field ='SELECT DISTINCT master_field.mafi_NameEn,
											master_field.mafi_FieldName
								FROM register_form
								LEFT JOIN master_field
								ON master_field.mafi_MasterFieldID = register_form.mafi_MasterFieldID
								LEFT JOIN mi_card
								ON mi_card.card_id = register_form.card_CardID
								WHERE master_field.mafi_Position = "'.$topic[$i].'"
								AND master_field.mafi_Deleted = ""
								AND master_field.mafi_MasterFieldID NOT IN (48,49)
								AND register_form.refo_Require = "Y"';

				$sql_field .= ' AND mi_card.brand_id IN ('.$brand_id.')';

				if ($card_id) {

					$sql_field .= ' AND mi_card.card_id IN ('.$card_id.')';
				}

			} else {

				$sql_field ='SELECT master_field.mafi_NameEn,
									master_field.mafi_FieldName
							FROM master_field
							WHERE master_field.mafi_Position = "'.$topic[$i].'"
							AND master_field.mafi_MasterFieldID NOT IN (48,49)
							AND master_field.mafi_Deleted = ""';
			}

			$sql_field .= 'ORDER BY master_field.mafi_FieldOrder';

			$oRes_field = $oDB->Query($sql_field);

			while ($field = $oRes_field->FetchRow(DBI_ASSOC)){

				if ($topic[$i] == 'Home Address') { $class = 'HomeA'; }
				else if ($topic[$i] == 'Work Address') { $class = 'WorkA'; }
				else { $class = $topic[$i]; }

				$master_field .= '<th class="'.$class.'">'.$field['mafi_NameEn'].'</th>';
			}
		}

		# CUSTOM

		if ($brand_id) {

			$sql_field = 'SELECT DISTINCT custom_field.cufi_CustomFieldID,
								custom_field.cufi_Name
							FROM custom_form
							LEFT JOIN custom_field
							ON custom_field.cufi_CustomFieldID = custom_form.cufi_CustomFieldID
							WHERE custom_field.cufi_Deleted = ""
							AND custom_form.cufo_Require = "Y"';

			$sql_field .= ' AND custom_field.bran_BrandID IN ('.$brand_id.')';

			if ($card_id) {

				$sql_field .= ' AND custom_form.card_CardID IN ('.$card_id.')';
			}

			$sql_field .= 'ORDER BY custom_field.cufi_CustomFieldID';

			$oRes_field = $oDB->Query($sql_field);

			while ($field = $oRes_field->FetchRow(DBI_ASSOC)){

				$master_field .= '<th>'.$field['cufi_Name'].'</th>';
			}
		}

		$master_field .= '<th style="text-align:center">First Register Date</th>
							<th style="text-align:center">Last Update Date</th>
							<th style="text-align:center">View</th>
					        </tr>';

		$oTmp->assign('master_field', $master_field);

		# MEMBER

		$member_id = '';

		$sql_member = 'SELECT DISTINCT a.member_id,
						MIN(a.date_create)
						FROM mb_member_register a
						WHERE 1  
						'.$where_date.' ';

		if ($brand_id) {

			$sql_member .= ' AND a.bran_BrandID IN ('.$brand_id.')';
		}

		if ($card_id) {

			$sql_member .= ' AND a.card_id IN ('.$card_id.')';
		}

		$sql_member .= ' GROUP BY a.member_id';

		$oRes_member = $oDB->Query($sql_member);

		while ($member = $oRes_member->FetchRow(DBI_ASSOC)){

			$member_id .= $member['member_id'].',';
		}

		$member_id = substr($member_id,0,-1);

		$sql_member = 'SELECT mb_member.*
						FROM mb_member';

		if ($member_id == '') {

			$sql_member .= 'WHERE member_id=0 ';

		} else {

			$sql_member .= ' WHERE member_id IN ('.$member_id.') ';
		}

		$sql_member .= ' ORDER BY mb_member.date_update DESC';	

		$oRes_member = $oDB->Query($sql_member);

		$check_member = $oDB->QueryOne($sql_member);

		$x = 1;

		$data_table = '';

		if ($check_member) {

		while ($member = $oRes_member->FetchRow(DBI_ASSOC)){

			# MEMBER IMAGE

			if($member['member_image']!='' && $member['member_image']!='user.png'){

				$member['member_image'] = '<img src="'.$path_upload_member.$member['member_image'].'" width="60" height="60" class="img-circle image_border"/>';	

			} else if ($member['facebook_id']!='') {

				$member['member_image'] = '<img src="http://graph.facebook.com/'.$member['facebook_id'].'/picture?type=square" width="60" height="60" class="img-circle image_border"/>';

			} else {

				$member['member_image'] = '<img src="../../images/user.png" width="60" height="60" class="img-circle image_border"/>';
			}

			# MEMBER STATUS

			if ($member['member_token']) { 

				$member_status = '<span class="glyphicon glyphicon-exclamation-sign" style="color:#f0ad4e;font-size:20px"></span>'; 
			}

			$data_table .= '<tr>
							  	<td>'.$x++.'<br><center>'.$member_status.'</center></td>
							  	<td style="text-align:center">'.$member['member_image'].'</td>';

			$topic = array("Profile", "Home Address", "Work Address", "Work", "Contact");

			for ($i=0; $i <5 ; $i++) { 

				if ($brand_id) {

					$sql_field ='SELECT DISTINCT master_field.mafi_NameEn,
										master_field.mafi_FieldName,
										master_field.mafi_MasterFieldID
								FROM register_form
								LEFT JOIN master_field
								ON master_field.mafi_MasterFieldID = register_form.mafi_MasterFieldID
								LEFT JOIN mi_card
								ON mi_card.card_id = register_form.card_CardID
								WHERE master_field.mafi_Position = "'.$topic[$i].'"
								AND master_field.mafi_Deleted = ""
								AND master_field.mafi_MasterFieldID NOT IN (48,49)
								AND register_form.refo_Require = "Y"';

					$sql_field .= ' AND mi_card.brand_id IN ('.$brand_id.')';

					if ($card_id) {

						$sql_field .= ' AND mi_card.card_id IN ('.$card_id.')';
					}

				} else {

					$sql_field ='SELECT master_field.mafi_NameEn,
										master_field.mafi_FieldName,
										master_field.mafi_MasterFieldID
								FROM master_field
								WHERE master_field.mafi_Position = "'.$topic[$i].'"
								AND master_field.mafi_MasterFieldID NOT IN (48,49)
								AND master_field.mafi_Deleted = ""';
				}

				$oRes_field = $oDB->Query($sql_field);

				while ($field = $oRes_field->FetchRow(DBI_ASSOC)){

					if ($field['mafi_FieldName'] == 'date_birth') {

						if ($member[$field['mafi_FieldName']] != '0000-00-00') {

							$member[$field['mafi_FieldName']] = DateOnly($member[$field['mafi_FieldName']]);

						} else {

							$member[$field['mafi_FieldName']] = '';
						}

					} else if ($field['mafi_FieldName'] == 'home_province' || $field['mafi_FieldName'] == 'work_province') {

						# PROVINCE

						$sql_province = 'SELECT prov_Name
											FROM province
											WHERE prov_ProvinceID = "'.$member[$field['mafi_FieldName']].'"';

						$province = $oDB->QueryOne($sql_province);

						if ($province) { $member[$field['mafi_FieldName']] = $province; }

						else { 

							if ($member[$field['mafi_FieldName']] == "0") { $member[$field['mafi_FieldName']] = ''; } 
						}

					} else {

						# TARGET

						$sql_target = 'SELECT mata_Name
										FROM master_target
										WHERE mafi_MasterFieldID = "'.$field['mafi_MasterFieldID'].'"
										AND mata_MasterTargetID = "'.$member[$field['mafi_FieldName']].'"';

						$target = $oDB->QueryOne($sql_target);

						if ($target) { $member[$field['mafi_FieldName']] = $target; }

						else { 

							if ($member[$field['mafi_FieldName']] == "0") { $member[$field['mafi_FieldName']] = ''; } 
						}
					}

					if ($topic[$i] == 'Home Address') { $class = 'HomeA'; }
					else if ($topic[$i] == 'Work Address') { $class = 'WorkA'; }
					else { $class = $topic[$i]; }

					$data_table .= '<td class="'.$class.'">'.$member[$field['mafi_FieldName']].'</td>';
				}
			}

			if($brand_id) {

				# CUSTOM

				$sql_custom = 'SELECT DISTINCT custom_field.cufi_CustomFieldID
								FROM custom_form
								LEFT JOIN custom_field
								ON custom_field.cufi_CustomFieldID = custom_form.cufi_CustomFieldID
								WHERE custom_field.cufi_Deleted = ""
								AND custom_form.cufo_Require = "Y"
								AND custom_field.bran_BrandID IN ('.$brand_id.')';

				if ($card_id) {

					$sql_custom .= ' AND custom_form.card_CardID IN ('.$card_id.')';
				}

				$sql_custom .= ' ORDER BY custom_field.cufi_CustomFieldID';

				$oRes_custom = $oDB->Query($sql_custom);

				while ($custom = $oRes_custom->FetchRow(DBI_ASSOC)){

					$sql_data = 'SELECT reda_Value
									FROM custom_register_data
									WHERE cufi_CustomFieldID = "'.$custom['cufi_CustomFieldID'].'"
									AND mebe_MemberID = "'.$member['member_id'].'"';

					$data = $oDB->QueryOne($sql_data);

					# TARGET

					$sql_target = 'SELECT clva_Name
									FROM custom_list_value
									WHERE cufi_CustomFieldID = "'.$custom['cufi_CustomFieldID'].'"
									AND clva_Value = "'.$data.'"';

					$target = $oDB->QueryOne($sql_target);

					if ($target) { $data = $target; }

					$data_table .= '<td>'.$data.'</td>';
				}
			}

			# REGISTER DATE

			$sql_register = 'SELECT MIN(date_create) 
								FROM mb_member_register 
								WHERE member_id = "'.$member['member_id'].'"';

			if ($brand_id) {

				$sql_register .= ' AND bran_BrandID IN ('.$brand_id.')';

				if ($card_id) {

					$sql_register .= ' AND card_id IN ('.$card_id.')';
				}
			}

			$register_date = $oDB->QueryOne($sql_register);

			$data_table .= '<td>'.DateTime($register_date).'</td>';

			$data_table .= '<td>'.DateTime($member['date_update']).'</td>';

			$data_table .= '<td style="text-align:center" width="5%"><span style="cursor:pointer" onclick="'."window.location.href='member_detail.php?id=".$member['member_id']."'".'" target="_blank"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>';

			$data_table .= '</tr>';

		}

		$oTmp->assign('data_table', $data_table);

		}
	}
}



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

$oTmp->assign('is_menu', 'is_member');

$oTmp->assign('content_file', 'member/member_master.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}


//========================================//

?>
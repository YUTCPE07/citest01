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

if ($_SESSION['role_action']['brand']['view'] != 1) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$time_insert = date("Y-m-d H:i:s");
$Act = $_REQUEST['act'];
$id = $_REQUEST['id'];

$where_brand = '';

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND a.brand_id = "'.$_SESSION['user_brand_id'].'"';
}


$sql = 'SELECT

		a.*,
		a.date_update AS date_update,
	  	a.brand_id AS brand_id,
	  	a.flag_del AS status_del,
	  	d.name AS category_brand,
	  	b.name_en AS type_brand,
	  	c.name AS company_type,
	  	t.name AS user_type

	  	FROM mi_brand AS a

		LEFT JOIN mi_category_brand AS d 
		ON d.category_brand_id = a.category_brand 

		LEFT JOIN mi_brand_type AS b
		ON b.brand_type_id = a.type_brand 

		LEFT JOIN mi_user AS u
		ON u.user_id = a.update_by 

		LEFT JOIN mi_user_type AS t
		ON u.user_type_id = t.user_type_id 

		LEFT JOIN mi_master AS c
		ON c.value = a.company_type 

		WHERE c.type = "company_type"
		'.$where_brand.' 

		ORDER BY CASE 
			WHEN a.flag_del = "0" THEN 1
            WHEN a.flag_del = "1" THEN 2 END ASC,
			a.flag_status ASC, 
			a.date_update DESC';


if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE mi_brand 
	 					SET flag_status='2',
	 						date_update='".$time_insert."' 
	 					WHERE brand_id='".$id."'";

 	$oDB->QueryOne($do_sql_status);
 	echo '<script>window.location.href="brand.php";</script>';

} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE mi_brand 
	 					SET flag_status='1',
	 						date_update='".$time_insert."'
	 					WHERE brand_id='".$id."'";

 	$oDB->QueryOne($do_sql_status);
 	echo '<script>window.location.href="brand.php";</script>';

} if($Act == 'recommend_y' && $id != '') {

	# UPDATE RECOMMEND YES

	$do_sql_recommend = "UPDATE mi_brand 
	 					SET flag_recommend='Yes',
	 						date_update='".$time_insert."' 
	 					WHERE brand_id='".$id."'";

 	$oDB->QueryOne($do_sql_recommend);
 	echo '<script>window.location.href="brand.php";</script>';

} else if($Act == 'recommend_n' && $id != '') {

	# UPDATE RECOMMEND NO

	$do_sql_recommend = "UPDATE mi_brand 
	 					SET flag_recommend='No',
	 						date_update='".$time_insert."' 
	 					WHERE brand_id='".$id."'";

 	$oDB->QueryOne($do_sql_recommend);
 	echo '<script>window.location.href="brand.php";</script>';

} else if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT flag_del FROM mi_brand WHERE brand_id ="'.$id.'"';

	$oRes = $oDB->Query($sql);
	$axRow = $oRes->FetchRow(DBI_ASSOC);
		
	if($axRow['flag_del']==0) {
 				
 		$do_sql_brand = "UPDATE mi_brand 
 							SET flag_del=1, 
 							flag_status='2',
 							date_update='".$time_insert."' 
 							WHERE brand_id='".$id."'";
 				
 		$do_sql_user = "UPDATE mi_user 
 							SET flag_del=1, 
 							flag_status='2',
 							date_update='".$time_insert."' 
 							WHERE brand_id='".$id."'";

 	} else if ($axRow['flag_del']==1) {

		$do_sql_brand = "UPDATE mi_brand 
							SET flag_del=0, 
							flag_status='2',
							date_update='".$time_insert."' 
							WHERE brand_id='".$id."'";
 				
 		$do_sql_user = "UPDATE mi_user 
 							SET flag_del=0, 
 							flag_status='2',
 							date_update='".$time_insert."' 
 							WHERE brand_id='".$id."'";
	}

 	$oDB->QueryOne($do_sql_brand);
 	$oDB->QueryOne($do_sql_user);
 			
 	echo '<script>window.location.href="brand.php";</script>';

} else if($Act=='xls'){

	require_once '../../lib/PHPExcel/Classes/PHPExcel.php';

	$objPHPExcel = new PHPExcel();

	$char = 'A';

	$chars = $char;

	$row_start = 1;

	$reportName = 'Brand Report';

	$objPHPExcel->setActiveSheetIndex(0);

	// HEAD SHEET

    $objWorkSheet = $objPHPExcel->createSheet('0');

    $objWorkSheet	->setCellValue('A1', 'Topics this report : '.$reportName )
                   	->setCellValue('A2', 'Issued this report  : '.$_SESSION['UNAME'].' / '.$_SESSION['FULLNAME'])
                   	->setCellValue('A3', 'Check out this report : '.$time_insert);

    $objWorkSheet->setTitle("Header");

    // DATA SHEET

    $objWorkSheet = $objPHPExcel->createSheet('1');

    $objWorkSheet 	->setCellValue($chars++.$row_start, 'Brand')
					->setCellValue($chars++.$row_start, 'Type')
					->setCellValue($chars++.$row_start, 'Category')
					->setCellValue($chars++.$row_start, 'Company Name')
					->setCellValue($chars++.$row_start, 'Company Type')
					->setCellValue($chars++.$row_start, 'Tax Type')
					->setCellValue($chars++.$row_start, 'TAX Identification No.')
					->setCellValue($chars++.$row_start, 'Issue By')
					->setCellValue($chars++.$row_start, 'Issue Date')
					->setCellValue($chars++.$row_start, 'Email')
					->setCellValue($chars++.$row_start, 'Phone')
					->setCellValue($chars++.$row_start, 'Mobile')
					->setCellValue($chars++.$row_start, 'Fax')
					->setCellValue($chars++.$row_start, 'Website')
					->setCellValue($chars++.$row_start, 'Facebook')
					->setCellValue($chars++.$row_start, 'Line')
					->setCellValue($chars++.$row_start, 'Instagram')
					->setCellValue($chars++.$row_start, 'Twitter')
					->setCellValue($chars++.$row_start, 'Slogan')
					->setCellValue($chars++.$row_start, 'Signature Info')
					->setCellValue($chars++.$row_start, 'Price Range Type')
					->setCellValue($chars++.$row_start, 'Special For Group')
					->setCellValue($chars++.$row_start, 'Special For Children')
					->setCellValue($chars++.$row_start, 'Special For Other')
					->setCellValue($chars++.$row_start, 'Status')
					->setCellValue($chars++.$row_start, 'Updated Date')
					->setCellValue($chars.$row_start, 'Delete');

	$row_start++;

	$oRes = $oDB->Query($sql);

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)) {

		if($axRow['status_del']==0) {	$axRow['status_del']="No";	}
		else if ($axRow['status_del']==1) {	$axRow['status_del']="Yes";	}

		if($axRow['flag_status']==1){	$axRow['flag_status'] = 'Active';	} 
		else {	$axRow['flag_status'] = 'Pending';	}

		if ($axRow['tax_type'] == 1) {	$axRow['tax_type'] = 'VAT. Registration';	}
		else if ($axRow['tax_type'] == 2) {	$axRow['tax_type'] = 'VAT. Exemption';	}
		else {	$axRow['tax_type'] = '-';	}

		if ($axRow['tax_issue_date'] == '0000-00-00 00:00:00') { $axRow['tax_issue_date'] = ''; }
		else { $axRow['tax_issue_date'] = DateOnly($axRow['tax_issue_date']); }

		$sql_price = 'SELECT name FROM mi_master WHERE type="price_lange_type" AND value="'.$axRow['price_range_type'].'"';
		$price_range_type = $oDB->QueryOne($sql_price);

		if ($price_range_type == '') { $price_range_type = '';	}

		if ($axRow['special_for_group'] == '') { $axRow['special_for_group'] = '';	}
		else {	$axRow['special_for_group'] = 'Yes';	}

		if ($axRow['special_for_children'] == '') { $axRow['special_for_children'] = '';	}
		else {	$axRow['special_for_children'] = 'Yes';	}

		$chars = $char;

		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['name']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['type_brand']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['category_brand']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['company_name']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['company_type']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['tax_type']);
		$objWorkSheet->setCellValueExplicit($chars++.$row_start, $axRow['tax_id'], PHPExcel_Cell_DataType::TYPE_STRING);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['tax_issue_by']);
		$objWorkSheet->setCellValue($chars++.$row_start, DateTime($axRow['tax_issue_date']));
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['email']);
		$objWorkSheet->setCellValueExplicit($chars++.$row_start, $axRow['phone'], PHPExcel_Cell_DataType::TYPE_STRING);
		$objWorkSheet->setCellValueExplicit($chars++.$row_start, $axRow['mobile'], PHPExcel_Cell_DataType::TYPE_STRING);
		$objWorkSheet->setCellValueExplicit($chars++.$row_start, $axRow['fax'], PHPExcel_Cell_DataType::TYPE_STRING);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['website']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['facebook_url']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['line_id']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['instragram']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['tweeter']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['slogan']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['signature_info']);
		$objWorkSheet->setCellValue($chars++.$row_start, $price_range_type);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['special_for_group']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['special_for_children']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['other']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['flag_status']);
		$objWorkSheet->setCellValue($chars++.$row_start, DateTime($axRow['date_update']));
		$objWorkSheet->setCellValue($chars.$row_start, $axRow['status_del']);

		$row_start++;
	}

    $objWorkSheet->setTitle("Data");

	$sharedStyle1 = new PHPExcel_Style();

	$sharedStyle1->applyFromArray(

		array(
			  'borders' => array(
								'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
								'right'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN)
								)
	));

	$objWorkSheet->setSharedStyle($sharedStyle1, "A1:".$chars.($row_start-1));

	$objWorkSheet->getStyle('A1:'.$chars.'1')->applyFromArray(

		array(
			'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
			'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
			'borders' => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
			'fill' => array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '003369'))
		)
	);

	$p=0;

	for($p=0;$p <= get_character_number($chars);$p++){

		$objWorkSheet->getColumnDimension($char++)->setAutoSize(true);
	}

	$objPHPExcel->setActiveSheetIndex(0);

	$date = date('Y-m-d');

	$strFileName = $reportName."_".$date.".xls";

	//======================================
	//			download to Excel
	//======================================

	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

	header('Content-Disposition: attachment;filename="'.$strFileName.'"');

	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

	$objWriter->save('php://output');

	exit;

} else {

	$oRes = $oDB->Query($sql);

	$i=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;


		# LOGO COVER BRAND

		if($axRow['logo_image']!=''){

			$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="80" height="80"/>';

			$logo_image = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="100" height="100"/>';

			$logo_view = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="150" height="150"/>';

		} else {

			$logo_brand = '<img src="../../images/400x400.png" width="80" height="80"/>';

			$logo_image = '<img src="../../images/400x400.png" width="100" height="100"/>';

			$logo_view = '<img src="../../images/400x400.png" width="150" height="150"/>';
		}

		if($axRow['cover']!=''){

			$cover_brand = '<img src="../../upload/'.$axRow['path_cover'].$axRow['cover'].'" class="image_border" height="150"/>';

		} else {

			$cover_brand = '<img src="../../images/img_size.jpg" height="150"/>';
		}

		# VIEW

			# DATA

			if ($axRow['tax_type'] == 1) {	$axRow['tax_type'] = 'VAT. Registration';	}
			else if ($axRow['tax_type'] == 2) {	$axRow['tax_type'] = 'VAT. Exemption';	}
			else {	$axRow['tax_type'] = '-';	}

			if ($axRow['tax_id'] == '') { $axRow['tax_id'] = '-';	}

			if ($axRow['tax_issue_by'] == '') { $axRow['tax_issue_by'] = '-';	}

			if ($axRow['tax_issue_date'] == '0000-00-00 00:00:00') { $axRow['tax_issue_date'] = '-'; }
			else { $axRow['tax_issue_date'] = DateOnly($axRow['tax_issue_date']); }

			if ($axRow['phone'] == '') { $axRow['phone'] = '-';	}

			if ($axRow['mobile'] == '') { $axRow['mobile'] = '-';	}

			if ($axRow['fax'] == '') { $axRow['fax'] = '-';	}

			if ($axRow['website'] == '') { $axRow['website'] = '-';	}

			if ($axRow['facebook_url'] == '') { $axRow['facebook_url'] = '-';	}

			if ($axRow['line_id'] == '') { $axRow['line_id'] = '-';	}

			if ($axRow['instragram'] == '') { $axRow['instragram'] = '-';	}

			if ($axRow['tweeter'] == '') { $axRow['tweeter'] = '-';	}

			if ($axRow['slogan'] == '') { $axRow['slogan'] = '-';	}

			if ($axRow['signature_info'] == '') { $axRow['signature_info'] = '-';	}

			if ($axRow['greeting_message'] == '') { $axRow['greeting_message'] = '-';	}

			$sql_price = 'SELECT name FROM mi_master WHERE type="price_lange_type" AND value="'.$axRow['price_range_type'].'"';
			$price_range_type = $oDB->QueryOne($sql_price);

			if ($price_range_type == '') { $price_range_type = '-';	}

			if ($axRow['special_for_group'] == '') { $axRow['special_for_group'] = '<span class="glyphicon glyphicon-unchecked"></span>';	}
			else {	$axRow['special_for_group'] = '<span class="glyphicon glyphicon-check"></span>';	}

			if ($axRow['special_for_children'] == '') { $axRow['special_for_children'] = '<span class="glyphicon glyphicon-unchecked"></span>';	}
			else {	$axRow['special_for_children'] = '<span class="glyphicon glyphicon-check"></span>';	}

			if ($axRow['other'] == '') { $axRow['other'] = '';	}
			else {	$axRow['other'] = '<span class="glyphicon glyphicon-check"></span> '.$axRow['other']; }

			$special_type = $axRow['special_for_group'].' Group<br>';
			$special_type .= $axRow['special_for_children'].' Children<br>';
			$special_type .= $axRow['other'];

			if ($axRow['text_color'] == 'white') { $axRow['text_color'] = 'F2F2F2';	}
			else {	$axRow['text_color'] = '111111'; }

			if ($axRow['code_color'] == 'FFFFFF') { $axRow['code_color'] = 'F2F2F2'; }

			if ($axRow['flag_hidden'] == 'No') { 
				$axRow['flag_hidden'] = '<span class="glyphicon glyphicon-eye-open"></span>';	
			} else { $axRow['flag_hidden'] = '<span class="glyphicon glyphicon-eye-close"></span>'; }

			if ($axRow['open_brief'] == '') { $axRow['open_brief'] = '-';	}
			if ($axRow['promotion_reservation_brief'] == '') { $axRow['promotion_reservation_brief'] = '-';	}
			if ($axRow['promotion_howtouse_brief'] == '') { $axRow['promotion_howtouse_brief'] = '-';	}
			if ($axRow['promotion_cancellation_brief'] == '') { $axRow['promotion_cancellation_brief'] = '-';	}
			if ($axRow['promotion_condition'] == '') { $axRow['promotion_condition'] = '-';	}
			if ($axRow['promotion_additional'] == '') { $axRow['promotion_additional'] = '-';	}
			if ($axRow['promotion_exception'] == '') { $axRow['promotion_exception'] = '-';	}
			if ($axRow['promotion_q1'] == '') { $axRow['promotion_q1'] = '-';	}
			if ($axRow['promotion_q2'] == '') { $axRow['promotion_q2'] = '-';	}
			if ($axRow['promotion_q3'] == '') { $axRow['promotion_q3'] = '-';	}
			if ($axRow['promotion_q4'] == '') { $axRow['promotion_q4'] = '-';	}
			if ($axRow['promotion_q5'] == '') { $axRow['promotion_q5'] = '-';	}

			if ($axRow['shop_reservation_brief'] == '') { $axRow['shop_reservation_brief'] = '-';	}
			if ($axRow['shop_howtouse_brief'] == '') { $axRow['shop_howtouse_brief'] = '-';	}
			if ($axRow['shop_cancellation_brief'] == '') { $axRow['shop_cancellation_brief'] = '-';	}
			if ($axRow['shop_condition'] == '') { $axRow['shop_condition'] = '-';	}
			if ($axRow['shop_additional'] == '') { $axRow['shop_additional'] = '-';	}
			if ($axRow['shop_exception'] == '') { $axRow['shop_exception'] = '-';	}
			if ($axRow['shop_q1'] == '') { $axRow['shop_q1'] = '-';	}
			if ($axRow['shop_q2'] == '') { $axRow['shop_q2'] = '-';	}
			if ($axRow['shop_q3'] == '') { $axRow['shop_q3'] = '-';	}
			if ($axRow['shop_q4'] == '') { $axRow['shop_q4'] = '-';	}
			if ($axRow['shop_q5'] == '') { $axRow['shop_q5'] = '-';	}

			if ($axRow['member_reservation_brief'] == '') { $axRow['member_reservation_brief'] = '-';	}
			if ($axRow['member_howtouse_brief'] == '') { $axRow['member_howtouse_brief'] = '-';	}
			if ($axRow['member_cancellation_brief'] == '') { $axRow['member_cancellation_brief'] = '-';	}
			if ($axRow['member_condition'] == '') { $axRow['member_condition'] = '-';	}
			if ($axRow['member_additional'] == '') { $axRow['member_additional'] = '-';	}
			if ($axRow['member_exception'] == '') { $axRow['member_exception'] = '-';	}
			if ($axRow['member_q1'] == '') { $axRow['member_q1'] = '-';	}
			if ($axRow['member_q2'] == '') { $axRow['member_q2'] = '-';	}
			if ($axRow['member_q3'] == '') { $axRow['member_q3'] = '-';	}
			if ($axRow['member_q4'] == '') { $axRow['member_q4'] = '-';	}
			if ($axRow['member_q5'] == '') { $axRow['member_q5'] = '-';	}

			$axRow['benefits'] = '';
			if ($axRow['benefits_1']) { $axRow['benefits'] .= '1.'.$axRow['benefits_1'].'<br>'; }
			if ($axRow['benefits_2']) { $axRow['benefits'] .= '2.'.$axRow['benefits_2'].'<br>'; }
			if ($axRow['benefits_3']) { $axRow['benefits'] .= '3.'.$axRow['benefits_3'].'<br>'; }
			if ($axRow['benefits_4']) { $axRow['benefits'] .= '4.'.$axRow['benefits_4'].'<br>'; }
			if ($axRow['benefits_5']) { $axRow['benefits'] .= '5.'.$axRow['benefits_5'].'<br>'; }
			if ($axRow['benefits_6']) { $axRow['benefits'] .= '6.'.$axRow['benefits_6'].'<br>'; }
			if ($axRow['benefits_7']) { $axRow['benefits'] .= '7.'.$axRow['benefits_7'].'<br>'; }
			if ($axRow['benefits_8']) { $axRow['benefits'] .= '8.'.$axRow['benefits_8'].'<br>'; }
			if ($axRow['benefits_9']) { $axRow['benefits'] .= '9.'.$axRow['benefits_9'].'<br>'; }
			if ($axRow['benefits_10']) { $axRow['benefits'] .= '10.'.$axRow['benefits_10']; }
			if ($axRow['benefits']=='') { $axRow['benefits'] = '-'; }

			$axRow['differences'] = '';
			if ($axRow['differences_1']) { $axRow['differences'] .= '1.'.$axRow['differences_1'].'<br>'; }
			if ($axRow['differences_2']) { $axRow['differences'] .= '2.'.$axRow['differences_2'].'<br>'; }
			if ($axRow['differences_3']) { $axRow['differences'] .= '3.'.$axRow['differences_3'].'<br>'; }
			if ($axRow['differences_4']) { $axRow['differences'] .= '4.'.$axRow['differences_4'].'<br>'; }
			if ($axRow['differences_5']) { $axRow['differences'] .= '5.'.$axRow['differences_5'].'<br>'; }
			if ($axRow['differences_6']) { $axRow['differences'] .= '6.'.$axRow['differences_6'].'<br>'; }
			if ($axRow['differences_7']) { $axRow['differences'] .= '7.'.$axRow['differences_7'].'<br>'; }
			if ($axRow['differences_8']) { $axRow['differences'] .= '8.'.$axRow['differences_8'].'<br>'; }
			if ($axRow['differences_9']) { $axRow['differences'] .= '9.'.$axRow['differences_9'].'<br>'; }
			if ($axRow['differences_10']) { $axRow['differences'] .= '10.'.$axRow['differences_10']; }
			if ($axRow['differences']=='') { $axRow['differences'] = '-'; }


			# BRANCH 

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
				
		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['brand_id'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>

				<div class="modal fade" id="View'.$axRow['brand_id'].'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
					<div class="modal-dialog" role="document" style="width:50%">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px">'.$axRow['flag_hidden'].' &nbsp; <b>'.$axRow['name'].'</b></span>
						        <div style="float:right">
						        <span class="glyphicon glyphicon-stop" style="font-size:25px;color:#'.$axRow['code_color'].'"></span>
						        <span class="glyphicon glyphicon-stop" style="font-size:25px;color:#'.$axRow['text_color'].'"></span>
						        </div>
						        
						        <hr>
						        <center>
						        	'.$logo_view.' '.$cover_brand.'<br><br>
						        	<span style="font-size:12px">
					                <ul id="tapMenu" class="nav nav-tabs">
					                    <li class="active">
					                    	<a data-toggle="tab" href="#basic'.$axRow['brand_id'].'">
					                    	<center><b>Basic</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#profile'.$axRow['brand_id'].'">
					                    	<center><b>Profile</b></center></a>
					                   	</li>
					                    <li>
					                    	<a data-toggle="tab" href="#social'.$axRow['brand_id'].'">
					                    	<center><b>Social</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#promotion'.$axRow['brand_id'].'">
					                    	<center><b>Promotion Info.</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#shop'.$axRow['brand_id'].'">
					                    	<center><b>Shop Info.</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#member'.$axRow['brand_id'].'">
					                    	<center><b>Member Info.</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#branch'.$axRow['brand_id'].'">
					                    	<center><b>Branch</b></center></a>
					                    </li>
					                </ul>
					                </span>
					                <div class="tab-content">
					                    <div id="basic'.$axRow['brand_id'].'" class="tab-pane active"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Brand Name</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['name'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Type</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['type_brand'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Category</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['category_brand'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Slogan</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.nl2br($axRow['slogan']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Signature Info.</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.nl2br($axRow['signature_info']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Benefits</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['benefits'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Differences</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['differences'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Opening Hours</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['open_brief'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['open_description']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Price Range Type</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$price_range_type.'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Special For Type</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$special_type.'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="profile'.$axRow['brand_id'].'" class="tab-pane"><br>
								        	<table width="100%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Company / Organization / Shop Name</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['company_name'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Company Type</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['company_type'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Tax Type</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['tax_type'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Vat</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['tax_vat'].' %</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Tax Identification No.</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['tax_id'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Issue By</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['tax_issue_by'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Issue Date</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['tax_issue_date'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Email</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['email'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Phone</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['phone'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Mobile</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['mobile'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Fax</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['fax'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="social'.$axRow['brand_id'].'" class="tab-pane"><br>
					                    	<table width="80%" class="myPopup">
								        		<tr height="35px">
								        			<td style="text-align:right" width="45%"><img src="../../images/icon/web.png" width="25" height="25" alt="Website"></td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['website'].'</td>
								        		</tr>
								        		<tr height="35px">
								        			<td style="text-align:right"><img src="../../images/icon/facebook.png" width="25" height="25" alt="Website"></td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['facebook_url'].'</td>
								        		</tr>
								        		<tr height="35px">
								        			<td style="text-align:right"><img src="../../images/icon/line.png" width="25" height="25" alt="Website"></td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['line_id'].'</td>
								        		</tr>
								        		<tr height="35px">
								        			<td style="text-align:right"><img src="../../images/icon/instagram.png" width="25" height="25" alt="Website"></td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['instragram'].'</td>
								        		</tr>
								        		<tr height="35px">
								        			<td style="text-align:right"><img src="../../images/icon/twiter.png" width="25" height="25" alt="Website"></td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['tweeter'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="promotion'.$axRow['brand_id'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Reservation</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['promotion_reservation_brief'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['promotion_reservation_description']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">How To Use</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['promotion_howtouse_brief'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['promotion_howtouse_description']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">Cancellation Policy</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['promotion_cancellation_brief'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['promotion_cancellation_description']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">Condition</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.nl2br($axRow['promotion_condition']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">Additional Information</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.nl2br($axRow['promotion_additional']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">Exception</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.nl2br($axRow['promotion_exception']).'</td>
								        		</tr>
								        		<tr>
								        			<td colspan="3" style="text-align:center">
								        				<hr>
								        				Q&A
								        			</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">1.</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['promotion_q1'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['promotion_a1']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">2.</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['promotion_q2'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['promotion_a2']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">3.</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['promotion_q3'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['promotion_a3']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">4.</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['promotion_q4'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['promotion_a4']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">5.</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['promotion_q5'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['promotion_a5']).'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="shop'.$axRow['brand_id'].'" class="tab-pane"><br>
								        	
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Reservation</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['shop_reservation_brief'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['shop_reservation_description']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">How To Use</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['shop_howtouse_brief'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['shop_howtouse_description']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">Cancellation Policy</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['shop_cancellation_brief'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['shop_cancellation_description']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">Condition</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.nl2br($axRow['shop_condition']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">Additional Information</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.nl2br($axRow['shop_additional']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">Exception</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.nl2br($axRow['shop_exception']).'</td>
								        		</tr>
								        		<tr>
								        			<td colspan="3" style="text-align:center">
								        				<hr>
								        				Q&A
								        			</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">1.</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['shop_q1'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['shop_a1']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">2.</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['shop_q2'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['shop_a2']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">3.</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['shop_q3'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['shop_a3']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">4.</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['shop_q4'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['shop_a4']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">5.</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['shop_q5'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['shop_a5']).'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="member'.$axRow['brand_id'].'" class="tab-pane"><br>
								        	
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Reservation</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['member_reservation_brief'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['member_reservation_description']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">How To Use</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['member_howtouse_brief'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['member_howtouse_description']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">Cancellation Policy</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['member_cancellation_brief'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['member_cancellation_description']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">Condition</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.nl2br($axRow['member_condition']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">Additional Information</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.nl2br($axRow['member_additional']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">Exception</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.nl2br($axRow['member_exception']).'</td>
								        		</tr>
								        		<tr>
								        			<td colspan="3" style="text-align:center">
								        				<hr>
								        				Q&A
								        			</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">1.</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['member_q1'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['member_a1']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">2.</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['member_q2'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['member_a2']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">3.</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['member_q3'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['member_a3']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">4.</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['member_q4'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['member_a4']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">5.</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['member_q5'].'</td>
								        		</tr>
								        		<tr>
								        			<td></td>
								        			<td></td>
								        			<td>'.nl2br($axRow['member_a5']).'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="branch'.$axRow['brand_id'].'" class="tab-pane"><br>';

		if ($check_brnc) {

			$view .= '						<a target="_blank" href="branch_qrcode.php?id='.$axRow['brand_id'].'">
											<button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-print" aria-hidden="true"></span>&nbsp; Print All QRCodes</button></a><br><br>';
		}
								        	
		$view .= '				        	<div style="height:320px;overflow:auto;">
					                    	<table style="width:80%;" class="table table-striped table-bordered myPopup">
								        		<thead><tr class="th_table">
				                                    <td style="text-align:center"><b>Branch</b></td>
				                                    <td colspan="2" style="text-align:center"><b>Qr Code</b></td>
				                                </tr></thead>
				                                <tbody>'.$branch_data.'</tbody>
								        	</table>
								        	</div>
					                    </div>
					                </div>
						        </center>
						    </div>
						    <div class="modal-footer">';

		if ($_SESSION['role_action']['brand']['edit'] == 1) {		    
			
			$view .= '       <a href="brand_create.php?act=edit&id='.$axRow['brand_id'].'">
						        <button type="button" class="btn btn-default btn-sm">Edit</button></a>';
		}
		
		$view .= '      	<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		# DELETED

		if($axRow['status_del']==0) {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['brand_id'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>

				<div class="modal fade" id="Deleted'.$axRow['brand_id'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="120px" style="text-align:center">'.$logo_image.'</td>
						        	<td>
								        <p style="font-size:12px;padding-left:10px;">
								        	<b>"'.$axRow['name'].'"</b><br>
								            By clicking the <b>"Inactive"</b> button to:<br>
								            &nbsp; &nbsp;- Inactive this brand<br>
								            &nbsp; &nbsp;- Inactive user\'s brand
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="brand.php?act=delete&id='.$axRow['brand_id'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		
		} else if ($axRow['status_del']==1) {
				
			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['brand_id'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>

				<div class="modal fade" id="Deleted'.$axRow['brand_id'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="120px" style="text-align:center">'.$logo_image.'</td>
						        	<td>
								        <p style="font-size:12px;padding-left:10px;">
								        	<b>"'.$axRow['name'].'"</b><br>
								           	By clicking the <b>"Active"</b> button to:<br>
								            &nbsp; &nbsp;- Active this brand<br>
								            &nbsp; &nbsp;- Active user\'s brand<br>
								            &nbsp; &nbsp;- Change status to Pending
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="brand.php?act=delete&id='.$axRow['brand_id'].'">
						        <button type="button" class="btn btn-default btn-sm">Active</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		}


		# STATUS

		$status = '';

		if($axRow['status_del']==1){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';	

		} else {

			if($axRow['flag_status']==1){

				if ($_SESSION['role_action']['brand']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'brand.php?act=active&id='.$axRow['brand_id'].'\'">
		                    <option class="status_default" value="'.$axRow['brand_id'].'" selected>On</option>
		                    <option class="status_default">Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';	
		        }

			} else {

				if ($_SESSION['role_action']['brand']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_pending" name="pending_status" onchange="window.location.href=\'brand.php?act=pending&id='.$axRow['brand_id'].'\'">
		                    <option class="status_default">On</option>
		                    <option class="status_default" value="'.$axRow['brand_id'].'" selected>Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';	
		        }
			}
		}


		# RECOMMEND

		$recommend = '';

		if($axRow['flag_recommend']=='Yes'){

			$recommend = '<form id="myForm" method="POST">
						<select class="form-control text-md" onchange="window.location.href=\'brand.php?act=recommend_n&id='.$axRow['brand_id'].'\'">
		                    <option class="status_default" value="'.$axRow['brand_id'].'" selected>Yes</option>
		                    <option class="status_default">No</option>
		                </select>
		            </form>';

		} else {

			$recommend = '<form id="myForm" method="POST">
						<select class="form-control text-md" onchange="window.location.href=\'brand.php?act=recommend_y&id='.$axRow['brand_id'].'\'">
		                    <option class="status_default">Yes</option>
		                    <option class="status_default" value="'.$axRow['brand_id'].'" selected>No</option>
		                </select>
		            </form>';
		}


		# DATA TABLE

		$data_table .= '<tr >
							<td >'.$i.'</td>
							<td style="text-align:center">'.$logo_brand.'</td>
							<td >'.$axRow['name'].'</td>
							<td >'.$axRow['category_brand'].'</td>
							<td >'.$axRow['email'].'</td>';

		if($_SESSION['user_type_id_ses']==1){

			$data_table .= '<td style="text-align:center">'.$recommend.'</td>';
		}

		$data_table .= '	<td style="text-align:center">'.$status.'</td>
							<td style="text-align:center">'.DateTime($axRow['date_update']).'<hr>'.$axRow['user_type'].'</td>';

		if ($_SESSION['role_action']['brand']['view'] == 1) {

			$data_table .= '<td style="text-align:center">'.$view.'</td>';
		}

		if ($_SESSION['role_action']['brand']['delete'] == 1) {

			$data_table .= 	'<td style="text-align:center">'.$deleted.'</td>';
		}

		$data_table .= '</tr>';
	}
}

$oTmp->assign('data_table', $data_table);
$oTmp->assign('is_menu', 'is_brand');
$oTmp->assign('content_file', 'brand/brand.htm');
$oTmp->display('layout/template.html');

//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
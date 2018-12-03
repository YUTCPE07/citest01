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

if ($_SESSION['role_action']['branch']['view'] != 1) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$path_upload_logo = $_SESSION['path_upload_logo'];

$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];


$where_brand = '';


if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND a.brand_id = "'.$_SESSION['user_brand_id'].'" AND a.flag_del=0';

	if($_SESSION['user_branch_id']){

		$where_brand .= ' AND a.branch_id = "'.$_SESSION['user_branch_id'].'"';
	}
}


# SEARCH

$brand_id = "";

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

else {	$where_search = " AND b.brand_id IN (".$brand_id.")";	}



# SQL

$sql = 'SELECT 
		a.*,
		b.name as brand_name,
		b.logo_image,
		b.path_logo,
		a.flag_del AS status_del,
		c.prov_Name AS province,
	  	t.name AS user_type,
	  	p.coun_Nicename,
	  	d.dist_Name,
	  	e.sudi_Name,
	  	l.land_Name

		FROM mi_branch AS a

		LEFT JOIN mi_brand AS b
		ON a.brand_id = b.brand_id 

		LEFT JOIN mi_user AS u
		ON u.user_id = a.update_by 

		LEFT JOIN mi_user_type AS t
		ON u.user_type_id = t.user_type_id 

		LEFT JOIN province AS c
		ON a.province_id = c.prov_ProvinceID 

		LEFT JOIN country AS p
		ON a.country_id = p.coun_CountryID 

		LEFT JOIN district AS d
		ON a.district_id = d.dist_DistrictID 

		LEFT JOIN sub_district AS e
		ON a.sub_district_id = e.sudi_SubDistrictID 

		LEFT JOIN landmark AS l
		ON a.landmark_id = l.land_LandmarkID 

		WHERE 1
		'.$where_search.'
		'.$where_brand.' 

		ORDER BY CASE 
			WHEN a.flag_del = "0" THEN 1
            WHEN a.flag_del = "1" THEN 2 END ASC,
			a.flag_status ASC, 
			a.date_update DESC';


if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE mi_branch 
	 					SET flag_status='2',
	 						date_update='".$time_insert."' 
	 					WHERE branch_id='".$id."'";

 	$oDB->QueryOne($do_sql_status);
 	echo '<script>window.location.href="branch.php";</script>';

} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE mi_branch 
	 					SET flag_status='1',
	 						date_update='".$time_insert."'
	 					WHERE branch_id='".$id."'";

 	$oDB->QueryOne($do_sql_status);
 	echo '<script>window.location.href="branch.php";</script>';

} else if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT flag_del FROM mi_branch WHERE branch_id ="'.$id.'"';
	$oRes = $oDB->Query($sql);
	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($axRow['flag_del']==0) {

 		$do_sql_branch = "UPDATE mi_branch
 							SET flag_del=1, 
 							flag_status='2',
 							date_update='".$time_insert."' 
 							WHERE branch_id='".$id."'";

 		$do_sql_user = "UPDATE mi_user 
 							SET flag_del=1, 
 							flag_status='2',
 							date_update='".$time_insert."' 
 							WHERE branch_id='".$id."'";

 	} else if ($axRow['flag_del']==1) {

		$do_sql_branch = "UPDATE mi_branch
							SET flag_del=0, 
							flag_status='2',
							date_update='".$time_insert."' 
							WHERE branch_id='".$id."'";

 		$do_sql_user = "UPDATE mi_user 
 							SET flag_del=0, 
 							flag_status='2',
 							date_update='".$time_insert."' 
 							WHERE branch_id='".$id."'";
	}

 	$oDB->QueryOne($do_sql_branch);
 	$oDB->QueryOne($do_sql_user);

 	echo '<script>window.location.href="branch.php";</script>';


} else if($Act=='xls'){

	require_once '../../lib/PHPExcel/Classes/PHPExcel.php';

	$objPHPExcel = new PHPExcel();

	$char = 'A';

	$chars = $char;

	$row_start = 1;

	$reportName = 'Branch Report';

	// HEAD SHEET

    $objWorkSheet = $objPHPExcel->createSheet('0');

    $objWorkSheet	->setCellValue('A1', 'Topics this report : '.$reportName )
                   	->setCellValue('A2', 'Issued this report  : '.$_SESSION['UNAME'].' / '.$_SESSION['FULLNAME'])
                   	->setCellValue('A3', 'Check out this report : '.$time_insert);

    $objWorkSheet->setTitle("Header");

    // DATA SHEET

    $objWorkSheet = $objPHPExcel->createSheet('1');

	$objWorkSheet	->setCellValue($chars++.$row_start, 'Brand')
					->setCellValue($chars++.$row_start, 'Branch')
					->setCellValue($chars++.$row_start, 'Headquarter')
					->setCellValue($chars++.$row_start, 'Address')
					->setCellValue($chars++.$row_start, 'Moo')
					->setCellValue($chars++.$row_start, 'Junction')
					->setCellValue($chars++.$row_start, 'Soi')
					->setCellValue($chars++.$row_start, 'Street')
					->setCellValue($chars++.$row_start, 'Sub District')
					->setCellValue($chars++.$row_start, 'District')
					->setCellValue($chars++.$row_start, 'Province')
					->setCellValue($chars++.$row_start, 'Postcode')
					->setCellValue($chars++.$row_start, 'Email')
					->setCellValue($chars++.$row_start, 'Phone')
					->setCellValue($chars++.$row_start, 'Mobile')
					->setCellValue($chars++.$row_start, 'Fax')
					->setCellValue($chars++.$row_start, 'Operation Time')
					->setCellValue($chars++.$row_start, 'Max Customer')
					->setCellValue($chars++.$row_start, 'Credit Card Use')
					->setCellValue($chars++.$row_start, 'Reserve')
					->setCellValue($chars++.$row_start, 'Parking Area')
					->setCellValue($chars++.$row_start, 'Children')
					->setCellValue($chars++.$row_start, 'Description')
					->setCellValue($chars++.$row_start, 'Special Info.')
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
		
		if($axRow['default_status']==1) { $axRow['default_status'] = 'Yes'; } 
		else {	$axRow['default_status'] = 'No';	}

		if ($axRow['max_customer'] == '0') { $axRow['max_customer'] = 'Unlimit'; }

		if ($axRow['flag_credit_use'] == '1') { $axRow['flag_credit_use'] = 'Yes';	}
		else {	$axRow['flag_credit_use'] = 'No';	}

		if ($axRow['flag_reserve'] == '1') { $axRow['flag_reserve'] = 'Yes';	}
		else {	$axRow['flag_reserve'] = 'No';	}

		if ($axRow['flag_parking_area'] == '1') { $axRow['flag_parking_area'] = 'Yes';	}
		else {	$axRow['flag_parking_area'] = 'No';	}

		if ($axRow['flag_children'] == '1') { $axRow['flag_children'] = 'Yes';	}
		else {	$axRow['flag_children'] = 'No';	}

		$chars = $char;

		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['brand_name']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['name']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['default_status']);
		$objWorkSheet->setCellValueExplicit($chars++.$row_start, $axRow['address_no'], PHPExcel_Cell_DataType::TYPE_STRING);
		$objWorkSheet->setCellValueExplicit($chars++.$row_start, $axRow['moo'], PHPExcel_Cell_DataType::TYPE_STRING);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['junction']);
		$objWorkSheet->setCellValueExplicit($chars++.$row_start, $axRow['soi'], PHPExcel_Cell_DataType::TYPE_STRING);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['road']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['sudi_Name']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['dist_Name']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['province']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['postcode']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['email']);
		$objWorkSheet->setCellValueExplicit($chars++.$row_start, $axRow['phone'], PHPExcel_Cell_DataType::TYPE_STRING);
		$objWorkSheet->setCellValueExplicit($chars++.$row_start, $axRow['mobile'], PHPExcel_Cell_DataType::TYPE_STRING);
		$objWorkSheet->setCellValueExplicit($chars++.$row_start, $axRow['fax'], PHPExcel_Cell_DataType::TYPE_STRING);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['operation_time']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['max_customer']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['flag_credit_use']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['flag_reserve']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['flag_parking_area']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['flag_children']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['description']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['signature_info']);
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

		# STATUS

		$status = '';

		if($axRow['status_del']==1){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

		} else {

			if($axRow['flag_status']==1){

				if ($_SESSION['role_action']['branch']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
								<select class="form-control input-sm status_active" name="active_status" onchange="window.location.href=\'branch.php?act=active&id='.$axRow['branch_id'].'\'">
				                    <option class="status_default" value="'.$axRow['branch_id'].'" selected>On</option>
				                    <option class="status_default">Off</option>
				                </select>
				            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control input-sm status_active" name="active_status">On</button>';
		        }

			} else {

				if ($_SESSION['role_action']['branch']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
								<select class="form-control input-sm status_pending" name="pending_status" onchange="window.location.href=\'branch.php?act=pending&id='.$axRow['branch_id'].'\'">
				                    <option class="status_default">On</option>
				                    <option class="status_default" value="'.$axRow['branch_id'].'" selected>Off</option>
				                </select>
				            </form>';
		        } else {

		        	$status = '<button style="width:60px;" class="form-control input-sm status_pending" name="pending_status">Off</button>';
		        }
			}
		}


		# HEADQUARTERS
		
		if($axRow['default_status']==1) {	

			$default_status = '<div style="float:right"><span class="glyphicon glyphicon-map-marker"></span> Headquarter</div>';	

		} else {	$default_status = '';	}



		# LOGO

		if($axRow['logo_image']!=''){

			$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';

			$logo_image = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="100" height="100"/>';

		} else {

			$logo_brand = '<img src="../../images/400x400.png" width="60" height="60"/>';

			$logo_image = '<img src="../../images/400x400.png" width="100" height="100"/>';
		}



		# QRCODE

		if($axRow['qr_code_image']!=''){

			$qr_code_image = '<img src="../../upload/'.$axRow['path_qr'].$axRow['qr_code_image'].'" class="image_border" width="60" height="60"/>';

			$qr_code_view = '<img src="../../upload/'.$axRow['path_qr'].$axRow['qr_code_image'].'" class="image_border" width="100" height="100"/>';
		}



		# VIEW

			# DATA

			if ($axRow['email'] == '') { $axRow['email'] = '-';	}

			if ($axRow['phone'] == '') { $axRow['phone'] = '-';	}

			if ($axRow['mobile'] == '') { $axRow['mobile'] = '-';	}

			if ($axRow['fax'] == '') { $axRow['fax'] = '-';	}

			if ($axRow['operation_time'] == '') { $axRow['operation_time'] = '-';	}

			if ($axRow['address_no'] == '') { $axRow['address_no'] = '-';	}

			if ($axRow['moo'] == '') { $axRow['moo'] = '-';	}

			if ($axRow['junction'] == '') { $axRow['junction'] = '-';	}

			if ($axRow['soi'] == '') { $axRow['soi'] = '-';	}

			if ($axRow['road'] == '') { $axRow['road'] = '-';	}

			if ($axRow['postcode'] == '') { $axRow['postcode'] = '-';	}

			if ($axRow['max_customer'] == '0') { $axRow['max_customer'] = 'Unlimit';	}

			if ($axRow['flag_credit_use'] == '1') { $axRow['flag_credit_use'] = '<span class="glyphicon glyphicon-check"></span>';	}
			else {	$axRow['flag_credit_use'] = '<span class="glyphicon glyphicon-unchecked"></span>';	}

			if ($axRow['flag_reserve'] == '1') { $axRow['flag_reserve'] = '<span class="glyphicon glyphicon-check"></span>';	}
			else {	$axRow['flag_reserve'] = '<span class="glyphicon glyphicon-unchecked"></span>';	}

			if ($axRow['flag_parking_area'] == '1') { $axRow['flag_parking_area'] = '<span class="glyphicon glyphicon-check"></span>';	}
			else {	$axRow['flag_parking_area'] = '<span class="glyphicon glyphicon-unchecked"></span>';	}

			if ($axRow['flag_children'] == '1') { $axRow['flag_children'] = '<span class="glyphicon glyphicon-check"></span>';	}
			else {	$axRow['flag_children'] = '<span class="glyphicon glyphicon-unchecked"></span>';	}

			if ($axRow['description'] == '') { $axRow['description'] = '-';	}

			if ($axRow['signature_info'] == '') { $axRow['signature_info'] = '-';	}

			if ($axRow['how_to_get_there'] == '') { $axRow['how_to_get_there'] = '-';	}

			if ($axRow['flag_hidden'] == 'No') { 
				$axRow['flag_hidden'] = '<span class="glyphicon glyphicon-eye-open"></span>';	
			} else { $axRow['flag_hidden'] = '<span class="glyphicon glyphicon-eye-close"></span>'; }


			# LANDMARK

			$landmark_data = '';

			if ($axRow['land_Name']!='') {

				$landmark_data .= '<tr>
									    <td style="text-align:right" width="45%">Landmark</td>
									    <td style="text-align:center" width="5%">:</td>
									    <td>'.$axRow['land_Name'].'</td>
									</tr>
									<tr>
									    <td style="text-align:right" width="45%">Floor</td>
									    <td style="text-align:center" width="5%">:</td>
									    <td>'.$axRow['landmark_floor'].'</td>
									</tr>';
			}



		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['branch_id'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
				<div class="modal fade" id="View'.$axRow['branch_id'].'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px">'.$axRow['flag_hidden'].' &nbsp; <b>'.$axRow['name'].'</b></span>
						        <hr>
						        <center>
						        	<table width="90%" class="myPopup"><tr>
						        		<td width="110px" style="text-align:center">'.$logo_image.'</td>
						        		<td width="110px" style="text-align:center">'.$qr_code_view.'</td>
						        		<td width="70px" style="text-align:right">
						        			Email<br>
						        			Phone<br>
						        			Mobile<br>
						        			Fax<br>
						        			Operation Time</td>
						        		<td width="5%" style="text-align:center">
						        			:<br>
						        			:<br>
						        			:<br>
						        			:<br>
						        			:</td>
						        		<td>
						        			'.$axRow['email'].'<br>
						        			'.$axRow['phone'].'<br>
						        			'.$axRow['mobile'].'<br>
						        			'.$axRow['fax'].'<br>
						        			'.$axRow['operation_time'].'</td>
						        	</tr></table><br>
						        	<span style="font-size:12px">
					                <ul id="tapMenu" class="nav nav-tabs">
					                    <li class="active">
					                    	<a data-toggle="tab" href="#address'.$axRow['branch_id'].'">
					                    	<center><b>Address</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#special'.$axRow['branch_id'].'">
					                    	<center><b>Special Info.</b></center></a>
					                   	</li>
					                    <li>
					                    	<a data-toggle="tab" href="#note'.$axRow['branch_id'].'">
					                    	<center><b>Note</b></center></a>
					                    </li>
					                </ul>
					                </span>
					                <div class="tab-content">
					                    <div id="address'.$axRow['branch_id'].'" class="tab-pane active"><br>
								        	<table width="80%" class="myPopup">
								        		'.$landmark_data.'
								        		<tr>
								        			<td style="text-align:right" width="45%">Address</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['address_no'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Moo</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['moo'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Junction</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['junction'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Soi</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['soi'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Street</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['road'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Sub District</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['sudi_Name'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">District</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['dist_Name'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Province</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['province'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Contry</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['coun_Nicename'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Postcode</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['postcode'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">How To Get There</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.nl2br($axRow['how_to_get_there']).'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="special'.$axRow['branch_id'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Max Customer</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['max_customer'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Credit Card Use</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['flag_credit_use'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Reserve</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['flag_reserve'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Parking Area</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['flag_parking_area'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Children</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['flag_children'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="note'.$axRow['branch_id'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" valign="top" width="45%">Description</td>
								        			<td style="text-align:center" width="5%" valign="top">:</td>
								        			<td>'.nl2br($axRow['description']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Special Info.</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.nl2br($axRow['signature_info']).'</td>
								        		</tr>
								        	</table>
					                    </div>
					                </div>
						        </center>
						    </div>
						    <div class="modal-footer">';

			if ($_SESSION['role_action']['branch']['edit'] == 1) {

				$view .= '	    <a href="branch_create.php?act=edit&id='.$axRow['branch_id'].'">
						        <button type="button" class="btn btn-default btn-sm">Edit</button></a>';
			}

			$view .= '        	<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';



		# DELETED

		if($axRow['status_del']==0) {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['branch_id'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>
				<div class="modal fade" id="Deleted'.$axRow['branch_id'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="120px" style="text-align:center" valign="top">'.$logo_image.'</td>
						        	<td>
								        <p style="font-size:12px;padding-left:10px;">
								        	<b>"'.$axRow['name'].'"</b><br>
								            By clicking the <b>"Inactive"</b> button to:<br>
								            &nbsp; &nbsp;- Inactive this branch<br>
								            &nbsp; &nbsp;- Inactive user\'s branch
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="branch.php?act=delete&id='.$axRow['branch_id'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		} else if ($axRow['status_del']==1) {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['branch_id'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>
				<div class="modal fade" id="Deleted'.$axRow['branch_id'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="120px" style="text-align:center" valign="top">'.$logo_image.'</td>
						        	<td>
								        <p style="font-size:12px;padding-left:10px;">
								        	<b>"'.$axRow['name'].'"</b><br>
								           	By clicking the <b>"Active"</b> button to:<br>
								            &nbsp; &nbsp;- Active this branch<br>
								            &nbsp; &nbsp;- Active user\'s branch<br>
								            &nbsp; &nbsp;- Change status to Pending
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="branch.php?act=delete&id='.$axRow['branch_id'].'">
						        <button type="button" class="btn btn-default btn-sm">Active</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		}




		# DATA TABLE

		$data_table .= '<tr >
							<td >'.$i.'</td>
							<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
								<span style="font-size:11px;">'.$axRow['brand_name'].'</span>
							</td>
							<td >'.$axRow['name'].$default_status.'</td>
							<td style="text-align:center">'.$qr_code_image.'</td>
							<td >'.$axRow['email'].'</td>
							<td style="text-align:center">'.$status.'</td>
							<td style="text-align:center">'.DateTime($axRow['date_update']).'</td>';

		if ($_SESSION['role_action']['branch']['view'] == 1) {

			$data_table .=	'<td style="text-align:center">'.$view.'</td>';
		}

		if ($_SESSION['role_action']['branch']['delete'] == 1) {

			$data_table .= 	'<td style="text-align:center">'.$deleted.'</td>';
		}

		$data_table .=	'</tr>';

		$asData[] = $axRow;
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

$oTmp->assign('is_menu', 'is_branch');

$oTmp->assign('content_file', 'branch/branch.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
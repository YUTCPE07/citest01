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

if ($_SESSION['role_action']['stamp']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$path_upload_collection = $_SESSION['path_upload_collection'];

$time_insert = date("Y-m-d H:i:s");



$where_brand = '';

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND mi_brand.brand_id = "'.$_SESSION['user_brand_id'].'" 
					AND motivation_plan_stamp.mops_Deleted!="T"';
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
else {	$where_search = " AND mi_brand.brand_id IN (".$brand_id.")";	}



$sql = 'SELECT

		motivation_plan_stamp.*,
		mi_brand.name as brand_name,
		mi_brand.logo_image as brand_image,
		mi_brand.path_logo,
		coty_Name,
		coty_Image

		FROM motivation_plan_stamp

		LEFT JOIN collection_type
		ON coty_CollectionTypeID = mops_CollectionTypeID

		LEFT JOIN mi_brand
		ON brand_id = bran_BrandID

		WHERE 1

		'.$where_search.'
		'.$where_brand.' 

		ORDER BY CASE 
			WHEN motivation_plan_stamp.mops_Deleted = "" THEN 1
	        WHEN motivation_plan_stamp.mops_Deleted = "T" THEN 2 END ASC,
			motivation_plan_stamp.mops_Status DESC, 
			motivation_plan_stamp.mops_UpdatedDate DESC';


if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE motivation_plan_stamp 
	 					SET mops_Status='F',
	 						mops_PrivilegeType='None',
	 						mops_PrivilegeID='0',
	 						mops_UpdatedDate='".$time_insert."',
	 						mops_UpdatedBy='".$_SESSION['UID']."'
	 					WHERE mops_MotivationStampID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

	$do_sql_priv = "UPDATE privilege 
	 					SET priv_MotivationID='0',
	 						priv_Motivation='None',
	 						priv_UpdatedDate='".$time_insert."',
	 						priv_UpdatedBy='".$_SESSION['UID']."'
	 					WHERE priv_MotivationID='".$id."'
	 						AND priv_Motivation='Stamp'";

 	$oDB->QueryOne($do_sql_priv);

 	echo '<script>window.location.href="stamp.php";</script>';


} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE motivation_plan_stamp 
	 					SET mops_Status='T',
	 						mops_PrivilegeType='None',
	 						mops_PrivilegeID='0',
	 						mops_UpdatedDate='".$time_insert."',
	 						mops_UpdatedBy='".$_SESSION['UID']."'
	 					WHERE mops_MotivationStampID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

	$do_sql_priv = "UPDATE privilege 
	 					SET priv_MotivationID='0',
	 						priv_Motivation='None',
	 						priv_UpdatedDate='".$time_insert."',
	 						priv_UpdatedBy='".$_SESSION['UID']."'
	 					WHERE priv_MotivationID='".$id."'
	 						AND priv_Motivation='Stamp'";

 	$oDB->QueryOne($do_sql_priv);

 	echo '<script>window.location.href="stamp.php";</script>';


} else if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT mops_Deleted FROM motivation_plan_stamp WHERE mops_MotivationStampID ="'.$id.'"';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($axRow['mops_Deleted']=='') {


 		$do_sql_stamp = "UPDATE motivation_plan_stamp
 							SET mops_Deleted='T', 
 							mops_Status='Pending',
	 						mops_PrivilegeType='None',
	 						mops_PrivilegeID='0',
 							mops_UpdatedDate='".$time_insert."' 
 							WHERE mops_MotivationStampID='".$id."'";

 	} else if ($axRow['mops_Deleted']=='T') {

		$do_sql_stamp = "UPDATE motivation_plan_stamp
 							SET mops_Deleted='', 
 							mops_Status='Pending',
	 						mops_PrivilegeType='None',
	 						mops_PrivilegeID='0',
 							mops_UpdatedDate='".$time_insert."' 
 							WHERE mops_MotivationStampID='".$id."'";
	}

 	$oDB->QueryOne($do_sql_stamp);

	$do_sql_priv = "UPDATE privilege 
	 					SET priv_MotivationID='0',
	 						priv_Motivation='None',
	 						priv_UpdatedDate='".$time_insert."',
	 						priv_UpdatedBy='".$_SESSION['UID']."'
	 					WHERE priv_MotivationID='".$id."'
	 						AND priv_Motivation='Stamp'";

 	$oDB->QueryOne($do_sql_priv);

 	echo '<script>window.location.href="stamp.php";</script>';



} else if($Act=='xls'){

	require_once '../../lib/PHPExcel/Classes/PHPExcel.php';

	$objPHPExcel = new PHPExcel();

	$char = 'A';

	$chars = $char;

	$row_start = 1;

	$reportName = 'Motivation Stamp Report';

	$objPHPExcel->setActiveSheetIndex(0)

				// ->setCellValue('A1', 'Topics this report : '.$reportName )

				// ->setCellValue('A2', 'Issued this report  : '.$_SESSION['UNAME'].' / '.$_SESSION['FULLNAME'])

				// ->setCellValue('A3', 'Check out this report : '.$time_insert)

				// ->setCellValue($chars++.$row_start, 'No.')

				->setCellValue($chars++.$row_start, 'Brand')

				->setCellValue($chars++.$row_start, 'Name')

				->setCellValue($chars++.$row_start, 'Status')

				->setCellValue($chars++.$row_start, 'Stamp QTY')

				->setCellValue($chars++.$row_start, 'Type')

				->setCellValue($chars++.$row_start, 'Method')

				->setCellValue($chars++.$row_start, 'Maximum (Times/Day/Member)')

				->setCellValue($chars++.$row_start, 'Objective')

				->setCellValue($chars++.$row_start, 'Description')

				->setCellValue($chars++.$row_start, 'Multiple')

				->setCellValue($chars++.$row_start, 'Start Date (Multiple)')

				->setCellValue($chars++.$row_start, 'End Date (Multiple)')

				->setCellValue($chars++.$row_start, 'Date Update')

				->setCellValue($chars.$row_start, 'Delete');

	// $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:'.$chars.'1');

	// $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:'.$chars.'2');

	// $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A3:'.$chars.'3');

	// $i = 6;

	$row_start++;

	$oRes = $oDB->Query($sql);

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)) {

		if($axRow['mops_Deleted']=='') {	$axRow['mops_Deleted']="No";	}
		else if ($axRow['mops_Deleted']=='T') {	$axRow['mops_Deleted']="Yes";	}

		if($axRow['mops_Status']=='T') {	$axRow['mops_Status']="Active";	}
		else if ($axRow['mops_Status']=='F') {	$axRow['mops_Status']="Pending";	}

		if ($axRow['mops_MaxStampPerDay']==0) { $axRow['mops_MaxStampPerDay'] = 'Unlimited'; } 
		else {	$axRow['mops_MaxStampPerDay'] = $axRow['mops_MaxStampPerDay']; }

		if ($axRow['mops_CollectionMethod']=='No') { $axRow['mops_CollectionMethod'] = 'No Expiry';

		} else if ($axRow['mops_CollectionMethod']=='Exp') {

			if ($axRow['mops_PeriodType']=='Y') {  $axRow['mops_PeriodType'] = 'Years';	}

			if ($axRow['mops_PeriodType']=='M') {  $axRow['mops_PeriodType'] = 'Months';	}

			if ($axRow['mops_PeriodTypeEnd']=='Y') {  $axRow['mops_PeriodTypeEnd'] = 'End of Year';	}

			if ($axRow['mops_PeriodTypeEnd']=='M') {  $axRow['mops_PeriodTypeEnd'] = 'End of Month';	}

			$axRow['mops_CollectionMethod'] = $axRow['mops_PeriodTime'].' '.$axRow['mops_PeriodType'].' ('.$axRow['mops_PeriodTypeEnd'].')';

		} else if ($axRow['mops_CollectionMethod']=='Fix') { $axRow['mops_CollectionMethod'] = DateOnly($axRow['mops_EndDate']); }

		if ($axRow['mops_Multiple']==0) { $axRow['mops_Multiple'] = ''; }

		if ($axRow['mops_MultipleStartDate']=='0000-00-00') { $axRow['mops_MultipleStartDate'] = ''; }
		else { $axRow['mops_MultipleStartDate'] = DateOnly($axRow['mops_MultipleStartDate']); }

		if ($axRow['mops_MultipleEndDate']=='0000-00-00') { $axRow['mops_MultipleEndDate'] = '';	}
		else { $axRow['mops_MultipleEndDate'] = DateOnly($axRow['mops_MultipleEndDate']); }

		// $chars = $char;

		// $objPHPExcel->getActiveSheet()->setCellValue($chars++ . $i, ($i-5));

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['brand_name']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['mops_Name']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['mops_Status']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['mops_StampQty']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['mops_Method']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['mops_CollectionMethod']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['mops_MaxStampPerDay']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['mops_Objective']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['mops_Description']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['mops_Multiple']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, DateOnly($axRow['mops_MultipleStartDate']));

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, DateOnly($axRow['mops_MultipleEndDate']));

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, DateTime($axRow['mops_UpdatedDate']));

		$objPHPExcel->getActiveSheet()->setCellValue($chars . $row_start, $axRow['mops_Deleted']);

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


} else {

	$oRes = $oDB->Query($sql);

	$z=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$z++;


		# STATUS

		$status = '';

		if($axRow['mops_Deleted']=='T'){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

		} else {

			if($axRow['mops_Status']=='T'){

				if ($_SESSION['role_action']['stamp']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'stamp.php?act=active&id='.$axRow['mops_MotivationStampID'].'\'">
		                    <option class="status_default" value="'.$axRow['mops_MotivationStampID'].'" selected>On</option>
		                    <option class="status_default">Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';	
		        }

			} else {

				if ($_SESSION['role_action']['stamp']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_pending" name="pending_status" onchange="window.location.href=\'stamp.php?act=pending&id='.$axRow['mops_MotivationStampID'].'\'">
		                    <option class="status_default">On</option>
		                    <option class="status_default" value="'.$axRow['mops_MotivationStampID'].'" selected>Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';	
		        }
			}
		}


		# LOGO

		if($axRow['brand_image']!=''){

			$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_image'].'" class="image_border" width="60" height="60"/>';

			$logo_view = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_image'].'" class="image_border" width="150" height="150"/>';

		} else {

			$logo_brand = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';

			$logo_view = '<img src="../../images/400x400.png" class="image_border" width="150" height="150"/>';
		}


		# COLLECTION IMAGE

		$coll_image = '<img src="'.$path_upload_collection.$axRow['coty_Image'].'" width="30" height="30"/>';



		# DELETED

		if($axRow['mops_Deleted']=='') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['mops_MotivationStampID'].'"><span class="glyphicon glyphicon-eye-open" aria-idden="true"></span> Active</button>
				<div class="modal fade" id="Deleted'.$axRow['mops_MotivationStampID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center><span style="font-size:12px;padding-left:10px;">
							        <b>"'.$axRow['mops_Name'].'"</b><br>
								    By clicking the <b>"Inactive"</b> button to:<br>
								    Inactive this Motivation Stamp<br>
						        </span></center>
						    </div>
						    <div class="modal-footer">
						    	<a href="stamp.php?act=delete&id='.$axRow['mops_MotivationStampID'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		} else if ($axRow['mops_Deleted']=='T') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['mops_MotivationStampID'].'"><span class="glyphicon glyphicon-eye-close" aria-idden="true"></span> Inactive</button>
				<div class="modal fade" id="Deleted'.$axRow['mops_MotivationStampID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center><span style="font-size:12px;padding-left:10px;">
							        <b>"'.$axRow['mops_Name'].'"</b><br>
								    By clicking the <b>"Active"</b> button to:<br>
								    Active this Motivation Stamp<br>
						        </span></center>
						    </div>
						    <div class="modal-footer">
						    	<a href="stamp.php?act=delete&id='.$axRow['mops_MotivationStampID'].'">
						        <button type="button" class="btn btn-default btn-sm">Active</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		}



		# MAX Stamp PER DAY

		if ($axRow['mops_MaxStampPerDay']==0) { $axRow['mops_MaxStampPerDay'] = 'Unlimited'; } 
		else {	$axRow['mops_MaxStampPerDay'] = $axRow['mops_MaxStampPerDay'].' Times / Day / Member'; }




		# METHOD

		if ($axRow['mops_CollectionMethod']=='No') {

			$axRow['mops_CollectionMethod'] = 'No Expiry';

		} else if ($axRow['mops_CollectionMethod']=='Exp') {

			if ($axRow['mops_PeriodType']=='Y') {  $axRow['mops_PeriodType'] = 'Years';	}

			if ($axRow['mops_PeriodType']=='M') {  $axRow['mops_PeriodType'] = 'Months';	}

			if ($axRow['mops_PeriodTypeEnd']=='Y') {  $axRow['mops_PeriodTypeEnd'] = 'End of Year';	}

			if ($axRow['mops_PeriodTypeEnd']=='M') {  $axRow['mops_PeriodTypeEnd'] = 'End of Month';	}

			$axRow['mops_CollectionMethod'] = $axRow['mops_PeriodTime'].' '.$axRow['mops_PeriodType'].' ('.$axRow['mops_PeriodTypeEnd'].')';

		} else if ($axRow['mops_CollectionMethod']=='Fix') {

			$axRow['mops_CollectionMethod'] = DateOnly($axRow['mops_EndDate']);
		}



		# MULTIPLE

		if ($axRow['mops_Multiple']==0) {	$axRow['mops_Multiple'] = '-';	}

		if ($axRow['mops_MultipleStartDate']=='0000-00-00') {	$axRow['mops_MultipleStartDate'] = '-';	}
		else { $axRow['mops_MultipleStartDate'] = DateOnly($axRow['mops_MultipleStartDate']); }

		if ($axRow['mops_MultipleEndDate']=='0000-00-00') {	$axRow['mops_MultipleEndDate'] = '-';	}
		else { $axRow['mops_MultipleEndDate'] = DateOnly($axRow['mops_MultipleEndDate']); }



		# PRIVILEGE

		$sql_priv = 'SELECT priv_Name AS privilege_name,
							"Privilege" AS type,
							priv_Image AS privilege_image,
							priv_ImagePath AS privilege_path,
							priv_Description AS privilege_description
						FROM privilege
						WHERE priv_Motivation="Stamp"
						AND priv_MotivationID='.$axRow['mops_MotivationStampID'].'

					UNION

					SELECT coup_Name AS privilege_name,
							"Coupon" AS type,
							coup_Image AS privilege_image,
							coup_ImagePath AS privilege_path,
							coup_Description AS privilege_description
						FROM coupon
						WHERE coup_Motivation="Stamp"
						AND coup_Birthday=""
						AND coup_MotivationID='.$axRow['mops_MotivationStampID'].'

					UNION

					SELECT coup_Name AS privilege_name,
							"Birthday Coupon" AS type,
							coup_Image AS privilege_image,
							coup_ImagePath AS privilege_path,
							coup_Description AS privilege_description
						FROM coupon
						WHERE coup_Motivation="Stamp"
						AND coup_Birthday="T"
						AND coup_MotivationID='.$axRow['mops_MotivationStampID'].'

					UNION

					SELECT acti_Name AS privilege_name,
							"Activity" AS type,
							acti_Image AS privilege_image,
							acti_ImagePath AS privilege_path,
							acti_Description AS privilege_description
						FROM activity
						WHERE acti_Motivation="Stamp"
						AND acti_MotivationID='.$axRow['mops_MotivationStampID'].'';

		$oRes_priv = $oDB->Query($sql_priv);

		$data_privilege = '';

		while ($axRow_priv = $oRes_priv->FetchRow(DBI_ASSOC)){

			if ($axRow_priv['privilege_description']=="") { $axRow_priv['privilege_description'] = '-'; }
			else { $axRow_priv['privilege_description'] = nl2br($axRow_priv['privilege_description']); }

			$data_privilege .= '<tr>
									<td width="120px" style="text-align:center"><img src="../../upload/'.$axRow_priv['privilege_path'].$axRow_priv['privilege_image'].'" width="100px" class="image_border"></td>
									<td width="70px" style="text-align:right">Name<br>Type<br>Description</td>
									<td width="20px" style="text-align:center">:<br>:<br>:</td>
									<td>'.$axRow_priv['privilege_name'].'<br>'.$axRow_priv['type'].'<br>'.$axRow_priv['privilege_description'].'</td>
								</tr>';
		}

		if ($data_privilege == '') {

			$data_privilege .= '<tr>
									<td colspan="4" style="text-align:center">No Privilege Data</td>
								</tr>';
		}



		# VIEW

			# DATA

			if ($axRow['mops_Objective']=='') {	$axRow['mops_Objective'] = '-';	}

			if ($axRow['mops_Description']=='') {	$axRow['mops_Description'] = '-';	}

		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['mops_MotivationStampID'].'"><span class="glyphicon glyphicon-eye-open" aria-idden="true"></span></button>
				<div class="modal fade" id="View'.$axRow['mops_MotivationStampID'].'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
					<div class="modal-dialog" role="document" style="width:55%">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>'.$axRow['mops_Name'].'</b></span>
						        <hr>
						        <center>
						        	<table width="70%" class="myPopup">
						        		<tr>
						        			<td width="30%" style="text-align:right" rowspan="8">'.$logo_view.'</td>
						        			<td></td>
						        			<td width="5%"></td>
						        			<td width="30%"></td>
						        		</tr>
						        		<tr><td></td><td>&nbsp;</td><td></td></tr>
						        		<tr>
						        			<td style="text-align:right" valign="center">Collection</td>
						        			<td style="text-align:center" valign="center">:</td>
						        			<td valign="top">'.$coll_image.'</td>
						        		</tr>
						        		<tr>
						        			<td style="text-align:right">Quantity</td>
						        			<td style="text-align:center">:</td>
						        			<td>'.$axRow['mops_StampQty'].' Stamp Qty</td>
						        		</tr>
						        		<tr><td></td><td>&nbsp;</td><td></td></tr>
						        		<tr><td></td><td>&nbsp;</td><td></td></tr>
						        		<tr><td></td><td>&nbsp;</td><td></td></tr>
						        	</table><br>
						        	<span style="font-size:12px">
					                <ul id="tapMenu" class="nav nav-tabs">
					                    <li class="active">
					                    	<a data-toggle="tab" href="#time'.$axRow['mops_MotivationStampID'].'">
					                    	<center><b>Expiry</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#limitation'.$axRow['mops_MotivationStampID'].'">
					                    	<center><b>Limitation</b></center></a>
					                   	</li>
					                    <li>
					                    	<a data-toggle="tab" href="#note'.$axRow['mops_MotivationStampID'].'">
					                    	<center><b>Note</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#multiple'.$axRow['mops_MotivationStampID'].'">
					                    	<center><b>Multiple</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#privilege'.$axRow['mops_MotivationStampID'].'">
					                    	<center><b>Privilege</b></center></a>
					                    </li>
					                </ul>
					                </span>
					                <div class="tab-content">
					                    <div id="time'.$axRow['mops_MotivationStampID'].'" class="tab-pane active"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Expiry</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['mops_CollectionMethod'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="limitation'.$axRow['mops_MotivationStampID'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Maximum</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['mops_MaxStampPerDay'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="note'.$axRow['mops_MotivationStampID'].'" class="tab-pane"><br>
					                    	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" valign="top" width="45%">Objective</td>
								        			<td style="text-align:center" valign="top" width="5%">:</td>
								        			<td>'.$axRow['mops_Objective'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top" width="45%">Description</td>
								        			<td style="text-align:center" valign="top" width="5%">:</td>
								        			<td>'.$axRow['mops_Description'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="multiple'.$axRow['mops_MotivationStampID'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Multiple</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['mops_Multiple'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Start Date</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['mops_MultipleStartDate'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">End Date</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['mops_MultipleEndDate'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="privilege'.$axRow['mops_MotivationStampID'].'" class="tab-pane"><br>
								        	<table style="width:80%" class="table table-striped table-bordered myPopup">
								        		<thead>
								        		<tr class="th_table">
								        			<th>Privilege</th>
								        			<th colspan="3">Detail</th>
								        		</tr>
								        		</thead>
								        		<tbody>
								        			'.$data_privilege.'
								        		</tbody>
								        	</table>
					                    </div>
					                </div>
						        </center>
						    </div>
						    <div class="modal-footer">';

			if ($_SESSION['role_action']['stamp']['edit'] == 1) {	

				if ($axRow['mops_Status']=='F') {

					$view .= '  <a href="stamp_create.php?act=edit&id='.$axRow['mops_MotivationStampID'].'">
						        <button type="button" class="btn btn-default btn-sm">Edit</button></a>';
				} else {

					$view .= '  <a href="stamp_edit.php?act=edit&id='.$axRow['mops_MotivationStampID'].'">
						        <button type="button" class="btn btn-default btn-sm">Edit</button></a>';
				}
			}

				$view .= '      <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';




		# DATA TABLE

		$data_table .= '<tr >
							<td >'.$z.'</td>
							<td style="text-align:center">'.$logo_brand.'<br>
								<span style="font-size:11px;">'.$axRow['brand_name'].'</span>
							</td>
							<td>'.$axRow['mops_Name'].'</td>
							<td style="text-align:center">'.$axRow['mops_StampQty'].'</td>
							<td style="text-align:center">'.$coll_image.'<br>
								<span style="font-size:11px;">'.$axRow['coty_Name'].'</td>
							<td >'.$axRow['mops_CollectionMethod'].'</td>
							<td style="text-align:center">'.$status.'</td>
							<td >'.DateTime($axRow['mops_UpdatedDate']).'</td>';

		if ($_SESSION['role_action']['stamp']['view'] == 1) {

			$data_table .=	'<td style="text-align:center">'.$view.'</td>';
		}

		if ($_SESSION['role_action']['stamp']['delete'] == 1) {

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

$oTmp->assign('is_menu', 'is_stamp');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_motivation', 'in');

$oTmp->assign('inner_motivation', 'in');

$oTmp->assign('content_file', 'motivation/stamp.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
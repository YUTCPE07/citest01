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

if ($_SESSION['role_action']['activity']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$approve = $_REQUEST['approve'];



$where_brand = '';

if ($_SESSION['user_type_id_ses']>1 ) {

	$where_brand = ' AND activity.bran_BrandID = "'.$_SESSION['user_brand_id'].'" AND activity.acti_Deleted=""';
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

		activity.*,
		mi_brand.name AS brand_name,
		mi_brand.logo_image AS brand_logo,
		mi_brand.path_logo,
		mi_privilege_type.name AS privilege_type_name

		FROM activity

		INNER JOIN mi_brand
		ON mi_brand.brand_id = activity.bran_BrandID

		LEFT JOIN mi_privilege_type
		ON activity.prty_PrivilegeTypeID = mi_privilege_type.privilege_type_id

		WHERE 1

		'.$where_search.'
		'.$where_brand.' 

		ORDER BY CASE 
			WHEN activity.acti_Deleted = "" THEN 1
	        WHEN activity.acti_Deleted = "T" THEN 2 END ASC,
			activity.acti_Status ASC, 
			activity.acti_UpdatedDate DESC';


if($Act == 'approve' && $id != '') {

	# APPROVE IMAGE

	$sql = '';
	$sql .= 'SELECT acti_ImageNew, acti_Image FROM activity WHERE acti_ActivityID ="'.$id.'"';
	$oRes = $oDB->Query($sql);
	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($approve == 'unapprove') {

 		# UNAPPROVE

		// if ($axRow['acti_ImageNew']!="") {

		// 	unlink_file($oDB,'activity','acti_ImageNew','acti_ActivityID',$id,$path_upload_activity,$axRow['acti_ImageNew']);

		// 	$do_sql_upload = "UPDATE activity 

		// 						SET acti_ImageNew='',

		// 						acti_Status='Pending',

		// 						acti_UpdatedDate='".$time_insert."' 

		// 						WHERE acti_ActivityID='".$id."' ";

		// } else if ($axRow['acti_Image']!=""){

		// 	unlink_file($oDB,'activity','acti_Image','acti_ActivityID',$id,$path_upload_activity,$axRow['acti_Image']);

		// 	$do_sql_upload = "UPDATE activity 

		// 						SET acti_Image='',

		// 						acti_Status='Pending',

		// 						acti_UpdatedDate='".$time_insert."'  

		// 						WHERE acti_ActivityID='".$id."' ";

		// }

			
		$do_sql_upload = "UPDATE activity 
							SET acti_Image='',
							acti_UpdatedDate='".$time_insert."',
							acti_UpdatedBy='".$_SESSION['UID']."'
							WHERE acti_ActivityID='".$id."' ";

 		$oDB->QueryOne($do_sql_upload);
 	}

	if ($approve == 'approve') {

		# APPROVE

		// if ($axRow['acti_Image']!="") {

		// 	unlink_file($oDB,'activity','acti_Image','acti_ActivityID',$id,$path_upload_activity,$axRow['acti_Image']);

		// 	$do_sql_upload = "UPDATE activity 

		// 						SET acti_Image='".$axRow['acti_ImageNew']."', 

		// 						acti_ImageNew='',

		// 						acti_UpdatedDate='".$time_insert."' 

		// 						WHERE acti_ActivityID='".$id."'";

		// } else {

		// 	$do_sql_upload = "UPDATE activity 

		// 						SET acti_Image='".$axRow['acti_ImageNew']."', 

		// 						acti_ImageNew='',

		// 						acti_Status='Pending',

		// 						acti_UpdatedDate='".$time_insert."' 

		// 						WHERE acti_ActivityID='".$id."'";

		// }

			
		$do_sql_upload = "UPDATE activity 
							SET acti_Approve='T',
							acti_UpdatedDate='".$time_insert."',
							acti_UpdatedBy='".$_SESSION['UID']."'
							WHERE acti_ActivityID='".$id."' ";

 		$oDB->QueryOne($do_sql_upload);
	}

	echo '<script> window.location.href="activity.php"; </script>';


} else if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE activity 
	 					SET acti_Status='Pending',
	 						acti_UpdatedDate='".$time_insert."' 
	 					WHERE acti_ActivityID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="activity.php";</script>';

} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE activity 
	 					SET acti_Status='Active',
	 						acti_UpdatedDate='".$time_insert."' 
	 					WHERE acti_ActivityID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="activity.php";</script>';

} else if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT acti_Deleted FROM activity WHERE acti_ActivityID ="'.$id.'"';
	$oRes = $oDB->Query($sql);
	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($axRow['acti_Deleted']=='') {

 		$do_sql_acti = "UPDATE activity
 							SET acti_Deleted='T', 
 							acti_Status='Pending',
 							acti_Motivation='None',
 							acti_MotivationID=0,
 							acti_UpdatedDate='".$time_insert."' 
 							WHERE acti_ActivityID='".$id."'";

 		$do_sql_regis = "UPDATE mi_card_register
 							SET status='1', 
 							date_update='".$time_insert."' 
 							WHERE activity_id='".$id."'";

 		$do_sql_point = "UPDATE motivation_plan_point
 							SET mopp_PrivilegeType='None', 
 							mopp_PrivilegeID='0' 
 							WHERE mopp_PrivilegeID='".$id."'
 							AND mopp_PrivilegeType='Activity'";

 		$do_sql_stamp = "UPDATE motivation_plan_stamp
 							SET mops_PrivilegeType='None', 
 							mops_PrivilegeID='0' 
 							WHERE mops_PrivilegeID='".$id."'
 							AND mops_PrivilegeType='Activity'";

 	} else if ($axRow['acti_Deleted']=='T') {

		$do_sql_acti = "UPDATE activity
 							SET acti_Deleted='', 
 							acti_Status='Pending',
 							acti_UpdatedDate='".$time_insert."' 
 							WHERE acti_ActivityID='".$id."'";

 		$do_sql_regis = "";
 		$do_sql_point = "";
 		$do_sql_stamp = "";
	}


 	$oDB->QueryOne($do_sql_acti);
 	$oDB->QueryOne($do_sql_regis);
 	$oDB->QueryOne($do_sql_point);
 	$oDB->QueryOne($do_sql_stamp);

 	echo '<script>window.location.href="activity.php";</script>';


} else if($Act=='xls'){

	require_once '../../lib/PHPExcel/Classes/PHPExcel.php';

	$objPHPExcel = new PHPExcel();

	$char = 'A';

	$chars = $char;

	$row_start = 1;

	$reportName = 'Activity Report';

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
					->setCellValue($chars++.$row_start, 'Activity')
					->setCellValue($chars++.$row_start, 'Start Date')
					->setCellValue($chars++.$row_start, 'End Date')
					->setCellValue($chars++.$row_start, 'Start Time')
					->setCellValue($chars++.$row_start, 'End Time')
					->setCellValue($chars++.$row_start, 'Description')
					->setCellValue($chars++.$row_start, 'Type')
					->setCellValue($chars++.$row_start, 'Repetition Use')
					->setCellValue($chars++.$row_start, 'Activity QTY')
					->setCellValue($chars++.$row_start, 'Transfer To Other Member')
					->setCellValue($chars++.$row_start, 'Special Period Type')
					->setCellValue($chars++.$row_start, 'Repetition Use (Member)')
					->setCellValue($chars++.$row_start, 'Activity QTY (Member)')
					->setCellValue($chars++.$row_start, 'Reservation')
					->setCellValue($chars++.$row_start, 'Start Date (Reservation)')
					->setCellValue($chars++.$row_start, 'End Date (Reservation)')
					->setCellValue($chars++.$row_start, 'Start Time (Reservation)')
					->setCellValue($chars++.$row_start, 'End Time (Reservation)')
					->setCellValue($chars++.$row_start, 'Track Like')
					->setCellValue($chars++.$row_start, 'Track Review')
					->setCellValue($chars++.$row_start, 'Track Request')
					->setCellValue($chars++.$row_start, 'Track Share')
					->setCellValue($chars++.$row_start, 'Map Latitude')
					->setCellValue($chars++.$row_start, 'Map Longitude')
					->setCellValue($chars++.$row_start, 'Location Detail')
					->setCellValue($chars++.$row_start, 'Financial Start Date')
					->setCellValue($chars++.$row_start, 'Financial End Date')
					->setCellValue($chars++.$row_start, 'Price')
					->setCellValue($chars++.$row_start, 'Cost')
					->setCellValue($chars++.$row_start, 'Payment')
					->setCellValue($chars++.$row_start, 'Condition')
					->setCellValue($chars++.$row_start, 'Exception')
					->setCellValue($chars++.$row_start, 'How To Use')
					->setCellValue($chars++.$row_start, 'Note')
					->setCellValue($chars++.$row_start, 'Product Category')
					->setCellValue($chars++.$row_start, 'Product')
					->setCellValue($chars++.$row_start, 'Status')
					->setCellValue($chars++.$row_start, 'Update Date')
					->setCellValue($chars.$row_start, 'Delete');

	$row_start++;

	$oRes = $oDB->Query($sql);

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)) {


		if($axRow['acti_Deleted']=='') {	$axRow['acti_Deleted']="No";	}
		else if ($axRow['acti_Deleted']=='T') {	$axRow['acti_Deleted']="Yes";	}

		if ($axRow['acti_QtyPer'] == 'Not') { $axRow['acti_Repetition'] = 'Not Specific'; }

		if ($axRow['acti_Repetition'] == 'T') { 
			$axRow['acti_Repetition'] = 'Yes'; 
			$acti_Repetition = $axRow['acti_Qty'].' Per '.$axRow['acti_QtyPer'];
		} else { 
			$axRow['acti_Repetition'] = 'No'; 
			$acti_Repetition = '';
		}

		if ($axRow['acti_Transfer'] == '') { $axRow['acti_Transfer'] = 'No'; } 
		else {	$axRow['acti_Transfer'] = 'Yes';	}

		if ($axRow['acti_RepetitionMember'] == 'T') { 
			$axRow['acti_RepetitionMember'] = 'Yes'; 
			$acti_RepetitionMember = $axRow['acti_Qty'].' Per '.$axRow['acti_QtyPer'];
		} else { 
			$axRow['acti_RepetitionMember'] = 'No'; 
			$acti_RepetitionMember = '';
		}

		if ($axRow['prca_ProductCategoryID'] == 0) { $category_name = ''; }
		else {
			$category = 'SELECT name FROM mi_products_category WHERE category_id="'.$axRow['prca_ProductCategoryID'].'"';
			$category_name = $oDB->QueryOne($category);
		}

		if ($axRow['prod_ProductID'] == 0) { $product_name = ''; }
		else {
			$product_name = 'SELECT name FROM mi_products WHERE products_id="'.$axRow['prod_ProductID'].'"';
			$product_name = $oDB->QueryOne($product_name);
		}

		if ($axRow['acti_Reservation'] == 'T') { 
			$axRow['acti_Reservation'] = 'Yes';  
			$axRow['acti_StartDateReservation'] = DateOnly($axRow['acti_StartDateReservation']);
			$axRow['acti_EndDateReservation'] = DateOnly($axRow['acti_EndDateReservation']);
			$axRow['acti_StartTimeReservation'] = TimeOnly($axRow['acti_StartTimeReservation']);
			$axRow['acti_EndTimeReservation'] = TimeOnly($axRow['acti_EndTimeReservation']);
		} else {
			$axRow['acti_Reservation'] = 'No';  
			$axRow['acti_StartDateReservation'] = '';
			$axRow['acti_EndDateReservation'] = '';
			$axRow['acti_StartTimeReservation'] = '';
			$axRow['acti_EndTimeReservation'] = '';
		}

		if ($axRow['acti_StartDateSell'] == '0000-00-00') { $axRow['acti_StartDateSell'] = '';	}
		else { $axRow['acti_StartDateSell'] = DateOnly($axRow['acti_StartDateSell']); }

		if ($axRow['acti_EndDateSell'] == '0000-00-00') { $axRow['acti_EndDateSell'] = '';	}
		else { $axRow['acti_EndDateSell'] = DateOnly($axRow['acti_EndDateSell']); }

		if ($axRow['acti_Payment'] == 'F') { $axRow['acti_Payment'] = 'No'; } 
		else { $axRow['acti_Payment'] = 'Yes';	}

		if ($axRow['acti_TrackLike'] == 'T') { $axRow['acti_TrackLike'] = 'Yes'; } 
		else {	$axRow['acti_TrackLike'] = 'No';	}

		if ($axRow['acti_TrackReview'] == 'T') { $axRow['acti_TrackReview'] = 'Yes'; } 
		else {	$axRow['acti_TrackReview'] = 'No';	}

		if ($axRow['acti_TrackRequest'] == 'T') { $axRow['acti_TrackRequest'] = 'Yes'; } 
		else {	$axRow['acti_TrackRequest'] = 'No';	}

		if ($axRow['acti_TrackShare'] == 'T') { $axRow['acti_TrackShare'] = 'Yes'; } 
		else {	$axRow['acti_TrackShare'] = 'No';	}


		$chars = $char;

		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['brand_name']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_Name']);
		$objWorkSheet->setCellValue($chars++.$row_start, DateOnly($axRow['acti_StartDate']));
		$objWorkSheet->setCellValue($chars++.$row_start, DateOnly($axRow['acti_EndDate']));
		$objWorkSheet->setCellValue($chars++.$row_start, TimeOnly($axRow['acti_StartTime']));
		$objWorkSheet->setCellValue($chars++.$row_start, TimeOnly($axRow['acti_EndTime']));
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_Description']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['privilege_type_name']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_Repetition']);
		$objWorkSheet->setCellValue($chars++.$row_start, $acti_Repetition);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_Transfer']);
		$objWorkSheet->setCellValue($chars++.$row_start, $special_period_type);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_RepetitionMember']);
		$objWorkSheet->setCellValue($chars++.$row_start, $acti_RepetitionMember);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_Reservation']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_StartDateReservation']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_EndDateReservation']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_StartTimeReservation']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_EndTimeReservation']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_TrackLike']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_TrackReview']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_TrackRequest']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_TrackShare']);
		$objWorkSheet->setCellValueExplicit($chars++.$row_start, $axRow['acti_Latitude'], PHPExcel_Cell_DataType::TYPE_STRING);
		$objWorkSheet->setCellValueExplicit($chars++.$row_start, $axRow['acti_Longitude'], PHPExcel_Cell_DataType::TYPE_STRING);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_Location']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_StartDateSell']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_EndDateSell']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_Price']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_Cost']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_Payment']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_Condition']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_Exception']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_HowToUse']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_Note']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['category_name']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['product_name']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['acti_Status']);
		$objWorkSheet->setCellValue($chars++.$row_start, DateTime($axRow['acti_UpdatedDate']));
		$objWorkSheet->setCellValue($chars.$row_start, $axRow['acti_Deleted']);

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

	$i=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;


		# APPROVE

		if ($axRow['acti_Approve']=='' && $axRow['acti_Image']=='') {

			$image_status = '<span class="glyphicon glyphicon-exclamation-sign" style="color:#f0ad4e;font-size:20px"></span>';

			$button_approve = '';
            $button_unapprove = '';

		} else if ($axRow['acti_Approve']=='T' && $axRow['acti_Image']) {

			$image_status = '';
			$button_approve = '';

            $button_unapprove = '<a style="cursor:pointer" href="activity.php?act=approve&approve=unapprove&id='.$axRow['acti_ActivityID'].'">

                                <button type="button" class="btn btn-default btn-sm">

                                <span class="glyphicon glyphicon-remove-sign" aria-hidden="true" style="color:red"></span> Unapprove</button></a>';
		} else {

			$image_status = '';

			$button_approve = '<a style="cursor:pointer" href="activity.php?act=approve&approve=approve&id='.$axRow['acti_ActivityID'].'">
                                <button type="button" class="btn btn-default btn-sm">
                                <span class="glyphicon glyphicon-ok-sign" aria-hidden="true" style="color:green"></span> Approve</button></a>';

            $button_unapprove = '<a style="cursor:pointer" href="activity.php?act=approve&approve=unapprove&id='.$axRow['acti_ActivityID'].'">
                                <button type="button" class="btn btn-default btn-sm">
                                <span class="glyphicon glyphicon-remove-sign" aria-hidden="true" style="color:red"></span> Unapprove</button></a>';
		}


		# STATUS

		$status = '';

		if($axRow['acti_Deleted']=='T'){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

		} else {

			if($axRow['acti_Status']=='Active'){

				if ($_SESSION['role_action']['activity']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
									<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'activity.php?act=active&id='.$axRow['acti_ActivityID'].'\'">
					                    <option class="status_default" value="'.$axRow['acti_ActivityID'].'" selected>On</option>
					                    <option class="status_default">Off</option>
					                </select>
					            </form>';
		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';
		        }

			} else {

				if ($_SESSION['role_action']['activity']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
									<select class="form-control text-md status_pending" name="pending_status" onchange="window.location.href=\'activity.php?act=pending&id='.$axRow['acti_ActivityID'].'\'">
					                    <option class="status_default">On</option>
					                    <option class="status_default" value="'.$axRow['acti_ActivityID'].'" selected>Off</option>
					                </select>
					            </form>';
		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
		        }
			}
		}


		# LOGO

		if($axRow['brand_logo']!=''){

			$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" width="60" height="60"/>';

			$logo_view = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" width="150" height="150"/>';

		} else {

			$logo_brand = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';

			$logo_view = '<img src="../../images/400x400.png" class="image_border" width="150" height="150"/>';
		}


		# PRIVILEGE IMAGE

		if($axRow['acti_ImageNew']!=''){

			$acti_image = '<img src="../../upload/'.$axRow['acti_ImagePath'].$axRow['acti_ImageNew'].'" class="image_border" width="128" height="80"/>';

			$acti_view = '<img src="../../upload/'.$axRow['acti_ImagePath'].$axRow['acti_ImageNew'].'" class="image_border" width="136" height="85"/>';

			$acti_data = '<img src="../../upload/'.$axRow['acti_ImagePath'].$axRow['acti_ImageNew'].'" class="image_border" width="240" height="150"/>';

		} else {

			if($axRow['acti_Image']!=''){

				$acti_image = '<img src="../../upload/'.$axRow['acti_ImagePath'].$axRow['acti_Image'].'" class="image_border" width="128" height="80"/>';

				$acti_view = '<img src="../../upload/'.$axRow['acti_ImagePath'].$axRow['acti_Image'].'" class="image_border" width="136" height="85"/>';

				$acti_data = '<img src="../../upload/'.$axRow['acti_ImagePath'].$axRow['acti_Image'].'" class="image_border" width="240" height="150"/>';

			} else {

				$acti_image = '<img src="../../images/card_privilege.jpg" width="128" height="80"/>';

				$acti_view = '<img src="../../images/card_privilege.jpg" width="136" height="85"/>';

				$acti_data = '<img src="../../images/card_privilege.jpg" width="240" height="150"/>';
			}
		}


		# DATE - TIME

		$date = DateOnly($axRow['acti_StartDate']).' - '.DateOnly($axRow['acti_EndDate']);

		$time = TimeOnly($axRow['acti_StartTime']).' - '.TimeOnly($axRow['acti_EndTime']);


		# VIEW

			# DATA

			if ($axRow['acti_QtyPer'] == 'Not') { $axRow['acti_QtyPer'] = 'Not Specific'; }

			if ($axRow['acti_QtyPerMember'] == 'Not') { $axRow['acti_QtyPerMember'] = 'Not Specific'; }

			if ($axRow['acti_Repetition'] == '') { $axRow['acti_Repetition'] = 

				'<tr>
					<td style="text-align:right" width="45%">Repetition Use</td>
					<td style="text-align:center" width="5%">:</td>
					<td> - </td>
				</tr>';	

			} else { $axRow['acti_Repetition'] = 

				'<tr>
					<td style="text-align:right" width="45%">Repetition</td>
					<td style="text-align:center" width="5%">:</td>
					<td><span class="glyphicon glyphicon-check"></span> Use</td>
				</tr>
				<tr>
					<td style="text-align:right">Activity QTY</td>
					<td style="text-align:center">:</td>
					<td>'.$axRow['acti_Qty'].' Per '.$axRow['acti_QtyPer'].'</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td>'.$axRow['acti_QtyPerData'].'</td>
				</tr>';	
			}

			if ($axRow['acti_RepetitionMeber'] == '') { $axRow['acti_RepetitionMeber'] = 

				'<tr>
					<td style="text-align:right" width="45%">Repetition Use</td>
					<td style="text-align:center" width="5%">:</td>
					<td> - </td>
				</tr>';	

			} else { $axRow['acti_RepetitionMeber'] = 

				'<tr>
					<td style="text-align:right" width="45%">Repetition Use</td>
					<td style="text-align:center" width="5%">:</td>
					<td><span class="glyphicon glyphicon-check"></span> Use</td>
				</tr>
				<tr>
					<td style="text-align:right">Activity QTY</td>
					<td style="text-align:center">:</td>
					<td>'.$axRow['acti_QtyMember'].' Per '.$axRow['acti_QtyPerMember'].'</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td>'.$axRow['acti_QtyPerMemberData'].'</td>
				</tr>';	
			}

			if ($axRow['acti_Reservation'] == '') { $axRow['acti_Reservation'] = 

				'<tr>
					<td style="text-align:right" width="45%">Reservation</td>
					<td style="text-align:center" width="5%">:</td>
					<td> - </td>
				</tr>';	

			} else { $axRow['acti_Reservation'] = 

				'<tr>
					<td style="text-align:right" width="45%">Reservation</td>
					<td style="text-align:center" width="5%">:</td>
					<td><span class="glyphicon glyphicon-check"></span></td>
				</tr>
				<tr>
					<td style="text-align:right">Start Date</td>
					<td style="text-align:center">:</td>
					<td>'.$axRow['acti_StartDateReservation'].'</td>
				</tr>
				<tr>
					<td style="text-align:right">End Date</td>
					<td style="text-align:center">:</td>
					<td>'.$axRow['acti_EndDateReservation'].'</td>
				</tr>
				<tr>
					<td style="text-align:right">Start Time</td>
					<td style="text-align:center">:</td>
					<td>'.$axRow['acti_StartTimeReservation'].'</td>
				</tr>
				<tr>
					<td style="text-align:right">End Time</td>
					<td style="text-align:center">:</td>
					<td>'.$axRow['acti_EndTimeReservation'].'</td>
				</tr>';	
			}

			if ($axRow['acti_Transfer'] == '') { 

				$axRow['acti_Transfer'] = '<span class="glyphicon glyphicon-unchecked"></span>';	

			} else {	$axRow['acti_Transfer'] = '<span class="glyphicon glyphicon-check"></span>';	}

			if ($axRow['acti_Description'] == '') { $axRow['acti_Description'] = '-';	}
			else { $axRow['acti_Description'] = nl2br($axRow['acti_Description']); }

			if ($axRow['acti_Condition'] == '') { $axRow['acti_Condition'] = '-';	}
			else { $axRow['acti_Condition'] = nl2br($axRow['acti_Condition']); }

			if ($axRow['acti_Exception'] == '') { $axRow['acti_Exception'] = '-';	}
			else { $axRow['acti_Exception'] = nl2br($axRow['acti_Exception']); }

			if ($axRow['acti_HowToUse'] == '') { $axRow['acti_HowToUse'] = '-';	}
			else { $axRow['acti_HowToUse'] = nl2br($axRow['acti_HowToUse']); }

			if ($axRow['acti_Note'] == '') { $axRow['acti_Note'] = '-';	}
			else { $axRow['acti_Note'] = nl2br($axRow['acti_Note']); }

			if ($axRow['acti_Location'] == '') { $axRow['acti_Location'] = '-';	}
			else { $axRow['acti_Location'] = nl2br($axRow['acti_Location']); }

			if ($axRow['acti_StartDateSell'] == '0000-00-00') { $axRow['acti_StartDateSell'] = '-';	}
			else { $axRow['acti_StartDateSell'] = DateTime($axRow['acti_StartDateSell']); }

			if ($axRow['acti_EndDateSell'] == '0000-00-00') { $axRow['acti_EndDateSell'] = '-';	}
			else { $axRow['acti_EndDateSell'] = DateTime($axRow['acti_EndDateSell']); }

			if ($axRow['acti_Payment'] == '') { 

				$axRow['acti_Payment'] = '<span class="glyphicon glyphicon-unchecked"></span> Credit Card';	

			} else if ($axRow['acti_Payment'] == 'CreditCard') {

				$axRow['acti_Payment'] = '<span class="glyphicon glyphicon-check"></span> Credit Card';	
			}

			if ($axRow['acti_SpecialPeriodType'] == '0') { $special_period_type = '-';	}
			else {	

				$special_period_type = 'SELECT name FROM mi_master WHERE type="special_period_type" AND value="'.$axRow['acti_SpecialPeriodType'].'"';

				$special_period_type = $oDB->QueryOne($special_period_type);
			}

			if ($axRow['acti_LimitUse'] == 'T') { $axRow['acti_LimitUse'] = 'One Time Per '.$axRow['acti_OneTimePer'];	}
			else {	 $axRow['acti_LimitUse'] = '-';	}

			if ($axRow['acti_TrackLike'] == 'T') { 

				$axRow['acti_TrackLike'] = '<span class="glyphicon glyphicon-check"></span> Like';	

			} else {	$axRow['acti_TrackLike'] = '<span class="glyphicon glyphicon-unchecked"></span> Like';	}

			if ($axRow['acti_TrackReview'] == 'T') { 

				$axRow['acti_TrackReview'] = '<span class="glyphicon glyphicon-check"></span> Review';	

			} else {	$axRow['acti_TrackReview'] = '<span class="glyphicon glyphicon-unchecked"></span> Review';	}

			if ($axRow['acti_TrackRequest'] == 'T') { 

				$axRow['acti_TrackRequest'] = '<span class="glyphicon glyphicon-check"></span> Request';	

			} else {	$axRow['acti_TrackRequest'] = '<span class="glyphicon glyphicon-unchecked"></span> Request';	}

			if ($axRow['acti_TrackShare'] == 'T') { 

				$axRow['acti_TrackShare'] = '<span class="glyphicon glyphicon-check"></span> Share';	

			} else {	$axRow['acti_TrackShare'] = '<span class="glyphicon glyphicon-unchecked"></span> Share';	}

			if ($axRow['acti_Hidden'] == 'No') { 

				$axRow['acti_Hidden'] = '<span class="glyphicon glyphicon-eye-open"></span>';	

			} else {	$axRow['acti_Hidden'] = '<span class="glyphicon glyphicon-eye-close"></span>';	}



			# PRODUCT CATEGORY

			if ($axRow['prca_ProductCategoryID'] == 0) {	$category_name = '-';	}

			else {

				$category = 'SELECT name FROM mi_products_category WHERE category_id="'.$axRow['prca_ProductCategoryID'].'"';

				$category_name = $oDB->QueryOne($category);
			}


			# PRODUCT

			if ($axRow['prod_ProductID'] == 0) {	$product_name = '-';	$product_img = '';	}

			else {

				$product_name = 'SELECT name FROM mi_products WHERE products_id="'.$axRow['prod_ProductID'].'"';
				$product_name = $oDB->QueryOne($product_name);

				$product_img = 'SELECT image FROM mi_products WHERE products_id="'.$axRow['prod_ProductID'].'"';
				$product_img = $oDB->QueryOne($product_img);

				$product_path = 'SELECT path_image FROM mi_products WHERE products_id="'.$axRow['prod_ProductID'].'"';
				$product_path = $oDB->QueryOne($product_path);

				$product_img = '<img src="../../upload/'.$product_path.$product_img.'" width="150" height="150"/>';
			}


			# MOTIVATION

			if ($axRow['acti_Motivation'] == 'Point') { 

				$icon_sql = "SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID = 3";
				$icon = $oDB->QueryOne($icon_sql);

				$icon ='<img src="../../upload/collection_upload/'.$icon.'" width="12px" height="12px" style="margin-bottom:3px">';

				$plan_sql = "SELECT mopp_Name, mopp_PointQty, mopp_UseAmount FROM motivation_plan_point WHERE mopp_MotivationPointID='".$axRow['acti_MotivationID']."'";

				$get_point = $oDB->Query($plan_sql);
				$point = $get_point->FetchRow(DBI_ASSOC);

				$motivation_plan = $point['mopp_Name'].' &nbsp;('.number_format($point['mopp_UseAmount'],2).' ฿ / '.$point['mopp_PointQty'].' '.$icon.')';

			} else if ($axRow['acti_Motivation'] == 'Stamp') {

				$plan_sql = "SELECT mops_Name, mops_StampQty, mops_CollectionTypeID FROM motivation_plan_stamp WHERE mops_MotivationStampID='".$axRow['acti_MotivationID']."'";

				$get_stamp = $oDB->Query($plan_sql);
				$stamp = $get_stamp->FetchRow(DBI_ASSOC);

				$icon_sql = "SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID = ".$stamp['mops_CollectionTypeID'];

				$icon = $oDB->QueryOne($icon_sql);
				$icon ='<img src="../../upload/collection_upload/'.$icon.'" width="12px" height="12px" style="margin-bottom:3px">';

				$motivation_plan = $stamp['mops_Name'].' &nbsp;(1 Times / '.$stamp['mops_StampQty'].' '.$icon.')';

			} else {

				$motivation_plan = 'None';
			}


			# CUSTOM

			$sql_field = 'SELECT custom_field.cufi_Name 
							FROM custom_form_privilege 
							LEFT JOIN custom_field
							ON custom_field.cufi_CustomFieldID = custom_form_privilege.cufi_CustomFieldID
							WHERE custom_form_privilege.cufo_PrivilegeID="'.$axRow['acti_ActivityID'].'"
							AND custom_form_privilege.cufo_Type="Activity"
							AND custom_form_privilege.cufo_Deleted=""';

			$custom_field = $oDB->Query($sql_field);

			$check_field = $oDB->QueryOne($sql_field);

			$custom = '';

			if ($check_field) {

				while ($cufi_Name = $custom_field->FetchRow(DBI_ASSOC)){

					$custom .= $cufi_Name['cufi_Name']."<br>";
				}

			} else { $custom .= "-"; }

		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['acti_ActivityID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
				<div class="modal fade" id="View'.$axRow['acti_ActivityID'].'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
					<div class="modal-dialog" role="document" style="width:80%">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px">'.$axRow['acti_Hidden'].' &nbsp; '.$axRow['acti_Name'].'</b></span>';

		if($_SESSION['user_type_id_ses']==1){

			$view .= '	        <span style="float:right">'.$button_approve.' &nbsp; '.$button_unapprove.'</span>';
		}

		$view .= '		        <hr>
						        <center>
						        	'.$logo_view.' '.$acti_data.'<br><br>
						        	<span style="font-size:12px">
					                <ul id="tapMenu" class="nav nav-tabs">
					                    <li class="active">
					                    	<a data-toggle="tab" href="#basic'.$axRow['acti_ActivityID'].'">
					                    	<center><b>Basic</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#repetition'.$axRow['acti_ActivityID'].'">
					                    	<center><b>Repetition</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#transfer'.$axRow['acti_ActivityID'].'">
					                    	<center><b>Transfer</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#special'.$axRow['acti_ActivityID'].'">
					                    	<center><b>Special</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#member'.$axRow['acti_ActivityID'].'">
					                    	<center><b>Member</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#reservation'.$axRow['acti_ActivityID'].'">
					                    	<center><b>Reservation</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#performance'.$axRow['acti_ActivityID'].'">
					                    	<center><b>Performance</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#location'.$axRow['acti_ActivityID'].'">
					                    	<center><b>Location</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#financial'.$axRow['acti_ActivityID'].'">
					                    	<center><b>Financial</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#note'.$axRow['acti_ActivityID'].'">
					                    	<center><b>Note</b></center></a>
					                    </li>';

		if($_SESSION['user_type_id_ses']==1){

			$view .= '					<li>
					                    	<a data-toggle="tab" href="#product'.$axRow['acti_ActivityID'].'">
					                    	<center><b>Product</b></center></a>
					                    </li>';
		}

		$view .= '	                    <li>
					                    	<a data-toggle="tab" href="#custom'.$axRow['acti_ActivityID'].'">
					                    	<center><b>Custom</b></center></a>
					                    </li>
					                </ul>
					                </span>
					                <div class="tab-content">
					                    <div id="basic'.$axRow['acti_ActivityID'].'" class="tab-pane active"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Start Date</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.DateOnly($axRow['acti_StartDate']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">End Date</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.DateOnly($axRow['acti_EndDate']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Start Time</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.TimeOnly($axRow['acti_StartTime']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">End Time</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.TimeOnly($axRow['acti_EndTime']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Privilege Type</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['privilege_type_name'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Description</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['acti_Description'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="repetition'.$axRow['acti_ActivityID'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		'.$axRow['acti_Repetition'].'
								        	</table>
					                    </div>
					                    <div id="transfer'.$axRow['acti_ActivityID'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Allow Transfer</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['acti_Transfer'].' to other member</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="special'.$axRow['acti_ActivityID'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Special Period Type</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$special_period_type.'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="member'.$axRow['acti_ActivityID'].'" class="tab-pane"><br>
					                    	<table width="80%" class="myPopup">
								        		'.$axRow['acti_RepetitionMeber'].'
								        	</table>
					                    </div>
					                    <div id="reservation'.$axRow['acti_ActivityID'].'" class="tab-pane"><br>
					                    	<table width="80%" class="myPopup">
								        		'.$axRow['acti_Reservation'].'
								        	</table>
					                    </div>
					                    <div id="performance'.$axRow['acti_ActivityID'].'" class="tab-pane"><br>
					                    	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" valign="top" width="45%">Track</td>
								        			<td style="text-align:center" valign="top" width="5%">:</td>
								        			<td>'.$axRow['acti_TrackLike'].'<br>
								        				'.$axRow['acti_TrackReview'].'<br>
								        				'.$axRow['acti_TrackRequest'].'<br>
								        				'.$axRow['acti_TrackShare'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="location'.$axRow['acti_ActivityID'].'" class="tab-pane"><br>
					                    	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" valign="top" width="45%">Location</td>
								        			<td style="text-align:center" valign="top" width="5%">:</td>
								        			<td>'.$axRow['acti_Location'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="financial'.$axRow['acti_ActivityID'].'" class="tab-pane"><br>
					                    	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Start Date</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['acti_StartDateSell'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">End Date</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['acti_EndDateSell'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Price</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['acti_Price'].' Baht.</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Cost</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['acti_Cost'].' Baht.</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Payment</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['acti_Payment'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="note'.$axRow['acti_ActivityID'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" valign="top" width="45%">Condition</td>
								        			<td style="text-align:center" width="5%" valign="top">:</td>
								        			<td>'.$axRow['acti_Condition'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Exception</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['acti_Exception'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">How To Use</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['acti_HowToUse'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Note</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['acti_Note'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="product'.$axRow['acti_ActivityID'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" valign="top" width="45%">Product Category</td>
								        			<td style="text-align:center" width="5%" valign="top">:</td>
								        			<td>'.$category_name.'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Product</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$product_name.'<br>
								        				'.$product_img.'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="custom'.$axRow['acti_ActivityID'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" valign="top" width="45%">Custom Field</td>
								        			<td style="text-align:center" width="5%" valign="top">:</td>
								        			<td>'.$custom.'</td>
								        		</tr>
								        	</table>
					                    </div>
					                </div>
						        </center>
						    </div>
						    <div class="modal-footer">';

			if ($_SESSION['role_action']['activity']['edit'] == 1) {		    

				$view .= '       <a href="activity_create.php?act=edit&id='.$axRow['acti_ActivityID'].'">
						        <button type="button" class="btn btn-default btn-sm">Edit</button></a>';
			}

			$view .= '      <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';


		# DELETED

		if($axRow['acti_Deleted']=='') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['acti_ActivityID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>
				<div class="modal fade" id="Deleted'.$axRow['acti_ActivityID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="150px" style="text-align:center" valign="top">'.$acti_view.'</td>
						        	<td>
								        <p style="font-size:12px;padding-left:10px;">
								        	<b>"'.$axRow['acti_Name'].'"</b><br>
								            By clicking the <b>"Inactive"</b> button to:<br>
								            &nbsp; &nbsp;- Inactive this privilege<br>
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="activity.php?act=delete&id='.$axRow['acti_ActivityID'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		} else if ($axRow['acti_Deleted']=='T') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['acti_ActivityID'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>
				<div class="modal fade" id="Deleted'.$axRow['acti_ActivityID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="150px" style="text-align:center" valign="top">'.$acti_view.'</td>
						        	<td>
								        <p style="font-size:12px;padding-left:10px;">
								        	<b>"'.$axRow['acti_Name'].'"</b><br>
								           	By clicking the <b>"Active"</b> button to:<br>
								            &nbsp; &nbsp;- Active this privilege<br>
								            &nbsp; &nbsp;- Change status to Pending
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="activity.php?act=delete&id='.$axRow['acti_ActivityID'].'">
						        <button type="button" class="btn btn-default btn-sm">Active</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		}


		# DATA TABLE

		$data_table .= '<tr >
							<td >'.$i.'<br><br><center>'.$image_status.'</center></td>
							<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
								<span style="font-size:11px;">'.$axRow['brand_name'].'</span>
							</td>
							<td style="text-align:center">'.$acti_image.'</td>
							<td >'.$axRow['acti_Name'].'</td>
							<td >'.$axRow['privilege_type_name'].'</td>
							<td >'.$date.'<br>'.$time.'</td>
							<td style="text-align:center">'.$status.'</td>
							<td >'.DateTime($axRow['acti_UpdatedDate']).'</td>';

		if ($_SESSION['role_action']['activity']['view'] == 1) {

			$data_table .=	'<td style="text-align:center">'.$view.'</td>';
		}

		if ($_SESSION['role_action']['activity']['delete'] == 1) {

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

$oTmp->assign('is_menu', 'is_activity');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_privilege', 'in');

$oTmp->assign('content_file', 'activity/activity.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}


//========================================//

?>
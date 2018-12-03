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

if ($_SESSION['role_action']['coupon']['view'] != 1) {

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

	$where_brand = ' AND coupon.bran_BrandID = "'.$_SESSION['user_brand_id'].'" AND coupon.coup_Deleted=""';
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

		coupon.*,
		mi_brand.name AS brand_name,
		mi_brand.logo_image AS brand_logo,
		mi_brand.path_logo,
		mi_privilege_type.name AS privilege_type_name

		FROM coupon

		INNER JOIN mi_brand
		ON mi_brand.brand_id = coupon.bran_BrandID

		LEFT JOIN mi_privilege_type
		ON coupon.prty_PrivilegeTypeID = mi_privilege_type.privilege_type_id

		WHERE coup_Birthday!="T"
		'.$where_search.'
		'.$where_brand.' 

		ORDER BY CASE 
			WHEN coupon.coup_Deleted = "" THEN 1
	        WHEN coupon.coup_Deleted = "T" THEN 2 END ASC,
			coupon.coup_Status ASC, 
			coupon.coup_UpdatedDate DESC';


if($Act == 'approve' && $id != '') {

	# APPROVE IMAGE

	$sql = '';
	$sql .= 'SELECT coup_ImageNew, coup_Image FROM coupon WHERE coup_CouponID ="'.$id.'"';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($approve == 'unapprove') {

 		# UNAPPROVE

		// if ($axRow['coup_ImageNew']!="") {

		// 	unlink_file($oDB,'coupon','coup_ImageNew','coup_CouponID',$id,$path_upload_coupon,$axRow['coup_ImageNew']);

		// 	$do_sql_upload = "UPDATE coupon 

		// 						SET coup_ImageNew='',

		// 						coup_Status='Pending',

		// 						coup_UpdatedDate='".$time_insert."' 

		// 						WHERE coup_CouponID='".$id."' ";

		// } else if ($axRow['coup_Image']!=""){

		// 	unlink_file($oDB,'coupon','coup_Image','coup_CouponID',$id,$path_upload_coupon,$axRow['coup_Image']);

		// 	$do_sql_upload = "UPDATE coupon 

		// 						SET coup_Image='',

		// 						coup_Status='Pending',

		// 						coup_UpdatedDate='".$time_insert."'  

		// 						WHERE coup_CouponID='".$id."' ";

		// }

		unlink_file($oDB,'coupon','coup_Image','coup_CouponID',$id,$path_upload_coupon,$axRow['coup_Image']);

		$do_sql_upload = "UPDATE coupon 
							SET coup_Approve='',
							coup_Status='Pending',
							coup_UpdatedDate='".$time_insert."',
							coup_UpdatedBy='".$_SESSION['UID']."'
							WHERE coup_CouponID='".$id."' ";

 		$oDB->QueryOne($do_sql_upload);
 	}

	
	if ($approve == 'approve') {

		# APPROVE

		// if ($axRow['coup_Image']!="") {

		// 	unlink_file($oDB,'coupon','coup_Image','coup_CouponID',$id,$path_upload_coupon,$axRow['coup_Image']);

		// 	$do_sql_upload = "UPDATE coupon 

		// 						SET coup_Image='".$axRow['coup_ImageNew']."', 

		// 						coup_ImageNew='',

		// 						coup_UpdatedDate='".$time_insert."' 

		// 						WHERE coup_CouponID='".$id."'";

		// } else {

		// 	$do_sql_upload = "UPDATE coupon 

		// 						SET coup_Image='".$axRow['coup_ImageNew']."', 

		// 						coup_ImageNew='',

		// 						coup_Status='Pending',

		// 						coup_UpdatedDate='".$time_insert."' 

		// 						WHERE coup_CouponID='".$id."'";

		// }

		$do_sql_upload = "UPDATE coupon 
							SET coup_Approve='T',
							coup_UpdatedDate='".$time_insert."',
							coup_UpdatedBy='".$_SESSION['UID']."'
							WHERE coup_CouponID='".$id."' ";

 		$oDB->QueryOne($do_sql_upload);
	}

	echo '<script> window.location.href="coupon.php"; </script>';

} else if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE coupon 
	 					SET coup_Status='Pending',
	 						coup_UpdatedDate='".$time_insert."' 
	 					WHERE coup_CouponID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="coupon.php";</script>';

} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE coupon 
	 					SET coup_Status='Active',
	 						coup_UpdatedDate='".$time_insert."' 
	 					WHERE coup_CouponID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="coupon.php";</script>';

} else if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT coup_Deleted FROM coupon WHERE coup_CouponID ="'.$id.'"';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($axRow['coup_Deleted']=='') {

 		$do_sql_coup = "UPDATE coupon
 							SET coup_Deleted='T', 
 							coup_Status='Pending',
 							coup_Motivation='None',
 							coup_MotivationID='0',
 							coup_UpdatedDate='".$time_insert."' 
 							WHERE coup_CouponID='".$id."'";

 		$do_sql_regis = "UPDATE mi_card_register
 							SET status='1', 
 							date_update='".$time_insert."' 
 							WHERE coupon_id='".$id."'";

 		$do_sql_point = "UPDATE motivation_plan_point
 							SET mopp_PrivilegeType='None', 
 							mopp_PrivilegeID='0' 
 							WHERE mopp_PrivilegeID='".$id."'
 							AND mopp_PrivilegeType='Coupon'";

 		$do_sql_stamp = "UPDATE motivation_plan_stamp
 							SET mops_PrivilegeType='None', 
 							mops_PrivilegeID='0' 
 							WHERE mops_PrivilegeID='".$id."'
 							AND mops_PrivilegeType='Coupon'";

 	} else if ($axRow['coup_Deleted']=='T') {

		$do_sql_coup = "UPDATE coupon
 							SET coup_Deleted='', 
 							coup_Status='Pending',
 							coup_UpdatedDate='".$time_insert."' 
 							WHERE coup_CouponID='".$id."'";

 		$do_sql_regis = "";
 		$do_sql_point = "";
 		$do_sql_stamp = "";
	}


 	$oDB->QueryOne($do_sql_coup);
 	$oDB->QueryOne($do_sql_regis);
 	$oDB->QueryOne($do_sql_point);
 	$oDB->QueryOne($do_sql_stamp);

 	echo '<script>window.location.href="coupon.php";</script>';

} else if($Act=='xls'){

	require_once '../../lib/PHPExcel/Classes/PHPExcel.php';

	$objPHPExcel = new PHPExcel();

	$char = 'A';

	$chars = $char;

	$row_start = 1;

	$reportName = 'Coupon Report';

	$objPHPExcel->setActiveSheetIndex(0);

	// HEAD SHEET

    $objWorkSheet = $objPHPExcel->createSheet('0');

    $objWorkSheet	->setCellValue('A1', 'Topics this report : '.$reportName )
                   	->setCellValue('A2', 'Issued this report  : '.$_SESSION['UNAME'].' / '.$_SESSION['FULLNAME'])
                   	->setCellValue('A3', 'Check out this report : '.$time_insert);

    $objWorkSheet->setTitle("Header");

    // DATA SHEET

    $objWorkSheet = $objPHPExcel->createSheet('1');

	$objWorkSheet	->setCellValue($chars++.$row_start, 'Brand')
					->setCellValue($chars++.$row_start, 'Coupon')
					->setCellValue($chars++.$row_start, 'Validate')
					->setCellValue($chars++.$row_start, 'Start Date')
					->setCellValue($chars++.$row_start, 'End Date')
					->setCellValue($chars++.$row_start, 'Time')
					->setCellValue($chars++.$row_start, 'Start Time')
					->setCellValue($chars++.$row_start, 'End Time')
					->setCellValue($chars++.$row_start, 'Description')
					->setCellValue($chars++.$row_start, 'Type')
					->setCellValue($chars++.$row_start, 'Repetition Use')
					->setCellValue($chars++.$row_start, 'Allow Transfer')
					->setCellValue($chars++.$row_start, 'Special Period Type')
					->setCellValue($chars++.$row_start, 'Repetition Use (Member)')
					->setCellValue($chars++.$row_start, 'Track Like')
					->setCellValue($chars++.$row_start, 'Track Review')
					->setCellValue($chars++.$row_start, 'Track Request')
					->setCellValue($chars++.$row_start, 'Track Share')
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

	// $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:'.$chars.'1');

	// $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:'.$chars.'2');

	// $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A3:'.$chars.'3');

	// $i = 6;

	$row_start++;

	$oRes = $oDB->Query($sql);

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)) {

		if($axRow['coup_Deleted']=='') { $axRow['coup_Deleted']="No"; }
		else if ($axRow['coup_Deleted']=='T') {	$axRow['coup_Deleted']="Yes"; }

		if ($axRow['coup_Transfer'] == '') { $axRow['coup_Transfer'] = 'No'; } 
		else {	$axRow['coup_Transfer'] = 'Yes';	}

		if ($axRow['coup_SpecialPeriodType'] == '0') { $special_period_type = '';	}
		else {

			$special_period_type = 'SELECT name FROM mi_master WHERE type="special_period_type" AND value="'.$axRow['coup_SpecialPeriodType'].'"';

			$special_period_type = $oDB->QueryOne($special_period_type);
		}

		if ($axRow['coup_StartDateSell'] == '0000-00-00') { $axRow['coup_StartDateSell'] = '';	}
		else { $axRow['coup_StartDateSell'] = DateOnly($axRow['coup_StartDateSell']); }

		if ($axRow['coup_EndDateSell'] == '0000-00-00') { $axRow['coup_EndDateSell'] = '';	}
		else { $axRow['coup_EndDateSell'] = DateOnly($axRow['coup_EndDateSell']); }

		if ($axRow['coup_Time'] == 'Fix') { 

			$axRow['coup_Time'] = 'Fix Time';

			if ($axRow['coup_StartTime'] == '00:00:00') { $axRow['coup_StartTime'] = '';	}
			else { $axRow['coup_StartTime'] = TimeOnly($axRow['coup_StartTime']); }

			if ($axRow['coup_EndTime'] == '00:00:00') { $axRow['coup_EndTime'] = '';	}
			else { $axRow['coup_EndTime'] = TimeOnly($axRow['coup_EndTime']); } 

		} else { 

			$axRow['coup_Time'] = 'All Day';
			$axRow['coup_StartTime'] = '';
			$axRow['coup_EndTime'] = '';
		}

		if ($axRow['coup_Payment'] == 'F') { $axRow['coup_Payment'] = 'No'; } 
		else { $axRow['coup_Payment'] = 'Yes';	}

		if ($axRow['coup_TrackLike'] == 'T') { $axRow['coup_TrackLike'] = 'Yes'; } 
		else {	$axRow['coup_TrackLike'] = 'No';	}

		if ($axRow['coup_TrackReview'] == 'T') { $axRow['coup_TrackReview'] = 'Yes'; } 
		else {	$axRow['coup_TrackReview'] = 'No';	}

		if ($axRow['coup_TrackRequest'] == 'T') { $axRow['coup_TrackRequest'] = 'Yes'; } 
		else {	$axRow['coup_TrackRequest'] = 'No';	}

		if ($axRow['coup_TrackShare'] == 'T') { $axRow['coup_TrackShare'] = 'Yes'; } 
		else {	$axRow['coup_TrackShare'] = 'No';	}

		if ($axRow['coup_Method'] == 'Dpd') { 

			$axRow['coup_Method'] = 'Member Life Time';
			$axRow['coup_StartDate'] = '';
			$axRow['coup_EndDate'] = '';

		} else { $axRow['coup_Method'] = 'Specific Time';

			if ($axRow['coup_StartDate'] == '0000-00-00') { $axRow['coup_StartDate'] = '';	}
			else { $axRow['coup_StartDate'] = DateOnly($axRow['coup_StartDate']); }

			if ($axRow['coup_EndDate'] == '0000-00-00') { $axRow['coup_EndDate'] = '';	}
			else { $axRow['coup_EndDate'] = DateOnly($axRow['coup_EndDate']); }
		}

		if ($axRow['coup_QtyPer'] == 'Not') { $axRow['coup_QtyPer'] = 'Not Specific'; }

		if ($axRow['coup_Repetition'] == 'T') { 

			$axRow['coup_Repetition'] = 'Yes'; 
			$coup_Repetition = $axRow['coup_Qty'].' Per '.$axRow['coup_QtyPer'];

		} else { 

			$axRow['coup_Repetition'] = 'No'; 
			$coup_Repetition = '';
		}

		if ($axRow['coup_RepetitionMember'] == 'T') { 

			$axRow['coup_RepetitionMember'] = 'Yes'; 
			$coup_RepetitionMember = $axRow['coup_Qty'].' Per '.$axRow['coup_QtyPer'];

		} else { 

			$axRow['coup_RepetitionMember'] = 'No'; 
			$coup_RepetitionMember = '';
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


		$chars = $char;

		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['brand_name']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['coup_Name']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['coup_Method']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['coup_StartDate']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['coup_EndDate']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['coup_Time']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['coup_StartTime']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['coup_EndTime']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['coup_Description']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['privilege_type_name']);
		$objWorkSheet->setCellValue($chars++.$row_start, $coup_Repetition);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['coup_Transfer']);
		$objWorkSheet->setCellValue($chars++.$row_start, $special_period_type);
		$objWorkSheet->setCellValue($chars++.$row_start, $coup_RepetitionMember);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['coup_TrackLike']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['coup_TrackReview']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['coup_TrackRequest']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['coup_TrackShare']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['coup_Location']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['coup_StartDateSell']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['coup_EndDateSell']);
		$objWorkSheet->setCellValue($chars++.$row_start, number_format($axRow['coup_Price'],2).' ฿');
		$objWorkSheet->setCellValue($chars++.$row_start, number_format($axRow['coup_Cost'],2).' ฿');
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['coup_Payment']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['coup_Condition']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['coup_Exception']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['coup_HowToUse']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['coup_Note']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['category_name']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['product_name']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['coup_Status']);
		$objWorkSheet->setCellValue($chars++.$row_start, DateTime($axRow['coup_UpdatedDate']));
		$objWorkSheet->setCellValue($chars.$row_start, $axRow['coup_Deleted']);

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


		# APPROVE

		if ($axRow['coup_Approve']=='' && $axRow['coup_Image']=='') {

			$image_status = '<span class="glyphicon glyphicon-exclamation-sign" style="color:#f0ad4e;font-size:20px"></span>';

			$button_approve = '';

            $button_unapprove = '';

		} else if ($axRow['coup_Approve']=='T' && $axRow['coup_Image']) {

			$image_status = '';

			$button_approve = '';

            $button_unapprove = '<a style="cursor:pointer" href="coupon.php?act=approve&approve=unapprove&id='.$axRow['coup_CouponID'].'">
                                <button type="button" class="btn btn-default btn-sm">
                                <span class="glyphicon glyphicon-remove-sign" aria-hidden="true" style="color:red"></span> Unapprove</button></a>';
		} else {

			$image_status = '';

			$button_approve = '<a style="cursor:pointer" href="coupon.php?act=approve&approve=approve&id='.$axRow['coup_CouponID'].'">
                                <button type="button" class="btn btn-default btn-sm">
                                <span class="glyphicon glyphicon-ok-sign" aria-hidden="true" style="color:green"></span> Approve</button></a>';

            $button_unapprove = '<a style="cursor:pointer" href="coupon.php?act=approve&approve=unapprove&id='.$axRow['coup_CouponID'].'">
                                <button type="button" class="btn btn-default btn-sm">
                                <span class="glyphicon glyphicon-remove-sign" aria-hidden="true" style="color:red"></span> Unapprove</button></a>';
		}



		# STATUS

		$status = '';

		if($axRow['coup_Deleted']=='T'){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

		} else {

			if($axRow['coup_Status']=='Active'){

				if ($_SESSION['role_action']['coupon']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'coupon.php?act=active&id='.$axRow['coup_CouponID'].'\'">
		                    <option class="status_default" value="'.$axRow['coup_CouponID'].'" selected>On</option>
		                    <option class="status_default">Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';
		        }

			} else {

				if ($_SESSION['role_action']['coupon']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
									<select class="form-control text-md status_pending" name="pending_status" onchange="window.location.href=\'coupon.php?act=pending&id='.$axRow['coup_CouponID'].'\'">
					                    <option class="status_default">On</option>
					                    <option class="status_default" value="'.$axRow['coup_CouponID'].'" selected>Off</option>
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

			$logo_brand = '<img src="../../images/400x400.png" width="60" height="60"/>';

			$logo_view = '<img src="../../images/400x400.png" width="150" height="150"/>';
		}



		# COUPON IMAGE

		if($axRow['coup_ImageNew']!=''){

			$coup_image = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_ImageNew'].'" class="image_border" width="128" height="80"/>';

			$coup_view = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_ImageNew'].'" class="image_border" width="136" height="85"/>';

			$coup_data = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_ImageNew'].'" class="image_border" width="240" height="150"/>';

		} else {

			if($axRow['coup_Image']!=''){

				$coup_image = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_Image'].'" class="image_border" width="128" height="80"/>';

				$coup_view = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_Image'].'" class="image_border" width="136" height="85"/>';

				$coup_data = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_Image'].'" class="image_border" width="240" height="150"/>';

			} else {

				$coup_image = '<img src="../../images/card_privilege.jpg" width="128" height="80"/>';

				$coup_view = '<img src="../../images/card_privilege.jpg" width="136" height="85"/>';

				$coup_data = '<img src="../../images/card_privilege.jpg" width="240" height="150"/>';
			}
		}



		# DELETED

		if($axRow['coup_Deleted']=='') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['coup_CouponID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>
				<div class="modal fade" id="Deleted'.$axRow['coup_CouponID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="150px" style="text-align:center" valign="top">'.$coup_view.'</td>
						        	<td>
								        <p style="font-size:12px;padding-left:10px;">
								        	<b>"'.$axRow['coup_Name'].'"</b><br>
								            By clicking the <b>"Inactive"</b> button to:<br>
								            &nbsp; &nbsp;- Inactive this coupon<br>
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="coupon.php?act=delete&id='.$axRow['coup_CouponID'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		} else if ($axRow['coup_Deleted']=='T') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['coup_CouponID'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>
				<div class="modal fade" id="Deleted'.$axRow['coup_CouponID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="150px" style="text-align:center" valign="top">'.$coup_view.'</td>
						        	<td>
								        <p style="font-size:12px;padding-left:10px;">
								        	<b>"'.$axRow['coup_Name'].'"</b><br>
								           	By clicking the <b>"Active"</b> button to:<br>
								            &nbsp; &nbsp;- Active this coupon<br>
								            &nbsp; &nbsp;- Change status to Pending
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="coupon.php?act=delete&id='.$axRow['coup_CouponID'].'">
						        <button type="button" class="btn btn-default btn-sm">Active</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		}



		# VIEW

			# DATA

			if ($axRow['coup_Method'] == 'Dpd') {

				$validate = '<tr>
								<td style="text-align:right" width="45%">Validate</td>
								<td style="text-align:center" width="5%">:</td>
								<td>Member Life Time</td>
							</tr>';
			} else {

				$validate = '<tr>
								<td style="text-align:right" width="45%">Start Date</td>
								<td style="text-align:center" width="5%">:</td>
								<td>'.DateOnly($axRow['coup_StartDate']).'</td>
							</tr>
							<tr>
								<td style="text-align:right" width="45%">End Date</td>
								<td style="text-align:center" width="5%">:</td>
								<td>'.DateOnly($axRow['coup_EndDate']).'</td>
							</tr>';
			}

			if ($axRow['coup_Repetition'] == '') { 

				$axRow['coup_Repetition'] = '<tr>
												<td style="text-align:right" width="45%">Repetition</td>
												<td style="text-align:center" width="5%">:</td>
												<td> - </td>
											</tr>';	
			} else { 

				if ($axRow['coup_QtyPer'] == 'Not') { $axRow['coup_QtyPer'] = 'Not Specific'; }

				$axRow['coup_Repetition'] = '<tr>
												<td style="text-align:right" width="45%">Repetition</td>
												<td style="text-align:center" width="5%">:</td>
												<td><span class="glyphicon glyphicon-check"></span> Use</td>
											</tr>
											<tr>
												<td style="text-align:right">Coupon QTY</td>
												<td style="text-align:center">:</td>
												<td>'.$axRow['coup_Qty'].' Per '.$axRow['coup_QtyPer'].'</td>
											</tr>
											<tr>
												<td></td>
												<td></td>
												<td>'.$axRow['coup_QtyPerData'].'</td>
											</tr>';	
			}

			if ($axRow['coup_RepetitionMember'] == '') { 

				$axRow['coup_RepetitionMember'] = '<tr>
														<td style="text-align:right" width="45%">Repetition</td>
														<td style="text-align:center" width="5%">:</td>
														<td> - </td>
													</tr>';	

			} else { 

				$axRow['coup_RepetitionMember'] = '<tr>
														<td style="text-align:right" width="45%">Repetition</td>
														<td style="text-align:center" width="5%">:</td>
														<td><span class="glyphicon glyphicon-check"></span> Use</td>
													</tr>
													<tr>
														<td style="text-align:right">Coupon QTY</td>
														<td style="text-align:center">:</td>
														<td>'.$axRow['coup_QtyMember'].' Per '.$axRow['coup_QtyPerMember'].'</td>
													</tr>
													<tr>
														<td></td>
														<td></td>
														<td>'.$axRow['coup_QtyPerMemberData'].'</td>
													</tr>';	
			}

			if ($axRow['coup_Transfer'] == '') { 

				$axRow['coup_Transfer'] = '<span class="glyphicon glyphicon-unchecked"></span>';	

			} else {	$axRow['coup_Transfer'] = '<span class="glyphicon glyphicon-check"></span>';	}

			if ($axRow['coup_SpecialPeriodType'] == '0') { $special_period_type = '-';	}
			else {	

				$special_period_type = 'SELECT name FROM mi_master WHERE type="special_period_type" AND value="'.$axRow['coup_SpecialPeriodType'].'"';

				$special_period_type = $oDB->QueryOne($special_period_type);
			}

			if ($axRow['coup_Description'] == '') { $axRow['coup_Description'] = '-';	}

			if ($axRow['coup_Location'] == '') { $axRow['coup_Location'] = '-';	}

			if ($axRow['coup_StartDateSell'] == '0000-00-00') { $axRow['coup_StartDateSell'] = '-';	}
			else { $axRow['coup_StartDateSell'] = DateTime($axRow['coup_StartDateSell']); }

			if ($axRow['coup_EndDateSell'] == '0000-00-00') { $axRow['coup_EndDateSell'] = '-';	}
			else { $axRow['coup_EndDateSell'] = DateTime($axRow['coup_EndDateSell']); }

			if ($axRow['coup_Time'] == 'Fix') {

				if ($axRow['coup_StartTime'] == '00:00:00') { $axRow['coup_StartTime'] = '-';	}
				else { $axRow['coup_StartTime'] = TimeOnly($axRow['coup_StartTime']); }

				if ($axRow['coup_EndTime'] == '00:00:00') { $axRow['coup_EndTime'] = '-';	}
				else { $axRow['coup_EndTime'] = TimeOnly($axRow['coup_EndTime']); }

				$time = '<tr>
							<td style="text-align:right" width="45%">Start Time</td>
							<td style="text-align:center" width="5%">:</td>
							<td>'.$axRow['coup_StartTime'].'</td>
						</tr>
						<tr>
							<td style="text-align:right">End Time</td>
							<td style="text-align:center">:</td>
							<td>'.$axRow['coup_EndTime'].'</td>
						</tr>';
			} else {

				$time = '<tr>
							<td style="text-align:right" width="45%">Time</td>
							<td style="text-align:center" width="5%">:</td>
							<td>All Day</td>
						</tr>';
			}

			if ($axRow['coup_Payment'] == 'F') { 

				$axRow['coup_Payment'] = '<span class="glyphicon glyphicon-unchecked"></span>';	

			} else {

				$axRow['coup_Payment'] = '<span class="glyphicon glyphicon-check"></span>';	
			}

			if ($axRow['coup_Condition'] == '') { $axRow['coup_Condition'] = '-';	}

			if ($axRow['coup_Exception'] == '') { $axRow['coup_Exception'] = '-';	}

			if ($axRow['coup_HowToUse'] == '') { $axRow['coup_HowToUse'] = '-';	}

			if ($axRow['coup_Note'] == '') { $axRow['coup_Note'] = '-';	}

			if ($axRow['coup_TrackLike'] == 'T') { 

				$axRow['coup_TrackLike'] = '<span class="glyphicon glyphicon-check"></span> Like';	

			} else {	$axRow['coup_TrackLike'] = '<span class="glyphicon glyphicon-unchecked"></span> Like';	}

			if ($axRow['coup_TrackReview'] == 'T') { 

				$axRow['coup_TrackReview'] = '<span class="glyphicon glyphicon-check"></span> Review';	

			} else {	$axRow['coup_TrackReview'] = '<span class="glyphicon glyphicon-unchecked"></span> Review';	}

			if ($axRow['coup_TrackRequest'] == 'T') { 

				$axRow['coup_TrackRequest'] = '<span class="glyphicon glyphicon-check"></span> Request';	

			} else {	$axRow['coup_TrackRequest'] = '<span class="glyphicon glyphicon-unchecked"></span> Request';	}

			if ($axRow['coup_TrackShare'] == 'T') { 

				$axRow['coup_TrackShare'] = '<span class="glyphicon glyphicon-check"></span> Share';	

			} else {	$axRow['coup_TrackShare'] = '<span class="glyphicon glyphicon-unchecked"></span> Share';	}

			if ($axRow['coup_Hidden'] == 'No') { 

				$axRow['coup_Hidden'] = '<span class="glyphicon glyphicon-eye-open"></span>';	

			} else {	$axRow['coup_Hidden'] = '<span class="glyphicon glyphicon-eye-close"></span>';	}



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

			// if ($axRow['coup_Motivation'] == 'Point') { 

			// 	$icon_sql = "SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID = 3";

			// 	$icon = $oDB->QueryOne($icon_sql);

			// 	$icon ='<img src="../../upload/collection_upload/'.$icon.'" width="12px" height="12px" style="margin-bottom:3px">';

			// 	$plan_sql = "SELECT mopp_Name, mopp_PointQty, mopp_UseAmount FROM motivation_plan_point WHERE mopp_MotivationPointID='".$axRow['coup_MotivationID']."'";

			// 	$get_point = $oDB->Query($plan_sql);

			// 	$point = $get_point->FetchRow(DBI_ASSOC);

			// 	$motivation_plan = $point['mopp_Name'].' &nbsp;('.number_format($point['mopp_UseAmount'],2).' ฿ / '.$point['mopp_PointQty'].' '.$icon.')';

			// } else if ($axRow['coup_Motivation'] == 'Stamp') {

			// 	$plan_sql = "SELECT mops_Name, mops_StampQty, mops_CollectionTypeID FROM motivation_plan_stamp WHERE mops_MotivationStampID='".$axRow['coup_MotivationID']."'";

			// 	$get_stamp = $oDB->Query($plan_sql);

			// 	$stamp = $get_stamp->FetchRow(DBI_ASSOC);

			// 	$icon_sql = "SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID = ".$stamp['mops_CollectionTypeID'];

			// 	$icon = $oDB->QueryOne($icon_sql);

			// 	$icon ='<img src="../../upload/collection_upload/'.$icon.'" width="12px" height="12px" style="margin-bottom:3px">';

			// 	$motivation_plan = $stamp['mops_Name'].' &nbsp;(1 Times / '.$stamp['mops_StampQty'].' '.$icon.')';

			// } else {

			// 	$motivation_plan = 'None';
			// }


			# CUSTOM

			$sql_field = 'SELECT custom_field.cufi_Name 
							FROM custom_form_privilege 
							LEFT JOIN custom_field
							ON custom_field.cufi_CustomFieldID = custom_form_privilege.cufi_CustomFieldID
							WHERE custom_form_privilege.cufo_PrivilegeID="'.$axRow['coup_CouponID'].'"
							AND custom_form_privilege.cufo_Type="Coupon"
							AND custom_form_privilege.cufo_Deleted=""';

			$custom_field = $oDB->Query($sql_field);

			$check_field = $oDB->QueryOne($sql_field);

			$custom = '';

			if ($check_field) {

				while ($cufi_Name = $custom_field->FetchRow(DBI_ASSOC)){

					$custom .= $cufi_Name['cufi_Name']."<br>";
				}

			} else { $custom .= "-"; }


		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['coup_CouponID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
				<div class="modal fade" id="View'.$axRow['coup_CouponID'].'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
					<div class="modal-dialog" role="document" style="width:70%">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px">'.$axRow['coup_Hidden'].' &nbsp; '.$axRow['coup_Name'].'</b></span>';

		if($_SESSION['user_type_id_ses']==1){

			$view .= '	        <span style="float:right">'.$button_approve.' &nbsp; '.$button_unapprove.'</span>';
		}

		$view .= '		        <hr>
						        <center>
						        	'.$logo_view.' '.$coup_data.'<br><br>
						        	<span style="font-size:12px">
					                <ul id="tapMenu" class="nav nav-tabs">
					                    <li class="active">
					                    	<a data-toggle="tab" href="#basic'.$axRow['coup_CouponID'].'">
					                    	<center><b>Basic</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#repetition'.$axRow['coup_CouponID'].'">
					                    	<center><b>Repetition</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#transfer'.$axRow['coup_CouponID'].'">
					                    	<center><b>Transfer</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#special'.$axRow['coup_CouponID'].'">
					                    	<center><b>Special</b></center></a>
					                   	</li>
					                    <li>
					                    	<a data-toggle="tab" href="#member'.$axRow['coup_CouponID'].'">
					                    	<center><b>Member</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#performance'.$axRow['coup_CouponID'].'">
					                    	<center><b>Performance</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#location'.$axRow['coup_CouponID'].'">
					                    	<center><b>Location</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#financial'.$axRow['coup_CouponID'].'">
					                    	<center><b>Financial</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#note'.$axRow['coup_CouponID'].'">
					                    	<center><b>Note</b></center></a>
					                    </li>';

		if($_SESSION['user_type_id_ses']==1){

			$view .= '					<li>
					                    	<a data-toggle="tab" href="#product'.$axRow['coup_CouponID'].'">
					                    	<center><b>Product</b></center></a>
					                    </li>';
		}

		$view .= '	                    <li>
					                    	<a data-toggle="tab" href="#custom'.$axRow['coup_CouponID'].'">
					                    	<center><b>Custom</b></center></a>
					                    </li>
					                </ul>
					                </span>
					                <div class="tab-content">
					                    <div id="basic'.$axRow['coup_CouponID'].'" class="tab-pane active"><br>
								        	<table width="80%" class="myPopup">
								        		'.$validate.'
								        		'.$time.'
								        		<tr>
								        			<td style="text-align:right">Privilege Type</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['privilege_type_name'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Description</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['coup_Description'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="repetition'.$axRow['coup_CouponID'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		'.$axRow['coup_Repetition'].'
								        	</table>
					                    </div>
					                    <div id="transfer'.$axRow['coup_CouponID'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Allow Transfer</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['coup_Transfer'].' to other member</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="special'.$axRow['coup_CouponID'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Special Period Type</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$special_period_type.'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="member'.$axRow['coup_CouponID'].'" class="tab-pane"><br>
					                    	<table width="80%" class="myPopup">
								        		'.$axRow['coup_RepetitionMember'].'
								        	</table>
					                    </div>
					                    <div id="performance'.$axRow['coup_CouponID'].'" class="tab-pane"><br>
					                    	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" valign="top" width="45%">Track</td>
								        			<td style="text-align:center" valign="top" width="5%">:</td>
								        			<td>'.$axRow['coup_TrackLike'].'<br>
								        				'.$axRow['coup_TrackReview'].'<br>
								        				'.$axRow['coup_TrackRequest'].'<br>
								        				'.$axRow['coup_TrackShare'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="location'.$axRow['coup_CouponID'].'" class="tab-pane"><br>
					                    	<table width="80%" class="myPopup">
								        		<tr height="35px">
								        			<td style="text-align:right" width="45%">Location Detail</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['coup_Location'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="financial'.$axRow['coup_CouponID'].'" class="tab-pane"><br>
					                    	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Start Date</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['coup_StartDateSell'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">End Date</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['coup_EndDateSell'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Price</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['coup_Price'].' Baht.</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Cost</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['coup_Cost'].' Baht.</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Payment</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['coup_Payment'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="note'.$axRow['coup_CouponID'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" valign="top" width="45%">Condition</td>
								        			<td style="text-align:center" width="5%" valign="top">:</td>
								        			<td>'.$axRow['coup_Condition'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Exception</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['coup_Exception'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">How To Use</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['coup_HowToUse'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Note</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['coup_Note'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="product'.$axRow['coup_CouponID'].'" class="tab-pane"><br>
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
					                    <div id="custom'.$axRow['coup_CouponID'].'" class="tab-pane"><br>
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

			if ($_SESSION['role_action']['coupon']['edit'] == 1) {		    

				$view .= '       <a href="coupon_create.php?act=edit&id='.$axRow['coup_CouponID'].'">
						        <button type="button" class="btn btn-default btn-sm">Edit</button></a>';
			}

				$view .= '      <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';



		# DATA TABLE

		$data_table .= '<tr >
							<td >'.$i.'<br><br><center>'.$image_status.'</center></td>
							<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
								<span style="font-size:11px;">'.$axRow['brand_name'].'</span>
							</td>
							<td style="text-align:center">'.$coup_image.'</td>
							<td >'.$axRow['coup_Name'].'</td>
							<td >'.$axRow['privilege_type_name'].'</td>
							<td style="text-align:center">'.$status.'</td>
							<td >'.DateTime($axRow['coup_UpdatedDate']).'</td>';

		if ($_SESSION['role_action']['coupon']['view'] == 1) {

			$data_table .=	'<td style="text-align:center">'.$view.'</td>';
		}

		if ($_SESSION['role_action']['coupon']['delete'] == 1) {

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

$oTmp->assign('is_menu', 'is_coupon');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_privilege', 'in');

$oTmp->assign('content_file', 'coupon/coupon.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
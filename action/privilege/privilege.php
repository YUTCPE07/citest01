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

if ($_SESSION['role_action']['privilege']['view'] != 1) {

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

	$where_brand = ' AND privilege.bran_BrandID="'.$_SESSION['user_brand_id'].'" AND privilege.priv_Deleted=""';
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
		privilege.*,
		mi_brand.name AS brand_name,
		mi_brand.logo_image AS brand_logo,
		mi_brand.path_logo,
		mi_privilege_type.name AS privilege_type_name

		FROM privilege

		LEFT JOIN mi_brand
		ON mi_brand.brand_id = privilege.bran_BrandID

		LEFT JOIN mi_privilege_type
		ON privilege.prty_PrivilegeTypeID = mi_privilege_type.privilege_type_id

		WHERE 1

		'.$where_search.'
		'.$where_brand.' 

		ORDER BY CASE 
			WHEN privilege.priv_Deleted = "" THEN 1
	        WHEN privilege.priv_Deleted = "T" THEN 2 END ASC,
			privilege.priv_Status ASC, 
			privilege.priv_UpdatedDate DESC';

if($Act == 'approve' && $id != '') {

	# APPROVE IMAGE

	$sql = '';

	$sql .= 'SELECT priv_ImageNew, priv_Image FROM privilege WHERE priv_PrivilegeID ="'.$id.'"';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);


	if($approve == 'unapprove') {

 		# UNAPPROVE

		// if ($axRow['priv_ImageNew']!="") {

		// 	unlink_file($oDB,'privilege','priv_ImageNew','priv_PrivilegeID',$id,$path_upload_privilege,$axRow['priv_ImageNew']);

		// 	$do_sql_upload = "UPDATE privilege 

		// 						SET priv_ImageNew='',

		// 						priv_Status='Pending',

		// 						priv_UpdatedDate='".$time_insert."' 

		// 						WHERE priv_PrivilegeID='".$id."' ";

		// } else if ($axRow['priv_Image']!=""){

		// 	unlink_file($oDB,'privilege','priv_Image','priv_PrivilegeID',$id,$path_upload_privilege,$axRow['priv_Image']);

		// 	$do_sql_upload = "UPDATE privilege 

		// 						SET priv_Image='',

		// 						priv_Status='Pending',

		// 						priv_UpdatedDate='".$time_insert."'  

		// 						WHERE priv_PrivilegeID='".$id."' ";

		// }

		unlink_file($oDB,'privilege','priv_Image','priv_PrivilegeID',$id,$path_upload_privilege,$axRow['priv_Image']);

		$do_sql_upload = "UPDATE privilege 
							SET priv_Image='',
							priv_Approve='',
							priv_Status='Pending',
							priv_UpdatedDate='".$time_insert."',
							priv_UpdatedBy='".$_SESSION['UID']."'  
							WHERE priv_PrivilegeID='".$id."' ";

 		$oDB->QueryOne($do_sql_upload);
 	}

		
	if ($approve == 'approve') {

		# APPROVE

		// if ($axRow['priv_Image']!="") {

		// 	unlink_file($oDB,'privilege','priv_Image','priv_PrivilegeID',$id,$path_upload_privilege,$axRow['priv_Image']);

		// 	$do_sql_upload = "UPDATE privilege 

		// 						SET priv_Image='".$axRow['priv_ImageNew']."', 

		// 						priv_ImageNew='',

		// 						priv_UpdatedDate='".$time_insert."' 

		// 						WHERE priv_PrivilegeID='".$id."'";

		// } else {

		// 	$do_sql_upload = "UPDATE privilege 

		// 						SET priv_Image='".$axRow['priv_ImageNew']."', 

		// 						priv_ImageNew='',

		// 						priv_Status='Pending',

		// 						priv_UpdatedDate='".$time_insert."' 

		// 						WHERE priv_PrivilegeID='".$id."'";

		// }

		$do_sql_upload = "UPDATE privilege 
							SET priv_Approve='T',
							priv_UpdatedDate='".$time_insert."',
							priv_UpdatedBy='".$_SESSION['UID']."'
							WHERE priv_PrivilegeID='".$id."' ";

 		$oDB->QueryOne($do_sql_upload);
	}

	echo '<script> window.location.href="privilege.php"; </script>';


} else if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE privilege 
	 					SET priv_Status='Pending',
	 						priv_UpdatedDate='".$time_insert."' 
	 					WHERE priv_PrivilegeID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="privilege.php";</script>';

} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE privilege 
	 					SET priv_Status='Active',
	 						priv_UpdatedDate='".$time_insert."' 
	 					WHERE priv_PrivilegeID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="privilege.php";</script>';

} else if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT priv_Deleted FROM privilege WHERE priv_PrivilegeID ="'.$id.'"';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

		
	if($axRow['priv_Deleted']=='') {

 		$do_sql_priv = "UPDATE privilege
 							SET priv_Deleted='T', 
 							priv_Status='Pending',
 							priv_Motivation='None',
 							priv_MotivationID=0,
 							priv_UpdatedDate='".$time_insert."' 
 							WHERE priv_PrivilegeID='".$id."'";

 		$do_sql_regis = "UPDATE mi_card_register
 							SET status='1', 
 							date_update='".$time_insert."' 
 							WHERE privilege_id='".$id."'";

 		$do_sql_point = "UPDATE motivation_plan_point
 							SET mopp_PrivilegeType='None', 
 							mopp_PrivilegeID='0' 
 							WHERE mopp_PrivilegeID='".$id."'
 							AND mopp_PrivilegeType='Privilege'";

 		$do_sql_stamp = "UPDATE motivation_plan_stamp
 							SET mops_PrivilegeType='None', 
 							mops_PrivilegeID='0' 
 							WHERE mops_PrivilegeID='".$id."'
 							AND mops_PrivilegeType='Privilege'";

 	} else if ($axRow['priv_Deleted']=='T') {

		$do_sql_priv = "UPDATE privilege
 							SET priv_Deleted='', 
 							priv_Status='Pending',
 							priv_UpdatedDate='".$time_insert."' 
 							WHERE priv_PrivilegeID='".$id."'";

 		$do_sql_regis = "";
 		$do_sql_point = "";
 		$do_sql_stamp = "";
	}


 	$oDB->QueryOne($do_sql_priv);
 	$oDB->QueryOne($do_sql_regis);
 	$oDB->QueryOne($do_sql_point);
 	$oDB->QueryOne($do_sql_stamp);

 	echo '<script>window.location.href="privilege.php";</script>';

} else if($Act=='xls'){

	require_once '../../lib/PHPExcel/Classes/PHPExcel.php';

	$objPHPExcel = new PHPExcel();

	$char = 'A';

	$chars = $char;

	$row_start = 1;

	$reportName = 'Privilege Report';

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
					->setCellValue($chars++.$row_start, 'Privilege')
					->setCellValue($chars++.$row_start, 'Motivation Plans')
					->setCellValue($chars++.$row_start, 'Type')
					->setCellValue($chars++.$row_start, 'Description')
					->setCellValue($chars++.$row_start, 'Special Period Type')
					->setCellValue($chars++.$row_start, 'Start Date')
					->setCellValue($chars++.$row_start, 'End Date')
					->setCellValue($chars++.$row_start, 'Limit Use')
					->setCellValue($chars++.$row_start, 'Track Like')
					->setCellValue($chars++.$row_start, 'Track Review')
					->setCellValue($chars++.$row_start, 'Track Request')
					->setCellValue($chars++.$row_start, 'Track Share')
					->setCellValue($chars++.$row_start, 'Cost')
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

		if($axRow['priv_Deleted']=='') { $axRow['priv_Deleted']="No"; }
		else if ($axRow['priv_Deleted']=='T') { $axRow['priv_Deleted']="Yes"; }

		if ($axRow['priv_SpecialPeriodType'] == '0') { $special_period_type = ''; }
		else {	

			$special_period_type = 'SELECT name FROM mi_master WHERE type="special_period_type" AND value="'.$axRow['priv_SpecialPeriodType'].'"';
			$special_period_type = $oDB->QueryOne($special_period_type);
		}

		if ($axRow['priv_StartDateSpecial'] == '0000-00-00') { $axRow['priv_StartDateSpecial'] = ''; }
		else { $axRow['priv_StartDateSpecial'] = DateOnly($axRow['priv_StartDateSpecial']); }

		if ($axRow['priv_EndDateSpecial'] == '0000-00-00') { $axRow['priv_EndDateSpecial'] = ''; }
		else { $axRow['priv_EndDateSpecial'] = DateOnly($axRow['priv_EndDateSpecial']); }

		if ($axRow['priv_LimitUse'] == 'T') { $axRow['priv_LimitUse'] = 'One Time Per '.$axRow['priv_OneTimePer'];	}
		else { $axRow['priv_LimitUse'] = ''; }

		if ($axRow['priv_TrackLike'] == 'T') { $axRow['priv_TrackLike'] = 'Yes'; } 
		else { $axRow['priv_TrackLike'] = 'No';	}

		if ($axRow['priv_TrackReview'] == 'T') { $axRow['priv_TrackReview'] = 'Yes'; } 
		else { $axRow['priv_TrackReview'] = 'No';	}

		if ($axRow['priv_TrackRequest'] == 'T') { $axRow['priv_TrackRequest'] = 'Yes'; } 
		else { $axRow['priv_TrackRequest'] = 'No';	}

		if ($axRow['priv_TrackShare'] == 'T') { $axRow['priv_TrackShare'] = 'Yes'; } 
		else { $axRow['priv_TrackShare'] = 'No';	}

		if ($axRow['priv_Motivation'] == 'Point') { 

			$plan_sql = "SELECT mopp_Name, mopp_PointQty, mopp_UseAmount FROM motivation_plan_point WHERE mopp_MotivationPointID='".$axRow['priv_MotivationID']."'";

			$get_point = $oDB->Query($plan_sql);

			$point = $get_point->FetchRow(DBI_ASSOC);

			$motivation_plan = $point['mopp_Name'].' ('.number_format($point['mopp_UseAmount'],2).' ฿ / '.$point['mopp_PointQty'].' Point)';

		} else if ($axRow['priv_Motivation'] == 'Stamp') {

			$plan_sql = "SELECT mops_Name, mops_StampQty, mops_CollectionTypeID FROM motivation_plan_stamp WHERE mops_MotivationStampID='".$axRow['priv_MotivationID']."'";

			$get_stamp = $oDB->Query($plan_sql);
			$stamp = $get_stamp->FetchRow(DBI_ASSOC);

			$icon_sql = "SELECT coty_Name FROM collection_type WHERE coty_CollectionTypeID = ".$stamp['mops_CollectionTypeID'];
			$icon = $oDB->QueryOne($icon_sql);

			$motivation_plan = $stamp['mops_Name'].' (1 Times / '.$stamp['mops_StampQty'].' '.$icon.')';

		} else { $motivation_plan = 'None'; }

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

		$objWorkSheet->setCellValue($chars++ . $row_start, $axRow['brand_name']);
		$objWorkSheet->setCellValue($chars++ . $row_start, $axRow['priv_Name']);
		$objWorkSheet->setCellValue($chars++ . $row_start, $motivation_plan);
		$objWorkSheet->setCellValue($chars++ . $row_start, $axRow['privilege_type_name']);
		$objWorkSheet->setCellValue($chars++ . $row_start, $axRow['priv_Description']);
		$objWorkSheet->setCellValue($chars++ . $row_start, $axRow['special_period_type']);
		$objWorkSheet->setCellValue($chars++ . $row_start, $axRow['priv_StartDateSpecial']);
		$objWorkSheet->setCellValue($chars++ . $row_start, $axRow['priv_EndDateSpecial']);
		$objWorkSheet->setCellValue($chars++ . $row_start, $axRow['priv_LimitUse']);
		$objWorkSheet->setCellValue($chars++ . $row_start, $axRow['priv_TrackLike']);
		$objWorkSheet->setCellValue($chars++ . $row_start, $axRow['priv_TrackReview']);
		$objWorkSheet->setCellValue($chars++ . $row_start, $axRow['priv_TrackRequest']);
		$objWorkSheet->setCellValue($chars++ . $row_start, $axRow['priv_TrackShare']);
		$objWorkSheet->setCellValue($chars++ . $row_start, number_format($axRow['priv_Cost'],2).' ฿');
		$objWorkSheet->setCellValue($chars++ . $row_start, $axRow['priv_Condition']);
		$objWorkSheet->setCellValue($chars++ . $row_start, $axRow['priv_Exception']);
		$objWorkSheet->setCellValue($chars++ . $row_start, $axRow['priv_HowToUse']);
		$objWorkSheet->setCellValue($chars++ . $row_start, $axRow['priv_Note']);
		$objWorkSheet->setCellValue($chars++ . $row_start, $axRow['category_name']);
		$objWorkSheet->setCellValue($chars++ . $row_start, $axRow['product_name']);
		$objWorkSheet->setCellValue($chars++ . $row_start, $axRow['priv_Status']);
		$objWorkSheet->setCellValue($chars++ . $row_start, DateTime($axRow['priv_UpdatedDate']));
		$objWorkSheet->setCellValue($chars . $row_start, $axRow['priv_Deleted']);

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

		if ($axRow['priv_Approve']=='' && $axRow['priv_Image']=='') {

			$image_status = '<span class="glyphicon glyphicon-exclamation-sign" style="color:#f0ad4e;font-size:20px"></span>';

			$button_approve = '';

            $button_unapprove = '';

		} else if ($axRow['priv_Approve']=='T' && $axRow['priv_Image']) {

			$image_status = '';

			$button_approve = '';

            $button_unapprove = '<a style="cursor:pointer" href="privilege.php?act=approve&approve=unapprove&id='.$axRow['priv_PrivilegeID'].'">
                                <button type="button" class="btn btn-default btn-sm">
                                <span class="glyphicon glyphicon-remove-sign" aria-hidden="true" style="color:red"></span> Unapprove</button></a>';
		} else {

			$image_status = '';

			$button_approve = '<a style="cursor:pointer" href="privilege.php?act=approve&approve=approve&id='.$axRow['priv_PrivilegeID'].'">
                                <button type="button" class="btn btn-default btn-sm">
                                <span class="glyphicon glyphicon-ok-sign" aria-hidden="true" style="color:green"></span> Approve</button></a>';

            $button_unapprove = '<a style="cursor:pointer" href="privilege.php?act=approve&approve=unapprove&id='.$axRow['priv_PrivilegeID'].'">
                                <button type="button" class="btn btn-default btn-sm">
                                <span class="glyphicon glyphicon-remove-sign" aria-hidden="true" style="color:red"></span> Unapprove</button></a>';
		}


		# STATUS

		$status = '';

		if($axRow['priv_Deleted']=='T'){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

		} else {

			if($axRow['priv_Status']=='Active'){

				if ($_SESSION['role_action']['privilege']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'privilege.php?act=active&id='.$axRow['priv_PrivilegeID'].'\'">
		                    <option class="status_default" value="'.$axRow['priv_PrivilegeID'].'" selected>On</option>
		                    <option class="status_default">Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';
		        }

			} else {

				if ($_SESSION['role_action']['privilege']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_pending" name="pending_status" onchange="window.location.href=\'privilege.php?act=pending&id='.$axRow['priv_PrivilegeID'].'\'">
		                    <option class="status_default">On</option>
		                    <option class="status_default" value="'.$axRow['priv_PrivilegeID'].'" selected>Off</option>
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


		# PRIVILEGE IMAGE

		if($axRow['priv_ImageNew']!=''){

			$priv_image = '<img src="../../upload/'.$axRow['priv_ImagePath'].$axRow['priv_ImageNew'].'" class="image_border" width="128" height="80"/>';

			$priv_view = '<img src="../../upload/'.$axRow['priv_ImagePath'].$axRow['priv_ImageNew'].'" class="image_border" width="136" height="85"/>';

			$priv_data = '<img src="../../upload/'.$axRow['priv_ImagePath'].$axRow['priv_ImageNew'].'" class="image_border" width="240" height="150"/>';

		} else {

			if($axRow['priv_Image']!=''){

				$priv_image = '<img src="../../upload/'.$axRow['priv_ImagePath'].$axRow['priv_Image'].'" class="image_border" width="128" height="80"/>';

				$priv_view = '<img src="../../upload/'.$axRow['priv_ImagePath'].$axRow['priv_Image'].'" class="image_border" width="136" height="85"/>';

				$priv_data = '<img src="../../upload/'.$axRow['priv_ImagePath'].$axRow['priv_Image'].'" class="image_border" width="240" height="150"/>';

			} else {

				$priv_image = '<img src="../../images/card_privilege.jpg" width="128" height="80"/>';

				$priv_view = '<img src="../../images/card_privilege.jpg" width="136" height="85"/>';

				$priv_data = '<img src="../../images/card_privilege.jpg" width="240" height="150"/>';
			}
		}


		# VIEW

			# DATA

			if ($axRow['priv_Description'] == '') { $axRow['priv_Description'] = '-';	}

			if ($axRow['priv_Condition'] == '') { $axRow['priv_Condition'] = '-';	}

			if ($axRow['priv_Exception'] == '') { $axRow['priv_Exception'] = '-';	}

			if ($axRow['priv_HowToUse'] == '') { $axRow['priv_HowToUse'] = '-';	}

			if ($axRow['priv_Note'] == '') { $axRow['priv_Note'] = '-';	}

			if ($axRow['priv_SpecialPeriodType'] == '0') { $special_period_type = '-';	}

			else {	

				$special_period_type = 'SELECT name FROM mi_master WHERE type="special_period_type" AND value="'.$axRow['priv_SpecialPeriodType'].'"';

				$special_period_type = $oDB->QueryOne($special_period_type);
			}

			if ($axRow['priv_StartDateSpecial'] == '0000-00-00') { $axRow['priv_StartDateSpecial'] = '-';	}

			if ($axRow['priv_EndDateSpecial'] == '0000-00-00') { $axRow['priv_EndDateSpecial'] = '-';	}

			if ($axRow['priv_LimitUse'] == 'T') { $axRow['priv_LimitUse'] = 'One Time Per '.$axRow['priv_OneTimePer'];	}
			else {	 $axRow['priv_LimitUse'] = '-';	}

			if ($axRow['priv_TrackLike'] == 'T') { 

				$axRow['priv_TrackLike'] = '<span class="glyphicon glyphicon-check"></span> Like';	

			} else {	$axRow['priv_TrackLike'] = '<span class="glyphicon glyphicon-unchecked"></span> Like';	}

			if ($axRow['priv_TrackReview'] == 'T') { 

				$axRow['priv_TrackReview'] = '<span class="glyphicon glyphicon-check"></span> Review';	

			} else {	$axRow['priv_TrackReview'] = '<span class="glyphicon glyphicon-unchecked"></span> Review';	}

			if ($axRow['priv_TrackRequest'] == 'T') { 

				$axRow['priv_TrackRequest'] = '<span class="glyphicon glyphicon-check"></span> Request';	

			} else {	$axRow['priv_TrackRequest'] = '<span class="glyphicon glyphicon-unchecked"></span> Request';	}

			if ($axRow['priv_TrackShare'] == 'T') { 

				$axRow['priv_TrackShare'] = '<span class="glyphicon glyphicon-check"></span> Share';	

			} else {	$axRow['priv_TrackShare'] = '<span class="glyphicon glyphicon-unchecked"></span> Share';	}

			if ($axRow['priv_Hidden'] == 'No') { 

				$axRow['priv_Hidden'] = '<span class="glyphicon glyphicon-eye-open"></span>';	

			} else {	$axRow['priv_Hidden'] = '<span class="glyphicon glyphicon-eye-close"></span>';	}


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

			if ($axRow['priv_Motivation'] == 'Point') { 

				$icon_sql = "SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID = 3";

				$icon = $oDB->QueryOne($icon_sql);

				$icon ='<img src="../../upload/collection_upload/'.$icon.'" width="12px" height="12px" style="margin-bottom:3px">';

				$plan_sql = "SELECT mopp_Name, mopp_PointQty, mopp_UseAmount FROM motivation_plan_point WHERE mopp_MotivationPointID='".$axRow['priv_MotivationID']."'";

				$get_point = $oDB->Query($plan_sql);

				$point = $get_point->FetchRow(DBI_ASSOC);

				$motivation_plan = $point['mopp_Name'].' &nbsp;('.number_format($point['mopp_UseAmount'],2).' ฿ / '.$point['mopp_PointQty'].' '.$icon.')';

			} else if ($axRow['priv_Motivation'] == 'Stamp') {

				$plan_sql = "SELECT mops_Name, mops_StampQty, mops_CollectionTypeID FROM motivation_plan_stamp WHERE mops_MotivationStampID='".$axRow['priv_MotivationID']."'";

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
							WHERE custom_form_privilege.cufo_PrivilegeID="'.$axRow['priv_PrivilegeID'].'"
							AND custom_form_privilege.cufo_Type="Privilege"
							AND custom_form_privilege.cufo_Deleted=""';

			$custom_field = $oDB->Query($sql_field);

			$check_field = $oDB->QueryOne($sql_field);

			$custom = '';

			if ($check_field) {

				while ($cufi_Name = $custom_field->FetchRow(DBI_ASSOC)){

					$custom .= $cufi_Name['cufi_Name']."<br>";
				}

			} else { $custom .= "-"; }


		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['priv_PrivilegeID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
				<div class="modal fade" id="View'.$axRow['priv_PrivilegeID'].'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
					<div class="modal-dialog" role="document" style="width:60%">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px">'.$axRow['priv_Hidden'].' &nbsp; '.$axRow['priv_Name'].'</b></span>';

		if($_SESSION['user_type_id_ses']==1){

			$view .= '	        <span style="float:right">'.$button_approve.$button_unapprove.'</span>';
		}

		$view .= '		        <hr>
						        <center>
						        	'.$logo_view.' '.$priv_data.'<br><br>
						        	<span style="font-size:12px">
					                <ul id="tapMenu" class="nav nav-tabs">
					                    <li class="active">
					                    	<a data-toggle="tab" href="#basic'.$axRow['priv_PrivilegeID'].'">
					                    	<center><b>Basic</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#special'.$axRow['priv_PrivilegeID'].'">
					                    	<center><b>Special</b></center></a>
					                   	</li>
					                    <li>
					                    	<a data-toggle="tab" href="#member'.$axRow['priv_PrivilegeID'].'">
					                    	<center><b>Member</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#performance'.$axRow['priv_PrivilegeID'].'">
					                    	<center><b>Performance</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#financial'.$axRow['priv_PrivilegeID'].'">
					                    	<center><b>Financial</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#note'.$axRow['priv_PrivilegeID'].'">
					                    	<center><b>Note</b></center></a>
					                    </li>';

		if($_SESSION['user_type_id_ses']==1){

			$view .= '					<li>
					                    	<a data-toggle="tab" href="#product'.$axRow['priv_PrivilegeID'].'">
					                    	<center><b>Product</b></center></a>
					                    </li>';
		}

		$view .= '	                    <li style="width:12.5%">
					                    	<a data-toggle="tab" href="#custom'.$axRow['priv_PrivilegeID'].'">
					                    	<center><b>Custom</b></center></a>
					                    </li>
					                </ul>
					                </span>
					                <div class="tab-content">
					                    <div id="basic'.$axRow['priv_PrivilegeID'].'" class="tab-pane active"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Motivation Plans</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$motivation_plan.'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Privilege Type</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['privilege_type_name'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Description</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['priv_Description'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="special'.$axRow['priv_PrivilegeID'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Special Period Type</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$special_period_type.'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">Start Date</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['priv_StartDateSpecial'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">End Date</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['priv_EndDateSpecial'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="member'.$axRow['priv_PrivilegeID'].'" class="tab-pane"><br>
					                    	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Limit Use</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['priv_LimitUse'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="performance'.$axRow['priv_PrivilegeID'].'" class="tab-pane"><br>
					                    	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" valign="top" width="45%">Track</td>
								        			<td style="text-align:center" valign="top" width="5%">:</td>
								        			<td>'.$axRow['priv_TrackLike'].'<br>
								        				'.$axRow['priv_TrackReview'].'<br>
								        				'.$axRow['priv_TrackRequest'].'<br>
								        				'.$axRow['priv_TrackShare'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="financial'.$axRow['priv_PrivilegeID'].'" class="tab-pane"><br>
					                    	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Cost</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['priv_Cost'].' ฿</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="note'.$axRow['priv_PrivilegeID'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" valign="top" width="45%">Condition</td>
								        			<td style="text-align:center" width="5%" valign="top">:</td>
								        			<td>'.$axRow['priv_Condition'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Exception</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['priv_Exception'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">How To Use</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['priv_HowToUse'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Note</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['priv_Note'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="product'.$axRow['priv_PrivilegeID'].'" class="tab-pane"><br>
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
					                    <div id="custom'.$axRow['priv_PrivilegeID'].'" class="tab-pane"><br>
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

			if ($_SESSION['role_action']['privilege']['edit'] == 1) {		    

				$view .= '       <a href="privilege_create.php?act=edit&id='.$axRow['priv_PrivilegeID'].'">
						        <button type="button" class="btn btn-default btn-sm">Edit</button></a>';
			}

				$view .= '      <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';


		# DELETED

		if($axRow['priv_Deleted']=='') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['priv_PrivilegeID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>
				<div class="modal fade" id="Deleted'.$axRow['priv_PrivilegeID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="140px" style="text-align:center" valign="top">'.$priv_view.'</td>
						        	<td>
								        <p style="font-size:12px;padding-left:10px;">
								        	<b>"'.$axRow['priv_Name'].'"</b><br>
								            By clicking the <b>"Inactive"</b> button to:<br>
								            &nbsp; &nbsp;- Inactive this privilege<br>
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="privilege.php?act=delete&id='.$axRow['priv_PrivilegeID'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		} else if ($axRow['priv_Deleted']=='T') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['priv_PrivilegeID'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>
				<div class="modal fade" id="Deleted'.$axRow['priv_PrivilegeID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="140px" style="text-align:center" valign="top">'.$priv_view.'</td>
						        	<td>
								        <p style="font-size:12px;padding-left:10px;">
								        	<b>"'.$axRow['priv_Name'].'"</b><br>
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
						    	<a href="privilege.php?act=delete&id='.$axRow['priv_PrivilegeID'].'">
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
							<td style="text-align:center">'.$priv_image.'</td>
							<td >'.$axRow['priv_Name'].'</td>
							<td >'.$axRow['privilege_type_name'].'</td>
							<td style="text-align:center">'.$status.'</td>
							<td >'.DateTime($axRow['priv_UpdatedDate']).'</td>';

		if ($_SESSION['role_action']['privilege']['view'] == 1) {

			$data_table .=	'<td style="text-align:center">'.$view.'</td>';
		}

		if ($_SESSION['role_action']['privilege']['delete'] == 1) {

			$data_table .= 	'<td style="text-align:center">'.$deleted.'</td>';
		}

		$data_table .=	'</tr>';
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

$oTmp->assign('is_menu', 'is_privilege');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_privilege', 'in');

$oTmp->assign('content_file', 'privilege/privilege.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
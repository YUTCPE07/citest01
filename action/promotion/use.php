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


if ($_SESSION['role_action']['promotion']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");
$Act = $_REQUEST['act'];
$id = $_REQUEST['id'];

$where_brand = '';


if ($_SESSION['user_type_id_ses']>1 ) {

	$where_brand = ' AND hilight_coupon.bran_BrandID = "'.$_SESSION['user_brand_id'].'" AND hilight_coupon.coup_Deleted=""';
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
		hilight_coupon.*,
		mi_brand.name AS brand_name,
		mi_brand.logo_image AS brand_logo,
		mi_brand.path_logo,
		mi_privilege_type.name AS privilege_type_name

		FROM hilight_coupon

		INNER JOIN mi_brand
		ON mi_brand.brand_id = hilight_coupon.bran_BrandID

		LEFT JOIN mi_privilege_type
		ON hilight_coupon.prty_PrivilegeTypeID = mi_privilege_type.privilege_type_id

		WHERE  hilight_coupon.coup_Type = "Use"

		'.$where_search.'
		'.$where_brand.' 

		ORDER BY CASE 
			WHEN hilight_coupon.coup_Deleted = "" THEN 1
	        WHEN hilight_coupon.coup_Deleted = "T" THEN 2 END ASC,
			hilight_coupon.coup_Status ASC, 
			hilight_coupon.coup_UpdatedDate DESC';


if($Act == 'variety_category' && $id != '') {

	# UPDATE VARIETY

	$variety_id = $_REQUEST['variety_id'];

	$do_sql_variety = "UPDATE hilight_coupon 
	 					SET vaca_VarietyCategoryID='".$variety_id."',
	 						coup_UpdatedDate='".$time_insert."' 
	 					WHERE coup_CouponID='".$id."'";

 	$oDB->QueryOne($do_sql_variety);

 	echo '<script>window.location.href="use.php";</script>';


} else if($Act == 'display_data' && $id != '') {

	# UPDATE DISPLAY DATA

	$display_id = $_REQUEST['display_id'];

	$do_sql_display = "UPDATE hilight_coupon 
	 					SET coup_DisplayData='".$display_id."',
	 						coup_UpdatedDate='".$time_insert."' 
	 					WHERE coup_CouponID='".$id."'";

 	$oDB->QueryOne($do_sql_display);

 	echo '<script>window.location.href="use.php";</script>';


} else if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE hilight_coupon 
	 					SET coup_Status='Pending',
	 						coup_UpdatedDate='".$time_insert."' 
	 					WHERE coup_CouponID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="use.php";</script>';



} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE hilight_coupon 
	 					SET coup_Status='Active',
	 						coup_UpdatedDate='".$time_insert."' 
	 					WHERE coup_CouponID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="use.php";</script>';



} else if($Act == 'send' && $id != '') {

	# UPDATE SEND

	$do_sql_send = "UPDATE hilight_coupon 
	 					SET coup_SendEmail='',
	 						coup_UpdatedDate='".$time_insert."' 
	 					WHERE coup_CouponID='".$id."'";

 	$oDB->QueryOne($do_sql_send);

 	echo '<script>window.location.href="use.php";</script>';



} else if($Act == 'no_send' && $id != '') {

	# UPDATE NO SEND

	$do_sql_send = "UPDATE hilight_coupon 
	 					SET coup_SendEmail='T',
	 						coup_UpdatedDate='".$time_insert."' 
	 					WHERE coup_CouponID='".$id."'";

 	$oDB->QueryOne($do_sql_send);

 	echo '<script>window.location.href="use.php";</script>';



} else if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT coup_Deleted FROM hilight_coupon WHERE coup_CouponID ="'.$id.'"';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

		
	if($axRow['coup_Deleted']=='') {

 		$do_sql_coup = "UPDATE hilight_coupon
 							SET coup_Deleted='T', 
 							coup_Status='Pending',
 							coup_Motivation='None',
 							coup_MotivationID='0',
 							coup_UpdatedDate='".$time_insert."' 
 							WHERE coup_CouponID='".$id."'";

 		$do_sql_point = "UPDATE motivation_plan_point
 							SET mopp_PrivilegeType='None', 
 							mopp_PrivilegeID='0' 
 							WHERE mopp_PrivilegeID='".$id."'
 							AND mopp_PrivilegeType='HiCoupon'";

 		$do_sql_stamp = "UPDATE motivation_plan_stamp
 							SET mops_PrivilegeType='None', 
 							mops_PrivilegeID='0' 
 							WHERE mops_PrivilegeID='".$id."'
 							AND mops_PrivilegeType='HiCoupon'";

 	} else if ($axRow['coup_Deleted']=='T') {

		$do_sql_coup = "UPDATE hilight_coupon
 							SET coup_Deleted='', 
 							coup_Status='Pending',
 							coup_UpdatedDate='".$time_insert."' 
 							WHERE coup_CouponID='".$id."'";

 		$do_sql_point = "";
 		$do_sql_stamp = "";
	}

 	$oDB->QueryOne($do_sql_coup);
 	$oDB->QueryOne($do_sql_point);
 	$oDB->QueryOne($do_sql_stamp);

 	echo '<script>window.location.href="use.php";</script>';


} else if($Act=='xls'){

	require_once '../../lib/PHPExcel/Classes/PHPExcel.php';

	$objPHPExcel = new PHPExcel();

	$char = 'A';

	$chars = $char;

	$row_start = 5;

	$reportName = 'Coupon Report';

	$objPHPExcel->setActiveSheetIndex(0)

				->setCellValue('A1', 'Topics this report : '.$reportName )

				->setCellValue('A2', 'Issued this report  : '.$_SESSION['UNAME'].' / '.$_SESSION['FULLNAME'])

				->setCellValue('A3', 'Check out this report : '.$time_insert)

				->setCellValue($chars++.$row_start, 'No.')

				->setCellValue($chars++.$row_start, 'Brand')

				->setCellValue($chars++.$row_start, 'Coupon')

				->setCellValue($chars++.$row_start, 'Type')

				->setCellValue($chars++.$row_start, 'Status')

				->setCellValue($chars++.$row_start, 'Update Date')

				->setCellValue($chars.$row_start, 'Delete');

	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:'.$chars.'1');

	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:'.$chars.'2');

	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A3:'.$chars.'3');

	$i = 6;

	$oRes = $oDB->Query($sql);

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)) {

		if($axRow['coup_Deleted']=='') {	$axRow['coup_Deleted']="No";	}

		else if ($axRow['coup_Deleted']=='T') {	$axRow['coup_Deleted']="Yes";	}

		$chars = $char;

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $i, ($i-5));

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $i, $axRow['brand_name']);

		$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars++ . $i, $axRow['coup_Name']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $i, $axRow['privilege_type_name']);

		$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars++ . $i, $axRow['coup_Status']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $i, $axRow['coup_UpdatedDate']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars . $i, $axRow['coup_Deleted']);

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

} else {

	$oRes = $oDB->Query($sql);

	$i=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;

		# STATUS

		$status = '';

		if($axRow['coup_Deleted']=='T'){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

		} else {

			if($axRow['coup_Status']=='Active'){

				if ($_SESSION['role_action']['earn_attention']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'use.php?act=active&id='.$axRow['coup_CouponID'].'\'">
		                    <option class="status_default" value="'.$axRow['coup_CouponID'].'" selected>On</option>
		                    <option class="status_default">Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';
		        }

			} else {

				if ($_SESSION['role_action']['earn_attention']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_pending" name="pending_status" onchange="window.location.href=\'use.php?act=pending&id='.$axRow['coup_CouponID'].'\'">
		                    <option class="status_default">On</option>
		                    <option class="status_default" value="'.$axRow['coup_CouponID'].'" selected>Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
		        }
			}
		}


		# SEND EMAIL

		$send_email = '';

		if($axRow['coup_SendEmail']=='T'){

			if ($_SESSION['role_action']['earn_attention']['edit'] == 1) {

				$send_email = '<form id="myForm" method="POST">
						<select class="form-control text-md status_active" name="send_status" onchange="window.location.href=\'use.php?act=send&id='.$axRow['coup_CouponID'].'\'">
		                    <option class="status_default" value="'.$axRow['coup_CouponID'].'" selected>Yes</option>
		                    <option class="status_default">No</option>
		                </select>
		            </form>';

		    } else {

		        $send_email = '<button style="width:60px;" class="form-control text-md status_active">Yes</button>';
		    }

		} else {

			if ($_SESSION['role_action']['earn_attention']['edit'] == 1) {

				$send_email = '<form id="myForm" method="POST">
						<select class="form-control text-md status_inactive" name="no_status" onchange="window.location.href=\'use.php?act=no_send&id='.$axRow['coup_CouponID'].'\'">
		                    <option class="status_default" value="'.$axRow['coup_CouponID'].'" selected>No</option>
		                    <option class="status_default">Yes</option>
		                </select>
		            </form>';

		    } else {

		        $send_email = '<button style="width:60px;" class="form-control text-md status_inactive">No</button>';
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

		if($axRow['bran_BrandID']=='0'){

			$logo_brand = '<img src="../../images/mi_action_logo.png" class="image_border" width="60" height="60"/>';

			$logo_view = '<img src="../../images/mi_action_logo.png" class="image_border" width="150" height="150"/>';

			$axRow['brand_name'] = 'MemberIn';
		}


		# QR CODE 

		$qr_view = '<img src="../../upload/'.$axRow['coup_QrPath'].'QCH-'.str_pad($axRow['coup_CouponID'],4,"0",STR_PAD_LEFT).'-'.str_pad($axRow['bran_BrandID'],4,"0",STR_PAD_LEFT).'.png" width="150" height="150" class="image_border"/>';


		# COUPON IMAGE

		if($axRow['coup_Image']!=''){

			$coup_image = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_Image'].'" class="image_border" width="128" height="80"/>';

			$coup_view = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_Image'].'" class="image_border" width="136" height="85"/>';

			$coup_data = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_Image'].'" class="image_border" width="240" height="150"/>';

		} else {

			$coup_image = '<img src="../../images/card_privilege.jpg" width="128" height="80"/>';

			$coup_view = '<img src="../../images/card_privilege.jpg" width="136" height="85"/>';

			$coup_data = '<img src="../../images/card_privilege.jpg" width="240" height="150"/>';
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
						        	<td width="140px" style="text-align:center" valign="top">'.$coup_view.'</td>
						        	<td>
								        <p style="font-size:12px;padding-left:10px;">
								        	<b>"'.$axRow['coup_Name'].'"</b><br>
								            By clicking the <b>"Inactive"</b> button to:<br>
								            &nbsp; &nbsp;- Inactive this earn attention<br>
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="use.php?act=delete&id='.$axRow['coup_CouponID'].'">
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
						        	<td width="140px" style="text-align:center" valign="top">'.$coup_view.'</td>
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
						    	<a href="use.php?act=delete&id='.$axRow['coup_CouponID'].'">
						        <button type="button" class="btn btn-default btn-sm">Active</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		}


		# VARIETY CATEGORY

		$data_variety = '<option value="0">Not Specific</option>';

		$oRes_head = $oDB->Query("SELECT vc.vaca_VarietyCategoryID AS id, 
										vc.vaca_Name AS name
								FROM variety AS vr
								LEFT JOIN variety_category AS vc
								ON vr.vari_VarietyCategoryID = vc.vaca_VarietyCategoryID
								WHERE vc.vaca_Status=1 
								AND vc.vaca_Type='Promotion'
								AND vr.vari_Status='1'
								GROUP BY vc.vaca_VarietyCategoryID");

		if ($oRes_head) {
				
			while ($axRow_head = $oRes_head->FetchRow(DBI_ASSOC)) {

				$data_variety .= '<optgroup label="'.$axRow_head["name"].'"> ';

				$oRes_detail = $oDB->Query("SELECT vari_VarietyID AS id, 
											vari_Title AS name
											FROM variety
											WHERE vari_VarietyCategoryID='".$axRow_head["id"]."'
											AND vari_Status='1'");
				
				while ($axRow_detail = $oRes_detail->FetchRow(DBI_ASSOC)) {

					if ($axRow['vari_VarietyID']==$axRow_detail['id']) { $select = 'selected'; }
					else { $select = ''; }

					$data_variety .= '<option value="'.$axRow_detail["id"].'" '.$select.'>'.$axRow_detail["name"].'</option> ';
				}

				$data_variety .= '</optgroup> ';
			}
		}


		# VIEW

			# DATA

			if ($axRow['vaca_NameEn'] == '') { $axRow['vaca_NameEn'] = 'Not Specific';	}

			if ($axRow['coup_QtyPer'] == 'Not') { $axRow['coup_QtyPer'] = 'Not Specific'; }

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

			$branch_data = "";

			if ($axRow['brnc_BranchID']) {

				$token = strtok($axRow['brnc_BranchID'] , ",");

				$branch = array();

				$j = 0;

				while ($token !== false) {

	    			$branch[$j] =  $token;
	    			$token = strtok(",");
	    			$j++;
				}

				$arrlength = count($branch);

				for($x = 0; $x < $arrlength; $x++) {

					$sql_branch = 'SELECT name FROM mi_branch WHERE branch_id = "'.$branch[$x].'"';
					$name = $oDB->QueryOne($sql_branch);

					$branch_data .= '<tr>
										<td style="text-align:center" rowspan="2">'.$name.'</td>
										<td style="text-align:center" rowspan="2"><img src="../../upload/'.$axRow['coup_QrPath'].'QHC-'.str_pad($axRow['coup_CouponID'],4,"0",STR_PAD_LEFT).'-'.str_pad($branch[$x],4,"0",STR_PAD_LEFT).'.png" width="90" height="90" class="image_border"/></td>
										<td style="text-align:center"><a target="_blank" href="earn_attention_qrcode.php?id='.$axRow['coup_CouponID'].'&branch='.$branch[$x].'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-print" aria-hidden="true"></span>&nbsp; Print QRCode</button></a></a>
										</td>
									</tr>
									<tr>
										<td style="text-align:center">
											<a target="_blank" href="earn_attention_a4.php?id='.$axRow['coup_CouponID'].'&branch='.$branch[$x].'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-print" aria-hidden="true"></span>&nbsp; A4</button></a>&nbsp;
											<a target="_blank" href="earn_attention_a5.php?id='.$axRow['coup_CouponID'].'&branch='.$branch[$x].'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-print" aria-hidden="true"></span>&nbsp; A5</button></a>&nbsp;
											<a target="_blank" href="earn_attention_a6.php?id='.$axRow['coup_CouponID'].'&branch='.$branch[$x].'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-print" aria-hidden="true"></span>&nbsp; A6</button></a>
										</td>
									</tr>';
				}

			} else {

				$branch_data = '<tr><td colspan="3" style="text-align:center">No Branch Data</td></tr>';
			}

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

			} else { $axRow['coup_Repetition'] = '<tr>
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

			} else { $axRow['coup_RepetitionMember'] = '<tr>
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

			if ($axRow['coup_SpecialPeriodType'] == '0') { $special_period_type = '-';	}

			else {	

				$special_period_type = 'SELECT name FROM mi_master WHERE type="special_period_type" AND value="'.$axRow['coup_SpecialPeriodType'].'"';

				$special_period_type = $oDB->QueryOne($special_period_type);
			}

			if ($axRow['coup_Description'] == '') { $axRow['coup_Description'] = '-';	}

			if ($axRow['coup_Location'] == '') { $axRow['coup_Location'] = '-';	}

			if ($axRow['coup_Payment'] == 'F') { 

				$axRow['coup_Payment'] = '<span class="glyphicon glyphicon-unchecked"></span>';	

			} else {

				$axRow['coup_Payment'] = '<span class="glyphicon glyphicon-check"></span>';	}

			if ($axRow['coup_StartDateSell'] == '0000-00-00') { $axRow['coup_StartDateSell'] = '-';	}

				else { $axRow['coup_StartDateSell'] = DateTime($axRow['coup_StartDateSell']); }

			if ($axRow['coup_EndDateSell'] == '0000-00-00') { $axRow['coup_EndDateSell'] = '-';	}

				else { $axRow['coup_EndDateSell'] = DateTime($axRow['coup_EndDateSell']); }

			if ($axRow['coup_Condition'] == '') { $axRow['coup_Condition'] = '-';	}

			if ($axRow['coup_Exception'] == '') { $axRow['coup_Exception'] = '-';	}

			if ($axRow['coup_HowToUse'] == '') { $axRow['coup_HowToUse'] = '-';	}

			if ($axRow['coup_Participation'] == '') { $axRow['coup_Participation'] = '-';	}

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

			if ($axRow['coup_SendEmail'] == 'T') { 

				$axRow['coup_SendEmail'] = '<span class="glyphicon glyphicon-check"></span>';	

			} else {	$axRow['coup_SendEmail'] = '<span class="glyphicon glyphicon-unchecked"></span>';	}

			if ($axRow['coup_Hidden'] == 'No') { 

				$axRow['coup_Hidden'] = '<span class="glyphicon glyphicon-eye-open"></span>';	

			} else {	$axRow['coup_Hidden'] = '<span class="glyphicon glyphicon-eye-close"></span>';	}

			if ($axRow['coup_Website'] == '') { $axRow['coup_Website'] = '-';	}

			if ($axRow['coup_Facebook'] == '') { $axRow['coup_Facebook'] = '-';	}


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


			# INFORMATION REQUEST

			$data_request = '';

			$topic = array("Profile", "Contact");

			for ($k=0; $k <2 ; $k++) { 

				# TOPIC

				$data_request .= '<tr class="th_table">
									<td style="text-align:center" colspan="2"><b>'.$topic[$k].'</b></td>
								</tr>';

				# MASTER FIELD

				$field = 'SELECT * FROM master_field 
							WHERE mafi_Position="'.$topic[$k].'" 
							AND mafi_MasterFieldID IN (2,3,5,6,20,23)';

				$oRes_field = $oDB->Query($field);

				while ($master_field = $oRes_field->FetchRow(DBI_ASSOC)){

					$check = "";

					# CHECK DATA

					$data = 'SELECT hcre_Deleted FROM hilight_coupon_request 
								WHERE mafi_MasterFieldID="'.$master_field['mafi_MasterFieldID'].'" 
								AND coup_CouponID="'.$axRow['coup_CouponID'].'"';

					$check_data = $oDB->QueryOne($data);

					$id = 'SELECT hcre_HilightCouponRequestID FROM hilight_coupon_request 
								WHERE mafi_MasterFieldID="'.$master_field['mafi_MasterFieldID'].'" 
								AND coup_CouponID="'.$axRow['coup_CouponID'].'"';

					$check_id = $oDB->QueryOne($id);

					if ($check_data == "T") { $check = "glyphicon glyphicon-unchecked"; }

					else if ($check_id) { $check = "glyphicon glyphicon-check"; }

					else { $check = "glyphicon glyphicon-unchecked"; }

					$data_request .= '<tr>
										<td style="text-align:center" width="20%">
											<span class="'.$check.'"></span></td>
										<td style="text-align:center"><b>'.$master_field['mafi_NameEn'].'</b></td>
									</tr>';
				}
			}


			# BENEFITS

			$axRow['benefits'] = '';
			if ($axRow['coup_Benefits1']) { $axRow['benefits'] .= '1.'.$axRow['coup_Benefits1'].'<br>'; }
			if ($axRow['coup_Benefits2']) { $axRow['benefits'] .= '2.'.$axRow['coup_Benefits2'].'<br>'; }
			if ($axRow['coup_Benefits3']) { $axRow['benefits'] .= '3.'.$axRow['coup_Benefits3'].'<br>'; }
			if ($axRow['coup_Benefits4']) { $axRow['benefits'] .= '4.'.$axRow['coup_Benefits4'].'<br>'; }
			if ($axRow['coup_Benefits5']) { $axRow['benefits'] .= '5.'.$axRow['coup_Benefits5'].''; }
			if ($axRow['benefits']=='') { $axRow['benefits'] = '-'; }

			$axRow['differences'] = '';
			if ($axRow['coup_Differences1']) { $axRow['differences'] .= '1.'.$axRow['coup_Differences1'].'<br>'; }
			if ($axRow['coup_Differences2']) { $axRow['differences'] .= '2.'.$axRow['coup_Differences2'].'<br>'; }
			if ($axRow['coup_Differences3']) { $axRow['differences'] .= '3.'.$axRow['coup_Differences3'].'<br>'; }
			if ($axRow['coup_Differences4']) { $axRow['differences'] .= '4.'.$axRow['coup_Differences4'].'<br>'; }
			if ($axRow['coup_Differences5']) { $axRow['differences'] .= '5.'.$axRow['coup_Differences5'].''; }
			if ($axRow['differences']=='') { $axRow['differences'] = '-'; }

		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['coup_CouponID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
				<div class="modal fade" id="View'.$axRow['coup_CouponID'].'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
					<div class="modal-dialog" role="document" style="width:70%">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>'.$axRow['coup_Hidden'].' &nbsp; '.$axRow['coup_Name'].'</b></span>
						        <hr>
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
					                    	<center><b>Limitation</b></center></a>
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
					                    	<a data-toggle="tab" href="#note'.$axRow['coup_CouponID'].'">
					                    	<center><b>Information</b></center></a>
					                    </li>';

		if ($_SESSION['user_type_id_ses']==1) {

			$view .= ' 					<li>
					                    	<a data-toggle="tab" href="#request'.$axRow['coup_CouponID'].'">
					                    	<center><b>Info. Request</b></center></a>
					                    </li>';
		}

		$view .= '	                </ul>
					                </span>
					                <div class="tab-content">
					                    <div id="basic'.$axRow['coup_CouponID'].'" class="tab-pane active"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right">Send Email</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['coup_SendEmail'].'</td>
								        		</tr>
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
								        			<td>'.nl2br($axRow['coup_Description']).'</td>
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
								        		<tr height="35px">
								        			<td style="text-align:right"><img src="../../images/icon/web.png" width="25" height="25" alt="Website"></td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['coup_Website'].'</td>
								        		</tr>
								        		<tr height="35px">
								        			<td style="text-align:right"><img src="../../images/icon/facebook.png" width="25" height="25" alt="Facebook"></td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['coup_Facebook'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="repetition'.$axRow['coup_CouponID'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		'.$axRow['coup_Repetition'].'
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
								        	<a target="_blank" href="earn_attention_qrcode.php?id='.$axRow['coup_CouponID'].'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-print" aria-hidden="true"></span>&nbsp; Print All QRCodes</button></a><br>

								        	<a target="_blank" href="earn_attention_a4.php?id='.$axRow['coup_CouponID'].'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-print" aria-hidden="true"></span>&nbsp; Print All A4</button></a>
								        	<a target="_blank" href="earn_attention_a5.php?id='.$axRow['coup_CouponID'].'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-print" aria-hidden="true"></span>&nbsp; Print All A5</button></a>
								        	<a target="_blank" href="earn_attention_a6.php?id='.$axRow['coup_CouponID'].'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-print" aria-hidden="true"></span>&nbsp; Print All A6</button></a>
								        	<br><br>
								        	
								        	<div style="height:320px;overflow:auto;">
					                    	<table style="width:80%;" class="table table-striped table-bordered myPopup">
								        		<thead><tr class="th_table">
				                                    <td style="text-align:center"><b>Branch</b></td>
				                                    <td colspan="2" style="text-align:center"><b>Qr Code</b></td>
				                                </tr></thead>
				                                <tbody>'.$branch_data.'</tbody>
								        	</table>
								        	</div>
					                    </div>
					                    <div id="note'.$axRow['coup_CouponID'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" valign="top">Minimum Participation</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['coup_Participation'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Activity Duration</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['coup_ActivityDuration'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">How To Use</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.nl2br($axRow['coup_HowToUse']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top" width="45%">Condition</td>
								        			<td style="text-align:center" width="5%" valign="top">:</td>
								        			<td>'.nl2br($axRow['coup_Condition']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Additional Information</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.nl2br($axRow['coup_Note']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Exception</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.nl2br($axRow['coup_Exception']).'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Contact</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.nl2br($axRow['coup_Contact']).'</td>
								        		</tr>
								        	</table>
					                    </div>';

		if ($_SESSION['user_type_id_ses']==1) {

			$view .= '					<div id="request'.$axRow['coup_CouponID'].'" class="tab-pane"><br>
								        	<table class="table table-bordered" cellspacing="0" style="background-color:white;text-align:center;valign:center;width:50%">
								        		'.$data_request.'
								        	</table>
					                    </div>';
		}

		$view .= '					</div>
						        </center>
						    </div>
						    <div class="modal-footer">';

			if ($_SESSION['role_action']['earn_attention']['edit'] == 1) {		    

				$view .= '       <a href="use_create.php?act=edit&id='.$axRow['coup_CouponID'].'">
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
							<td >'.$axRow['coup_Name'].'</td>';

		if($_SESSION['user_type_id_ses']==1){

			$data_table .= '<td style="text-align:center">
								<form method="POST">
									<select class="form-control text-md" name="variety_id" onchange="VarietySelect(this.value,'.$axRow['coup_CouponID'].')">
										'.$data_variety.'
		                			</select>
		                		</form>
		            		</td>
							<td style="text-align:center">
								<form method="POST">
									<select class="form-control text-md" name="display_id" onchange="DisplaySelect(this.value,'.$axRow['coup_CouponID'].')">';

			if ($axRow['coup_DisplayData']=='Not Specific') { $select = 'selected'; }
			else { $select = ''; }

			$data_table .= '			<option value="Not Specific" '.$select.'>Not Specific</option>';

			if ($axRow['coup_DisplayData']=='Recommend') { $select = 'selected'; }
			else { $select = ''; }

			$data_table .= '			<option value="Recommend" '.$select.'>Recommend</option>';

			if ($axRow['coup_DisplayData']=='Recently') { $select = 'selected'; }
			else { $select = ''; }

			$data_table .= '			<option value="Recently" '.$select.'>Recently</option>';

			if ($axRow['coup_DisplayData']=='Most Active') { $select = 'selected'; }
			else { $select = ''; }

			$data_table .= '			<option value="Most Active" '.$select.'>Most Active</option>
		                			</select>
		            			</form>
		            		</td>';
		}

		$data_table .= '	<td style="text-align:center">'.$status.'</td>
							<td >'.DateTime($axRow['coup_UpdatedDate']).'</td>
							<td style="text-align:center">'.$send_email.'</td>';

		if ($_SESSION['role_action']['promotion']['view'] == 1) {

			$data_table .=	'<td style="text-align:center">'.$view.'</td>';
		}

		if ($_SESSION['role_action']['promotion']['delete'] == 1) {

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

$oTmp->assign('content_file', 'promotion/use.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>

<script type="text/javascript">
	
	function VarietySelect(variety_id,coupon_id) {

      window.location.href="use.php?act=variety_category&id="+coupon_id+"&variety_id="+variety_id;
	}
	
	function DisplaySelect(display_id,coupon_id) {

      window.location.href="use.php?act=display_data&id="+coupon_id+"&display_id="+display_id;
	}

</script>
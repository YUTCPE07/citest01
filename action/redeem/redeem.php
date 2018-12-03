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

if ($_SESSION['role_action']['redeems']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$path_upload_collection = $_SESSION['path_upload_collection'];


$where_brand = '';

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND reward_redeem.bran_BrandID = "'.$_SESSION['user_brand_id'].'"';
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

		reward_redeem.*,
		reward_redeem.rede_RewardRedeemID AS redeem_id,
		reward_redeem.brnc_BranchID AS branch_id,
		reward_ratio.*,
		reward.*,
		mi_brand.logo_image,
		mi_brand.path_logo,
		mi_brand.name as brand_name

	  	FROM reward_redeem

	  	LEFT JOIN reward_ratio
	  	ON reward_redeem.rede_RewardRedeemID = reward_ratio.rede_RewardRedeemID

	  	LEFT JOIN reward
	  	ON reward_redeem.rewa_RewardID = reward.rewa_RewardID

		LEFT JOIN mi_brand
		ON mi_brand.brand_id = reward_redeem.bran_BrandID 

		WHERE reward.bran_BrandID != "0"

		'.$where_search.'
		'.$where_brand.'

		GROUP BY reward_redeem.rede_RewardRedeemID

		ORDER BY CASE 
			WHEN reward_redeem.rede_Deleted = "" THEN 1
	        WHEN reward_redeem.rede_Deleted = "T" THEN 2 END ASC,
			reward_redeem.rede_Status ASC, 
			reward_redeem.rede_UpdatedDate DESC';


if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE reward_redeem 
	 					SET rede_Status='Pending',
	 						rede_UpdatedDate='".$time_insert."' 
	 					WHERE rede_RewardRedeemID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="redeem.php";</script>';


} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE reward_redeem 
	 					SET rede_Status='Active',
	 						rede_UpdatedDate='".$time_insert."' 
	 					WHERE rede_RewardRedeemID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="redeem.php";</script>';


} else if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT rede_Deleted FROM reward_redeem WHERE rede_RewardRedeemID ="'.$id.'"';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($axRow['rede_Deleted']=='') {

 		$do_sql_redeem = "UPDATE reward_redeem
 							SET rede_Deleted='T', 
 							rede_UpdatedDate='".$time_insert."' 
 							WHERE rede_RewardRedeemID='".$id."'";

 	} else if ($axRow['rede_Deleted']=='T') {

		$do_sql_redeem = "UPDATE reward_redeem
 							SET rede_Deleted='', 
 							rede_UpdatedDate='".$time_insert."' 
 							WHERE rede_RewardRedeemID='".$id."'";
	}

 	$oDB->QueryOne($do_sql_redeem);

 	echo '<script>window.location.href="redeem.php";</script>';



} else if($Act=='xls'){


	require_once '../../lib/PHPExcel/Classes/PHPExcel.php';

	$objPHPExcel = new PHPExcel();

	$char = 'A';

	$chars = $char;

	$row_start = 1;

	$reportName = 'Redeems Report';

	$objPHPExcel->setActiveSheetIndex(0)

				// ->setCellValue('A1', 'Topics this report : '.$reportName )

				// ->setCellValue('A2', 'Issued this report  : '.$_SESSION['UNAME'].' / '.$_SESSION['FULLNAME'])

				// ->setCellValue('A3', 'Check out this report : '.$time_insert)

				// ->setCellValue($chars++.$row_start, 'No.')

				->setCellValue($chars++.$row_start, 'Brand')

				->setCellValue($chars++.$row_start, 'Redeem')

				->setCellValue($chars++.$row_start, 'Rewards')

				->setCellValue($chars++.$row_start, 'Period')

				->setCellValue($chars++.$row_start, 'Start Date')

				->setCellValue($chars++.$row_start, 'End Date')

				->setCellValue($chars++.$row_start, 'Ratio')

				->setCellValue($chars++.$row_start, 'Rewards Qty')

				->setCellValue($chars++.$row_start, 'Auto Redeem')

				->setCellValue($chars++.$row_start, 'Description')

				->setCellValue($chars++.$row_start, 'Condition')

				->setCellValue($chars++.$row_start, 'Update Date')

				->setCellValue($chars.$row_start, 'Delete');

	// $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:'.$chars.'1');

	// $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:'.$chars.'2');

	// $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A3:'.$chars.'3');

	// $i = 6;

	$row_start++;

	$oRes = $oDB->Query($sql);

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)) {

		if($axRow['bran_BrandID']=='0'){ $axRow['brand_name'] = 'MemberIn'; }

		if($axRow['rede_Deleted']=='') {	$axRow['rede_Deleted']="No";	}

		else if ($axRow['rede_Deleted']=='T') {	$axRow['rede_Deleted']="Yes";	}

		if($axRow['rede_Time']=='T') {	

			$axRow['rede_Time'] = "Specific";

			$axRow['rede_StartDate'] = DateTime($axRow['rede_StartDate']);	

			$axRow['rede_EndDate'] = DateTime($axRow['rede_EndDate']);

		} else if ($axRow['rede_Time']=='F') {	

			$axRow['rede_Time'] = "Not Specific";

			$axRow['rede_StartDate'] = "";	

			$axRow['rede_EndDate'] = "";		
		}


		if ($axRow['coty_CollectionTypeID'] && $axRow['rera_RewardQty_Stamp']) {

			$sql_image = 'SELECT coty_Name FROM collection_type WHERE coty_CollectionTypeID="'.$axRow['coty_CollectionTypeID'].'"';

	 		$coty_Image = $oDB->QueryOne($sql_image);

			$ratio = 'Stamp ('.$coty_Image.') &nbsp; '.$axRow['rera_RewardQty_Stamp'].'';
		}

		if ($axRow['rera_RewardQty_Point']) {

			$ratio = 'Point '.$axRow['rera_RewardQty_Point'].'';
		}

		if($axRow['rede_AutoRedeem']=='F') {	$axRow['rede_AutoRedeem']="No";	}

		else if ($axRow['rede_AutoRedeem']=='T') {	$axRow['rede_AutoRedeem']="Yes";	}

		$chars = $char;

		// $objPHPExcel->getActiveSheet()->setCellValue($chars++ . $i, ($i-5));

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['brand_name']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['rede_Name']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['rewa_Name']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['rede_Time']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['rede_StartDate']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['rede_EmdDate']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $ratio);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['rera_RewardQty']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['rede_AutoRedeem']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['rede_Description']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['rede_Condition']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['rede_UpdatedDate']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars . $row_start, $axRow['rede_Deleted']);

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

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;



		# STATUS

		$status = '';

		if($axRow['rede_Deleted']=='T' || $axRow['rewa_Deleted']=='T'){

			$status = '<button style="width:80px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

		} else {

			if($axRow['rewa_Status']=='Active'){

				if($axRow['rede_Status']=='Active'){

					if ($_SESSION['role_action']['redeems']['edit'] == 1) {

						$status = '<form id="myForm" method="POST">
							<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'redeem.php?act=active&id='.$axRow['rede_RewardRedeemID'].'\'">
			                    <option class="status_default" value="'.$axRow['rede_RewardRedeemID'].'" selected>On</option>
			                    <option class="status_default">Off</option>
			                </select>
			            </form>';

			        } else {

			        	$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';
			        }

				} else {

					if ($_SESSION['role_action']['redeems']['edit'] == 1) {

						$status = '<form id="myForm" method="POST">
							<select class="form-control text-md status_pending" name="pending_status" onchange="window.location.href=\'redeem.php?act=pending&id='.$axRow['rede_RewardRedeemID'].'\'">
			                    <option class="status_default">On</option>
			                    <option class="status_default" value="'.$axRow['rede_RewardRedeemID'].'" selected>Off</option>
			                </select>
			            </form>';

			        } else {

			        	$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
			        }
				}

				// $status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';

			} else {

				$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
			}
		}


		# START - END DATE

		if ($axRow['rede_Time']=="F") {

			$axRow['rede_StartDate'] = '-'; 

			$axRow['rede_EndDate'] = '-'; 

		} else {

			if ($axRow['rede_StartDate']=='0000-00-00') {	$axRow['rede_StartDate'] = '-'; }

			else {	$axRow['rede_StartDate'] = DateOnly($axRow['rede_StartDate']); }

			if ($axRow['rede_EndDate']=='0000-00-00') {	$axRow['rede_EndDate'] = '-'; }

			else {	$axRow['rede_EndDate'] = DateOnly($axRow['rede_EndDate']); }
		}



		# LOGO

		if($axRow['logo_image']!=''){

			$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';

		} else {

			$logo_brand = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';
		}

		if($axRow['bran_BrandID']=='0'){

			$logo_brand = '<img src="../../images/mi_action_logo.png" class="image_border" width="60" height="60"/>';

			$axRow['brand_name'] = 'MemberIn';
		}


		# REWARDS IMAGE

		if ($axRow['rewa_Type']=='Card') {

			$sql_card = 'SELECT image, image_newupload,path_image FROM mi_card WHERE card_id="'.$axRow['card_CardID'].'"';

			$oRes_card = $oDB->Query($sql_card);

			$axRow_card = $oRes_card->FetchRow(DBI_ASSOC);

			# REWARDS IMAGE

			if($axRow_card['image']!=''){

				$rewards_img = '<img src="../../upload/'.$axRow_card['path_image'].$axRow_card['image'].'" class="img-rounded image_border" height="70"/>';

				$rewards_view = '<img src="../../upload/'.$axRow_card['path_image'].$axRow_card['image'].'" class="img-rounded image_border" width="150"/>';

			} else if($axRow_card['image_newupload']!=''){

				$rewards_img = '<img src="../../upload/'.$axRow_card['path_image'].$axRow_card['image_newupload'].'" class="img-rounded image_border" height="70"/>';

				$rewards_view = '<img src="../../upload/'.$axRow_card['path_image'].$axRow_card['image_newupload'].'" class="img-rounded image_border" width="150"/>';

			} else {

				$rewards_img = '<img src="../../images/400x400.png" class="img-rounded image_border" height="70"/>';

				$rewards_view = '<img src="../../images/400x400.png" class="img-rounded image_border" width="150"/>';
			}

		} else {

			if($axRow['rewa_Image']!=''){

				$rewards_img = '<img src="../../upload/'.$axRow['rewa_ImagePath'].$axRow['rewa_Image'].'" class="image_border" width="70" height="70"/>';

				$rewards_view = '<img src="../../upload/'.$axRow['rewa_ImagePath'].$axRow['rewa_Image'].'" class="image_border" width="150" height="150"/>';

			} else {

				$rewards_img = '<img src="../../images/400x400.png" class="image_border" width="70" height="70"/>';

				$rewards_view = '<img src="../../images/400x400.png" class="image_border" width="150" height="150"/>';
			}
		}



		# REDEEM QR

		$redeem_qr = '<img src="../../upload/'.$axRow['rede_QrPath'].$axRow['rede_Qr'].'.png" class="image_border" width="150" height="150"/>';



		# DELETED

		if($axRow['rede_Deleted']=='') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['rede_RewardRedeemID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>
				<div class="modal fade" id="Deleted'.$axRow['rede_RewardRedeemID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="140px" style="text-align:center" valign="top">'.$rewards_view.'</td>
						        	<td>
								        <p style="font-size:12px;padding-left:10px;">
								        	<b>"'.$axRow['rede_Name'].'"</b><br>
								            By clicking the <b>"Inactive"</b> button to:<br>
								            &nbsp; &nbsp;- Inactive this redeems<br>
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="redeem.php?act=delete&id='.$axRow['rede_RewardRedeemID'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		} else if ($axRow['rede_Deleted']=='T') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['rede_RewardRedeemID'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>
				<div class="modal fade" id="Deleted'.$axRow['rede_RewardRedeemID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="140px" style="text-align:center" valign="top">'.$rewards_view.'</td>
						        	<td>
								        <p style="font-size:12px;padding-left:10px;">
								        	<b>"'.$axRow['rede_Name'].'"</b><br>
								           	By clicking the <b>"Active"</b> button to:<br>
								            &nbsp; &nbsp;- Active this redeems<br>
								            &nbsp; &nbsp;- Change status to Pending
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="redeem.php?act=delete&id='.$axRow['rede_RewardRedeemID'].'">
						        <button type="button" class="btn btn-default btn-sm">Active</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		}



		# VIEW

			# DATA

			if ($axRow['rede_Description'] == '') { $axRow['rede_Description'] = '-';	}

			if ($axRow['rede_Condition'] == '') { $axRow['rede_Condition'] = '-';	}

			if ($axRow['rede_Time'] == 'F') { $axRow['rede_Time'] = 'Not Specific';	}
			else { $axRow['rede_Time'] = 'Specific';	}


			# BRANCH

			$branch_data = "";

			if ($axRow['branch_id']) {

				$token = strtok($axRow['branch_id'] , ",");

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
									<td style="text-align:center">'.$name.'</td>
									<td style="text-align:center"><img src="../../upload/'.$axRow['rede_QrPath'].'RDB-'.str_pad($axRow['rede_RewardRedeemID'],4,"0",STR_PAD_LEFT).'-'.str_pad($branch[$x],4,"0",STR_PAD_LEFT).'.png" width="80" height="80" class="image_border"/></td>
									<td style="text-align:center"><a target="_blank" href="redeem_qrcode.php?id='.$axRow['rede_RewardRedeemID'].'&branch='.$branch[$x].'">QRCode Link</td>
									</tr>';
				}

			} else {

				$branch_data = '<tr><td colspan="3" style="text-align:center">No Branch Data</td></tr>';
			}


			# CARD REWARD

			if ($axRow['rewa_Type']=='Card') {

				if ($axRow['rede_Expired']=='Original') { $axRow['rede_Expired'] = 'Use Original Expiration Date'; } 

				else {  $axRow['rede_Expired'] = 'Extend The Expiration Date'; }

				$expired = '<tr>
								<td style="text-align:right">Expiration Date</td>
								<td style="text-align:center">:</td>
								<td>'.$axRow['rede_Expired'].'</td>
							</tr>';

			} else { $expired = ''; }


			# AUTO REDEEMS

			if ($axRow['rede_AutoRedeem'] == 'T') { $axRow['rede_AutoRedeem'] = 'Yes'; }

			if ($axRow['rede_AutoRedeem'] == 'F') { $axRow['rede_AutoRedeem'] = 'No'; }


			# REDEEM LIMIT

			$limit = '';

			if ($axRow['rede_RedeemLimit'] == 'Unlimit') { 

				$limit = '<tr>
							<td style="text-align:right" width="45%">Redeem Times</td>
							<td style="text-align:center" width="5%">:</td>
							<td>'.$axRow['rede_RedeemLimit'].'</td>
						</tr>';

			} else {

				$limit = '<tr>
							<td style="text-align:right" width="45%">Redeem Times</td>
							<td style="text-align:center" width="5%">:</td>
							<td>'.$axRow['rede_NumberTime'].'</td>
						</tr>'; 
			}


			# RATIO

			$ratio = '';

			$coty_Image = '';

			if ($axRow['coty_CollectionTypeID'] && $axRow['rera_RewardQty_Stamp']) {

				$sql_image = 'SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID="'.$axRow['coty_CollectionTypeID'].'"';

	 			$coty_Image = $oDB->QueryOne($sql_image);

				$coty_Image = '<img src="'.$path_upload_collection.$coty_Image.'" style="margin-bottom:5px" width="12" height="12"/>';

				$ratio .= '<tr>
							<td style="text-align:right" width="45%">Stamp</td>
							<td style="text-align:center" width="5%">:</td>
							<td>'.$coty_Image.' &nbsp; '.$axRow['rera_RewardQty_Stamp'].'</td>
						</tr>';
			}


			if ($axRow['rera_RewardQty_Point']) {

				$ratio .= '<tr>
							<td style="text-align:right" width="45%">Point</td>
							<td style="text-align:center" width="5%">:</td>
							<td>'.$axRow['rera_RewardQty_Point'].' '.$coty_Image.'</td>
						</tr>';
			}


			if ($axRow['rera_AmountPlus']!='0.00') {

				$ratio .= '<tr>
							<td style="text-align:right" width="45%">Amount</td>
							<td style="text-align:center" width="5%">:</td>
							<td>'.$axRow['rera_AmountPlus'].' ฿</td>
						</tr>';
			}


			if ($axRow['rera_RewardQty']) {

				$ratio .= '<tr>
							<td style="text-align:right" width="45%">Rewards Qty</td>
							<td style="text-align:center" width="5%">:</td>
							<td>'.$axRow['rera_RewardQty'].'</td>
						</tr>';
			}


			if ($axRow['rede_Payment'] && $axRow['rera_AmountPlus']) {

				$ratio .= '<tr>
							<td style="text-align:right" width="45%">Payment</td>
							<td style="text-align:center" width="5%">:</td>
							<td>Credit Card</td>
						</tr>';
			}

			if ($axRow['rede_Repetition'] == '') { 

				$axRow['rede_Repetition'] = '<tr>
												<td style="text-align:right" width="45%">Limited</td>
												<td style="text-align:center" width="5%">:</td>
												<td> - </td>
											</tr>';	
			} else { 

				$axRow['rede_Repetition'] = '<tr>
												<td style="text-align:right" width="45%">Limited</td>
												<td style="text-align:center" width="5%">:</td>
												<td><span class="glyphicon glyphicon-check"></span> Use</td>
											</tr>
											<tr>
												<td style="text-align:right">QTY</td>
												<td style="text-align:center">:</td>
												<td>'.$axRow['rede_Qty'].' Per '.$axRow['rede_QtyPer'].'</td>
											</tr>
											<tr>
												<td></td>
												<td></td>
												<td>'.$axRow['rede_QtyPerData'].'</td>
											</tr>';	
			}


			# HIDDEN

			if ($axRow['rede_Hidden'] == 'No') { 
				$axRow['rede_Hidden'] = '<span class="glyphicon glyphicon-eye-open"></span>';	
			} else { $axRow['rede_Hidden'] = '<span class="glyphicon glyphicon-eye-close"></span>'; }


		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['rede_RewardRedeemID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
				<div class="modal fade" id="View'.$axRow['rede_RewardRedeemID'].'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px">'.$axRow['rede_Hidden'].' &nbsp; <b>'.$axRow['rede_Name'].'</b></span>
						        <hr>
						        <center>
						        	<table width="100%" class="myPopup"><tr>
						        		<td width="170px" style="text-align:center">'.$redeem_qr.'</td>
						        		<td width="170px" style="text-align:center">'.$rewards_view.'</td>
						        		<td style="text-align:right">Rewards<br>Category<br><br>Start Date<br>End Date</td>
						        		<td width="5%" style="text-align:center">:<br>:<br><br>:<br>:</td>
						        		<td width="130px">'.$axRow['rewa_Name'].'<br>'.$axRow['rewa_Type'].'<br><br>
						        		'.$axRow['rede_StartDate'].'<br>'.$axRow['rede_EndDate'].'</td>
						        	</tr></table>
						        	<br><br>
						        	<span style="font-size:12px">
					                <ul id="tapMenu" class="nav nav-tabs">
					                    <li class="active">
					                    	<a data-toggle="tab" href="#ratio'.$axRow['rede_RewardRedeemID'].'">
					                    	<center><b>Redeems Ratio</b></center></a>
					                   	</li>
					                    <li>
					                    	<a data-toggle="tab" href="#action'.$axRow['rede_RewardRedeemID'].'">
					                    	<center><b>Action</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#member'.$axRow['rede_RewardRedeemID'].'">
					                    	<center><b>Member</b></center></a>
					                   	</li>
					                    <li>
					                    	<a data-toggle="tab" href="#location'.$axRow['rede_RewardRedeemID'].'">
					                    	<center><b>Location</b></center></a>
					                   	</li>
					                    <li>
					                    	<a data-toggle="tab" href="#note'.$axRow['rede_RewardRedeemID'].'">
					                    	<center><b>Note</b></center></a>
					                    </li>
					                </ul>
					                </span>
					                <div class="tab-content">
					                    <div id="ratio'.$axRow['rede_RewardRedeemID'].'" class="tab-pane active"><br>
								        	<table width="80%" class="myPopup">
								        		'.$ratio.'
								        	</table>
					                    </div>
					                    <div id="action'.$axRow['rede_RewardRedeemID'].'" class="tab-pane"><br>
					                    	<table width="80%" class="myPopup">
								        		'.$limit.'
								        		<tr>
								        			<td style="text-align:right">Auto Redeems</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['rede_AutoRedeem'].'</td>
								        		</tr>
								        		'.$expired.'
								        	</table>
					                    </div>
					                    <div id="member'.$axRow['rede_RewardRedeemID'].'" class="tab-pane"><br>
					                    	<table width="80%" class="myPopup">
								        		'.$axRow['rede_Repetition'].'
								        	</table>
					                    </div>
					                    <div id="location'.$axRow['rede_RewardRedeemID'].'" class="tab-pane"><br>
					                    	<table style="width:80%" class="table table-striped table-bordered myPopup">
								        		<thead><tr class="th_table">
				                                    <td style="text-align:center"><b>Branch</b></td>
				                                    <td colspan="2" style="text-align:center"><b>Qr Code</b></td>
				                                </tr></thead>
				                                <tbody>'.$branch_data.'</tbody>
								        	</table>
					                    </div>
					                    <div id="note'.$axRow['rede_RewardRedeemID'].'" class="tab-pane"><br>
					                    	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%" valign="top">Description</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td valign="top">'.$axRow['rede_Description'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Condition</td>
								        			<td style="text-align:center">:</td>
								        			<td valign="top">'.$axRow['rede_Condition'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                </div>
						        </center>
						    </div>
						    <div class="modal-footer">';

			if ($_SESSION['role_action']['redeems']['edit'] == 1) {		    

				$view .= '       <a href="redeem_create.php?act=edit&id='.$axRow['redeem_id'].'">
						        <button type="button" class="btn btn-default btn-sm">Edit</button></a>';
			}

				$view .= '      <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';



		# DATA TABLE

		$data_table .= '<tr >
							<td >'.$i.'</td>
							<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
								<span style="font-size:11px;">'.$axRow['brand_name'].'</span>
							</td>
							<td style="text-align:center"><a href="../reward/reward.php">'.$rewards_img.'</a></td>
							<td>'.$axRow['rede_Name'].'</td>
							<td >'.$axRow['rewa_Type'].'</td>
							<td style="text-align:center">'.$axRow['rera_RewardQty'].'</td>
							<td >'.$axRow['rede_Time'].'</td>
							<td style="text-align:center">'.$status.'</td>
							<td >'.DateTime($axRow['rede_UpdatedDate']).'</td>';

		if ($_SESSION['role_action']['redeems']['view'] == 1) {

			$data_table .=	'<td style="text-align:center">'.$view.'</td>';
		}

		if ($_SESSION['role_action']['redeems']['delete'] == 1) {

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

$oTmp->assign('is_menu', 'is_redeems');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_motivation', 'in');

$oTmp->assign('content_file', 'redeem/redeem.htm');

$oTmp->display('layout/template.html');


//========================================//


$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
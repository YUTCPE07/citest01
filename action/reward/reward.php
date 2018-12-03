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

if ($_SESSION['role_action']['rewards']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$where_brand = '';



if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND reward.bran_BrandID = "'.$_SESSION['user_brand_id'].'" AND reward.rewa_Deleted=""';
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

		reward.*,
		mi_brand.logo_image,
		mi_brand.name as brand_name,
		mi_brand.path_logo,
		mi_tg_activity.activity_name as category_name

	  	FROM reward

		LEFT JOIN mi_brand
		ON mi_brand.brand_id = reward.bran_BrandID 

		LEFT JOIN mi_tg_activity
		ON mi_tg_activity.id_activity = reward.rewa_Category

		WHERE reward.bran_BrandID != "0"
		'.$where_search.'
		'.$where_brand.' 

		ORDER BY CASE 
			WHEN reward.rewa_Deleted = "" THEN 1
	        WHEN reward.rewa_Deleted = "T" THEN 2 END ASC,
			reward.rewa_Status ASC, 
			reward.rewa_UpdatedDate DESC';


if($Act == 'variety_category' && $id != '') {

	# UPDATE VARIETY

	$variety_id = $_REQUEST['variety_id'];

	$do_sql_variety = "UPDATE reward 
	 					SET vaca_VarietyCategoryID='".$variety_id."',
	 						rewa_UpdatedDate='".$time_insert."' 
	 					WHERE rewa_RewardID='".$id."'";

 	$oDB->QueryOne($do_sql_variety);

 	echo '<script>window.location.href="reward.php";</script>';


} else if($Act == 'display_data' && $id != '') {

	# UPDATE DISPLAY DATA

	$display_id = $_REQUEST['display_id'];

	$do_sql_display = "UPDATE reward 
	 					SET rewa_DisplayData='".$display_id."',
	 						rewa_UpdatedDate='".$time_insert."' 
	 					WHERE rewa_RewardID='".$id."'";

 	$oDB->QueryOne($do_sql_display);

 	echo '<script>window.location.href="reward.php";</script>';


} else if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE reward 
	 					SET rewa_Status='Pending',
	 						rewa_UpdatedDate='".$time_insert."' 
	 					WHERE rewa_RewardID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="reward.php";</script>';


} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE reward 
	 					SET rewa_Status='Active',
	 						rewa_UpdatedDate='".$time_insert."' 
	 					WHERE rewa_RewardID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="reward.php";</script>';


} else if($Act == 'delete' && $id != '') {


	# UPDATE DELETED

	$sql = 'SELECT rewa_Deleted FROM reward WHERE rewa_RewardID ="'.$id.'"';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

	
	if($axRow['rewa_Deleted']=='') {

 		$do_sql_reward = "UPDATE reward
 							SET rewa_Deleted='T', 
 							rewa_RewardID='Pending',
 							rewa_UpdatedDate='".$time_insert."' 
 							WHERE rewa_RewardID='".$id."'";

 		$do_sql_redeem = "UPDATE reward_redeem
 							SET rede_Deleted='T', 
 							rede_UpdatedDate='".$time_insert."' 
 							WHERE rewa_RewardID='".$id."'";

 		$oDB->QueryOne($do_sql_redeem);

 	} else if ($axRow['rewa_Deleted']=='T') {

		$do_sql_reward = "UPDATE reward
 							SET rewa_Deleted='', 
 							rewa_RewardID='Pending',
 							rewa_UpdatedDate='".$time_insert."' 
 							WHERE rewa_RewardID='".$id."'";
	}

 	$oDB->QueryOne($do_sql_reward);

 	echo '<script>window.location.href="reward.php";</script>';


} else if($Act=='xls'){


	require_once '../../lib/PHPExcel/Classes/PHPExcel.php';

	$objPHPExcel = new PHPExcel();

	$char = 'A';

	$chars = $char;

	$row_start = 1;

	$reportName = 'Rewards Report';

	$objPHPExcel->setActiveSheetIndex(0)

				// ->setCellValue('A1', 'Topics this report : '.$reportName )

				// ->setCellValue('A2', 'Issued this report  : '.$_SESSION['UNAME'].' / '.$_SESSION['FULLNAME'])

				// ->setCellValue('A3', 'Check out this report : '.$time_insert)

				// ->setCellValue($chars++.$row_start, 'No.')

				->setCellValue($chars++.$row_start, 'Brand')

				->setCellValue($chars++.$row_start, 'Rewards')

				->setCellValue($chars++.$row_start, 'Status')

				->setCellValue($chars++.$row_start, 'Description')

				->setCellValue($chars++.$row_start, 'Category')

				->setCellValue($chars++.$row_start, 'Type')

				->setCellValue($chars++.$row_start, 'QTY')

				->setCellValue($chars++.$row_start, 'UOM')

				->setCellValue($chars++.$row_start, 'Price')

				->setCellValue($chars++.$row_start, 'Cost')

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

		if($axRow['rewa_Deleted']=='') {	$axRow['rewa_Deleted']="No";	}
		else if ($axRow['rewa_Deleted']=='T') {	$axRow['rewa_Deleted']="Yes";	}


		$chars = $char;

		// $objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, ($row_start-5));

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['brand_name']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['rewa_Name']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['rewa_Status']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['rewa_Description']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['category_name']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['rewa_Type']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['rewa_Qty']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['rewa_UOM']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, number_format($axRow['rewa_Price'],2).' Bath');

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['rewa_Cost']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, DateTime($axRow['rewa_UpdatedDate']));

		$objPHPExcel->getActiveSheet()->setCellValue($chars . $row_start, $axRow['rewa_Deleted']);

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


		# STATUS

		$status = '';

		if($axRow['rewa_Deleted']=='T'){

			$status = '<button style="width:80px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

		} else {

			if($axRow['rewa_Status']=='Active'){

				if ($_SESSION['role_action']['rewards']['edit'] == 1) {

					$sql_rede = 'SELECT rede_RewardRedeemID 
									FROM reward_redeem 
									WHERE rewa_RewardID="'.$axRow['rewa_RewardID'].'"
									AND rede_Status="Active"';

					$check_rede = $oDB->QueryOne($sql_rede);

					if ($check_rede) {

		        		$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';
		        		
					} else {

						$status = '<form id="myForm" method="POST">
							<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'reward.php?act=active&id='.$axRow['rewa_RewardID'].'\'">
			                    <option class="status_default" value="'.$axRow['rewa_RewardID'].'" selected>On</option>
			                    <option class="status_default">Off</option>
			                </select>
			            </form>';
					}

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';
		        }

			} else {

				if ($_SESSION['role_action']['rewards']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_pending" name="pending_status" onchange="window.location.href=\'reward.php?act=pending&id='.$axRow['rewa_RewardID'].'\'">
		                    <option class="status_default">On</option>
		                    <option class="status_default" value="'.$axRow['rewa_RewardID'].'" selected>Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
		        }
			}
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

		if ($axRow['rewa_Type'] == 'Card') {

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

			# REWARDS IMAGE

			if($axRow['rewa_Image']!=''){

				$rewards_img = '<img src="../../upload/'.$axRow['rewa_ImagePath'].$axRow['rewa_Image'].'" class="image_border" width="70" height="70"/>';

				$rewards_view = '<img src="../../upload/'.$axRow['rewa_ImagePath'].$axRow['rewa_Image'].'" class="image_border" width="150" height="150"/>';

			} else {

				$rewards_img = '<img src="../../images/400x400.png" class="image_border" width="70" height="70"/>';

				$rewards_view = '<img src="../../images/400x400.png" class="image_border" width="150" height="150"/>';
			}
		}



		# DELETED

		if($axRow['rewa_Deleted']=='') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['rewa_RewardID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>
				<div class="modal fade" id="Deleted'.$axRow['rewa_RewardID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
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
								        	<b>"'.$axRow['rewa_Name'].'"</b><br>
								            By clicking the <b>"Inactive"</b> button to:<br>
								            &nbsp; &nbsp;- Inactive this rewards<br>
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="reward.php?act=delete&id='.$axRow['rewa_RewardID'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		} else if ($axRow['rewa_Deleted']=='T') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['rewa_RewardID'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>
				<div class="modal fade" id="Deleted'.$axRow['rewa_RewardID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
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
								        	<b>"'.$axRow['rewa_Name'].'"</b><br>
								           	By clicking the <b>"Active"</b> button to:<br>
								            &nbsp; &nbsp;- Active this rewards<br>
								            &nbsp; &nbsp;- Change status to Pending
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="reward.php?act=delete&id='.$axRow['rewa_RewardID'].'">
						        <button type="button" class="btn btn-default btn-sm">Active</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		}


		# VIEW

			# DATA

			if ($axRow['vaca_NameEn'] == '') { $axRow['vaca_NameEn'] = 'Not Specific';	}

			if ($axRow['rewa_Description'] == '') { $axRow['rewa_Description'] = '-';	}

			if ($axRow['rewa_UOM'] == '') { $axRow['rewa_UOM'] = '-';	}

			if ($axRow['rewa_Limit'] == 'F') { $axRow['rewa_Qty'] = 'Unlimit';	}


			# BRANCH

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
									<td style="text-align:center">'.$name.'</td>
									<td style="text-align:center"><img src="../../upload/'.$axRow['rewa_QrPath'].'QRW-'.str_pad($axRow['rewa_RewardID'],4,"0",STR_PAD_LEFT).'-'.str_pad($branch[$x],4,"0",STR_PAD_LEFT).'.png" width="80" height="80" class="image_border"/></td>
									<td style="text-align:center"><a target="_blank" href="reward_qrcode.php?id='.$axRow['rewa_RewardID'].'&branch='.$branch[$x].'">QRCode Link</td>
									</tr>
									</tr>';
				}

			} else {

				$branch_data = '<tr><td colspan="3" style="text-align:center">No Branch Data</td></tr>';
			}

			# AGE

			if ($axRow['rewa_Age'] == '') { $age = '-';	}

			else {

	            $token = strtok($axRow['rewa_Age'] , ",");

				$target_data = array();

				$z = 0;

				while ($token !== false) {

					$target_data[$z] =  $token;
					$token = strtok(",");
					$z++;
				}

				$arrlength = count($target_data);

				$age = "";

				for($x=0; $x<$arrlength; $x++) {

					if ($x == 1) { $age .= ' - '; }

					$sql_target = 'SELECT mata_NameEn
									FROM master_target
									WHERE mata_MasterTargetID="'.$target_data[$x].'"';

		 			$age .= $oDB->QueryOne($sql_target);
				}

				$age .= ' Years Old';
			}


			# GENDER

			if ($axRow['rewa_Gender'] == '1') {  $axRow['rewa_Gender'] = 'Male'; }

			else if ($axRow['rewa_Gender'] == '2') {  $axRow['rewa_Gender'] = 'Female'; }	

			else { $axRow['rewa_Gender'] = '-';	}	


			# MARITAL

			if ($axRow['rewa_Marital'] == '0') { $axRow['rewa_Marital'] = '-';	}

			else {

				$sql_target = 'SELECT mata_NameEn
								FROM master_target
								WHERE mafi_MasterFieldID="7"
								AND mata_MasterTargetID="'.$axRow['rewa_Marital'].'"';

	 			$axRow['rewa_Marital'] = $oDB->QueryOne($sql_target);
			}


			# EDUCATION

			if ($axRow['rewa_Education'] == '0') { $axRow['rewa_Education'] = '-';	}

			else {

				$sql_target = 'SELECT mata_NameEn
								FROM master_target
								WHERE mafi_MasterFieldID="12"
								AND mata_MasterTargetID="'.$axRow['rewa_Education'].'"';

	 			$axRow['rewa_Education'] = $oDB->QueryOne($sql_target);
			}



			# ACTIVITY

			if ($axRow['rewa_Activity'] == '0') { $axRow['rewa_Activity'] = '-';	}

			else {

				$sql_target = 'SELECT mata_NameEn
								FROM master_target
								WHERE mafi_MasterFieldID="13"
								AND mata_MasterTargetID="'.$axRow['rewa_Activity'].'"';

	 			$axRow['rewa_Activity'] = $oDB->QueryOne($sql_target);
			}



			# INCOME

			if ($axRow['rewa_MonthlyPersonalIncome'] == '0') { $axRow['rewa_MonthlyPersonalIncome'] = '-';	
			} else {

				$sql_target = 'SELECT mata_NameEn
								FROM master_target
								WHERE mafi_MasterFieldID="19" 
								AND mata_MasterTargetID="'.$axRow['rewa_MonthlyPersonalIncome'].'"';

	 			$axRow['rewa_MonthlyPersonalIncome'] = $oDB->QueryOne($sql_target);
			}


			# PROVINCE

			if ($axRow['rewa_Province'] == '0') { $axRow['rewa_Province'] = '-';	
			} else {

				$sql_province = 'SELECT prov_Name
								FROM province
								WHERE prov_ProvinceID="'.$axRow['rewa_Province'].'"';

	 			$axRow['rewa_Province'] = $oDB->QueryOne($sql_province);
			}


			# REWARD TARGET

			$sql_target = 'SELECT reta_Target, cufi_CustomFieldID
							FROM reward_target
							WHERE rewa_RewardID="'.$axRow['rewa_RewardID'].'" AND reta_Deleted=""';

			$oRes_target = $oDB->Query($sql_target);

			$reward_target = "";

			while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

				# FIELD

				$sql_field = 'SELECT cufi_Name 
								FROM custom_field 
								WHERE cufi_CustomFieldID="'.$target['cufi_CustomFieldID'].'"';

				$field = $oDB->QueryOne($sql_field);

				# VALUE

				$sql_value = 'SELECT clva_Name 
								FROM custom_list_value 
								WHERE cufi_CustomFieldID="'.$target['cufi_CustomFieldID'].'"
								AND clva_Value="'.$target['reta_Target'].'"';

				$value = $oDB->QueryOne($sql_value);

				$reward_target .= '<tr>
								    <td style="text-align:right">'.$field.'</td>
								    <td style="text-align:center">:</td>
								    <td>'.$value.'</td>
								</tr>';
			}

		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['rewa_RewardID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
				<div class="modal fade" id="View'.$axRow['rewa_RewardID'].'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>'.$axRow['rewa_Name'].'</b></span>
						        <hr>
						        <center>
						        	<table width="60%" class="myPopup"><tr>
						        		<td width="180px">'.$rewards_view.'</td>
						        		<td>Description :<br>'.$axRow['rewa_Description'].'</td>
						        	</tr></table>
						        	<br><br>
						        	<span style="font-size:12px">
					                <ul id="tapMenu" class="nav nav-tabs">
					                    <li class="active">
					                    	<a data-toggle="tab" href="#basic'.$axRow['rewa_RewardID'].'">
					                    	<center><b>Basic</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#financial'.$axRow['rewa_RewardID'].'">
					                    	<center><b>Financial</b></center></a>
					                   	</li>
					                    <li>
					                    	<a data-toggle="tab" href="#location'.$axRow['rewa_RewardID'].'">
					                    	<center><b>Location</b></center></a>
					                   	</li>
					                    <li>
					                    	<a data-toggle="tab" href="#properly'.$axRow['rewa_RewardID'].'">
					                    	<center><b>Properly</b></center></a>
					                    </li>
					                </ul>
					                </span>
					                <div class="tab-content">
					                    <div id="basic'.$axRow['rewa_RewardID'].'" class="tab-pane active"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Category</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['category_name'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Type</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['rewa_Type'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Qty</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['rewa_Qty'].'</td>
								        		</tr>';

		if ($axRow['rewa_Type'] == 'Card') {						        		

			$view .= '						    <tr>
								        			<td style="text-align:right">Auto Reward</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['rewa_AutoReward'].'</td>
								        		</tr>';

		} else if ($axRow['rewa_Type'] == 'Discount') {	

			if ($axRow['rewa_DiscountType'] == 'Amount') { $type = '฿'; }

			if ($axRow['rewa_DiscountType'] == 'Percent') { $type = '%'; }	

			if ($axRow['rewa_MinPay'] && $axRow['rewa_MaxPay']) { 

				$pay = $axRow['rewa_MinPay'].' - '.$axRow['rewa_MaxPay'].' ฿';

			} else if ($axRow['rewa_MinPay']) { $pay = 'Minimum'.$axRow['rewa_MinPay'].' ฿';

			} else if ($axRow['rewa_MaxPay']) { $pay = 'Maximum'.$axRow['rewa_MaxPay'].' ฿';

			} else { $pay = '-'; }        		

			$view .= '						    <tr>
								        			<td style="text-align:right">Discount</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['rewa_Discount'].' '.$type.'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Pay</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$pay.'</td>
								        		</tr>';
		} else {			        		

			$view .= '						    <tr>
								        			<td style="text-align:right">UOM</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['rewa_UOM'].'</td>
								        		</tr>';
		}

		$view .= '			        	</table>
					                    </div>
					                    <div id="financial'.$axRow['rewa_RewardID'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Price</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.number_format($axRow['rewa_Price'],2).' ฿</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Cost</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.number_format($axRow['rewa_Cost'],2).' ฿</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="location'.$axRow['rewa_RewardID'].'" class="tab-pane"><br>
					                    	<table style="width:80%" class="table table-striped table-bordered myPopup">
								        		<thead><tr class="th_table">
				                                    <td style="text-align:center"><b>Branch</b></td>
				                                    <td colspan="2" style="text-align:center"><b>Qr Code</b></td>
				                                </tr></thead>
				                                <tbody>'.$branch_data.'</tbody>
								        	</table>
					                    </div>
					                    <div id="properly'.$axRow['rewa_RewardID'].'" class="tab-pane"><br>
					                    	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Gender</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['rewa_Gender'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Age</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$age.'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Marital Status</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['rewa_Marital'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Education</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['rewa_Education'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Activity / Lifestyle</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['rewa_Activity'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Monthly Personal Income</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['rewa_MonthlyPersonalIncome'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Province</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['rewa_Province'].'</td>
								        		</tr>
								        		'.$reward_target.'
								        	</table>
					                    </div>
					                </div>
						        </center>
						    </div>
						    <div class="modal-footer">';

			if ($_SESSION['role_action']['rewards']['edit'] == 1) {		    

				$view .= '       <a href="reward_create.php?act=edit&id='.$axRow['rewa_RewardID'].'">
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
							<td style="text-align:center">'.$rewards_img.'</td>
							<td>'.$axRow['rewa_Name'].'</td>
							<td >'.$axRow['rewa_Qty'].'</td>
							<td >'.$axRow['category_name'].'</td>
							<td >'.$axRow['rewa_Type'].'</td>
							<td style="text-align:center">'.$status.'</td>
							<td >'.DateTime($axRow['rewa_UpdatedDate']).'</td>';

		if ($_SESSION['role_action']['rewards']['view'] == 1) {

			$data_table .=	'<td style="text-align:center">'.$view.'</td>';
		}

		if ($_SESSION['role_action']['rewards']['delete'] == 1) {

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

$oTmp->assign('is_menu', 'is_rewards');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_motivation', 'in');

$oTmp->assign('content_file', 'reward/reward.htm');

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

      window.location.href="reward.php?act=variety_category&id="+coupon_id+"&variety_id="+variety_id;
	}
	
	function DisplaySelect(display_id,coupon_id) {

      window.location.href="reward.php?act=display_data&id="+coupon_id+"&display_id="+display_id;
	}

</script>
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

if ($_SESSION['role_action']['card']['view'] != 1) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$time_insert = date("Y-m-d H:i:s");
$today = date("Y-m-d");
$Act = $_REQUEST['act'];
$id = $_REQUEST['id'];
$approve = $_REQUEST['approve'];


$where_brand = '';

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND a.brand_id = "'.$_SESSION['user_brand_id'].'" AND a.flag_del=0 ';
}


# SEARCH

$brand_id = "";

$where_search = "";

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
else {	$where_search = " AND c.brand_id IN (".$brand_id.")";	}



$sql = 'SELECT 

		a.*,
		a.flag_del AS status_del,
		b.name AS card_type_name,
		c.name AS brand_name,
		c.path_logo,
		c.logo_image,
		a.brand_id AS card_brand_id

		FROM mi_card AS a

  		LEFT JOIN mi_card_type AS b
    	ON a.card_type_id = b.card_type_id

		LEFT JOIN mi_brand AS c
		ON a.brand_id = c.brand_id

		WHERE 1

		'.$where_search.'
		'.$where_brand.' 

		ORDER BY CASE 
			WHEN a.flag_del = "0" THEN 1
	        WHEN a.flag_del = "1" THEN 2 END ASC,
			a.flag_status ASC, 
			a.date_update DESC';


# CHECK EXPRIED DATE

// $oRes = $oDB->Query($sql);

// while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

// 	if ($axRow['date_expired'] != "0000-00-00") {

// 		$date_expired = strtotime($axRow['date_expired']);
// 		$today = strtotime($today);

// 		if ($date_expired <= $today) {
			
// 			$do_sql_card = "UPDATE mi_card SET flag_status=2 WHERE card_id= '".$axRow['card_id']."'";
// 			$oDB->QueryOne($do_sql_card);
// 		}
// 	}
// }


if($Act == 'variety_category' && $id != '') {

	# UPDATE VARIETY

	$variety_id = $_REQUEST['variety_id'];

	$do_sql_variety = "UPDATE mi_card 
	 					SET variety_category_id='".$variety_id."',
	 						date_update='".$time_insert."' 
	 					WHERE card_id='".$id."'";

 	$oDB->QueryOne($do_sql_variety);

 	echo '<script>window.location.href="card.php";</script>';


} else if($Act == 'display_data' && $id != '') {

	# UPDATE DISPLAY DATA

	$display_id = $_REQUEST['display_id'];

	$do_sql_display = "UPDATE mi_card 
	 					SET display_data='".$display_id."',
	 						date_update='".$time_insert."' 
	 					WHERE card_id='".$id."'";

 	$oDB->QueryOne($do_sql_display);

 	echo '<script>window.location.href="card.php";</script>';


} else if($Act == 'approve' && $id != '') {

	# APPROVE IMAGE

	$sql = '';

	$sql .= 'SELECT image_newupload, image FROM mi_card WHERE card_id ="'.$id.'"';

	$oRes = $oDB->Query($sql);
	$axRow = $oRes->FetchRow(DBI_ASSOC);
		
	if($approve == 'unapprove') {

 		# UNAPPROVE

		// if ($axRow['image']!="") {

		// 	unlink_file($oDB,'mi_card','image_newupload','card_id',$id,$path_upload_card,$axRow['image_newupload']);

		// 	$do_sql_upload = "UPDATE mi_card 
		// 						SET image_newupload='',
		// 						flag_status='2',
		// 						date_update='".$time_insert."' 
		// 						WHERE card_id='".$id."' ";

		// } else if ($axRow['image_newupload']!=""){

		// 	unlink_file($oDB,'mi_card','image','card_id',$id,$path_upload_card,$axRow['image']);
				
		// 	$do_sql_upload = "UPDATE mi_card 
		// 						SET image='',
		// 						flag_status='2',
		// 						date_update='".$time_insert."'  
		// 						WHERE card_id='".$id."' ";
		// }

		unlink_file($oDB,'mi_card','image','card_id',$id,$path_upload_card,$axRow['image']);
				
		$do_sql_upload = "UPDATE mi_card 
							SET image='',
							flag_approve='',
							flag_status='2',
							date_update='".$time_insert."'  
							WHERE card_id='".$id."' ";
 			
 		$oDB->QueryOne($do_sql_upload);
 	}
		

	if ($approve == 'approve') {

		# APPROVE

		// if ($axRow['image']!="") {

		// 	unlink_file($oDB,'mi_card','image','card_id',$id,$path_upload_card,$axRow['image']);

		// 	$do_sql_upload = "UPDATE mi_card 
		// 						SET image='".$axRow['image_newupload']."', 
		// 						image_newupload='',
		// 						date_update='".$time_insert."' 
		// 						WHERE card_id='".$id."'";
		// } else {

		// 	$do_sql_upload = "UPDATE mi_card 
		// 						SET image='".$axRow['image_newupload']."', 
		// 						image_newupload='',
		// 						flag_status='2',
		// 						date_update='".$time_insert."' 
		// 						WHERE card_id='".$id."'";

		// }

		$do_sql_upload = "UPDATE mi_card 
							SET flag_approve='T',
							date_update='".$time_insert."' 
							WHERE card_id='".$id."'";

 		$oDB->QueryOne($do_sql_upload);

	}

	echo '<script> window.location.href="card.php"; </script>';


} else if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE mi_card 
	 					SET flag_status='2',
	 						date_update='".$time_insert."' 
	 					WHERE card_id='".$id."'";

 	$oDB->QueryOne($do_sql_status);
 	echo '<script>window.location.href="card.php";</script>';


} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE mi_card 
	 					SET flag_status='1',
	 						date_update='".$time_insert."' 
	 					WHERE card_id='".$id."'";

 	$oDB->QueryOne($do_sql_status);
 	echo '<script>window.location.href="card.php";</script>';


} else if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT flag_del FROM mi_card WHERE card_id ="'.$id.'"';

	$oRes = $oDB->Query($sql);
	$axRow = $oRes->FetchRow(DBI_ASSOC);
		
	if($axRow['flag_del']=='0') {
 				
 		$do_sql_card = "UPDATE mi_card
 							SET flag_del='1', 
 							flag_status='2',
 							date_update='".$time_insert."' 
 							WHERE card_id='".$id."'";

 	} else if ($axRow['flag_del']=='1') {

		$do_sql_card = "UPDATE mi_card
 							SET flag_del='0', 
 							flag_status='2',
 							date_update='".$time_insert."' 
 							WHERE card_id='".$id."'";
	}

 	$oDB->QueryOne($do_sql_card);
 			
 	echo '<script>window.location.href="card.php";</script>';


} else if($Act=='xls'){

	require_once '../../lib/PHPExcel/Classes/PHPExcel.php';

	$objPHPExcel = new PHPExcel();

	$char = 'A';

	$chars = $char;

	$row_start = 1;

	$reportName = 'Card Report';

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
					->setCellValue($chars++.$row_start, 'Card')
					->setCellValue($chars++.$row_start, 'Card Type')
					->setCellValue($chars++.$row_start, 'Automatic Renew')
					->setCellValue($chars++.$row_start, 'Description')
					->setCellValue($chars++.$row_start, 'Purpose')
					->setCellValue($chars++.$row_start, 'Last Register Date')
					->setCellValue($chars++.$row_start, 'Period')
					->setCellValue($chars++.$row_start, 'Price')
					->setCellValue($chars++.$row_start, 'Multiple per Person')
					->setCellValue($chars++.$row_start, 'Special Code')
					->setCellValue($chars++.$row_start, 'Maximum No. of Cards')
					->setCellValue($chars++.$row_start, 'Condition')
					->setCellValue($chars++.$row_start, 'Exception')
					->setCellValue($chars++.$row_start, 'Status')
					->setCellValue($chars++.$row_start, 'Updated Date')
					->setCellValue($chars.$row_start, 'Delete');

	$row_start++;

	$oRes = $oDB->Query($sql);

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)) {

		if($axRow['status_del']=='0') {	$axRow['status_del']="No";	}
		else if ($axRow['status_del']=='1') {	$axRow['status_del']="Yes";	}

		if($axRow['flag_status']=='1') {	$axRow['flag_status']="Active";	}
		else if ($axRow['flag_status']=='2') {	$axRow['flag_status']="Pending";	}

		if ($axRow['date_last_register'] == '0000-00-00') { $axRow['date_last_register'] = '';	}
		else {	$axRow['date_last_register'] = DateOnly($axRow['date_last_register']);	}

		if ($axRow['period_type'] == '1') { $axRow['period_type'] = DateOnly($axRow['date_expired']); } 
		else if ($axRow['period_type'] == '2') { $axRow['period_type'] = $axRow['period_type_other'].' Months'; } 
		else if ($axRow['period_type'] == '3') { $axRow['period_type'] = $axRow['period_type_other'].' Years'; } 
		else if ($axRow['period_type'] == '4') { $axRow['period_type'] = 'Member Life Time'; }

		if($axRow['special_code']=='') {	$axRow['special_code']="No";	}
		else if ($axRow['special_code']=='T') {	$axRow['special_code']="Yes";	}


		$chars = $char;

		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['brand_name']);
		$objWorkSheet->setCellValueExplicit($chars++.$row_start, $axRow['name']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['card_type_name']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['flag_autorenew']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['description']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['purpose']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['date_last_register']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['period_type']);
		$objWorkSheet->setCellValue($chars++.$row_start, number_format($axRow['member_fee'],2).' ฿');
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['flag_multiple']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['special_code']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['limit_member']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['condition_card']);
		$objWorkSheet->setCellValue($chars++.$row_start, $axRow['exception']);
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

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;


		# STATUS

		$status = '';

		if($axRow['status_del']=='1'){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

		} else {

			if($axRow['flag_status']=='1'){

				if ($_SESSION['role_action']['card']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'card.php?act=active&id='.$axRow['card_id'].'\'">
		                    <option class="status_default" value="'.$axRow['card_id'].'" selected>On</option>
		                    <option class="status_default">Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';
		        }

			} else {

				if ($_SESSION['role_action']['card']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_pending" name="pending_status" onchange="window.location.href=\'card.php?act=pending&id='.$axRow['card_id'].'\'">
		                    <option class="status_default">On</option>
		                    <option class="status_default" value="'.$axRow['card_id'].'" selected>Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
		        }
			}
		}


		# DISPLAY DATA

		$display = array("Not Specific","Recommed","Recently","Most Active");

		$display_data = '<form id="myForm" method="POST">
							<select class="form-control text-md" name="display_data" onchange="window.location.href=\'card.php?act=display_data&id='.$axRow['card_id'].'\'">';

		for ($j=0; $j<4; $j++) {

			if ($axRow['display_data'] == $display[$j]) { $selected = 'selected'; }
			else { $selected = 'selected'; }

			$display_data .= '	<option value="'.$display[$j].'" '.$selected.'>'.$display[$j].'</option>';
		}

		$display_data .= '	</select>
		            	</form>';


		# LOGO

		if($axRow['logo_image']!=''){

			$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';

		} else {

			$logo_brand = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';
		}


		# QRCODE

		if($axRow['qr_code_image']!=''){

			$qr_code = '<img src="../../upload/'.$axRow['path_qr'].$axRow['qr_code_image'].'" class="image_border" width="150" height="150"/>';
		}


		# CARD IMAGE

		if($axRow['image_newupload']!=''){

			$card_image = '<img src="../../upload/'.$axRow['path_image'].$axRow['image_newupload'].'" class="img-rounded image_border" width="128" height="80"/>';

			$card_view = '<img src="../../upload/'.$axRow['path_image'].$axRow['image_newupload'].'" class="img-rounded image_border" width="136" height="85"/>';

			$card_data = '<img src="../../upload/'.$axRow['path_image'].$axRow['image_newupload'].'" class="img-rounded image_border" width="240" height="150"/>';

		} else {

			if($axRow['image']!=''){

				$card_image = '<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'" class="img-rounded image_border" width="128" height="80"/>';

				$card_view = '<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'" class="img-rounded image_border" width="136" height="85"/>';

				$card_data = '<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'" class="img-rounded image_border" width="240" height="150"/>';

			} else {

				$card_image = '<img src="../../images/card_privilege.jpg" class="img-rounded image_border" width="128" height="80"/>';

				$card_view = '<img src="../../images/card_privilege.jpg" class="img-rounded image_border" width="136" height="85"/>';

				$card_data = '<img src="../../images/card_privilege.jpg" class="img-rounded image_border" width="240" height="150"/>';
			}
		}


		# APPROVE

		if ($axRow['flag_approve']=='' && $axRow['image']=='') {

			$image_status = '<span class="glyphicon glyphicon-exclamation-sign" style="color:#f0ad4e;font-size:20px" alt="aaaaa"></span>';

			$button_approve = '';

            $button_unapprove = '';
                                
		} else if ($axRow['flag_approve']=='T' && $axRow['image']) {

			$image_status = '';

			$button_approve = '';

            $button_unapprove = '<a style="cursor:pointer" href="card.php?act=approve&approve=unapprove&id='.$axRow['card_id'].'">
                                <button type="button" class="btn btn-default btn-sm">
                                <span class="glyphicon glyphicon-remove-sign" aria-hidden="true" style="color:red"></span> Unapprove</button></a>';
		} else {

			$image_status = '';

			$button_approve = '<a style="cursor:pointer" href="card.php?act=approve&approve=approve&id='.$axRow['card_id'].'">
                                <button type="button" class="btn btn-default btn-sm">
                                <span class="glyphicon glyphicon-ok-sign" aria-hidden="true" style="color:green"></span> Approve</button></a>';

            $button_unapprove = '<a style="cursor:pointer" href="card.php?act=approve&approve=unapprove&id='.$axRow['card_id'].'">
                                <button type="button" class="btn btn-default btn-sm">
                                <span class="glyphicon glyphicon-remove-sign" aria-hidden="true" style="color:red"></span> Unapprove</button></a>';
		}

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
									<td style="text-align:center"><img src="../../upload/'.$axRow['path_qr'].'MAC-'.str_pad($axRow['card_id'],4,"0",STR_PAD_LEFT).'-'.str_pad($branch[$x],4,"0",STR_PAD_LEFT).'.png" width="80" height="80" class="image_border"/></td>
									<td style="text-align:center"><a target="_blank" href="card_qrcode.php?id='.$axRow['card_id'].'&branch='.$branch[$x].'">QRCode Link</td>
									</tr>
									</tr>';
			}

		} else {

			$branch_data = '<tr><td colspan="3" style="text-align:center">No Branch Data</td></tr>';
		}


		# DELETED

		if($axRow['status_del']=='0') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['card_id'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>

				<div class="modal fade" id="Deleted'.$axRow['card_id'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="150px" style="text-align:center" valign="top">'.$card_view.'</td>
						        	<td>
								        <p style="font-size:12px;padding-left:10px;">
								        	<b>"'.$axRow['name'].'"</b><br>
								            By clicking the <b>"Inactive"</b> button to:<br>
								            &nbsp; &nbsp;- Inactive this news<br>
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="card.php?act=delete&id='.$axRow['card_id'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		
		} else if ($axRow['status_del']=='1') {
				
			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['card_id'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>

				<div class="modal fade" id="Deleted'.$axRow['card_id'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="150px" style="text-align:center" valign="top">'.$card_view.'</td>
						        	<td>
								        <p style="font-size:12px;padding-left:10px;">
								        	<b>"'.$axRow['name'].'"</b><br>
								           	By clicking the <b>"Active"</b> button to:<br>
								            &nbsp; &nbsp;- Active this news<br>
								            &nbsp; &nbsp;- Change status to Pending
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="card.php?act=delete&id='.$axRow['card_id'].'">
						        <button type="button" class="btn btn-default btn-sm">Active</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		}


		# VARIETY CATEGORY

		$sql_variety = 'SELECT variety.vari_VarietyID, variety.vari_Title
						FROM variety
						LEFT JOIN variety_category
						ON variety.vari_VarietyCategoryID = variety_category.vaca_VarietyCategoryID
						WHERE variety_category.vaca_Type="Card" 
						AND variety.vari_Status="1"';

		$oRes_vaca = $oDB->Query($sql_variety);

		$data_variety = '<option value="0">Not Specific</option>';

		while ($vaca = $oRes_vaca->FetchRow(DBI_ASSOC)){

			if ($vaca['vari_VarietyID'] == $axRow['vari_VarietyID']) { $select = 'selected'; }
			else { $select = ''; }

			$data_variety .= '<option value="'.$vaca['vari_VarietyID'].'" '.$select.'>'.$vaca['vaca_NameEn'].'</option>';
		}



		# VIEW

			# DATA

			if ($variety_category == '') { $variety_category = 'Not Specific';	}

			if ($axRow['limit_member'] == '') { $axRow['limit_member'] = 'Unlimit';	}

			if ($axRow['description'] == '') { $axRow['description'] = '-';	}
			else { $axRow['description'] = nl2br($axRow['description']); }

			if ($axRow['purpose'] == '') { $axRow['purpose'] = '-';	}
			else { $axRow['purpose'] = nl2br($axRow['purpose']); }

			if ($axRow['condition_card'] == '') { $axRow['condition_card'] = '-';	}
			else { $axRow['condition_card'] = nl2br($axRow['condition_card']); }

			if ($axRow['exception'] == '') { $axRow['exception'] = '-';	}
			else { $axRow['exception'] = nl2br($axRow['exception']); }

			if ($axRow['register_condition'] == '') { $axRow['register_condition'] = '-';	}
			else { $axRow['register_condition'] = nl2br($axRow['register_condition']); }

			if ($axRow['how_to_activate'] == '') { $axRow['how_to_activate'] = '-';	}
			else { $axRow['how_to_activate'] = nl2br($axRow['how_to_activate']); }

			if ($axRow['how_to_use'] == '') { $axRow['how_to_use'] = '-';	}
			else { $axRow['how_to_use'] = nl2br($axRow['how_to_use']); }

			if ($axRow['collection_data'] == '') { $axRow['collection_data'] = '-';	}
			else { $axRow['collection_data'] = nl2br($axRow['collection_data']); }

			if ($axRow['re_new'] == '') { $axRow['re_new'] = '-';	}
			else { $axRow['re_new'] = nl2br($axRow['re_new']); }

			if ($axRow['upgrade_data'] == '') { $axRow['upgrade_data'] = '-';	}
			else { $axRow['upgrade_data'] = nl2br($axRow['upgrade_data']); }

			if ($axRow['where_to_use'] == '') { $axRow['where_to_use'] = '-';	}
			else { $axRow['where_to_use'] = nl2br($axRow['where_to_use']); }

			if ($axRow['source_information'] == '') { $axRow['source_information'] = '-';	}
			else { $axRow['source_information'] = nl2br($axRow['source_information']); }

			if ($axRow['birthday_privileges'] == '') { $axRow['birthday_privileges'] = '-';	}

			if ($axRow['date_last_register'] == '0000-00-00') { $axRow['date_last_register'] = '-';	}
			else {	$axRow['date_last_register'] = DateOnly($axRow['date_last_register']);	}

			if ($axRow['special_code'] == 'T') { $axRow['special_code'] = 'Yes';	}
			else {	$axRow['special_code'] = 'No';	}

			if ($axRow['flag_hidden'] == 'No') { 
				$axRow['flag_hidden'] = '<span class="glyphicon glyphicon-eye-open"></span>';	
			} else {	$axRow['flag_hidden'] = '<span class="glyphicon glyphicon-eye-close"></span>';	}

			if ($axRow['period_type'] == '1') { 

				$axRow['period_type'] = '<tr>
								        	<td style="text-align:right" width="45%">Period Type</td>
								        	<td style="text-align:center" width="5%">:</td>
								        	<td>Not Specific</td>
								        </tr>
								        <tr>
								        	<td style="text-align:right">Expired Date </td>
								        	<td style="text-align:center">:</td>
								        	<td>'.DateOnly($axRow['date_expired']).'</td>
								        </tr>';	

			} else if ($axRow['period_type'] == '2') { 

				$axRow['period_type'] = '<tr>
								        	<td style="text-align:right" width="45%">Period Type</td>
								        	<td style="text-align:center" width="5%">:</td>
								        	<td>'.$axRow['period_type_other'].' Months</td>
								        </tr>';	

			} else if ($axRow['period_type'] == '3') { 

				$axRow['period_type'] = '<tr>
								        	<td style="text-align:right" width="45%">Period Type</td>
								        	<td style="text-align:center" width="5%">:</td>
								        	<td>'.$axRow['period_type_other'].' Years</td>
								        </tr>';	

			} else if ($axRow['period_type'] == '4') { 

				$axRow['period_type'] = '<tr>
								        	<td style="text-align:right" width="45%">Period Type</td>
								        	<td style="text-align:center" width="5%">:</td>
								        	<td>Member Life Time</td>
								        </tr>';	

			}

			if ($axRow['price_type'] == 'Free Card') { 

				$axRow['price_type'] = '<tr>
								        	<td style="text-align:right">Price Type</td>
								        	<td style="text-align:center">:</td>
								        	<td>'.$axRow['price_type'].'</td>
								        </tr>';	

			} else if ($axRow['price_type'] == 'Not Free Card') { 

				$percent_off = (($axRow['original_fee']-$axRow['member_fee'])/$axRow['original_fee'])*100;

				$axRow['price_type'] = '<tr>
								        	<td style="text-align:right">Price Type</td>
								        	<td style="text-align:center">:</td>
								        	<td>'.$axRow['price_type'].'</td>
								        </tr>
								        <tr>
								        	<td style="text-align:right">Member Fee</td>
								        	<td style="text-align:center">:</td>
								        	<td>'.number_format($axRow['member_fee'],2).' ฿ ('.number_format($percent_off).'% Off)</td>
								        </tr>
								        <tr>
								        	<td style="text-align:right">Original Fee</td>
								        	<td style="text-align:center">:</td>
								        	<td>'.number_format($axRow['original_fee'],2).' ฿</td>
								        </tr>';	

				if($_SESSION['user_type_id_ses']==1){

					$axRow['price_type'] .= '<tr>
									        	<td style="text-align:right">Charge</td>
									        	<td style="text-align:center">:</td>
									        	<td>'.$axRow['charge_percent'].' % ('.number_format($axRow['member_fee']*($axRow['charge_percent']/100),2).' ฿)</td>
									        </tr>';
				}

			} else if ($axRow['price_type'] == 'Info. Card') { 

				$axRow['price_type'] = '<tr>
								        	<td style="text-align:right">Price Type</td>
								        	<td style="text-align:center">:</td>
								        	<td>'.$axRow['price_type'].'</td>
								        </tr>
								        <tr>
								        	<td style="text-align:right">Member Fee</td>
								        	<td style="text-align:center">:</td>
								        	<td>'.number_format($axRow['member_fee'],2).' ฿</td>
								        </tr>';	
								        
				if($_SESSION['user_type_id_ses']==1){

					$axRow['price_type'] .= '<tr>
									        	<td style="text-align:right">Charge</td>
									        	<td style="text-align:center">:</td>
									        	<td>'.$axRow['charge_percent'].' % ('.number_format($axRow['member_fee']*($axRow['charge_percent']/100),2).' ฿)</td>
									        </tr>';
				}
			}


			# PRIVILEGES

			$privilege_data = '';
			if ($axRow['privileges_1']) { $privilege_data .= '1.'.$axRow['privileges_1'].'<br>'; }
			if ($axRow['privileges_2']) { $privilege_data .= '2.'.$axRow['privileges_2'].'<br>'; }
			if ($axRow['privileges_3']) { $privilege_data .= '3.'.$axRow['privileges_3'].'<br>'; }
			if ($axRow['privileges_4']) { $privilege_data .= '4.'.$axRow['privileges_4'].'<br>'; }
			if ($axRow['privileges_5']) { $privilege_data .= '5.'.$axRow['privileges_5'].'<br>'; }
			if ($axRow['privileges_6']) { $privilege_data .= '6.'.$axRow['privileges_6'].'<br>'; }
			if ($axRow['privileges_7']) { $privilege_data .= '7.'.$axRow['privileges_7'].'<br>'; }
			if ($axRow['privileges_8']) { $privilege_data .= '8.'.$axRow['privileges_8'].'<br>'; }
			if ($axRow['privileges_9']) { $privilege_data .= '9.'.$axRow['privileges_9'].'<br>'; }
			if ($axRow['privileges_10']) { $privilege_data .= '10.'.$axRow['privileges_10']; }
			if ($privilege_data=='') { $privilege_data = '-'; }


			# REWARD

			$reward_data = '';
			if ($axRow['reward_1']) { $reward_data .= '1.'.$axRow['reward_1'].'<br>'; }
			if ($axRow['reward_2']) { $reward_data .= '2.'.$axRow['reward_2'].'<br>'; }
			if ($axRow['reward_3']) { $reward_data .= '3.'.$axRow['reward_3'].'<br>'; }
			if ($axRow['reward_4']) { $reward_data .= '4.'.$axRow['reward_4'].'<br>'; }
			if ($axRow['reward_5']) { $reward_data .= '5.'.$axRow['reward_5'].''; }
			if ($reward_data=='') { $reward_data = '-'; }


			# PRODUCT CATEGORY

			if ($axRow['prca_ProductCategoryID'] == 0) {	$axRow['prca_ProductCategoryID'] = '-';	}
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

				$product_img = '<img src="'.$path_upload_products.$axRow['product_img'].'" width="150" height="150"/>';
			}

				
		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['card_id'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>

				<div class="modal fade" id="View'.$axRow['card_id'].'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px">'.$axRow['flag_hidden'].' &nbsp; '.$axRow['name'].'</b></span>';

		if($_SESSION['user_type_id_ses']==1){

			$view .= '	        <span style="float:right">'.$button_approve.' &nbsp; '.$button_unapprove.'</span>';
		}

		$view .= '		        <hr>
						        <center>
						        	'.$qr_code.' '.$card_data.'<br><br>
						        	<span style="font-size:12px">
					                <ul id="tapMenu" class="nav nav-tabs">
					                    <li class="active">
					                    	<a data-toggle="tab" href="#basic'.$axRow['card_id'].'">
					                    	<center><b>Basic</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#register'.$axRow['card_id'].'">
					                    	<center><b>Register Time</b></center></a>
					                   	</li>
					                    <li>
					                    	<a data-toggle="tab" href="#price'.$axRow['card_id'].'">
					                    	<center><b>Period & Price</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#limitation'.$axRow['card_id'].'">
					                    	<center><b>Limitation</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#location'.$axRow['card_id'].'">
					                    	<center><b>Location</b></center></a>
					                    </li>
					                    <li>
					                    	<a data-toggle="tab" href="#information'.$axRow['card_id'].'">
					                    	<center><b>Information</b></center></a>
					                    </li>
					                </ul>
					                </span>
					                <div class="tab-content">
					                    <div id="basic'.$axRow['card_id'].'" class="tab-pane active"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Existing Member</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['flag_existing'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Card Type</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['card_type_name'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Description</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['description'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Purpose</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['purpose'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="register'.$axRow['card_id'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Last Register Date</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['date_last_register'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="price'.$axRow['card_id'].'" class="tab-pane"><br>
					                    	<table width="80%" class="myPopup">
								        		'.$axRow['period_type'].'
								        		'.$axRow['price_type'].'
								        		<tr>
								        			<td style="text-align:right" width="45%">Multiple per Person</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['flag_multiple'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" width="45%">Special Code</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['special_code'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="limitation'.$axRow['card_id'].'" class="tab-pane"><br>
					                    	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" valign="top" width="45%">Maximum No. of Cards</td>
								        			<td style="text-align:center" valign="top" width="5%">:</td>
								        			<td>'.$axRow['limit_member'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="location'.$axRow['card_id'].'" class="tab-pane"><br>
					                    	<table style="width:90%" class="table table-striped table-bordered myPopup">
								        		<thead><tr class="th_table">
				                                    <td style="text-align:center"><b>Branch</b></td>
				                                    <td colspan="2" style="text-align:center"><b>Qr Code</b></td>
				                                </tr></thead>
				                                <tbody>'.$branch_data.'</tbody>
								        	</table>
					                    </div>
					                    <div id="information'.$axRow['card_id'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" valign="top" width="45%">Register Condition</td>
								        			<td style="text-align:center" width="5%" valign="top">:</td>
								        			<td>'.$axRow['register_condition'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">How To Activate</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['how_to_activate'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Privileges</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$privilege_data.'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Birthday Privileges</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['birthday_privileges'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">How To Use</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['how_to_use'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Collection</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['collection_data'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Re New</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['re_new'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Upgrade</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['upgrade_data'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Where To Use</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['where_to_use'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Reward</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$reward_data.'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Condition</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['condition_card'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Exception</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['exception'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right" valign="top">Source Information</td>
								        			<td style="text-align:center" valign="top">:</td>
								        			<td>'.$axRow['source_information'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                </div>
						        </center>
						    </div>
						    <div class="modal-footer">';

			if ($_SESSION['role_action']['card']['edit'] == 1) {		

				$view .= '       <a href="card_create.php?act=edit&id='.$axRow['card_id'].'">
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
							<td style="text-align:center">'.$card_image.'</td>
							<td >'.$axRow['name'].'</td>
							<td >'.$axRow['card_type_name'].'</td>';

		if($_SESSION['user_type_id_ses']==1){

			$data_table .= '<td style="text-align:center">
								<form method="POST">
									<select class="form-control text-md" name="variety_id" onchange="VarietySelect(this.value,'.$axRow['card_id'].')">
										'.$data_variety.'
		                			</select>
		                		</form>
		            		</td>
							<td style="text-align:center">
								<form method="POST">
									<select class="form-control text-md" name="display_id" onchange="DisplaySelect(this.value,'.$axRow['card_id'].')">';

			if ($axRow['display_data']=='Not Specific') { $select = 'selected'; }
			else { $select = ''; }

			$data_table .= '			<option value="Not Specific" '.$select.'>Not Specific</option>';

			if ($axRow['display_data']=='Recommend') { $select = 'selected'; }
			else { $select = ''; }

			$data_table .= '			<option value="Recommend" '.$select.'>Recommend</option>';

			if ($axRow['display_data']=='Recently') { $select = 'selected'; }
			else { $select = ''; }

			$data_table .= '			<option value="Recently" '.$select.'>Recently</option>';

			if ($axRow['display_data']=='Most Active') { $select = 'selected'; }
			else { $select = ''; }

			$data_table .= '			<option value="Most Active" '.$select.'>Most Active</option>
		                			</select>
		            			</form>
		            		</td>';
		}

		$data_table .= '	<td style="text-align:right">'.number_format($axRow['member_fee'],2).' ฿ </td>
							<td style="text-align:center">'.$status.'</td>
							<td >'.DateTime($axRow['date_update']).'</td>';

		if ($_SESSION['role_action']['card']['view'] == 1) {

			$data_table .=	'<td style="text-align:center">'.$view.'</td>';
		}

		if ($_SESSION['role_action']['card']['delete'] == 1) {

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

$oTmp->assign('is_menu', 'is_card');

$oTmp->assign('content_file', 'card/card.htm');

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

      window.location.href="card.php?act=variety_category&id="+coupon_id+"&variety_id="+variety_id;
	}
	
	function DisplaySelect(display_id,coupon_id) {

      window.location.href="card.php?act=display_data&id="+coupon_id+"&display_id="+display_id;
	}

</script>
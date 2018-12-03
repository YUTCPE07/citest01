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


$path_upload_member = $_SESSION['path_upload_member'];


$oTmp = new TemplateEngine();

$oDB = new DBI();

if ($bDebug) {

	$oErr = new Tracker();

	$oDB->SetTracker($oErr);

}

$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$path_upload_logo = $_SESSION['path_upload_logo'];

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND special_code.bran_BrandID = "'.$_SESSION['user_brand_id'].'" AND scca_Deleted != "T"';

}


$sql = 'SELECT

	special_code.*,
	mi_brand.name AS brand_name,
	mi_brand.logo_image,
	mi_brand.path_logo,
	mi_user_type.name AS user_type,
	mb_member.member_image,
	mb_member.firstname,
	mb_member.lastname,
	mb_member.facebook_id

  	FROM special_code

	LEFT JOIN mb_member

	ON special_code.memb_MemberID = mb_member.member_id

  	LEFT JOIN mi_brand

	ON mi_brand.brand_id = special_code.bran_BrandID

	LEFT JOIN mi_user

	ON mi_user.user_id = special_code.spco_CreatedBy

	LEFT JOIN mi_user_type

	ON mi_user.user_type_id = mi_user_type.user_type_id

	WHERE spco_Type = "Card"

	'.$where_brand.' 

	ORDER BY 

	CASE WHEN spco_Deleted = "" THEN 1
         WHEN spco_Deleted = "T" THEN 2
         END ASC,   

	CASE WHEN spco_Status = "Complete" THEN 1
         WHEN spco_Status = "Wait" THEN 2
         END ASC, 

	spco_CreatedDate DESC

	';


if ($Act == 'delete' && $id != '') {

		# UPDATE DELETED

		$sql = '';

		$sql .= 'SELECT spco_Deleted
				FROM special_code
				WHERE spco_SpecialCodeID ="'.$id.'"';

		$delete_status = $oDB->QueryOne($sql);


		if($delete_status=='T') {
 				
 			$do_sql_delete = "UPDATE special_code 
 							SET spco_Deleted='', 
 								spco_UpdatedBy=".$_SESSION['UID'].", 
 								spco_UpdatedDate='".$time_insert."'
 							WHERE spco_SpecialCodeID='".$id."'";
 				
 			$do_sql_list = "UPDATE special_code_list
 							SET spcl_Deleted='', 
 								spcl_UpdatedBy=".$_SESSION['UID'].", 
 								spco_UpdatedDate='".$time_insert."'
 							WHERE spco_SpecialCodeID='".$id."'";

 		} else if ($delete_status=='') {
 				
 			$do_sql_delete = "UPDATE special_code 
 							SET spco_Deleted='T', 
 								spco_UpdatedBy=".$_SESSION['UID'].", 
 								spco_UpdatedDate='".$time_insert."'
 							WHERE spco_SpecialCodeID='".$id."'";
 				
 			$do_sql_list = "UPDATE special_code_list
 							SET spcl_Deleted='T', 
 								spcl_UpdatedBy=".$_SESSION['UID'].", 
 								spco_UpdatedDate='".$time_insert."'
 							WHERE spco_SpecialCodeID='".$id."'";

		}

		$oDB->QueryOne($do_sql_delete);
		$oDB->QueryOne($do_sql_list);
 			
 		echo '<script>window.location.href="card.php";</script>';

}


if($Act=='xls'){

	require_once '../../lib/PHPExcel/Classes/PHPExcel.php';

	$objPHPExcel = new PHPExcel();

	$char = 'A';

	$chars = $char;

	$row_start = 5;

	$reportName = 'Brand Report';

	$objPHPExcel->setActiveSheetIndex(0)

				->setCellValue('A1', 'Topics this report : '.$reportName )

				->setCellValue('A2', 'Issued this report  : '.$_SESSION['UNAME'].' / '.$_SESSION['FULLNAME'])

				->setCellValue('A3', 'Check out this report : '.$time_insert)

				->setCellValue($chars++.$row_start, 'No.')

				->setCellValue($chars++.$row_start, 'Brand')

				->setCellValue($chars++.$row_start, 'Type')

				->setCellValue($chars++.$row_start, 'Email')

				->setCellValue($chars++.$row_start, 'Status')

				->setCellValue($chars++.$row_start, 'Update Date')

				->setCellValue($chars++.$row_start, 'Update By')

				->setCellValue($chars.$row_start, 'Delete');


	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:'.$chars.'1');

	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:'.$chars.'2');

	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A3:'.$chars.'3');

	$i = 6;

	$oRes = $oDB->Query($sql);

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)) {

		if($axRow['bran_Deleted']=='T') { 	$axRow['bran_Deleted']="Yes";	}
		else {	$axRow['bran_Deleted']="No";	}


		$chars = $char;

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $i, ($i-5));

		$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars++ . $i, $axRow['bran_Name'], PHPExcel_Cell_DataType::TYPE_STRING);

		$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars++ . $i, $axRow['brty_NameEn'], PHPExcel_Cell_DataType::TYPE_STRING);

		$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars++ . $i, $axRow['bran_Email'], PHPExcel_Cell_DataType::TYPE_STRING);

		$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars++ . $i, $axRow['bran_Status'], PHPExcel_Cell_DataType::TYPE_STRING);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $i, $axRow['bran_UpdatedDate']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $i, $axRow['usty_NameEn']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars . $i, $axRow['bran_Deleted']);

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
			'font'    => array(	'bold'      => true, 'color' => array('rgb' => 'FFFFFF') ),
			'alignment' => array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,	) ,
			'borders' => array(	'top'     => array( 'style' => PHPExcel_Style_Border::BORDER_THIN ) 	),
			'fill' 	=> array( 	'type'		=> PHPExcel_Style_Fill::FILL_SOLID, 'color'		=> array('rgb' => '003369') )
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

	$sql_data = $oDB->Query($sql);

	if (!$sql_data) {

		echo "An error occurred: ".mysql_error();

	} else {

		$i = 0;

		$data_table = "";

		while($axRow = $sql_data->FetchRow(DBI_ASSOC)) {

			$i++;


			# STATUS

			$status = '';

			if($axRow['spco_Deleted']=='T'){

				$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

			} else {

				if($axRow['spco_Status']=='Complete'){

					$status = '<button style="width:100px;" class="form-control text-md status_active">Complete</button>';

				} else if($axRow['spco_Status']=='Pending'){

					$status = '<button style="width:100px;" class="form-control text-md status_pending">Pending</button>';

				} else {

					$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Expire</button>';

				}
			}


			# LOGO

			if($axRow['logo_image']!=''){

				$axRow['logo_image'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" height="60" width="60" />';

			} else {

				$axRow['logo_image'] = '<img src="../../images/400x400.png" class="image_border" height="60" width="60" />';		
			}


			# MEMBER

			if($axRow['member_image']!='' && $axRow['member_image']!='user.png') {

				$member_image = '<img class="img-circle image_border" src="'.$path_upload_member.$axRow['member_image'].'"width="100" height="100"/>';

				$axRow['member_image'] = '<img class="img-circle image_border" src="'.$path_upload_member.$axRow['member_image'].'"width="70" height="70"/>';

			} else if ($axRow['facebook_id']!='') {
				
				$member_image = '<img class="img-circle image_border" src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=square" width="100" height="100" />';
				
				$axRow['member_image'] = '<img class="img-circle image_border" src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=square" width="70" height="70" />';

			} else {

				$axRow['member_image'] = '<img src="../../images/user.png" width="70" height="70" class="img-circle image_border" />';

				$member_image = '<img src="../../images/user.png" width="100" height="100" class="img-circle image_border" />';
			}


			if ($axRow['firstname']!='' || $axRow['lastname']!='') {
				
				$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>';
			
			} else { $member_name = ''; }


			# DELETED

			if($axRow['spco_Deleted']=='') {

				$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['spco_SpecialCodeID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>

					<div class="modal fade" id="Deleted'.$axRow['spco_SpecialCodeID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
							    <div class="modal-body" align="left">
							        <span style="font-size:16px"><b>Please confirm your choice</b><span>
							        <hr>
							        <center>
							        <table width="70%" class="myPopup">
							        	<tr>
							        	<td width="140px" style="text-align:center" valign="top">'.$member_image.'</td>
							        	<td>
									        <p style="font-size:14px;padding-left:10px;">
									        	<b>"'.$axRow['spco_SpecialCode'].'"</b><br>
									            By clicking the <b>"Inactive"</b> button to:<br>
									            &nbsp; &nbsp;- Inactive this Code<br>
									        </p>
									    </td>
							        	</tr>
							        </table>
							        </center>
							    </div>
							    <div class="modal-footer">
							    	<a href="card.php?act=delete&id='.$axRow['spco_SpecialCodeID'].'">
							        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
							        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
							    </div>
							</div>
						</div>
					</div>';
			
			} else if ($axRow['spco_Deleted']=='T') {
					
				$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['spco_SpecialCodeID'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>

					<div class="modal fade" id="Deleted'.$axRow['spco_SpecialCodeID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
							    <div class="modal-body" align="left">
							        <span style="font-size:16px"><b>Please confirm your choice</b><span>
							        <hr>
							        <center>
							        <table width="70%" class="myPopup">
							        	<tr>
							        	<td width="140px" style="text-align:center" valign="top">'.$member_image.'</td>
							        	<td>
									        <p style="font-size:14px;padding-left:10px;">
									        	<b>"'.$axRow['spco_SpecialCode'].'"</b><br>
									           	By clicking the <b>"Active"</b> button to:<br>
									            &nbsp; &nbsp;- Active this Code<br>
									            &nbsp; &nbsp;- Change status to Pending
									        </p>
									    </td>
							        	</tr>
							        </table>
							        </center>
							    </div>
							    <div class="modal-footer">
							    	<a href="card.php?act=delete&id='.$axRow['spco_SpecialCodeID'].'">
							        <button type="button" class="btn btn-default btn-sm">Active</button></a>
							        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
							    </div>
							</div>
						</div>
					</div>';
			}



			# VIEW

				$data_card = '';

				$sql_card = 'SELECT mi_card.*,
							mi_card_type.name AS card_type_name
							FROM special_code_list
							LEFT JOIN mi_card
							ON mi_card.card_id = special_code_list.spcl_ID
					  		LEFT JOIN mi_card_type
					    	ON mi_card.card_type_id = mi_card_type.card_type_id
							WHERE spco_SpecialCodeID="'.$axRow['spco_SpecialCodeID'].'"';

				$oRes_card = $oDB->Query($sql_card);
	 			
	 			$check_card = $oDB->QueryOne($sql_card);

				if ($check_card) {

					while ($card = $oRes_card->FetchRow(DBI_ASSOC)){


						# CARD IMAGE

						if($card['image_newupload']!=''){

							$card_image = '<img src="../../upload/'.$card['path_image'].$card['image_newupload'].'" class="image_radius image_border" width="128" height="80"/>';

						} else {

							if($card['image']!=''){

								$card_image = '<img src="../../upload/'.$card['path_image'].$card['image'].'" class="image_radius image_border" width="128" height="80"/>';

							} else {

								$card_image = '<img src="../../images/card_privilege.jpg" class="image_radius image_border" width="128" height="80"/>';
							}
						}

						# STATUS

						$card_status = '';

						if($axRow['status_del']=='1'){

							$card_status = 'Inactive';

						} else {

							if($axRow['flag_status']=='1'){

						        $card_status = 'Active';

							} else {

								$card_status = 'Pending';
							}
						}

						# PERIOD

						if ($card['period_type'] == '1') { 

							$card['period_type'] = 'Expired Date ('.DateOnly($card['date_expired']).')';	

						} else if ($card['period_type'] == '2') { 

							$card['period_type'] = $card['period_type_other'].' Months';	

						} else if ($card['period_type'] == '3') { 

							$card['period_type'] = $card['period_type_other'].' Years';	

						} else if ($card['period_type'] == '4') { 

							$card['period_type'] = 'Member Life Time';	
						}

						# PRICE

						if ($card['price_type'] == 'Free Card') { 

							$card['price_type'] = $card['price_type'];	

						} else if ($card['price_type'] == 'Not Free Card') { 

							$card['price_type'] = $card['member_fee'].' Baht';

						} else if ($card['price_type'] == 'Info. Card') { 

							$card['price_type'] = $card['member_fee'].' Baht';
						}

						$data_card .= '<tr>
										<td style="text-align:center">'.$card_image.'</td>
										<td width="80px" style="text-align:right">Name<br>
											Type<br>
											Period Type<br>
											Member Fee<br>
											Status</td>
										<td>'.$card['name'].'<br>
											'.$card['card_type_name'].'<br>
											'.$card['period_type'].'<br>
											'.$card['price_type'].'<br>
											'.$card_status.'</td>
										</tr>';
					}

				} else {

					$data_card = '<tr><td colspan="3" style="text-align:center"><b>No Card Data</b></td></tr>';
				}

					
			$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['spco_SpecialCodeID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>

					<div class="modal fade" id="View'.$axRow['spco_SpecialCodeID'].'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
						<div class="modal-dialog" role="document" style="width:60%">
							<div class="modal-content">
							    <div class="modal-body" align="left">
							        <span style="font-size:16px"><b>'.$axRow['spco_SpecialCode'].'</b></span>
							        <span style="float:right">'.$status.'</span>
							        <hr>
							        <center>
							        <table class="myPopup">
							        	<tr>
							        	<td width="150px" style="text-align:center" valign="top">'.$member_image.'</td>
							        	<td>'.$member_name.
											$axRow['spco_Email'].'<br>
											'.$axRow['spco_Mobile'].'
									    </td>
							        	</tr>
							        </table>
							        <br>
								    <table style="width:80%" class="table table-striped table-bordered myPopup">
									    <thead>
											<tr class="th_table">
											<th width="150px" style="text-align:center"><b>Card</b></th>
											<th style="text-align:center" colspan="2"><b>Card Data</b></th>
											</tr>
										</thead>
										<tbody>
								        	'.$data_card.'
										</tbody>
								    </table>
							        </center>
							    </div>
							    <div class="modal-footer">
							    <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
							    </div>
							</div>
						</div>
					</div>';


			$data_table .= '

				<tr>

					<td width="5%">'.$i.'</td>

					<td width="5%" style="text-align:center">'.$axRow['logo_image'].'<br><span style="font-size:11px;">'.$axRow['brand_name'].'</span></td>

					<td width="10%">'.$axRow['spco_SpecialCode'].'</td>

					<td width="5%" style="text-align:center">'.$axRow['member_image'].'</td>

					<td>'.$member_name.
						$axRow['spco_Email'].'<br>
						'.$axRow['spco_Mobile'].'</td>

					<td width="5%">'.$status.'</td>

					<td width="15%">'.DateTime($axRow['spco_UpdatedDate']).'</td>';

			if ($_SESSION['role_action']['pro_card']['view'] == 1) {

				$data_table .=	'<td style="text-align:center">'.$view.'</td>';
			}

			if ($_SESSION['role_action']['pro_card']['delete'] == 1) {

				$data_table .= 	'<td style="text-align:center">'.$deleted.'</td>';
			}

		}

	}

}

$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_pro_card');

$oTmp->assign('content_file', 'special_code/card.htm');

$oTmp->display('layout/layout.htm');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
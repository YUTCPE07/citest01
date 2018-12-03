<?php

header('Content-Type:text/html; charset=UTF-8');

//========================================//

ini_set("display_errors",1);
error_reporting(1);

//========================================//

include('../../include/common.php');
include('../../lib/function_normal.php');
include('../../include/common_check.php');
include("../../lib/phpmailer/class.phpmailer.php"); 
require_once ( "../../lib/phpmailer/PHPMailerAutoload.php" );
require_once('../../include/connect.php');

//========================================//


$oTmp = new TemplateEngine();
$oDB = new DBI();

if ($bDebug) {

	$oErr = new Tracker();
	$oDB->SetTracker($oErr);
}

//========================================//

if ($_SESSION['role_action']['mi_contact_us']['view'] != 1 && $_SESSION['role_action']['ma_contact_us']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$time_insert = date("Y-m-d H:i:s");

$path_upload_logo = $_SESSION['path_upload_logo'];

$Act = $_REQUEST['act'];



$sql = "SELECT mi_contact_us.*,
			mi_contact.firstname,
			mi_contact.lastname,
			mi_master.name,
			mi_brand.name AS brand_name

			FROM mi_contact_us

			LEFT JOIN mi_master
			ON mi_contact_us.contact_type = mi_master.value

			LEFT JOIN mi_user
			ON mi_user.user_id = mi_contact_us.user_id

			LEFT JOIN mi_contact
			ON mi_contact.user_id=mi_user.user_id

			LEFT JOIN mi_brand
			ON mi_brand.brand_id = mi_user.brand_id

			WHERE mi_master.type = 'contact_type'
			ORDER BY mi_contact_us.contact_date DESC";


if($Act == 'send_contact'){

	$contact_type = trim_txt($_REQUEST['contact_type']);

	$contact_text = trim_txt($_REQUEST['contact_text']);

	$contact_email = trim_txt($_REQUEST['contact_email']);

	$contact_mobile = trim_txt($_REQUEST['contact_mobile']);

	$contact_by = $_SESSION['UID'];


	if($contact_type){	$sql_contact_us .= 'contact_type="'.$contact_type.'"';   }

	if($contact_text){	$sql_contact_us .= ',contact_text="'.$contact_text.'"';   }

	if($contact_email){	$sql_contact_us .= ',contact_email="'.$contact_email.'"';   }

	if($contact_by){	$sql_contact_us .= ',user_id="'.$contact_by.'"';   }

	if($contact_mobile){	$sql_contact_us .= ',contact_mobile="'.$contact_mobile.'"';   }


	$do_sql_contact_us = 'INSERT INTO mi_contact_us SET '.$sql_contact_us;

	$oDB->QueryOne($do_sql_contact_us);		# excute data

	$page = $_SERVER['HTTP_REFERER'];



	# SEND MAIL #

	$sql_mail = "SELECT mi_contact_us.*,
						mi_contact.firstname,
						mi_contact.lastname,
						mi_master.name,
						mi_brand.name AS brand_name

					FROM mi_contact_us

					LEFT JOIN mi_master
					ON mi_contact_us.contact_type = mi_master.value

					LEFT JOIN mi_user
					ON mi_user.user_id = mi_contact_us.user_id

					LEFT JOIN mi_contact
					ON mi_contact.user_id = mi_user.user_id

					LEFT JOIN mi_brand
					ON mi_brand.brand_id = mi_user.brand_id

					WHERE mi_master.type = 'contact_type'
					AND mi_contact.user_id = ".$contact_by."
					AND mi_contact_us.contact_type = ".$contact_type;

	$oRes = $oDB->Query($sql_mail);

	$axRow_mail = $oRes->FetchRow(DBI_ASSOC);


	if ($axRow_mail['brand_name']) {

		$axRow_mail['brand_name'] = "(".$axRow_mail['brand_name'].")";
	}



	$mail = new PHPMailer();

	$mail = new PHPMailer;

	$html = "From : ".$axRow_mail['firstname']." ".$axRow_mail['lastname']." | ".$axRow_mail['type_name']." 
				".$axRow_mail['brand_name']."<br>
				Email : ".$contact_email."<br>
				Mobile : ".$contact_mobile."<br>
				Date : ".date('Y-m-d H:i:s')."<br><br>
				".$axRow_mail['name']." :<br>
		 		".$contact_text."";

	$mail->Debugoutput = 'html';

	//Set the hostname of the mail server

	$mail->Host = 'mail.memberin.com';

	// use

	// $mail->Host = gethostbyname('smtp.gmail.com');

	// if your network does not support SMTP over IPv6

	//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission

	$mail->SMTPSecure = '25';

	//Set the encryption system to use - ssl (deprecated) or tls

	//Whether to use SMTP authentication

	$mail->SMTPAuth = true;

	//Username to use for SMTP authentication - use full email address for gmail

	$mail->Username = "noreply@memberin.com";

	//Password to use for SMTP authentication

	$mail->Password = "m3mb3rIN@2016";

	//Set who the message is to be sent from

	$mail->CharSet = 'UTF-8';

	// Set PHPMailer to use the sendmail transport

	$mail->isSendmail();

	//Set who the message is to be sent from

	$mail->setFrom('noreply@memberin.com', 'MemberIn');

	//Set an alternative reply-to address

	//Set who the message is to be sent to

	$mail->addAddress('lechieng.k@gmail.com');

	//Set the subject line

	$mail->Subject = 'MemberIn | Contact Us';

	//Read an HTML message body from an external file, convert referenced images to embedded,

	//convert HTML into a basic plain-text alternative body

	$mail->msgHTML($html);

	$mail->send();

	echo '<script>alert("Send Contact Complete ...");
			window.location.href = "'.$page.'";
		</script>';

	exit;


} else if($Act=='xls'){

	require_once '../../lib/PHPExcel/Classes/PHPExcel.php';

	$objPHPExcel = new PHPExcel();

	$char = 'A';

	$chars = $char;

	$row_start = 5;

	$reportName = 'Branch Report';

	$objPHPExcel->setActiveSheetIndex(0)

				->setCellValue('A1', 'Topics this report : '.$reportName )

				->setCellValue('A2', 'Issued this report  : '.$_SESSION['UNAME'].' / '.$_SESSION['FULLNAME'])

				->setCellValue('A3', 'Check out this report : '.$time_insert)

				->setCellValue($chars++.$row_start, 'No.')

				->setCellValue($chars++.$row_start, 'Type')

				->setCellValue($chars++.$row_start, 'Detail')

				->setCellValue($chars++.$row_start, 'By')

				->setCellValue($chars++.$row_start, 'Email')

				->setCellValue($chars++.$row_start, 'Mobile')

				->setCellValue($chars.$row_start, 'Contact Date');

	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:'.$chars.'1');

	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:'.$chars.'2');

	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A3:'.$chars.'3');

	$i = 6;

	$oRes = $oDB->Query($sql);

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)) {

		if ($axRow['brand_name']) { $axRow['brand_name'] = '('.$axRow['brand_name'].')'; }

		$name_by = $axRow['firstname'].' '.$axRow['lastname'].' '.$axRow['brand_name'];

		$chars = $char;

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $i, ($i-5));

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $i, $axRow['name']);

		$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars++ . $i, $axRow['contact_text']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $i, $name_by);

		$objPHPExcel->getActiveSheet()->setCellValueExplicit($chars++ . $i, $axRow['contact_email']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $i, $axRow['contact_mobile']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars . $i, $axRow['contact_date']);

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

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;

		if ($axRow['brand_name']) { $axRow['brand_name'] = '('.$axRow['brand_name'].')'; }

		$name_by = $axRow['firstname'].' '.$axRow['lastname'].' '.$axRow['brand_name'];

		$data_table .= '<tr >
							<td>'.$i.'</td>
							<td>'.$axRow['name'].'</td>
							<td>'.$axRow['contact_text'].'</td>
							<td>'.$name_by.'</td>
							<td><a href="mailto:'.$axRow['contact_email'].'?subject='.$axRow['contact_type_name'].'">'.$axRow['contact_email'].'</a></td>
							<td>'.$axRow['contact_mobile'].'</td>								
							<td>'.DateTime($axRow['contact_date']).'</td>
						</tr>';

		$asData[] = $axRow;
	}
}



$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_contact_us');

$oTmp->assign('content_file', 'contact/contact.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
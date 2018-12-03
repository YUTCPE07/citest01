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

if ($_SESSION['role_action']['earn_attention_report']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$path_upload_member = $_SESSION['path_upload_member'];
$StartDate = $_REQUEST['StartDate'];
$EndDate = $_REQUEST['EndDate'];
$Act = $_REQUEST['act'];


$where_brand = "";

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND mi_branch.brand_id = "'.$_SESSION['user_brand_id'].'"';
}

if($_SESSION['user_branch_id']){

	$where_brand .= ' AND mi_branch.branch_id = "'.$_SESSION['user_branch_id'].'"';
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

else {	$where_search = " AND mi_brand.brand_id IN (".$brand_id.")";	}

if ($StartDate && $EndDate) {

	$date = $EndDate;

	$date1 = str_replace('-', '/', $date);

	$EndDate1 = date('Y-m-d',strtotime($date1 . "+1 days"));

	$where_date = ' AND hilight_coupon_trans.hico_CreatedDate BETWEEN "'.$StartDate.'" AND "'.$EndDate1.'" ';

	$oTmp->assign('dataStartDate', $StartDate);

	$oTmp->assign('dataEndDate', $EndDate);

} else if ($StartDate) {

	$where_date = ' AND hilight_coupon_trans.hico_CreatedDate >= "'.$StartDate.'" ';

	$oTmp->assign('dataStartDate', $StartDate);

} else if ($EndDate) {

	$date = $EndDate;

	$date1 = str_replace('-', '/', $date);

	$EndDate1 = date('Y-m-d',strtotime($date1 . "+1 days"));

	$where_date = ' AND hilight_coupon_trans.hico_CreatedDate <= "'.$EndDate1.'" ';

	$oTmp->assign('dataEndDate', $EndDate);

} else {

	$where_date = '';
}


$sql_earn ='SELECT
					hilight_coupon_trans.hico_HilightCouponID,
					hilight_coupon_trans.hico_CreatedDate,
					hilight_coupon.coup_Name AS coupon_name,
					hilight_coupon.coup_Image,
					hilight_coupon.coup_ImageNew,
					hilight_coupon.coup_ImagePath,
					hilight_coupon.coup_CouponID AS coupon_id,
					hilight_coupon.coup_Type AS coupon_type,
					mi_brand.name AS brand_name,
					mi_brand.logo_image,
					mi_brand.path_logo,
					mb_member.firstname,
					mb_member.lastname,
					mb_member.facebook_name,
					mb_member.email AS member_email,
					mb_member.mobile AS member_mobile,
					mb_member.member_image AS member_image,
					mb_member.facebook_id AS facebook_id,
					mb_member.flag_gender AS gender,
					mb_member.date_birth AS birthday,
					mb_member.platform AS platform,
					mi_branch.name AS branch_name,
					hilight_coupon_trans.hico_Like,
					hilight_coupon_trans.hico_Comment

					FROM hilight_coupon_trans

					LEFT JOIN mb_member
					ON  hilight_coupon_trans.memb_MemberID = mb_member.member_id

					LEFT JOIN mi_branch
					ON hilight_coupon_trans.brnc_BranchID = mi_branch.branch_id

					LEFT JOIN hilight_coupon
					ON hilight_coupon_trans.coup_CouponID = hilight_coupon.coup_couponID

					LEFT JOIN mi_brand
					ON mi_branch.brand_id = mi_brand.brand_id

					WHERE 1
					'.$where_brand.'
					'.$where_search.'
					'.$where_date.'

					ORDER BY hilight_coupon_trans.hico_CreatedDate DESC';

if($Act=='xls'){

	require_once '../../lib/PHPExcel/Classes/PHPExcel.php';

	$objPHPExcel = new PHPExcel();

	$char = 'A';

	$chars = $char;

	$row_start = 1;

	$reportName = 'Earn Attention Report';

	$objPHPExcel->setActiveSheetIndex(0)

				// ->setCellValue('A1', 'Topics this report : '.$reportName )

				// ->setCellValue('A2', 'Issued this report  : '.$_SESSION['UNAME'].' / '.$_SESSION['FULLNAME'])

				// ->setCellValue('A3', 'Check out this report : '.$time_insert)

				->setCellValue($chars++.$row_start, 'No.')

				->setCellValue($chars++.$row_start, 'Use Date')

				->setCellValue($chars++.$row_start, 'Used Code')

				->setCellValue($chars++.$row_start, 'Member')

				->setCellValue($chars++.$row_start, 'Gender')

				->setCellValue($chars++.$row_start, 'Birthday')

				->setCellValue($chars++.$row_start, 'Email')

				->setCellValue($chars++.$row_start, 'Mobile')

				->setCellValue($chars++.$row_start, 'Earn Attention')

				->setCellValue($chars++.$row_start, 'Like')

				->setCellValue($chars++.$row_start, 'Review')

				->setCellValue($chars.$row_start, 'Branch');

	// $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:'.$chars.'1');

	// $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:'.$chars.'2');

	// $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A3:'.$chars.'3');

	$i = 1;

	$row_start++;

	$oRes = $oDB->Query($_SESSION['earn_export']);

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)) {

		# FIRSTNAME

		$sql_field ='SELECT hcre_Deleted 
						FROM hilight_coupon_request 
						WHERE coup_couponID="'.$earn['coupon_id'].'"
						AND mafi_MasterFieldID="2"';
		$firstname = $oDB->QueryOne($sql_field);
		if ($firstname=="T") { $earn['firstname'] = ''; } 


		# LASTNAME

		$sql_field ='SELECT hcre_Deleted 
						FROM hilight_coupon_request 
						WHERE coup_couponID="'.$earn['coupon_id'].'"
						AND mafi_MasterFieldID="3"';
		$lastname = $oDB->QueryOne($sql_field);
		if ($lastname=="T") { $earn['lastname'] = ''; } 


		# GENDER

		$sql_field = 'SELECT hcre_Deleted 
						FROM hilight_coupon_request 
						WHERE coup_couponID="'.$axRow['coupon_id'].'"
						AND mafi_MasterFieldID="5"';

		$gender = $oDB->QueryOne($sql_field);
		if ($gender=="T") { $axRow['gender'] = ''; }
		else { 

			if ($axRow['gender']==1) { $axRow['gender'] = 'Male'; }
			else { $axRow['gender'] = 'Female';} 
		}


		# BIRTHDAY

		$sql_field = 'SELECT hcre_Deleted 
						FROM hilight_coupon_request 
						WHERE coup_couponID="'.$axRow['coupon_id'].'"
						AND mafi_MasterFieldID="6"';

		$birthday = $oDB->QueryOne($sql_field);

		if ($birthday=="T" || $axRow['birthday']=='0000-00-00') { $axRow['birthday'] = ''; }
		else {	$axRow['birthday'] = DateOnly($axRow['birthday']); }


		# MOBILE

		$sql_field = 'SELECT hcre_Deleted 
						FROM hilight_coupon_request 
						WHERE coup_couponID="'.$axRow['coupon_id'].'"
						AND mafi_MasterFieldID="20"';

		$mobile = $oDB->QueryOne($sql_field);

		if ($mobile=="T") { $axRow['member_mobile'] = ''; }


		# EMAIL

		$sql_field = 'SELECT hcre_Deleted 
						FROM hilight_coupon_request 
						WHERE coup_couponID="'.$axRow['coupon_id'].'"
						AND mafi_MasterFieldID="23"';

		$email = $oDB->QueryOne($sql_field);

		if ($email=="T") { $axRow['member_email'] = ''; }

		if ($axRow['hico_Like'] == 'Like') { $like = $axRow['hico_Like']; }
		else { $like = '-'; }

		if ($axRow['hico_Comment'] == '') { $comment = '-'; }


		$chars = $char;

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $i++);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, DateTime($axRow['hico_CreatedDate']));

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['hico_HilightCouponID']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['firstname'].' '.$axRow['lastname']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['gender']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['birthday']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['member_email']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['member_mobile']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $axRow['coupon_name']);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $like);

		$objPHPExcel->getActiveSheet()->setCellValue($chars++ . $row_start, $comment);

		$objPHPExcel->getActiveSheet()->setCellValue($chars . $row_start, $axRow['branch_name']);

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


	$_SESSION['earn_export'] = $sql_earn;

	$data_earn = "";

	$earn_n = "1";

	$rs_earn = $oDB->Query($sql_earn);

	if (!$rs_earn) {

		echo "An error occurred: ".mysql_error();

	} else {

		while($axRow = $rs_earn->FetchRow(DBI_ASSOC)) {

			# MEMBER	

			if($axRow['member_image']!='' && $axRow['member_image']!='user.png'){

				$axRow['member_image'] = '<img src="'.$path_upload_member.$axRow['member_image'].'" width="50" height="50" class="img-circle image_border"/>';	

			} else if ($axRow['facebook_id']!='') {

				$axRow['member_image'] = '<img src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=large" width="50" height="50" class="img-circle image_border"/>';

			} else {

				$axRow['member_image'] = '<img src="../../images/user.png" width="50" height="50" class="img-circle image_border"/>';
			}

			$member_name = '';

			if ($axRow['firstname'] || $axRow['lastname']) {

				if ($axRow['member_email']) {

					if ($axRow['member_mobile']) {
								
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>'.$axRow['member_mobile'];

					} else { $member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email']; }

				} else {

					if ($axRow['member_mobile']) {
								
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_mobile'];

					} else { $member_name = $axRow['firstname'].' '.$axRow['lastname']; }
				}

			} else {

				if ($axRow['member_email']) {

					if ($axRow['member_mobile']) { $member_name = $axRow['member_email'].'<br>'.$axRow['member_mobile'];

					} else { $member_name = $axRow['member_email']; }

				} else {

					if ($axRow['member_mobile']) { $member_name = $axRow['member_mobile'];

					} else { $member_name = ''; }
				}
			}


			# LOGO

			if($axRow['logo_image']!=''){

				$axRow['logo_image'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="50" height="50"/>';

			} else {

				$axRow['logo_image'] = '<img src="../../images/400x400.png" class="image_border" width="50" height="50"/>';
			}


			# EARN ATTENTION IMAGE

			if($axRow['coup_ImageNew']!=''){

				$coup_image = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_ImageNew'].'" class="image_border" width="80" height="50"/>';

			} else if($axRow['coup_Image']!=''){

				$coup_image = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_Image'].'" class="image_border" width="80" height="50"/>';

			} else {

				$coup_image = '<img src="../../images/card_privilege.jpg" width="80" height="50"/>';
			}

			if ($axRow['hico_Like'] != 'Like') { $axRow['hico_Like'] = '-'; }
			else { $axRow['hico_Like'] = '<span style="font-size:15px;" class="glyphicon glyphicon-thumbs-up"></span>'; }

			if ($axRow['hico_Comment'] == '') { $axRow['hico_Comment'] = '-'; }


			# TABLE

		  	$data_earn .= '<tr>
								<td>'.$earn_n++.'</td>
								<td style="text-align:center">'.$axRow['hico_HilightCouponID'].'</td>
								<td style="text-align:center">'.$axRow['member_image'].'</td>
								<td>'.$member_name.'</td>';

			if ($axRow['coupon_type']=='Use') {

				$data_earn .= '	<td style="text-align:center"><a href="../promotion/use.php">'.$coup_image.'<br>
									<span style="font-size:11px">'.$axRow['coupon_name'].'</span></td>';

			} else {

				$data_earn .= '	<td style="text-align:center"><a href="../promotion/buy.php">'.$coup_image.'<br>
									<span style="font-size:11px">'.$axRow['coupon_name'].'</span></td>'; 
			}

			$data_earn .= '		<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['logo_image'].'</a><br>
									<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
								<td >'.$axRow['branch_name'].'</td>
								<td style="text-align:center">'.$axRow['hico_Like'].'</td>
								<td >'.$axRow['hico_Comment'].'</td>
								<td style="text-align:center">'.$axRow['platform'].'</td>
								<td>'.DateTime($axRow['hico_CreatedDate']).'</td>
							</tr>' ;
		}
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

$oTmp->assign('data_earn', $data_earn);

$oTmp->assign('is_menu', 'is_analytics');

$oTmp->assign('content_file','analytics/earn_use.htm');

$oTmp->display('layout/template.html');


//========================================//


$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
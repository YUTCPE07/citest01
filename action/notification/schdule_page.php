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

$sql = 'SELECT schdule_notification.*,
		mi_brand.name AS brand_name,
		mi_brand.logo_image,
		mi_brand.path_logo,
		target_list.tali_Name,
		push_notification.puno_Header,
		IF(schdule_notification.puno_Type="Push","Push Notification",schdule_notification.puno_Type) AS puno_Type,
		IF(schdule_notification.puno_Type="Push",
			DATE_FORMAT(schdule_notification.scno_StartDate,"%d %M %Y"),
			CONCAT(DATE_FORMAT(schdule_notification.scno_StartDate,"%d %M %Y"),"<br>-<br>",
			DATE_FORMAT(schdule_notification.scno_EndDate,"%d %M %Y"))) AS schd_date,
		IF(schdule_notification.puno_Type="Push",
			DATE_FORMAT(schdule_notification.scno_StartTime,"%H:%i"),
			CONCAT(DATE_FORMAT(schdule_notification.scno_StartDate,"%H:%i"),"<br>-<br>",
			DATE_FORMAT(schdule_notification.scno_EndTime,"%H:%i"))) AS schd_time
		FROM schdule_notification 

		LEFT JOIN target_list
		ON schdule_notification.tali_TargetListID = target_list.tali_TargetListID

		LEFT JOIN push_notification
		ON schdule_notification.puno_PushNotificationID = push_notification.puno_PushNotificationID

		LEFT JOIN mi_brand
		ON schdule_notification.bran_BrandID = mi_brand.brand_id

		ORDER BY scno_UpdatedDate DESC';

$oRes = $oDB->Query($sql);

$i=0;

$data_table = '';

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$i++;


	# LOGO

	if($axRow['logo_image']!=''){

		$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';

	} else {

		$logo_brand = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';
	}

	if ($axRow['bran_BrandID']=='0') {

		$logo_brand = '<img src="../../images/mi_action_logo.png" width="60" class="image_border" height="60"/>';

		$axRow['brand_name'] = 'MemberIn';
	}



	# DATA TABLE

	$data_table .= '<tr >
						<td >'.$i.'</td>
						<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
							<span style="font-size:11px;">'.$axRow['brand_name'].'</span>
						</td>
						<td>'.$axRow['scno_Name'].'</td>
						<td>'.$axRow['puno_Header'].'</td>
						<td>'.$axRow['tali_Name'].'</td>
						<td>'.$axRow['puno_Type'].'</td>
						<td style="text-align:center">'.$axRow['schd_date'].'</td>
						<td style="text-align:center">'.$axRow['schd_time'].'</td>
						<td style="text-align:center">'.DateTime($axRow['scno_UpdatedDate']).'</td>';

	if ($_SESSION['role_action']['push_notification']['edit'] == 1) {

		$data_table .=	'<td style="text-align:center">
							<a href="schdule_create.php?act=edit&id='.$axRow['scno_SchduleNotificationID'].'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></a></td>';
	}

	$data_table .=	'</tr>';
}



$oTmp->assign('data_table', $data_table);

$oTmp->assign('content_file', 'notification/schdule_page.htm');
$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
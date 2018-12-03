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

$sql = 'SELECT push_notification.*,
		IF(push_notification.puno_Type="Push","Push Notification",push_notification.puno_Type) AS puno_Type,
		mi_brand.name AS brand_name,
		mi_brand.logo_image,
		mi_brand.path_logo
		FROM push_notification 

		LEFT JOIN mi_brand
		ON push_notification.bran_BrandID = mi_brand.brand_id

		ORDER BY puno_UpdatedDate DESC';

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



	# VIEW

	$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Basic'.$axRow['puno_PushNotificationID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>

			<div class="modal fade" id="Basic'.$axRow['puno_PushNotificationID'].'" tabindex="-1" role="dialog" aria-labelledby="BasicDataLabel">
				<div class="modal-dialog" role="document" style="width:325px">
					<div class="modal-content">
						<div class="modal-body" align="left">
							<span style="text-align:left">
								<span style="font-size:16px">'.$axRow['puno_Header'].'</span><br>
								'.nl2br($axRow['puno_Detail']).'
							</span>
							<hr>
							<center>
							    <img src="../../upload/'.$axRow['puno_ImagePath'].$axRow['puno_Image'].'" width="280"/>
							</center>
							<div>
								<br>'.htmlspecialchars_decode(htmlspecialchars_decode(base64_decode($axRow['puno_Description']))).'<br><br>
								<button type="button" class="btn btn-danger btn-lg" style="width:280px;background-color:#c9302c;color:white;border:none"> See More </button><br><br>
							</div>
						</div>';
	$view .= '			
					</div>
				</div>
			</div>';



	# DATA TABLE

	$data_table .= '<tr >
						<td >'.$i.'</td>
						<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
							<span style="font-size:11px;">'.$axRow['brand_name'].'</span>
						</td>
						<td>'.$axRow['puno_Header'].'</td>
						<td>'.nl2br($axRow['puno_Detail']).'</td>
						<td>'.$axRow['puno_Type'].'</td>
						<td style="text-align:center">'.DateTime($axRow['puno_UpdatedDate']).'</td>
						<td style="text-align:center">'.$view.'</td>';

	if ($_SESSION['role_action']['push_notification']['edit'] == 1) {

		$data_table .=	'<td style="text-align:center">
							<a href="push_notification_create.php?act=edit&id='.$axRow['puno_PushNotificationID'].'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></a></td>';
	}

	$data_table .=	'</tr>';
}



$oTmp->assign('data_table', $data_table);
$oTmp->assign('content_file', 'notification/push_notification.htm');
$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
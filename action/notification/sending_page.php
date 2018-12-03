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

if ($_SESSION['role_action']['push_notification']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$sql = 'SELECT IF(sending_result.sere_Status="Read",
			CONCAT("<span style=\'color:green\'>",sending_result.sere_Status,"</span>"),
			CONCAT("<span style=\'color:orange\'>",sending_result.sere_Status,"</span>")) AS sere_Status,
		sending_result.sere_SendDate,
		push_notification.puno_Header,
		push_notification.puno_Type,
		push_notification.bran_BrandID,
		mb_member.firstname,
		mb_member.lastname,
		mb_member.email,
		mb_member.mobile,
		mb_member.facebook_id,
		mi_brand.name AS brand_name,
		mi_brand.logo_image,
		mi_brand.path_logo

		FROM sending_result 

		LEFT JOIN mb_member
		ON sending_result.memb_MemberID = mb_member.member_id

		LEFT JOIN schdule_notification
		ON schdule_notification.scno_SchduleNotificationID = sending_result.scno_SchduleNotificationID

		LEFT JOIN push_notification
		ON push_notification.puno_PushNotificationID = schdule_notification.puno_PushNotificationID

		LEFT JOIN mi_brand
		ON push_notification.bran_BrandID = mi_brand.brand_id

		ORDER BY sending_result.sere_SendDate DESC';

$oRes = $oDB->Query($sql);

$i=0;

$data_table = '';

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$i++;


	# LOGO

	if($axRow['logo_image']!=''){

		$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="50" height="50"/>';

	} else {

		$logo_brand = '<img src="../../images/400x400.png" class="image_border" width="50" height="50"/>';
	}

	if ($axRow['bran_BrandID']=='0') {

		$logo_brand = '<img src="../../images/mi_action_logo.png" width="50" class="image_border" height="50"/>';
		$axRow['brand_name'] = 'MemberIn';
	}


	# MEMBER

	$member_name = '';

	if ($axRow['firstname'].' '.$axRow['lastname']) {

		if ($axRow['email']) {

			if ($axRow['mobile']) {

				$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'];

			} else { $member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email']; }

		} else {

			if ($axRow['mobile']) { 

				$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile']; 

			} else { $member_name = $axRow['firstname'].' '.$axRow['lastname']; }
		}

	} else {

		if ($axRow['email']) {

			if ($axRow['mobile']) {

				$member_name = $axRow['email'].'<br>'.$axRow['mobile'];

			} else { $member_name = $axRow['email']; }

		} else {

			if ($axRow['mobile']) { 

				$member_name = $axRow['mobile']; 

			} else { $member_name = ''; }
		}
	}

		
	if($axRow['member_image']!='' && $axRow['member_image']!='user.png'){

		$axRow['member_image'] = '<img src="'.$path_upload_member.$axRow['member_image'].'" width="50" height="50" class="img-circle image_border"/>';	

	} else if ($axRow['facebook_id']!='') {

		$axRow['member_image'] = '<img src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=large" width="50" height="50" class="img-circle image_border"/>';

	} else {

		$axRow['member_image'] = '<img src="../../images/user.png" width="50" height="50" class="img-circle image_border"/>';
	}


	# DATA TABLE

	$data_table .= '<tr >
						<td >'.$i.'</td>
						<td style="text-align:center">'.$axRow['member_image'].'</td>
						<td>'.$member_name.'</td>
						<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
							<span style="font-size:11px;">'.$axRow['brand_name'].'</span></td>
						<td>'.$axRow['puno_Header'].'</td>
						<td style="text-align:center">'.$axRow['puno_Type'].'</td>
						<td style="text-align:center">'.$axRow['sere_Status'].'</td>
						<td style="text-align:center">'.DateTime($axRow['sere_SendDate']).'</td>
					</tr>';
}



$oTmp->assign('data_table', $data_table);
$oTmp->assign('content_file', 'notification/sending_page.htm');
$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
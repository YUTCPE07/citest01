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

if ($_SESSION['role_action']['member_new']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");	

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$path_upload_member = $_SESSION['path_upload_member'];

$today = date("Y-m-d H:i:s");

$day = date("Y-m-d");


# MEMBER

$sql ='SELECT member_id,facebook_id,email,firstname,lastname,mobile,member_image,member_token,date_create,platform,
				(SELECT COUNT(card_id)
					FROM mb_member_register
					WHERE member_id = mb_member.member_id
					AND (date_expire > "'.$today.'"
					AND (date_start <= "'.$day.'" || date_start = "0000-00-00")
					OR period_type="4")
					AND flag_del="") AS card_active,
				(SELECT COUNT(card_id)
					FROM mb_member_register
					WHERE member_id = mb_member.member_id
					AND date_expire <= "'.$today.'"
					AND (date_start <= "'.$day.'" || date_start = "0000-00-00")
					AND date_expire != date_create
					AND flag_del!="") AS card_expire,
				(SELECT COUNT(*) 
					FROM hilight_coupon_trans 
					WHERE memb_MemberID = mb_member.member_id) AS earn_use
		FROM mb_member 
		ORDER BY mb_member.date_create DESC';

$oRes_member = $oDB->Query($sql);

$x = 1;

$data_table = '';

while ($member = $oRes_member->FetchRow(DBI_ASSOC)){

	# MEMBER IMAGE

	if($member['member_image']!='' && $member['member_image']!='user.png'){

		$member['member_image'] = '<img src="'.$path_upload_member.$member['member_image'].'" width="60" height="60" class="img-circle image_border"/>';	

	} else if ($member['facebook_id']!='') {

		$member['member_image'] = '<img src="http://graph.facebook.com/'.$member['facebook_id'].'/picture?type=square" width="60" height="60" class="img-circle image_border"/>';

	} else {

		$member['member_image'] = '<img src="../../images/user.png" width="60" height="60" class="img-circle image_border"/>';
	}



	# MEMBER STATUS

	if ($member['member_token']) { 

		$member_status = '<span class="glyphicon glyphicon-exclamation-sign" style="color:#f0ad4e;font-size:20px"></span>'; 
	}



	# MEMBER NAME

	$member_name = '';

	if ($member['firstname'] || $member['lastname']) {

		if ($member['email']) {

			if ($member['mobile']) {
								
				$member_name = $member['firstname'].' '.$member['lastname'].'<br>'.$member['email'].'<br>'.$member['mobile'];

			} else { $member_name = $member['firstname'].' '.$member['lastname'].'<br>'.$member['email']; }

		} else {

			if ($member['mobile']) {
								
				$member_name = $member['firstname'].' '.$member['lastname'].'<br>'.$member['mobile'];

			} else { $member_name = $member['firstname'].' '.$member['lastname']; }
		}

	} else {

		if ($member['email']) {

			if ($member['mobile']) { $member_name = $member['email'].'<br>'.$member['mobile'];

			} else { $member_name = $member['email']; }

		} else {

			if ($member['mobile']) { $member_name = $member['mobile'];

			} else { $member_name = ''; }
		}
	}



	# DATA TABLE

	$data_table .= '<tr>
						<td>'.$x++.'<br><center>'.$member_status.'</center></td>
						<td style="text-align:center">'.$member['member_image'].'</td>
						<td>'.$member_name.'</td>
						<td style="text-align:center">'.$member['earn_use'].'</td>
						<td style="text-align:center">'.$member['card_active'].'</td>
						<td style="text-align:center">'.$member['card_expire'].'</td>
						<td style="text-align:center">'.$member['platform'].'</td>
						<td>'.DateTime($member['date_create']).'</td>
						<td style="text-align:center" width="5%"><span style="cursor:pointer" onclick="'."window.location.href='member_detail.php?id=".$member['member_id']."'".'" target="_blank"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>';

	$data_table .= '</tr>';
}





$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_member');

$oTmp->assign('content_file', 'member/new_member.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
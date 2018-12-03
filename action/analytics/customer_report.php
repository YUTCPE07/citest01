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

if ($_SESSION['role_action']['customer_balance']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$path_upload_member = $_SESSION['path_upload_member'];


$where_member = '';

if($_SESSION['user_type_id_ses'] > 1) {

	## MEMBER ID

	$sql_count = "SELECT count(*)
					FROM mb_member_register
					LEFT JOIN mi_card
					ON mb_member_register.card_id = mi_card.card_id
					WHERE mi_card.brand_id=".$_SESSION['user_brand_id'];

	$count_regis = $oDB->QueryOne($sql_count);

	$member_register = "SELECT member_id 
						FROM mb_member_register
						LEFT JOIN mi_card
						ON mb_member_register.card_id = mi_card.card_id
						WHERE mi_card.brand_id=".$_SESSION['user_brand_id'];

	## CARD ID

	$sql_count_card = "SELECT count(*)
						FROM mi_card
						WHERE brand_id=".$_SESSION['user_brand_id'];

	$count_card = $oDB->QueryOne($sql_count_card);

	$sql_card = "SELECT card_id 
						FROM mi_card
						WHERE brand_id=".$_SESSION['user_brand_id'];

	$data_register = "";

	$i = 1;

	$register = $oDB->Query($member_register);

	while($axRow_register = $register->FetchRow(DBI_ASSOC)) {

		if ($i == $count_regis) { $data_register .= $axRow_register['member_id']; } 
		else { $data_register .= $axRow_register['member_id'].","; }

		$i++;
	}

	$data_card = "";

	$j = 1;

	$card = $oDB->Query($sql_card);

	while($axRow_card = $card->FetchRow(DBI_ASSOC)) {

		if ($j == $count_card) { $data_card_count .= $axRow_card['card_id']; } 
		else { $data_card_count .= $axRow_card['card_id'].","; }

		$j++;
	}

	if ($data_register) {

		$where_member .= " AND mb_member.member_id IN (".$data_register.")";

	} else {

		$where_member .= " AND mi_card.brand_id =".$_SESSION['user_brand_id'];
	}

} else {

	$sql_count = "SELECT count(*)
					FROM mb_member_register
					LEFT JOIN mi_card
					ON mb_member_register.card_id = mi_card.card_id";

	$count_regis = $oDB->QueryOne($sql_count);

	$member_register = "SELECT member_id 
						FROM mb_member_register
						LEFT JOIN mi_card
						ON mb_member_register.card_id = mi_card.card_id";

	$data_register = "";

	$i = 1;

	$register = $oDB->Query($member_register);

	while($axRow_register = $register->FetchRow(DBI_ASSOC)) {

		if ($i == $count_regis) { $data_register .= $axRow_register['member_id']; } 
		else { $data_register .= $axRow_register['member_id'].","; }
		$i++;
	}

	if ($data_register) {

		$where_member .= " AND mb_member.member_id IN (".$data_register.")";
	}
}

$sql_member = "SELECT firstname, 
						lastname, 
						facebook_id, 
						facebook_name, 
						member_id, 
						member_image,
						email,
						mobile,
						MAX(date_use) AS use_date FROM (

						SELECT MAX(member_privilege_trans.mepe_CreatedDate) AS date_use,
								mb_member.*
							FROM mb_member
							LEFT JOIN member_privilege_trans
							ON member_privilege_trans.memb_MemberID = mb_member.member_id
							LEFT JOIN mb_member_register
							ON mb_member_register.member_id = mb_member.member_id
							LEFT JOIN mi_card
							ON mb_member_register.card_id = mi_card.card_id
							WHERE member_privilege_trans.mepe_Deleted=''
							".$where_member."
							GROUP BY mb_member.member_id

						UNION

						SELECT MAX(member_coupon_trans.meco_CreatedDate) AS date_use,
								mb_member.*
							FROM mb_member
							LEFT JOIN member_coupon_trans
							ON member_coupon_trans.memb_MemberID = mb_member.member_id
							LEFT JOIN mb_member_register
							ON mb_member_register.member_id = mb_member.member_id
							LEFT JOIN mi_card
							ON mb_member_register.card_id = mi_card.card_id
							WHERE member_coupon_trans.meco_Deleted=''
							".$where_member."
							GROUP BY mb_member.member_id

						UNION

						SELECT MAX(member_activity_trans.meac_CreatedDate) AS date_use,
								mb_member.*
							FROM mb_member
							LEFT JOIN member_activity_trans
							ON member_activity_trans.memb_MemberID = mb_member.member_id
							LEFT JOIN mb_member_register
							ON mb_member_register.member_id = mb_member.member_id
							LEFT JOIN mi_card
							ON mb_member_register.card_id = mi_card.card_id
							WHERE member_activity_trans.meac_Deleted=''
							".$where_member."
							GROUP BY mb_member.member_id

					) member_trans

					GROUP BY member_id 
					ORDER BY use_date DESC";

$rs_member = $oDB->Query($sql_member);

$i=0;

$data_table = '';

while ($axRow = $rs_member->FetchRow(DBI_ASSOC)){

	$i++;

	# MEMBER

	if($axRow['member_image']!='' && $axRow['member_image']!='user.png') {

		$axRow['member_image'] = '<img class="img-circle image_border" src="'.$path_upload_member.$axRow['member_image'].'"width="60" height="60"/>';

	} else if ($axRow['facebook_id']!='') {
			
		$axRow['member_image'] = '<img class="img-circle image_border" src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=square" width="60" height="60" />';
	
	} else {

		$axRow['member_image'] = '<img src="../../images/user.png" width="60" height="60" class="img-circle image_border" />';
	}

	$member_name = '';

	if ($axRow['firstname'] || $axRow['lastname']) {

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

			if ($axRow['mobile']) { $member_name = $axRow['email'].'<br>'.$axRow['mobile'];

			} else { $member_name = $axRow['email']; }

		} else {

			if ($axRow['mobile']) { $member_name = $axRow['mobile'];

			} else { $member_name = ''; }
		}
	}


	# DATE USE

	if (!$axRow['use_date']) {	$axRow['use_date'] = "-";	}
	else { $axRow['use_date'] = DateTime($axRow['use_date']); }


	# TABLE

	$data_member .= '<tr>
						<td>'.$i.'</td>
						<td style="text-align:center">'.$axRow['member_image'].'</td>
						<td>'.$member_name.'</td>
						<td style="text-align:center">'.$axRow['use_date'].'</td>
						<td style="text-align:center">
						  	<span style="cursor:pointer" onclick="'."window.location.href='customer_balance.php?id=".$axRow['member_id']."'".'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
	  				</tr>';
}


$oTmp->assign('data_member', $data_member);
$oTmp->assign('is_menu', 'is_analytics');
$oTmp->assign('content_file', 'analytics/customer_report.htm');
$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
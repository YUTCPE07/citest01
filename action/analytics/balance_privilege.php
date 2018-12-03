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


$id = $_REQUEST['id'];
$card_id = $_REQUEST['card_id'];
$type = $_REQUEST['type'];

$path_upload_member = $_SESSION['path_upload_member'];


# CARD 

$sql = 'SELECT * FROM mi_card WHERE card_id = "'.$card_id.'"';

$oRes = $oDB->Query($sql)or die(mysql_error());

$asData = array();

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$asData = $axRow;
}

$oTmp->assign('card', $asData);


# PRIVILEGE

if ($type=='p') {

	$table = 'privilege';
	$head = 'priv_';
	$body = 'Privilege';
	$trans = 'mepe';
	$trans_body = 'Privlege';

} else if ($type=='c') {

	$table = 'coupon';
	$head = 'coup_';
	$body = 'Coupon';
	$trans = 'meco';
	$trans_body = 'Coupon';

	$sql_data = 'SELECT * FROM coupon WHERE coup_CouponID="'.$id.'"';

} else if ($type=='a') {

	$table = 'activity';
	$head = 'acti_';
	$body = 'Activity';
	$trans = 'meac';
	$trans_body = 'Activity';
}

$sql_privilege = 'SELECT '.$table.'.'.$head.'Image AS image,
						'.$table.'.'.$head.'ImagePath AS path_image,
						'.$table.'.'.$head.'Name AS name,
						'.$table.'.'.$head.'Status AS status,
						'.$table.'.'.$head.'Description AS description,
						mi_privilege_type.name AS privilege_type_name,
						mi_brand.name AS brand_name

						FROM '.$table.'

						LEFT JOIN mi_privilege_type
						ON '.$table.'.prty_PrivilegeTypeID = mi_privilege_type.privilege_type_id

						LEFT JOIN mi_brand
						ON mi_brand.brand_id = '.$table.'.bran_BrandID

						WHERE '.$table.'.'.$head.$body.'ID = "'.$id.'"';

$oRes_privilege = $oDB->Query($sql_privilege)or die(mysql_error());

$asData = array();

while ($privilege = $oRes_privilege->FetchRow(DBI_ASSOC)){

	if ($privilege['description']=='') { $privilege['description'] = '-'; }

	$asData = $privilege;
}

$oTmp->assign('data', $asData);




# MEMBER

$where_branch = '';

if($_SESSION['user_branch_id']){

	$where_branch .= ' AND member_'.$table.'_trans.brnc_BranchID = "'.$_SESSION['user_branch_id'].'"';
}

$sql_register = 'SELECT mb_member.member_id,
						mb_member.facebook_id,
						mb_member.facebook_name,
						mb_member.firstname,
						mb_member.lastname,
						mb_member.email,
						mb_member.mobile,
						mb_member.member_image,
						mb.member_card_code,

						(SELECT COUNT(member_'.$table.'_trans.'.$trans.'_Member'.$trans_body.'ID) 
							FROM member_'.$table.'_trans
							LEFT JOIN mb_member_register
							ON mb_member_register.member_register_id=member_'.$table.'_trans.mere_MemberRegisterID
							WHERE member_'.$table.'_trans.'.$head.$body.'ID="'.$id.'" 
							AND member_'.$table.'_trans.'.$trans.'_Deleted=""
							AND member_'.$table.'_trans.memb_MemberID = mb.member_id
							AND ((mb_member_register.date_start<="'.date('Y-m-d').'" AND mb_member_register.date_expire>"'.date('Y-m-d').'") OR mb_member_register.period_type=4 OR mb_member_register.date_expire=mb_member_register.date_create)
							AND mb_member_register.flag_del="") AS total,

						(SELECT MAX(member_'.$table.'_trans.'.$trans.'_CreatedDate) 
							FROM member_'.$table.'_trans
							LEFT JOIN mb_member_register
							ON mb_member_register.member_register_id=member_'.$table.'_trans.mere_MemberRegisterID
							WHERE member_'.$table.'_trans.'.$head.$body.'ID="'.$id.'" 
							AND member_'.$table.'_trans.'.$trans.'_Deleted=""
							AND member_'.$table.'_trans.memb_MemberID = mb.member_id
							AND ((mb_member_register.date_start<="'.date('Y-m-d').'" AND mb_member_register.date_expire>"'.date('Y-m-d').'") OR mb_member_register.period_type=4 OR mb_member_register.date_expire=mb_member_register.date_create)
							AND mb_member_register.flag_del="") AS date_use

						FROM mb_member_register mb

						LEFT JOIN mb_member
						ON mb.member_id = mb_member.member_id 

						WHERE mb.card_id="'.$card_id.'"
						AND mb.flag_del=""
						AND ((mb.date_start<="'.date('Y-m-d').'" AND mb.date_expire>"'.date('Y-m-d').'") OR mb.period_type=4 OR mb.date_expire=mb.date_create)

						GROUP BY mb.member_id
						ORDER BY total DESC';

// echo $sql_register;
// exit();

$oRes = $oDB->Query($sql_register)or die(mysql_error());

$table_privilege = "<table id='example' class='table table-striped table-bordered'>
					<thead>
					<tr class='th_table'>
						<td>Member</td>
						<td>Profile</td>
						<td>Total</td>
						<td>Use</td>
						<td>Balance</td>
						<td>Last Date Use</td>
					</tr>
					</thead>
					<tbody>";

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	# MEMBER

	if($axRow['member_image']!='' && $axRow['member_image']!='user.png') {

		$axRow['member_image'] = '<img class="img-circle image_border" src="'.$path_upload_member.$axRow['member_image'].'"width="50" height="50"/>';

	} else if ($axRow['facebook_id']!='') {
				
		$axRow['member_image'] = '<img class="img-circle image_border" src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=square" width="50" height="50" />';

	} else {

		$axRow['member_image'] = '<img src="../../images/user.png" width="50" height="50" class="img-circle image_border" />';
	}

	$member_name = '';

	if ($axRow['firstname'].' '.$axRow['lastname']) {

		if ($axRow['email']) {

			if ($axRow['mobile']) {

				if ($card_code) {

					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'].'<br>Member Card : '.$card_code;

				} else {

					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'];
				}

			} else {

				if ($card_code) {

					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>Member Card : '.$card_code;

				} else {

					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'];
				}
			}

		} else {

			if ($axRow['mobile']) {

				if ($card_code) {

					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile'].'<br>Member Card : '.$card_code;

				} else {

					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile'];
				}
				
			} else { 

				if ($card_code) {

					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>Member Card : '.$card_code;

				} else {

					$member_name = $axRow['firstname'].' '.$axRow['lastname'];
				}
			}
		}

	} else {

		if ($axRow['email']) {

			if ($axRow['mobile']) { $member_name = $axRow['email'].'<br>'.$axRow['mobile']; } 
				
			else { $member_name = $axRow['email']; }

		} else {

			if ($axRow['mobile']) { $member_name = $axRow['mobile']; } 
				
			else { $member_name = ''; }
		}
	}


	# TYPE

	if ($type == 'c') {

		$oRes_coup = $oDB->Query($sql_data)or die(mysql_error());
		$coupon = $oRes_coup->FetchRow(DBI_ASSOC);

		if (($coupon['coup_Repetition']=='T' || $coupon['coup_RepetitionMember']=='T') && ($coupon['coup_QtyPer']=='Not Specific' || $coupon['coup_QtyPerMember']=='Not Specific')) {

			$last_total = 0;
			$time_all = $coupon['coup_Qty'];
			$time_member = $coupon['coup_QtyMember'];

			if ($time_all!=0 && $time_member!=0) {

				if ($time_member>$time_all) { $last_total = $time_all; }
				else { $last_total = $time_member; }

			} else {

				if ($time_all==0 && $time_member==0) { $last_total = "-"; }
				else if ($time_member==0) { $last_total = $time_all; }
				else { $last_total = $time_member; }
			}

			$total = 0;

			if ($last_total != 0) {
				
				$sql_count = 'SELECT COUNT(member_register_id)
								FROM mb_member_register
								WHERE card_id="'.$card_id.'"
								AND member_id="'.$axRow['member_id'].'"
								AND flag_del=""
								AND date_start<="'.date("Y-m-d").'"
								AND date_expire>"'.date("Y-m-d").'"';
				$count_card = $oDB->QueryOne($sql_count);

				$total = $count_card*$last_total;
			}

			$balance = 0;

			if ($axRow['total'] != 0 && $total != 0) {
				
				$balance = $total - $axRow['total'];
			}

		} else {

			$total = '-';
			$balance = '-';
		}

	} else {

		$total = '-';
		$balance = '-';
	}

	if ($axRow['date_use']=='') { $axRow['date_use'] = '-'; }
	else { $axRow['date_use'] = DateTime($axRow['date_use']); }

	$table_privilege .= "<tr>
							<td style='text-align:center'>".$axRow['member_image']."</td>
							<td>".$member_name."</td>
							<td style='text-align:center'>".$total."</td>
							<td style='text-align:center'>".$axRow['total']."</td>
							<td style='text-align:center'>".$balance."</td>
							<td style='text-align:center'>".$axRow['date_use']."</td>";

	$table_privilege .= "</tr>";
}

$table_privilege .= "</tbody>";
		
$table_privilege .= "</table>";




$oTmp->assign('table_privilege', $table_privilege);

$oTmp->assign('is_menu', 'is_analytics');

$oTmp->assign('content_file', 'analytics/balance_privilege.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
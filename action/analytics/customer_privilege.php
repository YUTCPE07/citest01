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
$regis_id = $_REQUEST['regis_id'];
$type = $_REQUEST['type'];

$path_upload_member = $_SESSION['path_upload_member'];


# MEMBER REGISTER 

$sql = 'SELECT mb_member.member_id,
				mb_member.firstname,
				mb_member.lastname,
				mb_member.email,
				mb_member.mobile,
				mb_member.date_birth,
				mb_member.facebook_id,
				mi_card.name,
				mi_card.path_image,
				mi_card.image,
				mb_member_register.date_start,
				mb_member_register.date_expire,
				mb_member_register.date_create,
				mb_member_register.period_type,
				mb_member_register.period_type_other
		FROM mb_member_register
		LEFT JOIN mb_member
		ON mb_member.member_id = mb_member_register.member_id
		LEFT JOIN mi_card
		ON mi_card.card_id = mb_member_register.card_id
		WHERE member_register_id = "'.$regis_id.'"';

$oRes = $oDB->Query($sql)or die(mysql_error());

$asData = array();

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	if ($axRow['date_birth']=="0000-00-00") { $axRow['date_birth'] = "-"; } 
	else { $axRow['date_birth'] = DateOnly($axRow['date_birth']); }

	if ($axRow['nickname']=="") { $axRow['nickname']="-"; }
	if ($axRow['firstname']=="") { $axRow['firstname']="-"; }
	if ($axRow['lastname']=="") { $axRow['lastname']="-"; }

	# START DATE

	if ($axRow['period_type']=='4') { $axRow['end_date'] = '-'; }
	else { $axRow['end_date'] = DateOnly($axRow['date_expire']); }

	if ($axRow['date_start']=='0000-00-00') { $axRow['start_date'] = DateOnly($axRow['date_create']); }
	else { $axRow['start_date'] = DateOnly($axRow['date_start']); }

	$asData = $axRow;
}

$oTmp->assign('regis', $asData);


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

$sql_register = 'SELECT COUNT(member_coupon_trans.meco_MemberCouponID) AS count_use,
					member_transaction_h.meth_MemberTransactionID AS head_code,
					member_coupon_trans.meco_CreatedDate AS use_date,
					mi_branch.name AS branch_name
				FROM member_coupon_trans
				LEFT JOIN mi_branch
				ON mi_branch.branch_id = member_coupon_trans.brnc_BranchID
				LEFT JOIN member_transaction_h
				ON member_transaction_h.meth_MemberTransactionHID = member_coupon_trans.meth_MemberTransactionHID
				WHERE mere_MemberRegisterID = "'.$regis_id.'"
				AND coup_CouponID = "'.$id.'"
				AND meco_Deleted = ""
				GROUP BY member_coupon_trans.meth_MemberTransactionHID';

$oRes = $oDB->Query($sql_register)or die(mysql_error());

$table_privilege = "<table id='example' class='table table-striped table-bordered' style='background-color:white'>
					<thead>
					<tr class='th_table'>
						<td>No.</td>
						<td>Use Date</td>
						<td>Code Use</td>
						<td>No. of Use</td>
						<td>Branch</td>
					</tr>
					</thead>
					<tbody>";

$y = 0;

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$y++;

	$table_privilege .= '<tr>
						<td>'.$y.'.</td>
						<td style="text-align:center">'.DateTime($axRow['use_date']).'</td>
						<td style="text-align:center">'.$axRow['head_code'].'</td>
						<td style="text-align:center">'.$axRow['count_use'].'</td>
						<td style="text-align:center">'.$axRow['branch_name'].'</td>
					</tr>';
}

$table_privilege .= "</tbody>";
		
$table_privilege .= "</table>";




$oTmp->assign('table_privilege', $table_privilege);

$oTmp->assign('is_menu', 'is_analytics');

$oTmp->assign('content_file', 'analytics/customer_privilege.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
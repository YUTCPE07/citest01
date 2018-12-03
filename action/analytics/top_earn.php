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

$path_upload_member = $_SESSION['path_upload_member'];


if($_SESSION['user_branch_id']){

	$where_branch .= ' AND mi_branch.branch_id = "'.$_SESSION['user_branch_id'].'"';
}


$sql = 'SELECT hilight_coupon.*,
			mi_brand.name AS brand_name
			FROM hilight_coupon
			LEFT JOIN mi_brand
			ON mi_brand.brand_id = hilight_coupon.bran_BrandID
			WHERE hilight_coupon.coup_CouponID = "'.$id.'"';

$oRes = $oDB->Query($sql)or die(mysql_error());

$asData = array();

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	if ($axRow['coup_Description']=="") { $axRow['coup_Description']="-"; }
	else { $axRow['coup_Description'] = nl2br($axRow['coup_Description']); }

	$asData = $axRow;
}



$sql_branch = 'SELECT brnc_BranchID FROM hilight_coupon WHERE coup_CouponID ='.$id;

$earn_branch = $oDB->QueryOne($sql_branch);


$sql_branch = 'SELECT name as txt, branch_id as id 
				FROM mi_branch
				WHERE branch_id IN ('.$earn_branch.')';


$sql_coupon = 'SELECT coup_Name as txt, coup_CouponID as id 
					FROM hilight_coupon 
					WHERE coup_CouponID = "'.$id.'" ';


$oRes_branch = $oDB->Query($sql_branch)or die(mysql_error());

$oRes_coupon = $oDB->Query($sql_coupon)or die(mysql_error());

$total_use = get_total_earn_use($id,"","");



$table_earn = '<center>
				<br>
	 			<span style="font-size:16px"><b>Total Use &nbsp; : &nbsp; '.number_format($total_use).' &nbsp; Times</span></b>
	 			<br>
	 			</center>
	 			<table id="example" class="table table-bordered" style="background-color:white;">
					<thead><tr class="th_table">
						<th style="text-align:center;width:15%"">Customer</th>
						<th style="text-align:center;">Profile</th>';

$a=0;

while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)) {

	$table_earn .= "<th style='text-align:center'>".$axRow_branch['txt']."</th>";

	$branch_id[$a]  = $axRow_branch['id'];

	$a++;
}

if($_SESSION['user_type_id_ses'] != 3) {

	$table_earn .= "<th style='text-align:center;width:10%'>รวม</th>";
}

$table_earn .= "</tr></thead><tbody>";



# MEMBER REGISTER

$sql_register = 'SELECT DISTINCT
						mb_member.member_id,
						mb_member.facebook_id,
						mb_member.facebook_name,
						mb_member.firstname,
						mb_member.lastname,
						mb_member.email,
						mb_member.mobile,
						mb_member.member_image,
						COUNT(hilight_coupon_trans.hico_HilightCouponID) AS total

						FROM mb_member

						LEFT JOIN hilight_coupon_trans
						ON hilight_coupon_trans.memb_MemberID = mb_member.member_id

						WHERE hilight_coupon_trans.coup_CouponID = "'.$id.'"
						GROUP BY hilight_coupon_trans.memb_MemberID
						ORDER BY total DESC';

$oRes_register = $oDB->Query($sql_register)or die(mysql_error());

$check_regis = $oDB->QueryOne($sql_register);


if ($check_regis) {

	while ($axRow = $oRes_register->FetchRow(DBI_ASSOC)) {

		$total_member = 0;

		# MEMBER

		if($axRow['member_image']!='' && $axRow['member_image']!='user.png') {

			$axRow['member_image'] = '<img class="img-circle image_border" src="'.$path_upload_member.$axRow['member_image'].'"width="50" height="50"/>';

		} else if ($axRow['facebook_id']!='') {

			$axRow['member_image'] = '<img class="img-circle image_border" src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=square" width="50" height="50" />';

		} else {

			$axRow['member_image'] = '<img src="../../images/user.png" width="50" height="50" class="img-circle image_border" />';
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



		$table_earn .= "<tr>
							<td style='text-align:center'>".$axRow['member_image']."</td>
							<td>".$member_name."</td>";


		for ($i=0; $i < $a; $i++) {

			$member_coup = get_total_earn_use($id,$branch_id[$i],$axRow['member_id']);

			$total_member += $member_coup;

			$table_earn .= "<td style='text-align:center'>".number_format($member_coup)."</td>";
		}

		if($_SESSION['user_type_id_ses'] != 3) {

			$table_earn .= "<td style='text-align:center;background-color:#F2F2F2'><b>".number_format($total_member)."</b></td>";
		}

		$table_earn .= "</tr>";
	}
}

$table_earn .= "</tbody>";

$table_earn .="</table>";




$oTmp->assign('table_earn', $table_earn);

$oTmp->assign('data', $asData);

$oTmp->assign('is_menu', 'is_analytics');

$oTmp->assign('content_file', 'analytics/top_earn.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());

}

//========================================//

?>
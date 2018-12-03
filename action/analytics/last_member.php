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


$sql ='SELECT * FROM mb_member WHERE member_id = "'.$id.'"';

$oRes = $oDB->Query($sql);

$asData = array();

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$i++;

	if ($axRow['date_birth']=="0000-00-00") { $axRow['date_birth'] = "-"; } 
	else { $axRow['date_birth'] = DateOnly($axRow['date_birth']); }

	if ($axRow['nickname']=="") { $axRow['nickname']="-"; }
	if ($axRow['firstname']=="") { $axRow['firstname']="-"; }
	if ($axRow['lastname']=="") { $axRow['lastname']="-"; }
	if ($axRow['email']=="") { $axRow['email']="-"; }
	if ($axRow['mobile']=="") { $axRow['mobile']="-"; }

	$asData = $axRow;
}


if($_SESSION['user_brand_id']){

	$where_brand = 'AND mi_brand.brand_id = "'.$_SESSION['user_brand_id'].'"';
}


if($_SESSION['user_branch_id']){

	$where_branch = ' AND mi_branch.branch_id = "'.$_SESSION['user_branch_id'].'"';
}


$sql_member = "SELECT * FROM (

				SELECT member_privilege_trans.mepe_CreatedDate AS date_use,
					member_privilege_trans.mepe_MemberPrivlegeID AS code_use,
					member_privilege_trans.priv_PrivilegeID AS privilege_id,
					privilege.priv_Name AS privilege_name,
					privilege.priv_Image AS privilege_image,
					privilege.priv_ImageNew AS privilege_new,
					privilege.priv_ImagePath AS path_image,
					'Privilege' AS type,
					mi_card.name AS card_name,
					mi_card.image AS card_image,
					mi_card.image_newupload AS card_new,
					mi_card.path_image AS path_card,
					mi_branch.name AS branch_name,
					mi_brand.name AS brand_name,
					mi_brand.path_logo AS path_logo,
					mi_brand.logo_image AS brand_logo

					FROM member_privilege_trans

					LEFT JOIN mi_card
					ON member_privilege_trans.card_CardID = mi_card.card_id

					LEFT JOIN privilege
					ON member_privilege_trans.priv_PrivilegeID = privilege.priv_PrivilegeID

					LEFT JOIN mi_branch
					ON member_privilege_trans.brnc_BranchID = mi_branch.branch_id

					LEFT JOIN mi_brand
					ON mi_brand.brand_id = mi_branch.brand_id

					WHERE member_privilege_trans.memb_MemberID = ".$id."
					".$where_brand."
					".$where_branch."

				UNION

				SELECT member_coupon_trans.meco_CreatedDate AS date_use,
					member_coupon_trans.meco_MemberCouponID AS code_use,
					member_coupon_trans.coup_CouponID AS privilege_id,
					coupon.coup_Name AS privilege_name,
					coupon.coup_Image AS privilege_image,
					coupon.coup_ImageNew AS privilege_new,
					coupon.coup_ImagePath AS path_image,
					'Coupon' AS type,
					mi_card.name AS card_name,
					mi_card.image AS card_image,
					mi_card.image_newupload AS card_new,
					mi_card.path_image AS path_card,
					mi_branch.name AS branch_name,
					mi_brand.name AS brand_name,
					mi_brand.path_logo AS path_logo,
					mi_brand.logo_image AS brand_logo

					FROM member_coupon_trans

					LEFT JOIN mi_card
					ON member_coupon_trans.card_CardID = mi_card.card_id

					LEFT JOIN coupon
					ON member_coupon_trans.coup_CouponID = coupon.coup_CouponID

					LEFT JOIN mi_branch
					ON member_coupon_trans.brnc_BranchID = mi_branch.branch_id

					LEFT JOIN mi_brand
					ON mi_brand.brand_id = mi_branch.brand_id

					WHERE member_coupon_trans.memb_MemberID = ".$id."
					AND coupon.coup_Birthday = ''
					".$where_brand."
					".$where_branch."

				UNION

				SELECT member_coupon_trans.meco_CreatedDate AS date_use,
					member_coupon_trans.meco_MemberCouponID AS code_use,
					member_coupon_trans.coup_CouponID AS privilege_id,
					coupon.coup_Name AS privilege_name,
					coupon.coup_Image AS privilege_image,
					coupon.coup_ImageNew AS privilege_new,
					coupon.coup_ImagePath AS path_image,
					'Birthday Coupon' AS type,
					mi_card.name AS card_name,
					mi_card.image AS card_image,
					mi_card.image_newupload AS card_new,
					mi_card.path_image AS path_card,
					mi_branch.name AS branch_name,
					mi_brand.name AS brand_name,
					mi_brand.path_logo AS path_logo,
					mi_brand.logo_image AS brand_logo

					FROM member_coupon_trans

					LEFT JOIN mi_card
					ON member_coupon_trans.card_CardID = mi_card.card_id

					LEFT JOIN coupon
					ON member_coupon_trans.coup_CouponID = coupon.coup_CouponID

					LEFT JOIN mi_branch
					ON member_coupon_trans.brnc_BranchID = mi_branch.branch_id

					LEFT JOIN mi_brand
					ON mi_brand.brand_id = mi_branch.brand_id

					WHERE member_coupon_trans.memb_MemberID = ".$id."
					AND coupon.coup_Birthday = 'T'
					".$where_brand."
					".$where_branch."

				UNION

				SELECT member_activity_trans.meac_CreatedDate AS date_use,
					member_activity_trans.meac_MemberActivityID AS code_use,
					member_activity_trans.acti_ActivityID AS privilege_id,
					activity.acti_Name AS privilege_name,
					activity.acti_Image AS privilege_image,
					activity.acti_ImageNew AS privilege_new,
					activity.acti_ImagePath AS path_image,
					'Activity' AS type,
					mi_card.name AS card_name,
					mi_card.image AS card_image,
					mi_card.image_newupload AS card_new,
					mi_card.path_image AS path_card,
					mi_branch.name AS branch_name,
					mi_brand.name AS brand_name,
					mi_brand.path_logo AS path_logo,
					mi_brand.logo_image AS brand_logo

					FROM member_activity_trans

					LEFT JOIN mi_card
					ON member_activity_trans.card_CardID = mi_card.card_id

					LEFT JOIN activity
					ON member_activity_trans.acti_ActivityID = activity.acti_ActivityID

					LEFT JOIN mi_branch
					ON member_activity_trans.brnc_BranchID = mi_branch.branch_id

					LEFT JOIN mi_brand
					ON mi_brand.brand_id = mi_branch.brand_id

					WHERE member_activity_trans.memb_MemberID = ".$id."
					".$where_brand."
					".$where_branch."

				UNION

				SELECT hilight_coupon_trans.hico_CreatedDate AS date_use,
					hilight_coupon_trans.hico_HilightCouponID AS code_use,
					hilight_coupon_trans.coup_CouponID AS privilege_id,
					hilight_coupon.coup_Name AS privilege_name,
					hilight_coupon.coup_Image AS privilege_image,
					hilight_coupon.coup_ImageNew AS privilege_new,
					hilight_coupon.coup_ImagePath AS path_image,
					'Earn Attention' AS type,
					'-' AS card_name,
					'-' AS card_image,
					'-' AS card_new,
					'-' AS path_card,
					mi_branch.name AS branch_name,
					mi_brand.name AS brand_name,
					mi_brand.path_logo AS path_logo,
					mi_brand.logo_image AS brand_logo

					FROM hilight_coupon_trans

					LEFT JOIN hilight_coupon
					ON hilight_coupon_trans.coup_CouponID = hilight_coupon.coup_CouponID

					LEFT JOIN mi_branch
					ON hilight_coupon_trans.brnc_BranchID = mi_branch.branch_id

					LEFT JOIN mi_brand
					ON mi_brand.brand_id = mi_branch.brand_id

					WHERE hilight_coupon_trans.memb_MemberID = ".$id."
					".$where_brand."
					".$where_branch."

				) member_trans

				ORDER BY date_use DESC";

$oRes_member = $oDB->Query($sql_member)or die(mysql_error());

$check_member = $oDB->QueryOne($sql_member);

if ($check_member) {

	$table_member = "<table id='example' class='table table-bordered' style='background-color:white;'>
						<thead><tr class='th_table'>
							<th>Use Date</th>
							<th>Code Use</th>
							<th>Brand</th>
							<th>Card</th>
							<th>Privilege</th>
							<th>Type</th>
							<th>Branch</th>
							</tr>
						</thead>
						<tbody>";

	while ($axRow = $oRes_member->FetchRow(DBI_ASSOC)) {

		# LOGO IMAGE

		if($axRow['brand_logo']!=''){

			$axRow['brand_logo'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" width="50" height="50"/>';

		} else {

			$axRow['brand_logo'] = '<img src="../../images/400x400.png" width="50" height="50"/>';
		}


		# CARD IMAGE

		if ($axRow['type'] == "Earn Attention") {

			$axRow['card_image'] = '';

		} else {

			if($axRow['card_new']!=''){

				$axRow['card_image'] = '<img src="../../upload/'.$axRow['path_card'].$axRow['card_new'].'" height="50px" class="img-rounded image_border"/>';

			} else if ($axRow['card_image']!='') {

				$axRow['card_image'] = '<img src="../../upload/'.$axRow['path_card'].$axRow['card_image'].'" height="50px" class="img-rounded image_border"/>';

			} else {

				$axRow['card_image'] = '<img src="../../images/card_privilege.jpg" height="50px" class="img-rounded image_border"/>';
			}
		}


		# PRIVILEGE IMAGE

		if($axRow['privilege_new']!=''){

			$privilege_img = '<img src="../../upload/'.$axRow['path_image'].$axRow['priv_ImageNew'].'" height="50px" class="image_border">';

		} else if ($axRow['privilege_image']!='') {

			$privilege_img = '<img src="../../upload/'.$axRow['path_image'].$axRow['privilege_image'].'" height="50px" class="image_border">';

		} else {

			$privilege_img = '<img src="../../images/card_privilege.jpg" height="50px" class="image_border">';
		}


		$table_member .= "<tr>
							<td style='text-align:center'>".DateTime($axRow['date_use'])."</td>
							<td style='text-align:center'>".$axRow['code_use']."</td>
							<td style='text-align:center'><a href='../brand/brand.php'>".$axRow['brand_logo']."</a><br>
								<span style='font-size:11px'>".$axRow['brand_name']."</span></td>
							<td style='text-align:center'><a href='../card/card.php'>".$axRow['card_image']."</a><br>
								<span style='font-size:11px'>".$axRow['card_name']."</span></td>
							<td style='text-align:center'>";

		if ($axRow['type'] == 'Privilege') {

			$table_member .= "<a href='../privilege/privilege.php'>".$privilege_img."</a><br>";

		} elseif ($axRow['type'] == 'Coupon') {

			$table_member .= "<a href='../coupon/coupon.php'>".$privilege_img."</a><br>";

		} elseif ($axRow['type'] == 'Activity') {

			$table_member .= "<a href='../activity/activity.php'>".$privilege_img."</a><br>";
			
		} elseif ($axRow['type'] == 'Earn Attention') {

			$sql_type = "SELECT coup_Type FROM hilight_coupon WHERE coup_CouponID='".$axRow['privilege_id']."'";
			$coup_Type = $oDB->QueryOne($sql_type);

			if ($coup_Type=='Use') {

				$table_member .= "<a href='../earn_attention/use.php'>".$privilege_img."</a><br>";

			} else {

				$table_member .= "<a href='../earn_attention/buy.php'>".$privilege_img."</a><br>";
			}

		} else {

			$table_member .= "<a href='../coupon/birthday.php'>".$privilege_img."</a><br>";
		}

		$table_member .= "		<span style='font-size:11px'>".$axRow['privilege_name']."</span></td>
							<td style='text-align:center'>".$axRow['type']."</td>
							<td style='text-align:center'>".$axRow['branch_name']."</td>
						</tr>";
	}

	$table_member .= "</tbody></table>";
}



$as_name_title_type = list_type_master_value($oDB,'name_title_type',$axRow['name_title_type']);
if ($as_name_title_type=="") { $as_name_title_type = "-"; }

$oTmp->assign('name_title_type', $as_name_title_type);

$oTmp->assign('data', $asData);

$oTmp->assign('table_member', $table_member);

$oTmp->assign('is_menu', 'is_analytics');

$oTmp->assign('content_file', 'analytics/last_member.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
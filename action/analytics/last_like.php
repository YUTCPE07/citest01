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

$type = $_REQUEST['type'];

$path_upload_member = $_SESSION['path_upload_member'];

if($_SESSION['user_branch_id']){

	$where_branch = ' AND mi_branch.branch_id = "'.$_SESSION['user_branch_id'].'"';
}



if ($type=="Privilege") {

	$sql = 'SELECT privilege.priv_Name AS name,
				privilege.priv_Image AS image,
				privilege.priv_ImageNew AS image_new,
				privilege.priv_ImagePath AS path_image,
				privilege.priv_Status AS status,
				privilege.priv_Description AS description,
				privilege.priv_Motivation AS motivation,
				privilege.priv_MotivationID AS motivation_id,
				mi_brand.name AS brand_name,
				"Privilege" AS type

				FROM privilege

				LEFT JOIN mi_brand
				ON mi_brand.brand_id = privilege.bran_BrandID

				WHERE privilege.priv_PrivilegeID = "'.$id.'"';

} else if ($type=="Coupon") {

	$sql = 'SELECT coupon.coup_Name AS name,
				coupon.coup_Image AS image,
				coupon.coup_ImageNew AS image_new,
				coupon.coup_ImagePath AS path_image,
				coupon.coup_Status AS status,
				coupon.coup_Description AS description,
				coupon.coup_Motivation AS motivation,
				coupon.coup_MotivationID AS motivation_id,
				mi_brand.name AS brand_name,
				"Coupon" AS type

				FROM coupon

				LEFT JOIN mi_brand
				ON mi_brand.brand_id = coupon.bran_BrandID

				WHERE coupon.coup_CouponID = "'.$id.'"';

} else if ($type=="Birthday Coupon") {

	$sql = 'SELECT coupon.coup_Name AS name,
				coupon.coup_Image AS image,
				coupon.coup_ImageNew AS image_new,
				coupon.coup_ImagePath AS path_image,
				coupon.coup_Status AS status,
				coupon.coup_Description AS description,
				coupon.coup_Motivation AS motivation,
				coupon.coup_MotivationID AS motivation_id,
				mi_brand.name AS brand_name,
				"Birthday Coupon" AS type

				FROM coupon

				LEFT JOIN mi_brand
				ON mi_brand.brand_id = coupon.bran_BrandID

				WHERE coupon.coup_CouponID = "'.$id.'"';

} else if ($type=="Activity") {

	$sql = 'SELECT activity.acti_Name AS name,
				activity.acti_Image AS image,
				activity.acti_ImageNew AS image_new,
				activity.acti_ImagePath AS path_image,
				activity.acti_Status AS status,
				activity.acti_Description AS description,
				activity.acti_Motivation AS motivation,
				activity.acti_MotivationID AS motivation_id,
				mi_brand.name AS brand_name,
				"Activity" AS type

				FROM activity

				LEFT JOIN mi_brand
				ON mi_brand.brand_id = activity.bran_BrandID

				WHERE activity.acti_ActivityID = "'.$id.'"';

} else {

	$sql = 'SELECT hilight_coupon.coup_Name AS name,
				hilight_coupon.coup_Image AS image,
				hilight_coupon.coup_ImageNew AS image_new,
				hilight_coupon.coup_ImagePath AS path_image,
				hilight_coupon.coup_Status AS status,
				hilight_coupon.coup_Description AS description,
				hilight_coupon.coup_Motivation AS motivation,
				hilight_coupon.coup_MotivationID AS motivation_id,
				mi_brand.name AS brand_name,
				"Earn Attention" AS type

				FROM hilight_coupon

				LEFT JOIN mi_brand
				ON mi_brand.brand_id = hilight_coupon.bran_BrandID

				WHERE hilight_coupon.coup_CouponID = "'.$id.'"';
}

$oRes = $oDB->Query($sql)or die(mysql_error());

$asData = array();

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	if ($axRow['description']=="") { $axRow['description']="-"; }
	else { $axRow['description'] = nl2br($axRow['description']); }

	if ($axRow['motivation'] == 'Point') { 

		$icon_sql = "SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID = 3";
		$icon = $oDB->QueryOne($icon_sql);
		$icon ='<img src="../../upload/collection_upload/'.$icon.'" width="12px" height="12px" style="margin-bottom:3px">';

		$plan_sql = "SELECT mopp_Name, mopp_PointQty, mopp_UseAmount FROM motivation_plan_point WHERE mopp_MotivationPointID='".$axRow['motivation_id']."'";
		$get_point = $oDB->Query($plan_sql);
		$point = $get_point->FetchRow(DBI_ASSOC);

		$axRow['motivation'] = $point['mopp_Name'].' ('.number_format($point['mopp_UseAmount'],2).' ฿ / '.$point['mopp_PointQty'].' '.$icon.')';

	} else if ($axRow['motivation'] == 'Stamp') {

		$plan_sql = "SELECT mops_Name, mops_StampQty, mops_CollectionTypeID FROM motivation_plan_stamp WHERE mops_MotivationStampID='".$axRow['motivation_id']."'";
		$get_stamp = $oDB->Query($plan_sql);
		$stamp = $get_stamp->FetchRow(DBI_ASSOC);

		$icon_sql = "SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID = ".$stamp['mops_CollectionTypeID'];
		$icon = $oDB->QueryOne($icon_sql);
		$icon ='<img src="../../upload/collection_upload/'.$icon.'" width="12px" height="12px" style="margin-bottom:3px">';

		$axRow['motivation'] = $stamp['mops_Name'].' (1 Times / '.$stamp['mops_StampQty'].' '.$icon.')';

	} else { $axRow['motivation'] = '-'; } 

	$asData = $axRow;
}


if ($type != "Earn Attention") {

	if ($type=="Privilege") {

		$table_trans = 'member_privilege_trans';
		$create_date = 'mepe_CreatedDate';
		$code = 'mepe_MemberPrivlegeID';
		$privilege_id = 'priv_PrivilegeID';
		$privilege_like = 'mepe_Like';

	} else if ($type=="Coupon" || $type=="Birthday Coupon") {

		$table_trans = 'member_coupon_trans';
		$create_date = 'meco_CreatedDate';
		$code = 'meco_MemberCouponID';
		$privilege_id = 'coup_CouponID';
		$privilege_like = 'meco_Like';

	} else {

		$table_trans = 'member_activity_trans';
		$create_date = 'meac_CreatedDate';
		$code = 'meac_MemberActivityID';
		$privilege_id = 'acti_ActivityID';
		$privilege_like = 'meac_Like';
	}

		$sql_like = "SELECT DISTINCT
						member_transaction_h.meth_CreatedDate AS date_use,
						member_transaction_h.meth_MemberTransactionID AS code_use,
						mi_branch.name AS branch_name,
						mi_card.name AS card_name,
						mi_card.image AS card_image,
						mi_card.path_image AS path_image,
						mb_member.firstname, 
						mb_member.lastname, 
						mb_member.facebook_id, 
						mb_member.facebook_name, 
						mb_member.member_id, 
						mb_member.member_image,
						mb_member.mobile,
						mb_member.email

						FROM ".$table_trans."

						LEFT JOIN mb_member
						ON ".$table_trans.".memb_MemberID = mb_member.member_id

						LEFT JOIN mi_card
						ON ".$table_trans.".card_CardID = mi_card.card_id

						LEFT JOIN mi_branch
						ON ".$table_trans.".brnc_BranchID = mi_branch.branch_id

						LEFT JOIN mi_brand
						ON mi_brand.brand_id = mi_branch.brand_id

						LEFT JOIN member_transaction_h
						ON member_transaction_h.meth_MemberTransactionHID = ".$table_trans.".meth_MemberTransactionHID

						WHERE ".$table_trans.".".$privilege_id." = ".$id."
						AND  member_transaction_h.meth_Like='T'
						".$where_branch."

						ORDER BY date_use DESC";

	$oRes_like = $oDB->Query($sql_like)or die(mysql_error());

	$check_like = $oDB->QueryOne($sql_like);

	if ($check_like) {

		$table_like = "<table id='example' class='table table-bordered' style='background-color:white;'>
							<thead><tr class='th_table'>
								<th style='text-align:center;'>Like Date</th>
								<th style='text-align:center;'>Code Use</th>
								<th style='text-align:center;'>Member</th>
								<th style='text-align:center;'>Profile</th>
								<th style='text-align:center;'>Card</th>
								<th style='text-align:center;'>Branch</th>
								</tr></thead><tbody>";

		while ($axRow = $oRes_like->FetchRow(DBI_ASSOC)) {

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



			# CARD IMAGE

			if($axRow['card_new']!=''){

				$axRow['card_image'] = '<img src="../../upload/'.$axRow['path_image'].$axRow['card_new'].'" height="50px" class="img-rounded image_border"/>';

			} else if ($axRow['card_image']!='') {

				$axRow['card_image'] = '<img src="../../upload/'.$axRow['path_image'].$axRow['card_image'].'" height="50px" class="img-rounded image_border"/>';

			} else {

				$axRow['card_image'] = '<img src="../../images/card_privilege.jpg" height="50px" class="img-rounded image_border"/>';
			}


			$table_like .= "<tr>
								<td style='text-align:center'>".DateTime($axRow['date_use'])."</td>
								<td style='text-align:center'>".$axRow['code_use']."</td>
								<td style='text-align:center'>".$axRow['member_image']."</td>
								<td>".$member_name."</td>
								<td style='text-align:center'><a href='../card/card.php'>".$axRow['card_image']."</a><br>
									<span style='font-size:11px'>".$axRow['card_name']."</span></td>
								<td style='text-align:center'>".$axRow['branch_name']."</td>
							</tr>";
		}

		$table_like .= "</tbody></table>";
	}

} else {

	$sql_like = "SELECT 
					hilight_coupon_trans.hico_CreatedDate AS date_use,
					hilight_coupon_trans.hico_HilightCouponID AS code_use,
					mi_branch.name AS branch_name,
					mb_member.firstname, 
					mb_member.lastname, 
					mb_member.facebook_id, 
					mb_member.facebook_name, 
					mb_member.member_id, 
					mb_member.member_image,
					mb_member.email

					FROM hilight_coupon_trans

					LEFT JOIN mb_member
					ON hilight_coupon_trans.memb_MemberID = mb_member.member_id

					LEFT JOIN mi_branch
					ON hilight_coupon_trans.brnc_BranchID = mi_branch.branch_id

					LEFT JOIN mi_brand
					ON mi_brand.brand_id = mi_branch.brand_id

					WHERE hilight_coupon_trans.coup_CouponID = ".$id."
					AND  hilight_coupon_trans.hico_Like='Like'
					".$where_branch."

					ORDER BY date_use DESC";

	$oRes_like = $oDB->Query($sql_like)or die(mysql_error());

	$check_like = $oDB->QueryOne($sql_like);

	if ($check_like) {

		$table_like = "<table id='example' class='table table-bordered' style='background-color:white;'>
							<thead><tr class='th_table'>
								<th style='text-align:center;'>Like Date</th>
								<th style='text-align:center;'>Code Use</th>
								<th style='text-align:center;'>Member</th>
								<th style='text-align:center;'>Profile</th>
								<th style='text-align:center;'>Branch</th>
							</tr></thead><tbody>";

		while ($axRow = $oRes_like->FetchRow(DBI_ASSOC)) {

			# MEMBER

			if($axRow['member_image']!='') {

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


			$table_like .= "<tr>
								<td style='text-align:center'>".DateTime($axRow['date_use'])."</td>
								<td style='text-align:center'>".$axRow['code_use']."</td>
								<td style='text-align:center'>".$axRow['member_image']."</td>
								<td>".$member_name."</td>
								<td style='text-align:center'>".$axRow['branch_name']."</td>
							</tr>";
		}

		$table_like .= "</tbody></table>";
	}
}



$oTmp->assign('table_like', $table_like);

$oTmp->assign('data', $asData);

$oTmp->assign('is_menu', 'is_analytics');

$oTmp->assign('content_file', 'analytics/last_like.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
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

if ($_SESSION['role_action']['privilege_customer']['delete'] != 1) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");
$Act = $_REQUEST['act'];
$id = $_REQUEST['id'];


if($Act=='save' && $id!='') {

	$reason_del = trim_txt($_REQUEST['reason_del']);

	# UPDATE DELETED

	$sql_del = 'SELECT
					priv_PrivilegeID AS privilege_id,
					"p" AS type,
					mepe_MemberPrivlegeID AS code_use
					FROM member_privilege_trans
					WHERE meth_MemberTransactionHID = "'.$id.'"

				UNION

				SELECT
					coup_CouponID AS privilege_id,
					"c" AS type,
					meco_MemberCouponID AS code_use
					FROM member_coupon_trans
					WHERE meth_MemberTransactionHID = "'.$id.'"

				UNION

				SELECT
					acti_ActivityID AS privilege_id,
					"a" AS type,
					meac_MemberActivityID AS code_use
					FROM member_activity_trans
					WHERE meth_MemberTransactionHID = "'.$id.'"';

	$rs_del = $oDB->Query($sql_del);
	while($del = $rs_del->FetchRow(DBI_ASSOC)) {

		if ($del['type'] == "p") {

			$table = "member_privilege_trans";
			$head = "mepe";
			$body = "mepe_MemberPrivlegeID";
			$privilege = "priv_PrivilegeID";

		} elseif ($del['type'] == "c") {

			$table = "member_coupon_trans";
			$head = "meco";
			$body = "meco_MemberCouponID";
			$privilege = "coup_CouponID";

		} else {

			$table = "member_activity_trans";
			$head = "meac";
			$body = "meac_MemberActivityID";
			$privilege = "acti_ActivityID";
		}

		$do_deleted = 'UPDATE '.$table.'
						SET '.$head.'_Deleted="T",
							'.$head.'_DeletedDate="'.$time_insert.'",
							'.$head.'_DeletedBy="'.$_SESSION['UID'].'",
							'.$head.'_Reason="'.$reason_del.'"
						WHERE meth_MemberTransactionHID="'.$id.'"
						AND '.$body.'="'.$del['code_use'].'"';

		$oDB->QueryOne($do_deleted);

		# POINT

		$sql_point = 'UPDATE member_motivation_point_trans
						SET memp_Deleted="T" 
						WHERE mepe_MemberPrivlegeID="'.$del['code_use'].'"';
		$oDB->QueryOne($sql_point);

		# STAMP

		$sql_stamp = 'UPDATE member_motivation_stamp_trans
						SET mems_Deleted="T" 
						WHERE mepe_MemberPrivlegeID="'.$del['code_use'].'"';
		$oDB->QueryOne($sql_stamp);
	}

	$do_deleted = 'UPDATE member_transaction_h
					SET meth_Deleted="T",
						meth_UpdatedDate="'.$time_insert.'",
						meth_UpdatedBy="'.$_SESSION['UID'].'",
						meth_Reason="'.$reason_del.'"
					WHERE meth_MemberTransactionHID="'.$id.'"';

	$oDB->QueryOne($do_deleted);
	
	echo '<script>window.location.href="customer.php";</script>';
}


# TRANSACTION

$sql_transaction = 'SELECT
						mi_card.name AS card_name,
						mi_card.image AS card_image,
						mi_card.image_newupload AS card_image_new,
						mi_card.path_image,
						mi_brand.name AS brand_name,
						mb_member.firstname,
						mb_member.lastname,
						mb_member.facebook_name,
						mb_member.email AS member_email,
						mb_member.mobile AS member_mobile,
						mb_member.member_image AS member_image,
						mb_member.member_id AS member_id,
						mb_member.facebook_id AS facebook_id,
						member_privilege_trans.mepe_Status AS status,
						member_privilege_trans.mepe_Deleted AS status_del,
						member_privilege_trans.mepe_MemberPrivlegeID AS code_use,
						member_privilege_trans.mepe_CreatedDate AS create_date,
						COUNT(member_privilege_trans.mepe_MemberPrivlegeID) AS count_use,
						privilege.priv_Name AS privilege_name,
						privilege.priv_Image AS privilege_image,
						privilege.priv_ImagePath AS privilege_path,
						privilege.priv_PrivilegeID AS privilege_id,
						"Privilege" AS type,
						member_transaction_h.meth_MemberTransactionID AS head_code

						FROM member_privilege_trans

						LEFT JOIN mb_member
						ON  member_privilege_trans.memb_MemberID = mb_member.member_id

						LEFT JOIN mi_card
						ON member_privilege_trans.card_CardID = mi_card.card_id

						LEFT JOIN mi_brand
						ON mi_card.brand_id = mi_brand.brand_id

						LEFT JOIN privilege
						ON member_privilege_trans.priv_PrivilegeID = privilege.priv_PrivilegeID

						LEFT JOIN member_transaction_h
						ON member_privilege_trans.meth_MemberTransactionHID = member_transaction_h.meth_MemberTransactionHID

						WHERE member_privilege_trans.meth_MemberTransactionHID = "'.$id.'"
						GROUP BY member_privilege_trans.priv_PrivilegeID

					UNION

					SELECT
						mi_card.name AS card_name,
						mi_card.image AS card_image,
						mi_card.image_newupload AS card_image_new,
						mi_card.path_image,
						mi_brand.name AS brand_name,
						mb_member.firstname,
						mb_member.lastname,
						mb_member.facebook_name,
						mb_member.email AS member_email,
						mb_member.mobile AS member_mobile,
						mb_member.member_image AS member_image,
						mb_member.member_id AS member_id,
						mb_member.facebook_id AS facebook_id,
						member_coupon_trans.meco_Status AS status,
						member_coupon_trans.meco_Deleted AS status_del,
						member_coupon_trans.meco_MemberCouponID AS code_use,
						member_coupon_trans.meco_CreatedDate AS create_date,
						COUNT(member_coupon_trans.meco_MemberCouponID) AS count_use,
						coupon.coup_Name AS privilege_name,
						coupon.coup_Image AS privilege_image,
						coupon.coup_ImagePath AS privilege_path,
						coupon.coup_CouponID AS privilege_id,
						"Coupon" AS type,
						member_transaction_h.meth_MemberTransactionID AS head_code

						FROM member_coupon_trans

						LEFT JOIN mb_member
						ON  member_coupon_trans.memb_MemberID = mb_member.member_id

						LEFT JOIN mi_card
						ON member_coupon_trans.card_CardID = mi_card.card_id

						LEFT JOIN mi_brand
						ON mi_card.brand_id = mi_brand.brand_id

						LEFT JOIN coupon
						ON member_coupon_trans.coup_CouponID = coupon.coup_CouponID

						LEFT JOIN member_transaction_h
						ON member_coupon_trans.meth_MemberTransactionHID = member_transaction_h.meth_MemberTransactionHID

						WHERE member_coupon_trans.meth_MemberTransactionHID = "'.$id.'"
						AND coupon.coup_Birthday = ""
						GROUP BY member_coupon_trans.coup_CouponID

					UNION

					SELECT
						mi_card.name AS card_name,
						mi_card.image AS card_image,
						mi_card.image_newupload AS card_image_new,
						mi_card.path_image,
						mi_brand.name AS brand_name,
						mb_member.firstname,
						mb_member.lastname,
						mb_member.facebook_name,
						mb_member.email AS member_email,
						mb_member.mobile AS member_mobile,
						mb_member.member_image AS member_image,
						mb_member.member_id AS member_id,
						mb_member.facebook_id AS facebook_id,
						member_coupon_trans.meco_Status AS status,
						member_coupon_trans.meco_Deleted AS status_del,
						member_coupon_trans.meco_MemberCouponID AS code_use,
						member_coupon_trans.meco_CreatedDate AS create_date,
						COUNT(member_coupon_trans.meco_MemberCouponID) AS count_use,
						coupon.coup_Name AS privilege_name,
						coupon.coup_Image AS privilege_image,
						coupon.coup_ImagePath AS privilege_path,
						coupon.coup_CouponID AS privilege_id,
						"Brithday Coupon" AS type,
						member_transaction_h.meth_MemberTransactionID AS head_code

						FROM member_coupon_trans

						LEFT JOIN mb_member
						ON  member_coupon_trans.memb_MemberID = mb_member.member_id

						LEFT JOIN mi_card
						ON member_coupon_trans.card_CardID = mi_card.card_id

						LEFT JOIN mi_brand
						ON mi_card.brand_id = mi_brand.brand_id

						LEFT JOIN coupon
						ON member_coupon_trans.coup_CouponID = coupon.coup_CouponID

						LEFT JOIN member_transaction_h
						ON member_coupon_trans.meth_MemberTransactionHID = member_transaction_h.meth_MemberTransactionHID

						WHERE member_coupon_trans.meth_MemberTransactionHID = "'.$id.'"
						AND coupon.coup_Birthday = "T"
						GROUP BY member_coupon_trans.coup_CouponID

					UNION

					SELECT
						mi_card.name AS card_name,
						mi_card.image AS card_image,
						mi_card.image_newupload AS card_image_new,
						mi_card.path_image,
						mi_brand.name AS brand_name,
						mb_member.firstname,
						mb_member.lastname,
						mb_member.facebook_name,
						mb_member.email AS member_email,
						mb_member.mobile AS member_mobile,
						mb_member.member_image AS member_image,
						mb_member.member_id AS member_id,
						mb_member.facebook_id AS facebook_id,
						member_activity_trans.meac_Status AS status,
						member_activity_trans.meac_Deleted AS status_del,
						member_activity_trans.meac_MemberActivityID AS code_use,
						member_activity_trans.meac_CreatedDate AS create_date,
						COUNT(member_activity_trans.meac_MemberActivityID) AS count_use,
						activity.acti_Name AS privilege_name,
						activity.acti_Image AS privilege_image,
						activity.acti_ImagePath AS privilege_path,
						activity.acti_ActivityID AS privilege_id,
						"Activity" AS type,
						member_transaction_h.meth_MemberTransactionID AS head_code

						FROM member_activity_trans

						LEFT JOIN mb_member
						ON  member_activity_trans.memb_MemberID = mb_member.member_id

						LEFT JOIN mi_card
						ON member_activity_trans.card_CardID = mi_card.card_id

						LEFT JOIN mi_brand
						ON mi_card.brand_id = mi_brand.brand_id

						LEFT JOIN activity
						ON member_activity_trans.acti_ActivityID = activity.acti_ActivityID

						LEFT JOIN member_transaction_h
						ON member_activity_trans.meth_MemberTransactionHID = member_transaction_h.meth_MemberTransactionHID

						WHERE member_activity_trans.meth_MemberTransactionHID = "'.$id.'"
						GROUP BY member_activity_trans.acti_ActivityID';

$rs_trans = $oDB->Query($sql_transaction);
$trans = $rs_trans->FetchRow(DBI_ASSOC);
$asData = array();
$asData = $trans;

$oTmp->assign('data',$asData);


# TRANSACTION DETIL

$detail = "";

$rs_detail = $oDB->Query($sql_transaction);
while($detail = $rs_detail->FetchRow(DBI_ASSOC)) {


	# PRIVILEGE IMAGE

	if($detail['privilege_image']!=''){

		$privilege_image = '<img src="../../upload/'.$detail['privilege_path'].$detail['privilege_image'].'" class="image_border" height="60"/>';

	} else {

		$privilege_image = '<img src="../../images/card_privilege.jpg" height="60"/>';
	}


	# DETAIL

	$privilege_data = '<table style="width:100%">
							<tr>
								<td style="text-align:right;width:60px">
									Name<br>
									Type<br>
									Use Date
								</td>
								<td style="text-align:center;width:20px">
									:<br>
									:<br>
									:
								</td>
								<td>
									'.$detail['privilege_name'].'<br>
									'.$detail['type'].'<br>
									'.DateTime($detail['create_date']).'<br>
								</td>
							</tr>
						</table>';


	$detail_data .= "<tr>
						<td style='text-align:center'>".$privilege_image."</td>
						<td>".$privilege_data."</td>
						<td style='text-align:center'>".$detail['count_use']."</td>
					</tr>";
}




# TRANSACTION DELETE

$sql_delete = 'SELECT
					member_privilege_trans.mepe_CreatedDate AS create_date,
					member_privilege_trans.mepe_Reason AS reason_del,
					member_privilege_trans.mepe_DeletedDate AS delete_date,
	  				mi_user_type.name AS delete_by,
					privilege.priv_Name AS privilege_name,
					privilege.priv_Image AS privilege_image,
					privilege.priv_ImagePath AS privilege_path,
					privilege.priv_PrivilegeID AS privilege_id,
					"Privilege" AS type

					FROM member_privilege_trans

					LEFT JOIN privilege
					ON member_privilege_trans.priv_PrivilegeID = privilege.priv_PrivilegeID

					LEFT JOIN member_transaction_h
					ON member_privilege_trans.meth_MemberTransactionHID = member_transaction_h.meth_MemberTransactionHID

					LEFT JOIN mi_user
					ON mi_user.user_id = member_privilege_trans.mepe_DeletedBy 

					LEFT JOIN mi_user_type
					ON mi_user.user_type_id = mi_user_type.user_type_id 

					WHERE member_privilege_trans.meth_MemberTransactionHID = "'.$id.'"
					AND member_privilege_trans.mepe_Deleted = "T"

				UNION

				SELECT
					member_coupon_trans.meco_CreatedDate AS create_date,
					member_coupon_trans.meco_Reason AS reason_del,
					member_coupon_trans.meco_DeletedDate AS delete_date,
	  				mi_user_type.name AS delete_by,
					coupon.coup_Name AS privilege_name,
					coupon.coup_Image AS privilege_image,
					coupon.coup_ImagePath AS privilege_path,
					coupon.coup_CouponID AS privilege_id,
					"Coupon" AS type

					FROM member_coupon_trans

					LEFT JOIN coupon
					ON member_coupon_trans.coup_CouponID = coupon.coup_CouponID

					LEFT JOIN member_transaction_h
					ON member_coupon_trans.meth_MemberTransactionHID = member_transaction_h.meth_MemberTransactionHID

					LEFT JOIN mi_user
					ON mi_user.user_id = member_coupon_trans.meco_DeletedBy 

					LEFT JOIN mi_user_type
					ON mi_user.user_type_id = mi_user_type.user_type_id 

					WHERE member_coupon_trans.meth_MemberTransactionHID = "'.$id.'"
					AND coupon.coup_Birthday = ""
					AND member_coupon_trans.meco_Deleted = "T"

				UNION

				SELECT
					member_coupon_trans.meco_CreatedDate AS create_date,
					member_coupon_trans.meco_Reason AS reason_del,
					member_coupon_trans.meco_DeletedDate AS delete_date,
	  				mi_user_type.name AS delete_by,
					coupon.coup_Name AS privilege_name,
					coupon.coup_Image AS privilege_image,
					coupon.coup_ImagePath AS privilege_path,
					coupon.coup_CouponID AS privilege_id,
					"Birthday Coupon" AS type

					FROM member_coupon_trans

					LEFT JOIN coupon
					ON member_coupon_trans.coup_CouponID = coupon.coup_CouponID

					LEFT JOIN member_transaction_h
					ON member_coupon_trans.meth_MemberTransactionHID = member_transaction_h.meth_MemberTransactionHID

					LEFT JOIN mi_user
					ON mi_user.user_id = member_coupon_trans.meco_DeletedBy 

					LEFT JOIN mi_user_type
					ON mi_user.user_type_id = mi_user_type.user_type_id 

					WHERE member_coupon_trans.meth_MemberTransactionHID = "'.$id.'"
					AND coupon.coup_Birthday = "T"
					AND member_coupon_trans.meco_Deleted = "T"

				UNION

				SELECT
					member_activity_trans.meac_CreatedDate AS create_date,
					member_activity_trans.meac_Reason AS reason_del,
					member_activity_trans.meac_DeletedDate AS delete_date,
	  				mi_user_type.name AS delete_by,
					activity.acti_Name AS privilege_name,
					activity.acti_Image AS privilege_image,
					activity.acti_ImagePath AS privilege_path,
					activity.acti_ActivityID AS privilege_id,
					"Activity" AS type

					FROM member_activity_trans

					LEFT JOIN activity
					ON member_activity_trans.acti_ActivityID = activity.acti_ActivityID

					LEFT JOIN member_transaction_h
					ON member_activity_trans.meth_MemberTransactionHID = member_transaction_h.meth_MemberTransactionHID

					LEFT JOIN mi_user
					ON mi_user.user_id = member_activity_trans.meac_DeletedBy 

					LEFT JOIN mi_user_type
					ON mi_user.user_type_id = mi_user_type.user_type_id 

					WHERE member_activity_trans.meth_MemberTransactionHID = "'.$id.'"
					AND member_activity_trans.meac_Deleted = "T"

				ORDER BY delete_date DESC';

$rs_delete = $oDB->Query($sql_delete);

$detail_del = "";

while($del_data = $rs_delete->FetchRow(DBI_ASSOC)) {


	# PRIVILEGE IMAGE

	if($del_data['privilege_image']!=''){

		$privilege_image = '<img src="../../upload/'.$del_data['privilege_path'].$del_data['privilege_image'].'" class="image_border" height="60"/>';

	} else {

		$privilege_image = '<img src="../../images/card_privilege.jpg" height="60"/>';
	}


	# REASON

	if ($del_data['reason_del']=='') { $del_data['reason_del'] = '-'; }


	# DETAIL DATA

	$privilege_data = '<table style="width:100%">
							<tr>
								<td style="text-align:right;width:60px">
									Name<br>
									Type<br>
									Use Date
								</td>
								<td style="text-align:center;width:20px">
									:<br>
									:<br>
									:
								</td>
								<td>
									'.$del_data['privilege_name'].'<br>
									'.$del_data['type'].'<br>
									'.DateTime($del_data['create_date']).'<br>
								</td>
							</tr>
						</table>';


	$detail_del .= "<tr>
						<td style='text-align:center'>".$privilege_image."</td>
						<td>".$privilege_data."</td>
						<td style='text-align:center'>".$del_data['reason_del']."</td>
						<td style='text-align:center'>".DateTime($del_data['delete_date'])."</td>
						<td style='text-align:center'>".$del_data['delete_by']."</td>
					</tr>";
}




$oTmp->assign('detail_data',$detail_data);

$oTmp->assign('detail_del',$detail_del);

$oTmp->assign('is_menu', 'is_transaction');

$oTmp->assign('content_file','transaction/customer_delete.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
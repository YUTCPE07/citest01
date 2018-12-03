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

if ($_SESSION['role_action']['top']['view'] != 1) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$path_upload_member = $_SESSION['path_upload_member'];

$path_upload_collection = $_SESSION['path_upload_collection'];



$filter_point = $_REQUEST['filter_point'];

$filter_stamp = $_REQUEST['filter_stamp'];

$filter_purchase = $_REQUEST['filter_purchase'];

$filter_redeem = $_REQUEST['filter_redeem'];

$filter_branch = $_REQUEST['filter_branch'];

$filter_like = $_REQUEST['filter_like'];

$filter_comment = $_REQUEST['filter_comment'];

$filter_earn = $_REQUEST['filter_earn'];



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

	## BRANCH ID

	$sql_count_branch = "SELECT count(*)
						FROM mi_branch
						WHERE brand_id=".$_SESSION['user_brand_id'];

	$count_branch = $oDB->QueryOne($sql_count_branch);

	$sql_branch = "SELECT branch_id 
						FROM mi_branch
						WHERE brand_id=".$_SESSION['user_brand_id'];

	## EARN

	$sql_count_earn = "SELECT count(*)
						FROM hilight_coupon
						WHERE bran_BrandID=".$_SESSION['user_brand_id'];

	$count_earn = $oDB->QueryOne($sql_count_earn);

	$sql_earn = "SELECT coup_CouponID 
						FROM hilight_coupon
						WHERE bran_BrandID=".$_SESSION['user_brand_id'];

}

if($_SESSION['user_branch_id']){

	## PRIVILEGE ID 

	$sql_priv = "SELECT DISTINCT privilege_id 
						FROM mi_card_register
						WHERE branch_id='".$_SESSION['user_branch_id']."'
						AND status='0'
						AND privilege_id!=''";

	$count_priv = 0;
	$sql_count_priv = $oDB->Query($sql_priv);
	while($axRow_count_priv = $sql_count_priv->FetchRow(DBI_ASSOC)) {
		$count_priv++;
	}


	## COUPON ID 

	$sql_coup = "SELECT DISTINCT mi_card_register.coupon_id 
						FROM mi_card_register
						LEFT JOIN coupon
						on mi_card_register.coupon_id = coupon.coup_CouponID
						WHERE mi_card_register.branch_id=".$_SESSION['user_branch_id']." 
						AND mi_card_register.status='0'
						AND mi_card_register.coupon_id!=''
						AND coupon.coup_Birthday!='T'";

	$count_coup = 0;
	$sql_count_coup = $oDB->Query($sql_coup);
	while($axRow_count_coup = $sql_count_coup->FetchRow(DBI_ASSOC)) {
		$count_coup++;
	}


	## HBD ID 

	$sql_hbd = "SELECT DISTINCT mi_card_register.coupon_id 
						FROM mi_card_register
						LEFT JOIN coupon
						on mi_card_register.coupon_id = coupon.coup_CouponID
						WHERE mi_card_register.branch_id=".$_SESSION['user_branch_id']." 
						AND mi_card_register.status='0'
						AND mi_card_register.coupon_id!=''
						AND coupon.coup_Birthday='T'";

	$count_hbd = 0;
	$sql_count_hbd = $oDB->Query($sql_hbd);
	while($axRow_count_hbd = $sql_count_hbd->FetchRow(DBI_ASSOC)) {
		$count_hbd++;
	}


	## ACTIVITY ID 

	$sql_acti = "SELECT DISTINCT activity_id 
						FROM mi_card_register
						WHERE branch_id=".$_SESSION['user_branch_id']." 
						AND status='0'
						AND activity_id!=''";

	$count_acti = 0;
	$sql_count_acti = $oDB->Query($sql_acti);
	while($axRow_count_acti = $sql_count_acti->FetchRow(DBI_ASSOC)) {
		$count_acti++;
	}

}

$data_member = "";
$data_card = "";
$data_privilege = "";
$data_coupon = "";
$data_hbd = "";
$data_activity = "";
$data_brand = "";
$data_branch = "";
$data_your = "";
$data_point = "";
$data_stamp = "";
$data_purchase = "";
$data_rating = "";
$data_comment = "";
$data_earn = "";

$memb_n = "1";
$card_n = "1";
$priv_n = "1";
$coup_n = "1";
$hbd_n = "1";
$acti_n = "1";
$bran_n = "1";
$brnc_n = "1";
$your_n = "1";
$point_n = "1";
$stamp_n = "1";
$purchase_n = "1";
$redeem_n = "1";
$rating_n = "1";
$comment_n = "1";
$earn_n = "1";


/* ============== */
/*   Top Member   */
/* ============== */

	# WHERE BRANCH

	if($_SESSION['user_type_id_ses'] > 1) {

		$branch_id = "";

		$j = 1;

		$branch = $oDB->Query($sql_branch);

		while($axRow_branch = $branch->FetchRow(DBI_ASSOC)) {

			if ($j == $count_branch) { $branch_id .= $axRow_branch['branch_id']; } 
			else { $branch_id .= $axRow_branch['branch_id'].","; }

			$j++;
		}
	}

	# SEARCH

	$member_status = $_REQUEST['member_status'];

	$where_member = '';

	if ($member_status == "All") { $where_member = ' WHERE 1'; }

	else if ($member_status == "Active" || !$member_status) { 

		$where_member = " WHERE (
					(SELECT COUNT(member_privilege_trans.mepe_MemberPrivlegeID) 
						FROM member_privilege_trans
						LEFT JOIN mb_member_register
						ON mb_member_register.member_register_id = member_privilege_trans.mere_MemberRegisterID
						WHERE member_privilege_trans.mepe_Deleted=''
						AND mb_member_register.flag_del=''";

		if ($_SESSION['user_type_id_ses']==2) {

			$where_member .= ' AND member_privilege_trans.memb_MemberID=mb_member.member_id
						AND member_privilege_trans.brnc_BranchID IN ('.$branch_id.')';
			
		} else if ($_SESSION['user_type_id_ses']==3) {

			$where_member .= ' AND member_privilege_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
							AND member_privilege_trans.memb_MemberID=mb_member.member_id';
		} else {

			$where_member .= ' AND member_privilege_trans.memb_MemberID=mb_member.member_id';
		}

		$where_member .= "	)

						+

						(SELECT COUNT(member_coupon_trans.meco_MemberCouponID) 
							FROM member_coupon_trans
							LEFT JOIN mb_member_register
							ON mb_member_register.member_register_id = member_coupon_trans.mere_MemberRegisterID
							WHERE member_coupon_trans.meco_Deleted=''
							AND mb_member_register.flag_del=''";

		if ($_SESSION['user_type_id_ses']==2) {

			$where_member .= ' AND member_coupon_trans.memb_MemberID=mb_member.member_id
						AND member_coupon_trans.brnc_BranchID IN ('.$branch_id.')';
			
		} else if ($_SESSION['user_type_id_ses']==3) {

			$where_member .= ' AND member_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
							AND member_coupon_trans.memb_MemberID=mb_member.member_id';
		} else {

			$where_member .= ' AND member_coupon_trans.memb_MemberID=mb_member.member_id';
		}

		$where_member .= "	)

						+

						(SELECT COUNT(member_activity_trans.meac_MemberActivityID) 
							FROM member_activity_trans
							LEFT JOIN mb_member_register
							ON mb_member_register.member_register_id = member_activity_trans.mere_MemberRegisterID
							WHERE member_activity_trans.meac_Deleted=''
							AND mb_member_register.flag_del=''";

		if ($_SESSION['user_type_id_ses']==2) {

			$where_member .= ' AND member_activity_trans.memb_MemberID=mb_member.member_id
						AND member_activity_trans.brnc_BranchID IN ('.$branch_id.')';
			
		} else if ($_SESSION['user_type_id_ses']==3) {

			$where_member .= ' AND member_activity_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
							AND member_activity_trans.memb_MemberID=mb_member.member_id';
		} else {

			$where_member .= ' AND member_activity_trans.memb_MemberID=mb_member.member_id';
		}

		$where_member .= "	)

						+

						(SELECT COUNT(hilight_coupon_trans.hico_HilightCouponID) 
							FROM hilight_coupon_trans
							WHERE hilight_coupon_trans.hico_Deleted=''";

		if ($_SESSION['user_type_id_ses']==2) {

			$member .= 'AND hilight_coupon_trans.memb_MemberID=mb_member.member_id
						AND hilight_coupon_trans.brnc_BranchID IN ('.$branch_id.')';
			
		} else if ($_SESSION['user_type_id_ses']==3) {

			$member .= 'AND hilight_coupon_trans.memb_MemberID=mb_member.member_id
						AND hilight_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';
		} else {

			$member .= 'AND hilight_coupon_trans.memb_MemberID=mb_member.member_id';
		}

		$where_member .= "	)

						+

						(SELECT COUNT(hilight_coupon_buy.hcbu_HilightCouponBuyID) 
							FROM hilight_coupon_buy
							WHERE hilight_coupon_buy.hcbu_Deleted=''";

		if ($_SESSION['user_type_id_ses']==2) {

			$member .= 'AND hilight_coupon_buy.memb_MemberID=mb_member.member_id
						AND hilight_coupon_buy.brnc_BranchID IN ('.$branch_id.')';
			
		} else if ($_SESSION['user_type_id_ses']==3) {

			$member .= 'AND hilight_coupon_buy.memb_MemberID=mb_member.member_id
						AND hilight_coupon_buy.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';
		} else {

			$member .= 'AND hilight_coupon_buy.memb_MemberID=mb_member.member_id';
		}

		$where_member .= "	)
					) != 0";

	} else {

		$where_member = " WHERE (
					(SELECT COUNT(member_privilege_trans.mepe_MemberPrivlegeID) 
						FROM member_privilege_trans
						LEFT JOIN mb_member_register
						ON mb_member_register.member_register_id = member_privilege_trans.mere_MemberRegisterID
						WHERE member_privilege_trans.mepe_Deleted=''
						AND mb_member_register.flag_del=''";

		if ($_SESSION['user_type_id_ses']==2) {

			$where_member .= ' AND member_privilege_trans.memb_MemberID=mb_member.member_id
						AND member_privilege_trans.brnc_BranchID IN ('.$branch_id.')';
			
		} else if ($_SESSION['user_type_id_ses']==3) {

			$where_member .= ' AND member_privilege_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
							AND member_privilege_trans.memb_MemberID=mb_member.member_id';
		} else {

			$where_member .= ' AND member_privilege_trans.memb_MemberID=mb_member.member_id';
		}

		$where_member .= "	)

						+

						(SELECT COUNT(member_coupon_trans.meco_MemberCouponID) 
							FROM member_coupon_trans
							LEFT JOIN mb_member_register
							ON mb_member_register.member_register_id = member_coupon_trans.mere_MemberRegisterID
							WHERE member_coupon_trans.meco_Deleted=''
							AND mb_member_register.flag_del=''";

		if ($_SESSION['user_type_id_ses']==2) {

			$where_member .= ' AND member_coupon_trans.memb_MemberID=mb_member.member_id
						AND member_coupon_trans.brnc_BranchID IN ('.$branch_id.')';
			
		} else if ($_SESSION['user_type_id_ses']==3) {

			$where_member .= ' AND member_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
							AND member_coupon_trans.memb_MemberID=mb_member.member_id';
		} else {

			$where_member .= ' AND member_coupon_trans.memb_MemberID=mb_member.member_id';
		}

		$where_member .= "	)

						+

						(SELECT COUNT(member_activity_trans.meac_MemberActivityID) 
							FROM member_activity_trans
							LEFT JOIN mb_member_register
							ON mb_member_register.member_register_id = member_activity_trans.mere_MemberRegisterID
							WHERE member_activity_trans.meac_Deleted=''
							AND mb_member_register.flag_del=''";

		if ($_SESSION['user_type_id_ses']==2) {

			$where_member .= ' AND member_activity_trans.memb_MemberID=mb_member.member_id
						AND member_activity_trans.brnc_BranchID IN ('.$branch_id.')';
			
		} else if ($_SESSION['user_type_id_ses']==3) {

			$where_member .= ' AND member_activity_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
							AND member_activity_trans.memb_MemberID=mb_member.member_id';
		} else {

			$where_member .= ' AND member_activity_trans.memb_MemberID=mb_member.member_id';
		}

		$where_member .= "	)

						+

						(SELECT COUNT(hilight_coupon_trans.hico_HilightCouponID) 
							FROM hilight_coupon_trans
							WHERE hilight_coupon_trans.hico_Deleted=''";

		if ($_SESSION['user_type_id_ses']==2) {

			$member .= 'AND hilight_coupon_trans.memb_MemberID=mb_member.member_id
						AND hilight_coupon_trans.brnc_BranchID IN ('.$branch_id.')';
			
		} else if ($_SESSION['user_type_id_ses']==3) {

			$member .= 'AND hilight_coupon_trans.memb_MemberID=mb_member.member_id
						AND hilight_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';
		} else {

			$member .= 'AND hilight_coupon_trans.memb_MemberID=mb_member.member_id';
		}

		$where_member .= "	)

						+

						(SELECT COUNT(hilight_coupon_buy.hcbu_HilightCouponBuyID) 
							FROM hilight_coupon_buy
							WHERE hilight_coupon_buy.hcbu_Deleted=''";

		if ($_SESSION['user_type_id_ses']==2) {

			$member .= 'AND hilight_coupon_buy.memb_MemberID=mb_member.member_id
						AND hilight_coupon_buy.brnc_BranchID IN ('.$branch_id.')';
			
		} else if ($_SESSION['user_type_id_ses']==3) {

			$member .= 'AND hilight_coupon_buy.memb_MemberID=mb_member.member_id
						AND hilight_coupon_buy.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';
		} else {

			$member .= 'AND hilight_coupon_buy.memb_MemberID=mb_member.member_id';
		}

		$where_member .= "	)
					) = 0";
	}



	$member = "SELECT 
				mb_member.firstname, 
				mb_member.lastname, 
				mb_member.facebook_id, 
				mb_member.facebook_name, 
				mb_member.member_id, 
				mb_member.member_image,
				mb_member.email,
				mb_member.mobile,

				(
					(SELECT COUNT(member_privilege_trans.mepe_MemberPrivlegeID) 
						FROM member_privilege_trans
						LEFT JOIN mb_member_register
						ON mb_member_register.member_register_id = member_privilege_trans.mere_MemberRegisterID
						WHERE member_privilege_trans.mepe_Deleted=''
						AND mb_member_register.flag_del=''";

	if ($_SESSION['user_type_id_ses']==2) {

		$member .= ' AND member_privilege_trans.memb_MemberID=mb_member.member_id
						AND member_privilege_trans.brnc_BranchID IN ('.$branch_id.')';
		
	} else if ($_SESSION['user_type_id_ses']==3) {

		$member .= ' AND member_privilege_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
						AND member_privilege_trans.memb_MemberID=mb_member.member_id';
	} else {

		$member .= ' AND member_privilege_trans.memb_MemberID=mb_member.member_id';
	}

	$member .= "	)

					+

					(SELECT COUNT(member_coupon_trans.meco_MemberCouponID) 
						FROM member_coupon_trans
						LEFT JOIN mb_member_register
						ON mb_member_register.member_register_id = member_coupon_trans.mere_MemberRegisterID
						WHERE member_coupon_trans.meco_Deleted=''
						AND mb_member_register.flag_del=''";

	if ($_SESSION['user_type_id_ses']==2) {

		$member .= ' AND member_coupon_trans.memb_MemberID=mb_member.member_id
					AND member_coupon_trans.brnc_BranchID IN ('.$branch_id.')';
		
	} else if ($_SESSION['user_type_id_ses']==3) {

		$member .= ' AND member_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
						AND member_coupon_trans.memb_MemberID=mb_member.member_id';
	} else {

		$member .= ' AND member_coupon_trans.memb_MemberID=mb_member.member_id';
	}

	$member .= "	)

					+

					(SELECT COUNT(member_activity_trans.meac_MemberActivityID) 
						FROM member_activity_trans
						LEFT JOIN mb_member_register
						ON mb_member_register.member_register_id = member_activity_trans.mere_MemberRegisterID
						WHERE member_activity_trans.meac_Deleted=''
						AND mb_member_register.flag_del=''";

	if ($_SESSION['user_type_id_ses']==2) {

		$member .= ' AND member_activity_trans.memb_MemberID=mb_member.member_id
					AND member_activity_trans.brnc_BranchID IN ('.$branch_id.')';
		
	} else if ($_SESSION['user_type_id_ses']==3) {

		$member .= ' AND member_activity_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
						AND member_activity_trans.memb_MemberID=mb_member.member_id';
	} else {

		$member .= ' AND member_activity_trans.memb_MemberID=mb_member.member_id';
	}

	$member .= "	)

					+

					(SELECT COUNT(hilight_coupon_trans.hico_HilightCouponID) 
						FROM hilight_coupon_trans
						WHERE hilight_coupon_trans.hico_Deleted=''";

	if ($_SESSION['user_type_id_ses']==2) {

		$member .= 'AND hilight_coupon_trans.memb_MemberID=mb_member.member_id
					AND hilight_coupon_trans.brnc_BranchID IN ('.$branch_id.')';
		
	} else if ($_SESSION['user_type_id_ses']==3) {

		$member .= 'AND hilight_coupon_trans.memb_MemberID=mb_member.member_id
					AND hilight_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';
	} else {

		$member .= 'AND hilight_coupon_trans.memb_MemberID=mb_member.member_id';
	}

	$member .= "	)

					+

					(SELECT COUNT(hilight_coupon_buy.hcbu_HilightCouponBuyID) 
						FROM hilight_coupon_buy
						WHERE hilight_coupon_buy.hcbu_Deleted=''";

	if ($_SESSION['user_type_id_ses']==2) {

		$member .= 'AND hilight_coupon_buy.memb_MemberID=mb_member.member_id
					AND hilight_coupon_buy.brnc_BranchID IN ('.$branch_id.')';
		
	} else if ($_SESSION['user_type_id_ses']==3) {

		$member .= 'AND hilight_coupon_buy.memb_MemberID=mb_member.member_id
					AND hilight_coupon_buy.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';
	} else {

		$member .= 'AND hilight_coupon_buy.memb_MemberID=mb_member.member_id';
	}

	$member .= "	)
				) AS total

				FROM mb_member

				LEFT JOIN mb_member_register
				ON mb_member_register.member_id = mb_member.member_id

				LEFT JOIN mi_card
				ON mb_member_register.card_id = mi_card.card_id

				".$where_member."";

	if($_SESSION['user_type_id_ses'] > 1) {

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

			if ($j == $count_card) { $data_card .= $axRow_card['card_id']; } 
			else { $data_card .= $axRow_card['card_id'].","; }

			$j++;
		}

		if ($data_register) {

			$member .= " AND mb_member.member_id IN (".$data_register.")";

		} else {

			$member .= " AND mi_card.brand_id =".$_SESSION['user_brand_id'];
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

			if ($i == $count_regis) {

				$data_register .= $axRow_register['member_id'];

			} else {

				$data_register .= $axRow_register['member_id'].",";
			}

			$i++;
		}

		if ($data_register) {

			$member .= " AND mb_member.member_id IN (".$data_register.")";
		}
	}

	$member .= "	GROUP BY mb_member.member_id
				ORDER BY total DESC";



$rs_member = $oDB->Query($member);

if (!$rs_member) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_member->FetchRow(DBI_ASSOC)) {

		# MEMBER

		if($axRow['member_image']!='' && $axRow['member_image']!='user.png') {

			$axRow['member_image'] = '<img class="img-circle image_border" src="'.$path_upload_member.$axRow['member_image'].'"width="60" height="60"/>';

		} else if ($axRow['facebook_id']!='') {
			
			$axRow['member_image'] = '<img class="img-circle image_border" src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=large" width="60" height="60" />';
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


	  	$data_member .= '<tr>
					  	<td>'.$memb_n++.'</td>
					  	<td style="text-align:center">'.$axRow['member_image'].'</td>
					  	<td>'.$member_name.'</td>
					  	<td style="text-align:center">'.number_format($axRow['total']).'</td>
					  	<td style="text-align:center">
					  		<span style="cursor:pointer" onclick="'."window.location.href='top_member.php?id=".$axRow['member_id']."'".'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
	  				</tr>';
	}


	#  member_status dropdownlist

	$select_member = '';

	$select_member .= '<option value="All"';
		if ($member_status == "All") {	$select_member .= ' selected';	}
	$select_member .= '>All</option>';

	$select_member .=	'<option value="Active"';
		if ($member_status == "Active" || !$member_status) {	$select_member .= ' selected';	}
	$select_member .= '>Active</option>';

	$select_member .=	'<option value="Inactive"';
		if ($member_status == "Inactive") {	$select_member .= ' selected';	}
	$select_member .= '>Inactive</option>';

	$oTmp->assign('member_status', $select_member);
}

/* ============ */
/*   Top Card   */
/* ============ */

	# SEARCH

	$card_status = $_REQUEST['card_status'];

	$where_card = '';

	if ($card_status == "All") { $where_card = ''; }

	else if ($card_status == "Active" || !$card_status) { 

		$where_card = " AND (
					(SELECT COUNT(member_privilege_trans.mepe_MemberPrivlegeID) 
						FROM member_privilege_trans
						WHERE member_privilege_trans.mepe_Deleted=''";

		if ($_SESSION['user_type_id_ses']==2) {

			$where_card .= ' AND member_privilege_trans.card_CardID=mi_card.card_id
						AND mi_card.brand_id="'.$_SESSION['user_brand_id'].'"';
			
		} else if ($_SESSION['user_type_id_ses']==3) {

			$where_card .= ' AND member_privilege_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
							AND member_privilege_trans.card_CardID=mi_card.card_id';
		} else {

			$where_card .= ' AND member_privilege_trans.card_CardID=mi_card.card_id';
		}

		$where_card .= "	)

						+

						(SELECT COUNT(member_coupon_trans.meco_MemberCouponID) 
							FROM member_coupon_trans
							WHERE member_coupon_trans.meco_Deleted=''";

		if ($_SESSION['user_type_id_ses']==2) {

			$where_card .= ' AND member_coupon_trans.card_CardID=mi_card.card_id
						AND mi_card.brand_id="'.$_SESSION['user_brand_id'].'"';
			
		} else if ($_SESSION['user_type_id_ses']==3) {

			$where_card .= ' AND member_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
							AND member_coupon_trans.card_CardID=mi_card.card_id';
		} else {

			$where_card .= ' AND member_coupon_trans.card_CardID=mi_card.card_id';
		}

		$where_card .= "	)

						+

						(SELECT COUNT(member_activity_trans.meac_MemberActivityID) 
							FROM member_activity_trans
							WHERE member_activity_trans.meac_Deleted=''";

		if ($_SESSION['user_type_id_ses']==2) {

			$where_card .= ' AND member_activity_trans.card_CardID=mi_card.card_id
						AND mi_card.brand_id="'.$_SESSION['user_brand_id'].'"';
			
		} else if ($_SESSION['user_type_id_ses']==3) {

			$where_card .= ' AND member_activity_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
							AND member_activity_trans.card_CardID=mi_card.card_id';
		} else {

			$where_card .= ' AND member_activity_trans.card_CardID=mi_card.card_id';
		}

		$where_card .= "	)

					) != '0'";

	}

	else { 

		$where_card = " AND (
					(SELECT COUNT(member_privilege_trans.mepe_MemberPrivlegeID) 
						FROM member_privilege_trans
						WHERE member_privilege_trans.mepe_Deleted=''";

		if ($_SESSION['user_type_id_ses']==2) {

			$where_card .= ' AND member_privilege_trans.card_CardID=mi_card.card_id
						AND mi_card.brand_id="'.$_SESSION['user_brand_id'].'"';
			
		} else if ($_SESSION['user_type_id_ses']==3) {

			$where_card .= ' AND member_privilege_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
							AND member_privilege_trans.card_CardID=mi_card.card_id';
		} else {

			$where_card .= ' AND member_privilege_trans.card_CardID=mi_card.card_id';
		}

		$where_card .= "	)

						+

						(SELECT COUNT(member_coupon_trans.meco_MemberCouponID) 
							FROM member_coupon_trans
							WHERE member_coupon_trans.meco_Deleted=''";

		if ($_SESSION['user_type_id_ses']==2) {

			$where_card .= ' AND member_coupon_trans.card_CardID=mi_card.card_id
						AND mi_card.brand_id="'.$_SESSION['user_brand_id'].'"';
			
		} else if ($_SESSION['user_type_id_ses']==3) {

			$where_card .= ' AND member_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
							AND member_coupon_trans.card_CardID=mi_card.card_id';
		} else {

			$where_card .= ' AND member_coupon_trans.card_CardID=mi_card.card_id';
		}

		$where_card .= "	)

						+

						(SELECT COUNT(member_activity_trans.meac_MemberActivityID) 
							FROM member_activity_trans
							WHERE member_activity_trans.meco_Deleted=''";

		if ($_SESSION['user_type_id_ses']==2) {

			$where_card .= ' AND member_activity_trans.card_CardID=mi_card.card_id
						AND mi_card.brand_id="'.$_SESSION['user_brand_id'].'"';
			
		} else if ($_SESSION['user_type_id_ses']==3) {

			$where_card .= ' AND member_activity_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
							AND member_activity_trans.card_CardID=mi_card.card_id';
		} else {

			$where_card .= ' AND member_activity_trans.card_CardID=mi_card.card_id';
		}

		$where_card .= "	)

					) = '0'"; 
	}


	$card = "SELECT 
				mi_card.name AS card_name,
				mi_card.image AS card_image,
				mi_card.image_newupload,
				mi_card.path_image,
				mi_brand.name AS brand_name,
				mi_brand.logo_image AS brand_logo,
				mi_brand.path_logo,
				mi_card.card_id AS card_id,
				mi_card.flag_status AS card_status,
				mi_card.member_fee,
				mi_card_type.name AS card_type,

				(
					(SELECT COUNT(member_privilege_trans.mepe_MemberPrivlegeID) 
						FROM member_privilege_trans
						WHERE member_privilege_trans.mepe_Deleted=''";

	if ($_SESSION['user_type_id_ses']==2) {

		$card .= ' AND member_privilege_trans.card_CardID=mi_card.card_id
					AND mi_card.brand_id="'.$_SESSION['user_brand_id'].'"';
		
	} else if ($_SESSION['user_type_id_ses']==3) {

		$card .= ' AND member_privilege_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
					AND member_privilege_trans.card_CardID=mi_card.card_id';
	} else {

		$card .= ' AND member_privilege_trans.card_CardID=mi_card.card_id';
	}

	$card .= "	)

					+

					(SELECT COUNT(member_coupon_trans.meco_MemberCouponID) 
						FROM member_coupon_trans
						WHERE member_coupon_trans.meco_Deleted=''";

	if ($_SESSION['user_type_id_ses']==2) {

		$card .= ' AND member_coupon_trans.card_CardID=mi_card.card_id
					AND mi_card.brand_id="'.$_SESSION['user_brand_id'].'"';
		
	} else if ($_SESSION['user_type_id_ses']==3) {

		$card .= ' AND member_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
						AND member_coupon_trans.card_CardID=mi_card.card_id';
	} else {

		$card .= ' AND member_coupon_trans.card_CardID=mi_card.card_id';
	}

	$card .= "	)

					+

					(SELECT COUNT(member_activity_trans.meac_MemberActivityID) 
						FROM member_activity_trans
						WHERE member_activity_trans.meac_Deleted=''";

	if ($_SESSION['user_type_id_ses']==2) {

		$card .= ' AND member_activity_trans.card_CardID=mi_card.card_id
					AND mi_card.brand_id="'.$_SESSION['user_brand_id'].'"';
		
	} else if ($_SESSION['user_type_id_ses']==3) {

		$card .= ' AND member_activity_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
						AND member_activity_trans.card_CardID=mi_card.card_id';
	} else {

		$card .= ' AND member_activity_trans.card_CardID=mi_card.card_id';
	}

	$card .= "	)

				) AS total

				FROM mi_card

  				LEFT JOIN mi_card_type
    			ON mi_card.card_type_id = mi_card_type.card_type_id
				LEFT JOIN mi_brand
				ON mi_card.brand_id = mi_brand.brand_id 

				WHERE mi_card.flag_del!='1' "

				.$where_card;

	if($_SESSION['user_type_id_ses'] > 1) {

		$card .= "AND mi_card.brand_id =".$_SESSION['user_brand_id']." ";	

	}

	$card .= " GROUP BY mi_card.card_id
				ORDER BY total DESC";


$rs_card = $oDB->Query($card);

$data_card = "";

if (!$rs_card) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_card->FetchRow(DBI_ASSOC)) {

		# LOGO

		if($axRow['brand_logo']!=''){

			$axRow['brand_logo'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" height="60" width="60" />';

		} else {

			$axRow['brand_logo'] = '<img src="../../images/400x400.png" class="image_border" height="60" width="60" />';	
		}


		# CARD

		if($axRow['image_newupload']!=''){

			$axRow['card_image'] = '<img src="../../upload/'.$axRow['path_image'].$axRow['image_newupload'].'" height="60" class="img-rounded image_border"/>';

		} else if($axRow['card_image']!='') {

			$axRow['card_image'] = '<img src="../../upload/'.$axRow['path_image'].$axRow['card_image'].'" height="60" class="img-rounded image_border"/>';

		} else {

			$axRow['card_image'] = '<img src="../../images/card_privilege.jpg" height="60" class="img-rounded image_border"/>';	
		}


		# STATUS

		if($axRow['card_status']==1){ 

			$axRow['card_status'] = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>'; 

		} else { 

			$axRow['card_status'] = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>'; 
		}


		# TABLE

	  	$data_card .= '<tr>
					  	<td>'.$card_n++.'</td>
					  	<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['brand_logo'].'</a><br>
					  		<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
					  	<td style="text-align:center"><a href="../card/card.php">'.$axRow['card_image'].'</a></td>
					  	<td>'.$axRow['card_name'].'</td>
					  	<td>'.$axRow['card_type'].'</td>
					  	<td style="padding-right:15px;text-align:right">'.number_format($axRow['member_fee'],2).' ฿</td>
					  	<td style="text-align:center">'.$axRow['card_status'].'</td>
					  	<td style="text-align:center">'.number_format($axRow['total']).'</td>
					  	<td style="text-align:center">
					  		<span style="cursor:pointer" onclick="'."window.location.href='top_card.php?id=".$axRow['card_id']."'".'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
	  				</tr>';
	}


	#  card_status dropdownlist

	$select_card = '';

	$select_card .= '<option value="All"';
		if ($card_status == "All") {	$select_card .= ' selected';	}
	$select_card .= '>All</option>';

	$select_card .=	'<option value="Active"';
		if ($card_status == "Active" || !$card_status) {	$select_card .= ' selected';	}
	$select_card .= '>Active</option>';

	$select_card .=	'<option value="Inactive"';
		if ($card_status == "Inactive") {	$select_card .= ' selected';	}
	$select_card .= '>Inactive</option>';

	$oTmp->assign('card_status', $select_card);
}

/* ================= */
/*   Top Privilege   */
/* ================= */

	# SEARCH

	$privilege_status = $_REQUEST['privilege_status'];

	$where_privilege = '';

	if ($privilege_status == "All") { $where_privilege = ''; }

	else if ($privilege_status == "Active" || !$privilege_status) { 

		$where_privilege = " AND (SELECT COUNT(member_privilege_trans.mepe_MemberPrivlegeID) 
					FROM member_privilege_trans 
					WHERE member_privilege_trans.priv_PrivilegeID=privilege.priv_PrivilegeID
					AND member_privilege_trans.mepe_Deleted=''";

		if ($_SESSION['user_type_id_ses']==3) {

			$where_privilege .= ' AND member_privilege_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';
		}

		$where_privilege .= ") != '0'";

	} else { 

		$where_privilege = " AND (SELECT COUNT(member_privilege_trans.mepe_MemberPrivlegeID) 
					FROM member_privilege_trans 
					WHERE member_privilege_trans.priv_PrivilegeID=privilege.priv_PrivilegeID
					AND member_privilege_trans.mepe_Deleted=''";

		if ($_SESSION['user_type_id_ses']==3) {

			$where_privilege .= ' AND member_privilege_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';
		}

		$where_privilege .= ") = '0'";
	}


	$privilege = "SELECT 
				privilege.priv_Name AS privilege_name,
				privilege.priv_Image,
				privilege.priv_ImageNew,
				privilege.priv_ImagePath,
				privilege.priv_PrivilegeID,
				privilege.priv_Status,
				mi_privilege_type.name AS privilege_type,
				mi_brand.logo_image AS brand_logo,
				mi_brand.path_logo,
				mi_brand.brand_id,
				mi_brand.name AS brand_name,

				(SELECT COUNT(member_privilege_trans.mepe_MemberPrivlegeID) 
					FROM member_privilege_trans 
					WHERE member_privilege_trans.priv_PrivilegeID=privilege.priv_PrivilegeID
					AND member_privilege_trans.mepe_Deleted=''";

	if ($_SESSION['user_type_id_ses']==3) {

		$privilege .= ' AND member_privilege_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';
	}

	$privilege .= ") AS total

				FROM privilege
				
				LEFT JOIN mi_privilege_type
				ON privilege.prty_PrivilegeTypeID = mi_privilege_type.privilege_type_id
				LEFT JOIN mi_brand
				ON privilege.bran_BrandID = mi_brand.brand_id 

				WHERE privilege.priv_Deleted != 'T'"

				.$where_privilege;

	if($_SESSION['user_type_id_ses'] == 2) {

		$privilege .= " AND privilege.bran_BrandID = ".$_SESSION['user_brand_id']." ";
	}

	$data_priv = "";

	if($_SESSION['user_type_id_ses'] == 3) {

		$i = 1;

		$privilege_sql = $oDB->Query($sql_priv);

		while($axRow_priv = $privilege_sql->FetchRow(DBI_ASSOC)) {

			if ($i == $count_priv) { $data_priv .= $axRow_priv['privilege_id']; } 
			else { $data_priv .= $axRow_priv['privilege_id'].","; }

			$i++;
		}

		if ($data_priv) {

			$privilege .= " AND privilege.priv_PrivilegeID IN (".$data_priv.") ";
		}
	}

	$privilege .= " GROUP BY privilege.priv_PrivilegeID 
				ORDER BY total DESC";


$rs_priv = $oDB->Query($privilege);

if (!$rs_priv) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_priv->FetchRow(DBI_ASSOC)) {

		# LOGO

		if($axRow['brand_logo']!=''){

			$axRow['brand_logo'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" height="60" width="60" />';

		} else {

			$axRow['brand_logo'] = '<img src="../../images/400x400.png" class="image_border" height="60" width="60" />';	
		}


		# PRIVILEGE

		if($axRow['priv_ImageNew']!=''){

			$axRow['priv_Image'] = '<img src="../../upload/'.$axRow['priv_ImagePath'].$axRow['priv_ImageNew'].'" height="60" class="image_border"/>';

		} else if($axRow['priv_Image']!='') {

			$axRow['priv_Image'] = '<img src="../../upload/'.$axRow['priv_ImagePath'].$axRow['priv_Image'].'" height="60" class="image_border"/>';

		} else {

			$axRow['priv_Image'] = '<img src="../../images/card_privilege.jpg" height="60" class="image_border"/>';	
		}


		# STATUS

		if($axRow['priv_Status']=='Active'){ 

			$axRow['priv_Status'] = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>'; 

		} else { 

			$axRow['priv_Status'] = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>'; 
		}


		# TABLE

	  	$data_privilege .= '<tr>
					  	<td>'.$priv_n++.'</td>
					  	<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['brand_logo'].'</a><br>
					  		<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
					  	<td style="text-align:center"><a href="../privilege/privilege.php">'.$axRow['priv_Image'].'</a></td>
					  	<td>'.$axRow['privilege_name'].'</td>
					  	<td>'.$axRow['privilege_type'].'</td>
					  	<td>'.$axRow['priv_Status'].'</td>
					  	<td style="text-align:center">'.number_format($axRow['total']).'</td>
						<td style="text-align:center">
							<span style="cursor:pointer" onclick="'."window.location.href='top_privilege.php?id=".$axRow['priv_PrivilegeID']."'".'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
	  				</tr>';
	}


	#  privilege_status dropdownlist

	$select_privilege = '';

	$select_privilege .= '<option value="All"';
		if ($privilege_status == "All") {	$select_privilege .= ' selected';	}
	$select_privilege .= '>All</option>';

	$select_privilege .=	'<option value="Active"';
		if ($privilege_status == "Active" || !$privilege_status) {	$select_privilege .= ' selected';	}
	$select_privilege .= '>Active</option>';

	$select_privilege .=	'<option value="Inactive"';
		if ($privilege_status == "Inactive") {	$select_privilege .= ' selected';	}
	$select_privilege .= '>Inactive</option>';

	$oTmp->assign('privilege_status', $select_privilege);
}

/* ================= */
/*     Top Coupon    */
/* ================= */

	# SEARCH

	$coupon_status = $_REQUEST['coupon_status'];

	$where_coupon = '';

	if ($coupon_status == "All") { $where_coupon = ''; }

	else if ($coupon_status == "Active" || !$coupon_status) { 

		$where_coupon = " AND (SELECT COUNT(member_coupon_trans.meco_MemberCouponID) 
					FROM member_coupon_trans";

		if ($_SESSION['user_type_id_ses']==3) {

			$where_coupon .= ' LEFT JOIN coupon
						ON coupon.coup_CouponID = member_coupon_trans.coup_CouponID
						WHERE member_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
						AND member_coupon_trans.meco_Deleted=""
						AND coupon.coup_Birthday!="T"';
		} else {

			$where_coupon .= " WHERE member_coupon_trans.coup_CouponID=coupon.coup_CouponID
								AND member_coupon_trans.meco_Deleted=''";
		}

		$where_coupon .= " ) != '0'";

	} else { 

		$where_coupon = " AND (SELECT COUNT(member_coupon_trans.meco_MemberCouponID) 
					FROM member_coupon_trans";

		if ($_SESSION['user_type_id_ses']==3) {

			$where_coupon .= ' LEFT JOIN coupon
						ON coupon.coup_CouponID = member_coupon_trans.coup_CouponID
						WHERE member_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
						AND member_coupon_trans.meco_Deleted=""
						AND coupon.coup_Birthday!="T"';
		} else {

			$where_coupon .= " WHERE member_coupon_trans.coup_CouponID=coupon.coup_CouponID
								AND member_coupon_trans.meco_Deleted=''";
		} 

		$where_coupon .= " ) = '0'";
	}


	$coupon = "SELECT 
				coupon.coup_Name AS privilege_name,
				coupon.coup_Image,
				coupon.coup_ImageNew,
				coupon.coup_ImagePath,
				coupon.coup_CouponID,
				coupon.coup_Status,
				mi_privilege_type.name AS privilege_type,
				mi_brand.logo_image AS brand_logo,
				mi_brand.path_logo,
				mi_brand.brand_id,
				mi_brand.name AS brand_name,

				(SELECT COUNT(member_coupon_trans.meco_MemberCouponID) 
					FROM member_coupon_trans";

	if ($_SESSION['user_type_id_ses']==3) {

		$coupon .= ' LEFT JOIN coupon
					ON coupon.coup_CouponID = member_coupon_trans.coup_CouponID
					WHERE member_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
					AND member_coupon_trans.meco_Deleted=""
					AND coupon.coup_Birthday!="T"';
	} else {

		$coupon .= " WHERE member_coupon_trans.coup_CouponID=coupon.coup_CouponID
					AND member_coupon_trans.meco_Deleted=''
					AND coupon.coup_Birthday!='T'";
	}

	$coupon .= " ) AS total

				FROM coupon
				
				LEFT JOIN mi_privilege_type
				ON coupon.prty_PrivilegeTypeID = mi_privilege_type.privilege_type_id
				LEFT JOIN mi_brand
				ON coupon.bran_BrandID = mi_brand.brand_id 

				WHERE coupon.coup_Birthday !='T'
				AND coupon.coup_Deleted !='T'"

				.$where_coupon;


	if($_SESSION['user_type_id_ses'] == 2) {

		$coupon .= " AND coupon.bran_BrandID = ".$_SESSION['user_brand_id']." ";
	}

	$data_coup = "";

	if($_SESSION['user_type_id_ses'] == 3) {

		$i = 1;

		$coupon_sql = $oDB->Query($sql_coup);

		while($axRow_coup = $coupon_sql->FetchRow(DBI_ASSOC)) {

			if ($i == $count_coup) { $data_coup .= $axRow_coup['coupon_id']; } 
			else { $data_coup .= $axRow_coup['coupon_id'].","; }

			$i++;
		}

		if ($data_coup) {

			$coupon .= " AND coupon.coup_CouponID IN (".$data_coup.") ";
		}
	}

	$coupon .= " GROUP BY coupon.coup_CouponID 
				ORDER BY total DESC";


$rs_coup = $oDB->Query($coupon);



if (!$rs_coup) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_coup->FetchRow(DBI_ASSOC)) {

		# LOGO

		if($axRow['brand_logo']!=''){

			$axRow['brand_logo'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" height="60" width="60" />';

		} else {

			$axRow['brand_logo'] = '<img src="../../images/400x400.png" class="image_border" height="60" width="60" />';	
		}


		# PRIVILEGE

		if($axRow['coup_ImageNew']!=''){

			$axRow['coup_Image'] = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_ImageNew'].'" height="60" class="image_border"/>';

		} else if($axRow['coup_Image']!='') {

			$axRow['coup_Image'] = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_Image'].'" height="60" class="image_border"/>';

		} else {

			$axRow['coup_Image'] = '<img src="../../images/card_privilege.jpg" height="60" class="image_border"/>';	
		}


		# STATUS

		if($axRow['coup_Status']=='Active'){ 

			$axRow['coup_Status'] = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>'; 

		} else { 

			$axRow['coup_Status'] = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>'; 
		}


		# TABLE

	  	$data_coupon .= '<tr>
					  	<td>'.$coup_n++.'</td>
					  	<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['brand_logo'].'</a><br>
					  		<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
					  	<td style="text-align:center"><a href="../coupon/coupon.php">'.$axRow['coup_Image'].'</a></td>
					  	<td>'.$axRow['privilege_name'].'</td>
					  	<td>'.$axRow['privilege_type'].'</td>
					  	<td>'.$axRow['coup_Status'].'</td>
					  	<td style="text-align:center">'.number_format($axRow['total']).'</td>
						<td style="text-align:center">
							<span style="cursor:pointer" onclick="'."window.location.href='top_coupon.php?id=".$axRow['coup_CouponID']."'".'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
	  				</tr>';
	}


	#  coupon_status dropdownlist

	$select_coupon = '';

	$select_coupon .= '<option value="All"';
		if ($coupon_status == "All") {	$select_coupon .= ' selected';	}
	$select_coupon .= '>All</option>';

	$select_coupon .=	'<option value="Active"';
		if ($coupon_status == "Active" || !$coupon_status) {	$select_coupon .= ' selected';	}
	$select_coupon .= '>Active</option>';

	$select_coupon .=	'<option value="Inactive"';
		if ($coupon_status == "Inactive") {	$select_coupon .= ' selected';	}
	$select_coupon .= '>Inactive</option>';

	$oTmp->assign('coupon_status', $select_coupon);
}

/* ================= */
/*    Top Birthday   */
/* ================= */

	# SEARCH

	$hbd_status = $_REQUEST['hbd_status'];

	$where_hbd = '';

	if ($hbd_status == "All") { $where_hbd = ''; }

	else if ($hbd_status == "Active" || !$hbd_status) { 

		$where_hbd = " AND (SELECT COUNT(member_coupon_trans.meco_MemberCouponID) 
					FROM member_coupon_trans";

		if ($_SESSION['user_type_id_ses']==3) {

			$where_hbd .= ' LEFT JOIN coupon
						ON coupon.coup_CouponID = member_coupon_trans.coup_CouponID
						WHERE member_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
						AND member_coupon_trans.meco_Deleted=""
						AND coupon.coup_Birthday="T"';
		} else {

			$where_hbd .= " WHERE member_coupon_trans.coup_CouponID=coupon.coup_CouponID
							AND member_coupon_trans.meco_Deleted=''";
		}

		$where_hbd .= " ) != '0'";
	}

	else { 

		$where_hbd = " AND (SELECT COUNT(member_coupon_trans.meco_MemberCouponID) 
					FROM member_coupon_trans";

		if ($_SESSION['user_type_id_ses']==3) {

			$where_hbd .= ' LEFT JOIN coupon
						ON coupon.coup_CouponID = member_coupon_trans.coup_CouponID
						WHERE member_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
						AND member_coupon_trans.meco_Deleted=""
						AND coupon.coup_Birthday="T"';
		} else {

			$where_hbd .= " WHERE member_coupon_trans.coup_CouponID=coupon.coup_CouponID
							AND member_coupon_trans.meco_Deleted=''";
		}

		$where_hbd .= " ) = '0'";
	}


		$hbd = "SELECT 
				coupon.coup_Name AS privilege_name,
				coupon.coup_Image,
				coupon.coup_ImageNew,
				coupon.coup_ImagePath,
				coupon.coup_CouponID,
				coupon.coup_Status,
				mi_privilege_type.name AS privilege_type,
				mi_brand.logo_image AS brand_logo,
				mi_brand.path_logo,
				mi_brand.brand_id,
				mi_brand.name AS brand_name,

				(SELECT COUNT(member_coupon_trans.meco_MemberCouponID) 
					FROM member_coupon_trans ";

	if ($_SESSION['user_type_id_ses']==3) {

		$hbd .= ' LEFT JOIN coupon
					ON coupon.coup_CouponID = member_coupon_trans.coup_CouponID
					WHERE member_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
					AND member_coupon_trans.meco_Deleted=""
					AND coupon.coup_Birthday="T"';
	} else {

		$hbd .= " WHERE member_coupon_trans.coup_CouponID=coupon.coup_CouponID
					AND member_coupon_trans.meco_Deleted=''
					AND coupon.coup_Birthday='T'";

	}

	$hbd .= " ) AS total

				FROM coupon
				
				LEFT JOIN mi_privilege_type
				ON coupon.prty_PrivilegeTypeID = mi_privilege_type.privilege_type_id
				LEFT JOIN mi_brand
				ON coupon.bran_BrandID = mi_brand.brand_id 

				WHERE coupon.coup_Birthday ='T'
				AND coupon.coup_Deleted !='T'"

				.$where_hbd;


	if($_SESSION['user_type_id_ses'] == 2) {

		$hbd .= " AND coupon.bran_BrandID = ".$_SESSION['user_brand_id']." ";
	}

	$data_coup = "";

	if($_SESSION['user_type_id_ses'] == 3) {

		$i = 1;

		$coupon_sql = $oDB->Query($sql_coup);

		while($axRow_coup = $coupon_sql->FetchRow(DBI_ASSOC)) {

			if ($i == $count_coup) { $data_coup .= $axRow_coup['coupon_id']; } 
			else { $data_coup .= $axRow_coup['coupon_id'].","; }

			$i++;
		}

		if ($data_coup) {

			$hbd .= " AND coupon.coup_CouponID IN (".$data_coup.") ";
		}
	}

	$hbd .= " GROUP BY coupon.coup_CouponID 
				ORDER BY total DESC";



$rs_hbd = $oDB->Query($hbd);



if (!$rs_hbd) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_hbd->FetchRow(DBI_ASSOC)) {

		# LOGO

		if($axRow['brand_logo']!=''){

			$axRow['brand_logo'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" height="60" width="60" />';

		} else {

			$axRow['brand_logo'] = '<img src="../../images/400x400.png" class="image_border" height="60" width="60" />';	
		}


		# PRIVILEGE

		if($axRow['coup_ImageNew']!=''){

			$axRow['coup_Image'] = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_ImageNew'].'" height="60" class="image_border"/>';

		} else if($axRow['coup_Image']!='') {

			$axRow['coup_Image'] = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_Image'].'" height="60" class="image_border"/>';

		} else {

			$axRow['coup_Image'] = '<img src="../../images/card_privilege.jpg" height="60" class="image_border"/>';	
		}


		# STATUS

		if($axRow['coup_Status']=='Active'){ 

			$axRow['coup_Status'] = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>'; 

		} else { 

			$axRow['coup_Status'] = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>'; 
		}


		# TABLE

	  	$data_hbd .= '<tr>
					  	<td>'.$hbd_n++.'</td>
					  	<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['brand_logo'].'</a><br>
					  		<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
					  	<td style="text-align:center"><a href="../coupon/birthday.php">'.$axRow['coup_Image'].'</a></td>
					  	<td>'.$axRow['privilege_name'].'</td>
					  	<td>'.$axRow['privilege_type'].'</td>
					  	<td>'.$axRow['coup_Status'].'</td>
					  	<td style="text-align:center">'.number_format($axRow['total']).'</td>
						<td style="text-align:center">
							<span style="cursor:pointer" onclick="'."window.location.href='top_coupon.php?id=".$axRow['coup_CouponID']."'".'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
	  				</tr>';
	}


	#  hbd_status dropdownlist

	$select_hbd = '';

	$select_hbd .= '<option value="All"';
		if ($hbd_status == "All") {	$select_hbd .= ' selected';	}
	$select_hbd .= '>All</option>';

	$select_hbd .=	'<option value="Active"';
		if ($hbd_status == "Active" || !$hbd_status) {	$select_hbd .= ' selected';	}
	$select_hbd .= '>Active</option>';

	$select_hbd .=	'<option value="Inactive"';
		if ($hbd_status == "Inactive") {	$select_hbd .= ' selected';	}
	$select_hbd .= '>Inactive</option>';

	$oTmp->assign('hbd_status', $select_hbd);
}

/* ================= */
/*    Top Activity   */
/* ================= */

	# SEARCH

	$activity_status = $_REQUEST['activity_status'];

	$where_activity = '';

	if ($activity_status == "All") { $where_activity = ''; }

	else if ($activity_status == "Active" || !$activity_status) { 

		$where_activity = " AND (SELECT COUNT(member_activity_trans.meac_MemberActivityID) 
					FROM member_activity_trans";

		if ($_SESSION['user_type_id_ses']==3) {

			$where_activity .= ' LEFT JOIN activity
						ON activity.acti_ActivityID = member_activity_trans.acti_ActivityID
						WHERE member_activity_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
						AND member_activity_trans.meac_Deleted=""';
		} else {

			$where_activity .= " WHERE member_activity_trans.acti_ActivityID=activity.acti_ActivityID
								AND member_activity_trans.meac_Deleted=''";
		}

		$where_activity .= " ) != '0'";

	} else { 

		$where_activity = " AND (SELECT COUNT(member_activity_trans.meac_MemberActivityID) 
					FROM member_activity_trans";

		if ($_SESSION['user_type_id_ses']==3) {

			$where_activity .= ' LEFT JOIN activity
						ON activity.acti_ActivityID = member_activity_trans.acti_ActivityID
						WHERE member_activity_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
						AND member_activity_trans.meac_Deleted=""';
		} else {

			$where_activity .= " WHERE member_activity_trans.acti_ActivityID=activity.acti_ActivityID
								AND member_activity_trans.meac_Deleted=''";
		}

		$where_activity .= " ) = '0'";
	}


	$activity = "SELECT 
				activity.acti_Name AS privilege_name,
				activity.acti_Image,
				activity.acti_ImagePath,
				activity.acti_ImageNew,
				activity.acti_ActivityID,
				activity.acti_Status,
				mi_privilege_type.name AS privilege_type,
				mi_brand.logo_image AS brand_logo,
				mi_brand.path_logo,
				mi_brand.brand_id,
				mi_brand.name AS brand_name,

				(SELECT COUNT(member_activity_trans.meac_MemberActivityID) 
					FROM member_activity_trans
					WHERE member_activity_trans.acti_ActivityID=activity.acti_ActivityID
					AND member_activity_trans.meac_Deleted=''";

	if ($_SESSION['user_type_id_ses']==3) {

		$activity .= ' AND member_activity_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';
	}

	$activity .= ") AS total

				FROM activity
				
				LEFT JOIN mi_privilege_type
				ON activity.prty_PrivilegeTypeID = mi_privilege_type.privilege_type_id
				LEFT JOIN mi_brand
				ON activity.bran_BrandID = mi_brand.brand_id 

				WHERE activity.acti_Deleted != 'T'"

				.$where_activity;


	if($_SESSION['user_type_id_ses'] == 2) {

		$activity .= " AND activity.bran_BrandID = ".$_SESSION['user_brand_id']." ";
	}

	$data_acti = "";

	if($_SESSION['user_type_id_ses'] == 3) {

		$i = 1;

		$activity_sql = $oDB->Query($sql_acti);

		while($axRow_acti = $activity_sql->FetchRow(DBI_ASSOC)) {

			if ($i == $count_acti) { $data_acti .= $axRow_acti['activity_id']; } 
			else { $data_acti .= $axRow_acti['activity_id'].","; }

			$i++;
		}

		if ($data_acti) {

			$activity .= " AND activity.acti_ActivityID IN (".$data_acti.") ";
		}
	}

	$activity .= " GROUP BY activity.acti_ActivityID 
				ORDER BY total DESC";



$rs_activity = $oDB->Query($activity);

if (!$rs_activity) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_activity->FetchRow(DBI_ASSOC)) {

		# LOGO

		if($axRow['brand_logo']!=''){

			$axRow['brand_logo'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" height="60" width="60" />';

		} else {

			$axRow['brand_logo'] = '<img src="../../images/400x400.png" class="image_border" height="60" width="60" />';	
		}


		# PRIVILEGE

		if($axRow['acti_ImageNew']!=''){

			$axRow['acti_Image'] = '<img src="../../upload/'.$axRow['acti_ImagePath'].$axRow['acti_ImageNew'].'" height="60" class="image_border"/>';

		} else if($axRow['acti_Image']!='') {

			$axRow['acti_Image'] = '<img src="../../upload/'.$axRow['acti_ImagePath'].$axRow['acti_Image'].'" height="60" class="image_border"/>';

		} else {

			$axRow['acti_Image'] = '<img src="../../images/card_privilege.jpg" height="60" class="image_border"/>';	
		}


		# STATUS

		if($axRow['acti_Status']=='Active'){ 

			$axRow['acti_Status'] = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>'; 

		} else { 

			$axRow['acti_Status'] = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>'; 
		}


		# TABLE

	  	$data_activity .= '<tr>
					  	<td>'.$acti_n++.'</td>
					  	<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['brand_logo'].'</a><br>
					  		<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
					  	<td style="text-align:center"><a href="../activity/activity.php">'.$axRow['acti_Image'].'</a></td>
					  	<td>'.$axRow['privilege_name'].'</td>
					  	<td>'.$axRow['privilege_type'].'</td>
					  	<td>'.$axRow['acti_Status'].'</td>
					  	<td style="text-align:center">'.number_format($axRow['total']).'</td>
						<td style="text-align:center">
							<span style="cursor:pointer" onclick="'."window.location.href='top_activity.php?id=".$axRow['acti_ActivityID']."'".'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
	  				</tr>';
	}


	#  activity_status dropdownlist

	$select_activity = '';

	$select_activity .= '<option value="All"';
		if ($activity_status == "All") {	$select_activity .= ' selected';	}
	$select_activity .= '>All</option>';

	$select_activity .=	'<option value="Active"';
		if ($activity_status == "Active" || !$activity_status) {	$select_activity .= ' selected';	}
	$select_activity .= '>Active</option>';

	$select_activity .=	'<option value="Inactive"';
		if ($activity_status == "Inactive") {	$select_activity .= ' selected';	}
	$select_activity .= '>Inactive</option>';

	$oTmp->assign('activity_status', $select_activity);
}

/* ============= */
/*   Top Brand   */
/* ============= */

	$brand = "SELECT *,
				mi_brand.name AS brand_name,
				mi_brand.brand_id AS brand_id_data,
				mi_brand.logo_image AS brand_logo,
				mi_brand.path_logo,
			  	mi_category_brand.name AS category_brand,
			  	mi_brand_type.name_en AS type_brand,

				((SELECT COUNT(mepe_MemberPrivlegeID) 
					FROM member_privilege_trans
					LEFT JOIN mi_branch
					ON mi_branch.branch_id = member_privilege_trans.brnc_BranchID
					WHERE mi_branch.brand_id=mi_brand.brand_id
					AND member_privilege_trans.mepe_Deleted='')
					+
				(SELECT COUNT(meco_MemberCouponID) 
					FROM member_coupon_trans 
					LEFT JOIN mi_branch
					ON mi_branch.branch_id = member_coupon_trans.brnc_BranchID
					WHERE mi_branch.brand_id=mi_brand.brand_id
					AND member_coupon_trans.meco_Deleted='')
					+
				(SELECT COUNT(meac_MemberActivityID) 
					FROM member_activity_trans 
					LEFT JOIN mi_branch
					ON mi_branch.branch_id = member_activity_trans.brnc_BranchID
					WHERE mi_branch.brand_id=mi_brand.brand_id
					AND member_activity_trans.meac_Deleted='')
					+
				(SELECT COUNT(hico_HilightCouponID) 
					FROM hilight_coupon_trans 
					LEFT JOIN mi_branch
					ON mi_branch.branch_id = hilight_coupon_trans.brnc_BranchID
					WHERE mi_branch.brand_id=mi_brand.brand_id
					AND hilight_coupon_trans.hico_Deleted='')) 

					AS total

				FROM mi_brand
				
				LEFT JOIN mi_category_brand
				ON mi_category_brand.category_brand_id = mi_brand.category_brand 
				LEFT JOIN mi_brand_type
				ON mi_brand_type.brand_type_id = mi_brand.type_brand 

				WHERE mi_brand.flag_del != '1'

				GROUP BY mi_brand.brand_id 
				ORDER BY total DESC";


$rs_brand = $oDB->Query($brand);

if (!$rs_brand) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_brand->FetchRow(DBI_ASSOC)) {

		# LOGO

		if($axRow['brand_logo']!=''){

			$axRow['brand_logo'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" height="60" width="60" />';

		} else {

			$axRow['brand_logo'] = '<img src="../../images/400x400.png" class="image_border" height="60" width="60" />';	
		}


		# STATUS

		if($axRow['flag_status']=='1'){ 

			$axRow['flag_status'] = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>'; 

		} else { 

			$axRow['flag_status'] = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>'; 
		}


		# TABLE

	  	$data_brand .= '<tr>
					  	<td>'.$bran_n++.'</td>
					  	<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['brand_logo'].'</a></td>
					  	<td>'.$axRow['brand_name'].'</td>
					  	<td>'.$axRow['type_brand'].'</td>
					  	<td>'.$axRow['category_brand'].'</td>
					  	<td>'.$axRow['flag_status'].'</td>
					  	<td style="text-align:center">'.number_format($axRow['total']).'</td>
					  	<td style="text-align:center">
					  		<span style="cursor:pointer" onclick="'."window.location.href='top_brand.php?id=".$axRow['brand_id_data']."'".'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
	  				</tr>';
	}
}


/* ============= */
/*   Top Branch  */
/* ============= */

if($_SESSION['user_type_id_ses'] == 2) {

	$branch = "SELECT mi_branch.*,
				mi_branch.branch_id AS branch_id_data,
				mi_branch.name AS branch_name,
				mi_branch.phone AS branch_phone,
				mi_branch.mobile AS branch_mobile,
				mi_branch.email AS branch_email,
				mi_brand.name AS brand_name,
				mi_brand.path_logo,
				mi_brand.logo_image AS brand_logo,

				((SELECT COUNT(mepe_MemberPrivlegeID) 
					FROM member_privilege_trans 
					WHERE brnc_BranchID=mi_branch.branch_id
					AND member_privilege_trans.mepe_Deleted='')+
				(SELECT COUNT(meco_MemberCouponID) 
					FROM member_coupon_trans 
					WHERE brnc_BranchID=mi_branch.branch_id
					AND member_coupon_trans.meco_Deleted='')+
				(SELECT COUNT(meac_MemberActivityID) 
					FROM member_activity_trans 
					WHERE brnc_BranchID=mi_branch.branch_id
					AND member_activity_trans.meac_Deleted='')) AS total

				FROM mi_branch
				
				LEFT JOIN mi_brand
				ON mi_brand.brand_id = mi_branch.brand_id

				WHERE mi_branch.brand_id = ".$_SESSION['user_brand_id']." 
				AND mi_branch.flag_del != '1'

				GROUP BY mi_branch.branch_id 
				ORDER BY total DESC";

	$rs_branch = $oDB->Query($branch);

	if (!$rs_branch) {

		echo "An error occurred: ".mysql_error();

	} else {

		while($axRow = $rs_branch->FetchRow(DBI_ASSOC)) {

			# LOGO

			if($axRow['brand_logo']!=''){

				$axRow['brand_logo'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" height="60" width="60" />';

			} else {

				$axRow['brand_logo'] = '<img src="../../images/400x400.png" class="image_border" height="60" width="60" />';	
			}


			# STATUS

			if($axRow['flag_status']=='1'){ 

				$axRow['flag_status'] = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>'; 

			} else { 

				$axRow['flag_status'] = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>'; 
			}


			# PHONE

			if ($axRow['branch_phone']) {

				if ($axRow['branch_mobile']) {

					$axRow['branch_phone'] = PhoneForm($axRow['branch_phone'])."<br>".MobileForm($axRow['branch_mobile']);
				}

			} else {

				$axRow['branch_phone'] = MobileForm($axRow['branch_mobile']);
			}

		  	$data_branch .= '<tr>
						  	<td>'.$brnc_n++.'</td>
						  	<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['brand_logo'].'</a><br>
					  			<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
						  	<td>'.$axRow['branch_name'].'</td>
						  	<td>'.$axRow['branch_email'].'</td>
						  	<td>'.$axRow['branch_phone'].'</td>
						  	<td style="text-align:center">'.$axRow['flag_status'].'</td>
						  	<td style="text-align:center">'.number_format($axRow['total']).'</td>
						  	<td style="text-align:center">
						  		<span style="cursor:pointer" onclick="'."window.location.href='top_branch.php?id=".$axRow['branch_id_data']."'".'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
					  	</tr>';
		}
	}
}

/* ============= */
/*  Your Branch  */
/* ============= */

if($_SESSION['user_type_id_ses'] == 3) {

	$your = "SELECT *,
				mi_branch.branch_id AS branch_id_data,
				mi_branch.name AS branch_name,
				mi_branch.phone AS branch_phone,
				mi_branch.mobile AS branch_mobile,
				mi_branch.email AS branch_email,
				mi_brand.name AS brand_name,
				mi_brand.path_logo,
				mi_brand.logo_image AS brand_logo,

				((SELECT COUNT(mepe_MemberPrivlegeID) 
					FROM member_privilege_trans 
					WHERE brnc_BranchID=mi_branch.branch_id
					AND member_privilege_trans.mepe_Deleted='')+
				(SELECT COUNT(meco_MemberCouponID) 
					FROM member_coupon_trans 
					WHERE brnc_BranchID=mi_branch.branch_id
					AND member_coupon_trans.meco_Deleted='')+
				(SELECT COUNT(meac_MemberActivityID) 
					FROM member_activity_trans 
					WHERE brnc_BranchID=mi_branch.branch_id
					AND member_activity_trans.meac_Deleted='')) AS total

				FROM mi_branch
				
				LEFT JOIN mi_brand
				ON mi_brand.brand_id = mi_branch.brand_id

				WHERE mi_branch.branch_id = ".$_SESSION['user_branch_id']."

				GROUP BY mi_branch.branch_id 
				ORDER BY total DESC";

	$rs_your = $oDB->Query($your);


	if (!$rs_your) {

		echo "An error occurred: ".mysql_error();

	} else {

		while($axRow = $rs_your->FetchRow(DBI_ASSOC)) {

			# LOGO

			if($axRow['brand_logo']!=''){

				$axRow['brand_logo'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" height="60" width="60" />';

			} else {

				$axRow['brand_logo'] = '<img src="../../images/400x400.png" class="image_border" height="60" width="60" />';		
			}


			# STATUS

			if($axRow['flag_status']=='1'){ 

				$axRow['flag_status'] = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>'; 

			} else { 

				$axRow['flag_status'] = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>'; 
			}


			# PHONE

			if ($axRow['branch_phone']) {

				if ($axRow['branch_mobile']) {

					$axRow['branch_phone'] = PhoneForm($axRow['branch_phone'])."<br>".MobileForm($axRow['branch_mobile']);
				}

			} else {

				$axRow['branch_phone'] = MobileForm($axRow['branch_mobile']);
			}

		  	$data_your .= '<tr>
						  	<td>'.$your_n++.'</td>
						  	<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['brand_logo'].'</a><br>
					  			<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
						  	<td>'.$axRow['branch_name'].'</td>
						  	<td>'.$axRow['branch_email'].'</td>
						  	<td>'.$axRow['branch_phone'].'</td>
						  	<td style="text-align:center">'.$axRow['flag_status'].'</td>
						  	<td style="text-align:center">'.number_format($axRow['total']).'</td>
						  	<td style="text-align:center">
						  		<span style="cursor:pointer" onclick="'."window.location.href='your_branch.php?id=".$axRow['branch_id_data']."'".'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
					  	</tr>';
		}
	}
}

/* ============= */
/*   Top Point   */
/* ============= */

	$where_point = '';

	if ($filter_point == "All") { $where_point = ''; } 
	else if ($filter_point == "Active" || !$filter_point) { 

		$where_point = ' AND (SELECT SUM(IFNULL(member_motivation_point_trans.memp_PointQty,0)) AS sum_point FROM member_motivation_point_trans WHERE member_motivation_point_trans.mere_MemberRegisterID=mb_member_register.member_register_id 
			AND member_motivation_point_trans.memp_Status="Active"
			AND member_motivation_point_trans.memp_Deleted=""'; 

		if ($_SESSION['user_type_id_ses'] == 3) {

			$where_point .= " AND member_motivation_point_trans.brnc_BranchID='".$_SESSION['user_branch_id']."'";
		}

		$where_point .= ") != 0";

	} else { 

		$where_point = ' AND (SELECT SUM(IFNULL(member_motivation_point_trans.memp_PointQty,0)) AS sum_point FROM member_motivation_point_trans WHERE member_motivation_point_trans.mere_MemberRegisterID=mb_member_register.member_register_id 
			AND member_motivation_point_trans.memp_Status="Active"
			AND member_motivation_point_trans.memp_Deleted=""'; 

		if ($_SESSION['user_type_id_ses'] == 3) {

			$where_point .= " AND member_motivation_point_trans.brnc_BranchID='".$_SESSION['user_branch_id']."'";
		}

		$where_point .= ") IS NULL";
	}

	$point = "SELECT 
				mb_member.firstname, 
				mb_member.lastname, 
				mb_member.facebook_id, 
				mb_member.facebook_name, 
				mb_member.member_id, 
				mb_member.member_image,
				mb_member.email,
				mb_member.mobile,
				mi_brand.logo_image AS brand_logo,
				mi_brand.path_logo,
				mi_brand.brand_id,
				mi_brand.name AS brand_name,

				(SELECT SUM(IFNULL(member_motivation_point_trans.memp_PointQty,0)) 
					FROM member_motivation_point_trans 
					WHERE member_motivation_point_trans.mere_MemberRegisterID=mb_member_register.member_register_id 
					AND member_motivation_point_trans.memp_Status='Active'
					AND member_motivation_point_trans.memp_Deleted=''";

	if ($_SESSION['user_type_id_ses'] == 3) {

			$point .= " AND member_motivation_point_trans.brnc_BranchID='".$_SESSION['user_branch_id']."'";
	}

	$point .= ") AS point_total,

				(SELECT SUM(IFNULL(reward_redeem_trans.rera_RewardQty_Point,0)) FROM reward_redeem_trans WHERE reward_redeem_trans.mere_MemberRegisterID=mb_member_register.member_register_id AND reward_redeem_trans.rede_AutoRedeem='F'";

	if ($_SESSION['user_type_id_ses'] == 3) {

			$point .= " AND reward_redeem_trans.brnc_BranchID='".$_SESSION['user_branch_id']."'";
	}

	$point .= ") AS point_use

				FROM mb_member_register 

				LEFT JOIN mb_member
				ON mb_member.member_id = mb_member_register.member_id

				LEFT JOIN mi_brand
				ON mi_brand.brand_id = mb_member_register.bran_BrandID";


	if($_SESSION['user_type_id_ses'] > 1) {

		$data_register = "";

		$i = 1;

		$register = $oDB->Query($member_register);

		while($axRow_register = $register->FetchRow(DBI_ASSOC)) {

			if ($i == $count_regis) { $data_register .= $axRow_register['member_id']; } 
			else { $data_register .= $axRow_register['member_id'].","; }

			$i++;
		}

		if ($data_register) {

			$point .= " WHERE mb_member.member_id IN (".$data_register.")
						AND mb_member_register.bran_BrandID = ".$_SESSION['user_brand_id']." ";
		}

	} else {

		$sql_count = "SELECT count(DISTINCT member_id)
						FROM mb_member_register
						LEFT JOIN mi_card
						ON mb_member_register.card_id = mi_card.card_id";

		$count_regis = $oDB->QueryOne($sql_count);

		$member_register = "SELECT DISTINCT member_id 
							FROM mb_member_register
							LEFT JOIN mi_card
							ON mb_member_register.card_id = mi_card.card_id";

		$data_register = "";

		$i = 1;

		$register = $oDB->Query($member_register);

		while($axRow_register = $register->FetchRow(DBI_ASSOC)) {

			if ($i == $count_regis) {

				$data_register .= $axRow_register['member_id'];

			} else {

				$data_register .= $axRow_register['member_id'].",";
			}

			$i++;
		}

		if ($data_register) {

			$point .= " WHERE mb_member.member_id IN (".$data_register.")";
		}
	}

	$point .= $where_point;

	$point .= " GROUP BY mb_member_register.member_register_id, mb_member_register.bran_BrandID
				ORDER BY point_total DESC";	



	$rs_point = $oDB->Query($point);

	if (!$rs_point) {

		echo "An error occurred: ".mysql_error();

	} else {

		while($axRow = $rs_point->FetchRow(DBI_ASSOC)) {

		# MEMBER

			if($axRow['member_image']!='' && $axRow['member_image']!='user.png') {

				$axRow['member_image'] = '<img class="img-circle image_border" src="'.$path_upload_member.$axRow['member_image'].'"width="60" height="60"/>';

			} else if ($axRow['facebook_id']!='') {
				
				$axRow['member_image'] = '<img class="img-circle image_border" src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=large" width="60" height="60" />';
			} else {

				$axRow['member_image'] = '<img src="../../images/user.png" width="60" height="60" class="img-circle image_border" />';
			}

			# MEMBER BRAND ID

			$sql_brand = 'SELECT member_brand_code
							FROM mb_member_register
							WHERE bran_BrandID="'.$axRow['brand_id'].'"
							AND member_id="'.$axRow['member_id'].'"';
			$brand_code = $oDB->QueryOne($sql_brand);

			$member_name = '';

			if ($axRow['firstname'].' '.$axRow['lastname']) {

				if ($axRow['email']) {

					if ($axRow['mobile']) {

						if ($brand_code) {
							
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'].'<br>Member Brand : '.$brand_code;

						} else {
							
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'];
						}

					} else {

						if ($brand_code) {
							
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>Member Brand : '.$brand_code;

						} else {
							
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'];
						}
					}

				} else {

					if ($axRow['mobile']) {

						if ($brand_code) {
							
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile'].'<br>Member Brand : '.$brand_code;

						} else {
							
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile'];
						}
					
					} else { 

						if ($brand_code) {
							
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>Member Brand : '.$brand_code;

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


		# LOGO

			if($axRow['brand_logo']!=''){

				$axRow['brand_logo'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" height="60" width="60" />';

			} else {

				$axRow['brand_logo'] = '<img src="../../images/400x400.png" class="image_border" height="60" width="60" />';	
			}


		  	$data_point .= '<tr>
						  	<td>'.$point_n++.'</td>
						  	<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['brand_logo'].'</a><br>
						  		<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
						  	<td style="text-align:center">'.$axRow['member_image'].'</td>
						  	<td>'.$member_name.'</td>
						  	<td style="text-align:center">'.number_format($axRow['point_total']).'<br><br>
						  	<a href="point_total.php?member='.$axRow['member_id'].'&brand='.$axRow['brand_id'].'">
						  	<button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></a></td>
						  	<td style="text-align:center">'.number_format($axRow['point_use']).'<br><br>
						  	<a href="point_use.php?member='.$axRow['member_id'].'&brand='.$axRow['brand_id'].'">
						  	<button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></a></td>
						  	<td style="text-align:center">'.number_format($axRow['point_total']-$axRow['point_use']).'</td>
					  	</tr>';
		}
	}


/* ============= */
/*   Top Stamp   */
/* ============= */

	$where_stamp = '';

	if ($filter_stamp == "All") { $where_stamp = ''; } 
	else if ($filter_stamp == "Active" || !$filter_stamp) { 

		$where_stamp = ' AND (SELECT SUM(IFNULL(member_motivation_stamp_trans.mems_StampQty,0)) FROM member_motivation_stamp_trans WHERE member_motivation_stamp_trans.mere_MemberRegisterID=mb_member_register.member_register_id
			AND member_motivation_stamp_trans.mems_Deleted=""'; 

		if ($_SESSION['user_type_id_ses'] == 3) {

			$where_stamp .= " AND member_motivation_stamp_trans.brnc_BranchID='".$_SESSION['user_branch_id']."'";
		}

		$where_stamp .= ") != 0";

	} else { 

		$where_stamp = ' AND (SELECT SUM(IFNULL(member_motivation_stamp_trans.mems_StampQty,0)) FROM member_motivation_stamp_trans WHERE member_motivation_stamp_trans.mere_MemberRegisterID=mb_member_register.member_register_id'; 

		if ($_SESSION['user_type_id_ses'] == 3) {

			$where_stamp .= " AND member_motivation_stamp_trans.brnc_BranchID='".$_SESSION['user_branch_id']."'";
		}

		$where_stamp .= ") IS NULL";
	}

	$stamp = "SELECT 
				mb_member.firstname, 
				mb_member.lastname, 
				mb_member.facebook_id, 
				mb_member.facebook_name, 
				mb_member.member_id, 
				mb_member.member_image,
				mb_member.email,
				mb_member.mobile,
				mi_brand.logo_image AS brand_logo,
				mi_brand.path_logo,
				mi_brand.brand_id,
				mi_brand.name AS brand_name,

				(SELECT SUM(IFNULL(member_motivation_stamp_trans.mems_StampQty,0)) 
					FROM member_motivation_stamp_trans 
					WHERE member_motivation_stamp_trans.mere_MemberRegisterID=mb_member_register.member_register_id
					AND member_motivation_stamp_trans.mems_Deleted=''";

	if ($_SESSION['user_type_id_ses'] == 3) {

		$stamp .= " AND member_motivation_stamp_trans.brnc_BranchID='".$_SESSION['user_branch_id']."'";
	}

	$stamp .= ") AS stamp_total,

				(SELECT SUM(IFNULL(reward_redeem_trans.rera_RewardQty_Stamp,0)) FROM reward_redeem_trans WHERE reward_redeem_trans.mere_MemberRegisterID=mb_member_register.member_register_id AND reward_redeem_trans.rede_AutoRedeem='F'";

	if ($_SESSION['user_type_id_ses'] == 3) {

		$stamp .= " AND reward_redeem_trans.brnc_BranchID='".$_SESSION['user_branch_id']."'";
	}

	$stamp .= ") AS stamp_use

				FROM mb_member_register 

				LEFT JOIN mb_member
				ON mb_member.member_id = mb_member_register.member_id

				LEFT JOIN mi_brand
				ON mi_brand.brand_id = mb_member_register.bran_BrandID";

	if($_SESSION['user_type_id_ses'] > 1) {

		$data_register = "";

		$i = 1;

		$register = $oDB->Query($member_register);

		while($axRow_register = $register->FetchRow(DBI_ASSOC)) {

			if ($i == $count_regis) { $data_register .= $axRow_register['member_id']; } 
			else { $data_register .= $axRow_register['member_id'].","; }

			$i++;
		}

		if ($data_register) {

			$stamp .= " WHERE mb_member.member_id IN (".$data_register.")
						AND mb_member_register.bran_BrandID = ".$_SESSION['user_brand_id']." ";
		}

	} else {

		$sql_count = "SELECT count(DISTINCT member_id)
						FROM mb_member_register
						LEFT JOIN mi_card
						ON mb_member_register.card_id = mi_card.card_id";

		$count_regis = $oDB->QueryOne($sql_count);

		$member_register = "SELECT DISTINCT member_id 
							FROM mb_member_register
							LEFT JOIN mi_card
							ON mb_member_register.card_id = mi_card.card_id";

		$data_register = "";

		$i = 1;

		$register = $oDB->Query($member_register);

		while($axRow_register = $register->FetchRow(DBI_ASSOC)) {

			if ($i == $count_regis) {

				$data_register .= $axRow_register['member_id'];

			} else {

				$data_register .= $axRow_register['member_id'].",";
			}

			$i++;
		}

		if ($data_register) {

			$stamp .= " WHERE mb_member.member_id IN (".$data_register.")";
		}
	}

	$stamp .= $where_stamp;

	$stamp .= " GROUP BY mb_member_register.member_register_id, mb_member_register.bran_BrandID
				ORDER BY stamp_total DESC";	


	$rs_stamp = $oDB->Query($stamp);

	if (!$rs_stamp) {

		echo "An error occurred: ".mysql_error();

	} else {

		while($axRow = $rs_stamp->FetchRow(DBI_ASSOC)) {

		# MEMBER

			if($axRow['member_image']!='' && $axRow['member_image']!='user.png') {

				$axRow['member_image'] = '<img class="img-circle image_border" src="'.$path_upload_member.$axRow['member_image'].'"width="60" height="60"/>';

			} else if ($axRow['facebook_id']!='') {
				
				$axRow['member_image'] = '<img class="img-circle image_border" src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=large" width="60" height="60" />';
			} else {

				$axRow['member_image'] = '<img src="../../images/user.png" width="60" height="60" class="img-circle image_border" />';
			}

			# MEMBER BRAND ID

			$sql_brand = 'SELECT member_brand_code
							FROM mb_member_register
							WHERE bran_BrandID="'.$axRow['brand_id'].'"
							AND member_id="'.$axRow['member_id'].'"';
			$brand_code = $oDB->QueryOne($sql_brand);

			$member_name = '';

			if ($axRow['firstname'].' '.$axRow['lastname']) {

				if ($axRow['email']) {

					if ($axRow['mobile']) {

						if ($brand_code) {
							
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'].'<br>Member Brand : '.$brand_code;

						} else {
							
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'];
						}

					} else {

						if ($brand_code) {
							
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>Member Brand : '.$brand_code;

						} else {
							
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'];
						}
					}

				} else {

					if ($axRow['mobile']) {

						if ($brand_code) {
							
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile'].'<br>Member Brand : '.$brand_code;

						} else {
							
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile'];
						}
					
					} else { 

						if ($brand_code) {
							
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>Member Brand : '.$brand_code;

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


		# LOGO

			if($axRow['brand_logo']!=''){

				$axRow['brand_logo'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" height="60" width="60" />';

			} else {

				$axRow['brand_logo'] = '<img src="../../images/400x400.png" class="image_border" height="60" width="60" />';	
			}


		  	$data_stamp .= '<tr>
						  	<td>'.$stamp_n++.'</td>
						  	<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['brand_logo'].'</a><br>
						  		<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
						  	<td style="text-align:center">'.$axRow['member_image'].'</td>
						  	<td>'.$member_name.'</td>
						  	<td style="text-align:center">'.number_format($axRow['stamp_total']).'<br><br>

						  	<a href="stamp_total.php?member='.$axRow['member_id'].'&brand='.$axRow['brand_id'].'">
						  	<button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></a>
						  	</td>
						  	<td style="text-align:center">'.number_format($axRow['stamp_use']).'<br><br>
						  	<a href="stamp_use.php?member='.$axRow['member_id'].'&brand='.$axRow['brand_id'].'">
						  	<button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></a>
						  	</td>
						  	<td style="text-align:center">'.number_format($axRow['stamp_total']-$axRow['stamp_use']).'</td>
					  	</tr>';
		}
	}


/* =============== */
/*   Top Purchase  */
/* =============== */


	# SEARCH DATE

	if ($_REQUEST['StartSales'] != '') { $StartSales = $_REQUEST['StartSales']; }
	else { $StartSales = $_SESSION['sale_start']; }

	if ($_REQUEST['EndSales'] != '') { $EndSales = $_REQUEST['EndSales']; }
	else { $EndSales = $_SESSION['sale_end']; }

	$where_date = '';

	if ($StartSales && $EndSales) {
		
		$where_date = ' AND member_motivation_point_trans.memp_CreatedDate BETWEEN "'.$StartSales.'" AND "'.$EndSales.'" ';
		$oTmp->assign('dataStartSales', $StartSales);
		$oTmp->assign('dataEndSales', $EndSales);

		$_SESSION['sale_start'] = $StartSales;
		$_SESSION['sale_end'] = $EndSales;

	} else if ($StartSales) {
		
		$where_date = ' AND member_motivation_point_trans.memp_CreatedDate >= "'.$StartSales.'" ';
		$oTmp->assign('dataStartSales', $StartSales);

		$_SESSION['sale_start'] = $StartSales;

	} else if ($EndSales) {
		
		$where_date = ' AND member_motivation_point_trans.memp_CreatedDate <= "'.$EndSales.'" ';
		$oTmp->assign('dataEndSales', $EndSales);

		$_SESSION['sale_end'] = $EndSales;

	} else {

		$where_date = '';
	}


	$where_purchase = '';

	if ($filter_purchase == "All") { $where_purchase = ''; } 
	else if ($filter_purchase == "Active" || !$filter_purchase) { 

		$where_purchase = ' AND (SELECT SUM(IFNULL(member_motivation_point_trans.memp_PointQty,0)) FROM member_motivation_point_trans WHERE member_motivation_point_trans.mere_MemberRegisterID=mb_member_register.member_register_id
			AND member_motivation_point_trans.memp_Deleted=""'; 

		if ($_SESSION['user_type_id_ses'] == 3) {

			$purchase .= " AND member_motivation_point_trans.brnc_BranchID='".$_SESSION['user_branch_id']."'";
		
		} else {

			if ($filter_branch != 'All' && $filter_branch) { 

				$purchase .= " AND member_motivation_point_trans.brnc_BranchID='".$filter_branch."'";
			}
		}

		$where_purchase .= $where_date;

		$where_purchase .= ') != 0';

	} else { 

		$where_purchase = ' AND (SELECT SUM(IFNULL(member_motivation_point_trans.memp_PointQty,0)) FROM member_motivation_point_trans WHERE member_motivation_point_trans.mere_MemberRegisterID=mb_member_register.member_register_id
			AND member_motivation_point_trans.memp_Deleted=""'; 

		if ($_SESSION['user_type_id_ses'] == 3) {

			$purchase .= " AND member_motivation_point_trans.brnc_BranchID='".$_SESSION['user_branch_id']."'";
		
		} else {

			if ($filter_branch != 'All' && $filter_branch) { 

				$purchase .= " AND member_motivation_point_trans.brnc_BranchID='".$filter_branch."'";
			}
		}

		$where_purchase .= $where_date;

		$where_purchase .= ') IS NULL';
	}

	$purchase = "SELECT 
				mb_member.firstname, 
				mb_member.lastname, 
				mb_member.facebook_id, 
				mb_member.facebook_name, 
				mb_member.member_id, 
				mb_member.member_image,
				mb_member.email,
				mb_member.mobile,
				mi_brand.logo_image AS brand_logo,
				mi_brand.path_logo,
				mi_brand.brand_id,
				mi_brand.name AS brand_name,

				(SELECT SUM(IFNULL(member_motivation_point_trans.memp_ReceiptAmount,0)) 
					FROM member_motivation_point_trans 
					WHERE member_motivation_point_trans.mere_MemberRegisterID=mb_member_register.member_register_id 
					AND member_motivation_point_trans.memp_Status='Active'
					AND member_motivation_point_trans.memp_Deleted=''

					".$where_date."";

	$purchase .= ") AS point_total,

				(SELECT COUNT(member_motivation_point_trans.memp_ReceiptAmount) FROM member_motivation_point_trans WHERE member_motivation_point_trans.mere_MemberRegisterID=mb_member_register.member_register_id";

	if ($_SESSION['user_type_id_ses'] == 3) {

		$purchase .= " AND member_motivation_point_trans.brnc_BranchID='".$_SESSION['user_branch_id']."'";

	} else {

		if ($filter_branch != 'All' && $filter_branch) { 

			$purchase .= " AND member_motivation_point_trans.brnc_BranchID='".$filter_branch."'";
		}
	}

	$purchase .= $where_date;

	$purchase .= ") AS use_total

				FROM mb_member_register 

				LEFT JOIN mb_member
				ON mb_member.member_id = mb_member_register.member_id

				LEFT JOIN mi_brand
				ON mi_brand.brand_id = mb_member_register.bran_BrandID";


	if($_SESSION['user_type_id_ses'] > 1) {

		$data_register = "";

		$i = 1;

		$register = $oDB->Query($member_register);

		while($axRow_register = $register->FetchRow(DBI_ASSOC)) {

			if ($i == $count_regis) { $data_register .= $axRow_register['member_id']; } 
			else { $data_register .= $axRow_register['member_id'].","; }

			$i++;
		}

		if ($data_register) {

			$purchase .= " WHERE mb_member.member_id IN (".$data_register.")
						AND mb_member_register.bran_BrandID = ".$_SESSION['user_brand_id']." ";
		}

	} else {

		$sql_count = "SELECT count(DISTINCT member_id)
						FROM mb_member_register
						LEFT JOIN mi_card
						ON mb_member_register.card_id = mi_card.card_id";

		$count_regis = $oDB->QueryOne($sql_count);

		$member_register = "SELECT DISTINCT member_id 
							FROM mb_member_register
							LEFT JOIN mi_card
							ON mb_member_register.card_id = mi_card.card_id";

		$data_register = "";

		$i = 1;

		$register = $oDB->Query($member_register);

		while($axRow_register = $register->FetchRow(DBI_ASSOC)) {

			if ($i == $count_regis) {

				$data_register .= $axRow_register['member_id'];

			} else {

				$data_register .= $axRow_register['member_id'].",";
			}

			$i++;
		}

		if ($data_register) {

			$purchase .= " WHERE mb_member.member_id IN (".$data_register.")";
		}
	}

	$purchase .= $where_purchase;

	$purchase .= " GROUP BY mb_member_register.member_register_id, mb_member_register.bran_BrandID
				ORDER BY point_total DESC";	



	$rs_purchase = $oDB->Query($purchase);

	if (!$rs_purchase) {

		echo "An error occurred: ".mysql_error();

	} else {

		while($axRow = $rs_purchase->FetchRow(DBI_ASSOC)) {

		# MEMBER

			if($axRow['member_image']!='' && $axRow['member_image']!='user.png') {

				$axRow['member_image'] = '<img class="img-circle image_border" src="'.$path_upload_member.$axRow['member_image'].'"width="60" height="60"/>';

			} else if ($axRow['facebook_id']!='') {
				
				$axRow['member_image'] = '<img class="img-circle image_border" src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=large" width="60" height="60" />';
			} else {

				$axRow['member_image'] = '<img src="../../images/user.png" width="60" height="60" class="img-circle image_border" />';
			}

			# MEMBER BRAND ID

			$sql_brand = 'SELECT member_brand_code
							FROM mb_member_register
							WHERE bran_BrandID="'.$axRow['brand_id'].'"
							AND member_id="'.$axRow['member_id'].'"';
			$brand_code = $oDB->QueryOne($sql_brand);

			$member_name = '';

			if ($axRow['firstname'].' '.$axRow['lastname']) {

				if ($axRow['email']) {

					if ($axRow['mobile']) {

						if ($brand_code) {
							
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'].'<br>Member Brand : '.$brand_code;

						} else {
							
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'];
						}

					} else {

						if ($brand_code) {
							
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>Member Brand : '.$brand_code;

						} else {
							
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'];
						}
					}

				} else {

					if ($axRow['mobile']) {

						if ($brand_code) {
							
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile'].'<br>Member Brand : '.$brand_code;

						} else {
							
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile'];
						}
					
					} else { 

						if ($brand_code) {
							
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>Member Brand : '.$brand_code;

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


		# LOGO

			if($axRow['brand_logo']!=''){

				$axRow['brand_logo'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" height="60" width="60" />';

			} else {

				$axRow['brand_logo'] = '<img src="../../images/400x400.png" class="image_border" height="60" width="60" />';	
			}



		  	$data_purchase .= '<tr>
						  	<td>'.$purchase_n++.'</td>
						  	<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['brand_logo'].'</a><br>
						  		<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
						  	<td style="text-align:center">'.$axRow['member_image'].'</td>
						  	<td>'.$member_name.'</td>
						  	<td style="text-align:center">'.number_format($axRow['use_total']).'</td>
						  	<td style="text-align:center">'.number_format($axRow['point_total'],2).' ฿</td>
						  	<td style="text-align:center"><a href="top_purchase.php?member='.$axRow['member_id'].'&brand='.$axRow['brand_id'].'">
						  	<button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></a></td>
					  	</tr>';
		}
	}


/* ============= */
/*   Top Redeem  */
/* ============= */

	# SEARCH

	$where_redeem = '';

	if ($filter_redeem == "All") { $where_redeem = ''; }

	else if ($filter_redeem == "Active" || !$filter_redeem) { 

		$where_redeem = ' AND (SELECT COUNT(reward_redeem_trans.rede_RewardRedeemID) 
					FROM reward_redeem_trans ';

		if ($_SESSION['user_type_id_ses']==2) {

			$where_redeem .= ' WHERE reward_redeem_trans.rede_RewardRedeemID=reward_redeem.rede_RewardRedeemID
						AND reward_redeem.bran_BrandID="'.$_SESSION['user_brand_id'].'"';
			
		} else if ($_SESSION['user_type_id_ses']==3) {

			$where_redeem .= ' WHERE reward_redeem_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
							AND reward_redeem_trans.rede_RewardRedeemID=reward_redeem.rede_RewardRedeemID';
		} else {

			$where_redeem .= ' WHERE reward_redeem_trans.rede_RewardRedeemID=reward_redeem.rede_RewardRedeemID';
		}

		$where_redeem .= ") != 0 ";

	} else { 

		$where_redeem = ' AND (SELECT COUNT(reward_redeem_trans.rede_RewardRedeemID) 
					FROM reward_redeem_trans ';

		if ($_SESSION['user_type_id_ses']==2) {

			$where_redeem .= ' WHERE reward_redeem_trans.rede_RewardRedeemID=reward_redeem.rede_RewardRedeemID
						AND reward_redeem.bran_BrandID="'.$_SESSION['user_brand_id'].'"';
			
		} else if ($_SESSION['user_type_id_ses']==3) {

			$where_redeem .= ' WHERE reward_redeem_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
							AND reward_redeem_trans.rede_RewardRedeemID=reward_redeem.rede_RewardRedeemID';
		} else {

			$where_redeem .= ' WHERE reward_redeem_trans.rede_RewardRedeemID=reward_redeem.rede_RewardRedeemID';
		}

		$where_redeem .= ") = 0 ";
	}



	$redeem = "SELECT 
				reward.rewa_Name AS reward_name,
				reward.rewa_Image AS reward_image,
				reward.rewa_ImagePath,
				reward.rewa_Type AS reward_type,
				reward.rewa_Status AS reward_status,
				mi_brand.name AS brand_name,
				mi_brand.logo_image AS brand_logo,
				mi_brand.path_logo,
				reward_redeem.rede_Name AS redeem_name,
				reward_redeem.rede_RewardRedeemID AS redeem_id,
				mi_tg_activity.activity_name as category_name,


				(SELECT COUNT(reward_redeem_trans.rede_RewardRedeemID) 
					FROM reward_redeem_trans ";

	if ($_SESSION['user_type_id_ses']==2) {

		$redeem .= ' WHERE reward_redeem_trans.rede_RewardRedeemID=reward_redeem.rede_RewardRedeemID
					AND reward_redeem.bran_BrandID="'.$_SESSION['user_brand_id'].'"';
		
	} else if ($_SESSION['user_type_id_ses']==3) {

		$redeem .= ' WHERE reward_redeem_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
						AND reward_redeem_trans.rede_RewardRedeemID=reward_redeem.rede_RewardRedeemID';
	} else {

		$redeem .= ' WHERE reward_redeem_trans.rede_RewardRedeemID=reward_redeem.rede_RewardRedeemID';
	}

	$redeem .= ") AS total

				FROM reward_redeem

  				LEFT JOIN reward
    			ON reward_redeem.rewa_RewardID = reward.rewa_RewardID
				LEFT JOIN mi_brand
				ON reward_redeem.bran_BrandID = mi_brand.brand_id 
				LEFT JOIN mi_tg_activity
				ON mi_tg_activity.id_activity = reward.rewa_Category

				WHERE reward_redeem.rede_Deleted!='T' "

				.$where_redeem;

	if($_SESSION['user_type_id_ses'] > 1) {

		$redeem .= " AND reward_redeem.bran_BrandID =".$_SESSION['user_brand_id']." ";	

	}

	$redeem .= " GROUP BY reward_redeem.rede_RewardRedeemID
				ORDER BY total DESC";



$rs_redeem = $oDB->Query($redeem);

$data_redeem = "";

if (!$rs_redeem) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_redeem->FetchRow(DBI_ASSOC)) {

		# LOGO

		if($axRow['brand_logo']!=''){

			$axRow['brand_logo'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" height="60" width="60" />';

		} else {

			$axRow['brand_logo'] = '<img src="../../images/400x400.png" class="image_border" height="60" width="60" />';	
		}


		# REWARD

		if($axRow['reward_image']!=''){

			$axRow['reward_image'] = '<img src="../../upload/'.$axRow['rewa_ImagePath'].$axRow['reward_image'].'" width="60" height="60" class="image_border"/>';

		} else {

			$axRow['reward_image'] = '<img src="../../images/400x400.png" width="60" height="60" class="image_border"/>';	
		}


		# STATUS

		if($axRow['reward_status']=="Active"){ 

			$axRow['reward_status'] = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>'; 

		} else { 

			$axRow['reward_status'] = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>'; 
		}


		# TABLE

	  	$data_redeem .= '<tr>
					  	<td>'.$redeem_n++.'</td>
					  	<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['brand_logo'].'</a><br>
					  		<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
					  	<td style="text-align:center"><a href="../reward/reward.php">'.$axRow['reward_image'].'</a></td>
					  	<td>'.$axRow['redeem_name'].'</td>
					  	<td>'.$axRow['category_name'].'</td>
					  	<td style="text-align:center">'.$axRow['reward_status'].'</td>
					  	<td style="text-align:center">'.number_format($axRow['total']).'</td>
					  	<td style="text-align:center">
					  		<span style="cursor:pointer" onclick="'."window.location.href='top_redeem.php?id=".$axRow['redeem_id']."'".'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
	  				</tr>';
	}
}


/* ============= */
/*   Top Rating  */
/* ============= */

	# SEARCH

	$where_like = ''; 

	if ($filter_like == "All") { 

		$where_like = '';

	} else if ($filter_like == "Active" || !$filter_like) { 

		$where_like = " WHERE avg_rating!=0";

	} else { 

		$where_like = " WHERE avg_rating=0";
	}

	// $where_like = ''; 

	// if ($filter_like == "All") { 

	// 	$where_like = '';

	// } else if ($filter_like == "Active" || !$filter_like) { 

	// 	$where_like = " WHERE total_like!=0";

	// } else { 

	// 	$where_like = " WHERE total_like=0";
	// }



	// $like = "SELECT *
	// 			FROM (SELECT 
	// 			privilege.priv_PrivilegeID AS id, 
	// 			privilege.priv_Name AS name,
 //                'Privilege' AS type,
 //                privilege.priv_Image AS image,
 //                privilege.priv_ImageNew AS image_new,
 //                privilege.priv_ImagePath AS path_image,
 //                privilege.priv_Status AS status,
 //                mi_brand.name AS brand_name,
 //                mi_brand.logo_image AS brand_logo,
 //                mi_brand.path_logo,
	// 			(SELECT DISTINCT COUNT(member_privilege_trans.meth_MemberTransactionHID)
	// 			FROM member_privilege_trans 
	// 			LEFT JOIN member_transaction_h 
	// 			ON member_privilege_trans.meth_MemberTransactionHID= member_transaction_h.meth_MemberTransactionHID
	// 			WHERE member_privilege_trans.priv_PrivilegeID= privilege.priv_PrivilegeID 
	// 			AND member_transaction_h.meth_Like='T'";

	// if ($_SESSION['user_type_id_ses']==3) {

	// 	$like .= ' AND privilege.brnc_BranchID='.$_SESSION['user_branch_id'].'';
	// }			

	// $like .= " 	GROUP BY privilege.priv_PrivilegeID) AS total_like,
	// 			(SELECT COUNT(member_privilege_trans.mepe_MemberPrivlegeID) 
	// 				FROM member_privilege_trans 
	// 				WHERE member_privilege_trans.priv_PrivilegeID=privilege.priv_PrivilegeID
	// 				AND member_privilege_trans.mepe_Deleted=''";

	// if ($_SESSION['user_type_id_ses']==3) {

	// 	$like .= ' AND member_privilege_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';
	// }

	// $like .= ") AS total_use

	// 			FROM privilege
	// 			LEFT JOIN mi_brand
	// 			ON privilege.bran_BrandID = mi_brand.brand_id 

	// 			WHERE privilege.priv_Deleted != 'T'";

	// if($_SESSION['user_type_id_ses'] == 2) {

	// 	$like .= " AND privilege.bran_BrandID = ".$_SESSION['user_brand_id']." ";
	// }

	// $like .= " GROUP BY privilege.priv_PrivilegeID

	// 		UNION 

	// 		SELECT 
	// 			coupon.coup_CouponID AS id, 
	// 			coupon.coup_Name AS name,
 //                'Coupon' AS type,
 //                coupon.coup_Image AS image,
 //                coupon.coup_ImageNew AS image_new,
 //                coupon.coup_ImagePath AS path_image,
 //                coupon.coup_Status AS status,
 //                mi_brand.name AS brand_name,
 //                mi_brand.logo_image AS brand_logo,
 //                mi_brand.path_logo,
	// 			(SELECT DISTINCT COUNT(member_coupon_trans.meth_MemberTransactionHID)
	// 			FROM member_coupon_trans 
	// 			LEFT JOIN member_transaction_h 
	// 			ON member_coupon_trans.meth_MemberTransactionHID= member_transaction_h.meth_MemberTransactionHID
	// 			WHERE member_coupon_trans.coup_CouponID= coupon.coup_CouponID 
	// 			AND member_transaction_h.meth_Like='T'";

	// if ($_SESSION['user_type_id_ses']==3) {

	// 	$like .= ' AND member_coupon_trans.brnc_BranchID='.$_SESSION['user_branch_id'].'';
	// }			

	// $like .= " 	GROUP BY member_coupon_trans.coup_CouponID) AS total_like,
	// 			(SELECT COUNT(member_coupon_trans.meco_MemberCouponID) 
	// 				FROM member_coupon_trans 
	// 				WHERE member_coupon_trans.coup_CouponID=coupon.coup_CouponID
	// 				AND member_coupon_trans.meco_Deleted=''";

	// if ($_SESSION['user_type_id_ses']==3) {

	// 	$like .= ' AND member_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';
	// }

	// $like .= ") AS total_use

	// 			FROM coupon
	// 			LEFT JOIN mi_brand
	// 			ON coupon.bran_BrandID = mi_brand.brand_id 

	// 			WHERE coupon.coup_Deleted != 'T'
	// 			AND coupon.coup_Birthday = ''";

	// if($_SESSION['user_type_id_ses'] == 2) {

	// 	$like .= " AND coupon.bran_BrandID = ".$_SESSION['user_brand_id']." ";
	// }

	// $like .= " GROUP BY coupon.coup_CouponID

	// 		UNION 

	// 		SELECT 
	// 			coupon.coup_CouponID AS id, 
	// 			coupon.coup_Name AS name,
 //                'Birthday Coupon' AS type,
 //                coupon.coup_Image AS image,
 //                coupon.coup_ImageNew AS image_new,
 //                coupon.coup_ImagePath AS path_image,
 //                coupon.coup_Status AS status,
 //                mi_brand.name AS brand_name,
 //                mi_brand.logo_image AS brand_logo,
 //                mi_brand.path_logo,
	// 			(SELECT DISTINCT COUNT(member_coupon_trans.meth_MemberTransactionHID)
	// 			FROM member_coupon_trans 
	// 			LEFT JOIN member_transaction_h 
	// 			ON member_coupon_trans.meth_MemberTransactionHID= member_transaction_h.meth_MemberTransactionHID
	// 			WHERE member_coupon_trans.coup_CouponID= coupon.coup_CouponID 
	// 			AND member_transaction_h.meth_Like='T'";

	// if ($_SESSION['user_type_id_ses']==3) {

	// 	$like .= ' AND member_coupon_trans.brnc_BranchID='.$_SESSION['user_branch_id'].'';
	// }			

	// $like .= " 	GROUP BY member_coupon_trans.coup_CouponID) AS total_like,
	// 			(SELECT COUNT(member_coupon_trans.meco_MemberCouponID) 
	// 				FROM member_coupon_trans 
	// 				WHERE member_coupon_trans.coup_CouponID=coupon.coup_CouponID
	// 				AND member_coupon_trans.meco_Deleted=''";

	// if ($_SESSION['user_type_id_ses']==3) {

	// 	$like .= ' AND member_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';
	// }

	// $like .= ") AS total_use

	// 			FROM coupon
	// 			LEFT JOIN mi_brand
	// 			ON coupon.bran_BrandID = mi_brand.brand_id 

	// 			WHERE coupon.coup_Deleted != 'T'
	// 			AND coupon.coup_Birthday = 'T'";

	// if($_SESSION['user_type_id_ses'] == 2) {

	// 	$like .= " AND coupon.bran_BrandID = ".$_SESSION['user_brand_id']." ";
	// }

	// $like .= " GROUP BY coupon.coup_CouponID

	// 		UNION 

	// 		SELECT 
	// 			activity.acti_ActivityID AS id, 
	// 			activity.acti_Name AS name,
 //                'Activity' AS type,
 //                activity.acti_Image AS image,
 //                activity.acti_ImageNew AS image_new,
 //                activity.acti_ImagePath AS path_image,
 //                activity.acti_Status AS status,
 //                mi_brand.name AS brand_name,
 //                mi_brand.logo_image AS brand_logo,
 //                mi_brand.path_logo,
	// 			(SELECT DISTINCT COUNT(member_activity_trans.meth_MemberTransactionHID)
	// 			FROM member_activity_trans 
	// 			LEFT JOIN member_transaction_h 
	// 			ON member_activity_trans.meth_MemberTransactionHID= member_transaction_h.meth_MemberTransactionHID
	// 			WHERE member_activity_trans.acti_ActivityID= activity.acti_ActivityID 
	// 			AND member_transaction_h.meth_Like='T'";

	// if ($_SESSION['user_type_id_ses']==3) {

	// 	$like .= ' AND member_activity_trans.brnc_BranchID='.$_SESSION['user_branch_id'].'';
	// }			

	// $like .= " 	GROUP BY member_activity_trans.acti_ActivityID) AS total_like,
	// 			(SELECT COUNT(member_activity_trans.meac_MemberActivityID) 
	// 				FROM member_activity_trans 
	// 				WHERE member_activity_trans.acti_ActivityID=activity.acti_ActivityID
	// 				AND member_activity_trans.meac_Deleted=''";

	// if ($_SESSION['user_type_id_ses']==3) {

	// 	$like .= ' AND member_activity_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';
	// }

	// $like .= ") AS total_use

	// 			FROM activity
	// 			LEFT JOIN mi_brand
	// 			ON activity.bran_BrandID = mi_brand.brand_id 

	// 			WHERE activity.acti_Deleted != 'T'";

	// if($_SESSION['user_type_id_ses'] == 2) {

	// 	$like .= " AND activity.bran_BrandID = ".$_SESSION['user_brand_id']." ";
	// }

	// $like .= " GROUP BY activity.acti_ActivityID

	// 		UNION 

	// 		SELECT hilight_coupon.coup_CouponID AS id, 
	// 				hilight_coupon.coup_Name AS name,
 //                    'Earn Attention' AS type,
 //                    hilight_coupon.coup_Image AS image,
 //                    hilight_coupon.coup_ImageNew AS image_new,
 //                    hilight_coupon.coup_ImagePath AS path_image,
 //                    hilight_coupon.coup_Status AS status,
 //                    mi_brand.name AS brand_name,
 //                    mi_brand.logo_image AS brand_logo,
 //                    mi_brand.path_logo,
	// 				(SELECT COUNT(hilight_coupon_trans.hico_HilightCouponID) 
	// 				FROM hilight_coupon_trans
	// 				WHERE hilight_coupon_trans.coup_CouponID=hilight_coupon.coup_CouponID
	// 				AND hilight_coupon_trans.hico_Like='Like'
	// 				AND hilight_coupon_trans.hico_Deleted=''";

	// if ($_SESSION['user_type_id_ses']==3) {

	// 	$like .= ' AND hilight_coupon_trans.brnc_BranchID='.$_SESSION['user_branch_id'].'';
	// }			

	// $like .= " 		) AS total_like,
	// 				(SELECT COUNT(hilight_coupon_trans.hico_HilightCouponID) 
	// 				FROM hilight_coupon_trans
	// 				WHERE hilight_coupon_trans.coup_CouponID=hilight_coupon.coup_CouponID
	// 				AND hilight_coupon_trans.hico_Deleted=''";

	// if ($_SESSION['user_type_id_ses']==3) {

	// 	$like .= ' AND hilight_coupon_trans.brnc_BranchID='.$_SESSION['user_branch_id'].'';
	// }			

	// $like .= " 		) AS total_use
	// 		FROM hilight_coupon
	// 		LEFT JOIN mi_brand
	// 		ON hilight_coupon.bran_BrandID=mi_brand.brand_id";

	// if ($_SESSION['user_type_id_ses']==2) {

	// 	$like .= ' WHERE hilight_coupon.bran_BrandID="'.$_SESSION['user_brand_id'].'" ';
	// } 

	// $like .= ") AS top_like

	// 		".$where_like." 

	// 		ORDER BY total_like DESC";

	$like = "SELECT *
	 			FROM (SELECT 
				privilege.priv_PrivilegeID AS id, 
				privilege.priv_Name AS name,
                'Privilege' AS type,
                privilege.priv_Image AS image,
                privilege.priv_ImageNew AS image_new,
                privilege.priv_ImagePath AS path_image,
                privilege.priv_Status AS status,
                mi_brand.name AS brand_name,
                mi_brand.logo_image AS brand_logo,
                mi_brand.path_logo,
				(SELECT avg(member_privilege_trans.mepe_Rating) 
				FROM member_privilege_trans 
				WHERE member_privilege_trans.priv_PrivilegeID=privilege.priv_PrivilegeID 
				AND member_privilege_trans.mepe_Rating>0";

	if ($_SESSION['user_type_id_ses']==3) {

		$like .= ' AND member_privilege_trans.brnc_BranchID='.$_SESSION['user_branch_id'].'';
	}			

	$like .= "	) AS avg_rating,
				(SELECT COUNT(member_privilege_trans.mepe_MemberPrivlegeID) 
					FROM member_privilege_trans 
					WHERE member_privilege_trans.priv_PrivilegeID=privilege.priv_PrivilegeID
					AND member_privilege_trans.mepe_Deleted=''";

	if ($_SESSION['user_type_id_ses']==3) {

		$like .= ' AND member_privilege_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';
	}

	$like .= ") AS total_use

				FROM privilege
				LEFT JOIN mi_brand
				ON privilege.bran_BrandID = mi_brand.brand_id 

				WHERE privilege.priv_Deleted != 'T'";

	if($_SESSION['user_type_id_ses'] == 2) {

		$like .= " AND privilege.bran_BrandID = ".$_SESSION['user_brand_id']." ";
	}

	$like .= " GROUP BY privilege.priv_PrivilegeID

			UNION 

			SELECT 
				coupon.coup_CouponID AS id, 
				coupon.coup_Name AS name,
                'Coupon' AS type,
                coupon.coup_Image AS image,
                coupon.coup_ImageNew AS image_new,
                coupon.coup_ImagePath AS path_image,
                coupon.coup_Status AS status,
                mi_brand.name AS brand_name,
                mi_brand.logo_image AS brand_logo,
                mi_brand.path_logo,
				(SELECT avg(member_coupon_trans.meco_Rating) 
				FROM member_coupon_trans 
				WHERE member_coupon_trans.coup_CouponID=coupon.coup_CouponID 
				AND member_coupon_trans.meco_Rating>0";

	if ($_SESSION['user_type_id_ses']==3) {

		$like .= ' AND member_coupon_trans.brnc_BranchID='.$_SESSION['user_branch_id'].'';
	}			

	$like .= "	) AS avg_rating,
				(SELECT COUNT(member_coupon_trans.meco_MemberCouponID) 
					FROM member_coupon_trans 
					WHERE member_coupon_trans.coup_CouponID=coupon.coup_CouponID
					AND member_coupon_trans.meco_Deleted=''";

	if ($_SESSION['user_type_id_ses']==3) {

		$like .= ' AND member_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';
	}

	$like .= ") AS total_use

				FROM coupon
				LEFT JOIN mi_brand
				ON coupon.bran_BrandID = mi_brand.brand_id 

				WHERE coupon.coup_Deleted != 'T'
				AND coupon.coup_Birthday = ''";

	if($_SESSION['user_type_id_ses'] == 2) {

		$like .= " AND coupon.bran_BrandID = ".$_SESSION['user_brand_id']." ";
	}

	$like .= " GROUP BY coupon.coup_CouponID

			UNION 

			SELECT 
				coupon.coup_CouponID AS id, 
				coupon.coup_Name AS name,
                'Birthday Coupon' AS type,
                coupon.coup_Image AS image,
                coupon.coup_ImageNew AS image_new,
                coupon.coup_ImagePath AS path_image,
                coupon.coup_Status AS status,
                mi_brand.name AS brand_name,
                mi_brand.logo_image AS brand_logo,
                mi_brand.path_logo,
				(SELECT avg(member_coupon_trans.meco_Rating) 
				FROM member_coupon_trans 
				WHERE member_coupon_trans.coup_CouponID=coupon.coup_CouponID 
				AND member_coupon_trans.meco_Rating>0";

	if ($_SESSION['user_type_id_ses']==3) {

		$like .= ' AND member_coupon_trans.brnc_BranchID='.$_SESSION['user_branch_id'].'';
	}			

	$like .= "	) AS avg_rating,
				(SELECT COUNT(member_coupon_trans.meco_MemberCouponID) 
					FROM member_coupon_trans 
					WHERE member_coupon_trans.coup_CouponID=coupon.coup_CouponID
					AND member_coupon_trans.meco_Deleted=''";

	if ($_SESSION['user_type_id_ses']==3) {

		$like .= ' AND member_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';
	}

	$like .= ") AS total_use

				FROM coupon
				LEFT JOIN mi_brand
				ON coupon.bran_BrandID = mi_brand.brand_id 

				WHERE coupon.coup_Deleted != 'T'
				AND coupon.coup_Birthday = 'T'";

	if($_SESSION['user_type_id_ses'] == 2) {

		$like .= " AND coupon.bran_BrandID = ".$_SESSION['user_brand_id']." ";
	}

	$like .= " GROUP BY coupon.coup_CouponID

			UNION 

			SELECT 
				activity.acti_ActivityID AS id, 
				activity.acti_Name AS name,
                'Activity' AS type,
                activity.acti_Image AS image,
                activity.acti_ImageNew AS image_new,
                activity.acti_ImagePath AS path_image,
                activity.acti_Status AS status,
                mi_brand.name AS brand_name,
                mi_brand.logo_image AS brand_logo,
                mi_brand.path_logo,
				(SELECT avg(member_activity_trans.meac_Rating) 
				FROM member_activity_trans 
				WHERE member_activity_trans.acti_ActivityID=activity.acti_ActivityID 
				AND member_activity_trans.meac_Rating>0";

	if ($_SESSION['user_type_id_ses']==3) {

		$like .= ' AND member_activity_trans.brnc_BranchID='.$_SESSION['user_branch_id'].'';
	}			

	$like .= "	) AS avg_rating,
				(SELECT COUNT(member_activity_trans.meac_MemberActivityID) 
					FROM member_activity_trans 
					WHERE member_activity_trans.acti_ActivityID=activity.acti_ActivityID
					AND member_activity_trans.meac_Deleted=''";

	if ($_SESSION['user_type_id_ses']==3) {

		$like .= ' AND member_activity_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';
	}

	$like .= ") AS total_use

				FROM activity
				LEFT JOIN mi_brand
				ON activity.bran_BrandID = mi_brand.brand_id 

				WHERE activity.acti_Deleted != 'T'";

	if($_SESSION['user_type_id_ses'] == 2) {

		$like .= " AND activity.bran_BrandID = ".$_SESSION['user_brand_id']." ";
	}

	$like .= " GROUP BY activity.acti_ActivityID

			UNION 

			SELECT hilight_coupon.coup_CouponID AS id, 
					hilight_coupon.coup_Name AS name,
                    'Earn Attention' AS type,
                    hilight_coupon.coup_Image AS image,
                    hilight_coupon.coup_ImageNew AS image_new,
                    hilight_coupon.coup_ImagePath AS path_image,
                    hilight_coupon.coup_Status AS status,
                    mi_brand.name AS brand_name,
                    mi_brand.logo_image AS brand_logo,
                    mi_brand.path_logo,
					(SELECT avg(hilight_coupon_trans.hico_Rating) 
					FROM hilight_coupon_trans 
					WHERE hilight_coupon_trans.coup_CouponID=hilight_coupon.coup_CouponID 
					AND hilight_coupon_trans.hico_Rating>0";

	if ($_SESSION['user_type_id_ses']==3) {

		$like .= ' 	AND hilight_coupon_trans.brnc_BranchID='.$_SESSION['user_branch_id'].'';
	}			

	$like .= "		) AS avg_rating,
					(SELECT COUNT(hilight_coupon_trans.hico_HilightCouponID) 
					FROM hilight_coupon_trans
					WHERE hilight_coupon_trans.coup_CouponID=hilight_coupon.coup_CouponID
					AND hilight_coupon_trans.hico_Deleted=''";

	if ($_SESSION['user_type_id_ses']==3) {

		$like .= ' AND hilight_coupon_trans.brnc_BranchID='.$_SESSION['user_branch_id'].'';
	}			

	$like .= " 		) AS total_use
			FROM hilight_coupon
			LEFT JOIN mi_brand
			ON hilight_coupon.bran_BrandID=mi_brand.brand_id";

	if ($_SESSION['user_type_id_ses']==2) {

		$like .= ' WHERE hilight_coupon.bran_BrandID="'.$_SESSION['user_brand_id'].'" ';
	} 

	$like .= " ) top_rating

			".$where_like." 

			ORDER BY avg_rating DESC";

$rs_like = $oDB->Query($like);

$data_like = "";

if (!$rs_like) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_like->FetchRow(DBI_ASSOC)) {

		# LOGO

		if($axRow['brand_logo']!=''){

			$axRow['brand_logo'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" height="60" width="60" />';

		} else {

			$axRow['brand_logo'] = '<img src="../../images/400x400.png" class="image_border" height="60" width="60" />';	
		}


		# PRIVILEGE

		if($axRow['image_new']!=''){

			$axRow['image'] = '<img src="../../upload/'.$axRow['path_image'].$axRow['image_new'].'" height="60" class="image_border"/>';

		} else if($axRow['image']!=''){

			$axRow['image'] = '<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'" height="60" class="image_border"/>';

		} else {

			$axRow['image'] = '<img src="../../images/card_privilege.png" height="60" class="image_border"/>';	
		}


		# STATUS

		if($axRow['status']=="Active"){ 

			$axRow['status'] = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>'; 

		} else { 

			$axRow['status'] = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>'; 
		}


		# TABLE

	  	$data_like .= '<tr>
					  	<td>'.$like_n++.'</td>
					  	<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['brand_logo'].'</a><br>
					  		<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
					  	<td style="text-align:center">';

		if ($axRow['type'] == 'Privilege') {

			$data_like .= "<a href='../privilege/privilege.php'>".$axRow['image']."</a>";

		} elseif ($axRow['type'] == 'Coupon') {

			$data_like .= "<a href='../coupon/coupon.php'>".$axRow['image']."</a>";

		} elseif ($axRow['type'] == 'Activity') {

			$data_like .= "<a href='../activity/activity.php'>".$axRow['image']."</a><";
			
		} elseif ($axRow['type'] == 'Earn Attention') {

			$sql_type = "SELECT coup_Type FROM hilight_coupon WHERE coup_CouponID='".$axRow['id']."'";
			$coup_Type = $oDB->QueryOne($sql_type);

			if ($coup_Type=='Use') {

				$data_like .= "<a href='../earn_attention/use.php'>".$axRow['image']."</a>";

			} else {

				$data_like .= "<a href='../earn_attention/buy.php'>".$axRow['image']."</a>";
			}

		} else {

			$data_like .= "<a href='../coupon/birthday.php'>".$axRow['image']."</a>";
		}


		$data_like .= '	</td>
						<td>'.$axRow['name'].'</td>
					  	<td>'.$axRow['type'].'</td>
					  	<td style="text-align:center">'.$axRow['status'].'</td>
					  	<td style="text-align:center">'.number_format($axRow['total_use']).'</td>
					  	<td style="text-align:center">'.number_format($axRow['avg_rating']).'</td>
					  	<td style="text-align:center">
					  		<span style="cursor:pointer" onclick="'."window.location.href='top_rating.php?id=".$axRow['id'].'&type='.$axRow['type']."'".'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
	  				</tr>';
	}
}


/* ============= */
/*  Top Comment  */
/* ============= */

	# SEARCH

	$where_comment = ''; 

	if ($filter_comment == "All") { 

		$where_comment = '';

	} else if ($filter_comment == "Active" || !$filter_comment) { 

		$where_comment = " WHERE total_comment!=0";

	} else { 

		$where_comment = " WHERE total_comment=0";
	}



	$comment = "SELECT *
				FROM (SELECT 
				privilege.priv_PrivilegeID AS id, 
				privilege.priv_Name AS name,
                'Privilege' AS type,
                privilege.priv_Image AS image,
                privilege.priv_ImageNew AS image_new,
                privilege.priv_ImagePath AS path_image,
                privilege.priv_Status AS status,
                mi_brand.name AS brand_name,
                mi_brand.logo_image AS brand_logo,
                mi_brand.path_logo,
				(SELECT DISTINCT COUNT(member_privilege_trans.meth_MemberTransactionHID)
				FROM member_privilege_trans 
				LEFT JOIN member_transaction_h 
				ON member_privilege_trans.meth_MemberTransactionHID= member_transaction_h.meth_MemberTransactionHID
				WHERE member_privilege_trans.priv_PrivilegeID= privilege.priv_PrivilegeID 
				AND member_transaction_h.meth_Comment!=''";

	if ($_SESSION['user_type_id_ses']==3) {

		$comment .= ' AND privilege.brnc_BranchID='.$_SESSION['user_branch_id'].'';
	}			

	$comment .= " 	GROUP BY privilege.priv_PrivilegeID) AS total_comment,
				(SELECT COUNT(member_privilege_trans.mepe_MemberPrivlegeID) 
					FROM member_privilege_trans 
					WHERE member_privilege_trans.priv_PrivilegeID=privilege.priv_PrivilegeID
					AND member_privilege_trans.mepe_Deleted=''";

	if ($_SESSION['user_type_id_ses']==3) {

		$comment .= ' AND member_privilege_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';
	}

	$comment .= ") AS total_use

				FROM privilege
				LEFT JOIN mi_brand
				ON privilege.bran_BrandID = mi_brand.brand_id 

				WHERE privilege.priv_Deleted != 'T'";

	if($_SESSION['user_type_id_ses'] == 2) {

		$comment .= " AND privilege.bran_BrandID = ".$_SESSION['user_brand_id']." ";
	}

	$comment .= " GROUP BY privilege.priv_PrivilegeID

			UNION 

			SELECT 
				coupon.coup_CouponID AS id, 
				coupon.coup_Name AS name,
                'Coupon' AS type,
                coupon.coup_Image AS image,
                coupon.coup_ImageNew AS image_new,
                coupon.coup_ImagePath AS path_image,
                coupon.coup_Status AS status,
                mi_brand.name AS brand_name,
                mi_brand.logo_image AS brand_logo,
                mi_brand.path_logo,
				(SELECT DISTINCT COUNT(member_coupon_trans.meth_MemberTransactionHID)
				FROM member_coupon_trans 
				LEFT JOIN member_transaction_h 
				ON member_coupon_trans.meth_MemberTransactionHID= member_transaction_h.meth_MemberTransactionHID
				WHERE member_coupon_trans.coup_CouponID= coupon.coup_CouponID 
				AND member_transaction_h.meth_Comment!=''";

	if ($_SESSION['user_type_id_ses']==3) {

		$comment .= ' AND member_coupon_trans.brnc_BranchID='.$_SESSION['user_branch_id'].'';
	}			

	$comment .= " 	GROUP BY member_coupon_trans.coup_CouponID) AS total_comment,
				(SELECT COUNT(member_coupon_trans.meco_MemberCouponID) 
					FROM member_coupon_trans 
					WHERE member_coupon_trans.coup_CouponID=coupon.coup_CouponID
					AND member_coupon_trans.meco_Deleted=''";

	if ($_SESSION['user_type_id_ses']==3) {

		$comment .= ' AND member_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';
	}

	$comment .= ") AS total_use

				FROM coupon
				LEFT JOIN mi_brand
				ON coupon.bran_BrandID = mi_brand.brand_id 

				WHERE coupon.coup_Deleted != 'T'
				AND coupon.coup_Birthday = ''";

	if($_SESSION['user_type_id_ses'] == 2) {

		$comment .= " AND coupon.bran_BrandID = ".$_SESSION['user_brand_id']." ";
	}

	$comment .= " GROUP BY coupon.coup_CouponID

			UNION 

			SELECT 
				coupon.coup_CouponID AS id, 
				coupon.coup_Name AS name,
                'Birthday Coupon' AS type,
                coupon.coup_Image AS image,
                coupon.coup_ImageNew AS image_new,
                coupon.coup_ImagePath AS path_image,
                coupon.coup_Status AS status,
                mi_brand.name AS brand_name,
                mi_brand.logo_image AS brand_logo,
                mi_brand.path_logo,
				(SELECT DISTINCT COUNT(member_coupon_trans.meth_MemberTransactionHID)
				FROM member_coupon_trans 
				LEFT JOIN member_transaction_h 
				ON member_coupon_trans.meth_MemberTransactionHID= member_transaction_h.meth_MemberTransactionHID
				WHERE member_coupon_trans.coup_CouponID= coupon.coup_CouponID 
				AND member_transaction_h.meth_Comment!=''";

	if ($_SESSION['user_type_id_ses']==3) {

		$comment .= ' AND member_coupon_trans.brnc_BranchID='.$_SESSION['user_branch_id'].'';
	}			

	$comment .= " 	GROUP BY member_coupon_trans.coup_CouponID) AS total_comment,
				(SELECT COUNT(member_coupon_trans.meco_MemberCouponID) 
					FROM member_coupon_trans 
					WHERE member_coupon_trans.coup_CouponID=coupon.coup_CouponID
					AND member_coupon_trans.meco_Deleted=''";

	if ($_SESSION['user_type_id_ses']==3) {

		$comment .= ' AND member_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';
	}

	$comment .= ") AS total_use

				FROM coupon
				LEFT JOIN mi_brand
				ON coupon.bran_BrandID = mi_brand.brand_id 

				WHERE coupon.coup_Deleted != 'T'
				AND coupon.coup_Birthday = 'T'";

	if($_SESSION['user_type_id_ses'] == 2) {

		$comment .= " AND coupon.bran_BrandID = ".$_SESSION['user_brand_id']." ";
	}

	$comment .= " GROUP BY coupon.coup_CouponID

			UNION 

			SELECT 
				activity.acti_ActivityID AS id, 
				activity.acti_Name AS name,
                'Activity' AS type,
                activity.acti_Image AS image,
                activity.acti_ImageNew AS image_new,
                activity.acti_ImagePath AS path_image,
                activity.acti_Status AS status,
                mi_brand.name AS brand_name,
                mi_brand.logo_image AS brand_logo,
                mi_brand.path_logo,
				(SELECT DISTINCT COUNT(member_activity_trans.meth_MemberTransactionHID)
				FROM member_activity_trans 
				LEFT JOIN member_transaction_h 
				ON member_activity_trans.meth_MemberTransactionHID=member_transaction_h.meth_MemberTransactionHID
				WHERE member_activity_trans.acti_ActivityID=activity.acti_ActivityID 
				AND member_transaction_h.meth_Comment!=''";

	if ($_SESSION['user_type_id_ses']==3) {

		$comment .= ' AND member_activity_trans.brnc_BranchID='.$_SESSION['user_branch_id'].'';
	}			

	$comment .= " 	GROUP BY member_activity_trans.acti_ActivityID) AS total_comment,
				(SELECT COUNT(member_activity_trans.meac_MemberActivityID) 
					FROM member_activity_trans 
					WHERE member_activity_trans.acti_ActivityID=activity.acti_ActivityID
					AND member_activity_trans.meac_Deleted=''";

	if ($_SESSION['user_type_id_ses']==3) {

		$comment .= ' AND member_activity_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';
	}

	$comment .= ") AS total_use

				FROM activity
				LEFT JOIN mi_brand
				ON activity.bran_BrandID = mi_brand.brand_id 

				WHERE activity.acti_Deleted != 'T'";

	if($_SESSION['user_type_id_ses'] == 2) {

		$comment .= " AND activity.bran_BrandID = ".$_SESSION['user_brand_id']." ";
	}

	$comment .= " GROUP BY activity.acti_ActivityID

			UNION 

			SELECT hilight_coupon.coup_CouponID AS id, 
					hilight_coupon.coup_Name AS name,
                    'Earn Attention' AS type,
                    hilight_coupon.coup_Image AS image,
                    hilight_coupon.coup_ImageNew AS image_new,
                    hilight_coupon.coup_ImagePath AS path_image,
                    hilight_coupon.coup_Status AS status,
                    mi_brand.name AS brand_name,
                    mi_brand.logo_image AS brand_logo,
                    mi_brand.path_logo,
					(SELECT COUNT(hilight_coupon_trans.hico_HilightCouponID) 
					FROM hilight_coupon_trans
					WHERE hilight_coupon_trans.coup_CouponID=hilight_coupon.coup_CouponID
					AND hilight_coupon_trans.hico_Comment!=''
					AND hilight_coupon_trans.hico_Deleted=''";

	if ($_SESSION['user_type_id_ses']==3) {

		$comment .= ' AND hilight_coupon_trans.brnc_BranchID='.$_SESSION['user_branch_id'].'';
	}			

	$comment .= " 		) AS total_comment,
					(SELECT COUNT(hilight_coupon_trans.hico_HilightCouponID) 
					FROM hilight_coupon_trans
					WHERE hilight_coupon_trans.coup_CouponID=hilight_coupon.coup_CouponID
					AND hilight_coupon_trans.hico_Deleted=''";

	if ($_SESSION['user_type_id_ses']==3) {

		$comment .= ' AND hilight_coupon_trans.brnc_BranchID='.$_SESSION['user_branch_id'].'';
	}			

	$comment .= " 		) AS total_use
			FROM hilight_coupon
			LEFT JOIN mi_brand
			ON hilight_coupon.bran_BrandID=mi_brand.brand_id";

	if ($_SESSION['user_type_id_ses']==2) {

		$comment .= ' WHERE hilight_coupon.bran_BrandID="'.$_SESSION['user_brand_id'].'" ';
	} 

	$comment .= ") AS top_comment

			".$where_comment." 

			ORDER BY total_comment DESC";


$rs_comment = $oDB->Query($comment);

$data_comment = "";

if (!$rs_comment) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_comment->FetchRow(DBI_ASSOC)) {

		# LOGO

		if($axRow['brand_logo']!=''){

			$axRow['brand_logo'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" height="60" width="60" />';

		} else {

			$axRow['brand_logo'] = '<img src="../../images/400x400.png" class="image_border" height="60" width="60" />';	
		}


		# PRIVILEGE

		if($axRow['image_new']!=''){

			$axRow['image'] = '<img src="../../upload/'.$axRow['path_image'].$axRow['image_new'].'" height="60" class="image_border"/>';

		} else if($axRow['image']!=''){

			$axRow['image'] = '<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'" height="60" class="image_border"/>';

		} else {

			$axRow['image'] = '<img src="../../images/card_privilege.png" height="60" class="image_border"/>';	
		}


		# STATUS

		if($axRow['status']=="Active"){ 

			$axRow['status'] = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>'; 

		} else { 

			$axRow['status'] = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>'; 
		}


		# TABLE

	  	$data_comment .= '<tr>
					  	<td>'.$comment_n++.'</td>
					  	<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['brand_logo'].'</a><br>
					  		<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
					  	<td style="text-align:center">';

		if ($axRow['type'] == 'Privilege') {

			$data_comment .= "<a href='../privilege/privilege.php'>".$axRow['image']."</a>";

		} elseif ($axRow['type'] == 'Coupon') {

			$data_comment .= "<a href='../coupon/coupon.php'>".$axRow['image']."</a>";

		} elseif ($axRow['type'] == 'Activity') {

			$data_comment .= "<a href='../activity/activity.php'>".$axRow['image']."</a>";
			
		} elseif ($axRow['type'] == 'Earn Attention') {

			$sql_type = "SELECT coup_Type FROM hilight_coupon WHERE coup_CouponID='".$axRow['id']."'";
			$coup_Type = $oDB->QueryOne($sql_type);

			if ($coup_Type=='Use') {

				$data_comment .= "<a href='../earn_attention/use.php'>".$axRow['image']."</a>";

			} else {

				$data_comment .= "<a href='../earn_attention/buy.php'>".$axRow['image']."</a>";
			}

		} else {

			$data_comment .= "<a href='../coupon/birthday.php'>".$axRow['image']."</a>";
		}

		$data_comment .='</td>
					  	<td>'.$axRow['name'].'</td>
					  	<td>'.$axRow['type'].'</td>
					  	<td style="text-align:center">'.$axRow['status'].'</td>
					  	<td style="text-align:center">'.number_format($axRow['total_use']).'</td>
					  	<td style="text-align:center">'.number_format($axRow['total_comment']).'</td>
					  	<td style="text-align:center">
					  		<span style="cursor:pointer" onclick="'."window.location.href='top_comment.php?id=".$axRow['id'].'&type='.$axRow['type']."'".'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
	  				</tr>';
	}
}

/* ====================== */
/*   Top Earn Attention   */
/* ====================== */

	# SEARCH

	$earn_status = $_REQUEST['filter_earn'];

	$where_earn = '';

	if ($earn_status == "All") { $where_earn = ''; }

	else if ($earn_status == "Active" || !$earn_status) { 

		$where_earn = " AND (SELECT COUNT(hilight_coupon_trans.hico_HilightCouponID) 
					FROM hilight_coupon_trans";

		if ($_SESSION['user_type_id_ses']==3) {

			$where_earn .= ' LEFT JOIN hilight_coupon
						ON hilight_coupon.coup_CouponID = hilight_coupon_trans.coup_CouponID
						WHERE hilight_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
						AND hilight_coupon_trans.hico_Deleted=""';
		} else {

			$where_earn .= " WHERE hilight_coupon_trans.coup_CouponID=hilight_coupon.coup_CouponID
							AND hilight_coupon_trans.hico_Deleted=''";
		}

		$where_earn .= " ) != '0'";

	}

	else { 

		$where_earn = " AND (SELECT COUNT(hilight_coupon_trans.hico_HilightCouponID) 
					FROM hilight_coupon_trans";

		if ($_SESSION['user_type_id_ses']==3) {

			$where_earn .= ' LEFT JOIN hilight_coupon
						ON hilight_coupon.coup_CouponID = hilight_coupon_trans.coup_CouponID
						WHERE hilight_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
						AND hilight_coupon_trans.hico_Deleted=""';
		} else {

			$where_earn .= " WHERE hilight_coupon_trans.coup_CouponID=hilight_coupon.coup_CouponID
							AND hilight_coupon_trans.hico_Deleted=''";

		}

		$where_earn .= " ) = '0'";
	}


		$earn = "SELECT 
				hilight_coupon.coup_Name AS privilege_name,
				hilight_coupon.coup_Image,
				hilight_coupon.coup_ImageNew,
				hilight_coupon.coup_ImagePath,
				hilight_coupon.coup_CouponID,
				hilight_coupon.coup_Status,
				hilight_coupon.coup_Type,
				mi_brand.logo_image AS brand_logo,
				mi_brand.path_logo,
				mi_brand.brand_id,
				mi_brand.name AS brand_name,

				(SELECT COUNT(hilight_coupon_trans.hico_HilightCouponID)
					FROM hilight_coupon_trans ";

	if ($_SESSION['user_type_id_ses']==3) {

		$earn .= ' LEFT JOIN hilight_coupon
					ON hilight_coupon.coup_CouponID = hilight_coupon_trans.coup_CouponID
					WHERE hilight_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
					AND hilight_coupon_trans.hico_Deleted=""';
	} else {

		$earn .= " WHERE hilight_coupon_trans.coup_CouponID=hilight_coupon.coup_CouponID
					AND hilight_coupon_trans.hico_Deleted=''";
	}

	$earn .= "		) AS total_use,

					(SELECT COUNT(hilight_coupon_buy.hcbu_HilightCouponBuyID)
					FROM hilight_coupon_buy ";

	if ($_SESSION['user_type_id_ses']==3) {

		$earn .= ' LEFT JOIN hilight_coupon
					ON hilight_coupon.coup_CouponID = hilight_coupon_buy.hico_HilightCouponID
					WHERE hilight_coupon_buy.brnc_BranchID="'.$_SESSION['user_branch_id'].'"
					AND hilight_coupon_buy.hcbu_Deleted=""';
	} else {

		$earn .= " WHERE hilight_coupon_buy.hico_HilightCouponID=hilight_coupon.coup_CouponID
					AND hilight_coupon_buy.hcbu_Deleted=''";
	}

	$earn .= "	) AS total_buy

				FROM hilight_coupon
				
				LEFT JOIN mi_brand
				ON hilight_coupon.bran_BrandID = mi_brand.brand_id 

				WHERE hilight_coupon.coup_Deleted !='T'"

				.$where_earn;


	if($_SESSION['user_type_id_ses'] == 2) {

		$earn .= " AND hilight_coupon.bran_BrandID = ".$_SESSION['user_brand_id']." ";
	}

	$data_earn = "";

	if($_SESSION['user_type_id_ses'] == 3) {

		$k = 1;

		$earn_sql = $oDB->Query($sql_earn);

		while($axRow_earn = $earn_sql->FetchRow(DBI_ASSOC)) {

			if ($k == $count_earn) { $data_earn .= $axRow_earn['coup_CouponID']; } 
			else { $data_earn .= $axRow_earn['coup_CouponID'].","; }

			$k++;
		}

		if ($data_earn) {

			$earn .= " AND hilight_coupon.coup_CouponID IN (".$data_earn.") ";
		}
	}

	$earn .= " GROUP BY hilight_coupon.coup_CouponID 
				ORDER BY (total_use+total_buy) DESC";

$rs_earn = $oDB->Query($earn);

if (!$rs_earn) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_earn->FetchRow(DBI_ASSOC)) {

		# LOGO

		if($axRow['brand_logo']!=''){

			$axRow['brand_logo'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" height="60" width="60" />';

		} else {

			$axRow['brand_logo'] = '<img src="../../images/400x400.png" class="image_border" height="60" width="60" />';	
		}


		# PRIVILEGE

		if($axRow['coup_ImageNew']!=''){

			$axRow['coup_Image'] = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_ImageNew'].'" height="60" class="image_border"/>';

		} else if($axRow['coup_Image']!='') {

			$axRow['coup_Image'] = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_Image'].'" height="60" class="image_border"/>';

		} else {

			$axRow['coup_Image'] = '<img src="../../images/card_privilege.jpg" height="60" class="image_border"/>';	
		}


		# STATUS

		if($axRow['coup_Status']=='Active'){ 

			$axRow['coup_Status'] = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>'; 

		} else { 

			$axRow['coup_Status'] = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>'; 
		}


		# TABLE

	  	$data_earn .= '<tr>
					  	<td>'.$earn_n++.'</td>
					  	<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['brand_logo'].'</a><br>
					  		<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
					  	<td style="text-align:center">';

		if ($axRow['coup_Type']=='Use') {

			$data_earn .= "<a href='../earn_attention/use.php'>".$axRow['coup_Image']."</a>";

		} else {

			$data_earn .= "<a href='../earn_attention/buy.php'>".$axRow['coup_Image']."</a>";
		}

		$data_earn .= '	</td>
					  	<td>'.$axRow['privilege_name'].'</td>
					  	<td>'.$axRow['coup_Type'].'</td>
					  	<td>'.$axRow['coup_Status'].'</td>
					  	<td style="text-align:center">'.number_format($axRow['total_use']+$axRow['total_buy']).'</td>
						<td style="text-align:center">
							<span style="cursor:pointer" onclick="'."window.location.href='top_earn.php?id=".$axRow['coup_CouponID']."'".'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
	  				</tr>';
	}
}


/* ============= */
/*    END TAP    */
/* ============= */


#  filter_like dropdownlist

$like_filter = '';

$like_filter .= '<option value="All"';
				
	if ($filter_like == "All") {	$like_filter .= ' selected';	}

$like_filter .= '>All</option>';
$like_filter .=	'<option value="Active"';
				
	if ($filter_like == "Active" || !$filter_like) {	$like_filter .= ' selected';	}

$like_filter .= '>Active</option>';
$like_filter .=	'<option value="Inactive"';
				
	if ($filter_like == "Inactive") {	$like_filter .= ' selected';	}

$like_filter .= '>Inactive</option>';

$oTmp->assign('like_filter', $like_filter);


#  filter_comment dropdownlist

$comment_filter = '';

$comment_filter .= '<option value="All"';
				
	if ($filter_comment == "All") {	$comment_filter .= ' selected';	}

$comment_filter .= '>All</option>';
$comment_filter .=	'<option value="Active"';
				
	if ($filter_comment == "Active" || !$filter_comment) {	$comment_filter .= ' selected';	}

$comment_filter .= '>Active</option>';
$comment_filter .=	'<option value="Inactive"';
				
	if ($filter_comment == "Inactive") {	$comment_filter .= ' selected';	}

$comment_filter .= '>Inactive</option>';

$oTmp->assign('comment_filter', $comment_filter);


#  filter_point dropdownlist

$point_filter = '';

$point_filter .= '<option value="All"';
				
	if ($filter_point == "All") {	$point_filter .= ' selected';	}

$point_filter .= '>All</option>';
$point_filter .=	'<option value="Active"';
				
	if ($filter_point == "Active" || !$filter_point) {	$point_filter .= ' selected';	}

$point_filter .= '>Active</option>';
$point_filter .=	'<option value="Inactive"';
				
	if ($filter_point == "Inactive") {	$point_filter .= ' selected';	}

$point_filter .= '>Inactive</option>';

$oTmp->assign('point_filter', $point_filter);


#  filter_stamp dropdownlist

$stamp_filter = '';

$stamp_filter .= '<option value="All"';
				
	if ($filter_stamp == "All") {	$stamp_filter .= ' selected';	}

$stamp_filter .= '>All</option>';
$stamp_filter .=	'<option value="Active"';
				
	if ($filter_stamp == "Active" || !$filter_stamp) {	$stamp_filter .= ' selected';	}

$stamp_filter .= '>Active</option>';
$stamp_filter .=	'<option value="Inactive"';
				
	if ($filter_stamp == "Inactive") {	$stamp_filter .= ' selected';	}

$stamp_filter .= '>Inactive</option>';

$oTmp->assign('stamp_filter', $stamp_filter);


#  filter_purchase dropdownlist

$purchase_filter = '';

$purchase_filter .= '<option value="All"';
				
	if ($filter_purchase == "All") {	$purchase_filter .= ' selected';	}

$purchase_filter .= '>All</option>';
$purchase_filter .=	'<option value="Active"';
				
	if ($filter_purchase == "Active" || !$filter_purchase) {	$purchase_filter .= ' selected';	}

$purchase_filter .= '>Active</option>';
$purchase_filter .=	'<option value="Inactive"';
				
	if ($filter_purchase == "Inactive") {	$purchase_filter .= ' selected';	}

$purchase_filter .= '>Inactive</option>';

$oTmp->assign('purchase_filter', $purchase_filter);



#  filter_branch dropdownlist

if ($_SESSION['user_type_id_ses'] == 2) {

	$branch_filter = '';

	$branch_filter .= '<option value="All"';
					
		if ($filter_branch == "All") {	$branch_filter .= ' selected';	}

	$branch_filter .= '>All</option>';

	$sql_branch_filter = 'SELECT branch_id, name 
							FROM mi_branch 
							WHERE brand_id ="'.$_SESSION['user_brand_id'].'" AND flag_del="0" 
							ORDER BY name ASC';

	$oRes = $oDB->Query($sql_branch_filter);

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$branch_filter .= '<option value="'.$axRow['branch_id'].'"';
						
			if ($filter_branch == $axRow['branch_id']) {	$branch_filter .= ' selected';	}

		$branch_filter .= '>'.$axRow['name'].'</option>';
	
	}

	$oTmp->assign('branch_filter', $branch_filter);

}


#  filter_redeem dropdownlist

$select_redeem = '';

$select_redeem .= '<option value="All"';
	if ($filter_redeem == "All") {	$select_redeem .= ' selected';	}
$select_redeem .= '>All</option>';

$select_redeem .=	'<option value="Active"';
	if ($filter_redeem == "Active" || !$filter_redeem) {	$select_redeem .= ' selected';	}
$select_redeem .= '>Active</option>';

$select_redeem .=	'<option value="Inactive"';
	if ($filter_redeem == "Inactive") {	$select_redeem .= ' selected';	}
$select_redeem .= '>Inactive</option>';

$oTmp->assign('redeem_filter', $select_redeem);


#  filter_earn dropdownlist

$select_earn = '';

$select_earn .= '<option value="All"';
	if ($filter_earn == "All") {	$select_earn .= ' selected';	}
$select_earn .= '>All</option>';

$select_earn .=	'<option value="Active"';
	if ($filter_earn == "Active" || !$filter_earn) {	$select_earn .= ' selected';	}
$select_earn .= '>Active</option>';

$select_earn .=	'<option value="Inactive"';
	if ($filter_earn == "Inactive") {	$select_earn .= ' selected';	}
$select_earn .= '>Inactive</option>';

$oTmp->assign('earn_filter', $select_earn);



$oTmp->assign('data_member', $data_member);
$oTmp->assign('data_card', $data_card);
$oTmp->assign('data_privilege', $data_privilege);
$oTmp->assign('data_coupon', $data_coupon);
$oTmp->assign('data_hbd', $data_hbd);
$oTmp->assign('data_activity', $data_activity);
$oTmp->assign('data_brand', $data_brand);
$oTmp->assign('data_branch', $data_branch);
$oTmp->assign('data_your', $data_your);
$oTmp->assign('data_point', $data_point);
$oTmp->assign('data_stamp', $data_stamp);
$oTmp->assign('data_purchase', $data_purchase);
$oTmp->assign('data_redeem', $data_redeem);
$oTmp->assign('data_like', $data_like);
$oTmp->assign('data_comment', $data_comment);
$oTmp->assign('data_earn', $data_earn);

$oTmp->assign('is_menu', 'is_analytics');
$oTmp->assign('content_file', 'analytics/top.htm');
$oTmp->display('layout/template.html');

?>

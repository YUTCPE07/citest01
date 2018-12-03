<?php

header('Content-Type:text/html; charset=UTF-8');

//========================================//

ini_set("display_errors",1);

error_reporting(1);

//========================================//

include('../../include/common.php');
include('../../include/common_check.php');
include('../../lib/function_normal.php');
require_once('../../include/connect.php');

// ====================================================


$oTmp = new TemplateEngine();
$oDB = new DBI();

if ($bDebug) {
	$oErr = new Tracker();
	$oDB->SetTracker($oErr);
}


//========================================//

if ($_SESSION['role_action']['last']['view'] != 1) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$path_upload_member = $_SESSION['path_upload_member'];

$path_upload_collection = $_SESSION['path_upload_collection'];

$path_share = $_SESSION['path_share'];


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

}

if($_SESSION['user_type_id_ses']==3){

	## PRIVILEGE ID 

	$sql_priv = "SELECT DISTINCT privilege_id 
						FROM mi_card_register
						WHERE branch_id=".$_SESSION['user_branch_id']." 
						AND mi_card_register.status=0
						AND mi_card_register.privilege_id!=''";
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
						AND mi_card_register.status=0
						AND mi_card_register.activity_id!=''";
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
$data_point = "";
$data_stamp = "";
$data_redeem = "";
$data_like = "";
$data_comment = "";
$data_earn = "";
$data_share = "";

$memb_n = "1";
$card_n = "1";
$priv_n = "1";
$coup_n = "1";
$hbd_n = "1";
$acti_n = "1";
$point_n = "1";
$stamp_n = "1";
$redeem_n = "1";
$like_n = "1";
$comment_n = "1";
$earn_n = "1";
$share_n = "1";


/* ============== */
/*   Last Member  */
/* ============== */

	$where_member = '';
	$where_hico = '';

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

			if ($j == $count_card) { $data_card_count .= $axRow_card['card_id']; } 
			else { $data_card_count .= $axRow_card['card_id'].","; }

			$j++;
		}

		if ($data_register) {

			$where_member .= " AND mb_member.member_id IN (".$data_register.")";

		} else {

			$where_member .= " AND mi_card.brand_id =".$_SESSION['user_brand_id'];
		}

		$where_hico = " AND hilight_coupon.bran_BrandID =".$_SESSION['user_brand_id'];

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

		$where_hico = "";
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

							UNION

							SELECT MAX(hilight_coupon_trans.hico_CreatedDate) AS date_use,
									mb_member.*
								FROM mb_member
								LEFT JOIN hilight_coupon_trans
								ON hilight_coupon_trans.memb_MemberID = mb_member.member_id
								LEFT JOIN hilight_coupon
								ON hilight_coupon_trans.coup_CouponID = hilight_coupon.coup_CouponID
								WHERE hilight_coupon_trans.hico_Deleted=''
								".$where_hico."
								GROUP BY mb_member.member_id

							) member_trans

							GROUP BY member_id 
							ORDER BY use_date DESC";


$rs_member = $oDB->Query($sql_member);

if (!$rs_member) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_member->FetchRow(DBI_ASSOC)) {

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
						  	<td>'.$memb_n++.'</td>
						  	<td style="text-align:center">'.$axRow['member_image'].'</td>
						  	<td>'.$member_name.'</td>
						  	<td style="text-align:center">'.$axRow['use_date'].'</td>
						  	<td style="text-align:center">
						  		<span style="cursor:pointer" onclick="'."window.location.href='last_member.php?id=".$axRow['member_id']."'".'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
	  					</tr>';
	}
}

/* ============ */
/*   Last Card  */
/* ============ */

	$where_priv = '';
	$where_coup = '';
	$where_acti = '';

	if ($_SESSION['user_type_id_ses']==2) {

		$where_priv .= ' WHERE mi_card.brand_id="'.$_SESSION['user_brand_id'].'"';
		$where_coup .= ' WHERE mi_card.brand_id="'.$_SESSION['user_brand_id'].'"';
		$where_acti .= ' WHERE mi_card.brand_id="'.$_SESSION['user_brand_id'].'"';
		
	} else if ($_SESSION['user_type_id_ses']==3) {

		$where_priv .= ' WHERE member_privilege_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';
		$where_coup .= ' WHERE member_coupon_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';
		$where_acti .= ' WHERE member_activity_trans.brnc_BranchID="'.$_SESSION['user_branch_id'].'"';

	} else {

		$where_priv = '';
		$where_coup = '';
		$where_acti = '';
	}

	$sql_card = "SELECT * FROM (
					SELECT 
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
						MAX(member_privilege_trans.mepe_CreatedDate) AS date_use

						FROM mi_card
						LEFT JOIN mi_card_type
						ON mi_card_type.card_type_id = mi_card.card_type_id
						LEFT JOIN mi_brand
						ON mi_card.brand_id = mi_brand.brand_id
						LEFT JOIN member_privilege_trans
						ON member_privilege_trans.card_CardID = mi_card.card_id
						".$where_priv."
						GROUP BY mi_card.card_id

					UNION

					SELECT 
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
						MAX(member_coupon_trans.meco_CreatedDate) AS date_use

						FROM mi_card
						LEFT JOIN mi_card_type
						ON mi_card_type.card_type_id = mi_card.card_type_id
						LEFT JOIN mi_brand
						ON mi_card.brand_id = mi_brand.brand_id
						LEFT JOIN member_coupon_trans
						ON member_coupon_trans.card_CardID = mi_card.card_id
						".$where_coup."
						GROUP BY mi_card.card_id

					UNION

					SELECT 
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
						MAX(member_activity_trans.meac_CreatedDate) AS date_use

						FROM mi_card
						LEFT JOIN mi_card_type
						ON mi_card_type.card_type_id = mi_card.card_type_id
						LEFT JOIN mi_brand
						ON mi_card.brand_id = mi_brand.brand_id
						LEFT JOIN member_activity_trans
						ON member_activity_trans.card_CardID = mi_card.card_id
						".$where_acti."
						GROUP BY mi_card.card_id

					UNION

					SELECT 
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
						MAX(member_identify.mein_CreatedDate) AS date_use

						FROM mi_card
						LEFT JOIN mi_card_type
						ON mi_card_type.card_type_id = mi_card.card_type_id
						LEFT JOIN mi_brand
						ON mi_card.brand_id = mi_brand.brand_id
						LEFT JOIN member_identify
						ON member_identify.card_CardID = mi_card.card_id
						".$where_acti."
						GROUP BY mi_card.card_id

					) member_trans

					GROUP BY card_id 
					ORDER BY date_use DESC";

$rs_card = $oDB->Query($sql_card);

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


		# DATE USE

		if (!$axRow['date_use']) {	$axRow['date_use'] = "-";	}
		else { $axRow['date_use'] = DateTime($axRow['date_use']); }


		# TABLE

	  	$data_card .= '<tr>
					  	<td>'.$card_n++.'</td>
					  	<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['brand_logo'].'</a><br>
					  		<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
					  	<td style="text-align:center"><a href="../card/card.php">'.$axRow['card_image'].'</a></td>
					  	<td>'.$axRow['card_name'].'</td>
					  	<td>'.$axRow['card_type'].'</td>
					  	<td style="padding-right:15px;text-align:right">'.$axRow['member_fee'].' ฿</td>
					  	<td>'.$axRow['card_status'].'</td>
					  	<td style="text-align:center">'.$axRow['date_use'].'</td>
					  	<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href = 
												'last_card.php?id=".$axRow['card_id']."'".'">
												<button type="button" class="btn btn-default btn-sm">
												<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
	  				</tr>';
	}
}

/* ================= */
/*  Last Privilege   */
/* ================= */

	$where_priv = '';

	if($_SESSION['user_type_id_ses'] == 2) {

		$where_priv .= " WHERE privilege.bran_BrandID = ".$_SESSION['user_brand_id']." ";
	}

	$data_priv = "";

	if($_SESSION['user_type_id_ses'] == 3) {

		$i = 1;

		$privilege = $oDB->Query($sql_priv);

		while($axRow_priv = $privilege->FetchRow(DBI_ASSOC)) {

			if ($i == $count_priv) { $data_priv .= $axRow_priv['privilege_id']; } 
			else { $data_priv .= $axRow_priv['privilege_id'].","; }
			$i++;
		}

		$where_priv .= " WHERE member_privilege_trans.brnc_BranchID = ".$_SESSION['user_branch_id']." ";

		if ($data_priv) {

			$where_priv .= " AND privilege.priv_PrivilegeID IN (".$data_priv.") ";
		}
	}


	$sql_privilege = "SELECT 
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
						MAX(member_privilege_trans.mepe_CreatedDate) AS date_use

						FROM privilege

						LEFT JOIN mi_privilege_type
						ON privilege.prty_PrivilegeTypeID = mi_privilege_type.privilege_type_id
						
						LEFT JOIN member_privilege_trans
						ON privilege.priv_PrivilegeID = member_privilege_trans.priv_PrivilegeID

						LEFT JOIN mi_brand
						ON privilege.bran_BrandID = mi_brand.brand_id

						".$where_priv."

						GROUP BY privilege.priv_PrivilegeID 

						ORDER BY date_use DESC";

$rs_priv = $oDB->Query($sql_privilege);

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


		# DATE USE

		if (!$axRow['date_use']) {	$axRow['date_use'] = "-";	}
		else { $axRow['date_use'] = DateTime($axRow['date_use']); }


		# TABLE

	  	$data_privilege .= '<tr>
						  	<td>'.$priv_n++.'</td>
						  	<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['brand_logo'].'</a><br>
						  		<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
						  	<td style="text-align:center"><a href="../privilege/privilege.php">'.$axRow['priv_Image'].'</a></td>
						  	<td>'.$axRow['privilege_name'].'</td>
						  	<td>'.$axRow['privilege_type'].'</td>
						  	<td>'.$axRow['priv_Status'].'</td>
						  	<td style="text-align:center">'.$axRow['date_use'].'</td>
							<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href='last_privilege.php?id=".$axRow['priv_PrivilegeID']."'".'">
								<button type="button" class="btn btn-default btn-sm">
								<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
	  					</tr>';
	}
}

/* ================= */
/*    Last Coupon    */
/* ================= */

	$where_coup = '';

	if($_SESSION['user_type_id_ses'] == 2) {

		$where_coup .= " AND coupon.bran_BrandID = ".$_SESSION['user_brand_id']." ";
	}

	$data_coup = "";

	if($_SESSION['user_type_id_ses'] == 3) {

		$i = 1;

		$coupon = $oDB->Query($sql_coup);

		while($axRow_coup = $coupon->FetchRow(DBI_ASSOC)) {

			if ($i == $count_coup) { $data_coup .= $axRow_coup['coupon_id']; } 
			else { $data_coup .= $axRow_coup['coupon_id'].","; }
			$i++;
		}

		$where_coup .= " AND member_coupon_trans.brnc_BranchID = ".$_SESSION['user_branch_id']." ";

		if ($data_coup) {

			$where_coup .= " AND coupon.coup_CouponID IN (".$data_coup.") ";
		}
	}

	$sql_coupon = "SELECT 
					coupon.coup_Name AS coupon_name,
					coupon.coup_Image,
					coupon.coup_ImageNew,
					coupon.coup_ImagePath,
					coupon.coup_CouponID,
					coupon.coup_Status,
					mi_privilege_type.name AS privilege_type,
					mi_brand.logo_image AS brand_logo,
					mi_brand.brand_id,
					mi_brand.path_logo,
					mi_brand.name AS brand_name,
					MAX(member_coupon_trans.meco_CreatedDate) AS date_use

					FROM coupon

					LEFT JOIN mi_privilege_type
					ON coupon.prty_PrivilegeTypeID = mi_privilege_type.privilege_type_id
					
					LEFT JOIN member_coupon_trans
					ON coupon.coup_CouponID = member_coupon_trans.coup_CouponID

					LEFT JOIN mi_brand
					ON coupon.bran_BrandID = mi_brand.brand_id

					WHERE coupon.coup_Birthday!='T'

					".$where_coup."

					GROUP BY coupon.coup_CouponID 

					ORDER BY date_use DESC";

$rs_coup = $oDB->Query($sql_coupon);

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


		# DATE USE

		if (!$axRow['date_use']) {	$axRow['date_use'] = "-";	}
		else { $axRow['date_use'] = DateTime($axRow['date_use']); }


		# TABLE

	  	$data_coupon .= '<tr>
						  	<td>'.$coup_n++.'</td>
						  	<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['brand_logo'].'</a><br><span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
						  	<td style="text-align:center"><a href="../coupon/coupon.php">'.$axRow['coup_Image'].'</a></td>
						  	<td>'.$axRow['coupon_name'].'</td>
						  	<td>'.$axRow['privilege_type'].'</td>
						  	<td>'.$axRow['coup_Status'].'</td>
						  	<td style="text-align:center">'.$axRow['date_use'].'</td>
							<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href='last_coupon.php?id=".$axRow['coup_CouponID']."'".'">
								<button type="button" class="btn btn-default btn-sm">
								<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
	  					</tr>';
	}
}

/* ================= */
/*      Last HBD     */
/* ================= */

	$where_hbd = '';

	if($_SESSION['user_type_id_ses'] == 2) {

		$where_hbd .= " AND coupon.bran_BrandID = ".$_SESSION['user_brand_id']." ";
	}

	$data_coup = "";

	if($_SESSION['user_type_id_ses'] == 3) {

		$i = 1;

		$coupon = $oDB->Query($sql_coup);

		while($axRow_coup = $coupon->FetchRow(DBI_ASSOC)) {

			if ($i == $count_coup) { $data_coup .= $axRow_coup['coupon_id']; } 
			else { $data_coup .= $axRow_coup['coupon_id'].","; }
			$i++;
		}

		$where_hbd .= " AND member_coupon_trans.brnc_BranchID = ".$_SESSION['user_branch_id']." ";

		if ($data_hbd) {

			$where_hbd .= " AND coupon.coup_CouponID IN (".$data_hbd.") ";
		}
	}


	$sql_hbd = "SELECT 
					coupon.coup_Name AS coupon_name,
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
					MAX(member_coupon_trans.meco_CreatedDate) AS date_use

					FROM coupon

					LEFT JOIN mi_privilege_type
					ON coupon.prty_PrivilegeTypeID = mi_privilege_type.privilege_type_id
					
					LEFT JOIN member_coupon_trans
					ON coupon.coup_CouponID = member_coupon_trans.coup_CouponID

					LEFT JOIN mi_brand
					ON coupon.bran_BrandID = mi_brand.brand_id

					WHERE coupon.coup_Birthday='T'
					".$where_coup."

					GROUP BY coupon.coup_CouponID 
					ORDER BY date_use DESC";

$rs_hbd = $oDB->Query($sql_hbd);

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


		# DATE USE

		if (!$axRow['date_use']) {	$axRow['date_use'] = "-";	}
		else { $axRow['date_use'] = DateTime($axRow['date_use']); }


		# TABLE

	  	$data_hbd .= '<tr>
						  	<td>'.$hbd_n++.'</td>
						  	<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['brand_logo'].'</a><br>
						  		<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
						  	<td style="text-align:center"><a href="../coupon/birthday.php">'.$axRow['coup_Image'].'</a></td>
						  	<td>'.$axRow['coupon_name'].'</td>
						  	<td>'.$axRow['privilege_type'].'</td>
						  	<td>'.$axRow['coup_Status'].'</td>
						  	<td style="text-align:center">'.$axRow['date_use'].'</td>
							<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href='last_coupon.php?id=".$axRow['coup_CouponID']."'".'">
								<button type="button" class="btn btn-default btn-sm">
								<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
	  					</tr>';
	}
}

/* ================= */
/*   Last Activity   */
/* ================= */

	$where_acti = '';

	if($_SESSION['user_type_id_ses'] == 2) {

		$where_acti .= " WHERE activity.bran_BrandID = ".$_SESSION['user_brand_id']." ";
	}

	$data_acti = "";

	if($_SESSION['user_type_id_ses'] == 3) {

		$i = 1;

		$activity = $oDB->Query($sql_acti);

		while($axRow_acti = $activity->FetchRow(DBI_ASSOC)) {

			if ($i == $count_acti) { $data_acti .= $axRow_acti['activity_id']; } 
			else { $data_acti .= $axRow_acti['activity_id'].","; }
			$i++;
		}

		$where_acti .= " WHERE member_activity_trans.brnc_BranchID = ".$_SESSION['user_branch_id']." ";

		if ($data_acti) {

			$where_acti .= " AND activity.acti_ActivityID IN (".$data_priv.") ";
		}
	}


	$sql_activity = "SELECT 
					activity.acti_Name AS activity_name,
					activity.acti_Image,
					activity.acti_ImageNew,
					activity.acti_ImagePath,
					activity.acti_ActivityID,
					activity.acti_Status,
					mi_privilege_type.name AS privilege_type,
					mi_brand.logo_image AS brand_logo,
					mi_brand.path_logo,
					mi_brand.brand_id,
					mi_brand.name AS brand_name,
					MAX(member_activity_trans.meac_CreatedDate) AS date_use

					FROM activity

					LEFT JOIN mi_privilege_type
					ON activity.prty_PrivilegeTypeID = mi_privilege_type.privilege_type_id
					
					LEFT JOIN member_activity_trans
					ON activity.acti_ActivityID = member_activity_trans.acti_ActivityID

					LEFT JOIN mi_brand
					ON activity.bran_BrandID = mi_brand.brand_id

					".$where_acti."

					GROUP BY activity.acti_ActivityID 
					ORDER BY date_use DESC";



$rs_acti = $oDB->Query($sql_activity);

if (!$rs_acti) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_acti->FetchRow(DBI_ASSOC)) {

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


		# DATE USE

		if (!$axRow['date_use']) {	$axRow['date_use'] = "-";	}
		else { $axRow['date_use'] = DateTime($axRow['date_use']); }


		# TABLE

	  	$data_activity .= '<tr>
						  	<td>'.$acti_n++.'</td>
						  	<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['brand_logo'].'</a><br>
						  		<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
						  	<td style="text-align:center"><a href="../activity/activity.php">'.$axRow['acti_Image'].'</a></td>
						  	<td>'.$axRow['activity_name'].'</td>
						  	<td>'.$axRow['privilege_type'].'</td>
						  	<td>'.$axRow['acti_Status'].'</td>
						  	<td style="text-align:center">'.$axRow['date_use'].'</td>
							<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href='last_activity.php?id=".$axRow['acti_ActivityID']."'".'">
								<button type="button" class="btn btn-default btn-sm">
								<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
	  					</tr>';
	}
}

/* ================= */
/*    Last Redeem    */
/* ================= */

	$where_redeem = '';

	if($_SESSION['user_type_id_ses'] == 2) {

		$where_redeem .= " WHERE reward_redeem.bran_BrandID = ".$_SESSION['user_brand_id']." ";
	}

	if($_SESSION['user_type_id_ses'] == 3) {

		$where_redeem .= " WHERE reward_redeem_trans.brnc_BranchID = ".$_SESSION['user_branch_id']." ";
	}


	$sql_redeem = "SELECT 
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
					MAX(reward_redeem_trans.retr_RedeemDate) AS date_use

					FROM reward_redeem

	  				LEFT JOIN reward
	    			ON reward_redeem.rewa_RewardID = reward.rewa_RewardID

	  				LEFT JOIN reward_redeem_trans
	    			ON reward_redeem.rede_RewardRedeemID = reward_redeem_trans.rede_RewardRedeemID

					LEFT JOIN mi_brand
					ON reward_redeem.bran_BrandID = mi_brand.brand_id 

					LEFT JOIN mi_tg_activity
					ON mi_tg_activity.id_activity = reward.rewa_Category

					".$where_redeem."

					GROUP BY reward_redeem.rede_RewardRedeemID 
					ORDER BY date_use DESC";

$rs_redeem = $oDB->Query($sql_redeem);

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


		# DATE USE

		if (!$axRow['date_use']) {	$axRow['date_use'] = "-";	}
		else { $axRow['date_use'] = DateTime($axRow['date_use']); }


		# TABLE

	  	$data_redeem .= '<tr>
						  	<td>'.$redeem_n++.'</td>
						  	<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['brand_logo'].'</a><br>
						  		<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
						  	<td style="text-align:center"><a href="../reward/reward.php">'.$axRow['reward_image'].'</a></td>
						  	<td>'.$axRow['redeem_name'].'</td>
						  	<td>'.$axRow['category_name'].'</td>
						  	<td style="text-align:center">'.$axRow['reward_status'].'</td>
						  	<td style="text-align:center">'.$axRow['date_use'].'</td>
							<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href='last_redeem.php?id=".$axRow['redeem_id']."'".'">
								<button type="button" class="btn btn-default btn-sm">
								<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
	  					</tr>';
	}
}

/* ================= */
/*     Last Point    */
/* ================= */

	$where_point = '';

	if($_SESSION['user_type_id_ses'] == 2) {

		$where_point .= " AND mi_brand.brand_id = ".$_SESSION['user_brand_id']." ";
	}

	if($_SESSION['user_type_id_ses'] == 3) {

		$where_point .= " AND member_motivation_point_trans.brnc_BranchID = ".$_SESSION['user_branch_id']." ";
	}


	$sql_point = "SELECT  
					mb_member.firstname, 
					mb_member.lastname, 
					mb_member.facebook_id, 
					mb_member.facebook_name, 
					mb_member.member_id, 
					mb_member.member_image,
					mb_member.email,
					mb_member.mobile,
					MAX(member_motivation_point_trans.memp_CollectedDate) AS date_collect,
					MAX(member_motivation_point_trans.memp_UpdatedDate) AS date_entry

					FROM member_motivation_point_trans

	  				LEFT JOIN mb_member_register
	    			ON mb_member_register.member_register_id = member_motivation_point_trans.mere_MemberRegisterID

	  				LEFT JOIN mb_member
	    			ON mb_member.member_id = mb_member_register.member_id

					LEFT JOIN mi_branch
					ON mi_branch.branch_id = member_motivation_point_trans.brnc_BranchID

					LEFT JOIN mi_brand
					ON mi_branch.brand_id = mi_brand.brand_id 

					WHERE member_motivation_point_trans.memp_Status = 'Active'

					".$where_point."

					GROUP BY mb_member.member_id 

					ORDER BY date_collect DESC";

$rs_point = $oDB->Query($sql_point);

if (!$rs_point) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_point->FetchRow(DBI_ASSOC)) {


		# POINT

		$data_qty = "SELECT memp_PointQty
					FROM member_motivation_point_trans
	  				WHERE memp_UpdatedDate='".$axRow['date_entry']."'";

		$point = $oDB->QueryOne($data_qty);


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

		if (!$axRow['date_entry']) {	$axRow['date_entry'] = "-";	}
		else { $axRow['date_entry'] = DateTime($axRow['date_entry']); }


		# DATE ENTRY

		if (!$axRow['date_collect']) {	$axRow['date_collect'] = "-";	}
		else { $axRow['date_collect'] = DateTime($axRow['date_collect']); }


		# TABLE

	  	$data_point .= '<tr>
						  	<td>'.$point_n++.'</td>
						  	<td style="text-align:center">'.$axRow['member_image'].'</td>
						  	<td>'.$member_name.'</td>
						  	<td style="text-align:center">'.number_format($point).'</td>
						  	<td style="text-align:center">'.$axRow['date_collect'].'</td>
						  	<td style="text-align:center">'.$axRow['date_entry'].'</td>
							<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href='last_point.php?id=".$axRow['member_id']."'".'">
								<button type="button" class="btn btn-default btn-sm">
								<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
	  					</tr>';
	}
}

/* ================= */
/*     Last Stamp    */
/* ================= */

	$where_stamp = '';

	if($_SESSION['user_type_id_ses'] == 2) {

		$where_stamp .= " WHERE mi_brand.brand_id = ".$_SESSION['user_brand_id']." ";
	}

	if($_SESSION['user_type_id_ses'] == 3) {

		$where_stamp .= " WHERE member_motivation_stamp_trans.brnc_BranchID = ".$_SESSION['user_branch_id']." ";
	}


	$sql_stamp = "SELECT  
					mb_member.firstname, 
					mb_member.lastname, 
					mb_member.facebook_id, 
					mb_member.facebook_name, 
					mb_member.member_id, 
					mb_member.member_image,
					mb_member.email,
					mb_member.mobile,
					MAX(member_motivation_stamp_trans.mems_CreatedDate) AS date_entry,
					MAX(member_motivation_stamp_trans.mems_UpdatedDate) AS date_use,
					collection_type.coty_Image AS collect_img,
					collection_type.coty_Name AS collect_name

					FROM member_motivation_stamp_trans

	  				LEFT JOIN mb_member_register
	    			ON mb_member_register.member_register_id = member_motivation_stamp_trans.mere_MemberRegisterID

	  				LEFT JOIN mb_member
	    			ON mb_member.member_id = mb_member_register.member_id

					LEFT JOIN mi_branch
					ON mi_branch.branch_id = member_motivation_stamp_trans.brnc_BranchID

					LEFT JOIN mi_brand
					ON mi_branch.brand_id = mi_brand.brand_id 

					LEFT JOIN collection_type
					ON collection_type.coty_CollectionTypeID = member_motivation_stamp_trans.coty_CollectionTypeID 

					".$where_stamp."

					GROUP BY mb_member.member_id 
					ORDER BY date_use DESC";

$rs_stamp = $oDB->Query($sql_stamp);

if (!$rs_stamp) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_stamp->FetchRow(DBI_ASSOC)) {


		# STAMP

		$data_qty = "SELECT mems_StampQty
					FROM member_motivation_stamp_trans
	  				WHERE mems_UpdatedDate='".$axRow['date_use']."'";

		$stamp = $oDB->QueryOne($data_qty);


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


		# COLLECTION IMAGE

		$collect_img = '<img src="'.$path_upload_collection.$axRow['collect_img'].'" height="30px">';


		# DATE USE

		if (!$axRow['date_use']) {	$axRow['date_use'] = "-";	}
		else { $axRow['date_use'] = DateTime($axRow['date_use']); }


		# DATE ENTRY

		if (!$axRow['date_entry']) {	$axRow['date_entry'] = "-";	}
		else { $axRow['date_entry'] = DateTime($axRow['date_entry']); }


		# TABLE

	  	$data_stamp .= '<tr>
						  	<td>'.$stamp_n++.'</td>
						  	<td style="text-align:center">'.$axRow['member_image'].'</td>
						  	<td>'.$member_name.'</td>
						  	<td style="text-align:center">'.number_format($stamp).'</td>
						  	<td style="text-align:center">'.$collect_img.'<br>
						  		<span style="font-size:11px">'.$axRow['collect_name'].'</span></td>
						  	<td style="text-align:center">'.$axRow['date_entry'].'</td>
						  	<td style="text-align:center">'.$axRow['date_use'].'</td>
							<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href='last_stamp.php?id=".$axRow['member_id']."'".'">
								<button type="button" class="btn btn-default btn-sm">
								<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
	  					</tr>';
	}
}

/* ================= */
/*     Last Like     */
/* ================= */

	$where_like = '';


	$sql_like = "SELECT *
				FROM (SELECT privilege.priv_PrivilegeID AS id, 
					privilege.priv_Name AS name,
                    'Privilege' AS type,
                    privilege.priv_Image AS image,
                    privilege.priv_ImageNew AS image_new,
                    privilege.priv_ImagePath AS path_image,
                    privilege.priv_Status AS status,
                    mi_brand.name AS brand_name,
                    mi_brand.logo_image AS brand_logo,
                    mi_brand.path_logo AS path_logo,
					MAX(member_transaction_h.meth_UpdatedDate) AS date_use
					FROM privilege 
					LEFT JOIN member_privilege_trans
					ON member_privilege_trans.priv_PrivilegeID=privilege.priv_PrivilegeID
					LEFT JOIN member_transaction_h
					ON member_transaction_h.meth_MemberTransactionHID = member_privilege_trans.meth_MemberTransactionHID
					LEFT JOIN mi_brand
					ON privilege.bran_BrandID=mi_brand.brand_id 
					WHERE member_transaction_h.meth_Like='T'";

	if ($_SESSION['user_type_id_ses']==2) {

		$sql_like .= ' AND privilege.bran_BrandID="'.$_SESSION['user_brand_id'].'" ';
	} 

	if($_SESSION['user_type_id_ses'] == 3) {

		$sql_like .= " AND member_transaction_h.brnc_BranchID = ".$_SESSION['user_branch_id']." ";
	}

	$sql_like .= " GROUP BY member_privilege_trans.priv_PrivilegeID
					
				UNION 

				SELECT coupon.coup_CouponID AS id, 
					coupon.coup_Name AS name,
                    'Coupon' AS type,
                    coupon.coup_Image AS image,
                    coupon.coup_ImageNew AS image_new,
                    coupon.coup_ImagePath AS path_image,
                    coupon.coup_Status AS status,
                    mi_brand.name AS brand_name,
                    mi_brand.logo_image AS brand_logo,
                    mi_brand.path_logo AS path_logo,
					MAX(member_transaction_h.meth_UpdatedDate) AS date_use
					FROM coupon
					LEFT JOIN member_coupon_trans
					ON member_coupon_trans.coup_CouponID=coupon.coup_CouponID
					LEFT JOIN member_transaction_h
					ON member_transaction_h.meth_MemberTransactionHID = member_coupon_trans.meth_MemberTransactionHID
					LEFT JOIN mi_brand
					ON coupon.bran_BrandID=mi_brand.brand_id
					WHERE coupon.coup_Birthday='' 
					AND member_transaction_h.meth_Like='T'";

	if ($_SESSION['user_type_id_ses']==2) {

		$sql_like .= ' AND coupon.bran_BrandID="'.$_SESSION['user_brand_id'].'" ';
	} 

	if($_SESSION['user_type_id_ses'] == 3) {

		$sql_like .= " AND member_transaction_h.brnc_BranchID = ".$_SESSION['user_branch_id']." ";
	}

	$sql_like .= " GROUP BY member_coupon_trans.coup_CouponID
					
				UNION 

				SELECT coupon.coup_CouponID AS id, 
					coupon.coup_Name AS name,
                    'Birthday Coupon' AS type,
                    coupon.coup_Image AS image,
                    coupon.coup_ImageNew AS image_new,
                    coupon.coup_ImagePath AS path_image,
                    coupon.coup_Status AS status,
                    mi_brand.name AS brand_name,
                    mi_brand.logo_image AS brand_logo,
                    mi_brand.path_logo AS path_logo,
					MAX(member_transaction_h.meth_UpdatedDate) AS date_use
					FROM coupon
					LEFT JOIN member_coupon_trans
					ON member_coupon_trans.coup_CouponID=coupon.coup_CouponID
					LEFT JOIN member_transaction_h
					ON member_transaction_h.meth_MemberTransactionHID = member_coupon_trans.meth_MemberTransactionHID
					LEFT JOIN mi_brand
					ON coupon.bran_BrandID=mi_brand.brand_id
					WHERE coupon.coup_Birthday='T' 
					AND member_transaction_h.meth_Like='T'";

	if ($_SESSION['user_type_id_ses']==2) {

		$sql_like .= ' AND coupon.bran_BrandID="'.$_SESSION['user_brand_id'].'" ';
	} 

	if($_SESSION['user_type_id_ses'] == 3) {

		$sql_like .= " AND member_transaction_h.brnc_BranchID = ".$_SESSION['user_branch_id']." ";
	}

	$sql_like .= " GROUP BY member_coupon_trans.coup_CouponID
					
				UNION 

				SELECT activity.acti_ActivityID AS id, 
					activity.acti_Name AS name,
                    'Activity' AS type,
                    activity.acti_Image AS image,
                    activity.acti_ImageNew AS image_new,
                    activity.acti_ImagePath AS path_image,
                    activity.acti_Status AS status,
                    mi_brand.name AS brand_name,
                    mi_brand.logo_image AS brand_logo,
                    mi_brand.path_logo AS path_logo,
					MAX(member_transaction_h.meth_UpdatedDate) AS date_use
					FROM activity
					LEFT JOIN member_activity_trans
					ON member_activity_trans.acti_ActivityID=activity.acti_ActivityID
					LEFT JOIN member_transaction_h
					ON member_transaction_h.meth_MemberTransactionHID = member_activity_trans.meth_MemberTransactionHID
					LEFT JOIN mi_brand
					ON activity.bran_BrandID=mi_brand.brand_id 
					WHERE member_transaction_h.meth_Like='T'";

	if ($_SESSION['user_type_id_ses']==2) {

		$sql_like .= ' AND activity.bran_BrandID="'.$_SESSION['user_brand_id'].'"';
	} 

	if($_SESSION['user_type_id_ses'] == 3) {

		$sql_like .= " AND member_transaction_h.brnc_BranchID = ".$_SESSION['user_branch_id']." ";
	}

	$sql_like .= " GROUP BY member_activity_trans.acti_ActivityID
					
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
                    mi_brand.path_logo AS path_logo,
					MAX(hilight_coupon_trans.hico_UpdatedDate) AS date_use
					FROM hilight_coupon
					LEFT JOIN hilight_coupon_trans
					ON hilight_coupon_trans.coup_CouponID=hilight_coupon.coup_CouponID
					LEFT JOIN mi_brand
					ON hilight_coupon.bran_BrandID=mi_brand.brand_id
					WHERE hilight_coupon_trans.hico_Like='Like'";

	if ($_SESSION['user_type_id_ses']==2) {

		$sql_like .= ' AND hilight_coupon.bran_BrandID="'.$_SESSION['user_brand_id'].'" ';
	} 

	if($_SESSION['user_type_id_ses'] == 3) {

		$sql_like .= " AND hilight_coupon_trans.brnc_BranchID = ".$_SESSION['user_branch_id']." ";
	}

	$sql_like .= " GROUP BY hilight_coupon_trans.coup_CouponID

					) AS last_like

					ORDER BY date_use DESC";

$rs_like = $oDB->Query($sql_like);

if (!$rs_like) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_like->FetchRow(DBI_ASSOC)) {


		# DATE USE

		if (!$axRow['date_use']) {	$axRow['date_use'] = "-";	}
		else { $axRow['date_use'] = DateTime($axRow['date_use']); }


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
						  		<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>';

		if ($axRow['type']=='Privilege') {

			$data_like .= '	<td style="text-align:center"><a href="../privilege/privilege.php">'.$axRow['image'].'</a></td>';

		} elseif ($axRow['type']=='Coupon') {

			$data_like .= '	<td style="text-align:center"><a href="../coupon/coupon.php">'.$axRow['image'].'</a></td>';

		} elseif ($axRow['type']=='Earn Attention') {

			$sql_type = 'SELECT coup_Type FROM hilight_coupon WHERE coup_CouponID="'.$axRow['id'].'"';
			$coup_Type = $oDB->Query($sql_type);

			if ($coup_Type=="Use") {

				$data_like .= '	<td style="text-align:center"><a href="../promotion/use.php">'.$axRow['image'].'</a></td>';

			} else {

				$data_like .= '	<td style="text-align:center"><a href="../promotion/buy.php">'.$axRow['image'].'</a></td>';
			}

		} elseif ($axRow['type']=='Activity') {

			$data_like .= '	<td style="text-align:center"><a href="../activity/activity.php">'.$axRow['image'].'</a></td>';

		} else {

			$data_like .= '	<td style="text-align:center"><a href="../coupon/birthday.php">'.$axRow['image'].'</a></td>';
		}

		$data_like .= '	  	<td>'.$axRow['name'].'</td>
						  	<td>'.$axRow['type'].'</td>
						  	<td style="text-align:center">'.$axRow['status'].'</td>
						  	<td style="text-align:center">'.$axRow['date_use'].'</td>
							<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href='last_like.php?id=".$axRow['id'].'&type='.$axRow['type']."'".'">
								<button type="button" class="btn btn-default btn-sm">
								<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
	  					</tr>';
	}
}

/* ================= */
/*    Last Comment   */
/* ================= */

	$where_comment = '';


	$sql_comment = "SELECT *
				FROM (SELECT privilege.priv_PrivilegeID AS id, 
					privilege.priv_Name AS name,
                    'Privilege' AS type,
                    privilege.priv_Image AS image,
                    privilege.priv_ImageNew AS image_new,
                    privilege.priv_ImagePath AS path_image,
                    privilege.priv_Status AS status,
                    mi_brand.name AS brand_name,
                    mi_brand.logo_image AS brand_logo,
                    mi_brand.path_logo AS path_logo,
					MAX(member_transaction_h.meth_UpdatedDate) AS date_use
					FROM privilege 
					LEFT JOIN member_privilege_trans
					ON member_privilege_trans.priv_PrivilegeID=privilege.priv_PrivilegeID
					LEFT JOIN member_transaction_h
					ON member_transaction_h.meth_MemberTransactionHID = member_privilege_trans.meth_MemberTransactionHID
					LEFT JOIN mi_brand
					ON privilege.bran_BrandID=mi_brand.brand_id 
					WHERE member_transaction_h.meth_Comment!=''";

	if ($_SESSION['user_type_id_ses']==2) {

		$sql_comment .= ' AND privilege.bran_BrandID="'.$_SESSION['user_brand_id'].'" ';
	} 

	if($_SESSION['user_type_id_ses'] == 3) {

		$sql_comment .= " AND member_transaction_h.brnc_BranchID = ".$_SESSION['user_branch_id']." ";
	}

	$sql_comment .= " GROUP BY member_privilege_trans.priv_PrivilegeID
					
				UNION 

				SELECT coupon.coup_CouponID AS id, 
					coupon.coup_Name AS name,
                    'Coupon' AS type,
                    coupon.coup_Image AS image,
                    coupon.coup_ImageNew AS image_new,
                    coupon.coup_ImagePath AS path_image,
                    coupon.coup_Status AS status,
                    mi_brand.name AS brand_name,
                    mi_brand.logo_image AS brand_logo,
                    mi_brand.path_logo AS path_logo,
					MAX(member_transaction_h.meth_UpdatedDate) AS date_use
					FROM coupon
					LEFT JOIN member_coupon_trans
					ON member_coupon_trans.coup_CouponID=coupon.coup_CouponID
					LEFT JOIN member_transaction_h
					ON member_transaction_h.meth_MemberTransactionHID = member_coupon_trans.meth_MemberTransactionHID
					LEFT JOIN mi_brand
					ON coupon.bran_BrandID=mi_brand.brand_id
					WHERE coupon.coup_Birthday='' 
					AND member_transaction_h.meth_Comment!=''";

	if ($_SESSION['user_type_id_ses']==2) {

		$sql_comment .= ' AND coupon.bran_BrandID="'.$_SESSION['user_brand_id'].'" ';
	} 

	if($_SESSION['user_type_id_ses'] == 3) {

		$sql_comment .= " AND member_transaction_h.brnc_BranchID = ".$_SESSION['user_branch_id']." ";
	}

	$sql_comment .= " GROUP BY member_coupon_trans.coup_CouponID
					
				UNION 

				SELECT coupon.coup_CouponID AS id, 
					coupon.coup_Name AS name,
                    'Birthday Coupon' AS type,
                    coupon.coup_Image AS image,
                    coupon.coup_ImageNew AS image_new,
                    coupon.coup_ImagePath AS path_image,
                    coupon.coup_Status AS status,
                    mi_brand.name AS brand_name,
                    mi_brand.logo_image AS brand_logo,
                    mi_brand.path_logo AS path_logo,
					MAX(member_transaction_h.meth_UpdatedDate) AS date_use
					FROM coupon
					LEFT JOIN member_coupon_trans
					ON member_coupon_trans.coup_CouponID=coupon.coup_CouponID
					LEFT JOIN member_transaction_h
					ON member_transaction_h.meth_MemberTransactionHID = member_coupon_trans.meth_MemberTransactionHID
					LEFT JOIN mi_brand
					ON coupon.bran_BrandID=mi_brand.brand_id
					WHERE coupon.coup_Birthday='T' 
					AND member_transaction_h.meth_Comment!=''";

	if ($_SESSION['user_type_id_ses']==2) {

		$sql_comment .= ' AND coupon.bran_BrandID="'.$_SESSION['user_brand_id'].'" ';
	} 

	if($_SESSION['user_type_id_ses'] == 3) {

		$sql_comment .= " AND member_transaction_h.brnc_BranchID = ".$_SESSION['user_branch_id']." ";
	}

	$sql_comment .= " GROUP BY member_coupon_trans.coup_CouponID
					
				UNION 

				SELECT activity.acti_ActivityID AS id, 
					activity.acti_Name AS name,
                    'Activity' AS type,
                    activity.acti_Image AS image,
                    activity.acti_ImageNew AS image_new,
                    activity.acti_ImagePath AS path_image,
                    activity.acti_Status AS status,
                    mi_brand.name AS brand_name,
                    mi_brand.logo_image AS brand_logo,
                    mi_brand.path_logo AS path_logo,
					MAX(member_transaction_h.meth_UpdatedDate) AS date_use
					FROM activity
					LEFT JOIN member_activity_trans
					ON member_activity_trans.acti_ActivityID=activity.acti_ActivityID
					LEFT JOIN member_transaction_h
					ON member_transaction_h.meth_MemberTransactionHID = member_activity_trans.meth_MemberTransactionHID
					LEFT JOIN mi_brand
					ON activity.bran_BrandID=mi_brand.brand_id 
					WHERE member_transaction_h.meth_Comment!=''";

	if ($_SESSION['user_type_id_ses']==2) {

		$sql_comment .= ' AND activity.bran_BrandID="'.$_SESSION['user_brand_id'].'"';
	} 

	if($_SESSION['user_type_id_ses'] == 3) {

		$sql_comment .= " AND member_transaction_h.brnc_BranchID = ".$_SESSION['user_branch_id']." ";
	}

	$sql_comment .= " GROUP BY member_activity_trans.acti_ActivityID
					
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
                    mi_brand.path_logo AS path_logo,
					MAX(hilight_coupon_trans.hico_UpdatedDate) AS date_use
					FROM hilight_coupon
					LEFT JOIN hilight_coupon_trans
					ON hilight_coupon_trans.coup_CouponID=hilight_coupon.coup_CouponID
					LEFT JOIN mi_brand
					ON hilight_coupon.bran_BrandID=mi_brand.brand_id
					WHERE hilight_coupon_trans.hico_Comment!=''";

	if ($_SESSION['user_type_id_ses']==2) {

		$sql_comment .= ' AND hilight_coupon.bran_BrandID="'.$_SESSION['user_brand_id'].'" ';
	} 

	if($_SESSION['user_type_id_ses'] == 3) {

		$sql_comment .= " AND hilight_coupon_trans.brnc_BranchID = ".$_SESSION['user_branch_id']." ";
	}

	$sql_comment .= " GROUP BY hilight_coupon_trans.coup_CouponID
					) AS last_like

					ORDER BY date_use DESC";

$rs_comment = $oDB->Query($sql_comment);

if (!$rs_comment) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_comment->FetchRow(DBI_ASSOC)) {


		# DATE USE

		if (!$axRow['date_use']) {	$axRow['date_use'] = "-";	}
		else { $axRow['date_use'] = DateTime($axRow['date_use']); }


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
						  		<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>';

		if ($axRow['type']=='Privilege') {

			$data_comment .= '	<td style="text-align:center"><a href="../privilege/privilege.php">'.$axRow['image'].'</a></td>';

		} elseif ($axRow['type']=='Coupon') {

			$data_comment .= '	<td style="text-align:center"><a href="../coupon/coupon.php">'.$axRow['image'].'</a></td>';

		} elseif ($axRow['type']=='Earn Attention') {

			$sql_type = 'SELECT coup_Type FROM hilight_coupon WHERE coup_CouponID="'.$axRow['id'].'"';
			$coup_Type = $oDB->Query($sql_type);

			if ($coup_Type=="Use") {

				$data_comment .= '	<td style="text-align:center"><a href="../promotion/use.php">'.$axRow['image'].'</a></td>';

			} else {

				$data_comment .= '	<td style="text-align:center"><a href="../promotion/buy.php">'.$axRow['image'].'</a></td>';
			}

		} elseif ($axRow['type']=='Activity') {

			$data_comment .= '	<td style="text-align:center"><a href="../activity/activity.php">'.$axRow['image'].'</a></td>';
			
		} else {

			$data_comment .= '	<td style="text-align:center"><a href="../coupon/birthday.php">'.$axRow['image'].'</a></td>';
		}

		$data_comment .= '	<td>'.$axRow['name'].'</td>
						  	<td>'.$axRow['type'].'</td>
						  	<td style="text-align:center">'.$axRow['status'].'</td>
						  	<td style="text-align:center">'.$axRow['date_use'].'</td>
							<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href='last_comment.php?id=".$axRow['id'].'&type='.$axRow['type']."'".'">
								<button type="button" class="btn btn-default btn-sm">
								<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
	  					</tr>';
	}
}

/* ===================== */
/*  Last Earn Attention  */
/* ===================== */

	$data_earn = "";

	$sql_earn = "SELECT * FROM (
					SELECT 
						hilight_coupon.coup_Name AS coupon_name,
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
						MAX(hilight_coupon_trans.hico_CreatedDate) AS date_use

						FROM hilight_coupon
						
						LEFT JOIN hilight_coupon_trans
						ON hilight_coupon.coup_CouponID = hilight_coupon_trans.coup_CouponID

						LEFT JOIN mi_brand
						ON hilight_coupon.bran_BrandID = mi_brand.brand_id";

	if($_SESSION['user_type_id_ses'] == 2) {

		$sql_earn .= " WHERE hilight_coupon.bran_BrandID = ".$_SESSION['user_brand_id']." ";
	}

	if($_SESSION['user_type_id_ses'] == 3) {

		$sql_earn .= " WHERE hilight_coupon_trans.brnc_BranchID = ".$_SESSION['user_branch_id']." ";
	}

	$sql_earn .= "		GROUP BY hilight_coupon.coup_CouponID

					UNION

					SELECT 
						hilight_coupon.coup_Name AS coupon_name,
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
						MAX(hilight_coupon_buy.hcbu_CreatedDate) AS date_use

						FROM hilight_coupon
						
						LEFT JOIN hilight_coupon_buy
						ON hilight_coupon.coup_CouponID = hilight_coupon_buy.hico_HilightCouponID

						LEFT JOIN mi_brand
						ON hilight_coupon.bran_BrandID = mi_brand.brand_id";

	if($_SESSION['user_type_id_ses'] == 2) {

		$sql_earn .= " WHERE hilight_coupon.bran_BrandID = ".$_SESSION['user_brand_id']." ";
	}

	if($_SESSION['user_type_id_ses'] == 3) {

		$sql_earn .= " WHERE hilight_coupon_buy.brnc_BranchID = ".$_SESSION['user_branch_id']." ";
	}

	$sql_earn .= "		GROUP BY hilight_coupon.coup_CouponID

					) AS hc

					ORDER BY hc.date_use DESC";



$rs_earn = $oDB->Query($sql_earn);

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


		# DATE USE

		if (!$axRow['date_use']) { $date_use = "-"; } 
		else { $date_use = DateTime($axRow['date_use']); }


		# TABLE

	  	$data_earn .= '<tr>
						  	<td>'.$earn_n++.'</td>
						  	<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['brand_logo'].'</a><br>
						  		<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>';

		if ($axRow['coup_Type']=='Use') {
		
			$data_earn .= '	<td style="text-align:center"><a href="../promotion/use.php">'.$axRow['coup_Image'].'</td>';

		} else {
		
			$data_earn .= '	<td style="text-align:center"><a href="../promotion/buy.php">'.$axRow['coup_Image'].'</td>';
		}

		$data_earn .= '	  	<td>'.$axRow['coupon_name'].'</td>
						  	<td>'.$axRow['coup_Type'].'</td>
						  	<td>'.$axRow['coup_Status'].'</td>
						  	<td style="text-align:center">'.$date_use.'</td>
							<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href='last_earn.php?id=".$axRow['coup_CouponID']."'".'">
								<button type="button" class="btn btn-default btn-sm">
								<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></span></td>
	  					</tr>';
	}
}

/* ===================== */
/*      Last Share       */
/* ===================== */

	$where_share = '';

	if($_SESSION['user_type_id_ses'] == 2) {

		$where_share .= " WHERE b.brand_id = ".$_SESSION['user_brand_id']." ";
	}

	$data_share = "";

	if($_SESSION['user_type_id_ses'] == 3) {

		$where_share .= " WHERE mpt.brnc_BranchID = ".$_SESSION['user_branch_id']." ";
	}


	$sql_share = "SELECT spt.share_privilege_trans_image AS image_privilege,
						spt.share_privilege_trans_CreatedDate AS date_share,
						spt.share_privilege_trans_type AS type,
						spt.share_privilege_trans_id AS id,
						p.priv_Name,
						b.name AS brand_name,
						b.logo_image AS brand_logo,
						b.path_logo,
						m.firstname,
						m.lastname,
						m.member_image,
						m.mobile,
						m.email,
						m.facebook_id
					FROM share_privilege_trans spt
					INNER JOIN member_privilege_trans mpt
					ON spt.share_privilege_trans_id = mpt.mepe_MemberPrivlegeID
					INNER JOIN privilege p
					ON p.priv_PrivilegeID = mpt.priv_PrivilegeID
					INNER JOIN mi_brand b
					ON p.bran_BrandID = b.brand_id
					INNER JOIN mb_member m
					ON mpt.memb_MemberID = m.member_id
					".$where_share."

					UNION SELECT spt.share_privilege_trans_image AS image_privilege,
						spt.share_privilege_trans_CreatedDate AS date_share,
						spt.share_privilege_trans_type AS type,
						spt.share_privilege_trans_id AS id,
						p.coup_Name,
						b.name AS brand_name,
						b.logo_image AS brand_logo,
						b.path_logo,
						m.firstname,
						m.lastname,
						m.member_image,
						m.mobile,
						m.email,
						m.facebook_id
					FROM share_privilege_trans spt
					INNER JOIN member_coupon_trans mpt
					ON spt.share_privilege_trans_id = mpt.meco_MemberCouponID
					INNER JOIN coupon p
					ON p.coup_CouponID = mpt.coup_CouponID
					INNER JOIN mi_brand b
					ON p.bran_BrandID = b.brand_id
					INNER JOIN mb_member m
					ON mpt.memb_MemberID = m.member_id
					".$where_share."

					UNION SELECT spt.share_privilege_trans_image AS image_privilege,
						spt.share_privilege_trans_CreatedDate AS date_share,
						spt.share_privilege_trans_type AS type,
						spt.share_privilege_trans_id AS id,
						p.acti_Name,
						b.name AS brand_name,
						b.logo_image AS brand_logo,
						b.path_logo,
						m.firstname,
						m.lastname,
						m.member_image,
						m.mobile,
						m.email,
						m.facebook_id
					FROM share_privilege_trans spt
					INNER JOIN member_activity_trans mpt
					ON spt.share_privilege_trans_id = mpt.meac_MemberActivityID
					INNER JOIN activity p
					ON p.acti_ActivityID = mpt.acti_ActivityID
					INNER JOIN mi_brand b
					ON p.bran_BrandID = b.brand_id
					INNER JOIN mb_member m
					ON mpt.memb_MemberID = m.member_id
					".$where_share."

					UNION SELECT spt.share_privilege_trans_image AS image_privilege,
						spt.share_privilege_trans_CreatedDate AS date_share,
						spt.share_privilege_trans_type AS type,
						spt.share_privilege_trans_id AS id,
						p.coup_Name,
						b.name AS brand_name,
						b.logo_image AS brand_logo,
						b.path_logo,
						m.firstname,
						m.lastname,
						m.member_image,
						m.mobile,
						m.email,
						m.facebook_id
					FROM share_privilege_trans spt
					INNER JOIN hilight_coupon_trans mpt
					ON spt.share_privilege_trans_id = mpt.hico_HilightCouponID
					INNER JOIN hilight_coupon p
					ON p.coup_CouponID = mpt.coup_CouponID
					INNER JOIN mi_brand b
					ON p.bran_BrandID = b.brand_id
					INNER JOIN mb_member m
					ON mpt.memb_MemberID = m.member_id
					".$where_share."

					ORDER BY date_share DESC";

$rs_share = $oDB->Query($sql_share);

if (!$rs_share) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_share->FetchRow(DBI_ASSOC)) {

		# LOGO

		if($axRow['brand_logo']!=''){

			$axRow['brand_logo'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" height="60" width="60" />';

		} else {

			$axRow['brand_logo'] = '<img src="../../images/400x400.png" class="image_border" height="60" width="60" />';	
		}


		# MEMBER

		if($axRow['member_image']!='' && $axRow['member_image']!='user.png') {

			$axRow['member_image'] = '<img class="img-circle image_border" src="'.$path_upload_member.$axRow['member_image'].'" width="60" height="60"/>';

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


		# PRIVILEGE

		if($axRow['image_privilege']!=''){

			$privilege_image = '<img src="'.$path_share.$axRow['image_privilege'].'" height="60" class="image_border"/>';

			$privilege_view = '<img src="'.$path_share.$axRow['image_privilege'].'" width="476" height="562" class="image_border"/>';
		}


		# TYPE

		if ($axRow['type']=='privilege') { $axRow['type'] = 'Privilege'; }

		if ($axRow['type']=='coupon') { $axRow['type'] = 'Coupon'; }

		if ($axRow['type']=='activity') { $axRow['type'] = 'Activity'; }

		if ($axRow['type']=='hicoupon') { $axRow['type'] = 'Earn Attention'; }

		if ($axRow['type']=='birthday') { $axRow['type'] = 'Birthday Coupon'; }


		# VIEW

		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['id'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>

				<div class="modal fade" id="View'.$axRow['id'].'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>'.$axRow['id'].'</b></span>
						        <hr>
						        <center>
						        	'.$privilege_view.'<br><br>
						        </center>
						    </div>
						</div>
					</div>
				</div>';


		# TABLE

	  	$data_share .= '<tr>
						  	<td>'.$share_n++.'</td>
						  	<td style="text-align:center">'.$axRow['id'].'</td>
						  	<td style="text-align:center">'.$axRow['member_image'].'</td>
						  	<td>'.$member_name.'</td>
						  	<td style="text-align:center">'.$privilege_image.'</td>
						  	<td style="text-align:center">'.$axRow['type'].'</td>
						  	<td style="text-align:center">'.DateTime($axRow['date_share']).'</td>
							<td style="text-align:center">'.$view.'</td>
	  					</tr>';
	}
}

/* ============= */
/*    END TAP    */
/* ============= */

$pathupload = $_SESSION['path_upload_member'];
$usertype = $_SESSION['user_type_id_ses'];
$brandid = $_SESSION['user_brand_id'];
$branchid = $_SESSION['user_branch_id'];

$oTmp->assign('pathupload', $pathupload);
$oTmp->assign('usertype', $usertype);
$oTmp->assign('brandid', $brandid);
$oTmp->assign('branchid', $branchid);

$oTmp->assign('data_member', $data_member);
$oTmp->assign('data_card', $data_card);
$oTmp->assign('data_privilege', $data_privilege);
$oTmp->assign('data_coupon', $data_coupon);
$oTmp->assign('data_hbd', $data_hbd);
$oTmp->assign('data_activity', $data_activity);
$oTmp->assign('data_point', $data_point);
$oTmp->assign('data_stamp', $data_stamp);
$oTmp->assign('data_redeem', $data_redeem);
$oTmp->assign('data_like', $data_like);
$oTmp->assign('data_comment', $data_comment);
$oTmp->assign('data_earn', $data_earn);
$oTmp->assign('data_share', $data_share);

$oTmp->assign('is_menu', 'is_analytics');
$oTmp->assign('content_file', 'analytics/last.htm');
$oTmp->display('layout/template.html');

?>

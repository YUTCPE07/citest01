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

if ($_SESSION['role_action']['member_share']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$path_upload_member = $_SESSION['path_upload_member'];

$where_brand = "";

$where_hbd = "";

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND mi_brand.brand_id = "'.$_SESSION['user_brand_id'].'"';
}


$data_privilege = "";
$data_coupon = "";
$data_hbd = "";
$data_activity = "";
$data_card = "";
$data_app = "";


$priv_n = "1";
$coup_n = "1";
$hbd_n = "1";
$acti_n = "1";
$card_n = "1";
$app_n = "1";



/* ============== */
/*    PRIVILEGE   */
/* ============== */

$sql_privilege ='SELECT

					privilege.priv_Name AS privilege_name,
					privilege.priv_Image,
					privilege.priv_ImageNew,
					privilege.priv_ImagePath,
					mi_brand.name AS brand_name,
					mi_brand.logo_image,
					mi_brand.path_logo,
					mb_member.firstname,
					mb_member.lastname,
					mb_member.facebook_name,
					mb_member.email AS member_email,
					mb_member.mobile AS member_mobile,
					mb_member.member_image AS member_image,
					mb_member.facebook_id AS facebook_id,
					member_share_privilege.shpr_ShareType AS share_type,
					member_share_privilege.shpr_Platform AS platform,
					member_share_privilege.shpr_CreatedDate

					FROM member_share_privilege

					LEFT JOIN mb_member
					ON  member_share_privilege.shpr_MemberID = mb_member.member_id

					LEFT JOIN privilege
					ON member_share_privilege.shpr_PrivilegeID = privilege.priv_PrivilegeID

					LEFT JOIN mi_brand
					ON privilege.bran_BrandID = mi_brand.brand_id

					WHERE member_share_privilege.shpr_PrivilegeID!="0"

					'.$where_brand.'

					ORDER BY member_share_privilege.shpr_CreatedDate DESC';

$rs_priv = $oDB->Query($sql_privilege);

if (!$rs_priv) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_priv->FetchRow(DBI_ASSOC)) {

		# MEMBER

		if($axRow['member_image']!='' && $axRow['member_image']!='user.png'){

			$axRow['member_image'] = '<img src="'.$path_upload_member.$axRow['member_image'].'" width="60" height="60" class="img-circle image_border"/>';	

		} else if ($axRow['facebook_id']!='') {

			$axRow['member_image'] = '<img src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=large" width="60" height="60" class="img-circle image_border"/>';

		} else {

			$axRow['member_image'] = '<img src="../../images/user.png" width="60" height="60" class="img-circle image_border"/>';
		}

		$member_name = '';

		if ($axRow['firstname'] || $axRow['lastname']) {

			if ($axRow['member_email']) {

				if ($axRow['member_mobile']) {
								
					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>'.$axRow['member_mobile'];

				} else { $member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email']; }

			} else {

				if ($axRow['member_mobile']) {
								
					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_mobile'];

				} else { $member_name = $axRow['firstname'].' '.$axRow['lastname']; }
			}

		} else {

			if ($axRow['member_email']) {

				if ($axRow['member_mobile']) { $member_name = $axRow['member_email'].'<br>'.$axRow['member_mobile'];

				} else { $member_name = $axRow['member_email']; }

			} else {

				if ($axRow['mobile']) { $member_name = $axRow['member_mobile'];

				} else { $member_name = ''; }
			}
		}


		# LOGO

		if($axRow['logo_image']!=''){

			$axRow['logo_image'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';

		} else {

			$axRow['logo_image'] = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';
		}


		# PRIVILEGE IMAGE

		if($axRow['priv_ImageNew']!=''){

			$priv_image = '<img src="../../upload/'.$axRow['priv_ImagePath'].$axRow['priv_ImageNew'].'" class="image_border" height="60"/>';

		} else if($axRow['priv_Image']!=''){

			$priv_image = '<img src="../../upload/'.$axRow['priv_ImagePath'].$axRow['priv_Image'].'" class="image_border" height="60"/>';

		} else {

			$priv_image = '<img src="../../images/card_privilege.jpg" height="60"/>';
		}



		# TABLE

		$data_privilege .= '<tr>
								<td>'.$priv_n++.'</td>
								<td style="text-align:center">'.$axRow['member_image'].'</td>
								<td>'.$member_name.'</td>
								<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['logo_image'].'</a><br>
									<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
								<td style="text-align:center"><a href="../privilege/privilege.php">'.$priv_image.'</a><br>
									<span style="font-size:11px">'.$axRow['privilege_name'].'</span></td>
								<td >'.$axRow['share_type'].'</td>
								<td >'.$axRow['platform'].'</td>
								<td>'.DateTime($axRow['shpr_CreatedDate']).'</td>
							</tr>' ;
		}
	}


$oTmp->assign('data_privilege', $data_privilege);


/* ============== */
/*      COUPON    */
/* ============== */

$sql_coupon ='SELECT

					coupon.coup_Name AS coupon_name,
					coupon.coup_Image,
					coupon.coup_ImageNew,
					coupon.coup_ImagePath,
					mi_brand.name AS brand_name,
					mi_brand.logo_image,
					mi_brand.path_logo,
					mb_member.firstname,
					mb_member.lastname,
					mb_member.facebook_name,
					mb_member.email AS member_email,
					mb_member.mobile AS member_mobile,
					mb_member.member_image AS member_image,
					mb_member.facebook_id AS facebook_id,
					member_share_privilege.shpr_ShareType AS share_type,
					member_share_privilege.shpr_Platform AS platform,
					member_share_privilege.shpr_CreatedDate

					FROM member_share_privilege

					LEFT JOIN mb_member
					ON  member_share_privilege.shpr_MemberID = mb_member.member_id

					LEFT JOIN coupon
					ON member_share_privilege.shpr_PrivilegeID = coupon.coup_CouponID

					LEFT JOIN mi_brand
					ON coupon.bran_BrandID = mi_brand.brand_id

					WHERE member_share_privilege.shpr_CouponID!="0"
					AND coupon.coup_Birthday!="T"

					'.$where_brand.'

					ORDER BY member_share_privilege.shpr_CreatedDate DESC';

$rs_coup = $oDB->Query($sql_coupon);

if (!$rs_coup) {

	echo "An error occurred: ".mysql_error();

} else {


	while($axRow = $rs_coup->FetchRow(DBI_ASSOC)) {

		# MEMBER

		if($axRow['member_image']!='' && $axRow['member_image']!='user.png'){

			$axRow['member_image'] = '<img src="'.$path_upload_member.$axRow['member_image'].'" width="60" height="60" class="img-circle image_border"/>';	

		} else if ($axRow['facebook_id']!='') {

			$axRow['member_image'] = '<img src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=large" width="60" height="60" class="img-circle image_border"/>';

		} else {

			$axRow['member_image'] = '<img src="../../images/user.png" width="60" height="60" class="img-circle image_border"/>';
		}

		$member_name = '';

		if ($axRow['firstname'] || $axRow['lastname']) {

			if ($axRow['member_email']) {

				if ($axRow['member_mobile']) {
								
					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>'.$axRow['member_mobile'];

				} else { $member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email']; }

			} else {

				if ($axRow['member_mobile']) {
								
					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_mobile'];

				} else { $member_name = $axRow['firstname'].' '.$axRow['lastname']; }
			}

		} else {

			if ($axRow['member_email']) {

				if ($axRow['member_mobile']) { $member_name = $axRow['member_email'].'<br>'.$axRow['member_mobile'];

				} else { $member_name = $axRow['member_email']; }

			} else {

				if ($axRow['mobile']) { $member_name = $axRow['member_mobile'];

				} else { $member_name = ''; }
			}
		}


		# LOGO

		if($axRow['logo_image']!=''){

			$axRow['logo_image'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';

		} else {

			$axRow['logo_image'] = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';
		}



		# PRIVILEGE IMAGE

		if($axRow['coup_ImageNew']!=''){

			$coup_image = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_ImageNew'].'" class="image_border" height="60"/>';

		} else if($axRow['coup_Image']!=''){

			$coup_image = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_Image'].'" class="image_border" height="60"/>';

		} else {

			$coup_image = '<img src="../../images/card_privilege.jpg" height="60"/>';
		}



		# FACEBOOK

		if (!$axRow['facebook_name']) { $axRow['facebook_name'] = '-'; }




		# TABLE

		$data_coupon .= '<tr>
							<td>'.$coup_n++.'</td>
							<td style="text-align:center">'.$axRow['member_image'].'</td>
							<td>'.$member_name.'</td>
							<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['logo_image'].'</a><br>
								<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
							<td style="text-align:center"><a href="../coupon/coupon.php">'.$coup_image.'</a><br>
								<span style="font-size:11px">'.$axRow['coupon_name'].'</span></td>
							<td >'.$axRow['share_type'].'</td>
							<td >'.$axRow['platform'].'</td>
							<td>'.DateTime($axRow['shpr_CreatedDate']).'</td>
							</tr>' ;
		}
	}

$oTmp->assign('data_coupon', $data_coupon);


/* ============== */
/*       HBD      */
/* ============== */


$sql_hbd ='SELECT

					coupon.coup_Name AS coupon_name,
					coupon.coup_Image,
					coupon.coup_ImageNew,
					coupon.coup_ImagePath,
					mi_brand.name AS brand_name,
					mi_brand.logo_image,
					mi_brand.path_logo,
					mb_member.firstname,
					mb_member.lastname,
					mb_member.facebook_name,
					mb_member.email AS member_email,
					mb_member.mobile AS member_mobile,
					mb_member.member_image AS member_image,
					mb_member.facebook_id AS facebook_id,
					member_share_privilege.shpr_ShareType AS share_type,
					member_share_privilege.shpr_Platform AS platform,
					member_share_privilege.shpr_CreatedDate

					FROM member_share_privilege

					LEFT JOIN mb_member
					ON  member_share_privilege.shpr_MemberID = mb_member.member_id

					LEFT JOIN coupon
					ON member_share_privilege.shpr_PrivilegeID = coupon.coup_CouponID

					LEFT JOIN mi_brand
					ON coupon.bran_BrandID = mi_brand.brand_id

					WHERE member_share_privilege.shpr_CouponID!="0"
					AND coupon.coup_Birthday="T"

					'.$where_brand.'

					ORDER BY member_share_privilege.shpr_CreatedDate DESC';


$rs_hbd = $oDB->Query($sql_hbd);

if (!$rs_hbd) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_hbd->FetchRow(DBI_ASSOC)) {

		# MEMBER

		if($axRow['member_image']!='' && $axRow['member_image']!='user.png'){

			$axRow['member_image'] = '<img src="'.$path_upload_member.$axRow['member_image'].'" width="60" height="60" class="img-circle image_border"/>';	

		} else if ($axRow['facebook_id']!='') {

			$axRow['member_image'] = '<img src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=large" width="60" height="60" class="img-circle image_border"/>';

		} else {

			$axRow['member_image'] = '<img src="../../images/user.png" width="60" height="60" class="img-circle image_border"/>';
		}

		$member_name = '';

		if ($axRow['firstname'] || $axRow['lastname']) {

			if ($axRow['member_email']) {

				if ($axRow['member_mobile']) {
								
					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>'.$axRow['member_mobile'];

				} else { $member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email']; }

			} else {

				if ($axRow['member_mobile']) {
								
					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_mobile'];

				} else { $member_name = $axRow['firstname'].' '.$axRow['lastname']; }
			}

		} else {

			if ($axRow['member_email']) {

				if ($axRow['member_mobile']) { $member_name = $axRow['member_email'].'<br>'.$axRow['member_mobile'];

				} else { $member_name = $axRow['member_email']; }

			} else {

				if ($axRow['mobile']) { $member_name = $axRow['member_mobile'];

				} else { $member_name = ''; }
			}
		}


		# LOGO

		if($axRow['logo_image']!=''){

			$axRow['logo_image'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';

		} else {

			$axRow['logo_image'] = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';
		}



		# PRIVILEGE IMAGE

		if($axRow['coup_ImageNew']!=''){

			$coup_image = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_ImageNew'].'" class="image_border" height="60"/>';

		} else if($axRow['coup_Image']!=''){

			$coup_image = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_Image'].'" class="image_border" height="60"/>';

		} else {

			$coup_image = '<img src="../../images/card_privilege.jpg" height="60"/>';
		}



		# TABLE

		$data_hbd .= '<tr>
							<td>'.$hbd_n++.'</td>
							<td style="text-align:center">'.$axRow['member_image'].'</td>
							<td>'.$member_name.'</td>
							<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['logo_image'].'</a><br>
								<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
							<td style="text-align:center"><a href="../coupon/birthday.php">'.$coup_image.'</a><br>
								<span style="font-size:11px">'.$axRow['coupon_name'].'</span></td>
							<td >'.$axRow['share_type'].'</td>
							<td >'.$axRow['platform'].'</td>
							<td>'.DateTime($axRow['shpr_CreatedDate']).'</td>
						</tr>' ;
		}
	}

$oTmp->assign('data_hbd', $data_hbd);


/* ============== */
/*    ACTIVITY    */
/* ============== */

$sql_activity ='SELECT

					activity.acti_Name AS activity_name,
					activity.acti_Image,
					activity.acti_ImageNew,
					activity.acti_ImagePath,
					mi_brand.name AS brand_name,
					mi_brand.logo_image,
					mi_brand.path_logo,
					mb_member.firstname,
					mb_member.lastname,
					mb_member.facebook_name,
					mb_member.email AS member_email,
					mb_member.mobile AS member_mobile,
					mb_member.member_image AS member_image,
					mb_member.facebook_id AS facebook_id,
					member_share_privilege.shpr_ShareType AS share_type,
					member_share_privilege.shpr_Platform AS platform,
					member_share_privilege.shpr_CreatedDate

					FROM member_share_privilege

					LEFT JOIN mb_member
					ON  member_share_privilege.shpr_MemberID = mb_member.member_id

					LEFT JOIN activity
					ON member_share_privilege.shpr_ActivityID = activity.acti_ActivityID

					LEFT JOIN mi_brand
					ON activity.bran_BrandID = mi_brand.brand_id

					WHERE member_share_privilege.shpr_ActivityID!="0"
					'.$where_brand.'

					ORDER BY member_share_privilege.shpr_CreatedDate DESC';

$rs_acti = $oDB->Query($sql_activity);

if (!$rs_acti) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_acti->FetchRow(DBI_ASSOC)) {

		# MEMBER

		if($axRow['member_image']!='' && $axRow['member_image']!='user.png'){

			$axRow['member_image'] = '<img src="'.$path_upload_member.$axRow['member_image'].'" width="60" height="60" class="img-circle image_border"/>';	

		} else if ($axRow['facebook_id']!='') {

			$axRow['member_image'] = '<img src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=large" width="60" height="60" class="img-circle image_border"/>';

		} else {

			$axRow['member_image'] = '<img src="../../images/user.png" width="60" height="60" class="img-circle image_border"/>';
		}

		$member_name = '';

		if ($axRow['firstname'] || $axRow['lastname']) {

			if ($axRow['member_email']) {

				if ($axRow['member_mobile']) {
								
					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>'.$axRow['member_mobile'];

				} else { $member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email']; }

			} else {

				if ($axRow['member_mobile']) {
								
					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_mobile'];

				} else { $member_name = $axRow['firstname'].' '.$axRow['lastname']; }
			}

		} else {

			if ($axRow['member_email']) {

				if ($axRow['member_mobile']) { $member_name = $axRow['member_email'].'<br>'.$axRow['member_mobile'];

				} else { $member_name = $axRow['member_email']; }

			} else {

				if ($axRow['mobile']) { $member_name = $axRow['member_mobile'];

				} else { $member_name = ''; }
			}
		}


		# LOGO

		if($axRow['logo_image']!=''){

			$axRow['logo_image'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';

		} else {

			$axRow['logo_image'] = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';
		}



		# PRIVILEGE IMAGE

		if($axRow['acti_ImageNew']!=''){

			$acti_image = '<img src="../../upload/'.$axRow['acti_ImagePath'].$axRow['acti_ImageNew'].'" class="image_border" height="60"/>';

		} else if($axRow['acti_Image']!=''){

			$acti_image = '<img src="../../upload/'.$axRow['acti_ImagePath'].$axRow['acti_Image'].'" class="image_border" height="60"/>';

		} else {

			$acti_image = '<img src="../../images/card_privilege.jpg" height="60"/>';
		}




		# TABLE

		$data_activity .= '<tr>
								<td>'.$acti_n++.'</td>
								<td style="text-align:center">'.$axRow['member_image'].'</td>
								<td>'.$member_name.'</td>
								<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['logo_image'].'</a><br>
									<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
								<td style="text-align:center"><a href="../activity/activity.php">'.$acti_image.'</a><br>
									<span style="font-size:11px">'.$axRow['activity_name'].'</span></td>
								<td >'.$axRow['share_type'].'</td>
								<td >'.$axRow['platform'].'</td>
								<td>'.DateTime($axRow['shpr_CreatedDate']).'</td>
							</tr>' ;
		}
	}

$oTmp->assign('data_activity', $data_activity);


/* ============== */
/*      CARD      */
/* ============== */

$sql_card = 'SELECT

					mi_card.name AS card_name,
					mi_card.image,
					mi_card.image_newupload,
					mi_card.path_image,
					mi_brand.name AS brand_name,
					mi_brand.logo_image,
					mi_brand.path_logo,
					mb_member.firstname,
					mb_member.lastname,
					mb_member.facebook_name,
					mb_member.email AS member_email,
					mb_member.mobile AS member_mobile,
					mb_member.member_image AS member_image,
					mb_member.facebook_id AS facebook_id,
					member_share_card.shca_ShareType AS share_type,
					member_share_card.shca_Platform AS platform,
					member_share_card.shca_CreatedDate

					FROM member_share_card

					LEFT JOIN mb_member
					ON  member_share_card.shca_MemberID = mb_member.member_id

					LEFT JOIN mi_card
					ON member_share_card.shca_CardID = mi_card.card_id

					LEFT JOIN mi_brand
					ON mi_card.brand_id = mi_brand.brand_id

					WHERE 1
					'.$where_brand.'

					ORDER BY member_share_card.shca_CreatedDate DESC';

$rs_card = $oDB->Query($sql_card);

if (!$rs_card) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_card->FetchRow(DBI_ASSOC)) {

		# MEMBER

		if($axRow['member_image']!='' && $axRow['member_image']!='user.png'){

			$axRow['member_image'] = '<img src="'.$path_upload_member.$axRow['member_image'].'" width="50" height="50" class="img-circle image_border"/>';	

		} else if ($axRow['facebook_id']!='') {

			$axRow['member_image'] = '<img src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=large" width="50" height="50" class="img-circle image_border"/>';

		} else {

			$axRow['member_image'] = '<img src="../../images/user.png" width="50" height="50" class="img-circle image_border"/>';
		}

		$member_name = '';

		if ($axRow['firstname'] || $axRow['lastname']) {

			if ($axRow['member_email']) {

				if ($axRow['member_mobile']) {
								
					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>'.$axRow['member_mobile'];

				} else { $member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email']; }

			} else {

				if ($axRow['member_mobile']) {
								
					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_mobile'];

				} else { $member_name = $axRow['firstname'].' '.$axRow['lastname']; }
			}

		} else {

			if ($axRow['member_email']) {

				if ($axRow['member_mobile']) { $member_name = $axRow['member_email'].'<br>'.$axRow['member_mobile'];

				} else { $member_name = $axRow['member_email']; }

			} else {

				if ($axRow['mobile']) { $member_name = $axRow['member_mobile'];

				} else { $member_name = ''; }
			}
		}


		# LOGO

		if($axRow['logo_image']!=''){

			$axRow['logo_image'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';

		} else {

			$axRow['logo_image'] = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';
		}



		# CARD

		if($axRow['image_newupload']!=''){

			$axRow['image'] = '<img src="../../upload/'.$axRow['path_image'].$axRow['image_newupload'].'" height="60" class="img-rounded image_border"/>';

		} else if($axRow['image']!='') {

			$axRow['image'] = '<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'" height="60" class="img-rounded image_border"/>';

		} else {

			$axRow['image'] = '<img src="../../images/card_privilege.jpg" height="60" class="img-rounded image_border"/>';	
		}




		# TABLE

		$data_card .= '<tr>
							<td>'.$card_n++.'</td>
							<td style="text-align:center">'.$axRow['member_image'].'</td>
							<td>'.$member_name.'</td>
							<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['logo_image'].'</a><br>
								<span style="font-size:11px">'.$axRow['brand_name'].'</span></td>
							<td style="text-align:center"><a href="../card/card.php">'.$axRow['image'].'</a><br>
								<span style="font-size:11px">'.$axRow['card_name'].'</span></td>
							<td >'.$axRow['share_type'].'</td>
							<td >'.$axRow['platform'].'</td>
							<td>'.DateTime($axRow['shca_CreatedDate']).'</td>
						</tr>' ;
		}
	}

$oTmp->assign('data_card', $data_card);



/* ============== */
/*      APP       */
/* ============== */

$sql_app = 'SELECT

					mb_member.firstname,
					mb_member.lastname,
					mb_member.facebook_name,
					mb_member.email AS member_email,
					mb_member.mobile AS member_mobile,
					mb_member.member_image AS member_image,
					mb_member.facebook_id AS facebook_id,
					member_share_app.shap_ShareType AS share_type,
					member_share_app.shap_Platform AS platform,
					member_share_app.shap_CreatedDate

					FROM member_share_app

					LEFT JOIN mb_member
					ON  member_share_app.shap_MemberID = mb_member.member_id

					ORDER BY member_share_app.shap_CreatedDate DESC';

$rs_app = $oDB->Query($sql_app);

if (!$rs_app) {

	echo "An error occurred: ".mysql_error();

} else {

	while($axRow = $rs_app->FetchRow(DBI_ASSOC)) {

		# MEMBER

		if($axRow['member_image']!='' && $axRow['member_image']!='user.png'){

			$axRow['member_image'] = '<img src="'.$path_upload_member.$axRow['member_image'].'" width="50" height="50" class="img-circle image_border"/>';	

		} else if ($axRow['facebook_id']!='') {

			$axRow['member_image'] = '<img src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=large" width="50" height="50" class="img-circle image_border"/>';

		} else {

			$axRow['member_image'] = '<img src="../../images/user.png" width="50" height="50" class="img-circle image_border"/>';
		}


		$member_name = '';

		if ($axRow['firstname'] || $axRow['lastname']) {

			if ($axRow['member_email']) {

				if ($axRow['member_mobile']) {
								
					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email'].'<br>'.$axRow['member_mobile'];

				} else { $member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_email']; }

			} else {

				if ($axRow['member_mobile']) {
								
					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['member_mobile'];

				} else { $member_name = $axRow['firstname'].' '.$axRow['lastname']; }
			}

		} else {

			if ($axRow['member_email']) {

				if ($axRow['member_mobile']) { $member_name = $axRow['member_email'].'<br>'.$axRow['member_mobile'];

				} else { $member_name = $axRow['member_email']; }

			} else {

				if ($axRow['mobile']) { $member_name = $axRow['member_mobile'];

				} else { $member_name = ''; }
			}
		}


		# TABLE

		$data_app .= '<tr>
							<td>'.$app_n++.'</td>
							<td style="text-align:center">'.$axRow['member_image'].'</td>
							<td>'.$member_name.'</td>
							<td >'.$axRow['share_type'].'</td>
							<td >'.$axRow['platform'].'</td>
							<td>'.DateTime($axRow['shap_CreatedDate']).'</td>
						</tr>' ;
		}
	}

$oTmp->assign('data_app', $data_app);



/* ============= */
/*    END TAP    */
/* ============= */



$oTmp->assign('is_menu', 'is_member_insights');

$oTmp->assign('content_file','member_insights/member_share.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
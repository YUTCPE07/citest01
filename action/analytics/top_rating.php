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
		
	} else {$axRow['motivation'] = '-'; } 

	$asData = $axRow;
}



if ($type=="Privilege") {

	$sql_card = 'SELECT DISTINCT
						mi_card.*,
						mi_card.image AS card_image,
						mi_card.image_newupload,
						mi_card.path_image,
						mi_card.name AS card_name,
						mi_brand.name AS brand_name
						FROM mi_card_register
						LEFT JOIN mi_card
						ON mi_card_register.card_id = mi_card.card_id
						LEFT JOIN mi_brand
						ON mi_brand.brand_id = mi_card.brand_id
						WHERE mi_card_register.privilege_id = '.$id.' 
						AND mi_card_register.status=1';

} else if ($type=="Coupon" || $type=="Birthday Coupon") {

	$sql_card = 'SELECT DISTINCT
						mi_card.*,
						mi_card.image AS card_image,
						mi_card.image_newupload,
						mi_card.path_image,
						mi_card.name AS card_name,
						mi_brand.name AS brand_name
						FROM mi_card_register
						LEFT JOIN mi_card
						ON mi_card_register.card_id = mi_card.card_id
						LEFT JOIN mi_brand
						ON mi_brand.brand_id = mi_card.brand_id
						WHERE mi_card_register.coupon_id = '.$id.' 
						AND mi_card_register.status=1';

} else if ($type=="Activity") {

	$sql_card = 'SELECT DISTINCT

						mi_card.*,
						mi_card.image AS card_image,
						mi_card.image_newupload,
						mi_card.path_image,
						mi_card.name AS card_name,
						mi_brand.name AS brand_name
						FROM mi_card_register
						LEFT JOIN mi_card
						ON mi_card_register.card_id = mi_card.card_id
						LEFT JOIN mi_brand
						ON mi_brand.brand_id = mi_card.brand_id
						WHERE mi_card_register.activity_id = '.$id.' 
						AND mi_card_register.status=1';
}



if ($type != "Earn Attention") {

	$oRes_card = $oDB->Query($sql_card)or die(mysql_error());

	$table_rating = "";

	$x = 1;

	while ($axRow_card = $oRes_card->FetchRow(DBI_ASSOC)){

		if ($x > 1) { $table_rating .= '<hr>'; }
		else { $table_rating .= '<br>'; }
		
		$x++;

		$card_id = $axRow_card["card_id"];


		# CARD IMAGE

		if($axRow_card['image_newupload']!=''){

			$axRow_card['card_image'] = '<img src="../../upload/'.$axRow_card['path_image'].$axRow_card['image_newupload'].'" class="img-rounded image_border" height="100"/>';

		} else if($axRow_card['card_image']!=''){

			$axRow_card['card_image'] = '<img src="../../upload/'.$axRow_card['path_image'].$axRow_card['card_image'].'" class="img-rounded image_border" height="100" />';

		} else {

			$axRow_card['card_image'] = '<img src="../../image/card_privilege.jpg" class="img-rounded image_border" height="100" />';
		}


		# CARD PERIOD

		if ($axRow_card['period_type'] == '1') { 

			$axRow_card['period_type'] = DateOnly($axRow_card['date_expired']);	

		} else if ($axRow_card['period_type'] == '2') { 

			$axRow_card['period_type'] = $axRow_card['period_type_other'].' Months';	

		} else if ($axRow_card['period_type'] == '3') { 

			$axRow_card['period_type'] = $axRow_card['period_type_other'].' Years';	

		} else if ($axRow_card['period_type'] == '4') { 

			$axRow_card['period_type'] = 'Member Life Time';	
		}


		$sql_brand_id = 'SELECT brand_id FROM mi_card WHERE card_id = "'.$card_id.'"';

		$brand_id = $oDB->QueryOne($sql_brand_id);

		$sql_branch = 'SELECT name as txt, branch_id as id 
						FROM mi_branch 
						WHERE brand_id = "'.$brand_id.'" '.$where_branch.'';

		$oRes_branch = $oDB->Query($sql_branch)or die(mysql_error());


		if ($type=="Privilege") {

			$total_use = get_total_privilege_rating($id,"",$card_id,"");

		} else if ($type=="Coupon" || $type=="Birthday Coupon") {

			$total_use = get_total_coupon_rating($id,"",$card_id,"");

		} else {

			$total_use = get_total_activity_rating($id,"",$card_id,"");
		}


		$table_rating .= '<table class="myPopup">
							<tr>
								<td rowspan="4" width="200px" valign="top" style="text-align:center;">
									'.$axRow_card['card_image'].'</td>
								<td style="text-align:right;width:100px;"><b>Brand</b></td>
								<td style="text-align:center;width:30px;"><b>:</b></td>
								<td><b>'.$axRow_card['brand_name'].'</b></td>
							</tr>
							<tr>
								<td style="text-align:right"><b>Card</b></td>
								<td style="text-align:center;"><b>:</b></td>
								<td><b>'.$axRow_card['card_name'].'</b></td></tr>
							<tr>
								<td style="text-align:right"><b>Card Multiple</b></td>
								<td style="text-align:center;"><b>:</b></td>
								<td><b>'.$axRow_card['flag_multiple'].'</b></td></tr>
							<tr>
								<td style="text-align:right"><b>Period</b></td>
								<td style="text-align:center;"><b>:</b></td>
								<td><b>'.$axRow_card['period_type'].'</b></td></tr>
						</table>
						<br>
						<center>
	 						<span style="font-size:16px"><b>Rating &nbsp; : &nbsp; '.number_format($total_use).'</span></b>
	 					</center>
	 					<br>
	 					<table class="table table-bordered display" style="background-color:white;">
							<thead><tr class="th_table">
							<th style="text-align:center;">Member</th>
							<th style="text-align:center;">Profile</th>';
		$a=0;

		while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)) {

			$table_rating .= "<th style='text-align:center'>".$axRow_branch['txt']."</th>";

			$branch_id[$a]  = $axRow_branch['id'];

			$a++;
		}

		if($_SESSION['user_type_id_ses'] != 3) {

			$table_rating .= "<th style='text-align:center;'>รวม</th>";
		}

		$table_rating .= "</tr></thead><tbody>";



		# MEMBER REGISTER

		if ($type=="Privilege") {

			$member_trans = "member_privilege_trans.mepe_MemberPrivlegeID";
			$trans_table = "member_privilege_trans";
			$trans_id = "member_privilege_trans.priv_PrivilegeID";
			$trans_rating = "member_privilege_trans.mepe_Rating";
			$trans_del = "member_privilege_trans.mepe_Deleted";

		} else if ($type=="Coupon" || $type=="Birthday Coupon") {

			$member_trans = "member_coupon_trans.meco_MemberCouponID";
			$trans_table = "member_coupon_trans";
			$trans_id = "member_coupon_trans.coup_CouponID";
			$trans_rating = "member_coupon_trans.meco_Rating";
			$trans_del = "member_coupon_trans.meco_Deleted";

		} else {

			$member_trans = "member_activity_trans.meac_MemberActivityID";
			$trans_table = "member_activity_trans";
			$trans_id = "member_activity_trans.acti_ActivityID";
			$trans_rating = "member_activity_trans.meac_Rating";
			$trans_del = "member_activity_trans.meac_Deleted";
		}

		$sql_register = 'SELECT DISTINCT

							mb_member.member_id,
							mb_member.facebook_id,
							mb_member.firstname,
							mb_member.lastname,
							mb_member.email,
							mb_member.mobile,
							mb_member.member_image,

							AVG('.$trans_rating.') AS total

							FROM '.$trans_table.'

							LEFT JOIN mb_member
							ON '.$trans_table.'.memb_MemberID = mb_member.member_id

							LEFT JOIN member_transaction_h
							ON '.$trans_table.'.meth_MemberTransactionHID = member_transaction_h.meth_MemberTransactionHID

							WHERE member_transaction_h.card_CardID = "'.$card_id.'"
							AND '.$trans_id.' = "'.$id.'"
							AND '.$trans_rating.' != "0"
							AND '.$trans_del.' = ""
							GROUP BY '.$trans_table.'.memb_MemberID
							ORDER BY total DESC';

		$oRes_register = $oDB->Query($sql_register)or die(mysql_error());

		$check_regis = $oDB->QueryOne($sql_register);

		if ($check_regis) {

			while ($axRow = $oRes_register->FetchRow(DBI_ASSOC)) {

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

				$table_rating .= "<tr>
								<td style='text-align:center'>".$axRow['member_image']."</td>
								<td>".$member_name."</td>";

				for ($i=0; $i < $a; $i++) {

					if ($type=="Privilege") {

						$member_like = get_total_privilege_rating($id,$branch_id[$i],$card_id,$axRow['member_id']);

					} else if ($type=="Coupon" || $type=="Birthday Coupon") {

						$member_like = get_total_coupon_rating($id,$branch_id[$i],$card_id,$axRow['member_id']);

					} else {

						$member_like = get_total_activity_rating($id,$branch_id[$i],$card_id,$axRow['member_id']);
					}

					$table_rating .= "<td style='text-align:center'>".number_format($member_like)."</td>";
				}

				if($_SESSION['user_type_id_ses'] != 3) {

					$table_rating .= "<td style='text-align:center;background-color:#F2F2F2'><b>".number_format($axRow['total'])."</b></td>";
				}

				$table_rating .= "</tr>";
			}

		}


		$table_rating .= "</tbody>";

		$table_rating .="</table>";
	}

} else {

	$total_use = get_total_earn_rating($id,"","");

	$table_rating .= '<br>
					<center>
	 					<span style="font-size:16px"><b>Total Like &nbsp; : &nbsp; '.number_format($total_use).' &nbsp; Times</span></b>
	 				</center>
	 				<br>';

	$table_rating .= "<table id='example' class='table table-bordered' style='background-color:white;'>
						<thead><tr class='th_table'>
						<th style='text-align:center;'>Member</th>
						<th style='text-align:center;'>Profile</th>";
	$a=0;

	$sql_branch = 'SELECT brnc_BranchID FROM hilight_coupon WHERE coup_CouponID ='.$id;

	$earn_branch = $oDB->QueryOne($sql_branch);

	$sql_branch = 'SELECT name as txt, branch_id as id 
					FROM mi_branch
					WHERE branch_id IN ('.$earn_branch.')';

	$oRes_branch = $oDB->Query($sql_branch);

	while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)) {

		$table_rating .= "<th style='text-align:center'>".$axRow_branch['txt']."</th>";

		$branch_id[$a]  = $axRow_branch['id'];

		$a++;
	}

	if($_SESSION['user_type_id_ses'] != 3) {

		$table_rating .= "<th style='text-align:center;width:10%'>รวม</th>";
	}

	$table_rating .= "</tr></thead><tbody>";

	$sql_use = 'SELECT DISTINCT

						mb_member.member_id,
						mb_member.facebook_id,
						mb_member.facebook_name,
						mb_member.firstname,
						mb_member.lastname,
						mb_member.email,
						mb_member.member_image,

						AVG(hilight_coupon_trans.hico_Rating) AS total

						FROM mb_member

						LEFT JOIN hilight_coupon_trans
						ON hilight_coupon_trans.memb_MemberID = mb_member.member_id

						WHERE hilight_coupon_trans.coup_CouponID = "'.$id.'"
						AND hilight_coupon_trans.hico_Rating != 0
						GROUP BY hilight_coupon_trans.memb_MemberID
						ORDER BY total DESC';

	$oRes_use = $oDB->Query($sql_use)or die(mysql_error());

	$check_use = $oDB->QueryOne($sql_use);

	if ($check_use) {

		while ($axRow = $oRes_use->FetchRow(DBI_ASSOC)) {

			$total_member = 0;

			# MEMBER

			if($axRow['member_image']!='') {

				$axRow['member_image'] = '<img class="img-circle image_border" src="'.$path_upload_member.$axRow['member_image'].'"width="50" height="50"/>';

			} else if ($axRow['facebook_id']!='') {

				$axRow['member_image'] = '<img class="img-circle image_border" src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=large" width="50" height="50" />';

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

			$table_rating .= "<tr>
							<td style='text-align:center'>".$axRow['member_image']."</td>
							<td>".$member_name."</td>";

			for ($i=0; $i < $a; $i++) {

				$member_like = get_total_earn_rating($id,$branch_id[$i],$axRow['member_id']);

				$total_member += $member_like;

				$table_rating .= "<td style='text-align:center;'>".number_format($member_like)."</td>";
			}


			if($_SESSION['user_type_id_ses'] != 3) {

				$table_rating .= "<td style='text-align:center;background-color:#F2F2F2'><b>".number_format($total_member)."</b></td>";
			}

			$table_rating .= "</tr>";
		}
	}


	$table_rating .= "</tbody>";

	$table_rating .="</table>";
}




$oTmp->assign('table_rating', $table_rating);

$oTmp->assign('data', $asData);

$oTmp->assign('is_menu', 'is_analytics');

$oTmp->assign('content_file', 'analytics/top_rating.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
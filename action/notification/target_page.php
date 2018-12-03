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

if ($_SESSION['role_action']['push_notification']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$sql = 'SELECT target_list.*,
		mi_brand.name AS brand_name,
		mi_brand.logo_image,
		mi_brand.path_logo
		FROM target_list 

		LEFT JOIN mi_brand
		ON target_list.bran_BrandID = mi_brand.brand_id

		ORDER BY tali_UpdatedDate DESC';

$oRes = $oDB->Query($sql);

$i=0;

$data_table = '';

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$i++;


	# LOGO

	if($axRow['logo_image']!=''){

		$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';
		$logo_view = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="100" height="100"/>';

	} else {

		$logo_brand = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';
		$logo_view = '<img src="../../images/400x400.png" class="image_border" width="100" height="100"/>';
	}

	if ($axRow['bran_BrandID']=='0') {

		$logo_brand = '<img src="../../images/mi_action_logo.png" width="60" class="image_border" height="60"/>';
		$logo_view = '<img src="../../images/mi_action_logo.png" width="100" class="image_border" height="100"/>';

		$axRow['brand_name'] = 'MemberIn';
	}


	# TARGET

	if ($axRow['tali_SQLView'] == 'promotion_view') { $target_type = 'Promotion'; }
	elseif ($axRow['tali_SQLView'] == 'privilege_view') { $target_type = 'Privilege'; }
	elseif ($axRow['tali_SQLView'] == 'redeem_view') { $target_type = 'Redeem'; }
	elseif ($axRow['tali_SQLView'] == 'checkin_view') { $target_type = 'Checkin'; }
	elseif ($axRow['tali_SQLView'] == 'member_profile_view') { $target_type = 'Member Profile'; }


	# DESCRIPTION

	if ($axRow['tali_Description']) { $axRow['tali_Description'] = nl2br($axRow['tali_Description']); } 
	else { $axRow['tali_Description'] = '-'; }



	# TARGET VIEW

	if ($axRow['tali_SQLView'] == 'promotion_view') { # PROMOTION

		$data_view = '<table class="table table-striped table-bordered" style="width:550px;margin:0">
						<thead>
							<tr class="th_table">
								<th>Promotion</th>
								<th colspan="2">Detail</th>
							</tr>
						</thead>
						<tbody>';

		$sql_view = 'SELECT tavi_ID AS id
						FROM target_view 
						WHERE tali_TargetListID="'.$axRow['tali_TargetListID'].'" 
						AND tavi_Type="Promotion"
						AND tavi_Deleted=""';

		$oRes_view = $oDB->Query($sql_view);

		while ($view = $oRes_view->FetchRow(DBI_ASSOC)){

			$sql_target = 'SELECT hilight_coupon.coup_Name AS name, 
								hilight_coupon.coup_CouponID AS id,
								hilight_coupon.coup_Image AS image,
								hilight_coupon.coup_ImagePath AS image_path,
								hilight_coupon.coup_Type AS type,
								hilight_coupon.coup_StartDate AS start_date,
								hilight_coupon.coup_EndDate AS end_date,
								mi_brand.name AS brand_name
							FROM hilight_coupon
							LEFT JOIN mi_brand
							ON hilight_coupon.bran_BrandID = mi_brand.brand_id
							WHERE hilight_coupon.coup_Deleted = ""
							AND hilight_coupon.coup_CouponID = "'.$view['id'].'"
							AND mi_brand.flag_del = ""';

			$oRes_target = $oDB->Query($sql_target);
			$target = $oRes_target->FetchRow(DBI_ASSOC);

			$data_view .= '<tr>
							<td style="text-align:center" width="170px"><img src="../../upload/'.$target['image_path'].$target['image'].'" height="80px" class="image_border"></td>
							<td style="text-align:right" width="120px">
								Brand<br>
								Promotion<br>
								Type<br>
								Start-End Date</td>
							<td>
								'.$target['brand_name'].'<br>
								'.$target['name'].'<br>
								'.$target['type'].'<br>
								'.DateOnly($target['start_date']).' - '.DateOnly($target['end_date']).'</td>
							</tr>';
		}

		$data_view .= '	</tbody>
					</table>';

	} elseif ($axRow['tali_SQLView'] == 'checkin_view') { # CHECKIN

		$data_view = '<table class="table table-striped table-bordered" style="width:550px;margin:0">
						<thead>
							<tr class="th_table">
								<th>Card</th>
								<th colspan="2">Detail</th>
							</tr>
						</thead>
						<tbody>';

		$sql_view = 'SELECT tavi_ID AS id
						FROM target_view 
						WHERE tali_TargetListID="'.$axRow['tali_TargetListID'].'" 
						AND tavi_Type="Card"
						AND tavi_Deleted=""';

		$oRes_view = $oDB->Query($sql_view);

		while ($view = $oRes_view->FetchRow(DBI_ASSOC)){

			$sql_target = 'SELECT mi_card.name AS name, 
									mi_card.card_id AS id,
									mi_card.image AS image,
									mi_card.path_image AS image_path,
									mi_card.member_fee AS member_fee,
									mi_card.flag_multiple AS multiple,
									mi_brand.name AS brand_name
							FROM mi_card
							LEFT JOIN mi_brand
							ON mi_card.brand_id = mi_brand.brand_id
							WHERE mi_card.flag_del = "0"
							AND mi_card.card_id = "'.$view['id'].'"
							AND mi_brand.flag_del = ""';

			$oRes_target = $oDB->Query($sql_target);
			$target = $oRes_target->FetchRow(DBI_ASSOC);

			$data_view .= '<tr>
							<td style="text-align:center" width="170px"><img src="../../upload/'.$target['image_path'].$target['image'].'" height="80px" class="image_border img-rounded"></td>
							<td style="text-align:right" width="120px">
								Brand<br>
								Card<br>
								Member Fee<br>
								Multiple</td>
							<td>
								'.$target['brand_name'].'<br>
								'.$target['name'].'<br>
								'.number_format($target['member_fee'],2).' ฿<br>
								'.$target['multiple'].'</td>
						</tr>';
		}

		$data_view .= '	</tbody>
					</table>';

	} elseif ($axRow['tali_SQLView'] == 'privilege_view') { # PRIVILEGE

		$data_view = '<table class="table table-striped table-bordered" style="width:550px;margin:0">
						<thead>
							<tr class="th_table">
								<th>Privilege</th>
								<th colspan="2">Detail</th>
							</tr>
						</thead>
						<tbody>';

		$sql_view = 'SELECT tavi_ID AS id
						FROM target_view 
						WHERE tali_TargetListID="'.$axRow['tali_TargetListID'].'" 
						AND tavi_Type="Privilege"
						AND tavi_Deleted=""';

		$oRes_view = $oDB->Query($sql_view);

		while ($view = $oRes_view->FetchRow(DBI_ASSOC)){

			$sql_target = 'SELECT privilege.priv_Name AS name, 
								privilege.priv_PrivilegeID AS id,
								privilege.priv_Image AS image,
								privilege.priv_ImagePath AS image_path,
								mi_brand.name AS brand_name,
								"Privilege" AS type
								FROM privilege
								LEFT JOIN mi_brand
								ON privilege.bran_BrandID = mi_brand.brand_id
								WHERE privilege.priv_Deleted = ""
								AND privilege.priv_PrivilegeID = "'.$view['id'].'"
								AND mi_brand.flag_del = ""';

			$oRes_target = $oDB->Query($sql_target);
			$target = $oRes_target->FetchRow(DBI_ASSOC);

			$data_view .= '<tr>
							<td style="text-align:center" width="170px"><img src="../../upload/'.$target['image_path'].$target['image'].'" height="80px" class="image_border"></td>
							<td style="text-align:right" width="120px">
								Brand<br>
								Privilege<br>
								Type</td>
							<td>
								'.$target['brand_name'].'<br>
								'.$target['name'].'<br>
								'.$target['type'].'</td>
						</tr>';
		}

		$sql_view = 'SELECT tavi_ID AS id
						FROM target_view 
						WHERE tali_TargetListID="'.$axRow['tali_TargetListID'].'" 
						AND tavi_Type="Coupon"
						AND tavi_Deleted=""';

		$oRes_view = $oDB->Query($sql_view);

		while ($view = $oRes_view->FetchRow(DBI_ASSOC)){

			$sql_target = 'SELECT coupon.coup_Name AS name, 
								coupon.coup_CouponID AS id,
								coupon.coup_Image AS image,
								coupon.coup_ImagePath AS image_path,
								mi_brand.name AS brand_name,
								"Coupon" AS type
								FROM coupon
								LEFT JOIN mi_brand
								ON coupon.bran_BrandID = mi_brand.brand_id
								WHERE coupon.coup_Deleted = ""
								AND coupon.coup_CouponID = "'.$view['id'].'"
								AND mi_brand.flag_del = ""
								AND coupon.coup_Birthday = ""';

			$oRes_target = $oDB->Query($sql_target);
			$target = $oRes_target->FetchRow(DBI_ASSOC);

			$data_view .= '<tr>
							<td style="text-align:center" width="170px"><img src="../../upload/'.$target['image_path'].$target['image'].'" height="80px" class="image_border"></td>
							<td style="text-align:right" width="120px">
								Brand<br>
								Privilege<br>
								Type</td>
							<td>
								'.$target['brand_name'].'<br>
								'.$target['name'].'<br>
								'.$target['type'].'</td>
						</tr>';
		}

		$sql_view = 'SELECT tavi_ID AS id
						FROM target_view 
						WHERE tali_TargetListID="'.$axRow['tali_TargetListID'].'" 
						AND tavi_Type="Birthday"
						AND tavi_Deleted=""';

		$oRes_view = $oDB->Query($sql_view);

		while ($view = $oRes_view->FetchRow(DBI_ASSOC)){

			$sql_target = 'SELECT coupon.coup_Name AS name, 
								coupon.coup_CouponID AS id,
								coupon.coup_Image AS image,
								coupon.coup_ImagePath AS image_path,
								mi_brand.name AS brand_name,
								"Birthday Coupon" AS type
								FROM coupon
								LEFT JOIN mi_brand
								ON coupon.bran_BrandID = mi_brand.brand_id
								WHERE coupon.coup_Deleted = ""
								AND coupon.coup_CouponID = "'.$view['id'].'"
								AND mi_brand.flag_del = ""
								AND coupon.coup_Birthday = "T"';


			$oRes_target = $oDB->Query($sql_target);
			$target = $oRes_target->FetchRow(DBI_ASSOC);

			$data_view .= '<tr>
							<td style="text-align:center" width="170px"><img src="../../upload/'.$target['image_path'].$target['image'].'" height="80px" class="image_border"></td>
							<td style="text-align:right" width="120px">
								Brand<br>
								Privilege<br>
								Type</td>
							<td>
								'.$target['brand_name'].'<br>
								'.$target['name'].'<br>
								'.$target['type'].'</td>
						</tr>';
		}

		$sql_view = 'SELECT tavi_ID AS id
						FROM target_view 
						WHERE tali_TargetListID="'.$axRow['tali_TargetListID'].'" 
						AND tavi_Type="Activity"
						AND tavi_Deleted=""';

		$oRes_view = $oDB->Query($sql_view);

		while ($view = $oRes_view->FetchRow(DBI_ASSOC)){

			$sql_target = 'SELECT activity.acti_Name AS name, 
								activity.acti_ActivityID AS id,
								activity.acti_Image AS image,
								activity.acti_ImagePath AS image_path,
								mi_brand.name AS brand_name,
								"Activity" AS type
								FROM activity
								LEFT JOIN mi_brand
								ON activity.bran_BrandID = mi_brand.brand_id
								WHERE activity.acti_Deleted = ""
								AND activity.acti_ActivityID = "'.$view['id'].'"
								AND mi_brand.flag_del = ""';

			$oRes_target = $oDB->Query($sql_target);
			$target = $oRes_target->FetchRow(DBI_ASSOC);

			$data_view .= '<tr>
							<td style="text-align:center" width="170px"><img src="../../upload/'.$target['image_path'].$target['image'].'" height="80px" class="image_border"></td>
							<td style="text-align:right" width="120px">
								Brand<br>
								Privilege<br>
								Type</td>
							<td>
								'.$target['brand_name'].'<br>
								'.$target['name'].'<br>
								'.$target['type'].'</td>
						</tr>';
		}

		$data_view .= '	</tbody>
					</table>';

	} elseif ($axRow['tali_SQLView'] == 'redeem_view') { # REDEEM

		$data_view = '<table class="table table-striped table-bordered" style="width:550px;margin:0">
						<thead>
							<tr class="th_table">
								<th>Reward</th>
								<th colspan="2">Detail</th>
							</tr>
						</thead>
						<tbody>';

		$sql_view = 'SELECT tavi_ID AS id
						FROM target_view 
						WHERE tali_TargetListID="'.$axRow['tali_TargetListID'].'" 
						AND tavi_Type="Reward"
						AND tavi_Deleted=""';

		$oRes_view = $oDB->Query($sql_view);

		while ($view = $oRes_view->FetchRow(DBI_ASSOC)){

			$sql_target = 'SELECT reward.rewa_Name AS name, 
									reward.rewa_RewardID AS id,
									IF(reward.card_CardID=0,
										CONCAT("<img src=\'../../upload/",reward.rewa_ImagePath,reward.rewa_Image,"\' height=\'80px\' class=\'image_border\'>"),
										CONCAT("<img src=\'../../upload/",mi_card.path_image,mi_card.image,"\' height=\'80px\' class=\'image_border img-rounded\'>")
									) AS image,
									reward.rewa_Limit AS reward_limit,
									reward.rewa_Qty AS qty,
									reward.rewa_Type AS type,
									mi_brand.name AS brand_name
								FROM reward
								LEFT JOIN mi_brand
								ON reward.bran_BrandID = mi_brand.brand_id
								LEFT JOIN mi_card
								ON reward.card_CardID = mi_card.card_id
								WHERE reward.rewa_Deleted = ""
								AND reward.rewa_RewardID = "'.$view['id'].'"
								AND mi_brand.flag_del = ""
								ORDER BY name';

			$oRes_target = $oDB->Query($sql_target);
			$target = $oRes_target->FetchRow(DBI_ASSOC);

			if ($target["reward_limit"]=='T') { $target['qty'] = number_format($target['qty']); }
			else { $target['qty'] = "Unlimit"; }

			$data_view .= '<tr>
								<td style="text-align:center;width:170px">'.$target['image'].'</td>
								<td style="text-align:right;width:120px">
									Brand<br>
									Reward<br>
									Type<br>
									Qty</td>
								<td>
									'.$target['brand_name'].'<br>
									'.$target['name'].'<br>
									'.$target['type'].'<br>
									'.$target['qty'].'</td>
							</tr>';
		}

		$data_view .= '	</tbody>
					</table>';

	} elseif ($axRow['tali_SQLView'] == 'member_profile_view') { # MEMBER PROFILE

		$data_view = '<table class="table table-striped table-bordered" style="width:550px;margin:0">
						<thead>
							<tr class="th_table">
								<th>Gender</th>
								<th>Age</th>
							</tr>
						</thead>
						<tbody>';

		# GENDER

		$sql_view = 'SELECT tavi_ID AS id
						FROM target_view 
						WHERE tali_TargetListID="'.$axRow['tali_TargetListID'].'" 
						AND tavi_Type="Gender"';
		$gender_view = $oDB->QueryOne($sql_view);

		# AGE

		$sql_view = 'SELECT tavi_ID AS id
						FROM target_view 
						WHERE tali_TargetListID="'.$axRow['tali_TargetListID'].'" 
						AND tavi_Type="Age"';
		$age_view = $oDB->QueryOne($sql_view);

		if ($age_view != 'All') {

	        $age_basic = $age_view;
	        $token = strtok($age_basic,",");
	     	$basic_age1 = $token;
	     	$token = strtok (",");
	     	$basic_age2 = $token;

			$sql_age_basic1 = 'SELECT mata_NameEn FROM master_target WHERE mata_MasterTargetID="'.$basic_age1.'"';
			$age_basic1 = $oDB->QueryOne($sql_age_basic1);

			$sql_age_basic2 = 'SELECT mata_NameEn FROM master_target WHERE mata_MasterTargetID="'.$basic_age2.'"';
			$age_basic2 = $oDB->QueryOne($sql_age_basic2);

			$age_view = $age_basic1.' - '.$age_basic2.' Age Restriction';
		}

		$data_view .= '<tr>
							<td style="text-align:center;width:170px">'.$gender_view.'</td>
							<td style="text-align:center;width:120px">'.$age_view.'</td>
							</tr>
						</tbody>
					</table>';
	}



	# VIEW

	$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#'.$axRow['tali_TargetListID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>

			<div class="modal fade" id="'.$axRow['tali_TargetListID'].'" tabindex="-1" role="dialog" aria-labelledby="BasicDataLabel">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-body" align="left">
							<span style="font-size:16px;"><b>'.$axRow['tali_Name'].'</b></span>
							<div style="font-size:16px;float:right"><b>'.$target_type.'</b></div>
							<hr>
							<table>
								<tr>
									<td style="text-align:center;width:150px">'.$logo_view.'<br>'.$axRow['brand_name'].'</td>
									<td>Description :<br>
									<div style="padding-left:80px">'.$axRow['tali_Description'].'</div></td>
								</tr>
							</table>
							<hr>
							<center>'.$data_view.'</center>
						</div>
					</div>
				</div>
			</div>';


	# DATA TABLE

	$data_table .= '<tr >
						<td >'.$i.'</td>
						<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
							<span style="font-size:11px;">'.$axRow['brand_name'].'</span>
						</td>
						<td>'.$axRow['tali_Name'].'</td>
						<td>'.$target_type.'<hr><center>'.$view.'</center></td>
						<td>'.$axRow['tali_Description'].'</td>
						<td style="text-align:center">'.DateTime($axRow['tali_UpdatedDate']).'</td>';

	if ($_SESSION['role_action']['push_notification']['edit'] == 1) {

		$data_table .=	'<td style="text-align:center">
							<a href="target_create.php?act=edit&id='.$axRow['tali_TargetListID'].'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></a></td>';
	}

	$data_table .=	'</tr>';
}



$oTmp->assign('data_table', $data_table);
$oTmp->assign('content_file', 'notification/target_page.htm');
$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
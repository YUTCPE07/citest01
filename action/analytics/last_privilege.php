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

	$where_branch = ' AND mi_branch.branch_id = "'.$_SESSION['user_branch_id'].'"';
}


$sql = 'SELECT privilege.*,
			mi_privilege_type.name AS privilege_type_name,
			mi_brand.name AS brand_name

			FROM privilege

			LEFT JOIN mi_privilege_type
			ON privilege.prty_PrivilegeTypeID = mi_privilege_type.privilege_type_id

			LEFT JOIN mi_brand
			ON mi_brand.brand_id = privilege.bran_BrandID

			WHERE privilege.priv_PrivilegeID = "'.$id.'"';

$oRes = $oDB->Query($sql)or die(mysql_error());

$asData = array();

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	if ($axRow['priv_Description']=="") { $axRow['priv_Description']="-"; }
	else { $axRow['priv_Description'] = nl2br($axRow['priv_Description']); }

	if ($axRow['priv_Motivation'] == 'Point') { 

		$icon_sql = "SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID = 3";
		$icon = $oDB->QueryOne($icon_sql);
		$icon ='<img src="../../upload/collection_upload/'.$icon.'" width="12px" height="12px" style="margin-bottom:3px">';

		$plan_sql = "SELECT mopp_Name, mopp_PointQty, mopp_UseAmount FROM motivation_plan_point WHERE mopp_MotivationPointID='".$axRow['priv_MotivationID']."'";
		$get_point = $oDB->Query($plan_sql);
		$point = $get_point->FetchRow(DBI_ASSOC);

		$axRow['priv_Motivation'] = $point['mopp_Name'].' ('.number_format($point['mopp_UseAmount'],2).' ฿ / '.$point['mopp_PointQty'].' '.$icon.')';

	} else if ($axRow['priv_Motivation'] == 'Stamp') {

		$plan_sql = "SELECT mops_Name, mops_StampQty, mops_CollectionTypeID FROM motivation_plan_stamp WHERE mops_MotivationStampID='".$axRow['priv_MotivationID']."'";
		$get_stamp = $oDB->Query($plan_sql);
		$stamp = $get_stamp->FetchRow(DBI_ASSOC);

		$icon_sql = "SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID = ".$stamp['mops_CollectionTypeID'];
		$icon = $oDB->QueryOne($icon_sql);
		$icon ='<img src="../../upload/collection_upload/'.$icon.'" width="12px" height="12px" style="margin-bottom:3px">';

		$axRow['priv_Motivation'] = $stamp['mops_Name'].' (1 Times / '.$stamp['mops_StampQty'].' '.$icon.')';
	} 


	$asData = $axRow;
}


$sql_privilege = "SELECT 
					member_privilege_trans.mepe_CreatedDate AS date_use,
					member_privilege_trans.mepe_MemberPrivlegeID AS code_use,
					mi_branch.name AS branch_name,
					mi_card.card_id,
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

					FROM member_privilege_trans

					LEFT JOIN mb_member
					ON member_privilege_trans.memb_MemberID = mb_member.member_id

					LEFT JOIN mi_card
					ON member_privilege_trans.card_CardID = mi_card.card_id

					LEFT JOIN mi_branch
					ON member_privilege_trans.brnc_BranchID = mi_branch.branch_id

					LEFT JOIN mi_brand
					ON mi_brand.brand_id = mi_branch.brand_id

					WHERE member_privilege_trans.priv_PrivilegeID = ".$id."
					".$where_branch."

					ORDER BY date_use DESC";

$oRes_privilege = $oDB->Query($sql_privilege)or die(mysql_error());

$check_privilege = $oDB->QueryOne($sql_privilege);

if ($check_privilege) {

	$table_privilege = "<table id='example' class='table table-bordered' style='background-color:white;'>
							<thead><tr class='th_table'>
							<th style='text-align:center;'>Use Date</th>
							<th style='text-align:center;'>Code Use</th>
							<th style='text-align:center;'>Member</th>
							<th style='text-align:center;'>Profile</th>
							<th style='text-align:center;'>Card</th>
							<th style='text-align:center;'>Branch</th>
							</tr></thead><tbody>";

	while ($axRow = $oRes_privilege->FetchRow(DBI_ASSOC)) {

		# MEMBER CARD ID

		$sql_card = 'SELECT member_card_code
						FROM mb_member_register
						WHERE card_id="'.$axRow['card_id'].'"
						AND member_id="'.$axRow['member_id'].'"';
		$card_code = $oDB->QueryOne($sql_card);


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

					if ($card_code) {
									
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'].'<br>Member Card : '.$card_code;

					} else {
									
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'];
					}

				} else {

					if ($card_code) {
									
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>Member Card : '.$card_code;
								
					} else {
									
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'];
					}
				}

			} else {

				if ($axRow['mobile']) {

					if ($card_code) {
									
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile'].'<br>Member Card : '.$card_code;

					} else {
									
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile'];
					}

				} else {

					if ($card_code) {
									
						$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>Member Card : '.$card_code;

					} else {

						$member_name = $axRow['firstname'].' '.$axRow['lastname'];
					} 
				}
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

		$table_privilege .= "<tr>
								<td style='text-align:center'>".DateTime($axRow['date_use'])."</td>
								<td style='text-align:center'>".$axRow['code_use']."</td>
								<td style='text-align:center'>".$axRow['member_image']."</td>
								<td>".$member_name."</td>
								<td style='text-align:center'><a href='../card/card.php'>".$axRow['card_image']."</a><br>
									<span style='font-size:11px'>".$axRow['card_name']."</span></td>
								<td style='text-align:center'>".$axRow['branch_name']."</td>
							</tr>";
	}

	$table_privilege .= "</tbody></table>";
}



$oTmp->assign('table_privilege', $table_privilege);

$oTmp->assign('data', $asData);

$oTmp->assign('is_menu', 'is_analytics');

$oTmp->assign('content_file', 'analytics/last_privilege.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
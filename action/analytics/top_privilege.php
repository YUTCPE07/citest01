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

$sql_card_privilege = 'SELECT DISTINCT
						mi_card.*,
						mi_card.image AS card_image,
						mi_card.name AS card_name,
						mi_brand.name AS brand_name
						FROM mi_card_register
						LEFT JOIN mi_card
						ON mi_card_register.card_id = mi_card.card_id
						LEFT JOIN mi_brand
						ON mi_brand.brand_id = mi_card.brand_id
						WHERE mi_card_register.privilege_id = '.$id.' 
						AND mi_card_register.status=1';

$oRes_card = $oDB->Query($sql_card_privilege)or die(mysql_error());

$table_privilege = "";

$x = 1;

while ($axRow_card = $oRes_card->FetchRow(DBI_ASSOC)){

	if ($x > 1) { $table_privilege .= '<hr>'; }
	else { $table_privilege .= '<br>'; }
	
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

		$axRow_card['period_type'] = DateOnly($axRow_card['date_expire']);	

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

	$sql_privilege = 'SELECT priv_Name as txt, priv_PrivilegeID as id 
						FROM privilege 
						WHERE bran_BrandID = "'.$brand_id.'" 
						AND priv_PrivilegeID = "'.$id.'" ';


	$oRes_branch = $oDB->Query($sql_branch)or die(mysql_error());

	$oRes_privilege = $oDB->Query($sql_privilege)or die(mysql_error());

	$total_use = get_total_privilege_use($id,"",$card_id,"");


	$table_privilege .= '<table class="myPopup">
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
	 						<span style="font-size:16px"><b>Total Use &nbsp; : &nbsp; '.number_format($total_use).' &nbsp; Times</span></b>
	 					</center>
	 					<br>
	 					<table class="table table-bordered display" style="background-color:white;">
							<thead><tr class="th_table">
							<th style="text-align:center;width:15%"">Member</th>
							<th style="text-align:center;">Profile</th>';

	$a=0;

	while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)) {

		$table_privilege .= "<th style='text-align:center'>".$axRow_branch['txt']."</th>";

		$branch_id[$a]  = $axRow_branch['id'];

		$a++;
	}

	if($_SESSION['user_type_id_ses'] != 3) {

		$table_privilege .= "<th style='text-align:center;width:10%'>รวม</th>";
	}
		
	$table_privilege .= "</tr></thead><tbody>";


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
						mb_member_register.member_card_code,

						COUNT(member_privilege_trans.mepe_MemberPrivlegeID) AS total

						FROM mb_member_register
						LEFT JOIN mb_member
						ON mb_member_register.member_id = mb_member.member_id
						LEFT JOIN member_privilege_trans
						ON member_privilege_trans.memb_MemberID = mb_member.member_id
						WHERE mb_member_register.card_id = "'.$card_id.'"
						AND member_privilege_trans.priv_PrivilegeID = "'.$id.'"
						GROUP BY member_privilege_trans.memb_MemberID
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

						if ($axRow['member_card_code']) {
								
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'].'<br>Member Card : '.$axRow['member_card_code'];

						} else {
								
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'];
						}

					} else {

						if ($axRow['member_card_code']) {
								
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>Member Card : '.$axRow['member_card_code'];
							
						} else {
								
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'];
						}
					}

				} else {

					if ($axRow['mobile']) {

						if ($axRow['member_card_code']) {
								
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile'].'<br>Member Card : '.$axRow['member_card_code'];

						} else {
								
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile'];
						}

					} else {

						if ($axRow['member_card_code']) {
								
							$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>Member Card : '.$axRow['member_card_code'];

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




			$table_privilege .= "<tr>
							<td style='text-align:center'>".$axRow['member_image']."</td>
							<td>".$member_name."</td>";

			for ($i=0; $i < $a; $i++) {

				$member_priv = get_total_privilege_use($id,$branch_id[$i],$card_id,$axRow['member_id']);

				$total_member += $member_priv;

				$table_privilege .= "<td style='text-align:center'>".number_format($member_priv)."</td>";

			}

			if($_SESSION['user_type_id_ses'] != 3) {

				$table_privilege .= "<td style='text-align:center;background-color:#F2F2F2'><b>".number_format($total_member)."</b></td>";
			}

			$table_privilege .= "</tr>";
		}
	} 

	$table_privilege .= "</tbody>";
		
	$table_privilege .="</table>";
}



$oTmp->assign('table_privilege', $table_privilege);

$oTmp->assign('data', $asData);

$oTmp->assign('is_menu', 'is_analytics');

$oTmp->assign('content_file', 'analytics/top_privilege.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
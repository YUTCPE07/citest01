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

$path_upload_collection = $_SESSION['path_upload_collection'];



if($_SESSION['user_branch_id']){

	$where_branch .= ' AND mi_branch.branch_id = "'.$_SESSION['user_branch_id'].'"';
}



$sql = 'SELECT

		reward.*,
		mi_brand.name as brand_name,
		mi_tg_activity.activity_name as category_name,
		reward_ratio.rera_RewardQty as reward_qty,
		collection_type.coty_Image as collection_type,
		reward_ratio.rera_RewardQty_Point as point_qty,
		reward_ratio.rera_RewardQty_Stamp as stamp_qty,
		reward_ratio.coty_CollectionTypeID as collection_id

	  	FROM reward

		LEFT JOIN mi_brand
		ON mi_brand.brand_id = reward.bran_BrandID 

		LEFT JOIN mi_tg_activity
		ON mi_tg_activity.id_activity = reward.rewa_Category

		LEFT JOIN reward_redeem
		ON reward_redeem.rewa_RewardID = reward.rewa_RewardID

		LEFT JOIN reward_ratio
		ON reward_redeem.rede_RewardRedeemID = reward_ratio.rede_RewardRedeemID

		LEFT JOIN collection_type
		ON collection_type.coty_CollectionTypeID = reward_ratio.coty_CollectionTypeID

		WHERE reward_redeem.rede_RewardRedeemID = "'.$id.'"';


$oRes = $oDB->Query($sql)or die(mysql_error());

$asData = array();


while ($axRow = $oRes->FetchRow(DBI_ASSOC)){


	if ($axRow['rewa_Description']=="") { $axRow['rewa_Description']="-"; }

	if ($axRow['rewa_UOM']=="") { $axRow['rewa_UOM']="-"; }

	if ($axRow['rewa_Limit']=="F") { $axRow['rewa_Qty']="Unlimit"; }

	if ($axRow['collection_id']=="0") {	

		$axRow['collection_id'] = $axRow['reward_qty']." / ".$axRow['point_qty']." Points";

	} else {

		$axRow['collection_id'] = $axRow['reward_qty']." / ".$axRow['stamp_qty']." <img src='".$path_upload_collection.$axRow['collection_type']."' style='margin-bottom:8px' width='20' height='20'/>";
	}

	$asData = $axRow;
}



$sql_brand_id = 'SELECT bran_BrandID FROM reward_redeem WHERE rede_RewardRedeemID = "'.$id.'"';

$brand_id = $oDB->QueryOne($sql_brand_id);


$sql_branch = 'SELECT name as txt, branch_id as id 
					FROM mi_branch 
					WHERE brand_id = "'.$brand_id.'" '.$where_branch.'';

$oRes_branch = $oDB->Query($sql_branch)or die(mysql_error());

$total_use = get_redeem_use($id,"","");

$table_redeem .= "<center><br>
					<span style='font-size:16px'><b>Total Redeem &nbsp; : &nbsp; ".$total_use." &nbsp; Times</span></b>
					<br></center>";

$table_redeem .= "<table id='example' class='table table-bordered' style='background-color:white;'>
					<thead><tr class='th_table'>
						<th style='text-align:center;width:15%'>Member</th>
						<th style='text-align:center;'>Profile</th>";

$a=0;

while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)) {

	$table_redeem .= "<th style='text-align:center'>".$axRow_branch['txt']."</th>";

	$branch_id[$a]  = $axRow_branch['id'];

	$a++;
}

if($_SESSION['user_type_id_ses'] != 3) {

	$table_redeem .= "<th style='text-align:center;width:10%'>รวม</th>";
}

$table_redeem .= "</tr></thead><tbody>";



# MEMBER REDEEM

$sql_redeem = 'SELECT DISTINCT

					mb_member.member_id,
					mb_member.facebook_id,
					mb_member.firstname,
					mb_member.lastname,
					mb_member.email,
					mb_member.mobile,
					mb_member.member_image,
					COUNT(reward_redeem_trans.retr_RewardRedeemTransID) AS total

					FROM reward_redeem

					LEFT JOIN reward_redeem_trans
					ON reward_redeem_trans.rede_RewardRedeemID = reward_redeem.rede_RewardRedeemID

					LEFT JOIN mb_member
					ON reward_redeem_trans.memb_MemberID = mb_member.member_id

					WHERE reward_redeem.rede_RewardRedeemID = "'.$id.'"
					GROUP BY reward_redeem_trans.memb_MemberID
					ORDER BY total DESC';

$oRes_redeem = $oDB->Query($sql_redeem)or die(mysql_error());

$check_redeem = $oDB->QueryOne($sql_redeem);


if ($check_redeem) {

	while ($axRow = $oRes_redeem->FetchRow(DBI_ASSOC)) {

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
							
					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email'].'<br>'.$axRow['mobile'];

				} else { $member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['email']; }

			} else {

				if ($axRow['mobile']) {
							
					$member_name = $axRow['firstname'].' '.$axRow['lastname'].'<br>'.$axRow['mobile'];

				} else { $member_name = $axRow['firstname'].' '.$axRow['lastname']; }
			}

		} else {

			if ($axRow['email']) {

				if ($axRow['mobile']) { $member_name = $axRow['member_email'].'<br>'.$axRow['mobile'];

				} else { $member_name = $axRow['email']; }

			} else {

				if ($axRow['mobile']) { $member_name = $axRow['mobile'];

				} else { $member_name = ''; }
			}
		}



		$table_redeem .= "<tr>
							<td style='text-align:center'>".$axRow['member_image']."</td>
							<td>".$member_name."</td>";

		for ($i=0; $i < $a; $i++) {

			$member_priv = get_redeem_use($id,$branch_id[$i],$axRow['member_id']);

			$total_member += $member_priv;

			$table_redeem .= "<td style='text-align:center'>".number_format($member_priv)."</td>";
		}



		if($_SESSION['user_type_id_ses'] != 3) {

			$table_redeem .= "<td style='text-align:center;background-color:#F2F2F2'><b>".number_format($total_member)."</b></td>";
		}

		$table_redeem .= "</tr>";
	}
}

$table_redeem .= "</tbody>";

$table_redeem .="</table>";



$oTmp->assign('table_redeem', $table_redeem);

$oTmp->assign('data', $asData);

$oTmp->assign('is_menu', 'is_analytics');

$oTmp->assign('content_file', 'analytics/top_redeem.htm');

$oTmp->display('layout/template.html');




//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
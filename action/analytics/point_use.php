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


$member_id = $_REQUEST['member'];

$brand_id = $_REQUEST['brand'];

$path_upload_member = $_SESSION['path_upload_member'];



$sql ='SELECT * FROM mb_member WHERE member_id = "'.$member_id.'"';

$oRes = $oDB->Query($sql);

$asData = array();



while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	if ($axRow['date_birth']=="0000-00-00") { $axRow['date_birth'] = "-"; } 
	else { $axRow['date_birth'] = DateOnly($axRow['date_birth']); }

	if ($axRow['nickname']=="") { $axRow['nickname']="-"; }

	if ($axRow['firstname']=="") { $axRow['firstname']="-"; }

	if ($axRow['lastname']=="") { $axRow['lastname']="-"; }

	$asData = $axRow;
}



if($_SESSION['user_branch_id']){

	$where_branch .= ' AND member_motivation_point_trans.brnc_BranchID = "'.$_SESSION['user_branch_id'].'"';
}



$sql_point = 'SELECT

					reward_redeem.rede_Name AS redeem_name,
					reward.rewa_Name AS reward_name,
					reward.rewa_Image AS reward_image,
					reward.rewa_ImagePath,
					reward_redeem_trans.rera_RewardQty_Point AS redeem_point,
					reward_redeem_trans.retr_RedeemDate AS redeem_date,
					mi_branch.name AS branch_name

					FROM reward_redeem_trans

					LEFT JOIN mb_member_register
					ON mb_member_register.member_register_id = reward_redeem_trans.mere_MemberRegisterID

					LEFT JOIN reward_redeem
					ON reward_redeem_trans.rede_RewardRedeemID = reward_redeem.rede_RewardRedeemID

					LEFT JOIN reward
					ON reward.rewa_RewardID = reward_redeem.rewa_RewardID

					LEFT JOIN mi_brand
					ON mi_brand.brand_id = reward.bran_BrandID

					LEFT JOIN mi_branch
					ON reward_redeem_trans.brnc_BranchID = mi_branch.branch_id

					WHERE mb_member_register.member_id = '.$member_id.'
					AND mi_brand.brand_id = '.$brand_id.'
					AND reward_redeem_trans.coty_CollectionTypeID = ""
					'.$where_branch.'

					ORDER BY reward_redeem_trans.retr_RedeemDate DESC';

$oRes_point = $oDB->Query($sql_point);

$check_point = $oDB->QueryOne($sql_point);

$total_sql = "SELECT SUM(reward_redeem_trans.rera_RewardQty_Point)

					FROM reward_redeem_trans

					LEFT JOIN mb_member_register
					ON mb_member_register.member_register_id = reward_redeem_trans.mere_MemberRegisterID

					LEFT JOIN reward_redeem
					ON reward_redeem_trans.rede_RewardRedeemID = reward_redeem.rede_RewardRedeemID

					LEFT JOIN mi_brand
					ON mi_brand.brand_id = reward_redeem.bran_BrandID

					WHERE mb_member_register.member_id ='".$member_id."'
					AND reward_redeem_trans.rede_AutoRedeem='F'
					AND mi_brand.brand_id ='".$brand_id."'"
					.$where_branch."";

$total_use = $oDB->QueryOne($total_sql);


$table_member = "<center><br>
					<span style='font-size:16px'><b>Total Point Use &nbsp; : &nbsp; ".number_format($total_use)."</span></b>
				<br></center>";

$table_member .= "<table id='example' class='table table-bordered' style='background-color:white;' >
					<thead>
						<tr class='th_table'>
							<th style='text-align:center'><b>Redeems Date</b></th>
							<th style='text-align:center'><b>Rewards</b></th>
							<th style='text-align:center'><b>Redeems</b></th>
							<th style='text-align:center'><b>Branch</b></th>
							<th style='text-align:center'><b>Auto Redeems</b></th>
							<th style='text-align:center'><b>Point Use</b></th>
						</tr>
					</thead>";

if ($check_point) {

	$table_member .= "<tbody>";

	while ($axRow = $oRes_point->FetchRow(DBI_ASSOC)){

		# REWARDS IMAGE

		if($axRow['reward_image']!=''){

			$axRow['reward_image'] = '<img src="../../upload/'.$axRow['rewa_ImagePath'].$axRow['reward_image'].'" width="50px" height="50px"></a>';

		} else {

			$axRow['card_imreward_imageage'] = '<img src="../../images/400x400.png" width="50px" height="50px">';
		}



		# AUTO REDEEMS

		if ($axRow['auto_redeem']!='F') { 

			$axRow['redeem_stamp'] = 0; 

			$auto_redeem = '<span style="color:green"><b>Yes</b></span>';

		} else {

			$auto_redeem = '<span style="color:red"><b>No</b></span>';
		}



		$table_member .= "<tr><td style='text-align:center'>".DateOnly($axRow['redeem_date'])."</td>
							<td style='text-align:center'>".$axRow['reward_image']."<br>
								<span style='font-size:11px'>".$axRow['reward_name']."</span></td>
							<td>".$axRow['redeem_name']."</td>
							<td>".$axRow['branch_name']."</td>
							<td style='text-align:center'>".$auto_redeem."</td>
							<td style='text-align:center;background-color:#F2F2F2'><b>".number_format($axRow['redeem_point'])."</b></td>
						</tr>";
	}

	$table_member .= "</tbody>";
}

$table_member .= "</table>";






$as_name_title_type = list_type_master_value($oDB,'name_title_type',$axRow['name_title_type']);

if ($as_name_title_type=="") { $as_name_title_type = "-"; }

$oTmp->assign('name_title_type', $as_name_title_type);



$oTmp->assign('data', $asData);

$oTmp->assign('table_member', $table_member);

$oTmp->assign('is_menu', 'is_analytics');

$oTmp->assign('content_file', 'analytics/point_use.htm');

$oTmp->display('layout/template.html');




//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
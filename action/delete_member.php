<?php

header('Content-Type:text/html; charset=UTF-8');

//========================================//

ini_set("display_errors",1);

error_reporting(1);

//========================================//

include('../include/common_login.php');

include('../lib/pagination_class.php');

include('../lib/function_normal.php');

include('../include/common_check.php');

//========================================//


$oTmp = new TemplateEngine();

$oDB = new DBI();

if ($bDebug) {

	$oErr = new Tracker();

	$oDB->SetTracker($oErr);

}

$id = $_REQUEST['member_id'];

if ($id) {

	$sql_regis = 'SELECT member_register_id
					FROM mb_member_register
					WHERE member_id ='.$id;
	$regis = $oDB->Query($sql_regis);

	while ($axRow = $regis->FetchRow(DBI_ASSOC)){
		$regis_id .= $axRow['member_register_id'].",";
	}

	$str_regis = strlen($regis_id);
	$regis_id = substr($regis_id,0,$str_regis-1);


	if ($regis_id) {
		
		$sql_collection = 'DELETE FROM member_collection WHERE mere_MemberRegisterID IN ('.$regis_id.')';
		$oDB->QueryOne($sql_collection);
		
		$sql_point_trans = 'DELETE FROM member_motivation_point_trans WHERE mere_MemberRegisterID IN ('.$regis_id.')';
		$oDB->QueryOne($sql_point_trans);
		
		$sql_stamp_trans = 'DELETE FROM member_motivation_stamp_trans WHERE mere_MemberRegisterID IN ('.$regis_id.')';
		$oDB->QueryOne($sql_stamp_trans);
	}

	//==============================================================


	$sql_member = 'DELETE FROM mb_member WHERE member_id='.$id;
	$sql_register = 'DELETE FROM mb_member_register WHERE member_id='.$id;
	$sql_amount = 'DELETE FROM mb_member_register_amount WHERE member_id='.$id;
	$sql_privilege = 'DELETE FROM mi_member_use_privilege WHERE member_id='.$id;
	$sql_share = 'DELETE FROM mb_member_share WHERE member_id='.$id;
	$sql_activity = 'DELETE FROM member_activity WHERE memb_MemberID='.$id;
	$sql_activity_trans = 'DELETE FROM member_activity_trans WHERE memb_MemberID='.$id;
	$sql_coupon = 'DELETE FROM member_coupon WHERE memb_MemberID='.$id;
	$sql_coupon_trans = 'DELETE FROM member_coupon_trans WHERE memb_MemberID='.$id;
	$sql_privilege = 'DELETE FROM member_privilege WHERE memb_MemberID='.$id;
	$sql_privilege_trans = 'DELETE FROM member_privilege_trans WHERE memb_MemberID='.$id;
	$sql_share_app = 'DELETE FROM member_share_app WHERE shap_MemberID='.$id;
	$sql_share_card = 'DELETE FROM member_share_card WHERE shca_MemberID='.$id;
	$sql_share_privilege = 'DELETE FROM member_share_privilege WHERE shpr_MemberID='.$id;
	$sql_redeem = 'DELETE FROM reward_redeem_trans WHERE memb_MemberID='.$id;
	$sql_hcoupon = 'DELETE FROM hilight_coupon_trans WHERE memb_MemberID='.$id;
	$sql_trans = 'DELETE FROM member_transaction_h WHERE memb_MemberID='.$id;
	$sql_identify = 'DELETE FROM member_identify WHERE memb_MemberID='.$id;


	$oDB->QueryOne($sql_member);
	$oDB->QueryOne($sql_register);
	$oDB->QueryOne($sql_amount);
	$oDB->QueryOne($sql_privilege);
	$oDB->QueryOne($sql_share);
	$oDB->QueryOne($sql_activity);
	$oDB->QueryOne($sql_activity_trans);
	$oDB->QueryOne($sql_coupon);
	$oDB->QueryOne($sql_coupon_trans);
	$oDB->QueryOne($sql_privilege);
	$oDB->QueryOne($sql_privilege_trans);
	$oDB->QueryOne($sql_share_app);
	$oDB->QueryOne($sql_share_card);
	$oDB->QueryOne($sql_share_privilege);
	$oDB->QueryOne($sql_redeem);
	$oDB->QueryOne($sql_hcoupon);
	$oDB->QueryOne($sql_trans);
	$oDB->QueryOne($sql_identify);

	echo "<script>alert('complete');</script>";
}



####################################################################




$regis = $_REQUEST['regis_id'];

if ($regis) {

	$sql_data_regis = 'SELECT card_id,member_id
					FROM mb_member_register
					WHERE member_register_id ='.$regis;
	$oRes_regis = $oDB->Query($sql_data_regis);

	while ($axRowregis = $oRes_regis->FetchRow(DBI_ASSOC)){
		$member_id = $axRowregis['member_id'];
		$card_id = $axRowregis['card_id'];
	}

	//========================================================
		
		$sql_trans = 'DELETE FROM member_transaction_h WHERE memb_MemberID='.$member_id.' AND card_CardID ='.$card_id;
		$oDB->QueryOne($sql_trans);

	//========================================================
		
		$sql_privilege = 'DELETE FROM member_privilege WHERE memb_MemberID='.$member_id.' AND card_CardID ='.$card_id;
		$oDB->QueryOne($sql_privilege);
		
	//=========================================================
		
		$sql_coupon = 'DELETE FROM member_coupon WHERE memb_MemberID='.$member_id.' AND card_CardID ='.$card_id;
		$oDB->QueryOne($sql_coupon);

	//=========================================================
		
		$sql_activity = 'DELETE FROM member_activity WHERE memb_MemberID='.$member_id.' AND card_CardID ='.$card_id;
		$oDB->QueryOne($sql_activity);
	
	//=========================================================


	$sql_register = "DELETE FROM mb_member_register WHERE member_register_id=".$regis;
	$sql_amount = "DELETE FROM mb_member_register_amount WHERE member_id=".$member_id." AND card_id=".$card_id;
	$sql_privilege = "DELETE FROM mi_member_use_privilege WHERE member_id=".$member_id." AND card_id=".$card_id;
	$sql_privilege_trans = "DELETE FROM member_privilege_trans WHERE memb_MemberID=".$member_id." AND card_CardID=".$card_id;
	$sql_activity_trans = "DELETE FROM member_activity_trans WHERE memb_MemberID=".$member_id." AND card_CardID=".$card_id;
	$sql_coupon_trans = "DELETE FROM member_coupon_trans WHERE memb_MemberID=".$member_id." AND card_CardID=".$card_id;
	$sql_point = "DELETE FROM member_motivation_point_trans WHERE mere_MemberRegisterID=".$regis;
	$sql_stamp = "DELETE FROM member_motivation_stamp_trans WHERE mere_MemberRegisterID=".$regis;
	$sql_collection = "DELETE FROM member_collection WHERE mere_MemberRegisterID=".$regis;
	$sql_identify = "DELETE FROM member_identify WHERE mere_MemberRegisterID=".$regis;


	$oDB->QueryOne($sql_register);
	$oDB->QueryOne($sql_amount);
	$oDB->QueryOne($sql_privilege);
	$oDB->QueryOne($sql_privilege_trans);
	$oDB->QueryOne($sql_activity_trans);
	$oDB->QueryOne($sql_coupon_trans);
	$oDB->QueryOne($sql_point);
	$oDB->QueryOne($sql_stamp);
	$oDB->QueryOne($sql_collection);
	$oDB->QueryOne($sql_identify);

	echo "<script>alert('complete');</script>";
}



####################################################################

## SHOW DATA ##

echo "<table><tr valign='top'><td width='400px'>";

$data_member = "<b>ID &nbsp; - &nbsp; Email &nbsp; (Mobile) </b><br>";

$sql_data_member = 'SELECT member_id,email,mobile FROM mb_member ORDER BY member_id DESC';
$oRes_member = $oDB->Query($sql_data_member);

while ($axRowmember = $oRes_member->FetchRow(DBI_ASSOC)){

	$data_member .= "<b>".$axRowmember['member_id']." &nbsp; - &nbsp; ".$axRowmember['email']." (".$axRowmember['mobile'].")</b><br>";

	$sql_data_card = 'SELECT mi_card.name,
							mb_member_register.member_register_id,
							mi_brand.name AS brand_name
					FROM mb_member_register
					LEFT JOIN mi_card
					ON mb_member_register.card_id = mi_card.card_id
					LEFT JOIN mi_brand
					ON mi_brand.brand_id = mi_card.brand_id
					WHERE mb_member_register.member_id ='.$axRowmember['member_id'];

	$oRes_card = $oDB->Query($sql_data_card);
	while ($axRowcard = $oRes_card->FetchRow(DBI_ASSOC)){

		$data_member .= " - ".$axRowcard['member_register_id']." (".$axRowcard['name']." / ".$axRowcard['brand_name'].")<br>";

	}

	$data_member .= "<br>";
}

echo $data_member;

echo "</td>";

$oTmp->assign('data_member', $data_member);

//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());

}

//========================================//

?>

<td>

<h4>DELETE DATA MEMBER</h4>

<form enctype="multipart/form-data" method="post" action="delete_member.php" name="frm1" id="frm1">
Member ID : <input id="member_id" name="member_id" class="form-control text-md" type="text" >
<input type="submit" value="submit"><br><br>

</form>


<h4>DELETE DATA MEMBER REGISTER CARD</h4>

<form enctype="multipart/form-data" method="post" action="delete_member.php" name="frm2" id="frm2">
REGISTER ID : <input id="regis_id" name="regis_id" class="form-control text-md" type="text" >
<input type="submit" value="submit"><br><br>

</form>

</td>
</tr>
</table>
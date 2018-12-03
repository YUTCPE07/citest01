<?php

header('Content-Type:text/html; charset=UTF-8');

//========================================//

ini_set("display_errors",1);
error_reporting(1);

//========================================//

include('../../include/common.php');
include('../../lib/function_normal.php');
include('../../include/common_check.php');
include('../../omise/lib/Omise.php');
require_once('../../include/connect.php');

//========================================//

$oTmp = new TemplateEngine();

$oDB = new DBI();

if ($bDebug) {

	$oErr = new Tracker();
	$oDB->SetTracker($oErr);
}

//========================================//

if ($_SESSION['role_action']['received_lists']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$today = date("Y-m-d");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$path_upload_member = $_SESSION['path_upload_member'];



# SEARCH

$transfer_status = $_REQUEST['transfer_status'];

$where_transfer_c = '';

$where_transfer_p = '';



if ($transfer_status == "All" || !$transfer_status) { $where_transfer_c = ''; $where_transfer_p = ''; } 

else { 

	$where_transfer_c = ' AND mb_member_register.payr_TransferStatus="'.$transfer_status.'"'; 
	$where_transfer_p = ' AND hilight_coupon_buy.hcbu_TransferStatus="'.$transfer_status.'"'; 
}



$brand_id = "";



for($k=0 ; $k<count($_POST["brand_id"]) ; $k++){

	if(trim($_POST["brand_id"][$k]) != ""){

		if ($_POST["brand_id"][$k]==0) {

			$brand_id = 0;

		} else {

			if ($k==count($_POST["brand_id"])-1) {	$brand_id .= $_POST["brand_id"][$k];	} 

			else {	$brand_id .= $_POST["brand_id"][$k].",";	}
		}
	}
}



if ($brand_id=="" || $brand_id==0) {	$where_search = "";	} 

else {	$where_search = "AND mi_brand.brand_id IN (".$brand_id.")";	}



# BRAND LOGIN


if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND mi_brand.brand_id = "'.$_SESSION['user_brand_id'].'"';
}



$sql ='SELECT 
		mb_member_register.receipt_no AS ReceiptNo,
		SUM(mb_member_register.total_amt) AS TotalAmt,
		mb_member_register.date_create AS DateCreated,
		mb_member_register.status AS Status,
		mb_member_register.member_register_id AS BuyID,
		mb_member_register.payr_TransferStatus AS TransferStatus,
		COUNT(mb_member_register.receipt_no) AS ItemQty,
		"Card" AS ItemType,
		mi_card.card_id AS ItemID,
		mi_card.name AS ItemName,
		mi_card.image AS ItemImage,
		mi_card.path_image AS ItemPath,
		mb_member.member_id AS MemberID,
		mb_member.member_image AS MemberImage,
		mb_member.firstname AS MemberFirstName,
		mb_member.lastname AS MemberLastName,
		mb_member.email AS MemberEmail,
		mb_member.facebook_id AS MemberFacebook,
		mb_member.mobile AS MemberMobile,
		mi_token_type.name AS TokenName,
		mi_brand.logo_image AS BrandLogo,
		mi_brand.path_logo AS BrandPath,
		mi_brand.name AS BrandName

		FROM mb_member_register

		LEFT JOIN mi_card
		ON mi_card.card_id = mb_member_register.card_id

		LEFT JOIN mb_member
		ON mb_member.member_id = mb_member_register.member_id

		LEFT JOIN mi_brand
		ON mi_brand.brand_id = mb_member_register.bran_BrandID

		LEFT JOIN mi_token_type
		ON mb_member_register.token_type_id = mi_token_type.token_type_id

		WHERE mi_card.card_id != "0"
		AND mb_member_register.receipt_no != ""

		'.$where_brand.'
		'.$where_search.'
		'.$where_transfer_c.'

		GROUP BY mb_member_register.receipt_no

		UNION

		SELECT 
		hilight_coupon_buy.hcbu_ReceiptNo AS ReceiptNo,
		SUM(hilight_coupon_buy.hcbu_TotalAmount) AS TotalAmt,
		hilight_coupon_buy.hcbu_CreatedDate AS DateCreated,
		hilight_coupon_buy.hcbu_Status AS Status,
		hilight_coupon_buy.hcbu_HilightCouponBuyID AS BuyID,
		hilight_coupon_buy.hcbu_TransferStatus AS TransferStatus,
		COUNT(hilight_coupon_buy.hcbu_ReceiptNo) AS ItemQty,
		"Shop" AS ItemType,
		hilight_coupon.coup_CouponID AS ItemID,
		hilight_coupon.coup_Name AS ItemName,
		hilight_coupon.coup_Image AS ItemImage,
		hilight_coupon.coup_ImagePath AS ItemPath,
		mb_member.member_id AS MemberID,
		mb_member.member_image AS MemberImage,
		mb_member.firstname AS MemberFirstName,
		mb_member.lastname AS MemberLastName,
		mb_member.email AS MemberEmail,
		mb_member.facebook_id AS MemberFacebook,
		mb_member.mobile AS MemberMobile,
		mi_token_type.name AS TokenName,
		mi_brand.logo_image AS BrandLogo,
		mi_brand.path_logo AS BrandPath,
		mi_brand.name AS BrandName

		FROM hilight_coupon_buy

		LEFT JOIN hilight_coupon
		ON hilight_coupon.coup_CouponID = hilight_coupon_buy.hico_HilightCouponID

		LEFT JOIN mb_member
		ON mb_member.member_id = hilight_coupon_buy.memb_MemberID

		LEFT JOIN mi_brand
		ON mi_brand.brand_id = hilight_coupon_buy.bran_BrandID

		LEFT JOIN mi_token_type
		ON hilight_coupon_buy.token_type_id = mi_token_type.token_type_id

		WHERE hilight_coupon_buy.hico_HilightCouponID != "0"
		AND hilight_coupon_buy.hcbu_ReceiptNo != ""

		'.$where_brand.'
		'.$where_search.'
		'.$where_transfer_p.'

		GROUP BY hilight_coupon_buy.hcbu_ReceiptNo
		
		ORDER BY DateCreated DESC';



if($Act == 'delete' && $id != '') {

	# UPDATE DELETE

 	$do_sql_register = "UPDATE mb_member_register SET flag_del='T', payr_UpdatedDate='".$time_insert."', payr_UpdatedBy='".$_SESSION['UID']."', payr_TransferStatus='Cancel' WHERE member_register_id='".$id."'";

 	$oDB->QueryOne($do_sql_register);

 	echo '<script>window.location.href="receipt_lists.php";</script>';
}



$oRes = $oDB->Query($sql);

$i=0;

$asData = array();

$data_table = '';

$total_amount = 0;



while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$i++;

	if ($axRow['ReceiptNo'] != "") {

		# CHECK TRANSACTION USE

		// $transaction_use = "No";

		// if ($axRow['ItemType'] == 'Card') {

		// 	$sql_use_acti = 'SELECT * FROM member_activity_trans WHERE memb_MemberID='.$axRow['MemberID'].' AND card_CardID='.$axRow['ItemID'];

		// 	$data_use_acti = $oDB->QueryOne($sql_use_acti);

		// 	if ($data_use_acti) { $transaction_use = "Yes"; }

		// 	$sql_use_coup = 'SELECT * FROM member_coupon_trans WHERE memb_MemberID='.$axRow['MemberID'].' AND card_CardID='.$axRow['ItemID'];

		// 	$data_use_coup = $oDB->QueryOne($sql_use_coup);

		// 	if ($data_use_coup) { $transaction_use = "Yes"; }

		// 	$sql_use_priv = 'SELECT * FROM member_privilege_trans WHERE memb_MemberID='.$axRow['MemberID'].' AND card_CardID='.$axRow['ItemID'];

		// 	$data_use_priv = $oDB->QueryOne($sql_use_priv);

		// 	if ($data_use_priv) { $transaction_use = "Yes"; }
		// }


		# MEMBER

		$member_name = '';

		if ($axRow['MemberFirstName'] || $axRow['MemberLastName']) {

			if ($axRow['MemberEmail']) {

				if ($axRow['MemberMobile']) {
							
					$member_name = $axRow['MemberFirstName'].' '.$axRow['MemberLastName'].'<br>'.$axRow['MemberEmail'].'<br>'.$axRow['MemberMobile'];

				} else { $member_name = $axRow['MemberFirstName'].' '.$axRow['MemberLastName'].'<br>'.$axRow['MemberEmail']; }

			} else {

				if ($axRow['MemberMobile']) {
							
					$member_name = $axRow['MemberFirstName'].' '.$axRow['MemberLastName'].'<br>'.$axRow['MemberMobile'];

				} else { $member_name = $axRow['MemberFirstName'].' '.$axRow['MemberLastName']; }
			}

		} else {

			if ($axRow['MemberEmail']) {

				if ($axRow['MemberMobile']) { $member_name = $axRow['MemberEmail'].'<br>'.$axRow['MemberMobile'];

				} else { $member_name = $axRow['MemberEmail']; }

			} else {

				if ($axRow['MemberMobile']) { $member_name = $axRow['MemberMobile'];

				} else { $member_name = ''; }
			}
		}

		if($axRow['MemberImage']!='' && $axRow['MemberImage']!='https://www.memberin.com/images/user.png'){

			$member = '<img src="'.$path_upload_member.$axRow['MemberImage'].'" width="150" height="150" class="img-circle image_border"/>';

			$axRow['MemberImage'] = '<img src="'.$path_upload_member.$axRow['MemberImage'].'" width="50" height="50" class="img-circle image_border"/>';

		} else if ($axRow['MemberFacebook']!='') {

			$member = '<img src="http://graph.facebook.com/'.$axRow['MemberFacebook'].'/picture?type=large" width="150" height="150" class="img-circle image_border"/>';

			$axRow['MemberImage'] = '<img src="http://graph.facebook.com/'.$axRow['MemberFacebook'].'/picture?type=large" width="50" height="50" class="img-circle image_border"/>';

		} else {

			$member = '<img src="../../images/user.png" width="150" height="150" class="img-circle image_border"/>';

			$axRow['MemberImage'] = '<img src="../../images/user.png" width="50" height="50" class="img-circle image_border"/>';
		}


		# ITEM IMAGE

		if($axRow['ItemImage']!=''){	

			$item = '<img src="../../upload/'.$axRow['ItemPath'].$axRow['ItemImage'].'" height="150"  class="image_border';

			$axRow['ItemImage'] = '<img src="../../upload/'.$axRow['ItemPath'].$axRow['ItemImage'].'" height="50"  class="image_border';

		} else {

			$item = '<img src="../../images/card_privilege.jpg" height="150" class="image_border';	

			$axRow['ItemImage'] = '<img src="../../images/card_privilege.jpg" height="50" class="image_border';	
		}

		if ($axRow['ItemType'] == 'Card') { 

			$item .= ' img-rounded '; 

			$axRow['ItemImage'] .= ' img-rounded '; 
		}

		$axRow['ItemImage'] .= '"/>';
		$item .= '"/>';



		# LOGO BRAND

		if($axRow['BrandLogo']!=''){

			$axRow['BrandLogo'] = '<img src="../../upload/'.$axRow['BrandPath'].$axRow['BrandLogo'].'" class="image_border" width="50" height="50"/>';

		} else {

			$axRow['BrandLogo'] = '<img src="../../images/400x400.png" class="image_border" width="50" height="50"/>';
		}


		# TOTAL AMOUNT

		$total_amount += $axRow['TotalAmt'];


		# DATATABLE

		$data_table .= '<tr>
							<td >'.$i.'</td>
							<td >'.$axRow['ReceiptNo'].'</td>
							<td>'.DateTime($axRow['DateCreated']).'</td>
							<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['BrandLogo'].'</a><br>
								<span style="font-size:11px;">'.$axRow['BrandName'].'</span></td>
							<td style="text-align:center">'.$axRow['MemberImage'].'</td>
							<td>'.$member_name.'</td>
							<td style="text-align:center"><a href="../card/card.php">'.$axRow['ItemImage'].'</a><br>
								<span style="font-size:11px;">'.$axRow['ItemName'].'</span></td>
							<td >'.$axRow['ItemType'].'</td>
							<td style="text-align:center">'.number_format($axRow['ItemQty']).'</td>
							<td style="text-align:right">'.number_format($axRow['TotalAmt'],2).' ฿</td>';

		if ($_SESSION['user_type_id_ses']==1) {

			$data_table .= '<td >'.$axRow['TokenName'].'</td>';
		}

		if ($axRow['Status'] == "Cancel") { $color = "red"; }
		else { $color = "black"; }

		$data_table .= '	<td style="text-align:center"><span style="color:'.$color.'">'.$axRow['Status'].'</span></td>
							<td style="text-align:center"><span style="color:'.$color.'">'.$axRow['TransferStatus'].'</span></td>';

		if ($axRow['ReceiptNo'] != "") {

			$data_table .= '<td style="text-align:center"><button type="button" style="cursor:pointer" class="btn btn-default btn-sm" onClick="openReceipt(\''.$axRow['ReceiptNo'].'\',\''.$axRow['ItemType'].'\')"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></td>';

		} else {

			$data_table .= '<td style="text-align:center"><button type="button" style="cursor:pointer" class="btn btn-default btn-sm" disabled><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></td>';
		}

		$data_table .= '</tr>';
	}
}




#  brand dropdownlist

$sql_brand ='SELECT brand_id, name FROM mi_brand WHERE flag_del!=1 ORDER BY name';

$oRes_brand = $oDB->Query($sql_brand);

$select_brand = '';

$selected = "";

if ($brand_id==0) {	$selected = "selected";	}

else {	$selected = "";	}

$select_brand .= '<option value="0" '.$selected.'>All</option>';

$selected = "";

while ($axRow = $oRes_brand->FetchRow(DBI_ASSOC)){

	for($j=0 ; $j<count($_POST["brand_id"]) ; $j++){

		if ($axRow['brand_id']==$_POST["brand_id"][$j]) {	$selected = "selected";	}

	}

	$select_brand .= '<option value="'.$axRow['brand_id'].'" '.$selected.'>'.$axRow['name'].'</option>';

	$selected = "";

}

$oTmp->assign('select_brand', $select_brand);





#  transfer_status dropdownlist

$select_transfer = '';

$select_transfer .= '<option value="All"';

	if ($transfer_status == "All" || !$transfer_status) {	$select_transfer .= ' selected';	}

$select_transfer .= '>All</option>';

$select_transfer .=	'<option value="Wait"';

	if ($transfer_status == "Wait") {	$select_transfer .= ' selected';	}

$select_transfer .= '>Wait</option>';

$select_transfer .=	'<option value="Request"';

	if ($transfer_status == "Request") {	$select_transfer .= ' selected';	}

$select_transfer .= '>Request</option>';

$select_transfer .=	'<option value="Transfer"';

	if ($transfer_status == "Transfer") {	$select_transfer .= ' selected';	}

$select_transfer .= '>Transfer</option>';

$oTmp->assign('select_transfer', $select_transfer);




$oTmp->assign('total_amount', number_format($total_amount, 2, '.', ','));

$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_financial');

$oTmp->assign('content_file','financial/receipt_lists.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
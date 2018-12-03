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

if ($_SESSION['role_action']['tax_invoice']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$today = date("Y-m-d");

$transfer = $_REQUEST['transfer'];

$id = $_REQUEST['id'];

$Act = $_REQUEST['act'];




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



if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND mi_brand.brand_id = "'.$_SESSION['user_brand_id'].'"';
}




$sql ='SELECT 

		invoice_h.*,
		mi_brand.logo_image,
		mi_brand.path_logo,
		mi_brand.name AS brand_name

		FROM invoice_h

		LEFT JOIN mi_brand
		ON mi_brand.brand_id = invoice_h.bran_BrandID

		WHERE invoice_h.invh_InvoiceReceipt!=""
		'.$where_brand.'
		'.$where_search.'

		ORDER BY invoice_h.invh_InvoiceHID DESC' ;


$oRes = $oDB->Query($sql);

$i=0;

$asData = array();

$data_table = '';

$total = 0;

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$data_invoice = "";

	$j = 0;

	$sql_invoiceb ='SELECT invoice_b.* FROM invoice_b WHERE invh_InvoiceHID='.$axRow['invh_InvoiceHID'];

	$oRes_invoiceb = $oDB->Query($sql_invoiceb);

	$total_receipt = 0;

	$total_member = 0;

	$total_mi = 0;

	$total_payment = 0;

	$total_service = 0;

	$total_vat = 0;

	$total_amount = 0;

	$transfer_fee = 0;

	while ($axRow_invoiceb = $oRes_invoiceb->FetchRow(DBI_ASSOC)){

		$transfer_fee += $axRow_invoiceb['invb_TotalAmount'];

		$j++;

		if ($axRow_invoiceb['invb_Type'] == 'Card') {

			$sql_member = 'SELECT mb_member.firstname,
								mb_member.lastname,
								mb_member.mobile,
								mb_member.email,
								mi_card.name AS item_name,
								mi_token_type.name AS TokenName,
								mb_member_register.total_amt AS total_amt
							FROM mb_member
							LEFT JOIN mb_member_register
							ON mb_member.member_id = mb_member_register.member_id
							LEFT JOIN mi_card
							ON mi_card.card_id = mb_member_register.card_id
							LEFT JOIN mi_token_type
							ON mb_member_register.token_type_id = mi_token_type.token_type_id
							WHERE mb_member_register.receipt_no="'.$axRow_invoiceb['invb_ReceiptNo'].'"';

		} else {

			$sql_member = 'SELECT mb_member.firstname,
								mb_member.lastname,
								mb_member.mobile,
								mb_member.email,
								hilight_coupon.coup_Name AS item_name,
								mi_token_type.name AS TokenName,
								hilight_coupon_buy.hcbu_TotalAmount AS total_amt
							FROM mb_member
							LEFT JOIN hilight_coupon_buy
							ON mb_member.member_id = hilight_coupon_buy.memb_MemberID
							LEFT JOIN hilight_coupon
							ON hilight_coupon.coup_CouponID = hilight_coupon_buy.hico_HilightCouponID
							LEFT JOIN mi_token_type
							ON hilight_coupon_buy.token_type_id = mi_token_type.token_type_id
							WHERE hilight_coupon_buy.hcbu_ReceiptNo="'.$axRow_invoiceb['invb_ReceiptNo'].'"';
		}

		$oRes_member = $oDB->Query($sql_member);
		$member = $oRes_member->FetchRow(DBI_ASSOC);

		$member_name = '';

		if ($member['firstname'].' '.$member['lastname']) {

			if ($member['email']) {

				if ($member['mobile']) {
							
					$member_name = $member['firstname'].' '.$member['lastname'].'<br>'.$member['email'].'<br>'.$member['mobile'];

				} else { $member_name = $member['firstname'].' '.$member['lastname'].'<br>'.$member['email']; }

			} else {

				if ($member['mobile']) {
							
					$member_name = $member['firstname'].' '.$member['lastname'].'<br>'.$member['mobile'];

				} else { $member_name = $member['firstname'].' '.$member['lastname']; }
			}

		} else {

			if ($member['email']) {

				if ($member['mobile']) { $member_name = $member['email'].'<br>'.$member['mobile'];

				} else { $member_name = $member['email']; }

			} else {

				if ($member['mobile']) { $member_name = $member['mobile'];

				} else { $member_name = ''; }
			}
		}

		$strNewDate = date("Y-m-d", strtotime("-1 day", strtotime($axRow['invh_CreatedDate'])));

		$charge_percent = ($axRow_invoiceb['invb_MI']*100)/$member['total_amt'];

		$data_invoice .= '<tr>
							<td>'.$j.'</td>
							<td>'.$member_name.'</td>
							<td>'.$member['item_name'].'</td>
							<td>'.$axRow_invoiceb['invb_Type'].'</td>
							<td style="text-align:right">'.number_format($member['total_amt'],2).' ฿</td>
							<td style="text-align:right">'.number_format($axRow_invoiceb['invb_MemberFee'],2).' ฿</td>
							<td style="text-align:center">'.$member['TokenName'].'</td>
							<td style="text-align:right">'.$charge_percent.' %</td>
							<td style="text-align:right">'.number_format($axRow_invoiceb['invb_MI'],2).' ฿</td>
							<td style="text-align:right">'.number_format($axRow_invoiceb['invb_ServiceCharge'],2).' ฿</td>
							<td style="text-align:right">'.number_format($axRow_invoiceb['invb_Vat'],2).' ฿</td>
							<td style="text-align:right">'.number_format($axRow_invoiceb['invb_TotalAmount'],2).' ฿</td>
						</tr>';

		$total_receipt += $member['total_amt'];

		$total_member += $axRow_invoiceb['invb_MemberFee'];

		$total_payment += $axRow_invoiceb['invb_MI'];

		$total_service += $axRow_invoiceb['invb_ServiceCharge'];

		$total_vat += $axRow_invoiceb['invb_Vat'];

		$total_amount += $axRow_invoiceb['invb_TotalAmount'];
	}



	$data_invoice .= '<tr>
						<td colspan="4" style="text-align:center"><b>Total</b></td>
						<td style="text-align:right"><b>'.number_format($total_receipt,2).' ฿</b></td>
						<td style="text-align:right"><b>'.number_format($total_member,2).' ฿</b></td>
						<td colspan="2"></td>
						<td style="text-align:right"><b>'.number_format($total_payment,2).' ฿</b></td>
						<td style="text-align:right"><b>'.number_format($total_service,2).' ฿</b></td>
						<td style="text-align:right"><b>'.number_format($total_vat,2).' ฿</b></td>
						<td style="text-align:right"><b>'.number_format($total_amount,2).' ฿</b></td>
					</tr>';
	$i++;

	if($axRow['logo_image']!=''){

		$axRow['logo_image'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="50" height="50"/>';

	} else {

		$axRow['logo_image'] = '<img src="../../images/400x400.png" class="image_border" width="50" height="50"/>';
	}




	# DATA TABLE =================================================================

	$total += $transfer_fee;

	$strNewDate = date("Y-m-d", strtotime("-1 day", strtotime($axRow['invh_CreatedDate'])));

	$data_table .= '<tr>
						<td >'.$i.'</td>
						<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['logo_image'].'</a><br>
							<span style="font-size:11px;">'.$axRow['brand_name'].'</td>
						<td style="text-align:center">'.DateOnly($strNewDate).'</td>
						<td>'.$axRow['invh_InvoiceReceipt'].'</td>
						<td style="text-align:right">'.number_format($transfer_fee,2).' ฿</td>
						<td style="text-align:center">'.$axRow['invh_Status'].'</td>
						<td style="text-align:center">'.DateTime($axRow['invh_UpdatedDate']).'</td>
						<td style="text-align:center">';

	if ($_SESSION['user_type_id_ses']==1) {

		$data_table .= '	<button type="button" style="cursor:pointer" class="btn btn-default btn-sm" onClick="openOriginalReceipt(\''.$axRow['invh_InvoiceHID'].'\')" title="Original/ต้นฉบับ"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button><br><br>';
	}

	$data_table .= '		<button type="button" style="cursor:pointer" class="btn btn-default btn-sm" onClick="openCopyReceipt(\''.$axRow['invh_InvoiceHID'].'\')" title="Copy/สำเนา"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></td>
						<td style="text-align:center"><button type="button" style="cursor:pointer" class="btn btn-default btn-sm" data-toggle="modal" data-target="#TaxDetail'.$axRow['invh_InvoiceHID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
						<div class="modal fade" id="TaxDetail'.$axRow['invh_InvoiceHID'].'" tabindex="-1" role="dialog" aria-labelledby="TaxDataLabel">
							<div class="modal-dialog" role="document" style="width:80%">
								<div class="modal-content">
								    <div class="modal-body" align="left">
								        <span style="font-size:16px"><b>
								        	<span style="float:left">
								        		Date : '.DateOnly($strNewDate).'<br>
								        		Invoice : '.$axRow['invh_InvoiceReceipt'].'<br></span>
								        	<span style="float:right">'.$axRow['brand_name'].'<br></span>
								        </b></span>
								        <br><br>
								        <hr>
								        <center><div id="parent" class="table-responsive" style="overflow-x: scroll;">
								        <table class="table table-striped table-bordered myPopupData">
								            <thead><tr class="th_table" style="text-align:center">
								            	<th>No.</th>
								            	<th>Customer</th>
								            	<th>Item</th>
								            	<th>Type</th>
								            	<th>Receipt Amount</th>
								            	<th>Service Fee</th>
								            	<th>Payment Type</th>
								            	<th>% Fee</th>
								            	<th>Transaction Fee</th>
								            	<th>Sum</th>
								            	<th>Vat</th>
								            	<th>Total Amount</th>
								            </tr></thead>
											<tbody>
												'.$data_invoice.'
											</tbody>
								        </table></div></center>
								        <hr>
								        <div style="text-align:right">
									        <a href="tax_invoice.php?act=xls&id='.$axRow['invh_InvoiceHID'].'">
									        <button type="button" class="btn btn-default btn-sm">Export Excel</button></a>
									        <button type="button" class="btn btn-primary btn-sm" onClick="openPrint('.$axRow['invh_InvoiceHID'].')">Print</button>
								        </div>
								    </div>
								</div>
							</div>
						</div>
						</td>';

	$data_table .= '</tr>';

	$asData[] = $axRow;
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




$oTmp->assign('total_amount', number_format($total, 2, '.', ','));

$oTmp->assign('transfer', 'transfer');

$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_financial');

$oTmp->assign('content_file','financial/tax_invoice.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
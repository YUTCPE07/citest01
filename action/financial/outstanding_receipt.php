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
include('../../lib/phpmailer/class.phpmailer.php'); 
require_once ('../../lib/phpmailer/PHPMailerAutoload.php');

//========================================//

$oTmp = new TemplateEngine();
$oDB = new DBI();

if ($bDebug) {

	$oErr = new Tracker();
	$oDB->SetTracker($oErr);
}

//========================================//

if ($_SESSION['role_action']['outstanding_received']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$time_insert = date("Y-m-d H:i:s");
$Act = $_REQUEST['act'];
$transfer_status = $_REQUEST['transfer_status'];




$where_transfer = '';

if (!$transfer_status || $transfer_status == "All") {

	$where_transfer = '';

} else if ($transfer_status == "Wait") {

	$where_transfer = ' AND outstanding_balance.ouba_Status="Wait"';

} else {

	$where_transfer = ' AND outstanding_balance.ouba_Status="'.$transfer_status.'"';
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



if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND mi_brand.brand_id = "'.$_SESSION['user_brand_id'].'"';
}




# APPROVE TRANSFER =====================================================================


if($_SESSION['user_type_id_ses']==1){

	# DATA APPROVE

	$sql_brand = "SELECT brand_id,logo_image,path_logo,name FROM mi_brand WHERE flag_del=0 ".$where_search;

	$oRes_brand = $oDB->Query($sql_brand);

	$cashin_amount = 0;

	while ($axRow_brand = $oRes_brand->FetchRow(DBI_ASSOC)){

		$transfer_amount = 0;

		$sql_transfer = "SELECT 

						outstanding_balance.ouba_OutstandingBalanceID, 
						outstanding_balance.ouba_OutstandingBalance, 
						outstanding_balance.bran_BrandID, 
						invoice_h.invh_InvoiceHID

						FROM outstanding_balance

						LEFT JOIN invoice_h
						ON invoice_h.ouba_OutstandingBalanceID = outstanding_balance.ouba_OutstandingBalanceID

						WHERE outstanding_balance.ouba_Status = 'Request'
						AND outstanding_balance.bran_BrandID='".$axRow_brand['brand_id']."'";

		$oRes_transfer = $oDB->Query($sql_transfer);

		while ($axRow_transfer = $oRes_transfer->FetchRow(DBI_ASSOC)){

			$sql_invoiceb = "SELECT invb_TotalAmount, invb_ReceiptNo
								FROM invoice_b
								WHERE invh_InvoiceHID=".$axRow_transfer['invh_InvoiceHID'];

			$oRes_invoiceb = $oDB->Query($sql_invoiceb);

			$total_invoice = 0;

			while ($axRow_invoiceb = $oRes_invoiceb->FetchRow(DBI_ASSOC)){

				$total_invoice += $axRow_invoiceb['invb_TotalAmount'];
			}

			$transfer_amount += $axRow_transfer['ouba_OutstandingBalance']-$total_invoice;
		}

		if($axRow_brand['logo_image']!=''){

			$axRow_brand['logo_image'] = '<img src="../../upload/'.$axRow_brand['path_logo'].$axRow_brand['logo_image'].'" class="image_border" width="80" height="80"/>';

		} else {

			$axRow_brand['logo_image'] = '<img src="../../images/400x400.png" class="image_border" width="80" height="80"/>';
		}


		if ($transfer_amount != 0) {

			$data_approve .= '<table class="table table-striped table-bordered myPopupData" cellspacing="0">
	    						<thead><tr class="th_table">
					        		<th colspan="3">'.$axRow_brand['name'].'</span></th>
								</tr></thead>
								<tbody>
									<tr>
										<td rowspan="3" style="text-align:center">'.$axRow_brand['logo_image'].'</td>
										<td style="text-align:right">Amount </td>
										<td width="30%" style="text-align:right">'.number_format($transfer_amount,2).' ฿ </td>
									</tr>
									<tr>
										<td style="text-align:right">Transfer Service </td>
										<td style="text-align:right"><span style="font-size:12px;color:red">- 30.00 ฿ </span></td>
									</tr>
									<tr>
										<td style="text-align:right">Total </td>
										<td style="text-align:right">'.number_format($transfer_amount-30,2).' ฿ </td>
									</tr>
								</tbody>
							</table>';
		}
	}

	// $data_approve .= '<table class="table table-striped table-bordered" cellspacing="0">

	//     				<thead><tr class="th_table">

	// 				        <th colspan="3"><span style="font-size:12px">MemberIn</span></th>

	// 						</tr></thead>

	// 						<tbody>

	// 							<tr>

	// 								<td rowspan="3" style="text-align:center"><img src="../../images/LOGO_BRAND.png" height="50"></td>

	// 								<td style="text-align:right"><span style="font-size:12px">Amount </span></td>

	// 								<td width="30%" style="text-align:right"><span style="font-size:12px">'.$cashin_amount.' ฿ </span></td>

	// 							</tr>

	// 							<tr>

	// 								<td style="text-align:right"><span style="font-size:12px">Transfer Service </span></td>

	// 								<td style="text-align:right"><span style="font-size:12px;color:red">- 30 ฿ </span></td>

	// 							</tr>

	// 							<tr>

	// 								<td style="text-align:right"><span style="font-size:12px">Total </span></td>

	// 								<td style="text-align:right"<span style="font-size:12px">'.($cashin_amount-30).' ฿ </span></td>

	// 							</tr>

	// 						</tbody>

	// 					</table>';



	# BUTTON APPROVE

	$check_approve = 'SELECT *
						FROM outstanding_balance
						WHERE outstanding_balance.ouba_Status="Request"';

	$approve_check = $oDB->QueryOne($check_approve);

	if ($approve_check) {

		$button_approve = '<button class="btn btn_hide" type="submit" style="width:150px;float:right;color:black" data-toggle="modal" data-target="#Approve">Approve</button><br>';

		$button_approve .= '

			<div class="modal fade" id="Approve" tabindex="-1" role="dialog" aria-labelledby="TrandferDataLabel">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-body" align="left">
							<span style="font-size:16px"><b>Please confirm your choice</b></span>
							<hr>
							<center>
								<table width="80%" class="myPopup"><tr><td>
									'.$data_approve.'
								</td></tr></table>
							</center>
						</div>
						<div class="modal-footer">
							<a href="outstanding_receipt.php?act=approve">
							<button type="button" class="btn btn-default btn-sm">Approve Transfer</button></a>
							<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						</div>
					</div>
				</div>
			</div>';

	} else {

		$button_approve = '<button class="btn btn-primary btn_hide" type="submit" style="width:150px;float:right" disabled>Approve</button><br>';
	}
}



# REQUEST TRANSFER =====================================================================

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND mi_brand.brand_id = "'.$_SESSION['user_brand_id'].'"';

	# BANK ACCOUNT

	$sql_bank = 'SELECT bank_account_number AS bank_number,
					recipient_token
					FROM mi_brand_bank_account 
					WHERE brand_id="'.$_SESSION['user_brand_id'].'" AND default_status=1';

	$oRes_bank = $oDB->Query($sql_bank);

	$axRow_bank = $oRes_bank->FetchRow(DBI_ASSOC);

	$recipient = OmiseRecipient::retrieve($axRow_bank['recipient_token']);


	$sql_bank_name = 'SELECT name_en
						FROM mi_bank
						WHERE bank_omise ="'.$recipient['bank_account']['brand'].'"';

	$bank = $oDB->QueryOne($sql_bank_name);



	# REQUEST TRANSFER

	$sql_request ='SELECT 

					outstanding_balance.*,
					invoice_h.invh_InvoiceHID,
					invoice_h.invh_InvoiceReceipt

					FROM outstanding_balance

					LEFT JOIN invoice_h
					ON invoice_h.ouba_OutstandingBalanceID = outstanding_balance.ouba_OutstandingBalanceID

					WHERE outstanding_balance.bran_BrandID = '.$_SESSION['user_brand_id'].'
					AND outstanding_balance.ouba_Status = "Wait"
					ORDER BY outstanding_balance.ouba_OutstandingReceipt DESC';

	$oRes_request = $oDB->Query($sql_request);

	$check_request = $oDB->QueryOne($sql_request);

	$data_request = "";


	if ($check_request) {

		$data_request .= '<table class="table table-striped table-bordered myPopupData" cellspacing="0" width="100%">
		    				<thead><tr class="th_table">
						        <th width="25%">Date</th>
								<th>Outstanding Receipt</th>
						        <th>Invoice No.</th>
						        <th>Amount</th>
						    </tr></thead>
						    <tbody>';

		$total_amount = 0;

		while ($axRow_request = $oRes_request->FetchRow(DBI_ASSOC)){

			$sql_invoiceb = "SELECT invb_TotalAmount
								FROM invoice_b 
								WHERE invh_InvoiceHID='".$axRow_request['invh_InvoiceHID']."'";

			$oRes_invoiceb = $oDB->Query($sql_invoiceb);

			$brand_exp = 0;

			while ($axRow_invoiceb = $oRes_invoiceb->FetchRow(DBI_ASSOC)){

				$brand_exp += $axRow_invoiceb['invb_TotalAmount'];
			}

			$transfer_amount = $axRow_request['ouba_OutstandingBalance']-$brand_exp;

			$total_amount += $transfer_amount;

			$data_request .= '<tr>
								<td>'.DateOnly($axRow_request['ouba_CreatedDate']).'</td>
								<td>'.$axRow_request['ouba_OutstandingReceipt'].'</td>
								<td>'.$axRow_request['invh_InvoiceReceipt'].'</td>
								<td style="text-align:right">'.number_format($transfer_amount,2).' ฿ </td>
							</tr>';
		}

		$data_request .= '<tr>
							<td colspan="3" style="text-align:right">Transfer Service </td>
							<td style="text-align:right"><span style="color:red">- 30.00 ฿ </span></td>
						</tr>
						<tr>
							<td colspan="3" style="text-align:right"><b>Total </b.</td>
							<td style="text-align:right"><b>'.number_format($total_amount-30,2).' ฿ </b></td>
						</tr>';

		$data_request .= '</tbody></table>';

	} else {

		$data_request .= '<table class="table table-striped table-bordered myPopupData" cellspacing="0" width="100%">
		    				<thead><tr class="th_table">
						        <th width="25%">Date</th>
								<th>Outstanding Receipt</th>
						        <th>Invoice No.</th>
						        <th>Amount</th>
						    </tr></thead>
						    <tbody>
						    <tr>
								<td style="text-align:center">-</td>
								<td style="text-align:center">-</td>
								<td style="text-align:center">-</td>
								<td style="text-align:right">0.00 ฿ </td>
							</tr>
							<tr>
								<td colspan="3" style="text-align:right">Transfer Service </td>
								<td style="text-align:right"><span style="color:red">- 0 ฿ </td>
							</tr>
							<tr>
								<td colspan="3" style="text-align:right"><b>Total </b.</td>
								<td style="text-align:right"><b>0.00 ฿ </b></td>
							</tr>
							</tbody>
						</table>';
	}



	# BUTTON REQUEST

	$button_bank = "";

	if ($axRow_bank && $check_request) {

		$button_bank = '<button class="btn btn-primary btn_hide" type="submit" style="width:150px;float:right" data-toggle="modal" data-target="#Transfer'.$axRow['member_register_id'].'">Request Transfer</button><br>';

		$button_bank .= '

				<div class="modal fade" id="Transfer'.$axRow['member_register_id'].'" tabindex="-1" role="dialog" aria-labelledby="TrandferDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center><span style="font-size:12px">
						        <table class="myPopup" width="80%">
						            <tr style="text-align:center">
						            	<td colspan="3">'.$data_request.'</td>
						            </tr>
						            <tr valign="top">
						            	<td width="20%" style="text-align:right"><b>Bank</b></td>
						            	<td width="5%" style="text-align:center"> : </td>
						            	<td>'.$recipient['bank_account']['name'].'<br>
						            		'.$axRow_bank['bank_number'].'<br>
						            		'.$bank.'</td>
						            </tr>
						        </table></span></center>
						    </div>
						    <div class="modal-footer">
						        <a href="outstanding_receipt.php?act=transfer">
						        <button type="button" class="btn btn-default btn-sm">Request Transfer</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
	} else {

		$button_bank = '<button class="btn btn-primary btn_hide" type="submit" style="width:150px;float:right" disabled>Request Transfer</button><br>';
	}
}


$sql ='SELECT 

		outstanding_balance.*,
		invoice_h.invh_InvoiceHID,
		invoice_h.invh_InvoiceReceipt,
		mi_brand.logo_image,
		mi_brand.path_logo,
		mi_brand.name AS brand_name

		FROM outstanding_balance

		LEFT JOIN invoice_h
		ON invoice_h.ouba_OutstandingBalanceID = outstanding_balance.ouba_OutstandingBalanceID

		LEFT JOIN mi_brand
		ON mi_brand.brand_id = outstanding_balance.bran_BrandID

		WHERE 1

		'.$where_brand.'
		'.$where_search.'
		'.$where_transfer.'

		ORDER BY outstanding_balance.ouba_OutstandingBalanceID DESC';


# REQUEST TRANSFER =====================================================================

if($Act == 'transfer'){

	$transfer_amount = 0;

	$sql_transfer = "SELECT 

					outstanding_balance.ouba_OutstandingBalanceID, 
					outstanding_balance.ouba_OutstandingBalance, 
					outstanding_balance.bran_BrandID, 
					invoice_h.invh_InvoiceHID

					FROM outstanding_balance

					LEFT JOIN invoice_h
					ON invoice_h.ouba_OutstandingBalanceID = outstanding_balance.ouba_OutstandingBalanceID

					WHERE outstanding_balance.ouba_Status = 'Wait'
					AND outstanding_balance.bran_BrandID='".$_SESSION['user_brand_id']."'";

	$oRes_transfer = $oDB->Query($sql_transfer);

	while ($axRow_transfer = $oRes_transfer->FetchRow(DBI_ASSOC)){

		$do_sql_transfer = "UPDATE outstanding_balance 
							SET ouba_Status = 'Request',
								ouba_UpdatedDate = '".$time_insert."',
								ouba_UpdatedBy = '".$_SESSION['UID']."'
							WHERE ouba_OutstandingBalanceID=".$axRow_transfer['ouba_OutstandingBalanceID'];

		$oDB->QueryOne($do_sql_transfer);

		$sql_invoiceb = "SELECT invb_ReceiptNo
							FROM invoice_b
							WHERE invh_InvoiceHID=".$axRow_transfer['invh_InvoiceHID'];

		$oRes_invoiceb = $oDB->Query($sql_invoiceb);

		while ($axRow_invoiceb = $oRes_invoiceb->FetchRow(DBI_ASSOC)){

			$do_sql_regis = "UPDATE mb_member_register 
								SET payr_TransferStatus = 'Request',
									payr_UpdatedDate = '".$time_insert."',
									payr_UpdatedBy = '".$_SESSION['UID']."'
								WHERE member_register_id=".$axRow_invoiceb['invb_ReceiptNo'];

			$oDB->QueryOne($do_sql_regis);
		}
	}

	echo '<script>window.location.href="outstanding_receipt.php";</script>';

	exit;
}




# APPROVE =====================================================================

if($Act == 'approve'){

	$sql_brand = "SELECT brand_id FROM mi_brand WHERE flag_del=0 ".$where_search;

	$oRes_brand = $oDB->Query($sql_brand);

	$cashin_amount = 0;

	while ($axRow_brand = $oRes_brand->FetchRow(DBI_ASSOC)){

		$transfer_amount = 0;

		$sql_transfer = "SELECT 

						outstanding_balance.ouba_OutstandingBalanceID, 
						outstanding_balance.ouba_OutstandingBalance, 
						outstanding_balance.bran_BrandID, 
						invoice_h.invh_InvoiceHID

						FROM outstanding_balance

						LEFT JOIN invoice_h
						ON invoice_h.ouba_OutstandingBalanceID = outstanding_balance.ouba_OutstandingBalanceID

						WHERE outstanding_balance.ouba_Status = 'Request'
						AND outstanding_balance.bran_BrandID='".$axRow_brand['brand_id']."'";

		$oRes_transfer = $oDB->Query($sql_transfer);

		$check_transfer = $oDB->QueryOne($sql_transfer);

		if ($check_transfer) {	$payslip_receipt = payslip_receipt();	}

		while ($axRow_transfer = $oRes_transfer->FetchRow(DBI_ASSOC)){

			$do_sql_out = "UPDATE outstanding_balance 
							SET ouba_Status = 'Transfer',
								ouba_UpdatedDate = '".$time_insert."',
								ouba_UpdatedBy = '".$_SESSION['UID']."'
							WHERE ouba_OutstandingBalanceID=".$axRow_transfer['ouba_OutstandingBalanceID'];

			$oDB->QueryOne($do_sql_out);

			$sql_invoiceb = "SELECT invb_TotalAmount, invb_ReceiptNo
								FROM invoice_b
								WHERE invh_InvoiceHID=".$axRow_transfer['invh_InvoiceHID'];

			$oRes_invoiceb = $oDB->Query($sql_invoiceb);

			$total_invoice = 0;

			while ($axRow_invoiceb = $oRes_invoiceb->FetchRow(DBI_ASSOC)){

				$total_invoice += $axRow_invoiceb['invb_TotalAmount'];

				$do_sql_regis = "UPDATE mb_member_register 
									SET payr_TransferStatus = 'Transfer',
										payr_UpdatedDate = '".$time_insert."',
										payr_UpdatedBy = '".$_SESSION['UID']."'
									WHERE member_register_id=".$axRow_invoiceb['invb_ReceiptNo'];

				$oDB->QueryOne($do_sql_regis);
			}

			$sql_get_id = 'SELECT max(pays_PayslipID) FROM payslip';

			$last_id = $oDB->QueryOne($sql_get_id);

			$id_new = $last_id+1;


			$do_sql_payslip = "INSERT INTO payslip 
								SET pays_PayslipID = '".$id_new."',
									pays_PayslipNo = '".$payslip_receipt."',
									bran_BrandID = '".$axRow_transfer['bran_BrandID']."',
									invh_InvoiceHID = '".$axRow_transfer['invh_InvoiceHID']."',
									ouba_OutstandingBalanceID = '".$axRow_transfer['ouba_OutstandingBalanceID']."',
									pays_Status = 'Request',
									pays_Amount = ".($axRow_transfer['ouba_OutstandingBalance']-$total_invoice).",
									pays_RequestDate = '".$time_insert."',
									pays_UpdatedDate = '".$time_insert."',
									pays_UpdatedBy = '".$_SESSION['UID']."',
									pays_CreatedDate = '".$time_insert."',
									pays_CreatedBy = '".$_SESSION['UID']."'";

			$oDB->QueryOne($do_sql_payslip);

			$transfer_amount += $axRow_transfer['ouba_OutstandingBalance']-$total_invoice;


			# DATA EMAIL

			$sql_view ='SELECT 
							invoice_b.invb_Type AS item_type, 
							SUM(invoice_b.invb_TotalAmount) AS total_inv,
							COUNT(invoice_b.invb_ReceiptNo) AS qty, 
							SUM(hilight_coupon_buy.hcbu_TotalAmount) AS total_amount, 
							invoice_b.invb_CreatedDate AS date_create,
							hilight_coupon.coup_Name AS item_name,
							hilight_coupon.coup_Price AS item_price

						FROM hilight_coupon_buy 

						LEFT JOIN invoice_b 
						ON hilight_coupon_buy.hcbu_ReceiptNo = invoice_b.invb_ReceiptNo 

						LEFT JOIN invoice_h 
						ON invoice_h.invh_InvoiceHID = invoice_b.invh_InvoiceHID 

						LEFT JOIN outstanding_balance 
						ON invoice_h.ouba_OutstandingBalanceID = outstanding_balance.ouba_OutstandingBalanceID 

						LEFT JOIN hilight_coupon
						ON hilight_coupon_buy.hico_HilightCouponID = hilight_coupon.coup_CouponID 

						WHERE invoice_h.ouba_OutstandingBalanceID="'.$axRow_transfer['ouba_OutstandingBalanceID'].'" 
						GROUP BY hilight_coupon_buy.hico_HilightCouponID

						UNION

						SELECT 
							invoice_b.invb_Type AS item_type, 
							SUM(invoice_b.invb_TotalAmount) AS total_inv,
							COUNT(invoice_b.invb_ReceiptNo) AS qty, 
							SUM(mb_member_register.total_amt) AS total_amount, 
							invoice_b.invb_CreatedDate AS date_create,
							mi_card.name AS item_name,
							mi_card.member_fee AS item_price

						FROM mb_member_register 

						LEFT JOIN invoice_b 
						ON mb_member_register.receipt_no = invoice_b.invb_ReceiptNo 

						LEFT JOIN invoice_h 
						ON invoice_h.invh_InvoiceHID = invoice_b.invh_InvoiceHID 

						LEFT JOIN outstanding_balance 
						ON invoice_h.ouba_OutstandingBalanceID = outstanding_balance.ouba_OutstandingBalanceID 

						LEFT JOIN mi_card
						ON mb_member_register.card_id = mi_card.card_id 

						WHERE invoice_h.ouba_OutstandingBalanceID="'.$axRow_transfer['ouba_OutstandingBalanceID'].'" 
						GROUP BY mb_member_register.card_id';

			$oRes_view = $oDB->Query($sql_view);

			while ($axRow_view = $oRes_view->FetchRow(DBI_ASSOC)){

				$data_email .= '<tr>
							<td style="text-align:center;padding:5px 10px 5px 10px">'.DateOnly($axRow_view['date_create']).'</td>
							<td style="padding:5px 10px 5px 10px">'.$axRow_view['item_name'].' ('.number_format($axRow_view['item_price'],2).' ฿)</td>
							<td style="text-align:center;padding:5px 10px 5px 10px">'.$axRow_view['item_type'].'</td>
							<td style="text-align:right;padding:5px 10px 5px 10px">'.$axRow_view['qty'].'</td>
							<td style="text-align:right;padding:5px 10px 5px 10px">'.number_format($axRow_view['total_amount']-$axRow_view['total_inv'],2).' ฿ </td>
						</tr>';

				$total_view += $transfer_view;
			}
		}

		if ($check_transfer) {	

			$data_email .= '<tr>
								<td></td>
								<td colspan="3" style="padding:5px 10px 5px 10px">Transfer Service</td>
								<td style="text-align:right;padding:5px 10px 5px 10px"><span style="color:red">-30.00 ฿ </span></td>
							</tr>';
		}

		if ($transfer_amount != 0) {

			$sql_bank ='SELECT recipient_token
						FROM mi_brand_bank_account 
						WHERE default_status=1 AND brand_id='.$axRow_brand['brand_id'];

			$bank_recipient = $oDB->QueryOne($sql_bank);

			# OMISE BRAND

			$transfer_token = OmiseTransfer::create(array(
											  			'amount' => $transfer_amount*100,
											  			'recipient' => $bank_recipient
			));

			if ($transfer_token['id']) {

				$sql_get_id = 'SELECT max(payp_PaymentPayableTransID) FROM payment_payable_trans';

				$last_id = $oDB->QueryOne($sql_get_id);

				$id_new = $last_id+1;

				$sql_bank ='SELECT brand_bank_account_id
							FROM mi_brand_bank_account 
							WHERE default_status=1 AND brand_id="'.$axRow_brand['brand_id'].'"';

				$brand_bank = $oDB->QueryOne($sql_bank);

				$sql_payment_trans ='INSERT INTO payment_payable_trans
										SET payp_PaymentPayableTransID = "'.$id_new.'",
										pays_PayslipNo = "'.$payslip_receipt.'",
										payp_TokenID = "'.$transfer_token['id'].'",
										payp_Status = "Complete",
										bank_BankID = "'.$brand_bank.'",
										payp_PaymentGateWayID = "3",
										payp_PaymentType = "Omise",
										payp_Amount = "'.$transfer_amount.'",
										payp_GateWayFee = "30",
										payp_NetAmount = "'.($transfer_amount-30).'",
										payp_UpdatedDate = "'.$time_insert.'",
										payp_UpdatedBy = "'.$_SESSION['UID'].'",
										payp_CreatedDate = "'.$time_insert.'",
										payp_CreatedBy = "'.$_SESSION['UID'].'"';

				$oDB->QueryOne($sql_payment_trans);

				# SEND EMAIL

				$sql_comm = 'SELECT user.email, user.user_id
								FROM communication AS comm
								LEFT JOIN mi_user AS user
								ON user.user_id = comm.user_UserID
								WHERE comm.comm_Deleted=""
								AND comm.coto_TopicID="3"
								AND comm.comm_Type="Email"
								AND user.flag_del="0"
								AND comm.bran_BrandID="'.$axRow_brand['brand_id'].'"
								GROUP BY user.user_id';

				$oRes_comm = $oDB->Query($sql_comm);

				while ($comm = $oRes_comm->FetchRow(DBI_ASSOC)){

					$sql_brand = 'SELECT name, brand_id, code_color, text_color 
									FROM mi_brand
									WHERE brand_id = "'.$axRow_brand['brand_id'].'"';
					$oRes_brand = $oDB->Query($sql_brand);
					$brand = $oRes_brand->FetchRow(DBI_ASSOC);

					$sql_brnc = 'SELECT COUNT(*) AS count
								FROM communication
								WHERE user_UserID="'.$comm['user_id'].'"
								AND coto_TopicID="3"
								AND comm_Type="Email"
								AND comm_Deleted=""';

					$brnc_count = $oDB->QueryOne($sql_brnc);

					$sql_brnc = 'SELECT brnc_BranchID
								FROM communication
								WHERE user_UserID="'.$comm['user_id'].'"
								AND coto_TopicID="3"
								AND comm_Type="Email"
								AND comm_Deleted=""';

					$oRes_brnc = $oDB->Query($sql_brnc);

					$HTML = '<center>
							<table style="width:720px;background-color:white;" cellspacing="0" cellpadding="0">
								<tr height="100px">
									<td style="text-align:center" colspan="5">
										<img src="http://www.memberin.com/images/LOGO.png" height="70px">
									</td>
								</tr>
								<tr>
									<td colspan="5">
										<b>Hi '.$brand['name'].'</b><br>
										We\'ve issued you a payout of <b>'.number_format($transfer_amount-30,2).'฿</b> via Bank Transfer.<br>
										This payout should arrive in your account by tomorrow, taking into consideration weekends and holidays.<br><br>
									</td>
								</tr>
								<tr>
									<td align="center">
										<table style="border-spacing:0px;border-collapse:collapse;width:100%" border="1px">
											<tr style="background:#'.$brand['code_color'].';color:'.$brand['text_color'].';">
												<td style="text-align:center;padding:5px 10px 5px 10px">Date</td>
												<td style="text-align:center;padding:5px 10px 5px 10px">Detail</td>
												<td style="text-align:center;padding:5px 10px 5px 10px">Type</td>
												<td style="text-align:center;padding:5px 10px 5px 10px">Qty</td>
												<td style="text-align:center;padding:5px 10px 5px 10px">Amount</td>
											</tr>
											'.$data_email.'
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="5">
										<br>
										You can view the status of your payouts in your <a href="www.memberin.com/demo/action/financial/transfer_history.php"><b>Transfer History</b></a>. If you have any questions, please contact at bd@memberin.com or call at 02-061-1169<br><br>
										Thanks,<br>
										MemberIn Application Team
									</td>
								</tr>
							</table>
							</center>';


					$mail = new PHPMailer();
					$mail = new PHPMailer;

					$mail->Debugoutput = 'html';
					$mail->Host = 'mail.memberin.com';
					$mail->SMTPSecure = '25';
					$mail->SMTPAuth = true;
					$mail->Username = "noreply@memberin.com";
					$mail->Password = "m3mb3rIN@2016";
					$mail->CharSet = 'UTF-8';
					$mail->isSendmail();
					$mail->setFrom('noreply@memberin.com', 'MemberIn');
					// $mail->addAddress($comm['email']);
					$mail->addAddress('lechieng.k@gmail.com');
					$mail->Subject = 'Payout of '.number_format($transfer_amount-30,2).' ฿ sent';

					$mail->msgHTML($HTML);

					$mail->send();
				}
			}
		}
	}


	# OMISE MEMBERIN

	// $transfer_brand = OmiseTransfer::create(array(

	//   	'amount' => $cashin_amount*100,

	//   	'recipient' => 'recp_test_5283rp3dkbg9omr3y4q'

	// ));

	
	echo '<script>window.location.href="outstanding_receipt.php";</script>';

	exit;
}





# DATA TABLE =====================================================================

$oRes = $oDB->Query($sql);

$i=0;

$asData = array();

$data_table = '';

$total = 0;

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$i++;


	# LOGO IMAGE

	if($axRow['logo_image']!=''){

		$axRow['logo_image'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="50" height="50"/>';

	} else {

		$axRow['logo_image'] = '<img src="../../images/400x400.png" width="50" class="image_border" height="50"/>';
	}



	$sql_invoiceb = "SELECT invb_TotalAmount
					FROM invoice_b 
					WHERE invh_InvoiceHID='".$axRow['invh_InvoiceHID']."'";

	$oRes_invoiceb = $oDB->Query($sql_invoiceb);

	$brand_exp = 0;

	while ($axRow_invoiceb = $oRes_invoiceb->FetchRow(DBI_ASSOC)){

		$brand_exp += $axRow_invoiceb['invb_TotalAmount'];
	}


	$transfer_amount = $axRow['ouba_OutstandingBalance']-$brand_exp;



	# DATA VIEW =====================================================================

	$data_view = "";


	$sql_view ='SELECT 
				invoice_h.*,
				invoice_b.*,
				outstanding_balance.*

				FROM invoice_h

				LEFT JOIN invoice_b
				ON invoice_h.invh_InvoiceHID = invoice_b.invh_InvoiceHID

				LEFT JOIN outstanding_balance
				ON invoice_h.ouba_OutstandingBalanceID = outstanding_balance.ouba_OutstandingBalanceID

				WHERE invoice_h.ouba_OutstandingBalanceID="'.$axRow['ouba_OutstandingBalanceID'].'"';

	$oRes_view = $oDB->Query($sql_view);

	$j = 0;

	$total_view = 0;

	while ($axRow_view = $oRes_view->FetchRow(DBI_ASSOC)){

		$j++;

		if ($axRow_view['invb_Type'] == 'Card') {

			$sql_buy = 'SELECT receipt_no,
								SUM(total_amt) AS total_amt
						FROM mb_member_register 
						WHERE receipt_no="'.$axRow_view['invb_ReceiptNo'].'"
						GROUP BY receipt_no';

		} else {

			$sql_buy = 'SELECT hcbu_ReceiptNo AS receipt_no,
								SUM(hcbu_TotalAmount) AS total_amt 
						FROM hilight_coupon_buy 
						WHERE hcbu_ReceiptNo="'.$axRow_view['invb_ReceiptNo'].'"
						GROUP BY hcbu_ReceiptNo';
		}

		$oRes_buy = $oDB->Query($sql_buy);
		$axRow_buy = $oRes_buy->FetchRow(DBI_ASSOC);

		$transfer_view = $axRow_buy['total_amt']-$axRow_view['invb_TotalAmount'];

		$data_view .= '<tr>
						<td >'.$j.'</td>
						<td>'.$axRow_buy['receipt_no'].'</td>
						<td style="text-align:right">'.number_format($axRow_buy['total_amt'],2).' ฿ </td>
						<td style="text-align:right">'.$axRow_view['invb_TotalAmount'].' ฿ </td>
						<td style="text-align:right">'.number_format($transfer_view,2).' ฿ </td>';
		$data_view .= '</tr>';

		$total_view += $transfer_view;
	}

	$data_view .= '<tr>
						<td colspan="4" style="text-align:center"><span style="font-size:12px"><b>TOTAL</b></span></td>
						<td style="text-align:right"><span style="font-size:12px"><b>'.number_format($total_view,2).' ฿ </b></span></td>
					</tr>';


	# DATA TABLE =====================================================================

	$total += $transfer_amount;

	$strNewDate = date("Y-m-d", strtotime("-1 day", strtotime($axRow['ouba_CreatedDate'])));

	if ($axRow['invh_InvoiceReceipt'] == '') { $axRow['invh_InvoiceReceipt'] = '-'; }

	$data_table .= '<tr>
						<td >'.$i.'</td>
						<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['logo_image'].'</a><br>
							<span style="font-size:11px;">'.$axRow['brand_name'].'</td>
						<td style="text-align:center">'.DateOnly($strNewDate).'</td>
						<td>'.$axRow['ouba_OutstandingReceipt'].'</td>
						<td>'.$axRow['invh_InvoiceReceipt'].'</td>
						<td style="text-align:right">'.number_format($transfer_amount,2).' ฿ </td>
						<td style="text-align:center">'.$axRow['ouba_Status'].'</td>
						<td style="text-align:center">'.DateTime($axRow['ouba_UpdatedDate']).'</td>
						<td style="text-align:center">

							<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['ouba_OutstandingReceipt'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
							<div class="modal fade" id="View'.$axRow['ouba_OutstandingReceipt'].'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
								<div class="modal-dialog modal-table" role="document">
									<div class="modal-content">
										<div class="modal-body" align="left">
											<span style="font-size:16px">
												Outstanding Receipt : '.$axRow['ouba_OutstandingReceipt'].'
								        		<span style="float:right">Date : '.DateOnly($strNewDate).'</span>
								        		<br>
												Invoice No. : '.$axRow['invh_InvoiceReceipt'].'
								        		<span style="float:right">'.$axRow['brand_name'].'</span>
											</span>
											<hr>
									        <center><table class="table table-striped table-bordered myPopupData">
									            <thead><tr class="th_table">
									            	<th>No.</th>
									            	<th>Receipt No.</th>
									            	<th>Receipt Amount</th>
									            	<th>Invoice Amount</th>
									            	<th>Amount</th>
									            </tr></thead>
												<tbody>
													'.$data_view.'
												</tbody>
									        </table></center>
									        <div style="text-align:right">
										        <button type="button" class="btn btn-primary btn-sm" onClick="openPrint('.$axRow['ouba_OutstandingBalanceID'].')">Print</button>
									        </div>
										</div>
									</div>
								</div>
							</div>
						</td>';

	$data_table .= '</tr>';

	$asData[] = $axRow;
}




function payslip_receipt() {

	$strSQLUpdate = "UPDATE document SET doct_DocumentLastNum  = doct_DocumentLastNum+1 WHERE doct_DocumentID = '5'";

	$strSQL = "SELECT doct_DocumentShortName,doct_DocumentLastNum FROM document WHERE doct_DocumentID = '5'";

	mysql_query($strSQLUpdate);

  	$objQuery =	mysql_query($strSQL);

	$jsonTempData = array();

	$record = mysql_fetch_assoc($objQuery);

	$short  = $record['doct_DocumentShortName'];

	$number = $record['doct_DocumentLastNum'];

 	$number = str_pad($number, 8, "0", STR_PAD_LEFT);

	$text = $short.$number;

	return $text;
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





$oTmp->assign('total_amount', number_format($total, 2, '.', ','));

$oTmp->assign('button_bank', $button_bank);

$oTmp->assign('button_approve', $button_approve);

$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_financial');

$oTmp->assign('content_file','financial/outstanding_receipt.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
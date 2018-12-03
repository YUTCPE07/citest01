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

if ($_SESSION['role_action']['transfer_history']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$path_upload_logo = $_SESSION['path_upload_logo'];





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

		payment_payable_trans.*,
		mi_brand_bank_account.recipient_token,
		mi_brand.logo_image,
		mi_brand.path_logo,
		mi_brand.name AS brand_name

		FROM payment_payable_trans

		LEFT JOIN mi_brand_bank_account
		ON mi_brand_bank_account.brand_bank_account_id = payment_payable_trans.bank_BankID

		LEFT JOIN mi_brand
		ON mi_brand.brand_id = mi_brand_bank_account.brand_id

		WHERE 1 

		'.$where_brand.'
		'.$where_search.'

		ORDER BY payment_payable_trans.payp_CreatedDate DESC';

$oRes = $oDB->Query($sql);

$i=0;

$data_table = '';

$total_amount = 0;

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$i++;


	# LOGO IMAGE

	if($axRow['logo_image']!=''){

		$axRow['logo_image'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="50" height="50"/>';

	} else {

		$axRow['logo_image'] = '<img src="../../images/400x400.png" width="50" height="50"/>';
	}



	# STATUS

	if($axRow['payp_Status']=='Complete'){

		$axRow['payp_Status'] = "<span style='color:green'><b>".$axRow['payp_Status']."</b></span>";

	} else {

		$axRow['payp_Status'] = "<span style='color:red'><b>".$axRow['payp_Status']."</b></span>";
	}

	$bank_brand = OmiseRecipient::retrieve($axRow['recipient_token']);

	$transfer = OmiseTransfer::retrieve($axRow['payp_TokenID']);

	if ($transfer['paid']=="" || $transfer['paid']==0) {	$transfer_status = "<span style='color:red'><b>No</b></span>";
	} else {	$transfer_status = "<span style='color:green'><b>Yes</b></span>";	}


	$data_table .= '<tr>
						<td >'.$i.'</td>
						<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['logo_image'].'</a><br>
							<span style="font-size:11px;">'.$axRow['brand_name'].'</td>';

	$total_amount += $axRow['payp_NetAmount'];

	$data_table .= '	<td>'.$axRow['pays_PayslipNo'].'</td>
						<td>'.$bank_brand['bank_account']['name'].' 
							(XXXXXX'.$bank_brand['bank_account']['last_digits'].')</td>
						<td>'.DateOnly($axRow['payp_CreatedDate']).'</td>
						<td style="text-align:right">'.number_format($axRow['payp_NetAmount'],2).' ฿</td>
						<td >'.$axRow['payp_Status'].'</td>
						<td style="text-align:center"><button type="button" style="cursor:pointer" class="btn btn-default btn-sm" onClick="openReceipt(\''.$axRow['pays_PayslipNo'].'\')"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></td>
					</tr>';

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



$oTmp->assign('total_amount', number_format($total_amount, 2, '.', ','));

$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_financial');

$oTmp->assign('content_file','financial/transfer_history.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
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

if ($_SESSION['role_action']['bank_account']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];



$where_brand = '';

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND a.brand_id = "'.$_SESSION['user_brand_id'].'" AND a.flag_del=0';
}



# SEARCH

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
else {	$where_search = " AND a.brand_id IN (".$brand_id.")";	}




$sql = 'SELECT 

		a.*,
		b.name AS brand_name,
		b.logo_image,
		b.path_logo,
		a.flag_del as status_del

	  	FROM mi_brand_bank_account AS a

		LEFT JOIN mi_brand AS b 
		ON a.brand_id = b.brand_id

		WHERE 1
		'.$where_search.'
		'.$where_brand.' 

		ORDER BY CASE 
			WHEN a.flag_del = "0" THEN 1
	        WHEN a.flag_del = "1" THEN 2 END ASC,
			a.date_update DESC';


if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT flag_del FROM mi_brand_bank_account WHERE brand_bank_account_id ="'.$id.'"';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($axRow['flag_del']=='0') {

 		$do_sql_bank = "UPDATE mi_brand_bank_account
 						SET flag_del='1', 
 						date_update='".$time_insert."' 
 						WHERE brand_bank_account_id='".$id."'";

 	} else if ($axRow['flag_del']=='1') {

		$do_sql_bank = "UPDATE mi_brand_bank_account
 						SET flag_del='0', 
 						date_update='".$time_insert."' 
 						WHERE brand_bank_account_id='".$id."'";
	}

 	$oDB->QueryOne($do_sql_bank);

 	echo '<script>window.location.href="bank_account.php";</script>';


} else {


	$oRes = $oDB->Query($sql);

	$i=0;

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;


		# OMISE

		$recipient = OmiseRecipient::retrieve($axRow['recipient_token']);

		$sql_bank = 'SELECT name_en FROM mi_bank WHERE bank_omise ="'.$recipient['bank_account']['brand'].'"';

		$bank = $oDB->QueryOne($sql_bank);


		# LOGO

		if($axRow['logo_image']!=''){

			$brand_logo = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="100" height="100"/>';

			$axRow['logo_image'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';

		} else {

			$brand_logo = '<img src="../../images/400x400.png" width="100" height="100"/>';

			$axRow['logo_image'] = '<img src="../../images/400x400.png" width="60" height="60"/>';
		}



		# BRANCH

		$branch_data = "";

		if ($axRow['branch_id']) {

			$token = strtok($axRow['branch_id'] , ",");

			$branch = array();

			$j = 0;

			while ($token !== false) {

	    		$branch[$j] =  $token;
	    		$token = strtok(",");
	    		$j++;
			}

			$arrlength = count($branch);

			for($x = 0; $x < $arrlength; $x++) {

				$sql_branch = 'SELECT name FROM mi_branch WHERE branch_id = "'.$branch[$x].'"';
				$name = $oDB->QueryOne($sql_branch);

				$branch_data .= $name.'<br>';
			}
		}



		# DELETED

		if($axRow['flag_del']=='0') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['brand_bank_account_id'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>
				<div class="modal fade" id="Deleted'.$axRow['brand_bank_account_id'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="140px" style="text-align:center" valign="top">'.$brand_logo.'</td>
						        	<td>
								        <p style="font-size:12px;padding-left:10px;">
								        	<b>XXXXXX'.$recipient['bank_account']['last_digits'].'<br>
								        	'.$bank.'</b><br><br>
								            By clicking the <b>"Inactive"</b> button to:<br>
								            &nbsp; &nbsp;- Inactive this bank<br>
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="bank_account.php?act=delete&id='.$axRow['brand_bank_account_id'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		} else if ($axRow['flag_del']=='1') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['brand_bank_account_id'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>
				<div class="modal fade" id="Deleted'.$axRow['brand_bank_account_id'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="140px" style="text-align:center" valign="top">'.$brand_logo.'</td>
						        	<td>
								        <p style="font-size:12px;padding-left:10px;">
								        	<b>XXXXXX'.$recipient['bank_account']['last_digits'].'<br>
								        	'.$bank.'</b><br><br>
								           	By clicking the <b>"Active"</b> button to:<br>
								            &nbsp; &nbsp;- Active this bank
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="bank_account.php?act=delete&id='.$axRow['brand_bank_account_id'].'">
						        <button type="button" class="btn btn-default btn-sm">Active</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		}


		if($axRow['status_del']==0) {

			$Eye = "<a href='bank_account.php?act=delete&id=".$axRow["brand_bank_account_id"]."' onclick='return ConfirmDel()'>
			<button type='button' class='btn btn-default btn-sm'>
  			<span class='glyphicon glyphicon-eye-open' aria-hidden='true'></span> Active</button></a>";

		} else if ($axRow['status_del']==1) {

			$Eye="<a href='bank_account.php?act=delete&id=".$axRow["brand_bank_account_id"]."' onclick='return ConfirmDel()'>
			<button type='button' class='btn btn-default btn-sm'>
  			<span class='glyphicon glyphicon-eye-close' aria-hidden='true'></span> Inactive</button></a>";
		}



		if($axRow['default_status']==1){

			$axRow['default_status'] = "<b><span style='color:green'>Yes</span></b>";

		} else {

			$axRow['default_status'] = "<b><span style='color:gray'>No</span></b>";
		}



		# DATA TABLE

		$data_table .= '<tr >
							<td >'.$i.'</td>
							<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['logo_image'].'</a><br>
								<span style="font-size:11px;">'.$axRow['brand_name'].'</span>
							</td>
							<td >'.$bank.'</td>
							<td >'.$recipient['bank_account']['name'].'</td>
							<td >XXXXXX'.$recipient['bank_account']['last_digits'].'</td>
							<td >'.$branch_data.'</td>
							<td style="text-align:center">'.$axRow['default_status'].'</td>
							<td >'.DateTime($axRow['date_update']).'</td>';

		if ($_SESSION['role_action']['bank_account']['edit'] == 1) {

			$data_table .=	'<td ><a href="bank_account_create.php?act=edit&id='.$axRow['brand_bank_account_id'].'">
								<button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></td>';
		}

		if ($_SESSION['role_action']['bank_account']['delete'] == 1) {

			$data_table .= 	'<td >'.$deleted.'</td>';
		}

		$data_table .=	'</tr>';

		$asData[] = $axRow;
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



$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_bank');

$oTmp->assign('content_file', 'financial/bank_account.htm');

$oTmp->display('layout/template.html');

$oTmp->assign('data', $asData);



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
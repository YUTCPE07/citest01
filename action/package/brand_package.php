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

if ($_SESSION['role_action']['package_brand']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$time_insert = date("Y-m-d H:i:s");

$id = $_REQUEST['id'];

$path_upload_logo = $_SESSION['path_upload_logo'];



# CHECK LOGIN

if ($_SESSION['user_type_id_ses'] != 1) {

	$sql = 'SELECT package_master.* FROM package_master WHERE pama_Deleted!="T" ORDER BY pama_PackageMasterID';

	$oRes = $oDB->Query($sql);

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		# DATA VIEW

		$sql_master = 'SELECT DISTINCT function.mema_MenuMasterID, 
						menu_master.mema_Name
						FROM function
						LEFT JOIN menu_master
						ON menu_master.mema_MenuMasterID = function.mema_MenuMasterID';;

		$oRes_master = $oDB->Query($sql_master);

		$data_view = '';

		while ($axRow_master = $oRes_master->FetchRow(DBI_ASSOC)){

			$data_check = 'SELECT package_function.pafu_PackageFunctionID 
							FROM package_function
							LEFT JOIN function
							ON function.func_FunctionID = package_function.func_FunctionID
							WHERE function.mema_MenuMasterID='.$axRow_master["mema_MenuMasterID"].'
							AND package_function.pama_PackageMasterID='.$axRow['pama_PackageMasterID'].'
							AND package_function.pafu_Deleted=""';

			$check = $oDB->QueryOne($data_check);

			if ($check) {

				$data_view .= '<tr bgcolor="#CCCCCC"><td colspan="2" style="text-align:center"><b>'.$axRow_master["mema_Name"].'</b></td></tr>';

				$sql_function = 'SELECT package_function.pafu_Deleted,
								package_function.func_FunctionID,
								function.func_Name,
								function.func_Description
								FROM package_function
								LEFT JOIN function
								ON package_function.func_FunctionID = function.func_FunctionID
								WHERE function.mema_MenuMasterID='.$axRow_master["mema_MenuMasterID"].' 
								AND package_function.pama_PackageMasterID='.$axRow["pama_PackageMasterID"].'
								AND package_function.pafu_Deleted=""
								AND function.func_Deleted!="T"';

				$oRes_function = $oDB->Query($sql_function);

				while ($axRow_func = $oRes_function->FetchRow(DBI_ASSOC)){

					$data_view .= '<tr>
									<td>'.$axRow_func['func_Name'].'</td>
									<td>'.$axRow_func['func_Description'].'</td>
									</tr>';
				}
			}
		}

		if ($data_view=="") {

			$data_view = '<tr><td colspan="2" style="text-align:center">No Function</td></tr>';
		}


		# VIEW

		if ($axRow['pama_RegisterPrice']==0) {	$axRow['pama_RegisterPrice'] = 'Free';	}
		else {	$axRow['pama_RegisterPrice'] = $axRow['pama_RegisterPrice'].' ฿';	}

		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['pama_PackageMasterID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
				<div class="modal fade" id="View'.$axRow['pama_PackageMasterID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>'.$axRow['pama_Name'].'</b></span>
						        <hr>
						        <center>
						        	<table width="100%" border="0">
						        		<tr>
						        			<td width="30%" style="text-align:right">Register Price/Member</td>
						        			<td style="text-align:center" width="5%">:</td>
						        			<td>'.$axRow['pama_RegisterPrice'].'</td>
						        			<td width="30%" style="text-align:right">Register Form</td>
						        			<td style="text-align:center" width="5%">:</td>
						        			<td>'.$axRow['pama_RegisterForm'].'</td>
						        		</tr>
						        		<tr>
						        			<td style="text-align:right">Max Member</td>
						        			<td style="text-align:center">:</td>
						        			<td>'.$axRow['pama_MaxMember'].'</td>
						        			<td style="text-align:right">Custom Field</td>
						        			<td style="text-align:center">:</td>
						        			<td>'.$axRow['pama_AddCustomField'].'</td>
						        		</tr>
						        		<tr>
						        			<td style="text-align:right">Max User</td>
						        			<td style="text-align:center">:</td>
						        			<td>'.$axRow['pama_MaxUser'].'</td>
						        			<td style="text-align:right">Motivation Redeem</td>
						        			<td style="text-align:center">:</td>
						        			<td>'.$axRow['pama_MotivationRedeem'].'</td>
						        		</tr>
						        		<tr>
						        			<td style="text-align:right">Payment Charge</td>
						        			<td style="text-align:center">:</td>
						        			<td>'.$axRow['pama_PaymentCharge'].' %</td>
						        			<td style="text-align:right">Price</td>
						        			<td style="text-align:center">:</td>
						        			<td>'.$axRow['pama_Price'].' ฿</td>
						        		</tr>
						        		<tr>
						        			<td style="text-align:right">Card Register</td>
						        			<td style="text-align:center">:</td>
						        			<td>'.$axRow['pama_CardRegister'].'</td>
						        			<td style="text-align:right">Maintainace Price</td>
						        			<td style="text-align:center">:</td>
						        			<td>'.$axRow['pama_MaintainacePrice'].' ฿</td>
						        		</tr>
						        	</table>
						        	<br>
						           	<table class="table table-striped table-bordered myPopupData">
								        <thead>
								        <tr class="th_table" style="text-align:center">
								            <th width="40%">Function Name</th>
								            <th>Description</th>
								        </tr></thead>
										<tbody>
											'.$data_view.'
										</tbody>
								    </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';


		# STATUS

		$status = '';

		$buy = '';

		$sql_status = 'SELECT pama_PackageMasterID FROM brand_package 
						WHERE brpa_Deleted!="T" 
						AND bran_BrandID="'.$_SESSION['user_brand_id'].'"';

		$package_data = $oDB->QueryOne($sql_status);

		if ($package_data == $axRow['pama_PackageMasterID']) {	

			$status = "Used";

			$buy = '';	

		} else {	

			$status = "No Used";

			$buy = '<button type="button" class="btn btn-default btn-sm">
					<span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>
					</button>';
		}


		# DETAIL

		$optional = '';

		if ($axRow['pama_MaxMember'] == 0) {	$axRow['pama_MaxMember'] = 'Unlimited'; }

		if ($axRow['pama_RegisterPrice'] == 0) {	$axRow['pama_RegisterPrice'] = 'Free'; }

		if ($axRow['pama_RegisterPrice'] == 30) {	

			$axRow['pama_RegisterPrice'] = 'Free 500 **';

			$axRow['pama_MaxMember'] = '>500';

			$optional = '** Free Member 500, Except 30 baht/member<br>And not over 1,000 member';
		}

		if ($axRow['pama_RegisterPrice'] > 30) {	

			$axRow['pama_RegisterPrice'] = $axRow['pama_RegisterPrice'].' baht/member';
		}

		$detail = '- Register Price Member is '.$axRow['pama_RegisterPrice'].'<br>';

		$detail .= '- Maximum Member '.$axRow['pama_MaxMember'].', User '.$axRow['pama_MaxUser'].'<br>';

		$detail .= '- '.$axRow['pama_CardRegister'].' Card Register<br>';

		if ($axRow['pama_MotivationRedeem'] == 'Point, Stamp') {

			$detail .= '- Advance Privilege and Motivation Redeem';

		} else {

			$detail .= '- Basic Privilege and 1 Collection Type Motivation Redeem';
		}


		# DATA TABLE

		$data_table .= '<tr >
							<td >'.$axRow['pama_Name'].'</td>
							<td >'.$detail.'</td>
							<td >'.$optional.'</td>
							<td style="text-align:right">'.$axRow['pama_Price'].'</td>
							<td style="text-align:center">'.$status.'</td>
							<td style="text-align:center">'.$view.'</td>
							<td style="text-align:center">'.$buy.'</td>
						</tr>';

		$asData[] = $axRow;
	}

} else {

	$sql = 'SELECT mi_brand.*, package_master.pama_Name, brand_package.brpa_UpdatedDate
			FROM mi_brand
			LEFT JOIN brand_package
			ON mi_brand.brand_id = brand_package.bran_BrandID 
			LEFT JOIN package_master
			ON brand_package.pama_PackageMasterID = package_master.pama_PackageMasterID 
			ORDER BY CASE 
				WHEN mi_brand.flag_del = "0" THEN 1
	            WHEN mi_brand.flag_del = "1" THEN 2 END ASC,
				mi_brand.flag_status ASC, 
				brand_package.brpa_UpdatedDate DESC';

	$oRes = $oDB->Query($sql);

	$asData = array();

	$data_table = '';

	$i = 0;

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;


		# LOGO BRAND

		if($axRow['logo_image']!=''){

			$axRow['logo_image'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" width="60" height="60"/>';

		} else {

			$axRow['logo_image'] = '<img src="../../images/400x400.png" width="60" height="60"/>';
		}



		# STATUS

		$status = '';

		if($axRow['flag_del']=='1'){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

		} else {

			if($axRow['flag_status']=='1'){

				$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';

			} else {

				$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
			}
		}



		# DATA TABLE

		$data_table .= '<tr >
							<td >'.$i.'</td>
							<td style="text-align:center"><a href="../brand/brand.php">'.$axRow['logo_image'].'</a><br>
								<span style="font-size:10px;">'.$axRow['name'].'</span></td>
							<td >'.$axRow['pama_Name'].'</td>
							<td style="text-align:center">'.$status.'</td>
							<td >'.DateTime($axRow['brpa_UpdatedDate']).'</td>
						</tr>';
	}
}





$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_package_brand');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('content_file', 'package/brand_package.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
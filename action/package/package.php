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

if ($_SESSION['role_action']['package']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$time_insert = date("Y-m-d H:i:s");
$Act = $_REQUEST['act'];
$id = $_REQUEST['id'];


$sql = 'SELECT package_master.*
	  	FROM package_master
		ORDER BY CASE 
			WHEN pama_Deleted = "" THEN 1
            WHEN pama_Deleted = "T" THEN 2 END ASC,
			pama_Status ASC, 
			pama_UpdatedDate DESC';


if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE package_master 
	 					SET pama_Status='Pending',
	 						pama_UpdatedDate='".$time_insert."',
	 						pama_UpdatedBy='".$_SESSION['UID']."' 
	 					WHERE pama_PackageMasterID='".$id."'";

 	$oDB->QueryOne($do_sql_status);
 	echo '<script>window.location.href="package.php";</script>';

} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE package_master 
	 					SET pama_Status='Active',
	 						pama_UpdatedDate='".$time_insert."',
	 						pama_UpdatedBy='".$_SESSION['UID']."' 
	 					WHERE pama_PackageMasterID='".$id."'";

 	$oDB->QueryOne($do_sql_status);
 	echo '<script>window.location.href="package.php";</script>';

} else if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT pama_Deleted FROM package_master WHERE pama_PackageMasterID ="'.$id.'"';

	$oRes = $oDB->Query($sql);
	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($axRow['pama_Deleted']=="") {

 		$do_sql_package = "UPDATE package_master 
	 							SET pama_Deleted='T', 
	 								pama_Status='Pending',
	 								pama_UpdatedDate='".$time_insert."',
	 								pama_UpdatedBy='".$_SESSION['UID']."' 
	 							WHERE pama_PackageMasterID='".$id."' ";

 	} else if ($axRow['pama_Deleted']=="T") {

		$do_sql_package = "UPDATE package_master 
								SET pama_Deleted='', 
	 								pama_Status='Pending',
	 								pama_UpdatedDate='".$time_insert."',
	 								pama_UpdatedBy='".$_SESSION['UID']."' 
								WHERE pama_PackageMasterID='".$id."'";
	}

 	$oDB->QueryOne($do_sql_package);
 	echo '<script>window.location.href="package.php";</script>';

} else {

	$oRes = $oDB->Query($sql);

	$i=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;

		# DELETED

		if($axRow['pama_Deleted']=='') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['pama_PackageMasterID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>
				<div class="modal fade" id="Deleted'.$axRow['pama_PackageMasterID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						            <span>
						            	<b>"'.$axRow['pama_Name'].'"</b><br>
						           </span>
						        </center>
						        <p style="padding-left:100px;"><br>
						            By clicking the <b>"Inactive"</b> button to:<br>
						            &nbsp; &nbsp;- Inactive this package<br>
						            &nbsp; &nbsp;- Change status to Pending
						        </p>
						    </div>
						    <div class="modal-footer">
						        <a href="package.php?act=delete&id='.$axRow['pama_PackageMasterID'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		} else if ($axRow['pama_Deleted']=='T') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['pama_PackageMasterID'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>
				<div class="modal fade" id="Deleted'.$axRow['pama_PackageMasterID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						            <span>
						            	<b>"'.$axRow['pama_Name'].'"</b><br>
						           </span>
						        </center>
						        <p style="padding-left:100px;"><br>
						           	By clicking the <b>"Active"</b> button to:<br>
						            &nbsp; &nbsp;- Active this package<br>
						            &nbsp; &nbsp;- Change status to Pending
						        </p>
						    </div>
						    <div class="modal-footer">
						        <a href="package.php?act=delete&id='.$axRow['pama_PackageMasterID'].'">
						        <button type="button" class="btn btn-default btn-sm">Active</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		}

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

				$data_view .= '<tr bgcolor="#CCCCCC"><td colspan="2" align="center"><b>'.$axRow_master["mema_Name"].'</b></td></tr>';

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

			$data_view = '<tr><td colspan="2" align="center">No Function</td></tr>';
		}

		# VIEW

		if ($axRow['pama_RegisterPrice']==0) {	$axRow['pama_RegisterPrice'] = 'Free';	}
		else {	$axRow['pama_RegisterPrice'] = $axRow['pama_RegisterPrice'].' Baht.';	}

		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['pama_PackageMasterID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
				<div class="modal fade" id="View'.$axRow['pama_PackageMasterID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>'.$axRow['pama_Name'].'</b></span>
						        <hr>
						        <center>
						        	<table width="100%" class="myPopup">
						        		<tr>
						        			<td width="30%" align="right">Register Price/Member</td>
						        			<td align="center" width="5%">:</td>
						        			<td>'.$axRow['pama_RegisterPrice'].'</td>
						        			<td width="30%" align="right">Register Form</td>
						        			<td align="center" width="5%">:</td>
						        			<td>'.$axRow['pama_RegisterForm'].'</td>
						        		</tr>
						        		<tr>
						        			<td align="right">Max Member</td>
						        			<td align="center">:</td>
						        			<td>'.$axRow['pama_MaxMember'].'</td>
						        			<td align="right">Custom Field</td>
						        			<td align="center">:</td>
						        			<td>'.$axRow['pama_AddCustomField'].'</td>
						        		</tr>
						        		<tr>
						        			<td align="right">Max User</td>
						        			<td align="center">:</td>
						        			<td>'.$axRow['pama_MaxUser'].'</td>
						        			<td align="right">Motivation Redeem</td>
						        			<td align="center">:</td>
						        			<td>'.$axRow['pama_MotivationRedeem'].'</td>
						        		</tr>
						        		<tr>
						        			<td align="right">Payment Charge</td>
						        			<td align="center">:</td>
						        			<td>'.$axRow['pama_PaymentCharge'].' %</td>
						        			<td align="right">Price</td>
						        			<td align="center">:</td>
						        			<td>'.$axRow['pama_Price'].' Baht.</td>
						        		</tr>
						        		<tr>
						        			<td align="right">Card Register</td>
						        			<td align="center">:</td>
						        			<td>'.$axRow['pama_CardRegister'].'</td>
						        			<td align="right">Maintainace Price</td>
						        			<td align="center">:</td>
						        			<td>'.$axRow['pama_MaintainacePrice'].' Baht.</td>
						        		</tr>
						        	</table>
						        	<br>
						           	<table class="table table-striped table-bordered myPopupData">
								        <thead>
								        <tr class="th_table" align="center">
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
						        <a href="package_create.php?act=edit&id='.$axRow['pama_PackageMasterID'].'">
						        <button type="button" class="btn btn-default btn-sm">Edit</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		# STATUS

		$status = '';

		if($axRow['pama_Deleted']=='T'){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

		} else {

			if($axRow['pama_Status']=='Active'){

				if ($_SESSION['role_action']['package']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'package.php?act=active&id='.$axRow['pama_PackageMasterID'].'\'">
		                    <option class="status_default" value="'.$axRow['pama_PackageMasterID'].'" selected>On</option>
		                    <option class="status_default">Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';
		        }

			} else {

				if ($_SESSION['role_action']['package']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_pending" name="pending_status" onchange="window.location.href=\'package.php?act=pending&id='.$axRow['pama_PackageMasterID'].'\'">
		                    <option class="status_default">On</option>
		                    <option class="status_default" value="'.$axRow['pama_PackageMasterID'].'" selected>Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
		        }
			}
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

		$data_table .= '
						<tr >
							<td >'.$i.'</td>
							<td >'.$axRow['pama_Name'].'</td>
							<td >'.$detail.'</td>
							<td >'.$optional.'</td>
							<td style="text-align:right">'.$axRow['pama_Price'].'</td>
							<td >'.$status.'</td>
							<td >'.DateTime($axRow['pama_UpdatedDate']).'</td>';

		if ($_SESSION['role_action']['package']['view'] == 1) {

			$data_table .=	'<td style="text-align:center">'.$view.'</td>';
		}

		if ($_SESSION['role_action']['package']['delete'] == 1) {

			$data_table .= 	'<td style="text-align:center">'.$deleted.'</td>';
		}

		$data_table .=	'</tr>';

		$asData[] = $axRow;
	}
}


$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_package');

$oTmp->assign('content_file', 'package/package.htm');

$oTmp->display('layout/template.html');


//========================================//


$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}


//========================================//

?>
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

if ($_SESSION['role_action']['memberin_action']['view'] != 1) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//



$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];


$sql = 'SELECT 
		memberin_action.*
		FROM memberin_action 
		ORDER BY memberin_action.miac_UpdatedDate DESC';



if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE memberin_action 
	 					SET miac_Status='F',
	 						miac_UpdatedDate='".$time_insert."' 
	 					WHERE miac_MemberinActionID='".$id."'";

 	$oDB->QueryOne($do_sql_status);
 	echo '<script>window.location.href="action.php";</script>';


} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE memberin_action 
	 					SET miac_Status='T',
	 						miac_UpdatedDate='".$time_insert."' 
	 					WHERE miac_MemberinActionID='".$id."'";

 	$oDB->QueryOne($do_sql_status);
 	echo '<script>window.location.href="action.php";</script>';


} else if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT miac_Deleted FROM memberin_action WHERE miac_MemberinActionID ="'.$id.'"';

	$oRes = $oDB->Query($sql);
	$axRow = $oRes->FetchRow(DBI_ASSOC);
		
	if($axRow['miac_Deleted']=='') {
 				
 		$do_sql_type = "UPDATE memberin_action
 							SET miac_Deleted='T', 
 							miac_Status='F',
 							miac_UpdatedDate='".$time_insert."' 
 							WHERE miac_MemberinActionID='".$id."'";

 	} else if ($axRow['miac_Deleted']=='T') {

		$do_sql_type = "UPDATE memberin_action
 							SET miac_Deleted='', 
 							miac_Status='F',
 							miac_UpdatedDate='".$time_insert."' 
 							WHERE miac_MemberinActionID='".$id."'";
	}

 	$oDB->QueryOne($do_sql_type);
 			
 	echo '<script>window.location.href="action.php";</script>';




} else {


	$oRes = $oDB->Query($sql);

	$i=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;

		if ($axRow['miac_Description']=='') { $axRow['miac_Description'] = '-'; }


		# STATUS

		$status = '';

		if($axRow['miac_Deleted']=='T'){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

		} else {

			if($axRow['miac_Status']=='T'){

				if ($_SESSION['role_action']['memberin_action']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'action.php?act=active&id='.$axRow['miac_MemberinActionID'].'\'">
		                    <option class="status_default" value="'.$axRow['miac_MemberinActionID'].'" selected>On</option>
		                    <option class="status_default">Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';
		        }

			} else {

				if ($_SESSION['role_action']['memberin_action']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_pending" name="pending_status" onchange="window.location.href=\'action.php?act=pending&id='.$axRow['miac_MemberinActionID'].'\'">
		                    <option class="status_default">On</option>
		                    <option class="status_default" value="'.$axRow['miac_MemberinActionID'].'" selected>Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
		        }
			}

		}


		# DELETED

		if($axRow['miac_Deleted']=='') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['miac_MemberinActionID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>

				<div class="modal fade" id="Deleted'.$axRow['miac_MemberinActionID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b><span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td style="text-align:center;font-size:14px">
								        	<b>"'.$axRow['miac_Name'].'"</b><br>
								            By clicking the <b>"Inactive"</b> button to:<br>
								            &nbsp; &nbsp;- Inactive this action<br>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="action.php?act=delete&id='.$axRow['miac_MemberinActionID'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		
		} else if ($axRow['miac_Deleted']=='T') {
				
			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['miac_MemberinActionID'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>

				<div class="modal fade" id="Deleted'.$axRow['miac_MemberinActionID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b><span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td style="text-align:center;font-size:14px">
								        	<b>"'.$axRow['miac_Name'].'"</b><br>
								           	By clicking the <b>"Active"</b> button to:<br>
								            &nbsp; &nbsp;- Active this action<br>
								            &nbsp; &nbsp;- Change status to Pending
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="action.php?act=delete&id='.$axRow['miac_MemberinActionID'].'">
						        <button type="button" class="btn btn-default btn-sm">Active</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		}




		# DATA TABLE

		$data_table .= '<tr >

							<td >'.$i.'</td>

							<td >'.$axRow['miac_Name'].'</td>

							<td >'.$axRow['miac_Type'].'</td>

							<td >'.nl2br($axRow['miac_Description']).'</td>

							<td style="text-align:center">'.$status.'</td>

							<td >'.DateTime($axRow['miac_UpdatedDate']).'</td>';

		if ($_SESSION['role_action']['motivation_action']['edit'] == 1) {

			if ($axRow['brand_id']==0 && $_SESSION['user_type_id_ses']>1) { $edit='disabled';	} else { $edit='';	}

			$data_table .=	'<td style="text-align:center">
							<a href="action_create.php?act=edit&id='.$axRow['miac_MemberinActionID'].'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></a></td>';
		}

		if ($_SESSION['role_action']['motivation_action']['delete'] == 1) {

			$data_table .= 	'<td style="text-align:center">'.$deleted.'</td>';
		}
						
		$data_table .=	'</tr>';


		$asData[] = $axRow;

	}
}




$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_memberin_action');

$oTmp->assign('content_file', 'memberin_motivation/action.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
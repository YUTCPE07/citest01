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

if ($_SESSION['role_action']['variety']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];



$sql = 'SELECT variety.*,
			variety_category.vaca_NameEn,
			variety_category.vaca_Type

  		FROM variety 

		LEFT JOIN variety_category
		ON vaca_VarietyCategoryID = vari_VarietyCategoryID

		ORDER BY CASE 
			WHEN vari_Deleted = "0" THEN 1
            WHEN vari_Deleted = "1" THEN 2 END ASC,
			vari_Status ASC, 
			vari_UpdatedDate DESC';

$oRes = $oDB->Query($sql) or die(mysql_error($sql));



if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE variety 
	 					SET vari_Status='2',
	 						vari_UpdatedDate='".$time_insert."' 
	 					WHERE vari_VarietyID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="variety.php";</script>';



} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE variety
	 					SET vari_Status='1',
	 						vari_UpdatedDate='".$time_insert."'
	 					WHERE vari_VarietyID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="variety.php";</script>';



} else if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql_delete = 'SELECT vari_Deleted FROM variety WHERE vari_VarietyID ="'.$id.'"';

	$oRes = $oDB->Query($sql_delete);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($axRow['vari_Deleted']=='1') {

 		$do_sql_variety = "UPDATE variety 
 							SET vari_Deleted='0', 
 							vari_Status='2',
 							vari_UpdatedDate='".$time_insert."'
 							WHERE vari_VarietyID='".$id."'";

 	} else if ($axRow['vari_Deleted']=='0') {

		$do_sql_variety = "UPDATE variety 
							SET vari_Deleted='1',
 							vari_Status='2',
 							vari_UpdatedDate='".$time_insert."'
							WHERE vari_VarietyID='".$id."'";
	}

	$oDB->QueryOne($do_sql_variety);

 	echo '<script>window.location.href="variety.php";</script>';



} else {


	$oRes = $oDB->Query($sql);

	$i=0;

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;

		# VARIETY IMAGE

		if($axRow['vari_Image']!=''){

			$variety_image = '<img src="../../upload/'.$axRow['vari_ImagePath'].$axRow['vari_Image'].'" width="70" height="70"/>';

			$variety_del = '<img src="../../upload/'.$axRow['vari_ImagePath'].$axRow['vari_Image'].'" width="100" height="100"/>';

		} else {

			$variety_image = '<img src="../../images/400x400.png" width="70" height="70"/>';

			$variety_del = '<img src="../../images/400x400.png" width="100" height="100"/>';
		}


		# LOGO

		// if($axRow['logo_image']!=''){

		// 	$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';

		// } else {

		// 	$logo_brand = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';
		// }



		# DELETED

		if($axRow['vari_Deleted']=='0') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['vari_VarietyID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>
				<div class="modal fade" id="Deleted'.$axRow['vari_VarietyID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b><span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="140px" style="text-align:center" valign="top">'.$variety_del.'</td>
						        	<td>
								        <p style="font-size:14px;padding-left:10px;">
								        	<b>"'.$axRow['vari_Title'].'"</b><br>
								            By clicking the <b>"Inactive"</b> button to:<br>
								            &nbsp; &nbsp;- Inactive this variety<br>
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="variety.php?act=delete&id='.$axRow['vari_VarietyID'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		} else if ($axRow['vari_Deleted']=='1') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['vari_VarietyID'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>
				<div class="modal fade" id="Deleted'.$axRow['vari_VarietyID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b><span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="140px" style="text-align:center" valign="top">'.$variety_del.'</td>
						        	<td>
								        <p style="font-size:14px;padding-left:10px;">
								        	<b>"'.$axRow['vari_Title'].'"</b><br>
								           	By clicking the <b>"Active"</b> button to:<br>
								            &nbsp; &nbsp;- Active this variety<br>
								            &nbsp; &nbsp;- Change status to Pending
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="variety.php?act=delete&id='.$axRow['vari_VarietyID'].'">
						        <button type="button" class="btn btn-default btn-sm">Active</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		}



		# STATUS

		$status = '';

		if($axRow['vari_Deleted']=='1'){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

		} else {

			if($axRow['vari_Status']=='1'){

				if ($_SESSION['role_action']['variety']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'variety.php?act=active&id='.$axRow['vari_VarietyID'].'\'">
		                    <option class="status_default" value="'.$axRow['vari_VarietyID'].'" selected>On</option>
		                    <option class="status_default">Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';
		        }

			} else {

				if ($_SESSION['role_action']['variety']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_pending" name="pending_status" onchange="window.location.href=\'variety.php?act=pending&id='.$axRow['vari_VarietyID'].'\'">
		                    <option class="status_default">On</option>
		                    <option class="status_default" value="'.$axRow['vari_VarietyID'].'" selected>Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
		        }
			}
		}


		$data_table .= '<tr >
							<td >'.$i.'</td>
							<td style="text-align:center">'.$variety_image.'</td>
							<td >'.$axRow['vari_Title'].'</td>
							<td >'.$axRow['vaca_NameEn'].'</td>
							<td >'.$axRow['vaca_Type'].'</td>
							<td >'.$status.'</td>
							<td >'.DateTime($axRow['vari_UpdatedDate']).'</td>';

		if ($_SESSION['role_action']['variety']['edit'] == 1) {

			$data_table .=	'<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href='variety_create.php?act=edit&id=".$axRow['vari_VarietyID']."'".'">
				<button type="button" class="btn btn-default btn-sm">
				<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></span></td>';
		}

		if ($_SESSION['role_action']['variety']['delete'] == 1) {

			$data_table .= 	'<td style="text-align:center">'.$deleted.'</td>';
		}

		$data_table .=	'</tr>';

		$asData[] = $axRow;
	}
}




$oTmp->assign('data_table', $data_table);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_variety');

$oTmp->assign('content_file', 'variety/variety.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
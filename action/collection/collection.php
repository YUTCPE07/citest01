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

if ($_SESSION['role_action']['collection']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$time_insert = date("Y-m-d H:i:s");

$path_upload_collection = $_SESSION['path_upload_collection'];

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$sql = "SELECT
		collection_type.*,
		mi_user.username

		FROM collection_type

		LEFT JOIN mi_user
		ON mi_user.user_id = collection_type.coty_UpdatedBy

		ORDER BY CASE 
			WHEN collection_type.coty_Deleted = '' THEN 1
            WHEN collection_type.coty_Deleted = 'T' THEN 2 END ASC,
			collection_type.coty_Status DESC, 
			collection_type.coty_UpdatedDate DESC";


if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE collection_type 
	 					SET coty_Status='F',
	 						coty_UpdatedDate='".$time_insert."' 
	 					WHERE coty_CollectionTypeID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="collection.php";</script>';

} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE collection_type 
	 					SET coty_Status='T',
	 						coty_UpdatedDate='".$time_insert."'
	 					WHERE coty_CollectionTypeID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="collection.php";</script>';

} else if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql_delete = 'SELECT coty_Deleted
					FROM collection_type
					WHERE coty_CollectionTypeID ="'.$id.'"';

	$oRes = $oDB->Query($sql_delete);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($axRow['coty_Deleted']=='T') {

 		$do_sql_collection = "UPDATE collection_type 
 								SET coty_Deleted='', 
 								coty_Status='F',
 								coty_UpdatedDate='".$time_insert."'
 								WHERE coty_CollectionTypeID='".$id."'";

 	} else if ($axRow['coty_Deleted']=='') {

		$do_sql_collection = "UPDATE collection_type 
								SET coty_Deleted='T',
 								coty_Status='F',
 								coty_UpdatedDate='".$time_insert."'
								WHERE coty_CollectionTypeID='".$id."'";
	}

	$oDB->QueryOne($do_sql_collection);

 	echo '<script>window.location.href="collection.php";</script>';

} else {

	$oRes = $oDB->Query($sql);

	$i=0;

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;


		# COLLECTION IMAGE

		if($axRow['coty_Image']!=''){

			$collect_image = '<img src="'.$path_upload_collection.$axRow['coty_Image'].'" width="50" height="50"/>';

			$collect_del = '<img src="'.$path_upload_collection.$axRow['coty_Image'].'" width="100" height="100"/>';

		} else {

			$collect_image = '<img src="../../images/400x400.png" width="50" height="50"/>';

			$collect_del = '<img src="../../images/400x400.png" width="100" height="100"/>';
		}



		# STATUS

		$status = '';

		if($axRow['coty_Deleted']=='T'){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

		} else {

			if($axRow['coty_Status']=='T'){

				if ($_SESSION['role_action']['collection']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
								<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'collection.php?act=active&id='.$axRow['coty_CollectionTypeID'].'\'">
				                    <option class="status_default" value="'.$axRow['coty_CollectionTypeID'].'" selected>On</option>
				                    <option class="status_default">Off</option>
				                </select>
				            </form>';
		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';
		        }

			} else {

				if ($_SESSION['role_action']['collection']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
								<select class="form-control text-md status_pending" name="pending_status" onchange="window.location.href=\'collection.php?act=pending&id='.$axRow['coty_CollectionTypeID'].'\'">
				                    <option class="status_default">On</option>
				                    <option class="status_default" value="'.$axRow['coty_CollectionTypeID'].'" selected>Off</option>
				                </select>
				            </form>';
		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
		        }
			}
		}


		# DELETED

		if($axRow['coty_Deleted']=='') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['coty_CollectionTypeID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>
				<div class="modal fade" id="Deleted'.$axRow['coty_CollectionTypeID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b><span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="120px" style="text-align:center">'.$collect_del.'</td>
						        	<td>
								        <p style="font-size:14px;padding-left:10px;">
								        	<b>"'.$axRow['coty_Name'].'"</b><br>
								            By clicking the <b>"Inactive"</b> button to:<br>
								            &nbsp; &nbsp;- Inactive this collection
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="collection.php?act=delete&id='.$axRow['coty_CollectionTypeID'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		} else if ($axRow['coty_Deleted']=='T') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['coty_CollectionTypeID'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>
				<div class="modal fade" id="Deleted'.$axRow['coty_CollectionTypeID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b><span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="120px" style="text-align:center">'.$collect_del.'</td>
						        	<td>
								        <p style="font-size:14px;padding-left:10px;">
								        	<b>"'.$axRow['coty_Name'].'"</b><br>
								           	By clicking the <b>"Active"</b> button to:<br>
								            &nbsp; &nbsp;- Active this collection<br>
								            &nbsp; &nbsp;- Change status to Pending
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="collection.php?act=delete&id='.$axRow['coty_CollectionTypeID'].'">
						        <button type="button" class="btn btn-default btn-sm">Active</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		}


		# TABLE

		$data_table .= '<tr>
							<td >'.$i.'</td>
							<td style="text-align:center">'.$collect_image.'</td>
							<td >'.$axRow['coty_Name'].'</td>
							<td style="text-align:center">'.$axRow['coty_Type'].'</td>
							<td style="text-align:center">'.$status.'</td>
							<td style="text-align:center">'.DateTime($axRow['coty_UpdatedDate']).'</td>
							<td style="text-align:center">'.$axRow['username'].'</td>';

		if ($_SESSION['role_action']['collection']['edit'] == 1) {

			$data_table .= '<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href='collection_create.php?act=edit&id=".$axRow['coty_CollectionTypeID']."'".'">
				<button type="button" class="btn btn-default btn-sm">
				<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></span></td>';
		}

		if ($_SESSION['role_action']['collection']['delete'] == 1) {

			$data_table .= 	'<td style="text-align:center">'.$deleted.'</td>';
		}

		$data_table .= '</tr>';

		$asData[] = $axRow;
	}
}





$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_collection');

$oTmp->assign('content_file', 'collection/collection.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
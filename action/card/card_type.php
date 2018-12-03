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

if ($_SESSION['role_action']['card_type']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//



$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$path_upload_logo = $_SESSION['path_upload_logo'];



$where_brand = '';

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' WHERE mi_card_type.brand_id IN (0,'.$_SESSION['user_brand_id'].') AND mi_card_type.flag_del=0';
}



$sql = 'SELECT 

		mi_card_type.*,
		mi_brand.name AS brand_name,
		mi_brand.logo_image

		FROM mi_card_type

		LEFT JOIN mi_brand
		ON mi_brand.brand_id = mi_card_type.brand_id

		'.$where_brand.' 

		ORDER BY CASE 
			WHEN mi_card_type.flag_del = "0" THEN 1
	        WHEN mi_card_type.flag_del = "1" THEN 2 END ASC,
			mi_card_type.flag_status ASC, 
			mi_card_type.date_update DESC';


if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE mi_card_type 
	 					SET flag_status='2',
	 						date_update='".$time_insert."' 
	 					WHERE card_type_id='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="card_type.php";</script>';


} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE mi_card_type 
	 					SET flag_status='1',
	 						date_update='".$time_insert."' 
	 					WHERE card_type_id='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="card_type.php";</script>';


} else if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT flag_del FROM mi_card_type WHERE card_type_id ="'.$id.'"';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);


	if($axRow['flag_del']=='0') {

 		$do_sql_type = "UPDATE mi_card_type
 							SET flag_del='1', 
 							flag_status='2',
 							date_update='".$time_insert."' 
 							WHERE card_type_id='".$id."'";

 	} else if ($axRow['flag_del']=='1') {

		$do_sql_type = "UPDATE mi_card_type
 							SET flag_del='', 
 							flag_status='2',
 							date_update='".$time_insert."' 
 							WHERE card_type_id='".$id."'";
	}

 	$oDB->QueryOne($do_sql_type);

 	echo '<script>window.location.href="card_type.php";</script>';

} else {

	$oRes = $oDB->Query($sql);

	$i=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;


		# STATUS

		$status = '';

		if($axRow['flag_del']=='1'){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

		} else {

			if($axRow['flag_status']=='1'){

				if ($_SESSION['role_action']['card_type']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
								<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'card_type.php?act=active&id='.$axRow['card_type_id'].'\'">
				                    <option class="status_default" value="'.$axRow['card_type_id'].'" selected>On</option>
				                    <option class="status_default">Off</option>
				                </select>
				            </form>';
		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';
		        }

			} else {

				if ($_SESSION['role_action']['card_type']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
								<select class="form-control text-md status_pending" name="pending_status" onchange="window.location.href=\'card_type.php?act=pending&id='.$axRow['card_type_id'].'\'">
				                    <option class="status_default">On</option>
				                    <option class="status_default" value="'.$axRow['card_type_id'].'" selected>Off</option>
				                </select>
				            </form>';
		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
		        }
			}
		}


		# LOGO

		if($axRow['logo_image']!=''){

			$logo_brand = '<img src="'.$path_upload_logo.$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';

			$logo_view = '<img src="'.$path_upload_logo.$axRow['logo_image'].'" class="image_border" width="150" height="150"/>';

		} else {

			$logo_brand = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';

			$logo_view = '<img src="../../images/400x400.png" width="150" height="150"/>';
		}

		if($axRow['brand_id']=='0'){

			$logo_brand = '<img src="../../images/mi_action_logo.png" class="image_border" width="60" height="60"/>';

			$logo_view = '<img src="../../images/mi_action_logo.png" class="image_border" width="150" height="150"/>';

			$axRow['brand_name'] = 'MemberIn';
		}


		# DELETED

		if($axRow['flag_del']=='0') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['card_type_id'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>
				<div class="modal fade" id="Deleted'.$axRow['card_type_id'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b><span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup"><tr>
						        	<td width="140px" style="text-align:center" valign="top">'.$logo_view.'</td>
						        	<td>
								        <p style="font-size:14px;padding-left:10px;">
								        	<b>"'.$axRow['name'].'"</b><br>
								            By clicking the <b>"Inactive"</b> button to:<br>
								            &nbsp; &nbsp;- Inactive this card<br>
								        </p>
								    </td>
						        </tr></table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="card_type.php?act=delete&id='.$axRow['card_type_id'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		} else if ($axRow['flag_del']=='1') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['card_type_id'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>
				<div class="modal fade" id="Deleted'.$axRow['card_type_id'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b><span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup"><tr>
						        	<td width="140px" style="text-align:center" valign="top">'.$logo_view.'</td>
						        	<td>
								        <p style="font-size:14px;padding-left:10px;">
								        	<b>"'.$axRow['mame'].'"</b><br>
								           	By clicking the <b>"Active"</b> button to:<br>
								            &nbsp; &nbsp;- Active this card<br>
								            &nbsp; &nbsp;- Change status to Pending
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="card_type.php?act=delete&id='.$axRow['card_type_id'].'">
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
							<td style="text-align:center">'.$logo_brand.'<br>
								<span style="font-size:11px;">'.$axRow['brand_name'].'</span>
							</td>
							<td >'.$axRow['name'].'</td>
							<td >'.$axRow['description'].'</td>
							<td style="text-align:center">'.$status.'</td>
							<td >'.DateTime($axRow['date_update']).'</td>';

		if ($_SESSION['role_action']['card_type']['edit'] == 1) {

			if ($axRow['brand_id']==0 && $_SESSION['user_type_id_ses']>1) { $edit='disabled';	} else { $edit='';	}

			$data_table .=	'<td style="text-align:center">
							<a href="card_type_create.php?act=edit&id='.$axRow['card_type_id'].'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></a></td>';
		}

		if ($_SESSION['role_action']['card_type']['delete'] == 1) {

			$data_table .= 	'<td style="text-align:center">'.$deleted.'</td>';
		}
	
		$data_table .=	'</tr>';

		$asData[] = $axRow;
	}
}



$oTmp->assign('course_name_s', $course_name_s);

$oTmp->assign('location_s', $location_s);

$oTmp->assign('num_row_all', $num_row_all);

$oTmp->assign('pagintion', $pagintion);

$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_card_type');

$oTmp->assign('content_file', 'card/card_type.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
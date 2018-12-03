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

if ($_SESSION['role_action']['variety_category']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];


$sql = 'SELECT variety_category.*
  		FROM variety_category
		ORDER BY CASE 
			WHEN vaca_Deleted = "0" THEN 1
            WHEN vaca_Deleted = "1" THEN 2 END ASC,
			vaca_Status ASC, 
			vaca_UpdatedDate DESC';

$oRes = $oDB->Query($sql) or die(mysql_error($sql));



if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE variety_category
	 					SET vaca_Status='2',
	 						vaca_UpdatedDate='".$time_insert."' 
	 					WHERE vaca_VarietyCategoryID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="variety_category.php";</script>';


} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE variety_category
	 					SET vaca_Status='1',
	 						vaca_UpdatedDate='".$time_insert."'
	 					WHERE vaca_VarietyCategoryID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="variety_category.php";</script>';


} else if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql_delete = 'SELECT vaca_Deleted FROM variety_category WHERE vaca_VarietyCategoryID ="'.$id.'"';

	$oRes = $oDB->Query($sql_delete);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($axRow['vaca_Deleted']=='1') {

 		$do_sql_variety = "UPDATE variety_category 
 							SET vaca_Deleted='0', 
 							vaca_Status='2',
 							vaca_UpdatedDate='".$time_insert."'
 							WHERE vaca_VarietyCategoryID='".$id."'";

 	} else if ($axRow['vaca_Deleted']=='0') {

		$do_sql_variety = "UPDATE variety_category 
							SET vaca_Deleted='1',
 							vaca_Status='2',
 							vaca_UpdatedDate='".$time_insert."'
							WHERE vaca_VarietyCategoryID='".$id."'";
	}

	$oDB->QueryOne($do_sql_variety);

 	echo '<script>window.location.href="variety_category.php";</script>';



} else {

	$oRes = $oDB->Query($sql);

	$i=0;

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;


		# MAIN CATEGORY BRAND

		$sql_category = 'SELECT name
							FROM mi_main_category_brand 
							WHERE main_category_brand_id IN ('.$axRow["vaca_MainCategoryBrandID"].')';

		$oRes_category = $oDB->Query($sql_category);
		$data_category = '';
		$x = 0;

		while ($category = $oRes_category->FetchRow(DBI_ASSOC)){

			if ($x==0) { $data_category .= $category['name']; } 
			else { $data_category .= '<br>'.$category['name']; }

			$x++;
		}


		# DELETED

		if($axRow['vaca_Deleted']=='0') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['vaca_VarietyCategoryID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>
				<div class="modal fade" id="Deleted'.$axRow['vaca_VarietyCategoryID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b><span>
						        <hr>
						        <center>
								    <b>"'.$axRow['vaca_Name'].'"</b><br>
								    By clicking the <b>"Inactive"</b> button to:<br>
								    &nbsp; &nbsp;- Inactive this variety category
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="variety_category.php?act=delete&id='.$axRow['vaca_VarietyCategoryID'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		} else if ($axRow['vaca_Deleted']=='1') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['vaca_VarietyCategoryID'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>
				<div class="modal fade" id="Deleted'.$axRow['vaca_VarietyCategoryID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b><span>
						        <hr>
						        <center>
						        	<b>"'.$axRow['vaca_Name'].'"</b><br>
								    By clicking the <b>"Active"</b> button to:<br>
								    &nbsp; &nbsp;- Active this variety category<br>
								    &nbsp; &nbsp;- Change status to Pending
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="variety_category.php?act=delete&id='.$axRow['vaca_VarietyCategoryID'].'">
						        <button type="button" class="btn btn-default btn-sm">Active</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		}



		# STATUS

		$status = '';

		if($axRow['vaca_Deleted']=='1'){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

		} else {

			if($axRow['vaca_Status']=='1'){

				if ($_SESSION['role_action']['variety_category']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'variety_category.php?act=active&id='.$axRow['vaca_VarietyCategoryID'].'\'">
		                    <option class="status_default" value="'.$axRow['vaca_VarietyCategoryID'].'" selected>On</option>
		                    <option class="status_default">Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';
		        }

			} else {

				if ($_SESSION['role_action']['variety_category']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_pending" name="pending_status" onchange="window.location.href=\'variety_category.php?act=pending&id='.$axRow['vaca_VarietyCategoryID'].'\'">
		                    <option class="status_default">On</option>
		                    <option class="status_default" value="'.$axRow['vaca_VarietyCategoryID'].'" selected>Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
		        }
			}
		}


		$data_table .= '<tr >
							<td >'.$i.'</td>
							<td>'.$axRow['vaca_Name'].'</td>
							<td>'.$axRow['vaca_NameEn'].'</td>
							<td>'.$axRow['vaca_Type'].'</td>
							<td>'.$data_category.'</td>
							<td style="text-align:center">'.$status.'</td>
							<td >'.DateTime($axRow['vaca_UpdatedDate']).'</td>';

		if ($_SESSION['role_action']['variety_category']['edit'] == 1) {

			$data_table .=	'<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href='variety_category_create.php?act=edit&id=".$axRow['vaca_VarietyCategoryID']."'".'">
				<button type="button" class="btn btn-default btn-sm">
				<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></span></td>';
		}

		$data_table .= '	<td style="text-align:center">'.$deleted.'</td>
						</tr>';

		$asData[] = $axRow;
	}
}



$oTmp->assign('data_table', $data_table);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_variety_category');

$oTmp->assign('type_file_upload', '<span class="text-rq">Type file : .jpg , .png , .gif only &nbsp | &nbsp Size : 640 x 400 px</span>');

$oTmp->assign('content_file', 'variety/variety_category.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
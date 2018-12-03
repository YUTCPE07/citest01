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

if ($_SESSION['role_action']['brand_category']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$time_insert = date("Y-m-d H:i:s");
$Act = $_REQUEST['act'];
$id = $_REQUEST['id'];

$sql = 'SELECT cb.*,
		mb.name AS main_name,
		mb.name_en AS main_name_en
  		FROM mi_category_brand AS cb
  		LEFT JOIN mi_main_category_brand AS mb
  		ON cb.main_category_id = mb.main_category_brand_id
		ORDER BY CASE 
			WHEN flag_del = "" THEN 1
	        WHEN flag_del = "T" THEN 2 END ASC,
			flag_status ASC, 
			update_date DESC';

if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE mi_category_brand 
	 					SET flag_status='2',
	 						update_date='".$time_insert."' 
	 					WHERE category_brand_id='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="brand_category.php";</script>';

} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE mi_category_brand 
	 					SET flag_status='1',
	 						update_date='".$time_insert."' 
	 					WHERE catgory_brand_id='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="brand_category.php";</script>';

} else if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT flag_del FROM mi_category_brand WHERE category_brand_id ="'.$id.'"';
	$oRes = $oDB->Query($sql);
	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($axRow['flag_del']=='') {

 		$do_sql_category = "UPDATE mi_category_brand
 							SET flag_del='T', 
 							flag_status='2',
 							update_date='".$time_insert."' 
 							WHERE category_brand_id='".$id."'";

 	} else if ($axRow['flag_del']=='T') {

		$do_sql_category = "UPDATE mi_category_brand
 							SET flag_del='', 
 							flag_status='2',
 							update_date='".$time_insert."' 
 							WHERE category_brand_id='".$id."'";
	}

 	$oDB->QueryOne($do_sql_category);
 	echo '<script>window.location.href="brand_category.php";</script>';

} else {

	$oRes = $oDB->Query($sql);

	$data_table = '';

	$i=0;

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;


		# STATUS

		$status = '';

		if($axRow['flag_del']=='T'){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

			$approve = '<button style="width:50px;" class="form-control text-md status_inactive" disabled>No</button>';

			$approve_sel = $approve;

		} else {

			if($axRow['flag_status']=='1'){

				if ($_SESSION['role_action']['brand_category']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
								<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'brand_category.php?act=active&id='.$axRow['category_brand_id'].'\'">
				                    <option class="status_default" value="'.$axRow['category_brand_id'].'" selected>On</option>
				                    <option class="status_default">Off</option>
				                </select>
				            </form>';
		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';
		        }

			} else {

				if ($_SESSION['role_action']['brand_category']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
								<select class="form-control text-md status_pending" name="approve_status" onchange="window.location.href=\'brand_category.php?act=pending&id='.$axRow['category_brand_id'].'\'">
				                    <option class="status_default">On</option>
				                    <option class="status_default" value="'.$axRow['category_brand_id'].'" selected>Off</option>
				                </select>
				            </form>';
		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
		        }
			}
		}


		# DELETED

		if($axRow['flag_del']=='') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['category_brand_id'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>
				<div class="modal fade" id="Deleted'.$axRow['category_brand_id'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b><span>
						        <hr>
						        <center>
						        <p style="font-size:14px;padding-left:10px;">
								   <b>"'.$axRow['name'].' / '.$axRow['name_en'].'"</b><br>
								   By clicking the <b>"Inactive"</b> button to: Inactive this category<br>
								</p>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="brand_category.php?act=delete&id='.$axRow['category_brand_id'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		} else if ($axRow['flag_del']=='T') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['category_brand_id'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>
				<div class="modal fade" id="Deleted'.$axRow['category_brand_id'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b><span>
						        <hr>
						        <center>
						        <p style="font-size:14px;">
								    <b>"'.$axRow['name'].' / '.$axRow['name_en'].'"</b><br>
								    By clicking the <b>"Active"</b> button to:<br>
								    &nbsp; &nbsp;- Active this category<br>
								    &nbsp; &nbsp;- Change status to Pending
								</p>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="brand_category.php?act=delete&id='.$axRow['category_brand_id'].'">
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
							<td >'.$axRow['name'].' / '.$axRow['name_en'].'</td>
							<td >'.$axRow['main_name'].' / '.$axRow['main_name_en'].'</td>
							<td style="text-align:center">'.$status.'</td>
							<td >'.DateTime($axRow['update_date']).'</td>
							<td style="text-align:center"><a href="brand_category_create.php?act=edit&id='.$axRow['category_brand_id'].'">
						        <button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-pencil"></span></button></a></td>';

		if ($_SESSION['role_action']['brand_category']['delete'] == 1) {

			$data_table .= 	'<td style="text-align:center">'.$deleted.'</td>';
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

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_brand_category');

$oTmp->assign('content_file', 'brand/brand_category.htm');

$oTmp->display('layout/template.html');


//========================================//


$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
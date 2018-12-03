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

if ($_SESSION['role_action']['gallery']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$id = $_REQUEST['id'];

$Act = $_REQUEST['act'];

$path_upload_logo = $_SESSION['path_upload_logo'];



$where_brand = '';

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND gallery.bran_BrandID IN ('.$_SESSION['user_brand_id'].',0) AND gallery.gall_Deleted=""';
}



# SEARCH

$brand_id = "";

$where_search = '';

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
else {	$where_search = " AND mi_brand.brand_id IN (".$brand_id.")";	}




# SQL

$sql = 'SELECT 

		gallery.*,
		mi_brand.logo_image,
		mi_brand.name as brand_name,
		mi_brand.path_logo

		FROM gallery

		LEFT JOIN mi_brand 
		ON mi_brand.brand_id = gallery.bran_BrandID

		WHERE 1

		'.$where_search.'
		'.$where_brand.' 

		ORDER BY CASE 
			WHEN gallery.gall_Deleted = "" THEN 1
            WHEN gallery.gall_Deleted = "T" THEN 2 END ASC,
			gallery.gall_Status ASC, 
			gallery.gall_UpdatedDate DESC';


if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE gallery 
	 					SET gall_Status='Pending',
	 						gall_UpdatedDate='".$time_insert."' 
	 					WHERE gall_GalleryID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="gallery_type.php";</script>';


} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE gallery 
	 					SET gall_Status='Active',
	 						gall_UpdatedDate='".$time_insert."'
	 					WHERE gall_GalleryID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="gallery_type.php";</script>';


} else if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT gall_Deleted FROM gallery WHERE gall_GalleryID ="'.$id.'"';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

		
	if($axRow['gall_Deleted']=='') {

 		$do_sql_gallery = "UPDATE gallery
 							SET gall_Deleted='T', 
 							gall_Status='Pending',
 							gall_UpdatedDate='".$time_insert."' 
 							WHERE gall_GalleryID='".$id."'";

 	} else if ($axRow['gall_Deleted']=='T') {

		$do_sql_gallery = "UPDATE gallery
							SET gall_Deleted='', 
							gall_Status='Pending',
							gall_UpdatedDate='".$time_insert."' 
							WHERE gall_GalleryID='".$id."'";
	}

 	$oDB->QueryOne($do_sql_gallery);

 	echo '<script>window.location.href="gallery_type.php";</script>';


} else {

	$oRes = $oDB->Query($sql);

	$i=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;


		# STATUS

		$status = '';

		if($axRow['gall_Deleted']=='T'){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

		} else {

			if($axRow['gall_Status']=='Active'){

				if (($_SESSION['role_action']['gallery']['edit'] == 1 && $axRow['bran_BrandID']!=0) || $_SESSION['user_type_id_ses']==1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'gallery_type.php?act=active&id='.$axRow['gall_GalleryID'].'\'">
		                    <option class="status_default" value="'.$axRow['gall_GalleryID'].'" selected>On</option>
		                    <option class="status_default">Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';
		        }

			} else {

				if (($_SESSION['role_action']['gallery']['edit'] == 1 && $axRow['bran_BrandID']!=0) || $_SESSION['user_type_id_ses']==1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_pending" name="pending_status" onchange="window.location.href=\'gallery_type.php?act=pending&id='.$axRow['gall_GalleryID'].'\'">
		                    <option class="status_default">On</option>
		                    <option class="status_default" value="'.$axRow['gall_GalleryID'].'" selected>Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
		        }
			}
		}


		# LOGO BRAND

		if($axRow['logo_image']!=''){

			$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';

		} else {

			$logo_brand = '<img src="../../images/400x400.png" width="60" height="60"/>';
		}

		if ($axRow['bran_BrandID']=='0') {

			$logo_brand = '<img src="../../images/mi_action_logo.png" width="60" class="image_border" height="60"/>';

			$axRow['brand_name'] = 'MemberIn';
		}



		# DELETED

		if($axRow['gall_Deleted']=='') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['gall_GalleryID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>
				<div class="modal fade" id="Deleted'.$axRow['gall_GalleryID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td style="text-align:center">
								        <b>"'.$axRow['gall_Name'].'"</b><br>
								        By clicking the <b>"Inactive"</b> button to:<br>
								        &nbsp; &nbsp;- Inactive this gallery
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="gallery_type.php?act=delete&id='.$axRow['gall_GalleryID'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		} else if ($axRow['gall_Deleted']=='T') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['gall_GalleryID'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>
				<div class="modal fade" id="Deleted'.$axRow['gall_GalleryID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td style="text-align:center">
								        <b>"'.$axRow['gall_Name'].'"</b><br>
								        By clicking the <b>"Active"</b> button to:<br>
								        &nbsp; &nbsp;- Active this gallery<br>
								        &nbsp; &nbsp;- Change status to Pending
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="gallery_type.php?act=delete&id='.$axRow['gall_GalleryID'].'">
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
							<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
								<span style="font-size:11px;">'.$axRow['brand_name'].'</span></td>
							<td >'.$axRow['gall_Name'].'</td>
							<td >'.$axRow['gall_Description'].'</td>
							<td style="text-align:center">'.$status.'</td>
							<td >'.DateTime($axRow['gall_UpdatedDate']).'</td>';

		if (($_SESSION['role_action']['gallery']['edit'] == 1 && $axRow['bran_BrandID']!=0) || $_SESSION['user_type_id_ses']==1) {

			$data_table .=	'<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href='gallery_type_create.php?act=edit&id=".$axRow['gall_GalleryID']."'".'">
				<button type="button" class="btn btn-default btn-sm">
				<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></span></td>';

		} else { $data_table .= 	'<td style="text-align:center">-</td>'; }

		if (($_SESSION['role_action']['gallery']['edit'] == 1 && $axRow['bran_BrandID']!=0) || $_SESSION['user_type_id_ses']==1) {

			$data_table .= 	'<td style="text-align:center">'.$deleted.'</td>';
		
		} else { $data_table .= 	'<td style="text-align:center">-</td>'; }

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

$oTmp->assign('is_menu', 'is_gallery');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_setting', 'in');

$oTmp->assign('content_file', 'gallery/gallery_type.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
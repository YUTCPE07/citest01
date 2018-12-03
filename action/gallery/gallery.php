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

if ($_SESSION['role_action']['product']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$id = $_REQUEST['id'];

$Act = $_REQUEST['act'];

$path_upload_logo = $_SESSION['path_upload_logo'];

$path_upload_products = $_SESSION['path_upload_products'];



$where_brand = '';

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND a.brand_id = "'.$_SESSION['user_brand_id'].'" AND a.flag_del=0 ';
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
else {	$where_search = " AND c.brand_id IN (".$brand_id.")";	}




# SQL

$sql = 'SELECT 

		a.*,
		a.description as product_description,
		a.name as name ,
		a.flag_del as status_del,
		c.logo_image,
		c.name as brand_name,
		c.path_logo,
		d.gall_Name as gallery_name

		FROM mi_products AS a

		LEFT JOIN mi_brand AS c
		ON a.brand_id = c.brand_id

		LEFT JOIN gallery AS d
		ON a.gallery_id = d.gall_GalleryID

		WHERE 1

		'.$where_search.'
		'.$where_brand.' 

		ORDER BY CASE 
			WHEN a.flag_del = "0" THEN 1
            WHEN a.flag_del = "1" THEN 2 END ASC,
			a.flag_status ASC, 
			a.date_update DESC';

if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE mi_products 
	 					SET flag_status='2',
	 						date_update='".$time_insert."' 
	 					WHERE products_id='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="gallery.php";</script>';


} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE mi_products 
	 					SET flag_status='1',
	 						date_update='".$time_insert."'
	 					WHERE products_id='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="gallery.php";</script>';


} else if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT flag_del FROM mi_products WHERE products_id ="'.$id.'"';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($axRow['flag_del']==0) {

 		$do_sql_product = "UPDATE mi_products
 							SET flag_del=1, 
 							flag_status='2',
 							date_update='".$time_insert."' 
 							WHERE products_id='".$id."'";

 	} else if ($axRow['flag_del']==1) {

		$do_sql_product = "UPDATE mi_products
							SET flag_del=0, 
							flag_status='2',
							date_update='".$time_insert."' 
							WHERE products_id='".$id."'";
	}


 	$oDB->QueryOne($do_sql_product);

 	echo '<script>window.location.href="gallery.php";</script>';


} else {

	$oRes = $oDB->Query($sql);

	$i=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;


		# STATUS

		$status = '';

		if($axRow['status_del']==1){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

		} else {

			if($axRow['flag_status']==1){

				if ($_SESSION['role_action']['product']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'gallery.php?act=active&id='.$axRow['products_id'].'\'">
		                    <option class="status_default" value="'.$axRow['products_id'].'" selected>On</option>
		                    <option class="status_default">Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';
		        }

			} else {

				if ($_SESSION['role_action']['product']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_pending" name="pending_status" onchange="window.location.href=\'gallery.php?act=pending&id='.$axRow['products_id'].'\'">
		                    <option class="status_default">On</option>
		                    <option class="status_default" value="'.$axRow['products_id'].'" selected>Off</option>
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


		# LOGO PRODUCT

		if ($axRow['product_type']=='Image') {

			if($axRow['image']!=''){

				$product_image = '<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'" class="image_border" width="80" height="80"/>';

				$product_view = '<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'" class="image_border" width="150" height="150"/>';

			} else {

				$product_image = '<img src="../../images/400x400.png" width="80" height="80"/>';

				$product_view = '<img src="../../images/400x400.png" width="150" height="150"/>';
			}

		} else {

			if($axRow['video_link']!=''){

				$product_image = '<iframe width="142" height="80" src="https://www.youtube.com/embed/'.$axRow['video_link'].'?rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';

				$product_view = '<iframe width="530" height="300" src="https://www.youtube.com/embed/'.$axRow['video_link'].'?rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';

			} else {

				$product_image = '<img src="../../images/400x400.png" width="80" height="80"/>';

				$product_view = '<img src="../../images/400x400.png" width="150" height="150"/>';
			}
		}


		# VIEW

			# DATA

			if ($axRow['hilight_status'] == 'Y') {	$axRow['hilight_status'] = '<span class="glyphicon glyphicon-ok"></span>';	}
			else {	$axRow['hilight_status'] = '<span class="glyphicon glyphicon-remove"></span>';	}

			if ($axRow['product_description'] == '') { $axRow['product_description'] = '-';	}


		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['products_id'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
				<div class="modal fade" id="View'.$axRow['products_id'].'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>'.$axRow['name'].'</b></span>
						        <hr>
						        <center>
						        	<table width="80%" class="myPopup">';

		if ($axRow['product_type'] == 'Image') {

			$view .= '	        		<tr>
						        			<td width="30%" rowspan="6" style="text-align:center">'.$product_view.'</td>
						        			<td valign="top" style="text-align:right">Brand</td>
						        			<td width="5%" style="text-align:center" valign="top">:</td>
						        			<td valign="top" width="40%">'.$axRow['brand_name'].'</td>
						        		</tr>
						        		<tr>
						        			<td valign="top" style="text-align:right">Gallery Type</td>
						        			<td style="text-align:center" valign="top">:</td>
						        			<td valign="top">'.$axRow['gallery_name'].'</td>
						        		</tr>
						        		<tr>
						        			<td valign="top" style="text-align:right">Feature Type</td>
						        			<td style="text-align:center" valign="top">:</td>
						        			<td valign="top">'.$axRow['feature_type'].'</td>
						        		</tr>
						        		<tr>
						        			<td valign="top" style="text-align:right">Name</td>
						        			<td style="text-align:center" valign="top">:</td>
						        			<td valign="top">'.$axRow['name'].'</td>
						        		</tr>';

			if($_SESSION['user_type_id_ses']==1){

				$view .= '		    	<tr>
						        			<td valign="top" style="text-align:right">Highlight</td>
						        			<td style="text-align:center" valign="top">:</td>
						        			<td valign="top">'.$axRow['hilight_status'].'</td>
						        		</tr>';
			}

			$view .= '		      		<tr>
						        			<td valign="top" style="text-align:right">Description</td>
						        			<td style="text-align:center" valign="top">:</td>
						        			<td valign="top">'.$axRow['product_description'].'</td>
						        		</tr>
						        	</table>';
		} else {

			$view .= '	        		<tr>
						        			<td colspan="3" style="text-align:center">'.$product_view.'<br><br></td>
						        		</tr>
						        		<tr>
						        			<td width="40%" valign="top" style="text-align:right">Gallery Type</td>
						        			<td width="5%" style="text-align:center" valign="top">:</td>
						        			<td width="40%" valign="top">'.$axRow['gallery_name'].'</td>
						        		</tr>
						        		<tr>
						        			<td valign="top" style="text-align:right">Feature Type</td>
						        			<td style="text-align:center" valign="top">:</td>
						        			<td valign="top">'.$axRow['feature_type'].'</td>
						        		</tr>
						        		<tr>
						        			<td valign="top" style="text-align:right">Name</td>
						        			<td style="text-align:center" valign="top">:</td>
						        			<td valign="top">'.$axRow['name'].'</td>
						        		</tr>';

			if($_SESSION['user_type_id_ses']==1){

				$view .= '		    	<tr>
						        			<td valign="top" style="text-align:right">Highlight</td>
						        			<td style="text-align:center" valign="top">:</td>
						        			<td valign="top">'.$axRow['hilight_status'].'</td>
						        		</tr>';
			}

			$view .= '		      		<tr>
						        			<td valign="top" style="text-align:right">Description</td>
						        			<td style="text-align:center" valign="top">:</td>
						        			<td valign="top">'.$axRow['product_description'].'</td>
						        		</tr>
						        	</table>';
		}
		
		$view .= '		        </center>
						    </div>
						    <div class="modal-footer">';

			if ($_SESSION['role_action']['product']['edit'] == 1) {

				$view .= '	    <a href="gallery_create.php?act=edit&id='.$axRow['products_id'].'">
						        <button type="button" class="btn btn-default btn-sm">Edit</button></a>';
			}

			$view .= '        	<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';


		# DELETED

		if($axRow['status_del']==0) {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['products_id'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>
				<div class="modal fade" id="Deleted'.$axRow['products_id'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="180px" style="text-align:center" valign="top">'.$product_view.'</td>
						        	<td>
								        <p style="font-size:12px;padding-left:10px;">
								        	<b>"'.$axRow['name'].'"</b><br>
								            By clicking the <b>"Inactive"</b> button to:<br>
								            &nbsp; &nbsp;- Inactive this Gallery
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="gallery.php?act=delete&id='.$axRow['products_id'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		} else if ($axRow['status_del']==1) {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['products_id'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>
				<div class="modal fade" id="Deleted'.$axRow['products_id'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="180px" style="text-align:center">'.$product_view.'</td>
						        	<td>
								        <p style="font-size:12px;padding-left:10px;">
								        	<b>"'.$axRow['name'].'"</b><br>
								           	By clicking the <b>"Active"</b> button to:<br>
								            &nbsp; &nbsp;- Active this Gallery<br>
								            &nbsp; &nbsp;- Change status to Pending
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="gallery.php?act=delete&id='.$axRow['products_id'].'">
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
							<td style="text-align:center">'.$product_image.'</td>
							<td >'.$axRow['name'].'</td>
							<td >'.$axRow['gallery_name'].'</td>
							<td >'.$axRow['feature_type'].'</td>
							<td style="text-align:center">'.$status.'</td>
							<td >'.DateTime($axRow['date_update']).'</td>';

		if ($_SESSION['role_action']['product']['view'] == 1) {

			$data_table .=	'<td style="text-align:center">'.$view.'</td>';
		}

		if ($_SESSION['role_action']['product']['delete'] == 1) {

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

$oTmp->assign('is_menu', 'is_product');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_setting', 'in');

$oTmp->assign('content_file', 'gallery/gallery.html');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
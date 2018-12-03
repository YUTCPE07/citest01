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

if ($_SESSION['role_action']['mobile_banner']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];



$where_brand = '';

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND a.brand_id = "'.$_SESSION['user_brand_id'].'"';
}


$sql = 'SELECT a.*,
		b.name AS brand_name,
		b.path_logo,
		b.logo_image

  		FROM mi_banner AS a

  		LEFT JOIN mi_brand AS b
		ON a.brand_id = b.brand_id

		'.$where_brand.' 

		ORDER BY CASE 
			WHEN a.flag_del = "0" THEN 1
            WHEN a.flag_del = "1" THEN 2 END ASC, 
			a.date_update DESC';


if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT flag_del FROM mi_banner WHERE banner_id ="'.$id.'"';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

		
	if($axRow['flag_del']==0) {

 		$do_sql_banner = "UPDATE mi_banner 
 							SET flag_del=1, 
 							date_update='".$time_insert."' 
 							WHERE banner_id='".$id."'";

 	} else if ($axRow['flag_del']==1) {

		$do_sql_banner = "UPDATE mi_banner 
							SET flag_del=0,
							date_update='".$time_insert."' 
							WHERE banner_id='".$id."'";
	}


 	$oDB->QueryOne($do_sql_banner);

 	echo '<script>window.location.href="mobile_banner.php";</script>';


} else {

	$oRes = $oDB->Query($sql);

	$i=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;


		# LOGO BRAND

		if($axRow['logo_image']!=''){

			$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="70" height="70"/>';

			$logo_view = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="150" height="150"/>';

		} else {

			$logo_brand = '<img src="../../images/400x400.png" width="70" height="70"/>';

			$logo_view = '<img src="../../images/400x400.png" width="150" height="150"/>';
		}



		# BANNER

		if($axRow['image']!=''){

			$banner = '<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'" class="image_border" height="80" width="203"/>';

			$banner_image = '<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'" class="image_border" height="100" width="254"/>';

			$banner_view = '<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'" class="image_border" height="150" width="381"/>';

		} else {

			$banner = '<img src="../../images/img_size.jpg" height="80" width="203"/>';

			$banner_image = '<img src="../../images/img_size.jpg" height="100" width="254"/>';

			$banner_view = '<img src="../../images/img_size.jpg" height="150" width="381"/>';
		}



		# VIEW

			# DATA

			if ($axRow['tax_type'] == 1) {	$axRow['tax_type'] = 'VAT. Registration';	}

			else if ($axRow['tax_type'] == 2) {	$axRow['tax_type'] = 'VAT. Exemption';	}

			else {	$axRow['tax_type'] = '-';	}

			if ($axRow['tax_id'] == '') { $axRow['tax_id'] = '-';	}

			
		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['brand_id'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
				<div class="modal fade" id="View'.$axRow['brand_id'].'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>'.$axRow['brand_name'].'</b><span>
						        <hr>
						        <center>
						        	'.$logo_view.' '.$banner_view.'<br><br>'.$axRow['description'].'
						        </center>
						    </div>
						    <div class="modal-footer">';

		if ($_SESSION['role_action']['mobile_banner']['edit'] == 1) {		    

			$view .= '       <a href="mobile_banner_create.php?act=edit&id='.$axRow['banner_id'].'">
						        <button type="button" class="btn btn-default btn-sm">Edit</button></a>';
		}

		$view .= '      <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';


		# DELETED

		if($axRow['flag_del']==0) {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['banner_id'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>
				<div class="modal fade" id="Deleted'.$axRow['banner_id'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b><span>
						        <hr>
						        <center>
						        <table width="100%" class="myPopup">
						        	<tr>
						        	<td width="280px" style="text-align:center">'.$banner_image.'</td>
						        	<td>
								        <p style="font-size:14px;padding-left:10px;">
								            By clicking the <b>"Inactive"</b> button to:<br>
								            &nbsp; &nbsp;- Inactive this banner
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="mobile_banner.php?act=delete&id='.$axRow['banner_id'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		} else if ($axRow['flag_del']==1) {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['banner_id'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>
				<div class="modal fade" id="Deleted'.$axRow['banner_id'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b><span>
						        <hr>
						        <center>
						        <table width="100%" class="myPopup">
						        	<tr>
						        	<td width="280px" style="text-align:center">'.$banner_image.'</td>
						        	<td>
								        <p style="font-size:14px;padding-left:10px;">
								           	By clicking the <b>"Active"</b> button to:<br>
								            &nbsp; &nbsp;- Active this banner
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="mobile_banner.php?act=delete&id='.$axRow['banner_id'].'">
						        <button type="button" class="btn btn-default btn-sm">Active</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		}


		# TABLE

		$data_table .= '<tr >
							<td >'.$i.'</td>
							<td style="text-align:center">'.$logo_brand.'<br><span style="font-size:11px;">'.$axRow['brand_name'].'</span></td>
							<td style="text-align:center">'.$banner.'</td>
							<td >'.$axRow['description'].'</td>
							<td >'.DateTime($axRow['date_update']).'</td>';

		if ($_SESSION['role_action']['mobile_banner']['view'] == 1) {

			$data_table .= '<td style="text-align:center">'.$view.'</td>';
		}

		if ($_SESSION['role_action']['mobile_banner']['delete'] == 1) {

			$data_table .= 	'<td style="text-align:center">'.$deleted.'</td>';
		}

		$data_table .= '</tr>';

		$asData[] = $axRow;
	}
}





$oTmp->assign('data_table', $data_table);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_mobile_banner');

$oTmp->assign('type_file_upload', '<span class="text-rq">Type file : .jpg , .png , .gif only</span>');

$oTmp->assign('content_file', 'mobile/mobile_banner.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
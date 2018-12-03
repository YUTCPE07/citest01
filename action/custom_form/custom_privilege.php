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

if ($_SESSION['role_action']['custom_privilege']['view'] != 1) {

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

	$where_brand = ' AND custom_field.bran_BrandID IN (0,'.$_SESSION['user_brand_id'].') AND custom_field.cufi_Deleted=""';
}


$sql = 'SELECT 

		custom_field.*,
		mi_brand.name AS brand_name,
		mi_brand.logo_image,
		mi_brand.path_logo,
		field_type.fity_Name 

		FROM custom_field

		LEFT JOIN field_type
		ON field_type.fity_FieldTypeID = custom_field.fity_FieldTypeID

		LEFT JOIN mi_brand
		ON mi_brand.brand_id = custom_field.bran_BrandID

		WHERE custom_field.cufi_Type="Privilege"
		'.$where_brand.' 

		ORDER BY CASE 
			WHEN custom_field.cufi_Deleted = "" THEN 1
	        WHEN custom_field.cufi_Deleted = "T" THEN 2 END ASC,
			custom_field.cufi_UpdatedDate DESC';


if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT cufi_Deleted FROM custom_field WHERE cufi_CustomFieldID ="'.$id.'"';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($axRow['cufi_Deleted']=='') {

 		$do_sql_form = "UPDATE custom_field
 							SET cufi_Deleted='T', 
 							cufi_UpdatedDate='".$time_insert."' 
 							WHERE cufi_CustomFieldID='".$id."'";

 	} else if ($axRow['cufi_Deleted']=='T') {

		$do_sql_form = "UPDATE custom_field
 							SET cufi_Deleted='', 
 							cufi_UpdatedDate='".$time_insert."' 
 							WHERE cufi_CustomFieldID='".$id."'";
	}

 	$oDB->QueryOne($do_sql_form);

 	echo '<script>window.location.href="custom.php";</script>';


} else {

	$oRes = $oDB->Query($sql);

	$i=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;


		# LOGO

		if($axRow['logo_image']!=''){

			$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';

		} else {

			$logo_brand = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';
		}

		if($axRow['brand_id']=='0'){

			$logo_brand = '<img src="../../images/mi_action_logo.png" class="image_border" width="60" height="60"/>';
		}


		# DELETED

		if($axRow['cufi_Deleted']=='') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['cufi_CustomFieldID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>
				<div class="modal fade" id="Deleted'.$axRow['cufi_CustomFieldID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="140px" style="text-align:center" valign="top">'.$logo_view.'</td>
						        	<td>
								        <p style="font-size:12px;padding-left:10px;">
								        	<b>"'.$axRow['name'].'"</b><br>
								            By clicking the <b>"Inactive"</b> button to:<br>
								            &nbsp; &nbsp;- Inactive this card<br>
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="card_type.php?act=delete&id='.$axRow['cufi_CustomFieldID'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		} else if ($axRow['cufi_Deleted']=='T') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['cufi_CustomFieldID'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>
				<div class="modal fade" id="Deleted'.$axRow['cufi_CustomFieldID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="140px" style="text-align:center" valign="top">'.$logo_view.'</td>
						        	<td>
								        <p style="font-size:12px;padding-left:10px;">
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
						    	<a href="card_type.php?act=delete&id='.$axRow['cufi_CustomFieldID'].'">
						        <button type="button" class="btn btn-default btn-sm">Active</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		}


		# VALUE

		$value = 'SELECT clva_Name, clva_NameEn FROM custom_list_value WHERE cufi_CustomFieldID ="'.$axRow['cufi_CustomFieldID'].'"';

		$oRes_val = $oDB->Query($value);

		$value = '';

		while ($axRow_val = $oRes_val->FetchRow(DBI_ASSOC)){

			$value .= '- '.$axRow_val['clva_Name']."<br>";
		}


		# DATA TABLE

		$data_table .= '<tr >
							<td >'.$i.'</td>
							<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
								<span style="font-size:11px;">'.$axRow['brand_name'].'</span>
							</td>
							<td >'.$axRow['cufi_Name'].'<br>'.$axRow['cufi_NameEn'].'</td>
							<td >'.$value.'</td>
							<td >'.$axRow['fity_Name'].'</td>
							<td >'.DateTime($axRow['cufi_UpdatedDate']).'</td>';

		if ($_SESSION['role_action']['custom_privilege']['edit'] == 1) {

			$data_table .=	'<td style="text-align:center">
							<a href="custom_privilege_create.php?act=edit&id='.$axRow['cufi_CustomFieldID'].'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></a></td>';
		}

		if ($_SESSION['role_action']['custom_privilege']['delete'] == 1) {

			$data_table .= 	'<td style="text-align:center">'.$deleted.'</td>';
		}

		$data_table .=	'</tr>';

		$asData[] = $axRow;
	}
}





$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_custom_privilege');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_privilege', 'in');

$oTmp->assign('content_file', 'custom_form/custom_privilege.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
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

if ($_SESSION['role_action']['news_category']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];


$sql = 'SELECT news_category.*
  		FROM news_category
		ORDER BY CASE 
			WHEN neca_Deleted = "" THEN 1
            WHEN neca_Deleted = "T" THEN 2 END ASC,
			neca_Status ASC, 
			neca_UpdatedDate DESC';

$oRes = $oDB->Query($sql) or die(mysql_error($sql));





if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE news_category
	 					SET neca_Status='Pending',
	 						neca_UpdatedDate='".$time_insert."' 
	 					WHERE neca_NewsCategoryID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="news_category.php";</script>';

} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE news_category
	 					SET neca_Status='Active',
	 						neca_UpdatedDate='".$time_insert."'
	 					WHERE neca_NewsCategoryID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="news_category.php";</script>';



} else if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql_delete = 'SELECT neca_Deleted FROM news_category WHERE neca_NewsCategoryID ="'.$id.'"';

	$oRes = $oDB->Query($sql_delete);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($axRow['neca_Deleted']=='T') {

 		$do_sql_variety = "UPDATE news_category 
 							SET neca_Deleted='', 
 							neca_Status='Pending',
 							neca_UpdatedDate='".$time_insert."'
 							WHERE neca_NewsCategoryID='".$id."'";

 	} else if ($axRow['neca_Deleted']=='') {

		$do_sql_variety = "UPDATE news_category 
							SET neca_Deleted='T',
 							neca_Status='Pending',
 							neca_UpdatedDate='".$time_insert."'
							WHERE neca_NewsCategoryID='".$id."'";
	}

	$oDB->QueryOne($do_sql_variety);

 	echo '<script>window.location.href="news_category.php";</script>';


} else {

	$oRes = $oDB->Query($sql);

	$i=0;

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;


		# DELETED

		if($axRow['neca_Deleted']=='') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['neca_NewsCategoryID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>
				<div class="modal fade" id="Deleted'.$axRow['neca_NewsCategoryID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b><span>
						        <hr>
						        <center>
								    <b>"'.$axRow['neca_Name'].'"</b><br>
								    By clicking the <b>"Inactive"</b> button to:<br>
								    &nbsp; &nbsp;- Inactive this news category
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="news_category.php?act=delete&id='.$axRow['neca_NewsCategoryID'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		} else if ($axRow['neca_Deleted']=='T') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['neca_NewsCategoryID'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>
				<div class="modal fade" id="Deleted'.$axRow['neca_NewsCategoryID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b><span>
						        <hr>
						        <center>
						        	<b>"'.$axRow['neca_Name'].'"</b><br>
								    By clicking the <b>"Active"</b> button to:<br>
								    &nbsp; &nbsp;- Active this news category<br>
								    &nbsp; &nbsp;- Change status to Pending
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="news_category.php?act=delete&id='.$axRow['neca_NewsCategoryID'].'">
						        <button type="button" class="btn btn-default btn-sm">Active</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		}


		# STATUS

		$status = '';

		if($axRow['neca_Deleted']=='T'){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

		} else {

			if($axRow['neca_Status']=='Active'){

				if ($_SESSION['role_action']['news_category']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'news_category.php?act=active&id='.$axRow['neca_NewsCategoryID'].'\'">
		                    <option class="status_default" value="'.$axRow['neca_NewsCategoryID'].'" selected>On</option>
		                    <option class="status_default">Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';
		        }

			} else {

				if ($_SESSION['role_action']['news_category']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_pending" name="pending_status" onchange="window.location.href=\'news_category.php?act=pending&id='.$axRow['neca_NewsCategoryID'].'\'">
		                    <option class="status_default">On</option>
		                    <option class="status_default" value="'.$axRow['neca_NewsCategoryID'].'" selected>Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
		        }
			}
		}



		$data_table .= '<tr >
							<td >'.$i.'</td>
							<td>'.$axRow['neca_Name'].'</td>
							<td>'.$axRow['neca_NameEn'].'</td>
							<td style="text-align:center">'.$status.'</td>
							<td >'.DateTime($axRow['neca_UpdatedDate']).'</td>';

		if ($_SESSION['role_action']['news_category']['edit'] == 1) {

			$data_table .=	'<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href='news_category_create.php?act=edit&id=".$axRow['neca_NewsCategoryID']."'".'">
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

$oTmp->assign('is_menu', 'is_news_category');

$oTmp->assign('type_file_upload', '<span class="text-rq">Type file : .jpg , .png , .gif only &nbsp | &nbsp Size : 640 x 400 px</span>');

$oTmp->assign('content_file', 'news/news_category.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
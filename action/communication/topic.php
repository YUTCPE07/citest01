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

if ($_SESSION['role_action']['ma_topic']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];



$sql = 'SELECT 

		communication_topic.*,
	  	mi_user_type.name AS user_type

  		FROM communication_topic 

		LEFT JOIN mi_user
		ON mi_user.user_id = communication_topic.coto_UpdatedBy 

		LEFT JOIN mi_user_type
		ON mi_user.user_type_id = mi_user_type.user_type_id 

		ORDER BY CASE 
			WHEN coto_Deleted = "" THEN 1
	        WHEN coto_Deleted = "T" THEN 2 END ASC,
			coto_UpdatedDate DESC';


if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT coto_Deleted FROM communication_topic WHERE coto_TopicID ="'.$id.'"';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($axRow['coto_Deleted']=='') {

 		$do_sql_topic = "UPDATE communication_topic
 							SET coto_Deleted='T', 
 							coto_UpdatedDate='".$time_insert."' 
 							WHERE coto_TopicID='".$id."'";

 	} else if ($axRow['coto_Deleted']=='T') {

		$do_sql_topic = "UPDATE communication_topic
 							SET coto_Deleted='', 
 							coto_UpdatedDate='".$time_insert."' 
 							WHERE coto_TopicID='".$id."'";
	}

 	$oDB->QueryOne($do_sql_topic);

 	echo '<script>window.location.href="topic.php";</script>';


} else {


	$oRes = $oDB->Query($sql);

	$data_table = '';

	$i=0;

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;


		# DELETED

		if($axRow['coto_Deleted']=='') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['coto_TopicID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>
				<div class="modal fade" id="Deleted'.$axRow['coto_TopicID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b><span>
						        <hr>
						        <center>
						        <p style="font-size:14px;padding-left:10px;">
								   <b>"'.$axRow['coto_Name'].'"</b><br>
								   By clicking the <b>"Inactive"</b> button to: Inactive this topic
								</p>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="topic.php?act=delete&id='.$axRow['coto_TopicID'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		} else if ($axRow['coto_Deleted']=='T') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['coto_TopicID'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>
				<div class="modal fade" id="Deleted'.$axRow['coto_TopicID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b><span>
						        <hr>
						        <center>
						        <p style="font-size:14px;">
								    <b>"'.$axRow['coto_Name'].'"</b><br>
								    By clicking the <b>"Active"</b> button to: Active this topic
								</p>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="topic.php?act=delete&id='.$axRow['coto_TopicID'].'">
						        <button type="button" class="btn btn-default btn-sm">Active</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		}


		# EMAIL

		if ($axRow['coto_Email']!="T") { $check_email = "glyphicon glyphicon-unchecked"; }
		else { $check_email = "glyphicon glyphicon-check"; }



		# MOBILE

		if ($axRow['coto_Mobile']!="T") { $check_mobile = "glyphicon glyphicon-unchecked"; }
		else { $check_mobile = "glyphicon glyphicon-check"; }



		# BRANCH

		if ($axRow['coto_Branch']!="T") { $check_branch = "glyphicon glyphicon-unchecked"; }
		else { $check_branch = "glyphicon glyphicon-check"; }



		# ANYWHERE

		if ($axRow['coto_Anywhere']!="T") { $check_anywhere = "glyphicon glyphicon-unchecked"; }
		else { $check_anywhere = "glyphicon glyphicon-check"; }



		# DATA TABLE

		$data_table .= '<tr >
							<td >'.$i.'</td>
							<td >'.$axRow['coto_Name'].'</td>
							<td style="text-align:center"><span class="'.$check_email.'"></span></td>
							<td style="text-align:center"><span class="'.$check_mobile.'"></span></td>
							<td style="text-align:center"><span class="'.$check_branch.'"></span></td>
							<td style="text-align:center"><span class="'.$check_anywhere.'"></span></td>
							<td style="text-align:center">'.$axRow['user_type'].'</td>
							<td style="text-align:center">'.DateTime($axRow['coto_UpdatedDate']).'</td>';

		if ($_SESSION['role_action']['ma_topic']['edit']) {

			$data_table .=	'<td style="text-align:center"><span style="cursor:pointer" onclick="'."window.location.href='topic_create.php?act=edit&id=".$axRow['coto_TopicID']."'".'">
				<button type="button" class="btn btn-default btn-sm">
				<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></span></td>';
		}

		if ($_SESSION['role_action']['ma_topic']['delete']) {

			$data_table .= 	'<td style="text-align:center">'.$deleted.'</td>';
		}

		$data_table .=	'</tr>';

		$asData[] = $axRow;
	}
}



$oTmp->assign('data_table', $data_table);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_communication');

$oTmp->assign('content_file', 'communication/topic.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
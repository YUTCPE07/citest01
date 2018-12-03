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

if ($_SESSION['role_action']['memberin_point']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$path_upload_collection = $_SESSION['path_upload_collection'];



if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE memberin_point 
	 					SET mipo_Status='F',
	 						mipo_UpdatedDate='".$time_insert."' 
	 					WHERE mipo_MemberinPointID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="point.php";</script>';

} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE memberin_point 
	 					SET mipo_Status='T',
	 						mipo_UpdatedDate='".$time_insert."'
	 					WHERE mipo_MemberinPointID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="point.php";</script>';

} else if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT mipo_Deleted FROM memberin_point WHERE mipo_MemberinPointID ="'.$id.'"';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($axRow['mipo_Deleted']=='') {

 		$do_sql_point = "UPDATE memberin_point
 							SET mipo_Deleted='T', 
 							mipo_Status='F',
 							mipo_UpdatedDate='".$time_insert."' 
 							WHERE mipo_MemberinPointID='".$id."'";

 	} else if ($axRow['mipo_Deleted']=='T') {

		$do_sql_point = "UPDATE memberin_point
 							SET mipo_Deleted='', 
 							mipo_Status='F',
 							mipo_UpdatedDate='".$time_insert."' 
 							WHERE mipo_MemberinPointID='".$id."'";
	}

 	$oDB->QueryOne($do_sql_point);

 	echo '<script>window.location.href="point.php";</script>';

} else {

	$sql = 'SELECT memberin_point.*,
		  	collection_type.coty_Image
			FROM memberin_point
			LEFT JOIN collection_type
			ON collection_type.coty_CollectionTypeID = memberin_point.mipo_CollectionTypeID
			ORDER BY memberin_point.mipo_UpdatedDate DESC';

	$oRes = $oDB->Query($sql);

	$i=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;


		# MEMBERIN POINT

		$memberin_point = '<img src="'.$path_upload_collection.$axRow['coty_Image'].'" width="100" height="100"/>';

		$point_icon = '<img src="'.$path_upload_collection.$axRow['coty_Image'].'" width="20" height="20"/>';



		# STATUS

		$status = '';

		if($axRow['mipo_Deleted']=="T"){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';	

		} else {

			if($axRow['mipo_Status']=='T'){

				if ($_SESSION['role_action']['memberin_point']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'point.php?act=active&id='.$axRow['mipo_MemberinPointID'].'\'">
		                    <option class="status_default" value="'.$axRow['mipo_MemberinPointID'].'" selected>On</option>
		                    <option class="status_default">Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';	
		        }

			} else {

				if ($_SESSION['role_action']['memberin_point']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_pending" name="pending_status" onchange="window.location.href=\'point.php?act=pending&id='.$axRow['mipo_MemberinPointID'].'\'">
		                    <option class="status_default">On</option>
		                    <option class="status_default" value="'.$axRow['mipo_MemberinPointID'].'" selected>Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';	
		        }
			}
		}



		# DELETED

		if($axRow['mipo_Deleted']=='') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['mipo_MemberinPointID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>
				<div class="modal fade" id="Deleted'.$axRow['mipo_MemberinPointID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b><span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td>'.$memberin_point.'</td>
						        	<td>
								        <p style="font-size:14px;padding-left:10px;">
								            By clicking the <b>"Inactive"</b> button to:<br>
								            &nbsp; &nbsp;- Inactive this motivation point<br>
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="point.php?act=delete&id='.$axRow['mipo_MemberinPointID'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		} else if ($axRow['mipo_Deleted']=='T') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['mipo_MemberinPointID'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>
				<div class="modal fade" id="Deleted'.$axRow['mipo_MemberinPointID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b><span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td>'.$memberin_point.'</td>
						        	<td>
								        <p style="font-size:14px;padding-left:10px;">
								           	By clicking the <b>"Active"</b> button to:<br>
								            &nbsp; &nbsp;- Active this motivation point<br>
								            &nbsp; &nbsp;- Change status to Pending
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="point.php?act=delete&id='.$axRow['mipo_MemberinPointID'].'">
						        <button type="button" class="btn btn-default btn-sm">Active</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		}




		# METHOD

		if ($axRow['mipo_CollectionMethod']=='No') {

			$axRow['mipo_CollectionMethod'] = 'No Expiry';

		} else if ($axRow['mipo_CollectionMethod']=='Exp') {

			if ($axRow['mipo_PeriodType']=='Y') {  $axRow['mipo_PeriodType'] = 'Years';	}

			if ($axRow['mipo_PeriodType']=='M') {  $axRow['mipo_PeriodType'] = 'Months';	}

			if ($axRow['mipo_PeriodTypeEnd']=='Y') {  $axRow['mipo_PeriodTypeEnd'] = 'End of Year';	}

			if ($axRow['mipo_PeriodTypeEnd']=='M') {  $axRow['mipo_PeriodTypeEnd'] = 'End of Month';	}

			$axRow['mipo_CollectionMethod'] = $axRow['mipo_PeriodTime'].' '.$axRow['mipo_PeriodType'].' ('.$axRow['mipo_PeriodTypeEnd'].')';

		} else if ($axRow['mipo_CollectionMethod']=='Fix') {

			$axRow['mipo_CollectionMethod'] = DateOnly($axRow['mipo_EndDate']);
		}



		# MULTIPLE

		$sql_multiple = 'SELECT mipo_Multiple, mipo_MultipleStartDate, mipo_MultipleEndDate 
							FROM memberin_point 
							WHERE mipo_MemberinPointID = '.$axRow["mipo_MemberinPointID"].'
							GROUP BY mipo_MemberinPointID';

		$multiple = $oDB->QueryOne($sql_multiple);

		if ($multiple['mipo_Multiple']==0) {	$multiple['mipo_Multiple'] = '-';	}

		if ($multiple['mipo_MultipleStartDate']=='0000-00-00') {	$multiple['mipo_MultipleStartDate'] = '-';	}

		if ($multiple['mipo_MultipleEndDate']=='0000-00-00') {	$multiple['mipo_MultipleEndDate'] = '-';	}



		# VIEW

			# DATA

			if ($axRow['mipo_RequestReceiptNo'] == '') {	

				$axRow['mipo_RequestReceiptNo'] = '<span class="glyphicon glyphicon-unchecked"></span> Receieve No.';	

			} else {	

				$axRow['mipo_RequestReceiptNo'] = '<span class="glyphicon glyphicon-check"></span> Receieve No.';
			}

			if ($axRow['mipo_RequestReceiptAmount'] == '') {	

				$axRow['mipo_RequestReceiptAmount'] = '<span class="glyphicon glyphicon-unchecked"></span> Recieve Amount';	

			} else {	

				$axRow['mipo_RequestReceiptAmount'] = '<span class="glyphicon glyphicon-check"></span> Recieve Amount';	
			}

			if ($axRow['mipo_Description']=='') {	$axRow['mipo_Description'] = '-';	}


		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['mipo_MemberinPointID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
				<div class="modal fade" id="View'.$axRow['mipo_MemberinPointID'].'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
					<div class="modal-dialog" role="document" style="width:55%">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>'.$axRow['miac_Name'].'</b></span>
						        <hr>
						        <center>
						        	'.$memberin_point.'<br><br>
						        	<table width="80%" class="myPopup">
						        		<tr>
						        			<td style="text-align:right" valign="top">Require</td>
						        			<td style="text-align:center" valign="top" width="20px">:</td>
						        			<td valign="top">'.$axRow['mipo_RequestReceiptNo'].'<br>
						        				'.$axRow['mipo_RequestReceiptAmount'].'</td>
						        		</tr>
						        		<tr>
						        			<td style="text-align:right">Use Amount</td>
						        			<td style="text-align:center">:</td>
						        			<td>'.$axRow['mipo_UseAmount'].' Baht / '.$axRow['mipo_PointQty'].' Point Qty</td>
						        		</tr>
						        		<tr>
						        			<td style="text-align:right">Use Limit (Sales slip)</td>
						        			<td style="text-align:center">:</td>
						        			<td>'.$axRow['mipo_Method'].'</td>
						        		</tr>
						        	</table><br>
						        	<span style="font-size:12px">
					                <ul id="tapMenu" class="nav nav-tabs">
					                    <li class="active" style="width:33%">
					                    	<a data-toggle="tab" href="#time'.$axRow['mipo_MemberinPointID'].'">
					                    	<center><b>Expiry</b></center></a>
					                    </li>
					                    <li style="width:33%">
					                    	<a data-toggle="tab" href="#note'.$axRow['mipo_MemberinPointID'].'">
					                    	<center><b>Note</b></center></a>
					                    </li>
					                    <li style="width:33%">
					                    	<a data-toggle="tab" href="#multiple'.$axRow['mipo_MemberinPointID'].'">
					                    	<center><b>Multiple</b></center></a>
					                    </li>
					                </ul>
					                </span>
					                <div class="tab-content">
					                    <div id="time'.$axRow['mipo_MemberinPointID'].'" class="tab-pane active"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Expiry</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['mipo_CollectionMethod'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="note'.$axRow['mipo_MemberinPointID'].'" class="tab-pane"><br>
					                    	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" valign="top" width="45%">Description</td>
								        			<td style="text-align:center" valign="top" width="5%">:</td>
								        			<td>'.nl2br($axRow['mipo_Description']).'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="multiple'.$axRow['mipo_MemberinPointID'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Multiple</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$multiple['mipo_Multiple'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Start Date</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$multiple['mipo_MultipleStartDate'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">End Date</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$multiple['mipo_MultipleEndDate'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                </div>
						        </center>
						    </div>
						    <div class="modal-footer">';

			if ($_SESSION['role_action']['memberin_point']['edit'] == 1) {	

				$view .= '  <a href="point_create.php?act=edit&id='.$axRow['mipo_MemberinPointID'].'">
						        <button type="button" class="btn btn-default btn-sm">Edit</button></a>';
			}

				$view .= '      <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		$data_table .= '<tr >
							<td >'.$i.'</td>
							<td style="text-align:right">'.$axRow['mipo_UseAmount'].' ฿</td>
							<td style="text-align:center">'.$axRow['mipo_PointQty'].'</td>
							<td style="text-align:center">'.$point_icon.'</td>
							<td >'.$axRow['mipo_Method'].'</td>
							<td >'.$axRow['mipo_CollectionMethod'].'</td>
							<td style="text-align:center">'.$status.'</td>
							<td >'.DateTime($axRow['mipo_UpdatedDate']).'</td>';

		if ($_SESSION['role_action']['memberin_point']['view'] == 1) {

			$data_table .=	'<td style="text-align:center">'.$view.'</td>';
		}

		if ($_SESSION['role_action']['memberin_point']['delete'] == 1) {

			$data_table .= 	'<td style="text-align:center">'.$deleted.'</td>';
		}

		$data_table .=	'</tr>';

		$asData[] = $axRow;
	}
}




$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_memberin_point');

$oTmp->assign('content_file', 'memberin_motivation/point.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
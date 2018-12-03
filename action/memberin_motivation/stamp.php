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

if ($_SESSION['role_action']['memberin_stamp']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$path_upload_collection = $_SESSION['path_upload_collection'];




if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE memberin_stamp 
	 					SET mist_Status='F',
	 						mist_UpdatedDate='".$time_insert."' 
	 					WHERE mist_MemberinStampID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="stamp.php";</script>';

} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE memberin_stamp 
	 					SET mist_Status='T',
	 						mist_UpdatedDate='".$time_insert."'
	 					WHERE mist_MemberinStampID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="stamp.php";</script>';

} else if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT mist_Deleted FROM memberin_stamp WHERE mist_MemberinStampID ="'.$id.'"';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($axRow['mist_Deleted']=='') {

 		$do_sql_stamp = "UPDATE memberin_stamp
 							SET mist_Deleted='T', 
 							mist_Status='F',
 							mist_UpdatedDate='".$time_insert."' 
 							WHERE mist_MemberinStampID='".$id."'";

 	} else if ($axRow['mist_Deleted']=='T') {

		$do_sql_stamp = "UPDATE memberin_stamp
 							SET mist_Deleted='', 
 							mist_Status='F',
 							mist_UpdatedDate='".$time_insert."' 
 							WHERE mist_MemberinStampID='".$id."'";
	}

 	$oDB->QueryOne($do_sql_stamp);

 	echo '<script>window.location.href="stamp.php";</script>';

} else {

	$sql = 'SELECT
			memberin_stamp.*,
		  	collection_type.coty_Image
			FROM memberin_stamp
			LEFT JOIN collection_type
			ON collection_type.coty_CollectionTypeID = memberin_stamp.mist_CollectionTypeID
			ORDER BY memberin_stamp.mist_UpdatedDate DESC';

	$oRes = $oDB->Query($sql);

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;


		# STATUS

		$status = '';

		if($axRow['mist_Deleted']=="T"){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';	

		} else {

			if($axRow['mist_Status']=="T"){

				if ($_SESSION['role_action']['memberin_stamp']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'stamp.php?act=active&id='.$axRow['mist_MemberinStampID'].'\'">
		                    <option class="status_default" value="'.$axRow['mist_MemberinStampID'].'" selected>On</option>
		                    <option class="status_default">Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';	
		        }

			} else {

				if ($_SESSION['role_action']['memberin_stamp']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_pending" name="pending_status" onchange="window.location.href=\'stamp.php?act=pending&id='.$axRow['mist_MemberinStampID'].'\'">
		                    <option class="status_default">On</option>
		                    <option class="status_default" value="'.$axRow['mist_MemberinStampID'].'" selected>Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';	
		        }
			}
		}


		# MEMBERIN POINT

		$memberin_point = '<img src="'.$path_upload_collection.$axRow['coty_Image'].'" width="100" height="100"/>';

		$point_icon = '<img src="'.$path_upload_collection.$axRow['coty_Image'].'" width="20" height="20"/>';



		# DELETED

		if($axRow['mist_Deleted']=='') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['mist_MemberinStampID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>
				<div class="modal fade" id="Deleted'.$axRow['mist_MemberinStampID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
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
								            &nbsp; &nbsp;- Inactive this motivation stamp<br>
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="stamp.php?act=delete&id='.$axRow['mist_MemberinStampID'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		} else if ($axRow['mist_Deleted']=='T') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['mist_MemberinStampID'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>
				<div class="modal fade" id="Deleted'.$axRow['mist_MemberinStampID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
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
								            &nbsp; &nbsp;- Active this motivation stamp<br>
								            &nbsp; &nbsp;- Change status to Pending
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="stamp.php?act=delete&id='.$axRow['mist_MemberinStampID'].'">
						        <button type="button" class="btn btn-default btn-sm">Active</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		}


		# MAX POINT PER DAY

		if ($axRow['mist_MaxPointPerDay']==0) {

			$axRow['mist_MaxPointPerDay'] = 'Unlimited';

		} else {	$axRow['mist_MaxPointPerDay'] = $axRow['mist_MaxPointPerDay'].' Times / Day / Member'; }



		# METHOD

		if ($axRow['mist_CollectionMethod']=='No') {

			$axRow['mist_CollectionMethod'] = 'No Expiry';

		} else if ($axRow['mist_CollectionMethod']=='Exp') {

			if ($axRow['mist_PeriodType']=='Y') {  $axRow['mist_PeriodType'] = 'Years';	}

			if ($axRow['mist_PeriodType']=='M') {  $axRow['mist_PeriodType'] = 'Months';	}

			if ($axRow['mist_PeriodTypeEnd']=='Y') {  $axRow['mist_PeriodTypeEnd'] = 'End of Year';	}

			if ($axRow['mist_PeriodTypeEnd']=='M') {  $axRow['mist_PeriodTypeEnd'] = 'End of Month';	}

			$axRow['mist_CollectionMethod'] = $axRow['mist_PeriodTime'].' '.$axRow['mist_PeriodType'].' ('.$axRow['mist_PeriodTypeEnd'].')';

		} else if ($axRow['mist_CollectionMethod']=='Fix') {

			$axRow['mist_CollectionMethod'] = DateOnly($axRow['mist_EndDate']);
		}




		# MULTIPLE

		$sql_multiple = 'SELECT most_Multiple, most_MultipleStartDate, most_MultipleEndDate 
							FROM motivation_stamp 
							WHERE most_MotivationStampHID = '.$axRow["mist_MemberinStampID"].'
							GROUP BY most_MotivationStampHID';

		$multiple = $oDB->QueryOne($sql_multiple);

		if ($multiple['most_Multiple']==0) {	$multiple['most_Multiple'] = '-';	}

		if ($multiple['most_MultipleStartDate']=='0000-00-00') {	$multiple['most_MultipleStartDate'] = '-';	}

		if ($multiple['most_MultipleEndDate']=='0000-00-00') {	$multiple['most_MultipleEndDate'] = '-';	}



		# VIEW

			# DATA

			if ($axRow['mist_Description']=='') {	$axRow['mist_Description'] = '-';	}


		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['mist_MemberinStampID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
				<div class="modal fade" id="View'.$axRow['mist_MemberinStampID'].'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
					<div class="modal-dialog" role="document" style="width:55%">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>'.$axRow['miac_Name'].'</b></span>
						        <hr>
						        <center>
						        	'.$memberin_point.'<br><br>
						        	<table width="80%" class="myPopup">
						        		<tr>
						        			<td style="text-align:right" width="45%">Quantity</td>
						        			<td style="text-align:center" width="20px">:</td>
						        			<td>'.$axRow['mist_TimeQty'].' Times / '.$axRow['mist_StampQty'].' Stamp Qty</td>
						        		</tr>
						        	</table><br>
						        	<span style="font-size:12px">
					                <ul id="tapMenu" class="nav nav-tabs">
					                    <li class="active" style="width:25%">
					                    	<a data-toggle="tab" href="#time'.$axRow['mist_MemberinStampID'].'">
					                    	<center><b>Expiry</b></center></a>
					                    </li>
					                    <li style="width:25%">
					                    	<a data-toggle="tab" href="#limitation'.$axRow['mist_MemberinStampID'].'">
					                    	<center><b>Limitation</b></center></a>
					                   	</li>
					                    <li style="width:25%">
					                    	<a data-toggle="tab" href="#note'.$axRow['mist_MemberinStampID'].'">
					                    	<center><b>Note</b></center></a>
					                    </li>
					                    <li style="width:25%">
					                    	<a data-toggle="tab" href="#multiple'.$axRow['mist_MemberinStampID'].'">
					                    	<center><b>Multiple</b></center></a>
					                    </li>
					                </ul>
					                </span>
					                <div class="tab-content">
					                    <div id="time'.$axRow['mist_MemberinStampID'].'" class="tab-pane active"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Expiry</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['mist_CollectionMethod'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="limitation'.$axRow['mist_MemberinStampID'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Maximum</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$axRow['mist_MaxPointPerDay'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="note'.$axRow['mist_MemberinStampID'].'" class="tab-pane"><br>
					                    	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" valign="top" width="45%">Description</td>
								        			<td style="text-align:center" valign="top" width="5%">:</td>
								        			<td>'.$axRow['mist_Description'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                    <div id="multiple'.$axRow['mist_MemberinStampID'].'" class="tab-pane"><br>
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Multiple</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$multiple['most_Multiple'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Start Date</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$multiple['most_MultipleStartDate'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">End Date</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$multiple['most_MultipleEndDate'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                </div>
						        </center>
						    </div>
						    <div class="modal-footer">';

			if ($_SESSION['role_action']['stamp']['edit'] == 1) {	

				$view .= '  <a href="stamp_create.php?act=edit&id='.$axRow['mist_MemberinStampID'].'">
						        <button type="button" class="btn btn-default btn-sm">Edit</button></a>';
			}

				$view .= '      <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';



		# DATA TABLE

		$data_table .= '<tr >
							<td >'.$i.'</td>
							<td style="text-align:center">'.$axRow['mist_StampQty'].'</td>
							<td style="text-align:center">'.$point_icon.'</td>
							<td >'.$axRow['mist_CollectionMethod'].'</td>
							<td >'.$axRow['mist_MaxPointPerDay'].'</td>
							<td style="text-align:center">'.$status.'</td>
							<td >'.DateTime($axRow['mist_UpdatedDate']).'</td>';

		if ($_SESSION['role_action']['memberin_stamp']['view'] == 1) {

			$data_table .=	'<td style="text-align:center">'.$view.'</td>';
		}

		if ($_SESSION['role_action']['memberin_stamp']['delete'] == 1) {

			$data_table .= 	'<td style="text-align:center">'.$deleted.'</td>';
		}

		$data_table .=	'</tr>';
	}
}




$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_memberin_stamp');

$oTmp->assign('content_file', 'memberin_motivation/stamp.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
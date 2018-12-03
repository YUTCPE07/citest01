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

if ($_SESSION['role_action']['role']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];



$sql = 'SELECT

		role.*

	  	FROM role

		ORDER BY CASE 
			WHEN role_Deleted = "" THEN 1
            WHEN role_Deleted = "T" THEN 2 END ASC,
			role_Status ASC, 
			role_UpdatedDate DESC';


if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE role 
	 					SET role_Status='Pending',
	 						role_UpdatedDate='".$time_insert."',
	 						role_UpdatedBy='".$_SESSION['UID']."' 
	 					WHERE role_RoleID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="role.php";</script>';

} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE role 
	 					SET role_Status='Active',
	 						role_UpdatedDate='".$time_insert."',
	 						role_UpdatedBy='".$_SESSION['UID']."' 
	 					WHERE role_RoleID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="role.php";</script>';


} else if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT role_Deleted FROM role WHERE role_RoleID ="'.$id.'"';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);


	if($axRow['role_Deleted']=="") {

 		$do_sql_role = "UPDATE role 
	 						SET role_Deleted='T', 
	 							role_Status='Pending',
	 							role_UpdatedDate='".$time_insert."',
	 							role_UpdatedBy='".$_SESSION['UID']."' 
	 						WHERE role_RoleID='".$id."' ";

 	} else if ($axRow['role_Deleted']=="T") {

		$do_sql_role = "UPDATE role 
							SET role_Deleted='', 
	 							role_Status='Pending',
	 							role_UpdatedDate='".$time_insert."',
	 							role_UpdatedBy='".$_SESSION['UID']."' 
							WHERE role_RoleID='".$id."'";
	}

 	$oDB->QueryOne($do_sql_role);

 	echo '<script>window.location.href="role.php";</script>';


} else {


	$oRes = $oDB->Query($sql);

	$i=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;


		# DELETED

		if($axRow['role_Deleted']=='') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['role_RoleID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>
				<div class="modal fade" id="Deleted'.$axRow['role_RoleID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b><span>
						        <hr>
						        <center>
						            <span style="font-size:16px">
						            	<b>"'.$axRow['role_Name'].'"</b><br>
						           </span>
						        </center>
						        <p style="font-size:14px;padding-left:100px;"><br>
						            By clicking the <b>"Inactive"</b> button to:<br>
						            &nbsp; &nbsp;- Inactive this role
						        </p>
						    </div>
						    <div class="modal-footer">
						        <a href="role.php?act=delete&id='.$axRow['role_RoleID'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		} else if ($axRow['role_Deleted']=='T') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['role_RoleID'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>
				<div class="modal fade" id="Deleted'.$axRow['role_RoleID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b><span>
						        <hr>
						        <center>
						            <span style="font-size:16px">
						            	<b>"'.$axRow['role_Name'].'"</b><br>
						           </span>
						        </center>
						        <p style="font-size:14px;padding-left:100px;"><br>
						           	By clicking the <b>"Active"</b> button to:<br>
						            &nbsp; &nbsp;- Active this role
						        </p>
						    </div>
						    <div class="modal-footer">
						        <a href="role.php?act=delete&id='.$axRow['role_RoleID'].'">
						        <button type="button" class="btn btn-default btn-sm">Active</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		}



		# DATA VIEW

		$sql_0 = 'SELECT menu_master.*,
					role_action.roac_View,
					role_action.roac_Add,
					role_action.roac_Edit,
					role_action.roac_Delete

					FROM menu_master

					LEFT JOIN role_action
					ON menu_master.mema_MenuMasterID = role_action.mema_MenuMasterID

					WHERE role_action.role_RoleID = "'.$axRow['role_RoleID'].'" AND menu_master.mema_MenuLevel="0"
					ORDER BY menu_master.mema_SortNo';

		$oRes_0 = $oDB->Query($sql_0);

		$data_view = '';

		while ($axRow_0 = $oRes_0->FetchRow(DBI_ASSOC)){

			$check_1 = 'SELECT menu_master.*
						FROM menu_master
						WHERE mema_MenuLevel="1" AND mema_ParentID="'.$axRow_0['mema_MenuMasterID'].'"';

			$check_menu1 = $oDB->QueryOne($check_1);

			$data_1 = 'SELECT roac_View
						FROM role_action
						WHERE role_RoleID="'.$axRow['role_RoleID'].'" 
						AND mema_MenuMasterID="'.$axRow_0['mema_MenuMasterID'].'"';

			$data_1 = $oDB->QueryOne($data_1);

			if ($check_menu1 && $data_1=="T") {

				if ($axRow_0['roac_View'] == 'T') {	$checked_v = '<span class="glyphicon glyphicon-ok"></span>';	}
				else {	$checked_v = '<span class="glyphicon glyphicon-minus"></span>';	}

				$data_view .= '<tr>
									<td bgcolor="#CCCCCC" width="26%"><b>'.$axRow_0['mema_Name'].'</b></td>
									<td bgcolor="#CCCCCC" colspan="2"></td>
									<td bgcolor="#CCCCCC">'.$checked_v.'</td>
									<td bgcolor="#CCCCCC" colspan="3"></td>
								</tr>';

				$sql_1 = 'SELECT menu_master.*,
								role_action.roac_View,
								role_action.roac_Add,
								role_action.roac_Edit,
								role_action.roac_Delete

								FROM menu_master

								LEFT JOIN role_action
								ON menu_master.mema_MenuMasterID = role_action.mema_MenuMasterID

								WHERE mema_MenuLevel="1" AND role_action.role_RoleID = "'.$axRow['role_RoleID'].'" 
								AND mema_ParentID="'.$axRow_0['mema_MenuMasterID'].'"
								ORDER BY menu_master.mema_SortNo';

				$oRes_1 = $oDB->Query($sql_1);

				while ($axRow_1 = $oRes_1->FetchRow(DBI_ASSOC)){

					$check_2 = 'SELECT menu_master.*
									FROM menu_master
									WHERE mema_MenuLevel="2" AND mema_ParentID="'.$axRow_1['mema_MenuMasterID'].'"';

					$check_menu2 = $oDB->QueryOne($check_2);

					$data_2 = 'SELECT roac_View
								FROM role_action
								WHERE role_RoleID="'.$axRow['role_RoleID'].'" AND mema_MenuMasterID="'.$axRow_1['mema_MenuMasterID'].'"';

					$data_2 = $oDB->QueryOne($data_2);

					if ($check_menu2 && $data_2=="T") {

						if ($axRow_1['roac_View'] == 'T') {	$checked_v = '<span class="glyphicon glyphicon-ok"></span>';
						} else {	$checked_v = '<span class="glyphicon glyphicon-minus"></span>';	}

						$data_view .= '<tr>
											<td width="26%"></td>
											<td width="26%" bgcolor="#DDDDDD"><b>'.$axRow_1['mema_Name'].'</b></td>
											<td width="26%" bgcolor="#DDDDDD"></td>
											<td bgcolor="#DDDDDD">'.$checked_v.'</td>
											<td bgcolor="#DDDDDD" colspan="3"></td>
										</tr>';

						$sql_2 = 'SELECT menu_master.*,
										role_action.roac_View,
										role_action.roac_Add,
										role_action.roac_Edit,
										role_action.roac_Delete
										FROM menu_master
										LEFT JOIN role_action
										ON menu_master.mema_MenuMasterID = role_action.mema_MenuMasterID
										WHERE mema_MenuLevel="2" AND role_action.role_RoleID="'.$axRow['role_RoleID'].'" 
										AND mema_ParentID="'.$axRow_1['mema_MenuMasterID'].'"
										ORDER BY menu_master.mema_SortNo';

						$oRes_2 = $oDB->Query($sql_2);

						while ($axRow_2 = $oRes_2->FetchRow(DBI_ASSOC)){

							$check_3 = 'SELECT menu_master.*
											FROM menu_master
											WHERE mema_MenuLevel="3" AND mema_ParentID="'.$axRow_2['mema_MenuMasterID'].'"';

							$check_menu3 = $oDB->QueryOne($check_3);

							$data_3 = 'SELECT roac_View
										FROM role_action
										WHERE role_RoleID="'.$axRow['role_RoleID'].'" AND mema_MenuMasterID="'.$axRow_2['mema_MenuMasterID'].'"';

							$data_3 = $oDB->QueryOne($data_3);

							if ($check_menu3 && $data_3=="T") {

								if ($axRow_2['roac_View'] == 'T') {	$checked_v = '<span class="glyphicon glyphicon-ok"></span>';	
								} else {	$checked_v = '<span class="glyphicon glyphicon-minus"></span>';	}

								$data_view .= '<tr>
													<td width="26%"></td>
													<td width="26%"></td>
													<td width="26%" bgcolor="#EEEEEE"><b>'.$axRow_2['mema_Name'].'</b></td>
													<td bgcolor="#EEEEEE">'.$checked_v.'</td>
													<td colspan="3" bgcolor="#EEEEEE"></td>
												</tr>';

								$sql_3 = 'SELECT menu_master.*,
												role_action.roac_View,
												role_action.roac_Add,
												role_action.roac_Edit,
												role_action.roac_Delete
												FROM menu_master
												LEFT JOIN role_action
												ON menu_master.mema_MenuMasterID = role_action.mema_MenuMasterID
												WHERE mema_MenuLevel="3" AND role_action.role_RoleID="'.$axRow['role_RoleID'].'" AND mema_ParentID="'.$axRow_2['mema_MenuMasterID'].'"
												ORDER BY menu_master.mema_SortNo';

								$oRes_3 = $oDB->Query($sql_3);

								while ($axRow_3 = $oRes_3->FetchRow(DBI_ASSOC)){

									if ($axRow_3['roac_View'] == 'T') {	$checked_v = '<span class="glyphicon glyphicon-ok"></span>';	
									} else {	$checked_v = '<span class="glyphicon glyphicon-minus"></span>';	}

									if ($axRow_3['roac_Add'] == 'T') {	$checked_a = '<span class="glyphicon glyphicon-ok"></span>';	}
									else {	$checked_a = '<span class="glyphicon glyphicon-minus"></span>';	}

									if ($axRow_3['roac_Edit'] == 'T') {	$checked_e = '<span class="glyphicon glyphicon-ok"></span>';	}
									else {	$checked_e = '<span class="glyphicon glyphicon-minus"></span>';	}

									if ($axRow_3['roac_Delete'] == 'T') {	$checked_d = '<span class="glyphicon glyphicon-ok"></span>';	}
									else {	$checked_d = '<span class="glyphicon glyphicon-minus"></span>';	}

									$data_view .= '<tr>
													<td width="26%"></td>
													<td width="26%"></td>
													<td width="26%">'.$axRow_3['mema_Name'].'</td>
													<td>'.$checked_v.'</td>
													<td>'.$checked_a.'</td>
													<td>'.$checked_e.'</td>
													<td>'.$checked_d.'</td>
												</tr>';
								}

							} else {

								if ($check_menu3) {

									if ($axRow_2['roac_View'] == 'T') {	$checked_v = '<span class="glyphicon glyphicon-ok"></span>';	}

									else {	$checked_v = '<span class="glyphicon glyphicon-minus"></span>';	}

									$data_view .= '<tr>
														<td width="26%"></td>
														<td width="26%"></td>
														<td bgcolor="#EEEEEE" width="26%"><b>'.$axRow_2['mema_Name'].'</b></td>
														<td bgcolor="#EEEEEE">'.$checked_v.'</td>
														<td colspan="3" bgcolor="#EEEEEE"></td>
													</tr>';
								} else {

									if ($axRow_2['roac_View'] == 'T') {	$checked_v = '<span class="glyphicon glyphicon-ok"></span>';	}
									else {	$checked_v = '<span class="glyphicon glyphicon-minus"></span>';	}

									if ($axRow_2['roac_Add'] == 'T') {	$checked_a = '<span class="glyphicon glyphicon-ok"></span>';	}
									else {	$checked_a = '<span class="glyphicon glyphicon-minus"></span>';	}

									if ($axRow_2['roac_Edit'] == 'T') {	$checked_e = '<span class="glyphicon glyphicon-ok"></span>';	}
									else {	$checked_e = '<span class="glyphicon glyphicon-minus"></span>';	}

									if ($axRow_2['roac_Delete'] == 'T') {	$checked_d = '<span class="glyphicon glyphicon-ok"></span>';	}
									else {	$checked_d = '<span class="glyphicon glyphicon-minus"></span>';	}

									$data_view .= '<tr>
														<td width="26%"></td>
														<td width="26%">'.$axRow_2['mema_Name'].'</td>
														<td width="26%"></td>
														<td>'.$checked_v.'</td>
														<td>'.$checked_a.'</td>
														<td>'.$checked_e.'</td>
														<td>'.$checked_d.'</td>
													</tr>';
								}
							}
						}

					} else {

						if ($check_menu2) {

							if ($axRow_1['roac_View'] == 'T') {	$checked_v = '<span class="glyphicon glyphicon-ok"></span>';	}
							else {	$checked_v = '<span class="glyphicon glyphicon-minus"></span>';	}

							$data_view .= '<tr>
												<td width="26%"></td>
												<td bgcolor="#DDDDDD" width="26%"><b>'.$axRow_1['mema_Name'].'</b></td>
												<td bgcolor="#DDDDDD" width="26%"></td>
												<td bgcolor="#DDDDDD">'.$checked_v.'</td>
												<td bgcolor="#DDDDDD" colspan="3"></td>
											</tr>';
						} else {

							if ($axRow_1['roac_View'] == 'T') {	$checked_v = '<span class="glyphicon glyphicon-ok"></span>';	}
							else {	$checked_v = '<span class="glyphicon glyphicon-minus"></span>';	}

							if ($axRow_1['roac_Add'] == 'T') {	$checked_a = '<span class="glyphicon glyphicon-ok"></span>';	}
							else {	$checked_a = '<span class="glyphicon glyphicon-minus"></span>';	}

							if ($axRow_1['roac_Edit'] == 'T') {	$checked_e = '<span class="glyphicon glyphicon-ok"></span>';	}
							else {	$checked_e = '<span class="glyphicon glyphicon-minus"></span>';	}

							if ($axRow_1['roac_Delete'] == 'T') {	$checked_d = '<span class="glyphicon glyphicon-ok"></span>';	}
							else {	$checked_d = '<span class="glyphicon glyphicon-minus"></span>';	}

							$data_view .= '<tr>
												<td width="26%"></td>
												<td width="26%" bgcolor="#DDDDDD">'.$axRow_1['mema_Name'].'</td>
												<td width="26%" bgcolor="#DDDDDD"></td>
												<td bgcolor="#DDDDDD">'.$checked_v.'</td>
												<td bgcolor="#DDDDDD">'.$checked_a.'</td>
												<td bgcolor="#DDDDDD">'.$checked_e.'</td>
												<td bgcolor="#DDDDDD">'.$checked_d.'</td>
											</tr>';
						}
					}
				}
			}
		}

		# VIEW

		if ($axRow['role_MemberIn'] == 'T') { $MemberIn = ' (MemberIn)';
		} else {	 $MemberIn = '';	}

		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['role_RoleID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
				<div class="modal fade" id="View'.$axRow['role_RoleID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>'.$axRow['role_Name'].$MemberIn.'</b></span>
						        <hr>
						        <center>
						           	<table class="table table-bordered myPopupData">
								        <thead>
								        <tr class="th_table" align="center">
								            <th colspan="3">Menu</th>
								            <th width="5%">View</th>
								            <th width="5%">Add</th>
								            <th width="5%">Edit</th>
								            <th width="5%">Delete</th>
								        </tr></thead>
										<tbody>
											'.$data_view.'
										</tbody>
								    </table>
						        </center>
						    </div>
						    <div class="modal-footer">';

			if ($_SESSION['role_action']['role']['edit'] == 1) {		  
			  
				$view .= '       <a href="role_create.php?act=edit&id='.$axRow['role_RoleID'].'">
						        <button type="button" class="btn btn-default btn-sm">Edit</button></a>';
			}
				$view .= '      <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';


		# STATUS

		$status = '';

		if($axRow['role_Deleted']=='T'){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

		} else {

			if($axRow['role_Status']=='Active'){

				if ($_SESSION['role_action']['role']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'role.php?act=active&id='.$axRow['role_RoleID'].'\'">
		                    <option class="status_default" value="'.$axRow['role_RoleID'].'" selected>On</option>
		                    <option class="status_default">Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';
		        }

			} else {

				if ($_SESSION['role_action']['role']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_pending" name="pending_status" onchange="window.location.href=\'role.php?act=pending&id='.$axRow['role_RoleID'].'\'">
		                    <option class="status_default">On</option>
		                    <option class="status_default" value="'.$axRow['role_RoleID'].'" selected>Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
		        }
			}
		}


		# DATA TABLE

		$data_table .= '<tr >
							<td >'.$i.'</td>
							<td >'.$axRow['role_Name'].'</td>
							<td >'.$axRow['role_Type'].'</td>
							<td >'.$status.'</td>
							<td >'.DateTime($axRow['role_UpdatedDate']).'</td>';

		if ($_SESSION['role_action']['role']['view'] == 1) {

			$data_table .=	'<td style="text-align:center">'.$view.'</td>';
		}

		if ($_SESSION['role_action']['role']['delete'] == 1) {

			$data_table .= 	'<td style="text-align:center">'.$deleted.'</td>';
		}

		$data_table .=	'</tr>';

		$asData[] = $axRow;
	}
}



$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_role');

$oTmp->assign('content_file', 'role/role.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
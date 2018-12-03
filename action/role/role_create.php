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

if ($_SESSION['role_action']['role']['add'] != 1 || $_SESSION['role_action']['role']['add'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$time_insert = date("Y-m-d H:i:s");



# CHECK LOGIN

if ($_SESSION['user_type_id_ses'] != 1) {

	echo "<script>history.back();</script>";
	exit();
}

#######################################


# SEARCH MAX ROLE ID

	$sql_get_last_ins = 'SELECT max(role_RoleID) FROM role';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$role_id_new = $id_last_ins+1;

#######################################

# SEARCH MAX ROLE ACTION ID

	$sql_get_last_ins = 'SELECT max(roac_RoleActionID) FROM role_action';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$action_id_new = $id_last_ins+1;

#######################################



# DATA MENU MASTER

	$sql_0 = 'SELECT menu_master.*
					FROM menu_master
					WHERE mema_MenuLevel="0"
					ORDER BY mema_SortNo';

	$oRes_0 = $oDB->Query($sql_0);

	$data_menu = '';

	while ($axRow_0 = $oRes_0->FetchRow(DBI_ASSOC)){

		$data_menu .= '<tr bgcolor="#CCCCCC">
							<td width="15%"><b>'.$axRow_0['mema_Name'].'</b></td>
							<td colspan="2"></td>
							<td width="15%">
								<button type="button" class="btn btn-default btn-sm" id="'.$axRow_0['mema_MenuMasterID'].'" onclick="check_all(this.id)">
									<span class="glyphicon glyphicon-check" aria-hidden="true"></span>
								</button>
								<button type="button" class="btn btn-default btn-sm" id="'.$axRow_0['mema_MenuMasterID'].'" onclick="uncheck_all(this.id)">
									<span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>
								</button></td>
							<td><input type="checkbox" class="'.$axRow_0['mema_MenuMasterID'].'" name="view_'.$axRow_0['mema_MenuMasterID'].'" value="'.$axRow_0['mema_MenuMasterID'].'"></td>
							<td colspan="3"></td>
						</tr>';

		$sql_1 = 'SELECT menu_master.*
						FROM menu_master
						WHERE mema_MenuLevel="1" AND mema_ParentID="'.$axRow_0['mema_MenuMasterID'].'"
						ORDER BY mema_SortNo';

		$oRes_1 = $oDB->Query($sql_1);

		while ($axRow_1 = $oRes_1->FetchRow(DBI_ASSOC)){

			$check_2 = 'SELECT menu_master.*
							FROM menu_master
							WHERE mema_MenuLevel="2" AND mema_ParentID="'.$axRow_1['mema_MenuMasterID'].'"';

			$check_menu2 = $oDB->QueryOne($check_2);

			if ($check_menu2) {

				$data_menu .= '<tr>
									<td width="15%"></td>
									<td width="15%" bgcolor="#DDDDDD"><b>'.$axRow_1['mema_Name'].'</b></td>
									<td width="15%" bgcolor="#DDDDDD"></td>
									<td width="15%" bgcolor="#DDDDDD">
										<button type="button" class="btn btn-default btn-sm" id="'.$axRow_1['mema_MenuMasterID'].'" onclick="check_all(this.id)">
											<span class="glyphicon glyphicon-check" aria-hidden="true"></span>
										</button>
										<button type="button" class="btn btn-default btn-sm" id="'.$axRow_1['mema_MenuMasterID'].'" onclick="uncheck_all(this.id)">
											<span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>
										</button></td>
									<td bgcolor="#DDDDDD"><input type="checkbox" class="'.$axRow_1['mema_MenuMasterID'].'" name="view_'.$axRow_1['mema_MenuMasterID'].'" value="'.$axRow_1['mema_MenuMasterID'].'"></td>
									<td bgcolor="#DDDDDD" colspan="3"></td>
								</tr>';
			} else {

				$data_menu .= '<tr>
									<td width="15%"></td>
									<td width="15%">'.$axRow_1['mema_Name'].'</td>
									<td width="15%"></td>
									<td width="15%">
										<button type="button" class="btn btn-default btn-sm" id="'.$axRow_1['mema_MenuMasterID'].'" onclick="check_all(this.id)">
											<span class="glyphicon glyphicon-check" aria-hidden="true"></span>
										</button>
										<button type="button" class="btn btn-default btn-sm" id="'.$axRow_1['mema_MenuMasterID'].'" onclick="uncheck_all(this.id)">
											<span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>
										</button></td>
									<td><input type="checkbox" class="'.$axRow_1['mema_MenuMasterID'].'" name="view_'.$axRow_1['mema_MenuMasterID'].'" value="'.$axRow_1['mema_MenuMasterID'].'"></td>
									<td><input type="checkbox" class="'.$axRow_1['mema_MenuMasterID'].'" name="add_'.$axRow_1['mema_MenuMasterID'].'" value="'.$axRow_1['mema_MenuMasterID'].'"></td>
									<td><input type="checkbox" class="'.$axRow_1['mema_MenuMasterID'].'" name="edit_'.$axRow_1['mema_MenuMasterID'].'" value="'.$axRow_1['mema_MenuMasterID'].'"></td>
									<td><input type="checkbox" class="'.$axRow_1['mema_MenuMasterID'].'" name="delete_'.$axRow_1['mema_MenuMasterID'].'" value="'.$axRow_1['mema_MenuMasterID'].'"></td>
								</tr>';
			}

			$sql_2 = 'SELECT menu_master.*
							FROM menu_master
							WHERE mema_MenuLevel="2" AND mema_ParentID="'.$axRow_1['mema_MenuMasterID'].'"
							ORDER BY mema_SortNo';

			$oRes_2 = $oDB->Query($sql_2);

			while ($axRow_2 = $oRes_2->FetchRow(DBI_ASSOC)){

				$check_3 = 'SELECT menu_master.*
								FROM menu_master
								WHERE mema_MenuLevel="3" AND mema_ParentID="'.$axRow_2['mema_MenuMasterID'].'"';

				$check_menu3 = $oDB->QueryOne($check_3);

				if ($check_menu3) {

					$data_menu .= '<tr>
										<td width="15%"></td>
										<td width="15%"></td>
										<td width="15%" bgcolor="#EEEEEE"><b>'.$axRow_2['mema_Name'].'</b></td>
										<td width="15%" bgcolor="#EEEEEE">
											<button type="button" class="btn btn-default btn-sm" id="'.$axRow_2['mema_MenuMasterID'].'" onclick="check_all(this.id)">
												<span class="glyphicon glyphicon-check" aria-hidden="true"></span>
											</button>
											<button type="button" class="btn btn-default btn-sm" id="'.$axRow_2['mema_MenuMasterID'].'" onclick="uncheck_all(this.id)">
												<span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>
											</button></td>
										<td bgcolor="#EEEEEE"><input type="checkbox" class="'.$axRow_2['mema_MenuMasterID'].'" name="view_'.$axRow_2['mema_MenuMasterID'].'" value="'.$axRow_2['mema_MenuMasterID'].'"></td>
										<td colspan="3" bgcolor="#EEEEEE"></td>
									</tr>';

					$sql_3 = 'SELECT menu_master.*
								FROM menu_master
								WHERE mema_MenuLevel="3" AND mema_ParentID="'.$axRow_2['mema_MenuMasterID'].'"
								ORDER BY mema_SortNo';

					$oRes_3 = $oDB->Query($sql_3);

					while ($axRow_3 = $oRes_3->FetchRow(DBI_ASSOC)){

						$data_menu .= '<tr>
										<td width="15%"></td>
										<td width="15%"></td>
										<td width="15%">'.$axRow_3['mema_Name'].'</td>
										<td width="15%">
											<button type="button" class="btn btn-default btn-sm" id="'.$axRow_3['mema_MenuMasterID'].'" onclick="check_all(this.id)">
												<span class="glyphicon glyphicon-check" aria-hidden="true"></span>
											</button>
											<button type="button" class="btn btn-default btn-sm" id="'.$axRow_3['mema_MenuMasterID'].'" onclick="uncheck_all(this.id)">
												<span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>
											</button></td>
										<td><input type="checkbox" class="'.$axRow_3['mema_MenuMasterID'].'" name="view_'.$axRow_3['mema_MenuMasterID'].'" value="'.$axRow_3['mema_MenuMasterID'].'"></td>
										<td><input type="checkbox" class="'.$axRow_3['mema_MenuMasterID'].'" name="add_'.$axRow_3['mema_MenuMasterID'].'" value="'.$axRow_3['mema_MenuMasterID'].'"></td>
										<td><input type="checkbox" class="'.$axRow_3['mema_MenuMasterID'].'" name="edit_'.$axRow_3['mema_MenuMasterID'].'" value="'.$axRow_3['mema_MenuMasterID'].'"></td>
										<td><input type="checkbox" class="'.$axRow_3['mema_MenuMasterID'].'" name="delete_'.$axRow_3['mema_MenuMasterID'].'" value="'.$axRow_3['mema_MenuMasterID'].'"></td>
									</tr>';
					}

				} else {

					$data_menu .= '<tr>
										<td width="15%"></td>
										<td width="15%">'.$axRow_2['mema_Name'].'</td>
										<td width="15%"></td>
										<td width="15%">
											<button type="button" class="btn btn-default btn-sm" id="'.$axRow_2['mema_MenuMasterID'].'" onclick="check_all(this.id)">
												<span class="glyphicon glyphicon-check" aria-hidden="true"></span>
											</button>
											<button type="button" class="btn btn-default btn-sm" id="'.$axRow_2['mema_MenuMasterID'].'" onclick="uncheck_all(this.id)">
												<span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>
											</button></td>
										<td><input type="checkbox" class="'.$axRow_2['mema_MenuMasterID'].'" name="view_'.$axRow_2['mema_MenuMasterID'].'" value="'.$axRow_2['mema_MenuMasterID'].'"></td>
										<td><input type="checkbox" class="'.$axRow_2['mema_MenuMasterID'].'" name="add_'.$axRow_2['mema_MenuMasterID'].'" value="'.$axRow_2['mema_MenuMasterID'].'"></td>
										<td><input type="checkbox" class="'.$axRow_2['mema_MenuMasterID'].'" name="edit_'.$axRow_2['mema_MenuMasterID'].'" value="'.$axRow_2['mema_MenuMasterID'].'"></td>
										<td><input type="checkbox" class="'.$axRow_2['mema_MenuMasterID'].'" name="delete_'.$axRow_2['mema_MenuMasterID'].'" value="'.$axRow_2['mema_MenuMasterID'].'"></td>
									</tr>';
				}
			}
		}
	}


#######################################


if( $Act == 'edit' && $id != '' ){

	# EDIT

	$sql='	SELECT role.* FROM role WHERE role_RoleID ="'.$id.'"';

	$oRes = $oDB->Query($sql);

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$asData = $axRow;
	}


	$sql_0 = 'SELECT * FROM menu_master WHERE mema_MenuLevel="0" ORDER BY mema_SortNo';

	$oRes_0 = $oDB->Query($sql_0);

	$data_menu = '';

	while ($axRow_0 = $oRes_0->FetchRow(DBI_ASSOC)){

		$sql_check = 'SELECT * FROM role_action WHERE mema_MenuMasterID = "'.$axRow_0['mema_MenuMasterID'].'"';
		$oRes_check = $oDB->Query($sql_check);
		$check = $oRes_check->FetchRow(DBI_ASSOC);

		if ($check['roac_View'] == 'T') {	$checked_v = 'checked';	}
		else {	$checked_v = '';	}

		$data_menu .= '<tr bgcolor="#CCCCCC">
							<td width="15%"><b>'.$axRow_0['mema_Name'].'</b></td>
							<td colspan="2"></td>
							<td width="15%">
								<button type="button" class="btn btn-default btn-sm" id="'.$axRow_0['mema_MenuMasterID'].'" onclick="check_all(this.id)">
									<span class="glyphicon glyphicon-check" aria-hidden="true"></span>
								</button>
								<button type="button" class="btn btn-default btn-sm" id="'.$axRow_0['mema_MenuMasterID'].'" onclick="uncheck_all(this.id)">
									<span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>
								</button></td>
							<td><input type="checkbox" class="'.$axRow_0['mema_MenuMasterID'].'" name="view_'.$axRow_0['mema_MenuMasterID'].'" value="'.$axRow_0['mema_MenuMasterID'].'" '.$checked_v.'></td>
							<td colspan="3"></td>
						</tr>';

		$sql_1 = 'SELECT * FROM menu_master WHERE mema_MenuLevel="1" AND mema_ParentID="'.$axRow_0['mema_MenuMasterID'].'" ORDER BY mema_SortNo';

		$oRes_1 = $oDB->Query($sql_1);

		while ($axRow_1 = $oRes_1->FetchRow(DBI_ASSOC)){

			$check_2 = 'SELECT menu_master.*
							FROM menu_master
							WHERE mema_MenuLevel="2" AND mema_ParentID="'.$axRow_1['mema_MenuMasterID'].'"';

			$check_menu2 = $oDB->QueryOne($check_2);

			if ($check_menu2) {

				$sql_check = 'SELECT * 
								FROM role_action 
								WHERE mema_MenuMasterID = "'.$axRow_0['mema_MenuMasterID'].'"
								AND role_RoleID="'.$id.'"';

				$oRes_check = $oDB->Query($sql_check);
				$check = $oRes_check->FetchRow(DBI_ASSOC);

				if ($check['roac_View'] == 'T') {	$checked_v = 'checked';	}
				else {	$checked_v = '';	}

				$data_menu .= '<tr>
									<td width="15%"></td>
									<td width="15%" bgcolor="#DDDDDD"><b>'.$axRow_1['mema_Name'].'</b></td>
									<td width="15%" bgcolor="#DDDDDD"></td>
									<td width="15%" bgcolor="#DDDDDD">
										<button type="button" class="btn btn-default btn-sm" id="'.$axRow_1['mema_MenuMasterID'].'" onclick="check_all(this.id)">
											<span class="glyphicon glyphicon-check" aria-hidden="true"></span>
										</button>
										<button type="button" class="btn btn-default btn-sm" id="'.$axRow_1['mema_MenuMasterID'].'" onclick="uncheck_all(this.id)">
											<span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>
										</button></td>
									<td bgcolor="#DDDDDD"><input type="checkbox" class="'.$axRow_1['mema_MenuMasterID'].'" name="view_'.$axRow_1['mema_MenuMasterID'].'" value="'.$axRow_1['mema_MenuMasterID'].'" '.$checked_v.'></td>
									<td bgcolor="#DDDDDD" colspan="3"></td>
								</tr>';
			} else {

				$sql_check = 'SELECT * 
								FROM role_action 
								WHERE mema_MenuMasterID = "'.$axRow_1['mema_MenuMasterID'].'"
								AND role_RoleID="'.$id.'"';

				$oRes_check = $oDB->Query($sql_check);
				$check = $oRes_check->FetchRow(DBI_ASSOC);

				if ($check['roac_View'] == 'T') {	$checked_v = 'checked';	}
				else {	$checked_v = '';	}

				if ($check['roac_Add'] == 'T') {	$checked_a = 'checked';	}
				else {	$checked_a = '';	}

				if ($check['roac_Edit'] == 'T') {	$checked_e = 'checked';	}
				else {	$checked_e = '';	}

				if ($check['roac_Delete'] == 'T') {	$checked_d = 'checked';	}
				else {	$checked_d = '';	}

				$data_menu .= '<tr>
									<td width="15%"></td>
									<td width="15%">'.$axRow_1['mema_Name'].'</td>
									<td width="15%"></td>
									<td width="15%">
										<button type="button" class="btn btn-default btn-sm" id="'.$axRow_1['mema_MenuMasterID'].'" onclick="check_all(this.id)">
											<span class="glyphicon glyphicon-check" aria-hidden="true"></span>
										</button>
										<button type="button" class="btn btn-default btn-sm" id="'.$axRow_1['mema_MenuMasterID'].'" onclick="uncheck_all(this.id)">
											<span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>
										</button></td>
									<td><input type="checkbox" class="'.$axRow_1['mema_MenuMasterID'].'" name="view_'.$axRow_1['mema_MenuMasterID'].'" value="'.$axRow_1['mema_MenuMasterID'].'" '.$checked_v.'></td>
									<td><input type="checkbox" class="'.$axRow_1['mema_MenuMasterID'].'" name="add_'.$axRow_1['mema_MenuMasterID'].'" value="'.$axRow_1['mema_MenuMasterID'].'" '.$checked_a.'></td>
									<td><input type="checkbox" class="'.$axRow_1['mema_MenuMasterID'].'" name="edit_'.$axRow_1['mema_MenuMasterID'].'" value="'.$axRow_1['mema_MenuMasterID'].'" '.$checked_e.'></td>
									<td><input type="checkbox" class="'.$axRow_1['mema_MenuMasterID'].'" name="delete_'.$axRow_1['mema_MenuMasterID'].'" value="'.$axRow_1['mema_MenuMasterID'].'" '.$checked_d.'></td>
									</tr>';
			}


			$sql_2 = 'SELECT * 
						FROM menu_master 
						WHERE mema_MenuLevel="2" 
						AND mema_ParentID="'.$axRow_1['mema_MenuMasterID'].'" 
						ORDER BY mema_SortNo';

			$oRes_2 = $oDB->Query($sql_2);

			while ($axRow_2 = $oRes_2->FetchRow(DBI_ASSOC)){

				$check_3 = 'SELECT menu_master.*
								FROM menu_master
								WHERE mema_MenuLevel="3" 
								AND mema_ParentID="'.$axRow_2['mema_MenuMasterID'].'"';

				$check_menu3 = $oDB->QueryOne($check_3);

				if ($check_menu3) {

					$sql_check = 'SELECT * 
									FROM role_action 
									WHERE mema_MenuMasterID = "'.$axRow_2['mema_MenuMasterID'].'"
									AND role_RoleID="'.$id.'"';
									
					$oRes_check = $oDB->Query($sql_check);
					$check = $oRes_check->FetchRow(DBI_ASSOC);

					if ($check['roac_View'] == 'T') {	$checked_v = 'checked';	}
					else {	$checked_v = '';	}

					$data_menu .= '<tr>
										<td width="15%"></td>
										<td width="15%"></td>
										<td width="15%" bgcolor="#EEEEEE"><b>'.$axRow_2['mema_Name'].'</b></td>
										<td width="15%" bgcolor="#EEEEEE">
											<button type="button" class="btn btn-default btn-sm" id="'.$axRow_2['mema_MenuMasterID'].'" onclick="check_all(this.id)">
												<span class="glyphicon glyphicon-check" aria-hidden="true"></span>
											</button>
											<button type="button" class="btn btn-default btn-sm" id="'.$axRow_2['mema_MenuMasterID'].'" onclick="uncheck_all(this.id)">
												<span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>
											</button></td>
										<td bgcolor="#EEEEEE"><input type="checkbox" class="'.$axRow_2['mema_MenuMasterID'].'" name="view_'.$axRow_2['mema_MenuMasterID'].'" value="'.$axRow_2['mema_MenuMasterID'].'" '.$checked_v.'></td>
										<td colspan="3" bgcolor="#EEEEEE"></td>
									</tr>';

					$sql_3 = 'SELECT *
								FROM menu_master
								WHERE mema_MenuLevel="3" 
								AND mema_ParentID="'.$axRow_2['mema_MenuMasterID'].'"
								ORDER BY mema_SortNo';

					$oRes_3 = $oDB->Query($sql_3);

					while ($axRow_3 = $oRes_3->FetchRow(DBI_ASSOC)){

						$sql_check = 'SELECT * 
										FROM role_action 
										WHERE mema_MenuMasterID = "'.$axRow_3['mema_MenuMasterID'].'"
										AND role_RoleID="'.$id.'"';
										
						$oRes_check = $oDB->Query($sql_check);
						$check = $oRes_check->FetchRow(DBI_ASSOC);

						if ($check['roac_View'] == 'T') {	$checked_v = 'checked';	}
						else {	$checked_v = '';	}

						if ($check['roac_Add'] == 'T') {	$checked_a = 'checked';	}
						else {	$checked_a = '';	}

						if ($check['roac_Edit'] == 'T') {	$checked_e = 'checked';	}
						else {	$checked_e = '';	}

						if ($check['roac_Delete'] == 'T') {	$checked_d = 'checked';	}
						else {	$checked_d = '';	}

						$data_menu .= '<tr>
											<td width="15%"></td>
											<td width="15%"></td>
											<td width="15%">'.$axRow_3['mema_Name'].'</td>
											<td width="15%">
												<button type="button" class="btn btn-default btn-sm" id="'.$axRow_3['mema_MenuMasterID'].'" onclick="check_all(this.id)">
													<span class="glyphicon glyphicon-check" aria-hidden="true"></span>
												</button>
												<button type="button" class="btn btn-default btn-sm" id="'.$axRow_3['mema_MenuMasterID'].'" onclick="uncheck_all(this.id)">
													<span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>
												</button></td>
											<td><input type="checkbox" class="'.$axRow_3['mema_MenuMasterID'].'" name="view_'.$axRow_3['mema_MenuMasterID'].'" value="'.$axRow_3['mema_MenuMasterID'].'" '.$checked_v.'></td>
											<td><input type="checkbox" class="'.$axRow_3['mema_MenuMasterID'].'" name="add_'.$axRow_3['mema_MenuMasterID'].'" value="'.$axRow_3['mema_MenuMasterID'].'" '.$checked_a.'></td>
											<td><input type="checkbox" class="'.$axRow_3['mema_MenuMasterID'].'" name="edit_'.$axRow_3['mema_MenuMasterID'].'" value="'.$axRow_3['mema_MenuMasterID'].'" '.$checked_e.'></td>
											<td><input type="checkbox" class="'.$axRow_3['mema_MenuMasterID'].'" name="delete_'.$axRow_3['mema_MenuMasterID'].'" value="'.$axRow_3['mema_MenuMasterID'].'" '.$checked_d.'></td>
										</tr>';

					}

				} else {

					$sql_check = 'SELECT * 
									FROM role_action 
									WHERE mema_MenuMasterID = "'.$axRow_2['mema_MenuMasterID'].'"
									AND role_RoleID="'.$id.'"';
										
					$oRes_check = $oDB->Query($sql_check);
					$check = $oRes_check->FetchRow(DBI_ASSOC);

					if ($check['roac_View'] == 'T') {	$checked_v = 'checked';	}
					else {	$checked_v = '';	}

					if ($check['roac_Add'] == 'T') {	$checked_a = 'checked';	}
					else {	$checked_a = '';	}

					if ($check['roac_Edit'] == 'T') {	$checked_e = 'checked';	}
					else {	$checked_e = '';	}

					if ($check['roac_Delete'] == 'T') {	$checked_d = 'checked';	}
					else {	$checked_d = '';	}

					$data_menu .= '<tr>
										<td width="15%"></td>
										<td width="15%">'.$axRow_2['mema_Name'].'</td>
										<td width="15%"></td>
										<td width="15%">
											<button type="button" class="btn btn-default btn-sm" id="'.$axRow_2['mema_MenuMasterID'].'" onclick="check_all(this.id)">
												<span class="glyphicon glyphicon-check" aria-hidden="true"></span>
											</button>
											<button type="button" class="btn btn-default btn-sm" id="'.$axRow_2['mema_MenuMasterID'].'" onclick="uncheck_all(this.id)">
												<span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>
											</button></td>
										<td><input type="checkbox" class="'.$axRow_2['mema_MenuMasterID'].'" name="view_'.$axRow_2['mema_MenuMasterID'].'" value="'.$axRow_2['mema_MenuMasterID'].'" '.$checked_v.'></td>
										<td><input type="checkbox" class="'.$axRow_2['mema_MenuMasterID'].'" name="add_'.$axRow_2['mema_MenuMasterID'].'" value="'.$axRow_2['mema_MenuMasterID'].'" '.$checked_a.'></td>
										<td><input type="checkbox" class="'.$axRow_2['mema_MenuMasterID'].'" name="edit_'.$axRow_2['mema_MenuMasterID'].'" value="'.$axRow_2['mema_MenuMasterID'].'" '.$checked_e.'></td>
										<td><input type="checkbox" class="'.$axRow_2['mema_MenuMasterID'].'" name="delete_'.$axRow_2['mema_MenuMasterID'].'" value="'.$axRow_2['mema_MenuMasterID'].'" '.$checked_d.'></td>
									</tr>';
				}
			}
		}
	}

} else if( $Act == 'save' ){

	# ROLE

	$sql_role = '';

	$table_role = 'role';



	$role_Name = trim_txt($_REQUEST['role_Name']);

	$role_Status = trim_txt($_REQUEST['role_Status']);

	$role_Type = trim_txt($_REQUEST['role_Type']);



	if($role_Name){	$sql_role .= 'role_Name="'.$role_Name.'"';   }

	if($role_Status){	$sql_role .= ',role_Status="'.$role_Status.'"';   }

	if($role_Type){	$sql_role .= ',role_Type="'.$role_Type.'"';   }

	if($time_insert){	$sql_role .= ',role_UpdatedDate="'.$time_insert.'"';   }

	if($_SESSION['UID']){	$sql_role .= ',role_UpdatedBy="'.$_SESSION['UID'].'"';   }




	# ROLE ACTION

	$table_action = 'role_action';

	$menu_data = 'SELECT mema_MenuMasterID FROM menu_master WHERE mema_Deleted!="T"';

	$oRes_data = $oDB->Query($menu_data);

	while ($axRow_m = $oRes_data->FetchRow(DBI_ASSOC)){

		$sql_action = '';

		# VIEW

		if (trim_txt($_REQUEST['view_'.$axRow_m['mema_MenuMasterID']]) == $axRow_m['mema_MenuMasterID']) {

			$sql_action .= 'roac_View="T"';

		} else {	$sql_action .= 'roac_View="F"';	}


		# ADD

		if (trim_txt($_REQUEST['add_'.$axRow_m['mema_MenuMasterID']]) == $axRow_m['mema_MenuMasterID']) {

			$sql_action .= ',roac_Add="T"';

		} else {	$sql_action .= ',roac_Add="F"';	}



		# EDIT

		if (trim_txt($_REQUEST['edit_'.$axRow_m['mema_MenuMasterID']]) == $axRow_m['mema_MenuMasterID']) {

			$sql_action .= ',roac_Edit="T"';

		} else {	$sql_action .= ',roac_Edit="F"';	}



		# DELETE

		if (trim_txt($_REQUEST['delete_'.$axRow_m['mema_MenuMasterID']]) == $axRow_m['mema_MenuMasterID']) {

			$sql_action .= ',roac_Delete="T"';

		} else {	$sql_action .= ',roac_Delete="F"';	}

		if($_SESSION['UID']){	$sql_action .= ',roac_UpdatedBy="'.$_SESSION['UID'].'"';   }

		if($time_insert){	$sql_action .= ',roac_UpdatedDate="'.$time_insert.'"';   }




		# CHECK ID

		$sql_check = 'SELECT roac_RoleActionID 
						FROM role_action 
						WHERE role_RoleID="'.$id.'" 
						AND mema_MenuMasterID="'.$axRow_m['mema_MenuMasterID'].'"';

		$check_id = $oDB->QueryOne($sql_check);

		if ($id) {

			if ($check_id) {

				# UPDATE 

				$do_sql_action = "UPDATE ".$table_action." SET ".$sql_action." WHERE mema_MenuMasterID=".$axRow_m['mema_MenuMasterID']." AND role_RoleID=".$id;

			} else {

				# INSERT

				if ($action_id_new) {	$sql_action .= ',roac_RoleActionID="'.$action_id_new.'"';	}

				$sql_action .= ',role_RoleID="'.$id.'"';

				if ($axRow_m['mema_MenuMasterID']) {	$sql_action .= ',mema_MenuMasterID="'.$axRow_m['mema_MenuMasterID'].'"';	}

				if($time_insert){	$sql_action .= ',roac_CreatedDate="'.$time_insert.'"';   }

				if($_SESSION['UID']){	$sql_action .= ',roac_CreatedBy="'.$_SESSION['UID'].'"';   }

				$do_sql_action = 'INSERT INTO '.$table_action.' SET '.$sql_action;

				$action_id_new++;
			}

		} else {

			# INSERT

			if ($action_id_new) {	$sql_action .= ',roac_RoleActionID="'.$action_id_new.'"';	}

			if ($role_id_new) {	$sql_action .= ',role_RoleID="'.$role_id_new.'"';	}

			if ($axRow_m['mema_MenuMasterID']) {	$sql_action .= ',mema_MenuMasterID="'.$axRow_m['mema_MenuMasterID'].'"';	}

			if($time_insert){	$sql_action .= ',roac_CreatedDate="'.$time_insert.'"';   }

			if($_SESSION['UID']){	$sql_action .= ',roac_CreatedBy="'.$_SESSION['UID'].'"';   }

			$do_sql_action = 'INSERT INTO '.$table_action.' SET '.$sql_action;

			$action_id_new++;
		}

		$oDB->QueryOne($do_sql_action);
	}




	# ROLE

	if($id){

		# UPDATE

		$do_sql_role = 'UPDATE '.$table_role.' SET '.$sql_role.' WHERE role_RoleID="'.$id.'"';

	} else {

		# INSERT

		if($time_insert){	$sql_role .= ',role_CreatedDate="'.$time_insert.'"';   }

		if($_SESSION['UID']){	$sql_role .= ',role_CreatedBy="'.$_SESSION['UID'].'"';   }

		if($role_id_new){	$sql_role .= ',role_RoleID="'.$role_id_new.'"';   }

		$do_sql_role = 'INSERT INTO '.$table_role.' SET '.$sql_role;
	}

	$oDB->QueryOne($do_sql_role);	

	echo '<script>window.location.href="role.php";</script>';

	exit;
}




$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('data_menu', $data_menu);

$oTmp->assign('is_menu', 'is_role');

$oTmp->assign('content_file', 'role/role_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
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


if ($_SESSION['role_action']['communication']['edit'] != 1) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");
$id = $_REQUEST['id'];
$Act = $_REQUEST['act'];
$_SESSION['comm_brand'] = $id;


# SEARCH MAX COMMUNICATION_ID

	$sql_get_last_ins = 'SELECT max(comm_CommunicationID) FROM communication';
	$comm_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$comm_new = $comm_last_ins+1;

#######################################


if ($Act == 'edit' && $id != '' ){

	$data_table = "<div class='table-responsive'>
					<table id='example' class='table table-bordered' style='background-color:white;'>
					<tr class='th_table'>
						<td style='text-align:center' rowspan='3' width='200px'><b>Topic</b></td>";

	# USER

	$sql_user = 'SELECT mi_user.user_id AS id,
					mi_user.email,
					mi_contact.mobile,
					mi_contact.firstname,
					mi_contact.lastname
				FROM mi_user 
				LEFT JOIN mi_contact
				ON mi_user.user_id = mi_contact.user_id
				WHERE mi_user.flag_del!="1"
				AND mi_user.role_RoleID="4"
				AND mi_user.brand_id="'.$id.'"';
	$oRes_user = $oDB->Query($sql_user);

	$count_head = 0;

	while ($user = $oRes_user->FetchRow(DBI_ASSOC)){

		$count_head++;
	}

	$data_table .= '	<td style="text-align:center" colspan="'.($count_head*2).'"><b>User</b></td>
					</tr>
					<tr class="th_table">';

	$oRes_user = $oDB->Query($sql_user);
	while ($user = $oRes_user->FetchRow(DBI_ASSOC)){

		$member_name = '';

		if ($user['firstname'] || $user['lastname']) {

			if ($user['email']) {

				if ($user['mobile'] != '+66') {
								
					$member_name = $user['firstname'].' '.$user['lastname'].'<br>'.$user['email'].'<br>'.$user['mobile'];

				} else { $member_name = $user['firstname'].' '.$user['lastname'].'<br>'.$user['email']; }

			} else {

				if ($user['mobile'] != '+66') {
								
					$member_name = $user['firstname'].' '.$user['lastname'].'<br>'.$user['mobile'];

				} else { $member_name = $user['firstname'].' '.$user['lastname']; }
			}

		} else {

			if ($user['email']) {

				if ($user['mobile'] != '+66') { $member_name = $user['email'].'<br>'.$user['mobile'];

				} else { $member_name = $user['email']; }

			} else {

				if ($user['mobile'] != '+66') { $member_name = $user['mobile'];

				} else { $member_name = ''; }
			}
		}

		$data_table .= '<td colspan="2" style="text-align:center">
							<b>'.$member_name.'</b><br>
							<a href="user_create.php?act=edit&brand_id='.$id.'&user_id='.$user['id'].'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></a> &nbsp; &nbsp;
							<a href="communication_edit.php?act=delete&user='.$user['id'].'&id='.$id.'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button></a>
						</td>';
	}

	$data_table .= '</tr>
					<tr class="th_table">';

	$oRes_user = $oDB->Query($sql_user);
	while ($user = $oRes_user->FetchRow(DBI_ASSOC)){

		$data_table .= '<td style="text-align:center">
							<span class="glyphicon glyphicon-envelope" style="font-size:15px"></span></td>
						<td style="text-align:center">
							<span class="glyphicon glyphicon-phone" style="font-size:15px"></span></td>';
	}

	$data_table .= '</tr>';


	# TOPIC

	$sql_topic = "SELECT * FROM communication_topic WHERE coto_Deleted!='T' ORDER BY coto_Name ASC";
	$oRes_topic = $oDB->Query($sql_topic);

	$count_head = ($count_head*2)+1;

	while ($topic = $oRes_topic->FetchRow(DBI_ASSOC)){	

		$data_table .= '<tr style="background-color:#DDD">
							<td style="text-align:center"><b>'.$topic['coto_Name'].'</b></td>';

		$oRes_user = $oDB->Query($sql_user);
		while ($user = $oRes_user->FetchRow(DBI_ASSOC)){

			$data_table .= '<td style="text-align:center">
							<button type="button" class="btn btn-default btn-sm" id="'.$user['id'].'_'.$topic['coto_TopicID'].'" onclick="all_email(this.id)">
								<span class="glyphicon glyphicon-check" aria-hidden="true"></span>
							</button>
							<button type="button" class="btn btn-default btn-sm" id="'.$user['id'].'_'.$topic['coto_TopicID'].'" onclick="unall_email(this.id)">
								<span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>
							</button></td>
						<td style="text-align:center">
							<button type="button" class="btn btn-default btn-sm" id="'.$user['id'].'_'.$topic['coto_TopicID'].'" onclick="all_mobile(this.id)">
								<span class="glyphicon glyphicon-check" aria-hidden="true"></span>
							</button>
							<button type="button" class="btn btn-default btn-sm" id="'.$user['id'].'_'.$topic['coto_TopicID'].'" onclick="unall_mobile(this.id)">
								<span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>
							</button></td>';

		}

		$data_table .= '<tr>';

		if ($topic['coto_Anywhere']=='T') {

			$data_table .= '<tr>
								<td style="text-align:center"><b>Anywhere</b></td>';

			$oRes_user = $oDB->Query($sql_user);
			while ($user = $oRes_user->FetchRow(DBI_ASSOC)){

				if ($topic['coto_Email']=='T') { 

					$sql_comm = 'SELECT comm_Deleted AS del, comm_CommunicationID AS id FROM communication WHERE user_UserID="'.$user['id'].'" AND coto_TopicID="'.$topic['coto_TopicID'].'" AND brnc_BranchID="0" AND comm_Type="Email"';

					$oRes_email = $oDB->Query($sql_comm);
					$email = $oRes_email->FetchRow(DBI_ASSOC);

					$select = '';

					if ($email['del']=='' && $email['id']!='') { $select = 'checked'; }
					else { $select = ''; }

					$data_table .= "<td style='text-align:center'><input type='checkbox' id='e".$user['id']."_".$topic['coto_TopicID']."'  name='e".$topic['coto_TopicID']."_".$user['id']."_0' value='T' ".$select."></td>"; 

				} else {

					$data_table .= "<td style='text-align:center'><span style='margin-top:2px' class='glyphicon glyphicon-remove'></span></td>";
				}

				if ($topic['coto_Mobile']=='T') { 

					$sql_comm = 'SELECT comm_Deleted AS del, comm_CommunicationID AS id FROM communication WHERE user_UserID="'.$user['id'].'" AND coto_TopicID="'.$topic['coto_TopicID'].'" AND brnc_BranchID="0" AND comm_Type="Mobile"';

					$oRes_email = $oDB->Query($sql_comm);
					$email = $oRes_email->FetchRow(DBI_ASSOC);

					$select = '';

					if ($email['del']=='' && $email['id']!='') { $select = 'checked'; }
					else { $select = ''; }

					$data_table .= "<td style='text-align:center'><input type='checkbox' id='m".$user['id']."_".$topic['coto_TopicID']."' name='m".$topic['coto_TopicID']."_".$user['id']."_0' value='T' ".$select."></td>"; 

				} else {

					$data_table .= "<td style='text-align:center'><span style='margin-top:2px' class='glyphicon glyphicon-remove'></span></td>";
				}
			}

			$data_table .= "</tr>";
		}

		if ($topic['coto_Branch']=='T') {

			$sql_branch = "SELECT branch_id, name FROM mi_branch WHERE flag_status='1' AND flag_del='0' AND brand_id='".$id."'";
			$oRes_branch = $oDB->Query($sql_branch);
			while ($branch = $oRes_branch->FetchRow(DBI_ASSOC)){

				$data_table .= '<td style="text-align:center"><b>'.$branch['name'].'</b></td>';

				$oRes_user = $oDB->Query($sql_user);
				while ($user = $oRes_user->FetchRow(DBI_ASSOC)){

					if ($topic['coto_Email']=='T') { 

						$sql_comm = 'SELECT comm_Deleted AS del, comm_CommunicationID AS id FROM communication WHERE user_UserID="'.$user['id'].'" AND coto_TopicID="'.$topic['coto_TopicID'].'" AND brnc_BranchID="'.$branch['branch_id'].'" AND comm_Type="Email"';

						$oRes_email = $oDB->Query($sql_comm);
						$email = $oRes_email->FetchRow(DBI_ASSOC);

						$select = '';

						if ($email['del']=='' && $email['id']!='') { $select = 'checked'; }
						else { $select = ''; }

						$data_table .= "<td style='text-align:center'><input type='checkbox' id='e".$user['id']."_".$topic['coto_TopicID']."'  name='e".$topic['coto_TopicID']."_".$user['id']."_".$branch['branch_id']."'value='T' ".$select."></td>"; 

					} else {

						$data_table .= "<td style='text-align:center'><span style='margin-top:2px' class='glyphicon glyphicon-remove'></span></td>";
					}

					if ($topic['coto_Mobile']=='T') { 

						$sql_comm = 'SELECT comm_Deleted AS del, comm_CommunicationID AS id FROM communication WHERE user_UserID="'.$user['id'].'" AND coto_TopicID="'.$topic['coto_TopicID'].'" AND brnc_BranchID="'.$branch['branch_id'].'" AND comm_Type="Mobile"';

						$oRes_email = $oDB->Query($sql_comm);
						$email = $oRes_email->FetchRow(DBI_ASSOC);

						$select = '';

						if ($email['del']=='' && $email['id']!='') { $select = 'checked'; }
						else { $select = ''; }

						$data_table .= "<td style='text-align:center'><input type='checkbox' id='m".$user['id']."_".$topic['coto_TopicID']."' name='m".$topic['coto_TopicID']."_".$user['id']."_".$branch['branch_id']."' value='T' ".$select."></td>"; 

					} else {

						$data_table .= "<td style='text-align:center'><span style='margin-top:2px' class='glyphicon glyphicon-remove'></span></td>";
					}
				}

				$data_table .= '</tr>';
			}

		} else {

			$data_table .= '<tr>
								<td style="text-align:center"><b>All Branch</b></td>';

			$oRes_user = $oDB->Query($sql_user);
			while ($user = $oRes_user->FetchRow(DBI_ASSOC)){

				if ($topic['coto_Email']=='T') { 

					$sql_comm = 'SELECT comm_CommunicationID AS del, comm_CommunicationID AS id FROM communication WHERE user_UserID="'.$user['id'].'" AND coto_TopicID="'.$topic['coto_TopicID'].'" AND brnc_BranchID!="0" AND comm_Type="Email" AND comm_Deleted="T"';

					$check_all = $oDB->QueryOne($sql_comm);

					$select = '';

					if ($check_all) { $select = ''; }
					else { $select = 'checked'; }

					$data_table .= "<td style='text-align:center'><input type='checkbox' id='e".$user['id']."_".$topic['coto_TopicID']."'  name='e".$topic['coto_TopicID']."_".$user['id']."_All' value='T' ".$select."></td>"; 

				} else {

					$data_table .= "<td style='text-align:center'><span style='margin-top:2px' class='glyphicon glyphicon-remove'></span></td>";
				}

				if ($topic['coto_Mobile']=='T') { 

					$sql_comm = 'SELECT comm_CommunicationID AS del, comm_CommunicationID AS id FROM communication WHERE user_UserID="'.$user['id'].'" AND coto_TopicID="'.$topic['coto_TopicID'].'" AND brnc_BranchID!="0" AND comm_Type="Mobile" AND comm_Deleted="T"';

					$check_all = $oDB->QueryOne($sql_comm);

					$select = '';

					if ($check_all) { $select = ''; }
					else { $select = 'checked'; }

					$data_table .= "<td style='text-align:center'><input type='checkbox' id='m".$user['id']."_".$topic['coto_TopicID']."' name='m".$topic['coto_TopicID']."_".$user['id']."_All' value='T' ".$select."></td>"; 

				} else {

					$data_table .= "<td style='text-align:center'><span style='margin-top:2px' class='glyphicon glyphicon-remove'></span></td>";
				}
			}

			$data_table .= '</tr>';
		}
	}



	$data_table .= "</tr>";

	$data_table .= "</table></div>";

	$oTmp->assign('data_table', $data_table);

	$oTmp->assign('brand_id', $id);



} else if($Act == 'save') {


	$sql_topic = "SELECT * FROM communication_topic WHERE coto_Deleted!='T'";
	$oRes_topic = $oDB->Query($sql_topic);


	while ($topic = $oRes_topic->FetchRow(DBI_ASSOC)){

		$sql_user = "SELECT user_id FROM mi_user WHERE flag_del='0' AND brand_id='".$id."' AND role_RoleID='4'";
		$oRes_user = $oDB->Query($sql_user);

		while ($user = $oRes_user->FetchRow(DBI_ASSOC)){

			# EMAIL

			$sql_branch = "SELECT branch_id FROM mi_branch WHERE flag_del='0' AND brand_id='".$id."' AND flag_status='1'";
			$oRes_branch = $oDB->Query($sql_branch);

			while ($branch = $oRes_branch->FetchRow(DBI_ASSOC)){

				$sql_comm = '';

				if ($topic['coto_Branch']=='T') {

					$email_val = $_REQUEST["e".$topic['coto_TopicID']."_".$user['user_id']."_".$branch['branch_id']];
					if ($email_val=='T') { $sql_comm .= 'comm_Deleted=""'; } 
					else { $sql_comm .= 'comm_Deleted="T"'; }

				} else { 

					$email_val = $_REQUEST["e".$topic['coto_TopicID']."_".$user['user_id']."_All"];
					if ($email_val=='T') { $sql_comm .= 'comm_Deleted=""'; } 
					else { $sql_comm .= 'comm_Deleted="T"'; }
				}


				# CHECK

				$sql_id = "SELECT comm_CommunicationID FROM communication WHERE coto_TopicID='".$topic['coto_TopicID']."' AND user_UserID='".$user['user_id']."' AND comm_Type='Email' AND brnc_BranchID='".$branch['branch_id']."'";

				$check_id = $oDB->QueryOne($sql_id);

				$sql_comm .= ',comm_UpdatedDate="'.$time_insert.'"';   

				$sql_comm .= ',comm_UpdatedBy="'.$_SESSION['UID'].'"';

				$sql_comm .= ',comm_Type="Email"';

				$sql_comm .= ',user_UserID="'.$user['user_id'].'"';

				$sql_comm .= ',bran_BrandID="'.$id.'"';

				$sql_comm .= ',brnc_BranchID="'.$branch['branch_id'].'"';

				$sql_comm .= ',coto_TopicID="'.$topic['coto_TopicID'].'"';

				if ($check_id) {

					# UPDATE

					$do_sql_comm = "UPDATE communication SET ".$sql_comm." WHERE comm_CommunicationID='".$check_id."'";
					$oDB->QueryOne($do_sql_comm);

				} else {

					# INSERT

					$sql_comm .= ',comm_CommunicationID="'.$comm_new.'"';  

					$sql_comm .= ',comm_CreatedDate="'.$time_insert.'"';  

					$sql_comm .= ',comm_CreatedBy="'.$_SESSION['UID'].'"'; 


					$do_sql_comm = "INSERT communication SET ".$sql_comm."";
					$oDB->QueryOne($do_sql_comm);

					$comm_new++;
				}
			}

			# ANY WHERE

			$sql_comm = '';

			$email_val = $_REQUEST["e".$topic['coto_TopicID']."_".$user['user_id']."_0"];
			if ($email_val=='T') { $sql_comm .= 'comm_Deleted=""'; } 
			else { $sql_comm .= 'comm_Deleted="T"'; }

			# CHECK

			$sql_id = "SELECT comm_CommunicationID FROM communication WHERE coto_TopicID='".$topic['coto_TopicID']."' AND user_UserID='".$user['user_id']."' AND comm_Type='Email' AND brnc_BranchID='0'";

			$check_id = $oDB->QueryOne($sql_id);

			$sql_comm .= ',comm_UpdatedDate="'.$time_insert.'"';   

			$sql_comm .= ',comm_UpdatedBy="'.$_SESSION['UID'].'"';

			$sql_comm .= ',comm_Type="Email"';

			$sql_comm .= ',user_UserID="'.$user['user_id'].'"';

			$sql_comm .= ',bran_BrandID="'.$id.'"';

			$sql_comm .= ',brnc_BranchID="0"';

			$sql_comm .= ',coto_TopicID="'.$topic['coto_TopicID'].'"';

			if ($check_id) {

				# UPDATE

				$do_sql_comm = "UPDATE communication SET ".$sql_comm." WHERE comm_CommunicationID='".$check_id."'";
				$oDB->QueryOne($do_sql_comm);

			} else {

				# INSERT

				$sql_comm .= ',comm_CommunicationID="'.$comm_new.'"';  

				$sql_comm .= ',comm_CreatedDate="'.$time_insert.'"';  

				$sql_comm .= ',comm_CreatedBy="'.$_SESSION['UID'].'"'; 


				$do_sql_comm = "INSERT communication SET ".$sql_comm."";
				$oDB->QueryOne($do_sql_comm);

				$comm_new++;
			}


			# MOBILE


			$sql_branch = "SELECT branch_id FROM mi_branch WHERE flag_del='0' AND brand_id='".$id."' AND flag_status='1'";
			$oRes_branch = $oDB->Query($sql_branch);

			while ($branch = $oRes_branch->FetchRow(DBI_ASSOC)){

				$sql_comm = '';

				if ($topic['coto_Branch']=='T') {

					$mobile_val = $_REQUEST["m".$topic['coto_TopicID']."_".$user['user_id']."_".$branch['branch_id']];
					if ($mobile_val=='T') { $sql_comm .= 'comm_Deleted=""'; } 
					else { $sql_comm .= 'comm_Deleted="T"'; }

				} else { 

					$mobile_val = $_REQUEST["m".$topic['coto_TopicID']."_".$user['user_id']."_All"];
					if ($mobile_val=='T') { $sql_comm .= 'comm_Deleted=""'; } 
					else { $sql_comm .= 'comm_Deleted="T"'; }
				}


				# CHECK

				$sql_id = "SELECT comm_CommunicationID FROM communication WHERE coto_TopicID='".$topic['coto_TopicID']."' AND user_UserID='".$user['user_id']."' AND comm_Type='Mobile' AND brnc_BranchID='".$branch['branch_id']."'";
				$check_id = $oDB->QueryOne($sql_id);


				$sql_comm .= ',comm_UpdatedDate="'.$time_insert.'"';   

				$sql_comm .= ',comm_UpdatedBy="'.$_SESSION['UID'].'"';

				$sql_comm .= ',comm_Type="Mobile"';

				$sql_comm .= ',user_UserID="'.$user['user_id'].'"';

				$sql_comm .= ',bran_BrandID="'.$id.'"';

				$sql_comm .= ',brnc_BranchID="'.$branch['branch_id'].'"';

				$sql_comm .= ',coto_TopicID="'.$topic['coto_TopicID'].'"';


				if ($check_id) {

					# UPDATE

					$do_sql_comm = "UPDATE communication SET ".$sql_comm." WHERE comm_CommunicationID='".$check_id."'";
					$oDB->QueryOne($do_sql_comm);

				} else {

					# INSERT

					$sql_comm .= ',comm_CommunicationID="'.$comm_new.'"';  

					$sql_comm .= ',comm_CreatedDate="'.$time_insert.'"';  

					$sql_comm .= ',comm_CreatedBy="'.$_SESSION['UID'].'"'; 


					$do_sql_comm = "INSERT communication SET ".$sql_comm."";
					$oDB->QueryOne($do_sql_comm);

					$comm_new++;

				}
			}

			# ANY WHERE

			$sql_comm = '';

			$email_val = $_REQUEST["m".$topic['coto_TopicID']."_".$user['user_id']."_0"];
			if ($email_val=='T') { $sql_comm .= 'comm_Deleted=""'; } 
			else { $sql_comm .= 'comm_Deleted="T"'; }

			# CHECK

			$sql_id = "SELECT comm_CommunicationID FROM communication WHERE coto_TopicID='".$topic['coto_TopicID']."' AND user_UserID='".$user['user_id']."' AND comm_Type='Mobile' AND brnc_BranchID='0'";

			$check_id = $oDB->QueryOne($sql_id);

			$sql_comm .= ',comm_UpdatedDate="'.$time_insert.'"';   

			$sql_comm .= ',comm_UpdatedBy="'.$_SESSION['UID'].'"';

			$sql_comm .= ',comm_Type="Mobile"';

			$sql_comm .= ',user_UserID="'.$user['user_id'].'"';

			$sql_comm .= ',bran_BrandID="'.$id.'"';

			$sql_comm .= ',brnc_BranchID="0"';

			$sql_comm .= ',coto_TopicID="'.$topic['coto_TopicID'].'"';

			if ($check_id) {

				# UPDATE

				$do_sql_comm = "UPDATE communication SET ".$sql_comm." WHERE comm_CommunicationID='".$check_id."'";
				$oDB->QueryOne($do_sql_comm);

			} else {

				# INSERT

				$sql_comm .= ',comm_CommunicationID="'.$comm_new.'"';  

				$sql_comm .= ',comm_CreatedDate="'.$time_insert.'"';  

				$sql_comm .= ',comm_CreatedBy="'.$_SESSION['UID'].'"'; 


				$do_sql_comm = "INSERT communication SET ".$sql_comm."";
				$oDB->QueryOne($do_sql_comm);

				$comm_new++;
			}
		}
	}


	echo '<script type="text/javascript">window.location.href="communication.php";</script>';

	exit;



} else if($Act == 'delete') {

	$sql_email = "";

	$coem_EmailID = trim_txt($_REQUEST['email_id']);


	$sql_email .= 'coem_UpdatedDate="'.$time_insert.'"';   

	$sql_email .= ',coem_UpdatedBy="'.$_SESSION['UID'].'"';

	$sql_email .= ',coem_Deleted="T"';


	$do_sql_email = "UPDATE communication_email SET ".$sql_email." WHERE coem_EmailID='".$coem_EmailID."'";

	$oDB->QueryOne($do_sql_email);


	echo '<script type="text/javascript">window.location.href="communication_email.php?act=edit&id='.$id.'";</script>';
	exit;

}



$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_communication');

$oTmp->assign('content_file', 'communication/communication_edit.htm');

$oTmp->display('layout/template.html');


//========================================//


$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}


//========================================//

?>
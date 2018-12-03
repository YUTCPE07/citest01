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

if ($_SESSION['role_action']['user']['view'] != 1) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];


# SEARCH

$where_type = '';

$where_search = "";

$type_status = $_REQUEST['type_status'];

if ($type_status == "All" || !$type_status) {	$where_type = '';	} 
else {	$where_type = ' AND mi_user_type.user_type_id="'.$type_status.'"';	}

$brand_id = "";

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
else {	$where_search = " AND mi_brand.brand_id IN (".$brand_id.")";	}



# SQL

$sql = 'SELECT

		mi_user.*,
		mi_user.user_id AS id_user,
  		mi_user.flag_del AS status_del,
		IF(mi_user_type.name="Branch",CONCAT(mi_user_type.name,"<br>(",mi_branch.name,")"),mi_user_type.name) AS user_type,
  		mi_brand.logo_image,
  		mi_brand.path_logo,
  		mi_brand.name AS brand_name,
  		role.role_Name AS role_name,
  		mi_contact.*

		FROM mi_user

		LEFT JOIN mi_user_type
		ON mi_user.user_type_id = mi_user_type.user_type_id

		LEFT JOIN mi_brand
		ON mi_user.brand_id = mi_brand.brand_id

		LEFT JOIN mi_branch
		ON mi_user.branch_id = mi_branch.branch_id

		LEFT JOIN role
		ON mi_user.role_RoleID = role.role_RoleID

		LEFT JOIN mi_contact
		ON mi_user.user_id = mi_contact.user_id

		WHERE role.role_RoleID != "4"

		'.$where_search.'
		'.$where_type.'

		ORDER BY CASE
			WHEN mi_user.flag_del = "0" THEN 1
            WHEN mi_user.flag_del = "1" THEN 2 END ASC,
			mi_user.flag_status ASC,
			mi_user.date_update DESC';


if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE mi_user 
	 					SET flag_status='2',
	 						date_update='".$time_insert."' 
	 					WHERE user_id='".$id."'";

 	$oDB->QueryOne($do_sql_status);
 	echo '<script>window.location.href="user.php";</script>';


} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE mi_user 
	 					SET flag_status='1',
	 						date_update='".$time_insert."'
	 					WHERE user_id='".$id."'";

 	$oDB->QueryOne($do_sql_status);
 	echo '<script>window.location.href="user.php";</script>';


} else if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT flag_del FROM mi_user WHERE user_id ="'.$id.'"';

	$oRes = $oDB->Query($sql);
	$axRow = $oRes->FetchRow(DBI_ASSOC);
		
	if($axRow['flag_del']==0) {
 				
 		$do_sql_user = "UPDATE mi_user
 							SET flag_del=1, 
 							flag_status='2',
 							date_update='".$time_insert."' 
 							WHERE user_id='".$id."'";

 	} else if ($axRow['flag_del']==1) {
 				
 		$do_sql_user = "UPDATE mi_user 
 							SET flag_del=0, 
 							flag_status='2',
 							date_update='".$time_insert."' 
 							WHERE user_id='".$id."'";
	}

 	$oDB->QueryOne($do_sql_user);
 			
 	echo '<script>window.location.href="user.php";</script>';



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

				if ($_SESSION['role_action']['user']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'user.php?act=active&id='.$axRow['id_user'].'\'">
		                    <option class="status_default" value="'.$axRow['id_user'].'" selected>On</option>
		                    <option class="status_default">Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';
		        }

			} else {

				if ($_SESSION['role_action']['user']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_pending" name="pending_status" onchange="window.location.href=\'user.php?act=pending&id='.$axRow['id_user'].'\'">
		                    <option class="status_default">On</option>
		                    <option class="status_default" value="'.$axRow['id_user'].'" selected>Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
		        }
			}

		}


		# LOGO

		if($axRow['logo_image']!=''){

			$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';

			$logo_image = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="100" height="100"/>';

		} else {

			$logo_brand = '<img src="../../images/400x400.png" width="60" height="60"/>';

			$logo_image = '<img src="../../images/400x400.png" width="100" height="100"/>';

		}

		if ($axRow['brand_id']=='0') {

			$logo_brand = '<img src="../../images/mi_action_logo.png" width="60" class="image_border" height="60"/>';

			$logo_image = '<img src="../../images/mi_action_logo.png" width="100" class="image_border" height="100"/>';

			$axRow['brand_name'] = 'MemberIn';
		}


		# TRIAL

		if ($axRow['flag_trial']=='15') {	

			$axRow['flag_trial'] = "15 Days";
			$expried = DateOnly($axRow['date_expried']);

		} else if ($axRow['flag_trial']=='30') {	

			$axRow['flag_trial'] = "30 Days";
			$expried = DateOnly($axRow['date_expried']);

		} else if ($axRow['flag_trial']=='Specific') {	

			$expried = DateOnly($axRow['date_expried']);

		} else {	

			$axRow['flag_trial'] = "-";	
			$expried = "-";
		}



		# LAST LOGIN DATE

		if ($axRow['last_login_date']=='0000-00-00 00:00:00') { $axRow['last_login_date']='-'; } 
		else { $axRow['last_login_date']=DateOnly($axRow['last_login_date']); }



		# VIEW

			# DATA

			$sql_get_title = 'SELECT name FROM mi_master WHERE type="name_title_type" 
								AND value="'.$axRow['name_title_type'].'"';
			$title_name = $oDB->QueryOne($sql_get_title);

			if ($title_name == '') { $title_name = '-';	}
			if ($axRow['firstname'] == '') { $axRow['firstname'] = '-';	}
			if ($axRow['lastname'] == '') { $axRow['lastname'] = '-';	}
			if ($axRow['nickname'] == '') { $axRow['nickname'] = '-';	}
			if ($axRow['gender'] == '') { $axRow['gender'] = '-';	}
			if ($axRow['mobile'] == '') { $axRow['mobile'] = '-';	}
			if ($axRow['nationality'] == '') { $axRow['nationality'] = '-';	}
			if ($axRow['idcard_no'] == '') { $axRow['idcard_no'] = '-';	}
			if ($axRow['passport_no'] == '') { $axRow['passport_no'] = '-';	}

				
		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['id_user'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>

				<div class="modal fade" id="View'.$axRow['id_user'].'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px">
						        	<b>'.$axRow['username'].'
						        		<span style="float:right">'.$axRow['brand_name'].'</span>
						        	</b>
						        </span>
						        <hr>
						        <center>
						        	<table width="80%" class="myPopup"><tr>
						        		<td width="30%" style="text-align:center">'.$logo_image.'</td>
						        		<td style="text-align:right">
						        			User Type<br>
						        			Role<br>
						        			Username<br>
						        			Email<br>
						        			Expire<br>
						        			Last Login</td>
						        		<td width="5%" style="text-align:center">
						        			:<br>
						        			:<br>
						        			:<br>
						        			:<br>
						        			:<br>
						        			:</td>
						        		<td width="40%">
						        			'.$axRow['user_type'].'<br>
						        			'.$axRow['role_name'].'<br>
						        			'.$axRow['username'].'<br>
						        			'.$axRow['email'].'<br>
						        			'.$expried.'<br>
						        			'.$axRow['last_login_date'].'</td>
						        	</tr></table><br>
						        	<span style="font-size:12px">
					                <ul id="tapMenu" class="nav nav-tabs">
					                    <li class="active" style="width:100%">
					                    	<a data-toggle="tab" href="#profile'.$axRow['id_user'].'">
					                    	<center><b>Profile</b></center></a>
					                    </li>
					                </ul>
					                </span>
					                <div class="tab-content">
					                    <div id="profile'.$axRow['id_user'].'" class="tab-pane active">
								        	<table width="80%" class="myPopup">
								        		<tr>
								        			<td style="text-align:right" width="45%">Titlename</td>
								        			<td style="text-align:center" width="5%">:</td>
								        			<td>'.$title_name.'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Firstname</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['firstname'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Lastname</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['lastname'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Nickname</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['nickname'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Gender</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['gender'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Mobile</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['mobile'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Nationality</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['nationality'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">ID Card No.</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['idcard_no'].'</td>
								        		</tr>
								        		<tr>
								        			<td style="text-align:right">Passport No.</td>
								        			<td style="text-align:center">:</td>
								        			<td>'.$axRow['passport_no'].'</td>
								        		</tr>
								        	</table>
					                    </div>
					                </div>
						        </center>
						    </div>';

		if ($_SESSION['role_action']['user']['edit'] == 1) {

			$view .= '	    <div class="modal-footer">
						    <a href="user_create.php?act=edit&id='.$axRow['id_user'].'">
						        <button type="button" class="btn btn-default btn-sm">Edit</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>';
		}

		$view .='		</div>
					</div>
				</div>';


		# DELETED

		if($axRow['status_del']==0) {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['id_user'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>

				<div class="modal fade" id="Deleted'.$axRow['id_user'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b><span>
						        <hr>
						        <center>
						        <table width="70%">
						        	<tr>
						        	<td width="120px" style="text-align:center" valign="top">'.$logo_image.'</td>
						        	<td>
								        <p style="font-size:14px;padding-left:10px;">
								        	<b>"'.$axRow['username'].'"</b><br>
								            By clicking the <b>"Inactive"</b> button to:<br>
								            &nbsp; &nbsp;- Inactive this user
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="user.php?act=delete&id='.$axRow['id_user'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		
		} else if ($axRow['status_del']==1) {
				
			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['id_user'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>

				<div class="modal fade" id="Deleted'.$axRow['id_user'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b><span>
						        <hr>
						        <center>
						        <table width="70%">
						        	<tr>
						        	<td width="120px" style="text-align:center">'.$logo_image.'</td>
						        	<td>
								        <p style="font-size:14px;padding-left:10px;">
								        	<b>"'.$axRow['username'].'"</b><br>
								           	By clicking the <b>"Active"</b> button to:<br>
								            &nbsp; &nbsp;- Active this user<br>
								            &nbsp; &nbsp;- Change status to Pending
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="user.php?act=delete&id='.$axRow['id_user'].'">
						        <button type="button" class="btn btn-default btn-sm">Active</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		}



		$data_table .= '<tr >

							<td >'.$i.'</td>

							<td style="text-align:center">'.$logo_brand.'<br>
								<span style="font-size:11px;">'.$axRow['brand_name'].'</span></td>

							<td >'.$axRow['user_type'].'</td>

							<td >'.$axRow['role_name'].'</td>

							<td >'.$axRow['username'].'</td>

							<td >'.$axRow['email'].'</td>

							<td >'.$expried.'</td>

							<td >'.$status.'</td>

							<td >'.DateTime($axRow['date_update']).'</td>';

		if ($_SESSION['role_action']['user']['view'] == 1) {

			$data_table .=	'<td style="text-align:center">'.$view.'</td>';
		}

		if ($_SESSION['role_action']['user']['delete'] == 1) {

			$data_table .= 	'<td style="text-align:center">'.$deleted.'</td>';
		}
						
		$data_table .=	'</tr>';
	}

	$asData[] = $axRow;


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



#  type_status dropdownlist

$sql_type ='SELECT user_type_id, name FROM mi_user_type ORDER BY user_type_id';
$oRes_type = $oDB->Query($sql_type);

$select_type = '';
$selected = "";

if ($type_status==0) {	$selected = "selected";	}
else {	$selected = "";	}

$select_type .= '<option value="0" '.$selected.'>All</option>';

$selected = "";

while ($axRow = $oRes_type->FetchRow(DBI_ASSOC)){

	if ($axRow['user_type_id']==$type_status) {	$selected = "selected";	}

	$select_type .= '<option value="'.$axRow['user_type_id'].'" '.$selected.'>'.$axRow['name'].'</option>';

	$selected = "";
}

$oTmp->assign('select_type', $select_type);






$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_user');

$oTmp->assign('content_file', 'user/user.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());

}

//========================================//

?>

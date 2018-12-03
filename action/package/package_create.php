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

if ($_SESSION['role_action']['package']['add'] != 1 || $_SESSION['role_action']['package']['edit'] != 1) {

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

# SEARCH MAX PACKAGE MASTER ID

	$sql_get_last_ins = 'SELECT max(pama_PackageMasterID) FROM package_master';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$master_id_new = $id_last_ins+1;

#######################################

# SEARCH MAX PACKAGE FUNCTION ID

	$sql_get_last_ins = 'SELECT max(pafu_PackageFunctionID) FROM package_function';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$function_id_new = $id_last_ins+1;

#######################################

# DATA FUNCTION

	$sql_master = 'SELECT DISTINCT function.mema_MenuMasterID, 
					menu_master.mema_Name
					FROM function
					LEFT JOIN menu_master
					ON menu_master.mema_MenuMasterID = function.mema_MenuMasterID';

	$oRes_master = $oDB->Query($sql_master);
	$data_function = '';

	while ($axRow_master = $oRes_master->FetchRow(DBI_ASSOC)){

		$data_function .= '<tr bgcolor="#CCCCCC">
							<td><button type="button" class="btn btn-default btn-sm" id="'.$axRow_master['mema_MenuMasterID'].'" onclick="check_all(this.id)">
									<span class="glyphicon glyphicon-check" aria-hidden="true"></span>
								</button>
								<button type="button" class="btn btn-default btn-sm" id="'.$axRow_master['mema_MenuMasterID'].'" onclick="uncheck_all(this.id)">
									<span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>
								</button>
							</td>
							<td colspan="2"><b>'.$axRow_master["mema_Name"].'</b></td></tr>';

		$sql_function = 'SELECT * FROM function WHERE mema_MenuMasterID='.$axRow_master["mema_MenuMasterID"].' AND func_Deleted!="T"';

		$oRes_function = $oDB->Query($sql_function);

		while ($axRow = $oRes_function->FetchRow(DBI_ASSOC)){

			$data_function .= '<tr>
								<td><input type="checkbox" class="'.$axRow_master['mema_MenuMasterID'].'" name="function_'.$axRow['func_FunctionID'].'" value="'.$axRow['func_FunctionID'].'"></td>
								<td>'.$axRow['func_Name'].'</td>
								<td align="left"> &nbsp; &nbsp;'.$axRow['func_Description'].'</td>
							</tr>';
		}
	}

#######################################

if( $Act == 'edit' && $id != '' ){

	# EDIT

	$sql = 'SELECT package_master.*,
				package_function.*
			FROM package_master
			LEFT JOIN package_function
			ON package_function.pama_PackageMasterID = package_master.pama_PackageMasterID
			WHERE package_master.pama_PackageMasterID ='.$id;

	$oRes = $oDB->Query($sql);
	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$asData = $axRow;
	}

	$oRes_master = $oDB->Query($sql_master);

	$data_function = '';

	while ($axRow_master = $oRes_master->FetchRow(DBI_ASSOC)){

		$data_function .= '<tr bgcolor="#CCCCCC">
							<td><button type="button" class="btn btn-default btn-sm" id="'.$axRow_master['mema_MenuMasterID'].'" onclick="check_all(this.id)">
								<span class="glyphicon glyphicon-check" aria-hidden="true"></span>
							</button>
							<button type="button" class="btn btn-default btn-sm" id="'.$axRow_master['mema_MenuMasterID'].'" onclick="uncheck_all(this.id)">
								<span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>
							</button>
							</td>
							<td colspan="2"><b>'.$axRow_master["mema_Name"].'</b></td></tr>';

		$sql_function = 'SELECT func_Name,
							func_Description,
							func_FunctionID
							FROM function
							WHERE mema_MenuMasterID='.$axRow_master["mema_MenuMasterID"].' 
							AND func_Deleted!="T"';

		// $sql_function = 'SELECT package_function.pafu_Deleted,
		// 					package_function.func_FunctionID,
		// 					function.func_Name,
		// 					function.func_Description
		// 					FROM package_function
		// 					LEFT JOIN function
		// 					ON package_function.func_FunctionID = function.func_FunctionID
		// 					WHERE function.mema_MenuMasterID='.$axRow_master["mema_MenuMasterID"].' 
		// 					AND package_function.pama_PackageMasterID='.$id.'
		// 					AND function.func_Deleted!="T"';

		$oRes_function = $oDB->Query($sql_function);

		while ($axRow = $oRes_function->FetchRow(DBI_ASSOC)){

			$sql_check = 'SELECT pafu_Deleted, pafu_PackageFunctionID
								FROM package_function
								WHERE func_FunctionID='.$axRow['func_FunctionID'].' 
								AND pama_PackageMasterID='.$id.'';
			$oRes_check = $oDB->Query($sql_check);
			$check = $oRes_check->FetchRow(DBI_ASSOC);

			if ($check['pafu_Deleted'] != 'T' && $check['pafu_PackageFunctionID']) {	$checked = 'checked';	}
			else {	$checked = '';	}

			$data_function .= '<tr>
									<td><input type="checkbox" class="'.$axRow_master['mema_MenuMasterID'].'" name="function_'.$axRow['func_FunctionID'].'" value="'.$axRow['func_FunctionID'].'" '.$checked.'></td>
									<td>'.$axRow['func_Name'].'</td>
									<td align="left"> &nbsp; &nbsp;'.$axRow['func_Description'].'</td>
								</tr>';
		}
	}

} else if( $Act == 'save' ){

	# PACKAGE MASTER

	$sql_master = '';

	$table_master = 'package_master';

	$pama_Name = trim_txt($_REQUEST['pama_Name']);
	$pama_Status = trim_txt($_REQUEST['pama_Status']);
	$pama_RegisterPrice = trim_txt($_REQUEST['pama_RegisterPrice']);
	$pama_MaxMember = trim_txt($_REQUEST['pama_MaxMember']);
	$pama_PaymentCharge = trim_txt($_REQUEST['pama_PaymentCharge']);
	$pama_MaxUser = trim_txt($_REQUEST['pama_MaxUser']);
	$pama_BrandProfile = trim_txt($_REQUEST['pama_BrandProfile']);
	$pama_MultiCard = trim_txt($_REQUEST['pama_MultiCard']);
	$pama_CardRegister = trim_txt($_REQUEST['pama_CardRegister']);
	$pama_RegisterForm = trim_txt($_REQUEST['pama_RegisterForm']);
	$pama_AddCustomField = trim_txt($_REQUEST['pama_AddCustomField']);
	$pama_MotivationRedeem = trim_txt($_REQUEST['pama_MotivationRedeem']);
	$pama_MaintainacePrice = trim_txt($_REQUEST['pama_MaintainacePrice']);
	$pama_Price = trim_txt($_REQUEST['pama_Price']);

	if($pama_Name){	$sql_master .= 'pama_Name="'.$pama_Name.'"';   }
	if($pama_Status){	$sql_master .= ',pama_Status="'.$pama_Status.'"';   }
	if($pama_RegisterPrice){	$sql_master .= ',pama_RegisterPrice="'.$pama_RegisterPrice.'"';   }
	if($pama_MaxMember){	$sql_master .= ',pama_MaxMember="'.$pama_MaxMember.'"';   }
	if($pama_PaymentCharge){	$sql_master .= ',pama_PaymentCharge="'.$pama_PaymentCharge.'"';   }
	if($pama_MaxUser){	$sql_master .= ',pama_MaxUser="'.$pama_MaxUser.'"';   }
	if($pama_BrandProfile){	$sql_master .= ',pama_BrandProfile="'.$pama_BrandProfile.'"';   }
	if($pama_MultiCard){	$sql_master .= ',pama_MultiCard="'.$pama_MultiCard.'"';   }
	if($pama_CardRegister){	$sql_master .= ',pama_CardRegister="'.$pama_CardRegister.'"';   }
	if($pama_RegisterForm){	$sql_master .= ',pama_RegisterForm="'.$pama_RegisterForm.'"';   }
	if($pama_AddCustomField){	$sql_master .= ',pama_AddCustomField="'.$pama_AddCustomField.'"';   }
	if($pama_MotivationRedeem){	$sql_master .= ',pama_MotivationRedeem="'.$pama_MotivationRedeem.'"';   }
	if($pama_MaintainacePrice){	$sql_master .= ',pama_MaintainacePrice="'.$pama_MaintainacePrice.'"';   }
	if($pama_Price){	$sql_master .= ',pama_Price="'.$pama_Price.'"';   }
	if($time_insert){	$sql_master .= ',pama_UpdatedDate="'.$time_insert.'"';   }
	if($_SESSION['UID']){	$sql_master .= ',pama_UpdatedBy="'.$_SESSION['UID'].'"';   }

	# PACKAGE FUNCTION

	$sql_function = '';

	$table_function = 'package_function';

	$function_data = 'SELECT func_FunctionID FROM function WHERE func_Deleted!="T"';

	$oRes_data = $oDB->Query($function_data);

	while ($axRow_f = $oRes_data->FetchRow(DBI_ASSOC)){

		$sql_function = '';

		if (trim_txt($_REQUEST['function_'.$axRow_f['func_FunctionID']]) == $axRow_f['func_FunctionID']) {

			if($time_insert){	$sql_function .= 'pafu_UpdatedDate="'.$time_insert.'"';   }

			if($_SESSION['UID']){	$sql_function .= ',pafu_UpdatedBy="'.$_SESSION['UID'].'"';   }

			$sql_function .= ',pafu_Deleted=""';

		} else {

			if($time_insert){	$sql_function .= 'pafu_UpdatedDate="'.$time_insert.'"';   }

			if($_SESSION['UID']){	$sql_function .= ',pafu_UpdatedBy="'.$_SESSION['UID'].'"';   }

			$sql_function .= ',pafu_Deleted="T"';
		}

		if ($id) {

			# CHECK ID

			$sql_check = 'SELECT pafu_PackageFunctionID 
							FROM package_function
							WHERE pama_PackageMasterID="'.$id.'"
							AND func_FunctionID="'.$axRow_f['func_FunctionID'].'"';
			$package_function_id = $oDB->QueryOne($sql_check);

			if ($package_function_id) {

				# UPDATE 

				$do_sql_function = "UPDATE ".$table_function." SET ".$sql_function." WHERE pafu_PackageFunctionID=".$package_function_id;

				$oDB->QueryOne($do_sql_function);

			} else {

				# INSERT

				if ($function_id_new) {	$sql_function .= ',pafu_PackageFunctionID="'.$function_id_new.'"';	}

				if ($master_id_new) {	$sql_function .= ',pama_PackageMasterID="'.$id.'"';	}

				if ($axRow_f['func_FunctionID']) {	$sql_function .= ',func_FunctionID="'.$axRow_f['func_FunctionID'].'"';	}

				if($time_insert){	$sql_function .= ',pafu_CreatedDate="'.$time_insert.'"';   }

				if($_SESSION['UID']){	$sql_function .= ',pafu_CreatedBy="'.$_SESSION['UID'].'"';   }

				$do_sql_function = 'INSERT INTO '.$table_function.' SET '.$sql_function;

				$oDB->QueryOne($do_sql_function);

				$function_id_new++;
			}

		} else {

			# INSERT

			if ($function_id_new) {	$sql_function .= ',pafu_PackageFunctionID="'.$function_id_new.'"';	}

			if ($master_id_new) {	$sql_function .= ',pama_PackageMasterID="'.$master_id_new.'"';	}

			if ($axRow_f['func_FunctionID']) {	$sql_function .= ',func_FunctionID="'.$axRow_f['func_FunctionID'].'"';	}

			if($time_insert){	$sql_function .= ',pafu_CreatedDate="'.$time_insert.'"';   }

			if($_SESSION['UID']){	$sql_function .= ',pafu_CreatedBy="'.$_SESSION['UID'].'"';   }

			$do_sql_function = 'INSERT INTO '.$table_function.' SET '.$sql_function;

			$oDB->QueryOne($do_sql_function);

			$function_id_new++;
		}
	}

	# PACKAGE MASTER

	if($id){

		# UPDATE

		$do_sql_master = 'UPDATE '.$table_master.' SET '.$sql_master.' WHERE pama_PackageMasterID="'.$id.'"';

	} else {

		# INSERT

		if($time_insert){	$sql_master .= ',pama_CreatedDate="'.$time_insert.'"';   }

		if($_SESSION['UID']){	$sql_master .= ',pama_CreatedBy="'.$_SESSION['UID'].'"';   }

		if($master_id_new){	$sql_master .= ',pama_PackageMasterID="'.$master_id_new.'"';   }

		$do_sql_master = 'INSERT INTO '.$table_master.' SET '.$sql_master;
	}

	$oDB->QueryOne($do_sql_master);	

	echo '<script>window.location.href="package.php";</script>';

	exit;
}



$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('data_function', $data_function);

$oTmp->assign('is_menu', 'is_package');

$oTmp->assign('content_file', 'package/package_create.html');

$oTmp->display('layout/template.html');


//========================================//


$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}


//========================================//

?>
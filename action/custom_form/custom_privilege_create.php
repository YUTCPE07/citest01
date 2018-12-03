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

if (($_SESSION['role_action']['custom_privilege']['add'] != 1) || ($_SESSION['role_action']['custom_privilege']['edit'] != 1)) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];



# SEARCH MAX ID

	$sql_get_last_ins = 'SELECT max(cufi_CustomFieldID) FROM custom_field';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################


if( $Act == 'edit' && $id != '' ){

	# edit page

	$sql = 'SELECT * FROM custom_field WHERE cufi_CustomFieldID = "'.$id.'"';

	$oRes = $oDB->Query($sql);

	$i=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$asData = $axRow;
	}

	$data_list = "";

	$i = 1;

	$sql_list = 'SELECT * FROM custom_list_value WHERE cufi_CustomFieldID = "'.$id.'" AND clva_Deleted!="T"';

	$oRes_list = $oDB->Query($sql_list);

	while ($axRow = $oRes_list->FetchRow(DBI_ASSOC)){

		$data_list .= '<div class="adj_row" id="'.$i.'">
	                    <label class="lable-form">'.$i.'.</label> 
	                    <span class="form-inline">
	                    	<b>TH </b><input type="text" name="form[]" class="form-control text-md" style="width:200px" placeholder="Text" value="'.$axRow['clva_Name'].'">
	                    	&nbsp; &nbsp; &nbsp;
	                    	<b>EN </b><input type="text" name="formEn[]" class="form-control text-md" style="width:200px" placeholder="Text" value="'.$axRow['clva_NameEn'].'">
	                    </span>
	                </div>';
           $i++;
	}

	$data_list .= '<div id="new_gallery"></div>
					<div class="adj_row">
                   		<label class="lable-form"></label> 
                    	<button type="button" class="btn btn-default btn-sm" id="add_option">
                        	<span class="glyphicon glyphicon-plus" aria-hidden="true"></span><b> &nbsp; Add Option</b>
                    	</button>
                    	&nbsp; &nbsp; &nbsp;
                    	<button type="button" class="btn btn-default btn-sm" id="remove_option">
                        	<span class="glyphicon glyphicon-minus" aria-hidden="true"></span><b> &nbsp; Remove Option</b>
                    	</button>
                	</div>';

	} else if( $Act == 'save' ){

		$do_sql_card = "";

		$bran_BrandID = trim_txt($_REQUEST['bran_BrandID']);

		$cufi_Name = trim_txt($_REQUEST['cufi_Name']);

		$cufi_NameEn = trim_txt($_REQUEST['cufi_NameEn']);

		$fity_FieldTypeID = trim_txt($_REQUEST['fity_FieldTypeID']);



		$time_insert = date("Y-m-d H:i:s");



		$sql_custom_field = '';

		$table_custom_field = 'custom_field';



		# Action with custom_field table

		if($cufi_Name){	$sql_custom_field .= 'cufi_Name="'.$cufi_Name.'"';   }

		if($cufi_NameEn){	$sql_custom_field .= ',cufi_NameEn="'.$cufi_NameEn.'"';   }

		if($fity_FieldTypeID){	$sql_custom_field .= ',fity_FieldTypeID="'.$fity_FieldTypeID.'"';   }

		if($time_insert){	$sql_custom_field .= ',cufi_UpdatedDate="'.$time_insert.'"';   }

		if($_SESSION['UID']){	$sql_custom_field .= ',cufi_UpdatedBy="'.$_SESSION['UID'].'"';   }

		$sql_custom_field .= ',cufi_Type="Privilege"';   


		if ($id) {

			$do_sql_custom =  "UPDATE ".$table_custom_field." SET ".$sql_custom_field." WHERE cufi_CustomFieldID=".$id."";

		} else {

			# SEARCH FIELD ORDER

				$sql_get_value_id = 'SELECT max(cufi_FieldOrder) FROM custom_field WHERE bran_BrandID="'.$bran_BrandID.'"';
				$order_id = $oDB->QueryOne($sql_get_value_id);
				$order_id++;

			#######################################

			if($bran_BrandID){	$sql_custom_field .= ',bran_BrandID="'.$bran_BrandID.'"';   }

			if($time_insert){	$sql_custom_field .= ',cufi_CreatedDate="'.$time_insert.'"';   }

			if($_SESSION['UID']){	$sql_custom_field .= ',cufi_CreatedBy="'.$_SESSION['UID'].'"';   }

			$sql_custom_field .= ',cufi_FieldOrder="'.$order_id.'"';

			$sql_custom_field .= ',cufi_FieldName="cufi_'.$id_new.'"';

			$sql_custom_field .= ',cufi_CustomFieldID="'.$id_new.'"';

			$do_sql_custom = 'INSERT INTO '.$table_custom_field.' SET '.$sql_custom_field;
		}

		$oDB->QueryOne($do_sql_custom);



		## CUSTOM LIST ##

		$sql_custom_list = '';

		$table_custom_list = 'custom_list_value';



		# Action with custom_list_value table

		if ($id) {

			$x = 1;

			$e = "";

			for($i=0;$i<count($_POST["form"]);$i++) {

				if(trim($_POST["form"][$i]) != "" || trim($_POST["formEn"][$i]) != ""){

					# SEARCH VALUE CUSTOM LIST VALUE

						$sql_get_value_id = 'SELECT clva_CustomListValueID FROM custom_list_value WHERE clva_Value='.($i+1).' AND cufi_CustomFieldID='.$id;

						$value_id = $oDB->QueryOne($sql_get_value_id);

					#######################################


					if($_POST["form"][$i]){	$sql_custom_list = 'clva_Name="'.$_POST["form"][$i].'"';   }
					else {	$sql_custom_list = 'clva_Name="'.$_POST["formEn"][$i].'"';	}

					if($_POST["formEn"][$i]){	$sql_custom_list .= ',clva_NameEn="'.$_POST["formEn"][$i].'"';   }

					if($time_insert){	$sql_custom_list .= ',clva_UpdatedDate="'.$time_insert.'"';   }

					if($_SESSION['UID']){	$sql_custom_list .= ',clva_UpdatedBy="'.$_SESSION['UID'].'"';   }

					$sql_custom_list .= ',clva_Deleted=""';


					if ($value_id) {	# UPDATE CUSTOM LIST VALUE #

						$do_sql_list = "UPDATE ".$table_custom_list." SET ".$sql_custom_list." WHERE clva_CustomListValueID=".$value_id."";

					} else {			# INSERT CUSTOM LIST VALUE #

						# SEARCH MAX ID

							$sql_get_last_ins = 'SELECT max(clva_CustomListValueID) FROM custom_list_value';
							$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
							$id_list = $id_last_ins+1;

						#######################################

						$sql_custom_list .= ',cufi_CustomFieldID="'.$id.'"';

						$sql_custom_list .= ',clva_Value="'.$x.'"';

						$sql_custom_list .= ',clva_CustomListValueID="'.$id_list.'"';

						if($time_insert){	$sql_custom_list .= ',clva_CreatedDate="'.$time_insert.'"';   }

						if($_SESSION['UID']){	$sql_custom_list .= ',clva_CreatedBy="'.$_SESSION['UID'].'"';   }

						$do_sql_list = 'INSERT INTO '.$table_custom_list.' SET '.$sql_custom_list;
					}

					$oDB->QueryOne($do_sql_list);

					$x++;
				}
			}


			$do_deleted = "UPDATE ".$table_custom_list." SET clva_Deleted='T' WHERE cufi_CustomFieldID=".$custom_id." AND clva_Value>".($x-1);

			$oDB->QueryOne($do_deleted);

		} else {

			$x = 1;

			for($i=0;$i<count($_POST["form"]);$i++) {

				if(trim($_POST["form"][$i]) != ""){

					# SEARCH MAX ID

						$sql_get_last_ins = 'SELECT max(clva_CustomListValueID) FROM custom_list_value';
						$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
						$id_list = $id_last_ins+1;

					#######################################

					$sql_custom_list = 'cufi_CustomFieldID="'.$id_new.'"';

					if($_POST["form"][$i]){	$sql_custom_list .= ',clva_Name="'.$_POST["form"][$i].'"';   }

					if($_POST["formEn"][$i]){	$sql_custom_list .= ',clva_NameEn="'.$_POST["formEn"][$i].'"';   }

					$sql_custom_list .= ',clva_Value="'.$x.'"';

					$sql_custom_list .= ',clva_CustomListValueID="'.$id_list.'"';

					if($time_insert){	$sql_custom_list .= ',clva_UpdatedDate="'.$time_insert.'"';   }

					if($_SESSION['UID']){	$sql_custom_list .= ',clva_UpdatedBy="'.$_SESSION['UID'].'"';   }

					if($time_insert){	$sql_custom_list .= ',clva_CreatedDate="'.$time_insert.'"';   }

					if($_SESSION['UID']){	$sql_custom_list .= ',clva_CreatedBy="'.$_SESSION['UID'].'"';   }


					$do_sql_list = 'INSERT INTO '.$table_custom_list.' SET '.$sql_custom_list;

					$oDB->QueryOne($do_sql_list);

					$x++;
				}
			}
		}

		echo '<script>window.location.href="custom_privilege.php";</script>';

		exit;
	}





#  brand dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' and brand_id="'.$_SESSION['user_brand_id'].'" ';
}

$as_brand_id = dropdownlist_from_table($oDB,'mi_brand','brand_id','name','brand_id>0'.$where_brand,' ORDER BY name ASC');

$oTmp->assign('brand_id_opt', $as_brand_id);



#  field_type dropdownlist

$as_field_type = dropdownlist_from_table($oDB,'field_type','fity_FieldTypeID','fity_Name');

$oTmp->assign('field_type', $as_field_type);



$oTmp->assign('data', $asData);

$oTmp->assign('data_list', $data_list);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_custom_privilege');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_privilege', 'in');

$oTmp->assign('content_file', 'custom_form/custom_privilege_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
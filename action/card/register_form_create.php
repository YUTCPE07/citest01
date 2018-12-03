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

if (($_SESSION['role_action']['register_form']['add'] != 1) || ($_SESSION['role_action']['register_form']['edit'] != 1)) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$time_insert = date("Y-m-d H:i:s");
$Act = $_REQUEST['act'];
$id = $_REQUEST['id'];
$path_upload_card = $_SESSION['path_upload_card'];


# SEARCH MAX REGISTER FORM ID

	$sql_get_last_ins = 'SELECT max(refo_RegisterFormID) FROM register_form';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################

# SEARCH BRAND ID

	$sql_get_last_ins = 'SELECT brand_id FROM mi_card WHERE card_id='.$id;
	$id_brand = $oDB->QueryOne($sql_get_last_ins);

#######################################

# SEARCH MEMBER REGISTER

	$sql_get_member = 'SELECT member_register_id FROM mb_member_register WHERE card_id='.$id;
	$member_regis = $oDB->QueryOne($sql_get_member);

#######################################

# MEMBER FEE CARD

	$sql_get_amount = 'SELECT member_fee FROM mi_card WHERE card_id='.$id;
	$card_amount = $oDB->QueryOne($sql_get_amount);

#######################################


if( $Act == 'edit' && $id != '' ){

	# EDIT

	$sql_card = '';

	$sql_card = 'SELECT DISTINCT mi_card.*,
					mi_brand.name AS brand_name,
					mi_card_type.name AS card_type_name
					FROM mi_card
					LEFT JOIN mi_brand
					ON mi_brand.brand_id = mi_card.brand_id
					LEFT JOIN mi_card_type
					ON mi_card_type.card_type_id = mi_card.card_type_id
					WHERE mi_card.card_id = "'.$id.'"';

	$oRes = $oDB->Query($sql_card)or die(mysql_error());

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$axRow['date_expired'] = DateOnly($axRow['date_expired']);

		if ($axRow['period_type']==2) { $axRow['period_type_other'] = $axRow['period_type_other'].' Months'; }
		if ($axRow['period_type']==3) { $axRow['period_type_other'] = $axRow['period_type_other'].' Years'; }
		if ($axRow['period_type']==4) { $axRow['period_type_other'] = 'Member Life Time'; }

		if ($axRow['description']=="" || !$axRow['description']) { $axRow['description']="-"; }
		else { $axRow['description'] = nl2br($axRow['description']); }

		$axRow['member_fee'] = number_format($axRow['member_fee'],2).' ฿';

		$asData = $axRow;
	}


	# REGISTER FORM

	$topic = array("Profile", "Home Address", "Work Address", "Work", "Contact");

	$data_table = '';

	$data_basic = '';

	$data_function = '<script type="text/javascript">';

	for ($i=0; $i <5 ; $i++) { 

		# COUNT FIELD

		$field = 'SELECT COUNT(mafi_MasterFieldID) as total FROM master_field WHERE mafi_Position="'.$topic[$i].'"';
		$result = mysql_query($field);
		$count_field = mysql_fetch_assoc($result);

		$sql_1 = 'SELECT a.*,b.*,c.*,
						a.mafi_MasterFieldID AS master_field_id,
						b.refo_Target

						FROM master_field AS a

						LEFT JOIN register_form AS b
						ON b.mafi_MasterFieldID = a.mafi_MasterFieldID

						LEFT JOIN mi_card AS c
						ON b.card_CardID = c.card_id

						WHERE a.mafi_Position = "'.$topic[$i].'"
						AND a.mafi_Deleted != "T"
						AND c.card_id = "'.$id.'"
						GROUP BY a.mafi_FieldName
						ORDER BY a.mafi_FieldOrder';

		$oRes_1 = $oDB->Query($sql_1);

		$asData_1 = array();

		$sql_2 = 'SELECT
					d.mafi_MasterFieldID AS d_target

					FROM master_target AS d

					WHERE d.mata_Target = "T"
					GROUP BY mafi_MasterFieldID';

		$data_table .= '<tr bgcolor="#CCCCCC">
							<td style="text-align:center"><b>'.$topic[$i].'</b></td>
							<td style="text-align:center"><button type="button" class="btn btn-default btn-sm" id="'.$i.'" onclick="all_fill'.$i.'(this.id)">
									<span class="glyphicon glyphicon-check" aria-hidden="true"></span>
								</button>
								<button type="button" class="btn btn-default btn-sm" id="'.$i.'" onclick="unall_fill'.$i.'(this.id)">
									<span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>
								</button>
							</td>
							<td style="text-align:center"><button type="button" class="btn btn-default btn-sm" id="'.$i.'" onclick="all_must'.$i.'(this.id)">
									<span class="glyphicon glyphicon-check" aria-hidden="true"></span>
								</button>
								<button type="button" class="btn btn-default btn-sm" id="'.$i.'" onclick="unall_must'.$i.'(this.id)">
									<span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>
								</button>
							</td>
							<td style="text-align:center"><button type="button" class="btn btn-default btn-sm" id="'.$i.'" onclick="all_hide'.$i.'(this.id)">
									<span class="glyphicon glyphicon-check" aria-hidden="true"></span>
								</button>
								<button type="button" class="btn btn-default btn-sm" id="'.$i.'" onclick="unall_hide'.$i.'(this.id)">
									<span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>
								</button>
							</td>
							<td colspan="2"></td>
						</tr>';

			while ($axRow_1 = $oRes_1->FetchRow(DBI_ASSOC)){

				# DATA BASIC

                if ($axRow_1['mafi_MasterFieldID']==2 || $axRow_1['mafi_MasterFieldID']==3 || $axRow_1['mafi_MasterFieldID']==5 || $axRow_1['mafi_MasterFieldID']==6 || $axRow_1['mafi_MasterFieldID']==20 || $axRow_1['mafi_MasterFieldID']==23 || $axRow_1['mafi_MasterFieldID']==48 || $axRow_1['mafi_MasterFieldID']==49) {

		            if ($axRow_1['refo_FillIn']=='Y') { $check_f = ' checked="checked"'; }
		            else { $check_f = ''; }
		            if ($axRow_1['refo_Require']=='Y') { $check_r = ' checked="checked"'; }
		            else { $check_r = ''; }
		            if ($axRow_1['refo_Hidden']=='Y') { $check_h = ' checked="checked"'; }
		            else { $check_h = ''; }

					$data_basic .= '<tr>
	                                <td style="text-align:center"><b>'.$axRow_1['mafi_NameEn'].'</b></td>
	                                <td style="text-align:center"><input type="checkbox" value="1" name="b'.$axRow_1['mafi_FieldName'].'f" id="f'.$axRow_1['mafi_FieldName'].'" '.$check_f.' onchange="type_'.$axRow_1['mafi_FieldName'].'f();"></td>
	                                <td style="text-align:center"><input type="checkbox" value="1" name="b'.$axRow_1['mafi_FieldName'].'m" id="m'.$axRow_1['mafi_FieldName'].'" '.$check_r.' onchange="type_'.$axRow_1['mafi_FieldName'].'m();"></td>
	                                <td style="text-align:center"><input type="checkbox" value="1" name="b'.$axRow_1['mafi_FieldName'].'h" id="h'.$axRow_1['mafi_FieldName'].'" '.$check_h.'></td>';
	            }

				# DATA TABLE

				$data_table .= '<tr>
                                <td style="text-align:center"><b>'.$axRow_1['mafi_NameEn'].'</b></td>
                                <td style="text-align:center"><input type="checkbox" value="1" name="a'.$axRow_1['mafi_FieldName'].'f" id="f'.$axRow_1['mafi_FieldName'].'"';

                // if ($axRow_1['mafi_MasterFieldID']==2 || $axRow_1['mafi_MasterFieldID']==3 || $axRow_1['mafi_MasterFieldID']==5 || $axRow_1['mafi_MasterFieldID']==6) {

	            // $data_table .= ' checked="checked" disabled></td>';

                // } else {

	            if ($axRow_1['refo_FillIn']=='Y') { $data_table .= ' checked="checked"'; }

                $data_table .= ' onchange="type_'.$axRow_1['mafi_FieldName'].'f();" class="f'.$i.'"></td>';

                // }

            	$data_table .= '<td style="text-align:center"><input type="checkbox" value="1" name="a'.$axRow_1['mafi_FieldName'].'m" id="m'.$axRow_1['mafi_FieldName'].'"';

                // if ($axRow_1['mafi_MasterFieldID']==2 || $axRow_1['mafi_MasterFieldID']==3 || $axRow_1['mafi_MasterFieldID']==5 || $axRow_1['mafi_MasterFieldID']==6) {

	            // $data_table .= ' checked="checked" disabled></td>';

                // } else {

	                if ($axRow_1['refo_Require']=='Y') { $data_table .= ' checked="checked"'; }

					$data_table .= ' class="m'.$i.'" onchange="type_'.$axRow_1['mafi_FieldName'].'m();"></td>';

	            // }

            	$data_table .= '<td style="text-align:center"><input type="checkbox" value="1" name="a'.$axRow_1['mafi_FieldName'].'h" id="h'.$axRow_1['mafi_FieldName'].'"';

	                if ($axRow_1['refo_Hidden']=='Y') { $data_table .= ' checked="checked"'; }

					$data_table .= ' class="h'.$i.'"></td>';


            	$oRes_2 = $oDB->Query($sql_2);

				$asData_2 = array();

				$status = 0;

            	while ($axRow_2 = $oRes_2->FetchRow(DBI_ASSOC)) {

					if ($axRow_1['master_field_id'] == $axRow_2['d_target']) { # CHECK WHICH FIELD HAVE TARGET

						$status = 1;

						$data_function .= 'function type_'.$axRow_1['mafi_FieldName'].'f() {

								if (!f'.$axRow_1['mafi_FieldName'].'.checked) {

                					m'.$axRow_1['mafi_FieldName'].'.checked = false;
                					t'.$axRow_1['mafi_FieldName'].'.checked = false;
                					document.getElementById("'.$axRow_1['mafi_FieldName'].'_dp").style.display="none";
            					}}

								function type_'.$axRow_1['mafi_FieldName'].'m() {

            					if (m'.$axRow_1['mafi_FieldName'].'.checked) {              

                					f'.$axRow_1['mafi_FieldName'].'.checked = true;
            					}

            					if (!m'.$axRow_1['mafi_FieldName'].'.checked) {

                					t'.$axRow_1['mafi_FieldName'].'.checked = false;
                					document.getElementById("'.$axRow_1['mafi_FieldName'].'_dp").style.display="none";
            					}}

            					function type_'.$axRow_1['mafi_FieldName'].'t() {

            					if (t'.$axRow_1['mafi_FieldName'].'.checked) {

                					f'.$axRow_1['mafi_FieldName'].'.checked = true;
                					m'.$axRow_1['mafi_FieldName'].'.checked = true;
                					document.getElementById("'.$axRow_1['mafi_FieldName'].'_dp").style.display="";

            					} else {

                					document.getElementById("'.$axRow_1['mafi_FieldName'].'_dp").style.display="none";
            					}}';

            			# DATA BASIC

                		if ($axRow_1['mafi_MasterFieldID']==2 || $axRow_1['mafi_MasterFieldID']==3 || $axRow_1['mafi_MasterFieldID']==5 || $axRow_1['mafi_MasterFieldID']==6 || $axRow_1['mafi_MasterFieldID']==48 || $axRow_1['mafi_MasterFieldID']==49) {

							$data_function .= '

	            					function type_'.$axRow_1['mafi_FieldName'].'basic() {

	            					if (basic'.$axRow_1['mafi_FieldName'].'.checked) {

	                					document.getElementById("'.$axRow_1['mafi_FieldName'].'_basic").style.display="";

	            					} else {

	                					document.getElementById("'.$axRow_1['mafi_FieldName'].'_basic").style.display="none";
	            					}}';

							$data_basic .= '<td width="10%" style="text-align:center">

		                            <input class="test" type="checkbox" id="basic'.$axRow_1['mafi_FieldName'].'" name="b'.$axRow_1['mafi_FieldName'].'basic" value="1" onchange="type_'.$axRow_1['mafi_FieldName'].'basic();"';

		               		if ($axRow_1['refo_Target'] != 0) { $data_basic .= ' checked="checked"></td>'; }

		           			else { $data_basic .= '></td>'; }

	           				if ($axRow_2['d_target'] == 6) { # AGE

	           					$age_basic = $axRow_1['refo_Target'];

	           					$token = strtok($age_basic,",");

	     						$basic_age1 = $token;

	     						$token = strtok (",");

	     						$basic_age2 = $token;

								$data_basic .= '<td><div class="form-inline" id="date_birth_basic"';

								if ($axRow_1['refo_Target'] == "") {

	           						$data_basic .= ' style="display:none">';
	           					}

	           					else {

	           						$data_basic .= '>';
	           					}

	                            $data_basic .= '<select id="age_basic1" name="age_basic1" class="form-control" onchange="age_basic()";>';

	                            $query = "SELECT mata_MasterTargetID,mata_NameEn FROM master_target WHERE mafi_MasterFieldID='".$axRow_1['master_field_id']."'";

								$result = mysql_query($query) or die(mysql_error()."[".$query."]");

								while ($row = mysql_fetch_array($result)) {

									if ($row['mata_MasterTargetID']==$basic_age1) { $select='selected="selected"';}
									else{ $select='';}

	    							$data_basic .= "<option value=".$row['mata_MasterTargetID']." ".$select.">".$row['mata_NameEn']."</option>";
								}
	                                          
	                            $data_basic .= '</select>
	                            				&nbsp;-&nbsp;
	                                        	<span id="age_target_data" class="fontBlack">
	                                            <select id="age_basic2" name="age_basic2" class="form-control">';

	                            $query = "SELECT mata_MasterTargetID,mata_NameEn FROM master_target WHERE mafi_MasterFieldID='".$axRow_1['master_field_id']."'";

								$result = mysql_query($query) or die(mysql_error()."[".$query."]");

	                            while ($row = mysql_fetch_array($result)) {

									if ($row['mata_MasterTargetID']==$basic_age2) { $select='selected="selected"';}
									else{ $select='';}

	    							$data_basic .= "<option value=".$row['mata_MasterTargetID']." ".$select.">".$row['mata_NameEn']."</option>";
								}
	                                           
	                            $data_basic .= '</select>
	                                        	</span>&nbsp; Age Restriction</div></td>';

	           				} else { # TARGET

	            				$data_basic .= '<td><select class="form-control text-md" id="'.$axRow_1['mafi_FieldName'].'_basic" name="'.$axRow_1['mafi_FieldName'].'_basic"';

	    		       			if ($axRow_1['refo_Target'] == "") { $data_basic .= ' style="display:none">';
	           					} else { $data_basic .= '>'; }

	            				$query = "SELECT mata_MasterTargetID,mata_NameEn FROM master_target WHERE mafi_MasterFieldID='".$axRow_1['master_field_id']."'";

								$result = mysql_query($query) or die(mysql_error()."[".$query."]");

								while ($row = mysql_fetch_array($result)) {

									if ($row['mata_MasterTargetID']==$axRow_1['refo_Target']) { $select='selected="selected"';}

									else { $select='';}

	    							$data_basic .= "<option value=".$row['mata_MasterTargetID']." ".$select.">".$row['mata_NameEn']."</option>";
								}

	      						$data_basic .= '</select></td>';
							}
						}

            			# DATA TABLE

						$data_table .= '<td width="10%" style="text-align:center">

                            <input class="test" type="checkbox" id="t'.$axRow_1['mafi_FieldName'].'" name="a'.$axRow_1['mafi_FieldName'].'t" value="1" onchange="type_'.$axRow_1['mafi_FieldName'].'t();"';

               			if ($axRow_1['refo_Target'] != 0) {

           					$data_table .= ' checked="checked"></td>';

           				} else {

           					$data_table .= '></td>';
           				}

           				if ($axRow_2['d_target'] == 6) { # AGE

           					$data_age = $axRow_1['refo_Target'];

           					$token = strtok($data_age,",");

     						$data_age1 = $token;

     						$token = strtok (",");

     						$data_age2 = $token;

							$data_table .= '<td style="text-align:center"><div class="form-inline left" id="date_birth_dp"';

							if ($axRow_1['refo_Target'] == "") {

           						$data_table .= ' style="display:none">';

           					} else {

           						$data_table .= '>';
           					}

                            $data_table .= '<select id="age_dp1" name="age_dp1" 

                            				class="form-control" onchange="age_target()";>';

                            $query = "SELECT mata_MasterTargetID,mata_NameEn FROM master_target WHERE mafi_MasterFieldID='".$axRow_1['master_field_id']."'";

							$result = mysql_query($query) or die(mysql_error()."[".$query."]");

							while ($row = mysql_fetch_array($result)) {

								if ($row['mata_MasterTargetID']==$data_age1) { $select='selected="selected"';}
								else{ $select='';}

    							$data_table .= "<option value=".$row['mata_MasterTargetID']." ".$select.">".$row['mata_NameEn']."</option>";
							}
                                            
                            $data_table .= '</select>
                                        	&nbsp;-&nbsp;
                                        	<span id="age_target_dp" class="fontBlack">
                                            <select id="age_dp2" name="age_dp2" class="form-control">';

                            $query = "SELECT mata_MasterTargetID,mata_NameEn FROM master_target WHERE mafi_MasterFieldID='".$axRow_1['master_field_id']."'";

							$result = mysql_query($query) or die(mysql_error()."[".$query."]");

                            while ($row = mysql_fetch_array($result)) {

								if ($row['mata_MasterTargetID']==$data_age2) { $select='selected="selected"';}

								else{ $select='';}

    							$data_table .= "<option value=".$row['mata_MasterTargetID']." ".$select.">".$row['mata_NameEn']."</option>";
							}
        
                            $data_table .= '</select>

                                        	</span>&nbsp; Age Restriction</div></td>';

           				} else { # TARGET

            				$data_table .= '<td><select class="form-control text-md" id="'.$axRow_1['mafi_FieldName'].'_dp" name="'.$axRow_1['mafi_FieldName'].'_dp"';

    		       			if ($axRow_1['refo_Target'] == "") {

           						$data_table .= ' style="display:none">';

           					} else {

           						$data_table .= '>';
           					}

            				$query = "SELECT mata_MasterTargetID,mata_NameEn FROM master_target WHERE mafi_MasterFieldID='".$axRow_1['master_field_id']."'";

							$result = mysql_query($query) or die(mysql_error()."[".$query."]");

							while ($row = mysql_fetch_array($result)) {

								if ($row['mata_MasterTargetID']==$axRow_1['refo_Target']) { $select='selected="selected"';}

								else{ $select='';}

    							$data_table .= "<option value=".$row['mata_MasterTargetID']." ".$select.">".$row['mata_NameEn']."</option>";
							}

      						$data_table .= '</select></td>';
						}   
					} 
				}

				if ($axRow_1['master_field_id'] == 33) { # PROVINCE

					$status = 1;

					$data_function .= 'function type_'.$axRow_1['mafi_FieldName'].'f() {

								if (!f'.$axRow_1['mafi_FieldName'].'.checked) {

                					m'.$axRow_1['mafi_FieldName'].'.checked = false;
                					t'.$axRow_1['mafi_FieldName'].'.checked = false;
                					document.getElementById("'.$axRow_1['mafi_FieldName'].'_dp").style.display="none";
            					}}

								function type_'.$axRow_1['mafi_FieldName'].'m() {

            					if (m'.$axRow_1['mafi_FieldName'].'.checked) {              

                					f'.$axRow_1['mafi_FieldName'].'.checked = true;
            					}

            					
            					if (!m'.$axRow_1['mafi_FieldName'].'.checked) {

                					t'.$axRow_1['mafi_FieldName'].'.checked = false;
                					document.getElementById("'.$axRow_1['mafi_FieldName'].'_dp").style.display="none";
            					}}


            					function type_'.$axRow_1['mafi_FieldName'].'t() {

            					if (t'.$axRow_1['mafi_FieldName'].'.checked) {

                					f'.$axRow_1['mafi_FieldName'].'.checked = true;
                					m'.$axRow_1['mafi_FieldName'].'.checked = true;
                					document.getElementById("'.$axRow_1['mafi_FieldName'].'_dp").style.display="";

            					} else {

                					document.getElementById("'.$axRow_1['mafi_FieldName'].'_dp").style.display="none";
            					}}';

					$data_table .= '<td width="10%" style="text-align:center">

	                                <input type="checkbox" id="t'.$axRow_1['mafi_FieldName'].'" name="a'.$axRow_1['mafi_FieldName'].'t" value="1" onchange="type_'.$axRow_1['mafi_FieldName'].'t();"';

	                if ($axRow_1['refo_Target'] != '') {

	            		$data_table .= ' checked="checked"></td>';

	            	} else {

	            		$data_table .= '></td>';
	            	}

	            	$data_table .= '<td><select class="form-control text-md" id="'.$axRow_1['mafi_FieldName'].'_dp" name="'.$axRow_1['mafi_FieldName'].'_dp"';

	            	if ($axRow_1['refo_Target'] == '') {

	           			$data_table .= ' style="display:none">';

	           		} else {

	           			$data_table .= '>';
	           		}

					$query = "SELECT prov_ProvinceID,prov_Name FROM province WHERE prov_Deleted!='T'";

					$result = mysql_query($query) or die(mysql_error()."[".$query."]");

					while ($row = mysql_fetch_array($result)) {

						if ($row['prov_ProvinceID']==$axRow_1['refo_Target']) { $select='selected="selected"';}

						else{ $select='';}

	    				$data_table .= "<option value=".$row['prov_ProvinceID']." ".$select.">".$row['prov_Name']."</option>";
					}

      				$data_table .= '</select></td>';
				}

				if ($status == 0) {

					$data_function .= 'function type_'.$axRow_1['mafi_FieldName'].'f() {

										if (!f'.$axRow_1['mafi_FieldName'].'.checked) {

		                					m'.$axRow_1['mafi_FieldName'].'.checked = false;
		            					}}

										function type_'.$axRow_1['mafi_FieldName'].'m() {

		            					if (m'.$axRow_1['mafi_FieldName'].'.checked) {              

		                					f'.$axRow_1['mafi_FieldName'].'.checked = true;
		            					}}';

					$data_table .= '<td></td><td></td></tr>';

					if ($axRow_1['mafi_MasterFieldID']==2 || $axRow_1['mafi_MasterFieldID']==3 || $axRow_1['mafi_MasterFieldID']==5 || $axRow_1['mafi_MasterFieldID']==6 || $axRow_1['mafi_MasterFieldID']==20 || $axRow_1['mafi_MasterFieldID']==23 || $axRow_1['mafi_MasterFieldID']==48 || $axRow_1['mafi_MasterFieldID']==49) {

	      				$data_basic .= '<td></td><td></td></tr>';
	      			}
				}

	            $asData_1 = $axRow_1;

			} // WHILE SQL 1

		} // FOR

		$data_function .= '</script>';

		$data_custom = '';

		$script_custom = '<script type="text/javascript">';

		// for ($i=1; $i <= 5; $i++) { 

			$sql_custom = 'SELECT custom_field.*
							FROM custom_field
							WHERE bran_BrandID = "'.$id_brand.'"
							AND cufi_Deleted=""
							AND cufi_Type="Register"';

			$oRes_custom = $oDB->Query($sql_custom);

			while ($axRow_custom = $oRes_custom->FetchRow(DBI_ASSOC)){

				$sql_regis = 'SELECT custom_form.*
								FROM custom_form
								WHERE cufi_CustomFieldID="'.$axRow_custom['cufi_CustomFieldID'].'"
								AND card_CardID='.$id;

				$oRes_regis = $oDB->Query($sql_regis);

				$axRow_regis = $oRes_regis->FetchRow(DBI_ASSOC);

				$data_custom .= '<tr>';

				if ($axRow_custom['cufi_Name']) {

						$data_custom .= '<td style="text-align:center"><b>'.$axRow_custom['cufi_Name'].'</b></td>
										<td style="text-align:center"><input type="checkbox" class="f5" name="'.$axRow_custom['cufi_FieldName'].'f" id="f'.$axRow_custom['cufi_FieldName'].'" value="1" onchange="type_'.$axRow_custom['cufi_FieldName'].'f();"';

					if ($axRow_regis['cufo_FillIn'] == 'Y') { $data_custom .= ' checked'; }

					$data_custom .= '></td>
	                            		<td style="text-align:center"><input type="checkbox" class="m5" name="'.$axRow_custom['cufi_FieldName'].'m" id="m'.$axRow_custom['cufi_FieldName'].'" value="1" onchange="type_'.$axRow_custom['cufi_FieldName'].'m();"';

					if ($axRow_regis['cufo_Require'] == 'Y') { $data_custom .= ' checked'; }

	                $data_custom .= '></td>
	                            		<td style="text-align:center"><input type="checkbox" class="m5" name="'.$axRow_custom['cufi_FieldName'].'h" id="h'.$axRow_custom['cufi_FieldName'].'" value="1"';

					if ($axRow_regis['cufo_Hidden'] == 'Y') { $data_custom .= ' checked'; }

	                $data_custom .= '></td>';

	                if ($axRow_custom['fity_FieldTypeID']!=3 && $axRow_custom['fity_FieldTypeID']!=4 && $axRow_custom['fity_FieldTypeID']!=5) {

	                	$script_custom .= '

	                				function type_'.$axRow_custom['cufi_FieldName'].'f() {

										if (!f'.$axRow_custom['cufi_FieldName'].'.checked) {

		                					m'.$axRow_custom['cufi_FieldName'].'.checked = false;
	                				}}

	                				function type_'.$axRow_custom['cufi_FieldName'].'m() {

										if (m'.$axRow_custom['cufi_FieldName'].'.checked) {

		                					f'.$axRow_custom['cufi_FieldName'].'.checked = true;
	                				}}';

	                	$data_custom .= '<td></td>

	                					<td></td>';
	                } else {

			            $sql_list = 'SELECT custom_list_value.*
										FROM custom_list_value
										WHERE cufi_CustomFieldID = "'.$axRow_custom['cufi_CustomFieldID'].'"';

						$oRes_list = $oDB->Query($sql_list);

						$axRow_list = $oRes_list->FetchRow(DBI_ASSOC);

			            if ($axRow_list['clva_CustomListValueID']) { # TARGET

			            	$script_custom .= '

		            		function type_'.$axRow_custom['cufi_FieldName'].'f() {

								if (!f'.$axRow_custom['cufi_FieldName'].'.checked) {

                					m'.$axRow_custom['cufi_FieldName'].'.checked = false;
                					t'.$axRow_custom['cufi_FieldName'].'.checked = false;
                					document.getElementById("'.$axRow_custom['cufi_FieldName'].'_dp").style.display="none";
            					}}


		            		function type_'.$axRow_custom['cufi_FieldName'].'m() {

								if (!m'.$axRow_custom['cufi_FieldName'].'.checked) {

                					t'.$axRow_custom['cufi_FieldName'].'.checked = false;
                					document.getElementById("'.$axRow_custom['cufi_FieldName'].'_dp").style.display="none";

            					} else {

                					f'.$axRow_custom['cufi_FieldName'].'.checked = true;
            					}}

		            		function type_'.$axRow_custom['cufi_FieldName'].'t() {

								if (!t'.$axRow_custom['cufi_FieldName'].'.checked) {

                					document.getElementById("'.$axRow_custom['cufi_FieldName'].'_dp").style.display="none";

            					} else {

                					f'.$axRow_custom['cufi_FieldName'].'.checked = true;
                					m'.$axRow_custom['cufi_FieldName'].'.checked = true;
                					document.getElementById("'.$axRow_custom['cufi_FieldName'].'_dp").style.display="";
            					}}';

			            	$data_custom .= '<td style="text-align:center"><input type="checkbox" name="'.$axRow_custom['cufi_FieldName'].'t" id="t'.$axRow_custom['cufi_FieldName'].'" value="1" onchange="type_'.$axRow_custom['cufi_FieldName'].'t();"';

							if ($axRow_regis['cufo_Target'] != '') {

								$data_custom .= ' checked';
							}

			            	$data_custom .= '></td><td><select class="form-control text-md" id="'.$axRow_custom['cufi_FieldName'].'_dp" name="'.$axRow_custom['cufi_FieldName'].'_dp"';

			            	if ($axRow_regis['cufo_Target'] == '') {	$data_custom .= ' style="display:none">';	}
			           		else {	$data_custom .= '>';	}

			                $sql_get_list = 'SELECT * FROM custom_list_value WHERE cufi_CustomFieldID='.$axRow_custom['cufi_CustomFieldID'].' AND clva_Deleted != "T"';

							$oRes_list_value = $oDB->Query($sql_get_list);

		            		while ($axRow_value = $oRes_list_value->FetchRow(DBI_ASSOC)) {

	            				$query = "SELECT cufo_Target FROM custom_form WHERE cufi_CustomFieldID=".$axRow_custom['cufi_CustomFieldID']." AND card_CardID=".$id;

	            				$result = $oDB->QueryOne($query);

								if ($result==$axRow_value['clva_Value']) { $select='selected="selected"';}
								else{ $select='';}

		            			$data_custom .= '<option value="'.$axRow_value['clva_Value'].'" '.$select.'>'.$axRow_value['clva_Name'].'</option>';
	            			}

			                $data_custom .= '</select></td>';

			            } else {

	                		$script_custom .= '

	                				function type_'.$axRow_custom['cufi_FieldName'].'f() {

										if (!f'.$axRow_custom['cufi_FieldName'].'.checked) {

		                					m'.$axRow_custom['cufi_FieldName'].'.checked = false;
	                				}}

	                				function type_'.$axRow_custom['cufi_FieldName'].'m() {

										if (m'.$axRow_custom['cufi_FieldName'].'.checked) {

		                					f'.$axRow_custom['cufi_FieldName'].'.checked = true;
	                				}}';

			            }
	                }

				} else {

					$data_custom .= '

								<td style="text-align:center">
									<button type="button" class="btn btn-default btn-sm" value="'.$id.'" onclick="create_custom(this.value)"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
									</button>
								</td>
								<td></td>
	                            <td></td>
	                            <td></td>
	                            <td></td>';
				}

           		$data_custom .= '</tr>';
			}

			$script_custom .= '</script>';

} else if( $Act == 'save'){

	$table_register = 'register_form';

	$where_id = '';

	$query = "SELECT mafi_MasterFieldID,mafi_FieldName FROM master_field";

	$result = mysql_query($query) or die(mysql_error()."[".$query."]");

	while ($row = mysql_fetch_array($result)) {

		$do_sql_register = '';	

		$sql_register = '';

		$fill = '';

		$must = '';

		$hide = '';

		# BRAND ID

		if ($id_brand==65) { $n = 'b'; } 
		else { $n = 'a'; }


	    if ($_REQUEST[$n.$row['mafi_FieldName'].'h']==1) {

			$hide = 'Y';

		} else {

			$hide = 'N';
		}


	    if ($_REQUEST[$n.$row['mafi_FieldName'].'f']==1) {

			$fill = 'Y';

		} else {

			$fill = 'N';
		}

		if ($_REQUEST[$n.$row['mafi_FieldName'].'m']==1) {

			$fill = 'Y';
			$must = 'Y';

		} else {

			$must = 'N';
		}

		if ($row['mafi_MasterFieldID']==6) {

			if ($id_brand!=65) {

				if ($_REQUEST[$n.$row['mafi_FieldName'].'t']==1) {

					$age1 = $_REQUEST['age_dp1'];

					$age2 = $_REQUEST['age_dp2'];

					$target = $age1.','.$age2;

					$fill = 'Y';

					$must = 'Y';

				} else {

					$target = '';
				}

			} else {

				if ($_REQUEST[$n.$row['mafi_FieldName'].'basic']==1) {

					$age1 = $_REQUEST['age_basic1'];

					$age2 = $_REQUEST['age_basic2'];

					$target = $age1.','.$age2;

					$fill = 'Y';

					$must = 'Y';

				} else {

					$target = '';
				}
			}


					// if ($_REQUEST[$n.$row['mafi_FieldName'].'t']==1) {

					// 	$age1 = $_REQUEST['age_dp1'];
					// 	$age2 = $_REQUEST['age_dp2'];
					// 	$target = $age1.','.$age2;
					// 	$fill = 'Y';
					// 	$must = 'Y';

					// } else {

					// 	$target = '';
					// }

				} else {

					if ($id_brand != 65) {

						if ($_REQUEST[$n.$row['mafi_FieldName'].'t']==1) {

							$target = $_REQUEST[$row['mafi_FieldName'].'_dp'];

							$fill = 'Y';
							$must = 'Y';

						} else {

							$target = '';
						}

					} else {

						if ($_REQUEST[$n.$row['mafi_FieldName'].'basic']==1) {

							$target = $_REQUEST[$row['mafi_FieldName'].'_basic'];

							$fill = 'Y';
							$must = 'Y';

						} else {

							$target = '';
						}
					}

					// if ($_REQUEST[$n.$row['mafi_FieldName'].'t']==1) {

					// 	$target = $_REQUEST[$row['mafi_FieldName'].'_dp'];
					// 	$fill = 'Y';
					// 	$must = 'Y';

					// } else {

					// 	$target = '';
					// }
				}

				$sql_register = 'refo_UpdatedBy="'.$_SESSION['UID'].'"';

				$sql_register .= ',refo_UpdatedDate="'.$time_insert.'"';

				$sql_register .= ',refo_FillIn="'.$fill.'"';

				$sql_register .= ',refo_Require="'.$must.'"';

				$sql_register .= ',refo_Hidden="'.$hide.'"';

				$sql_register .= ',refo_Target="'.$target.'"';


				$do_sql_register = "UPDATE ".$table_register." SET ".$sql_register." WHERE card_CardID='".$id."' AND mafi_MasterFieldID = '".$row['mafi_MasterFieldID']."'";

				$oDB->QueryOne($do_sql_register);
			}

		// $do_sql_check = "UPDATE register_form 

		// 				SET refo_FillIn='Y', refo_Require='Y' 

		// 				WHERE mafi_MasterFieldID IN (2,3,5,6,20,23)";

		// $do_sql_check = "UPDATE register_form 

		// 				SET refo_FillIn='Y', refo_Require='Y' 

		// 				WHERE mafi_MasterFieldID IN (2,3,5,6)";

		// $oDB->QueryOne($do_sql_check);


		# CUSTOM

		$query = "SELECT * FROM custom_field WHERE bran_BrandID=".$id_brand;

		$result = mysql_query($query) or die(mysql_error()."[".$query."]");

		while ($row = mysql_fetch_array($result)) {

			$do_sql_register = '';	

			$sql_register = '';

			$fill = '';

			$must = '';

			$hide = '';

			$target = '';


	    	if ($_REQUEST[$row['cufi_FieldName'].'h']==1) {

	    		$hide = 'Y';

	    	} else { $hide = 'N'; }


	    	if ($_REQUEST[$row['cufi_FieldName'].'f']==1) {

				$fill = 'Y';

				if ($_REQUEST[$row['cufi_FieldName'].'m']==1) {

					$fill = 'Y';

					$must = 'Y';

					if ($_REQUEST[$row['cufi_FieldName'].'t']==1) {

						$target = $_REQUEST[$row['cufi_FieldName'].'_dp'];

						$fill = 'Y';

						$must = 'Y';

					} else {

						$target = '';
					}

				} else {

					$must = 'N';
					$target = '';
				}

			} else {

				$fill = 'N';
				$must = 'N';
				$target = '';
			}

			$sql_register = 'cufo_UpdatedBy="'.$_SESSION['UID'].'"';

			$sql_register .= ',cufo_UpdatedDate="'.$time_insert.'"';

			$sql_register .= ',card_CardID="'.$id.'"';

			$sql_register.=',cufo_FillIn="'.$fill.'"';

			$sql_register.=',cufo_Require="'.$must.'"';

			$sql_register.=',cufo_Hidden="'.$hide.'"';

			$sql_register.=',cufo_Target="'.$target.'"';

			if ($row['cufi_CustomFieldID']) {	$sql_register.=',cufi_CustomFieldID="'.$row['cufi_CustomFieldID'].'"';}

			$data = "SELECT * FROM custom_form WHERE card_CardID=".$id." AND cufi_CustomFieldID=".$row['cufi_CustomFieldID'];

			$form_data = $oDB->QueryOne($data);

			if ($form_data) {

				$do_sql_register = "UPDATE custom_form SET ".$sql_register." WHERE card_CardID=".$id." AND cufi_CustomFieldID = '".$row['cufi_CustomFieldID']."'";

			} else {

				# SEARCH MAX CUSTOM_FORM FORM ID

				$sql_get_last_ins = 'SELECT max(cufo_CustomFormID) FROM custom_form';
				$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
				$id_new = $id_last_ins+1;

				#######################################

				$sql_register .= ',cufo_CreatedBy="'.$_SESSION['UID'].'"';

				$sql_register .= ',cufo_CreatedDate="'.$time_insert.'"';

				$sql_register .= ',cufo_CustomFormID="'.$id_new.'"';

				$do_sql_register =  "INSERT INTO custom_form SET ".$sql_register."";
			}

			$oDB->QueryOne($do_sql_register);
		}

		$do_sql_card = "UPDATE mi_card SET date_update='".$time_insert."' WHERE card_id= '".$id."'";

		$oDB->QueryOne($do_sql_card);

		echo '<script>window.location.href = "register_form.php";</script>';

		exit;
	}



$oTmp->assign('card_amount', $card_amount);

$oTmp->assign('brand_id', $id_brand);

$oTmp->assign('card_id', $id);

$oTmp->assign('data_1', $asData_1);

$oTmp->assign('age1', $data_age1);

$oTmp->assign('age2', $data_age2);

$oTmp->assign('agebasic1', $basic_age1);

$oTmp->assign('agebasic2', $basic_age2);

$oTmp->assign('script_custom', $script_custom);

$oTmp->assign('data_function', $data_function);

$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('data_table', $data_table);

$oTmp->assign('data_basic', $data_basic);

$oTmp->assign('data_custom', $data_custom);

$oTmp->assign('is_menu', 'is_register_form');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_membership', 'in');

$oTmp->assign('content_file', 'card/register_form_create.htm');

$oTmp->display('layout/template.html');


//========================================//


$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}


//========================================//


?>
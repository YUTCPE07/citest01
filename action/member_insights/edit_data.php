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

$Act = $_REQUEST['act'];
$register_id = $_REQUEST['id'];
$time_insert = date("Y-m-d H:i:s");

$sql_member = 'SELECT mb_member.*,
					mb_member_register.card_id,
					mb_member_register.member_card_code,
					mb_member_register.member_brand_code,
					mi_card.image AS card_image,
					mi_card.path_image AS card_path
				FROM mb_member_register
				LEFT JOIN mb_member
				ON mb_member.member_id = mb_member_register.member_id
				LEFT JOIN mi_card
				ON mi_card.card_id = mb_member_register.card_id
				WHERE member_register_id="'.$register_id.'"';

$oRes = $oDB->Query($sql_member);
$member = $oRes->FetchRow(DBI_ASSOC);

$card_CardID = $member['card_id'];
$member_id = $member['member_id'];

// ==================================================================

if ($Act == 'save') {

	$member_card_code = trim_txt($_REQUEST['member_card_code']);
	$member_brand_code = trim_txt($_REQUEST['member_brand_code']);

	# CHECK MEMBER CARD CODE

	if ($member_card_code) {

		if ($member_card_code != $member['member_card_code']) {

			$sql_card_code = 'SELECT member_id 
								FROM mb_member_register 
								WHERE card_id="'.$card_CardID.'"
								AND member_card_code="'.$member_card_code.'"';
			$member_card_id = $oDB->QueryOne($sql_card_code);

			if ($member_card_id!=$member_id && $member_card_id!="") {

				echo '<script style="text/javascript">alert("ไม่สามารถใช้ Member Card ID นี้ได้");</script>';
				echo '<script style="text/javascript">window.location.href="edit_data.php?id='.$register_id.'";</script>';
				exit();

			} else {

				$do_card = 'UPDATE mb_member_register SET ';
				$do_card .= 'member_card_code="'.$member_card_code.'"';
				$do_card .= ' WHERE member_id="'.$member_id.'"'; 
				$do_card .= ' AND card_id="'.$card_CardID.'"'; 

				$oDB->QueryOne($do_card);
			}
		}
	}

	# CHECK MEMBER BRAND CODE

	if ($member_brand_code) {

		if ($member_brand_code != $member['member_brand_code']) {

			$sql_brand_code = 'SELECT member_id 
								FROM mb_member_register 
								WHERE card_CardID="'.$card_CardID.'"
								AND member_brand_code="'.$member_brand_code.'"';
			$member_brand_id = $oDB->QueryOne($sql_brand_code);

			if ($member_brand_id) {

				echo '<script style="text/javascript">alert("ไม่สามารถใช้ Member Brand ID นี้ได้");</script>';
				echo '<script style="text/javascript">window.location.href="edit_data.php&id='.$register_id.'";</script>';
				exit();

			} else {

				$do_brand = 'UPDATE mb_member_register SET ';
				$do_brand .= 'member_brand_code="'.$member_brand_code.'"';
				$do_brand .= ' WHERE member_id="'.$member_id.'"'; 
				$do_brand .= ' AND card_id="'.$card_CardID.'"'; 

				$oDB->QueryOne($do_brand);
			}
		}
	}



	$status_form = "true"; 

	$sql_field = 'SELECT a.*,b.*,

					a.mafi_MasterFieldID AS master_field_id,
					b.refo_Target

					FROM master_field AS a

					LEFT JOIN register_form AS b
					ON b.mafi_MasterFieldID = a.mafi_MasterFieldID

					WHERE a.mafi_Deleted != "T"
					AND b.card_CardID = "'.$card_CardID.'"
					AND a.mafi_MasterFieldID NOT IN (48,49)

					GROUP BY a.mafi_FieldName
					ORDER BY a.mafi_FieldOrder';

	$oRes = $oDB->Query($sql_field);

	$d = 1;

	while ($field = $oRes->FetchRow(DBI_ASSOC)){

		# BIRTHDAY

		if ($field['master_field_id'] == 6) {

			if ($birth_member == '0000-00-00' || $birth_member == '') {

				$year = trim_txt($_REQUEST[$field['mafi_FieldName'].'_year']);
				$month = trim_txt($_REQUEST[$field['mafi_FieldName'].'_month']);
				$date = trim_txt($_REQUEST[$field['mafi_FieldName'].'_date']);

				if ($year != "" && $month != "" && $date != "") { $birthday = $year."-".$month."-".$date; }
				else { $birthday = ""; }

				# AGE

	 			$age = (date("md", date("U", mktime(0, 0, 0, $month, $date, $year))) > date("md")
			    ? ((date("Y") - $year) - 1)
			    : (date("Y") - $year));

				if ($field['refo_Target']) {

		            $token = strtok($field['refo_Target'] , ",");
					$target = array();
					$i = 0;

					while ($token !== false) {

						$sql_target = 'SELECT mata_NameEn
										FROM master_target
										WHERE mata_MasterTargetID="'.$token.'"';
			 			$target[$i] = $oDB->QueryOne($sql_target);
						$token = strtok(",");
						$i++;
					}

					if ($target[0] <= $age && $age <= $target[1]) {

						if ($birthday) {

							if ($d == 1) {

								$data_member .= $field['mafi_FieldName'].'="'.$birthday.'"';
								$d++;

							} else {

								$data_member .= ','.$field['mafi_FieldName'].'="'.$birthday.'"';
								$d++;
							}
						}

					} else { $status_form = "false"; }
				
				} else {

					if ($birthday) {

						if ($d == 1) {

							$data_member .= $field['mafi_FieldName'].'="'.$birthday.'"';
							$d++;

						} else {

							$data_member .= ','.$field['mafi_FieldName'].'="'.$birthday.'"';
							$d++;
						}
					}
				}
			}
		
		} else if (trim_txt($_REQUEST[$field['mafi_FieldName']])) {

			if ($field['refo_Target']) {

				if ($field['refo_Target'] != trim_txt($_REQUEST[$field['mafi_FieldName']])) {

					$status_form = "false";

				} else {

					if ($d == 1) {

						$data_member .= $field['mafi_FieldName'].'="'.trim_txt($_REQUEST[$field['mafi_FieldName']]).'"'; 
						$d++;

					} else {

						$data_member .= ','.$field['mafi_FieldName'].'="'.trim_txt($_REQUEST[$field['mafi_FieldName']]).'"'; 
						$d++;
					}
				}

			} else {

				# MOBILE

				if ($field['master_field_id'] == 20) {

					$mobile_code = $_REQUEST['code_'.$field['mafi_FieldName']];
					$mobile = $mobile_code.$_REQUEST[$field['mafi_FieldName']];

					if ($d == 1) {

						$data_member .= $field['mafi_FieldName'].'="'.$mobile.'"';
						$d++;

					} else {

						$data_member .= ','.$field['mafi_FieldName'].'="'.$mobile.'"';
						$d++;
					}

				} else {

					if ($d == 1) {

						$data_member .= $field['mafi_FieldName'].'="'.trim_txt($_REQUEST[$field['mafi_FieldName']]).'"';
						$d++;

					} else {

						$data_member .= ','.$field['mafi_FieldName'].'="'.trim_txt($_REQUEST[$field['mafi_FieldName']]).'"';
						$d++;
					}
				} 
			}
		}
	}

	# MEMBER

	if ($status_form == "true") {

		$do_member = 'UPDATE mb_member SET '.$data_member;
		$do_member .= ',update_by="'.$_SESSION['UID'].'"'; 
		$do_member .= ',date_update="'.$time_insert.'" '; 
		$do_member .= 'WHERE member_id="'.$member_id.'"; '; 
			
		$oDB->QueryOne($do_member);
	}

	$status_custom = "true";

	$sql_custom = 'SELECT custom_field.*,
					custom_form.cufo_Target,
					field_type.fity_Name AS field_type
					FROM custom_field
					LEFT JOIN custom_form
					ON custom_form.cufi_CustomFieldID = custom_field.cufi_CustomFieldID
					LEFT JOIN field_type
					ON custom_field.fity_FieldTypeID = field_type.fity_FieldTypeID
					WHERE custom_form.card_CardID = "'.$card_CardID.'"
					AND custom_form.cufo_FillIn = "Y"
					ORDER BY custom_field.cufi_FieldOrder';

	$oRes = $oDB->Query($sql_custom);
	while ($field = $oRes->FetchRow(DBI_ASSOC)){

		$do_custom = '';

		if (trim_txt($_REQUEST[$field['cufi_FieldName']])) {

			$data_custom = trim_txt($_REQUEST['code_'.$field['cufi_FieldName']]).trim_txt($_REQUEST[$field['cufi_FieldName']]);

			if ($field['cufo_Target']) {

				if ($field['cufo_Target'] != $data_custom) {

					$status_custom = "false";

				} else {

					$sql_check = 'SELECT reda_Value
									FROM custom_register_data
									WHERE mebe_MemberID = "'.$member_id.'"
									AND card_CardID = "'.$card_CardID.'"
									AND cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
					$check = $oDB->Query($sql_check);

					if ($check) {

						$do_custom = 'UPDATE custom_register_data SET ';
						$do_custom .= 'reda_Value="'.$data_custom.'"';
						$do_custom .= ',reda_UpdatedBy="'.$_SESSION['UID'].'"'; 
						$do_custom .= ',reda_UpdatedDate="'.$time_insert.'"'; 
						$do_custom .= ' WHERE cufi_CustomFieldID="'.$field['cufi_CustomFieldID'].'"'; 
						$do_custom .= ' AND mebe_MemberID="'.$member_id.'"'; 
						$do_custom .= ' AND card_CardID="'.$card_CardID.'"; '; 

					} else {

						$do_custom = 'INSERT INTO custom_register_data SET ';
						$do_custom .= 'cufi_CustomFieldID="'.$field['cufi_CustomFieldID'].'"'; 
						$do_custom .= ',mebe_MemberID="'.$member_id.'"'; 
						$do_custom .= ',card_CardID="'.$card_CardID.'"'; 
						$do_custom .= ',reda_Value="'.$data_custom.'"'; 
						$do_custom .= ',reda_CreatedBy="'.$_SESSION['UID'].'"'; 
						$do_custom .= ',reda_CreatedDate="'.$time_insert.'"'; 
						$do_custom .= ',reda_UpdatedBy="'.$_SESSION['UID'].'"'; 
						$do_custom .= ',reda_UpdatedDate="'.$time_insert.'"; '; 
					}
				}

			} else {

				if ($status_custom == "true" && $status_form == "true") {

					$sql_check = 'SELECT reda_Value
									FROM custom_register_data
									WHERE mebe_MemberID = "'.$member_id.'"
									AND card_CardID = "'.$card_CardID.'"
									AND cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
					$check = $oDB->QueryOne($sql_check);

					if ($check) {

						$do_custom = 'UPDATE custom_register_data SET ';
						$do_custom .= 'reda_Value="'.$data_custom.'"';
						$do_custom .= ',reda_UpdatedBy="'.$_SESSION['UID'].'"'; 
						$do_custom .= ',reda_UpdatedDate="'.$time_insert.'"'; 
						$do_custom .= ' WHERE cufi_CustomFieldID="'.$field['cufi_CustomFieldID'].'"'; 
						$do_custom .= ' AND mebe_MemberID="'.$member_id.'"'; 
						$do_custom .= ' AND card_CardID="'.$card_CardID.'"; '; 

					} else {

						$do_custom = 'INSERT INTO custom_register_data SET ';
						$do_custom .= 'cufi_CustomFieldID="'.$field['cufi_CustomFieldID'].'"'; 
						$do_custom .= ',mebe_MemberID="'.$member_id.'"'; 
						$do_custom .= ',card_CardID="'.$card_CardID.'"'; 
						$do_custom .= ',reda_Value="'.$data_custom.'"'; 
						$do_custom .= ',reda_CreatedBy="'.$_SESSION['UID'].'"'; 
						$do_custom .= ',reda_CreatedDate="'.$time_insert.'"'; 
						$do_custom .= ',reda_UpdatedBy="'.$_SESSION['UID'].'"'; 
						$do_custom .= ',reda_UpdatedDate="'.$time_insert.'"; '; 
					}
				}
			}
			
			$oDB->QueryOne($do_custom);
		}
	}

	echo '<script style="text/javascript">window.location.href="member_register.php";</script>';
	exit();

} else {

	# MEMBER

	if ($member['member_image'] && $member['member_image']!='user.png') {

		$member_image = '<img src="../../upload/member_upload/'.$member['member_image'].'" width="100" height="100" class="img-circle image_border"/>';

	} else if ($member['facebook_id']) {

		 $member_image = '<img src="http://graph.facebook.com/'.$member['facebook_id'].'/picture?type=square" width="100" height="100" class="img-circle image_border" />';

	} else {
				                    	
		$member_image = '<img src="../../images/user.png" width="100" height="100" class="img-circle image_border" />';
	}


	# CARD IMAGE

	if($member['card_image']!=''){

		$card_image = '<img src="../../upload/'.$member['card_path'].$member['card_image'].'" class="img-rounded image_border" height="100px">';

	} else {

		$card_image = '<img src="../../images/card_privilege.jpg" class="img-rounded image_border" height="100px">';
	}

	$member_data = '<table width="400px">
						<tr>
							<td width="170px" style="text-align:right">'.$member_image.'</td>
							<td style="text-align:center"><span class="glyphicon glyphicon-plus" style="font-size:20px"></span></td>
							<td width="170px" style="text-align:left">'.$card_image.'</td>
						</tr>
					</table>';

	$member_data .= '<table width="500px">';

	$topic = array("Profile", "Home Address", "Work Address", "Work", "Contact");

	for ($i=0; $i <5 ; $i++) { 

		$sql_field = 'SELECT a.*,b.*,c.*,

							a.mafi_MasterFieldID AS master_field_id,
							b.refo_Target,
							d.fity_Name AS field_type

							FROM master_field AS a

							LEFT JOIN register_form AS b
							ON b.mafi_MasterFieldID = a.mafi_MasterFieldID

							LEFT JOIN mi_card AS c
							ON b.card_CardID = c.card_id

							LEFT JOIN field_type AS d
							ON a.mafi_FieldType = d.fity_FieldTypeID

							WHERE a.mafi_Position = "'.$topic[$i].'"
							AND a.mafi_Deleted != "T"
							AND c.card_id = "'.$card_CardID.'"
							AND b.refo_FillIn = "Y"

							GROUP BY a.mafi_FieldName
							ORDER BY a.mafi_FieldOrder';

		$oRes = $oDB->Query($sql_field);
		$check_field = $oDB->QueryOne($sql_field);

		if ($check_field) {

			$member_data .= '<tr height="40px"><td colspan="3" style="text-align:center"><u><b>'.$topic[$i].'</b></u></td></tr>';

			while ($field = $oRes->FetchRow(DBI_ASSOC)){

				if ($field['refo_Require']=='Y') { 

					$text_rq = ' <span class="text-rq">*</span>';
					$rq_af = 'required autofocus'; 

				} else { $text_rq = '';	$rq_af = '';  }

				$member_data .= '<tr height="40px">
									<td style="text-align:right"><b>'.$field['mafi_NameEn'].$text_rq.'</b></td>
									<td width="10px"></td>';

				if ($field['field_type']=='Text') {

					# MEMBER BRAND CODE & MEMBER CARD COE

					if ($field['master_field_id']=='48') { # CARD

						if ($member['member_card_code']) { $disabled = 'readonly'; }
						else { $disabled = ''; }
						$member[$field['mafi_FieldName']] = $member['member_card_code'];
							
					} elseif ($field['master_field_id']=='49') { # BRAND

						if ($member['member_brand_code']) { $disabled = 'readonly'; }
						else { $disabled = ''; }
						$member[$field['mafi_FieldName']] = $member['member_brand_code'];

					} else {

						if ($member[$field['mafi_FieldName']]) { $disabled = 'readonly'; }
						else { $disabled = ''; }
					}

					$member_data .= '<td style="text-align:center"><input type="text" name="'.$field['mafi_FieldName'].'" class="form-control text-md" placeholder="Text" '.$rq_af.' '.$disabled.' value="'.$member[$field['mafi_FieldName']].'">';
						
				} else if ($field['field_type']=='Number') {

					if ($member[$field['mafi_FieldName']]) { $disabled = 'readonly'; }
					else { $disabled = ''; }

					$member_data .= '<td style="text-align:center"><input type="number" name="'.$field['mafi_FieldName'].'" class="form-control text-md" placeholder="Number" value="'.$member[$field['mafi_FieldName']].'" '.$rq_af.' '.$disabled.'>';
						
				} else if ($field['field_type']=='Date') {

					if ($member[$field['mafi_FieldName']] != '0000-00-00') { $disabled = 'readonly'; }
					else { $disabled = ''; }

					$data = $member[$field['mafi_FieldName']];
					$year_data = substr($data,0,4);
					$month_data = substr($data,5,2);
					$date_data = substr($data,8,2);

					# DAY OPTION

					$option_date = '';

					for ($x = 1; $x < 32; $x++) {

						if ($x == $date_data) { 

							$select = 'selected="selected"';
							$date_member = $x;

						} else { $select = ''; }

						if (strlen($x) == 1) { $d = '0'.$x; }
						else { $d = $x; }

						$option_date .= '<option value="'.$d.'" '.$select.'>'.$d.'</option>';
					}


					# MONTH OPTION

					$month = ["Jan.", "Feb.", "Mar.", "Apr.", "May.", "Jun.", "Jul.", "Aug.", "Sep.", "Oct.", "Nov.", "Dec."];
					$option_month = '';

					for ($x = 1; $x < 13; $x++) {

						if ($x == $month_data) { 

							$select = 'selected="selected"';
							$month_member = $month[$x-1];

						} else { $select = ''; }

						if (strlen($x) == 1) { $d = '0'.$x; }
						else { $d = $x; }

						$option_month .= '<option value="'.($d).'" '.$select.'>'.$month[$x-1].'</option>';
					}


					# YEAR OPTION

					$this_year = date('Y',time());
					$start_year = $this_year-100;
					$option_year = '';

					if ($year_data > $this_year) { $year_data = $year_data-543; }

					for ($x = $this_year; $x >= $start_year; $x--) {

						if ($x == $year_data) { 

							$select = 'selected="selected"';
							$year_member = $x;

						} else { $select = ''; }

						$option_year .= '<option value="'.$x.'" '.$select.')>'.$x.'</option>';
					}

					$member_data .= '<td>';

					if ($disabled == 'readonly') {

						$member_data .= '<span class="form-inline">
										<input type="text" id="date" class="form-control text-md" name="'.$field['mafi_FieldName'].'_date" style="width:70px;text-align:center" value="'.$date_member.'" readonly>
										<input type="text" id="date" class="form-control text-md" name="'.$field['mafi_FieldName'].'_date" style="width:80px;text-align:center" value="'.$month_member.'" readonly>
										<input type="text" id="date" class="form-control text-md" name="'.$field['mafi_FieldName'].'_date" style="width:90px;text-align:center" value="'.$year_member.'" readonly>
									</span>';

					} else {

						$member_data .= '<span class="form-inline">
										<select id="date" class="form-control text-md" name="'.$field['mafi_FieldName'].'_date" style="width:70px" '.$rq_af.' '.$disabled.'>
											<option value=""> - - -</option>
											'.$option_date.'
										</select>
										<select id="month" class="form-control text-md" name="'.$field['mafi_FieldName'].'_month" style="width:80px" '.$rq_af.' '.$disabled.'>
											<option value=""> - - - -</option>
											'.$option_month.'
										</select>
										<select id="year" class="form-control text-md" name="'.$field['mafi_FieldName'].'_year" style="width:90px" '.$rq_af.' '.$disabled.'>
											<option value=""> - - - - -</option>
											'.$option_year.'
										</select></span>';
					}
						
				} else if ($field['field_type']=='Radio') {

					$x = 0;

					$data = $member[$field['mafi_FieldName']];

					$member_data .= '<td><span class="form-inline">';

					$sql_target = 'SELECT *
										FROM master_target
										WHERE mafi_MasterFieldID = "'.$field['master_field_id'].'"';
					$oRes_target = $oDB->Query($sql_target);
					while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

						if ($data != 0) {

							if ($data == $target['mata_MasterTargetID']) {

								if ($x==0) {

									$member_data .= '&nbsp;&nbsp;<span class="glyphicon glyphicon-check"></span> '.$target['mata_NameEn'].'<label>';

								} else {

									$member_data .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-check"></span> '.$target['mata_NameEn'].'<label>';
								}

							} else {

								if ($x==0) {

									$member_data .= '&nbsp;&nbsp;<span class="glyphicon glyphicon-unchecked"></span> '.$target['mata_NameEn'].'<label>';

								} else {

									$member_data .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-unchecked"></span> '.$target['mata_NameEn'].'<label>';
								}
							}

						} else {

							if ($x==0) { 

								$member_data .= '<input type="radio" name="'.$field['mafi_FieldName'].'" value="'.$target['mata_MasterTargetID'].'" checked> <label>'.$target['mata_NameEn'].'<label>'; 

							} else { 

								$member_data .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="'.$field['mafi_FieldName'].'" value="'.$target['mata_MasterTargetID'].'" '.$check.'> <label>'.$target['mata_NameEn'].'<label>'; 
							}
						}

						$x++;
					}

					$member_data .= '</span>';

				} else if ($field['field_type']=='Checkbox') {

					$member_data .= '<td><span class="form-inline"><label>';

					$sql_target = 'SELECT *
										FROM master_target
										WHERE mafi_MasterFieldID = "'.$field['master_field_id'].'"';
					$oRes_target = $oDB->Query($sql_target);
					while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

						$member_data .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="'.$field['mafi_FieldName'].'" value="'.$target['mata_MasterTargetID'].'"> '.$target['mata_NameEn'].'<br>';
					}

					$member_data .= '</label></span>';

				} else if ($field['field_type']=='Selection') {

					$data = $member[$field['mafi_FieldName']];

					if ($member[$field['mafi_FieldName']] != '') { $disabled = 'readonly'; }
					else { $disabled = ''; }

					if ($field['master_field_id'] == 33 || $field['master_field_id'] == 45) {

						if ($disabled == 'readonly' && $data!=0) {

							$sql_target = 'SELECT prov_Name FROM province WHERE prov_ProvinceID = "'.$data.'"';
							$data = $oDB->QueryOne($sql_target);

							$member_data .= '<td><input type="text" class="form-control text-md" value="'.$data.'" readonly>';

						} else {

							$sql_target = 'SELECT * FROM province WHERE prov_Deleted = "" ORDER BY prov_Name';
							$oRes_target = $oDB->Query($sql_target);

							$member_data .= '<td><select name="'.$field['mafi_FieldName'].'" class="form-control" '.$rq_af.' style="width:250px">
											<option value="">Please Select ..</option>';
								
							while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

								$member_data .= '<option value="'.$target['prov_ProvinceID'].'" '.$select.'>'.$target['prov_Name'].'</option>';
							}

							$member_data .= '</select>';
						}

					} elseif ($field['master_field_id'] == 34 || $field['master_field_id'] == 46) {

						if ($disabled == 'readonly' && $data!=0) {

							$sql_target = 'SELECT coun_NiceName FROM country WHERE coun_CountryID = "'.$data.'"';
							$data = $oDB->QueryOne($sql_target);

							$member_data .= '<td><input type="text" class="form-control text-md" value="'.$data.'" readonly>';

						} else {

							$sql_target = 'SELECT * FROM country WHERE coun_PhoneCode!=0 ORDER BY coun_Nicename';
							$oRes_target = $oDB->Query($sql_target);

							$member_data .= '<td><select name="'.$field['mafi_FieldName'].'" class="form-control" '.$rq_af.' style="width:250px">
											<option value="">Please Select ..</option>';
								
							while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

								$member_data .= '<option value="'.$target['coun_CountryID'].'" '.$select.'>'.$target['coun_Nicename'].'</option>';
							}

							$member_data .= '</select>';
						}

					} else {

						if ($disabled == 'readonly' && $data!=0) {

							$sql_target = 'SELECT mata_NameEn FROM master_target WHERE mata_MasterTargetID = "'.$data.'"';
							$data = $oDB->QueryOne($sql_target);

							$member_data .= '<td><input type="text" id="'.$field['mafi_FieldName'].'" class="form-control text-md" name="'.$field['mafi_FieldName'].'" value="'.$data.'" readonly>';

						} else {

							$sql_target = 'SELECT *
												FROM master_target
												WHERE mafi_MasterFieldID = "'.$field['master_field_id'].'"';
							$oRes_target = $oDB->Query($sql_target);

							$member_data .= '<td><select name="'.$field['mafi_FieldName'].'" class="form-control" '.$rq_af.' style="width:250px">
											<option value="">Please Select ..</option>';
								
							while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

								$member_data .= '<option value="'.$target['mata_MasterTargetID'].'" '.$select.'>'.$target['mata_NameEn'].'</option>';
							}

							$member_data .= '</select>';
						}
					}

				} else if ($field['field_type']=='Tel') {

					$data = $member[$field['mafi_FieldName']];
					$phone_code = '';

					if ($data) { $disabled = 'readonly'; }
					else { $disabled = ''; }

					$strlen = strlen($data);

					if ($strlen <= 10) { 

						$phone_num = substr($data, 1,9);
						$phone_code = substr($data, 0,1);

					} else { 

						$phone_num = substr($data, ($strlen-9),9); 
						$phone_code = substr($data, 0,($strlen-9));
					}

					if ($phone_code == "0" || $phone_code == "") { $phone_code = "+66"; }

					if ($disabled == 'readonly') {

						$member_data .= '<td><span class="form-inline">
										<input type="text" style="width:61px" name="code_'.$field['mafi_FieldName'].'" value="'.$phone_code.'" class="form-control text-md" '.$disabled.'>
										<input type="text" style="width:190px" name="'.$field['mafi_FieldName'].'" value="'.$phone_num.'" maxlength="9" class="form-control text-md" placeholder="Tel" '.$disabled.'>
									</span>';

					} else {

						# PHONE CODE

						$sql_code = 'SELECT DISTINCT coun_PhoneCode 
											FROM country 
											WHERE coun_PhoneCode NOT IN (0,1,7) 
											ORDER BY coun_PhoneCode';
						$oRes_code = $oDB->Query($sql_code);
						$option_code = '';
						while ($axRow_code = $oRes_code->FetchRow(DBI_ASSOC)){

							$check_code = '';
							if ('+'.$axRow_code['coun_PhoneCode'] == $phone_code) { $check_code = 'selected'; }

							$option_code .= '<option value="+'.$axRow_code['coun_PhoneCode'].'" '.$check_code.'>+'.$axRow_code['coun_PhoneCode'].'</option>';
						}

						$member_data .= '<td><span class="form-inline">
                    					<select class="form-control text-md" id="code_'.$field['mafi_FieldName'].'" name="code_'.$field['mafi_FieldName'].'" '.$rq_af.'>'.$option_code.'</select>
                    					<input type="text" style="width:168px" name="'.$field['mafi_FieldName'].'" value="'.$phone_num.'" maxlength="9" class="form-control text-md" placeholder="Tel" '.$rq_af.'>
                    				</span>';
					}
				}

				$member_data .= '	</td></tr>';
			}
		}
	}

	# CUSTOM DATA

	$sql_custom = 'SELECT custom_field.*,
						custom_form.cufo_Require,
						field_type.fity_Name AS field_type
						FROM custom_field
						LEFT JOIN custom_form
						ON custom_form.cufi_CustomFieldID = custom_field.cufi_CustomFieldID
						LEFT JOIN field_type
						ON custom_field.fity_FieldTypeID = field_type.fity_FieldTypeID
						WHERE custom_form.card_CardID = "'.$card_CardID.'"
						AND custom_form.cufo_FillIn = "Y"
						ORDER BY custom_field.cufi_FieldOrder';

	$oRes = $oDB->Query($sql_custom);
	$check_field = $oDB->QueryOne($sql_custom);

	if ($check_field) {

		$member_data .= '<tr height="40px"><td colspan="3" style="text-align:center"><u><b>Custom</b></u></td></tr>';

		while ($field = $oRes->FetchRow(DBI_ASSOC)){

			$sql_member_custom = 'SELECT reda_Value
										FROM custom_register_data 
										WHERE mebe_MemberID="'.$member_id.'"
										AND card_CardID="'.$card_CardID.'"
										AND cufi_CustomFieldID="'.$field['cufi_CustomFieldID'].'"';
			$data = $oDB->QueryOne($sql_member_custom);

			if ($field['cufo_Require']=='Y') { 

				$text_rq = ' <span class="text-rq">*</span>';
				$rq_af = 'required autofocus'; 

			} else { $text_rq = '';	$rq_af = '';  }

			$member_data .= '	<tr height="40px"><td style="text-align:right">
							<b>'.$field['cufi_Name'].$text_rq.'</b></td>
							<td width="10px"></td>';

			if ($field['field_type']=='Text') {

				$member_data .= '<td style="text-align:center"><input type="text" name="'.$field['cufi_FieldName'].'" class="form-control text-md" placeholder="Text" '.$rq_af.' value="'.$data.'">';
						
			} else if ($field['field_type']=='Number') {

				$member_data .= '<td style="text-align:center"><input type="number" name="'.$field['cufi_FieldName'].'" class="form-control text-md" placeholder="Number" '.$rq_af.' value="'.$data.'">';
						
			} else if ($field['field_type']=='Date') {

				$year_data = substr($data,0,4);
				$month_data = substr($data,5,2);
				$date_data = substr($data,8,2);

				# DAY OPTION

				$option_date = '';

				for ($x = 1; $x < 32; $x++) {

					if ($x == $date_data) { $select = 'selected="selected"'; }
					else { $select = ''; }

					if (strlen($x) == 1) { $d = '0'.$x; }
					else { $d = $x; }

					$option_date .= '<option value="'.$d.'" '.$select.'>'.$d.'</option>';
				}


				# MONTH OPTION

				$month = ["Jan.", "Feb.", "Mar.", "Apr.", "May.", "Jun.", "Jul.", "Aug.", "Sep.", "Oct.", "Nov.", "Dec."];
				$option_month = '';

				for ($x = 1; $x < 13; $x++) {

					if ($x == $month_data) { $select = 'selected="selected"'; }
					else { $select = ''; }

					if (strlen($x) == 1) { $d = '0'.$x; }
					else { $d = $x; }

					$option_month .= '<option value="'.($d).'" '.$select.'>'.$month[$x-1].'</option>';
				}


				# YEAR OPTION

				$this_year = date('Y',time());
				$start_year = $this_year-100;
				$option_year = '';

				if ($year_data > $this_year) { $year_data = $year_data-543; }

				for ($x = $this_year; $x >= $start_year; $x--) {

					if ($x == $year_data) { $select = 'selected="selected"'; }
					else { $select = ''; }

					$option_year .= '<option value="'.$x.'" '.$select.')>'.$x.'</option>';
				}

				$member_data .= '<td><span class="form-inline">
								<select id="date" class="form-control text-md" name="'.$field['mafi_FieldName'].'_date" style="width:70px" '.$rq_af.'>
									<option value=""> - - -</option>
									'.$option_date.'
								</select>
								<select id="month" class="form-control text-md" name="'.$field['mafi_FieldName'].'_month" style="width:80px" '.$rq_af.'>
									<option value=""> - - - -</option>
									'.$option_month.'
								</select>
								<select id="year" class="form-control text-md" name="'.$field['mafi_FieldName'].'_year" style="width:90px" '.$rq_af.'>
									<option value=""> - - - - -</option>
									'.$option_year.'
								</select></span>';
						
			} else if ($field['field_type']=='Radio') {

				$x = 0;

				$member_data .= '<td><span class="form-inline"><label>';

				$sql_target = 'SELECT *
									FROM custom_list_value
									WHERE cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
				$oRes_target = $oDB->Query($sql_target);
				while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

					if ($data != 0) {

						if ($data == $target['clva_CustomListValueID']) { $check = "checked"; }
						else { $check = ''; }

					} else {

						if ($x==0) { $check = "checked"; }
						else { $check = ''; }
					}

					$member_data .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="'.$field['cufi_FieldName'].'" value="'.$target['clva_CustomListValueID'].'" '.$check.'> '.$target['clva_Name'].'';

					$x++;
				}

				$member_data .= '</label></span>';

			} else if ($field['field_type']=='Checkbox') {

				$member_data .= '<td><span class="form-inline"><label>';

				$sql_target = 'SELECT *
									FROM custom_list_value
									WHERE cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
				$oRes_target = $oDB->Query($sql_target);
				while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

					if ($data == $target['clva_CustomListValueID']) { $check_c = 'checked'; }
					else { $check_c = 'checked'; }

					$member_data .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="'.$field['cufi_FieldName'].'" value="'.$target['clva_CustomListValueID'].'" '.$check_c.'> '.$target['clva_Name'].'<br>';
				}

				$member_data .= '</label></span>';

			} else if ($field['field_type']=='Selection') {

				$member_data .= '<td><select name="'.$field['cufi_FieldName'].'" class="form-control" '.$rq_af.' style="width:250px">
								<option value="">Please Select ..</option>';

				$sql_target = 'SELECT *
									FROM custom_list_value
									WHERE cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
				$oRes_target = $oDB->Query($sql_target);
				while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

					if ($data == $target['clva_CustomListValueID']) { $select = 'selected="selected"'; }
					else { $select = ''; }

					$member_data .= '<option value="'.$target['clva_CustomListValueID'].'" '.$select.'>'.$target['clva_Name'].'</option>';
				}

				$member_data .= '</select>';

			} else if ($field['field_type']=='Tel') {

				$strlen = strlen($data);

				if ($strlen <= 10) { 

					$phone_num = substr($data, 1,9);
					$phone_code = substr($data, 0,1);

				} else { 

					$phone_num = substr($data, ($strlen-9),9); 
					$phone_code = substr($data, 0,($strlen-9));
				}

				if ($phone_code == "0" || $phone_code == "") { $phone_code = "+66"; }

				# PHONE CODE

				$sql_code = 'SELECT DISTINCT coun_PhoneCode 
								FROM country 
								WHERE coun_PhoneCode NOT IN (0,1,7) 
								ORDER BY coun_PhoneCode';
				$oRes_code = $oDB->Query($sql_code);
				$option_code = '';
				while ($axRow_code = $oRes_code->FetchRow(DBI_ASSOC)){

					$check_code = '';
					if ('+'.$axRow_code['coun_PhoneCode'] == $phone_code) { $check_code = 'selected'; }

					$option_code .= '<option value="+'.$axRow_code['coun_PhoneCode'].'" '.$check_code.'>+'.$axRow_code['coun_PhoneCode'].'</option>';
				}

				$member_data .= '<td><span class="form-inline">
                    		<select class="form-control text-md" id="code_'.$field['cufi_FieldName'].'" name="code_'.$field['cufi_FieldName'].'" '.$rq_af.'>'.$option_code.'</select>
                    		<input type="text" style="width:168px" name="'.$field['cufi_FieldName'].'" value="'.$phone_num.'" maxlength="9" class="form-control text-md" placeholder="Tel" '.$rq_af.'>
                    	</span>';
			}

			$member_data .= '	</td></tr>';
		}
	}

	$member_data .= '</table>';
}




$oTmp->assign('member_data', $member_data);

$oTmp->assign('is_menu', 'is_analytics');

$oTmp->assign('content_file', 'member_insights/edit_data.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());

}

//========================================//

?>

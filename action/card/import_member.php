<?php

header('Content-Type:text/html; charset=UTF-8');

//========================================//

ini_set("display_errors",1);
error_reporting(1);

//========================================//

include('../../include/common.php');
include('../../lib/function_normal.php');
include('../../include/common_check.php');
include('../../lib/phpqrcode/qrlib.php');
include('../../include/common_check.php');
include('../../lib/PHPExcel/Classes/PHPExcel.php');
include('../../lib/PHPExcel/Classes/PHPExcel/IOFactory.php');
require_once('../../include/connect.php');

//========================================//

$oTmp = new TemplateEngine();

$oDB = new DBI();

if ($bDebug) {

	$oErr = new Tracker();
	$oDB->SetTracker($oErr);
}

//========================================//

if ($_SESSION['role_action']['upload_member']['add'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$Act = $_REQUEST['act'];

$time_insert = date("Y-m-d H:i:s");

$path_import_member = $_SESSION['path_import_member'];




$where_brand = '';

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND a.brand_id = "'.$_SESSION['user_brand_id'].'"';
}


# SEARCH MAX MEMBER_BRAND_ID

	$sql_get_last_ins = 'SELECT max(member_brand_id) FROM mb_member_brand';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_member_new = $id_last_ins+1;

#######################################

# SEARCH MAX MEMBER_CUSTOM_ID

	$sql_get_last_ins = 'SELECT max(member_custom_id) FROM mb_member_brand_custom';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_custom_new = $id_last_ins+1;

#######################################


if($Act == 'save') {

	$brand_id = trim_txt($_REQUEST['bran_BrandID']);

	$card_id = trim_txt($_REQUEST['card_CardID']);

	$duplicate = trim_txt($_REQUEST['duplicate']);

	// $platform = trim_txt($_REQUEST['platform']);


	if($_FILES["member_upload"]["name"] != ""){

		$file = strtolower($_FILES["member_upload"]["name"]);

		$type= strrchr($file,".");

		if(($type==".xls")||($type==".xlsx")) {

			# UPLOAD

			$target_file = $path_import_member.basename($_FILES["member_upload"]["name"]);

			move_uploaded_file($_FILES["member_upload"]["tmp_name"], $target_file);

			$new_file_name = 'import_card_'.$card_id.$type;

			$exp_name = explode('.',$path_import_member.$new_file_name);

			$i = count($exp_name)-1;

			$type = $exp_name[$i];

			$file_name = $_FILES["member_upload"]["name"];

			copy($path_import_member.$file_name,$path_import_member.$new_file_name);

			unlink($path_import_member.$file_name);


			$inputFileName = $path_import_member.$new_file_name;  

			$inputFileType = PHPExcel_IOFactory::identify($inputFileName);  

			$objReader = PHPExcel_IOFactory::createReader($inputFileType);  

			$objReader->setReadDataOnly(true);  

			$objPHPExcel = $objReader->load($inputFileName); 


			$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);

			$highestRow = $objWorksheet->getHighestRow();

			$highestColumn = $objWorksheet->getHighestColumn();


			$headingsArray = $objWorksheet->rangeToArray('A1:'.$highestColumn.'1',null, true, true, true);

			$headingsArray = $headingsArray[1];


			$r = 0;

			$namedDataArray = array();

			$headArray = array();

			for ($row = 1; $row <= $highestRow; ++$row) {

				$dataRow = $objWorksheet->rangeToArray('A'.$row.':'.$highestColumn.$row,null, true, true, true);

				if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '')) {

				    $x = 0;

				    foreach($headingsArray as $columnKey => $columnHeading) {

					    if ($r==0) {

					        $headArray[$x] = $dataRow[$row][$columnKey]; 

					        $namedDataArray[$r][$headArray[$x]] = $dataRow[$row][$columnKey]; 

					    } else { 

					        $namedDataArray[$r][$headArray[$x]] = $dataRow[$row][$columnKey];
					    }

					    $x++;
					}

					$r++;
				}
			}


			# CHECK DATA & INSERT / UPDATE DATA

			$brand_member = 'mb_member_brand';

			$brand_custom = 'mb_member_brand_custom';

			$member_email = '';

			$member_mobile = '';

			$member_card_code = '';

			for ($i=1; $i < count($namedDataArray) ; $i++) {

				$member_sql = '';

				# MEMBER BRAND

				$field_sql = '';

				$merge_sql = '';

				$platform = 'new member';


				# RANDOM TOKEN

				$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

				$randstring = '';

				for ($z = 0; $z < 10; $z++) {

					$randstring .= $characters[rand(0, strlen($characters))];
				}

				$member_email = '';
				$member_mobile = '';
				$member_card_code = '';

				foreach ($namedDataArray[$i] as $result => $columnHeading) {

					$sql_field = 'SELECT mafi_FieldName AS name, mafi_MasterFieldID AS id
									FROM master_field 
									WHERE mafi_NameEn="'.$result.'"';

					$oRes_field = $oDB->Query($sql_field);

					$axRow_field = $oRes_field->FetchRow(DBI_ASSOC);

					if ($axRow_field['name']) {

						# EMAIL

						if ($axRow_field['name'] == 'email' && $columnHeading) { $member_email = $columnHeading; }

						# MOBILE

						if ($axRow_field['name'] == 'mobile' && $columnHeading) { 

							if (strlen($columnHeading)==10) { $member_mobile = "+66".substr($columnHeading,1); } 
							else { $member_mobile = $columnHeading; }
						}

						# MEMBER CARD CODE

						if ($axRow_field['name'] == 'member_card_code' && $columnHeading) { 

							$member_card_code = $columnHeading; 
						}


						$sql_data = 'SELECT mata_MasterTargetID
										FROM master_target 
										WHERE mafi_MasterFieldID="'.$axRow_field['id'].'"
										AND mata_NameEn LIKE "%'.$columnHeading.'%"';

						$data_target = $oDB->QueryOne($sql_data);

						if ($data_target) {

							$field_sql .= $axRow_field['name'].'="'.$data_target.'", ';

							$merge_sql .= $axRow_field['name'].'="'.$data_target.'", ';

						} else {

							# MOBILE

							if ($axRow_field['name']=='mobile' && $columnHeading) { 

								if (strlen($columnHeading)==10) { 

									$field_sql .= $axRow_field['name'].'="+66'.substr($columnHeading,1).'", ';

									$merge_sql .= $axRow_field['name'].'="+66'.substr($columnHeading,1).'", ';

								} else {

									$field_sql .= $axRow_field['name'].'="'.$columnHeading.'", ';

									$merge_sql .= $axRow_field['name'].'="'.$columnHeading.'", ';
								}

							} elseif (($axRow_field['name']=='home_country' || $axRow_field['name']=='work_country') && $columnHeading) {

								$sql_country = 'SELECT coun_CountryID 
												FROM country 
												WHERE coun_Nicename="'.$columnHeading.'"';
								$data_country = $oDB->QueryOne($sql_country);

								$field_sql .= $axRow_field['name'].'="'.$data_country.'", ';

								if ($data_country) {

									$merge_sql .= $axRow_field['name'].'="'.$data_country.'", ';
								}

							} elseif (($axRow_field['name']=='home_province' || $axRow_field['name']=='work_province') && $columnHeading) {

								$sql_province = 'SELECT prov_ProvinceID 
												FROM province 
												WHERE prov_Name="'.$columnHeading.'"';
								$data_province = $oDB->QueryOne($sql_province);

								$field_sql .= $axRow_field['name'].'="'.$data_province.'", ';

								if ($data_province) {

									$merge_sql .= $axRow_field['name'].'="'.$data_province.'", ';
								}

							} else {

								$field_sql .= $axRow_field['name'].'="'.$columnHeading.'", ';

								if ($columnHeading) {

									$merge_sql .= $axRow_field['name'].'="'.$columnHeading.'", ';
								}
							}
						}

					} else {

						# EXPIRED DATE

						if ($result == 'Expried Date') {

							if ($columnHeading) { 

								$field_sql .= 'date_expried="'.$columnHeading.'", ';

								$merge_sql .= 'date_expried="'.$columnHeading.'", ';

								$platform = 'existing member'; 
							}
						}

						if ($result == 'Collect') {

							if ($columnHeading) { 

								$field_sql .= 'point_collect="'.$columnHeading.'", ';

								$merge_sql .= 'point_collect="'.$columnHeading.'", '; 

								$platform = 'existing member';
							}
						}
					}
				}


				# CHECK EMAIL & MOBILE & MEMBER CARD CODE

				$check = '';

				$where_check = '';

				if ($member_email) { 

					$where_check = ' AND (email="'.$member_email.'"'; 

					if ($member_mobile) { $where_check .= ' OR mobile="'.$member_mobile.'"'; }
					if ($member_card_code) { $where_check .= ' OR member_card_code="'.$member_card_code.'"'; }

					$where_check .= ')';
				
				} elseif ($member_mobile) { 

					$where_check = ' AND (mobile="'.$member_mobile.'"'; 
					if ($member_card_code) { $where_check .= ' OR member_card_code="'.$member_card_code.'"'; }

					$where_check .= ')';

				} elseif ($member_card_code) { $where_check = ' AND (member_card_code="'.$member_card_code.'")'; }

				$sql_check = 'SELECT member_brand_id 
								FROM mb_member_brand 
								WHERE card_id="'.$card_id.'"
								'.$where_check;

				$check = $oDB->QueryOne($sql_check);


				# CHECK MEMBERIN MEMBER

				$check_memberin = '';

				$sql_check = 'SELECT member_id FROM mb_member WHERE';

				if ($member_email != "") {

					$sql_check .= ' email="'.$member_email.'"';

					if ($member_mobile != "") {

						$sql_check .= ' OR mobile="'.$member_mobile.'"';
					}

				} else {

					if ($member_mobile != "") {

						$sql_check .= ' mobile="'.$member_mobile.'"';
					}
				}

				$check_memberin = $oDB->QueryOne($sql_check);

				if ($check_memberin) {

					# CHECK MEMBERIN REGISTER

					$check_register = '';

					$sql_check = 'SELECT member_register_id
									FROM mb_member_register
									WHERE member_id="'.$check_memberin.'" AND card_id="'.$card_id.'"';

					$check_register = $oDB->QueryOne($sql_check);
				}

				if (!$check) {

					if ($duplicate == 'not' || $duplicate == 'merge' || $duplicate == 'replace') { # INSERT

						$member_sql .= 'INSERT INTO '.$brand_member.' SET ';

						$member_sql .= $field_sql;

						$member_sql .= 'member_brand_id="'.$id_member_new.'", ';

						$member_sql .= 'brand_id="'.$brand_id.'", ';

						$member_sql .= 'card_id="'.$card_id.'", ';

						if ($check_memberin) { $member_sql .= 'member_id="'.$check_memberin.'", '; }

						if ($check_register) { $member_sql .= 'member_register_id="'.$check_register.'", '; }

						$member_sql .= 'date_create="'.$time_insert.'", ';

						$member_sql .= 'date_update="'.$time_insert.'", ';

						$member_sql .= 'platform="'.$platform.'", ';

						$member_sql .= 'member_token="'.$id_member_new.$randstring.'"';
					}

				} else {

					if ($duplicate == 'merge') { # UPDATE MERGE

						$member_sql .= 'UPDATE '.$brand_member.' SET ';

						$member_sql .= $merge_sql;

						$member_sql .= 'date_update="'.$time_insert.'", ';

						$member_sql .= 'platform="'.$platform.'" ';

						$member_sql .= 'WHERE member_brand_id="'.$check.'"';

					} else if ($duplicate == 'replace') { # UPDATE REPLACE

						$member_sql .= 'UPDATE '.$brand_member.' SET ';

						$member_sql .= $field_sql;

						$member_sql .= 'brand_id="'.$brand_id.'", ';

						$member_sql .= 'card_id="'.$card_id.'", ';

						$member_sql .= 'platform="'.$platform.'", ';

						$member_sql .= 'date_create="'.$time_insert.'", ';

						$member_sql .= 'date_update="'.$time_insert.'" ';

						$member_sql .= 'WHERE member_brand_id="'.$check.'"';
					}
				}

				$oDB->QueryOne($member_sql);


				# CUSTOM BRAND

				foreach ($namedDataArray[$i] as $result => $columnHeading) {

					$custom_sql = '';

					$sql_field = 'SELECT cufi_FieldName AS name, 
										cufi_CustomFieldID AS id 
										FROM custom_field 
										WHERE cufi_Name="'.$result.'"';

					$oRes_field = $oDB->Query($sql_field);

					$axRow_field = $oRes_field->FetchRow(DBI_ASSOC);

					if ($axRow_field['name']) { 

						if (!$check) {

							if ($duplicate == 'not' || $duplicate == 'merge' || $duplicate == 'replace') { # INSERT

								$custom_sql .= 'INSERT INTO '.$brand_custom.' SET ';

								$custom_sql .= 'member_custom_id="'.$id_custom_new.'", ';

								$custom_sql .= 'member_brand_id="'.$id_member_new.'", ';

								$custom_sql .= 'cufi_CustomFieldID="'.$axRow_field['id'].'", ';

								$custom_sql .= 'reda_CreatedDate="'.$time_insert.'", ';

								$custom_sql .= 'reda_UpdatedDate="'.$time_insert.'", ';

								$sql_data = 'SELECT clva_CustomListValueID
												FROM custom_list_value 
												WHERE cufi_CustomFieldID="'.$axRow_field['id'].'"
												AND clva_NameEn LIKE "%'.$columnHeading.'%"';

								$data_target = $oDB->QueryOne($sql_data);

								if ($data_target) {

									$custom_sql .= 'reda_Value="'.$data_target.'"'; 

								} else {

									$custom_sql .= 'reda_Value="'.$columnHeading.'"'; 
								}

								$id_custom_new++;
							}

						} else {

							if ($duplicate == 'merge') { # UPDATE MERGE

								$custom_sql .= 'UPDATE '.$brand_custom.' SET ';

								$custom_sql .= 'member_custom_id="'.$id_custom_new.'", ';

								$custom_sql .= 'member_brand_id="'.$id_member_new.'", ';

								$custom_sql .= 'reda_UpdatedDate="'.$time_insert.'", ';


								$sql_data = 'SELECT clva_CustomListValueID
												FROM custom_list_value 
												WHERE cufi_CustomFieldID="'.$axRow_field['id'].'"
												AND clva_NameEn LIKE "%'.$columnHeading.'%"';

								$data_target = $oDB->QueryOne($sql_data);

								if ($data_target) {

									$custom_sql .= 'reda_Value="'.$data_target.'" '; 

								} else {

									if ($columnHeading) {

										$custom_sql .= 'reda_Value="'.$columnHeading.'" '; 
									}
								}

								$custom_sql .= 'WHERE member_brand_id="'.$check.'" ';

								$custom_sql .= 'AND cufi_CustomFieldID="'.$axRow_field['id'].'" ';

								$id_custom_new++;

							} else if ($duplicate == 'replace') {

								$custom_sql .= "UPDATE ".$brand_custom." SET ";

								$custom_sql .= "member_custom_id='".$id_custom_new."', ";

								$custom_sql .= "reda_CreatedDate='".$time_insert."', ";

								$custom_sql .= "reda_UpdatedDate='".$time_insert."', ";

								$sql_data = 'SELECT clva_CustomListValueID
												FROM custom_list_value 
												WHERE cufi_CustomFieldID="'.$axRow_field['id'].'"
												AND clva_NameEn LIKE "%'.$columnHeading.'%"';

								$data_target = $oDB->QueryOne($sql_data);

								if ($data_target) {

									$custom_sql .= 'reda_Value="'.$data_target.'" '; 

								} else {

									$custom_sql .= 'reda_Value="'.$columnHeading.'" '; 
								}

								$custom_sql .= 'WHERE member_brand_id="'.$check.'" ';

								$custom_sql .= 'AND cufi_CustomFieldID="'.$axRow_field['id'].'" ';

								$id_custom_new++;
							}
						}
					}

					if ($custom_sql) { $oDB->QueryOne($custom_sql); }
				}

				$id_member_new++;
			}

			echo '<script>window.location.href="upload_member.php";</script>';
			exit();
		}
	}
}




#  brand dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' and brand_id="'.$_SESSION['user_brand_id'].'" ';
}

$as_brand_id = dropdownlist_from_table($oDB,'mi_brand','brand_id','name','brand_id>0'.$where_brand,' ORDER BY name ASC');

$oTmp->assign('brand_id_opt', $as_brand_id);



# card dropdownlist

$as_card = dropdownlist_from_table($oDB,'mi_card','card_id','name','flag_status="1"'.$where_brand,' ORDER BY name ASC');

$oTmp->assign('card_opt', $as_card);



$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('type_file_upload', '<span class="text-rq">Type file : .xls , .xlsx only</span>');

$oTmp->assign('is_menu', 'is_upload_member');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_membership', 'in');

$oTmp->assign('content_file', 'card/import_member.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
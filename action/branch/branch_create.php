<?phpheader('Content-Type:text/html; charset=UTF-8');//========================================//ini_set("display_errors",1);error_reporting(1);//========================================//include('../../include/common.php');include('../../lib/function_normal.php');include('../../include/common_check.php');include('../../lib/phpqrcode/qrlib.php');require_once('../../include/connect.php');//========================================//$oTmp = new TemplateEngine();$oDB = new DBI();if ($bDebug) {	$oErr = new Tracker();	$oDB->SetTracker($oErr);}//========================================//if (($_SESSION['role_action']['branch']['add'] != 1) || ($_SESSION['role_action']['branch']['edit'] != 1)) {	echo "<script> history.back(); </script>";	exit();}//========================================//$Act = $_REQUEST['act'];$id = $_REQUEST['id'];$mobile_format = array('3','7');$phone_format = array('3','6');$time_insert = date("Y-m-d H:i:s");# SEARCH MAX BRANCH_ID	$sql_get_last_ins = 'SELECT max(branch_id) FROM mi_branch';	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);	$id_new = $id_last_ins+1;#######################################	if( $Act == 'edit' && $id != '' ){		# EDIT		$sql = '';		$sql .= 'SELECT a.*,						b.name AS brand_name										FROM mi_branch AS a				LEFT JOIN mi_brand AS b				ON a.brand_id = b.brand_id				WHERE a.branch_id = "'.$id.'"';		$oRes = $oDB->Query($sql);		$i=0;		$asData = array();		$data_table = '';		while ($axRow = $oRes->FetchRow(DBI_ASSOC)){			$i++;			$last_val = 0;			for($d=0;$d<count($mobile_format);$d++){				$axRow['mobile_'.($d+1)] = substr($axRow['mobile'],$last_val,$mobile_format[$d]);				$last_val += $mobile_format[$d];			}			$last_val_2 = 0;			for($d=0;$d<count($phone_format);$d++){				$axRow['phone_'.($d+1)] = substr($axRow['phone'],$last_val_2,$phone_format[$d]);				$last_val_2 += $phone_format[$d];			}			$asData = $axRow;		}	} else if( $Act == 'save' ){		# SAVE		$id = trim_txt($_REQUEST['id']);		$brand_id = trim_txt($_REQUEST['brand_id']);		$branch_name = trim_txt($_REQUEST['branch_name']);		$description = trim_txt($_REQUEST['description']);		$map_latitude = trim_txt($_REQUEST['map_latitude']);		$map_longitude = trim_txt($_REQUEST['map_longitude']);		$how_to_get_there = trim_txt($_REQUEST['how_to_get_there']);		$facebook_place_id = trim_txt($_REQUEST['facebook_place_id']);		$operation_time = trim_txt($_REQUEST['operation_time']);		$signature_info = trim_txt($_REQUEST['signature_info']);		$max_customer = trim_txt($_REQUEST['max_customer']);		$email = trim_txt($_REQUEST['email']);		$default_status = trim_txt($_REQUEST['default_status']);		$address = trim_txt($_REQUEST['address']);		$moo = trim_txt($_REQUEST['moo']);		$junction = trim_txt($_REQUEST['junction']);		$soi = trim_txt($_REQUEST['soi']);		$road = trim_txt($_REQUEST['road']);		$sub_district = trim_txt($_REQUEST['sub_district']);		$district = trim_txt($_REQUEST['district']);		$province_id = trim_txt($_REQUEST['province_id']);		$landmark_id = trim_txt($_REQUEST['landmark_id']);		$landmark_floor = trim_txt($_REQUEST['landmark_floor']);		$country_id = trim_txt($_REQUEST['country_id']);		$postcode = trim_txt($_REQUEST['postcode']);		$fax = trim_txt($_REQUEST['fax']);		$flag_credit_use = trim_txt($_REQUEST['flag_credit_use']);		$flag_reserve = trim_txt($_REQUEST['flag_reserve']);		$flag_parking_area = trim_txt($_REQUEST['flag_parking_area']);		$flag_children = trim_txt($_REQUEST['flag_children']);		$flag_status = trim_txt($_REQUEST['flag_status']);		$date_status = trim_txt($_REQUEST['AutoDate']);		$flag_hidden = trim_txt($_REQUEST['flag_hidden']);		for($d=1;$d<=count($mobile_format);$d++){			$mobile .= trim_txt($_REQUEST['mobile_'.$d]);		}		for($d=1;$d<=count($phone_format);$d++){			$phone .= trim_txt($_REQUEST['phone_'.$d]);		}		$sql_branch = '';		if($branch_name){	$sql_branch .= 'name="'.$branch_name.'"';   }		if($brand_id){	$sql_branch .= ',brand_id="'.$brand_id.'"';   }		if($map_latitude){	$sql_branch .= ',map_latitude="'.$map_latitude.'"';   }		if($map_longitude){	$sql_branch .= ',map_longitude="'.$map_longitude.'"';   }		$sql_branch .= ',max_customer="'.$max_customer.'"';   		$sql_branch .= ',phone="'.$phone.'"';   		$sql_branch .= ',mobile="'.$mobile.'"';   		$sql_branch .= ',fax="'.$fax.'"';   		if($email){	$sql_branch .= ',email="'.$email.'"';   }		if($flag_credit_use){	$sql_branch .= ',flag_credit_use="'.$flag_credit_use.'"';   }		if($flag_reserve){	$sql_branch .= ',flag_reserve="'.$flag_reserve.'"';   }		if($flag_parking_area){	$sql_branch .= ',flag_parking_area="'.$flag_parking_area.'"';   }		if($flag_children){	$sql_branch .= ',flag_children="'.$flag_children.'"';   }		if($flag_status){	$sql_branch .= ',flag_status="'.$flag_status.'"';   }		if($time_insert){	$sql_branch .= ',date_update="'.$time_insert.'"';   }		if($default_status){	$sql_branch .= ',default_status="'.$default_status.'"';   }		if($address){	$sql_branch .= ',address_no="'.$address.'"';   }		$sql_branch .= ',moo="'.$moo.'"';   		$sql_branch .= ',junction="'.$junction.'"';   		$sql_branch .= ',soi="'.$soi.'"';   		if($road){	$sql_branch .= ',road="'.$road.'"';   }		if($sub_district){	$sql_branch .= ',sub_district_id="'.$sub_district.'"';   }		if($district){	$sql_branch .= ',district_id="'.$district.'"';   }		if($province_id){	$sql_branch .= ',province_id="'.$province_id.'"';   }		if($country_id){	$sql_branch .= ',country_id="'.$country_id.'"';   }		$sql_branch .= ',landmark_id="'.$landmark_id.'"';  		$sql_branch .= ',landmark_floor="'.$landmark_floor.'"';  		$sql_branch .= ',postcode="'.$postcode.'"';   		$sql_branch .= ',description="'.$description.'"';		$sql_branch .= ',operation_time="'.$operation_time.'"'; 		$sql_branch .= ',how_to_get_there="'.$how_to_get_there.'"';		$sql_branch .= ',facebook_place_id="'.$facebook_place_id.'"'; 		$sql_branch .= ',signature_info="'.$signature_info.'"';		$sql_branch .= ',date_status="'.$date_status.'"';		if($flag_hidden){	$sql_branch .= ',flag_hidden="'.$flag_hidden.'"';   }		else {	$sql_branch .= ',flag_hidden="No"';	}				if($id!='' && $id>0){			# UPDATE			$do_sql_branch = "UPDATE mi_branch SET ".$sql_branch." WHERE branch_id= '".$id."'";			$oDB->QueryOne($do_sql_branch);		} else {			# QR CODE			$qr_code_text = "BRN-".str_pad($id_new,4,"0",STR_PAD_LEFT);			$errorCorrectionLevel = 'H'; 			$matrixPointSize = 10;					$qr_code_image = $qr_code_text.'.png';			$file_full_path = '../../upload/'.$brand_id.'/qr_branch_upload/'.$qr_code_image;			QRcode::png($qr_code_text, $file_full_path, $errorCorrectionLevel, $matrixPointSize, 2);			$sql_branch .= ',qr_code_image="'.$qr_code_image.'",path_qr="'.$brand_id.'/qr_branch_upload/"';						# INSERT			if($time_insert){	$sql_branch .= ',date_create="'.$time_insert.'"';   }			if($id_new){	$sql_branch .= ',branch_id="'.$id_new.'"';   }			$do_sql_branch = 'INSERT INTO mi_branch SET '.$sql_branch;			$oDB->QueryOne($do_sql_branch);			$id = $id_new;			# HEADQUARTERS			$do_sql_head = 'SELECT branch_id FROM mi_branch WHERE brand_id='.$brand_id.' AND branch_id!='.$id;			$head = $oDB->QueryOne($do_sql_head);			if (!$head) {				$do_sql_branch = "UPDATE mi_branch SET default_status='1' WHERE branch_id= '".$id."'";				$oDB->QueryOne($do_sql_branch);			}		}		# HEADQUARTERS		if ($default_status==1) {			$do_sql_default = 'UPDATE mi_branch SET default_status=2 WHERE branch_id!="'.$id.'" AND brand_id="'.$brand_id.'"';			$oDB->QueryOne($do_sql_default);		}		echo '<script>window.location.href="branch.php";</script>';		exit;	}#  country dropdownlist$as_country = dropdownlist_from_table($oDB,'country','coun_CountryID','coun_Nicename','coun_Deleted!="1"');$oTmp->assign('country_opt', $as_country);if ($asData['country_id']==0) { 	$asData['country_id'] = 211; 	$asData['province_id'] = 1; }#  region dropdownlist$as_region = dropdownlist_from_table($oDB,'region','regi_RegionID','regi_Name','regi_Deleted!="T"');$oTmp->assign('region_opt', $as_region);#  province dropdownlist$as_province = dropdownlist_from_table($oDB,'province','prov_ProvinceID','prov_Name','prov_Deleted!="T"','ORDER BY CONVERT (prov_Name USING tis620)');$oTmp->assign('province_opt', $as_province);#  brand dropdownlistif($_SESSION['user_type_id_ses']>1){	$where_brand = ' brand_id="'.$_SESSION['user_brand_id'].'" ';}$as_brand_id = dropdownlist_from_table($oDB,'mi_brand','brand_id','name',$where_brand,' ORDER BY name ASC');$oTmp->assign('brand_id_opt', $as_brand_id);$oTmp->assign('data', $asData);$oTmp->assign('act', 'save');$oTmp->assign('is_menu', 'is_branch');$oTmp->assign('content_file', 'branch/branch_create.htm');$oTmp->display('layout/template.html');//========================================//$oDB->Close();if ($bDebug) {	echo($oErr->GetAll());}//========================================//?>
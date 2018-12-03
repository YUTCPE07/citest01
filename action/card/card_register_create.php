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
require_once('../../include/connect.php');

//========================================//


$oTmp = new TemplateEngine();
$oDB = new DBI();

if ($bDebug) {

	$oErr = new Tracker();
	$oDB->SetTracker($oErr);
}

//========================================//

if (($_SESSION['role_action']['card_register']['add'] != 1) || ($_SESSION['role_action']['card_register']['edit'] != 1)) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];



# SEARCH MAX CARD_REGISTER_ID

	$sql_get_last_ins = 'SELECT max(card_register_id) FROM mi_card_register';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################



$where_brand = '';

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND mi_card.brand_id = "'.$_SESSION['user_brand_id'].'"';
}

$where_branch = '';

if($_SESSION['user_type_id_ses']>2){

	$where_branch = ' AND branch_id = "'.$_SESSION['user_branch_id'].'"';
}



# CHECK CARD

$sqlcheck = '';

$sqlcheck = 'SELECT DISTINCT mi_card.*,
				mi_brand.name AS brand_name,
				mi_card_type.name AS card_type_name
				FROM mi_card
				LEFT JOIN mi_brand
				ON mi_brand.brand_id = mi_card.brand_id
				LEFT JOIN mi_card_type
				ON mi_card_type.card_type_id = mi_card.card_type_id
				WHERE mi_card.card_id = "'.$id.'"
				'.$where_brand.'';

$oRes = $oDB->Query($sqlcheck)or die(mysql_error());

$asCard = array();

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$axRow['date_expired'] = DateOnly($axRow['date_expired']);

	if ($axRow['period_type']==2) { $axRow['period_type_other'] = $axRow['period_type_other'].' Months'; }
	if ($axRow['period_type']==3) { $axRow['period_type_other'] = $axRow['period_type_other'].' Years'; }
	if ($axRow['period_type']==4) { $axRow['period_type_other'] = 'Member Life Time'; }

	if ($axRow['description']=="" || !$axRow['description']) { $axRow['description']="-"; }
	else { $axRow['description'] = nl2br($axRow['description']); }

	$axRow['member_fee'] = number_format($axRow['member_fee'],2).' Baht.';

	$asCard = $axRow;
}



# SEARCH 

	$search_privilege = $_POST["txt_search_privilege"];

	$search_branch = $_POST["txt_search_branch"];

	$sql_brand_id = 'SELECT brand_id FROM mi_card WHERE card_id = "'.$id.'"';

	$brand_id = $oDB->QueryOne($sql_brand_id);

	$sql_branch = 'SELECT name as txt,
						branch_id as id 
						FROM mi_branch 
						WHERE name LIKE "%'.$search_branch.'%" 
						AND brand_id = "'.$brand_id.'" 
						'.$where_branch.'';

	$sql_privilege = 'SELECT priv_Name as txt,
							priv_PrivilegeID as id,
							priv_Status,
							priv_Motivation,
							priv_MotivationID
							FROM privilege 
							WHERE bran_BrandID = "'.$brand_id.'" 
							AND priv_Name LIKE "%'.$search_privilege.'%" 
							AND priv_Deleted!="T"';

	$sql_coupon = '	SELECT coup_Name as txt, 
							coup_CouponID as id, 
							coup_Status 
							FROM coupon 
							WHERE bran_BrandID = "'.$brand_id.'" 
							AND coup_Name LIKE "%'.$search_privilege.'%" 
							AND coup_Deleted!="T" 
							AND coup_Birthday!="T"';

	$sql_hbd = 'SELECT coup_Name as txt, 
						coup_CouponID as id, 
						coup_Status 
						FROM coupon 
						WHERE bran_BrandID = "'.$brand_id.'" 
						AND coup_Name LIKE "%'.$search_privilege.'%" 
						AND coup_Deleted!="T" 
						AND coup_Birthday="T"';


	$sql_activity 	= 'SELECT acti_Name as txt, 
						acti_ActivityID as id, 
						acti_Status 
						FROM activity 
						WHERE bran_BrandID = "'.$brand_id.'" 
						AND acti_Name LIKE "%'.$search_privilege.'%" 
						AND acti_Deleted!="T"';

	$check_priv = $oDB->QueryOne($sql_privilege);

	$check_coup = $oDB->QueryOne($sql_coupon);

	$check_hbd = $oDB->QueryOne($sql_hbd);

	$check_acti = $oDB->QueryOne($sql_activity);


	$loops_txt = array();


	for($l=0;$l<5;$l++){

		if($l==0){	$loops_txt[$l]['sql'] = $sql_branch;

					$loops_txt[$l]['column_check'] = 'branch_id';

					$loops_txt[$l]['url_view'] = 'branch/branch_create';		}

		if($l==1){	$loops_txt[$l]['sql'] = $sql_privilege;

					$loops_txt[$l]['column_check'] = 'priv_PrivilegeID';

					$loops_txt[$l]['url_view'] = 'privilege/privilege_create';	}

		if($l==2){	$loops_txt[$l]['sql'] = $sql_coupon;

					$loops_txt[$l]['column_check'] = 'coup_CouponID';

					$loops_txt[$l]['url_view'] = 'coupon/coupon_create';	}

		if($l==3){	$loops_txt[$l]['sql'] = $sql_hbd;

					$loops_txt[$l]['column_check'] = 'coup_CouponID';

					$loops_txt[$l]['url_view'] = 'coupon/coupon_create';	}

		if($l==4){	$loops_txt[$l]['sql'] = $sql_activity;

					$loops_txt[$l]['column_check'] = 'acti_ActivityID';

					$loops_txt[$l]['url_view'] = 'activity/activity_create';	}

	} 




	if( $Act == 'edit' && $id != '' ||  $_POST["action"] == "Search" ){

		# EDIT

		$sql = '';

		$sql .= 'SELECT * FROM mi_card WHERE card_id = "'.$id.'" '.$where_brand.'';

		$search_privilege = $_POST["txt_search_privilege"];

		$search_branch = $_POST["txt_search_branch"];

		$oRes = $oDB->Query($sql);



		$asData = array();

		$table_coupon = '';

		$table_activity = '';

		while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

			$asData = $axRow;

		}

		## LOOP PRIVILEGE ##

		for($loops=0;$loops<count($loops_txt);$loops++){

			$oRes = $oDB->Query($loops_txt[$loops]['sql']);

			$i=0;


			if ($check_priv) {

				$oRes_branch = $oDB->Query($sql_branch);

				$oRes_privilege = $oDB->Query($sql_privilege);
									
				$privilege_id = array();

				$a=0;

				$table_privilege = "

					<table id='myTable' class='table table-bordered' style='background-color:white;'>
						<tr><thead style='background-color:#003369;color:#FFF'>
							<td>Branch \ Privilege</td>";


				while ($axRow_privilege = $oRes_privilege->FetchRow(DBI_ASSOC)) {

					if ($axRow_privilege['priv_Status'] == "Pending") { $status_priv = "#f0ad4e"; } 
					else { $status_priv = "#5cb85c"; }


				# MOTIVATION

				$motivation_plan = '';

				if ($axRow_privilege['priv_Motivation'] == 'Point') { 

					$icon_sql = "SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID = 3";
					$icon = $oDB->QueryOne($icon_sql);
					$icon ='<img src="../../upload/collection_upload/'.$icon.'" width="12px" height="12px" style="margin-bottom:3px">';

					$plan_sql = "SELECT mopp_Name, mopp_PointQty, mopp_UseAmount FROM motivation_plan_point WHERE mopp_MotivationPointID='".$axRow_privilege['priv_MotivationID']."'";
					$get_point = $oDB->Query($plan_sql);
					$point = $get_point->FetchRow(DBI_ASSOC);

					$motivation_plan = '<br>'.$point['mopp_Name'].'<br>('.number_format($point['mopp_UseAmount'],2).' ฿ / '.$point['mopp_PointQty'].' '.$icon.')';

				} else if ($axRow_privilege['priv_Motivation'] == 'Stamp') {

					$plan_sql = "SELECT mops_Name, mops_StampQty, mops_CollectionTypeID FROM motivation_plan_stamp WHERE mops_MotivationStampID='".$axRow_privilege['priv_MotivationID']."'";
					$get_stamp = $oDB->Query($plan_sql);
					$stamp = $get_stamp->FetchRow(DBI_ASSOC);

					$icon_sql = "SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID = ".$stamp['mops_CollectionTypeID'];
					$icon = $oDB->QueryOne($icon_sql);
					$icon ='<img src="../../upload/collection_upload/'.$icon.'" width="12px" height="12px" style="margin-bottom:3px">';

					$motivation_plan = '<br>'.$stamp['mops_Name'].'<br>(1 Times / '.$stamp['mops_StampQty'].' '.$icon.')';
				} 

				$table_privilege .= "

						<td style='text-align:center'>".$axRow_privilege['txt']." <span class='glyphicon glyphicon-certificate' style='color:".$status_priv.";'></span>".$motivation_plan."<br>

							<button type='button' class='btn btn-default btn-sm' id='".$axRow_privilege['id']."' onclick='all_priv(this.id)'>
								<span class='glyphicon glyphicon-check' aria-hidden='true'></span>
							</button>

							<button type='button' class='btn btn-default btn-sm' id='".$axRow_privilege['id']."' onclick='unall_priv(this.id)'>
								<span class='glyphicon glyphicon-unchecked' aria-hidden='true'></span>
							</button></td>";

				$privilege_id[$a]  = $axRow_privilege['id'];

				$a++;
			}				
													
			$table_privilege .= "</thead></tr><tr><tbody>";


			while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)) {

				$table_privilege .= "

						<tr><td class='td_head'>".$axRow_branch['txt']."
							<span style='float:right'>
								<button type='button' class='btn btn-default btn-sm' id='".$axRow_branch['id']."' onclick='all_brnc_priv(this.id)'>
									<span class='glyphicon glyphicon-check' aria-hidden='true'></span>
								</button>	
								<button type='button' class='btn btn-default btn-sm' id='".$axRow_branch['id']."' onclick='unall_brnc_priv(this.id)'>
									<span class='glyphicon glyphicon-unchecked' aria-hidden='true'></span>
								</button>
							</span></td>";

				for ($i=0; $i < $a; $i++) {

					$sql_check_register_id = 'SELECT qrcode_privileges_image
													FROM mi_card_register
													WHERE card_id = "'.$id.'"
													AND branch_id = '.$axRow_branch['id'].'
													AND status = "0"
													AND privilege_id = '.$privilege_id[$i].'';
																
					$check_register_id = $oDB->QueryOne($sql_check_register_id);

					## CHECK BOX

					$table_privilege .= "

							<td style='text-align:center' >
								<input type='checkbox' class='p".$privilege_id[$i]." bp".$axRow_branch['id']."' id='check_privilege".$axRow_branch['id']."_".$privilege_id[$i]."' name='check_privilege".$axRow_branch['id']."_".$privilege_id[$i]."' value='1'";

						if ($check_register_id) {

							$table_privilege .= " checked='checked'><br>
													<a target='_blank' href='card_register_qrcode.php?qrcode=".$check_register_id."' >QRCode Link<a/>
													</td>";

						} else {

							$table_privilege .= "></td>";
						}
				}
															
				$table_privilege .= "</tr>";								
			}

			$table_privilege .="</table>";

			$oTmp->assign('table_privilege', $table_privilege);
		}
	}



	## LOOP COUPON ##

	for($loops=0;$loops<count($loops_txt);$loops++){

		$oRes = $oDB->Query($loops_txt[$loops]['sql']);

		$i=0;

		if ($check_coup) {

			$oRes_branch = $oDB->Query($sql_branch);

			$oRes_coupon = $oDB->Query($sql_coupon);
									
			$coupon_id = array();

			$a=0;

			$table_coupon = "

					<table id='myTable' class='table table-bordered' style='background-color:white;'>
						<tr><thead style='background-color:#003369;color:#FFF'>
							<td>Branch \ Coupon</td>";

			while ($axRow_coupon = $oRes_coupon->FetchRow(DBI_ASSOC)) {

				if ($axRow_coupon['coup_Status'] == "Pending") { $status_priv = "#f0ad4e"; } 
				else { $status_priv = "#5cb85c"; }

				$table_coupon .= "

							<td style='text-align:center'>".$axRow_coupon['txt']." <span class='glyphicon glyphicon-certificate' style='color:".$status_priv.";'></span><br>
								<button type='button' class='btn btn-default btn-sm' id='".$axRow_coupon['id']."' onclick='all_coup(this.id)'>
									<span class='glyphicon glyphicon-check' aria-hidden='true'></span>
								</button>
								<button type='button' class='btn btn-default btn-sm' id='".$axRow_coupon['id']."' onclick='unall_coup(this.id)'>
									<span class='glyphicon glyphicon-unchecked' aria-hidden='true'></span>
								</button></td>";

				$coupon_id[$a]  = $axRow_coupon['id'];

				$a++;
			}
															
			$table_coupon .= "</thead></tr><tr><tbody>";

			while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)) {

				$table_coupon .= "<tr><td class='td_head'>".$axRow_branch['txt']."
										<span style='float:right'>
											<button type='button' class='btn btn-default btn-sm' id='".$axRow_branch['id']."' onclick='all_brnc_coup(this.id)'>
												<span class='glyphicon glyphicon-check' aria-hidden='true'></span>
											</button>
											<button type='button' class='btn btn-default btn-sm' id='".$axRow_branch['id']."' onclick='unall_brnc_coup(this.id)'>
												<span class='glyphicon glyphicon-unchecked' aria-hidden='true'></span>
											</button>
										</span></td>";

				for ($i=0; $i < $a; $i++) {

					$sql_check_register_id = 'SELECT qrcode_privileges_image
													FROM mi_card_register
													WHERE card_id = "'.$id.'"
													AND branch_id = '.$axRow_branch['id'].'
													AND status = "0"
													AND coupon_id = '.$coupon_id[$i].'';
																
					$check_register_id = $oDB->QueryOne($sql_check_register_id);

					## CHECK BOX

					$table_coupon .= "<td class='td_coupon".$coupon_id[$i]." td_branch".$axRow_branch['id']."' style='text-align:center' ><input type='checkbox' class='c".$coupon_id[$i]." bc".$axRow_branch['id']."' id='check_coupon".$axRow_branch['id']."_".$coupon_id[$i]."' name='check_coupon".$axRow_branch['id']."_".$coupon_id[$i]."' value='1'";

					if ($check_register_id) {

						$table_coupon .= " checked='checked'><br>
												<a target='_blank' href='card_register_qrcode.php?qrcode=".$check_register_id."' >QRCode Link<a/>
												</td>";

					} else {

						$table_coupon .= "></td>";
					}
				}
															
				$table_coupon .= "</tr>";							
			}

			$table_coupon .="</table>";

			$oTmp->assign('table_coupon', $table_coupon);
		}
	}



	## LOOP HBD ##

	for($loops=0;$loops<count($loops_txt);$loops++){

		$oRes = $oDB->Query($loops_txt[$loops]['sql']);

		$i=0;

		if ($check_hbd) {

			$oRes_branch = $oDB->Query($sql_branch);

			$oRes_hbd = $oDB->Query($sql_hbd);
									
			$hbd_id = array();

			$a=0;

			$table_hbd = "

					<table id='myTable' class='table table-bordered' style='background-color:white;'>
						<tr><thead style='background-color:#003369;color:#FFF'>
							<td>Branch \ Brithday Coupon</td>";

			while ($axRow_hbd = $oRes_hbd->FetchRow(DBI_ASSOC)) {

				if ($axRow_coupon['coup_Status'] == "Pending") { $status_priv = "#f0ad4e"; } 
				else { $status_priv = "#5cb85c"; }

				$table_hbd .= "

							<td style='text-align:center'>".$axRow_hbd['txt']." <span class='glyphicon glyphicon-certificate' style='color:".$status_priv.";'></span><br>
								<button type='button' class='btn btn-default btn-sm' id='".$axRow_hbd['id']."' onclick='all_hbd(this.id)'>
									<span class='glyphicon glyphicon-check' aria-hidden='true'></span>
								</button>
								<button type='button' class='btn btn-default btn-sm' id='".$axRow_hbd['id']."' onclick='unall_hbd(this.id)'>
									<span class='glyphicon glyphicon-unchecked' aria-hidden='true'></span>
								</button></td>";

				$hbd_id[$a]  = $axRow_hbd['id'];

				$a++;
			}
															
			$table_hbd .= "</thead></tr><tr><tbody>";

			while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)) {

				$table_hbd .= "<tr><td class='td_head'>".$axRow_branch['txt']."
										<span style='float:right'>
											<button type='button' class='btn btn-default btn-sm' id='".$axRow_branch['id']."' onclick='all_brnc_hbd(this.id)'>
												<span class='glyphicon glyphicon-check' aria-hidden='true'></span>
											</button>
											<button type='button' class='btn btn-default btn-sm' id='".$axRow_branch['id']."' onclick='unall_brnc_hbd(this.id)'>
												<span class='glyphicon glyphicon-unchecked' aria-hidden='true'></span>
											</button>
										</span></td>";

				for ($i=0; $i < $a; $i++) {

					$sql_check_register_id = 'SELECT qrcode_privileges_image
													FROM mi_card_register
													WHERE card_id = "'.$id.'"
													AND branch_id = '.$axRow_branch['id'].'
													AND status = "0"
													AND coupon_id = '.$hbd_id[$i].'';
																
					$check_register_id = $oDB->QueryOne($sql_check_register_id);

					## CHECK BOX

					$table_hbd .= "<td class='td_hbd".$hbd_id[$i]." td_branch".$axRow_branch['id']."' style='text-align:center' ><input type='checkbox' class='h".$hbd_id[$i]." bh".$axRow_branch['id']."' id='check_hbd".$axRow_branch['id']."_".$hbd_id[$i]."' name='check_hbd".$axRow_branch['id']."_".$hbd_id[$i]."' value='1'";

					if ($check_register_id) {

						$table_hbd .= " checked='checked'><br>
											<a target='_blank' href='card_register_qrcode.php?qrcode=".$check_register_id."' >QRCode Link<a/>
											</td>";

					} else {

						$table_hbd .= "></td>";
					}
				}
															
				$table_hbd .= "</tr>";								
			}

			$table_hbd .="</table>";

			$oTmp->assign('table_hbd', $table_hbd);
		}
	}



	## LOOP ACTIVITY ##

	for($loops=0;$loops<count($loops_txt);$loops++){

		$oRes = $oDB->Query($loops_txt[$loops]['sql']);

		$i=0;

		if ($check_acti) {

			$oRes_branch = $oDB->Query($sql_branch);

			$oRes_activity = $oDB->Query($sql_activity);
									
			$activity_id = array();

			$a=0;

			$table_activity = "

					<table id='myTable' class='table table-bordered' style='background-color:white;'>
						<tr><thead style='background-color:#003369;color:#FFF'>
							<td>Branch \ Activity</td>";

			while ($axRow_activity = $oRes_activity->FetchRow(DBI_ASSOC)) {

				if ($axRow_activity['acti_Status'] == "Pending") { $status_priv = "#f0ad4e"; } 
				else { $status_priv = "#5cb85c"; }

				$table_activity .= "

							<td style='text-align:center'>".$axRow_activity['txt']." <span class='glyphicon glyphicon-certificate' style='color:".$status_priv.";'></span><br>
								<button type='button' class='btn btn-default btn-sm' id='".$axRow_activity['id']."' onclick='all_acti(this.id)'>
									<span class='glyphicon glyphicon-check' aria-hidden='true'></span>
								</button>
								<button type='button' class='btn btn-default btn-sm' id='".$axRow_activity['id']."' onclick='unall_acti(this.id)'>
									<span class='glyphicon glyphicon-unchecked' aria-hidden='true'></span>
								</button></td>";

				$activity_id[$a]  = $axRow_activity['id'];

				$a++;
			}			
													
			$table_activity .= "</thead></tr><tr><tbody>";

			while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)) {

				$table_activity .= "<tr><td class='td_head'>".$axRow_branch['txt']."
										<span style='float:right'>
											<button type='button' class='btn btn-default btn-sm' id='".$axRow_branch['id']."' onclick='all_brnc_acti(this.id)'>
													<span class='glyphicon glyphicon-check' aria-hidden='true'></span>
											</button>
											<button type='button' class='btn btn-default btn-sm' id='".$axRow_branch['id']."' onclick='unall_brnc_acti(this.id)'>
													<span class='glyphicon glyphicon-unchecked' aria-hidden='true'></span>
											</button>	
										</span></td>";

				for ($i=0; $i < $a; $i++) {

					$sql_check_register_id = 'SELECT qrcode_privileges_image
													FROM mi_card_register
													WHERE card_id = "'.$id.'"
													AND branch_id = '.$axRow_branch['id'].'
													AND status = "0"
													AND activity_id = '.$activity_id[$i].'';
																
					$check_register_id = $oDB->QueryOne($sql_check_register_id);

					## CHECK BOX

					$table_activity .= "<td class='td_activity".$activity_id[$i]." td_branch".$axRow_branch['id']."' style='text-align:center' ><input type='checkbox' class='a".$activity_id[$i]." ba".$axRow_branch['id']."' id='check_activity".$axRow_branch['id']."_".$activity_id[$i]."' name='check_activity".$axRow_branch['id']."_".$activity_id[$i]."' value='1'";

					if ($check_register_id) {

						$table_activity .= " checked='checked'><br>
													<a target='_blank' href='card_register_qrcode.php?qrcode=".$check_register_id."' >QRCode Link<a/>
													</td>";
					} else {

						$table_activity .= "></td>";
					}
				}
															
				$table_activity .= "</tr>";								
			}

			$table_activity .="</table>";

			$oTmp->assign('table_activity', $table_activity);
		}
	}


} else if( $Act == 'save'){

	# SAVE

	$id = trim_txt($_REQUEST['id']);

	$oRes_privilege = $oDB->Query($sql_privilege);
		
	$privilege_id = array();

	$a=0;

	while ($axRow_privilege = $oRes_privilege->FetchRow(DBI_ASSOC)) {

		$privilege_id[$a]  = $axRow_privilege['id'];

		$a++;
	}

	$oRes_coupon = $oDB->Query($sql_coupon);
		
	$coupon_id = array();

	$b=0;

	while ($axRow_coupon = $oRes_coupon->FetchRow(DBI_ASSOC)) {

		$coupon_id[$b]  = $axRow_coupon['id'];

		$b++;
	}

	$oRes_hbd = $oDB->Query($sql_hbd);
		
	$hbd_id = array();

	$d=0;

	while ($axRow_hbd = $oRes_hbd->FetchRow(DBI_ASSOC)) {

		$hbd_id[$d]  = $axRow_hbd['id'];

		$d++;
	}

	$oRes_activity = $oDB->Query($sql_activity);
		
	$activity_id = array();

	$c=0;

	while ($axRow_activity = $oRes_activity->FetchRow(DBI_ASSOC)) {

		$activity_id[$c]  = $axRow_activity['id'];

		$c++;
	}
		
	$oRes_branch = $oDB->Query($sql_branch);

	while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)) {

		## SAVE PRIVILEGE ##

		for ($i=0; $i < $a; $i++) {

			$sql_check_register_id = 'SELECT card_id
											FROM mi_card_register
											WHERE card_id = "'.$id.'"
											AND branch_id = '.$axRow_branch['id'].'
											AND privilege_id = '.$privilege_id[$i].'';

			$check_register_id = $oDB->QueryOne($sql_check_register_id);

			if ($check_register_id) {

				if ($_POST['check_privilege'.$axRow_branch['id'].'_'.$privilege_id[$i].'']) {

					$do_sql_card = "UPDATE
										mi_card_register
										SET
										status = '0',
										date_update = '".$time_insert." '
										WHERE card_id= '".$id."'
										AND branch_id = '".$axRow_branch['id']."'
										AND privilege_id = ".$privilege_id[$i];
						
					$oDB->QueryOne($do_sql_card);

				} else {

					$do_sql_card = "UPDATE mi_card_register
										SET
										status = '1',
										date_update = '".$time_insert." '
										WHERE card_id= '".$id."'
										AND privilege_id = '".$privilege_id[$i]."'
										AND branch_id = ".$axRow_branch['id'];
						
					$oDB->QueryOne($do_sql_card);
				}

				$do_sql_card_2 = "UPDATE mi_card SET date_update='".$time_insert."' WHERE card_id=".$id;
				
				$oDB->QueryOne($do_sql_card_2);

			} else {

				if ($_POST['check_privilege'.$axRow_branch['id'].'_'.$privilege_id[$i].'']) {

					$qrcode_privileges_text = "QBP-".str_pad($id,4,"0",STR_PAD_LEFT)."-"
													.str_pad($brand_id,4,"0",STR_PAD_LEFT)."-"
													.str_pad($axRow_branch["id"],4,"0",STR_PAD_LEFT)."-"
													.str_pad($privilege_id[$i],4,"0",STR_PAD_LEFT)."";

					$file_full_path = '../../upload/'.$brand_id."/qr_card_register_upload/".$qrcode_privileges_text.".png";

					$qrcode_url = $qrcode_privileges_text.".png";

					$errorCorrectionLevel = 'H'; 

					$matrixPointSize = 10;	

					QRcode::png($qrcode_privileges_text, $file_full_path, $errorCorrectionLevel, $matrixPointSize, 2); 

					$sql_insert = 'INSERT INTO mi_card_register (card_id,branch_id,brand_id,privilege_id,qrcode_privileges_text,qrcode_privileges_image,date_create,date_update,path_qr)
										VALUES ("'.$id.'"
											,"'.$axRow_branch['id'].'"
											,"'.$brand_id.'"
											,"'.$privilege_id[$i].'"
											,"'.$qrcode_privileges_text.'"
											,"'.$qrcode_url.'"
											,"'.$time_insert.'"
											,"'.$time_insert.'"
											,"'.$brand_id.'/qr_card_register_upload/")';
								
					$oDB->QueryOne($sql_insert);
				}
			}
		}

		## SAVE COUPON ##

		for ($i=0; $i < $b; $i++) {

			$sql_check_register_id = 'SELECT card_id
											FROM mi_card_register
											WHERE card_id = "'.$id.'"
											AND branch_id = '.$axRow_branch['id'].'
											AND coupon_id = '.$coupon_id[$i];

			$check_register_id = $oDB->QueryOne($sql_check_register_id);

			if ($check_register_id) {

				if ($_POST['check_coupon'.$axRow_branch['id'].'_'.$coupon_id[$i].'']) {

					$do_sql_card = "UPDATE
										mi_card_register
										SET
										status = '0',
										date_update = '".$time_insert." '
										WHERE card_id= '".$id."'
										AND branch_id = '".$axRow_branch['id']."'
										AND coupon_id = ".$coupon_id[$i];
								
					$oDB->QueryOne($do_sql_card);

				} else {

					$do_sql_card = "UPDATE mi_card_register
										SET
										status = '1',
										date_update = '".$time_insert." '
										WHERE card_id= '".$id."'
										AND coupon_id = '".$coupon_id[$i]."'
										AND branch_id = ".$axRow_branch['id'];
						
					$oDB->QueryOne($do_sql_card);
				}

				$do_sql_card_2 = "UPDATE mi_card SET date_update='".$time_insert."' WHERE card_id= ".$id;
					
				$oDB->QueryOne($do_sql_card_2);

			} else {

				if ($_POST['check_coupon'.$axRow_branch['id'].'_'.$coupon_id[$i].'']) {

					$qrcode_coupon_text = "QBC-".str_pad($id,4,"0",STR_PAD_LEFT)."-"
												.str_pad($brand_id,4,"0",STR_PAD_LEFT)."-"
												.str_pad($axRow_branch["id"],4,"0",STR_PAD_LEFT)."-"
												.str_pad($coupon_id[$i],4,"0",STR_PAD_LEFT)."";

					$file_full_path = '../../upload/'.$brand_id."/qr_card_register_upload/".$qrcode_coupon_text.".png";

					$qrcode_url = $qrcode_coupon_text.".png";

					$errorCorrectionLevel = 'H';

					$matrixPointSize = 10;		

					QRcode::png($qrcode_coupon_text, $file_full_path, $errorCorrectionLevel, $matrixPointSize, 2); 
					
					$sql_insert = 'INSERT INTO mi_card_register (card_id,branch_id,brand_id,coupon_id,qrcode_privileges_text,qrcode_privileges_image,date_create,date_update,path_qr)
										VALUES ("'.$id.'"
										,"'.$axRow_branch['id'].'"
										,"'.$brand_id.'"
										,"'.$coupon_id[$i].'"
										,"'.$qrcode_coupon_text.'"
										,"'.$qrcode_url.'"
										,"'.$time_insert.'"
										,"'.$time_insert.'"
										,"'.$brand_id.'/qr_card_register_upload/")';
								
					$oDB->QueryOne($sql_insert);
				}
			}
		}

		## SAVE HBD ##

		for ($i=0; $i < $d; $i++) {

			$sql_check_register_id = 'SELECT card_id
											FROM mi_card_register
											WHERE card_id = "'.$id.'"
											AND branch_id = '.$axRow_branch['id'].'
											AND coupon_id = '.$hbd_id[$i];

			$check_register_id = $oDB->QueryOne($sql_check_register_id);

			if ($check_register_id) {

				if ($_POST['check_hbd'.$axRow_branch['id'].'_'.$hbd_id[$i].'']) {

					$do_sql_card = "UPDATE
										mi_card_register
										SET
										status = '0',
										date_update = '".$time_insert." '
										WHERE card_id= '".$id."'
										AND branch_id = '".$axRow_branch['id']."'
										AND coupon_id = ".$hbd_id[$i];
								
					$oDB->QueryOne($do_sql_card);

				} else {

					$do_sql_card = "UPDATE mi_card_register
										SET
										status = '1',
										date_update = '".$time_insert." '
										WHERE card_id= '".$id."'
										AND coupon_id = '".$hbd_id[$i]."'
										AND branch_id = ".$axRow_branch['id'];
						
					$oDB->QueryOne($do_sql_card);
				}

				$do_sql_card_2 = "UPDATE mi_card SET date_update='".$time_insert."' WHERE card_id= ".$id;
					
				$oDB->QueryOne($do_sql_card_2);

			} else {

				if ($_POST['check_hbd'.$axRow_branch['id'].'_'.$hbd_id[$i].'']) {

					$qrcode_coupon_text = "QBC-".str_pad($id,4,"0",STR_PAD_LEFT)."-"
												.str_pad($brand_id,4,"0",STR_PAD_LEFT)."-"
												.str_pad($axRow_branch["id"],4,"0",STR_PAD_LEFT)."-"
												.str_pad($hbd_id[$i],4,"0",STR_PAD_LEFT)."";

					$file_full_path = '../../upload/'.$brand_id."/qr_card_register_upload/".$qrcode_coupon_text.".png";

					$qrcode_url = $qrcode_coupon_text.".png";

					$errorCorrectionLevel = 'H';

					$matrixPointSize = 10;		

					QRcode::png($qrcode_coupon_text, $file_full_path, $errorCorrectionLevel, $matrixPointSize, 2); 

					$sql_insert = 'INSERT INTO mi_card_register (card_id,branch_id,brand_id,coupon_id,qrcode_privileges_text,qrcode_privileges_image,date_create,date_update,path_qr)
										VALUES ("'.$id.'"
										,"'.$axRow_branch['id'].'"
										,"'.$brand_id.'"
										,"'.$hbd_id[$i].'"
										,"'.$qrcode_coupon_text.'"
										,"'.$qrcode_url.'"
										,"'.$time_insert.'"
										,"'.$time_insert.'"
										,"'.$brand_id.'/qr_card_register_upload/")';
								
					$oDB->QueryOne($sql_insert);
				}
			}
		}

		## SAVE ACTIVITY ##
			
		for ($i=0; $i < $c; $i++) {

			$sql_check_register_id = 'SELECT card_id
											FROM mi_card_register
											WHERE card_id = "'.$id.'"
											AND branch_id = '.$axRow_branch['id'].'
											AND activity_id = '.$activity_id[$i];

			$check_register_id = $oDB->QueryOne($sql_check_register_id);

			if ($check_register_id) {

				if ($_POST['check_activity'.$axRow_branch['id'].'_'.$activity_id[$i].'']) {

					$do_sql_card = "UPDATE
										mi_card_register
										SET
										status = '0',
										date_update = '".$time_insert." '
										WHERE card_id= '".$id."'
										AND branch_id = '".$axRow_branch['id']."'
										AND activity_id = ".$activity_id[$i];
						
					$oDB->QueryOne($do_sql_card);

				} else {

					$do_sql_card = "UPDATE mi_card_register
										SET
										status = '1',
										date_update = '".$time_insert." '
										WHERE card_id= '".$id."'
										AND activity_id = '".$activity_id[$i]."'
										AND branch_id = ".$axRow_branch['id'];
						
					$oDB->QueryOne($do_sql_card);
				}

				$do_sql_card_2 = "UPDATE mi_card SET date_update = '".$time_insert."' WHERE card_id= ".$id;
					
				$oDB->QueryOne($do_sql_card_2);

			} else {

				if ($_POST['check_activity'.$axRow_branch['id'].'_'.$activity_id[$i].'']) {

					$qrcode_activity_text = "QBA-".str_pad($id,4,"0",STR_PAD_LEFT)."-"
												.str_pad($brand_id,4,"0",STR_PAD_LEFT)."-"
												.str_pad($axRow_branch["id"],4,"0",STR_PAD_LEFT)."-"
												.str_pad($activity_id[$i],4,"0",STR_PAD_LEFT)."";

					$file_full_path = '../../upload/'.$brand_id."/qr_card_register_upload/".$qrcode_activity_text.".png";

					$qrcode_url = $qrcode_activity_text.".png";

					$errorCorrectionLevel = 'H'; 

					$matrixPointSize = 10;	

					QRcode::png($qrcode_activity_text, $file_full_path, $errorCorrectionLevel, $matrixPointSize, 2);

					$sql_insert = '	INSERT INTO mi_card_register (card_id,branch_id,brand_id,activity_id,qrcode_privileges_text,qrcode_privileges_image,date_create,date_update,path_qr)
										VALUES ("'.$id.'"
										,"'.$axRow_branch['id'].'"
										,"'.$brand_id.'"
										,"'.$activity_id[$i].'"
										,"'.$qrcode_activity_text.'"
										,"'.$qrcode_url.'"
										,"'.$time_insert.'"
										,"'.$time_insert.'"
										,"'.$brand_id.'/qr_card_register_upload/")';
						
					$oDB->QueryOne($sql_insert);
				}
			}
		}
	}

	if ($_POST["action"] != "Search") {

		echo '<script>window.location.href="card_register.php";</script>';

	} else {

		echo '<script>window.location.href="card_register_create.php?act=edit&id='.$id.'";</script>';
	}

	exit;
}





$oTmp->assign('card', $asCard);

$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('search_privilege', $search_privilege);

$oTmp->assign('search_branch', $search_branch);

$oTmp->assign('is_menu', 'is_card_register');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_membership', 'in');

$oTmp->assign('content_file', 'card/card_register_create.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());

}

//========================================//


?>
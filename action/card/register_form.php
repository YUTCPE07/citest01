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


if ($_SESSION['role_action']['register_form']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}


//========================================//


$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$where_brand = '';


if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND  a.brand_id = "'.$_SESSION['user_brand_id'].'"';
}



# SEARCH

$brand_id = "";

$where_search = "";

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

else {	$where_search = " AND c.brand_id IN (".$brand_id.")";	}


$sql = 'SELECT 
		a.*,
		a.brand_id AS brand_id,
		a.flag_del AS status_del,
		b.name AS card_type_name,
		c.name AS brand_name,
		c.logo_image,
		c.path_logo,
		a.brand_id AS card_brand_id

		FROM mi_card AS a

  		LEFT JOIN mi_card_type AS b
    	ON a.card_type_id = b.card_type_id

		LEFT JOIN mi_brand AS c
		ON a.brand_id = c.brand_id

		WHERE a.flag_del="0"

		'.$where_search.'
		'.$where_brand.' 

		ORDER BY CASE 
			WHEN a.flag_del = "0" THEN 1
	        WHEN a.flag_del = "1" THEN 2 END ASC,
			a.flag_status ASC, 
			a.date_update DESC';

	$oRes = $oDB->Query($sql);

	$n=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$n++;


		# LOGO

		if($axRow['logo_image']!=''){

			$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';

			$brand_logo = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="150" height="150"/>';

		} else {

			$logo_brand = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';

			$brand_logo = '<img src="../../images/400x400.png" class="image_border" width="150" height="150"/>';
		}


		# CARD IMAGE

		if($axRow['image_newupload']!=''){

			$card_image = '<img src="../../upload/'.$axRow['path_image'].$axRow['image_newupload'].'" class="img-rounded image_border" width="128" height="80"/>';

			$card_data = '<img src="../../upload/'.$axRow['path_image'].$axRow['image_newupload'].'" class="img-rounded image_border" width="240" height="150"/>';

		} else {

			if($axRow['image']!=''){

				$card_image = '<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'" class="img-rounded image_border" width="128" height="80"/>';

				$card_data = '<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'" class="img-rounded image_border" width="240" height="150"/>';

			} else {

				$card_image = '<img src="../../images/card_privilege.jpg" class="img-rounded image_border" width="128" height="80"/>';

				$card_data = '<img src="../../images/card_privilege.jpg" class="img-rounded image_border" width="240" height="150"/>';
			}
		}



		# STATUS

		$status = '';

		if($axRow['flag_del']=='1'){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

		} else {

			if($axRow['flag_status']=='1'){

				$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';

			} else {

				$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
			}
		}



		# VIEW

			# PROFILE

			$data_profile = '';

			$sql_profile = 'SELECT
							a.*,
							b.mafi_NameEn,
							b.mafi_MasterFieldID

							FROM register_form AS a

							LEFT JOIN master_field AS b
							ON b.mafi_MasterFieldID = a.mafi_MasterFieldID

							WHERE b.mafi_Position = "Profile"
							AND b.mafi_Deleted != "T"
							AND a.card_CardID = "'.$axRow['card_id'].'"
							AND a.refo_FillIn = "Y"

							ORDER BY b.mafi_FieldOrder';

			$oRes_profile = $oDB->Query($sql_profile);

 			$check_profile = $oDB->QueryOne($sql_profile);

			if ($check_profile) {

				while ($axRow_profile = $oRes_profile->FetchRow(DBI_ASSOC)){

					$data_profile .= '<tr>
	                                <td style="text-align:center"><b>'.$axRow_profile['mafi_NameEn'].'</b></td>
	                                <td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>';

	                if ($axRow_profile['refo_Require']=="Y") {

						$data_profile .= '<td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>';

	                } else {

						$data_profile .= '<td></td>';
	                }

	                if ($axRow_profile['refo_Hidden']=="Y") {

						$data_profile .= '<td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>';

	                } else {

						$data_profile .= '<td></td>';
	                }

	                if ($axRow_profile['refo_Target']!="") {

	                	if ($axRow_profile['mafi_MasterFieldID']=="6") {

	                		$token = strtok($axRow_profile['refo_Target'] , ",");

							$target_data = array();

							$i = 0;

							while ($token !== false) {						   	

						    	$target_data[$i] =  $token;
						    	$token = strtok(",");
						    	$i++;
							}

							$arrlength = count($target_data);

							$target = "";

							for($x=0; $x<$arrlength; $x++) {

								if ($x == 1) { $target .= ' - '; }

								$sql_target = 'SELECT mata_NameEn
												FROM master_target
												WHERE mata_MasterTargetID="'.$target_data[$x].'"';

		 						$target .= $oDB->QueryOne($sql_target);
							}

							$target .= ' Age Restriction';

	                	} else {

							$sql_target = 'SELECT mata_NameEn
											FROM master_target
											WHERE mata_MasterTargetID="'.$axRow_profile['refo_Target'].'"';

	 						$target = $oDB->QueryOne($sql_target);
	                	}

						$data_profile .= '<td width="15%" style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>
											<td>'.$target.'</td>';
	                } else {

						$data_profile .= '<td width="15%"></td>
											<td></td>';
	                }

	                $data_profile .= '</tr>';
	            }

	        } else {

	            $data_profile .= '<tr><td colspan="5" style="text-align:center">No Register Form Data</td></tr>';
	        }


			# HOME

			$data_home = '';

			$sql_home = 'SELECT
							a.*,
							b.mafi_NameEn

							FROM register_form AS a

							LEFT JOIN master_field AS b
							ON b.mafi_MasterFieldID = a.mafi_MasterFieldID

							WHERE b.mafi_Position = "Home Address"
							AND b.mafi_Deleted != "T"
							AND a.card_CardID = "'.$axRow['card_id'].'"
							AND a.refo_FillIn = "Y"

							ORDER BY b.mafi_FieldOrder';

			$oRes_home = $oDB->Query($sql_home);

 			$check_home = $oDB->QueryOne($sql_home);

			if ($check_home) {

				while ($axRow_home = $oRes_home->FetchRow(DBI_ASSOC)){

					$data_home .= '<tr>
	                                <td style="text-align:center"><b>'.$axRow_home['mafi_NameEn'].'</b></td>
	                                <td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>';

	                if ($axRow_home['refo_Require']=="Y") {

						$data_home .= '<td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>';

	                } else {

						$data_home .= '<td></td>';
	                }

	                if ($axRow_home['refo_Hidden']=="Y") {

						$data_home .= '<td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>';

	                } else {

						$data_home .= '<td></td>';
	                }

	                if ($axRow_home['refo_Target']!="") {

						$sql_target = 'SELECT mata_NameEn
										FROM master_target
										WHERE mata_MasterTargetID="'.$axRow_home['refo_Target'].'"';

 						$target = $oDB->QueryOne($sql_target);

						$data_profile .= '<td width="15%" style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>
											<td>'.$target.'</td>';
	                } else {

						$data_home .= '<td width="15%"></td>
										<td></td>';
	                }

	                $data_home .= '</tr>';
	            }

	        } else {

	            $data_home .= '<tr><td colspan="5" style="text-align:center">No Register Form Data</td></tr>';
	        }


			# WORK ADDRESS

			$data_work_add = '';

			$sql_work_add = 'SELECT
							a.*,
							b.mafi_NameEn

							FROM register_form AS a

							LEFT JOIN master_field AS b
							ON b.mafi_MasterFieldID = a.mafi_MasterFieldID

							WHERE b.mafi_Position = "Work Address"
							AND b.mafi_Deleted != "T"
							AND a.card_CardID = "'.$axRow['card_id'].'"
							AND a.refo_FillIn = "Y"

							ORDER BY b.mafi_FieldOrder';

			$oRes_work_add = $oDB->Query($sql_work_add);
 			$check_work_add = $oDB->QueryOne($sql_work_add);

			if ($check_work_add) {

				while ($axRow_work_add = $oRes_work_add->FetchRow(DBI_ASSOC)){

					$data_work_add .= '<tr>
	                                <td style="text-align:center"><b>'.$axRow_work_add['mafi_NameEn'].'</b></td>
	                                <td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>';

	                if ($axRow_work_add['refo_Require']=="Y") {

						$data_work_add .= '<td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>';

	                } else {

						$data_work_add .= '<td></td>';
	                }

	                if ($axRow_work_add['refo_Hidden']=="Y") {

						$data_work_add .= '<td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>';

	                } else {

						$data_work_add .= '<td></td>';
	                }

	                if ($axRow_work_add['refo_Target']!="") {

						$sql_target = 'SELECT mata_NameEn
										FROM master_target
										WHERE mata_MasterTargetID="'.$axRow_work_add['refo_Target'].'"';

 						$target = $oDB->QueryOne($sql_target);

						$data_profile .= '<td style="text-align:center" width="15%"><span class="glyphicon glyphicon-ok"></span></td>
											<td>'.$target.'</td>';
	                } else {

						$data_work_add .= '<td width="15%"></td>
											<td></td>';
	                }

	                $data_work_add .= '</tr>';
	            }

	        } else {

	            $data_work_add .= '<tr><td colspan="5" style="text-align:center">No Register Form Data</td></tr>';
	        }


			# WORK

			$data_work = '';

			$sql_work = 'SELECT
							a.*,
							b.mafi_NameEn

							FROM register_form AS a

							LEFT JOIN master_field AS b
							ON b.mafi_MasterFieldID = a.mafi_MasterFieldID

							WHERE b.mafi_Position = "Work"
							AND b.mafi_Deleted != "T"
							AND a.card_CardID = "'.$axRow['card_id'].'"
							AND a.refo_FillIn = "Y"

							ORDER BY b.mafi_FieldOrder';

			$oRes_work = $oDB->Query($sql_work);

 			$check_work = $oDB->QueryOne($sql_work);

			if ($check_work) {

				while ($axRow_work = $oRes_work->FetchRow(DBI_ASSOC)){

					$data_work .= '<tr>
	                                <td style="text-align:center"><b>'.$axRow_work['mafi_NameEn'].'</b></td>
	                                <td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>';

	                if ($axRow_work['refo_Require']=="Y") {

						$data_work .= '<td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>';

	                } else {

						$data_work .= '<td></td>';
	                }

	                if ($axRow_work['refo_Hidden']=="Y") {

						$data_work .= '<td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>';

	                } else {

						$data_work .= '<td></td>';
	                }

	                if ($axRow_work['refo_Target']!="") {

						$sql_target = 'SELECT mata_NameEn
										FROM master_target
										WHERE mata_MasterTargetID="'.$axRow_work['refo_Target'].'"';

 						$target = $oDB->QueryOne($sql_target);

						$data_profile .= '<td width="15%" style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>
											<td>'.$target.'</td>';
	                } else {

						$data_work .= '<td width="15%"></td>
										<td></td>';
	                }

	                $data_work .= '</tr>';
	            }

	        } else {

	            $data_work .= '<tr><td colspan="5" style="text-align:center">No Register Form Data</td></tr>';
	        }


			# CONTACT

			$data_contact = '';

			$sql_contact = 'SELECT
							a.*,
							b.mafi_NameEn

							FROM register_form AS a

							LEFT JOIN master_field AS b
							ON b.mafi_MasterFieldID = a.mafi_MasterFieldID

							WHERE b.mafi_Position = "Contact"
							AND b.mafi_Deleted != "T"
							AND a.card_CardID = "'.$axRow['card_id'].'"
							AND a.refo_FillIn = "Y"

							ORDER BY b.mafi_FieldOrder';

			$oRes_contact = $oDB->Query($sql_contact);

 			$check_contact = $oDB->QueryOne($sql_contact);

			if ($check_contact) {

				while ($axRow_contact = $oRes_contact->FetchRow(DBI_ASSOC)){

					$data_contact .= '<tr>
	                                <td style="text-align:center"><b>'.$axRow_contact['mafi_NameEn'].'</b></td>
	                                <td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>';

	                if ($axRow_contact['refo_Require']=="Y") {

						$data_contact .= '<td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>';

	                } else {

						$data_contact .= '<td></td>';
	                }

	                if ($axRow_contact['refo_Hidden']=="Y") {

						$data_contact .= '<td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>';

	                } else {

						$data_contact .= '<td></td>';
	                }

	                $data_contact .= '<td></td><td></td>';
	                $data_contact .= '</tr>';
	            }

	        } else {

	            $data_contact .= '<tr><td colspan="5" style="text-align:center">No Register Form Data</td></tr>';
	        }


			# CUSTOM

			$data_custom = '';

			$sql_custom = 'SELECT
							a.*,
							b.cufi_Name

							FROM custom_form AS a

							LEFT JOIN custom_field AS b
							ON b.cufi_CustomFieldID = a.cufi_CustomFieldID

							WHERE b.cufi_Deleted != "T"
							AND a.card_CardID = "'.$axRow['card_id'].'"
							AND a.cufo_FillIn = "Y"

							ORDER BY b.cufi_FieldOrder';

			$oRes_custom = $oDB->Query($sql_custom);

 			$check_custom = $oDB->QueryOne($sql_custom);

			if ($check_custom) {

				while ($axRow_custom = $oRes_custom->FetchRow(DBI_ASSOC)){

					$data_custom .= '<tr>
	                                <td style="text-align:center"><b>'.$axRow_custom['cufi_Name'].'</b></td>';

	                if ($axRow_custom['cufo_FillIn']=="Y") {

						$data_custom .= '<td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>';

	                } else {

						$data_custom .= '<td></td>';
	                }

	                if ($axRow_custom['cufo_Require']=="Y") {

						$data_custom .= '<td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>';

	                } else {

						$data_custom .= '<td></td>';
	                }

	                if ($axRow_custom['cufo_Hidden']=="Y") {

						$data_custom .= '<td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>';

	                } else {

						$data_custom .= '<td></td>';
	                }

	                if ($axRow_custom['cufo_Target']!="") {

						$sql_target = 'SELECT clva_Name
										FROM custom_list_value
										WHERE clva_CustomListValueID="'.$axRow_custom['cufo_Target'].'"';

 						$target = $oDB->QueryOne($sql_target);

						$data_custom .= '<td style="text-align:center" width="15%"><span class="glyphicon glyphicon-ok"></span></td>
											<td>'.$target.'</td>';
	                } else {

						$data_custom .= '<td></td>
											<td></td>';
	                }

	                $data_custom .= '</tr>';
	            }

	        } else {

	            $data_custom .= '<tr><td colspan="5" style="text-align:center">No Register Form Data</td></tr>';
	        }


		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['card_id'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
				<div class="modal fade" id="View'.$axRow['card_id'].'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
					<div class="modal-dialog" role="document" style="width:60%">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>'.$axRow['name'].'</b></span>
								<hr>
						        <center>
						        	'.$brand_logo.' '.$card_data.'<br><br>
						        	<span style="font-size:12px">
					                <ul id="tapMenu" class="nav nav-tabs">
					                    <li class="active" style="width:16.6%">
					                    	<a data-toggle="tab" href="#profile'.$axRow['card_id'].'">
					                    	<center><b>Profile</b></center></a>
					                    </li>
					                    <li style="width:16.7%">
					                    	<a data-toggle="tab" href="#home'.$axRow['card_id'].'">
					                    	<center><b>Home Address</b></center></a>
					                   	</li>
					                    <li style="width:16.7%">
					                    	<a data-toggle="tab" href="#work_add'.$axRow['card_id'].'">
					                    	<center><b>Work Address</b></center></a>
					                   	</li>
					                    <li style="width:16.6%">
					                    	<a data-toggle="tab" href="#work'.$axRow['card_id'].'">
					                    	<center><b>Work</b></center></a>
					                    </li>
					                    <li style="width:16.6%">
					                    	<a data-toggle="tab" href="#contact'.$axRow['card_id'].'">
					                    	<center><b>Contact</b></center></a>
					                    </li>
					                    <li style="width:16.6%">
					                    	<a data-toggle="tab" href="#custom'.$axRow['card_id'].'">
					                    	<center><b>Custom</b></center></a>
					                    </li>
					                </ul>
					                </span>
					                <div class="tab-content">
					                    <div id="profile'.$axRow['card_id'].'" class="tab-pane active"><br>
								        	<table style="width:90%" class="table table-striped table-bordered myPopup">
									        	<thead>
													<tr class="th_table">
													<th style="text-align:center"><b>Target Member Type</b></th>
													<th width="10%" style="text-align:center"><b>Fill In</b></th>
													<th width="10%" style="text-align:center"><b>Require</b></th>
													<th width="10%" style="text-align:center"><b>Hide</b></th>
													<th colspan="2" width="40%" style="text-align:center"><b>Target</b></th>
													</tr>
												</thead>
												<tbody>
								        		'.$data_profile.'
												</tbody>
								        	</table>
					                    </div>
					                    <div id="home'.$axRow['card_id'].'" class="tab-pane"><br>
								        	<table style="width:90%" class="table table-striped table-bordered myPopup">
									        	<thead>
													<tr class="th_table">
													<th style="text-align:center"><b>Target Member Type</b></th>
													<th width="10%" style="text-align:center"><b>Fill In</b></th>
													<th width="10%" style="text-align:center"><b>Require</b></th>
													<th width="10%" style="text-align:center"><b>Hide</b></th>
													<th colspan="2" width="40%" style="text-align:center"><b>Target</b></th>
													</tr>
												</thead>
												<tbody>
								        		'.$data_home.'
												</tbody>
								        	</table>
					                    </div>
					                    <div id="work_add'.$axRow['card_id'].'" class="tab-pane"><br>
								        	<table style="width:90%" class="table table-striped table-bordered myPopup">
									        	<thead>
													<tr class="th_table">
													<th style="text-align:center"><b>Target Member Type</b></th>
													<th width="10%" style="text-align:center"><b>Fill In</b></th>
													<th width="10%" style="text-align:center"><b>Require</b></th>
													<th width="10%" style="text-align:center"><b>Hide</b></th>
													<th colspan="2" width="40%" style="text-align:center"><b>Target</b></th>
													</tr>
												</thead>
												<tbody>
								        		'.$data_work_add.'
												</tbody>
								        	</table>
					                    </div>
					                    <div id="work'.$axRow['card_id'].'" class="tab-pane"><br>
								        	<table style="width:90%" class="table table-striped table-bordered myPopup">
									        	<thead>
													<tr class="th_table">
													<th style="text-align:center"><b>Target Member Type</b></th>
													<th width="10%" style="text-align:center"><b>Fill In</b></th>
													<th width="10%" style="text-align:center"><b>Require</b></th>
													<th width="10%" style="text-align:center"><b>Hide</b></th>
													<th colspan="2" width="40%" style="text-align:center"><b>Target</b></th>
													</tr>
												</thead>
												<tbody>
								        		'.$data_work.'
												</tbody>
								        	</table>
					                    </div>
					                    <div id="contact'.$axRow['card_id'].'" class="tab-pane"><br>
								        	<table style="width:90%" class="table table-striped table-bordered myPopup">
									        	<thead>
													<tr class="th_table">
													<th style="text-align:center"><b>Target Member Type</b></th>
													<th width="10%" style="text-align:center"><b>Fill In</b></th>
													<th width="10%" style="text-align:center"><b>Require</b></th>
													<th width="10%" style="text-align:center"><b>Hide</b></th>
													<th colspan="2" width="40%" style="text-align:center"><b>Target</b></th>
													</tr>
												</thead>
												<tbody>
								        		'.$data_contact.'
												</tbody>
								        	</table>
					                    </div>
					                    <div id="custom'.$axRow['card_id'].'" class="tab-pane"><br>
								        	<table style="width:90%" class="table table-striped table-bordered myPopup">
									        	<thead>
													<tr class="th_table">
													<th style="text-align:center"><b>Target Member Type</b></th>
													<th width="10%" style="text-align:center"><b>Fill In</b></th>
													<th width="10%" style="text-align:center"><b>Require</b></th>
													<th width="10%" style="text-align:center"><b>Hide</b></th>
													<th colspan="2" width="40%" style="text-align:center"><b>Target</b></th>
													</tr>
												</thead>
												<tbody>
								        		'.$data_custom.'
												</tbody>
								        	</table>
					                    </div>
					                </div>
						        </center>
						    </div>
						    <div class="modal-footer">';

			if ($_SESSION['role_action']['register_form']['edit'] == 1) {		    

				$view .= '       <a href="register_form_create.php?act=edit&id='.$axRow['card_id'].'">
						        <button type="button" class="btn btn-default btn-sm">Edit</button></a>';
			}

				$view .= '      <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';


		# DATA TABLE

		$data_table .= '<tr >
							<td >'.$n.'</td>
							<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
								<span style="font-size:11px;">'.$axRow['brand_name'].'</span>
							</td>
							<td style="text-align:center"><a href="../card/card.php">'.$card_image.'</a></td>
							<td >'.$axRow['name'].'</td>
							<td >'.$axRow['card_type_name'].'</td>
							<td >'.$status.'</td>
							<td >'.DateTime($axRow['date_update']).'</td>';

		if ($_SESSION['role_action']['register_form']['view'] == 1) {

			$data_table .=	'<td style="text-align:center">'.$view.'</td>';
		}

		$data_table .=	'</tr>';

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


$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_register_form');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_membership', 'in');

$oTmp->assign('content_file', 'card/register_form.htm');

$oTmp->display('layout/template.html');


//========================================//


$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
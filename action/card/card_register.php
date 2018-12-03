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


if ($_SESSION['role_action']['card_register']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$where_brand = '';


if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND a.brand_id = "'.$_SESSION['user_brand_id'].'" AND a.flag_del=0 ';
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

	$i=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;


		# LOGO

		if($axRow['logo_image']!=''){

			$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';

		} else {

			$logo_brand = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';
		}


		# QRCODE

		if($axRow['qr_code_image']!=''){

			$qr_code = '<img src="../../upload/'.$axRow['path_qr'].$axRow['qr_code_image'].'" class="image_border" width="150" height="150"/>';
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

				$status = ' <button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';

			} else {

				$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
			}
		}



		# VIEW

			# DATA VIEW

			$sql_branch = 'SELECT DISTINCT mi_branch.name, mi_branch.branch_id
							FROM mi_card_register
							LEFT JOIN mi_branch
							ON mi_card_register.branch_id = mi_branch.branch_id
							WHERE mi_card_register.card_id="'.$axRow['card_id'].'"';

			$oRes_branch = $oDB->Query($sql_branch);


			# PRIVILEGE

			$data_priv = '';

			while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)){

				$data_priv .= '<thead>
								<tr class="th_table">
								<th colspan="3"><b>'.$axRow_branch['name'].'</b></th>
								</tr></thead>';

				$sql_priv = 'SELECT privilege.priv_Name, privilege.priv_Image, privilege.priv_ImageNew,
								mi_card_register.qrcode_privileges_image AS qr_priv,
								privilege.priv_ImagePath,mi_card_register.path_qr
								FROM mi_card_register

								LEFT JOIN mi_branch
								ON mi_card_register.branch_id = mi_branch.branch_id

								LEFT JOIN privilege
								ON mi_card_register.privilege_id = privilege.priv_PrivilegeID

								WHERE mi_card_register.card_id="'.$axRow['card_id'].'"
								AND mi_branch.branch_id="'.$axRow_branch['branch_id'].'"
								AND mi_card_register.status="0"
								AND mi_card_register.privilege_id!=""';

				$oRes_priv = $oDB->Query($sql_priv);

 				$check_priv = $oDB->QueryOne($sql_priv);

				if ($check_priv) {

					$data_priv .= '<tbody>';

					while ($axRow_priv = $oRes_priv->FetchRow(DBI_ASSOC)){

						if ($axRow_priv['priv_ImageNew']) {

							$image_priv = '<img src="../../upload/'.$axRow_priv['priv_ImagePath'].$axRow_priv['priv_ImageNew'].'" class="image_border" width="128" height="80"/>';

						} else if ($axRow_priv['priv_Image']) {

							$image_priv = '<img src="../../upload/'.$axRow_priv['priv_ImagePath'].$axRow_priv['priv_Image'].'" class="image_border" width="128" height="80"/>';

						} else {

							$image_priv = '<img src="../../images/card_privilege.jpg" width="128" height="80"/>';
						}

						$qr_priv = '<img src="../../upload/'.$axRow_priv['path_qr'].$axRow_priv['qr_priv'].'" class="image_border" width="80" height="80"/>';

						$data_priv .= '<tr>
										<td style="text-align:center" width="30%">'.$axRow_priv['priv_Name'].'</td>
										<td style="text-align:center">'.$image_priv.'</td>
										<td style="text-align:center">'.$qr_priv.'</td>
										</tr>';
					}

					$data_priv .= '</tbody>';	

				} else {

					$data_priv .= '<tbody>
									<tr>
									<td colspan="3" style="text-align:center">No Register Data</td>
									</tr></tbody>';
				}
			}


			# COUPON

			$oRes_branch = $oDB->Query($sql_branch);

			$data_coup = '';

			while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)){

				$data_coup .= '<thead>
								<tr class="th_table">
								<th colspan="3"><b>'.$axRow_branch['name'].'</b></th>
								</tr></thead>';

				$sql_coup = 'SELECT coupon.coup_Name, coupon.coup_Image, coupon.coup_ImageNew,
								mi_card_register.qrcode_privileges_image AS qr_coup,
								coupon.coup_ImagePath,mi_card_register.path_qr

								FROM mi_card_register

								LEFT JOIN mi_branch
								ON mi_card_register.branch_id = mi_branch.branch_id

								LEFT JOIN coupon
								ON mi_card_register.coupon_id = coupon.coup_CouponID

								WHERE mi_card_register.card_id="'.$axRow['card_id'].'"
								AND mi_branch.branch_id="'.$axRow_branch['branch_id'].'"
								AND mi_card_register.status="0"
								AND mi_card_register.coupon_id!=""
								AND coupon.coup_Birthday!="T"';

				$oRes_coup = $oDB->Query($sql_coup);

 				$check_coup = $oDB->QueryOne($sql_coup);

				if ($check_coup) {

					$data_coup .= '<tbody>';

					while ($axRow_coup = $oRes_coup->FetchRow(DBI_ASSOC)){

						if ($axRow_coup['coup_ImageNew']) {

							$image_coup = '<img src="../../upload/'.$axRow_coup['coup_ImagePath'].$axRow_coup['coup_ImageNew'].'" class="image_border" width="128" height="80"/>';

						} else if ($axRow_coup['coup_Image']) {

							$image_coup = '<img src="../../upload/'.$axRow_coup['coup_ImagePath'].$axRow_coup['coup_Image'].'" class="image_border" width="128" height="80"/>';

						} else {

							$image_coup = '<img src="../../images/card_privilege.jpg" width="128" height="80"/>';
						}

						$qr_coup = '<img src="../../upload/'.$axRow_coup['path_qr'].$axRow_coup['qr_coup'].'" class="image_border" width="80" height="80"/>';

						$data_coup .= '<tr>
										<td style="text-align:center" width="30%">'.$axRow_coup['coup_Name'].'</td>
										<td style="text-align:center">'.$image_coup.'</td>
										<td style="text-align:center">'.$qr_coup.'</td>
										</tr>';
					}

					$data_coup .= '</tbody>';	

				} else {

					$data_coup .= '<tbody>
									<tr>
									<td colspan="3" style="text-align:center">No Register Data</td>
									</tr></tbody>';
				}
			}


			# BIRTHDAY COUPON

			$oRes_branch = $oDB->Query($sql_branch);

			$data_hbd = '';

			while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)){

				$data_hbd .= '<thead>
								<tr class="th_table">
								<th colspan="3"><b>'.$axRow_branch['name'].'</b></th>
								</tr></thead>';

				$sql_hbd = 'SELECT coupon.coup_Name, coupon.coup_Image, coupon.coup_ImageNew,
								mi_card_register.qrcode_privileges_image AS qr_coup,
								coupon.coup_ImagePath,mi_card_register.path_qr
								FROM mi_card_register
								LEFT JOIN mi_branch
								ON mi_card_register.branch_id = mi_branch.branch_id
								LEFT JOIN coupon
								ON mi_card_register.coupon_id = coupon.coup_CouponID
								WHERE mi_card_register.card_id="'.$axRow['card_id'].'"
								AND mi_branch.branch_id="'.$axRow_branch['branch_id'].'"
								AND mi_card_register.status="0"
								AND mi_card_register.coupon_id!=""
								AND coupon.coup_Birthday="T"';

				$oRes_hbd = $oDB->Query($sql_hbd);

 				$check_hbd = $oDB->QueryOne($sql_hbd);

				if ($check_hbd) {

					$data_hbd .= '<tbody>';

					while ($axRow_hbd = $oRes_hbd->FetchRow(DBI_ASSOC)){

						if ($axRow_hbd['coup_ImageNew']) {

							$image_hbd = '<img src="../../upload/'.$axRow_hbd['coup_ImagePath'].$axRow_hbd['coup_ImageNew'].'" class="image_border" width="128" height="80"/>';

						} else if ($axRow_hbd['coup_Image']) {

							$image_hbd = '<img src="../../upload/'.$axRow_hbd['coup_ImagePath'].$axRow_hbd['coup_Image'].'" class="image_border" width="128" height="80"/>';

						} else {

							$image_hbd = '<img src="../../images/card_privilege.jpg" width="128" height="80"/>';
						}

						$qr_hbd = '<img src="../../upload/'.$axRow_hbd['path_qr'].$axRow_hbd['qr_coup'].'" class="image_border" width="80" height="80"/>';

						$data_hbd .= '<tr>
										<td style="text-align:center" width="30%">'.$axRow_hbd['coup_Name'].'</td>
										<td style="text-align:center">'.$image_hbd.'</td>
										<td style="text-align:center">'.$qr_hbd.'</td>
										</tr>';
					}

					$data_hbd .= '</tbody>';	

				} else {

					$data_hbd .= '<tbody>
									<tr>
									<td colspan="3" style="text-align:center">No Register Data</td>
									</tr></tbody>';
				}
			}



			# ACTIVITY

			$oRes_branch = $oDB->Query($sql_branch);

			$data_acti = '';

			while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)){

				$data_acti .= '<thead>
								<tr class="th_table">
								<th colspan="3"><b>'.$axRow_branch['name'].'</b></th>
								</tr></thead>';

				$sql_acti = 'SELECT activity.acti_Name, activity.acti_Image, activity.acti_ImageNew,
								mi_card_register.qrcode_privileges_image AS qr_acti,
								activity.acti_ImagePath,mi_card_register.path_qr
								FROM mi_card_register
								LEFT JOIN mi_branch
								ON mi_card_register.branch_id = mi_branch.branch_id
								LEFT JOIN activity
								ON mi_card_register.activity_id = activity.acti_ActivityID
								WHERE mi_card_register.card_id="'.$axRow['card_id'].'"
								AND mi_branch.branch_id="'.$axRow_branch['branch_id'].'"
								AND mi_card_register.status="0"
								AND mi_card_register.activity_id!=""';

				$oRes_acti = $oDB->Query($sql_acti);

 				$check_acti = $oDB->QueryOne($sql_acti);

				if ($check_acti) {

					$data_acti .= '<tbody>';

					while ($axRow_acti = $oRes_acti->FetchRow(DBI_ASSOC)){

						if ($axRow_acti['acti_ImageNew']) {

							$image_acti = '<img src="../../upload/'.$axRow_acti['acti_ImagePath'].$axRow_acti['acti_ImageNew'].'" class="image_border" width="128" height="80"/>';

						} else if ($axRow_acti['acti_Image']) {

							$image_acti = '<img src="../../upload/'.$axRow_acti['acti_ImagePath'].$axRow_acti['acti_Image'].'" class="image_border" width="128" height="80"/>';

						} else {

							$image_acti = '<img src="../../images/card_privilege.jpg" width="128" height="80"/>';
						}

						$qr_acti = '<img src="../../upload/'.$axRow_acti['path_qr'].$axRow_acti['qr_acti'].'" class="image_border" width="80" height="80"/>';

						$data_acti .= '<tr>
										<td style="text-align:center" width="30%">'.$axRow_acti['acti_Name'].'</td>
										<td style="text-align:center">'.$image_acti.'</td>
										<td style="text-align:center">'.$qr_acti.'</td>
										</tr>';
					}

					$data_acti .= '</tbody>';	

				} else {

					$data_acti .= '<tbody>
									<tr>
									<td colspan="3" style="text-align:center">No Register Data</td>
									</tr></tbody>';
				}
			}

		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['card_id'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
				<div class="modal fade" id="View'.$axRow['card_id'].'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>'.$axRow['name'].'</b></span>
								<hr>
						        <center>
						        	'.$qr_code.' '.$card_data.'<br><br>
						        	<span style="font-size:12px">
					                <ul id="tapMenu" class="nav nav-tabs">
					                    <li class="active" style="width:25%">
					                    	<a data-toggle="tab" href="#privilege'.$axRow['card_id'].'">
					                    	<center><b>Privilege</b></center></a>
					                    </li>
					                    <li style="width:25%">
					                    	<a data-toggle="tab" href="#coupon'.$axRow['card_id'].'">
					                    	<center><b>Coupon</b></center></a>
					                   	</li>
					                    <li style="width:25%">
					                    	<a data-toggle="tab" href="#hbd'.$axRow['card_id'].'">
					                    	<center><b>Birthday Coupon</b></center></a>
					                   	</li>
					                    <li style="width:25%">
					                    	<a data-toggle="tab" href="#activity'.$axRow['card_id'].'">
					                    	<center><b>Activity</b></center></a>
					                    </li>
					                </ul>
					                </span>
					                <div class="tab-content">
					                    <div id="privilege'.$axRow['card_id'].'" class="tab-pane active" style="height:320px;overflow:auto;"><br>
								        	<table width="80%" class="table table-striped table-bordered myPopup">
								        		'.$data_priv.'
								        	</table>
					                    </div>
					                    <div id="coupon'.$axRow['card_id'].'" class="tab-pane" style="height:320px;overflow:auto;"><br>
								        	<table width="80%" class="table table-striped table-bordered myPopup">
								        		'.$data_coup.'
								        	</table>
					                    </div>
					                    <div id="hbd'.$axRow['card_id'].'" class="tab-pane" style="height:320px;overflow:auto;"><br>
								        	<table width="80%" class="table table-striped table-bordered myPopup">
								        		'.$data_hbd.'
								        	</table>
					                    </div>
					                    <div id="activity'.$axRow['card_id'].'" class="tab-pane" style="height:320px;overflow:auto;"><br>
								        	<table width="80%" class="table table-striped table-bordered myPopup">
								        		'.$data_acti.'
								        	</table>
					                    </div>
					                </div>
						        </center>
						    </div>
						    <div class="modal-footer">';

			if ($_SESSION['role_action']['card_register']['edit'] == 1) {		    

				$view .= '       <a href="card_register_create.php?act=edit&id='.$axRow['card_id'].'">
						        <button type="button" class="btn btn-default btn-sm">Edit</button></a>';
			}

				$view .= '      <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';


		# DATA TABLE

		$data_table .= '<tr >
							<td >'.$i.'</td>
							<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
								<span style="font-size:11px;">'.$axRow['brand_name'].'</span>
							</td>
							<td style="text-align:center"><a href="../card/card.php">'.$card_image.'</a></td>
							<td >'.$axRow['name'].'</td>
							<td >'.$axRow['card_type_name'].'</td>
							<td >'.$status.'</td>
							<td >'.DateTime($axRow['date_update']).'</td>';

		if ($_SESSION['role_action']['card_register']['view'] == 1) {

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

$oTmp->assign('is_menu', 'is_card_register');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_membership', 'in');

$oTmp->assign('content_file', 'card/card_register.htm');

$oTmp->display('layout/template.html');


//========================================//


$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}


//========================================//

?>
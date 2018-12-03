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

if ($_SESSION['role_action']['earn_attention']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$path_upload_collection = $_SESSION['path_upload_collection'];

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];



$where_brand = '';

if ($_SESSION['user_type_id_ses']>1 ) {

	$where_brand = ' AND hilight_coupon.bran_BrandID = "'.$_SESSION['user_brand_id'].'" AND hilight_coupon.coup_Deleted=""';
}



# SEARCH

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



$sql = 'SELECT 

		hilight_coupon.*,
		mi_brand.name AS brand_name,
		mi_brand.logo_image AS brand_logo,
		mi_brand.path_logo,
		memberin_earn_motivation.memo_UpdatedDate

		FROM hilight_coupon

		INNER JOIN mi_brand
		ON mi_brand.brand_id = hilight_coupon.bran_BrandID

		LEFT JOIN memberin_earn_motivation
		ON memberin_earn_motivation.hico_HilightCouponID = hilight_coupon.coup_CouponID

		WHERE 1

		'.$where_search.'
		'.$where_brand.' 

		GROUP BY hilight_coupon.coup_CouponID

		ORDER BY CASE 
			WHEN hilight_coupon.coup_Deleted = "" THEN 1
	        WHEN hilight_coupon.coup_Deleted = "T" THEN 2 END ASC,
			hilight_coupon.coup_Status ASC, 
			memberin_earn_motivation.memo_UpdatedDate DESC';


	$oRes = $oDB->Query($sql);

	$i=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;


		# STATUS

		$status = '';

		if($axRow['coup_Deleted']=='T'){

			$status = '<button style="width:80px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

		} else {

			if($axRow['coup_Status']=='Active'){

				$status = '<button style="width:80px;" class="form-control text-md status_active">On</button>';

			} else {

				$status = '<button style="width:80px;" class="form-control text-md status_pending">Off</button>';
			}
		}



		# LOGO

		if($axRow['brand_logo']!=''){

			$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" width="60" height="60"/>';

			$logo_view = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" width="150" height="150"/>';

		} else {

			$logo_brand = '<img src="../../images/400x400.png" width="60" height="60"/>';

			$logo_view = '<img src="../../images/400x400.png" width="150" height="150"/>';
		}



		# TYPE 

		if ($axRow['coup_Type']=='Buy') { $axRow['coup_Type'] = 'Shop'; }
		else { $axRow['coup_Type'] = 'Promotion'; }



		# UPDATED DATE 

		if ($axRow['memo_UpdatedDate']=='') { $axRow['memo_UpdatedDate'] = '-'; }
		else { $axRow['memo_UpdatedDate'] = DateTime($axRow['memo_UpdatedDate']); }



		# COUPON IMAGE

		if($axRow['coup_Image']!=''){

			$coup_image = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_Image'].'" class="image_border" width="128" height="80"/>';

			$coup_data = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_Image'].'" class="image_border" width="240" height="150"/>';

		} else {

			$coup_image = '<img src="../../images/card_privilege.jpg" width="128" height="80"/>';

			$coup_data = '<img src="../../images/card_privilege.jpg" width="240" height="150"/>';
		}



		# VIEW

		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['coup_CouponID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
				<div class="modal fade" id="View'.$axRow['coup_CouponID'].'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>'.$axRow['coup_Name'].'</b></span>
						        <hr>
						        <center>
						        	'.$logo_view.' '.$coup_data.'<br><br>
						        	<span style="font-size:12px">
					                <ul id="tapMenu" class="nav nav-tabs">';

		if ($axRow['coup_Type']=='Promotion') {

			$sql_ac = 'SELECT memberin_action.*,
						miac_MemberinActionID AS id
						FROM memberin_action
						WHERE miac_Type="Stamp"';
			$oRes_ac = $oDB->Query($sql_ac);

		} else {

			$sql_ac = 'SELECT memberin_action.*,
						miac_MemberinActionID AS id
						FROM memberin_action';
			$oRes_ac = $oDB->Query($sql_ac);
		}

		$x = 1;

		while ($axRow_ac = $oRes_ac->FetchRow(DBI_ASSOC)){

			# FORM

			if ($x ==1) { $class = "active"; } else { $class = ""; }

			$view .= '<li class="'.$class.'"><a data-toggle="tab" href="#'.$axRow['coup_CouponID'].$axRow_ac['id'].'"><center><b>'.$axRow_ac['miac_Name'].'</b></center></a></li>';

			$x++;
		}

		$view .= '    		</ul>
					    </span>
					<div class="tab-content">';

			$oRes_ac = $oDB->Query($sql_ac);

			$count = $oDB->QueryOne($sql_count);

			$width = number_format(100/$count,2);

			$x = 1;

			while ($axRow_ac = $oRes_ac->FetchRow(DBI_ASSOC)){

				# FORM

				if ($x ==1) { $class = "active"; } else { $class = ""; }


				if ($axRow_ac['miac_Type']=='Stamp') {

					# DATA

					$sql_earn = 'SELECT memberin_stamp.* 
								FROM memberin_stamp
								LEFT JOIN memberin_earn_motivation
								ON memberin_earn_motivation.memo_MemberinMotivationID = memberin_stamp.mist_MemberinStampID
								LEFT JOIN collection_type
								ON memberin_stamp.mist_CollectionTypeID = collection_type.coty_CollectionTypeID
								WHERE memberin_earn_motivation.miac_MemberinActionID="'.$axRow_ac['id'].'"
								AND memberin_earn_motivation.hico_HilightCouponID="'.$axRow['coup_CouponID'].'"';

					$oRes_earn = $oDB->Query($sql_earn);

					$earn = $oRes_earn->FetchRow(DBI_ASSOC);


					# MEMBERIN POINT

					$sql_point = 'SELECT coty_Image 
									FROM collection_type 
									WHERE coty_CollectionTypeID="'.$earn['mist_CollectionTypeID'].'"';

					$point_img = $oDB->QueryOne($sql_point);

					$point_icon = '<img src="'.$path_upload_collection.$point_img.'" width="20" height="20"/>';


					# QUANTITY

					if ($earn['mist_StampQty'] != 0) {

						$quantity = $earn['mist_StampQty'].' '.$point_icon.' / 1 Times';

					} else { $quantity = '-'; }


					# MAX POINT PER DAY

					if ($earn['mist_MaxStampPerDay']==0) {

						$earn['mist_MaxStampPerDay'] = 'Unlimited';

					} else {	$earn['mist_MaxStampPerDay'] = $earn['mist_MaxStampPerDay'].' Times / Day / Member'; }


					# METHOD

					if ($earn['mist_CollectionMethod']=='No') {

						$earn['mist_CollectionMethod'] = 'No Expiry';

					} else if ($earn['mist_CollectionMethod']=='Exp') {

						if ($earn['mist_PeriodType']=='Y') {  $earn['mist_PeriodType'] = 'Years';	}

						if ($earn['mist_PeriodType']=='M') {  $earn['mist_PeriodType'] = 'Months';	}

						if ($earn['mist_PeriodTypeEnd']=='Y') {  $earn['mist_PeriodTypeEnd'] = 'End of Year';	}

						if ($earn['mist_PeriodTypeEnd']=='M') {  $earn['mist_PeriodTypeEnd'] = 'End of Month';	}

						$earn['mist_CollectionMethod'] = $earn['mist_PeriodTime'].' '.$earn['mist_PeriodType'].' ('.$earn['mist_PeriodTypeEnd'].')';

					} else if ($earn['mist_CollectionMethod']=='Fix') {

						$earn['mist_CollectionMethod'] = DateOnly($earn['mist_EndDate']);
					
					} else { $earn['mist_CollectionMethod'] = '-'; }


					# MULTIPLE

					if ($earn['mist_Multiple']==0) {	$earn['mist_Multiple'] = '-';	}

					if ($earn['mist_MultipleStartDate']=='0000-00-00') {	$earn['mist_MultipleStartDate'] = '-';	}

					if ($earn['mist_MultipleEndDate']=='0000-00-00') {	$earn['mist_MultipleEndDate'] = '-';	}

					$view .= '<div id="'.$axRow['coup_CouponID'].$axRow_ac['id'].'" class="tab-pane '.$class.'"><br>
								<table width="80%" class="myPopup">
									<tr>
									    <td style="text-align:right" width="50%">Quantity</td>
									    <td style="text-align:center" width="20px">:</td>
									    <td>'.$quantity.'</td>
									</tr>
									<tr>
									    <td style="text-align:right">Expiry</td>
									    <td style="text-align:center">:</td>
									    <td>'.$earn['mist_CollectionMethod'].'</td>
									</tr>
									<tr>
									    <td style="text-align:right" valign="top">Maximum</td>
									    <td style="text-align:center" valign="top">:</td>
									    <td>'.$earn['mist_MaxStampPerDay'].'</td>
									</tr>
									<tr>
									    <td style="text-align:right" valign="top">Multiple</td>
									    <td style="text-align:center" valign="top">:</td>
									    <td>'.$earn['mist_Multiple'].'</td>
									</tr>
									<tr>
									    <td style="text-align:right" valign="top">Multiple Start Date</td>
									    <td style="text-align:center" valign="top">:</td>
									    <td>'.$earn['mist_MultipleStartDate'].'</td>
									</tr>
									<tr>
									    <td style="text-align:right" valign="top">Multiple End Date</td>
									    <td style="text-align:center" valign="top">:</td>
									    <td>'.$earn['mist_MultipleEndDate'].'</td>
									</tr>
								</table>
						    </div>';

				} else {

					# DATA

					$sql_earn = 'SELECT memberin_point.* 
								FROM memberin_point
								LEFT JOIN memberin_earn_motivation
								ON memberin_earn_motivation.memo_MemberinMotivationID = memberin_point.mipo_MemberinPointID
								LEFT JOIN collection_type
								ON memberin_point.mipo_CollectionTypeID = collection_type.coty_CollectionTypeID
								WHERE memberin_earn_motivation.miac_MemberinActionID="'.$axRow_ac['id'].'"
								AND memberin_earn_motivation.hico_HilightCouponID="'.$axRow['coup_CouponID'].'"';

					$oRes_earn = $oDB->Query($sql_earn);

					$earn = $oRes_earn->FetchRow(DBI_ASSOC);


					# MEMBERIN POINT

					$sql_point = 'SELECT coty_Image 
									FROM collection_type 
									WHERE coty_CollectionTypeID="'.$earn['mipo_CollectionTypeID'].'"';

					$point_img = $oDB->QueryOne($sql_point);

					$point_icon = '<img src="'.$path_upload_collection.$point_img.'" width="20" height="20"/>';


					# QUANTITY

					if ($earn['mipo_PointQty'] != 0) {

						$quantity = $earn['mipo_PointQty'].' '.$point_icon.' / 1 Times';

					} else { $quantity = '-'; }


					# METHOD

					if ($earn['mipo_CollectionMethod']=='No') {

						$earn['mipo_CollectionMethod'] = 'No Expiry';

					} else if ($earn['mipo_CollectionMethod']=='Exp') {

						if ($earn['mipo_PeriodType']=='Y') {  $earn['mipo_PeriodType'] = 'Years';	}

						if ($earn['mipo_PeriodType']=='M') {  $earn['mipo_PeriodType'] = 'Months';	}

						if ($earn['mipo_PeriodTypeEnd']=='Y') {  $earn['mipo_PeriodTypeEnd'] = 'End of Year';	}

						if ($earn['mipo_PeriodTypeEnd']=='M') {  $earn['mipo_PeriodTypeEnd'] = 'End of Month';	}

						$earn['mipo_CollectionMethod'] = $earn['mipo_PeriodTime'].' '.$earn['mipo_PeriodType'].' ('.$earn['mipo_PeriodTypeEnd'].')';

					} else if ($earn['mipo_CollectionMethod']=='Fix') {

						$earn['mipo_CollectionMethod'] = DateOnly($earn['mipo_EndDate']);
					
					} else { $earn['mipo_CollectionMethod'] = '-'; }


					# MULTIPLE

					if ($earn['mipo_Multiple']==0) {	$earn['mipo_Multiple'] = '-';	}

					if ($earn['mipo_MultipleStartDate']=='0000-00-00') {	$earn['mipo_MultipleStartDate'] = '-';	}

					if ($earn['mipo_MultipleEndDate']=='0000-00-00') {	$earn['mipo_MultipleEndDate'] = '-';	}

					$view .= '<div id="'.$axRow['coup_CouponID'].$axRow_ac['id'].'" class="tab-pane '.$class.'"><br>
								<table width="80%" class="myPopup">
									<tr>
									    <td style="text-align:right" width="50%">Quantity</td>
									    <td style="text-align:center" width="20px">:</td>
									    <td>'.$quantity.' ('.$earn['mipo_Method'].')</td>
									</tr>
									<tr>
									    <td style="text-align:right">Expiry</td>
									    <td style="text-align:center">:</td>
									    <td>'.$earn['mipo_CollectionMethod'].'</td>
									</tr>
									<tr>
									    <td style="text-align:right" valign="top">Multiple</td>
									    <td style="text-align:center" valign="top">:</td>
									    <td>'.$earn['mipo_Multiple'].'</td>
									</tr>
									<tr>
									    <td style="text-align:right" valign="top">Multiple Start Date</td>
									    <td style="text-align:center" valign="top">:</td>
									    <td>'.$earn['mipo_MultipleStartDate'].'</td>
									</tr>
									<tr>
									    <td style="text-align:right" valign="top">Multiple End Date</td>
									    <td style="text-align:center" valign="top">:</td>
									    <td>'.$earn['mipo_MultipleEndDate'].'</td>
									</tr>
								</table>
						    </div>';

				}

				$x++;
			}

				$view .= '			</div>
						        </center>
						    </div>
						    <div class="modal-footer">';

			if ($_SESSION['role_action']['memberin_earn']['edit'] == 1) {		    

				$view .= '       <a href="earn_create.php?act=edit&id='.$axRow['coup_CouponID'].'">
						        <button type="button" class="btn btn-default btn-sm">Edit</button></a>';
			}

				$view .= '      <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';



		# DATA TABLE

		$data_table .= '<tr >
							<td >'.$i.'<br><br><center>'.$image_status.'</center></td>
							<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
								<span style="font-size:11px;">'.$axRow['brand_name'].'</span>
							</td>
							<td style="text-align:center">'.$coup_image.'</td>
							<td >'.$axRow['coup_Name'].'</td>
							<td >'.$axRow['coup_Type'].'</td>
							<td style="text-align:center">'.$status.'</td>
							<td style="text-align:center">'.$axRow['memo_UpdatedDate'].'</td>';

		if ($_SESSION['role_action']['memberin_earn']['view'] == 1) {

			$data_table .=	'<td style="text-align:center">'.$view.'</td>';
		}

		$data_table .=	'</tr>';
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

$oTmp->assign('is_menu', 'is_memberin_earn');

$oTmp->assign('content_file', 'memberin_motivation/earn.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
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
		hilight_coupon.coup_ImagePath,
		hilight_coupon.coup_Image,
		hilight_coupon.coup_Name,
		hilight_coupon_image.hcim_Image,
		hilight_coupon_image.hcim_ImagePath,
		hilight_coupon_image.hcim_Type,
		hilight_coupon_image.hcim_UpdatedDate,
		hilight_coupon_image.hcim_HilightCouponImageID,
		mi_brand.name AS brand_name,
		mi_brand.logo_image AS brand_logo,
		mi_brand.path_logo AS path_logo

		FROM hilight_coupon_image

		LEFT JOIN hilight_coupon
		ON hilight_coupon.coup_CouponID = hilight_coupon_image.hico_HilightCouponID

		INNER JOIN mi_brand
		ON mi_brand.brand_id = hilight_coupon.bran_BrandID

		WHERE 1

		'.$where_search.'
		'.$where_brand.' 

		ORDER BY CASE 
			WHEN hilight_coupon_image.hcim_Deleted = "" THEN 1
	        WHEN hilight_coupon_image.hcim_Deleted = "T" THEN 2 END ASC,
			hilight_coupon_image.hcim_UpdatedDate DESC';


if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT hcim_Deleted FROM hilight_coupon_image WHERE hcim_HilightCouponImageID ="'.$id.'"';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

		
	if($axRow['hcim_Deleted']=='') {

 		$do_sql_coup = "UPDATE hilight_coupon_image
 							SET hcim_Deleted='T', 
 							hcim_UpdatedDate='".$time_insert."' 
 							WHERE hcim_HilightCouponImageID='".$id."'";

 	} else if ($axRow['hcim_Deleted']=='T') {

		$do_sql_coup = "UPDATE hilight_coupon_image
 							SET hcim_Deleted='', 
 							hcim_UpdatedDate='".$time_insert."' 
 							WHERE hcim_HilightCouponImageID='".$id."'";
	}

 	$oDB->QueryOne($do_sql_coup);

 	echo '<script>window.location.href="image.php";</script>';

} else {

	$oRes = $oDB->Query($sql);

	$i=0;

	$asData = array();

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;


		# LOGO

		if($axRow['brand_logo']!=''){

			$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" width="60" height="60"/>';

		} else {

			$logo_brand = '<img src="../../images/400x400.png" width="60" height="60"/>';
		}

		if($axRow['bran_BrandID']=='0'){

			$logo_brand = '<img src="../../images/mi_action_logo.png" class="image_border" width="60" height="60"/>';

			$axRow['brand_name'] = 'MemberIn';
		}


		# COUPON IMAGE

		if($axRow['coup_Image']!=''){

			$coup_image = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_Image'].'" class="image_border" height="60"/>';

		} else {

			$coup_image = '<img src="../../images/card_privilege.jpg" height="60"/>';
		}



		# DATA TABLE

		$data_table .= '<tr >
							<td >'.$i.'</td>
							<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
								<span style="font-size:11px;">'.$axRow['brand_name'].'</span>
							</td>
							<td style="text-align:center">'.$coup_image.'<br>
								<span style="font-size:11px;">'.$axRow['coup_Name'].'</span></td>
							<td style="text-align:center"><img src="../../upload/'.$axRow['hcim_ImagePath'].$axRow['hcim_Image'].'" class="image_border" height="70"/></td>
							<td style="text-align:center">'.$axRow['hcim_Type'].'</td>
							<td style="text-align:center">'.DateTime($axRow['hcim_UpdatedDate']).'</td>
							<td style="text-align:center">
								<a href="image_create.php?act=edit&id='.$axRow['hcim_HilightCouponImageID'].'">
									<button type="button" class="btn btn-default btn-sm">
										<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
									</button>
								</a>
							</td>';

		if ($_SESSION['role_action']['earn_attention']['delete'] == 1) {

			$data_table .= 	'<td style="text-align:center">
								<a href="image.php?act=delete&id='.$axRow['hcim_HilightCouponImageID'].'">
									<button type="button" class="btn btn-default btn-sm">
										<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active
									</button>
								</a>
							</td>';
		}

		$data_table .=	'</tr>';

		$asData[] = $axRow;
	}
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

$oTmp->assign('is_menu', 'is_earn_attention');

$oTmp->assign('content_file', 'earn_attention/image.htm');

$oTmp->display('layout/template.html');


//========================================//


$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}


//========================================//

?>
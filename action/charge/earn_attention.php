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

if ($_SESSION['role_action']['charge_earn']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];
$id = $_REQUEST['id'];


if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE hilight_coupon 
	 					SET coup_ChargeStatus='Pending'
	 					WHERE coup_CouponID='".$id."'";

 	$oDB->QueryOne($do_sql_status);
 	echo '<script>window.location.href="earn_attention.php";</script>';


} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE hilight_coupon 
	 					SET coup_ChargeStatus='Active' 
	 					WHERE coup_CouponID='".$id."'";

 	$oDB->QueryOne($do_sql_status);
 	echo '<script>window.location.href="earn_attention.php";</script>';
}


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
		a.coup_CouponID,
		a.coup_Name,
		a.coup_Image,
		a.coup_ImagePath,
		a.coup_Price,
		a.coup_ChargePercent,
		a.coup_ExpenseFee,
		a.coup_ChargeStatus,
		a.coup_Status,
		a.coup_Deleted,
		c.name AS brand_name,
		c.logo_image,
		c.path_logo

		FROM hilight_coupon AS a

		LEFT JOIN mi_brand AS c
		ON a.bran_BrandID = c.brand_id

		WHERE coup_Type="Buy"
		'.$where_search.'
		'.$where_brand.' 

		ORDER BY CASE 
			WHEN a.coup_Deleted = "" THEN 1
	        WHEN a.coup_Deleted = "T" THEN 2 END ASC,
			a.coup_ChargeStatus ASC, 
			a.coup_UpdatedDate DESC';

	$oRes = $oDB->Query($sql);

	$i=0;

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;


		# STATUS

		$status = '';

		if($axRow['coup_Deleted']=='T'){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

		} else {

			if($axRow['coup_Status']=='Active'){

				$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';

			} else {

				$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
			}
		}


		# STATUS CHARGE

		if($axRow['coup_ChargeStatus']=='Active'){

			if ($_SESSION['role_action']['charge_earn']['edit'] == 1) {

				$charge_status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'earn_attention.php?act=active&id='.$axRow['coup_CouponID'].'\'">
		                    <option class="status_default" value="'.$axRow['coup_CouponID'].'" selected>On</option>
		                    <option class="status_default">Off</option>
		                </select>
		            </form>';

		    } else {

		        $charge_status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';
		    }

		} else {

			if ($_SESSION['role_action']['charge_earn']['edit'] == 1) {

				$charge_status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_pending" name="pending_status" onchange="window.location.href=\'earn_attention.php?act=pending&id='.$axRow['coup_CouponID'].'\'">
		                    <option class="status_default">On</option>
		                    <option class="status_default" value="'.$axRow['coup_CouponID'].'" selected>Off</option>
		                </select>
		            </form>';

		    } else {

		        $charge_status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
		    }
		}



		# LOGO

		if($axRow['logo_image']!=''){

			$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';

		} else {

			$logo_brand = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';
		}



		# EARN IMAGE

		if($axRow['coup_Image']!=''){

			$coup_image = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_Image'].'" class="image_border" width="128" height="80"/>';

		} else {

			$coup_image = '<img src="../../images/card_privilege.jpg" width="128" height="80"/>';
		}



		# CHARGE

		$charge = ($axRow['coup_Price']*$axRow['coup_ChargePercent'])/100;



		# DATA TABLE

		$data_table .= '<tr >
							<td >'.$i.'<br><br><center>'.$image_status.'</center></td>
							<td style="text-align:center">'.$logo_brand.'<br>
								<span style="font-size:11px;">'.$axRow['brand_name'].'</span>
							</td>
							<td style="text-align:center">'.$coup_image.'</td>
							<td >
								<table>
									<tr>
										<td style="text-align:right">Name</td>
										<td style="text-align:center" width="20px">:</td>
										<td>'.$axRow['coup_Name'].'</td>
									</tr>
									<tr>
										<td style="text-align:right">Status</td>
										<td style="text-align:center" width="20px">:</td>
										<td>'.$axRow['coup_Status'].'</td>
									</tr>
								</table></td>
							<td style="text-align:right">'.number_format($axRow['coup_Price']).' ฿</td>
							<td >'.number_format($axRow['coup_ChargePercent']).'% ('.number_format($charge,2).' ฿)</td>
							<td style="text-align:right">'.number_format($axRow['coup_ExpenseFee'],2).' ฿</td>
							<td style="text-align:center">'.$charge_status.'</td>';

		if ($_SESSION['role_action']['charge_earn']['edit'] == 1) {

			$data_table .=	'<td style="text-align:center">
							<a href="earn_create.php?act=edit&id='.$axRow['coup_CouponID'].'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></a></td>';
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

$oTmp->assign('is_menu', 'is_charge_condition');

$oTmp->assign('content_file', 'charge/earn_attention.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
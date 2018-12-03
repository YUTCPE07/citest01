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

if ($_SESSION['role_action']['charge_card']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];
$id = $_REQUEST['id'];


if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE mi_card 
	 					SET charge_status='Pending'
	 					WHERE card_id='".$id."'";

 	$oDB->QueryOne($do_sql_status);
 	echo '<script>window.location.href="card.php";</script>';


} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE mi_card 
	 					SET charge_status='Active' 
	 					WHERE card_id='".$id."'";

 	$oDB->QueryOne($do_sql_status);
 	echo '<script>window.location.href="card.php";</script>';
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
		a.card_id,
		a.name,
		a.image,
		a.path_image,
		a.charge_status,
		a.card_percent,
		a.member_fee,
		a.flag_status,
		a.flag_del AS status_del,
		c.name AS brand_name,
		c.logo_image,
		c.path_logo,
		a.brand_id AS card_brand_id

		FROM mi_card AS a

		LEFT JOIN mi_brand AS c
		ON a.brand_id = c.brand_id

		WHERE member_fee!="0"
		'.$where_search.'
		'.$where_brand.' 

		ORDER BY CASE 
			WHEN a.flag_del = "0" THEN 1
	        WHEN a.flag_del = "1" THEN 2 END ASC,
			a.charge_status ASC, 
			a.date_update DESC';

	$oRes = $oDB->Query($sql);

	$i=0;

	$data_table = '';

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;


		# STATUS

		$status = '';

		if($axRow['status_del']=='1'){ $status = 'Inactive';

		} else {

			if($axRow['flag_status']=='1'){ $status = 'Active'; } 
			else { $status = 'Pending'; }
		}


		# STATUS CHARGE

		if($axRow['charge_status']=='Active'){

			if ($_SESSION['role_action']['charge_card']['edit'] == 1) {

				$charge_status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'card.php?act=active&id='.$axRow['card_id'].'\'">
		                    <option class="status_default" value="'.$axRow['card_id'].'" selected>On</option>
		                    <option class="status_default">Off</option>
		                </select>
		            </form>';

		    } else {

		        $charge_status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';
		    }

		} else {

			if ($_SESSION['role_action']['charge_card']['edit'] == 1) {

				$charge_status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_pending" name="pending_status" onchange="window.location.href=\'card.php?act=pending&id='.$axRow['card_id'].'\'">
		                    <option class="status_default">On</option>
		                    <option class="status_default" value="'.$axRow['card_id'].'" selected>Off</option>
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



		# CARD IMAGE

		if($axRow['image_newupload']!=''){

			$card_image = '<img src="../../upload/'.$axRow['path_image'].$axRow['image_newupload'].'" class="img-rounded image_border" width="128" height="80"/>';

		} else {

			if($axRow['image']!=''){

				$card_image = '<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'" class="img-rounded image_border" width="128" height="80"/>';

			} else {

				$card_image = '<img src="../../images/card_privilege.jpg" class="img-rounded image_border" width="128" height="80"/>';
			}
		}



		# CHARGE

		$charge = ($axRow['member_fee']*$axRow['charge_percent'])/100;



		# DATA TABLE

		$data_table .= '<tr >
							<td >'.$i.'<br><br><center>'.$image_status.'</center></td>
							<td style="text-align:center">'.$logo_brand.'<br>
								<span style="font-size:11px;">'.$axRow['brand_name'].'</span>
							</td>
							<td style="text-align:center">'.$card_image.'</td>
							<td >
								<table>
									<tr>
										<td style="text-align:right">Name</td>
										<td style="text-align:center" width="20px">:</td>
										<td>'.$axRow['name'].'</td>
									</tr>
									<tr>
										<td style="text-align:right">Status</td>
										<td style="text-align:center" width="20px">:</td>
										<td>'.$status.'</td>
									</tr>
								</table>
							</td>
							<td style="text-align:right">'.number_format($axRow['member_fee']).' ฿</td>
							<td >'.number_format($axRow['charge_percent']).'% ('.number_format($charge,2).' ฿)</td>
							<td style="text-align:right">'.number_format($axRow['expense_fee'],2).' ฿</td>
							<td style="text-align:center">'.$charge_status.'</td>';

		if ($_SESSION['role_action']['charge_card']['edit'] == 1) {

			$data_table .=	'<td style="text-align:center">
							<a href="card_create.php?act=edit&id='.$axRow['card_id'].'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></a></td>';
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

$oTmp->assign('content_file', 'charge/card.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
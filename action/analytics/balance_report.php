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

if ($_SESSION['role_action']['privilege_balance']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$time_insert = date("Y-m-d H:i:s");

$today = date("Y-m-d");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$approve = $_REQUEST['approve'];



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
		c.path_logo,
		c.logo_image,
		a.brand_id AS card_brand_id

		FROM mi_card AS a

  		LEFT JOIN mi_card_type AS b
    	ON a.card_type_id = b.card_type_id

		LEFT JOIN mi_brand AS c
		ON a.brand_id = c.brand_id

		WHERE 1
		'.$where_search.'
		'.$where_brand.' 

		ORDER BY CASE 
			WHEN a.flag_del = "0" THEN 1
	        WHEN a.flag_del = "1" THEN 2 END ASC,
			a.flag_status ASC, 
			a.date_update DESC';


$oRes = $oDB->Query($sql);

$i=0;

$data_table = '';

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$i++;


	# STATUS

	$status = '';

	if($axRow['status_del']=='1'){

		$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

	} else {

		if($axRow['flag_status']=='1'){

			$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';
		} else {

			$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
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

		$card_image = '<img src="../../upload/'.$axRow['path_image'].$axRow['image_newupload'].'" class="img-rounded image_border"height="60"/>';

	} else {

		if($axRow['image']!=''){

			$card_image = '<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'" class="img-rounded image_border"height="60"/>';

		} else {

			$card_image = '<img src="../../images/card_privilege.jpg" class="img-rounded image_border"height="60"/>';
		}
	}



	# DATA TABLE

	$data_table .= '<tr >
						<td >'.$i.'<br><br><center>'.$image_status.'</center></td>
						<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
							<span style="font-size:11px;">'.$axRow['brand_name'].'</span></td>
						<td style="text-align:center"><a href="../card/card.php">'.$card_image.'</a></td>
						<td >'.$axRow['name'].'</td>
						<td >'.$axRow['card_type_name'].'</td>
						<td style="text-align:right">'.number_format($axRow['member_fee'],2).' ฿</td>
						<td style="text-align:center">'.$status.'</td>
						<td >'.DateTime($axRow['date_update']).'</td>';

	if ($_SESSION['role_action']['privilege_balance']['view'] == 1) {

		$data_table .=	'<td style="text-align:center"><a href="balance_card.php?card_id='.$axRow['card_id'].'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button></a></td>';
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

$oTmp->assign('is_menu', 'is_analytics');

$oTmp->assign('content_file', 'analytics/balance_report.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
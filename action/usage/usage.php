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

if ($_SESSION['role_action']['usage']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


# SQL BRAND

$sql_brand = 'SELECT usage_space.*,
                    mi_brand.name, 
                    mi_brand.logo_image, 
                    mi_brand.path_logo 

                FROM usage_space

                LEFT JOIN mi_brand
                ON usage_space.bran_BrandID = mi_brand.brand_id

                WHERE mi_brand.flag_del = "0"
                ORDER BY usage_space.usag_Upload DESC';

$oRes = $oDB->Query($sql_brand);

$data_table = '';

$i=0;

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$i++;


	# LOGO

	if($axRow['logo_image']!=''){

		$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';

	} else {

		$logo_brand = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';
	}

    $units = explode(' ', 'B KB MB GB TB PB');

    

	# DATA TABLE

	$data_table .= '<tr >
						<td >'.$i.'</td>
						<td style="text-align:center">'.$logo_brand.'<br>
							<span style="font-size:11px;">'.$axRow['name'].'</span></td>
						<td style="text-align:center">'.$news_img.'</td>
						<td >'.format_size($axRow["usag_Upload"]).'</td>
						<td ></td>
					</tr>';
}



function format_size($size) {

    global $units;

    $mod = 1024;

    for ($i = 0; $size > $mod; $i++) {

        $size /= $mod;
    }

    $endIndex = strpos($size, ".")+3;

    return substr( $size, 0, $endIndex).' '.$units[$i];
}



$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_usage');

$oTmp->assign('content_file', 'usage/usage.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
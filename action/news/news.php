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

if ($_SESSION['role_action']['news']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$path_upload_news = $_SESSION['path_upload_news'];

$path_upload_logo = $_SESSION['path_upload_logo'];

$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];



$where_brand = '';

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND bran_BrandID = "'.$_SESSION['user_brand_id'].'" AND news_Deleted="" ';
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

		news.*,
		mi_brand.name AS brand_name,
		mi_brand.path_logo,
		mi_brand.logo_image,
		news_category.neca_Name AS news_category

  		FROM news 

  		LEFT JOIN mi_brand 
		ON brand_id = bran_BrandID

  		LEFT JOIN news_category 
		ON news.neca_NewsCategoryID = news_category.neca_NewsCategoryID

		WHERE 1

		'.$where_search.'
		'.$where_brand.' 

		ORDER BY CASE 
			WHEN news_Deleted = "" THEN 1
	        WHEN news_Deleted = "T" THEN 2 END ASC,
			news_Status ASC, 
			news_UpdatedDate DESC';




if($Act == 'active' && $id != '') {

	# UPDATE PENDING

	$do_sql_status = "UPDATE news 
	 					SET news_Status='Pending',
 							news_Approve='Pending',
	 						news_UpdatedDate='".$time_insert."' 
	 					WHERE news_NewsID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="news.php";</script>';

} else if($Act == 'pending' && $id != '') {

	# UPDATE ACTIVE

	$do_sql_status = "UPDATE news 
	 					SET news_Status='Active',
 							news_Approve='Pending',
	 						news_UpdatedDate='".$time_insert."' 
	 					WHERE news_NewsID='".$id."'";

 	$oDB->QueryOne($do_sql_status);

 	echo '<script>window.location.href="news.php";</script>';

} else if($Act == 'delete' && $id != '') {

	# UPDATE DELETED

	$sql = 'SELECT news_Deleted FROM news WHERE news_NewsID ="'.$id.'"';

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if($axRow['news_Deleted']=='') {

 		$do_sql_news = "UPDATE news
 							SET news_Deleted='T', 
 							news_Status='Pending',
 							news_Approve='No',
 							news_UpdatedDate='".$time_insert."' 
 							WHERE news_NewsID='".$id."'";

 	} else if ($axRow['news_Deleted']=='T') {

		$do_sql_news = "UPDATE news
 							SET news_Deleted='', 
 							news_Status='Pending',
 							news_Approve='Pending',
 							news_UpdatedDate='".$time_insert."' 
 							WHERE news_NewsID='".$id."'";
	}


 	$oDB->QueryOne($do_sql_news);

 	echo '<script>window.location.href="news.php";</script>';


} else {

	$oRes = $oDB->Query($sql);

	$data_table = '';

	$i=0;

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$i++;


		# STATUS

		$status = '';

		if($axRow['news_Deleted']=='T'){

			$status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

		} else {

			if($axRow['news_Status']=='Active'){

				if ($_SESSION['role_action']['news']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_active" name="active_status" onchange="window.location.href=\'news.php?act=active&id='.$axRow['news_NewsID'].'\'">
		                    <option class="status_default" value="'.$axRow['news_NewsID'].'" selected>On</option>
		                    <option class="status_default">Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';
		        }

			} else {

				if ($_SESSION['role_action']['news']['edit'] == 1) {

					$status = '<form id="myForm" method="POST">
						<select class="form-control text-md status_pending" name="approve_status" onchange="window.location.href=\'news.php?act=pending&id='.$axRow['news_NewsID'].'\'">
		                    <option class="status_default">On</option>
		                    <option class="status_default" value="'.$axRow['news_NewsID'].'" selected>Off</option>
		                </select>
		            </form>';

		        } else {

		        	$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
		        }
			}
		}


		# LOGO

		if($axRow['logo_image']!=''){

			$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';

			$logo_view = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="150" height="150"/>';

		} else {

			$logo_brand = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';

			$logo_view = '<img src="../../images/400x400.png" width="150" height="150"/>';
		}



		# NEWS IMAGE

		if($axRow['news_Image']!=''){

			$news_img = '<img src="../../upload/'.$axRow['news_ImagePath'].$axRow['news_Image'].'" class="image_border" width="128" height="80"/>';

			$news_view = '<img src="../../upload/'.$axRow['news_ImagePath'].$axRow['news_Image'].'" class="image_border" width="240" height="150"/>';

		} else {

			$news_img = '<img src="../../images/400x400.png" width="128" height="80"/>';

			$news_view = '<img src="../../images/400x400.png" width="240" height="150"/>';
		}



		# CARD NAME

		$token = strtok($axRow['card_CardID'] , ",");

		$card = array();

		$j = 0;

		while ($token !== false) {

	    	$card[$j] =  $token;

	    	$token = strtok(",");

	    	$j++;
		}

		$arrlength = count($card);

		$card_data = "";

		for($x = 0; $x < $arrlength; $x++) {

			$do_sql_card =  "SELECT name FROM mi_card WHERE card_id=".$card[$x];

	 		$name = $oDB->QueryOne($do_sql_card);

	 		if ($x == $arrlength-1) {

				$card_data .= $name;

	 		} else {

				$card_data .= $name."<br>";
	 		}
		}


		# DELETED

		if($axRow['news_Deleted']=='') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['news_NewsID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Active</button>
				<div class="modal fade" id="Deleted'.$axRow['news_NewsID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="140px" style="text-align:center" valign="top">'.$news_view.'</td>
						        	<td>
								        <p style="font-size:12px;padding-left:10px;">
								        	<b>"'.$axRow['news_Title'].'"</b><br>
								            By clicking the <b>"Inactive"</b> button to:<br>
								            &nbsp; &nbsp;- Inactive this news<br>
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="news.php?act=delete&id='.$axRow['news_NewsID'].'">
						        <button type="button" class="btn btn-default btn-sm">Inactive</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';

		} else if ($axRow['news_Deleted']=='T') {

			$deleted = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Deleted'.$axRow['news_NewsID'].'"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span> Inactive</button>
				<div class="modal fade" id="Deleted'.$axRow['news_NewsID'].'" tabindex="-1" role="dialog" aria-labelledby="DeletedDataLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>Please confirm your choice</b></span>
						        <hr>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr>
						        	<td width="140px" style="text-align:center" valign="top">'.$news_view.'</td>
						        	<td>
								        <p style="font-size:12px;padding-left:10px;">
								        	<b>"'.$axRow['news_Title'].'"</b><br>
								           	By clicking the <b>"Active"</b> button to:<br>
								            &nbsp; &nbsp;- Active this news<br>
								            &nbsp; &nbsp;- Change status to Pending
								        </p>
								    </td>
						        	</tr>
						        </table>
						        </center>
						    </div>
						    <div class="modal-footer">
						    	<a href="news.php?act=delete&id='.$axRow['news_NewsID'].'">
						        <button type="button" class="btn btn-default btn-sm">Active</button></a>
						        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
						    </div>
						</div>
					</div>
				</div>';
		}


		# VIEW

			# DATA

			if ($axRow['news_ShortDescription'] == '') { $axRow['news_ShortDescription'] = '-';	}

				

		$view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['news_NewsID'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
				<div class="modal fade" id="View'.$axRow['news_NewsID'].'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
					<div class="modal-dialog" role="document" style="width:50%">
						<div class="modal-content">
						    <div class="modal-body" align="left">
						        <span style="font-size:16px"><b>'.$axRow['news_Title'].'</b></span>
						        <hr>
						        <center>
						        	'.$logo_view.' '.$news_view.'
						        	<br>
						        	<br>
								    <table width="80%" class="myPopup">
								        <tr>
								        	<td style="text-align:right" width="45%">Category</td>
								        	<td style="text-align:center" width="5%">:</td>
								        	<td>'.$axRow['news_category'].'</td>
								        </tr>
								        <tr>
								        	<td style="text-align:right" valign="top">Card</td>
								        	<td style="text-align:center" valign="top">:</td>
								        	<td valign="top">'.$card_data.'</td>
								        </tr>
								        <tr>
								        	<td style="text-align:right" valign="top">Description</td>
								        	<td style="text-align:center" valign="top">:</td>
								        	<td valign="top">'.$axRow['news_ShortDescription'].'</td>
								        </tr>
								    </table>
						        </center>
						    </div>
						    <div class="modal-footer">';

			if ($_SESSION['role_action']['news']['edit'] == 1) {		    

				$view .= '       <a href="news_create.php?act=edit&id='.$axRow['news_NewsID'].'">
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
								<span style="font-size:11px;">'.$axRow['brand_name'].'</span></td>
							<td style="text-align:center">'.$news_img.'</td>
							<td >'.$axRow['news_Title'].'</td>
							<td >'.$axRow['news_category'].'</td>
							<td >'.DateOnly($axRow['news_PostedDate']).'</td>
							<td style="text-align:center">'.$status.'</td>
							<td >'.DateTime($axRow['news_UpdatedDate']).'</td>';

		if ($_SESSION['role_action']['news']['edit'] == 1) {

			$data_table .=	'<td style="text-align:center">'.$view.'</td>';
		}

		if ($_SESSION['role_action']['news']['delete'] == 1) {

			$data_table .= 	'<td style="text-align:center">'.$deleted.'</td>';
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

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_news');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_messages', 'in');

$oTmp->assign('content_file', 'news/news.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
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



$id = $_REQUEST['id'];

$path_upload_member = $_SESSION['path_upload_member'];

$path_upload_collection = $_SESSION['path_upload_collection'];



$sql ='SELECT * FROM mb_member WHERE member_id = "'.$id.'"';

$oRes = $oDB->Query($sql);

$asData = array();

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	if ($axRow['date_birth']=="0000-00-00") { $axRow['date_birth'] = "-"; } 
	else { $axRow['date_birth'] = DateOnly($axRow['date_birth']); }

	if ($axRow['nickname']=="") { $axRow['nickname']="-"; }
	if ($axRow['firstname']=="") { $axRow['firstname']="-"; }
	if ($axRow['lastname']=="") { $axRow['lastname']="-"; }

	$asData = $axRow;
}



if($_SESSION['user_brand_id']){

	$where_brand = 'AND mi_branch.brand_id = "'.$_SESSION['user_brand_id'].'"';
}


if($_SESSION['user_branch_id']){

	$where_branch = ' AND member_motivation_stamp_trans.brnc_BranchID = "'.$_SESSION['user_branch_id'].'"';
}



$sql_stamp = "SELECT  

					member_motivation_stamp_trans.mems_CreatedDate AS date_entry,
					member_motivation_stamp_trans.mems_CollectedDate AS date_collect,
					member_motivation_stamp_trans.mems_StampQty AS stamp,
					member_motivation_stamp_trans.priv_PrivilegeID AS priv_id,
					member_motivation_stamp_trans.coup_CouponID AS coup_id,
					member_motivation_stamp_trans.acti_ActivityID AS acti_id,
					privilege.priv_Image AS priv_img,
					privilege.priv_ImageNew AS priv_new,
					privilege.priv_ImagePath AS priv_ImagePath,
					privilege.priv_Name AS priv_name,
					coupon.coup_Image AS coup_img,
					coupon.coup_ImageNew AS coup_new,
					coupon.coup_Name AS coup_name,
					activity.acti_Image AS acti_img,
					activity.acti_ImageNew AS acti_new,
					activity.acti_Name AS acti_name,
					mi_card.name AS card_name,
					mi_card.image AS card_image,
					mi_card.image_newupload AS card_new,
					mi_card.path_image AS path_image,
					mi_branch.name AS branch_name,
					mi_brand.name AS brand_name,
					mi_brand.logo_image AS brand_logo,
					mi_brand.path_logo AS path_logo,
					collection_type.coty_Image AS collect_img

					FROM member_motivation_stamp_trans

	  				LEFT JOIN mb_member_register
	    			ON mb_member_register.member_register_id = member_motivation_stamp_trans.mere_MemberRegisterID

	  				LEFT JOIN mb_member
	    			ON mb_member.member_id = mb_member_register.member_id

					LEFT JOIN mi_card
					ON mi_card.card_id = mb_member_register.card_id

					LEFT JOIN mi_branch
					ON mi_branch.branch_id = member_motivation_stamp_trans.brnc_BranchID

					LEFT JOIN mi_brand
					ON mi_branch.brand_id = mi_brand.brand_id 

					LEFT JOIN privilege
					ON privilege.priv_PrivilegeID = member_motivation_stamp_trans.priv_PrivilegeID 

					LEFT JOIN coupon
					ON coupon.coup_CouponID = member_motivation_stamp_trans.coup_CouponID 

					LEFT JOIN activity
					ON activity.acti_ActivityID = member_motivation_stamp_trans.acti_ActivityID 

					LEFT JOIN collection_type
					ON collection_type.coty_CollectionTypeID = member_motivation_stamp_trans.coty_CollectionTypeID 

					WHERE mb_member.member_id = ".$id."
					".$where_brand."
					".$where_branch."

					ORDER BY date_collect DESC";

$oRes_stamp = $oDB->Query($sql_stamp)or die(mysql_error());
$check_stamp = $oDB->QueryOne($sql_stamp);


if ($check_stamp) {

	$table_stamp = "<table id='example' class='table table-bordered' style='background-color:white;'>
						<thead><tr class='th_table'>
							<th style='text-align:center'><b>Collect Date</b></th>
							<th style='text-align:center'><b>Entry Date</b></th>
							<th style='text-align:center;'>Brand</th>
							<th style='text-align:center;'>Card</th>
							<th style='text-align:center;'>Privilege</th>
							<th style='text-align:center;'>Type</th>
							<th style='text-align:center;'>Branch</th>
							<th style='text-align:center;'>Stamp</th>
						</tr></thead><tbody>";

	while ($axRow = $oRes_stamp->FetchRow(DBI_ASSOC)) {

		# LOGO IMAGE

		if($axRow['brand_logo']!=''){

			$axRow['brand_logo'] = '<img src="../../upload/'.$axRow['path_logo'].$axRow['brand_logo'].'" class="image_border" width="50" height="50"/>';

		} else {

			$axRow['brand_logo'] = '<img src="../../images/400x400.png" width="50" height="50"/>';
		}


		# CARD IMAGE

		if($axRow['card_new']!=''){

			$axRow['card_image'] = '<img src="../../upload/'.$axRow['path_image'].$axRow['card_new'].'" height="50px" class="img-rounded image_border"/>';

		} else if ($axRow['card_image']!='') {

			$axRow['card_image'] = '<img src="../../upload/'.$axRow['path_image'].$axRow['card_image'].'" height="50px" class="img-rounded image_border"/>';

		} else {

			$axRow['card_image'] = '<img src="../../images/card_privilege.jpg" height="50px" class="img-rounded image_border"/>';
		}



		# COLLECTION IMAGE

		$collect_img = '<img src="'.$path_upload_collection.$axRow['collect_img'].'" height="20px" style="margin-bottom:5px">';



		# PRIVILEGE IMAGE

		if($axRow['priv_id']!='0'){

			if($axRow['priv_new']!="") {

				$privilege_img = '<img src="../../upload/'.$axRow['priv_ImagePath'].$axRow['priv_new'].'" height="50px">';

			} else if ($axRow['priv_img']!="") {

				$privilege_img = '<img src="../../upload/'.$axRow['priv_ImagePath'].$axRow['priv_img'].'" height="50px">';

			} else {

				$privilege_img = '<img src="../../images/card_privilege.jpg" height="50px"/>';
			}

			$type = "Privilege";

			$name = $axRow['priv_name'];

		} else if ($axRow['coup_id']!='0') {

			if($axRow['coup_new']!="") {

				$privilege_img = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_new'].'" height="50px">';

			} else if ($axRow['coup_img']!="") {

				$privilege_img = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_img'].'" height="50px">';

			} else {

				$privilege_img = '<img src="../../images/card_privilege.jpg" height="50px"/>';
			}

			$sql_type = "SELECT coup_Birthday FROM coupon WHERE coup_CouponID='".$axRow['coup_id']."'";
			$coup_Birthday = $oDB->QueryOne($sql_type);

			if ($coup_Birthday=='T') { $type = "Birthday Coupon"; } 
			else { $type = "Coupon"; }

			$name = $axRow['coup_name'];

		} else {

			if($axRow['acti_new']!="") {

				$privilege_img = '<img src="../../upload/'.$axRow['acti_ImagePath'].$axRow['acti_new'].'" height="50px">';

			} else if ($axRow['acti_img']!="") {

				$privilege_img = '<img src="../../upload/'.$axRow['acti_ImagePath'].$axRow['acti_img'].'" height="50px">';

			} else {

				$privilege_img = '<img src="../../images/card_privilege.jpg" height="50px"/>';
			}

			$type = "Activity";

			$name = $axRow['acti_name'];
		}


		$table_stamp .= "<tr>
							<td style='text-align:center'>".DateTime($axRow['date_collect'])."</td>
							<td style='text-align:center'>".DateTime($axRow['date_entry'])."</td>
							<td style='text-align:center'><a href='../brand/brand.php'>".$axRow['brand_logo']."</a><br>
								<span style='font-size:11px'>".$axRow['brand_name']."</span></td>
							<td style='text-align:center'><a href='../card/card.php'>".$axRow['card_image']."</a><br>
								<span style='font-size:11px'>".$axRow['card_name']."</span></td>
							<td style='text-align:center'>";

		if ($type == 'Privilege') {

			$table_stamp .= "<a href='../privilege/privilege.php'>".$privilege_img."</a><br>";

		} elseif ($type == 'Coupon') {

			$table_stamp .= "<a href='../coupon/coupon.php'>".$privilege_img."</a><br>";

		} elseif ($type == 'Activity') {

			$table_stamp .= "<a href='../activity/activity.php'>".$privilege_img."</a><br>";
			
		} elseif ($type == 'Earn Attention') {

			$sql_type = "SELECT coup_Type FROM hilight_coupon WHERE coup_CouponID='".$axRow['privilege_id']."'";
			$coup_Type = $oDB->QueryOne($sql_type);

			if ($coup_Type=='Use') {

				$table_stamp .= "<a href='../earn_attention/use.php'>".$privilege_img."</a><br>";

			} else {

				$table_stamp .= "<a href='../earn_attention/buy.php'>".$privilege_img."</a><br>";
			}

		} else {

			$table_stamp .= "<a href='../coupon/birthday.php'>".$privilege_img."</a><br>";
		}

		$table_stamp .= "	 	<span style='font-size:11px'>".$name."</span></td>
							<td style='text-align:center'>".$type."</td>
							<td style='text-align:center'>".$axRow['branch_name']."</td>
							<td style='text-align:center'>".$axRow['stamp']." &nbsp; ".$collect_img."</td>
						</tr>";
	}

	$table_stamp .= "</tbody></table>";
}





$as_name_title_type = list_type_master_value($oDB,'name_title_type',$axRow['name_title_type']);

if ($as_name_title_type=="") { $as_name_title_type = "-"; }

$oTmp->assign('name_title_type', $as_name_title_type);



$oTmp->assign('data', $asData);

$oTmp->assign('table_stamp', $table_stamp);

$oTmp->assign('is_menu', 'is_analytics');

$oTmp->assign('content_file', 'analytics/last_stamp.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
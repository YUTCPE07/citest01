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


$member_id = $_REQUEST['member'];

$brand_id = $_REQUEST['brand'];

$path_upload_member = $_SESSION['path_upload_member'];

$path_upload_collection = $_SESSION['path_upload_collection'];



$sql ='SELECT * FROM mb_member WHERE member_id = "'.$member_id.'"';

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



if($_SESSION['user_branch_id']){

	$where_branch .= ' AND member_motivation_stamp_trans.brnc_BranchID = "'.$_SESSION['user_branch_id'].'"';
}



$sql_stamp = 'SELECT DISTINCT

						mi_card.image AS card_image,
						mi_card.image_newupload,
						mi_card.path_image,
						mi_card.name AS card_name,
						mi_branch.name AS branch_name,
						member_motivation_stamp_trans.mems_StampQty AS stamp_qty,
						member_motivation_stamp_trans.mems_CollectedDate AS date_collect,
						member_motivation_stamp_trans.mems_CreatedDate AS date_entry,
						privilege.priv_PrivilegeID,
						privilege.priv_Name,
						privilege.priv_Image,
						privilege.priv_ImageNew,
						privilege.priv_ImagePath,
						coupon.coup_CouponID,
						coupon.coup_Name,
						coupon.coup_Image,
						coupon.coup_ImageNew,
						coupon.coup_ImagePath,
						activity.acti_ActivityID,
						activity.acti_Name,
						activity.acti_Image,
						activity.acti_ImageNew,
						activity.acti_ImagePath,
						collection_type.coty_Name,
						collection_type.coty_Image

						FROM member_motivation_stamp_trans

						LEFT JOIN mb_member_register
						ON mb_member_register.member_register_id = member_motivation_stamp_trans.mere_MemberRegisterID

						LEFT JOIN collection_type
						ON collection_type.coty_CollectionTypeID = member_motivation_stamp_trans.coty_CollectionTypeID

						LEFT JOIN privilege
						ON privilege.priv_PrivilegeID = member_motivation_stamp_trans.priv_PrivilegeID

						LEFT JOIN coupon
						ON coupon.coup_CouponID = member_motivation_stamp_trans.coup_CouponID

						LEFT JOIN activity
						ON activity.acti_ActivityID = member_motivation_stamp_trans.acti_ActivityID

						LEFT JOIN mi_card
						ON mb_member_register.card_id = mi_card.card_id

						LEFT JOIN mi_brand
						ON mi_brand.brand_id = mi_card.brand_id

						LEFT JOIN mi_branch
						ON member_motivation_stamp_trans.brnc_BranchID = mi_branch.branch_id

						WHERE mb_member_register.member_id = '.$member_id.'
						AND mi_brand.brand_id = '.$brand_id.'
						'.$where_branch.'
						GROUP BY member_motivation_stamp_trans.mems_CreatedDate
						ORDER BY member_motivation_stamp_trans.mems_CreatedDate DESC';


$oRes_stamp = $oDB->Query($sql_stamp);

$check_stamp = $oDB->QueryOne($sql_stamp);

$total_sql = "SELECT SUM(member_motivation_stamp_trans.mems_StampQty)
					FROM member_motivation_stamp_trans

					LEFT JOIN mb_member_register
					ON mb_member_register.member_register_id = member_motivation_stamp_trans.mere_MemberRegisterID

					LEFT JOIN mi_card
					ON mb_member_register.card_id = mi_card.card_id

					LEFT JOIN mi_brand
					ON mi_brand.brand_id = mi_card.brand_id

					WHERE mb_member_register.member_id='".$member_id."'
					AND mi_brand.brand_id ='".$brand_id."'"
					.$where_branch."";

$total_use = $oDB->QueryOne($total_sql);

$table_member = "<center><br>
					<span style='font-size:16px'><b>Total Stamp Collect &nbsp; : &nbsp; ".number_format($total_use)."</span></b>
				<br></center>";

$table_member .= "<table id='example' class='table table-bordered' style='background-color:white;' >
					<thead>
						<tr class='th_table'>
							<th style='text-align:center'><b>Collect Date</b></th>
							<th style='text-align:center'><b>Entry Date</b></th>
							<th style='text-align:center'><b>Card</b></th>
							<th style='text-align:center'><b>Privilege</b></th>
							<th style='text-align:center'><b>Branch</b></th>
							<th style='text-align:center'><b>Type</b></th>
							<th style='text-align:center'><b>Stamp Collect</b></th>
						</tr>
					</thead>";

if ($check_stamp) {

	$table_member .= "<tbody>";

	while ($axRow = $oRes_stamp->FetchRow(DBI_ASSOC)){

		# CARD IMAGE

		if($axRow['image_newupload']!=''){

			$axRow['card_image'] = '<img src="../../upload/'.$axRow['path_image'].$axRow['image_newupload'].'" height="50px" class="img-rounded image_border"/>';

		} else if ($axRow['card_image']!='') {

			$axRow['card_image'] = '<img src="../../upload/'.$axRow['path_image'].$axRow['card_image'].'" height="50px" class="img-rounded image_border"/>';

		} else {

			$axRow['card_image'] = '<img src="../../images/card_privilege.jpg" height="50px" class="img-rounded image_border"/>';
		}



		# PRIVILEGE IMAG

		if($axRow['priv_ImageNew']!=''){

			$privilege_img = '<img src="../../upload/'.$axRow['priv_ImagePath'].$axRow['priv_ImageNew'].'" height="50px" class="image_border">';
			$privilege_name = $axRow['priv_Name'];
			$type = "Privilege";

		} else if ($axRow['priv_Image']!='') {

			$privilege_img = '<img src="../../upload/'.$axRow['priv_ImagePath'].$axRow['priv_Image'].'" height="50px" class="image_border">';
			$privilege_name = $axRow['priv_Name'];
			$type = "Privilege";

		} else if ($axRow['coup_ImageNew']!='') {

			$privilege_img = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_ImageNew'].'" height="50px" class="image_border">';
			$privilege_name = $axRow['coup_Name'];
			$type = "Coupon";

		} else if ($axRow['coup_Image']!='') {

			$privilege_img = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_Image'].'" height="50px" class="image_border">';
			$privilege_name = $axRow['coup_Name'];

			$sql_type = "SELECT coup_Birthday FROM coupon WHERE coup_CouponID='".$axRow['coup_id']."'";
			$coup_Birthday = $oDB->QueryOne($sql_type);

			if ($coup_Birthday=='T') { $type = "Birthday Coupon"; } 
			else { $type = "Coupon"; }

		} else if ($axRow['acti_ImageNew']!='') {

			$privilege_img = '<img src="../../upload/'.$axRow['acti_ImagePath'].$axRow['acti_ImageNew'].'" height="50px" class="image_border">';
			$privilege_name = $axRow['acti_Name'];
			$type = "Activity";

		} else if ($axRow['acti_Image']!='') {

			$privilege_img = '<img src="../../upload/'.$axRow['acti_ImagePath'].$axRow['acti_Image'].'" height="50px" class="image_border">';
			$privilege_name = $axRow['acti_Name'];
			$type = "Activity";

		} else {

			$privilege_img = '<img src="../../images/card_privilege.jpg" height="50px" class="image_border"/>';
		}


		# COLLECTION IMAGE

		$coll_image = '<img src="'.$path_upload_collection.$axRow['coty_Image'].'" width="30" height="30"/>';



		$table_member .= "<tr><td style='text-align:center'>".DateTime($axRow['date_collect'])."</td>
							<td style='text-align:center'>".DateTime($axRow['date_entry'])."</td>
							<td style='text-align:center'><a href='../card/card.php'>".$axRow['card_image']."</a><br>
								<span style='font-size:11px'>".$axRow['card_name']."</span></td>
							<td style='text-align:center'>";

		if ($type == 'Privilege') {

			$table_member .= "<a href='../privilege/privilege.php'>".$privilege_img."</a><br>";

		} elseif ($type == 'Coupon') {

			$table_member .= "<a href='../coupon/coupon.php'>".$privilege_img."</a><br>";

		} elseif ($type == 'Activity') {

			$table_member .= "<a href='../activity/activity.php'>".$privilege_img."</a><br>";
			
		} else {

			$table_member .= "<a href='../coupon/birthday.php'>".$privilege_img."</a><br>";
		}

		$table_member .= "		<span style='font-size:11px'>".$privilege_name."</span></td>
							<td>".$axRow['branch_name']."</td>
							<td style='text-align:center'>".$coll_image."<br>
								<span style='font-size:11px'>".$axRow['coty_Name']."</span></td>
							<td style='text-align:center;background-color:#F2F2F2'><b>".number_format($axRow['stamp_qty'])."</b></td>
						</tr>";
	}

	$table_member .= "</tbody>";	
}

$table_member .= "</table>";



$as_name_title_type = list_type_master_value($oDB,'name_title_type',$axRow['name_title_type']);

if ($as_name_title_type=="") { $as_name_title_type = "-"; }

$oTmp->assign('name_title_type', $as_name_title_type);

$oTmp->assign('data', $asData);

$oTmp->assign('table_member', $table_member);

$oTmp->assign('is_menu', 'is_analytics');

$oTmp->assign('content_file', 'analytics/stamp_total.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
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

	$where_branch .= ' AND member_motivation_point_trans.brnc_BranchID = "'.$_SESSION['user_branch_id'].'"';
}	


# SEARCH DATE

$StartSales = $_SESSION['sale_start'];

$EndSales = $_SESSION['sale_end'];

$where_date = '';

if ($StartSales && $EndSales) {

	$where_date = ' AND member_motivation_point_trans.memp_CreatedDate BETWEEN "'.$StartSales.'" AND "'.$EndSales.'" ';

} else if ($StartSales) {

	$where_date = ' AND member_motivation_point_trans.memp_CreatedDate >= "'.$StartSales.'" ';

} else if ($EndSales) {

	$where_date = ' AND member_motivation_point_trans.memp_CreatedDate <= "'.$EndSales.'" ';

} else {

	$where_date = '';
}


$sql_point = 'SELECT DISTINCT

					mi_card.image AS card_image,
					mi_card.image_newupload,
					mi_card.path_image,
					mi_card.name AS card_name,
					mi_branch.name AS branch_name,
					member_motivation_point_trans.memp_ReceiptNo AS receipt_no,
					member_motivation_point_trans.memp_PointQty AS point_qty,
					member_motivation_point_trans.memp_ReceiptAmount AS receipt_amount,
					member_motivation_point_trans.memp_CreatedDate AS date_collect,
					privilege.priv_Name,
					privilege.priv_Image,
					privilege.priv_ImageNew,
					privilege.priv_ImagePath,
					coupon.coup_Name,
					coupon.coup_Image,
					coupon.coup_ImageNew,
					coupon.coup_ImagePath,
					activity.acti_Name,
					activity.acti_Image,
					activity.acti_ImageNew,
					activity.acti_ImagePath

					FROM member_motivation_point_trans

					LEFT JOIN mb_member_register
					ON mb_member_register.member_register_id = member_motivation_point_trans.mere_MemberRegisterID

					LEFT JOIN privilege
					ON privilege.priv_PrivilegeID = member_motivation_point_trans.priv_PrivilegeID

					LEFT JOIN coupon
					ON coupon.coup_CouponID = member_motivation_point_trans.coup_CouponID

					LEFT JOIN activity
					ON activity.acti_ActivityID = member_motivation_point_trans.acti_ActivityID

					LEFT JOIN mi_card
					ON mb_member_register.card_id = mi_card.card_id

					LEFT JOIN mi_brand
					ON mi_brand.brand_id = mi_card.brand_id

					LEFT JOIN mi_branch
					ON member_motivation_point_trans.brnc_BranchID = mi_branch.branch_id

					WHERE mb_member_register.member_id = '.$member_id.'
					AND mi_brand.brand_id = '.$brand_id.' 
					AND member_motivation_point_trans.memp_Status="Active"
					'.$where_branch.'
					'.$where_date.'

					GROUP BY member_motivation_point_trans.memp_CreatedDate
					ORDER BY member_motivation_point_trans.memp_CreatedDate DESC';

$oRes_point = $oDB->Query($sql_point);

$check_point = $oDB->QueryOne($sql_point);

$total_sql = "SELECT SUM(member_motivation_point_trans.memp_ReceiptAmount)

					FROM member_motivation_point_trans

					LEFT JOIN mb_member_register
					ON mb_member_register.member_register_id = member_motivation_point_trans.mere_MemberRegisterID

					LEFT JOIN mi_card
					ON mb_member_register.card_id = mi_card.card_id

					LEFT JOIN mi_brand
					ON mi_brand.brand_id = mi_card.brand_id

					WHERE mb_member_register.member_id='".$member_id."'
					AND mi_brand.brand_id ='".$brand_id."'

					".$where_branch."

					".$where_date."";

$total_use = $oDB->QueryOne($total_sql);

$table_member = "<center><br>
					<span style='font-size:16px'><b>Total Sales &nbsp; : &nbsp; ".number_format($total_use,2)." ฿</span></b>
					<br></center>";

$table_member .= "<table id='example' class='table table-bordered' style='background-color:white;' >
					<thead>
						<tr class='th_table'>
							<th style='text-align:center'><b>Date Sales</b></th>
							<th style='text-align:center'><b>Card</b></th>
							<th style='text-align:center'><b>Privilege</b></th>
							<th style='text-align:center'><b>Branch</b></th>
							<th style='text-align:center'><b>Receipt No.</b></th>
							<th style='text-align:center'><b>Point Collect</b></th>
							<th style='text-align:center'><b>Sales</b></th>
						</tr>
					</thead>";

if ($check_point) {

	$table_member .= "<tbody>";

	while ($axRow = $oRes_point->FetchRow(DBI_ASSOC)){

		# CARD IMAGE

		if($axRow['image_newupload']!=''){

			$axRow['card_image'] = '<img src="../../upload/'.$axRow['path_image'].$axRow['image_newupload'].'" height="50px" class="img-rounded image_border"/>';

		} else if ($axRow['card_image']!='') {

			$axRow['card_image'] = '<img src="../../upload/'.$axRow['path_image'].$axRow['card_image'].'" height="50px" class="img-rounded image_border"/>';

		} else {

			$axRow['card_image'] = '<img src="../../images/card_privilege.jpg" height="50px" class="img-rounded image_border"/>';
		}


		# PRIVILEGE IMAGE

		if($axRow['priv_ImageNew']!=''){

			$privilege_img = '<img src="../../upload/'.$axRow['priv_ImagePath'].$axRow['priv_ImageNew'].'" height="50px" class="image_border">';

			$privilege_name = $axRow['priv_Name'];

		} else if ($axRow['priv_Image']!='') {

			$privilege_img = '<img src="../../upload/'.$axRow['priv_ImagePath'].$axRow['priv_Image'].'" height="50px" class="image_border">';

			$privilege_name = $axRow['priv_Name'];

		} else if ($axRow['coup_ImageNew']!='') {

			$privilege_img = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_ImageNew'].'" height="50px" class="image_border">';

			$privilege_name = $axRow['coup_Name'];

		} else if ($axRow['coup_Image']!='') {

			$privilege_img = '<img src="../../upload/'.$axRow['coup_ImagePath'].$axRow['coup_Image'].'" height="50px" class="image_border">';

			$privilege_name = $axRow['coup_Name'];

		} else if ($axRow['acti_ImageNew']!='') {

			$privilege_img = '<img src="../../upload/'.$axRow['acti_ImagePath'].$axRow['acti_ImageNew'].'" height="50px" class="image_border">';

			$privilege_name = $axRow['acti_Name'];

		} else if ($axRow['acti_Image']!='') {

			$privilege_img = '<img src="../../upload/'.$axRow['acti_ImagePath'].$axRow['acti_Image'].'" height="50px" class="image_border">';

			$privilege_name = $axRow['acti_Name'];

		} else {

			$privilege_img = '<img src="../../images/card_privilege.jpg" height="50px" class="image_border"/>';
		}


		# RECEIPT NO

		if (!$axRow['receipt_no']) { $axRow['receipt_no'] = '-'; }

			$table_member .= "<tr><td style='text-align:center'>".DateTime($axRow['date_collect'])."</td>
								<td style='text-align:center'>".$axRow['card_image']."<br>
									<span style='font-size:11px'>".$axRow['card_name']."</a></td>
								<td style='text-align:center'>".$privilege_img."<br>
									<span style='font-size:11px'>".$privilege_name."</a></td>
								<td>".$axRow['branch_name']."</td>
								<td>".$axRow['receipt_no']."</td>
								<td style='text-align:center'>".number_format($axRow['point_qty'])."</td>
								<td style='text-align:center;background-color:#F2F2F2'><b>".number_format($axRow['receipt_amount'],2)." ฿</b></td></tr>";
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

$oTmp->assign('content_file', 'analytics/top_purchase.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
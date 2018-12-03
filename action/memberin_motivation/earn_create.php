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

if (($_SESSION['role_action']['memberin_earn']['add'] != 1) || ($_SESSION['role_action']['memberin_earn']['edit'] != 1)) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$time_insert = date("Y-m-d H:i:s");



# SEARCH MAX MEMBERIN_EARN_MOTIVATION ID

	$sql_get_last_ins = 'SELECT max(memo_MemberinEarnID) FROM memberin_earn_motivation';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_new = $id_last_ins+1;

#######################################



if( $Act == 'edit' && $id ){

	# DATA EARN ATTENTION

	$sql = '';

	$sql = 'SELECT hilight_coupon.coup_CouponID,
			hilight_coupon.coup_Type,
			hilight_coupon.coup_Status,
			hilight_coupon.coup_Name,
			hilight_coupon.coup_Image,
			hilight_coupon.coup_ImagePath,
			mi_brand.name AS brand_name

			FROM hilight_coupon

			LEFT JOIN mi_brand
			ON mi_brand.brand_id = hilight_coupon.bran_BrandID

			WHERE hilight_coupon.coup_CouponID = "'.$id.'"';

	$oRes = $oDB->Query($sql)or die(mysql_error());
	$asData = array();
	$axRow = $oRes->FetchRow(DBI_ASSOC);

	if ($axRow['coup_Description']=="") { $axRow['coup_Description']="-"; }

	$asData = $axRow;


	# DATA TAB ACTION

	$sql = '';

	if ($axRow['coup_Type']=='Use') {

		$sql = 'SELECT memberin_action.*,
						miac_MemberinActionID AS id
					FROM memberin_action
					WHERE miac_Type="Stamp"';
	} else{

		$sql = 'SELECT memberin_action.*,
						miac_MemberinActionID AS id
					FROM memberin_action';
	}

	$oRes_mi = $oDB->Query($sql);

	$data_form = "";

	while ($axRow_mi = $oRes_mi->FetchRow(DBI_ASSOC)){

		$data_form .= '<div class="adj_row">
		                    <label class="lable-form">'.$axRow_mi['miac_Name'].'</label>
		                    <span class="form-inline">
		                        <select id="action_'.$axRow_mi['miac_MemberinActionID'].'" class="form-control text-md" name="action_'.$axRow_mi['miac_MemberinActionID'].'">
		                            <option value="">Please Select ..</option>';

		if ($axRow_mi['miac_Type']=='Point') {

			$sql_moti = 'SELECT mipo_MemberinPointID AS id,
							mipo_CollectionMethod AS CollectionMethod,
							mipo_PeriodTime AS PeriodTime,
							mipo_PeriodType AS PeriodType,
							mipo_PeriodTypeEnd AS PeriodTypeEnd,
							mipo_EndDate AS EndDate,
							CONCAT(mipo_PointQty," ",collection_type.coty_Name," / ",mipo_UseAmount," ฿") AS name
							FROM memberin_point 
							LEFT JOIN collection_type
							ON memberin_point.mipo_CollectionTypeID = collection_type.coty_CollectionTypeID
							WHERE mipo_Deleted=""';

		} else {

			$sql_moti = 'SELECT memberin_stamp.mist_MemberinStampID AS id,
							mist_CollectionMethod AS CollectionMethod,
							mist_PeriodTime AS PeriodTime,
							mist_PeriodType AS PeriodType,
							mist_PeriodTypeEnd AS PeriodTypeEnd,
							mist_EndDate AS EndDate,
							CONCAT(memberin_stamp.mist_StampQty, " ",collection_type.coty_Name," / 1 Times") AS name
							FROM memberin_stamp 
							LEFT JOIN collection_type
							ON memberin_stamp.mist_CollectionTypeID = collection_type.coty_CollectionTypeID
							WHERE mist_Deleted=""';
		}

		$oRes_moti = $oDB->Query($sql_moti);

		$sql_check = 'SELECT memo_MemberinMotivationID 
						FROM memberin_earn_motivation 
						WHERE hico_HilightCouponID="'.$id.'"
						AND miac_MemberinActionID="'.$axRow_mi['miac_MemberinActionID'].'"';

		$check = $oDB->QueryOne($sql_check);

		while ($moti = $oRes_moti->FetchRow(DBI_ASSOC)){

			# METHOD

			if ($moti['CollectionMethod']=='No') {

				$exp_earn = 'No Expiry';

			} else if ($moti['CollectionMethod']=='Exp') {

				if ($moti['PeriodType']=='Y') {  $moti['PeriodType'] = 'Years';	}

				if ($moti['PeriodType']=='M') {  $moti['PeriodType'] = 'Months';	}

				if ($moti['PeriodTypeEnd']=='Y') {  $moti['PeriodTypeEnd'] = 'End of Year';	}

				if ($moti['PeriodTypeEnd']=='M') {  $moti['PeriodTypeEnd'] = 'End of Month';	}

				$exp_earn = $moti['PeriodTime'].' '.$moti['PeriodType'].' ('.$moti['PeriodTypeEnd'].')';

			} else if ($moti['CollectionMethod']=='Fix') {

				$exp_earn = 'Expire: '.DateOnly($moti['EndDate']);
					
			} else { $moti['CollectionMethod'] = '-'; }


			$select = '';
			if ($check == $moti['id']) { $select = 'selected'; }
		                            
			$data_form .= '<option value="'.$moti['id'].'" '.$select.'>'.$moti['name'].' &nbsp; ('.$exp_earn.')'.'</option>';
		}
		                            
		$data_form .= '         </select>
		                    </span>
		                </div>';
	}

} else if( $Act == 'save' ){

	$hico_HilightCouponID = trim_txt($_REQUEST['id']);

	$sql = '';

	$sql = 'SELECT miac_MemberinActionID AS id FROM memberin_action';

	$oRes = $oDB->Query($sql);

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$id = trim_txt($axRow['id']);

		$memo_MemberinMotivationID = trim_txt($_REQUEST['action_'.$id]);




		$sql_stamp = '';

		$table_stamp = 'memberin_earn_motivation';



		# ACTION EARN STAMP

		if($id){	$sql_stamp .= 'miac_MemberinActionID="'.$id.'"';   }

		if($hico_HilightCouponID){	$sql_stamp .= ',hico_HilightCouponID="'.$hico_HilightCouponID.'"';   }

		if($memo_MemberinMotivationID){	$sql_stamp .= ',memo_MemberinMotivationID="'.$memo_MemberinMotivationID.'"';   }

		if($_SESSION['UID']){	$sql_stamp .= ',memo_UpdatedBy="'.$_SESSION['UID'].'"';   }

		if($time_insert){	$sql_stamp .= ',memo_UpdatedDate="'.$time_insert.'"';	}



		# CHECK

		$sql_check = 'SELECT memo_MemberinEarnID 
						FROM memberin_earn_motivation 
						WHERE hico_HilightCouponID="'.$hico_HilightCouponID.'"
						AND miac_MemberinActionID="'.$id.'"';

		$check = $oDB->QueryOne($sql_check);

		if ($check){

			# UPDATE

			$do_table_stamp = "UPDATE ".$table_stamp." SET ".$sql_stamp." WHERE memo_MemberinEarnID= '".$check."'";
			$oDB->QueryOne($do_table_stamp);

		} else {

			# INSERT

			if($_SESSION['UID']){	$sql_stamp .= ',memo_CreatedBy="'.$_SESSION['UID'].'"';   }

			if($time_insert){	$sql_stamp .= ',memo_CreatedDate="'.$time_insert.'"';	}

			if($id_new){	$sql_stamp .= ',memo_MemberinEarnID="'.$id_new.'"';   }

			$id_new++;

			$do_table_stamp = 'INSERT INTO '.$table_stamp.' SET '.$sql_stamp;

			$oDB->QueryOne($do_table_stamp);
		}
	}

	echo '<script>window.location.href="earn.php";</script>';

	exit;
}





$oTmp->assign('data', $asData);

$oTmp->assign('data_form', $data_form);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_memberin_earn');

$oTmp->assign('content_file', 'memberin_motivation/earn_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
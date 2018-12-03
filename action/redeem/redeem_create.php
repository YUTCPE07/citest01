<?php

header('Content-Type:text/html; charset=UTF-8');

//========================================//

ini_set("display_errors",1);
error_reporting(1);

//========================================//

include('../../include/common.php');
include('../../lib/function_normal.php');
include('../../include/common_check.php');
include('../../lib/phpqrcode/qrlib.php');
require_once('../../include/connect.php');

//========================================//

$oTmp = new TemplateEngine();
$oDB = new DBI();

if ($bDebug) {

	$oErr = new Tracker();
	$oDB->SetTracker($oErr);
}

//========================================//

if (($_SESSION['role_action']['redeems']['add'] != 1) || ($_SESSION['role_action']['redeems']['edit'] != 1)) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];

$time_insert = date("Y-m-d H:i:s");	



# SEARCH MAX REDEEM ID

	$sql_get_last_ins = 'SELECT max(rede_RewardRedeemID) FROM reward_redeem';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_redeem_new = $id_last_ins+1;

#######################################

# SEARCH MAX RATIO ID

	$sql_get_last_ins = 'SELECT max(rera_RewardRatioID) FROM reward_ratio';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$id_ratio_new = $id_last_ins+1;

#######################################



if( $Act == 'edit' && $id != '' ){

	$sql = '';
	$sql .= 'SELECT 
				reward_redeem.*,
				reward_ratio.*

				FROM reward_redeem

				LEFT JOIN reward_ratio
				ON reward_ratio.rede_RewardRedeemID = reward_redeem.rede_RewardRedeemID

				WHERE reward_redeem.rede_RewardRedeemID = "'.$id.'" ';

	$oRes = $oDB->Query($sql);

	$i=0;

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$axRow['rede_TimeToCancel'] = $axRow['rede_TimeToCancel']/3600;

		$asData = $axRow;

		$data_branch = "";

		if ($axRow['brnc_BranchID']) {

			$token = strtok($axRow['brnc_BranchID'] , ",");
			$branch = array();

			$i = 0;

			while ($token !== false) {	

		    	$branch[$i] =  $token;
		    	$token = strtok(",");
		    	$i++;
			}

			$arrlength = count($branch);

			for($x = 0; $x < $arrlength; $x++) {

				$sql_branch = 'SELECT name FROM mi_branch WHERE branch_id = "'.$branch[$x].'"';
				$name = $oDB->QueryOne($sql_branch);

				$data_branch .= '<tr>
									<td style="text-align:center">'.$name.'</td>
									<td style="text-align:center"><img src="../../upload/'.$axRow['rede_QrPath'].'RDB-'.str_pad($id,4,"0",STR_PAD_LEFT).'-'.str_pad($branch[$x],4,"0",STR_PAD_LEFT).'.png" width="50" height="50" class="image_border"/></td>
									<td style="text-align:center"><a target="_blank" href="redeem_qrcode.php?id='.$id.'&branch='.$branch[$x].'">QRCode Link</td>
								</tr>';
			}

		} else {

			$data_branch = '<tr><td colspan="3" style="text-align:center">No Branch Data</td></tr>';
		}

		$oTmp->assign('branch_data', $data_branch);
	}

} else if( $Act == 'save' ){		

	$id = trim_txt($_REQUEST['id']);

	$bran_BrandID = trim_txt($_REQUEST['bran_BrandID']);	

	$rede_Time = trim_txt($_REQUEST['rede_Time']);			

	$rede_Name = trim_txt($_REQUEST['rede_Name']);		

	$rewa_RewardID = trim_txt($_REQUEST['rewa_RewardID']);					

	$rede_Description = trim_txt($_REQUEST['rede_Description']);				

	$rede_RedeemLimit = trim_txt($_REQUEST['rede_RedeemLimit']);		

	$rede_StartDate = trim_txt($_REQUEST['StartDate']);				

	$rede_EndDate = trim_txt($_REQUEST['EndDate']);		

	$rede_NumberTime = trim_txt($_REQUEST['rede_NumberTime']);

	$rede_AutoRedeem = trim_txt($_REQUEST['rede_AutoRedeem']);						

	$rede_Condition = trim_txt($_REQUEST['rede_Condition']);			

	$rede_Payment = trim_txt($_REQUEST['rede_Payment']);			

	$rede_Expired = trim_txt($_REQUEST['rede_Expired']);

	$rera_RewardQty = trim_txt($_REQUEST['RewardQty']);					

	$rera_RewardQty_Stamp = trim_txt($_REQUEST['RewardQty_Stamp']);

	$rera_RewardQty_Point = trim_txt($_REQUEST['RewardQty_Point']);			

	$rera_AmountPlus = trim_txt($_REQUEST['rera_AmountPlus']);			

	$coty_CollectionTypeID = trim_txt($_REQUEST['CollectionTypeID']);			

	$basic_type = trim_txt($_REQUEST['basic_type']);

	$rede_Hidden = trim_txt($_REQUEST['rede_Hidden']);

	$rede_Repetition = trim_txt($_REQUEST['rede_Repetition']);

	$rede_Qty = trim_txt($_REQUEST['rede_Qty']);

	$rede_QtyPer = trim_txt($_REQUEST['rede_QtyPer']);

	$data = "";

	foreach ($_POST['QtyPerData'] as $rede_QtyPerData)

		$data .= $rede_QtyPerData.",";

	$str_data = strlen($data);

	$rede_QtyPerData = substr($data,0,$str_data-1);



	# CARD

	$card_data = '';

	foreach ($_POST['rera_CardID'] as $card_id) {

		$card_data .= $card_id.",";
	}

	$str_card = strlen($card_data);

	$card_data = substr($card_data,0,$str_card-1);



	# BRANCH

	$branch_data = "";

	foreach ($_POST['brnc_BranchID'] as $branch_id) {

		$branch_data .= $branch_id.",";

		if ($id) { 

			$qrcode .= 'RDB-'.str_pad($id,4,"0",STR_PAD_LEFT).'-'.str_pad($branch_id,4,"0",STR_PAD_LEFT).",";

		} else { 

			$qrcode .= 'RDB-'.str_pad($id_new,4,"0",STR_PAD_LEFT).'-'.str_pad($branch_id,4,"0",STR_PAD_LEFT).","; 
		}
	}

	$str_branch = strlen($branch_data);

	$branch_data = substr($branch_data,0,$str_branch-1);

	$qrcode = substr($qrcode,0,$str_branch-1);



	$do = '';

	$sql_redeem = '';

	$sql_ratio = '';

	$table_redeem = 'reward_redeem';

	$table_redeem_ratio = 'reward_ratio';



	# Action with redeem table		

	if($rede_Name){	$sql_redeem .= 'rede_Name="'.$rede_Name.'"';   }	

	if($bran_BrandID){	$sql_redeem .= ',bran_BrandID="'.$bran_BrandID.'"';   }

	$sql_redeem .= ',rede_Status="Active"';  

	if($branch_data){	$sql_redeem .= ',brnc_BranchID="'.$branch_data.'"';   }

	if($rewa_RewardID){	$sql_redeem .= ',rewa_RewardID="'.$rewa_RewardID.'"';   }

	if($rede_Expired){	$sql_redeem .= ',rede_Expired="'.$rede_Expired.'"';   }

	$sql_redeem .= ',rede_Description="'.$rede_Description.'"';  

	if($rede_Time=='T'){	

		$sql_redeem .= ',rede_Time="'.$rede_Time.'"';   
		if($rede_StartDate){	$sql_redeem .= ',rede_StartDate="'.$rede_StartDate.'"';   }
		if($rede_EndDate){	$sql_redeem .= ',rede_EndDate="'.$rede_EndDate.'"';   }	

	} else {

		$sql_redeem .= ',rede_Time="'.$rede_Time.'"';   
		$sql_redeem .= ',rede_StartDate="0000-00-00"';   
		$sql_redeem .= ',rede_EndDate="0000-00-00"';   
	}	

	if ($basic_type=='Card') {	

		$sql_redeem .=',rede_AutoRedeem="T"';

	} else {

		if($rede_AutoRedeem){	$sql_redeem .=',rede_AutoRedeem="'.$rede_AutoRedeem.'"';	}
	}

	if($rede_RedeemLimit){	$sql_redeem .= ',rede_RedeemLimit="'.$rede_RedeemLimit.'"';   }	

	if($rede_NumberTime){	$sql_redeem .= ',rede_NumberTime="'.$rede_NumberTime.'"';   }

	if($rede_Repetition!='' && $rede_Qty!='0' && $rede_QtyPer!=''){

		$sql_redeem .= ',rede_Qty="'.$rede_Qty.'"';

		$sql_redeem .= ',rede_QtyPer="'.$rede_QtyPer.'"';

		$sql_redeem .= ',rede_Repetition="'.$rede_Repetition.'"';

		$sql_redeem .= ',rede_QtyPerData="'.$rede_QtyPerData.'"';

	} else {

		$sql_redeem .= ',rede_Qty="0"';

		$sql_redeem .= ',rede_Repetition=""';

		$sql_redeem .= ',rede_QtyPer=""';

		$sql_redeem .= ',rede_QtyPerData=""';
	}

	$sql_redeem.=',rede_Condition="'.$rede_Condition.'"';		

	if($rede_Payment){		$sql_redeem.=',rede_Payment="'.$rede_Payment.'"';	}

	if($time_insert){	$sql_redeem .= ',rede_UpdatedDate="'.$time_insert.'"';   }

	if($_SESSION['UID']){	$sql_redeem .= ',rede_UpdatedBy="'.$_SESSION['UID'].'"';   }

	if($rede_Hidden){	$sql_redeem .= ',rede_Hidden="'.$rede_Hidden.'"';   }
	else {	$sql_redeem .= ',rede_Hidden="No"';	}

	if ($basic_type=='Stamp') {			

		$sql_ratio .= 'rera_RewardQty_Stamp="'.$rera_RewardQty_Stamp.'"';   
		$sql_ratio .= ',rera_RewardQty_Point="0"';	
		$sql_ratio .= ',coty_CollectionTypeID="'.$coty_CollectionTypeID.'"';
		$sql_ratio .= ',rera_CardID=""';
	}		

	if ($basic_type=='Point') {			

		$sql_ratio .= 'rera_RewardQty_Stamp="0"';   
		$sql_ratio .= ',rera_RewardQty_Point="'.$rera_RewardQty_Point.'"';	
		$sql_ratio .= ',coty_CollectionTypeID="0"';	
		$sql_ratio .= ',rera_CardID=""';
	}		

	if ($basic_type=='Card') {	

		$sql_ratio .= 'rera_CardID="'.$card_data.'"';		
		$sql_ratio .= ',rera_RewardQty_Stamp="0"';   
		$sql_ratio .= ',rera_RewardQty_Point="0"';	
		$sql_ratio .= ',coty_CollectionTypeID="0"';	
	}			


	# CHECK CARD

	$sql_card = "SELECT rewa_Type FROM reward WHERE rewa_RewardID='".$rewa_RewardID."'";

	$rewa_Type = $oDB->QueryOne($sql_card);


	if ($rewa_Type == 'Card') { $sql_ratio .= ',rera_RewardQty="1"'; } 

	else { $sql_ratio .= ',rera_RewardQty="'.$rera_RewardQty.'"';	}


	if($rera_AmountPlus){	$sql_ratio .=',rera_AmountPlus="'.$rera_AmountPlus.'"';		}

	if($time_insert){	$sql_ratio .= ',rera_UpdatedDate="'.$time_insert.'"';   }

	if($_SESSION['UID']){	$sql_ratio .= ',rera_UpdatedBy="'.$_SESSION['UID'].'"';   }

		

	if($id){

		# UPDATE	

		$do_sql_redeem = "UPDATE ".$table_redeem." SET ".$sql_redeem." WHERE rede_RewardRedeemID= '".$id."'";

		$oDB->QueryOne($do_sql_redeem);


		$do_sql_ratio = "UPDATE ".$table_redeem_ratio." SET ".$sql_ratio." WHERE rede_RewardRedeemID= '".$id."'";

		$oDB->QueryOne($do_sql_ratio);

	} else {

		# INSERT

		$sql_redeem .= ',rede_QrPath="'.$bran_BrandID.'/qr_redeem_upload/"';   

		$sql_redeem .= ',rede_Qr="RDC-'.$id_redeem_new.'"';   

		if($time_insert){	$sql_redeem .= ',rede_CreatedDate="'.$time_insert.'"';   }

		if($_SESSION['UID']){	$sql_redeem .= ',rede_CreatedBy="'.$_SESSION['UID'].'"';   }

		if($id_redeem_new){	$sql_redeem .= ',rede_RewardRedeemID="'.$id_redeem_new.'"';   }

		$do_sql_redeem = 'INSERT INTO '.$table_redeem.' SET '.$sql_redeem;	

		$oDB->QueryOne($do_sql_redeem);



		if($time_insert){	$sql_ratio .= ',rera_CreatedDate="'.$time_insert.'"';   }

		if($_SESSION['UID']){	$sql_ratio .= ',rera_CreatedBy="'.$_SESSION['UID'].'"';   }

		if($id_redeem_new){	$sql_ratio .= ',rede_RewardRedeemID="'.$id_redeem_new.'"';   }

		if($id_ratio_new){	$sql_ratio .= ',rera_RewardRatioID="'.$id_ratio_new.'"';   }

		$do_sql_ratio = 'INSERT INTO '.$table_redeem_ratio.' SET '.$sql_ratio;	

		$oDB->QueryOne($do_sql_ratio);



		# QRCODE

		$qr_code_text = "RDC-".$id_redeem_new;

		$errorCorrectionLevel = 'H';

		$matrixPointSize = 10;		

		$qr_code_image = 'RDC-'.$id_redeem_new.'.png';

		$file_full_path = '../../upload/'.$bran_BrandID.'/qr_redeem_upload/'.$qr_code_image;

		QRcode::png($qr_code_text, $file_full_path, $errorCorrectionLevel, $matrixPointSize, 2); 

		$id = $id_redeem_new;
	}



	# QRCODE

	foreach ($_POST['brnc_BranchID'] as $branch_id) {

		$qrcode_redeem_text = "RDB-".str_pad($id,4,"0",STR_PAD_LEFT)."-"
									.str_pad($branch_id,4,"0",STR_PAD_LEFT)."";

		$file_full_path = '../../upload/'.$bran_BrandID.'/qr_redeem_upload/'.$qrcode_redeem_text.".png";

		$qrcode_url = $qrcode_redeem_text.".png";

		$errorCorrectionLevel = 'H'; 

		$matrixPointSize = 10;	

		QRcode::png($qrcode_redeem_text, $file_full_path, $errorCorrectionLevel, $matrixPointSize, 2); 
	}

	echo '<script>window.location.href="redeem.php";</script>';

	exit;		
}




#  brand dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' brand_id="'.$_SESSION['user_brand_id'].'" ';
}

$as_brand = dropdownlist_from_table($oDB,'mi_brand','brand_id','name',$where_brand,' ORDER BY name ASC');

$oTmp->assign('brand', $as_brand);



#  reward dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand_2 = ' bran_BrandID="'.$_SESSION['user_brand_id'].'" ';
}

$as_reward = dropdownlist_from_table($oDB,'reward','rewa_RewardID','rewa_Name',$where_brand_2,' ORDER BY rewa_Name ASC');

$oTmp->assign('reward', $as_reward);



#  icon dropdownlist

$as_icon = dropdownlist_from_table($oDB,'collection_type','coty_CollectionTypeID','coty_Name');

$oTmp->assign('collection_type', $as_icon);


$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_redeems');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_motivation', 'in');

$oTmp->assign('content_file', 'redeem/redeem_create.htm');

$oTmp->display('layout/template.html');


//========================================//


$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
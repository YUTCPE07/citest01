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


$Act = $_REQUEST['act'];

$time_insert = date("Y-m-d H:i:s");



# SEARCH MAX PROMOTION CODE ID

	$sql_get_last_ins = 'SELECT max(spco_SpecialCodeID) FROM special_code';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$code_id = $id_last_ins+1;

#######################################


# SEARCH MAX PROMOTION CODE LIST ID

	$sql_get_last_ins = 'SELECT max(spcl_SpecialCodeListID) FROM special_code_list';
	$id_last_ins = $oDB->QueryOne($sql_get_last_ins);
	$list_id = $id_last_ins+1;

#######################################



	if( $Act == 'save' ){


		$bran_BrandID = trim_txt($_REQUEST['bran_BrandID']);

		$spco_StartDate = trim_txt($_REQUEST['StartDate']);

		$spco_EndDate = trim_txt($_REQUEST['EndDate']);

		$spco_SendDate = trim_txt($_REQUEST['SendDate']);

		$promotion_code = trim_txt($_REQUEST['promotion_code']);

		$no_promotion = trim_txt($_REQUEST['no_promotion']);

		$spco_MemberType = trim_txt($_REQUEST['spco_MemberType']);




		$sql_special = '';

		$table_special = 'special_code';



		$sql_list = '';

		$table_list = 'special_code_list';



		# Action with special_code table


		if ($spco_MemberType == 'Register') {

			foreach ($_POST['memb_MemberID'] as $memb_MemberID) {

				$sql = "SELECT email, mobile FROM mb_member WHERE member_id=".$memb_MemberID;

				$oRes = $oDB->Query($sql);

				while ($axRow = $oRes->FetchRow(DBI_ASSOC)) {

					$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz0123456789";
					$code = ''; 

					$code_str = 10;

					if ($promotion_code) {

					    $code_str = strlen($promotion_code);
					    $code_str = 10-$code_str;
					}

					$code = $promotion_code . substr(str_shuffle($chars), 0, $code_str);

					$sql_get_code = 'SELECT spco_SpecialCode FROM special_code WHERE spco_SpecialCode='.$code;
					$get_code = $oDB->QueryOne($sql_get_code);

					while ($get_code) {

						$code = $promotion_code . substr(str_shuffle($chars), 0, $code_str);

						$sql_get_code = 'SELECT spco_SpecialCode FROM special_code WHERE spco_SpecialCode='.$code;
						$get_code = $oDB->QueryOne($sql_get_code);
					}



					// PROMOTION CODE


					if($code){	$sql_special = 'spco_SpecialCode="'.$code.'"';   }

					if($code_id){	$sql_special .= ',spco_SpecialCodeID="'.$code_id.'"';   }

					if($bran_BrandID){	$sql_special .= ',bran_BrandID="'.$bran_BrandID.'"';   }

					if($spco_StartDate){	$sql_special .= ',spco_StartDate="'.$spco_StartDate.'"';   }

					if($spco_EndDate){	$sql_special .= ',spco_EndDate="'.$spco_EndDate.'"';   }

					if($time_insert){	$sql_special .= ',spco_UpdatedDate="'.$time_insert.'"';   }

					if($_SESSION['UID']){	$sql_special .= ',spco_UpdatedBy="'.$_SESSION['UID'].'"';   }

					if($time_insert){	$sql_special .= ',spco_CreatedDate="'.$time_insert.'"';   }

					if($_SESSION['UID']){	$sql_special .= ',spco_CreatedBy="'.$_SESSION['UID'].'"';   }

					$sql_special .= ',spco_Status="Pending"';

					$sql_special .= ',spco_Type="Card"';

					$sql_special .= ',memb_MemberID="'.$memb_MemberID.'"';

					$sql_special .= ',spco_Email="'.$axRow['email'].'"';

					$sql_special .= ',spco_Mobile="'.$axRow['mobile'].'"';


					$do_sql_special = 'INSERT INTO '.$table_special.' SET '.$sql_special;

					$oDB->QueryOne($do_sql_special);
				

					foreach ($_POST['card_CardID'] as $card_CardID) {


						// PROMOTION LIST

						if($list_id){	$sql_list = 'spcl_SpecialCodeListID="'.$list_id.'"';   }

						if($code_id){	$sql_list .= ',spco_SpecialCodeID="'.$code_id.'"';   }

						if($card_CardID){	$sql_list .= ',spcl_ID="'.$card_CardID.'"';   }

						if($time_insert){	$sql_list .= ',spcl_UpdatedDate="'.$time_insert.'"';   }

						if($_SESSION['UID']){	$sql_list .= ',spcl_UpdatedBy="'.$_SESSION['UID'].'"';   }

						if($time_insert){	$sql_list .= ',spcl_CreatedDate="'.$time_insert.'"';   }

						if($_SESSION['UID']){	$sql_list .= ',spcl_CreatedBy="'.$_SESSION['UID'].'"';   }


						$do_sql_list = 'INSERT INTO '.$table_list.' SET '.$sql_list;

						$oDB->QueryOne($do_sql_list);

						$list_id++;
					}

					$code_id++;
				}
			}

		} else if ($spco_MemberType == 'New') {


			# NEW MEMBER

			for ($j=0; $j < $no_promotion; $j++) {

				$email = $_POST['email'][$j];
				$mobile = $_POST['mobile'][$j];

				$mobile_new = '0'.$mobile;
				$mobile = '+66'.$mobile;

				# SEARCH MEMBER

				$sql_member = 'SELECT member_id FROM mb_member WHERE email="'.$email.'" OR mobile="'.$mobile.'" OR mobile="'.$mobile_new.'"';
				$member_id = $oDB->QueryOne($sql_member);

				$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz0123456789";
				$code = ''; 

				if ($promotion_code) {

					$code_str = strlen($promotion_code);
					$code_str = 10-$code_str;
				}

				$code = $promotion_code . substr(str_shuffle($chars), 0, $code_str);

				$sql_get_code = 'SELECT spco_SpecialCode FROM special_code WHERE spco_SpecialCode='.$code;
				$get_code = $oDB->QueryOne($sql_get_code);

				while ($get_code) {

					$code = $promotion_code . substr(str_shuffle($chars), 0, $code_str);

					$sql_get_code = 'SELECT spco_SpecialCode FROM special_code WHERE spco_SpecialCode='.$code;
					$get_code = $oDB->QueryOne($sql_get_code);
				}



				// PROMOTION CODE


				$sql_special = 'spco_SpecialCode="'.$code.'"';   

				$sql_special .= ',spco_SpecialCodeID="'.$code_id.'"';   

				$sql_special .= ',bran_BrandID="'.$bran_BrandID.'"';   

				$sql_special .= ',spco_StartDate="'.$spco_StartDate.'"';   

				$sql_special .= ',spco_EndDate="'.$spco_EndDate.'"';   

				$sql_special .= ',spco_SendDate="'.$spco_SendDate.'"';  

				$sql_special .= ',spco_UpdatedDate="'.$time_insert.'"';   

				$sql_special .= ',spco_UpdatedBy="'.$_SESSION['UID'].'"';   

				$sql_special .= ',spco_CreatedDate="'.$time_insert.'"';   

				$sql_special .= ',spco_CreatedBy="'.$_SESSION['UID'].'"';   

				$sql_special .= ',spco_Status="Pending"';

				$sql_special .= ',spco_Type="Card"';

				$sql_special .= ',spco_Email="'.$email.'"';

				if ($mobile) { $sql_special .= ',spco_Mobile="'.$mobile.'"'; }

				$sql_special .= ',memb_MemberID="'.$member_id.'"';


				$do_sql_special = 'INSERT INTO '.$table_special.' SET '.$sql_special;

				$oDB->QueryOne($do_sql_special);
				

				foreach ($_POST['card_CardID'] as $card_CardID) {


					// PROMOTION LIST

					if($list_id){	$sql_list = 'spcl_SpecialCodeListID="'.$list_id.'"';   }

					if($code_id){	$sql_list .= ',spco_SpecialCodeID="'.$code_id.'"';   }

					if($card_CardID){	$sql_list .= ',spcl_ID="'.$card_CardID.'"';   }

					if($time_insert){	$sql_list .= ',spcl_UpdatedDate="'.$time_insert.'"';   }

					if($_SESSION['UID']){	$sql_list .= ',spcl_UpdatedBy="'.$_SESSION['UID'].'"';   }

					if($time_insert){	$sql_list .= ',spcl_CreatedDate="'.$time_insert.'"';   }

					if($_SESSION['UID']){	$sql_list .= ',spcl_CreatedBy="'.$_SESSION['UID'].'"';   }


					$do_sql_list = 'INSERT INTO '.$table_list.' SET '.$sql_list;

					$oDB->QueryOne($do_sql_list);

					$list_id++;
				}

				$code_id++;
			}

		} else {

			# NOT SPECIFIC

			$id_start = 0;

			$id_end = 0;

			for ($k=0; $k < $no_promotion; $k++) {

				// PROMOTION CODE

				if ($k==0) { 

					$id_start = $code_id;
					$id_end = $code_id;

				} else if ($k == ($no_promotion-1)) { $id_end = $code_id; }

				$sql_special = 'spco_SpecialCode="'.$_SESSION['random_code'][$k].'"';   

				$sql_special .= ',spco_SpecialCodeID="'.$code_id.'"';   

				$sql_special .= ',bran_BrandID="'.$bran_BrandID.'"';   

				$sql_special .= ',spco_StartDate="'.$spco_StartDate.'"';   

				$sql_special .= ',spco_EndDate="'.$spco_EndDate.'"';   

				$sql_special .= ',spco_UpdatedDate="'.$time_insert.'"';   

				$sql_special .= ',spco_UpdatedBy="'.$_SESSION['UID'].'"';   

				$sql_special .= ',spco_CreatedDate="'.$time_insert.'"';   

				$sql_special .= ',spco_CreatedBy="'.$_SESSION['UID'].'"';   

				$sql_special .= ',spco_Status="Pending"';

				$sql_special .= ',spco_Type="Card"';


				$do_sql_special = 'INSERT INTO '.$table_special.' SET '.$sql_special;

				$oDB->QueryOne($do_sql_special);

				foreach ($_POST['card_CardID'] as $card_CardID) {


					// PROMOTION LIST

					if($list_id){	$sql_list = 'spcl_SpecialCodeListID="'.$list_id.'"';   }

					if($code_id){	$sql_list .= ',spco_SpecialCodeID="'.$code_id.'"';   }

					if($card_CardID){	$sql_list .= ',spcl_ID="'.$card_CardID.'"';   }

					if($time_insert){	$sql_list .= ',spcl_UpdatedDate="'.$time_insert.'"';   }

					if($_SESSION['UID']){	$sql_list .= ',spcl_UpdatedBy="'.$_SESSION['UID'].'"';   }

					if($time_insert){	$sql_list .= ',spcl_CreatedDate="'.$time_insert.'"';   }

					if($_SESSION['UID']){	$sql_list .= ',spcl_CreatedBy="'.$_SESSION['UID'].'"';   }


					$do_sql_list = 'INSERT INTO '.$table_list.' SET '.$sql_list;

					$oDB->QueryOne($do_sql_list);

					$list_id++;
				}

				$code_id++;
			}

			$url_data = 'print_code.php?start='.$id_start.'&end='.$id_end;

			$link = '<script>window.open("https://www.memberin.com/demo/action/special_code/'.$url_data.'", "_blank");</script>';

	        echo $link;
		}



		echo '<script>window.location.href="card.php";</script>';

		exit;

	}




#  brand dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' and brand_id="'.$_SESSION['user_brand_id'].'" ';
}

$as_brand_id = dropdownlist_from_table($oDB,'mi_brand','brand_id','name','brand_id>0'.$where_brand,' ORDER BY name ASC');

$oTmp->assign('brand_id_opt', $as_brand_id);



#  card dropdownlist

$as_card = dropdownlist_from_table($oDB,'mi_card','card_id','name',' (card_type_id="7" OR special_code="T")'.$where_brand,' ORDER BY name ASC');

$oTmp->assign('card', $as_card);



$oTmp->assign('data', $asData);

$oTmp->assign('data_list', $data_list);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_pro_card');

$oTmp->assign('content_file', 'special_code/card_create.htm');

$oTmp->display('layout/layout.htm');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());

}

//========================================//

?>

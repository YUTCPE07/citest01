<?php

header('Content-Type:text/html; charset=UTF-8');

//========================================//

ini_set("display_errors",1);
error_reporting(1);

//========================================//

include('../../include/common.php');
include('../../lib/function_normal.php');
include('../../include/common_check.php');
include('../../omise/lib/Omise.php');
require_once('../../include/connect.php');

//========================================//


$oTmp = new TemplateEngine();
$oDB = new DBI();

if ($bDebug) {

	$oErr = new Tracker();
	$oDB->SetTracker($oErr);
}

//========================================//

if (($_SESSION['role_action']['bank_account']['add'] != 1) || ($_SESSION['role_action']['bank_account']['edit'] != 1)) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];

$id = $_REQUEST['id'];



$where_brand = '';

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' where a.brand_id = "'.$_SESSION['user_brand_id'].'"';
}




# SEARCH RECIPIENT #

	$sql_get_recipient = 'SELECT recipient_token FROM mi_brand_bank_account WHERE brand_bank_account_id ='.$id;
	$get_omise_recipient = $oDB->QueryOne($sql_get_recipient);
	$recipient_data = $get_omise_recipient;

################################

# SEARCH MAX BANK ID #

	$sql_get_last_ins = 'SELECT max(brand_bank_account_id) FROM mi_brand_bank_account';
	$new_bank_account_id = $oDB->QueryOne($sql_get_last_ins);
	$new_bank_account_id = $new_bank_account_id+1;

################################


if( $Act == 'edit' && $id != '' ){

	# EDIT

	$asRecipient = OmiseRecipient::retrieve($recipient_data);

	$sql='	SELECT a.*, b.name AS brand_name
			FROM mi_brand_bank_account as a

			LEFT JOIN mi_token_type as b
			ON a.token_type_id = b.token_type_id

			WHERE brand_bank_account_id ='.$id;

	$oRes = $oDB->Query($sql);

	$asData = array();

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$asData = $axRow;
	}


} else if( $Act == 'save' ){

	$brand_id = trim_txt($_REQUEST['bran_BrandID']);

	$bank_brand = trim_txt($_REQUEST['bank_brand']);

	$account_type = trim_txt($_REQUEST['account_type']);

	$bank_name = trim_txt($_REQUEST['bank_name']);

	$bank_number = trim_txt($_REQUEST['bank_number']);

	$default_status = trim_txt($_REQUEST['default_status']);

	$time_insert = date("Y-m-d H:i:s");


	# BRANCH

	$branch_data = "";

	foreach ($_POST['brnc_BranchID'] as $branch_id) {

		$branch_data .= $branch_id.",";
	}

	$str_branch = strlen($branch_data);

	$branch_data = substr($branch_data,0,$str_branch-1);




	$sql_bank = '';

	$table_bank = 'mi_brand_bank_account';



	$sql='	SELECT * FROM mi_brand WHERE brand_id = '.$brand_id;

	$oRes = $oDB->Query($sql);

	$axRow = $oRes->FetchRow(DBI_ASSOC);



	if($brand_id){	$sql_bank .= 'brand_id="'.$brand_id.'"';   }

	if($branch_data){	$sql_bank .= ',branch_id="'.$branch_data.'"';   }

	if($default_status){	$sql_bank .= ',default_status="'.$default_status.'"';   }

	if (strpos($bank_number, 'XXXXXX') === FALSE) {

		if($bank_number){	$sql_bank .= ',bank_account_number="'.$bank_number.'"';   }
	}



	if($id!='' && $id>0){

		# UPDATE

		$recipient = OmiseRecipient::retrieve($recipient_data);

		if (($account_type!=$recipient['type']) || ($bank_brand!=$recipient['bank_account']['brand']) || 

			($bank_name!=$recipient['bank_account']['name']) || (strpos($bank_number, 'XXXXXX') === FALSE)) {	

			if (strpos($bank_number, 'XXXXXX') === FALSE) {

				$recipient->update(array(

  						'type' => $account_type,
  						'bank_account' => array(
    					'brand' => $bank_brand,
    					'number' => $bank_number,
    					'name' => $bank_name
    				)
				));

			} else {

				$recipient->update(array(

  						'type' => $account_type,
  						'bank_account' => array(
    					'brand' => $bank_brand,
    					'name' => $bank_name
    				)
				));
			}
		}

		if($time_insert){	$sql_bank .= ',date_update="'.$time_insert.'"';   }

		$do_sql_bank =  'UPDATE '.$table_bank.' SET '.$sql_bank.' WHERE brand_bank_account_id="'.$id.'"';


	} else {

		# INSERT

		$recipient = OmiseRecipient::create(array(

							  				'name' => $axRow['name'],
							  				'description' => $description,
							  				'email' => $axRow['email'],
							  				'type' => $account_type,
							  				'tax_id' => $tax_id,
							  				'bank_account' => array(
							    			'brand' => $bank_brand,
							    			'number' => $bank_number,
							    			'name' => $bank_name
  				)
		));


		if($recipient['id']){	$sql_bank .= ',recipient_token="'.$recipient['id'].'"';   }

		if($time_insert){	$sql_bank .= ',date_create="'.$time_insert.'"';   }

		if($time_insert){	$sql_bank .= ',date_update="'.$time_insert.'"';   }

		if($new_bank_account_id){	$sql_bank .= ',brand_bank_account_id="'.$new_bank_account_id.'"';   }


		$do_sql_bank = 'INSERT INTO '.$table_bank.' SET '.$sql_bank;

		$id = $new_bank_account_id;
	}

	$oDB->QueryOne($do_sql_bank);



	if ($default_status==1) {

		$do_sql_default =  'UPDATE '.$table_bank.' SET default_status=2 WHERE brand_bank_account_id!="'.$id.'" AND brand_id="'.$brand_id.'"';

		$oDB->QueryOne($do_sql_default);
	}

	
	echo '<script>window.location.href = "bank_account.php";</script>';

	exit;
}






#  brand dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' and brand_id="'.$_SESSION['user_brand_id'].'" ';
}

$as_brand = dropdownlist_from_table($oDB,'mi_brand','brand_id','name','brand_id>0',$where_brand);

$oTmp->assign('brand_opt', $as_brand);



#  bank_brand dropdownlist

$as_bank_brand = dropdownlist_from_table($oDB,'mi_bank','bank_omise','name_en');

$oTmp->assign('bank_brand_opt', $as_bank_brand);





$oTmp->assign('recipient', $asRecipient);

$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_bank');

$oTmp->assign('content_file', 'financial/bank_account_create.htm');

$oTmp->display('layout/template.html');


//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
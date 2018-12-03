<?php

header('Content-Type:text/html; charset=UTF-8');

//========================================//

ini_set("display_errors",1);

error_reporting(1);

//========================================//

include('../include/common_login.php');
include('../lib/pagination_class.php');
include('../lib/function_normal.php');
include('../include/common_check.php');

//========================================//


$oTmp = new TemplateEngine();

$oDB = new DBI();

if ($bDebug) {

	$oErr = new Tracker();

	$oDB->SetTracker($oErr);

}

$today = date("Y-m-d");

$month = array("01","02","03","04","05","06","07","08","09","10","11","12");

foreach ($month as $value) {
	
	echo "<b>".$value."</b><br>";

	echo "<table><tr style='background-color:#CCC'>
			<td width='50px'></td>
			<td width='50px'><b>date</b></td>
			<td width='250px'><b>firstname - lastname</b></td>
			<td width='250px'><b>email</b></td>
			<td><b>card</b></td></tr>";

	$sql_member = "SELECT date_format(date_birth,'%d') AS birthday,
					firstname, lastname, email, member_id
					FROM mb_member 
					WHERE date_format(date_birth,'%m')='".$value."' 
					AND date_birth!='0000-00-00' 
					ORDER BY date_format(date_birth,'%m-%d') ASC";
	$oRes = $oDB->Query($sql_member);

	$i = 0;

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		if ($i == 0) { $color = "style='background-color:#EEE'"; $i++; }
		else if ($i == 1) { $color = "style='background-color:#DDD'"; $i--; }

		echo "<tr ".$color.">
				<td></td>
				<td>".$axRow['birthday']."</td>
				<td>".$axRow['firstname']." ".$axRow['lastname']."</td>
				<td>".$axRow['email']."</td>";

		$sql_register = "SELECT a.name as card_name,
						c.date_expire,
						c.period_type,
						b.name as brand_name
						FROM mb_member_register as c 
						LEFT JOIN mi_card as a
						ON a.card_id = c.card_id
						LEFT JOIN mi_brand as b
						ON a.brand_id = b.brand_id
						WHERE c.member_id='".$axRow['member_id']."'";
		$oRes_regis = $oDB->Query($sql_register);

		echo "<td>";

		while ($member = $oRes_regis->FetchRow(DBI_ASSOC)){

			echo $member['card_name']." (".$member['brand_name'].")<br>";
		}

		echo "</td></tr>";
	}

	echo "</table><br>";
}

//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());

}

//========================================//

?>
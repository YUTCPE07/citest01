<?php

$page = $_SERVER['PHP_SELF'];
$sec = "5";

?>

<html>
<head>
<meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'">
</head>
<body>

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


## SHOW DATA ##

echo "<table>
		<tr valign='top'>
			<td width='150px'><b>MEMBER ID</b></td>
			<td width='150px'><b>BRANCH ID</b></td>
			<td width='150px'><b>RADIUS</b></td>
		</tr>";

// $sql = 'SELECT * FROM member_branch';
// $oRes = $oDB->Query($sql);

// while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

// 	echo "<tr valign='top'>
// 				<td>".$axRow['member_id']."</td>
// 				<td>".$axRow['branch_id']."</td>
// 				<td>".$axRow['radius']."</td>
// 			</tr>";
// }

// echo "</table>";


$date_update = date("Y-m-d H:i:s");

$sql = "SELECT map_latitude,map_longitude
         	FROM mi_branch WHERE branch_id = '23' ";

$oRes = $oDB->Query($sql);

$record = $oRes->FetchRow(DBI_ASSOC);

$branch_lat = $record["map_latitude"];

$branch_lot = $record["map_longitude"];

$strSQL  = "SELECT date_test,round(111.1111 *
                DEGREES(ACOS(COS(RADIANS(".$branch_lat."))
                * COS(RADIANS(map_latitude))
                * COS(RADIANS(".$branch_lot." - map_longitude))
                + SIN(RADIANS(".$branch_lat."))
                * SIN(RADIANS(map_latitude)))),2) AS distance_in_km,member_id,branch_id
                FROM member_branch";

$stmt = $oDB->Query($strSQL);
 
while ($record = $stmt->FetchRow(DBI_ASSOC)) {
  
    $starttime = $record["date_test"]; 
                
    $to_time = strtotime($starttime);
    $from_time = strtotime($date_update);
    $record["date_test"] = round(abs($to_time - $from_time) / 60,2). " minute";

	echo "<tr valign='top'>
				<td>".$record['member_id']."</td>
				<td>".$record['branch_id']."</td>
				<td>".$record['date_test']."</td>
			</tr>";
}

echo "</table>";

//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());

}

//========================================//

?>
	
</body>
</html>
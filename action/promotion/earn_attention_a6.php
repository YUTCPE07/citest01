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

?>

<style type="text/css">

body {
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
}

* {
    box-sizing: border-box;
    -moz-box-sizing: border-box;
}

.page {
    width: 210mm;
    height: 297mm;
}
    
@page {
    size: A4;
    margin: 0;
}

@media print {
    html, body {
        width: 210mm;
        height: 297mm;
    }
    .page {
        margin: 0;
        border: initial;
        border-radius: initial;
        width: initial;
        min-height: initial;
        box-shadow: initial;
        background: initial;
        page-break-after: always;
    }
}

</style>

<html>
	<head>
		<title>.:: MemberIn ::.</title>
		<link rel="shortcut icon" href="/images/icon/favicon.ico">
	</head>
	<body>
		<center>

<?php

$branch = $_REQUEST['branch'];
$id = $_REQUEST['id'];

$sql_coup = 'SELECT coup_QrPath FROM hilight_coupon WHERE coup_CouponID="'.$id.'"';
$coup_QrPath = $oDB->QueryOne($sql_coup);

if ($branch) {

	echo '<div class="page">
			<img src="../../upload/'.$coup_QrPath.'A6_'.str_pad($id,4,"0",STR_PAD_LEFT).'-'.str_pad($branch,4,"0",STR_PAD_LEFT).'.jpg" height="558.6px" style="border:1px black solid;">
		</div>';

} else {

	$sql_branch = 'SELECT brnc_BranchID FROM hilight_coupon WHERE coup_CouponID="'.$id.'"';
	$brnc_BranchID = $oDB->QueryOne($sql_branch);

	$token = strtok($brnc_BranchID , ",");

	$branch = array();

	$j = 0;

	while ($token !== false) {

	    $branch[$j] =  $token;
	    $token = strtok(",");
	    $j++;
	}

	$arrlength = count($branch);

	for($x=0; $x < $arrlength; $x++) {

		echo '<div class="page">';

		if ($branch[$x]!="") {

			echo '<img src="../../upload/'.$coup_QrPath.'A6_'.str_pad($id,4,"0",STR_PAD_LEFT).'-'.str_pad($branch[$x],4,"0",STR_PAD_LEFT).'.jpg" height="558.6px" style="border:1px black solid;">';
		}

		$x++;

		if ($branch[$x]!="") {

			echo '<img src="../../upload/'.$coup_QrPath.'A6_'.str_pad($id,4,"0",STR_PAD_LEFT).'-'.str_pad($branch[$x],4,"0",STR_PAD_LEFT).'.jpg" height="558.6px" style="border:1px black solid;">';
		}

		$x++;

		if ($branch[$x]!="") {

			echo '<img src="../../upload/'.$coup_QrPath.'A6_'.str_pad($id,4,"0",STR_PAD_LEFT).'-'.str_pad($branch[$x],4,"0",STR_PAD_LEFT).'.jpg" height="558.6px" style="border:1px black solid;">';
		}

		$x++;

		if ($branch[$x]!="") {

			echo '<img src="../../upload/'.$coup_QrPath.'A6_'.str_pad($id,4,"0",STR_PAD_LEFT).'-'.str_pad($branch[$x],4,"0",STR_PAD_LEFT).'.jpg" height="558.6px" style="border:1px black solid;">';
		}

		echo '</div>';
	}
}

?>
		</center>
	</body>
</html>

<?php

//========================================//

$oDB->Close();

if ($bDebug) {
	echo($oErr->GetAll());
}

//========================================//

?>

<script type="text/javascript">

window.print();
  
</script>
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


$branch = $_REQUEST['branch'];
$id = $_REQUEST['id'];

$sql_coup = 'SELECT rewa_Name FROM reward WHERE rewa_RewardID="'.$id.'"';
$rewa_Name = $oDB->QueryOne($sql_rewa);

$sql_rewa = 'SELECT rewa_QrPath FROM reward WHERE rewa_RewardID="'.$id.'"';
$rewa_QrPath = $oDB->QueryOne($sql_rewa);

$sql_branch = 'SELECT name FROM mi_branch WHERE branch_id="'.$branch.'"';
$branch_name = $oDB->QueryOne($sql_branch);

?>

<style type="text/css">
	
@font-face {

  	font-family: 'Prompt';
  	src: url('../../css/font/Prompt/Prompt-SemiBold.ttf');
}

html, body {
	font-family: 'Prompt';
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
	
	echo "<img src='../../upload/".$rewa_QrPath."QRW-".str_pad($id,4,'0',STR_PAD_LEFT)."-".str_pad($branch,4,'0',STR_PAD_LEFT).".png'>"; 


?>

<table border="0">
	<tr>
		<td align="center"><span style="font-size: 40px">
		<?php echo $rewa_Name; ?>
		</span>
		</td>
	</tr>
	<tr>
		<td align="center">
			<span style="font-size: 25px"><?php echo "[ ".$branch_name." ]"; ?></span>
		</td>
	</tr>
</table>
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
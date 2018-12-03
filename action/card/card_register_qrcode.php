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


$qrcode = $_REQUEST['qrcode'];

$path_upload_qrregis = $_SESSION['path_upload_qrregis'];


$sql = 'SELECT a.*,

		b.priv_Name,
		d.coup_Name,
		e.acti_Name,
		c.name AS branch_name

		FROM mi_card_register AS a

		LEFT JOIN privilege AS b
		ON a.privilege_id = b.priv_PrivilegeID

		LEFT JOIN coupon AS d
		ON a.coupon_id = d.coup_CouponID

		LEFT JOIN activity AS e
		ON a.activity_id = e.acti_ActivityID

		LEFT JOIN mi_branch AS c 
		ON a.branch_id = c.branch_id

		WHERE a.qrcode_privileges_image = "'.$qrcode.'"';


$oRes = $oDB->Query($sql);

$axRow = $oRes->FetchRow(DBI_ASSOC)

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
	
	echo "<img src='../../upload/".$axRow['path_qr'].$axRow['qrcode_privileges_image']."'>"; 

?>

<table border="0">
	<tr>
		<td align="center"><span style="font-size: 40px">
		<?php if ($axRow['priv_Name']) {	echo $axRow['priv_Name'];	}
				if ($axRow['coup_Name']) {	echo $axRow['coup_Name'];	}
				if ($axRow['acti_Name']) {	echo $axRow['acti_Name'];	} ?>
		</span>
		</td>
	</tr>
	<tr>
		<td align="center">
			<span style="font-size: 25px"><?php echo "[ ".$axRow['branch_name']." ]"; ?></span>
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
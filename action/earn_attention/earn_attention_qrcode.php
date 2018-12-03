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
	
@font-face {
  	font-family: 'Prompt';
  	src: url('../../css/font/Prompt/Prompt-SemiBold.ttf');
}

html, body {
	font-family: 'Prompt';
}

table, tr, td {
	border: 1px solid black;
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

$sql_coup = 'SELECT coup_Name FROM hilight_coupon WHERE coup_CouponID="'.$id.'"';
$coup_Name = $oDB->QueryOne($sql_coup);

$sql_coup = 'SELECT coup_QrPath FROM hilight_coupon WHERE coup_CouponID="'.$id.'"';
$coup_QrPath = $oDB->QueryOne($sql_coup);

$sql_brand = 'SELECT mi_brand.logo_image 
				FROM mi_brand
				LEFT JOIN hilight_coupon
				ON mi_brand.brand_id = hilight_coupon.bran_BrandID 
				WHERE hilight_coupon.coup_CouponID="'.$id.'"';
$logo_brand = $oDB->QueryOne($sql_brand);

$sql_brand = 'SELECT mi_brand.path_logo 
				FROM mi_brand
				LEFT JOIN hilight_coupon
				ON mi_brand.brand_id = hilight_coupon.bran_BrandID 
				WHERE hilight_coupon.coup_CouponID="'.$id.'"';
$path_brand = $oDB->QueryOne($sql_brand);

if ($branch) {

	$sql_branch = 'SELECT name FROM mi_branch WHERE branch_id="'.$branch.'"';
	$branch_name = $oDB->QueryOne($sql_branch);

	echo '<table width="400px">
				<tr>
					<td align="center" width="400px">
						<img src="../../images/LOGO.png" width="150px" style="padding-top:25px;padding-bottom:15px"><br>
						<span style="font-size:15px">ใช้งานง่าย ได้ทุกโปร</span><br>
						<img src="../../upload/'.$coup_QrPath.'QHC-'.str_pad($id,4,'0',STR_PAD_LEFT).'-'.str_pad($branch,4,'0',STR_PAD_LEFT).'.png" width="198px" height="198px"><br>
						<span style="font-size:20px">'.$coup_Name.'</span><br>
						<span style="font-size:15px">[ '.$branch_name.' ]</span><br>
						<img src="../../upload/'.$path_brand.$logo_brand.'" width="150px" style="margin-top:10px;margin-bottom:25px;">
					</td>
				</tr>
			</table>';

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

	for($x = 0; $x < $arrlength; $x++) {

		echo '<table width="800px">';

		if ($branch[$x]!="") {

			$sql_branch = 'SELECT name FROM mi_branch WHERE branch_id="'.$branch[$x].'"';
			$branch_name = $oDB->QueryOne($sql_branch);

			echo '<tr>
					<td align="center" width="400px">
						<img src="../../images/LOGO.png" width="150px" style="padding-top:25px;padding-bottom:15px"><br>
						<span style="font-size:15px">ใช้งานง่าย ได้ทุกโปร</span><br>
						<img src="../../upload/'.$coup_QrPath.'QHC-'.str_pad($id,4,'0',STR_PAD_LEFT).'-'.str_pad($branch[$x],4,'0',STR_PAD_LEFT).'.png" width="198px" height="198px"><br>
						<span style="font-size:20px">'.$coup_Name.'</span><br>
						<span style="font-size:15px">[ '.$branch_name.' ]</span><br>
						<img src="../../upload/'.$path_brand.$logo_brand.'" width="150px" style="margin-top:10px;margin-bottom:25px;">
					</td>';

		} else { echo '<tr><td align="center" width="400px"> </td>'; }

		$x++;

		if ($branch[$x]!="") {

			$sql_branch = 'SELECT name FROM mi_branch WHERE branch_id="'.$branch[$x].'"';
			$branch_name = $oDB->QueryOne($sql_branch);

			echo '	<td align="center" width="400px">
						<img src="../../images/LOGO.png" width="150px" style="padding-top:25px;padding-bottom:15px"><br>
						<span style="font-size:15px">ใช้งานง่าย ได้ทุกโปร</span><br>
						<img src="../../upload/'.$coup_QrPath.'QHC-'.str_pad($id,4,'0',STR_PAD_LEFT).'-'.str_pad($branch[$x],4,'0',STR_PAD_LEFT).'.png" width="198px" height="198px"><br>
						<span style="font-size:20px">'.$coup_Name.'</span><br>
						<span style="font-size:15px">[ '.$branch_name.' ]</span><br>
						<img src="../../upload/'.$path_brand.$logo_brand.'" width="150px" style="margin-top:10px;margin-bottom:25px;">
					</td>
				</tr>';

		} else { echo '<td align="center" width="400px"> </td></tr>'; }

		$x++;

		# TR TABLE

		if ($branch[$x]!="") {

			$sql_branch = 'SELECT name FROM mi_branch WHERE branch_id="'.$branch[$x].'"';
			$branch_name = $oDB->QueryOne($sql_branch);

			echo '<tr>
					<td align="center" width="400px">
						<img src="../../images/LOGO.png" width="150px" style="padding-top:25px;padding-bottom:15px"><br>
						<span style="font-size:15px">ใช้งานง่าย ได้ทุกโปร</span><br>
						<img src="../../upload/'.$coup_QrPath.'QHC-'.str_pad($id,4,'0',STR_PAD_LEFT).'-'.str_pad($branch[$x],4,'0',STR_PAD_LEFT).'.png" width="198px" height="198px"><br>
						<span style="font-size:20px">'.$coup_Name.'</span><br>
						<span style="font-size:15px">[ '.$branch_name.' ]</span><br>
						<img src="../../upload/'.$path_brand.$logo_brand.'" width="150px" style="margin-top:10px;margin-bottom:25px;">
					</td>';
		}

		$x++;

		if ($branch[$x]!="") {

			$sql_branch = 'SELECT name FROM mi_branch WHERE branch_id="'.$branch[$x].'"';
			$branch_name = $oDB->QueryOne($sql_branch);

			echo '	<td align="center" width="400px">
						<img src="../../images/LOGO.png" width="150px" style="padding-top:25px;padding-bottom:15px"><br>
						<span style="font-size:15px">ใช้งานง่าย ได้ทุกโปร</span><br>
						<img src="../../upload/'.$coup_QrPath.'QHC-'.str_pad($id,4,'0',STR_PAD_LEFT).'-'.str_pad($branch[$x],4,'0',STR_PAD_LEFT).'.png" width="198px" height="198px"><br>
						<span style="font-size:20px">'.$coup_Name.'</span><br>
						<span style="font-size:15px">[ '.$branch_name.' ]</span><br>
						<img src="../../upload/'.$path_brand.$logo_brand.'" width="150px" style="margin-top:10px;margin-bottom:25px;">
					</td>
				</tr>';
		}

		$x++;

		if ($branch[$x]!="") {

			echo '</tr></table><br>';

		} else {

			echo '</tr></table>';
		}

		$x--;
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
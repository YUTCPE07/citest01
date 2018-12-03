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

if ($branch!="") {

	$sql_branch = 'SELECT mi_branch.name AS branch_name,
						mi_branch.qr_code_image AS qr_code,
						mi_branch.path_qr AS path_qr,
						mi_brand.path_logo AS path_logo,
						mi_brand.logo_image AS logo,
						mi_brand.name AS brand_name
					FROM mi_branch
					LEFT JOIN mi_brand
					ON mi_branch.brand_id = mi_brand.brand_id
					WHERE mi_branch.branch_id="'.$branch.'"';

	$oRes_brnc = $oDB->Query($sql_branch);
	$brnc = $oRes_brnc->FetchRow(DBI_ASSOC);

	echo '<table width="400px">
				<tr>
					<td align="center" width="400px">
						<img src="../../images/LOGO.png" width="150px" style="padding-top:25px;padding-bottom:15px"><br>
						<span style="font-size:15px">ใช้งานง่าย ได้ทุกโปร</span><br>
						<img src="../../upload/'.$brnc['path_qr'].$brnc['qr_code'].'" width="198px" height="198px"><br>
						<span style="font-size:20px">'.$brnc['branch_name'].'</span><br>
						<span style="font-size:15px">[ '.$brnc['brand_name'].' ]</span><br>
						<img src="../../upload/'.$brnc['path_logo'].$brnc['logo'].'" width="150px" style="margin-top:10px;margin-bottom:25px;">
					</td>
				</tr>
			</table>';

} else {

	$sql_branch = 'SELECT mi_branch.name AS branch_name,
						mi_branch.qr_code_image AS qr_code,
						mi_branch.path_qr AS path_qr,
						mi_brand.path_logo AS path_logo,
						mi_brand.logo_image AS logo,
						mi_brand.name AS brand_name
					FROM mi_brand
					LEFT JOIN mi_branch
					ON mi_branch.brand_id = mi_brand.brand_id
					WHERE mi_brand.brand_id="'.$id.'"';

	$oRes_brnc = $oDB->Query($sql_branch);

	echo '<table width="800px">';

	while ($brnc = $oRes_brnc->FetchRow(DBI_ASSOC)){

		if ($brnc['qr_code']!="") {

			echo '<tr>
					<td align="center" width="400px">
						<img src="../../images/LOGO.png" width="150px" style="padding-top:25px;padding-bottom:15px"><br>
						<span style="font-size:15px">ใช้งานง่าย ได้ทุกโปร</span><br>
						<img src="../../upload/'.$brnc['path_qr'].$brnc['qr_code'].'" width="198px" height="198px"><br>
						<span style="font-size:20px">'.$brnc['branch_name'].'</span><br>
						<span style="font-size:15px">[ '.$brnc['brand_name'].' ]</span><br>
						<img src="../../upload/'.$brnc['path_logo'].$brnc['logo'].'" width="150px" style="margin-top:10px;margin-bottom:25px;">
					</td>';

		} else { echo '<tr><td align="center" width="400px"> </td>'; }

		$brnc = $oRes_brnc->FetchRow(DBI_ASSOC);

		if ($brnc['qr_code']!="") {

			echo '<td align="center" width="400px">
					<img src="../../images/LOGO.png" width="150px" style="padding-top:25px;padding-bottom:15px"><br>
					<span style="font-size:15px">ใช้งานง่าย ได้ทุกโปร</span><br>
					<img src="../../upload/'.$brnc['path_qr'].$brnc['qr_code'].'" width="198px" height="198px"><br>
					<span style="font-size:20px">'.$brnc['branch_name'].'</span><br>
					<span style="font-size:15px">[ '.$brnc['brand_name'].' ]</span><br>
					<img src="../../upload/'.$brnc['path_logo'].$brnc['logo'].'" width="150px" style="margin-top:10px;margin-bottom:25px;">
				</td></tr>';

		} else { echo '<td align="center" width="400px"> </td></tr>'; }

		$brnc = $oRes_brnc->FetchRow(DBI_ASSOC);

		if ($brnc['qr_code']!="") {

			echo '<tr>
					<td align="center" width="400px">
						<img src="../../images/LOGO.png" width="150px" style="padding-top:25px;padding-bottom:15px"><br>
						<span style="font-size:15px">ใช้งานง่าย ได้ทุกโปร</span><br>
						<img src="../../upload/'.$brnc['path_qr'].$brnc['qr_code'].'" width="198px" height="198px"><br>
						<span style="font-size:20px">'.$brnc['branch_name'].'</span><br>
						<span style="font-size:15px">[ '.$brnc['brand_name'].' ]</span><br>
						<img src="../../upload/'.$brnc['path_logo'].$brnc['logo'].'" width="150px" style="margin-top:10px;margin-bottom:25px;">
					</td>';

		} else { echo '<tr><td align="center" width="400px"> </td>'; }

		$brnc = $oRes_brnc->FetchRow(DBI_ASSOC);

		if ($brnc['qr_code']!="") {

			echo '<td align="center" width="400px">
					<img src="../../images/LOGO.png" width="150px" style="padding-top:25px;padding-bottom:15px"><br>
					<span style="font-size:15px">ใช้งานง่าย ได้ทุกโปร</span><br>
					<img src="../../upload/'.$brnc['path_qr'].$brnc['qr_code'].'" width="198px" height="198px"><br>
					<span style="font-size:20px">'.$brnc['branch_name'].'</span><br>
					<span style="font-size:15px">[ '.$brnc['brand_name'].' ]</span><br>
					<img src="../../upload/'.$brnc['path_logo'].$brnc['logo'].'" width="150px" style="margin-top:10px;margin-bottom:25px;">
				</td></tr>';

		} else { echo '<td align="center" width="400px"> </td></tr>'; }

		$brnc = $oRes_brnc->FetchRow(DBI_ASSOC);

		if ($brnc['qr_code']!="") {

			echo '</tr></table><br>';

		} else {

			echo '</tr></table>';
		}
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
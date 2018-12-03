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

//========================================//

if ($_SESSION['role_action']['card']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$where_brand = '';

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND a.brand_id = "'.$_SESSION['user_brand_id'].'"';
}

$sql = 'SELECT 
		a.*,
		c.name AS brand_name,
		c.path_logo,
		c.logo_image,
		a.brand_id AS card_brand_id

		FROM mi_card AS a

		LEFT JOIN mi_brand AS c
		ON a.brand_id = c.brand_id

		WHERE a.flag_del="0"
		'.$where_brand.' 

		ORDER BY a.greeting_updateddate DESC';

$oRes = $oDB->Query($sql);

$i=0;

$data_table = '';

while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

	$i++;


	# STATUS

	$status = '';

	if($axRow['flag_status']=='1'){

		$status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';

	} else {

		$status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
	}


	# LOGO

	if($axRow['logo_image']!=''){

		$logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';

		$logo_view = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="70" height="70"/>';

	} else {

		$logo_brand = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';

		$logo_view = '<img src="../../images/400x400.png" class="image_border" width="70" height="70"/>';
	}


	# CARD IMAGE

	if($axRow['image']!=''){

		$card_image = '<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'" class="img-rounded image_border" height="60"/>';

		$card_view = '<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'" class="img-rounded image_border" width="120"/>';

	} else {

		$card_image = '<img src="../../images/card_privilege.jpg" class="img-rounded image_border" height="60"/>';

		$card_view = '<img src="../../images/card_privilege.jpg" class="img-rounded image_border" width="120"/>';
	}


	# UPDATED DATE

	if($axRow['greeting_updateddate']!='0000-00-00 00:00:00'){

		$axRow['greeting_updateddate'] = DateTime($axRow['greeting_updateddate']);

	} else { $axRow['greeting_updateddate'] = ''; }


	# VIEW

	$view_basic = '';

	if ($axRow['greeting_messages']) {

		$view_basic = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Basic'.$axRow['card_id'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>

				<div class="modal fade" id="Basic'.$axRow['card_id'].'" tabindex="-1" role="dialog" aria-labelledby="BasicDataLabel">
					<div class="modal-dialog" role="document" style="width:30%">
						<div class="modal-content">
						    <div align="center">
						        <span style="font-size:16px"><br><b>ขอต้อนรับท่านสู่การเป็นสมาชิก<br>'.$axRow['name'].'</b></span>
						        <br>
						        <span style="font-size:14px"><br><b>สิทธิประโยชน์ของสมาชิก</b></span>
						        <center>
						        <table width="70%" class="myPopup">
						        	<tr><td width="140px" valign="top">
						        		<br>'.nl2br($axRow['greeting_messages']).'<br>
						        	</td></tr>
						        </table>
						        </center>
						    </div>';

		if ($axRow['greeting_accept']=='Yes') {

			$view_basic .= '	<div>
									<hr>
									<span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>&nbsp;&nbsp;&nbsp;
									<span style="color:black;font-size:11px;">ข้าพเจ้าได้อ่านและยอมรับ ข้อตกลงและเงื่อนไขการใช้งานตามข้างต้น</span>
									<br><br>
								</div>
								<div style="background-color:#5CB2DA;height:50px;">
									<span style="color:white;font-size:16px;padding-top:30px"><b>ACCEPT</b></span>
								</div>';
		} else {

			$view_basic .= '	<br><br>';
		}

		$view_basic .= '		</div>
							</div>
						</div>';
	}

	$view_adv = '';

	if ($axRow['greeting_messages_ckedit']) {

		$view_adv = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#Advance'.$axRow['card_id'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>

				<div class="modal fade" id="Advance'.$axRow['card_id'].'" tabindex="-1" role="dialog" aria-labelledby="AdvanceDataLabel">
					<div class="modal-dialog" role="document" style="width:30%">
						<div class="modal-content">
						    <br>'.$logo_view.'<br>
						    <span style="font-size:16px"><br><b>ขอต้อนรับท่านสู่การเป็นสมาชิก<br>'.$axRow['name'].'</b></span>
						    <br><br>'.$card_view.'<br><br>
						    <center>
						    <div align="left" style="width:80%;font-weight:normal;">
						        <span style="font-size:12px">เรียน ท่านสมาชิก<br><br>
						        ขอต้อนรับท่านสู่การเป็นสมาชิก '.$axRow['name'].'<br><br>
						        ในฐานะสมาชิก ท่านจะได้รับสิทธิพิเศษต่างๆ จากร้านค้า อัพเดทข่าวสาร โปรโมชั่น สิทธิพิเศษอื่นๆ หวังอย่างยิ่งว่าท่านจะได้รับความคุ้มค่า ทุกครั้งที่ได้เข้ามาใช้บริการ<br></span>
						    </div>
						    <table width="70%" class="myPopup">
						        <tr><td width="140px" valign="top">
						        	<br>'.htmlspecialchars_decode(htmlspecialchars_decode(base64_decode($axRow['greeting_messages_ckedit']))).'<br>
						        </td></tr>
						    </table>
						    </center>
						</div>
					</div>
				</div>';
	}


	# DATA TABLE

	$data_table .= '<tr >
						<td >'.$i.'</td>
						<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
							<span style="font-size:11px;">'.$axRow['brand_name'].'</span>
						</td>
						<td style="text-align:center"><a href="../card/card.php">'.$card_image.'</a><br>
							<span style="font-size:11px;">'.$axRow['name'].'</span>
						</td>
						<td style="text-align:center">'.$view_basic.'</td>
						<td style="text-align:center">'.$view_adv.'</td>
						<td style="text-align:center">'.$status.'</td>
						<td >'.$axRow['greeting_updateddate'].'</td>';

	if ($_SESSION['role_action']['greeting_messages']['edit'] == 1) {

		$data_table .=	'<td style="text-align:center">
							<a href="greeting_messages_create.php?act=edit&id='.$axRow['card_id'].'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></a></td>';
	}

	$data_table .=	'</tr>';
}



$oTmp->assign('data_table', $data_table);

$oTmp->assign('is_menu', 'is_greeting_messages');

$oTmp->assign('in_brand', 'in');

$oTmp->assign('sub_messages', 'in');

$oTmp->assign('content_file', 'message/greeting_messages.htm');

$oTmp->display('layout/template.html');


//========================================//


$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}

//========================================//

?>
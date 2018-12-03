<?php
header('Content-Type:text/html; charset=UTF-8');
//========================================//
ini_set("display_errors",1);
error_reporting(1);

ini_set('memory_limit','128M');

//========================================//

include('../../include/common.php');
include('../../lib/pagination_class.php');
include('../../lib/function_normal.php');
include('../../lib/phpqrcode/qrlib.php');
include('../../include/common_check.php');

//========================================//

$oTmp = new TemplateEngine();
$oDB = new DBI();
if ($bDebug) {
	$oErr = new Tracker();
	$oDB->SetTracker($oErr);
}

$time_pic = date("Ymd");

$brand = $_REQUEST['brand'];

$path_upload_card = '../../upload/'.$brand.'/card_upload/';

$file_name = 'card_'.$brand.'.jpg';

$Act = $_REQUEST['act'];

$card = $_REQUEST['card_name'];


?>

<html>
<head>
<title>MemberIn :: Create Card</title>
<script src="jscolor/jscolor.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<link rel='stylesheet' type='text/css' href='//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css'>
<link rel="shortcut icon" href="../../favicon.ico">
</head>

<style type="text/css">
html, body {
	font-family: Arial, Helvetica, sans-serif;
	background-color: #eee;

}

.topic {

	text-align: right;
	padding-right: 10px;
}

.option {

	padding-left: 10px;
}

table {

	border-spacing: 0;
	border-collapse: 0;
}

a {

	text-decoration: none;
}

.text-rq {
	color: red;
}
</style>

<body>

<center>


<form action="create_card.php?brand='".$brand." method="post" enctype="multipart/form-data" name="frmMain">

	<table width="90%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="650px" align="center" valign="top">

				<?php 
					if ($_REQUEST['card'] == "" && $_REQUEST['status'] != 1) {
						echo '<img src=image/CARD/CARD1-01.png width=600 style=margin-top:25px>';
					}
					else if ($_REQUEST['status'] == 1) {
						echo '<img src='.$path_upload_card.$file_name.' width=600 style=border-radius:30px;margin-top:25px;>';
					}
					else {
						echo '<img src=image/CARD/'.$_REQUEST["card"].' width=600 style=border-radius:30px;margin-top:25px;>';
					}
				?>

			</td>
			<td>

				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="150px" class="topic"><br>Card Style <span class="text-rq">*</span><br></td>
						<td class="option"><br>
							&nbsp; <select class="btn dropdown-toggle" id="card_style" name="card_style" OnChange="form.submit();">
								<option value="1" <?php if($_REQUEST['card_style']=="1"){ echo "selected='selected'";} ?>>Style 1</option>
								<option value="2" <?php if($_REQUEST['card_style']=="2"){ echo "selected='selected'";} ?>>Style 2</option>
								<option value="3" <?php if($_REQUEST['card_style']=="3"){ echo "selected='selected'";} ?>>Style 3</option>
								<option value="4" <?php if($_REQUEST['card_style']=="4"){ echo "selected='selected'";} ?>>Style 4</option>
								<option value="5" <?php if($_REQUEST['card_style']=="5"){ echo "selected='selected'";} ?>>Style 5</option>
							</select><br><br>
							</td>
					</tr>
					<tr>
						<td width="130px" class="topic">Card <span class="text-rq">*</span></td>
						<td align="center">

						<?php 

						if ($_REQUEST['card_style'] == '') {
							$card_style = 1;
						}
						else {
							$card_style = $_REQUEST['card_style'];
						}


						if ($card_style == 1) {

							for ($i=1; $i <5 ; $i++) {

								$card_def = 'CARD1-0'.$i.'.png';

								echo '<a href="create_card.php?brand='.$brand.'&card_style='.$card_style.'&card=CARD1-0'.$i.'.png">
								<img src="image/CARD/CARD1-0'.$i.'.png" width="150px" name="img" 
								value="CARD1-0'.$i.'.png"></a> &nbsp; &nbsp;';


								$i = $i+1;

								echo '<a href="create_card.php?brand='.$brand.'&card_style='.$card_style.'&card=CARD1-0'.$i.'.png">
								<img src="image/CARD/CARD1-0'.$i.'.png" width="150px" name="img" 
								value="CARD1-0'.$i.'.png"></a>';

								if ($i == 2) {
									echo "<br><br>";
								}
							}

						}

						if ($card_style == 2) {
							
							for ($i=1; $i <5 ; $i++) { 

								$card_def = 'CARD2-0'.$i.'.png';

								echo '<a href="create_card.php?brand='.$brand.'&card_style='.$card_style.'&card=CARD2-0'.$i.'.png">
								<img src="image/CARD/CARD2-0'.$i.'.png" width="150px" name="img" 
								value="CARD2-0'.$i.'.png"></a> &nbsp; &nbsp;';

								$i = $i+1;

								echo '<a href="create_card.php?brand='.$brand.'&card_style='.$card_style.'&card=CARD2-0'.$i.'.png">
								<img src="image/CARD/CARD2-0'.$i.'.png" width="150px" name="img" 
								value="CARD2-0'.$i.'.png"></a>';

								if ($i == 2) {
									echo "<br><br>";
								}
							}

						}

						if ($card_style == 3) {
							
							for ($i=1; $i <5 ; $i++) { 

								$card_def = 'CARD3-0'.$i.'.png';

								echo '<a href="create_card.php?brand='.$brand.'&card_style='.$card_style.'&card=CARD3-0'.$i.'.png">
								<img src="image/CARD/CARD3-0'.$i.'.png" width="150px" name="img" 
								value="CARD3-0'.$i.'.png"></a> &nbsp; &nbsp;';

								$i = $i+1;

								echo '<a href="create_card.php?brand='.$brand.'&card_style='.$card_style.'&card=CARD3-0'.$i.'.png">
								<img src="image/CARD/CARD3-0'.$i.'.png" width="150px" name="img" 
								value="CARD3-0'.$i.'.png"></a>';

								if ($i == 2) {
									echo "<br><br>";
								}
							}

						}

						if ($card_style == 4) {
							
							for ($i=1; $i <5 ; $i++) { 

								$card_def = 'CARD4-0'.$i.'.png';

								echo '<a href="create_card.php?brand='.$brand.'card_style='.$card_style.'&card=CARD4-0'.$i.'.png">
								<img src="image/CARD/CARD4-0'.$i.'.png" width="150px" name="img" 
								value="CARD4-0'.$i.'.png"></a> &nbsp; &nbsp;';

								$i = $i+1;

								echo '<a href="create_card.php?brand='.$brand.'card_style='.$card_style.'&card=CARD4-0'.$i.'.png">
								<img src="image/CARD/CARD4-0'.$i.'.png" width="150px" name="img" 
								value="CARD4-0'.$i.'.png"></a>';

								if ($i == 2) {
									echo "<br><br>";
								}
							}

						}

						if ($card_style == 5) {
							
							for ($i=1; $i <5 ; $i++) {

								$card_def = 'CARD5-0'.$i.'.png';

								echo '<a href="create_card.php?brand='.$brand.'&card_style='.$card_style.'&card=CARD5-0'.$i.'.png">
								<img src="image/CARD/CARD5-0'.$i.'.png" width="150px" name="img" 
								value="CARD5-0'.$i.'.png"></a> &nbsp; &nbsp;';

								$i = $i+1;

								echo '<a href="create_card.php?brand='.$brand.'&card_style='.$card_style.'&card=CARD5-0'.$i.'.png">
								<img src="image/CARD/CARD5-0'.$i.'.png" width="150px" name="img" 
								value="CARD5-0'.$i.'.png"></a>';

								if ($i == 2) {
									echo "<br><br>";
								}
							}

						}


						?>
							


						<br><br></td>
					</tr>

					<tr>
						<td class="topic">Logo <span class="text-rq">*</span></td>
						<td class="option">
						<div class="col-xs-10">
							<input class="form-control text-md" name="fileUpload" type="file" id="fileUpload" required autofocus>
						</div></td>
					</tr>

					<tr>
						<td class="topic">Card Name <span class="text-rq">*</span><br><br><br>Card Description</td>
						<td class="option"><br>
						<div class="form-group">
						<div class="col-xs-8">
							<input type="text" class="form-control text-md" name="text-name" required autofocus>
						</div>
						<div class="col-xs-4">
							<input class="jscolor {onFineChange:'update(this)'} form-control text-md" name="color_1" value="000000">
						</div></div>
						
							<br><br>
						<div class="form-group">
						<div class="col-xs-8">
							<input type="text" class="form-control col-xs-7" name="text-description">
						</div>
						<div class="col-xs-4">
							<input class="jscolor {onFineChange:'update(this)'} form-control text-md" name="color_2" value="000000">
						</div></div>
						<br><br></td>
					</tr>

					<tr>
						<td></td>
						<td class="option"><br>
							<input class="btn btn-default" type="submit" name="Submit" value="CREATE & SAVE">
							<input type="hidden" name="card_name" value="<?php echo $_REQUEST['card']; ?>">
							<input type="hidden" name="brand" value="<?php echo $_REQUEST['brand']; ?>">
						<br></td>
					</tr>

				</table>

			</td>

		</tr>
	</table>

</form>


</center>

</body>
</html>


<?php

if ($card == "") {

	$card = $card_def;

} else if ($card != "") {

	$myCard = ImageCreateFromPng('image/CARD/'.$card.'');


	if(trim($_FILES["fileUpload"]["tmp_name"]) != "") {   

	# LOGO   

		$filename = $_FILES["fileUpload"]["name"];
		$ext = pathinfo($filename, PATHINFO_EXTENSION);

		if ($ext == 'png' || $ext == 'PNG') {
			$myLogo = imagecreatefromPng($_FILES["fileUpload"]["tmp_name"]);
			$Logo_old = ImageCreateFromPng($_FILES["fileUpload"]["tmp_name"]);
		}
		else if ($ext == 'jpg' || $ext == 'JPG' || $ext == 'jpeg' || $ext == 'JPEG') {
			$myLogo = imagecreatefromJpeg($_FILES["fileUpload"]["tmp_name"]);
			$Logo_old = ImageCreateFromJpeg($_FILES["fileUpload"]["tmp_name"]);
		}
		else if ($ext == 'gif' || $ext == 'GIF') {
			$myLogo = imagecreatefromGif($_FILES["fileUpload"]["tmp_name"]);
			$Logo_old = ImageCreateFromGif($_FILES["fileUpload"]["tmp_name"]);
		}
		
		$height = 200;

	# RESIZE

		$size = GetimageSize($_FILES["fileUpload"]["tmp_name"]);

		if ($size[0] > $size[1]) {
			$width = 200;
			$height = round($width*$size[1]/$size[0]);
		}

		else if ($size[0] < $size[1]) {
			$height = 200;
			$width = round($height*$size[0]/$size[1]);
		}

		else if ($size[0] == $size[1]) {
			$width = 200;
			$height = 200;
		}

		$photoX = ImagesX($Logo_old);
		$photoY = ImagesY($Logo_old);
		$Logo_new = ImageCreateTrueColor($width, $height);
		
		ImageCopyResampled($Logo_new, $Logo_old, 0, 0, 0, 0, $width+1, $height+1, $photoX, $photoY);

		$destWidth = 640;
		$destHeight = 400;
		$srcWidth = imagesx($Logo_new);
		$srcHeight = imagesy($Logo_new);

		$pxX = 0;
 		$pyY = 0;

 		if ($srcWidth > $srcHeight) {
 			$pxX = 68;
 			$pyY = 17 + (100 - ($srcHeight/2));
 		}

 		else if ($srcWidth < $srcHeight) {
 			$pxX = 68 + (100 - ($srcWidth/2));
 			$pyY = 17;
 		}

 		else if ($srcWidth == $srcHeight) {
 			$pxX = 68;
 			$pyY = 17;
 		}
 

		$white = imagecolorexact($Logo_new, 0, 0, 0);
		imagecolortransparent($Logo_new, $white);
		imagecopymerge($myCard, $Logo_new, $pxX, $pyY, 0, 0, $srcWidth, $srcHeight, 100);


		# FONT

		$font1 = 'RSU_BOLD.ttf';
		$font2 = 'RSU_BOLD.ttf';
		$fontsize1 = 25;
		$fontsize2 = 15;	
		$string1 = $_REQUEST["text-name"];
		$string2 = $_REQUEST["text-description"];

		// $string1 = iconv("tis-620", "utf-8", $string1);
		// $string2 = iconv("tis-620", "utf-8", $string2);

		$textwidth = 600;

		while (1){
			$box1 = imageTTFbbox( $fontsize1, 0, $font1, $string1 );
			$textwidth1 =  abs( $box1[2] );
			$textbodyheight1 = (abs($box1[7]))-2;
			if ( $textwidth1 < 620 )
				break;
			$fontsize1--;
		}

		while (1){
			$box2 = imageTTFbbox( $fontsize2, 0, $font2, $string2 );
			$textwidth2 =  abs( $box2[2] );
			$textbodyheight2 = (abs($box2[7]))-2;
			if ( $textwidth2 < 620 )
				break;
			$fontsize2--;
		}

		if ($string2 == "") {
			$Ycenter = 300;
		}
		else {
			$Ycenter = 280;
		}

		$Xcenter = 320;

		# COLOR

		$color_1 = $_REQUEST['color_1'];

      	$r1 = hexdec(substr($color_1,0,2));
      	$g1 = hexdec(substr($color_1,2,2));
      	$b1 = hexdec(substr($color_1,4,2));


      	$color_2 = $_REQUEST['color_2'];

      	$r2 = hexdec(substr($color_2,0,2));
      	$g2 = hexdec(substr($color_2,2,2));
      	$b2 = hexdec(substr($color_2,4,2));


		$color1 = ImageColorAllocate($myCard, $r1, $g1, $b1);
		$color2 = ImageColorAllocate($myCard, $r2, $g2, $b2);
		ImagettfText($myCard, $fontsize1, 0, (int) ($Xcenter-($textwidth1/2)), $Ycenter, $color1, $font1, $string1);
		ImagettfText($myCard, $fontsize2, 0, (int) ($Xcenter-($textwidth2/2)), 320, $color2, $font2, $string2);
 
		
		imagejpeg($myCard, $path_upload_card.$file_name, 100);
		imagedestroy($Logo_new);
		imagedestroy($myCard);


		echo "<script> document.location.href='create_card.php?brand=".$brand."&status=1';</script>";

	}

}
?>



<script>

function update(picker) {
    document.getElementById('hex-str').innerHTML = picker.toHEXString();
    document.getElementById('rgb-str').innerHTML = picker.toRGBString();

    document.getElementById('rgb').innerHTML =
        Math.round(picker.rgb[0]) + ', ' +
        Math.round(picker.rgb[1]) + ', ' +
        Math.round(picker.rgb[2]);

    document.getElementById('hsv').innerHTML =
        Math.round(picker.hsv[0]) + '&deg;, ' +
        Math.round(picker.hsv[1]) + '%, ' +
        Math.round(picker.hsv[2]) + '%';

}
</script>




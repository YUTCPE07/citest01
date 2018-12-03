<?php
header('Content-Type:text/html; charset=UTF-8');

//========================================//

ini_set("display_errors",1);
error_reporting(1);

ini_set('memory_limit','128M');

//========================================//

include('../../include/common.php');
include('../../lib/function_normal.php');
include('../../include/common_check.php');

//========================================//

?>

<html>
<head>
<title>.:: MemberIn ::.</title>
<script src="http://code.jquery.com/jquery-latest.js"></script>
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
	vertical-align: top;
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

<br>

<form action="create_coupon.php?brand='".$brand."'" method="post" enctype="multipart/form-data" name="frmMain">

<table width="1200px" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="700px" align="center" valign="top">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr align="center">
					<td>Style 1<br><br><input type="image" name="card_style" src="image/style_1.jpg" width="120px" value="1"></td>
					<td>Style 2<br><br><input type="image" name="card_style" src="image/style_2.jpg" width="120px" value="2"></td>
					<td>Style 3<br><br><input type="image" name="card_style" src="image/style_3.jpg" width="120px" value="3"></td>
					<td>Style 4<br><br><input type="image" name="card_style" src="image/style_4.jpg" width="120px" value="4"></td>
					<td>Style 5<br><br><input type="image" name="card_style" src="image/style_5.jpg" width="120px" value="5"></td>
				</tr>
				<tr>
					<td colspan="5" align="center">

					<hr>

					<?php

						error_reporting(1);

						$brand = $_REQUEST['brand'];
						$path_upload_coupon = '../../upload/'.$brand.'/coupon_upload/';
						$file_name = 'coupon_'.$brand.'.jpg';


						if (!$_REQUEST['card_style']) {	$card_style = 1;	}
						else {	$card_style = $_REQUEST['card_style'];	}

						$images = ImageCreateTrueColor(640,400);

					    $trans_colour = imagecolorallocatealpha($images, 0, 0, 0, 127);
					    imagefill($images, 0, 0, $trans_colour);


						# BG COLOR

						$color_bg = "E6E6E6";

						if ($_REQUEST['color_bg']) {	$color_bg = $_REQUEST['color_bg'];	}
						else {	$_REQUEST['color_bg'] = "E6E6E6";	}

				      	$r_bg = hexdec(substr($color_bg,0,2));
				      	$g_bg = hexdec(substr($color_bg,2,2));
				      	$b_bg = hexdec(substr($color_bg,4,2));

						$background = ImageColorAllocate($images, $r_bg, $g_bg, $b_bg);

						ImageFilledrectangle($images,0,0,640,400,$background);


						# LAYER 1

						$color_layer1 = "A4A4A4"; 

						if ($_REQUEST['color_layer1']) {	$color_layer1 = $_REQUEST['color_layer1'];	}
						else {	$_REQUEST['color_layer1'] = "A4A4A4"; }

				      	$r_layer1 = hexdec(substr($color_layer1,0,2));
				      	$g_layer1 = hexdec(substr($color_layer1,2,2));
				      	$b_layer1 = hexdec(substr($color_layer1,4,2));

						$layer1 = ImageColorAllocate($images, $r_layer1, $g_layer1, $b_layer1);


						# LAYER 2

						$color_layer2 = "585858"; 

						if ($_REQUEST['color_layer2']) {	$color_layer2 = $_REQUEST['color_layer2'];	}
						else {	$_REQUEST['color_layer2'] = "585858";	}
			 
				      	$r_layer2 = hexdec(substr($color_layer2,0,2));
				      	$g_layer2 = hexdec(substr($color_layer2,2,2));
				      	$b_layer2 = hexdec(substr($color_layer2,4,2));

						$layer2 = ImageColorAllocate($images, $r_layer2, $g_layer2, $b_layer2);

						$font = 'RSU_BOLD.ttf';


						# TEXT TYPE

						if ($_REQUEST["text-type"]) {	$string_type = $_REQUEST["text-type"];	}
						else {	$string_type = "COUPON";	}


						# TEXT NAME

						if ($_REQUEST["text-name"]) {	$string_name = $_REQUEST["text-name"];	}
						else {	$string_name = "Name";	}

						$color_name = $_REQUEST['color_name'];

						if ($_REQUEST["color_name"] && $_REQUEST["text-name"]) {	$color_name = $_REQUEST["color_name"];	}
						else {	$color_name = "585858";	}

				      	$r_name = hexdec(substr($color_name,0,2));
				      	$g_name = hexdec(substr($color_name,2,2));
				      	$b_name = hexdec(substr($color_name,4,2));

						$color_name = ImageColorAllocate($images, $r_name, $g_name, $b_name);


						# TEXT DESCRIPTION

						if ($_REQUEST["text-description"]) {	$description = $_REQUEST["text-description"];	}
						else {	$description = "";	}

						$color_des = $_REQUEST['color_des'];

						if ($_REQUEST["color_des"]) {	$color_des = $_REQUEST["color_des"];	}

				      	$r_des = hexdec(substr($color_des,0,2));
				      	$g_des = hexdec(substr($color_des,2,2));
				      	$b_des = hexdec(substr($color_des,4,2));

						$color_des = ImageColorAllocate($images, $r_des, $g_des, $b_des);




					# ========================================================================================= #




						# CARD STYLE

						if ($card_style==1) {

							# LAYER 1

							ImageFilledrectangle($images,0,50,640,95,$layer1);

							# LAYER 2

							ImageFilledrectangle($images,110,0,170,400,$layer2);

							# TEXT TYPE
							
							$font_size = 30;

							$box_type = imageTTFbbox( $font_size, 0, $font, $string_type );
							$type_width =  abs( $box_type[2] );

							ImagettfText($images, $font_size, 0, 610-$type_width, 84, $layer2, $font, $string_type);

							# TEXT NAME

							$size_name = 35;

							while(1) {
								$box_name = imageTTFbbox( $size_name, 0, $font, $string_name );
								$name_width =  abs( $box_name[2] );
								$name_height = (abs($box_name[7]))-2;
								if ( $name_width < 350 )	break;
								$size_name--;
							}

							$X_name = (int) (425-($name_width/2));
							$Y_name = 320;

							ImagettfText($images, $size_name, 0, $X_name, $Y_name, $color_name, $font, $string_name);

							if ($string_name=="Name") {
							
								ImagettfText($images, "20", 0, "372", "360", $layer1, $font, "Description");
							}

							# TEXT DESCRIPTION

							$size_des = 20;

							while(1) {
								$box_des = imageTTFbbox( $size_des, 0, $font, $description );
								$des_width =  abs( $box_des[2] );
								$des_height = (abs($box_des[7]))-2;
								if ( $des_width < 350 )	break;
								$size_des--;
							}

							$X_des = (int) (425-($des_width/2));
							$Y_des = 360;

							ImagettfText($images, $size_des, 0, $X_des, $Y_des, $color_des, $font, $description);

							# LOGO UPLOAD

							if(trim($_FILES["LogoUpload"]["tmp_name"]) != "") {

							# LOGO   

								$filename = $_FILES["LogoUpload"]["name"];
								$ext = pathinfo($filename, PATHINFO_EXTENSION);

								if ($ext == 'png' || $ext == 'PNG' || $ext == 'Png') {
									$myLogo = ImageCreateFromPng($_FILES["LogoUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromPng($_FILES["LogoUpload"]["tmp_name"]);
								}
								else if ($ext == 'jpg' || $ext == 'JPG' || $ext == 'jpeg' || $ext == 'JPEG') {
									$myLogo = ImageCreateFromJpeg($_FILES["LogoUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromJpeg($_FILES["LogoUpload"]["tmp_name"]);
								}
								else if ($ext == 'gif' || $ext == 'GIF') {
									$myLogo = imagecreatefromGif($_FILES["LogoUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromGif($_FILES["LogoUpload"]["tmp_name"]);
								}


								# RESIZE

								$size = GetimageSize($_FILES["LogoUpload"]["tmp_name"]);

								if ($size[0] > $size[1]) {
									$width = 160;
									$height = round($width*$size[1]/$size[0]);
								}

								else if ($size[0] < $size[1]) {
									$height = 160;
									$width = round($height*$size[0]/$size[1]);
								}

								else if ($size[0] == $size[1]) {
									$width = 160;
									$height = 160;
								}
								 
								$photoX = ImagesX($Logo_old);
								$photoY = ImagesY($Logo_old);
								$Logo_new = ImageCreateTrueColor($width, $height);
								ImageCopyResized($Logo_new, $Logo_old, 0, 0, 0, 0, $width+1, $height+1, $photoX, $photoY);

								$destWidth = 640;
								$destHeight = 400;
								$srcWidth = imagesx($Logo_new);
								$srcHeight = imagesy($Logo_new);

								$pxX = 0;
						 		$pyY = 0;

						 		if ($srcWidth > $srcHeight) {
						 			$pxX = 60;
						 			$pyY = 105 + (80 - ($srcHeight/2));
						 		}

						 		else if ($srcWidth < $srcHeight) {
						 			$pxX = 60 + (80 - ($srcWidth/2));
						 			$pyY = 105;
						 		}

						 		else if ($srcWidth == $srcHeight) {
						 			$pxX = 60;
						 			$pyY = 105;
						 		}

								$white = ImageColorExact($Logo_new, 0, 0, 0);
								ImageColorTransparent($Logo_new, $white);
						 
								ImageCopyMerge($images, $Logo_new, $pxX, $pyY, 0, 0, $srcWidth, $srcHeight, 100);

							} else {

								ImageFilledrectangle($images,60,105,220,265,$layer1);
								ImagettfText($images, "20", 0, "117", "195", $layer2, $font, "LOGO");

								ImageFilledrectangle($images,335,110,510,280,$layer1);
								ImagettfText($images, "18", 0, "383", "205", $layer2, $font, "PRODUCT");

							}

							# PRODUCT UPLOAD

							if(trim($_FILES["ProductUpload"]["tmp_name"]) != "") {   

							# PRODUCT 

								$filename = $_FILES["ProductUpload"]["name"];
								$ext = pathinfo($filename, PATHINFO_EXTENSION);

								if ($ext == 'png' || $ext == 'PNG' || $ext == 'Png') {
									$myLogo = ImageCreateFromPng($_FILES["ProductUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromPng($_FILES["ProductUpload"]["tmp_name"]);
								}
								else if ($ext == 'jpg' || $ext == 'JPG' || $ext == 'jpeg' || $ext == 'JPEG') {
									$myLogo = ImageCreateFromJpeg($_FILES["ProductUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromJpeg($_FILES["ProductUpload"]["tmp_name"]);
								}
								else if ($ext == 'gif' || $ext == 'GIF') {
									$myLogo = imagecreatefromGif($_FILES["ProductUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromGif($_FILES["ProductUpload"]["tmp_name"]);
								}


								# RESIZE

								$size = GetimageSize($_FILES["ProductUpload"]["tmp_name"]);

								if ($size[0] > $size[1]) {
									$width = 175;
									$height = round($width*$size[1]/$size[0]);
								}

								else if ($size[0] < $size[1]) {
									$height = 175;
									$width = round($height*$size[0]/$size[1]);
								}

								else if ($size[0] == $size[1]) {
									$width = 175;
									$height = 175;
								}
								 
								$photoX = ImagesX($Logo_old);
								$photoY = ImagesY($Logo_old);
								$Logo_new = ImageCreateTrueColor($width, $height);
								ImageCopyResized($Logo_new, $Logo_old, 0, 0, 0, 0, $width+1, $height+1, $photoX, $photoY);

								$destWidth = 640;
								$destHeight = 400;
								$srcWidth = imagesx($Logo_new);
								$srcHeight = imagesy($Logo_new);

								$pxX = 0;
						 		$pyY = 0;


						 		if ($srcWidth > $srcHeight) {
						 			$pxX = 336;
						 			$pyY = 107 + (87.5 - ($srcHeight/2));
						 		}

						 		else if ($srcWidth < $srcHeight) {
						 			$pxX = 336 + (87.5 - ($srcWidth/2));
						 			$pyY = 107;
						 		}

						 		else if ($srcWidth == $srcHeight) {
						 			$pxX = 336;
						 			$pyY = 107;
						 		}
						 		
								$white = ImageColorExact($Logo_new, 0, 0, 0);
								ImageColorTransparent($Logo_new, $white);
						 
								ImageCopyMerge($images, $Logo_new, $pxX, $pyY, 0, 0, $srcWidth, $srcHeight, 100);

							}

						} else if ($card_style==2) {

							# PRODUCT UPLOAD

							if(trim($_FILES["ProductUpload"]["tmp_name"]) != "") {   

							# PRODUCT 

								$filename = $_FILES["ProductUpload"]["name"];
								$ext = pathinfo($filename, PATHINFO_EXTENSION);

								if ($ext == 'png' || $ext == 'PNG' || $ext == 'Png') {
									$myLogo = ImageCreateFromPng($_FILES["ProductUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromPng($_FILES["ProductUpload"]["tmp_name"]);
								}
								else if ($ext == 'jpg' || $ext == 'JPG' || $ext == 'jpeg' || $ext == 'JPEG') {
									$myLogo = ImageCreateFromJpeg($_FILES["ProductUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromJpeg($_FILES["ProductUpload"]["tmp_name"]);
								}
								else if ($ext == 'gif' || $ext == 'GIF') {
									$myLogo = imagecreatefromGif($_FILES["ProductUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromGif($_FILES["ProductUpload"]["tmp_name"]);
								}


								# RESIZE

								$size = GetimageSize($_FILES["ProductUpload"]["tmp_name"]);

								if ($size[0] > $size[1]) {
									$height = 420;
									$width = round($height*$size[0]/$size[1]);
								}

								else if ($size[0] < $size[1]) {
									$width = 420;
									$height = round($width*$size[1]/$size[0]);
								}

								else if ($size[0] == $size[1]) {
									$width = 420;
									$height = 420;
								}
								 
								$photoX = ImagesX($Logo_old);
								$photoY = ImagesY($Logo_old);
								$Logo_new = ImageCreateTrueColor($width, $height);
								ImageCopyResized($Logo_new, $Logo_old, 0, 0, 0, 0, $width+1, $height+1, $photoX, $photoY);

								$destWidth = 640;
								$destHeight = 400;
								$srcWidth = imagesx($Logo_new);
								$srcHeight = imagesy($Logo_new);

								$pxX = 0;
						 		$pyY = 0;


						 		if ($srcWidth > $srcHeight) {
						 			$pxX = 0 - (($srcWidth/2)-210);
						 			$pyY = 0;
						 		}

						 		else if ($srcWidth < $srcHeight) {
						 			$pxX = 0;
						 			$pyY = 0 - (($srcHeight/2)-210);
						 		}

						 		else if ($srcWidth == $srcHeight) {
						 			$pxX = 0;
						 			$pyY = 10;
						 		}
						 		
								$white = ImageColorExact($Logo_new, 0, 0, 0);
								ImageColorTransparent($Logo_new, $white);
						 
								ImageCopyMerge($images, $Logo_new, $pxX, $pyY, 0, 0, $srcWidth, $srcHeight, 100);

							}

							# LAYER 1

							ImageFilledrectangle($images,420,0,640,400,$layer1);

							# TEXT TYPE
							
							$font_size = 20;

							while(1) {
								$box_type = imageTTFbbox( $font_size, 0, $font, $string_type );
								$type_width =  abs( $box_type[2] );
								$type_height = (abs($box_type[7]))-2;
								if ( $type_width < 200 )	break;
								$font_size--;
							}

							$X_type = 528-($type_width/2);
							$Y_type = 370;

							ImagettfText($images, $font_size, 0, $X_type, $Y_type, $layer2, $font, $string_type);

							# TEXT NAME

							$size_name = 30;

							while(1) {
								$box_name = imageTTFbbox( $size_name, 0, $font, $string_name );
								$name_width =  abs( $box_name[2] );
								$name_height = (abs($box_name[7]))-2;
								if ( $name_width < 190 )	break;
								$size_name--;
							}

							$X_name = 435;
							$Y_name = 200;

							ImagettfText($images, $size_name, 0, $X_name, $Y_name, $color_name, $font, $string_name);

							if ($string_name=="Name") {
							
								ImagettfText($images, "20", 0, "435", "240", $layer2, $font, "Description");
							}

							# TEXT DESCRIPTION

							$size_des = 20;

							while(1) {
								$box_des = imageTTFbbox( $size_des, 0, $font, $description );
								$des_width =  abs( $box_des[2] );
								$des_height = (abs($box_des[7]))-2;
								if ( $des_width < 190 )	break;
								$size_des--;
							}

							$X_des = 435;
							$Y_des = 240;

							ImagettfText($images, $size_des, 0, $X_des, $Y_des, $color_des, $font, $description);

							# LOGO UPLOAD

							if(trim($_FILES["LogoUpload"]["tmp_name"]) != "") {

							# LOGO   

								$filename = $_FILES["LogoUpload"]["name"];
								$ext = pathinfo($filename, PATHINFO_EXTENSION);

								if ($ext == 'png' || $ext == 'PNG' || $ext == 'Png') {
									$myLogo = ImageCreateFromPng($_FILES["LogoUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromPng($_FILES["LogoUpload"]["tmp_name"]);
								}
								else if ($ext == 'jpg' || $ext == 'JPG' || $ext == 'jpeg' || $ext == 'JPEG') {
									$myLogo = ImageCreateFromJpeg($_FILES["LogoUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromJpeg($_FILES["LogoUpload"]["tmp_name"]);
								}
								else if ($ext == 'gif' || $ext == 'GIF') {
									$myLogo = imagecreatefromGif($_FILES["LogoUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromGif($_FILES["LogoUpload"]["tmp_name"]);
								}


								# RESIZE

								$size = GetimageSize($_FILES["LogoUpload"]["tmp_name"]);

								if ($size[0] > $size[1]) {
									$width = 120;
									$height = round($width*$size[1]/$size[0]);
								}

								else if ($size[0] < $size[1]) {
									$height = 120;
									$width = round($height*$size[0]/$size[1]);
								}

								else if ($size[0] == $size[1]) {
									$width = 120;
									$height = 120;
								}
								 
								$photoX = ImagesX($Logo_old);
								$photoY = ImagesY($Logo_old);
								$Logo_new = ImageCreateTrueColor($width, $height);
								ImageCopyResized($Logo_new, $Logo_old, 0, 0, 0, 0, $width+1, $height+1, $photoX, $photoY);

								$destWidth = 640;
								$destHeight = 400;
								$srcWidth = imagesx($Logo_new);
								$srcHeight = imagesy($Logo_new);

								$pxX = 0;
						 		$pyY = 0;

						 		if ($srcWidth > $srcHeight) {
						 			$pxX = 470;
						 			$pyY = 0 + (60 - ($srcHeight/2));
						 		}

						 		else if ($srcWidth < $srcHeight) {
						 			$pxX = 470 + (60 - ($srcWidth/2));
						 			$pyY = 0;
						 		}

						 		else if ($srcWidth == $srcHeight) {
						 			$pxX = 470;
						 			$pyY = 0;
						 		}

								$white = ImageColorExact($Logo_new, 0, 0, 0);
								ImageColorTransparent($Logo_new, $white);
						 
								ImageCopyMerge($images, $Logo_new, $pxX, $pyY, 0, 0, $srcWidth, $srcHeight, 100);

							} else {

								ImageFilledrectangle($images,470,0,590,120,$background);
								ImagettfText($images, "18", 0, "505", "70", $layer2, $font, "LOGO");
								ImagettfText($images, "18", 0, "170", "200", $layer2, $font, "PRODUCT");

							}


						} else if ($card_style==3) {

							# PRODUCT UPLOAD

							if(trim($_FILES["ProductUpload"]["tmp_name"]) != "") {   

							# PRODUCT 

								$filename = $_FILES["ProductUpload"]["name"];
								$ext = pathinfo($filename, PATHINFO_EXTENSION);

								if ($ext == 'png' || $ext == 'PNG' || $ext == 'Png') {
									$myLogo = ImageCreateFromPng($_FILES["ProductUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromPng($_FILES["ProductUpload"]["tmp_name"]);
								}
								else if ($ext == 'jpg' || $ext == 'JPG' || $ext == 'jpeg' || $ext == 'JPEG') {
									$myLogo = ImageCreateFromJpeg($_FILES["ProductUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromJpeg($_FILES["ProductUpload"]["tmp_name"]);
								}
								else if ($ext == 'gif' || $ext == 'GIF') {
									$myLogo = imagecreatefromGif($_FILES["ProductUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromGif($_FILES["ProductUpload"]["tmp_name"]);
								}


								# RESIZE

								$size = GetimageSize($_FILES["ProductUpload"]["tmp_name"]);

								if ($size[0] > $size[1]) {
									$height = 400;
									$width = round($height*$size[0]/$size[1]);
								}

								else if ($size[0] < $size[1]) {
									$width = 400;
									$height = round($width*$size[1]/$size[0]);
								}

								else if ($size[0] == $size[1]) {
									$width = 400;
									$height = 400;
								}
								 
								$photoX = ImagesX($Logo_old);
								$photoY = ImagesY($Logo_old);
								$Logo_new = ImageCreateTrueColor($width, $height);
								ImageCopyResized($Logo_new, $Logo_old, 0, 0, 0, 0, $width+1, $height+1, $photoX, $photoY);

								$destWidth = 640;
								$destHeight = 400;
								$srcWidth = imagesx($Logo_new);
								$srcHeight = imagesy($Logo_new);

								$pxX = 0;
						 		$pyY = 0;


						 		if ($srcWidth > $srcHeight) {
						 			$pxX = 240 - (($srcWidth/2)-200);
						 			$pyY = 0;
						 		}

						 		else if ($srcWidth < $srcHeight) {
						 			$pxX = 240;
						 			$pyY = 0 - (($srcHeight/2)-200);
						 		}

						 		else if ($srcWidth == $srcHeight) {
						 			$pxX = 240;
						 			$pyY = 0;
						 		}
						 		
								$white = ImageColorExact($Logo_new, 0, 0, 0);
								ImageColorTransparent($Logo_new, $white);
						 
								ImageCopyMerge($images, $Logo_new, $pxX, $pyY, 0, 0, $srcWidth, $srcHeight, 100);

							}

							# LAYER 1

							ImageFilledrectangle($images,0,0,240,400,$layer1);

							# LAYER 2

							ImageFilledrectangle($images,0,0,240,5,$layer2);

							imagefilledellipse ($images, 50, 400, 200, 200, $layer2);
							imagefilledellipse ($images, 220, 380, 250, 250, $layer2);
							imagefilledellipse ($images, 360, 390, 200, 200, $layer2);
							imagefilledellipse ($images, 490, 360, 200, 200, $layer2);
							imagefilledellipse ($images, 580, 420, 200, 200, $layer2);
							imagefilledellipse ($images, 490, 360, 190, 190, $background);
							
							$font_size = 20;

							ImagettfText($images, $font_size, 0, 40, 365, $background, $font, $string_type);

							# TEXT NAME

							$size_name = 30;

							while(1) {
								$box_name = imageTTFbbox( $size_name, 0, $font, $string_name );
								$name_width =  abs( $box_name[2] );
								$name_height = (abs($box_name[7]))-2;
								if ( $name_width < 200 )	break;
								$size_name--;
							}

							$X_name = 20;
							$Y_name = 80;

							ImagettfText($images, $size_name, 0, $X_name, $Y_name, $color_name, $font, $string_name);

							if ($string_name=="Name") {
							
								ImagettfText($images, "20", 0, "20", "120", $layer2, $font, "Description");
							}

							# TEXT DESCRIPTION

							$size_des = 20;

							while(1) {
								$box_des = imageTTFbbox( $size_des, 0, $font, $description );
								$des_width =  abs( $box_des[2] );
								$des_height = (abs($box_des[7]))-2;
								if ( $des_width < 200 )	break;
								$size_des--;
							}

							$X_des = 20;
							$Y_des = 120;

							ImagettfText($images, $size_des, 0, $X_des, $Y_des, $color_des, $font, $description);

							# LOGO UPLOAD

							if(trim($_FILES["LogoUpload"]["tmp_name"]) != "") {

							# LOGO   

								$filename = $_FILES["LogoUpload"]["name"];
								$ext = pathinfo($filename, PATHINFO_EXTENSION);

								if ($ext == 'png' || $ext == 'PNG' || $ext == 'Png') {
									$myLogo = ImageCreateFromPng($_FILES["LogoUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromPng($_FILES["LogoUpload"]["tmp_name"]);
								}
								else if ($ext == 'jpg' || $ext == 'JPG' || $ext == 'jpeg' || $ext == 'JPEG') {
									$myLogo = ImageCreateFromJpeg($_FILES["LogoUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromJpeg($_FILES["LogoUpload"]["tmp_name"]);
								}
								else if ($ext == 'gif' || $ext == 'GIF') {
									$myLogo = imagecreatefromGif($_FILES["LogoUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromGif($_FILES["LogoUpload"]["tmp_name"]);
								}


								# RESIZE

								$size = GetimageSize($_FILES["LogoUpload"]["tmp_name"]);

								if ($size[0] > $size[1]) {
									$width = 120;
									$height = round($width*$size[1]/$size[0]);
								}

								else if ($size[0] < $size[1]) {
									$height = 120;
									$width = round($height*$size[0]/$size[1]);
								}

								else if ($size[0] == $size[1]) {
									$width = 120;
									$height = 120;
								}
								 
								$photoX = ImagesX($Logo_old);
								$photoY = ImagesY($Logo_old);
								$Logo_new = ImageCreateTrueColor($width, $height);
								ImageCopyResized($Logo_new, $Logo_old, 0, 0, 0, 0, $width+1, $height+1, $photoX, $photoY);

								$destWidth = 640;
								$destHeight = 400;
								$srcWidth = imagesx($Logo_new);
								$srcHeight = imagesy($Logo_new);

								$pxX = 0;
						 		$pyY = 0;

						 		if ($srcWidth > $srcHeight) {
						 			$pxX = 430;
						 			$pyY = 288 + (60 - ($srcHeight/2));
						 		}

						 		else if ($srcWidth < $srcHeight) {
						 			$pxX = 430 + (60 - ($srcWidth/2));
						 			$pyY = 288;
						 		}

						 		else if ($srcWidth == $srcHeight) {
						 			$pxX = 430;
						 			$pyY = 288;
						 		}

								$white = ImageColorExact($Logo_new, 0, 0, 0);
								ImageColorTransparent($Logo_new, $white);
						 
								ImageCopyMerge($images, $Logo_new, $pxX, $pyY, 0, 0, $srcWidth, $srcHeight, 100);

							} else {

								ImageFilledrectangle($images,430,290,550,410,$layer1);
								ImagettfText($images, "18", 0, "468", "360", $layer2, $font, "LOGO");
								ImagettfText($images, "18", 0, "410", "150", $layer2, $font, "PRODUCT");

							}

						} else if ($card_style==4) {

							# PRODUCT UPLOAD

							if(trim($_FILES["ProductUpload"]["tmp_name"]) != "") {   

							# PRODUCT 

								$filename = $_FILES["ProductUpload"]["name"];
								$ext = pathinfo($filename, PATHINFO_EXTENSION);

								if ($ext == 'png' || $ext == 'PNG' || $ext == 'Png') {
									$myLogo = ImageCreateFromPng($_FILES["ProductUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromPng($_FILES["ProductUpload"]["tmp_name"]);
								}
								else if ($ext == 'jpg' || $ext == 'JPG' || $ext == 'jpeg' || $ext == 'JPEG') {
									$myLogo = ImageCreateFromJpeg($_FILES["ProductUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromJpeg($_FILES["ProductUpload"]["tmp_name"]);
								}
								else if ($ext == 'gif' || $ext == 'GIF') {
									$myLogo = imagecreatefromGif($_FILES["ProductUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromGif($_FILES["ProductUpload"]["tmp_name"]);
								}


								# RESIZE

								$size = GetimageSize($_FILES["ProductUpload"]["tmp_name"]);

								if ($size[0] > $size[1]) {
									$height = 400;
									$width = round($height*$size[0]/$size[1]);
								}

								else if ($size[0] < $size[1]) {
									$width = 640;
									$height = round($width*$size[1]/$size[0]);
								}

								else if ($size[0] == $size[1]) {
									$width = 640;
									$height = 640;
								}
								 
								$photoX = ImagesX($Logo_old);
								$photoY = ImagesY($Logo_old);
								$Logo_new = ImageCreateTrueColor($width, $height);
								ImageCopyResized($Logo_new, $Logo_old, 0, 0, 0, 0, $width+1, $height+1, $photoX, $photoY);

								$destWidth = 640;
								$destHeight = 400;
								$srcWidth = imagesx($Logo_new);
								$srcHeight = imagesy($Logo_new);

						 		if ($srcWidth > $srcHeight) {
						 			$pxX = 0 + (320 - ($srcWidth/2));
						 			$pyY = 0;
						 		}

						 		else if ($srcWidth < $srcHeight) {
						 			$pxX = 0;
						 			$pyY = 0 + (200 - ($srcHeight/2));
						 		}

						 		else if ($srcWidth == $srcHeight) {
						 			$pxX = 0;
						 			$pyY = -110;
						 		}
						 		
								$white = ImageColorExact($Logo_new, 0, 0, 0);
								ImageColorTransparent($Logo_new, $white);

								ImageCopyMerge($images, $Logo_new, $pxX, $pyY, 0, 0, $srcWidth, $srcHeight, 100);

							}

							# LAYER 1

							ImageFilledrectangle($images,215,0,425,400,$layer1);
							ImageFilledrectangle($images,240,0,400,160,$background);

							# TEXT TYPE
							
							$size_type = 20;

							while(1) {
								$box_type = imageTTFbbox( $size_type, 0, $font, $string_type );
								$type_width =  abs( $box_type[2] );
								$type_height = (abs($box_type[7]))-2;
								if ( $type_width < 220 )	break;
								$size_type--;
							}

							$X_type = (int) (320-($type_width/2));
							$Y_type = 360;

							ImagettfText($images, $size_type, 0, $X_type, $Y_type, $layer2, $font, $string_type);

							# TEXT NAME

							$size_name = 35;

							while(1) {
								$box_name = imageTTFbbox( $size_name, 0, $font, $string_name );
								$name_width =  abs( $box_name[2] );
								$name_height = (abs($box_name[7]))-2;
								if ( $name_width < 220 )	break;
								$size_name--;
							}

							$X_name = (int) (320-($name_width/2));
							$Y_name = 240;

							ImagettfText($images, $size_name, 0, $X_name, $Y_name, $color_name, $font, $string_name);

							if ($string_name=="Name") {
							
								ImagettfText($images, "20", 0, "269", "280", $background, $font, "Description");
							}

							# TEXT DESCRIPTION

							$size_des = 16;

							while(1) {
								$box_des = imageTTFbbox( $size_des, 0, $font, $description );
								$des_width =  abs( $box_des[2] );
								$des_height = (abs($box_des[7]))-2;
								if ( $des_width < 220 )	break;
								$size_des--;
							}

							$X_des = (int) (320-($des_width/2));
							$Y_des = 280;

							ImagettfText($images, $size_des, 0, $X_des, $Y_des, $color_des, $font, $description);

							# LOGO UPLOAD

							if(trim($_FILES["LogoUpload"]["tmp_name"]) != "") {

							# LOGO   

								$filename = $_FILES["LogoUpload"]["name"];
								$ext = pathinfo($filename, PATHINFO_EXTENSION);

								if ($ext == 'png' || $ext == 'PNG' || $ext == 'Png') {
									$myLogo = ImageCreateFromPng($_FILES["LogoUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromPng($_FILES["LogoUpload"]["tmp_name"]);
								}
								else if ($ext == 'jpg' || $ext == 'JPG' || $ext == 'jpeg' || $ext == 'JPEG') {
									$myLogo = ImageCreateFromJpeg($_FILES["LogoUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromJpeg($_FILES["LogoUpload"]["tmp_name"]);
								}
								else if ($ext == 'gif' || $ext == 'GIF') {
									$myLogo = imagecreatefromGif($_FILES["LogoUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromGif($_FILES["LogoUpload"]["tmp_name"]);
								}


								# RESIZE

								$size = GetimageSize($_FILES["LogoUpload"]["tmp_name"]);

								if ($size[0] > $size[1]) {
									$width = 130;
									$height = round($width*$size[1]/$size[0]);
								}

								else if ($size[0] < $size[1]) {
									$height = 130;
									$width = round($height*$size[0]/$size[1]);
								}

								else if ($size[0] == $size[1]) {
									$width = 130;
									$height = 130;
								}
								 
								$photoX = ImagesX($Logo_old);
								$photoY = ImagesY($Logo_old);
								$Logo_new = ImageCreateTrueColor($width, $height);
								ImageCopyResized($Logo_new, $Logo_old, 0, 0, 0, 0, $width+1, $height+1, $photoX, $photoY);

								$destWidth = 640;
								$destHeight = 400;
								$srcWidth = imagesx($Logo_new);
								$srcHeight = imagesy($Logo_new);

								$pxX = 0;
						 		$pyY = 0;

						 		if ($srcWidth > $srcHeight) {
						 			$pxX = 255;
						 			$pyY = 10 + (65 - ($srcHeight/2));
						 		}

						 		else if ($srcWidth < $srcHeight) {
						 			$pxX = 255 + (65 - ($srcWidth/2));
						 			$pyY = 10;
						 		}

						 		else if ($srcWidth == $srcHeight) {
						 			$pxX = 255;
						 			$pyY = 10;
						 		}

								$white = ImageColorExact($Logo_new, 0, 0, 0);
								ImageColorTransparent($Logo_new, $white);

								ImageCopyMerge($images, $Logo_new, $pxX, $pyY, 0, 0, $srcWidth, $srcHeight, 100);

							} else {

								ImageFilledrectangle($images,255,10,385,140,$layer2);
								ImagettfText($images, "18", 0, "297", "80", $background, $font, "LOGO");
								ImagettfText($images, "18", 0, "65", "200", $layer2, $font, "PRODUCT");
								ImagettfText($images, "18", 0, "500", "200", $layer2, $font, "PRODUCT");

							}

						} else if ($card_style==5) {

							# LAYER 1

							ImageFilledrectangle($images,190,20,620,380,$layer1);

							# PRODUCT UPLOAD

							if(trim($_FILES["ProductUpload"]["tmp_name"]) != "") {   

							# PRODUCT 

								$filename = $_FILES["ProductUpload"]["name"];
								$ext = pathinfo($filename, PATHINFO_EXTENSION);

								if ($ext == 'png' || $ext == 'PNG' || $ext == 'Png') {
									$myLogo = ImageCreateFromPng($_FILES["ProductUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromPng($_FILES["ProductUpload"]["tmp_name"]);
								}
								else if ($ext == 'jpg' || $ext == 'JPG' || $ext == 'jpeg' || $ext == 'JPEG') {
									$myLogo = ImageCreateFromJpeg($_FILES["ProductUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromJpeg($_FILES["ProductUpload"]["tmp_name"]);
								}
								else if ($ext == 'gif' || $ext == 'GIF') {
									$myLogo = imagecreatefromGif($_FILES["ProductUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromGif($_FILES["ProductUpload"]["tmp_name"]);
								}


								# RESIZE

								$size = GetimageSize($_FILES["ProductUpload"]["tmp_name"]);

								if ($size[0] > $size[1]) {
									$height = 356;
									$width = round($height*$size[0]/$size[1]);
									$rect = ['x' => ($width-426)/2, 'y' => 0, 'width' => 426, 'height' => 356];
								}

								else if ($size[0] < $size[1]) {
									$width = 426;
									$height = round($width*$size[1]/$size[0]);
									$rect = ['x' => 0, 'y' => 0, 'width' => 426, 'height' => 356];
								}

								else if ($size[0] == $size[1]) {
									$width = 426;
									$height = 426;
									$rect = ['x' => 0, 'y' => 35, 'width' => 426, 'height' => 356];
								}
								 
								$photoX = ImagesX($Logo_old);
								$photoY = ImagesY($Logo_old);
								$Logo_new = ImageCreateTrueColor($width, $height);
								ImageCopyResized($Logo_new, $Logo_old, 0, 0, 0, 0, $width+1, $height+1, $photoX, $photoY);

								$destWidth = 640;
								$destHeight = 400;
								$srcWidth = imagesx($Logo_new);
								$srcHeight = imagesy($Logo_new);

						 		$pxX = 192;
						 		$pyY = 22;
						 		
								$white = ImageColorExact($Logo_new, 0, 0, 0);
								ImageColorTransparent($Logo_new, $white);

								$Logo_new = imagecrop($Logo_new,$rect);

								ImageCopyMerge($images, $Logo_new, $pxX, $pyY, 0, 0, $srcWidth, $srcHeight, 100);

							}

							# LOGO UPLOAD

							if(trim($_FILES["LogoUpload"]["tmp_name"]) != "") {

							# LOGO   

								$filename = $_FILES["LogoUpload"]["name"];
								$ext = pathinfo($filename, PATHINFO_EXTENSION);

								if ($ext == 'png' || $ext == 'PNG' || $ext == 'Png') {
									$myLogo = ImageCreateFromPng($_FILES["LogoUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromPng($_FILES["LogoUpload"]["tmp_name"]);
								}
								else if ($ext == 'jpg' || $ext == 'JPG' || $ext == 'jpeg' || $ext == 'JPEG') {
									$myLogo = ImageCreateFromJpeg($_FILES["LogoUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromJpeg($_FILES["LogoUpload"]["tmp_name"]);
								}
								else if ($ext == 'gif' || $ext == 'GIF') {
									$myLogo = imagecreatefromGif($_FILES["LogoUpload"]["tmp_name"]);
									$Logo_old = ImageCreateFromGif($_FILES["LogoUpload"]["tmp_name"]);
								}


								# RESIZE

								$size = GetimageSize($_FILES["LogoUpload"]["tmp_name"]);

								if ($size[0] > $size[1]) {
									$width = 110;
									$height = round($width*$size[1]/$size[0]);
								}

								else if ($size[0] < $size[1]) {
									$height = 110;
									$width = round($height*$size[0]/$size[1]);
								}

								else if ($size[0] == $size[1]) {
									$width = 110;
									$height = 110;
								}
								 
								$photoX = ImagesX($Logo_old);
								$photoY = ImagesY($Logo_old);
								$Logo_new = ImageCreateTrueColor($width, $height);
								ImageCopyResized($Logo_new, $Logo_old, 0, 0, 0, 0, $width+1, $height+1, $photoX, $photoY);

								$destWidth = 640;
								$destHeight = 400;
								$srcWidth = imagesx($Logo_new);
								$srcHeight = imagesy($Logo_new);

								$pxX = 0;
						 		$pyY = 0;

						 		if ($srcWidth > $srcHeight) {
						 			$pxX = 40;
						 			$pyY = 40 + (55 - ($srcHeight/2));
						 		}

						 		else if ($srcWidth < $srcHeight) {
						 			$pxX = 40 + (55 - ($srcWidth/2));
						 			$pyY = 40;
						 		}

						 		else if ($srcWidth == $srcHeight) {
						 			$pxX = 40;
						 			$pyY = 40;
						 		}

								$white = ImageColorExact($Logo_new, 0, 0, 0);
								ImageColorTransparent($Logo_new, $white);

								ImageCopyMerge($images, $Logo_new, $pxX, $pyY, 0, 0, $srcWidth, $srcHeight, 100);

							} else {

								ImageFilledrectangle($images,40,40,150,150,$layer2);
								ImageFilledrectangle($images,192,22,618,378,$background);
								ImagettfText($images, "18", 0, "73", "103", $background, $font, "LOGO");
								ImagettfText($images, "18", 0, "370", "200", $layer2, $font, "PRODUCT");

							}

							# LAYER 1

							ImageFilledrectangle($images,0,180,250,300,$layer1);

							# TEXT TYPE
							
							$size_type = 15;

							$box_type = imageTTFbbox( $size_type, 0, $font, $string_type );
							$type_width =  abs($box_type[2]);

							$X_type = (int) (180-$type_width);
							$Y_type = 320;

							ImagettfText($images, $size_type, 0, $X_type, $Y_type, $layer2, $font, $string_type);

							# TEXT NAME

							$size_name = 30;

							while(1) {
								$box_name = imageTTFbbox( $size_name, 0, $font, $string_name );
								$name_width =  abs( $box_name[2] );
								$name_height = (abs($box_name[7]))-2;
								if ( $name_width < 220 )	break;
								$size_name--;
							}

							$X_name = (int) (125-($name_width/2));
							$Y_name = 230;

							ImagettfText($images, $size_name, 0, $X_name, $Y_name, $color_name, $font, $string_name);

							if ($string_name=="Name") {
							
								ImagettfText($images, "15", 0, "88", "270", $layer2, $font, "Description");
							}

							# TEXT DESCRIPTION

							$size_des = 16;

							while(1) {
								$box_des = imageTTFbbox( $size_des, 0, $font, $description );
								$des_width =  abs( $box_des[2] );
								$des_height = (abs($box_des[7]))-2;
								if ( $des_width < 220 )	break;
								$size_des--;
							}

							$X_des = (int) (125-($des_width/2));
							$Y_des = 270;

							ImagettfText($images, $size_des, 0, $X_des, $Y_des, $color_des, $font, $description);

						}



						# MERGE

						echo "<img src=".$path_upload_coupon.$file_name.">";

						imagesavealpha($images, true);
						ImageJPEG($images,$path_upload_coupon.$file_name);
						ImageDestroy($images);

					?>

						
					</td>
				</tr>
			</table>
		</td>

		<td valign="top">

			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="150px" class="topic">Bg Color</td>
					<td class="option">
					<div class="col-xs-10">
						<input class="jscolor {onFineChange:'update(this)'} form-control text-md" name="color_bg" value="<?php if ($_REQUEST['color_bg']) { echo $_REQUEST['color_bg']; } else echo "000000"; ?>" style="width:100px" onchange="this.form.submit()"><br>
					</div>
					</td>
				</tr>
				<tr>
					<td width="150px" class="topic">Layer 1 Color</td>
					<td class="option">
					<div class="col-xs-10">
						<input class="jscolor {onFineChange:'update(this)'} form-control text-md" name="color_layer1" value="<?php if ($_REQUEST['color_layer1']) { echo $_REQUEST['color_layer1']; } else echo "000000"; ?>" style="width:100px" onchange="this.form.submit()"><br>
					</div>
					</td>
				</tr>
				<tr>
					<td width="150px" class="topic">Layer 2 Color</td>
					<td class="option">
					<div class="col-xs-10">
						<input class="jscolor {onFineChange:'update(this)'} form-control text-md" name="color_layer2" value="<?php if ($_REQUEST['color_layer2']) { echo $_REQUEST['color_layer2']; } else echo "000000"; ?>" style="width:100px" onchange="this.form.submit()"><br>
					</div>
					</td>
				</tr>
				<tr>
					<td class="topic">Logo <span class="text-rq">*</span></td>
					<td class="option">
					<div class="col-xs-10">
						<input class="form-control text-md" name="LogoUpload" type="file" value="<?php if ($_FILES["LogoUpload"]["tmp_name"]) { echo $_FILES["LogoUpload"]["tmp_name"]; } else echo ""; ?>">
					</div><br><br>
					<span class="text-rq" style="font-size:12px;padding-left:20px">Type file : .jpg, .gif, .png only</span>
					<br><br></td>
				</tr>
				<tr>
					<td class="topic">Product</td>
					<td class="option">
					<div class="col-xs-10">
						<input class="form-control text-md" name="ProductUpload" type="file">
					</div><br><br>
					<span class="text-rq" style="font-size:12px;padding-left:20px">Type file : .jpg, .gif, .png only</span>
					<br><br></td>
				</tr>
				<tr>
					<td class="topic">Type</td>
					<td class="option">
					<div class="form-group">
					<div class="col-xs-8">
						<input type="text" class="form-control text-md" name="text-type" value="<?php if ($_REQUEST['text-type']) { echo $_REQUEST['text-type']; } else echo "ACTIVITY"; ?>" onchange="this.form.submit()">
					</div>
					<br><br></td>
				</tr>
				<tr>
					<td class="topic">Name <span class="text-rq">*</span></td>
					<td class="option">
					<div class="form-group">
					<div class="col-xs-8">
						<input type="text" class="form-control text-md" name="text-name" value="<?php if ($_REQUEST['text-name']) { echo $_REQUEST['text-name']; } else echo ""; ?>" onchange="this.form.submit()">
					</div>
					<div class="col-xs-4">
						<input class="jscolor {onFineChange:'update(this)'} form-control text-md" name="color_name" value="<?php if ($_REQUEST['color_name']) { echo $_REQUEST['color_name']; } else echo "000000"; ?>" onchange="this.form.submit()">
					</div></div>
					<br><br></td>
				</tr>
				<tr>
					<td class="topic">Description</td>
					<td class="option">
					<div class="form-group">
					<div class="col-xs-8">
						<textarea class="form-control col-xs-7" name="text-description" rows="4" onchange="this.form.submit()"><?php echo $_REQUEST['text-description']; ?></textarea>
					</div>
					<div class="col-xs-4"><br>
						<input class="jscolor {onFineChange:'update(this)'} form-control text-md" name="color_des" value="<?php if ($_REQUEST['color_des']) { echo $_REQUEST['color_des']; } else echo "000000"; ?>" onchange="this.form.submit()">
					</div></div>
					<br><br><br><br><br></td>
				</tr>
				<tr>
					<td></td>
					<td class="option"><br>
						<input class="btn btn-default" type="submit" name="Submit" value="CREATE & SAVE">
						<input type="hidden" name="card_name" value="<?php echo $_REQUEST['card']; ?>">
						<input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>">
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
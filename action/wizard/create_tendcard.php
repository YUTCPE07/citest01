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

<?php

		$qr_image = imagecreatefromJpeg('../../images/tendcard/A6.jpg');
		$qr_logo = imagecreatefromJpeg('../../images/tendcard/qr.jpg');
						 
		ImageCopyMerge($qr_image, $qr_logo, 880, 747, 0, 0, 318, 318, 80);

		imagesavealpha($qr_image, true);
		ImageJPEG($qr_image,'../../images/tendcard/A6_t.jpg');

		# TEXT TYPE

		$font_size = 35;
		$string_text = 'โรีกรุงเทพsffsdfsdfsdfsdf';
		$font = 'RSU_BOLD.ttf';

		$images = imagecreatefromjpeg('../../images/tendcard/A6_t.jpg');  
		$color_name = ImageColorAllocate($images, 0, 0, 0);

		while(1) {
			
			$box_text = imageTTFbbox($font_size, 0, $font, $string_text);
			$text_width = abs($box_text[2]);
			if ( $text_width < 318 ) break;
			$font_size--;
		}

		$text_width = (int) (1039-($text_width/2));

		ImagettfText($images, $font_size, 0, $text_width, 1120, $color_name, $font, $string_text);

		ImageJPEG($images,'../../images/tendcard/A6_t.jpg');

		ImageDestroy($qr_image);
		ImageDestroy($images);
		ImageDestroy($qr_logo);

		echo "<img src='../../images/tendcard/A6_t.jpg'>";

?>
<?php echo '<pre>' ?>
<?php print_r($db)?>
<?php echo '</pre>' ?>
<?php
$str = '';

//echo (!empty(trim($str))) ? "true" : "false" ;

?>
<?php
function setDate($string) {return date("d-m-Y", strtotime($string));}

?>
<?php //echo $db['coup_ImagePath'] . $db['coup_Image'] ?>
<div class="lookup-head-img">
	<img class="img-fluid d-block ml-auto mr-auto shadow"
		src="upload/<?php echo $db['coup_ImagePath'] . $db['coup_Image'] ?>" alt="First slide">
</div>

<div class="container">
	<div class="d-flex flex-row-reverse p-md-5 p-4 ">
		<button class="btn btn-primary ml-md-5 ml-3">แชร์</button>
		<button class="btn btn-primary">ชื่นชอบ</button>
	</div>
		<div class="row">
			<div class="col-md-2">
				<img class="img-thumbnail shadow
					d-none d-md-block d-lg-block d-xl-block"
					src="upload/<?php echo $db['path_logo'] . $db['logo_image'] ?>" alt="First slide">
				<img class="img-thumbnail w-50 shadow rounded-circle ml-auto mr-auto mb-3
					d-block d-md-none d-lg-none d-xl-none"
					src="upload/<?php echo $db['path_logo'] . $db['logo_image'] ?>" alt="First slide">
			</div>
			<div class="col-md-10 w-100">
				<div class="row">
					<div class="col-md-12"><div class="h2 medium"><?php echo $db['coup_Name']; ?></div></div>
					<div class="col-md-12">
						<div class="text-justify" ><?php echo nl2br($db['signature_info']); ?></div>
					</div>
					<div class="col-md-2 h3">
						<i class="fas fa-heart text-danger" data-fa-transform="up-1"></i> 2.0k
					</div>
					<div class="col-md-10 h3">
						<?php $randomNum = rand(0, 5);?>
						<?php	for ($i = 1; $i <= 5; $i++) {?>
						<?php if ($i <= $randomNum) {?>
		        			<i class="fa fa-star text-warning " data-fa-transform="up-2"></i>
	        			<?php } elseif ($i >= $randomNum) {?>
		        			<i class="far fa-star text-warning " data-fa-transform="up-2"></i>
        				<?php }?>
	        			<?php }?>
	        			<?php echo $randomNum ?>
	              	</div>
				</div>
			</div>
		</div>
	<hr class="pb-3">
</div>

<div class="container">
	<div class="row">
		<div class="col-md-8">
			<?php //row 1 ?>
			<div class="row p-3">
				<div class="col-12 col-md-6 text-justify">
					<div class="row h5">
						<i class="fas fa-stopwatch mr-4" data-fa-transform="down-2"></i>
						<div class="h5 medium">ระยะเวลากิจกรรม: </div>
						<div class="h5 light ml-2"> <?php echo $db['coup_ActivityDuration']; ?></div>
					</div>
					<div class="row h5">
						<i class="fas fa-business-time mr-3" data-fa-transform="down-2"></i>
						<div class="h5 medium">ระยะเวลาโปร: </div>
						<div class="h5 light ml-5">
<?php echo ($db['coup_StartDate'] == '0000-00-00') ?
"00-00-0000 - " . setDate($db['coup_EndDate']) . '<br>' :
setDate($db['coup_StartDate']) . ' - ' . setDate($db['coup_EndDate']) . '<br>';
echo $db['coup_StartTime'] . ' - ' . $db['coup_EndTime'];
?>
						</div>
					</div>
					<?php if (!empty(trim($db['coup_Participation']))) {?>
					<div class="row h5">
						<i class="fas fa-infinity mr-3" data-fa-transform="down-2"></i>
						<div class="h5 medium">สิทธิ์คงเหลือ: </div>
						<div class="h5 light ml-2">
							<?php echo $db['coup_Participation']; ?>
						</div>
					</div>
					<?php }?>
					<div class="row h5">
						<i class="fas fa-store mr-3" data-fa-transform="down-2"></i>
						<div class="h5 medium">วันทำการ: </div>
						<div class="h5 light ml-2"><?php echo $db['open_brief']; ?></div>
					</div>
				</div>
				<div class="col-12 col-md-6 text-justify">
					<div class="row h5">
						<i class="fas fa-users mr-3"></i>
						<div class="h5 medium">ผู้เข้าร่วมขั้นต่ำ: </div>
						<div class="h5 light ml-2">
							<?php echo (empty(trim($db['coup_Participation']))) ? "ไม่กำหนด" :
$db['coup_Participation'];
?>
						</div>
					</div>
					<div class="row h5">
						<i class="far fa-calendar-alt mr-3"></i>
						<div class="h5 medium">ใช้สิทธิ์ได้ถึง: </div>
						<div class="h5 light ml-2">
							<?php
$mh = $db['coup_Method'];
if ($mh == 'No') {
	echo "ไม่กำหนด";
} elseif ($mh == 'Fix') {
	echo setDate($db['coup_EndDateUse']);
} elseif ($mh == 'Month') {
	echo 'Month';
} elseif ($mh == 'Year') {
	echo 'Year';
}
?>
						</div>
					</div>
					<div class="row h5">
						<i class="far fas fa-eraser mr-3"></i>
						<div>วิธีใช้สิทธิ์: </div>
						<div class="h5 light ml-2">
							<?php echo $db['shop_howtouse_brief']; ?>
						</div>
					</div>
					<div class="row h5">
						<i class="fas fa-user-tag mr-3"></i>
						<div>จำกัดสิทธิ์: </div>
						<div class="h5 light ml-2">ไม่จำกัด</div>
					</div>
				</div>
			</div>
			<?php //end row1 ?>
			<?php //row 2 ?>
			<!-- <div class="row p-3">
				<div>ไฮไลท์</div>
				<div class=" text-justify"><?php //echo $db['coup_Description']; ?></div>
			</div>
			<div class="row justify-content-center pt-3 pb-3">
				<img class="col-12" src="http://placehold.it/600x400">
			</div>
			<div class="row pt-3">
				<div class="col-12">โปรโมชั่นบัตรราคาพิเศษ</div>
				<div class=" col-12">Sky Zone จาก 2,500 บาท เหลือ 799 บาท ลด 68%</div>
				<div class=" col-12">Ocean/Clound Zone จาก 3,000 บาท เหลือ 1,199 บาท ลด 60%</div>
				<div class=" col-12">Star Zone จาก 4,000 บาท เหลือ 1,899 บาท ลด 53%</div>
			</div>
			<div class="row justify-content-center pt-3 pb-3">
				<img class="col-12" src="http://placehold.it/600x400">
			</div> -->
			<?php if (!empty($db['open_description'])): ?>
				<div class="row pt-3">
					<div class="col-12 h4 bold">เวลาทำการ</div>
					<div class=" col-12 h5 light text-justify">
						<?php echo nl2br($db['open_description']); ?>
					</div>
				</div>
			<?php endif?>

			<div class="row pt-3">
				<div class="col-12 h4 bold">วิธีการจอง</div>
				<div class="col-12 h5 light">
					<?php echo (empty($db['coup_HowToUse'])) ? "null" : nl2br($db['coup_HowToUse']); ?>
				</div>
			</div>
			<div class="row pt-3">
				<div class="col-12 h4 bold">ข้อตกลง/เงื่อนไข</div>
				<div class=" col-12 h5 light">
					<?php echo (empty($db['coup_Condition'])) ? "null" : nl2br($db['coup_Condition']); ?>
				</div>
			</div>
			<div class="row pt-3">
				<div class="col-12 h4 bold">ข้อยกเว้น</div>
				<div class=" col-12 h5 light text-justify">
					<?php echo (empty($db['coup_Exception'])) ? "null" : nl2br($db['coup_Exception']); ?>
				</div>
			</div>
			<?php if (!empty($db['shop_cancellation_description'])): ?>
				<div class="row pt-3">
					<div class="col-12 h4 bold">นโยบายการยกเลิก</div>
					<div class=" col-12 h5 light text-justify">
						<?php echo nl2br($db['shop_cancellation_description']); ?>
					</div>
				</div>
			<?php endif?>

			<!-- <div class="row pt-3">
				<div class="col-12">ที่ตั้ง</div>
				<div class=" col-12"> 9822 ถนน สาทรเหนือ ซอย สาทร 12 แขวง สีลม เขต บางรัก กรุงเทพมหานคร 10500</div>
			</div> -->
			<!-- <div class="row justify-content-center pt-3 pb-3">
				<img class="col-12" src="http://placehold.it/600x400">
			</div> -->
			<!-- <div class="row pt-3">
				<div class="col-12">การเดินทาง</div>
				<div class="row justify-content-center">
					<img class="col-12" src="http://placehold.it/600x400">
					<img class="col-12" src="http://placehold.it/600x400">
				</div>
			</div> -->
			<div class="row pt-3">
				<?php if (!empty($db['shop_q1'])): ?>
					<div class="col-12">ถาม/ตอบ</div>
				<?php endif?>
				<?php for ($i = 1; $i <= 5; $i++) {?>
				<?php if (!empty($db['shop_q' . $i])): ?>
					<div class=" col-12 text-justify">
						<div class="text-gray-dark h4">Q : </div>
						<?php echo (empty($db['shop_q' . $i])) ? "null" : $db['shop_q' . $i] . ' ?'; ?>
					</div>
					<div class=" col-12 text-justify">
						<div class="text-gray-dark h4">A : </div>
						<?php echo (empty($db['shop_a' . $i])) ? "null" : $db['shop_a' . $i]; ?>
					</div>
				<?php endif?>
				<?php }?>
			</div>
			<div class="row pt-3">
				<?php if (!empty($db['coup_Contact'])): ?>
				<div class="col-12 h4 bold">ติดต่อ</div>
				<div class="col-12 h5 light"><div class="text-gray-dark">โทร</div>
					<?php echo $db['coup_Contact']; ?>
				</div>
				<?php endif?>
			</div>
			<div class="row pt-3">
				<?php if (!(empty($db['website']) && empty($db['facebook_url']) &&
	empty($db['line_id']) && empty($db['instragram']) && empty($db['tweeter']))):
?>
				<div class="col-12">ที่มา</div>
<?php endif?>
				<?php if (!empty($db['website'])): ?>
					<a class="col-12 h4 thin" href="<?php echo $db['website']; ?>">
						<i class="fas fa-globe mr-3 text-info"></i><div>
							<?php echo $db['website']; ?>
						</div>
					</a>
				<?php endif?>
				<?php if (!empty($db['facebook_url'])): ?>
					<a class="col-12 h4 thin" href="<?php echo $db['facebook_url']; ?>">
						<i class="fab fa-facebook-square mr-3 text-blue"></i><div>
							<?php echo $db['facebook_url']; ?>
						</div>
					</a>
				<?php endif?>
				<?php if (!empty($db['line_id'])): ?>
					<a class="col-12 h4" href="<?php echo $db['line_id']; ?>">
						<i class="fab fa-line mr-3 text-success"></i><div>
							<?php echo $db['line_id']; ?>
						</div>
					</a>
				<?php endif?>
				<?php if (!empty($db['instragram'])): ?>
					<a class="col-12 h4" href="<?php echo $db['instragram']; ?>">
						<i class="fab fa-instagram mr-3 text-instagram"></i><div>
							<?php echo $db['instragram']; ?>
						</div>
					</a>
				<?php endif?>
				<?php if (!empty($db['tweeter'])): ?>
					<a class="col-12 h4" href="<?php echo $db['tweeter']; ?>">
						<i class="fab fa-twitter mr-3 text-info text-twitter"></i><div>
							<?php echo $db['tweeter']; ?>
						</div>
					</a>
				<?php endif?>

			</div>

			<?php //end row2 ?>
		</div>
		<?php //tab controller ,select time, select date, selectzone, show price, addtocart, buynow ?>
		<div class="col-md-4 ">
			<div class="row">
				<div id="my-calendar" class="blue material-theme" ></div>
				<!-- Outputs -->
				Day click : <br>
				<input class="form-control" id="my-input-a"><br>
				Month change : <br>
				<input class="form-control" id="my-input-b"><br>
			</div>
		</div>
	</div>
</div>

<div class="hr_footer_height"></div>
<?php //view product user select calender ?>
<script src="<?php echo base_url('assets/plugins/jsCalendar/jsCalendar.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/jsCalendar/jsCalendar.lang.uk.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/jsCalendar/jsCalendar_custom.js') ?>"></script>

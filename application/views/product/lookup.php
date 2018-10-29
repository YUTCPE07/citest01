<?php //echo '<pre>' ?>
<?php //print_r($db)?>
<?php //echo '</pre>' ?>
<?php
$str = '';

//echo (!empty(trim($str))) ? "true" : "false" ;

?>
<?php
function setDate($string) {return date("d-m-Y", strtotime($string));}

?>
<?php //echo $db['coup_ImagePath'] . $db['coup_Image'] ?>

<img class="img-fluid d-block ml-auto mr-auto shadow" src="upload/<?php echo $db['coup_ImagePath'] . $db['coup_Image'] ?>" alt="First slide">
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
					<div class="col-md-12"><h4><?php echo $db['coup_Name']; ?></h4></div>
					<div class="col-md-12">
						<p class="text-justify" ><?php echo nl2br($db['signature_info']); ?></p>
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
						<strong>ระยะเวลากิจกรรม: </strong>
						<h5 class="ml-2"> <?php echo $db['coup_ActivityDuration']; ?></h5>
					</div>
					<div class="row h5">
						<i class="fas fa-business-time mr-3" data-fa-transform="down-2"></i>
						<strong>ระยะเวลาโปร: </strong>
						<h5 class="ml-5">
							<?php
echo ($db['coup_StartDate'] == '0000-00-00') ?
"00-00-0000 - " . setDate($db['coup_EndDate']) . '<br>' :
setDate($db['coup_StartDate']) . ' - ' . setDate($db['coup_EndDate']) . '<br>';
echo $db['coup_StartTime'] . ' - ' . $db['coup_EndTime'];
?>
						</h5>
					</div>
					<?php //if(!empty(trim($db['coup_Participation']))){ ?>
					<div class="row h5">
						<i class="fas fa-infinity mr-3" data-fa-transform="down-2"></i>
						<strong>สิทธิ์คงเหลือ: </strong>
						<h5 class="ml-2">
							<?php echo $db['coup_Participation']; ?>
						</h5>
					</div>
					<?php //} ?>
					<div class="row h5">
						<i class="fas fa-store mr-3" data-fa-transform="down-2"></i>
						<strong>วันทำการ: </strong>
						<h5 class="ml-2"><?php echo $db['open_brief']; ?></h5>
					</div>
				</div>
				<div class="col-12 col-md-6 text-justify">
					<div class="row h5">
						<i class="fas fa-users mr-3"></i>
						<strong>ผู้เข้าร่วมขั้นต่ำ: </strong>
						<h5 class="ml-2">
							<?php echo (empty(trim($db['coup_Participation']))) ? "ไม่กำหนด" :
$db['coup_Participation'];
?>
						</h5>
					</div>
					<div class="row h5">
						<i class="far fa-calendar-alt mr-3"></i>
						<strong>ใช้สิทธิ์ได้ถึง: </strong>
						<h5 class="ml-2">
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
						</h5>
					</div>
					<div class="row h5">
						<i class="far fas fa-eraser mr-3"></i>
						<strong>วิธีใช้สิทธิ์: </strong>
						<h5 class="ml-2">
							<?php echo $db['shop_howtouse_brief']; ?>
						</h5>
					</div>
					<div class="row h5">
						<i class="fas fa-user-tag mr-3"></i>
						<strong>จำกัดสิทธิ์: </strong>
						<h5 class="ml-2">ไม่จำกัด</h5>
					</div>
				</div>
			</div>
			<?php //end row1 ?>
			<?php //row 2 ?>
			<!-- <div class="row p-3">
				<h3>ไฮไลท์</h3>
				<p class=" text-justify"><?php //echo $db['coup_Description']; ?></p>
			</div>
			<div class="row justify-content-center pt-3 pb-3">
				<img class="col-12" src="http://placehold.it/600x400">
			</div>
			<div class="row pt-3">
				<h3 class="col-12">โปรโมชั่นบัตรราคาพิเศษ</h3>
				<p class=" col-12">Sky Zone จาก 2,500 บาท เหลือ 799 บาท ลด 68%</p>
				<p class=" col-12">Ocean/Clound Zone จาก 3,000 บาท เหลือ 1,199 บาท ลด 60%</p>
				<p class=" col-12">Star Zone จาก 4,000 บาท เหลือ 1,899 บาท ลด 53%</p>
			</div>
			<div class="row justify-content-center pt-3 pb-3">
				<img class="col-12" src="http://placehold.it/600x400">
			</div> -->
			<div class="row pt-3">
				<h3 class="col-12">เวลาทำการ</h3>
				<p class=" col-12">
					<?php echo (empty($db['open_description'])) ? "null" : $db['open_description']; ?>
				</p>
			</div>
			<div class="row pt-3">
				<h3 class="col-12">วิธีการจอง</h3>
				<p class="col-12">
					<?php echo (empty($db['coup_HowToUse'])) ? "null" : $db['coup_HowToUse']; ?>
				</p>
			</div>
			<div class="row pt-3">
				<h3 class="col-12">ข้อตกลง/เงื่อนไข</h3>
				<p class=" col-12">
					<?php echo (empty($db['coup_Condition'])) ? "null" : $db['coup_Condition']; ?>
				</p>
			</div>
			<div class="row pt-3">
				<h3 class="col-12">ข้อยกเว้น</h3>
				<p class=" col-12 text-justify">
					<?php echo (empty($db['coup_Exception'])) ? "null" : $db['coup_Exception']; ?>
				</p>
			</div>
			<div class="row pt-3">
				<h3 class="col-12">นโยบายการยกเลิก</h3>
				<p class=" col-12 text-justify">
					<?php echo (empty($db['shop_cancellation_description'])) ? "null" : $db['shop_cancellation_description']; ?>
				</p>
			</div>
			<!-- <div class="row pt-3">
				<h3 class="col-12">ที่ตั้ง</h3>
				<p class=" col-12"> 9822 ถนน สาทรเหนือ ซอย สาทร 12 แขวง สีลม เขต บางรัก กรุงเทพมหานคร 10500</p>
			</div> -->
			<!-- <div class="row justify-content-center pt-3 pb-3">
				<img class="col-12" src="http://placehold.it/600x400">
			</div> -->
			<!-- <div class="row pt-3">
				<h3 class="col-12">การเดินทาง</h3>
				<div class="row justify-content-center">
					<img class="col-12" src="http://placehold.it/600x400">
					<img class="col-12" src="http://placehold.it/600x400">
				</div>
			</div> -->
			<div class="row pt-3">
				<h3 class="col-12">ถาม/ตอบ</h3>
				<?php
for ($i = 1; $i <= 5; $i++) {

	?>
				<p class=" col-12 text-justify">
					<strong class="text-gray-dark h4">Q : </strong>
					<?php echo (empty($db['shop_q' . $i])) ? "null" : $db['shop_q' . $i] . ' ?'; ?>
				</p>
				<p class=" col-12 text-justify">
					<strong class="text-gray-dark h4">A : </strong>
					<?php echo (empty($db['shop_a' . $i])) ? "null" : $db['shop_a' . $i]; ?>
				</p>
				<?php }?>
			</div>
			<div class="row pt-3">
				<h3 class="col-12">ติดต่อ</h3>
				<p class="col-12 h4 text-primary"><strong class="text-gray-dark">โทร</strong>
					<?php echo (empty($db['coup_Contact' . $i])) ? "null" : $db['coup_Contact' . $i]; ?>
				</p>
			</div>
			<div class="row pt-3">
				<h3 class="col-12">ที่มา</h3>
				<a class="col-12 h4" href="/">
					<i class="fas fa-globe mr-3 text-info"></i><strong>
						<?php echo (empty($db['website' . $i])) ? "null" : $db['website' . $i]; ?>
					</strong>
				</a>
				<a class="col-12 h4" href="/">
					<i class="fab fa-facebook-square mr-3 text-blue"></i><strong>
						<?php echo (empty($db['facebook_url' . $i])) ? "null" : $db['facebook_url' . $i]; ?>
					</strong>
				</a>
				<a class="col-12 h4" href="/">
					<i class="fab fa-line mr-3 text-success"></i><strong>
						<?php echo (empty($db['line_id' . $i])) ? "null" : $db['line_id' . $i]; ?>
					</strong>
				</a>
				<a class="col-12 h4" href="/">
					<i class="fab fa-instagram mr-3 text-instagram"></i><strong>
						<?php echo (empty($db['instragram' . $i])) ? "null" : $db['instragram' . $i]; ?>
					</strong>
				</a>
				<a class="col-12 h4" href="/">
					<i class="fab fa-twitter mr-3 text-info text-twitter"></i><strong>
						<?php echo (empty($db['tweeter' . $i])) ? "null" : $db['tweeter' . $i]; ?>
					</strong>
				</a>
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

<?php //view product user select calender ?>
<script src="<?php echo base_url('assets/plugins/jsCalendar/jsCalendar.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/jsCalendar/jsCalendar.lang.uk.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/jsCalendar/jsCalendar_custom.js') ?>"></script>

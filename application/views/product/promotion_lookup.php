<!-- test promotion/ (1-3) -->
<?php //echo '<pre>' ?>
<?php //print_r($db)?>
<?php //echo '</pre>' ?>

<?php
function setDate($string) {return date("d-m-Y", strtotime($string));}

?>
<?php //echo $db['coup_ImagePath'] . $db['coup_Image'] ?>
<div class="container-fluid-my">
	<div class="lookup-head-img pt-0">
		<img class="img-fluid d-block ml-auto mr-auto shadow border-my"
			src="upload/<?php echo $db['coup_ImagePath'] . $db['coup_Image'] ?>" alt="First slide">
	</div>
</div>


<div class="container">
	<div class="d-flex flex-row-reverse p-md-5 p-4 ">
		<button class="btn btn-primary ml-md-5 ml-3">แชร์</button>
		<button class="btn btn-primary">ชื่นชอบ</button>
	</div>
		<div class="row">
			<div class="col-md-2">
				<img class="img-thumbnail shadow imgBrandSize ml-auto
					d-none d-md-block d-lg-block d-xl-block"
					src="upload/<?php echo $db['path_logo'] . $db['logo_image'] ?>" alt="First slide">
				<img class="img-thumbnail w-50 shadow rounded-circle ml-auto mr-auto mb-3
					d-block d-md-none d-lg-none d-xl-none"
					src="upload/<?php echo $db['path_logo'] . $db['logo_image'] ?>" alt="First slide">
			</div>
			<div class="col-md-10 w-100">
				<div class="row">
					<div class="col-md-12"><div class="h3 medium"><?php echo $db['coup_Name']; ?></div></div>
					<div class="col-md-12"> <!-- test promotion/137@@ : promotion/1@@ -->
						<div class="text-justify" ><?php echo nl2br($db['signature_info']); ?></div>
					</div>

					<div class="col-md-10 h3"><!-- Rating Star -->

	              	</div>
				</div>
			</div>
		</div>
	<hr class="pb-3">
</div>
<div class="container pb-5">
	<div class="row">
		<div class="col-md-10">
			<div class="row p-3">
				<div class="col-12 col-md-6 d-flex align-items-start">
					<div class="mr-3"><i class="fas fa-business-time fa-fw"></i></div>
					<div class="medium">ระยะเวลาโปร: </div> <!-- test test promotion/3@@ : promotion/1@@ -->
					<div class="light ml-2" style="display: inline-grid;">
						<div>
							<?php echo date("d/m/y", strtotime($db['coup_StartDate'])) ?>
							-
							<?php echo date("d/m/y", strtotime($db['coup_EndDate'])) ?>
						</div>
						<?php if (!($db['coup_StartTime'] == '00:00:00' || $db['coup_StartTime'] == '00:00:00')): ?>
							<div class="pl-auto">
								<?php echo date("h:i", strtotime($db['coup_StartTime'])) ?>
								-
								<?php echo date("h:i", strtotime($db['coup_EndTime'])) ?>
							</div>
						<?php endif?>
					</div>
				</div>


				<?php if (!empty(trim($db['coup_Participation']))): ?>
					<div class="col-12 col-md-6 d-flex align-items-start">
						<div class="mr-3"><i class="fas fa-infinity fa-fw"></i></div>
						<div class="medium">สิทธิ์คงเหลือ: </div>
						<div class="light ml-2">
							<?php echo $db['coup_Participation']; ?>
						</div>
					</div>
				<?php endif?>

				<?php if (!empty(trim($db['open_brief']))): ?>
					<div class="col-12 col-md-6 d-flex align-items-start">
						<div class="mr-3"><i class="fas fa-store fa-fw"></i></div>
						<div class="medium">วันทำการ: </div>
						<div class="light ml-2"><?php echo $db['open_brief']; ?></div>
					</div>
				<?php endif?>

				<div class="col-12 col-md-6 d-flex align-items-start">
					<div class="mr-3"><i class="fas fa-users fa-fw"></i></div>
					<div class="medium">ผู้เข้าร่วมขั้นต่ำ: </div>
					<div class="light ml-2">
						<?php if (empty(trim($db['coup_Participation']))): ?>
							<?php echo "ไม่กำหนด" ?>
						<?php else: ?>
							<?php echo $db['coup_Participation'] ?>
						<?php endif?>
					</div>
				</div>

				<div class="col-12 col-md-6 d-flex align-items-start">
					<div class="mr-3"><i class="far fa-calendar-alt fa-fw"></i></div>
					<div class="medium">ใช้สิทธิ์ได้ถึง: </div>
					<div class="light ml-2">
						<?php $mh = $db['coup_Method'];?>
						<?php if ($mh == 'No'): ?>
							<?php echo "ไม่กำหนด"; ?>
						<?php elseif ($mh == 'Fix' && $db['coup_EndDateUse'] == '0000-00-00'): ?>
							<?php echo "-" ?>
						<?php elseif ($mh == 'Fix'): ?>
							<?php echo setDate($db['coup_EndDateUse']) ?>
						<?php elseif ($mh == 'Month'): ?>
							<?php echo 'Month'; ?>
						<?php elseif ($mh == 'Year'): ?>
							<?php echo 'Year'; ?>
						<?php endif?>
					</div>
				</div>

				<?php if (!empty(trim($db['shop_howtouse_brief']))): ?>
					<div class="col-12 col-md-6 d-flex align-items-start">
						<div class="mr-3"><i class="far fas fa-eraser fa-fw"></i></div>
						<div class= "menium">วิธีใช้สิทธิ์: </div>
						<div class= "light ml-2">
							<?php echo $db['shop_howtouse_brief']; ?>
						</div>
					</div>
				<?php endif?>

				<div class="col-12 col-md-6 d-flex align-items-start">
					<i class="fas fa-user-tag mr-3 fa-fw"></i>
					<div class="medium">จำกัดสิทธิ์: </div>
					<div class="light ml-2">
						<?php echo (empty(trim($db['coup_Participation']))) ? "ไม่จำกัด" : $db['coup_Participation']; ?>
					</div>
				</div>
			</div> <!-- end row -->
		</div>
		<div class="col-md-2 ">
			<a href="#">
				<div class="membercard_lookup_buttom bg-green text-center text-white p-2"
					style="border-radius: 10px 10px 10px 10px;">
					<div class="medium">รับสิทธิ์</div>
				</div>
			</a>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<?php if (!empty($db['open_description'])): ?>
				<div class="row pt-3">
					<div class="col-12 bold">เวลาทำการ</div>
					<div class=" col-12 light text-justify">
						<?php echo nl2br($db['open_description']); ?>
					</div>
				</div>
			<?php endif?>

			<?php if (!empty($db['coup_HowToUse'])): ?>
				<div class="row pt-3">
					<div class="col-12 bold">วิธีการจอง</div>
					<div class="col-12 light">
						<?php echo nl2br($db['coup_HowToUse']); ?>
					</div>
				</div>
			<?php endif?>

			<?php if (!empty($db['coup_Condition'])): ?>
				<div class="row pt-3">
					<div class="col-12 bold">ข้อตกลง/เงื่อนไข</div>
					<div class="col-12 light">
						<?php echo nl2br($db['coup_Condition']); ?>
					</div>
				</div>
			<?php endif?>

			<?php if (!empty($db['coup_Exception'])): ?>
				<div class="row pt-3">
					<div class="col-12 bold">ข้อยกเว้น</div>
					<div class=" col-12 light text-justify">
						<?php echo nl2br($db['coup_Exception']); ?>
					</div>
				</div>
			<?php endif?>

			<?php if (!empty($db['shop_cancellation_description'])): ?> <!-- start นโยบายการยกเลิก -->
				<div class="row pt-3">  <!-- test : promotion/112 : promotion/23-->
					<div class="col-12 bold">นโยบายการยกเลิก</div>
					<div class=" col-12 light text-justify">
						<?php echo nl2br($db['shop_cancellation_description']); ?>
					</div>
				</div>
			<?php endif?> <!-- end นโยบายการยกเลิก -->


			<div class="row pt-3"> <!-- ถาม/ตอบ test : promotion/157 : promotion/23 -->
				<?php function checkQuestionAnsIshave($db) {?>
					<?php for ($i = 1; $i <= 5; $i++) {?>
						<?php if (!empty($db['shop_q' . $i]) || !empty($db['shop_a' . $i])): ?>
							<?php return true;?>
						<?php endif?>
					<?php }?>
					<?php return false;?>
				<?php }?>

				<?php if (checkQuestionAnsIshave($db)): ?>
					<div class="col-12">ถาม/ตอบ</div>
				<?php endif?>
				<?php for ($i = 1; $i <= 5; $i++) {?>
					<?php if (!(empty($db['shop_q' . $i]) || empty($db['shop_a' . $i]))): ?>
						<div class=" col-12 text-justify">
							<div class="text-gray1 d-inline">Q : </div>
							<?php echo $db['shop_q' . $i] . ' ?'; ?>
						</div>
						<div class=" col-12 text-justify">
							<div class="text-gray1 d-inline">A : </div>
							<?php echo $db['shop_a' . $i]; ?>
						</div>
					<?php endif?>
				<?php }?>
			</div> <!-- end row ถาม/ตอบ -->

			<?php if (!empty($db['coup_Contact'])): ?> <!-- start ติดต่อ test : promotion/23 : promotion/126 -->
				<div class="row pt-3">
					<div class="col-12 bold">ติดต่อ</div>
					<div class="col-12 light">
						<div class="d-inline text-gray-dark">โทร</div>
						<div class="d-inline "><?php echo $db['coup_Contact']; ?></div>
					</div>
				</div>
			<?php endif?><!-- end  ติดต่อ-->

			<div class="row"> <!-- ที่มา test promotion/137@@ : promotion/26 -->
				<?php if ($db['website'] != '' || $db['facebook_url'] != '' || $db['line_id'] != '' || $db['instragram'] != '' || $db['tweeter'] != ''): ?>
					<div class="col-12 bold text-black pt-3">ที่มา</div>
				<?php endif?>
				<?php if ($db['website'] != ''): ?>
					<div class="col-12">
						<a href="<?php echo $db['website']; ?>">
							<i class="fas fa-globe mr-3 fa-fw"></i>
							<?php echo $db['website']; ?>
						</a>
					</div>
				<?php endif?>
				<?php if ($db['facebook_url'] != ''): ?>
					<div class="col-12">
						<a href="<?php echo $db['facebook_url']; ?>">
							<i class="fab fa-facebook-square mr-3 fa-fw"></i>
							<?php echo $db['facebook_url']; ?>
						</a>
					</div>
				<?php endif?>
				<?php if ($db['line_id'] != ''): ?>
					<div class="col-12">
						<a href="<?php echo $db['line_id']; ?>">
							<i class="fab fa-line mr-3 fa-fw"></i>
							<?php echo $db['line_id']; ?>
						</a>
					</div>
				<?php endif?>
				<?php if ($db['instragram'] != ''): ?>
					<div class="col-12">
						<a href="<?php echo $db['instragram']; ?>">
							<i class="fab fa-instagram mr-3 fa-fw"></i>
							<?php echo $db['instragram']; ?>
						</a>
					</div>
				<?php endif?>
				<?php if ($db['tweeter'] != ''): ?>
					<div class="col-12">
						<a href="<?php echo $db['tweeter']; ?>">
							<i class="fab fa-twitter mr-3 fa-fw"></i>
							<?php echo $db['tweeter']; ?>
						</a>
					</div>
				<?php endif?>
			</div><!-- end row ที่มา -->
		</div>
	</div>
</div>



<div class="hr_footer_height"></div>


<!-- test membercard/2 : -->
<?php //echo '<pre>'; ?>
<?php //print_r($db);?>
<?php //print_r($privileges);?>
<?php //print_r($coupon);?>
<?php //print_r($coupon_birthday);?>
<?php //print_r($activity);?>
<?php //print_r($reward);?>
<?php //echo '</pre>'; ?>

<!-- <div>This is member page</div> -->
<div class="lookup-head-img">
	<img class="img-fluid d-block ml-auto mr-auto shadow"
		src="upload/<?php echo $db['path_cover'] . $db['cover']; ?> " alt="First slide">
</div>

<div class="container h4 text-gray1">
	<div class="d-flex flex-row-reverse p-md-5 p-4 ">
		<button class="btn btn-primary ml-md-5 ml-3">แชร์</button>
		<button class="btn btn-primary">ชื่นชอบ</button>
	</div> <!-- end row -->
	<div class="row mb-5">
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
				<div class="col-md-12">
					<div class="h2 medium text-black">
						<?php echo $db['name']; ?>
					</div>
				</div>
				<div class="col-md-12">
					<div class="text-justify" ><?php echo $db['slogan']; ?></div>
				</div>
				<div class="col-md-10 h3"><!-- Rating Star -->

					<?php //$numStar = rand(0, 5);?>

              	</div>
			</div>
		</div>
	</div> <!-- end row -->
	<hr>

	<div class="row mt-5">
		<div class="col-10 ">
			<div class="row mb-5">
				<div class="col-lg-6 pb-3">
					<i class="fas fa-business-time mr-3 d-inline text-gray1"></i>
					<div class="d-inline meduim text-black">อายุสิทธิ์ :</div>
					<div class="d-inline "><?php echo $db['period_type']; ?></div>
				</div>
				<div class="col-lg-6 pb-3">
					<i class="fas fa-coins mr-3 d-inline text-gray1"></i>
					<div class="d-inline meduim text-black">ค่าสมัคร :</div>
					<div class="d-inline ">
						<?php if ($db['price_type'] == 'Free Card'): ?>
						<?php echo 'ฟรี'; ?>
						<?php endif?>
						<?php if ($db['price_type'] == 'Not Free Card'): ?>
						<?php echo $db['member_fee'] . ' ฿'; ?>
						<?php //echo number_format($db['member_fee']); ?> <!-- 15.00 is 15 -->
						<?php endif?>
					</div>
				</div>

				<div class="col-lg-6 pb-3">
					<i class="fas fa-infinity mr-3 d-inline text-gray1"></i>
					<div class="d-inline meduim text-black">สิทธิ์คงเหลือ :</div>
					<div class="d-inline ">
						<?php echo ($db['limit_member'] == '') ? 'ไม่จำกัด' : $db['limit_member']; ?>
					</div>
				</div>
				<!-- <div class="col-lg-6 pb-3">
					<i class="fas fa-store mr-3 d-inline text-gray1"></i>
					<div class="d-inline meduim text-black">เวลาทำการ :</div>
					<div class="d-inline ">-</div>
				</div> -->
				<?php if ($db['date_last_register'] != '0000-00-00'): ?>
					<div class="col-lg-6 pb-3">
						<!-- test on card_id 36 -->
						<i class="far fa-calendar-alt mr-3 d-inline text-gray1"></i>
						<div class="d-inline meduim text-black">สมัครได้ถึง :</div>
						<div class="d-inline ">
							<?php echo date("d/m/y", strtotime($db['date_last_register'])) ?>
						</div>
					</div>
				<?php endif?>
			</div> <!-- end row -->
		</div> <!-- end col-10 -->
		<div class="col-2">
			<a href="#">
				<div class="membercard_lookup_buttom bg-green text-center text-white p-2"
					style=" border-radius: 10px 10px 10px 10px;">
					<div class="medium">สมัคร</div>
				</div>
			</a>
		</div> <!-- end col-2 -->
	</div> <!-- end row -->

	<?php if ($db['description'] != ''): ?>
		<div class="row mt-5">
			<div class="col-10 ">
				<?php if ($db['description'] != ''): ?> <!-- test on id 2 -->
					<div class="row mb-5">
						<div class="col-12 bold text-black pb-3">รายละเอียด</div>
						<div class="col-12 light">
							<div>
								<?php echo nl2br($db['description']) ?>
							</div>
						</div>
					</div>
				<?php endif?>
				<!-- <div class="row mb-5">
					<div class="col-12 bold text-black">ข้อมูลบัตร</div>
					<div class="col-12 light">
						<div>ihkoaa skaldsalkdiu sajdasd8ajdasdjj sa9duasdjsl</div>
					</div>
				</div> -->
			</div>

		</div> <!-- end row -->
	<?php endif?>

	<?php if (count($privileges) > 0): ?>
		<div class="row mb-5">
			<div class="col-12">
				<div class="bold text-black">Privilege</div>
				<hr />
			</div>
			<div class="col-10">
				<div class="row">
					<?php foreach ($privileges as $key => $value): ?>
						<?php //echo '<pre>' ?>
						<?php //print_r($value)?>
						<?php //echo '</pre>' ?>
						<div class="product col-lg-4">
							<div class="card shadow mb-3 border border-secondary" style="max-width: 180rem;" >
					            <img class="card-img-top" '
				            		src="upload/<?php echo $value['priv_ImagePath'] . $value['priv_Image']; ?> " >
					            <div class="text-dark" '>
					          		<div class="card-title h5 bold m-1 setHeightCardHeadText">
					          			<?php echo $value['priv_Name'] ?>
					          		</div>
					              	<!-- <div class="row m-1">
						            	<small>
						            		(ยกเว้นเครื่องดื่มแอลลกอฮอลล์)
					            		</small>
				              		</div> -->
					            </div>
					            <!-- </a> -->
					        </div> <!-- emd card -->
					  	</div> <!-- end product -->
					<?php endforeach?>
				</div> <!-- end row -->
			</div> <!-- end col-10 -->
		</div> <!-- end row -->
	<?php endif?>

	<?php if (count($coupon) > 0): ?>
		<div class="row mb-5">
			<div class="col-12">
				<div class="bold text-black">Coupon</div>
				<hr />
			</div>
			<div class="col-10">
				<div class="row">
					<?php foreach ($coupon as $key => $value): ?>
						<?php //echo '<pre>' ?>
						<?php //print_r($value)?>
						<?php //echo '</pre>' ?>
						<div class="product col-lg-4">
							<div class="card shadow mb-3 border border-secondary" style="max-width: 180rem;" >
					            <img class="card-img-top" '
				            		src="upload/<?php echo $value['coup_ImagePath'] . $value['coup_Image']; ?> " >
					            <div class="text-dark" '>
					          		<div class="card-title h5 bold m-1 setHeightCardHeadText">
					          			<?php echo $value['coup_Name'] ?>
					          		</div>
					              	<!-- <div class="row m-1">
						            	<small>
						            		(ยกเว้นเครื่องดื่มแอลลกอฮอลล์)
					            		</small>
				              		</div> -->
					            </div>
					            <!-- </a> -->
					        </div> <!-- emd card -->
					  	</div> <!-- end product -->
					<?php endforeach?>
				</div> <!-- end row -->
			</div> <!-- end col-10 -->
		</div> <!-- end row -->
	<?php endif?>

	<?php if (count($coupon_birthday) > 0): ?>
		<div class="row mb-5">
			<div class="col-12">
				<div class="bold text-black">Birthday Coupon</div>
				<hr />
			</div>
			<div class="col-10">
				<div class="row">
					<?php foreach ($coupon_birthday as $key => $value): ?>
						<?php //echo '<pre>' ?>
						<?php //print_r($value)?>
						<?php //echo '</pre>' ?>
						<div class="product col-lg-4">
							<div class="card shadow mb-3 border border-secondary" style="max-width: 180rem;" >
					            <img class="card-img-top" '
				            		src="upload/<?php echo $value['coup_ImagePath'] . $value['coup_Image']; ?> " >
					            <div class="text-dark" '>
					          		<div class="card-title h5 bold m-1 setHeightCardHeadText">
					          			<?php echo $value['coup_Name'] ?>
					          		</div>
					              	<!-- <div class="row m-1">
						            	<small>
						            		(ยกเว้นเครื่องดื่มแอลลกอฮอลล์)
					            		</small>
				              		</div> -->
					            </div>
					            <!-- </a> -->
					        </div> <!-- emd card -->
					  	</div> <!-- end product -->
					<?php endforeach?>
				</div> <!-- end row -->
			</div> <!-- end col-10 -->
		</div> <!-- end row -->
	<?php endif?>

	<?php if (count($activity) > 0): ?> <!-- test on id 31 -->
		<div class="row mb-5">
			<div class="col-12">
				<div class="bold text-black">Activity</div>
				<hr />
			</div>
			<div class="col-10">
				<div class="row">
					<?php foreach ($activity as $key => $value): ?>
						<?php //echo '<pre>' ?>
						<?php //print_r($value)?>
						<?php //echo '</pre>' ?>
						<div class="product col-lg-4">
							<div class="card shadow mb-3 border border-secondary" style="max-width: 180rem;" >
					            <img class="card-img-top"
				            		src="upload/<?php echo $value['acti_ImagePath'] . $value['acti_Image']; ?> " >
					            <div class="text-dark">
					          		<div class="card-title h5 bold m-1 setHeightCardHeadText">
					          			<?php echo $value['acti_Name'] ?>
					          		</div>
					              	<!-- <div class="row m-1">
						            	<small>
						            		(ยกเว้นเครื่องดื่มแอลลกอฮอลล์)
					            		</small>
				              		</div> -->
					            </div>
					            <!-- </a> -->
					        </div> <!-- emd card -->
					  	</div> <!-- end product -->
					<?php endforeach?>
				</div> <!-- end row -->
			</div> <!-- end col-10 -->
		</div> <!-- end row -->
	<?php endif?>

	<div class="row">
		<div class="col-10 ">
			<?php if ($db['register_condition'] != ''): ?>
				<div class="row mb-5">
					<div class="col-12 bold text-black">วิธีสมัคร</div>
					<div class="col-12 light">
						<div><?php echo nl2br($db['register_condition']) ?></div>
					</div>
				</div>
			<?php endif?>
			<?php if ($db['how_to_use'] != ''): ?>
				<div class="row mb-5">
					<div class="col-12 bold text-black">วิธีใช้สิทธิ์</div>
					<div class="col-12 light">
						<div><?php echo nl2br($db['how_to_use']) ?></div>
					</div>
				</div>
			<?php endif?>
			<?php if ($db['how_to_activate'] != ''): ?>
				<div class="row mb-5">
					<div class="col-12 bold text-black">วิธีเปิดบัตร</div>
					<div class="col-12 light">
						<div><?php echo nl2br($db['how_to_activate']) ?></div>
					</div>
				</div>
			<?php endif?>
			<?php if ($db['collection_data'] != ''): ?> <!-- now mysql null -->
				<div class="row mb-5">
					<div class="col-12 bold text-black">การสะสมคะเเนน</div>
					<div class="col-12 light">
						<div><?php echo nl2br($db['collection_data']) ?></div>
					</div>
				</div>
			<?php endif?>
			<?php if ($db['re_new'] != ''): ?> <!-- test on id 92 -->
				<div class="row mb-5">
					<div class="col-12 bold text-black">วิธีต่ออายุบัตรสมาชิก</div>
					<div class="col-12 light">
						<div><?php echo nl2br($db['re_new']) ?></div>
					</div>
				</div>
			<?php endif?>
			<?php if ($db['upgrade_data'] != ''): ?> <!-- test on id 83 -->
				<div class="row mb-5">
					<div class="col-12 bold text-black">การอัพเกรดบัตรสมาชิก</div>
					<div class="col-12 light">
						<div><?php echo nl2br($db['upgrade_data']) ?></div>
					</div>
				</div>
			<?php endif?>
			<?php if ($db['how_to_activate'] != ''): ?>
				<div class="row mb-5">
					<div class="col-12 bold text-black">สาขาที่ร่วมรายการ</div>
					<div class="col-12 light">
						<div><?php echo nl2br($db['how_to_activate']) ?></div>
					</div>
				</div>
			<?php endif?>
			<?php if ($db['where_to_use'] != ''): ?>
				<div class="row mb-5">
					<div class="col-12 bold text-black">การเดินทาง</div>
					<div class="col-12 light">
						<div><?php echo nl2br($db['where_to_use']) ?></div>
					</div>
				</div>
			<?php endif?>
			<?php if ($db['exception'] != ''): ?>
				<div class="row mb-5">
					<div class="col-12 bold text-black">ข้อยกเว้น</div>
					<div class="col-12 light">
						<div><?php echo nl2br($db['exception']) ?></div>
					</div>
				</div>
			<?php endif?>
			<?php if ($db['shop_q1'] != ''): ?><!-- shop_q1-5 now mysql null all -->
				<div class="row mb-5">
					<div class="col-12 bold text-black">ถาม/ตอบ</div>
					<?php for ($i = 1; $i <= 5; $i++) {?>
						<?php if (!empty($db['shop_q' . $i])): ?>
									<div class=" col-12 text-justify">
										<div class="text-gray-dark h4 d-inline">Q : </div>
										<?php echo (empty($db['shop_q' . $i])) ? "null" : $db['shop_q' . $i] . ' ?'; ?>
									</div>
									<div class=" col-12 text-justify">
										<div class="text-gray-dark h4 d-inline">A : </div>
										<?php echo (empty($db['shop_a' . $i])) ? "null" : $db['shop_a' . $i]; ?>
									</div>
						<?php endif?>
					<?php }?>
				</div>
			<?php endif?>
			<?php if (count($reward) > 0): ?><!-- test on WHERE card_id = 12 -->
				<div class="row mb-5">
					<div class="col-12 bold text-black">รางวัล</div>
					<?php //echo '<pre>' ?>
					<?php //print_r($reward)?>
					<?php foreach ($reward as $key => $value): ?>
						<?php if ($value['rewa_ImagePath'] == '' || $value['rewa_Image'] == ''): ?>
							<?php $soreImage = 'images/400x400.png';?>
						<?php else: ?>
							<?php $soreImage = 'upload/' . $value['rewa_ImagePath'] . $value['rewa_Image'];?>
						<?php endif;?>
						<div class="product col-lg-4">
							<div class="card shadow mb-3 border border-secondary" style="max-width: 180rem;" >
					            <img class="card-img-top" '
				            		src="<?php echo $soreImage; ?> " >
					            <div class="text-dark" '>
					          		<div class="card-title h5 bold m-1 setHeightCardHeadText">
					          			<?php echo $value['rewa_Name'] ?>
					          		</div>
					              	<!-- <div class="row m-1">
						            	<small>
						            		(ยกเว้นเครื่องดื่มแอลลกอฮอลล์)
					            		</small>
				              		</div> -->
					            </div>
					        </div> <!-- emd card -->
					  	</div> <!-- end product -->
					<?php endforeach?>
				</div>
			<?php endif?>

		</div> <!-- end col-10 -->
	</div> <!-- end row -->

	<div class="row pt-3 mb-5">

		<?php if ($db['website'] != '' || $db['facebook_url'] != '' || $db['line_id'] != '' || $db['instragram'] != '' || $db['tweeter'] != ''): ?>
				<div class="col-12 bold text-black ">ติดต่อ</div>
		<?php endif?>
		<!-- test membercard/27 -->
		<?php if ($db['website'] != ''): ?>
			<div class="col-12">
				<a href="<?php echo $db['website']; ?>">
					<i class="fas fa-globe mr-3"></i>
					<?php echo $db['website']; ?>
				</a>
			</div>
		<?php endif?>
		<?php if ($db['facebook_url'] != ''): ?>
			<div class="col-12">
				<a href="<?php echo $db['facebook_url']; ?>">
					<i class="fab fa-facebook-square mr-3"></i>
					<?php echo $db['facebook_url']; ?>
				</a>
			</div>
		<?php endif?>
		<?php if ($db['line_id'] != ''): ?>
			<div class="col-12">
				<a href="<?php echo $db['line_id']; ?>">
					<i class="fab fa-line mr-3"></i>
					<?php echo $db['line_id']; ?>
				</a>
			</div>
		<?php endif?>
		<?php if ($db['instragram'] != ''): ?>
			<div class="col-12">
				<a href="<?php echo $db['instragram']; ?>">
					<i class="fab fa-instagram mr-3"></i>
					<?php echo $db['instragram']; ?>
				</a>
			</div>
		<?php endif?>
		<?php if ($db['tweeter'] != ''): ?>
			<div class="col-12">
				<a href="<?php echo $db['tweeter']; ?>">
					<i class="fab fa-twitter mr-3 "></i>
					<?php echo $db['tweeter']; ?>
				</a>
			</div>
		<?php endif?>
	</div>

</div> <!-- end conditener -->

<div class="hr_footer_height"></div>


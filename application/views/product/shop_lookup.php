<!-- test shop/36 : -->

<?php //echo '<pre>' ?>
<?php //print_r($db)?>
<?php //echo '</pre>' ?>

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

					<!-- heard is hide wait data -->
					<!-- <div class="col-md-2 h3">
						<i class="fas fa-heart text-danger" data-fa-transform="up-1"></i> 2.0k
					</div> -->

					<div class="col-md-10 h3"><!-- Rating Star -->
						<!-- shop_lookup ยังไม่มี DB rating star -->
						<!-- <?php //$numStar = rand(0, 5);?>
						<?php $numStar = 3.5;?>
						<?php $numStarCalculator = 3.5;?>
						<?php for ($i = 1; $i <= 5; $i++) {?>

							<?php if ($numStarCalculator >= 1): ?>
									<i class="fa fa-star text-warning " data-fa-transform="up-2"></i>
		        					<?php $numStarCalculator--;?>
							<?php else: ?>
			        			<?php if ($numStarCalculator >= 0.5): ?>
			        				<i class="fas fa-star-half-alt text-warning" data-fa-transform="up-2" ></i>
	        						<?php $numStarCalculator -= 0.5;?>
			        			<?php else: ?>
	        						<i class="far fa-star text-warning " data-fa-transform="up-2"></i>
			        			<?php endif;?>
		        			<?php endif;?>
	        			<?php }?>
	        			<?php echo $numStar; ?> -->
	              	</div>
				</div>
			</div>
		</div>
	<hr class="pb-3">
</div>

<div class="container pb-5">
	<div class="row">
		<div class="col-md-8">
			<?php //row 1 ?>
			<div class="row p-3" style="line-height: 2;">
				<div class="col-12 col-md-6 text-justify">
					<div class="row">
						<i class="fas fa-stopwatch mr-4" data-fa-transform="down-2"></i>
						<div class= medium">ระยะเวลากิจกรรม: </div>
						<div class= light ml-2"> <?php echo $db['coup_ActivityDuration']; ?></div>
					</div>
					<div class="row">
						<i class="fas fa-business-time mr-3" data-fa-transform="down-2"></i>
						<div class= medium">ระยะเวลาโปร: </div>
						<div class= light ml-5">
							<?php if ($db['coup_StartDate'] == '0000-00-00'): ?>
								<?php echo "00-00-0000 - " . setDate($db['coup_EndDate']) . '<br>' ?>
							<?php else: ?>
								<?php echo setDate($db['coup_StartDate']) . ' - ' . setDate($db['coup_EndDate']) . '<br>' ?>
							<?php endif?>
							<?php echo $db['coup_StartTime'] . ' - ' . $db['coup_EndTime'] ?>
						</div>
					</div>
					<?php if (!empty(trim($db['coup_Participation']))): ?>
						<div class="row">
							<i class="fas fa-infinity mr-3" data-fa-transform="down-2"></i>
							<div class= medium">สิทธิ์คงเหลือ: </div>
							<div class= light ml-2">
								<?php echo $db['coup_Participation']; ?>
							</div>
						</div>
					<?php endif?>
					<div class="row">
						<i class="fas fa-store mr-3" data-fa-transform="down-2"></i>
						<div class= medium">วันทำการ: </div>
						<div class= light ml-2"><?php echo $db['open_brief']; ?></div>
					</div>
				</div>
				<div class="col-12 col-md-6 text-justify">
					<div class="row">
						<i class="fas fa-users mr-3"></i>
						<div class= medium">ผู้เข้าร่วมขั้นต่ำ: </div>
						<div class= light ml-2">
							<?php if (empty(trim($db['coup_Participation']))): ?>
								<?php echo "ไม่กำหนด" ?>
							<?php else: ?>
								<?php echo $db['coup_Participation'] ?>
							<?php endif?>
						</div>
					</div>
					<div class="row">
						<i class="far fa-calendar-alt mr-3"></i>
						<div class= medium">ใช้สิทธิ์ได้ถึง: </div>
						<div class= light ml-2">
							<?php $mh = $db['coup_Method'];?>
							<?php if ($mh == 'No'): ?>
								<?php echo "ไม่กำหนด"; ?>
							<?php elseif ($mh == 'Fix'): ?>
								<?php echo setDate($db['coup_EndDateUse']) ?>
							<?php elseif ($mh == 'Month'): ?>
								<?php echo 'Month'; ?>
							<?php elseif ($mh == 'Year'): ?>
								<?php echo 'Year'; ?>
							<?php endif?>
						</div>
					</div>
					<div class="row">
						<i class="far fas fa-eraser mr-3"></i>
						<div>วิธีใช้สิทธิ์: </div>
						<div class= light ml-2">
							<?php echo $db['shop_howtouse_brief']; ?>
						</div>
					</div>
					<div class="row">
						<i class="fas fa-user-tag mr-3"></i>
						<div>จำกัดสิทธิ์: </div>
						<div class= light ml-2">ไม่จำกัด</div>
					</div>
				</div>
			</div>
			<?php //end row1 ?>
			<?php //row 2 ?>
			<!-- start for after version -->
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
			<!-- end for after version -->


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
				<div class="row pt-3">  <!-- test : shop/112 : shop/23-->
					<div class="col-12 bold">นโยบายการยกเลิก</div>
					<div class=" col-12 light text-justify">
						<?php echo nl2br($db['shop_cancellation_description']); ?>
					</div>
				</div>
			<?php endif?> <!-- end นโยบายการยกเลิก -->


			<!-- start for after version -->
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
			<!-- end for after version -->

			<div class="row pt-3" style="line-height: 2;"> <!-- ถาม/ตอบ test : shop/157 : shop/23 -->
				<?php function checkQuestionAnsIshave($db) {?>
					<?php for ($i = 1; $i <= 5; $i++) {?>
						<?php if (!empty($db['shop_q' . $i]) || !empty($db['shop_a' . $i])): ?>
							<?php return true;?>
						<?php endif?>
					<?php }?>
					<?php return false;?>
				<?php }?>

				<?php if (checkQuestionAnsIshave($db)): ?>
					<div class="col-12 bold text-black">ถาม/ตอบ</div>
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

			<?php if (!empty($db['coup_Contact'])): ?> <!-- start ติดต่อ test : shop/23 : shop/126 -->
				<div class="row pt-3">
					<div class="col-12 bold">ติดต่อ</div>
					<div class="col-12 light">
						<div class="d-inline text-gray-dark">โทร</div>
						<div class="d-inline "><?php echo $db['coup_Contact']; ?></div>
					</div>
				</div>
			<?php endif?><!-- end  ติดต่อ-->

			<div class="row"> <!-- ที่มา test shop/36 : shop/26 -->
				<?php if ($db['website'] != '' || $db['facebook_url'] != '' || $db['line_id'] != '' || $db['instragram'] != '' || $db['tweeter'] != ''): ?>
					<div class="col-12 bold text-black pt-3">ที่มา</div>
				<?php endif?>
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
			</div><!-- end row ที่มา -->

			<?php //end row2 ?>
		</div>
		<?php //tab controller ,select time, select date, selectzone, show price, addtocart, buynow ?>
		<div class="col-md-4 " ng-controller="shop_lookupController" id="focus_buy"
			ng-init="
				numForBuy = 1;
				productPrice = <?php echo $db['coup_Price'] ?>;
				productId = <?php echo $db['coup_CouponID'] ?>;
				">
			<div class="row mb-3">
				<div id="my-calendar" class="blue material-theme ml-auto mr-auto" ></div>
				<!-- Outputs -->
				<!-- Day click : <br>
				<input class="form-control" id="my-input-a"><br>
				Month change : <br>
				<input class="form-control" id="my-input-b"><br> -->
			</div>
			<div class="row">   <!-- select num for buy -->
				<div class="col-12 h3 medium text-black">เลือก</div>
				<ul class="pagination ml-auto mr-auto">
				    <li class="page-item">
				    	<div class="page-link bg-green text-white" ng-click="numForBuy = numForBuy + 1 ">
				    		<i class="fas fa-angle-up"></i>
				    	</div>
					</li>
					<li class="page-item " >
				    	<div class="page-link bg-white" >{{numForBuy}}</div>
					</li>
					<li class="page-item " ng-class="{disabled:numForBuy<2}" >
				    	<div class="page-link bg-green text-white" ng-click="numForBuy = numForBuy - 1">
				    		<i class="fas fa-angle-down"></i>
				    	</div>
					</li>
			    </ul>
			</div>
			<div class="row">
				<div class="col-12 h3 medium text-black">สรุปยอด</div>
				<div class="col-6 text-gray1 text-left pl-4">ราคาต่อคน</div>
				<div class="col-6 text-gray1 text-right pr-4">{{productPrice}} ฿</div>
				<hr class="w-100 h3 bg-green ml-sm-5">
				<div class="col-6 text-gray1 text-left pl-4">จำนวน</div>
				<div class="col-6 text-gray1 text-right pr-4">{{numForBuy}}</div>
				<hr class="w-100 h3 bg-green ml-sm-5">
				<div class="col-6 text-gray1 text-left pl-4">ราคารวม</div>
				<div class="col-6 text-gray1 text-right pr-4">{{productPrice*numForBuy}} ฿</div>
				<div class="col-12 text-center mt-5 pb-5">
					<button class="btn btn-primary">ซื้อเลย</button>
				</div>
			</div>

			<div class="text-right d-lg-none d-md-none" ng-if="!btnBuy"
				style="
					position: fixed;
					top: 90%;
					right: 0%;
					border-top-left-radius: 1rem;
					border-bottom-left-radius: 1rem;
					background-color: var(--color-green);
					cursor: pointer;
				"
			>
				<div class="h4 medium p-3 text-white"
					ng-click="scrollTo()"
					style="
						width: auto;
						margin-bottom: 0px;
					"
				>
					ซื้อเลย
				</div>
			</div>
		</div>
	</div>
</div>

<div class="hr_footer_height"></div>
<?php //view product user select calender ?>
<script src="<?php echo base_url('assets/plugins/jsCalendar/jsCalendar.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/jsCalendar/jsCalendar.lang.uk.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/jsCalendar/jsCalendar_custom.js') ?>"></script>

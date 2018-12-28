<!-- test shop/36 : -->
<?php //echo '<pre>' ?>
<?php //print_r($db)?>
<?php //echo '</pre>' ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/plugins/google-map/map.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/plugins/google-map/map.css'); ?>">

<?php
function setDate($string) {return date("d-m-Y", strtotime($string));}
function setTime($string) {return date("H:i", strtotime($string));}
?>
<?php //echo $db['coup_ImagePath'] . $db['coup_Image'] ?>
<div ng-controller="shop_lookupController" ng-init="init()">
	<div class="container-fluid-my">
		<div class="lookup-head-img pt-md-5 pt-0">
			<img class="img-fluid d-block ml-auto mr-auto shadow border-my"
			src="upload/<?php echo $db['coup_ImagePath'] . $db['coup_Image'] ?>" alt="First slide">
		</div>
	</div>

	<div class="container">

		<div class="d-flex flex-row-reverse p-md-5 p-4 ">
			<button class="btn btn-lg btn-primary bold ml-md-5 ml-3">แชร์</button>
			<button class="btn btn-lg btn-primary bold active">ชื่นชอบ</button>
		</div>
			<div class="row">
				<div class="col-lg-2">
					<a href="<?php echo base_url(); ?>brand/<?php echo $db['brand_id'] ?>">
						<img class="img-thumbnail shadow shadowHover imgBrandSize ml-auto
						d-none d-md-block d-lg-block d-xl-block"
						src="upload/<?php echo $db['path_logo'] . $db['logo_image'] ?>" alt="First slide">
						<img class="img-thumbnail w-50 shadow rounded-circle ml-auto mr-auto mb-3
							d-block d-md-none d-lg-none d-xl-none"
							src="upload/<?php echo $db['path_logo'] . $db['logo_image'] ?>" alt="First slide">
					</a>
				</div>
				<div class="col-lg-10 w-100">
					<div class="col-12">
						<div class="col-12"><div class="h2 medium"><?php echo $db['coup_Name']; ?></div></div>
						<div class="col-12">
							<div class="text-justify" ><?php echo nl2br($db['signature_info']); ?></div>
						</div>

						<div class="col-md-10 h3"><!-- Rating Star -->
							<!-- shop_lookup ยังไม่มี DB rating star -->
							<!-- <?php //$numStar = rand(0, 5);?>
							<?php $numStar = 3.5;?>
							<?php $numStarCalculator = 3.5;?>
							<?php for ($i = 1; $i <= 5; $i++) {?>

								<?php if ($numStarCalculator >= 1): ?>
										<i class="fa fa-star text-warning " data-fa-transform="up-2 fa-fw"></i>
			        					<?php $numStarCalculator--;?>
								<?php else: ?>
				        			<?php if ($numStarCalculator >= 0.5): ?>
				        				<i class="fas fa-star-half-alt text-warning" data-fa-transform="up-2" ></i>
		        						<?php $numStarCalculator -= 0.5;?>
				        			<?php else: ?>
		        						<i class="far fa-star text-warning " data-fa-transform="up-2 fa-fw"></i>
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
			<div class="col-lg-8">
				<?php //row 1 ?>
					<div class="row p-3" style="line-height: 2;">
						<div class="col-12 col-md-6 d-md-flex">
							<div class="">
								<i class="fas fa-bell fa-fw d-inline"></i>
								<div class="medium d-inline">ระยะเวลากิจกรรม: </div>
							</div>
							<div class=" light ml-5 ml-md-2">
								<?php if ($db['coup_ActivityDuration'] == 'Not Specific'): ?>
									<?php echo 'ไม่กำหนดวัน'; ?>
								<?php else: ?>
									<?php echo $db['coup_ActivityDuration']; ?>
								<?php endif?>
						 	</div>
						</div>
						<div class="col-12 col-md-6 d-md-flex ">
							<div class="">
								<i class="fas fa-users fa-fw d-inline"></i>
								<div class="medium d-inline">ผู้เข้าร่วมขั้นต่ำ: </div>
							</div>
							<div class="light ml-5 ml-md-2">
								<?php if (empty($db['coup_Participation'])): ?>
									<?php echo "ไม่กำหนด" ?>
								<?php else: ?>
									<?php echo $db['coup_Participation'] ?>
								<?php endif?>
						 	</div>
						</div>
						<div class="col-12 col-md-6 d-md-flex ">
							<div class="">
								<i class="fas fa-business-time fa-fw d-inline"></i>
								<div class="medium d-inline">ระยะเวลาโปร: </div>
							</div>
							<div class="light ml-5 ml-md-2">
								<?php if ($db['coup_StartDate'] == '0000-00-00'): ?>
									<?php echo "00-00-0000 - " . setDate($db['coup_EndDate']) . '<br>' ?>
								<?php else: ?>
									<?php echo setDate($db['coup_StartDate']) . ' - ' . setDate($db['coup_EndDate']) . '<br>' ?>
								<?php endif?>

								<?php if ($db['coup_StartTime'] == '00:00:00' || $db['coup_EndTime'] == '00:00:00'): ?>
									<?php echo 'ไม่กำหนดเวลา' . '<br>' ?>
								<?php else: ?>
									<?php echo setTime($db['coup_StartTime']) . ' - ' . setTime($db['coup_EndTime']) ?>
								<?php endif?>
						 	</div>
						</div>
						<div class="col-12 col-md-6 d-md-flex ">
							<div class="">
								<i class="fas fa-calendar-alt fa-fw d-inline"></i>
								<div class="medium d-inline">ใช้สิทธิ์ได้ถึง: </div>
							</div>
							<div class="light ml-5 ml-md-2">
								<?php $mh = $db['coup_Method'];?>
								<?php if ($mh == 'No'): ?>
									<?php echo "ไม่กำหนด"; ?>
								<?php elseif ($mh == 'Fix' && ($db['coup_EndDateUse'] == '0000-00-00')): ?>
									<?php echo "ไม่มีข้อมูล" ?>
								<?php elseif ($mh == 'Fix' && ($db['coup_EndDateUse'] != '0000-00-00')): ?>
									<?php echo setDate($db['coup_EndDateUse']) ?>
								<?php endif?>
						 	</div>
						</div>
						<div class="col-12 col-md-6 d-md-flex ">
							<div class="">
								<i class="fas fa-calendar-check fa-fw d-inline"></i>
								<div class="medium d-inline">วิธีการจอง: </div>
							</div>
							<div class="light ml-5 ml-md-2 text-child-limit">
								<?php if (empty($db['shop_reservation_brief'])): ?>
									<?php echo "ยังไม่กำหนด" ?>
								<?php else: ?>
									<?php echo $db["shop_reservation_brief"]; ?>
								<?php endif?>
						 	</div>
						</div>
						<div class="col-12 col-md-6 d-md-flex ">
							<div class="">
								<i class="fas fa-eraser fa-fw d-inline"></i>
								<div class="medium d-inline">วิธีใช้สิทธิ์: </div>
							</div>
							<div class="light ml-5 ml-md-2 text-child-limit">
								<?php if (empty($db['shop_howtouse_brief'])): ?>
									<?php echo "ยังไม่กำหนด" ?>
								<?php else: ?>
									<?php echo $db["shop_howtouse_brief"]; ?>
								<?php endif?>
						 	</div>
						</div>
						<div class="col-12 col-md-6 d-md-flex "> <!-- test shop/36 -->
							<div class="">
								<i class="fas fa-infinity fa-fw d-inline"></i>
								<div class="medium d-inline">สิทธิ์คงเหลือ: </div>
							</div>
							<div class="light ml-5 ml-md-2">
								<?php if (empty($db['coup_Repetition'])): ?> <!-- if null -->
									<?php echo 'ไม่จำกัด'; ?>
								<?php else: ?>
									<?php echo $db['coup_Repetition']; ?>
								<?php endif?>
						 	</div>
						</div>
						<div class="col-12 col-md-6 d-md-flex ">
							<div class="">
								<i class="fas fa-user-tag fa-fw d-inline"></i>
								<div class="medium d-inline">จำกัดสิทธิ์: </div>
							</div>
							<div class="light ml-5 ml-md-2">
								<?php if (empty($db['coup_RepetitionMember'])): ?>
									<?php echo "ไม่จำกัด" ?>
								<?php else: ?>
									<?php echo $db["coup_QtyMember"] . ' ครั้ง' ?>
									<?php if ($db["coup_QtyPerMember"] == 'Daily'): ?>
										<?php echo 'ต่อ วัน' ?>
									<?php elseif ($db["coup_QtyPerMember"] == 'Weekly'): ?>
										<?php echo 'ต่อ สัปดาห์ <br> ' . $db["coup_QtyPerMemberData"] ?>
									<?php elseif ($db["coup_QtyPerMember"] == 'Monthly'): ?>
										<?php echo 'ต่อเดือน <br>' . $db["coup_QtyPerMemberData"] ?>
									<?php endif?>
								<?php endif?>
						 	</div>
						</div>
						<div class="col-12 col-md-6 d-md-flex ">
							<div class="">
								<i class="fas fa-store fa-fw d-inline"></i>
								<div class="medium d-inline">วันทำการ: </div>
							</div>
							<div class="light ml-5 ml-md-2">
								<?php if (empty($db['open_brief'])): ?>
									<?php echo 'ยังไม่ได้กำหนด'; ?>
								<?php else: ?>
									<?php echo nl2br($db['open_brief']); ?>
								<?php endif?>
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
						<div class="row pt-5">
							<div class="col-12 bold">เวลาทำการ</div>
							<div class=" col-12 light text-justify">
								<?php echo nl2br($db['open_description']); ?>
							</div>
						</div>
					<?php endif?>

					<?php if (!empty($db['coup_HowToUse'])): ?>
						<div class="row pt-5">
							<div class="col-12 bold">วิธีการจอง</div>
							<div class="col-12 light">
								<?php echo nl2br($db['coup_HowToUse']); ?>
							</div>
						</div>
					<?php endif?>

					<?php if (!empty($db['coup_Condition'])): ?>
						<div class="row pt-5">
							<div class="col-12 bold">ข้อตกลง/เงื่อนไข</div>
							<div class="col-12 light">
								<?php echo nl2br($db['coup_Condition']); ?>
							</div>
						</div>
					<?php endif?>

					<?php if (!empty($db['coup_Exception'])): ?>
						<div class="row pt-5">
							<div class="col-12 bold">ข้อยกเว้น</div>
							<div class=" col-12 light text-justify">
								<?php echo nl2br($db['coup_Exception']); ?>
							</div>
						</div>
					<?php endif?>

					<?php if (!empty($db['shop_cancellation_description'])): ?> <!-- start นโยบายการยกเลิก -->
						<div class="row pt-5">  <!-- test : shop/112 : shop/23-->
							<div class="col-12 bold">นโยบายการยกเลิก</div>
							<div class=" col-12 light text-justify">
								<?php echo nl2br($db['shop_cancellation_description']); ?>
							</div>
						</div>
					<?php endif?> <!-- end นโยบายการยกเลิก -->

					<?php if (1): ?> <!-- start ที่ตั้ง -->
						<div class="row pt-5">
							<div class="col-12 bold">ที่ตั้ง</div>
							<div class="col-12 light text-justify">
								<?php echo $db['address_no'] . ' '; ?>
								<?php echo $db['moo'] . ' ' . $db['junction'] . ' ' . $db['soi'] . ' '; ?>
								<?php echo $db['sub_district'] . ' ' . $db['district'] . ' ' . $db['postcode'] . ' '; ?>
							</div>
						</div>

						<div class="row pt-4">  <!-- test : shop/112 : shop/23-->
							<div class="d-none">
								<input type="text" name="lat" value="<?php echo $db['map_latitude']; ?>" disabled>
								<input type="text" name="lng" value="<?php echo $db['map_longitude']; ?>" disabled>
							</div>

							<div class="col-12">
								<div class="boxMap mx-auto shadow">
								  <div id="map"></div>
								</div>
							</div>
						</div>
					<?php endif?> <!-- end ที่ตั้ง -->

					<!-- <div class="row pt-3">
						<div class="col-12">การเดินทาง</div>
						<div class="row justify-content-center">
							<img class="col-12" src="http://placehold.it/600x400">
							<img class="col-12" src="http://placehold.it/600x400">
						</div>
					</div> -->

					<div class="row pt-4" style="line-height: 2;"> <!-- ถาม/ตอบ test : shop/157 : shop/23 -->
						<?php function checkQuestionAnsIshave($db) {?>
							<?php for ($i = 1; $i <= 5; $i++) {?>
								<?php if (!empty($db['shop_q' . $i]) || !empty($db['shop_a' . $i])): ?>
									<?php return true;?>
								<?php endif?>
							<?php }?>
							<?php return false;?>
						<?php }?>

						<?php if (checkQuestionAnsIshave($db)): ?>
							<div class="col-12 bold text-black pt-5">ถาม/ตอบ</div>
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
						<div class="row pt-5">
							<div class="col-12 bold">ติดต่อ</div>
							<div class="col-12 light">
								<!-- <div class="d-inline text-gray-dark">โทร</div> -->
								<div class="d-inline "><?php echo $db['coup_Contact']; ?></div>
							</div>
						</div>
					<?php endif?><!-- end  ติดต่อ-->

					<?php if ($db['website'] != '' || $db['facebook_url'] != '' || $db['line_id'] != '' || $db['instragram'] != '' || $db['tweeter'] != ''): ?>
						<div class="row pt-5"> <!-- ที่มา test shop/36 : shop/26 -->
							<div class="col-12 bold text-black pt-3">ที่มา</div>

							<?php if ($db['website'] != ''): ?>
								<div class="col-12">
									<a href="<?php echo $db['website']; ?>">
										<div class="d-md-inline">
											<i class="fas fa-globe mr-3 fa-fw"></i>
										</div>
										<div class="d-md-inline">
											<?php echo $db['website']; ?>
										</div>
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
										<i class="fab fa-twitter mr-3  fa-fw"></i>
										<?php echo $db['tweeter']; ?>
									</a>
								</div>
							<?php endif?>

						</div><!-- end row ที่มา -->
					<?php endif?> <!-- end ที่มา -->


				<?php //end row2 ?>
			</div>

			<?php //tab controller ,select time, select date, selectzone, show price, addtocart, buynow ?>
			<div class="col-lg-4 " id="focus_buy"
				ng-init="numForBuy = 1;">
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
						<li class="page-item " ng-class="{disabled:numForBuy<2}" >
					    	<div class="page-link bg-green text-white" ng-click="numForBuy = numForBuy - 1">
					    		<i class="fas fa-angle-down fa-fw"></i>
					    	</div>
						</li>
						<li class="page-item " >
					    	<div class="page-link bg-white" >{{numForBuy}}</div>
						</li>
					    <li class="page-item">
					    	<div class="page-link bg-green text-white" ng-click="numForBuy = numForBuy + 1 ">
					    		<i class="fas fa-angle-up fa-fw"></i>
					    	</div>
						</li>
				    </ul>
				</div>
				<div class="row">
					<div class="col-12 h3 medium text-black">สรุปยอด</div>
					<div class="col-6 text-gray1 text-left pl-4">ราคาต่อคน</div>
					<div class="col-6 text-gray1 text-right pr-4">{{user.coup_Price*1}} ฿</div>
					<hr class="w-100 h3 bg-green ml-sm-5 ml-lg-0">
					<div class="col-6 text-gray1 text-left pl-4">จำนวน</div>
					<div class="col-6 text-gray1 text-right pr-4">{{numForBuy}}</div>
					<hr class="w-100 h3 bg-green ml-sm-5 ml-lg-0">
					<div class="col-6 text-gray1 text-left pl-4">ราคารวม</div>
					<div class="col-6 text-gray1 text-right pr-4">{{user.coup_Price*numForBuy}} ฿</div>
					<div class="col-12 text-center mt-5 pb-5">
						<button class="btn btn-primary" ng-click="userActionBuy(user,numForBuy)">ซื้อเลย</button>
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
			</div> <!-- end user select controller -->
		</div>
	</div>

	<div class="container pb-5">
		<div class="row pt-5">
			<div class="col-12 bold">สิ่งที่คุณอาจสนใจ</div>
			<div class="product col-md-4 productMargin" ng-repeat='product in productRecomment | limitTo:numLimitProductNow' >
				<div class="card shadow mb-3 mt-3 " style="max-width: 180rem;" >
					<img ng-if="product.logo_image != null"
						ng-click='lookup("barnd",product.coup_CouponID,product.coup_Type)'
						class="rounded-circle shadow-sm img-responsive logo-brand border border-secondary bg-light" ng-src="upload/{{product.path_logo+product.logo_image}}">
		            <img class="card-img-top" ng-click='lookup("coup",product.coup_CouponID,product.coup_Type)'
	            		ng-src="upload/{{product.coup_ImagePath+product.coup_Image}}" >
		            <div class="text-dark" ng-click='lookup("coup",product.coup_CouponID,product.coup_Type)'>
				            	<!-- {{product.coup_CouponID}} -->
		            	<div class="d-none">id>{{product.coup_CouponID}}|id-b>{{product.category_brand}}</div>
		          		<div class="card-title h5 bold m-1 setHeightCardHeadText ">
		          			{{product.coup_Name}}
		          		</div>
			            <div class="row m-1">
		              		<div class="text-right col-12 ">
		              			<div class="d-inline h6 regular pr-2 text-gray1">
		              					<!-- {{product.coup_Cost}} -->
		              					<!-- {{product.coup_Price}} -->
		              				<small ng-if="parseInt(product.coup_Cost)>0">
		              					ลด
		              					{{  Math.round(((product.coup_Cost - product.coup_Price)/product.coup_Cost)*100)  }}
		              					%
		              				</small>
		              				<!-- <small ng-if="parseInt(product.coup_Cost)==0">ฟรี</small> -->
	              				</div>
		              			<div class="d-inline h4 medium text-danger"
		              				ng-if="parseInt(product.coup_Cost)>0">{{ product.coup_Price|number:0}}฿
		              			</div>
		              			<div class="d-inline h4 medium text-danger"
		              				ng-if="parseInt(product.coup_Cost)==0"> ฟรี!
		              			</div>
		              		</div>
              			</div>
			            <div class="row m-1 mt-2" style="font-size: 0.3rem;">
		              		<div class="col-12 text-right text-gray1">
		              			<div class="d-inline" ng-if="product.coup_Type == 'Buy'">ขายเเล้ว</div>
		              			<div class="d-inline" ng-if="product.coup_Type == 'Member'">สมัครเเล้ว</div>
		              			<div class="d-inline" ng-if="product.coup_Type == 'Use'">ใช้เเล้ว</div>
		              			<div class="d-inline">
		              				{{product.coup_numUse}}
		              				<!-- {{ (coupon_trans | filter : { coup_CouponID: product.coup_CouponID } : true).length }} -->
		              			</div>
		              			<!-- <div >{{ rating(ratingDB,product.coup_CouponID,product.coup_Type)}}</div> -->
		              		</div>
		              		<!-- rating(ratingDB,117,'Use') -->
		              	</div>
		            </div>
	        	</div>
	  		</div>
	  		<div class="col-12">
	  			<div class="row p-3 text-right" ng-if="numLimitProductNow < productRecomment.length">
					<div class="box-additional ml-auto px-3 py-1">
						<div class="cursor-pointer h4 medium w-100 m-0" ng-click="additional()">เพิ่มเติม</div>
					</div>

					<!-- {{ numLimitProduct }} -->
				</div>
	  		</div>
		</div><!-- end สิ่งที่คุณอาจสนใจ -->
	</div>

</div> <!-- end shopLookupController -->


<script src="<?php echo base_url('assets/plugins/google-map/google_map.js') ?>"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCgOYYNGdJV_5X_VG1PRgFChTnekgc-6To&language=TH&region=TH&callback=initMap" async defer></script>

<div class="hr_footer_height"></div>
<?php //view product user select calender ?>
<script src="<?php echo base_url('assets/plugins/jsCalendar/jsCalendar.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/jsCalendar/jsCalendar.lang.uk.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/jsCalendar/jsCalendar_custom.js') ?>"></script>

<?php //google map API?>
<script src="<?php echo base_url('assets/plugins/google-map/google_map.js') ?>"></script>
<script type="text/javascript"
		src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCgOYYNGdJV_5X_VG1PRgFChTnekgc-6To&language=TH&region=TH&callback=initMap" async defer>
</script>


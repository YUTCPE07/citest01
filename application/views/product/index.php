

<div ng-controller='productController' class="pb-5 ">
<!-- <div ng-show="expression">LONDING...</div> layoutProductOff-->
<div class="container layoutProductOff pt-3" ng-class="{layoutProductOn:isReadyShow}">
<!-- <div class="container">  -->
	<?php //select menu top right ?>
	<div class="row mt-5 justify-content-end">
		<div class="btn-group">
		  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		    ทั้งหมด
		  </button>
		  <div class="dropdown-menu">
		    <a class="dropdown-item" href="#">แนะนำ</a>
		    <a class="dropdown-item" href="#">ล่าสุด</a>
		    <a class="dropdown-item" href="#">ยอดนิยม</a>
		    <a class="dropdown-item" href="#">สถานที่ใกล้เคียง</a>
		  </div>
		</div>
	</div>

	<div class="row">
		<?php //layout 1 ?>
		<div class="col-lg-3 mt-3 navbar-expand-lg menu">
			<?php //menu ?>
			<button class="navbar-toggler text-uppercase bg-primary text-white rounded" type="button"
				data-toggle="collapse" data-target="#menuResponetive" aria-controls="menuResponetive"
				aria-expanded="false"><i class="fas fa-filter"></i>คัดกรอง
		    </button>
	      	<h5 class="m-2">คัดกรอง</h5>
			<div class="row">
				<div class="shadow m-2 p-2 w-100 mb-5 bg-white rounded border border-secondary
					collapse navbar-collapse" id="menuResponetive" >
					<div class="row mb-3 ">
						<div class="col-7 "><strong>หมวดหมู่</strong></div>
						<div class="col-5 text-right">[ทั้งหมด]</div>
					</div>
					<?php //ng-repeat="(key, value) in players | groupBy: 'team'" ?>

					<div class="row" ng-repeat="(key,value) in products|groupBy:'category_brand'">
						<div class="col-8">
							<div class="form-check">
							  <input class="form-check-input" type="checkbox"
							  		ng-model="confirmed" ng-change="checkBoxProductType(this)"
							  		>

							  <label class="form-check-label">{{ catrogy_barnd[key-1].name }}</label>
							</div>
						</div>
						<div class="col-4 text-right">
							<p>{{ value.length }}</p>
							<p>{{ checkbox[key] }}</p>
						</div>
					</div>
				</div>
			</div> <?php //end row  ?>

			<!-- <div class="row">
				<div class="shadow p-3 w-100 mb-5 bg-white rounded border border-secondary" >
					<div class="row mb-3 ">
						<div class="col-12"><strong>ราคา</strong></div>
						<div class="col-12">
							<rzslider class="bg-greensmoot" rz-slider-model="priceSlider.minValue" rz-slider-high="priceSlider.maxValue" rz-slider-options="priceSlider.options"></rzslider>
							<div class="d-flex">
								<p class="mr-auto">เริ่มต้น</p>
								<p class="ml-auto">สูงสุด</p>
							</div>
						</div>
					</div>
				</div>
			</div> -->

		</div>

		<!-- test on 17,117 -->
		<!-- {{ createRating("117",coupon_trans | filter : { coup_CouponID: "117", hico_Rating: "!0"} : true) }} -->
		<!-- {{ coupon_trans | filter : { coup_CouponID: "117", hico_Rating: "!0"} : true }} -->
		<!-- {{ rating(ratingDB,117,'Use')}} -->
		<?php //layout 2 ?>
		<div class="col-lg-9" >
			<!-- <div class="col-lg-4"> -->
			<?php //start record product ?>
			<!-- <div ng-show="loading" class="loading">LOADING...</div> -->
			<div class="row" >
				<!-- <div class="col-lg-4 product" ng-repeat='product in products | filter:filterProduct'> -->
				<div class="product col-lg-4" ng-repeat='product in products | filter:filterProduct| startFrom:currentPage*pageSize | limitTo:pageSize'>
					<div class="card shadow mb-3 mt-5 border border-secondary" style="max-width: 180rem;" >
						<!-- <a href="<?php //echo base_Url('product/'); ?>{{product.coup_CouponID}}"> -->
						<img ng-if="product.logo_image != null"
							ng-click='lookup("barnd",product.coup_CouponID,product.coup_Type)'
							class="rounded-circle shadow-sm img-responsive logo-brand border border-secondary bg-light" ng-src="upload/{{product.path_logo+product.logo_image}}">
			            <img class="card-img-top" ng-click='lookup("coup",product.coup_CouponID,product.coup_Type)'
		            		ng-src="upload/{{product.coup_ImagePath+product.coup_Image}}" >
			            <div class="text-dark" ng-click='lookup("coup",product.coup_CouponID,product.coup_Type)'>
			            	<!-- {{product.coup_CouponID}} -->
			            	<div class="d-none">id>{{product.coup_CouponID}}|id-b>{{product.category_brand}}</div>
			          		<div class="card-title h5 bold m-1 setHeightCardHeadText">
			          			{{product.coup_Name}}
			          		</div>
				              <div class="row m-1">
				              		<div class="text-right col-12 ">
				              			<div class="d-inline h6 regular pr-2">
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
				              		<!-- <div class="col-3"> -->
				              			<!-- ส่วนลด -->
				              			<!-- <div><i class="fas fa-dollar-sign"></i> 5</div> -->
				              		<!-- </div> -->
				              		<!--<div class="col-5 text-center text-warning ">
				              			 <div class="d-inline" ng-repeat='i in [0,1,2,3,4]'>
				              				<div class="d-inline" ng-if="arrayRating[i]=='full'">
				              					<i class="fa fa-star text-warning " data-fa-transform="up-2"></i>
				              				</div>
				              				<div class="d-inline" ng-if="arrayRating[i]=='half'">
				              					<i class="fas fa-star-half-alt text-warning" data-fa-transform="up-2" ></i>
				              				</div>
				              				<div class="d-inline" ng-if="arrayRating[i]=='noting'">
				              					<i class="far fa-star text-warning " data-fa-transform="up-2"></i>
				              				</div>
				              			</div> -->
						        		<!-- test 2,117 -->
						        		<!-- <div>{{product.coup_CouponID}}</div> -->
						        		<!-- <div>{{product.coup_Type}}</div> -->
					        			<!-- <div ng-repeat="x in rating(ratingDB,product.coup_CouponID,product.coup_Type)">
					        				{{x}}
					        			</div> -->
					        			<!-- {{numRating}} -->
										<!-- <?php $numStarCalculator = 2;?>
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
				              		</div>-->
				              		<div class="col-12 text-right">
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
			            <!-- </a> -->
			        </div>
			  	</div>
				</div>

				<!-- <div ng-repeat='product in data | filter:filterProduct | startFrom:currentPage*pageSize | limitTo:pageSize ' > -->
			<!-- 	<div ng-repeat='product in data | filter:filterProduct | filter:currentPage*pageSize | limitTo:pageSize ' >
					<div class="row">
						<div class="col-6">
							{{product.coup_CouponID}}
						</div>
						<div class="col-6">
							{{product.coup_Name}}
						</div>
					</div>
				</div> -->
				<?php //start pagination ?>
				<!-- <ul>
			        <li ng-repeat="item in data | startFrom:currentPage*pageSize | limitTo:pageSize">
			            {{item}}
			        </li>
			    </ul> -->

			    <!-- {{currentPage+1}}/{{numberOfPages()}} -->


    			<!-- <pagination num-pages="noOfPages" current-page="currentPage" class="pagination-small"></pagination> -->


				<!-- <div class="d-flex justify-content-center ">
					<ul uib-pagination total-items="totalItems" ng-model="currentPage" ng-change="pageChanged()"
						maxSize="itemsPerPage"></ul>

				</div> -->

				<!-- test01 -->
				<div class="d-flex justify-content-center mb-3">
					<button class="btn btn-primary mr-auto" ng-disabled="currentPage == 0" ng-click="currentPage=currentPage-1">
			        Previous
			    	</button>
			    	 <h5>{{currentPage+1}}</h5>
				    <h5>/</h5>
				    <h5>{{pageAfterFilter}}</h5>
				    <button class="btn btn-primary ml-auto"  ng-click="currentPage=currentPage+1"
			    			ng-disabled="currentPage+1 >= pageAfterFilter">
				        Next
				    </button>
					<!-- <ul class="pagination">
					  <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
					  <li class="page-item active"><a class="page-link" href="#">1</a></li>
					  <li class="page-item"><a class="page-link" href="#">2</a></li>
					  <li class="page-item"><a class="page-link" href="#">3</a></li>
					  <li class="page-item"><a class="page-link" href="#">Next</a></li>
					</ul> -->
				</div>
		  	</div>
		</div>
	</div>
</div>
</div> <!-- end ProductController -->







<div ng-controller='productController' class="pb-5" >
<!-- {{myVar}}
<button ng-click="buttonClicked()">sssssssss</button> -->
	<?php //menu filter product type for mobile ?>
		<div class="modal fade" id="product_filter_left_mobile"  tabindex="-1" role="dialog" aria-labelledby="product_filter_left_mobile_title" aria-hidden="true">
		<!-- <div class="" id="product_filter_left_mobile"  tabindex="-1" role="dialog" aria-labelledby="product_filter_left_mobile_title" aria-hidden="true"> -->
			<div class="modal-dialog" role="document">
			    <div class="modal-content">
			        <div class="modal-header">
				        <div class="modal-title h4" id="product_filter_left_mobile_title">คัดกรอง</div>
				        <div class="modal-title h4 text-danger" data-dismiss="modal"><i class="far fa-times-circle"></i></div>
			        </div>
			        <div class="modal-body">
	        			<div class="row">
	        				<div class="col-6"><strong>หมวดหมู่</strong></div>
	        				<div class="col-6 text-right">[ทั้งหมด]</div>
	        			</div>
	        			<div ng-repeat="(key,value) in products|groupBy:'category_brand'">
	        				<div class="row" >
	        					<div class="col-8">
	        						<div class="form-check">
									  <input class="form-check-input" type="checkbox"
									  		ng-model="confirmed" ng-change="checkBoxProductType(this)"
									  		>
									  <label class="form-check-label">{{ catrogy_barnd[key-1].category_name }}</label>
									</div>
	        					</div>
	        					<div class="col-4 text-right">
	        						<p>{{ value.length }}</p>
									<p>{{ checkbox[key] }}</p>
	        					</div>
	        				</div>
	        			</div>
			        </div>
			        <div class="modal-footer">
				        <button type="button" class="btn btn-danger bg-danger text-white" data-dismiss="modal">
				        	<div class="medium">Close</div>
				    	</button>
			        </div>
			    </div>
		  	</div>
		</div>
	<?php //end menu filter product type for mobile ?>

<!-- <div ng-show="expression">LONDING...</div> layoutProductOff-->
	<div class="container layoutOff pt-3" ng-class="{layoutOn:isReadyShow}">
	<!-- <div class="container">  -->
		<?php //select menu top right ?>


		<!-- <select
			class="selectpicker">
		    <option value="-coup_numUse" >ยอดนิยม</option>
			<option value="coup_Price" >ราคาน้อยไปมาก</option>
			<option value="-coup_Price" >ราคามากไปน้อย</option>
		</select> -->
		<select ng-model="register.countryId" ng-options="country.id as country.name for country in options"></select>

		<div class="row mt-5 ">
			<!-- <select class="selectpicker " data-style="btn-primary" name=""> -->

			  <!-- <option ng-click="selectOderBy('-coup_numUse')" >ยอดนิยม</option> -->
			  <!-- <option ng-click="selectOderBy('coup_Price')" >ราคาน้อยไปมาก</option> -->
			  <!-- <option ng-click="selectOderBy('-coup_Price')" >ราคามากไปน้อย</option> -->
			<!-- </select> -->



			<!-- <select ng-model="blisterPackTemplateSelected" ng-change="changedValue(blisterPackTemplateSelected)"
            data-ng-options="blisterPackTemplate as blisterPackTemplate.name for blisterPackTemplate in blisterPackTemplates" class="btn btn-primary">
		      	<option value="">ทั้งหมด</option>
		    </select> -->
			<!-- <button type="button" class="btn btn-primary d-inline mr-auto d-block d-lg-none" data-toggle="modal" data-target="#product_filter_left_mobile" >
	            	คัดกรอง
			</button> -->
			<!-- <div uib-dropdown on-toggle="toggled(open)" class="d-inline ml-auto dropdownMyWide">
	            <button type="button" class="btn btn-primary btn-squre w-100 " href id="dropdown-product-filter" uib-dropdown-toggle>
	            	{{drowdownTextHead}}
	  			</button>
	            <div class="dropdown-menu" uib-dropdown-menu aria-labelledby="dropdown-product-filter">
	                <div class="dropdown-item text-center" ng-click="selectOderBy('-coup_numUse')">ยอดนิม</div>
	                <div class="dropdown-item text-center" ng-click="selectOderBy('coup_Price')">ราคาน้อยไปมาก</div>
	                <div class="dropdown-item text-center" ng-click="selectOderBy('-coup_Price')">ราคามากไปน้อย</div>
	            </div>
	        </div> -->

			 <!--  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			    ทั้งหมด
			  </button>
			  <div class="dropdown-menu">
			    <a class="dropdown-item" href="#">แนะนำ</a>
			    <a class="dropdown-item" href="#">ล่าสุด</a>
			    <a class="dropdown-item" href="#">ยอดนิยม</a>
			    <a class="dropdown-item" href="#">สถานที่ใกล้เคียง</a>
			  </div> -->
		</div>

		<div class="row">
			<?php //layout 1 ?>
			<div class="col-lg-3 d-none d-lg-block ">
				<?php //menu ?>
		      	<h5 class="mb-2">คัดกรอง</h5>
				<div class="row">
					<div class="shadow m-2 p-2 w-100 mb-5 bg-white rounded
						collapse navbar-collapse" id="menuResponetive" >
						<div class="row mb-3 pt-3">
							<div class="col-7 "><strong>หมวดหมู่</strong></div>
							<div class="col-5 text-right">[ทั้งหมด]</div>
						</div>
						<?php //ng-repeat="(key, value) in players | groupBy: 'team'" ?>

						<div class="productMenuHover" ng-repeat="barndType in catrogy_barnd" ng-click="menuFilterRowClick(barndType.category_brand);" name="productRow{{barndType.category_brand}}"  my-repeat-directive>
							<div class="d-flex justify-content-between px-2" >
								<div class="">
									<div class="d-inline">
										<i class="far fa-square" name="productCheckbox{{barndType.category_brand}}"></i>
									</div>
								  	<!-- <input class="form-check-input" type="checkbox" name="productCheckbox{{key}}"
								  		ng-model="confirmed" ng-change="menuFilterRowClick(this.key)"
								  		> -->
								  	<div class="d-inline">{{barndType.category_brand}} {{ barndType.category_name }}</div>
								</div>
								<div class="">
									<p>{{ barndType.product_category_length }}</p>
									<!-- <p>{{ checkbox[key] }}</p> -->
								</div>
							</div>
						</div>
					</div>
				</div> <?php //end row  ?>
			</div>

			<!-- test on 17,117 -->
			<!-- {{ createRating("117",coupon_trans | filter : { coup_CouponID: "117", hico_Rating: "!0"} : true) }} -->
			<!-- {{ coupon_trans | filter : { coup_CouponID: "117", hico_Rating: "!0"} : true }} -->
			<!-- {{ rating(ratingDB,117,'Use')}} -->
			<?php //layout 2 ?>
			<div class="col-lg-9 mt-4" >
				<!-- {{orderByStr}} -->
				<div class="row">
					<!-- <div class="col-lg-4 product" ng-repeat='product in products | filter:filterProduct'> -->
					<div class="product col-lg-4 productMargin" ng-repeat='product in products | filterMultiple:{category_brand:checkBoxCatagoryArr} | orderBy:orderByStr | limitTo:numLimitProduct'>
						<div class="card shadow mb-3 mt-3 " style="max-width: 180rem;" >
							<!-- <a href="<?php //echo base_Url('product/'); ?>{{product.coup_CouponID}}"> -->
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
					<div class="row">
						<div class="col-12 text-right">
							<button class="btn btn-primary" ng-click="additional()">เพิ่มเติม</button>
						</div>
					</div>
			  	</div>
			</div>
		</div>
	</div>




</div> <!-- end ProductController -->






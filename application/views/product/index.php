

<div ng-controller='productController' class="pb-5" ng-init="init()" ng-if="!isLoading">
	<!-- {{myVar}}
	<button ng-click="buttonClicked()">sssssssss</button> -->


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
		<!-- <select ng-model="register.countryId" ng-options="country.id as country.name for country in options"></select> -->

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

		<div class="row" ng-if="catrogy_barnd.length > 0">
			<?php //layout 1 ?>
			<div class="col-lg-12 text-right">
				<select class="prettyDropdown" ng-model="selectDropDrowns" ng-options="item.name for item in dropDrowns" >
				</select>
				<!-- <pre class="text-center">
					<div class="row" ng-repeat="product in products | orderBy: selectDropDrowns.value">
						<div class="col-6">
							{{product.coup_UpdatedDate}}
						</div>
						<div class="col-6">
							{{product.coup_numUse}}
						</div>

					</div>


				</pre> -->
			</div>
			<div class="col-lg-3 d-none d-lg-block ">
				<?php //menu ?>
		      	<h5 class="mb-2 medium h4">คัดกรอง</h5>
				<div class="row">
					<div class="shadow mr-2 p-2 w-100 bg-white rounded
						collapse navbar-collapse" id="menuResponetive" >
						<div class="row mb-3 pt-3">
							<div class="col-7 medium">หมวดหมู่</div>
							<div class="col-5 text-right text-gray1">[ทั้งหมด]</div>
						</div>
						<?php //ng-repeat="(key, value) in players | groupBy: 'team'" ?>

						<div class="productMenuHover" ng-repeat="(key, value) in catrogy_barnd | orderBy:'category_brand_name' | groupBy: 'category_brand_name' " ng-click="menuFilterRowClick(value[0].category_brand,value.length);" name="productRow{{value[0].category_brand}}" >
							<!-- {{key }}{{value.length }} -->
							<div class="d-flex justify-content-between px-2" >
								<div class="text-gray1">
									<div class="d-inline">
										<i class="far fa-square" name="productCheckbox{{value[0].category_brand}}"></i>
									</div>

								  	<div class="d-inline"> {{ key }}</div>
								</div>
								<div class="text-gray2">
									<p>{{ value.length }}</p>
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
					<div class="product col-md-6 col-lg-4 productMargin" ng-repeat='product in products | filterMultiple:{
							category_brand:checkBoxCatagoryArr
						} | orderBy:selectDropDrowns.value | limitTo:numLimitProduct ' >
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
			              			<!-- <hr class="my-0 mx-3"> -->
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
				            <!-- </a> -->
				        </div>
				  	</div>
				</div>
				<!-- {{numLimitProduct}}|{{numProductLimitNow}}<br> -->

				<div class="row mb-5 p-3 text-right" ng-if="numLimitProduct < numProductLimitNow">
					<div class="box-additional ml-auto px-3 py-1">
						<div class="cursor-pointer h4 medium w-100 m-0" ng-click="additional()">เพิ่มเติม</div>
					</div>

					<!-- {{ numLimitProduct }} -->
				</div>
		  	</div>
		</div>
	</div>

	<div class="container">
		<div class="row" ng-if="isLoading" >
			<div class="col-12 text-center">
				<div class="d-inline">
					<div class="mb-5">
						<div class="fa-3x">
							<i class="fas fa-spinner fa-pulse"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="row" ng-if="products.length == 0" >
			<div class="col-12 text-center mb-4">
				<div class="d-inline ">
					<div class="h3">ไม่พบสิ่งที่คุณหา</div>
					<i class="fas fa-search h3"></i>
				</div>
				<div class="py-3">
					<button onclick="history.go(-1)" class="btn btn-lg btn-primary">ย้อนกลับ</button>
				</div>
			</div>
		</div>
	</div>

<!-- <div class="teddt">asdsadasd</div> -->

</div> <!-- end ProductController -->


<script type="text/javascript">

	// $(document).ready(function() {

    // });
</script>



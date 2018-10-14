<div class="container" ng-controller='productController'>
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
		<div class="col-md-3 pt-3">
			<?php //menu ?>
			<h6 class="m-3">คัดกรอง</h6>
			<div class="row">
				<div class="shadow p-3 w-100 mb-5 bg-white rounded" >
					<div class="row mb-3 ">
					<div class="col-6"><strong>หมวดหมู่</strong></div>
					<div class="col-6 text-right">[ทั้งหมด]</div>
					</div>
					<?php //ng-repeat="(key, value) in players | groupBy: 'team'" ?>
			
					<div class="row" ng-repeat="(key,value) in products|groupBy:'category_brand'">
						<div class="col-6">
							<div class="form-check">
							  <input class="form-check-input" type="checkbox"
							  		ng-model="confirmed" ng-change="checkBoxProductType(this)" 
							  		>
							  <label class="form-check-label">Type{{ key }}</label>
							</div>
						</div>
						<div class="col-6 text-right">
							<p>{{ value.length }}</p>
							<p>{{ checkbox[key] }}</p>
						</div>
					</div>				</div>
			</div> <?php //end row  ?>
			<div class="row">
				<div class="shadow p-3 w-100 mb-5 bg-white rounded" >
					<div class="row mb-3 ">
						<div class="col-12"><strong>ราคา</strong></div>
						<!-- <div class="col-6 text-right">[ทั้งหมด]</div> -->
						<div class="col-12">



							<rzslider class="bg-greensmoot" rz-slider-model="priceSlider.minValue" rz-slider-high="priceSlider.maxValue" rz-slider-options="priceSlider.options"></rzslider>
							<div class="d-flex">
								<p class="mr-auto">เริ่มต้น</p>
								<p class="ml-auto">สูงสุด</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			
		</div>

		<?php //layout 2 ?>
		<div class="col-md-9 ">
			<!-- <div class="col-lg-4"> -->
			<?php //start record product ?>
			<div class="row">
				<!-- <div class="col-lg-4 productHover" ng-repeat='product in products | filter:filterProduct'> -->
				<div class="col-lg-4 productHover" ng-repeat='product in products | filter:filterProduct| startFrom:currentPage*pageSize | limitTo:pageSize'>
					<div class="card shadow mb-3 mt-5" style="max-width: 180rem;" >
						<a href="<?php echo base_Url('product/'); ?>{{product.coup_CouponID}}">
						<img class="rounded-circle shadow-sm img-responsive logo-brand" 
						ng-if="product.logo_image != null" ng-src="upload/{{product.path_logo+product.logo_image}}">
						<img class="rounded-circle shadow-sm img-responsive logo-brand" 
						ng-if="product.logo_image == null" ng-src="http://placehold.it/50x50">
			            <img class="card-img-top" ng-src="upload/{{product.coup_ImagePath+product.coup_Image}}" >
			            <div class="text-dark">

			          		<h5 class="card-title m-1">{{product.coup_Name}}</h5>
				              <div class="row m-1">
				              		<div class="col-2"></div>
				              		<div class="col-5 text-right" style="text-decoration: line-through;">
				              			<small>3000฿</small>
				              		</div>
				              		<div class="col-5 text-right text-danger"><h5>{{ product.coup_Price|number:0}}฿</h5></div>
		              		</div>
				            <div class="row m-1 mt-2" style="font-size: 0.3rem;">  		
				              		<div class="col-3"><small >
				              			<i class="fas fa-dollar-sign"></i> 5</small>
				              		</div>
				              		<div class="col-5 text-center text-warning " >
						        			<i class="fa fa-star fa-xs"></i>
						        			<i class="fa fa-star fa-xs"></i>
						        			<i class="fa fa-star fa-xs"></i>
						        			<i class="fa fa-star fa-xs"></i>
						        			<i class="fa fa-star fa-xs"></i>
				              		</div>
				              		<div class="col-4 text-right"><small>ขายเเล้ว 20</small></div>
			              	</div>
			            </div>
			            </a>
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
				<div class="d-flex justify-content-center">
					<button class="btn btn-primary mr-auto" ng-disabled="currentPage == 0" ng-click="currentPage=currentPage-1">
			        Previous
			    	</button>
			    	 <h5>{{currentPage+1}}</h5>
				    <h5>/</h5>
				    <h5>{{pageAfterFilter}}</h5> 
				    <button class="btn btn-primary ml-auto"  ng-click="currentPage=currentPage+1" 
			    			ng-hide="currentPage+1 >= pageAfterFilter">
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

				<!-- test02 -->
			    
			    <!-- <ul uib-pagination boundary-links="true" total-items="totalItems" ng-model="currentPage" class="pagination-sm" previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></ul>
			    <ul uib-pagination direction-links="false" boundary-links="true" total-items="totalItems" ng-model="currentPage"></ul>
			    <ul uib-pagination direction-links="false" total-items="totalItems" ng-model="currentPage" num-pages="smallnumPages"></ul>
			    <pre>The selected page no: {{currentPage}}</pre>
			    <button type="button" class="btn btn-info" ng-click="setPage(3)">Set current page to: 3</button> -->
		  	</div>


			<!-- <div class="row">
				<?php //start record product ?>
				<div class="col-lg-4 col-sm-6 portfolio-item mt-1 mb-1" ng-repeat='product in products'>
		          <div class="card h-100">
		            <a href="#"><img class="card-img-top" src="http://placehold.it/700x400" alt=""></a>
		            <div class="card-body">
		              <h5 class="card-title">{{product.coup_Name}}</h5>
		              <div class="row">
		              		<div class="col-4"><small><img src="http://placehold.it/50x50" alt=""></small></div>
		              		<div class="col-4 text-right" style="text-decoration: line-through;">
		              			<small>3000</small>
		              		</div>
		              		<div class="col-4"><small>{{product.coup_Price}} ฿</small></small></div>
		              		<div class="col-4"><small>+5</small></div>
		              		<div class="col-4"><small>star</small></div>
		              		<div class="col-4"><small><small>ขายเเล้ว 20</small></small></div>
		              </div>
		              
		            </div>
		          </div>
		      	</div>
		      	<?php //end record product ?>
			</div> -->
		</div>
	</div>
</div>




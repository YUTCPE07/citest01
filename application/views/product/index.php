<div class="container" ng-controller='productController'>
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
	  
	<label>Search: <input ng-model="searchText"></label>
	<table id="searchTextResults">
	<tr><th>Name</th><th>Phone</th></tr>
	  <!-- <tr ng-repeat="product in products | filter:{ category_brand: searchText }" -->
	  <!-- <tr ng-repeat="product in products | filter:{ category_brand: searchText }"
	  		ng-if="product.category_brand == searchText || searchText == '' ">
	    <td>{{product.coup_Name}}</td>
	    <td>{{product.category_brand}}</td>
	  </tr> -->
	</table>



	<div class="row">
		<?php //layout 1 ?>
		<div class="col-md-3 pt-3">
			<?php //menu ?>
			<h6 class="m-3">คัดกรอง</h6>
			<div class="shadow p-3 mb-5 bg-white rounded" >
				<div class="row mb-3 ">
				<div class="col-6"><strong>หมวดหมู่</strong></div>
				<div class="col-6 text-right">[ทั้งหมด]</div>
				</div>
				<?php //ng-repeat="(key, value) in players | groupBy: 'team'" ?>
				{{checkboxModel.value}}
		
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
				</div>
				
				
			</div>
			
		</div>

		<?php //layout 2 ?>
		<div class="col-md-9 ">

			<!-- test -->
			<table id="searchTextResults">
				<tr><th>Name</th><th>Phone</th></tr>
				  <!-- <tr ng-repeat="product in products | filter:{'category_brand':'1'}:true "> -->
				 <!--  <tr ng-repeat="product in products | filter:filterProduct">
				    <td>{{product.coup_Name}}</td>
				    <td>{{product.category_brand}}</td>
				  </tr> -->
			</table>

			<!-- <div class="col-lg-4"> -->

				<?php //start record product ?>
				<div class="row">
					<div class="col-md-4 productHover" ng-repeat='product in products | filter:filterProduct'>
							<div class="card shadow mb-3 mt-5" style="max-width: 180rem;" >
							<img class="rounded-circle shadow-sm img-responsive logo-brand" 
							ng-if="product.logo_image != null" ng-src="assets/images/server_img/{{product.path_logo+product.logo_image}}">
							<img class="rounded-circle shadow-sm img-responsive logo-brand" 
							ng-if="product.logo_image == null" ng-src="http://placehold.it/50x50">
				            <img class="card-img-top" ng-src="assets/images/server_img/{{product.coup_ImagePath+product.coup_Image}}" >
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
				        </div>
				  	</div>
					</div>
					
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




<div class="container h4">
		<!-- <div class="d-flex justify-content-between"> -->
		<div class="row text-center">
			<div class="col-3 text-gray1">ประวัติการใช้สิทธิ์</div>
			<div class="col-2 offset-7 text-gray1">จำนวน</div>
		</div>
		<div class="hrRow mb-5"></div>

		<div ng-repeat="(key, value) in dataMyRightHistorys | groupBy: 'date_use' as result">
			<!-- {{result[key].length}} -->
			<!-- {{value[0].peoduct_path}} -->
			<div class="bg-white py-4 shadow">
				<div class="row text-center">
					<div class="col-lg-3">
						<div class="userRight-img ml-auto mr-auto" ng-init="pathImg = 'upload/'+value[0].peoduct_path+value[0].product_image">
							<img src="{{pathImg}}" class="rounded img-responsive home_brand shadow" alt="">
						</div>
					</div>
					<div class="col-lg-7 text-left" >
						<div class="text-black bold">{{value[0].product_name}}</div>
						<div class="text-green h5">{{value[0].brand_name}}</div>
						<div class="text-gray1 h5" >ใช้เเล้วเมื่อ {{value[0].date_use | cmdate:'dd/MM/yyyy'}}</div>
					</div>
					<div class="col-lg-2 ">
						<div class="text-gray1">{{result[key].length}}</div>
						<button class="btn btn-primary mt-4 active" data-toggle="modal" data-target="#store_modal" >
							<i class="fas fa-search"></i>
							<i class="fas fa-qrcode"></i>
						</button>
					</div>
				</div>
			</div><br>


			<?php //modal myHistoryPage : show when user click look ?>
			<!-- <div class="modal fade" id="store_modal"  tabindex="-1" role="dialog" aria-labelledby="store_modal_title" aria-hidden="true"> -->
			<div class="" id="store_modal"  tabindex="-1" role="dialog" aria-labelledby="store_modal_title" aria-hidden="true">
				<div class="modal-dialog modal-lg" role="document">
		        	<div ng-repeat="item in value">
				    	<div class="modal-content">
				        	<!-- <div class="modal-header"></div> -->
			        		<div class="modal-body" >
								<div class="row text-center">
									<div class="col-lg-3">
										<div class="userRight-img ml-auto mr-auto" ng-init="pathImg = 'upload/'+item.peoduct_path+item.product_image">
											<img src="{{pathImg}}" class="rounded img-responsive home_brand shadow" alt="">
										</div>
									</div>
									<div class="col-lg-5 text-left">
										<div class="text-black bold">{{item.product_name}}</div>
										<div class="text-green h5">{{item.brand_name}}</div>
										<div class="text-gray1 h5">ใช้เเล้วเมื่อ {{item.date_use | cmdate:'dd/MM/yyyy'}}</div>
										<div class="text-gray1 h5">วันที่ซื้อ {{item.date_create | cmdate:'dd/MM/yyyy'}}</div>
									</div>
									<div class="col-lg-4 boxShowCode">
										<div>รหัสโค้ด</div>
										<div>{{item.code_use}}</div>
									</div>
								</div>
				        	</div>
				        </div><br>
				        <!-- <div class="modal-footer">
					        <button type="button" class="btn btn-danger bg-danger text-white" data-dismiss="modal">
					        	<div class="medium">Close</div>
					    	</button>
				        </div> -->
				    </div>
			  	</div>
			</div> <?php //end modal myHistoryPage : show when user click look ?>
		</div>
	</div>
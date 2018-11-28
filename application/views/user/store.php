<style>
	.store-row {
		background-color: white;
		border:thin solid var(--color-gray2);
	}

	.store-tabSelect{
		color: var(--color-black);
		border-bottom: 3px solid var(--color-green);
	}
	.brColum {
		border-right: 1px solid var(--color-gray2);
	}

	.justify-content-between {

	}

	.userRight-img {
		max-width: 200px;
	}
	.store-imgFooter {
		opacity: 0.7;
	}

</style>
<div class="bg-gray3" ng-controller="storeController" ng-init="init()">
	<div class="container py-5 h4">
		<div class="row text-center shadow store-row cursor-pointer">
			<div class="col-4 py-2 text-gray2 brColum" ng-class="{'store-tabSelect':myRightPage}" ng-click="selectTab('right')">สิทธิ์ของฉัน</div>
			<div class="col-4 py-2 text-gray2 brColum" ng-class="{'store-tabSelect':myHistoryPage}" ng-click="selectTab('rightHistory')">ประวัติการใช้</div>
			<div class="col-4 py-2 text-gray2 " ng-class="{'store-tabSelect':myRightExpPage}" ng-click="selectTab('rightExp')">หมดอายุ</div>
		</div>
	</div>
	<!-- {{dataMyRights | json}} -->
	<?php //my myRightPage ?>
	<?php //test usesr id 9 ?>
	<div class="container h4">
		<!-- <div class="d-flex justify-content-between"> -->
		<div class="row text-center">
			<div class="col-3 text-gray1">สิทธิ์ของฉัน</div>
			<div class="col-2 offset-7 text-gray1">จำนวน</div>
		</div>
		<div class="hrRow mb-5"></div>
		<div ng-repeat="dataMyRight in dataMyRights">
			<div class="bg-light py-4 shadow">
				<div class="row text-center">
					<div class="col-lg-3">
						<div class="userRight-img ml-auto mr-auto" ng-init="pathImg = 'upload/'+dataMyRight.product_imgPath+dataMyRight.product_image">
							<img src="{{pathImg}}" class="rounded img-responsive home_brand shadow" alt="">
						</div>
					</div>
					<div class="col-lg-7 text-left">
						<div class="text-black bold">{{dataMyRight.product_name}}</div>
						<div class="text-green h5">{{dataMyRight.brand_name}}</div>
						<div class="text-gray1 h5">วันหมดอายุ {{dataMyRight.date_expire}}</div>
					</div>
					<div class="col-lg-2 ">
						<div class="text-gray1">{{dataMyRight.count}}</div>
						<button class="btn btn-primary mt-4 active">ใช้สิทธิ์ </button>
					</div>
				</div>
			</div><br>
		</div>
	</div>


	<?php //my myHistoryPage ?>
	<div class="container h4">
		<!-- <div class="d-flex justify-content-between"> -->
		<div class="row text-center">
			<div class="col-3 text-gray1">ประวัติการใช้สิทธิ์</div>
			<div class="col-2 offset-7 text-gray1">จำนวน</div>
		</div>
		<div class="hrRow mb-5"></div>

		<div class="bg-light py-4 shadow">
			<div class="row text-center">
				<div class="col-lg-3">
					<div class="userRight-img ml-auto mr-auto">
						<img src="upload/171/logo_upload/logo_20180925_140408.jpg" class="rounded img-responsive home_brand shadow" alt="">
					</div>
				</div>
				<div class="col-lg-7 text-left">
					<div class="text-black">สิทธิพิเศษ Zummer bar</div>
					<div class="text-green h5">Zummer bar</div>
					<div class="text-gray1 h5">วันหมดอายุ 21/8/61</div>
				</div>
				<div class="col-lg-2 ">
					<div class="text-gray1">1</div>
					<button class="btn btn-primary mt-4 active">
						<i class="fas fa-search"></i>
						<i class="fas fa-qrcode"></i>
					</button>
				</div>
			</div>
		</div><br>
	</div>


	<?php //my myRightExpPage ?>
	<div class="container h4">
		<!-- <div class="d-flex justify-content-between"> -->
		<div class="row text-center">
			<div class="col-3 text-gray1">สิทธิ์ของฉัน</div>
			<div class="col-2 offset-7 text-gray1">จำนวน</div>
		</div>
		<div class="hrRow mb-5"></div>

		<div class="bg-light py-4 shadow">
			<div class="row text-center">
				<div class="col-lg-3">
					<div class="userRight-img ml-auto mr-auto">
						<img src="upload/171/logo_upload/logo_20180925_140408.jpg" class="rounded img-responsive home_brand shadow" alt="">
					</div>
				</div>
				<div class="col-lg-7 text-left">
					<div class="text-black">สิทธิพิเศษ Zummer bar</div>
					<div class="text-green h5">Zummer bar</div>
					<div class="text-gray1 h5">วันหมดอายุ 21/8/61</div>
				</div>
				<div class="col-lg-2 ">
					<div class="text-gray1">1</div>
					<button class="btn btn-primary mt-4 active">ต่ออายุ</button>
				</div>
			</div>
		</div><br>
	</div>

	<?php //img store foolter  ?>
	<div class="w-50 ml-auto">
		<img class="store-imgFooter" src="assets/images/user/store-01.jpg">
	</div>
	<!-- <div class="hr_footer_height"></div> -->
</div>

<script type="text/javascript">

</script>
<style type="text/css">
	.pay-boxContent {
		text-align: center;
		max-width: 550px;
		margin-left: auto;
		margin-right: auto;
		padding: 3rem;
	}

	.pay-boxContent-stepBox{

	    border-radius: .25rem;
	    display: inline-block;
	    text-align: center;

	}

	.pay-boxContent-point{
		color: var(--color-gray2);
	    text-align: center;
	    display: inline-block;
	    font-size: 90px;
	    position:relative;
		z-index: -2;
	}

	.pay-boxContent-stepBox-num{
		color: white;
		background-color: var(--color-gray2);
	    background-clip: border-box;
	    border: 1px solid rgba(0,0,0,.125);
	    border-radius: .25rem;
	    display: inline-block;
	    text-align: center;
	    width: 30px;
	    font-size: 20px;

	}

	.pay-boxContent-stepBox-text {
		position: relative;
		bottom: 28px;

	}

	.pay-boxContent-box-bankService {
		display: inline-block;
	}

	.pay-boxContent-img-bankService {
		width: 100px;
	}

	.pay-boxContent-img-bankMaster {
		width: 110px;
	}

	.pay-BankBorderHover:hover {
		border:solid 5px var(--color-green);
		transform: scale(1.1);
		cursor: pointer;
	}



	.pay-img-mark {
		width: 60px;
	}

</style>
<!-- <?php echo '<pre>' ?> -->
<!-- <?php print_r($userAction)?> -->
<!-- <?php echo json_encode($userAction) ?> -->
<!-- <?php echo '</pre>' ?> -->

<!-- html -->
<div class="h4" ng-controller="payController">
	<div class="container" ng-if="!actionRespone">
		<div class="pay-boxContent">
			<div class="pay-boxContent-stepBox">

				<div class="pay-boxContent-stepBox-num bg-green" >1</div>

			</div>
			<div class="pay-boxContent-point" ng-class="{'text-green':numStepNow==2||numStepNow==3}">........</div>
			<div class="pay-boxContent-stepBox">
				<div class="pay-boxContent-stepBox-num" ng-class="{'bg-green':numStepNow==2||numStepNow==3}">2</div>
			</div>
			<div class="pay-boxContent-point" ng-class="{'text-green':numStepNow==3}">........</div>
			<div class="pay-boxContent-stepBox">
				<div class="pay-boxContent-stepBox-num" ng-class="{'bg-green':numStepNow==3}">3</div>
			</div>
			<div class="row pay-boxContent-stepBox-text">
				<div class="col-4 h5 pr-5 text-green"> ซื้อ</div>
				<div class="col-4 h5 text-gray2" ng-class="{'text-green':numStepNow==2||numStepNow==3}">ชำระเงิน</div>
				<div class="col-4 h5 text-gray2 pl-5" ng-class="{'text-green':numStepNow==3}">เสร็จสิ้น</div>
			</div>
		</div> <!-- end pay-boxContent -->
	</div> <!-- end container -->

	<div class="row d-none" >
		<div class="col-2 offset-1 pr-5">test 1 2 3</div>
		<button class="col-1 btn" ng-click="numStepNow=1">1</button>
		<button class="col-1 offset-1 btn" ng-click="numStepNow=2">2 success</button>
		<button class="col-1 offset-1 btn" ng-click="numStepNow=3">3</button>
	</div>
	<div class="row pt-2 d-none">
		<div class="col-3 offset-1 pr-5">test bank faill sucess</div>
		<button class="col-1 btn" ng-click="userAction('success')">success</button>
		<button class="col-1 offset-1 btn" ng-click="userAction('error')">faill</button>
	</div>

	<div class="container" ng-if="!actionRespone">
		<div class="bold mb-4">เลือกช่องทางการชำระเงิน</div>
		<div class="card p-4 bg-gray3 d-block">
			<div class="pay-boxContent-box-bankService">
				<div class="medium">บัตรเครดิต/เดบิต</div>
			</div>
			<div class="pay-boxContent-box-bankService this_link pl-lg-5" ng-click="userSelectPay('visaMasterCard')">
				<!-- <a href="<?php echo base_url() ?>pay"> -->
					<img class="pay-boxContent-img-bankService" src="<?php echo base_url() ?>assets/images/pay/visa.png" >
					<img class="pay-boxContent-img-bankService" src="<?php echo base_url() ?>assets/images/pay/mastercard.png" >
				<!-- </a> -->
			</div>
		</div>
		<div class="card bg-gray3 mt-4 mb-5">
			<div class="row pt-4 px-4">
				<div class="col-12 medium">หักบัญชีธนาคาร</div>
			</div>
			<div class="row pb-4 pt-2 px-4">
				<div class="col-6 col-lg-2 mb-4 mb-lg-0">
					<!-- <a href="<?php echo base_url() ?>pay"> -->
						<img src="<?php echo base_url() ?>assets/images/pay/bankIcon/BBL.jpg" class="rounded pay-boxContent-img-bankMaster shadow border-my pay-BankBorderHover" ng-click="userSelectPay('BBL')">
					<!-- </a> -->
				</div>
				<div class="col-6 col-lg-2 mb-4 mb-lg-0">
					<!-- <a href="<?php echo base_url() ?>pay"> -->
						<img src="<?php echo base_url() ?>assets/images/pay/bankIcon/SCB.jpg" class="rounded pay-boxContent-img-bankMaster shadow border-my pay-BankBorderHover" ng-click="userSelectPay('SCB')">
					<!-- </a> -->
				</div>
				<div class="col-6 col-lg-2">
					<!-- <a href="<?php echo base_url() ?>pay"> -->
						<img src="<?php echo base_url() ?>assets/images/pay/bankIcon/BAY.jpg" class="rounded pay-boxContent-img-bankMaster shadow border-my pay-BankBorderHover" ng-click="userSelectPay('BAY')">
					<!-- </a> -->
				</div>
			</div>
		</div>
	</div> <!-- end container -->

	<div class="container my-5" ng-if="actionRespone">
		<!-- <div>actionRespone</div> -->
		<!-- <div ng-if="bankRespone==true"> -->
		<div ng-show="bankRespone==true">
			<div class="text-center">
				<img class="pay-img-mark d-inline-block" src="<?php echo base_url() ?>assets/images/pay/mark-success.png">
				<div class="d-inline-block medium" >ชำระเงินเรียบร้อย</div>
			</div>
			<div class="d-inline-block">
				<img class="ml-auto mr-auto" src="<?php echo base_url() ?>assets/images/pay/pay-success.png">
			</div>
			<div class="row pt-4">
				<div class="col-12 text-center">
					<a href="<?php echo base_url() ?>">
						<button class="btn btn-primary medium this_link">หน้าเเรก</button>
					</a>
					<a href="<?php echo base_url() ?>store">
						<button class="btn btn-primary active ml-5 medium this_link">สิทธิ์ของฉัน</button>
					</a>
				</div>
			</div>
		</div>
		<!-- <div ng-if="bankRespone==false "> -->
		<div ng-show="bankRespone==false ">
			<div class="text-center">
				<img class="pay-img-mark d-inline-block" src="<?php echo base_url() ?>assets/images/pay/mark-error.png">
				<div class="d-inline-block medium">การชำระเงินของท่านยังไม่สมบูรณ์</div>
			</div>
			<div class="d-inline">
				<img class="ml-auto mr-auto" src="<?php echo base_url() ?>assets/images/pay/pay-error.png">
			</div>
			<div class="row pt-4">
				<div class="col-12 text-center">
					<button class="btn btn-primary active medium this_link" ng-click="payAgian()">ชำระเงิน</button>
				</div>
			</div>
		</div>
	</div>


	<div class="container"><!-- modal -->
		<div class="modal fade" id="modalTestUserAction" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel" >
		<!-- <div class="" id="modalTestUserAction" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel"> -->
   			<div class="modal-dialog" role="document" style="width:80%">
   				<div class="modal-content p-4">
   					<div class="text-center bold">ทดสอบ Action Respone Form Bank</div>
   					<div class="text-center" >
   						<div class="text-green medium d-inline-block">user เลือก :</div>
   						<div class="d-inline-block">{{modalData.bank_name}}</div><br>
   						<div class="text-green medium d-inline-block">user วันที่ เวลา : </div>
   						<div class="d-inline-block">{{modalData.dateTime}}</div><br>
   						<div class="text-green medium d-inline-block">product id : </div>
   						<div class="d-inline-block">{{action.coup_CouponID}}</div><br>
   						<div class="text-green medium d-inline-block">product ราคา/ชิ้น : </div>
   						<div class="d-inline-block">{{action.coup_Price*1}}</div><br>
   						<div class="text-green medium d-inline-block">product จำนวน : </div>
   						<div class="d-inline-block">{{action.p_num_select}}</div><br>
   						<div class="text-green medium d-inline-block">product ราคารวม : </div>
   						<div class="d-inline-block">{{action.coup_Price*action.p_num_select}}</div><br>
   						<div class="text-green medium d-inline-block">brand id : </div>
   						<div class="d-inline-block">{{action.brand_id}}</div><br>
   						<div class="text-green medium d-inline-block">brand name : </div>
   						<div class="d-inline-block">{{action.brand_name}}</div><br>

   						<button class="btn" ng-click="userAction('success')">Success</button>
   						<button class="btn" ng-click="userAction('error')">Error</button>
   					</div>
				</div>
			</div>
		</div>

	</div>

</div>





<div class="hr_footer_height"></div>

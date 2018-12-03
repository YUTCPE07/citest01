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

</style>



<!-- html -->
<div class="h4 " ng-controller="payController">
	<div class="container ">
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

	<div class="row">
		<div class="pr-5">test 1 2 3</div>
		<button class="col-1 btn" ng-click="numStepNow=1">1</button>
		<button class="col-1 offset-1 btn" ng-click="numStepNow=2">2 success</button>
		<button class="col-1 offset-1 btn" ng-click="numStepNow=2">2 faill</button>
		<button class="col-1 offset-1 btn" ng-click="numStepNow=3">3</button>
	</div>

	<div class="container mb-5">
		<div class="bold mb-4">เลือกช่องทางการชำระเงิน</div>
		<div class="card p-4 bg-gray3 d-block">
			<div class="pay-boxContent-box-bankService">
				<div class="medium">บัตรเครดิต/เดบิต</div>
			</div>
			<div class="pay-boxContent-box-bankService">
				<a href="<?php echo base_url() ?>pay">
					<img class="pay-boxContent-img-bankService" src="<?php echo base_url() ?>assets/images/pay/visa.png" >
				</a>
			</div>
			<div class="pay-boxContent-box-bankService">
				<a href="<?php echo base_url() ?>pay">
					<img class="pay-boxContent-img-bankService" src="<?php echo base_url() ?>assets/images/pay/mastercard.png" >
				</a>
			</div>
		</div>
		<div class="card bg-gray3 mt-4">
			<div class="row pt-4 px-4">
				<div class="col-12 medium">หักบัญชีธนาคาร</div>
			</div>
			<div class="row pb-4 pt-2 px-4">
				<div class="col-2">
					<a href="<?php echo base_url() ?>pay">
						<img src="<?php echo base_url() ?>assets/images/pay/bankIcon/BBL.jpg" class="rounded img-responsive home_brand shadow border-my">
					</a>
				</div>
				<div class="col-2">
					<a href="<?php echo base_url() ?>pay">
						<img src="<?php echo base_url() ?>assets/images/pay/bankIcon/SCB.jpg" class="rounded img-responsive home_brand shadow border-my">
					</a>
				</div>
				<div class="col-2">
					<a href="<?php echo base_url() ?>pay">
						<img src="<?php echo base_url() ?>assets/images/pay/bankIcon/BAY.jpg" class="rounded img-responsive home_brand shadow border-my">
					</a>
				</div>
				<div class="col-2">
					<a href="<?php echo base_url() ?>pay">
						<img src="<?php echo base_url() ?>assets/images/pay/bankIcon/KTB.jpg" class="rounded img-responsive home_brand shadow border-my">
					</a>
				</div>
			</div>
		</div>
	</div>
</div>





<div class="hr_footer_height"></div>

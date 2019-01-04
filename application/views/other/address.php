<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/plugins/google-map/map.css'); ?>">
<style type="text/css">
	.qrCodeLine {
		position: relative;
		width: 100px;
	}

	.qrCodeLine > img {
		position: absolute;
		top: -60px;
	}
</style>

<img class="backgroud" src="<?php echo base_url(); ?>assets/images/background/background01_cus1.png">
<div class="container mt-5 h5 light text-gray1">
	<div class="mb-5">
		<div class="h3 medium text-black">ติดต่อเรา</div>
	</div>

	<div class="mb-5">
		<div class="row">
			<div class="col-md-12 col-lg-12">
				<div class="h4 medium text-black">ที่อยู่</div>
	    		<div class="">เลขที่ 283 ตึกฟอร์มูล่า ชั้น 4 ถนนสุรวงศ์ แขวงสุริยวงศ์ เขตบางรัก กรุงเทพมหานคร 10500</div>
			</div>
		</div>
	</div>

	<div class="mb-5">
		<div class="row">
			<div class="col-md-12 col-lg-12">
				<div class="boxMap mx-auto shadow">
				  <div id="map"></div>
				</div>
			</div>
		</div>
		<div class="d-none">
			<!-- <div class=""> -->
			<input type="text" name="lat" value="13.727014033336701" disabled>
			<input type="text" name="lng" value="100.522381067276" disabled>
		</div>
	</div>


	<div class="mb-4 row text-black">
		<div class="col-md-12 col-lg-12">
			<div class="footer-social2 this_link d-inline-block">
                <img class="facebook" src="<?php echo base_url('assets/images/template/footer/call.jpg'); ?>">
            </div>
            <div class="d-inline-block py-1 px-2" >063-2269654</div>
        </div>
	</div>

	<div class="mb-4 row text-black">
		<div class="col-md-12 col-lg-12">
			<div class="footer-social2 this_link d-inline-block">
                <img class="facebook" src="<?php echo base_url('assets/images/template/footer/line.png'); ?>">
            </div>
            <div class="d-inline-block py-1 pl-2" >ID Line: @memberin </div>
            <div class="d-inline-block py-1 pr-2" >หรือ เเสกน</div>
            <div class="qrCodeLine d-md-inline-block d-none">
	            <img class="" src="<?php echo base_url('assets/images/template/footer/QR_code.jpg'); ?>">
	        </div>
	        <div class="d-md-none text-center">
	            <img class="w-25" src="<?php echo base_url('assets/images/template/footer/QR_code.jpg'); ?>">
	        </div>
        </div>
	</div>

	<div class="mb-4 row text-black">
		<div class="col-md-12 col-lg-12">
			<div class="footer-social2 this_link d-inline-block">
                <a href="https://www.facebook.com/MemberInApp/">
                  <img class="facebook" src="<?php echo base_url('assets/images/template/footer/fb.png'); ?>">
                </a>
            </div>
            <div class="d-inline-block py-1 px-2" >
            	<a href="https://www.facebook.com/MemberInApp">https://www.facebook.com/MemberInApp</a>
        	</div>
        </div>
	</div>

	<div class="mb-5 row text-black">
		<div class="col-md-12 col-lg-12">
			<div class="footer-social2 this_link d-inline-block">
                <img class="facebook" src="<?php echo base_url('assets/images/template/footer/mail.png'); ?>">
            </div>
            <div class="d-inline-block py-1 px-2" >
            	bd@memberin.com
        	</div>
        </div>
	</div>

</div>

<div class="hr_footer_height"></div>



<script src="<?php echo base_url('assets/plugins/google-map/google_map.js') ?>"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCgOYYNGdJV_5X_VG1PRgFChTnekgc-6To&language=TH&region=TH&callback=initMap" async defer></script>
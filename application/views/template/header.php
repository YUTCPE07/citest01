
<!DOCTYPE html>
<html lang="en" ng-app="app">
<head>

	<meta charset="utf-8">
	<meta name="keywords" content="memberin,สิทธิพิเศษ,วอชเชอร์,สมาชิกออนไลน์">
	<!-- <meta name="description" content="<?php //echo $description ?>"> -->
	<meta name="description" content="webbord, forum">
  	<!-- <meta name="author" content="<?php //echo $author ?>" > -->
	<meta name="baseUrl" content="<?php echo base_url(); ?>" >
	<meta name="viewport" content="width=device-width,height=device-height, initial-scale=1.0, minimum-scale=1.0">
	<?php
//header('Access-Control-Allow-Origin: *');
//header("Access-Control-Allow-Methods: GET, OPTIONS");
?>

	<!-- <meta http-equiv="Content-type" content="text/html;charset=UTF-8" /> -->
	<base href="<?php echo base_url(); ?>" />
	<!-- <title><?php //echo $title ?></title> -->
	<title>MemberIn</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap/bootstrap.min.css'); ?>">

	<!-- template from bootstrap4(freelancer) -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap/magnific-popup.css'); ?>">
	<link rel="stylesheet" type="text/css"
		href="<?php echo base_url('assets/plugins/seekbar/rzslider.min.css'); ?>">


	<!-- plugin -->
	<link rel="stylesheet" type="text/css"
		href="<?php echo base_url('assets/plugins/swiper_slideLayout/swiper.css'); ?>">
	<link rel="stylesheet" type="text/css"
		href="<?php echo base_url('assets/plugins/jsCalendar/jsCalendar.css'); ?>">
	<!-- <link rel="stylesheet" type="text/css"
		hrel="<?php //echo base_url('assets/plugins/angular-star-rating/star-rating.css') ?>"> -->

	<!-- font -->
	<link href="https://fonts.googleapis.com/css?family=Prompt:100,300,400,500,700&amp;subset=thai" rel="stylesheet">
	<link href="<?php echo base_url('assets/css/custom/reset.css'); ?>" rel="stylesheet">


	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom/reset.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom/varible.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom/custom-button.css'); ?>">
	<!-- <link rel="stylesheet" type="text/css" href="<?php //echo base_url('assets/css/custom/custom.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php //echo base_url('assets/css/custom/custom_media.css'); ?>"> -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom/template/navbar.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom/template/footer.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom/home/home.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom/product/index.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom/product/lookup.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom/other/aboutus.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom/other/policy.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom/other/termsofuse.css'); ?>">












	<!-- Bootstrap core JavaScript -->
    <script src="<?php echo base_url('assets/js/jquery/jquery-3.3.1.min.js'); ?>" ></script>
    <script src="<?php echo base_url('assets/js/bootstrap/bootstrap-popper.min.js'); ?>" ></script>
    <script src="<?php echo base_url('assets/js/bootstrap/bootstrap.min.js'); ?>" ></script>
    <script src="<?php echo base_url('assets/js/bootstrap/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/plugins/fontawesome-free-5.3.1-web/js/all.min.js'); ?>"></script>


    <!-- Plugin jquery -->
    <script src="<?php echo base_url('assets/js/jquery/jquery.easing.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/jquery/jquery.magnific-popup.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/jquery/jquery-migrate-1.2.1.min.js'); ?>"></script>

    <?php //login facebook ?>
    <script src="<?php echo base_url('assets/js/facebook/app.js') ?>"></script>

    <?php //angular ?>
    <script src="<?php echo base_url('assets/js/angular/plugin/angular.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/angular/plugin/angular-animate.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/angular/plugin/angular-touch.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/angular/plugin/angular-sanitize.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/bootstrap/ui-bootstrap-tpls-3.0.5.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/angular/plugin/angular-filter.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/angular/app.js') ?>"></script>

    <script src="<?php echo base_url('assets/js/angular/initRun.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/angular/indexService.js') ?>"></script>

    <script src="<?php echo base_url('assets/js/angular/loginController.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/angular/productController.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/angular/navbarController.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/angular/homeController.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/angular/shop_lookupController.js') ?>"></script>

    <!-- <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.8/angular-material.min.js"></script> -->
    <?php //product rating star ?>
    <!-- <script src="<?php //echo base_url('assets/plugins/angular-star-rating/index.js') ?>"></script> -->

    <?php //plugins ?>
    <?php //view product filter seekbar for price ?>
    <script src="<?php echo base_url('assets/plugins/seekbar/rzslider.min.js') ?>"></script>
    <?php //view home content slider show ?>
    <script src="<?php echo base_url('assets/plugins/swiper_slideLayout/swiper.js') ?>"></script>
    <script src="<?php echo base_url('assets/plugins/swiper_slideLayout/swiper_custom.js') ?>"></script>

    <!-- <script src="<?php //echo base_url('assets/plugins/slick_slideLayout/slick.min.js') ?>"></script> -->
    <!-- <script src="<?php //echo base_url('assets/plugins/slick_slideLayout/custom.js') ?>"></script> -->
    <?php //view product user select calender jsCalendar_custom Load on lookup ?>

    <script src="<?php echo base_url('assets/js/custom.js') ?>"></script>
</head>

<body id="page-top" scroll>
	<?php //for navbar fix ?>
	<div class="hr_head_height"></div>




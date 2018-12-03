<style type="text/css">
	.lookup-head-img > img{
     	max-width: 640px;
    }
</style>

<!-- <pre><?php print_r($db)?></pre> -->
<!-- this is view brand_lookup -->

<div class="container-fluid-my">
	<div class="lookup-head-img pt-md-5">
		<img class="img-fluid d-block ml-auto mr-auto shadow border-my"
			src="upload/<?php echo $db['path_cover'] . $db['cover'] ?>" alt="First slide">
	</div>
</div>

<div class="container py-5">

	<div class="d-flex flex-row-reverse p-lg-5 ">
		<!-- <button class="btn btn-primary ml-lg-5 ml-3">แชร์</button> -->
		<!-- <button class="btn btn-primary">ชื่นชอบ</button> -->
	</div>  <!-- btn like shared -->
	<div class="row pb-2">
		<div class="col-lg-2">
			<img class="img-thumbnail shadow
				d-none d-lg-block d-lg-block d-xl-block"
				src="upload/<?php echo $db['path_logo'] . $db['logo_image'] ?>" alt="First slide">
			<img class="img-thumbnail shadow w-50 ml-auto mr-auto mb-3
				d-block d-lg-none d-lg-none d-xl-none"
				src="upload/<?php echo $db['path_logo'] . $db['logo_image'] ?>" alt="First slide">
		</div>
		<div class="col-lg-10 w-100">
			<div class="row">
				<div class="col-lg-12 h2 medium
				d-none d-lg-block d-lg-block d-xl-block"><?php echo $db['name']; ?></div>
				<div class="col-lg-12 h4 medium py-3 text-center
				d-block d-lg-none d-lg-none d-xl-none"><?php echo $db['name']; ?></div>
			</div>
			<div class="row">
				<div class="col-lg-12 text-gray1">
					<div class="text-justify" ><?php echo nl2br($db['slogan']); ?></div>
				</div>
				<div class="col-lg-12 text-gray2">
					<div class="text-justify" ><?php echo nl2br($db['company_name']); ?></div>
				</div>
			</div>
		</div>
	</div>
	<hr class="">
		<?php if (!empty($db['mobile'])): ?> <!-- start ติดต่อ test : shop/23 : shop/126 -->
			<div class="row">
				<div class="col-12 bold">ติดต่อ</div>
				<div class="col-12 light">
					<div class="d-inline text-gray-dark">โทร</div>
					<div class="d-inline "><?php echo $db['mobile']; ?></div>
				</div>
			</div>
		<?php endif?><!-- end  ติดต่อ-->
		<div class="row">
			<?php if ($db['website'] != '' || $db['facebook_url'] != '' || $db['line_id'] != '' || $db['instragram'] != '' || $db['tweeter'] != ''): ?>
				<div class="col-12 bold text-black pt-3">ที่มา</div>
			<?php endif?>
			<?php if ($db['website'] != ''): ?>
				<div class="col-12">
					<a href="<?php echo $db['website']; ?>">
						<i class="fas fa-globe mr-3"></i>
						<?php echo $db['website']; ?>
					</a>
				</div>
			<?php endif?>
			<?php if ($db['facebook_url'] != ''): ?>
				<div class="col-12">
					<a href="<?php echo $db['facebook_url']; ?>">
						<i class="fab fa-facebook-square mr-3"></i>
						<?php echo $db['facebook_url']; ?>
					</a>
				</div>
			<?php endif?>
			<?php if ($db['line_id'] != ''): ?>
				<div class="col-12">
					<a href="<?php echo $db['line_id']; ?>">
						<i class="fab fa-line mr-3"></i>
						<?php echo $db['line_id']; ?>
					</a>
				</div>
			<?php endif?>
			<?php if ($db['instragram'] != ''): ?>
				<div class="col-12">
					<a href="<?php echo $db['instragram']; ?>">
						<i class="fab fa-instagram mr-3"></i>
						<?php echo $db['instragram']; ?>
					</a>
				</div>
			<?php endif?>
			<?php if ($db['tweeter'] != ''): ?>
				<div class="col-12">
					<a href="<?php echo $db['tweeter']; ?>">
						<i class="fab fa-twitter mr-3 "></i>
						<?php echo $db['tweeter']; ?>
					</a>
				</div>
			<?php endif?>
		</div>
</div>
<div class="hr_footer_height"></div>

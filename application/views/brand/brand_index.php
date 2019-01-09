<?php //this is view brand_index ?>
<?php //echo "<pre>"; ?>
<?php //print_r($brands);?>
<?php //echo "</pre>"; ?>
<?php //exit;?>

<?php
function crateSrcImage($path, $name) {
	$arr = explode(".", $name);
	if (count($arr) == 2) {
		return 'upload/' . $path . $name;
	} else {
		return 'images/400x400.png';
	}
}
// echo crateSrcImage("ssss", "sss/ss");
// exit;
?>
<div ng-controller="brandController" ng-init="init();">
	<div class="container">
	  	<div class="pt-5 pb-3">
		    <div class="d-flex">
		        <div class="h3 medium text-black">แบรนด์</div>
		    </div>
	  	</div>
	</div>

	<div class="container">
	  	<div class="row pt-5 pb-3 h5 light text-gray1">
			<div ng-repeat="item in brands | limitTo: brandsLimitNow" class="col-4 col-md-2 text-center ">
	    		<a href="{{baseurl}}brand/{{item.brand_id}}">

					<img src="{{item.src}}" class="rounded img-responsive home_brand shadow shadowHover">
				</a>
				<div class="pt-3 pb-5">{{item.name}}</div>
	    	</div>
	  	</div>
	</div>

	<div class="container">
		<div class="row mb-5 p-3 text-right" >
			<div class="box-additional ml-auto px-3 py-1" ng-show="brandsLimitNow < brands.length">
				<div class="cursor-pointer h4 medium w-100 m-0"
				ng-click="brandsLimitNow = (brandsLimitNow + brandsLimitInit)">เพิ่มเติม</div>
			</div>
		</div>
	</div>

</div>


<div class="hr_footer_height"></div>
<!--

<?php foreach ($brands as $item): ?>
	<div class="col-4 col-md-2 text-center ">
		<!-- <?php //print_r($item)?>
		<?php //if (	): ?>

		<?php //endif ?>
		<a href="<?php echo base_url() . 'brand/' . $item['brand_id'] ?>">
			 <img src="https://via.placeholder.com/400x400" class="rounded img-responsive home_brand shadow" alt=" ">
			  $item['path_logo'] + $item['logo_image']
			<img src="<?php echo base_url() . crateSrcImage($item['path_logo'], $item['logo_image']) ?>" class="rounded img-responsive home_brand shadow shadowHover">
		</a>
		<div class="pt-3 pb-5"><?php echo $item['name']; ?></div>
	</div>
<?php endforeach;?> -->
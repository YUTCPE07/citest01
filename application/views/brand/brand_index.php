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
<div class="container">
  <div class="pt-5 pb-3">
      <div class="d-flex">
        <div class="h2 medium">Brands</div>
      </div>
  </div>
</div>

<div class="container">
  	<div class="row pt-5 pb-3">
  		<?php foreach ($brands as $item): ?>
			<div class="col-2 text-center ">
				<!-- <?php //print_r($item)?> -->
				<?php //if (	): ?>

				<?php //endif ?>
	    		<a href="<?php echo base_url() . 'brand/' . $item['brand_id'] ?>">
					<!-- <img src="https://via.placeholder.com/400x400" class="rounded img-responsive home_brand shadow" alt=" "> -->
					 <!-- $item['path_logo'] + $item['logo_image'] -->
					<img src="<?php echo base_url() . crateSrcImage($item['path_logo'], $item['logo_image']) ?>" class="rounded img-responsive home_brand shadow">
				</a>
				<div class="pt-3 pb-5"><?php echo $item['name']; ?></div>
	    	</div>
        <?php endforeach;?>
  	</div>
</div>



<div class="hr_footer_height"></div>



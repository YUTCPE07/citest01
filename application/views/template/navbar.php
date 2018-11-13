<!-- <nav class="navbar navbar-expand-lg bg-secondary fixed-top text-uppercase" id="mainNav"> -->
<div ng-controller="navbarController">
  <nav class="navbar navbar-expand-lg fixed-top shadow navbar-light
    text-uppercase" id="mainNav">
    <div class="container">
      <a class="navbar-brand" href="<?php echo base_url(); ?>">
        <img src="assets/images/template/navbar/logo_mini.png" alt="">
      </a>
      <!-- <a class="navbar-brand js-scroll-trigger" href="<?php //echo base_url(); ?>">Memberin</a> -->
      <button class="navbar-toggler navbar-toggler-right text-uppercase bg-green text-white rounded"
        type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive"
        aria-expanded="false" aria-label="Toggle navigation"><i class="fas fa-bars"></i>
      </button>

      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav mr-auto">
          <li uib-dropdown on-toggle="toggled(open)">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href id="simple-dropdown" uib-dropdown-toggle>
              หมวดหมู่
            </a>
            <div class="dropdown-menu" uib-dropdown-menu aria-labelledby="simple-dropdown">
                <a class="dropdown-item" href="#">Action</a>
                <a class="dropdown-item" href="#">Another action</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#">Something else here</a>
            </div>
          </li>
          <li class="nav-item mx-0 mx-lg-1">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="<?php echo base_url('product?page=1'); ?>">บทความ</a>
          </li>

        </ul>
        <ul class="navbar-nav ml-auto">
          <li class="nav-item mx-0 mx-lg-1">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="<?php echo base_url('product?page=1'); ?>">Product</a>
          </li>
         <!--  <li class="nav-item mx-0 mx-lg-1">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="about">About</a>
          </li> -->
          <!-- <li class="nav-item mx-0 mx-lg-1">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="#contact">Contact</a>
          </li> -->

          <li class="nav-item mx-0 mx-lg-1">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" data-toggle="modal" data-target="#login">Login</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</div>

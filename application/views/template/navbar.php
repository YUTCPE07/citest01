<!-- <div style="height: 50px; width: 50px;">
<img src="https://graph.facebook.com/1933048106773317/picture?type=square" ">
</div> -->

<!-- <nav class="navbar navbar-expand-lg bg-secondary fixed-top text-uppercase" id="mainNav"> -->
<div ng-controller="navbarController" data-ng-init="init();">
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
                <a class="dropdown-item" href="<?php echo base_url(); ?>product?page=1">ALL</a>
                <div ng-repeat="catrogy in catrogy_barnd" >
                  <a class="dropdown-item" href="<?php echo base_url(); ?>product?page=1">{{catrogy.category_name}}</a>
                </div>
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
          <li class="nav-item mx-0 mx-lg-1">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="<?php echo base_url('brand'); ?>">Brand</a>
          </li>
         <!--  <li class="nav-item mx-0 mx-lg-1">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="about">About</a>
          </li> -->


          <li class="nav-item mx-0 mx-lg-1">

            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger cursor-pointer" data-toggle="modal" data-target="#login" ng-show="!isUser" >เข้าสู่ระบบ </a>


            <!-- <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger cursor-pointer"  ng-show="isUser" ng-click="logout()">ออกจากระบบ</a> -->
            <div class="d-inline" ng-show="isUser">
                <img style="width: 35px; height: 35px;" class="rounded-circle shadow bg-white rounded" ng-src="https://graph.facebook.com/{{user.id}}/picture?type=square" >
            </div>
            <li class="d-inline " uib-dropdown on-toggle="toggled(open)" ng-show="isUser" >
              <!-- <a class="navbar-brand" href="<?php echo base_url(); ?>">
                <img src="assets/images/template/navbar/logo_mini.png" alt="">
              </a> -->
              <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger text-green" href id="dropdown-user" uib-dropdown-toggle>
                {{user.name}}
              </a>
              <div class="dropdown-menu" uib-dropdown-menu aria-labelledby="dropdown-user">
                  <a class="dropdown-item" href="<?php echo base_url(); ?>store">รายการสิทธิ์ของฉัน</a>
                  <a class="dropdown-item" href="<?php echo base_url(); ?>store">ประวัติการใช้สิทธิ์</a>
                  <a class="dropdown-item" href="<?php echo base_url(); ?>profile">ข้อมูลส่วนตัว</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="#">ตั้งค่า</a>
                  <a class="dropdown-item text-danger" href="" ng-click="logout()">ออกจากระบบ</a>
              </div>
            </li>
          </li>


        </ul>
      </div>
    </div>
  </nav>
</div>

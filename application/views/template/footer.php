<footer>
  <div class="lineBackToTop bg-red">
    <div class="container">
      <div class="lineBackToTop-box " onclick="backToTop();">
        <div class="lineBackToTop-box-text text-white" >Back To Top</div>
        <div class="lineBackToTop-box-img" >
          <img src="<?php echo base_url('assets/images/template/footer/arrow-up.png'); ?>">
        </div>
      </div>
    </div>
  </div>

    <div class="footer">
      <div class="container">
        <div class="footer-row">
            <div class="footer-col">
              <div class="footer-col-head regular">MemberIn</div>
              <div class="footer-col-row">
                  <a class="footer-col-col light " href="<?php echo base_url('/aboutus'); ?>">
                    เกี่ยวกับเรา
                  </a>
                  <a class="footer-col-col light" href="<?php echo base_url('/policy'); ?>">
                    นโยบายความเป็นส่วนตัวและความปลอดภัย
                  </a>
                  <a class="footer-col-col light" href="<?php echo base_url('/termsofuse'); ?>">
                    ข้อตกลงและเงื่อนไขของผู้ใช้งาน
                  </a>
                  <a class="footer-col-col light" href="https://www.memberin.com/contact.php">
                    ติดต่อเรา
                  </a>
              </div>
            </div>
            <div class="footer-col">
                <div class="footer-col-head regular">Membership</div>
                <div class="footer-col-row">
                  <a class="footer-col-col light" href="https://www.memberin.com/reason.php">
                    เหตุผลที่ควรสร้างสมาชิกดิจิตอล
                  </a>
                  <a class="footer-col-col light" href="https://www.memberin.com/welcome.php">
                    การต้อนรับ
                  </a>
                  <a class="footer-col-col light" href="https://www.memberin.com/membercare.php">
                    การดูแลสมาชิกอย่างมีความรับผิดชอบ
                  </a>
                </div>
            </div>
            <div class="footer-col">
              <div class="footer-col-contactHead regular">ติดต่อเรา</div>
              <div class="footer-col-contactBox">
                <div class="footer-col-contactBox-imgSocial mail this_link">
                  <img class="" src="<?php echo base_url('assets/images/template/footer/mail.png'); ?>">
                </div>
                <div class="footer-col-contactBox-textSocial light">bd@memberin.com</div>
              </div>
              <div class="footer-col-contactBox">
                <div class="footer-col-contactBox-imgSocial line this_link">
                   <img class="" src="<?php echo base_url('assets/images/template/footer/line.png'); ?>">
                </div>
              <div class="footer-col-contactBox-textSocial light">@memberin</div>
              </div>
              <div class="footer-col-row-contactQrRow">
                <div class="footer-col-row-contactQrRow-imgQrCode">
                  <img class="qr_code" src="<?php echo base_url('assets/images/template/footer/QR_code.jpg'); ?>">
                </div>
              </div>
              <div class="footer-col-row-contactOther">
                <div class="footer-col-col-contactOther-imgSocial this_link">
                    <img class="facebook" src="<?php echo base_url('assets/images/template/footer/fb.png'); ?>">
                </div>
                <div class="footer-col-col-contactOther-imgSocial this_link">
                    <img class="youtube" src="<?php echo base_url('assets/images/template/footer/youtube.png'); ?>">
                </div>
              </div>
            </div>
        </div>
      </div>
    </div>
</footer>

<div>
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
    <script src="<?php echo base_url('assets/js/angular/loginController.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/angular/productController.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/angular/navbarController.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/angular/indexService.js') ?>"></script>
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
</div>

</body>
</html>
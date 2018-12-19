<!-- <?php //img silde show in home ?> -->
<div ng-controller="homeController">
    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
      <ol class="carousel-indicators">
        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
        <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
        <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
      </ol>
      <div class="carousel-inner">
       <!--  <div class="carousel-item active img-head">
          <img class="d-block w-100" src="assets/images/home/slide_head (1).jpeg" alt="First slide">
        </div >-->
        <div class="carousel-item img-head active">
          <img class="d-block w-100" src="assets/images/home/slide_head_custom(16-7)_01.jpg" alt="First slide">
        </div>
        <div class="carousel-item img-head">
          <img class="d-block w-100" src="assets/images/home/slide_head_custom(16-7)_02.jpg" alt="Second slide">
        </div>
        <div class="carousel-item img-head">
          <img class="d-block w-100" src="assets/images/home/slide_head_custom(16-7)_03.jpg" alt="Third slide">
        </div>
      </div>
      <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>
    <div class="container">
      <div class="pt-5 pb-3">
          <div class="d-flex">
            <div class="h2 medium " ng-if="isUser">สวัสดี {{user.name}}</div>
            <div class="h2 medium " ng-if="!isUser">Memberin</div>
          </div>
          <div class="d-flex">
              <div class="h4 light" ng-if="isUser"> ยินดีต้อนรับเข้าสู่ memberin มาช้อปปิ้งสินค้าโปรโมชัน และ รับสิทธิพิเศษกัน</div>
              <div class="h4 light" ng-if="!isUser"> ยินดีต้อนรับ มาช้อปปิ้งสินค้าโปรโมชัน และ รับสิทธิพิเศษกัน</div>
          </div>
      </div>
    </div>

    <div class="container">
      <div class="d-flex">
        <div class="h4 medium">ใหม่ล่าสุด</div>
      </div>
      <div class="d-flex">
          <div class="h4 light">ขอเเนะนำสินค้า โปรโมชั่น สิทธิ์สมาชิกใหม่ล่าสุด</div>
      </div>
    </div>

      <?php //echo '<pre>'; ?>
      <?php //print_r($pd_Recommend); ?>


    <div class="container content1 p-2">
        <div class="swiper-container swiperProducts">
          <div class="swiper-wrapper"></div>
          <!-- Add Pagination -->
          <!-- <div class="swiper-pagination"></div> -->
          <!-- Add Arrows -->
          <!-- <div class="swiper-button-next"></div>
          <div class="swiper-button-prev"></div> -->
        </div>
        <!-- <p class="append-buttons"> -->

      </p>
    </div>
      <!-- <img src="https://picsum.photos/400/400?random&t=4" class="rounded img-responsive home_brand shadow" alt="image LOGO"> -->

    <div class="bg-greensmoot text-light pt-2 pb-5 mt-5">
      <div class="container">
          <div class="h4 medium pt-5 pb-3">แบรนด์แนะนำ</div>
            <!-- start row -->
              <div class="swiper-container swiperBrands">
                <div class="swiper-wrapper"></div>
                <!-- Add Pagination -->
                <!-- <div class="swiper-pagination"></div> -->
                <!-- Add Arrows -->
                <!-- <div class="swiper-button-next"></div> -->
                <!-- <div class="swiper-button-prev"></div> -->
              </div>
              <!-- <p class="append-buttons"> -->
            <!-- end row -->

          <div class="d-flex pt-5 pb-3">
            <div class="h4 medium">ใหม่ล่าสุด</div>
          </div>
          <div class="d-flex">
              <div class="h4 light">ขอเเนะนำสินค้า โปรโมชั่น สิทธิสมาชิกให้ทุกคนได้เลือกช้อป</div>
          </div>
          <!-- start row -->

            <div class="swiper-container swiperProducts">
              <div class="swiper-wrapper"></div>
              <!-- Add Pagination -->
              <!-- <div class="swiper-pagination"></div> -->
              <!-- Add Arrows -->
              <!-- <div class="swiper-button-next"></div> -->
              <!-- <div class="swiper-button-prev"></div> -->
            </div>
              <!-- <p class="append-buttons"></p> -->
      </div> <!-- end container-->
    </div> <!-- end bg-greensmoot -->
</div>




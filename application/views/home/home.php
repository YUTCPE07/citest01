<?php //img silde show in home ?>

<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
  <ol class="carousel-indicators">
    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
  </ol>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img class="d-block w-100" src="https://picsum.photos/900/500?random&t=1" alt="First slide">
    </div>
    <div class="carousel-item">
      <img class="d-block w-100" src="https://picsum.photos/900/500?random&t=2" alt="Second slide">
    </div>
    <div class="carousel-item">
      <img class="d-block w-100" src="https://picsum.photos/900/500?random&t=3" alt="Third slide">
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

  <div class="p-5">
      <div class="d-flex">
        <h2>สวัสดี เเครอท</h2>
      </div>
      <div class="d-flex">
          <h6> ยินดีต้อนรับเข้าสู่ memberin</h6>
      </div>
  </div>
</div>

  
<div class="container">
  <div class="d-flex">
    <h3>ใหม่ล่าสุด</h3>
  </div>
  <div class="d-flex">
      <h6>ขอเเนะนำสินค้า ใหม่ล่าสุด</h6>
  </div>
</div>

<div class="container">
  <div class="row">

    <?php //product silde ?>
    <div class="col-md-4 productHover">
      <div class="card shadow mb-3 mt-5" style="max-width: 180rem;" >
          <img class="rounded-circle shadow-sm img-responsive logo-brand" 
            src="http://placehold.it/50x50">
          <img class="card-img-top" src="https://picsum.photos/900/500?random&t=2" >
          <div class="text-dark">
            <h5 class="card-title m-1">{{product.coup_Name}}</h5>
            <div class="row m-1">
                <div class="col-4"></div>
                <div class="col-4 text-right" style="text-decoration: line-through;"><small>3000฿</small></div>
                <div class="col-4 text-right text-danger"><h5>500฿</h5></div>
            </div>
            <div class="row m-1 mt-2" style="font-size: 0.3rem;">     
                    <div class="col-3"><small >
                        <i class="fas fa-dollar-sign"></i> 5</small>
                    </div>
                    <div class="col-5 text-center text-warning " >
                      <i class="fa fa-star fa-xs"></i>
                      <i class="fa fa-star fa-xs"></i>
                      <i class="fa fa-star fa-xs"></i>
                      <i class="fa fa-star fa-xs"></i>
                      <i class="fa fa-star fa-xs"></i>
                    </div>
                    <div class="col-4 text-right"><small>ขายเเล้ว 20</small></div>
            </div>
          </div>
        </div>
    </div>

    <div class="col-md-4 productHover">
      <div class="card shadow mb-3 mt-5" style="max-width: 180rem;" >
          <img class="rounded-circle shadow-sm img-responsive logo-brand" 
            src="http://placehold.it/50x50">
          <img class="card-img-top" src="https://picsum.photos/900/500?random&t=5" >
          <div class="text-dark">
            <h5 class="card-title m-1">{{product.coup_Name}}</h5>
            <div class="row m-1">
                <div class="col-4"></div>
                <div class="col-4 text-right" style="text-decoration: line-through;"><small>3000฿</small></div>
                <div class="col-4 text-right text-danger"><h5>500฿</h5></div>
            </div>
            <div class="row m-1 mt-2" style="font-size: 0.3rem;">     
                    <div class="col-3"><small >
                        <i class="fas fa-dollar-sign"></i> 5</small>
                    </div>
                    <div class="col-5 text-center text-warning " >
                      <i class="fa fa-star fa-xs"></i>
                      <i class="fa fa-star fa-xs"></i>
                      <i class="fa fa-star fa-xs"></i>
                      <i class="fa fa-star fa-xs"></i>
                      <i class="fa fa-star fa-xs"></i>
                    </div>
                    <div class="col-4 text-right"><small>ขายเเล้ว 20</small></div>
            </div>
          </div>
        </div>
    </div>

    <div class="col-md-4 productHover">
      <div class="card shadow mb-3 mt-5" style="max-width: 180rem;" >
          <img class="rounded-circle shadow-sm img-responsive logo-brand" 
            src="http://placehold.it/50x50">
          <img class="card-img-top" src="https://picsum.photos/900/500?random&t=4" >
          <div class="text-dark">
            <h5 class="card-title m-1">{{product.coup_Name}}</h5>
            <div class="row m-1">
                <div class="col-4"></div>
                <div class="col-4 text-right" style="text-decoration: line-through;"><small>3000฿</small></div>
                <div class="col-4 text-right text-danger"><h5>500฿</h5></div>
            </div>
            <div class="row m-1 mt-2" style="font-size: 0.3rem;">     
                    <div class="col-3"><small >
                        <i class="fas fa-dollar-sign"></i> 5</small>
                    </div>
                    <div class="col-5 text-center text-warning " >
                      <i class="fa fa-star fa-xs"></i>
                      <i class="fa fa-star fa-xs"></i>
                      <i class="fa fa-star fa-xs"></i>
                      <i class="fa fa-star fa-xs"></i>
                      <i class="fa fa-star fa-xs"></i>
                    </div>
                    <div class="col-4 text-right"><small>ขายเเล้ว 20</small></div>
            </div>
          </div>
        </div>
    </div>


  </div>    
</div>
<div>
  
<div class="mt-5 mb-5 bg-greensmoot text-light">
  <div class="container">
      <h3 class="pt-5 pb-3">แบรนด์แนะนำ</h3>
      <!-- start row -->
      <div class="row">
          <div class="col-md-2">
            <img src="https://picsum.photos/400/400?random&t=4" class="rounded img-responsive home_brand shadow" alt="image LOGO">
          </div>
          <div class="col-md-2">
            <img src="https://picsum.photos/400/400?random&t=4" class="rounded img-responsive home_brand shadow" alt="image LOGO">
          </div>
          <div class="col-md-2">
            <img src="https://picsum.photos/400/400?random&t=4" class="rounded img-responsive home_brand shadow" alt="image LOGO">
          </div>
          <div class="col-md-2">
            <img src="https://picsum.photos/400/400?random&t=4" class="rounded img-responsive home_brand shadow" alt="image LOGO">
          </div>
          <div class="col-md-2">
            <img src="https://picsum.photos/400/400?random&t=4" class="rounded img-responsive home_brand shadow" alt="image LOGO">
          </div>
          <div class="col-md-2">
            <img src="https://picsum.photos/400/400?random&t=4" class="rounded img-responsive home_brand shadow" alt="image LOGO">
          </div>

      </div><!-- end row -->

      <div class="d-flex pt-5 pb-3">
        <h3>ใหม่ล่าสุด</h3>
      </div>
      <div class="d-flex">
          <h6 class="lead">ขอเเนะนำสินค้า โปรโมชั่น สิทธิ์สมาชิกให้ทุกคนได้เลือกช้อป</h6>
      </div>
      <!-- start row -->
      <div class="row">

          <div class="col-md-4 productHover">
              <div class="card shadow mb-3 mt-5" style="max-width: 180rem;" >
                  <img class="rounded-circle shadow-sm img-responsive logo-brand" 
                      src="http://placehold.it/50x50">
                  <img class="card-img-top" src="https://picsum.photos/900/500?random&t=4" >
                  <div class="text-dark">
                      <h5 class="card-title m-1">{{product.coup_Name}}</h5>
                      <!-- start row1 -->
                      <div class="row m-1">
                          <div class="col-4"></div>
                          <div class="col-4 text-right" style="text-decoration: line-through;"><small>3000฿</small></div>
                          <div class="col-4 text-right text-danger"><h5>500฿</h5></div>
                      </div><!-- end row1 -->
                      
                      <!-- start row2 -->
                      <div class="row m-1 mt-2" style="font-size: 0.3rem;">     
                              <div class="col-3"><small ><i class="fas fa-dollar-sign"></i> 5</small></div>
                              <div class="col-5 text-center text-warning " >
                                <i class="fa fa-star fa-xs"></i>
                                <i class="fa fa-star fa-xs"></i>
                                <i class="fa fa-star fa-xs"></i>
                                <i class="fa fa-star fa-xs"></i>
                                <i class="fa fa-star fa-xs"></i>
                              </div>
                              <div class="col-4 text-right"><small>ขายเเล้ว 20</small></div>
                      </div> <!-- end row2 -->
                  </div> <!-- end text dark -->
              </div> <!-- end card shadow -->
          </div> <!-- end col-md-4 productHover -->

          <div class="col-md-4 productHover">
              <div class="card shadow mb-3 mt-5" style="max-width: 180rem;" >
                  <img class="rounded-circle shadow-sm img-responsive logo-brand" 
                      src="http://placehold.it/50x50">
                  <img class="card-img-top" src="https://picsum.photos/900/500?random&t=4" >
                  <div class="text-dark">
                      <h5 class="card-title m-1">{{product.coup_Name}}</h5>
                      <!-- start row1 -->
                      <div class="row m-1">
                          <div class="col-4"></div>
                          <div class="col-4 text-right" style="text-decoration: line-through;"><small>3000฿</small></div>
                          <div class="col-4 text-right text-danger"><h5>500฿</h5></div>
                      </div><!-- end row1 -->
                      
                      <!-- start row2 -->
                      <div class="row m-1 mt-2" style="font-size: 0.3rem;">     
                              <div class="col-3"><small ><i class="fas fa-dollar-sign"></i> 5</small></div>
                              <div class="col-5 text-center text-warning " >
                                <i class="fa fa-star fa-xs"></i>
                                <i class="fa fa-star fa-xs"></i>
                                <i class="fa fa-star fa-xs"></i>
                                <i class="fa fa-star fa-xs"></i>
                                <i class="fa fa-star fa-xs"></i>
                              </div>
                              <div class="col-4 text-right"><small>ขายเเล้ว 20</small></div>
                      </div> <!-- end row2 -->
                  </div> <!-- end text dark -->
              </div> <!-- end card shadow -->
          </div> <!-- end col-md-4 productHover -->

          <div class="col-md-4 productHover">
              <div class="card shadow mb-3 mt-5" style="max-width: 180rem;" >
                  <img class="rounded-circle shadow-sm img-responsive logo-brand" 
                      src="http://placehold.it/50x50">
                  <img class="card-img-top" src="https://picsum.photos/900/500?random&t=4" >
                  <div class="text-dark">
                      <h5 class="card-title m-1">{{product.coup_Name}}</h5>
                      <!-- start row1 -->
                      <div class="row m-1">
                          <div class="col-4"></div>
                          <div class="col-4 text-right" style="text-decoration: line-through;"><small>3000฿</small></div>
                          <div class="col-4 text-right text-danger"><h5>500฿</h5></div>
                      </div><!-- end row1 -->
                      
                      <!-- start row2 -->
                      <div class="row m-1 mt-2" style="font-size: 0.3rem;">     
                              <div class="col-3"><small ><i class="fas fa-dollar-sign"></i> 5</small></div>
                              <div class="col-5 text-center text-warning " >
                                <i class="fa fa-star fa-xs"></i>
                                <i class="fa fa-star fa-xs"></i>
                                <i class="fa fa-star fa-xs"></i>
                                <i class="fa fa-star fa-xs"></i>
                                <i class="fa fa-star fa-xs"></i>
                              </div>
                              <div class="col-4 text-right"><small>ขายเเล้ว 20</small></div>
                      </div> <!-- end row2 -->
                  </div> <!-- end text dark -->
              </div> <!-- end card shadow -->
          </div> <!-- end col-md-4 productHover -->
      </div><!-- end row -->
  </div> <!-- end container-->
</div> <!-- end bg-greensmoot -->



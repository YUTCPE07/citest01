
<div class="modal fade" id="login" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" ng-controller="loginController">
<!-- <div class="pt-5 mt-5" id="login" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"> -->
  <div class="modal-dialog" role="document" ng-controller="loginController" id="modal-login">

    <div class="modal-content" ng-show="loginFrom">
      <div class="modal-header">
        <div class="modal-title h4 medium" id="exampleModalLabel">
          <i class="fas fa-user-alt pr-2"></i>เข้าสู่ระบบ
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <!-- <span aria-hidden="true">&times;</span> -->
          <span class="text-danger" aria-hidden="true">x</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group row ">
          <label for="emailOrPhone" class="col-4 col-form-label text-right">อีเมลล์/เบอร์โทร</label>
          <div class="col-8 text-right">
            <!-- placeholder="อีเมลล์/เบอร์โทร" -->
            <input type="text" class="form-control input-primary" id="emailOrPhone" autofocus>
          </div>
        </div>
        <div class="form-group row">
          <label for="password" class="col-4 col-form-label text-right">รหัสผ่าน</label>
          <div class="col-8 text-right">
            <input type="password" class="form-control input-primary" id="password">
          </div>
        </div>
        <div class="form-group row">
          <div class="col-12 text-right">
            <a class="" href=""><u>ลืมรหัสผ่าน</u></a>
          </div>
        </div>
        <br>
        <div class="form-group text-center">
            <button type="button" class="btn btn-primary btn-lg mr-5">ลงทะเบียน</button>
            <button type="button" class="btn btn-primary btn-lg ml-5">เข้าสู่ระบบ</button>
        </div>
      </div>
      <div class="modal-footer ">
        <!-- <button type="button" class="btn btn-secondary" ng-click="logoutFacebook();" >logout</button> -->
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
        <!-- <div class="d-flex" > -->
          <div class="mr-auto ml-auto">
            <img class="form-row img_login_facebook cursor-pointer" src=" <?php echo base_url('assets\images\login\login_facebook.png') ?>" ng-click="loginFacebook();">
          </div>
        <!-- </div> -->
      </div>
    </div>
  </div>
</div>
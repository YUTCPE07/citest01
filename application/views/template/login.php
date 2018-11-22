
<div class="modal fade" id="login" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" ng-controller="loginController">
<!-- <div class="pt-5 mt-5" id="login" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"> -->
  <div class="modal-dialog" role="document" ng-controller="loginController" id="modal-login">

    <div class="modal-content" ng-show="loginFrom">
      <div class="modal-header bg-green">
        <div class="modal-title h4 medium text-white" id="exampleModalLabel">
          <i class="fas fa-user-alt pr-2"></i>เข้าสู่ระบบ
        </div>
        <div class="close" data-dismiss="modal" aria-label="Close"><i class="far fa-times-circle"></i></i></div>
      </div>
      <div class="modal-body">
        <form ng-submit="loginSubmit()">
          <div class="form-group row pt-3">
            <label for="emailOrPhone" class="col-4 col-form-label text-right">อีเมลล์/เบอร์โทร</label>
            <div class="col-8 text-right">
              <!-- placeholder="อีเมลล์/เบอร์โทร" -->
              <input type="text" class="form-control input-primary" id="emailOrPhone" ng-model="loginInput.emailOrPhone" autofocus>
            </div>
          </div>
          <div class="form-group row">
            <label for="password" class="col-4 col-form-label text-right">รหัสผ่าน</label>
            <div class="col-8 text-right">
              <input type="password" class="form-control input-primary" id="password" ng-model="loginInput.password">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-12 text-right">
              <a class="" href=""><u>ลืมรหัสผ่าน</u></a>
            </div>
          </div>
          <br>
          <div class="form-group text-center">
              <button type="button" class="btn btn-primary btn-lg mr-5" ng-click="loginFrom_register()">ลงทะเบียน</button>
              <input class="btn btn-primary btn-lg active" type="submit" value="เข้าสู่ระบบ" />
              <!-- <button type="button" class="btn btn-primary btn-lg ml-5">เข้าสู่ระบบ</button> -->
          </div>
          <!-- {{loginInput | json}} -->
        </form>
      </div>
      <!-- <div class="modal-footer "> -->
        <!-- <button type="button" class="btn btn-secondary" ng-click="logoutFacebook();" >logout</button> -->
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
        <!-- <div class="d-flex" > -->
          <div class="mr-auto ml-auto pb-3">
            <img class="img_login_facebook cursor-pointer" src=" <?php echo base_url('assets\images\login\login_facebook.png') ?>" ng-click="loginFacebook();">
          </div>
        <!-- </div> -->
      <!-- </div> -->
    </div>
  </div>
</div>
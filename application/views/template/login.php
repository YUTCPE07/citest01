<style type="text/css">

</style>
<div class="modal fade" id="login" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true" ng-controller="loginController">
<!-- <div class="pt-5 mt-5" id="login" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true"> -->
  <div class="modal-dialog" role="document" id="modal-login"
    ng-controller="loginController" ng-init="init();"
    >

    <div class="modal-content" ng-show="loginFrom">
      <div class="modal-header bg-green">
        <div class="modal-title h4 medium text-white" id="loginModalLabel">
          <i class="fas fa-user-alt pr-2"></i>เข้าสู่ระบบ
        </div>
        <div class="close" data-dismiss="modal" aria-label="Close">
          <i class="far fa-times-circle"></i>
        </div>
      </div>
      <div class="modal-body">
        <form class="px-4" name="formLogin" novalidate>
          <div class="form-group row pt-3 mr-2">
            <label for="emailOrPhone" class="col-4 col-form-label text-right">อีเมลล์/เบอร์โทร</label>
            <div class="col-8 text-right">
              <!-- placeholder="อีเมลล์/เบอร์โทร" -->
                <!-- ng-model="loginInput.emailOrPhone" -->
              <input type="text" class="form-control input-primary" id="emailOrPhone" name="username"
                autofocus autocomplete="username"
                ng-model="user.username"
                ng-model-options="{ debounce: 1000 }"
                ng-change="isUsernameMyHave()"
                required
              >
              <div class="text-red text-left h6 mt-2" ng-show="formLogin.username.$dirty && formLogin.username.$invalid">
                <span ng-show="formLogin.username.$error.required">กรุณากรอก อีเมลล์/เบอร์โทร</span>
              </div>
              <div class="text-red text-left h6 mt-2" ng-if="isLoginUsernameFaill">
                <span>
                  อีเมลหรือหมายเลขโทรศัพท์ที่คุณป้อนไม่ตรงกับบัญชีผู้ใช้ใดๆ ลงทะเบียนเพื่อสมัครใช้งาน
                </span>
              </div>
            </div>
          </div>
          <div class="form-group row mr-2">
            <label for="password" class="col-4 col-form-label text-right">รหัสผ่าน</label>
            <div class="col-8 text-right">
              <!-- ng-model="loginInput.password" -->
              <input type="password" class="form-control input-primary" id="password" name="password"
                required
                autocomplete="current-password"
                ng-model="user.password"
                ng-change="isLoginPasswordFaill = false"
              >
              <div class="text-red text-left h6 mt-2" ng-show="formLogin.password.$dirty && formLogin.password.$invalid">กรุณากรอก Password</div>
              <div class="text-red text-left h6 mt-2" ng-if="isLoginPasswordFaill && !formLogin.password.$invalid">รหัสผ่านที่คุณป้อนไม่ถุกต้อง</div>
            </div>
          </div>
          <div class="form-group row mr-2">
            <div class="col-12 text-right">
              <div data-toggle="modal" data-target="#forgetPasswordModal"
                class="text-green cursor-pointer"
                ng-click="forgetPasswordModal()"><u>ลืมรหัสผ่าน</u></div>
            </div>
          </div>
          <br>
          <div class="form-group text-center">
              <button type="button" class="btn btn-primary btn-lg mr-5" ng-click="loginFrom_register()">
                ลงทะเบียน
              </button>
              <button type="button" class="btn btn-primary btn-lg active" ng-click="loginSubmit()"
                ng-disabled="formLogin.username.$invalid || isLoginUsernameFaill ||
                formLogin.password.$invalid
                ">
                เข้าสู่ระบบ
              </button>
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
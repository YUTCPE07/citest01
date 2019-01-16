
<!-- <div class="modal fade" id="registerFrom" role="dialog" aria-labelledby="registerFromLabel" aria-hidden="true" ng-controller="registerController" ng-init="init();"> -->
<div class="" id="registerFrom" role="dialog" aria-labelledby="registerFromLabel" aria-hidden="true" ng-controller="registerController" ng-init="init();">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-green">
        <div class="modal-title h4 medium text-white" id="registerFromLabel">
          <i class="fas fa-user-alt pr-2"></i>ลงทะเบียน
        </div> <!-- end modal-title -->
        <div class="close" data-dismiss="modal" aria-label="Close"><i class="far fa-times-circle"></i></i></div>
      </div> <!-- modal-header -->
      <div class="modal-body mt-4">
        <div class="container-fluid">
          <form ng-submit="registerSubmit()" name="registration" novalidate>
            <div class="form-group row">
              <label for="" class="col-2 offset-2 col-form-label text-right">ชื่อ*</label>
              <div class="col-6 text-right">
                <input type="text" class="form-control input-primary" ng-model="user.fname" autofocus
                ng-required="true" autocomplete="cc-given-name" name="fname">
                <div class="text-left px-lg-4 mt-2 h6 text-red"
                      ng-show="registration.fname.$dirty &&
                                registration.fname.$error.required">
                                กรุณากรอก ชื่อ
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-2 offset-2 col-form-label text-right">สกุล*</label>
              <div class="col-6 text-right">
                <input type="text" class="form-control input-primary" ng-model="user.lname"
                ng-required="true" autocomplete="cc-family-name" name="lname">
                <div class="text-left px-lg-4 mt-2 h6 text-red"
                      ng-show="registration.lname.$dirty &&
                                registration.lname.$error.required">
                                กรุณากรอก นามสกุล
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-2 offset-2 col-form-label text-right">อีเมล*</label>
              <div class="col-6 text-right">
                <input type="text" class="form-control input-primary" ng-model="user.email"
                ng-required="true" autocomplete="email" name="email"
                ng-model-options="{ debounce: 1000 }"
                ng-change="checkEmail();"
                >
                <!-- myForm.input.$error.email -->
                <div class="text-left px-lg-4 mt-2 h6 text-red"
                      ng-show="registration.email.$dirty &&
                                registration.email.$error.required">
                                กรุณากรอกอีเมล
                </div>
                <div class="text-left px-lg-4 mt-2 h6 text-red"
                      ng-show="(registration.email.$dirty && isInputEmailFaill) && !registration.email.$error.required">
                                รูปแบบอีเมลไม่ถูกต้อง <br /> ตัวอย่าง: memberin@gmail.com
                </div>
              </div>
            </div>

            <div class="form-group row">
              <label for="" class="col-2 offset-2 col-form-label text-right">เบอร์โทร*</label>
              <div class="col-6 text-right">
                <input type="number" class="form-control input-primary" name="phone"
                ng-model="user.phone"
                ng-required="true"
                ng-minlength="10"
                ng-maxlength="10"
                >
                <div class="text-left px-lg-4 mt-2 h6 text-red"
                      ng-show="registration.phone.$dirty &&
                                registration.phone.$error.required">
                                กรุณากรอก เบอร์โทร
                </div>
                <div class="text-left px-lg-4 mt-2 h6 text-red"
                      ng-show="((registration.phone.$error.maxlength ||
                                registration.phone.$error.maxlength  ) &&
                                registration.phone.$dirty) ">
                                กรอกเบอร์โทร 10 หลัก
                 </div>
              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-2 offset-2 col-form-label text-right">วันเกิด*</label>
              <div class="col-6">
                <input type="date" class="form-control input-primary" name="brithday"
                  ng-model="user.birthday"
                  ng-required="true"

                >
                <div class="text-left px-lg-4 mt-2 h6 text-red"
                      ng-show="(isInputBrithdayFaill &&
                                !registration.phone.$dirty) ">
                                เลือกวันเกิด
                 </div>
              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-2 offset-2 col-form-label text-right">เพศ*</label>
              <div class="col-6 text-right btn-group">
                <button class="btn btn-primary" type="button"
                  ng-class="{'active':genderActive}"
                  ng-click="
                    genderActive = !genderActive;
                    user.flag_gender = 1;
                    checkInputBrithday(registration.brithday.$viewValue);
                    "
                >ชาย
                </button>
                <button class="btn btn-primary" type="button"
                  ng-class="{'active':!genderActive}"
                  ng-click="
                    genderActive = !genderActive;
                    user.flag_gender = 2;
                    checkInputBrithday(registration.brithday.$viewValue);
                    "
                >หญิง
                </button>
              </div>
            </div>

            <div class="form-group row">
              <label for="" class="col-2 offset-2 col-form-label text-right">รหัสผ่าน*</label>
              <div class="col-6 text-right">
                  <input type="password" class="form-control input-primary" name="password"
                  autocomplete="new-password"
                  ng-model="user.password"
                  ng-required="true"
                  ng-click="checkInputBrithday(registration.brithday.$viewValue)"
                  >
                  <div class="text-left px-lg-4 mt-2 h6 text-red"
                      ng-show="registration.password.$dirty &&
                                registration.password.$error.required">
                                กรุณากรอกรหัสผ่าน
                  </div>

              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-3 offset-1 col-form-label text-right">ยืนยันรหัสผ่าน*</label>
              <div class="col-6 text-right">
                  <input type="password" class="form-control input-primary" name="passwordConfirm"
                  ng-model="user.passwordConfirm"
                  ng-required="true"
                  autocomplete="new-password"
                  ng-click="checkInputBrithday(registration.brithday.$viewValue)"
                  ng-model-options="{ debounce: 1000 }"
                  ng-change="checkPasswordConfirm();"
                  >
                  <div class="text-left px-lg-4 mt-2 h6 text-red"
                      ng-show="registration.passwordConfirm.$dirty &&
                                registration.passwordConfirm.$error.required">
                                กรุณากรอกยืนยันรหัสผ่าน
                  </div>
                  <div class="text-left px-lg-4 mt-2 h6 text-red"
                      ng-show="isPasswordConfirmFaill &&
                                registration.passwordConfirm.$dirty">
                                ยืนยันรหัสผ่าน ไม่ตรงกับ รหัสผ่านก่อนหน้า
                  </div>
              </div>
            </div>

            <div class="form-group text-center">
              <div class="col-6 offset-4">
                  <!-- <button type="button" class="btn btn-primary btn-lg mr-5 active">ลงทะเบียน</button> -->
                  <input class="btn btn-primary btn-lg mr-5 active" type="submit" value="ลงทะเบียน" />
                  <!-- <p>{{user | json }}</p> -->
              </div>
            </div>
            <div class="form-group text-center">
              <div class="col-6 offset-4">
                  <img class="mr-auto ml-auto form-row img_login_facebook cursor-pointer" src=" <?php echo base_url('assets\images\login\login_facebook.png') ?>" ng-click="loginFacebook();" style="width: unset;">
              </div>
            </div>
          </form>
        </div> <!-- end container-fluid -->
      </div> <!-- end modal-body -->
    </div> <!-- end modal-content -->
  </div><!-- end modal-dialog -->
</div><!-- end modal fade -->
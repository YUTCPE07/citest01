
<div class="modal fade" id="registerFrom" role="dialog" aria-labelledby="registerFromLabel" aria-hidden="true" ng-controller="registerController">
<!-- <div class="" id="registerFrom" role="dialog" aria-labelledby="registerFromLabel" aria-hidden="true" ng-controller="registerController"> -->
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
          <form ng-submit="registerSubmit()">
            <div class="form-group row">
              <label for="" class="col-2 offset-2 col-form-label text-right">ชื่อ*</label>
              <div class="col-6 text-right">
                <input type="text" class="form-control input-primary" ng-model="user.fname" autofocus required autocomplete="cc-given-name">
              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-2 offset-2 col-form-label text-right">สกุล*</label>
              <div class="col-6 text-right">
                <input type="text" class="form-control input-primary" ng-model="user.lname" required autocomplete="cc-family-name">
              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-2 offset-2 col-form-label text-right">อีเมล*</label>
              <div class="col-6 text-right">
                <input type="email" class="form-control input-primary" ng-model="user.email" required autocomplete="email">
              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-2 offset-2 col-form-label text-right">เบอร์โทร*</label>
              <div class="col-6 text-right">
                <input type="tel" class="form-control input-primary" maxlength="10" ng-model="user.phone" required autocomplete="tel-national" >
              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-2 offset-2 col-form-label text-right">วันเกิด*</label>
              <div class="col-6 text-right">
                <input type="text" class="form-control input-primary" ng-model="user.birthday" required>
              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-2 offset-2 col-form-label text-right">เพศ*</label>
              <div class="col-6 text-right btn-group">
                <button class="btn btn-primary active" type="button">ชาย</button>
                <button class="btn btn-primary" type="button">หญิง</button>
              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-2 offset-2 col-form-label text-right">รหัสผ่าน*</label>
              <div class="col-6 text-right">
                  <input type="password" class="form-control input-primary" ng-model="user.password" required autocomplete="new-password">
              </div>
            </div>
            <div class="form-group row">
              <label for="" class="col-3 offset-1 col-form-label text-right">ยืนยันรหัสผ่าน*</label>
              <div class="col-6 text-right">
                  <input type="password" class="form-control input-primary" ng-model="user.passwordConfirm" required autocomplete="new-password">
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
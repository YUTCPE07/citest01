
<div class="container py-5" ng-controller="test_loginController" ng-init="init();">
  <div class="row">
    <div class="col-6 ">
    <!-- <form ng-submit="submit()" novalidate> -->
    <form name="formLogin" novalidate>
      <!-- <h4 class="text-red">{{formLogin}}</h4> -->
      <div class="form-group" >
        <label for="Username">Username</label>
        <input type="text" class="form-control" name="username" ng-model="user.username" placeholder="Enter email or phone" ng-keyup="isUsernameMyHave()" required>
        <div class="text-red" ng-show="formLogin.username.$dirty && formLogin.username.$invalid">
          <span ng-show="formLogin.username.$error.required">กรุณากรอก Username</span>
        </div>
        <div class="text-red" ng-if="isLoginUsernameFaill">
          <span>
            อีเมลหรือหมายเลขโทรศัพท์ที่คุณป้อนไม่ตรงกับบัญชีผู้ใช้ใดๆ<br>
            สมัครใช้งานบัญชีผู้ใช้
          </span>

        </div>
      </div>
      <div class="form-group">
        <label for="Password">Password</label>
        <input type="password" class="form-control" name="password" ng-model="user.password" placeholder="Password" required>
        <small ng-show="formLogin.password.$dirty && formLogin.password.$invalid"
          class="form-text text-red">กรุณากรอก Password
        </small>
        <div class="text-red" ng-if="isLoginPasswordFaill">
          <span>
            รหัสผ่านที่คุณป้อนไม่ถุกต้อง <br> ลืมรหัสผ่าน?
          </span>
        </div>
      </div>

      <div class="form-check">
        <input type="checkbox" class="form-check-input" id="exampleCheck1">
        <label class="form-check-label" for="exampleCheck1">Check me out</label>
      </div><br>
      <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
      <button ng-click="submit()" class="btn btn-primary"
        ng-disabled="formLogin.username.$invalid ||  formLogin.password.$invalid">Login</button>
    </form>
  </div>

  <div class="col-6">

    <div><pre>{{formLogin.username | json}}</pre></div>
  </div>
  </div>
</div>



<div class="hr_footer_height"></div>

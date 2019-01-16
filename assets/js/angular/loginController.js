window.fbAsyncInit = function() {
  FB.init({
    appId            : '222937635283574',
    autoLogAppEvents : true,
    xfbml            : true,
    version          : 'v3.2'
  });

  FB.getLoginStatus(function(response) {
    if (response.status === 'connected') {
      // console.log(response.authResponse.accessToken,'response.authResponse.accessToken')
      // console.log('getLoginStatus>connected')
      // console.log(response)
      sessionStorage.user_token = response.authResponse.accessToken;
      FB.api('/me','GET',{"fields":"id,name,email,birthday"},
        function(response) {

          // console.log(responssse,'response')
          response.imgPath = `https://graph.facebook.com/${response.id}/picture?type=square`;
          response.loginBy = `facebook`;
          sessionStorage.user = JSON.stringify(response);
          // sessionStorage.setItem("user_id", response.id);
          // sessionStorage.setItem("user_name", response.name);
          // sessionStorage.setItem("user_email", response.email);
          // sessionStorage.setItem("user_birthday", response.birthday);
          // sendDataToService(response);
        }
      );
    }
    // else {
    //   // console.log('getLoginStatus>else')
    //   // sessionStorage.clear();
    //        // The user isn't logged in to Facebook. You can launch a
    //     // login dialog with a user gesture, but the user may have
    //     // to log in to Facebook before authorizing your application.
    //   sessionStorage.removeItem("user");
    //   sessionStorage.removeItem("user_token");
    // }
  });
};

// sessionStorage.test = 'test';
(function(d, s, id){
 var js, fjs = d.getElementsByTagName(s)[0];
 if (d.getElementById(id)) {return;}
 js = d.createElement(s); js.id = id;
 js.src = "https://connect.facebook.net/en_US/sdk.js";
 fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));


app.controller('loginController', ['$scope','$cookies', 'indexService','$location' ,
  function($scope,$cookies, indexService,$location) {
  // console.log(sessionStorage,'sessionStorage')
  $scope.init = function() {
    $scope.user = {};
    $scope.isLoginUsernameFaill = false;
    $scope.isLoginPasswordFaill = false;
    $scope.checkIsUserSession();
  }

  $scope.checkIsUserSession = function() {
    const app_session = $cookies.get('app_session');
    // console.log(app_session)
    if(app_session != undefined){
      $scope.isUserSession = true;
      isUserLogin(true);
      indexService.unlockData(app_session).then(function(respone){
            // console.log(respone);
            $scope.userSession = respone;
        });
    }else{
      $scope.isUserSession = false;
      isUserLogin(false);
    }
  }

  $scope.forgetPasswordModal = function() {
    $("#login").modal("hide");
  }

  $scope.loginSubmit = function() {
    // console.log($scope.user)
    if ($scope.user.username == undefined || $scope.user.password == undefined) {
      $scope.isLoginPasswordFaill = true;
      return;
    }

    $scope.isMyUser($scope.user);
  }

  $scope.isMyUser = function(user) {
    indexService.getSearchresultPost(baseurl + "Login/isMyUser",user)
      .then(function(respone){
          // $scope.productRecomment = respone.data;
          //console.log(respone) /*data real*/
          if(respone.data.isUser){
            indexService.lockData(respone.data).then(function(dataProtect){
              $cookies.put('app_session',dataProtect);
              $scope.isLoginPasswordFaill = false;
              location.reload();
              // window.location.href = baseurl + "test";
            });
          }else{
            $scope.isLoginPasswordFaill = true;
          }
      }, function(error){
          console.log("Some Error Occured", error);
      });
  }

  $scope.isUsernameMyHave = function() { //action after keyup 1000s
        // console.log($scope.user.username); 
        let username = $scope.user.username;
    indexService.getSearchresultPost(baseurl + "Login/isUsernameMyHave",username)
      .then(function(respone){
          // console.log(respone.data[0].value) /*data real*/
          let isResponeTrue = respone.data[0].value;
          if(isResponeTrue){
            $scope.isLoginUsernameFaill = false;
          }else{
            $scope.isLoginUsernameFaill = true;
          }
      }, function(error){
          console.log("Some Error Occured", error);
      });
  }

	$scope.loginFacebook = function(){
    FB.login(function(response) {
      // console.log(response.authResponse)
      if (response.authResponse) {
        var facebookId = response.authResponse.userID;
        //console.log(facebookId)
        indexService.getSearchresultPost(baseurl + "Login/getUserByFacebookId",facebookId)
        .then(function(respone){
          var data = respone.data[0]; 
          //console.log(data)
          if (data != undefined) {
            data.loginBy = "facebook";
            indexService.lockData(data).then(function(dataProtect){
              $cookies.put('app_session',dataProtect);
              location.reload();
            });
          }else{
            let facebookGraphStr = '/me?fields=birthday,gender,first_name,id,last_name,name,email'; 
            FB.api(facebookGraphStr, function(resNewUser) {
              // console.log(resNewUser,'response')
              addUserFormFacebook(resNewUser);
            });
          }
          //   }else{
          // $scope.isLoginPasswordFaill = true;
          //   }
        }, function(error){
            console.log("Some Error Occured", error);
        });
        // FB.api('/me?fields=id,name,email,birthday', function(response) {
        //   // $("#login").modal("hide");
        //   // response.imgPath = `https://graph.facebook.com/${response.id}/picture?type=square`;
        //   // response.loginBy = `facebook`;
        //   console.log(response,'response')
        //   isUserLogin(true);
        //   // location.reload();
        // });
      } else {
         console.log('$scope.loginFacebook else')//action when close _bank facebook login
      }
    });

    function addUserFormFacebook(user) {
      console.log('addUserFormFacebook')
      console.log(user)
      indexService.getSearchresultPost(baseurl + "Login/insertUserFormFacebook",user)
      .then(function(respone){
          if(respone.data){
            $scope.loginFacebook();
          }else{
            console.log('insertUserFormFacebook error');
          }
      });
    }
    // FB.login(function(response) {
    //   // console.log(response)
    //   // console.log(response.authResponse.accessToken)
    //   sessionStorage.user_token = response.authResponse.accessToken;
    //   if (response.authResponse) {
    //     FB.api('/me?fields=id,name,email,birthday', function(response) {
    //       // $("#login").modal("hide");
    //       response.imgPath = `https://graph.facebook.com/${response.id}/picture?type=square`;
    //       response.loginBy = `facebook`;
    //       console.log(response,'response')
    //       isUserLogin(true);
    //       sessionStorage.user = JSON.stringify(response);
    //       location.reload();
    //     });
    //   } else {
    //       isUserLogin(false);
    //     // console.log('User cancelled login or did not fully authorize.');
    //     sessionStorage.removeItem("user");
    //     sessionStorage.removeItem("user_token");
    //   }
    // });
  }

  // $scope.loginInput = {};
  // $scope.loginSubmit = function(){
  //   var input = $scope.loginInput;
  //   console.log(input)
  //   if (input.emailOrPhone == 'admin' && input.password=='admin') {
  //     console.log('true')
  //       isUserLogin(true);
  //       sessionStorage.user = JSON.stringify({
  //         "id":"0000048106700000",
  //         "name":"admin","email":"admin@admin.com",
  //         "birthday":"01/11/1994",
  //         "imgPath":`${baseurl}assets/images/login/admin.jpg`,
  //         "loginBy":`mainWebsite`
  //       });
  //       location.reload();
  //   }else{
  //     console.log('false')

  //   }
  // }

  $scope.loginFrom_register = function () {
    // console.log("loginFrom_register")
    $('#login').modal('toggle');
    $('#registerFrom').modal('toggle');
  }

  function isUserLogin(value) {
    // console.log(value,'value')
    if(value){
      $scope.loginFrom = false;
      // document.getElementById('btn-login').style.display = "none";
      // document.getElementById('btn-logOut').style.display = 'block';
    }else{
      $scope.loginFrom = true;
      // document.getElementById('btn-login').style.display = 'block';
      // document.getElementById('btn-logOut').style.display = 'none';
    }
  }
	

  // function checkLoginState() {
  //   console.log('click fb login')


  //   FB.getLoginStatus(function(response) {
  //     if (response.status === 'connected') {
  //       console.log('sucess');
  //       // setElements(true);
  //       // testAPI();
  //     }else{
  //       console.log('faill')
  //       // setElements(false);
  //     }
  //   });

  // }

}]);


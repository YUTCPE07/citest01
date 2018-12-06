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


app.controller('loginController', ['$scope', 'indexService' ,function($scope, indexService) {
  // console.log(sessionStorage,'sessionStorage')
  var user = JSON.parse(sessionStorage.getItem("user"));
  console.log(user)
  if(user===null){
    isUserLogin(false)
  }else{
    isUserLogin(true)
  }

	$scope.loginFacebook = function(){
    FB.login(function(response) {
      // console.log(response)
      // console.log(response.authResponse.accessToken)
      sessionStorage.user_token = response.authResponse.accessToken;
      if (response.authResponse) {
        FB.api('/me?fields=id,name,email,birthday', function(response) {
          // $("#login").modal("hide");
          response.imgPath = `https://graph.facebook.com/${response.id}/picture?type=square`;
          response.loginBy = `facebook`;
          console.log(response,'response')
          isUserLogin(true);
          sessionStorage.user = JSON.stringify(response);
          location.reload();
        });
      } else {
          isUserLogin(false);
        // console.log('User cancelled login or did not fully authorize.');
        sessionStorage.removeItem("user");
        sessionStorage.removeItem("user_token");
      }
    });
  }

  $scope.loginInput = {};
  $scope.loginSubmit = function(){
    var input = $scope.loginInput;
    console.log(input)
    if (input.emailOrPhone == 'admin' && input.password=='admin') {
      console.log('true')
        isUserLogin(true);
        sessionStorage.user = JSON.stringify({
          "id":"0000048106700000",
          "name":"admin","email":"admin@admin.com",
          "birthday":"01/11/1994",
          "imgPath":`${baseurl}assets/images/login/admin.jpg`,
          "loginBy":`mainWebsite`
        });
        location.reload();
    }else{
      console.log('false')

    }
  }

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
	

  function checkLoginState() {
    console.log('click fb login')


    FB.getLoginStatus(function(response) {
      if (response.status === 'connected') {
        console.log('sucess');
        // setElements(true);
        // testAPI();
      }else{
        console.log('faill')
        // setElements(false);
      }
    });

  }

}]);


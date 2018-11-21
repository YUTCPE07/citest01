<!-- <script  type ="text/javascript" >


  function statusChangeCallback(response){

    if (response.status === 'connected') {
      console.log('sucess');
      setElements(true);
      testAPI();
    }else{
      console.log('faill')
      setElements(false);

    }

  }

  function checkLoginState() {
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });
  }

   function logout() {
    FB.logout(function(response) {
       console.log("logout")
    });
  }

  function setElements(isLoginOn) {
    if(isLoginOn){
      document.getElementById('btn-login').style.display = "none";
      document.getElementById('btn-logOut').style.display = 'block';
    }else{
      document.getElementById('btn-login').style.display = 'block';
      document.getElementById('btn-logOut').style.display = 'none';
    }
  }

  function testAPI() {
    FB.api('/me?fields=id,name,email,birthday', function(response) {
      if (response && !response.error) {
        console.log('Successful login');
        console.log(response);
        buildHtml(response);
      }


    });
  }

  function buildHtml(user){
    let profileHtml = `
      <h3>${user.name}</h3>
      <h3>${user.email}</h3>
      <h3>${user.birthday}</h3>
    `;
    document.getElementById('profileHtml').innerHTML = profileHtml;
  }

  window.fbAsyncInit = function() {
    FB.init({
      appId      : '222937635283574',
      cookie     : true,
      xfbml      : true,
      version    : 'v3.2'
    });

    // FB.AppEvents.logPageView();

    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });
  };

   (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "https://connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));

</script>
<div id="profileHtml"></div>
<button id="btn-logOut" class="btn btn-primary" onclick="logout()" >LogOut</button>
<fb:login-button id="btn-login" scope="public_profile,email,user_birthday" onlogin="checkLoginState();">
</fb:login-button> -->
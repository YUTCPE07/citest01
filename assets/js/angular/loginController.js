
app.controller('loginController', ['$scope', 'indexService' ,function($scope, indexService) {
  	
  	$scope.loginFacebook = function(){

  		FB.login(function(res){
  			// console.log(res.authResponse)
  			if(res.authResponse){
  				FB.api('/me?fields=id,name',function(res) {
  					 indexService.loginService(res).success(function(data){
  					 	console.log(data)
  				// 	 	var getData = angular.extend(data);
  				// 	 	console.log(getData);
  					 });
  				// 	 // console.log(res)
  				});
  			}else{
  				console.log('loginFacebook error');
  			}
  		});
  	}

  	$scope.logoutFacebook = function(){

  		FB.logout(function(res) {
   			console.log('log out',res);
		});
	}

  	

}]);
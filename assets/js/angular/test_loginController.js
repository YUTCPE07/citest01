'use strict';
app.controller('test_loginController', ['$scope', '$http','$cookies','indexService', function ($scope, $http,$cookies,indexService) {
	console.log('test_loginController')

	$scope.init = function() {
		// $scope.testDis = true;
		$scope.isLoginUsernameFaill = false;
		$scope.isLoginPasswordFaill = false;
		$scope.user = {};
		// var sessionFormPhp = $('[name=sessionFormPhp]').val();
		// console.log(sessionFormPhp)

		// var favoriteCookie = $cookies.get('myFavorite');
		// Setting a cookie
		// $cookies.put('app_session', 'oatmeal');
		// $cookies.get('app_session')

		// ex lockData -> '111' -> 'IjExMSI='
		// indexService.lockData('111').then(function(respone){
	 	//	     console.log(respone); //IjExMSI=
	 	//	});

		// ex unlockData -> 'IjExMSI=' -> '111'
	 	// indexService.unlockData('IjExMSI=').then(function(respone){
	  	//     console.log(respone); //111
	  	// });

	  	$scope.checkIsUserSession();
	}

	$scope.logout = function() {
		$cookies.remove('app_session');
    	window.location.href = baseurl + "test";
	}

	$scope.testCookie = function() {
		var app_session = $cookies.get('app_session');

		console.log("this is cookie on app_session",app_session)
		// indexService.unlockData(app_session).then(function(res){
		// 	console.log(res)
		// });
		
	}

	$scope.checkIsUserSession = function() {
		var app_session = $cookies.get('app_session');
		if(app_session != undefined){
			$scope.isUserSession = true;
		}else{
			$scope.isUserSession = false;
		}
	}



	$scope.isUsernameMyHave = function() { //action after keyup 1000s
      	// console.log($scope.user.username);	
      	let username = $scope.user.username;
		indexService.getSearchresultPost(baseurl + "test/isUsernameMyHave",username)
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

	$scope.submit = function() {
		// console.log($scope.user)
		if ($scope.user.username == undefined || $scope.user.password == undefined) {
			$scope.isLoginPasswordFaill = true;
			return;
		}


		$scope.isMyUser($scope.user);
	}

	$scope.isMyUser = function(user) {
		console.log(user)
		indexService.getSearchresultPost(baseurl + "test/isMyUser",user)
	    .then(function(respone){
	        // $scope.productRecomment = respone.data;
	        //console.log(respone) /*data real*/
	        if(respone.data.isUser){
	        	// this is my user ture
	        	// console.log(JSON.stringify(respone.data))
	        	// let dataObjToStr =  JSON.stringify(respone.data);
	        	// let dataProtect = btoa(dataObjToStr);
	        	indexService.lockData(respone.data).then(function(dataProtect){
		        	$cookies.put('app_session',dataProtect);
		        	// console.log(JSON.parse(respone.data))
		        	$scope.isLoginPasswordFaill = false;
		        	window.location.href = baseurl + "test";
	 			});
	        }else{
				$scope.isLoginPasswordFaill = true;
	        }
	    }, function(error){
	        console.log("Some Error Occured", error);
	    });
	}




	$scope.setUserSession = function(user) {
		$scope.userMaster = user;
	}
	// var user = JSON.parse(sessionStorage.getItem("user"));
	// // console.log(user===null)
	// if(user===null){
	//     $scope.isUser = false;
	// }else{
	//     $scope.isUser = true;
	//     $scope.user = user;
	// }
	// indexService.get().then(function (data) {
        // $scope.productNew = data;
    	// },function(error){ console.log(error); 
    // });



 	//// Define the string
	// var string = 'Hello World!';

	// // Encode the String
	// var encodedString = btoa(string);
	// console.log(encodedString); // Outputs: "SGVsbG8gV29ybGQh"

	// // Decode the String
	// var decodedString = atob(encodedString);
	// console.log(decodedString); // Outputs: "Hello World!"
}]);
'use strict';
app.controller('test_loginController', ['$scope', '$http','indexService', function ($scope, $http,indexService) {
	console.log('test_loginController')

	$scope.init = function() {
		$scope.isLoginUsernameFaill = false;
		$scope.isLoginPasswordFaill = false;
		$scope.user = {};

	}

	$scope.isUsernameMyHave = function() {
		setTimeout(function() {
			console.log($scope.user.username);
		},1000);
	}

	$scope.submit = function() {
		// console.log($scope.user)
		$scope.isMyUser($scope.user);
	}


	$scope.isMyUser = function(user) {
		indexService.getSearchresultPost(baseurl + "test/isMyUser/isMyUser",user)
	    .then(function(respone){
	        // $scope.productRecomment = respone.data;
	        console.log(respone.data) /*data real*/
	        if(respone.data.length){
	        	// this is my user ture
	        }else{
				$scope.isLoginUsernameFaill = true;
	        }
	    }, function(error){
	        console.log("Some Error Occured", error);
	    });
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



}]);
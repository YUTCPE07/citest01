'use strict';
app.controller('homeController', ['$scope', '$http','$cookies','indexService', function ($scope, $http,$cookies,indexService) {
	// console.log('homeController')
	// var user = JSON.parse(sessionStorage.getItem("user"));
	// // console.log(user===null)
	// if(user===null){
	//     $scope.isUser = false;
	// }else{
	//     $scope.isUser = true;
	//     $scope.user = user;
	// }

	$scope.init = function() {

		$scope.checkIsUserSession();
	}

	$scope.checkIsUserSession = function() {
		const app_session = $cookies.get('app_session');
		if(app_session != undefined){
			$scope.isUserSession = true;
			indexService.unlockData(app_session).then(function(respone){
		  	    console.log(respone);
		  	    $scope.userSession = respone;
		  	});
		}else{
			$scope.isUserSession = false;
		}
	}

	// indexService.get().then(function (data) {
        // $scope.productNew = data;
    	// },function(error){ console.log(error); 
    // });



}]);
'use strict';
app.controller('navbarController',['$scope','$http','$location','indexService', function ($scope,$http,$location,indexService) {

	$scope.init = function () {
		var user = JSON.parse(sessionStorage.getItem("user"));
	      	if(user===null){
		    $scope.isUser = false;
		}else{
		    $scope.isUser = true;
		    $scope.user = user;
		}

		indexService.getdata_Catrogy_barnd().then(function (data) {
			console.log(data)
            $scope.catrogy_barnd = data;
        });
	}



	$scope.login = function () {
		console.log('login')
		$('#emailOrPhone').focus();
	}

	$scope.logout = function () {
		FB.logout(function(response) {
		  	sessionStorage.removeItem("user");
        	sessionStorage.removeItem("user_token");
		  	location.reload();
		});
	}

}]);
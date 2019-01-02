'use strict';
app.controller('userProfileController', ['$scope', '$http','indexService', function ($scope, $http,indexService) {
	console.log('userProfileController')

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
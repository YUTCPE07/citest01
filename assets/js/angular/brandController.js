'use strict';
app.controller('brandController', ['$scope', '$http','indexService', function ($scope, $http,indexService) {
	console.log('brandController')

	// var user = JSON.parse(sessionStorage.getItem("user"));
	// // console.log(user===null)
	// if(user===null){
	//     $scope.isUser = false;
	// }else{
	//     $scope.isUser = true;
	//     $scope.user = user;
	// }

	$scope.init = function() {
    	$scope.brandsLimitInit = 24;
    	$scope.brandsLimitNow = $scope.brandsLimitInit;
    	$scope.brandsLimitSee = $scope.brandsLimitInit;
    	$scope.baseurl = baseurl;
    	$scope.getSearchresultPost();
    }

	$scope.checkFormath = function(respone) {
		respone.forEach(function(item,index) {
		  	respone[index].src = $scope.checkBrandsImgSrc(item.path_logo,item.logo_image);
		  	if (respone.length == index+1) {
        		$scope.brands = respone;
		  	}
		});
	}

	$scope.checkBrandsImgSrc = function(path,name) {
		var arr = name.split(".");
		// console.log(arr)
		if (arr.length == 2) {
			return 'upload/' + path + name;
		} else {
			return 'images/400x400.png';
		}
	}

	
	$scope.getSearchresultPost = function() {
		indexService.getSearchresultPost(baseurl + "brand/getAllDataBrand")
	    .then(function(respone){
	        console.log(respone.data) /*data real*/

	        $scope.checkFormath(respone.data);
	        // $scope.brands = respone.data;
	    }, function(error){
	        console.log("Some Error Occured", error);
	    });
	}




}]);


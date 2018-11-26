'use strict';
app.controller('storeController', ['$scope', '$http','indexService', function ($scope, $http,indexService) {
	console.log('storeController')

	$scope.init = function() {
		$scope.myRightPage = true;
		$scope.myHistoryPage = false;
		$scope.myRightExpPage = false;
	}

	// indexService.get().then(function (data) {
        // $scope.productNew = data;
    	// },function(error){ console.log(error); 
    // });

    $scope.selectTab = function(value) {
    	// console.log(value)
    	if(value === 'right'){
    		$scope.myRightPage = true;
    		$scope.myHistoryPage = false;
    		$scope.myRightExpPage = false;
    	}else if (value === 'rightHistory') {
    		$scope.myRightPage = false;
    		$scope.myHistoryPage = true;
    		$scope.myRightExpPage = false;
    	}else if (value === 'rightExp'){
    		$scope.myRightPage = false;
    		$scope.myHistoryPage = false;
    		$scope.myRightExpPage = true;
    	}
    }

}]);
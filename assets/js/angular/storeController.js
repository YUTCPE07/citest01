'use strict';
app.controller('storeController', ['$scope', '$http','indexService', function ($scope, $http,indexService) {
	// console.log('storeController')

	$scope.init = function() {
		$scope.myRightPage = true;
		$scope.myHistoryPage = false;
		$scope.myRightExpPage = false;
	}

	/* get data myRight-------------------------------------------------------------*/
        indexService.getSearchresultPost(baseurl + "User_store/getStoreMyRight",'9')
        .then(function(respone){
            // console.log(respone.data)
            var data = formatDath(respone.data);
            // console.log(data)
            $scope.dataMyRights = data;
        }, function(error){
            console.log("Some Error Occured", error);
        });
    /*----------------------------------------------------------------------------*/






    /* get data myRightHistory-------------------------------------------------------------*/
        indexService.getSearchresultPost(baseurl + "User_store/getStoreMyRightHistory",'9')
        .then(function(respone){
            console.log(respone.data)
            // var data = formatDath(respone.data);
            // console.log(data)
            // $scope.dataMyRights = data;
        }, function(error){
            console.log("Some Error Occured", error);
        });
    /*----------------------------------------------------------------------------*/
    

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

    function formatDath(data) {
        var arrExpDateTime = data[0].date_expire.split(" ");
        var expDate = arrExpDateTime[0];
        var expTime = arrExpDateTime[1];
        var arrDate = expDate.split("-");
        var expDateStr =  arrDate[2]+'/'+arrDate[1]+'/'+arrDate[0];
        data[0].date_expire = expDateStr;
        return data; 
    }
        

}]);
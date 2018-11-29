'use strict';
app.controller('storeController', ['$scope', '$http','indexService','$filter', function ($scope, $http,indexService,$filter) {
	// console.log('storeController')
    

	$scope.init = function() {
		$scope.myRightPage = true;
		$scope.myHistoryPage = false;
		$scope.myRightExpPage = false;

        let user = JSON.parse(sessionStorage.getItem("user"));
        if(user===null){
            $scope.user = "ชื่อทดสอบ(ไม่ได้login)";
            // $scope.isUser = false;
        }else{
            // $scope.isUser = true;
            $scope.user = user;
        }
	}


    $scope.orederLookUp = function (data) {
        console.log(data)
        $scope.dataModal = data;
    }

    $scope.formathDate = function (dateTime) {
        console.log(dateTime)
        // var arrExpDateTime = dateTime.date_expire.split(" ");
        // var expDate = arrExpDateTime[0];
        // var expTime = arrExpDateTime[1];
        // var arrDate = expDate.split("-");
        // dateTime =  arrDate[2]+'/'+arrDate[1]+'/'+arrDate[0];
        return dateTime; 
    }

	/* get data myRight-------------------------------------------------------------*/
        indexService.getSearchresultPost(baseurl + "User_store/getStoreMyRight",'9')
        .then(function(respone){
            // console.log(respone.data) /*data real*/
            var dataForTest = `[{"date_expire":"2017-07-18 11:44:46","date_create":"2017-07-18 11:44:46","count":"1","product_id":"45","product_name":"MemberIn Card","product_image":"card_20170523_140108.jpg","product_imgPath":"50/card_upload/","brand_id":"50","brand_name":"Exhibition"},
            {"date_expire":"2017-07-18 11:44:46","date_create":"2017-07-18 11:44:46","count":"1","product_id":"45","product_name":"MemberIn Card","product_image":"card_20170523_140108.jpg","product_imgPath":"50/card_upload/","brand_id":"50","brand_name":"Exhibition"}]`;/*for many order*/
            // $scope.dataMyRights = respone.data;
            // $scope.dataMyRights = JSON.parse(dataForTest); /*datause*/
        }, function(error){
            console.log("Some Error Occured", error);
        });
    /*----------------------------------------------------------------------------*/






    /* get data myRightHistory-------------------------------------------------------------*/
        indexService.getSearchresultPost(baseurl + "User_store/getStoreMyRightHistory",'9')
        .then(function(respone){
            // console.log(respone.data)
            var data = respone.data;
            // var dataCount = $filter('filter')(data,{coup_CouponID :id});
            // var data = formatDath(respone.data);
            // console.log(data)
            // $scope.dataMyRightHistorys = respone.data; /*data use*/
        }, function(error){
            console.log("Some Error Occured", error);
        });
    /*----------------------------------------------------------------------------*/






    /* get data myRightExp-------------------------------------------------------------*/
        indexService.getSearchresultPost(baseurl + "User_store/getStoreMyRightExp",'9')
        .then(function(respone){
            console.log(respone.data)
            // $scope.dataMyRightHistorys = respone.data; /*data use*/
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

app.filter('cmdate', [
    '$filter', function($filter) {
        return function(input, format) {
            return $filter('date')(new Date(input), format);
        };
    }
]);
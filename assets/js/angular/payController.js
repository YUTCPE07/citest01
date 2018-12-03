'use strict';
app.controller('payController', ['$scope', '$http','indexService','$filter','$window', function ($scope, $http,indexService,$filter,$window) {
	console.log('payController')
    
    $scope.numStepNow = 1;


	/* get data myRight-------------------------------------------------------------*/
        // indexService.getSearchresultPost(baseurl + "User_store/getStoreMyRight",'9')
        // .then(function(respone){
        //     // console.log(respone.data) /*data real*/
        //     var dataForTest = `[{"date_expire":"2017-07-18 11:44:46","date_create":"2017-07-18 11:44:46","count":"1","product_id":"45","product_name":"MemberIn Card","product_image":"card_20170523_140108.jpg","product_imgPath":"50/card_upload/","brand_id":"50","brand_name":"Exhibition"},
        //     {"date_expire":"2017-07-18 11:44:46","date_create":"2017-07-18 11:44:46","count":"1","product_id":"45","product_name":"MemberIn Card","product_image":"card_20170523_140108.jpg","product_imgPath":"50/card_upload/","brand_id":"50","brand_name":"Exhibition"}]`;/*for many order*/
        //     // $scope.dataMyRights = respone.data;
        //     $scope.dataMyRights = JSON.parse(dataForTest); /*datause*/
        // }, function(error){
        //     console.log("Some Error Occured", error);
        // });
    /*----------------------------------------------------------------------------*/


}]);


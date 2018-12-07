'use strict';
app.controller('payController', ['$scope', '$http','indexService','$filter','$window', function ($scope, $http,indexService,$filter,$window) {
	console.log('payController')
    $scope.parseInt = window.parseInt; /*for Angular use math.round()*/

    function getFormattedDate() {
        var date = new Date();
        var str = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate() + " " +  date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
        return str;
    }

    var url_string = window.location.href; 
    var url = new URL(url_string);
    var p_id = url.searchParams.get("p_id");
    var p_num = url.searchParams.get("p_num");
    // console.log(p_id,p_num)
    indexService.getSearchresultPost(baseurl + "product/shop_lookup/shop_lookup",p_id)
    .then(function(respone){
        console.log(respone.data[0]) /*data real*/
        respone.data[0].p_num_select = p_num;
        $scope.action = respone.data[0];
    }, function(error){
        console.log("Some Error Occured", error);
    });




    $scope.numStepNow = 2;
    // $scope.actionRespone = false;
    $scope.actionRespone = false;
    $scope.bankRespone = 'false';
    $scope.userAction = function (value) {
        $("#modalTestUserAction").modal('toggle');
        if (value=='success') {
            $scope.actionRespone = true;
            $scope.bankRespone = true;
        }else{
            $scope.actionRespone = true;
            $scope.bankRespone = false;
        }
    }


    var dateNow = new Date();
    console.log(dateNow,'dateNow')
    $scope.modalData = {};
    $scope.userSelectPay = function (select) {
        $("#modalTestUserAction").modal('toggle'); /*show modal*/
        if (select == 'visaMasterCard') {
            $scope.modalData.bank_name = 'visa MasterCard';
        }else if (select == 'BBL') { /*กรุงเทพ*/
            $scope.modalData.bank_name = 'ธ กรุงเทพ';
        }else if (select == 'SCB') { /*ไทยพานิช*/
            $scope.modalData.bank_name = 'ธ ไทยพานิช';
        }else if (select == 'BAY') { /*กรุงศรี*/
            $scope.modalData.bank_name = 'ธ กรุงศรี';
            $scope.modalData.dateTime = getFormattedDate();
        }
    }

    $scope.payAgian = function(){
        $scope.actionRespone = false;
    }

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


'use strict';

app.factory('indexService', function ($q, $http) {


    return {
        get: function () {
            var deferred = $q.defer(); //เริ่มทำงาน
            $http.get(baseurl + 'Product/Product/getdata').then(function (result) {
                deferred.resolve(result.data); // เสร็จแล้วเอาไปเลย!!
            }, function (error) {
                deferred.reject(error) 
            });
            return deferred.promise; //รอตามสัญญา ขอเวลาอีกไม่นาน
        },
        get_hilight_coupon_trans: function () {
            var deferred = $q.defer(); 
            $http.get(baseurl + 'Product/Product/get_hilight_coupon_trans').then(function (result) {
                deferred.resolve(result.data);
            }, function (error) {
                deferred.reject(error) 
            });
            return deferred.promise; 
        },
        get_rating: function () {
            var deferred = $q.defer(); 
            $http.get(baseurl + 'Product/Product/get_rating').then(function (result) {
                deferred.resolve(result.data);
            }, function (error) {
                deferred.reject(error) 
            });
            return deferred.promise; 
        },
        getdata_Catrogy_barnd: function () {
            var deferred = $q.defer(); //เริ่มทำงาน
            $http.get(baseurl + 'Product/Product/getdata_Catrogy_barnd').then(function (result) {
                deferred.resolve(result.data); // เสร็จแล้วเอาไปเลย!!
            }, function (error) {
                deferred.reject(error) 
            });
            return deferred.promise; //รอตามสัญญา ขอเวลาอีกไม่นาน
        },
		
        
    };
});



// app.service('indexService', function($http){

// 	this.loginService =function(data) {
// 		var formData = data;
// 		var promise = $http({
// 			method: 'POST',
// 			url: baseurl + 'api-facebook-login',
// 			data: formData
// 		});
// 		console.log('ssss')
// 		return promise;
// 	}
// });
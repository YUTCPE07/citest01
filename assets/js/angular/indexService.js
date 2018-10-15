'use strict';

app.factory('indexService', function ($q, $http) {


    return {
        get: function () {
            var deferred = $q.defer(); //เริ่มทำงาน
            $http.get(baseurl + 'product/getdata').then(function (result) {
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
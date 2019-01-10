'use strict';
// app.factory('userService', function() {
//     return {
//         getUserFacebook: function () {
//             var user = JSON.parse(sessionStorage.getItem("user"));
//             console.log(user)
//             return user;
//         },
//     };
// });

app.factory('indexService', function ($q, $http) {

    return {
        getSearchresultPost : function(url,data){
            var defer = $q.defer();
            $http.post(url, data)
            .then(function(data, status, header, config){
                defer.resolve(data);
            }, function(error, status, header, config){
                defer.reject(error);
            });
            return defer.promise;
        },

        getAlldataProduct: function () {
            var deferred = $q.defer(); //เริ่มทำงาน
            $http.get(baseurl + 'Product/Product/getAlldataProduct').then(function (result) {
                deferred.resolve(result.data); // เสร็จแล้วเอาไปเลย!!
            }, function (error) {
                deferred.reject(error) 
            });
            return deferred.promise; //รอตามสัญญา ขอเวลาอีกไม่นาน
        },
        // get_hilight_coupon_trans: function () {
        //     var deferred = $q.defer(); 
        //     $http.get(baseurl + 'Product/Product/get_hilight_coupon_trans').then(function (result) {
        //         deferred.resolve(result.data);
        //     }, function (error) {
        //         deferred.reject(error) 
        //     });
        //     return deferred.promise; 
        // },
        // get_rating: function () {
        //     var deferred = $q.defer(); 
        //     $http.get(baseurl + 'Product/Product/get_rating').then(function (result) {
        //         deferred.resolve(result.data);
        //     }, function (error) {
        //         deferred.reject(error) 
        //     });
        //     return deferred.promise; 
        // },
        getdata_Catrogy_barnd: function () {
            var deferred = $q.defer(); //เริ่มทำงาน
            $http.get(baseurl + 'Product/Product/getdata_Catrogy_barnd').then(function (result) {
                deferred.resolve(result.data); // เสร็จแล้วเอาไปเลย!!
            }, function (error) {
                deferred.reject(error) 
            });
            return deferred.promise; //รอตามสัญญา ขอเวลาอีกไม่นาน
        },
		
        lockData : function(data){
            var defer = $q.defer();
            // data += 'stringify';
            let dataObjToStr =  JSON.stringify(data);
            let dataProtect = btoa(dataObjToStr);
            defer.resolve(dataProtect);
            return defer.promise;
        },

        unlockData : function(data){
            var defer = $q.defer();
            // data += 'parse';
            let dataUnProtect = atob(data);
            let dataStrToObj =  JSON.parse(dataUnProtect);
            defer.resolve(dataStrToObj);
            return defer.promise;
        },
    };
});

// app.factory('svc', function () {
//     var isShowFormSerach;
//     return {
//         setIsShowFormSerach: function(x) {
//             isShowFormSerach = x;
//         },
//         getIsShowFormSerach: function() {
//             return isShowFormSerach;
//         }
//     };
// });

'use strict';
app.controller('homeController', ['$scope', '$http','indexService', function ($scope, $http,indexService) {

	indexService.get().then(function (data) {
        $scope.productNew = data;
    	},function(error){ console.log(error); 
    });



});
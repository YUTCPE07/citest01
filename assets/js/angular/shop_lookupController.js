'use strict';
app.controller('shop_lookupController', ['$scope', '$http','indexService','$location','$filter', '$anchorScroll',
function ($scope, $http,indexService,$location,$filter,$anchorScroll) {

    $scope.scrollTo = function(el){
	 	console.log(el)
 		$location.hash(el);
      	$anchorScroll();
    }
}]);

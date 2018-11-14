'use strict';
app.controller('shop_lookupController', ['$scope', '$http','indexService','$location','$filter', '$anchorScroll',
function ($scope, $http,indexService,$location,$filter,$anchorScroll) {

    $scope.scrollTo = function(el){
    	$anchorScroll(el);
    }


}]);



app.directive("scroll", function ($window) {
    return function(scope, element, attrs) {
        angular.element($window).bind("scroll", function() {
        	var elementBody = angular.element(document.querySelector('body')); 
			var heightBody = elementBody[0].offsetHeight;
			var elementSelect = angular.element(document.querySelector('#focus_buy')); 
			var heightSelect = elementSelect[0].offsetHeight;
			var elementFooter = angular.element(document.querySelector('.footer')); 
			var footerSelect = elementFooter[0].offsetHeight;
			var heightSum = heightBody - (heightSelect + footerSelect+88);
        	// console.log(this.pageYOffset,heightSum)
        	if (this.pageYOffset >= heightSum) {
        		// console.log('scroll to elementSelect.');
                scope.btnBuy = true;
             } else {
                scope.btnBuy = false;
        		// console.log('scroll Other elementSelect.');
             }
            
            scope.$apply();
        });
    };
});
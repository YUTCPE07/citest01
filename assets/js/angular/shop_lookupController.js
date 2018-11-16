'use strict';
app.controller('shop_lookupController', 
['$scope', '$http','indexService','$location','$filter', '$anchorScroll', '$window',
function ($scope, $http,indexService,$location,$filter,$anchorScroll,$window) {

    $scope.scrollTo = function(){
    	$anchorScroll('focus_buy');
    }

 //    $window.onscroll = function(el) {
 //    	var scrollPosiion = document.documentElement.scrollTop;
	//   	// console.log('scroll',scrollPosiion);
	//   	console.log(document);
	// };

}]);



app.directive("scroll", function ($window) {
	// console.log($window.location.pathname)
	// var paramArr = $window.location.pathname.split('/');
	// var param = paramArr[2];
	// console.log(param);
	// if (param == 'shop') {
		return function(scope, element, attrs) {
	        angular.element($window).bind("scroll", function() {
	        	try {
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
				}
				catch(err) {
				    // console.log(err)
				}
	        	
	        });
	    };
	// }else{
		// return this;
	// }
    
});
'use strict';
app.controller('productController', ['$scope', '$http','indexService','$location', 
function ($scope, $http,indexService,$location) {
 	// console.log("this is controller")
 	 //___________________________________________________
    // getProduct
    //____________________________________________________
    // $scope.layoutProduct = true;
    // indexService.get().then(function (data) {
    //     $scope.products = data;
    //     // $scope.layoutProduct = false;
    // 	},function(error){ console.log(error); 
    // });
    // $scope.getProduct = function()
    // {
   		// $scope.baseUrl = new $window.URL($location.absUrl()).origin;
		    // $http({
		    //  method: 'get',
		    //  headers: {'Content-Type': 'application/json'},
		    //  url: baseurl + 'product/getdata'
		    // }).then(function successCallback(response) {
		    //   // Assign response to users object
		    //   $scope.products = response.data;
		    //   // console.table(response.data)
		    //   // pageinationCustom(response.data);
	      
	    // }); 
    // }
    //____________________________________________________
    // pageination controller
    //____________________________________________________

    $scope.currentPage = 0;
    $scope.pageSize = 9;

    indexService.get().then(function (data) {
        $scope.products = data;
        // console.log(data)
        $scope.filterResult = data.length;
        $scope.pageAfterFilter = Math.ceil(data.length/$scope.pageSize)
         $scope.isReadyShow = true; 

    	},function(error){ console.log(error);
    });


	$scope.setPage = function (pageNo) {
    	$scope.currentPage = pageNo;
	};

	$scope.pageChanged = function() {
    	console.log('Page changed to: ' + $scope.currentPage);
	};

	// $scope.maxSize = 5;
	// $scope.bigTotalItems = 175;
	// $scope.bigCurrentPage = 3;
    //____________________________________________________
    // checbox controller
    //____________________________________________________
    $scope.optionArrays = []; //[{type:'1',productCount:'9'},{type:'2',productCount:'3'}]
    $scope.filterProduct = function(product){
    	// console.log(product)
    	// console.log('product.category_brand',product.category_brand)
    	if($scope.optionArrays.length == 0){
    		return ($scope.optionArrays.indexOf(product.category_brand) === -1);
    	}
        return ($scope.optionArrays.indexOf(product.category_brand) !== -1);
    };
    $scope.checkBoxProductType = function(ele){
        $scope.currentPage = 0; 
    	// console.log('Type is length :',ele.value.length) //count product by type select
    	// console.log('key',ele.key,'ischeckbox',ele.confirmed)
		var optionArraysSum = $scope.optionArrays.filter(function(item){
			if(item != ele.key){
			  	return item;
			}
		});

		if(ele.confirmed){
			optionArraysSum.push(ele.key);
			// console.log($scope.optionArrays)
		}

		//generate filterResult (count product after select checkBox)
		if(ele.confirmed){
			$scope.filterResult = $scope.filterResult + ele.value.length;
			if($scope.optionArrays.length == 0){
				$scope.filterResult = ele.value.length;
			}
		}else{
			$scope.filterResult = $scope.filterResult - ele.value.length;
			if($scope.optionArrays.length == 0){
				$scope.filterResult = ele.value.length;
			}
			if($scope.filterResult == 0){
				$scope.filterResult = $scope.products.length;
			}
		}


		$scope.optionArrays = optionArraysSum;
		// console.log($scope.optionArrays)
		$scope.pageAfterFilter = Math.ceil($scope.filterResult/$scope.pageSize)
		// console.log($scope.filterResult)
    }
   	//____________________________________________________
    // seekbar controller
    //____________________________________________________
    $scope.priceSlider = {
        minValue: 200,
        maxValue: 10000,
        showSelectionBar: true,
        options: {
            floor: 0,
            ceil: 12000,
            translate: function(value) {
		      return value + '฿';
		    }
        }
    }

    console.log($scope.priceSlider)
}]);



app.filter('startFrom', function() {
    return function(input, start) {
    	// console.log(input)
        if (!input || !input.length) { return; }
        start = +start; //parse to int
        return input.slice(start);
    }
});
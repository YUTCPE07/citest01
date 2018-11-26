'use strict';
app.controller('productController', ['$scope', '$http','indexService','$location','$filter', 
function ($scope, $http,indexService,$location,$filter) {

    $scope.Math = window.Math; /*for Angular use math.round()*/
    $scope.parseInt = window.parseInt; /*for Angular use math.round()*/
    // console.log(window.location.pathname)


    //___________________________________________________
    // action product click 
    //____________________________________________________

        $scope.lookup = function(data,id,coup_Type) {
            if (data == 'coup' && coup_Type == 'Buy') {
                window.location.href = 'shop/'+id;
            }

            if (data =='coup' && coup_Type == 'Use') {
                window.location.href = 'promotion/'+id;
            }

            if (data =='coup' && coup_Type == 'Member') {
                window.location.href = 'membercard/'+id;
            }

            if (data =='barnd') {
                window.location.href = 'brand/'+id;
            }

        }
    //____________________________________________________



        
 	 //___________________________________________________
    // get Pre Data 
    //____________________________________________________



    

    

    //  ___________________________________________________
    // product > id to count number sell
    // ____________________________________________________
        // var coupon_trans_DB = function(){
        //     indexService.get_hilight_coupon_trans().then(function (data) {
        //         console.log(data);
        //         return data;
        //     },function(error){ 
        //         console.log(error);
        //     });
        // }


        indexService.get_hilight_coupon_trans().then(function (data) {
            // console.log(data);
            //ex 0:{coup_CouponID: "137" ,hico_HilightCouponID: "MBB019846"}
            $scope.coupon_trans = data;

            // createRating(data);

        },function(error){ 
            console.log(error);
        });
    // ____________________________________________________

   

    //  ___________________________________________________
    // product > Rating Star
    // ____________________________________________________
        indexService.get_rating().then(function (data) {
            // console.log(data);
            $scope.ratingDB = data;
        },function(error){ 
            console.log(error);
        });

        $scope.rating = function(data,id,type){
            // console.log(data)
            if (data != undefined && type == "Use") {
                var once = $filter('filter')(data,{coup_CouponID :id});
                 // console.log(once)
                // console.log(once[0].coup_count)
                // console.log(once[0].coup_sum)
                var ratinged = once[0].coup_sum/once[0].coup_count;
                 return createRatingArray(ratinged);
                 // return ratinged;
            }
            return;
        }
        // $scope.rating = function(data,id,type){
        //     return createRatingArray(3);
        // }
        

        function createRatingArray(numStarCalculator){

            var ratingArray = [];
            // var numStarCalculator = 3;
            for (var i = 1; i <= 5; i++) {
                if(numStarCalculator >= 1){
                    ratingArray.push('full');
                    numStarCalculator--;
                }else{
                    if(numStarCalculator >= 0.5){
                        ratingArray.push('href');
                        numStarCalculator -= 0.5;
                    }else{
                        ratingArray.push('noting');
                    }
                }
                if(i == 5){
                    console.log(ratingArray)
                    return ratingArray;
                }
            }
        }

        // $scope.ratingArray = createRatingArray();
        // console.log(createRatingArray())
      
    // ____________________________________________________



    //____________________________________________________
    // pageination& Get All Product controller
    //____________________________________________________
        var url =  new URL(window.location.href);
        var thisPage = url.searchParams.get("page");
        // var url_string = "http://www.example.com/t.html?a=1&b=3&c=m2-m3-m4-m5"; //window.location.href
        // var url = new URL(url_string);
        // var c = url.searchParams.get("c");
        // console.log(c); /*m2-m3-m4-m5*/

        $scope.currentPage = 0;
        $scope.pageSize = 9;
        indexService.getAlldataProduct().then(function (data) {
            $scope.products = data;
            $scope.category_brands = data;
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
    // menu checkbox fillter 
    //____________________________________________________
        
        indexService.getdata_Catrogy_barnd().then(function (data) {
            // console.log(data,'catrogy_barnd')
            $scope.catrogy_barnd = data;
        });
    //____________________________________________________



        function getProductTypeLenghtFromKey(key) {
          
        }

    //____________________________________________________
    // checbox controller
    //____________________________________________________

        $scope.optionArrays = []; //[{type:'1',productCount:'9'},{type:'2',productCount:'3'}]
        $scope.filterProduct = function(product){
        	//console.log(product)
        	// console.log('product.category_brand',product.category_brand)
        	if($scope.optionArrays.length == 0){
        		return ($scope.optionArrays.indexOf(product.category_brand) === -1);
        	}
            return ($scope.optionArrays.indexOf(product.category_brand) !== -1);
        };
        // var koma = 55;
        // var yaua = `sadsadsa${koma}dsadasdsda`;
        // console.log(yaua)
        $scope.menuFilterRowClick = function(key,length){
            // console.log(key,length)
            var jqueryCheckbok = $(`[name='productCheckbox${key}']`);
            var jqueryRow = $(`[name='productRow${key}']`);
            var confirmed;
            // console.log(jqueryCheckbok.hasClass('fa-square'))
            var boxIsCheck = !jqueryCheckbok.hasClass('fa-square');
            // console.log(beforClickBoxIsCheck)
            if(boxIsCheck){
                jqueryCheckbok.toggleClass("fa-check-square fa-square");
                jqueryRow.removeClass('bg-green text-white');
                confirmed = false;
            }else{
                jqueryCheckbok.toggleClass("fa-square fa-check-square");
                jqueryRow.addClass('bg-green text-white');
                confirmed = true;
            }
            $scope.checkBoxProductType(key,confirmed,length);
        }

        $scope.checkBoxProductType = function(key,confirmed,length){
            // console.log('checkBoxProductType')
            // console.log(key)
            // console.log(length)
            // console.log(confirmed)
            $scope.currentPage = 0; 
    		var optionArraysSum = $scope.optionArrays.filter(function(item){
    			if(item != key){
    			  	return item;
    			}
    		});

    		if(confirmed){
    			optionArraysSum.push(key);
    			// console.log($scope.optionArrays)
    		}

    		//generate filterResult (count product after select checkBox)
    		if(confirmed){
    			$scope.filterResult = $scope.filterResult + length;
    			if($scope.optionArrays.length == 0){
    				$scope.filterResult = length;
    			}
    		}else{
    			$scope.filterResult = $scope.filterResult - length;
    			if($scope.optionArrays.length == 0){
    				$scope.filterResult = length;
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

    // console.log($scope.priceSlider)
}]); /*end app.controller 'productController' */

app.filter('ratingFilter', function() {
    return function(data) {
        // console.log(data)
        for (var i = 0; i < data.length; i++) {
            // console.log(data[i].coup_CouoponID)
            if(i=10){
                return;
            }
        }
    return ;
  };
});

app.filter('startFrom', function() {
    return function(input, start) {
    	// console.log(input)
        if (!input || !input.length) { return; }
        start = +start; //parse to int
        return input.slice(start);
    }
});
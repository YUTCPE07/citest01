'use strict';
app.controller('navbarController',['$scope','$rootScope','$http','$location','$window','indexService','$cookies', 
	function ($scope,$rootScope,$http,$location,$window,indexService,$cookies) {

	$scope.isPageProduct = function () {
		var url_string = window.location.href; 
		if (url_string == (baseurl + "product")) {
			return true;
		}else{
			return false;
		}
	}

	$scope.getParameterBy = function(paramiterUrl) {
        var url_string = window.location.href; /*"http://www.example.com/t.html?a=1&b=3&c=m2-m3-m4-m5"*/
        var url = new URL(url_string);
        var paramiterValue = url.searchParams.get(paramiterUrl);
        return paramiterValue;
    }


    $scope.isShowSearchBtnGoback = function() {
    	var search = $scope.getParameterBy('search');
    	// console.log(search)
    	if (search == null) {
    		return false;
    	}else{
    		return true;
    	}
    }

	// $scope.isShowFormSerach = $scope.isPageProduct();

	$scope.toggleSearchUI = function() {
		
		$scope.isShowFormSerach = !$scope.isShowFormSerach;
		// if ($scope.isPageProduct()) {

		// 	// console.log('product page')
		// }else{

		// 	window.location.href = baseurl + "product";
		// 	// console.log('other page')
		// }	
	}

	$scope.keydownEnter = function(event) {
		// console.log('enter')
		if (event.key === "Enter") {
			var inputSearchValue = event.target.value;
			$scope.setUrlSearch(); 
			// $scope.setSesscionSearch(inputSearchValue);
		}
	}

	var jquerySearch = $('.navbarSearch > input');
	$scope.$on("clearForm", function() {
	   // $scope.searchValue = ''; 
	   // console.log("clearForm")
	   // console.log($scope)
	});


	$scope.navbarInput = {};
	$scope.setUrlSearch = function() {
		var value = $scope.navbarInput.searchValue;
		if (value == undefined) {
			$("#inputSerach").focus();
		}

		if (value != undefined) {
			window.location.href = baseurl + "product?search=" + value;
		}
	}

	$scope.setSesscionSearch = function(value) {
		$rootScope.$broadcast('navbarController_searchClick', value);
		// 	// var searchValue = jquerySearch.val().trim();
		// 	// $scope.searchValue = value;
		// 	var url_string = window.location.href; 
		// 	// console.log($scope.searchValue,value) 
		// 	// console.log(searchValue.length) 
		// 	// console.log(baseurl) 
		// 	sessionStorage.setItem("search", value.trim());
		// 	// console.log(url_string)
		// 	// console.log(baseurl)
		// 	if (url_string != (baseurl + "product")) {
		// 		window.location.href = baseurl + "product";
		// 	}else{
		// 		$scope.isShowFormSerach = true;
	}

	// }
	

	// $rootScope.$broadcast('click', 'btnSearch');

	$scope.setSearch = function () {
		var sessionSearch = sessionStorage.getItem("search");
		// console.log(sessionSearch)
		if (sessionSearch != '') {
			// console.log('sessionSearch = ready ')
			// console.log(jquerySearch)
			$scope.searchValue = sessionSearch;
			// jquerySearch.val('sessionSearch');
		}else{
			// console.log('sessionSearch = null ')
		}
	}    


	$scope.checkIsUserSession = function() {
		const app_session = $cookies.get('app_session');
		if(app_session != undefined){
			$scope.isUserSession = true;
			indexService.unlockData(app_session).then(function(respone){
		  	    $scope.userSession = respone;
		  	});
		}else{
			$scope.isUserSession = false;
		}
	}




	// var user = JSON.parse(sessionStorage.getItem("user"));
	$scope.init = function () {
		
        $scope.isReady = false;
        $scope.checkIsUserSession();

  //     	if(user===null){
		//     $scope.isUser = false;
		// }else{
		//     $scope.isUser = true;
		//     $scope.user = user;
		// }

		indexService.getdata_Catrogy_barnd().then(function (data) {
			// console.log(data)
            $scope.catrogy_barnd = data;
            $scope.isReady = true;
            $("#mainNav").toggleClass('d-flex');
        });

		var parameterSearch = $scope.getParameterBy('search');
        if(parameterSearch != null){
			$scope.isShowFormSerach = true;
            $scope.navbarInput.searchValue = parameterSearch;
        }else{
           	$scope.isShowFormSerach = false;
        }

	}

	$scope.login = function () {
		console.log('login')
		$('#emailOrPhone').focus();
	}

	$scope.logout = function () {

		if($scope.userSession.facebook_id == ''){
			$cookies.remove('app_session');
			location.reload();
		}else{
			FB.logout(function(response) {
				$cookies.remove('app_session');
  			  	location.reload();
			});
		}		

		// console.log('logout',user)
		// if(user.loginBy==='facebook'){
		// 	FB.logout(function(response) {
		// 		sessionStorage.removeItem("user");
  		//   			sessionStorage.removeItem("user_token");
  		//   			location.reload();
		// 	});
		// }else{
		//     sessionStorage.removeItem("user");
		// 	sessionStorage.removeItem("user_token");
		// 	location.reload();
		// }
	}
}]);

app.directive('focusMe', ['$timeout', '$parse', function ($timeout, $parse) { /*autofocus focus-me="true" */
    return {
        //scope: true,   // optionally create a child scope
        link: function (scope, element, attrs) {
            var model = $parse(attrs.focusMe);
            scope.$watch(model, function (value) {
                // console.log('value=', value);
                if (value === true) {
                    $timeout(function () {
                        element[0].focus();
                    });
                }
            });
            // to address @blesh's comment, set attribute value to 'false'
            // on blur event:
            element.bind('blur', function () {
                // console.log('blur');
                // scope.$apply(model.assign(scope, false));
            });
        }
    };
}]);
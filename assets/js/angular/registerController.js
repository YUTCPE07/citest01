app.controller('registerController', ['$scope', 'indexService', function($scope, indexService) {
// console.log('register')

	$scope.init = function() {
  		$scope.user = {};
  		$scope.user.flag_gender = 1; // set defalue mem = 1;
		$scope.genderActive = true; //men defalue active
	}

  	$scope.registerSubmit = function () {
  		
    	console.log('registerSubmit')
    	// console.log($scope.user.birthday)
    	console.log($scope.user)
  	}

  	$scope.checkInputBrithday = function(birthday) {
  		// console.log('checkInputBrithday',birthday);
  		if($scope.user.birthday == null || $scope.user.birthday == ''){
  			$scope.isInputBrithdayFaill = true;
  		}else{
  			$scope.user.birthday = birthday;
  			$scope.isInputBrithdayFaill = false;
  		}

  	}

  	$scope.checkPasswordConfirm = function() {
  		if ($scope.user.passwordConfirm === $scope.user.password) {
  			$scope.isPasswordConfirmFaill = false;
  		}else{
  			$scope.isPasswordConfirmFaill = true;
  		}	
  	}

  	$scope.checkEmail = function() {
  		let email = $scope.user.email;
  		var arr = email.split("@");
  		// console.log(arr)
  		if(arr.length === 2){
  			$scope.isInputEmailFaill = false;
  		}else{
  			$scope.isInputEmailFaill = true;
  		}
  	}

}]);


app.controller('registerController', ['$scope', 'indexService','$filter','$cookies', '$location',
function($scope, indexService,$filter,$cookies,$location) {
// console.log('register')

	$scope.init = function() {
  		$scope.user = {};
  		$scope.user.flag_gender = 1; // set defalue mem = 1;
		$scope.genderActive = true; //men defalue active
	}

  	$scope.registerSubmit = function () {
  		$scope.checkInputphone();
  		$scope.checkPasswordConfirm();
    	var user = $scope.user;
    	// user.mobile = user.phone;
    	// delete user.phone;
    	console.log(user)
    	// console.log($scope.user.birthday)
    	if ($scope.isInputPhoneFaill == false && 
    		$scope.isInputBrithdayFaill == false && 
    		$scope.isUsernameMyHave() && 
    		$scope.isPasswordConfirmFaill == false ){
    		console.log("ok")
			// indexService.getSearchresultPost(baseurl + "Login/insertUserFormNormal",user)
	  		//     	.then(function(respone){
		 //        // console.log(respone.data)
		 //        if(respone.data.mesage === 'success'){
		 //        	respone.data.loginBy = 'normal';
		 //            indexService.lockData(respone.data).then(function(dataProtect){
		 //              	$cookies.put('app_session',dataProtect);
		 //              	location.reload();
		 //              	// window.location.href = baseurl + "product";
		 //            });
		 //        }else{
		 //            // console.log('$scope.registerSubmit error');
		 //        }
	 	 //     	});
    	}
  	}

  	$scope.checkInputphone = function() {
  		var mobile = $scope.user.mobile;
  		// console.log(mobile)
  		var regex = /^[0-9]{10,10}$/;
  		$scope.isInputPhoneFaill = !(regex.test(mobile)); //.test return boolean
  		// console.log($scope.isInputPhoneFaill)
	}
  	// $("#inputRigisterUserBirthday").change(function () { //edit err ngModel:datefmt when use angular
  	// 	console.log("sadad")
	  //   $scope.checkInputBrithday();
  	// });

  	$scope.checkInputBrithday = function() {
  		//let birthdayValue = $("#inputRigisterUserBirthday").val(); //edit err ngModel:datefmt when use angular
  		var birthday = $scope.user.birthday;
  		birthday = $filter('date')(birthday, "yyyy-MM-dd");
  		// console.log('checkInputBrithday',birthday);
  		if(birthday == null || birthday == '' || birthday == undefined){
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

  	$scope.isUsernameMyHave = function() { //copy code form loginControler
        let username = $scope.user.email;
	    indexService.getSearchresultPost(baseurl + "Login/isUsernameMyHave",username)
	      .then(function(respone){
	          	// console.log(respone.data[0].value) /*data real*/
		        let isResponeTrue = respone.data[0].value;
		        // console.log(isResponeTrue)
		        if(isResponeTrue > 0){
		        	$scope.isEmailDuplicateFaill = true;
		          	return true;
		        }else{
		        	$scope.isEmailDuplicateFaill = false;
		          	return false;
		        }
	      }, function(error){
	          // console.log("Some Error Occured", error);
	      });
  		
	}

  	$scope.checkEmail = function() {
  		let email = ($scope.user.email).toLowerCase();
		var regex = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
		$scope.isInputEmailFaill = regex.test();
  	}

  	$scope.loginFacebook = function(){ //copy code form loginController
    	FB.login(function(response) {
	      	if (response.authResponse) {
		        var facebookId = response.authResponse.userID;
		        indexService.getSearchresultPost(baseurl + "Login/getUserByFacebookId",facebookId)
		        .then(function(respone){
			        var data = respone.data[0]; 
			        if (data != undefined) {
			        	// console.log('if')
			            data.loginBy = "facebook";
			            indexService.lockData(data).then(function(dataProtect){
			              $cookies.put('app_session',dataProtect);
			              location.reload();
			            });
			        }else{
			        	// console.log('else')
			            let facebookGraphStr = '/me?fields=birthday,gender,first_name,id,last_name,name,email'; 
			            FB.api(facebookGraphStr, function(resNewUser) {
			            	// console.log("add resNewUser",resNewUser)
			              	addUserFormFacebook(resNewUser);
			            });
			        }
		        }, function(error){
		            // console.log("Some Error Occured", error);
		        });
	        
		    } else {
		        // console.log('$scope.loginFacebook else')//action when close _bank facebook login
		    }
	    });
    }
 
    function addUserFormFacebook(user) { //copy code form loginController
	    var birthday = user.birthday;
	    birthday = $filter('date')(new Date(birthday), "yyyy-MM-dd");
	    user.birthday = birthday;
	    console.log(user)
	    indexService.getSearchresultPost(baseurl + "Login/insertUserFormFacebook",user)
      	.then(function(respone){
      		console.log(respone.data)
	        if(respone.data){
	            $scope.loginFacebook();
	        }
	    });
    }

}]);


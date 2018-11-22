app.controller('registerController', ['$scope', 'indexService' ,function($scope, indexService) {
// console.log('register')
  $scope.user = {};
  $scope.registerSubmit = function () {
    console.log('registerSubmit')
    console.log($scope.user)     
  }

}]);


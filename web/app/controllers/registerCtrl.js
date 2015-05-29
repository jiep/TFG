angular.module('app')
.controller("RegisterCtrl", function($scope, Restangular, $location){
  $scope.register = function(newuser){
    var resource = Restangular.all("register");
    resource.post(newuser).then(function(user){
      $location.path("login");
    });
  }

  $scope.login = function(user){
    Login.all('login').post(user).then(function(response) {
      if(response.status == 200){
        $scope.user = response.data;
        $rootScope.user = response.data;
        $location.path("profile");
      }
    });
  }
});

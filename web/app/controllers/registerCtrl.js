angular.module('app')
.controller("RegisterCtrl", function($scope, Restangular, $location){
  $scope.register = function(newuser){
    var resource = Restangular.all("register");
    resource.post(newuser).then(function(user){
      $location.path("login");
    });
  }
});

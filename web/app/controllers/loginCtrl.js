angular.module('app').controller("LoginCtrl", function($scope, Restangular, $rootScope, Login, $location){
  $scope.login = function(user){
    Login.all('login').post(user).then(function(response) {
      if(response.status == 200){
        $scope.user = response.data;
        $rootScope.user = response.data;
        $location.path("profile");
      }
    });
  }

  $scope.gotoStart = function(){
		$location.path("");
	}
  
});

angular.module('app')
.controller("ProfileCtrl", function($scope, $rootScope, Restangular, $location,$route){
  var user_id = $rootScope.user.id;

  var resource = Restangular.all("users/" + user_id + "/graphs");
  resource.getList().then(function(graphs){
    $scope.graphs = graphs;
  });

  $scope.deleteGraph = function(id){
    var resource = Restangular.all("users/" + user_id + "/graphs/" + id);
    resource.remove().then(function(graphs){
      $route.reload();
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

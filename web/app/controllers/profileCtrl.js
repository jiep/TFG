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
});

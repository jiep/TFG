angular.module('app')
.controller("ProfileCtrl", function($scope, $rootScope, Restangular, $location){
  var user_id = $rootScope.user.id;

  var resource = Restangular.all("users/" + user_id + "/graphs");
  resource.getList().then(function(graphs){
    $scope.graphs = graphs;
  });

  $scope.deleteGraph = function(id){
    var resource = Restangular.all("users/" + user_id + "/graphs/" + id);
    resource.remove().then(function(graphs){
      for(var i = 0; i < $scope.graphs.length; i++){
        if($scope.graphs[i].id == id){
          $scope.graphs.splice(i, 0);
        }
      }
    });
  }
});

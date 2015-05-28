angular.module('app')
.controller("ViewCtrl", function($scope, $routeParams, $location, $rootScope, Restangular){
  if($routeParams.id){

    var user_id = $rootScope.user.id;

    var resource = Restangular.one("users/" + user_id + "/graphs/" + $routeParams.id);
    console.log("users/" + user_id + "/graphs/" + $routeParams.id);
    resource.get().then(function(response){
      $scope.competitivityGraph = response.graph;
      $scope.measures = response.measures;

      $scope.cy = cytoscape({
        container: $('#cy')[0],
        elements: $scope.competitivityGraph.elements,
        zoom: 1,
        zoomingEnabled: false,
        layout: {
          name: 'circle'
        },
        style: [
          {
            selector: 'node',
            css: {
              'content': 'data(name)'
            }
          }
        ]
      });
    });

  }else{
    $location.path("");
  }
});

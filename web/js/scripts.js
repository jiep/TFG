var app = angular.module('app', ['chieffancypants.loadingBar', 'restangular', 'ngRoute', 'chart.js']);

app.config(function(cfpLoadingBarProvider) {
  cfpLoadingBarProvider.includeSpinner = true;
});

app.config(function($routeProvider, RestangularProvider) {
  $routeProvider.
  when('/', {
    controller: "MainCtrl",
    templateUrl: 'index.html'
  }).
  otherwise({
    redirectTo: '/'
  });

  RestangularProvider.setBaseUrl('http://localhost/TFG/api/sport/football/bbva');
});

app.controller("MainCtrl", function(Restangular, $scope) {
  var resource = Restangular.all("seasons");
  resource.getList().then(function(seasons) {
    $scope.seasons = seasons;
  });
  resource = Restangular.all("teams");
  resource.getList().then(function(teams) {
    $scope.teams = teams;
  });

  resource = Restangular.all("clasification");
  resource.getList().then(function(clasification) {
    $scope.clasification = clasification;
  });

  $scope.viewCompetitivityGraph = function(season) {
    var competitivityGraph = Restangular.one("competitivity?season=" + season);
    competitivityGraph.get().then(function(competitivityGraph) {
      $scope.competitivityGraph = competitivityGraph;

      cy = cytoscape({
        container: $('#cy')[0],
        elements: $scope.competitivityGraph.elements,
        layout: 'circle'
      });
    });
  };

  var measures = Restangular.one("measures");
  measures.get().then(function(measures) {
    $scope.measures = measures;

    console.log(measures.measures);

    /*$scope.labels = measures.labels;
    $scope.data = measures.measures;*/

    $scope.labels = $scope.measures.labels;

    $scope.data = [$scope.measures.measures];

  });
});
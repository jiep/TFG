var app = angular.module('app', ['chieffancypants.loadingBar', 'restangular', 'ngRoute', 'chart.js']);

app.config(function(cfpLoadingBarProvider) {
  cfpLoadingBarProvider.includeSpinner = true;
});

app.config(function($routeProvider, RestangularProvider) {
  $routeProvider.
  when('/', {
    controller: "MainCtrl",
    templateUrl: 'templates/season.html'
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

  var competitivityGraph = Restangular.one("competitivity");
  competitivityGraph.get().then(function(competitivityGraph) {
    $scope.competitivityGraph = competitivityGraph;

    $scope.cy = cytoscape({
      container: $('#cy')[0],
      elements: $scope.competitivityGraph.elements,
      layout: 'circle'
    });
  });

  var chartLine = Restangular.one("chartLine");
  chartLine.get().then(function(chartLine) {
    $scope.chartLine = chartLine;

    $scope.series = $scope.chartLine.teams;
    $scope.labels = $scope.chartLine.labels;
    $scope.data = $scope.chartLine.datasets;
    $scope.options = {
      responsive: true,
      animation: false,
      bezierCurve: false,
      datasetFill: false
    };
  });

});

app.controller('RadarCtrl', function($scope, Restangular) {
  var measures = Restangular.one("measures");
  measures.get().then(function(measures) {
    $scope.measures = measures;
    $scope.labels = $scope.measures.labels;
    $scope.data = [$scope.measures.measures];
  });
});
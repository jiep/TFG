var app = angular.module('app', ['chieffancypants.loadingBar', 'restangular', 'ngRoute']);

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
});
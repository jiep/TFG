angular.module('app')
.controller("predictionCtrl", function(Restangular, $scope, $routeParams) {

  var season = '';
  if ($routeParams.season) {
    season = $routeParams.season.replace('-', '/');
  }

  var resource = Restangular.all("sport/football/bbva/seasons");
  resource.getList().then(function(seasons) {
    $scope.seasons = seasons;
  });

  resource = Restangular.all("sport/football/bbva/teams");
  resource.getList().then(function(teams) {
    $scope.teams = teams;
  });

  var prediction = Restangular.one("sport/football/bbva/prediction?season=" + season);
  prediction.get().then(function(prediction) {
    $scope.prediction1 = prediction.results1;
    $scope.ranking1 = prediction.ranking1;
    $scope.prediction2 = prediction.results2;
    $scope.ranking2 = prediction.ranking2;
    $scope.prediction3 = prediction.results3;
    $scope.ranking3 = prediction.ranking3;
  });
});

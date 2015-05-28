angular.module('app')
.controller("TeamCtrl", function(Restangular, $scope, $routeParams, $rootScope) {
  var team;
  if ($routeParams.team) {
    team = $routeParams.team;
    $scope.team_id = team;
  }
  var resource = Restangular.all("sport/football/bbva/seasons");
  resource.getList().then(function(seasons) {
    $scope.seasons = seasons;
  });

  resource = Restangular.all("sport/football/bbva/teams");
  resource.getList().then(function(teams) {
    $scope.teams = teams;
  });

  var team_stats = Restangular.one("sport/football/bbva/team/" + team);
  team_stats.get().then(function(stats) {
    $scope.team_stats = stats;
    $rootScope.team_stats = stats;
  });
});

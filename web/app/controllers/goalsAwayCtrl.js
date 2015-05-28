angular.module('app')
.controller("GoalsAwayCtrl", function(Restangular, $location, $scope) {
  var team = $location.url().split("/")[3];

  var team_stats = Restangular.one("sport/football/bbva/team/" + team);
  team_stats.get().then(function(stats) {
    $scope.team_stats = stats;
    $scope.labels = ["Goles a favor", "Goles en contra"];
    $scope.data = [parseFloat($scope.team_stats.away_goals_favor), parseFloat($scope.team_stats.away_goals_contra)];
    $scope.options = {
      responsive: true,
      animation: false,
      datasetFill: false
    };
  });
});

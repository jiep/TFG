angular.module('app').controller("AwayCtrl", function(Restangular, $location, $scope) {
  var team = $location.url().split("/")[3];

  var team_stats = Restangular.one("sport/football/bbva/team/" + team);
  team_stats.get().then(function(stats) {
    $scope.team_stats = stats;
    $scope.labels = ["Partidos ganados", "Partidos empatados", "Partidos perdidos"];
    $scope.data = [parseFloat($scope.team_stats.away_win), parseFloat($scope.team_stats.away_draw), parseFloat($scope.team_stats.away_defeat)];
    $scope.options = {
      responsive: true,
      animation: false,
      datasetFill: false
    };
  });
});

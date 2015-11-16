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
    $scope.prediction = prediction;
  });


  /*function parsePrediction() {
    var pred = Restangular.stripRestangular($scope.prediction);
    var c = [];
    var d = [];
    var headers = ["Local", "Visitante", "1", "X", "2"];
    d.push(headers);
    for (var i = 0; i < 10; i++) {
      c = [];
      c.push(pred[i][3].local);
      c.push(pred[i][4].visitante);
      c.push(pred[i][0].uno);
      c.push(pred[i][1].equis);
      c.push(pred[i][2].dos);
      0
      d.push(c);
    }
    return d;
  }

  var d = new parsePrediction();*/

});

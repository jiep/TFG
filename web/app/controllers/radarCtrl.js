angular.module('app')
.controller('RadarCtrl', function($scope, Restangular, $routeParams) {
  var season = '';
  if ($routeParams.season) {
    season = $routeParams.season.replace('-', '/');
  }
  var measures = Restangular.one("sport/football/bbva/measures?season=" + season);
  measures.get().then(function(measures) {
    $scope.measures = measures;
    $scope.labels = $scope.measures.labels;
    $scope.data = [$scope.measures.measures];
  });
});

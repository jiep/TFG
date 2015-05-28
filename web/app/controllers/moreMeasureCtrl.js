angular.module('app')
.controller('MoreMeasureCtrl', function($scope, Restangular, $routeParams) {
  var season = '';
  if ($routeParams.season) {
    season = $routeParams.season.replace('-', '/');
  }
  var measures = Restangular.one("sport/football/bbva/measures?season=" + season);
  measures.get().then(function(measures) {
    $scope.measuresp = measures;
    $scope.labels = $scope.measuresp.labels_array;
    $scope.series = ["Distribución acumulada del grado normalizado","Distribución acumulada de la fuerza normalizada"]
    $scope.data = [$scope.measuresp.ncdd, $scope.measuresp.ncsd];
    $scope.options = {
      responsive: true,
      animation: false,
      bezierCurve: false,
      datasetFill: false
    };
  });
});

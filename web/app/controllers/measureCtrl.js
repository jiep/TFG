angular.module('app')
.controller('MeasureCtrl', function($scope, Restangular, $routeParams) {
  var season = '';
  if ($routeParams.season) {
    season = $routeParams.season.replace('-', '/');
  }
  var measures = Restangular.one("sport/football/bbva/measures?season=" + season);
  measures.get().then(function(measures) {
    $scope.measuresp = measures;
    $scope.labels = $scope.measuresp.labels_array;
    $scope.series = ["Distribución del grado normalizado","Distribución de la fuerza normalizada"]
    $scope.data = [$scope.measuresp.ndd, $scope.measuresp.nsd];
    $scope.options = {
      responsive: true,
      animation: false,
      bezierCurve: false,
      datasetFill: false
    };
  });
});

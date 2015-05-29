angular.module('app')
.controller("MainCtrl", function(Restangular, $scope, $routeParams, Login, $rootScope, $location) {
  var season = '';
  if ($routeParams.season) {
    season = $routeParams.season.replace('-', '/');
  }
  var resource = Restangular.all("sport/football/bbva/seasons");
  resource.getList().then(function(seasons) {
    $scope.seasons = seasons;
  });
  resource = Restangular.all("sport/football/bbva/teams?season=" + season);
  resource.getList().then(function(teams) {
    $scope.teams = teams;
  });

  var clasification = Restangular.one("sport/football/bbva/clasification?season=" + season);
  clasification.get().then(function(clasification_info) {
    $scope.number_teams = clasification_info.number;
    $scope.clasification = clasification_info.clasification;
  });

  var competitivityGraph = Restangular.one("sport/football/bbva/competitivity?season=" + season);
  competitivityGraph.get().then(function(competitivityGraph) {
    $scope.competitivityGraph = competitivityGraph.data;
    $scope.style = competitivityGraph.style;

    $scope.cy = cytoscape({
      container: $('#cy')[0],
      elements: $scope.competitivityGraph.elements,
      zoom: 1,
      zoomingEnabled: false,
      layout: {
        name: 'circle'
      },
      style: $scope.style
    });
  });

  var chartLine = Restangular.one("sport/football/bbva/chartLine?season=" + season);
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

  $scope.downloadPDF = function() {

    function parseClasification() {
      var clasif = Restangular.stripRestangular($scope.clasification);
      var c = [];
      var d = [];
      var headers = ["#", "Equipo", "PG", "PE", "PP", "GF", "GC", "Puntos"];
      d.push(headers);
      for (var i = 0; i < $scope.number_teams; i++) {
        c = [];
        c.push(clasif[i].posicion);
        c.push(clasif[i].equipo);
        c.push(clasif[i].partidos_ganados);
        c.push(clasif[i].partidos_empatados);
        c.push(clasif[i].partidos_perdidos);
        c.push(clasif[i].goles_favor);
        c.push(clasif[i].goles_contra);
        c.push(clasif[i].puntos);
        0
        d.push(c);
      }
      return d;
    }

    var d = new parseClasification();

    var hist = document.getElementById("canvas2").toDataURL("image/png", 1);

    var dd = {
      content: [{
          columns: [{
            width: '*',
            text: ''
          }, {
            width: 'auto',
            table: {
              headerRows: 1,
              alignment: 'center',
              body: d
            }
          }, {
            width: '*',
            text: ''
          }, ]
        },
        'Cambios de posición', {
          image: hist,
          width: 300,
          height: 300,
        },
        'Grafo de competitividad', {
          image: $scope.cy.png(),
          width: 300,
          height: 300,
        }
      ]
    }

    pdfMake.createPdf(dd).download();
  };

  $scope.login = function(user){
    Login.all('login').post(user).then(function(response) {
      if(response.status == 200){
        $scope.user = response.data;
        $rootScope.user = response.data;
        $location.path("profile");
      }
    });
  }
});

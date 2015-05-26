var app = angular.module('app', ['chieffancypants.loadingBar', 'restangular', 'ngRoute', 'chart.js']);

app.run(function($rootScope) {
  $rootScope.team_stats = [];
});

app.config(function(cfpLoadingBarProvider) {
  cfpLoadingBarProvider.includeSpinner = true;
});

app.config(function($routeProvider, RestangularProvider, $locationProvider) {
  $routeProvider.
  when('/', {
    controller: "MainCtrl",
    templateUrl: 'templates/season.html'
  }).
  when('/bbva/:season', {
    controller: "MainCtrl",
    templateUrl: 'templates/season.html'
  }).
  when('/bbva/team/:team', {
    controller: "TeamCtrl",
    templateUrl: 'templates/team.html'
  }).
  when('/register', {
    controller: "RegisterCtrl",
    templateUrl: 'templates/register.html'
  }).
  otherwise({
    redirectTo: '/'
  });

  $locationProvider.html5Mode(true);

  RestangularProvider.setBaseUrl('http://localhost/TFG/api/sport/football/bbva');
});

app.controller("MainCtrl", function(Restangular, $scope, $routeParams) {
  var season = '';
  if ($routeParams.season) {
    season = $routeParams.season.replace('-', '/');
  }
  var resource = Restangular.all("seasons");
  resource.getList().then(function(seasons) {
    $scope.seasons = seasons;
  });
  resource = Restangular.all("teams?season=" + season);
  resource.getList().then(function(teams) {
    $scope.teams = teams;
  });

  var clasification = Restangular.one("clasification?season=" + season);
  clasification.get().then(function(clasification_info) {
    $scope.number_teams = clasification_info.number;
    $scope.clasification = clasification_info.clasification;
  });

  var competitivityGraph = Restangular.one("competitivity?season=" + season);
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

  var chartLine = Restangular.one("chartLine?season=" + season);
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

    pdfMake.createPdf(dd).open();
  };
});

app.controller('RadarCtrl', function($scope, Restangular, $routeParams) {
  var season = '';
  if ($routeParams.season) {
    season = $routeParams.season.replace('-', '/');
  }
  var measures = Restangular.one("measures?season=" + season);
  measures.get().then(function(measures) {
    $scope.measures = measures;
    $scope.labels = $scope.measures.labels;
    $scope.data = [$scope.measures.measures];
  });
});

app.controller('MeasureCtrl', function($scope, Restangular, $routeParams) {
  var season = '';
  if ($routeParams.season) {
    season = $routeParams.season.replace('-', '/');
  }
  var measures = Restangular.one("measures?season=" + season);
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

app.controller('MoreMeasureCtrl', function($scope, Restangular, $routeParams) {
  var season = '';
  if ($routeParams.season) {
    season = $routeParams.season.replace('-', '/');
  }
  var measures = Restangular.one("measures?season=" + season);
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

app.controller("TeamCtrl", function(Restangular, $scope, $routeParams, $rootScope) {
  var team;
  if ($routeParams.team) {
    team = $routeParams.team;
    $scope.team_id = team;
  }
  var resource = Restangular.all("seasons");
  resource.getList().then(function(seasons) {
    $scope.seasons = seasons;
  });

  resource = Restangular.all("teams");
  resource.getList().then(function(teams) {
    $scope.teams = teams;
  });

  var team_stats = Restangular.one("team/" + team);
  team_stats.get().then(function(stats) {
    $scope.team_stats = stats;
    $rootScope.team_stats = stats;
  });
});

app.controller("LocalCtrl", function(Restangular, $location, $scope) {
  var team = $location.url().split("/")[3];

  var team_stats = Restangular.one("team/" + team);
  team_stats.get().then(function(stats) {
    $scope.team_stats = stats;
    $scope.labels = ["Partidos ganados", "Partidos empatados", "Partidos perdidos"];
    $scope.data = [parseFloat($scope.team_stats.local_win), parseFloat($scope.team_stats.local_draw), parseFloat($scope.team_stats.local_defeat)];
    $scope.options = {
      responsive: true,
      animation: false,
      datasetFill: false
    };
  });


});

app.controller("AwayCtrl", function(Restangular, $location, $scope) {
  var team = $location.url().split("/")[3];

  var team_stats = Restangular.one("team/" + team);
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


app.controller("GoalsLocalCtrl", function(Restangular, $location, $scope) {
  var team = $location.url().split("/")[3];

  var team_stats = Restangular.one("team/" + team);
  team_stats.get().then(function(stats) {
    $scope.team_stats = stats;
    $scope.labels = ["Goles a favor", "Goles en contra"];
    $scope.data = [parseFloat($scope.team_stats.local_goals_favor), parseFloat($scope.team_stats.local_goals_contra)];
    $scope.options = {
      responsive: true,
      animation: false,
      datasetFill: false
    };
  });


});

app.controller("GoalsAwayCtrl", function(Restangular, $location, $scope) {
  var team = $location.url().split("/")[3];

  var team_stats = Restangular.one("team/" + team);
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

app.controller("RegisterCtrl", function($scope){
  
});

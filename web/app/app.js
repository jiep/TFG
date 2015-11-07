var app = angular.module('app', ['chieffancypants.loadingBar', 'restangular', 'ngRoute', 'chart.js', 'ngFileUpload']);

app.run(function($rootScope) {
  $rootScope.team_stats = [];
  $rootScope.graphs = [];
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
  when('/bbva/prediction', {
    controller: "PredictionCtrl",
    templateUrl: 'templates/prediction.html'
  }).
  when('/register', {
    controller: "RegisterCtrl",
    templateUrl: 'templates/register.html'
  }).
  when('/profile', {
    controller: "ProfileCtrl",
    templateUrl: 'templates/profile.html'
  }).
  when('/login', {
    controller: "LoginCtrl",
    templateUrl: 'templates/login.html'
  }).
  when('/new', {
    controller: "NewCtrl",
    templateUrl: 'templates/new.html'
  }).
  when('/view/:id', {
    controller: "ViewCtrl",
    templateUrl: 'templates/view.html'
  }).
  otherwise({
    redirectTo: '/'
  });

  $locationProvider.html5Mode({
    enabled: true,
    requireBase: false
  });

  RestangularProvider.setBaseUrl('api');
});

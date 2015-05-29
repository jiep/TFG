angular.module('app')
.controller("NewCtrl", function($scope, $rootScope, $location){

  var user_id = $rootScope.user.id;

  $scope.uploadData = function(){

    var inputFileImage = document.getElementById("file");

    var file = inputFileImage.files[0];

    var data = new FormData();

    data.append('archivo',file);

    $.ajax({
      url:'api/users/' + user_id + '/graphs',
      type:'POST',
      contentType:false,
      data:data,
      processData:false,
      cache:false,
      success: function(response) {
        $location.path("profile");
	$scope.$apply();
      }

    });
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

	$scope.gotoStart = function(){
		$location.path("");
	}
});

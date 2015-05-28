angular.module('app')
.controller("NewCtrl", function($scope, $rootScope, $location){

  var user_id = $rootScope.user.id;

  $scope.uploadData = function(){
    var inputFileImage = document.getElementById("file");

    var file = inputFileImage.files[0];

    var data = new FormData();

    data.append('archivo',file);

    $.ajax({
      url:'http://localhost/TFG/api/users/' + user_id + '/graphs',
      type:'POST',
      contentType:false,
      data:data,
      processData:false,
      cache:false,
      success: function(response) {
        $location.path("profile");
      }

    });
  };
});

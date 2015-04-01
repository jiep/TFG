<?php

require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

$hostname = "localhost";
$username = "rankings";
$password = "1234";

try {
  $dbh = new PDO("mysql:host=$hostname;dbname=rankings;charset=utf8", $username, $password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
  echo $e->getMessage();
}

$app = new \Slim\Slim();
$app->get('/sport/:sportname/:league/teams', function ($sportname, $league) use ($dbh, $app) {
  try{
    $season = $app->request()->get("season");
    if($season){
      $sql = $dbh->prepare("SELECT DISTINCT equipo as name from rankings where temporada = \"$season\" order by equipo");
    }else{
      $sql = $dbh->prepare("SELECT DISTINCT equipo as name from rankings order by equipo");
    }
    $sql->execute();
    $result = $sql->fetchAll(PDO::FETCH_ASSOC);

    if($result){
      $app->response->status(200);
      $app->contentType('application/json; charset=utf-8');
      $app->response->body(json_encode($result));
    }else{
      $app->response->status(404);
      $app->response->body(json_encode(array("error" => "Resource not found")));
    }
  }catch(PDOException $e){
    echo "Error: " + $e->getMessage();
  }
});


$app->run();

?>

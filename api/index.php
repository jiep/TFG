<?php

require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

$hostname = 'localhost';
$username = 'rankings';
$password = '1234';

try {
    $dbh = new PDO("mysql:host=$hostname;dbname=rankings;charset=utf8", $username, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
}

$app = new \Slim\Slim();

$app->get('/sport/:sportname/:league/teams', function ($sportname, $league) use ($dbh, $app) {
  try {
      $season = $app->request()->get('season');
      if ($season) {
          $sql = $dbh->prepare("SELECT DISTINCT equipo as name from rankings where temporada = \"$season\" order by equipo");
      } else {
          $sql = $dbh->prepare('SELECT DISTINCT equipo as name from rankings order by equipo');
      }
      $sql->execute();
      $result = $sql->fetchAll(PDO::FETCH_ASSOC);

      if ($result) {
          $app->response->status(200);
          $app->contentType('application/json; charset=utf-8');
          $app->response->body(json_encode($result));
      } else {
          $app->response->status(404);
          $app->response->body(json_encode(array('error' => 'Resource not found')));
      }
  } catch (PDOException $e) {
      echo 'Error: ' + $e->getMessage();
  }
});

$app->get('/sport/:sportname/:league/seasons', function ($sportname, $league) use ($dbh, $app) {
  try {
      $sql = $dbh->prepare('SELECT DISTINCT temporada as season from rankings order by temporada desc');
      $sql->execute();
      $result = $sql->fetchAll(PDO::FETCH_ASSOC);

      if ($result) {
          $app->response->status(200);
          $app->contentType('application/json; charset=utf-8');
          $app->response->body(json_encode($result));
      } else {
          $app->response->status(404);
          $app->response->body(json_encode(array('error' => 'Resource not found')));
      }
  } catch (PDOException $e) {
      echo 'Error: ' + $e->getMessage();
  }
});

$app->get('/sport/:sportname/:league/competitivity', function ($sportname, $league) use ($dbh, $app) {
  include '../parser/datos_competitividad.php';
  $season = $app->request()->get('season');
  try {
      if ($season) {
          $app->response->status(200);
          $app->response->body(datos_competitividad($season));
      } else {
          $app->response->status(200);
          $app->response->body(datos_competitividad());
      }
  } catch (PDOException $e) {
      echo 'Error: ' + $e->getMessage();
  }
});

$app->get('/sport/:sportname/:league/measures', function ($sportname, $league) use ($dbh, $app) {
  include '../parser/medidas_competitividad.php';
  $season = $app->request()->get('season');
  try {
      if ($season) {
          $app->response->status(200);
          $app->response->body(medidas_competitividad($season));
      } else {
          $app->response->status(200);
          $app->response->body(medidas_competitividad());
      }
  } catch (PDOException $e) {
      echo 'Error: ' + $e->getMessage();
  }
});

$app->get('/sport/:sportname/:league/clasification', function ($sportname, $league) use ($dbh, $app) {
  include '../parser/medidas_competitividad.php';
  try {
      $season = $app->request()->get('season');
      $fixture = $app->request()->get('fixture');

      if ($season) {
          if ($fixture) {
              $sql = $dbh->prepare("select * from rankings where temporada = \"$season\" and jornada = $fixture order by posicion;");
          } else {
              $sql = $dbh->prepare("select * from rankings where temporada = \"$season\" and jornada = (SELECT max(jornada) from rankings where temporada = \"$season\") order by posicion;");
          }
      } else {
          $season = getLastSeason();
          if ($fixture) {
              $sql = $dbh->prepare("select * from rankings where temporada = \"$season\" and jornada = $fixture order by posicion;");
          } else {
              $sql = $dbh->prepare("select * from rankings where temporada = \"$season\" and jornada = (SELECT max(jornada) from rankings where temporada = \"$season\") order by posicion;");
          }
      }
      $sql->execute();
      $result = $sql->fetchAll(PDO::FETCH_ASSOC);

      if ($result) {
          $app->response->status(200);
          $app->contentType('application/json; charset=utf-8');
          $app->response->body(json_encode(array('number' => count($result), 'clasification' => $result)));
          //$app->response->body(json_encode($result));
      } else {
          $app->response->status(404);
          $app->response->body(json_encode(array('error' => 'Resource not found')));
      }
  } catch (PDOException $e) {
      echo 'Error: ' + $e->getMessage();
  }
});

$app->get('/sport/:sportname/:league/chartLine', function ($sportname, $league) use ($dbh, $app) {
  include '../parser/lineas.php';
  try {
      $season = $app->request()->get('season');

      if ($season == null) {
          $season = getLastSeason();
      }

      $result = getLineChart($season);

      if ($result) {
          $app->response->status(200);
          $app->contentType('application/json; charset=utf-8');
          $app->response->body($result);
      } else {
          $app->response->status(404);
          $app->response->body(json_encode(array('error' => 'Resource not found')));
      }
  } catch (PDOException $e) {
      echo 'Error: ' + $e->getMessage();
  }
});

$app->run();

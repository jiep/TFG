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

function authenticate(\Slim\Route $route)
{
    $hostname = 'localhost';
    $username = 'rankings';
    $password = '1234';
    $headers = apache_request_headers();
    $app = \Slim\Slim::getInstance();

    $dbh = new PDO("mysql:host=$hostname;dbname=rankings;charset=utf8", $username, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($headers['Authorization'])) {
        $api_key = $headers['Authorization'];

        $id = $dbh->prepare('select id from users where apiKey = :apiKey');
        $id->bindParam('apiKey', $api_key, PDO::PARAM_STR);
        $result = $id->execute();

        if ($id->rowCount() == 0) {
            $app->status(401);
            $app->contentType('application/json; charset=utf-8');
            echo json_encode(array('error' => 'Acceso denegado. ApiKey inválida'));
            $app->stop();
        } else {
            global $user_id;
        }
    } else {
        $app->status(400);
        $app->contentType('application/json; charset=utf-8');
        echo json_encode(array('error' => 'Falta apiKey en la petición'));
        $app->stop();
    }
}

$app->post('/register', function () use ($app, $dbh) {
  if ($app->request->post('username') && $app->request->post('password') && $app->request->post('email')) {
      include '../rankings/User.php';

      $username = $app->request->post('username');
      $password = $app->request->post('password');
      $email = $app->request->post('email');

      $username_valid = $dbh->prepare('select username from users where username = :username');
      $username_valid->bindParam('username', $username, PDO::PARAM_STR);
      $username_valid->execute();

      if ($username_valid->rowCount() == 0) {
          $user = new User($username, $password, $email);

          $sql = $dbh->prepare('INSERT INTO users (username, password, email, apiKey) VALUES (:username, :password, :email, :apikey)');
          $sql->bindParam('username', $user->getUsername(), PDO::PARAM_STR);
          $sql->bindParam('password', $user->getPassword(), PDO::PARAM_STR);
          $sql->bindParam('email', $user->getEmail(), PDO::PARAM_STR);
          $sql->bindParam('apikey', $user->getApiKey(), PDO::PARAM_STR);

          $result = $sql->execute();

          $app->response->status(201);
          $app->contentType('application/json; charset=utf-8');
          $app->response->body(json_encode(array('message' => 'Usuario creado con éxito')));
      } else {
          $app->response->status(200);
          $app->contentType('application/json; charset=utf-8');
          $app->response->body(json_encode(array('message' => 'Usuario ya existe')));
      }
  } else {
      $app->response->status(200);
      $app->contentType('application/json; charset=utf-8');
      $app->response->body(json_encode(array('error' => 'Alguno(s) de lo(s) parámetro(s) no es válido o falta alguno de ellos')));
  }
});

$app->post('/login', function () use ($dbh, $app) {
  function getPasswordHash($password)
  {
      return crypt($password, '$2a'.substr(sha1(mt_rand()), 0, 30));
  }
  if ($app->request->post('username') && $app->request->post('password')) {
      $username = $app->request->post('username');
      $password = $app->request->post('password');

      $login_valid = $dbh->prepare('select username, email, apiKey, id from users where username = :username and password = :password');
      $login_valid->bindParam('username', $username, PDO::PARAM_STR);
      $password_hash = getPasswordHash($password);
      $login_valid->bindParam('password', $password_hash, PDO::PARAM_STR);
      $login_valid->execute();

      if ($login_valid->rowCount() == 0) {
          $app->response->status(200);
          $app->contentType('application/json; charset=utf-8');
          $app->response->body(json_encode(array('message' => 'Nombre de usuario y/o contraseña incorrectos')));
      } else {
          $results = $login_valid->fetch(PDO::FETCH_ASSOC);
          $app->response->status(200);
          $app->contentType('application/json; charset=utf-8');
          $app->response->body(json_encode($results));
      }
  } else {
      $app->response->status(200);
      $app->contentType('application/json; charset=utf-8');
      $app->response->body(json_encode(array('message' => 'Alguno(s) de lo(s) parámetro(s) no es válido o falta alguno de ellos')));
  }
});

$app->get('/sport/:sportname/:league/teams', function ($sportname, $league) use ($dbh, $app) {
  include '../parser/datos_competitividad.php';
  try {
      $season = $app->request()->get('season');
      if ($season) {
          $sql = $dbh->prepare("select equipos.id, equipos.nombre from equipos where equipos.nombre IN (SELECT DISTINCT equipo from rankings where temporada = \"$season\") order by equipos.nombre");
      } else {
          $season = getLastSeason();
          $sql = $dbh->prepare("select equipos.id, equipos.nombre from equipos where equipos.nombre IN (SELECT DISTINCT equipo from rankings where temporada = \"$season\") order by equipos.nombre");
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

$app->get('/sport/:sportname/:league/team/:team', function ($sportname, $league, $team) use ($dbh, $app) {
  include '../parser/lineas.php';
  try {
      $season = $app->request()->get('season');

      if ($season == null) {
          $season = getLastSeason();
      }
      $sql_nombre = $dbh->prepare('select nombre from equipos where id = :team');
      $sql_nombre->bindParam('team', $team);
      $sql_nombre->execute();
      $result_name = $sql_nombre->fetchAll(PDO::FETCH_ASSOC);

      if (!empty($result_name)) {
          $name = $result_name[0]['nombre'];

          try {
              // Número de temporadas en primera división
              $sql_numero_temporadas = $dbh->prepare('select count(distinct temporada) as temporadas from rankings where equipo = :name');
              $sql_numero_temporadas->bindParam('name', $name, PDO::PARAM_STR);
              $sql_numero_temporadas->execute();
              $result_numero_temporadas = $sql_numero_temporadas->fetchAll(PDO::FETCH_ASSOC);

              // Número de goles a favor como local
              $sql_goles_locales_favor = $dbh->prepare('select sum(goles_local) as goles_local_favor from partidos where equipo_local = :name');
              $sql_goles_locales_favor->bindParam('name', $name, PDO::PARAM_STR);
              $sql_goles_locales_favor->execute();
              $result_goles_locales_favor = $sql_goles_locales_favor->fetchAll(PDO::FETCH_ASSOC);

              // Número de goles a favor como visitante
              $sql_goles_visitantes_favor = $dbh->prepare('select sum(goles_visitante) as goles_visitante_favor from partidos where equipo_visitante = :name');
              $sql_goles_visitantes_favor->bindParam('name', $name, PDO::PARAM_STR);
              $sql_goles_visitantes_favor->execute();
              $result_goles_visitantes_favor = $sql_goles_visitantes_favor->fetchAll(PDO::FETCH_ASSOC);

              // Número de goles en contra como local
              $sql_goles_locales_contra = $dbh->prepare('select sum(goles_visitante) as goles_local_contra from partidos where equipo_local = :name');
              $sql_goles_locales_contra->bindParam('name', $name, PDO::PARAM_STR);
              $sql_goles_locales_contra->execute();
              $result_goles_locales_contra = $sql_goles_locales_contra->fetchAll(PDO::FETCH_ASSOC);

              // Número de goles en contra como visitante
              $sql_goles_visitantes_contra = $dbh->prepare('select sum(goles_local) as goles_visitante_contra from partidos where equipo_visitante = :name');
              $sql_goles_visitantes_contra->bindParam('name', $name, PDO::PARAM_STR);
              $sql_goles_visitantes_contra->execute();
              $result_goles_visitantes_contra = $sql_goles_visitantes_contra->fetchAll(PDO::FETCH_ASSOC);

              // Número de victorias como local
              $sql_num_victorias_local = $dbh->prepare('select count(*) as victorias_local from partidos where equipo_local = :name and goles_local > goles_visitante');
              $sql_num_victorias_local->bindParam('name', $name, PDO::PARAM_STR);
              $sql_num_victorias_local->execute();
              $result_num_victorias_local = $sql_num_victorias_local->fetchAll(PDO::FETCH_ASSOC);

              // Número de victorias como visitante
              $sql_num_victorias_visitante = $dbh->prepare('select count(*) as victorias_visitante from partidos where equipo_visitante = :name and goles_local < goles_visitante');
              $sql_num_victorias_visitante->bindParam('name', $name, PDO::PARAM_STR);
              $sql_num_victorias_visitante->execute();
              $result_num_victorias_visitante = $sql_num_victorias_visitante->fetchAll(PDO::FETCH_ASSOC);

              // Número de empates como local
              $sql_num_empates_local = $dbh->prepare('select count(*) as empates_local from partidos where equipo_local = :name and goles_local = goles_visitante');
              $sql_num_empates_local->bindParam('name', $name, PDO::PARAM_STR);
              $sql_num_empates_local->execute();
              $result_num_empates_local = $sql_num_empates_local->fetchAll(PDO::FETCH_ASSOC);

              // Número de empates como visitante
              $sql_num_empates_visitante = $dbh->prepare('select count(*) as empates_visitante from partidos where equipo_visitante = :name and goles_local = goles_visitante');
              $sql_num_empates_visitante->bindParam('name', $name, PDO::PARAM_STR);
              $sql_num_empates_visitante->execute();
              $result_num_empates_visitante = $sql_num_empates_visitante->fetchAll(PDO::FETCH_ASSOC);

              // Número de derrotas como local
              $sql_num_derrotas_local = $dbh->prepare('select count(*) as derrotas_local from partidos where equipo_local = :name and goles_local < goles_visitante');
              $sql_num_derrotas_local->bindParam('name', $name, PDO::PARAM_STR);
              $sql_num_derrotas_local->execute();
              $result_num_derrotas_local = $sql_num_derrotas_local->fetchAll(PDO::FETCH_ASSOC);

              // Número de empates como visitante
              $sql_num_derrotas_visitante = $dbh->prepare('select count(*) as derrotas_visitante from partidos where equipo_visitante = :name and goles_local > goles_visitante');
              $sql_num_derrotas_visitante->bindParam('name', $name, PDO::PARAM_STR);
              $sql_num_derrotas_visitante->execute();
              $result_num_derrotas_visitante = $sql_num_derrotas_visitante->fetchAll(PDO::FETCH_ASSOC);

              // Número total de partidos jugados como local
              $sql_num_partidos_local = $dbh->prepare('select count(*) as jugados_local from partidos where equipo_local = :name');
              $sql_num_partidos_local->bindParam('name', $name, PDO::PARAM_STR);
              $sql_num_partidos_local->execute();
              $result_num_partidos_local = $sql_num_partidos_local->fetchAll(PDO::FETCH_ASSOC);

              // Número total de partidos jugados como visitante
              $sql_num_partidos_visitante = $dbh->prepare('select count(*) as jugados_visitante from partidos where equipo_visitante = :name');
              $sql_num_partidos_visitante->bindParam('name', $name, PDO::PARAM_STR);
              $sql_num_partidos_visitante->execute();
              $result_num_partidos_visitante = $sql_num_partidos_visitante->fetchAll(PDO::FETCH_ASSOC);
          } catch (PDOException $e) {
              echo 'Error: ' + $e->getMessage();
          }
          $app->response->status(200);
          $app->contentType('application/json; charset=utf-8');
          $app->response->body(json_encode(array('name' => $name, 'seasons' => $result_numero_temporadas[0]['temporadas'], 'local_goals_favor' => $result_goles_locales_favor[0]['goles_local_favor'], 'away_goals_favor' => $result_goles_visitantes_favor[0]['goles_visitante_favor'], 'local_goals_contra' => $result_goles_locales_contra[0]['goles_local_contra'], 'away_goals_contra' => $result_goles_visitantes_contra[0]['goles_visitante_contra'], 'local_win' => $result_num_victorias_local[0]['victorias_local'], 'away_win' => $result_num_victorias_visitante[0]['victorias_visitante'], 'local_draw' => $result_num_empates_local[0]['empates_local'], 'away_draw' => $result_num_empates_visitante[0]['empates_visitante'], 'local_defeat' => $result_num_derrotas_local[0]['derrotas_local'], 'away_defeat' => $result_num_derrotas_visitante[0]['derrotas_visitante'], 'local_total' => $result_num_partidos_local[0]['jugados_local'], 'away_total' => $result_num_partidos_visitante[0]['jugados_visitante'])));
      } else {
          $app->response->status(404);
          $app->response->body(json_encode(array('error' => 'Resource not found')));
      }
  } catch (PDOException $e) {
      echo 'Error: ' + $e->getMessage();
  }
});

$app->run();

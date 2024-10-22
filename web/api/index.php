<?php

require 'autoload.php';

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
  $body = json_decode($app->request->getBody());
  if ($body->username && $body->password && $body->email) {
      include '../../rankings/User.php';

      $username = $body->username;
      $password = $body->password;
      $email = $body->email;

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
  $body = json_decode($app->request->getBody());
  if ($body->username && $body->password) {
      $username = $body->username;
      $password = $body->password;

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
  include '../../parser/datos_competitividad.php';
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
  include '../../parser/datos_competitividad.php';
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
  include '../../parser/medidas_competitividad.php';
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
  include '../../parser/medidas_competitividad.php';
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
  include '../../parser/lineas.php';
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
  include '../../parser/lineas.php';
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

              // Número de partidos por resultado como local
              $sql_max_local = $dbh->prepare('select max(goles_local) as local, max(goles_visitante) as visitante from partidos where equipo_local = :name');
              $sql_max_local->bindParam('name', $name, PDO::PARAM_STR);
              $sql_max_local->execute();
              $result_max_local = $sql_max_local->fetch(PDO::FETCH_ASSOC);
              $goles_max_favor = $result_max_local['local'];
              $goles_max_contra = $result_max_local['visitante'];

              $results = array();
              $local_results = array();
              for($i = 0; $i <= $goles_max_favor; $i++){
                for($j = 0; $j <= $goles_max_contra; $j++){
                  // Cuenta el número de partidos con cada resultado
                  $sql_cuenta_local = $dbh->prepare('select count(*) as total from partidos where equipo_local = :name and goles_local = :goles_local and goles_visitante = :goles_visitante');
                  $sql_cuenta_local->bindParam('name', $name, PDO::PARAM_STR);
                  $sql_cuenta_local->bindParam('goles_local', $i, PDO::PARAM_STR);
                  $sql_cuenta_local->bindParam('goles_visitante', $j, PDO::PARAM_STR);
                  $sql_cuenta_local->execute();
                  $result_cuenta_local = $sql_cuenta_local->fetch(PDO::FETCH_ASSOC);
                  if($result_cuenta_local['total'] != 0){
                    $local_results["$i-$j"] = $result_cuenta_local['total'];
                  }
                }
              }

              $results['local'] = $local_results;

              // Número de partidos por resultado como visitante
              $sql_max_visitante = $dbh->prepare('select max(goles_local) as local, max(goles_visitante) as visitante from partidos where equipo_visitante = :name');
              $sql_max_visitante->bindParam('name', $name, PDO::PARAM_STR);
              $sql_max_visitante->execute();
              $result_max_visitante = $sql_max_visitante->fetch(PDO::FETCH_ASSOC);
              $goles_max_favor = $result_max_local['visitante'];
              $goles_max_contra = $result_max_local['local'];

              $results = array();
              $away_results = array();
              for($i = 0; $i <= $goles_max_favor; $i++){
                for($j = 0; $j <= $goles_max_contra; $j++){
                  // Cuenta el número de partidos con cada resultado
                  $sql_cuenta_visitante = $dbh->prepare('select count(*) as total from partidos where equipo_visitante = :name and goles_local = :goles_local and goles_visitante = :goles_visitante');
                  $sql_cuenta_visitante->bindParam('name', $name, PDO::PARAM_STR);
                  $sql_cuenta_visitante->bindParam('goles_local', $i, PDO::PARAM_STR);
                  $sql_cuenta_visitante->bindParam('goles_visitante', $j, PDO::PARAM_STR);
                  $sql_cuenta_visitante->execute();
                  $result_cuenta_visitante = $sql_cuenta_visitante->fetch(PDO::FETCH_ASSOC);
                  if($result_cuenta_visitante['total'] != 0){
                    $local_results["$i-$j"] = $result_cuenta_visitante['total'];
                  }
                }
              }

              $results['local'] = $local_results;
              $results['away'] = $local_results;


          } catch (PDOException $e) {
              echo 'Error: ' + $e->getMessage();
          }
          $app->response->status(200);
          $app->contentType('application/json; charset=utf-8');
          $app->response->body(json_encode(array('name' => $name, 'seasons' => $result_numero_temporadas[0]['temporadas'], 'local_goals_favor' => $result_goles_locales_favor[0]['goles_local_favor'], 'away_goals_favor' => $result_goles_visitantes_favor[0]['goles_visitante_favor'], 'local_goals_contra' => $result_goles_locales_contra[0]['goles_local_contra'], 'away_goals_contra' => $result_goles_visitantes_contra[0]['goles_visitante_contra'], 'local_win' => $result_num_victorias_local[0]['victorias_local'], 'away_win' => $result_num_victorias_visitante[0]['victorias_visitante'], 'local_draw' => $result_num_empates_local[0]['empates_local'], 'away_draw' => $result_num_empates_visitante[0]['empates_visitante'], 'local_defeat' => $result_num_derrotas_local[0]['derrotas_local'], 'away_defeat' => $result_num_derrotas_visitante[0]['derrotas_visitante'], 'local_total' => $result_num_partidos_local[0]['jugados_local'], 'away_total' => $result_num_partidos_visitante[0]['jugados_visitante'], "results" => $results)));
      } else {
          $app->response->status(404);
          $app->response->body(json_encode(array('error' => 'Resource not found')));
      }
  } catch (PDOException $e) {
      echo 'Error: ' + $e->getMessage();
  }
});

$app->get('/sport/:sportname/:league/prediction', function ($sportname, $league) use ($dbh, $app){
  include '../../prediccion/interpolacion/interpolation.php';
  include '../../prediccion/tendencia/tendencia.php';
  include '../../prediccion/combinacion/comb_convexa.php';
  include '../../prediccion/calcularClasif.php';

  try {

      $season = $app->request()->get('season');
      $fixture = $app->request()->get('fixture');

      if (! $season) { $season = getLastSeason(); }
      if (! $fixture) { $fixture = getLastFixture($season); }

      $sql = $dbh->prepare("select * from partidos where temporada = \"$season\" and jornada = $fixture;");
      $sql->execute();
      $result = $sql->fetchAll(PDO::FETCH_ASSOC);


      $pred_int = prob_interpolation($season,$fixture);

      $rank_int = calcularRanking($pred_int,$season,$fixture-1);

      $pred_tend = prob_tendencia($season,$fixture);

      $rank_tend = calcularRanking($pred_tend,$season,$fixture-1);

      $pred_conj = conjugar($pred_int,$pred_tend);

      $rank_conj = calcularRanking($pred_conj,$season,$fixture-1);

      if ($result) {
          $app->response->status(200);
          $app->contentType('application/json; charset=utf-8');
          $app->response->body(json_encode(array('results1' => $pred_int, 'ranking1' => $rank_int, 'results2' => $pred_tend, 'ranking2' => $rank_tend, 'results3' => $pred_conj, 'ranking3' => $rank_conj)));
      } else {
          $app->response->status(404);
          $app->response->body(json_encode(array('error' => 'Resource not found')));
      }
  } catch (PDOException $e) {
      echo 'Error: ' + $e->getMessage();
  }
});

$app->get('/users/:id/graphs', function ($id) use ($dbh, $app) {
  try {
          try {
              $sql_graphs = $dbh->prepare('select * from competitivity_graph where id_user = :id');
              $sql_graphs->bindParam('id', $id, PDO::PARAM_STR);
              $sql_graphs->execute();
              $result_graphs = $sql_graphs->fetchAll(PDO::FETCH_ASSOC);

          } catch (PDOException $e) {
              echo 'Error: ' + $e->getMessage();
          }
          $app->response->status(200);
          $app->contentType('application/json; charset=utf-8');
          $app->response->body(json_encode($result_graphs));
  } catch (PDOException $e) {
      echo 'Error: ' + $e->getMessage();
  }
});

$app->get('/users/:id_user/graphs/:graph_id', function ($id_user, $graph_id) use ($dbh, $app) {
  try {

              $sql_graphs = $dbh->prepare('select * from competitivity_graph where id_user = :id and id = :graph_id');
              $sql_graphs->bindParam('id', $id_user, PDO::PARAM_STR);
              $sql_graphs->bindParam('graph_id', $graph_id, PDO::PARAM_STR);
              $sql_graphs->execute();
              $result_graphs = $sql_graphs->fetch(PDO::FETCH_ASSOC);

              if($result_graphs){
                $sql_matrix1 = $dbh->prepare('select * from graph_vertex where id_graph = :graph_id');
                $sql_matrix1->bindParam('graph_id', $graph_id, PDO::PARAM_STR);
                $sql_matrix1->execute();
                $nodes1 = $sql_matrix1->fetchAll(PDO::FETCH_COLUMN, 2);

                $sql_matrix2 = $dbh->prepare('select * from graph_vertex where id_graph = :graph_id');
                $sql_matrix2->bindParam('graph_id', $graph_id, PDO::PARAM_STR);
                $sql_matrix2->execute();
                $nodes2 = $sql_matrix2->fetchAll(PDO::FETCH_COLUMN, 3);

                $sql_matrix3 = $dbh->prepare('select * from graph_vertex where id_graph = :graph_id');
                $sql_matrix3->bindParam('graph_id', $graph_id, PDO::PARAM_STR);
                $sql_matrix3->execute();
                $result_matrix = $sql_matrix3->fetchAll(PDO::FETCH_ASSOC);


                $nodes = array_unique(array_merge($nodes1, $nodes2));
                $nodes = array_values($nodes);

                $nodes_json = array();

                for($i = 0; $i < count($nodes); $i++){
                  $nodes_json[$i] = array("data" => array("id" => "$i", "name"=>$nodes[$i]));
                }

                $edges = array();

                $count = 0;
                for($i = 0; $i < count($result_matrix); $i++){
                  $edges[$count] = array("data" => array("source" => "" .array_search($result_matrix[$i]['source'], $nodes), "target" => "" .array_search($result_matrix[$i]['target'], $nodes), "weight" => $result_matrix[$i]['weight']));
                  $count++;
                }

                $app->response->status(200);
                $app->contentType('application/json; charset=utf-8');
                $app->response->body(json_encode(array("measures" => $result_graphs, "graph" =>array("elements" => array("nodes" => $nodes_json, "edges" => $edges)))));
              }

  } catch (PDOException $e) {
      echo 'Error: ' + $e->getMessage();
  }
});

$app->delete('/users/:id_user/graphs/:graph_id', function ($id_user, $graph_id) use ($dbh, $app) {
  include '../../parser/lineas.php';
  try {
          try {
              $sql_graphs = $dbh->prepare('select * from competitivity_graph where id_user = :id and id = :graph_id');
              $sql_graphs->bindParam('id', $id_user, PDO::PARAM_STR);
              $sql_graphs->bindParam('graph_id', $graph_id, PDO::PARAM_STR);
              $sql_graphs->execute();
              $result_graphs = $sql_graphs->fetch(PDO::FETCH_ASSOC);

              if($result_graphs){
                $sql_delete = $dbh->prepare('delete from competitivity_graph where id = :graph_id');
                $sql_delete->bindParam('graph_id', $graph_id, PDO::PARAM_STR);
                $sql_delete->execute();
              }



          } catch (PDOException $e) {
              echo 'Error: ' + $e->getMessage();
          }
          $app->response->status(200);
          $app->contentType('application/json; charset=utf-8');
          $app->response->body(json_encode(array("message" => "Grafo borrado correctamente")));
  } catch (PDOException $e) {
      echo 'Error: ' + $e->getMessage();
  }
});

$app->post('/users/:id/graphs', function ($id) use ($dbh, $app) {
  include '../../rankings/Ranking.php';
  include '../../rankings/RankingCollection.php';
  include '../../rankings/Graph.php';
  include '../../rankings/EvolutiveCompetitivityGraph.php';
  include '../../rankings/CompetitivityGraph.php';

  $file = $_FILES['archivo']['tmp_name'];
  $handle = fopen($file,"r");

  $line = fgets($handle);

  $data = explode(',', $line);
  $name = $data[0];
  $teams = $data[1];
  $fixtures = $data[2];
  //echo $name . $teams . $fixtures ."\n";

  $rankings = array();

  $first = true;
  foreach(file($file) as $line) {
    if($first){
      $first = false;
    }else{
      $team_name = explode(',', $line);
      $r = new Ranking();
      for($team = 0; $team < $teams; $team++){
        $team_name[$team] = str_replace("\n", "", $team_name[$team]);
        $r->add($team_name[$team]);
      }
      $rankings[] = $r;
    }
  }

  $rc = new RankingCollection($rankings);

  $nms =  $rc->calculateEvolutiveCompetitivityGraph()->normalizedMeanStrength();
  $efficiency =  $rc->calculateCompetitivityGraph()->efficiency();
  $cpl = $rc->calculateCompetitivityGraph()->characteristicPathLength();
  $diameter = $rc->calculateCompetitivityGraph()->diameter();
  $nmd = $rc->calculateEvolutiveCompetitivityGraph()->normalizedMeanDegree();
  $kendall = $rc->calculateEvolutiveCompetitivityGraph()->generalizedKendallsTau();

  $graph = $rc->calculateEvolutiveCompetitivityGraph()->exportAsCytoscapeJSON();

  try {
          try {
              $sql_graphs = $dbh->prepare('insert into competitivity_graph (name, id_user, nms, eficiency, cpl, diameter, nmd, kendall) VALUES (:name, :id_user,:nms, :efficiency, :cpl, :diameter, :nmd, :kendall)');
              $sql_graphs->bindParam('name', $name, PDO::PARAM_STR);
              $sql_graphs->bindParam('id_user', $id);
              $sql_graphs->bindParam('nms', $nms);
              $sql_graphs->bindParam('efficiency', $efficiency);
              $sql_graphs->bindParam('cpl', $cpl);
              $sql_graphs->bindParam('diameter', $diameter);
              $sql_graphs->bindParam('nmd', $nmd);
              $sql_graphs->bindParam('kendall', $kendall);
              $sql_graphs->execute();
              $graph_id = $dbh->lastInsertId();

              $matrix = $rc->calculateEvolutiveCompetitivityGraph()->getAdjacencyMatrix();

              $ranking = $rc->calculateEvolutiveCompetitivityGraph()->getElements()->ranking;

              $n = count($rc->calculateEvolutiveCompetitivityGraph()->getElements()->ranking);

              for($i = 0; $i < $n; $i++){
                for($j = 0; $j < $i; $j++){
                  if($matrix[$i][$j] != 0){
                    $sql_matrix = $dbh->prepare('insert into graph_vertex (id_graph, source, target, weight) VALUES (:id_graph, :source, :target, :weight)');
                    $sql_matrix->bindParam('id_graph', $graph_id);
                    $sql_matrix->bindParam('source', $ranking[$i], PDO::PARAM_STR);
                    $sql_matrix->bindParam('target', $ranking[$j], PDO::PARAM_STR);
                    $sql_matrix->bindParam('weight', $matrix[$i][$j]);

                    $sql_matrix->execute();
                  }
                }
              }

              //echo ("El id del grafo es: $graph_id");

          } catch (PDOException $e) {
              echo 'Error: ' + $e->getMessage();
          }

          $ret = array(
            "id" => $graph_id,
            "nms" => $nms,
            "efficiency" =>$efficiency,
            "cpl" => $cpl,
            "diamter" =>$diameter,
            "nmd" =>$nmd,
            "kendall" =>$kendall,
            "graph" => json_decode($graph));

          $app->response->status(200);
          $app->contentType('application/json; charset=utf-8');
          $app->response->body(json_encode($ret));
  } catch (PDOException $e) {
      echo 'Error: ' + $e->getMessage();
  }
});


$app->run();

<?php

class Connection {

  var $host;
  var $port;
  var $username;
  var $password;
  var $database;
  var $connection;

  function __contruct($host, $port, $username, $password, $database){
    $this->host = $host;
    $this->port = $port;
    $this->username = $username;
    $this->password = $password;
    $this->database = $database;
  }

  function connect(){
    $conn = mysql_connect($this->host . ":" . $this->port, $this->username, $this->password);
    if(!$conn){
      die ("Cannot connect to the database");
    }else{
      $this->connection = $conn;
    }
    return $this->connection;
  }

  function selectDatabase(){
    mysql_select_db($this->database);
  }

  function close(){
    mysql_close($this->myconn);
  }

  function query($query){
    $i = 0;

    $result = mysql_query($query);
    while($row = mysql_fetch_array($result))
    {
        foreach($row as $key => $value)
        {
            $data[$i][$key] = $value;
            $i++;
        }
    }
    return $data;
  }
}

?>

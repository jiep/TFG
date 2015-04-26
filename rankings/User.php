<?php

class User {

  var $username;
  var $password;
  var $email;
  var $apiKey;

  public function __construct($username, $password, $email){
    if($this->isValidEmail($email)){
      $this->email = $email;
    }

    $this->username = $username;
    $this->password = $this->getHashPassword($password);
    $this->apiKey = $this->generateApiKey();
  }

  public function generateApiKey(){
    return md5(uniqid(rand(), true));
  }

  private function getHashPassword($password){
    return crypt($password, '$2a' . substr(sha1(mt_rand()), 0, 30));
  }

  private function isValidEmail($email){
    print_r(filter_var($email, FILTER_VALIDATE_EMAIL));
    return filter_var($email, FILTER_VALIDATE_EMAIL);
  }
}

?>

<?php

class User
{
  public $username;
    public $password;
    public $email;
    public $apiKey;

    public function __construct($username, $password, $email)
    {
        if ($this->isValidEmail($email)) {
            $this->email = $email;
        }

        $this->username = $username;
        $this->password = $this->getHashPassword($password);
        $this->apiKey = $this->generateApiKey();
    }

    public function generateApiKey()
    {
        return md5(uniqid(rand(), true));
    }

    private function getHashPassword($password)
    {
        return crypt($password, '$2a'.substr(sha1(mt_rand()), 0, 30));
    }

    private function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }
}

<?php

include('autoload.php');

$email = 'example@example.com';
$username = 'example';
$password = '1234';

$user = new User($username, $password, $email);

print_r($user);

?>

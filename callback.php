<?php
session_start();
require_once('LineLogin.php');

$line = new LineLogin();
$get = $_GET;

$code = $get['code'];
$state = $get['state'];
$token = $line->token($code, $state);

if (property_exists($token, 'error')) {
    header('location: index.php');
    exit();
}

if ($token->access_token) {
    $profile = $line->profile($token->access_token);
    $_SESSION['profile'] = $profile;
    header('location: welcome.php');
    exit();
} else {
    // Handle error when token is not received
    header('location: index.php');
    exit();
}
?>

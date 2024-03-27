<?php
session_start();
require_once('LineLogin.php');

$line = new LineLogin();
$get = $_GET;

$code = $get['code'];
$state = $get['state'];
$token = $line->token($code, $state);

if (property_exists($token, 'error'))
    header('location: index.php');

if ($token->id_token) {
    $profile = $line->profile($token->access_token); // เรียกใช้งานเมทอด profile แทน profileFormIdToken
    $_SESSION['profile'] = $profile;
    header('location: welcome.php');
}
?>

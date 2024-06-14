<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=mdpj_user;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
    exit();
}
?>

<?php

include('resources/functions.php');

$name = $_POST['name'];
$login = encrypt_string(htmlspecialchars($_POST['login']));
$password = encrypt_string(htmlspecialchars($_POST['password']));
$note = sqlescape(htmlspecialchars($_POST['note']));
$group = $_POST['group'];

add_password_object($name, $login, $password, $group, $note);

header("Location: index.php");

?>
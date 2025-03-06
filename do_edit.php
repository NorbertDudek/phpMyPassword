<?php

include('resources/functions.php');

$id = $_POST['id'];

$name = $_POST['name'];
$login = encrypt_string(htmlspecialchars($_POST['login']));
$password = encrypt_string(htmlspecialchars($_POST['password']));
$group = $_POST['group'];
$note = sqlescape(htmlspecialchars($_POST['note']));

edit_password_object($id, $name, $login, $password, $group, $note);

header("Location: show.php?id=$id");

?>
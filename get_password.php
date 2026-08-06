<?php
include('resources/functions.php');
check_session_login();

$id = $_GET['id'];
$uid = get_my_uid();

// Zwracamy czysty tekst - hasło nie może być traktowane jako HTML,
// bo znaki specjalne (np. &, <, >) zostałyby błędnie zinterpretowane/przekodowane.
header('Content-Type: text/plain; charset=utf-8');

echo get_decrypted_password($id, $uid);
?>
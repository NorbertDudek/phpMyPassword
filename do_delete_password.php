<?php
include('resources/functions.php');

$id = $_POST['id'];

delete_password($id);

header("location: index.php");
?>
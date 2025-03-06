<?php
include('resources/functions.php');

$group_name = sqlescape(strtolower($_POST['group_name']));
$description = sqlescape($_POST['description']);
$parent = $_POST['parent'];

add_group($group_name, $description, $parent);
	
	
header("Location: groups.php");

?>
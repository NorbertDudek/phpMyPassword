<?php
include('resources/functions.php');

$gid = $_POST['gid'];
$group_name = strtolower($_POST['group_name']);
$description = $_POST['description'];
$parent = $_POST['parent'];


edit_permisiongroup($gid, $group_name, $description, $parent);
	
header("Location: permisiongroups.php");

?>
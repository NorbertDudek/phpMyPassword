<?php
include('resources/functions.php');

$type = $_POST['type'];
$login = strtolower($_POST['login']);
$password = $_POST['password'];
$admin = $_POST['admin'];
$force_password_change = checkInPOST('force_password_change');

//Create user (Local)
if ($type == "local") {
	add_local_user($login, $password);

	//Force a password change on next login if requested
	if ($force_password_change == 'on') {
		set_must_change_password(get_uid($login), true);
		}
	}
	
//Grant admin rights if applicable
if ($admin == 'on') {
	grant_user_admin_rights(get_uid($login));
	}
	
header("Location: admin.php");

?>
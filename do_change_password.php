<?php
include('resources/functions.php');

// Must be logged in
check_session_login();

$uid = get_my_uid();

// Nothing to do if a password change isn't actually required
if (!get_must_change_password($uid)) {
	header('Location: index.php');
	exit;
}

$type = get_user_type($uid);

// This flow only makes sense for local accounts; LDAP passwords are
// managed on the LDAP server, not in this application. Just clear the
// flag so an LDAP user can never get stuck here.
if ($type != 'local') {
	set_must_change_password($uid, false);
	header('Location: index.php');
	exit;
}

$password = checkInPOST('password');
$confirm_password = checkInPOST('confirm_password');

if ($password == '') {
	header('Location: change_password.php?error=empty');
	exit;
}

if ($password != $confirm_password) {
	header('Location: change_password.php?error=mismatch');
	exit;
}

update_user_password($uid, $password);
set_must_change_password($uid, false);

header('Location: index.php');
exit;
?>

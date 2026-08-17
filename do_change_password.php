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

// This flow only makes sense for local accounts. Just clear the flag
// so a non-local account (e.g. the superuser) can never get stuck here.
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

if (is_same_as_current_password($uid, $password)) {
	header('Location: change_password.php?error=same');
	exit;
}

update_user_password($uid, $password);
set_must_change_password($uid, false);

header('Location: index.php');
exit;
?>

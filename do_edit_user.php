<?php
include('resources/functions.php');

$uid = $_POST['uid'];

if (!am_i_admin()) {
	header("location: show_permissions_error.php");
	}
else {
	$type = get_user_type($uid);
	$new_password = $_POST['password'];
	$confirm_new_password = $_POST['confirm_password'];
	$admin = checkInPOST('admin');
	$export = checkInPOST('export');
	$passwordAdd = checkInPOST('passwordAdd');
	$passwordEdit = checkInPOST('passwordEdit');
	$passwordRemove = checkInPOST('passwordRemove');
	$groupAdd = checkInPOST('groupAdd');
	$groupEdit = checkInPOST('groupEdit');
	$groupRemove = checkInPOST('groupRemove');
	$groupSee = checkInPOST('groupSee');
	
	if ($type == 'local') {
		if ($new_password != '') {
			if ($new_password == $confirm_new_password) {
				update_user_password($uid, $new_password);
			}
			else {
				header("location: edit_user.php?uid=$uid&message=\"Passwords do not match\"");
			}
		}
	}
	
	$accGrant = 0;
	
	if ($admin == 'on')
		$accGrant += accAdmin;
	
	if ($export == 'on')
		$accGrant += accExport;
	
	if ($passwordAdd == 'on')
		$accGrant += accPasswordAdd;
	if ($passwordEdit == 'on')
		$accGrant += accPasswordEdit;
	if ($passwordRemove == 'on')
		$accGrant += accPasswordRemove;

	if ($groupAdd == 'on')
		$accGrant += accGroupAdd;
	if ($groupEdit == 'on')
		$accGrant += accGroupEdit;
	if ($groupRemove == 'on')
		$accGrant += accGroupRemove;
	if ($groupSee == 'on')
		$accGrant += accGroupSee;

	grant_user_rights($uid, $accGrant);
	
}
	
header("Location: admin.php");

?>
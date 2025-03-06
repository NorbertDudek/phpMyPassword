<?php


define(accAdmin, 1);
define(accExport, 2);
define(accPasswordAdd, 4);
define(accPasswordEdit, 8);
define(accPasswordRemove, 16);
define(accGroupAdd, 32);
define(accGroupEdit, 64);
define(accGroupRemove, 128);
define(accGroupSee, 256);


$IsShowError = true;

if ($IsShowError) {
	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	error_reporting(E_ALL);
}

// SESSION FUNCTIONS ***********************

// *****************************************
// FUNCTION: ldap_enabled()
// *****************************************
// Is LDAP enabled in config.php?
function ldap_enabled() {
	include('resources/config.php');
	if ($ldap == true) {
		return true;
		}
	else {
		return false;
		}
	}
//End Function


// *****************************************
// FUNCTION: check_login($user, $password)
// *****************************************
// Check for valid credentials
function check_login($login, $password) {
	include("resources/config.php");
	
	echo "Function: check_login // ";
	
	// Are we using the superuser account?
	if ($login == $superuser) {
		// We are superuser. Check password from config file
		if ($password == $superuser_password) {
			return true;
		}
		else {
			return false;
		}
	}
	else {
		// Not superuser, check database for login
		
		// Get login type
		$type = get_sql_value("SELECT type FROM users WHERE login = '$login'");
		
		// If type is null, user doesn't exist in DB
		if ($type == null) {
			return false;
			}
		
		// LDAP Login
		if ($type == "ldap") {
			echo "Login type is LDAP // ";
			return check_ldap_credentials($login, $password);
			}

		// Local login
		if ($type == "local") {
			$password_md5 = md5($password);
			if ($password_md5 == get_sql_value("SELECT password FROM users WHERE login = '$login'")) {
				return true;
			}
			else {
				return false;
			}
		}
	}
}
//End Function


// *****************************************
// FUNCTION: successful_login($user)
// *****************************************
// Setup session after successfully logging in
function successful_login($user) {
	// Start session if necessary
	if (!isset($_SESSION)) {
		session_start();
	}
	
	$uid = get_uid($user);
	
	// Add session to sessions table
	run_sql_command("INSERT INTO sessions(session_id,timestamp,uid) VALUES('". session_id() . "',now(), $uid)");
}
//End function


// *****************************************
// FUNCTION: check_session_login()
// *****************************************
// Check if our session is valid
function check_session_login() {
	// Start session if necessary
	if (!isset($_SESSION)) {
		session_start();
	}
	
	// Kill any expired sessions
	cleanup_old_sessions();
	
	// Query sessions table to get session ID timestamp
	$SessionTimestamp = get_sql_value("SELECT timestamp FROM sessions WHERE session_id='" . session_id() . "'");
	
	// Redirect to login page if timestamp is null
	if ($SessionTimestamp == NULL) {
		header('Location: login.php');
	}
	else {
		update_session_timestamp();
	}
}
//END FUNCTION


// *****************************************
// FUNCTION: kill_my_session()
// *****************************************
// Remove any entries with our session ID from the sessions table
function kill_my_session() {
	// Start session if necessary
	if (!isset($_SESSION)) {
		session_start();
	}
	
	run_sql_command("DELETE FROM sessions WHERE session_id='" . session_id() . "'");
	session_destroy();
	
}
// END FUNCTION


// *****************************************
// FUNCTION: update_session_timestamp()
// *****************************************
// Update session timestamp for login keepalive
function update_session_timestamp() {

	// Start session if necessary
	if (!isset($_SESSION)) {
		session_start();
	}
	
	run_sql_command("UPDATE sessions SET timestamp = now() WHERE session_id = '" . session_id() . "'");
}
//End function


// *****************************************
// FUNCTION: cleanup_old_sessions()
// *****************************************
// Cleanup old sessions that have expired
function cleanup_old_sessions() {
	include("resources/config.php");
	
	// Start session if necessary
	if (!isset($_SESSION)) {
		session_start();
	}
	
	// Query for all sessions
	$all_sessions = get_sql_results('select * from sessions');
	
	// Check each session timestamp
	foreach ($all_sessions as $session) {
		$diff = time() - strtotime($session['timestamp']);
		
		if ($diff > $session_timeout) {
			// Delete if time diff is beyond threshold
			run_sql_command("DELETE FROM sessions WHERE session_id = '" . $session['session_id'] . "'");
		}
	}	
}
//End function


// USER AND GROUP FUNCTIONS ****************

// *****************************************
// FUNCTION: add_permisiongroup($group_name, $description)
// *****************************************
function add_permisiongroup($group_name, $description) {
	include("resources/config.php");
	
	$encoded_description = string2html($description);
	
	run_sql_command("INSERT INTO permisiongroups (name, description) VALUES ('$group_name', '$encoded_description')");
}

// *****************************************
// FUNCTION: add_group($group_name, $description, $parent)
// *****************************************
function add_group($group_name, $description, $parent) {
	include("resources/config.php");
	
	$encoded_description = string2html($description);
	$my_uid = get_my_uid();
	
	run_sql_command("INSERT INTO groups (name, description, parent, owner) VALUES ('$group_name', '$encoded_description', $parent, $my_uid)");
}

// *****************************************
// FUNCTION: edit_group($gid, $group_name, $description, $parent)
// *****************************************
// Update the name or description of group GID
function edit_group($gid, $group_name, $description, $parent) {
	run_sql_command("UPDATE groups SET name='$group_name' WHERE gid=$gid");
	run_sql_command("UPDATE groups SET description='$description' WHERE gid=$gid");
	run_sql_command("UPDATE groups SET parent='$parent' WHERE gid=$gid");
	}

// *****************************************
// FUNCTION: edit_permisiongroup($gid, $group_name, $description, $parent)
// *****************************************
// Update the name or description of group GID
function edit_permisiongroup($gid, $group_name, $description, $parent) {
	run_sql_command("UPDATE permisiongroups SET name='$group_name' WHERE gid=$gid");
	run_sql_command("UPDATE permisiongroups SET description='$description' WHERE gid=$gid");
	run_sql_command("UPDATE permisiongroups SET parent='$parent' WHERE gid=$gid");
	}


// *****************************************
// FUNCTION: add_ldap_user($login)
// *****************************************
function add_ldap_user($login) {
	include("resources/config.php");
	
	run_sql_command("INSERT INTO users (login, type) VALUES ('$login', 'ldap')");
}


// *****************************************
// FUNCTION: add_local_user($login, $password)
// *****************************************
function add_local_user($login, $password) {
	include("resources/config.php");
	
	$pass_enc = md5($password);
	run_sql_command("INSERT INTO users (login, password, type) VALUES ('$login', '$pass_enc', 'local')");
}


// *****************************************
// FUNCTION: update_user_password($uid, $password)
// *****************************************
function update_user_password($uid, $password) {
	include("resources/config.php");
	
	$pass_enc = md5($password);
	echo "DEBUG: uid $uid / Endrypted password $pass_enc";
	run_sql_command("UPDATE users SET password='$pass_enc' WHERE uid=$uid");
}


// *****************************************
// FUNCTION: grant_user_rights($uid, $accGrant)
// *****************************************
function grant_user_rights($uid, $accGrant) {
	include("resources/config.php");

	run_sql_command("UPDATE users SET admin=$accGrant WHERE uid='$uid'");
}

// *****************************************
// FUNCTION: get_uid($login)
// *****************************************
// Returns -1 if user is the superuser defined in config.php
// Otherwise returns UID in users table
function get_uid($login) {
	include("resources/config.php");
	
	// If superuser, UID is -1
	if ($login == $superuser) {
		$uid = -1;
	}
	else {
		$uid = get_sql_value("SELECT uid FROM users WHERE login='$login'");
	}
		
	return $uid;
}


// *****************************************
// FUNCTION: get_login($uid)
// *****************************************
// Returns the login of UID
function get_login($uid) {
	include("resources/config.php");
	
	// If UID is -1, login is superuser
	if ($uid == -1) {
		$login = $superuser;
	}
	else {
		$login = get_sql_value("SELECT login FROM users WHERE uid=$uid");
	}
		
	return $login;
}


// *****************************************
// FUNCTION: get_user_type($uid)
// *****************************************
// Returns the user type of UID
function get_user_type($uid) {
	include("resources/config.php");
	
	// If UID is -1, login is superuser
	if ($uid == -1) {
		return "superuser";
	}
	else {
		return get_sql_value("SELECT type FROM users WHERE uid=$uid");
	}
}


// *****************************************
// FUNCTION: get_user_permission($uid)
// *****************************************
// Returns 1 if the user is an admin, 0 if not
function get_user_permission($uid) {
	include("resources/config.php");
	
	// If UID is -1, login is superuser
	if ($uid == -1) {
		return 1;
	}
	else {
		return get_sql_value("SELECT admin FROM users WHERE uid=$uid");
	}
}


// *****************************************
// FUNCTION: get_my_uid()
// *****************************************
// Returns the UID of the active logged in session
function get_my_uid() {
	include("resources/config.php");
	
	// Start session if necessary
	if (!isset($_SESSION)) {
		session_start();
	}

	return get_sql_value("SELECT uid FROM sessions WHERE session_id='" . session_id() . "'");
}


// *****************************************
// FUNCTION: am_i_admin();
// *****************************************
// Do I have admin rights?
function am_i_admin() {
	$uid = get_my_uid();
	
	// Are we superuser?
	if ($uid == -1) {
		return true;
		}
	
	// Check users table
	$admin = get_sql_value("SELECT admin FROM users WHERE uid=$uid") & 1;
	
	if ($admin == 1) {
		return true;
		}
	else {
		return false;
		}
	}
//End function

// *****************************************
// FUNCTION: grant_user_rights($uid, $accGrant)
// *****************************************
function user_rights() {
	$uid = get_my_uid();

	$admin = get_sql_value("SELECT admin FROM users WHERE uid=$uid");

	return $admin;
}


// *****************************************
// FUNCTION: check_ldap_credentials($user, $password)
// *****************************************
// Check credentials against LDAP server
function check_ldap_credentials($user, $password) {
	//Include config.php for LDAP server info
	include('resources/config.php');
	
	//Make sure a password was provided
	if ($password == '') {
		return false;
		}
	
	//Connect to LDAP server
	define(LDAP_OPT_DIAGNOSTIC_MESSAGE, 0x0032);
	$ldapconn = ldap_connect($ldap_server, $ldap_port) or die("Could not connect to LDAP server!");
		
	//Set LDAP options
	ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
	
	if ($ldapconn) {
		//Verify credentials
		$ldapbind = ldap_bind($ldapconn, $ldap_domain . "\\" . $user, $password);
		
		if($ldapbind) {
			return true;
		}
		else {
			return false;
		}
	}
	
	return false;
}
//End function


// *****************************************
// FUNCTION: ldap_user_search($username)
// *****************************************
// Searches for LDAP user
function ldap_user_search($username) {
	include('resources/config.php');
	
	$oLDAP = ldap_connect($ldap_server,389);

	ldap_set_option($oLDAP, LDAP_OPT_REFERRALS, 0);
	ldap_set_option($oLDAP, LDAP_OPT_PROTOCOL_VERSION, 3);

	$oDIR = ldap_bind($oLDAP, $ldap_user, $ldap_password);

	$sQuery = '(samaccountname=' . $username . '*)';
	$oSearch = ldap_search($oLDAP, $ldap_base_dn, $sQuery, array('samaccountname'));

	return ldap_get_entries($oLDAP, $oSearch);
}


// *****************************************
// FUNCTION: get_group_list($parent)
// *****************************************
// Returns array of all groups
function get_group_list($parent) {
	return get_sql_results("SELECT * FROM groups WHERE parent=$parent ORDER BY name");
	}
	
// *****************************************
// FUNCTION: get_permisiongroup_list($parent)
// *****************************************
// Returns array of all groups
function get_permisiongroup_list($parent) {
	return get_sql_results("SELECT * FROM permisiongroups WHERE parent=$parent ORDER BY name");
	}
	
// *****************************************
// FUNCTION: get_user_list()
// *****************************************
// Returns array of all groups
function get_user_list() {
	return get_sql_results("SELECT * FROM users ORDER BY login");
	}


// *****************************************
// FUNCTION: get_shared_users($gid)
// *****************************************
// Returns array of user info for all users shared with object with provided ID
// Returns NULL if object has not been shared with any users
function get_shared_users($gid) {
	return get_sql_results("SELECT * FROM vPasswordSharedUsers WHERE gid=$gid ORDER BY shared_login");
	}

// *****************************************
// FUNCTION: get_group_name($gid)
// *****************************************
// Returns name of group with provided gid
function get_group_name($gid) {
	return get_sql_value("SELECT name FROM groups WHERE gid=$gid");
	}

// *****************************************
// FUNCTION: get_permisiongroup_name($gid)
// *****************************************
// Returns name of group with provided gid
function get_permisiongroup_name($gid) {
	return get_sql_value("SELECT name FROM permisiongroups WHERE gid=$gid");
	}

// *****************************************
// FUNCTION: get_group_description($gid)
// *****************************************
// Returns description of group with provided GID
function get_group_description($gid) {
	return get_sql_value("SELECT description FROM groups WHERE gid=$gid");
	}
	
// *****************************************
// FUNCTION: get_permisiongroup_description($gid)
// *****************************************
// Returns description of group with provided GID
function get_permisiongroup_description($gid) {
	return get_sql_value("SELECT description FROM permisiongroups WHERE gid=$gid");
	}
	
// *****************************************
// FUNCTION: get_group_parent($gid)
// *****************************************
// Returns description of group with provided GID
function get_group_parent($gid) {
	return get_sql_value("SELECT parent FROM groups WHERE gid=$gid");
	}

// *****************************************
// FUNCTION: get_permisiongroup_parent($gid)
// *****************************************
// Returns description of group with provided GID
function get_permisiongroup_parent($gid) {
	return get_sql_value("SELECT parent FROM permisiongroups WHERE gid=$gid");
	}

// *****************************************
// FUNCTION: get_group_path($gid)
// *****************************************
// Returns path of group with provided GID
function get_group_path($gid) {
	$path = get_group_name($gid);
	
	if (get_group_parent($gid) != 0) {
		$gid = get_group_parent($gid);
		$path = get_group_name($gid) .'/' .$path;
	}
	
	return $path;
	}

// *****************************************
// FUNCTION: get_group_options($gid, $selected = 0, $without_group = 0)
// *****************************************
// Returns path of group with provided GID
function get_group_options($gid, $selected = 0, $without_group = 0) {
	echo $gid;
	
	$result = "";
	$results = get_group_list($gid);
	foreach ($results as $entry) {
		$id =  $entry['gid'];
		if (check_group_permissions($id, get_my_uid()))  {
			$name = $entry['name'];
			$selected_op = ($id == $selected ? "selected" : "");
		
			if ($id != $without_group) {
				$result = $result ."<option value=\"$id\" $selected_op>".get_group_path($id)."</option>";
				$result = $result .get_group_options($id, $selected, $without_group);
			}
		}
	}

	return $result;
	
	}

// *****************************************
// FUNCTION: get_group_options($gid, $selected = 0, $without_group = 0)
// *****************************************
// Returns path of group with provided GID
function get_permisiongroup_options($gid, $selected = 0, $without_group = 0) {
	echo $gid;
	
	$result = "";
	$results = get_permisiongroup_list($gid);
	foreach ($results as $entry) {
		$id =  $entry['gid'];
		$name = $entry['name'];
		$selected_op = ($id == $selected ? "selected" : "");
	
		if ($id != $without_group) {
			$result = $result ."<option value=\"$id\" $selected_op>".get_group_path($id)."</option>";
			$result = $result .get_permisiongroup_options($id, $selected, $without_group);
		}
	}

	return $result;
	
	}

// *****************************************
// FUNCTION: get_my_passwords($gid)
// *****************************************
// Get all passwords where my UID is the owner
function get_my_passwords($gid) {
	return get_sql_results("SELECT * FROM data WHERE group_id=$gid ORDER BY name");
	}

// *****************************************
// FUNCTION: get_groups($withoutgid = 0)
// *****************************************
// Get all groups 
function get_groups($withoutgid = 0) {
	return get_sql_results("SELECT * FROM groups WHERE gid <> $withoutgid ORDER BY name");
	}


// *****************************************
// FUNCTION: delete_user($uid)
// *****************************************
// Delete user UID and remove shared permissions
function delete_user($uid) {
	// Delete shared user permissions
	run_sql_command("DELETE FROM user_permissions WHERE uid=$uid");
	
	// Delete group membership
	run_sql_command("DELETE FROM group_members WHERE uid=$uid");
	
	// Delete the user entry
	run_sql_command("DELETE FROM users WHERE uid=$uid");
	}


// *****************************************
// FUNCTION: delete_group($gid)
// *****************************************
// Delete user UID and remove shared permissions
function delete_group($gid) {
	// Delete the group entry
	run_sql_command("DELETE FROM groups WHERE gid=$gid");
	}

// *****************************************
// FUNCTION: delete_group($gid)
// *****************************************
// Delete user UID and remove shared permissions
function delete_permisiongroup($gid) {
	// Delete shared group permissions
	run_sql_command("DELETE FROM group_permissions WHERE gid=$gid");
	
	// Delete group membership
	run_sql_command("DELETE FROM group_members WHERE gid=$gid");
	
	// Delete the group entry
	run_sql_command("DELETE FROM permisiongroups WHERE gid=$gid");
	}


// DATABASE FUNCTIONS *********************


// *****************************************
// FUNCTION: get_sql_value($query)
// *****************************************
// Return a single value from a single row
function get_sql_value($query) {
	// Include config.php for DB settings
	include("resources/config.php");
	
	// Connect to MySQL
	$oMySQL = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
	if (mysqli_connect_errno()) {
		echo("DB ERROR: " . mysqli_connect_error());
		die();
	}
	
	// Run query
	$result = mysqli_query($oMySQL, $query);
	
	// Return NULL if no rows returned
	if (mysqli_num_rows($result) == 0) {
		$sResult = NULL;
	}
	else {
		$row = mysqli_fetch_row($result);
		$sResult = $row[0];
	}
	
	// Close connection
	mysqli_close($oMySQL);
	
	return $sResult;
}


// *****************************************
// FUNCTION: get_sql_results($query)
// *****************************************
// Get array of results from a SQL query
function get_sql_results($query) {
	// Include config.php for DB settings
	include("resources/config.php");
	
	// Connect to MySQL
	$oMySQL = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
	if (mysqli_connect_errno()) {
		echo("DB ERROR: " . mysqli_connect_error());
		die();
	}
	
	// Run query
	$result = mysqli_query($oMySQL, $query);
	
	// Were results returned?
	if (mysqli_affected_rows($oMySQL) > 0) {
		// At least one row was returned
		// Build array of all rows
		while ($row = mysqli_fetch_array($result)) {
			$ret[] = $row;
		}
	}
	
	// No results, return null
	else {
		$ret = null;
		}
	
	// Close connection
	mysqli_close($oMySQL);
	
	return $ret;
}


// *****************************************
// FUNCTION: run_sql_command($query)
// *****************************************
// Run a raw SQL command
// Does not check output or return value
function run_sql_command($query) {
	// Include config.php for DB settings
	include("resources/config.php");
	
	// Connect to MySQL
	$oMySQL = mysqli_connect($db_host, $db_user, $db_pass, $db_name);	
	if (mysqli_connect_errno()) {
		echo "DB CONNECTION ERROR: " . mysqli_connect_error();
		die();
		}
	
	// Run query
	mysqli_query($oMySQL, $query);
	
	// Query error handling
	if (mysqli_errno($oMySQL)) {
		echo "DB QUERY ERROR: " . mysqli_error($oMySQL);
		die();
		}
	
	// Close connection
	mysqli_close($oMySQL);
}


// *****************************************
// FUNCTION: sqlescape($string)
// *****************************************
// Convert string into SQL string
function sqlescape($string) {
	// Include config.php for DB settings
	include("resources/config.php");
	
	// Connect to MySQL
	$oMySQL = mysqli_connect($db_host, $db_user, $db_pass, $db_name);	
	if (mysqli_connect_errno()) {
		echo "DB CONNECTION ERROR: " . mysqli_connect_error();
		die();
	}
	
	$escaped_string = mysqli_real_escape_string($oMySQL, $string);
	
	mysqli_close($oMySQL);
	
	return $escaped_string;
}


// STRING FUNCTIONS **********************


// *****************************************
// FUNCTION: string2html($string)
// *****************************************
// Make a string display nicely in HTML
function string2html($string) {	
	// Line break
	$string = str_replace("\n","<br>",$string);
	
	return $string;
}

// ENCRYPTION FUNCTIONS **********************


// *****************************************
// FUNCTION: encrypt_string($string)
// *****************************************
// Encrypt a string using the secret key
function encrypt_string($simple_string) {
	// Include config.php for secret key
	include("resources/config.php");
	
	// http://blog.justin.kelly.org.au/simple-mcrypt-encrypt-decrypt-functions-for-p/
	// return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $secret_key, $string, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
		
	$ciphering = "AES-128-CTR";
	$iv_length = openssl_cipher_iv_length($ciphering);
	$options = 0;
	$decryption_iv = '1234567891011121';
	$decryption_key = $secret_key;	
	echo openssl_encrypt($simple_string, $ciphering, $encryption_key, $options, $encryption_iv)."<br>";
	return openssl_encrypt($simple_string, $ciphering, $encryption_iv, $options, $encryption_key);
}


// *****************************************
// FUNCTION: decrypt_string($string)
// *****************************************
// Decrypt a string using the secret key
function decrypt_string($simple_string) {
	global $IsShowError;

	// Include config.php for secret key
	include("resources/config.php");
	
	// http://blog.justin.kelly.org.au/simple-mcrypt-encrypt-decrypt-functions-for-p/
	// return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $secret_key, base64_decode($string), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
	$ciphering = "AES-128-CTR";
	$iv_length = openssl_cipher_iv_length($ciphering);
	$options = 0;
	$encryption_iv = '1234567891011121';
	$encryption_key = $secret_key;

if ($IsShowError) {
	ini_set('display_errors', '0');
	ini_set('display_startup_errors', '0');
	error_reporting(E_NONE);
}

	return openssl_decrypt($simple_string, $ciphering, $decryption_key, $options, $decryption_iv);

if ($IsShowError) {
	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	error_reporting(E_ALL);
}
}


// *****************************************
// FUNCTION: get_decrypted_password($id, $uid)
// *****************************************
// Returns a decrypted password only if the user ID can access it
function get_decrypted_password($id, $uid) {

	// Check if I have permissions
	$encrypted_pass = get_sql_value("SELECT password FROM data WHERE id=$id");
	return decrypt_string($encrypted_pass);
}


// OBJECT FUNCTIONS **********************


// *****************************************
// FUNCTION: get_password_object($id)
// *****************************************
// Returns array of data for the provided ID
function get_password_object($id) {
	$results = get_sql_results("SELECT * FROM data WHERE id=$id");
	return $results[0];
	}
	

// *****************************************
// FUNCTION: edit_password_object($id, $name, $login, $password, $group, $note)
// *****************************************
// Updates data for the provided ID
function edit_password_object($id, $name, $login, $password, $group, $note) {
	run_sql_command("UPDATE data SET name='$name' WHERE id=$id");
	run_sql_command("UPDATE data SET login='$login' WHERE id=$id");
	run_sql_command("UPDATE data SET password='$password' WHERE id=$id");
	run_sql_command("UPDATE data SET group_id=$group WHERE id=$id");
	run_sql_command("UPDATE data SET note='$note' WHERE id=$id");
	}
	

// *****************************************
// FUNCTION: add_password_object($name, $login, $password, $group, $note)
// *****************************************
// Returns array of data for the provided ID
function add_password_object($name, $login, $password, $group, $note) {
//	echo "INSERT INTO data (name, login, password, note, group_id) VALUES ('$name', '$login', '$password', '$note', $group)";
	run_sql_command("INSERT INTO data (name, login, password, note, group_id) VALUES ('$name', '$login', '$password', '$note', $group)");
	}


// *****************************************
// FUNCTION: delete_password($id)
// *****************************************
// Delete password ID and all defined permissions associated	
function delete_password($id) {
	// Delete shared user permissions
	run_sql_command("DELETE FROM user_permissions WHERE id=$id");
	
	// Delete shared group permissions
	run_sql_command("DELETE FROM group_permissions WHERE id=$id");
	
	// Delete the password entry
	run_sql_command("DELETE FROM data WHERE id=$id");
	}


// *****************************************
// FUNCTION: get_object_name($gid)
// *****************************************
// Returns the name of object ID
function get_object_name($id) {
	return get_sql_value("SELECT name FROM data WHERE id=$id");
	}


// *****************************************
// FUNCTION: get_group_membership($uid)
// *****************************************
// Returns array of groups that the provided UID belongs to
function get_permissiongroup_membership($uid) {
	return get_sql_results("SELECT gid, name FROM vGroupMembers WHERE uid=$uid");
	}


// *****************************************
// FUNCTION: check_object_permissions($id, $uid)
// *****************************************
// Tests if a given UID has access to an pbject ID
function check_object_permissions($id, $uid) {
	// Check if UID is owner
	$gid = get_sql_value("SELECT group_id FROM data WHERE id=$id");
	return check_group_permissions($gid, $uid);
}

function check_group_permissions($id, $uid)  {
	
	$owner = get_sql_value("SELECT owner FROM groups WHERE gid=$id");
	if ($uid == $owner) {
		return true;
	}
	
	// Check if ID is shared with UID
	if (get_sql_value("SELECT id FROM user_permissions WHERE id=$id AND uid=$uid") != null) {
		return true;
	}

	// Check if ID is shared with groups
	$my_groups = get_permissiongroup_membership($uid);	// Get all groups that uid belongs to
	foreach ($my_groups as $group) {			// Iterate through all groups
		$gid = $group['gid'];
		if (get_sql_value("SELECT id FROM group_permissions WHERE gid='$gid' AND id='$id'") != null) {	// Check if object ID is shared with GID
			return true;
		}
	}

	$parent = get_sql_value("SELECT parent FROM groups WHERE gid=$id");
	if ($parent != 0)  {
		return check_group_permissions($parent, $uid);
	}
	
	
	// Deny otherwise
	return false;
}


// *****************************************
// FUNCTION: get_owner($gid)
// *****************************************
// Returns UID of owner for the provided password ID
function get_owner($gid) {
	return get_sql_value("SELECT owner FROM groups WHERE gid=$gid");
	}

// *****************************************
// FUNCTION: get_permisiongroup_members($gid)
// *****************************************
// Returns array of members of group with provided GID
function get_permisiongroup_members($gid) {
	return get_sql_results("SELECT uid, login FROM vGroupMembers WHERE gid=$gid");
	}

// *****************************************
// FUNCTION: add_user_to_group($gid, $uid)
// *****************************************
// Add user UID to group GID
function add_user_to_permisiongroup($gid, $uid) {
	// Check that user isn't already a member
	if (get_sql_value("SELECT uid FROM group_members WHERE gid=$gid AND uid=$uid") == NULL) {
		run_sql_command("INSERT INTO group_members (gid, uid) VALUES ('$gid', '$uid')");
		}
	}

// *****************************************
// FUNCTION: remove_user_from_permisiongroup($gid, $uid)
// *****************************************
// Remove user UID from group GID
function remove_user_from_permisiongroup($gid, $uid) {
	run_sql_command("DELETE FROM group_members WHERE gid=$gid AND uid=$uid");
	}

// *****************************************
// FUNCTION: share_with_user($gid, $uid, $mode = 'r')
// *****************************************
// Share object ID with user UID. Default mode is read-only.
function share_with_user($gid, $uid, $mode = 'r') {
	// Check that we aren't already sharing with this user
	if (get_sql_value("SELECT mode FROM user_permissions WHERE gid=$gid and uid=$uid") == NULL) {
		run_sql_command("insert into user_permissions (id, uid, mode) values ('$gid', '$uid', '$mode')");
		}
	}


function share_with_group($id, $gid) {
	if (get_sql_value("SELECT mode FROM group_permissions WHERE gid=$gid and id=$id") == NULL) {
		run_sql_command("insert into group_permissions (id, gid, mode) values ('$id', '$gid', '$mode')");
	}
}

// *****************************************
// FUNCTION: get_shared_groups($id)
// *****************************************
// Returns array of group info for all groups shared with object with provided ID
// Returns NULL if object has not been shared with any groups
function get_shared_groups($gid) {
	return get_sql_results("SELECT * FROM vPasswordSharedGroups WHERE gid=$gid ORDER BY shared_group");
	}

function unshare_user($id, $uid)  {
	run_sql_command("DELETE FROM user_permissions WHERE id=$id AND uid=$uid");
}

function get_permissiongroup_name($gid)  {
	return get_sql_value("SELECT name FROM permisiongroups WHERE gid=$gid");
}

function unshare_group($id, $gid)  {
	run_sql_command("DELETE FROM group_permissions WHERE id=$id AND gid=$gid");
}

function checkInPOST($name)  {
	if (array_key_exists($name, $_POST))
	{
		return $_POST[$name];
	}
	else
	{
		return "";
	}
}



class HttpAcceptLanguageHeaderLocaleDetector
{
  const HTTP_ACCEPT_LANGUAGE_HEADER_KEY = 'HTTP_ACCEPT_LANGUAGE';

  public static function detect()
  {
    $httpAcceptLanguageHeader = static::getHttpAcceptLanguageHeader();
    if ($httpAcceptLanguageHeader == null) {
      return [];
    }
    $locales = static::getWeightedLocales($httpAcceptLanguageHeader);
    $sortedLocales = static::sortLocalesByWeight($locales);
    return array_map(function ($weightedLocale) {
      return $weightedLocale['locale'];
    }, $sortedLocales);
  }
  
  private static function getHttpAcceptLanguageHeader()
  {
    if (isset($_SERVER[static::HTTP_ACCEPT_LANGUAGE_HEADER_KEY])) {
      return trim($_SERVER['HTTP_ACCEPT_LANGUAGE']);
    } else {
      return null;
    }
  }
  
  private static function getWeightedLocales($httpAcceptLanguageHeader)
  {
    if (strlen($httpAcceptLanguageHeader) == 0) {
      return [];
    }
    $weightedLocales = [];
    // We break up the string 'en-CA,ar-EG;q=0.5' along the commas,
    // and iterate over the resulting array of individual locales. Once
    // we're done, $weightedLocales should look like
    // [['locale' => 'en-CA', 'q' => 1.0], ['locale' => 'ar-EG', 'q' => 0.5]]
    foreach (explode(',', $httpAcceptLanguageHeader) as $locale) {
      // separate the locale key ("ar-EG") from its weight ("q=0.5")
      $localeParts = explode(';', $locale);
      $weightedLocale = ['locale' => $localeParts[0]];
      if (count($localeParts) == 2) {
        // explicit weight e.g. 'q=0.5'
        $weightParts = explode('=', $localeParts[1]);
        // grab the '0.5' bit and parse it to a float
        $weightedLocale['q'] = floatval($weightParts[1]);
      } else {
        // no weight given in string, ie. implicit weight of 'q=1.0'
        $weightedLocale['q'] = 1.0;
      }
      $weightedLocales[] = $weightedLocale;
    }  
    return $weightedLocales;
  }
  
  /**
   * Sort by high to low `q` value
   */
  private static function sortLocalesByWeight($locales)
  {
    usort($locales, function ($a, $b) {
      // usort will cast float values that we return here into integers,
      // which can mess up our sorting. So instead of subtracting the `q`,
      // values and returning the difference, we compare the `q` values and
      // explicitly return integer values.
      if ($a['q'] == $b['q']) {
        return 0;
      }
      if ($a['q'] > $b['q']) {
        return -1;
      }
      return 1;
    });
    return $locales;
  }
}

function getUserLang()
{
	if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		$localeToUse = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
	}
	else {
		$localeToUse = Locale::getDefault();
	}

    if(strstr($localeToUse, '-') === FALSE)
    {
        switch($localeToUse )
        {
            case 'pl': $localeToUse  = 'pl-PL'; break;
            case 'en': $localeToUse  = 'en-US'; break;
        }
    }
	$localeToUse =  str_replace("-", "_", $localeToUse);
    return $localeToUse;
}

?>
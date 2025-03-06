<?php

	include('resources/functions.php');
	include('resources/config.php');

$fileContent = ";name;login;password;notes".chr(13);

function show_password($gid, $level) {
	global $fileContent;
	
	$results = get_my_passwords($gid);
	foreach ($results as $entry) {
		$id =  $entry['id'];
		$name = $entry['name'];
		$login = htmlspecialchars_decode(decrypt_string($entry['login']));
		$password = htmlspecialchars_decode(decrypt_string($entry['password']));
		$notes = $entry['note'];
		
		$fileContent .= ";".$name.";".$login.";".$password.";".str_replace(chr(13).chr(10), "\\n", $notes).chr(13);
	}
}

function show_group($gid, $level = 0) {
	global $fileContent;
	
	$groups = get_group_list($gid);
	foreach ($groups as $group) {
		$id = $group['gid'];
		if (check_group_permissions($id, get_my_uid())) {
			$path = get_group_path($group['gid']);
			$description = get_group_description($group['gid']);

			$fileContent .= $path." - ".$description.chr(13);
			
			show_password($id, $level);
			
			show_group($id, $level +1);
		}
	}
}

 show_group(0);


//	check_session_login();

	header("Content-type: text/plain; charset=ISO-8859-2");
	header("Content-Disposition: attachment; filename*=ISO-8859-2''exportpassword.csv");
	header("Pragma: no-cache");
	header("Expires: 0");
		
	echo mb_convert_encoding( $fileContent, 'ISO-8859-2', 'UTF-8');
		
//	echo $fileContent
?>

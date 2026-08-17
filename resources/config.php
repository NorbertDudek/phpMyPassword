<?php
// Require HTTPS?
// If true, all pages will automatically redirect to their HTTPS address
$require_https = false;

// Database info
$db_host = 'localhost';
$db_name = 'ndudek_password';
$db_user = 'ndudek_password';
$db_pass = 'Dupi?8kupide';

// Secret_key is the hash that will be used for encrypting/decrypting all password entries.
// DO NOT CHANGE THE SECRET KEY AFTER ADDING DATA
// YOU WILL NOT BE ABLE TO RETRIEVE ANY DATA THAT WAS CREATED PRIOR TO CHANGING THE SECRET KEY
$secret_key = 'k|M8-VUC{roF&S{KZxF1_)>?4{d)iM;?)oXW5)y;]ZXrF"r6QuoiH1@,RaIY0|2';

// Locally defined superuser account
// This is the default login with full admin rights.
// It should not be used other than to create other logins and groups.
$superuser = 'pmvadmin';
$superuser_password = 'password';

// Once session idle timeout is reached users will have to login again
$session_timeout = "900";	// 900 seconds = 15 minutes

// Set to false for a more 'professional' look
$doge = true;
?>

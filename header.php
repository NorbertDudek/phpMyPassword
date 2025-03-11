<?php
// Redirect to install.php for initial configuration
$expl = explode('/', $_SERVER['SCRIPT_FILENAME']);
$expl[count($expl) - 1] = "install.php";
$install_php = implode('/', $expl);
if (file_exists($install_php)) {
	header('Location: install.php');
}
else {

	// Necessary includes
	include('resources/functions.php');
	include('resources/config.php');

	// Redirect to HTTPS if necessary
	if ($require_https == true) {
		if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == '') {
			$redir = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			header("Location: $redir");
		}
	}

	// Check login, redirect to login page if necessary
	check_session_login();

	// Do not render page if admin privileges are required
	if (isset($requireadmin)) {
		if ($requireadmin) {
			if (!am_i_admin()) {
				header('Location: show_permissions_error.php');
			}
		}
	}
 
}

if (basename($_SERVER['PHP_SELF']) != 'login.php')
    $myRights = user_rights();


/// language

$language = getUserLang();
putenv("LANG=" . $language); 
setlocale(LC_ALL, $language);
$domain = "message";
bindtextdomain($domain, "locale"); 
bind_textdomain_codeset($domain, 'UTF-8');

textdomain($domain);
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" type="image/x-icon" href="images/favicon.jpg">
		
		<title>phpPassMan</title>
		
		<!-- jQuery -->
		<script src="resources/jquery/jquery.min.js"></script>
		<script src="resources/jquery/jquery-ui.min.js"></script>
		<link rel="stylesheet" type="text/css" href="resources/jquery/jquery-ui.min.css"/>
		
		<!-- Bootstrap -->
		<link rel="stylesheet" type="text/css" href="resources/bootstrap/css/bootstrap.css"/>
		<link rel="stylesheet" type="text/css" href="resources/bootstrap/css/bootstrap-theme.css"/>
		<script src="resources/bootstrap/js/bootstrap.min.js"></script>

	</head>

	<body>
		
		<div class="bg-primary">
			<div class="container">
				<div class="row">
					<div class="col-xs-6">
						<h1>phpPassMan</h1>Powered by <a target="blank" href="https://kandisoft.pl">KANDISoft</a>
					</div>
					<div class="col-md-6">
						
						<div class="text-right">
							 <br><img src="images/logo.png">
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<p/>
		<?php if (basename($_SERVER['PHP_SELF']) != 'login.php') { ?>
		<div class="container">	
			<div class="navbar navbar-default">
				<div class="navbar-collapse collapse">
					<ul class="navbar-nav nav">
						<li><a href="index.php"><?php echo _("Passwords"); ?></a></li>
						<!---
						<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Passwords <span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="index.php">All</a></li>
								<li><a href="index.php?show=mine">Owned by me</a></li>
								<li><a href="index.php?show=shared">Shared with me</a></li>
							</ul>
						</li>
						--->
						<?php
						if (($myRights & accGroupSee) != 0)
						{
						?>
						<li><a href="groups.php"><?php echo _("Groups"); ?></a></li>
						<?php
						}
						?>
						

						<?php
						if (($myRights & (accPasswordAdd | accGroupAdd)) != 0)
						{
						?>
						<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo _("Add"); ?><span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<?php
								if (($myRights & accPasswordAdd) != 0)
								{
								?>
								<li><a href="add.php"><?php echo _("Password"); ?></a></li>
								<?php
								}
								if (($myRights & accGroupAdd) != 0)
								{
								?>
								<li><a href="add_group.php"><?php echo _("Group"); ?></a></li>
								<?php
								}
								?>
							</ul>
						</li>
						<?php
						}
						?>
						
						<?php
						if ((($myRights & accExport) != 0) and (isset($exportvisible)))
						{
						?>
						<li><a href="export.php"><?php echo _("Export"); ?></a></li>
						<?php
						}
						?>
						
					</ul>
					<ul class="navbar-nav nav navbar-right">
						<?php if (am_i_admin()) { ?>
						<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo _("Admin"); ?> <span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="admin.php"><?php echo _("Users"); ?></a></li>
								<li><a href="permisiongroups.php?show=mine"><?php echo _("Groups"); ?></a></li>
							</ul>
						</li>
						<?php } ?>
						<li><a href="logout.php"><?php echo _("Logout"); ?></a></li>
					</ul>
				</div>
			</div>
		</div>
		
		<div class="container">
			<p class="text-right"><?php echo _("Logged in as:")." "; echo get_login(get_my_uid()); ?></p>
      <?php } ?>

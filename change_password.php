<?php
include('resources/functions.php');
include('resources/config.php');

// Redirect to HTTPS if necessary
if ($require_https == true) {
	if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == '') {
		$redir = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		header("Location: $redir");
		exit;
	}
}

// Must be logged in to be here
check_session_login();

$uid = get_my_uid();

// If this account isn't (or is no longer) required to change its
// password, there's nothing to do here
if (!get_must_change_password($uid)) {
	header('Location: index.php');
	exit;
}

// Language setup
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
		<link rel="stylesheet" type="text/css" href="resources/bootstrap/css/bootstrap.css"/>
		<link rel="stylesheet" type="text/css" href="resources/bootstrap/css/bootstrap-theme.css"/>
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

		<div class="container">
			<div class="row">
				<!-- lefthand padding -->
				<div class="col-sm-2">&nbsp;</div>
				<div class="col-sm-8">
					<div class="panel panel-primary">
						<div class="panel-heading"><?php echo _("You must change your password"); ?></div>
						<div class="panel-body">
							<?php if (isset($_GET['error'])) { ?>
							<div class="alert alert-danger">
								<?php
								if ($_GET['error'] == 'mismatch') {
									echo _("Passwords do not match.");
								}
								elseif ($_GET['error'] == 'empty') {
									echo _("Please enter a new password.");
								}
								?>
							</div>
							<?php } ?>

							<p><?php echo _("For security reasons, you must set a new password before you can continue."); ?></p>

							<form method="POST" action="do_change_password.php" class="form-horizontal">
								<div class="form-group">
									<label for="password" class="col-sm-4 control-label"><?php echo _("New password"); ?>:</label>
									<div class="col-sm-8">
										<input type="password" class="form-control" name="password" placeholder="<?php echo _("New password"); ?>" maxlength="128">
									</div>
								</div>
								<div class="form-group">
									<label for="confirm_password" class="col-sm-4 control-label"><?php echo _("Confirm password"); ?>:</label>
									<div class="col-sm-8">
										<input type="password" class="form-control" name="confirm_password" placeholder="<?php echo _("Confirm password"); ?>" maxlength="128">
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-8 col-sm-offset-4">
										<button type="submit" class="btn btn-primary"><?php echo _("Set new password"); ?></button>
									</div>
								</div>
							</form>

							<p><a href="logout.php"><?php echo _("Logout"); ?></a></p>
						</div>
					</div>
				</div>
			</div>
		</div>

	</body>
</html>

<!--
	phpMyPassword is released under the terms of the GNU General
	Public License as published by the Free Software Foundation
	(version 3).

	phpMyPassword is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    A copy of the GNU General Public License should be included
	along with phpMyPassword in the file LICENSE.txt. If not, see
	<http://www.gnu.org/licenses/>.
-->

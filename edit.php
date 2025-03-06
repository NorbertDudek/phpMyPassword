<?php require_once('header.php'); ?>

<?php
$id = $_GET['id'];


// Check permissions
if (!(check_object_permissions($id, get_my_uid()))) {
	// We don't have permissions!	?>
	<div class="bg-danger center-block">ACCESS DENIED</div>
<?php	}
else {
	// Permissions are good
	$pass_entry = get_password_object($id);

	$name = $pass_entry['name'];
	$login = decrypt_string($pass_entry['login']);
	$password = decrypt_string($pass_entry['password']);
	$note = $pass_entry['note'];
	$group = $pass_entry['group_id'];
?>

<div class="row">
	<!-- lefthand padding -->
	<div class="col-sm-3">&nbsp;</div>
	<div class="col-sm-6">
		<div class="panel panel-primary">
			<div class="panel-heading"><strong><?php echo _("Edit object"); ?>:</strong> <?php echo $name;?></div>
			<div class="panel-body">

				<form action="do_edit.php" class="form-horizontal" method="POST">
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<div class="form-group">
						<label for="name" class="col-sm-3 control-label"><?php echo _("Name"); ?>:</label>
						<div class="col-sm-9"><input type="text" class="form-control" name="name" maxlength="256" value="<?php echo $name;?>"></div>
					</div>
					<div class="form-group">
						<label for="login" class="col-sm-3 control-label"><?php echo _("User name"); ?>:</label>
						<div class="col-sm-9"><input type="text" class="form-control" name="login" maxlength="256" value="<?php echo $login; ?>"></div>
					</div>
					<div class="form-group">
						<label for="group" class="col-sm-3 control-label"><?php echo _("Group"); ?>:</label>
						<div class="col-sm-9"><select class="form-control" name="group">
						<?php
							echo get_group_options(0, $group);
						?>
						</select></div>
					</div>
					<div class="form-group">
						<label for="password" class="col-sm-3 control-label"><?php echo _("Password"); ?>:</label>
						<div class="col-sm-9"><input type="password" class="form-control" name="password" maxlength="256" value="<?php echo $password; ?>"></div>
					</div>
					<div class="form-group">
						<label for="note" class="col-sm-3 control-label"><?php echo _("Notes"); ?>:</label>
						<div class="col-sm-9"><textarea class="form-control" name="note" rows="4"><?php echo $note; ?></textarea></div>
					</div>
					<div class="form-group">
						<div class="col-sm-3"></div>
						<div class="col-sm-9"><input type="submit" class="btn btn-sm btn-primary" value="<?php echo _("Save changes"); ?>"></div>
					</div>
					<div class="col-sm-12">&nbsp;</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php } ?>

<?php require_once('footer.php'); ?>
<?php
require_once('header.php');

$gid = $_GET['gid'];
// Check permissions
if (!(check_group_permissions($gid, get_my_uid()))) {
	// We don't have permissions!	?>
	<div class="bg-danger center-block">ACCESS DENIED</div>
<?php	}
else {
	// Permissions are good
$group_name = get_group_name($gid);
$group_description = get_group_description($gid);
$group_parent = get_group_parent($gid);
$shared_users = get_shared_users($gid);
$shared_groups = get_shared_groups($gid);

?>

<div class="row">
	<!-- lefthand padding -->
	<div class="col-sm-3">&nbsp;</div>
	<div class="col-sm-6">
		<div class="panel panel-primary">
			<div class="panel-heading">Edit Group</div>
			<div class="panel-body">
				<form action="do_edit_group.php" method="POST" class="form-horizontal">
					<input type="hidden" name="gid" value="<?php echo $gid?>">
					<div class="form-group">
						<label class="control-label col-sm-3">ID:</label>
						<div class="col-sm-9"><p class="form-control-static"><?php echo $gid; ?></p></div>
					</div>
					<div class="form-group">
						<label for="group_name" class="control-label col-sm-3">Name:</label>
						<div class="col-sm-9"><input type="text" class="form-control" id="group_name" name="group_name" maxlength="128" value="<?php echo $group_name; ?>"></div>
					</div>
					<div class="form-group">
						<label for="parent" class="col-sm-3 control-label">Parent:</label>
						<div class="col-sm-9"><select class="form-control" name="parent">
						<option value="0"></option>';
						<?php
							echo get_group_options(0, $group_parent, $gid);
						?>
						</select></div>
					</div>
					<div class="form-group">
						<label for="description" class="col-sm-3 control-label">Description:</label>
						<div class="col-sm-9"><textarea name="description" class="form-control" rows="4" cols="40" maxlength="1024"><?php echo $group_description; ?></textarea></div>
					</div>
					<div class="form-group">
						<div class="col-sm-3">&nbsp;</div>
						<div class="col-sm-9">
							<input type="submit" class="btn btn-sm btn-primary" value="Save Changes">
						</div>
					</div>
				</form>
				
				<form action="share_with_user.php" class="form-horizontal" method="POST">
					<input type="hidden" name="id" value="<?php echo $gid; ?>">
					<div class="form-group">
						<label for="shared_users" class="col-sm-3 control-label">Shared Users:</label>
						<div class="col-sm-9">
							<select class="form-control input-sm" name="shared_users[]" multiple>
							<?php foreach ($shared_users as $user) {
								$shared_uid = $user['shared_uid'];
								$shared_username = $user['shared_login']; ?>
								<option value="<?php echo $shared_uid;?>"><?php echo $shared_username;?></option>
								<?php } //End ForEach ?>
							</select>
							<button class="btn btn-xs btn-primary" name="action" value="add">Add User(s)</button>
							<button class="btn btn-xs btn-danger" name="action" value="remove">Remove Selected User(s)</button>
						</div>
					</div>
				</form>
				<form action="share_with_group.php" class="form-horizontal" method="POST">
					<input type="hidden" name="id" value="<?php echo $gid; ?>">
					<div class="form-group">
						<label for="shared_groups" class="col-sm-3 control-label">Shared Groups:</label>
						<div class="col-sm-9">
							<select class="form-control input-sm" name="shared_groups[]" multiple>
							<?php foreach ($shared_groups as $group) {
								$shared_gid = $group['shared_gid'];
								$shared_group = $group['shared_group']; ?>
								<option value="<?php echo $shared_gid;?>"><?php echo $shared_group;?></option>
								<?php } //End ForEach ?>
							</select>
							<button class="btn btn-xs btn-primary" name="action" value="add">Add Group(s)</button>
							<button class="btn btn-xs btn-danger" name="action" value="remove">Remove Selected Group(s)</button>
						</div>
					</div>
				</form>
				
				
			</div>
		</div>
	</div>
</div>

<?php } ?>

<?php
require_once('footer.php');
?>
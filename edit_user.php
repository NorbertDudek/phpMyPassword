<?php
$requireadmin = true;
require_once('header.php');

$uid = $_GET['uid'];
$login = get_login($uid);
$type = get_user_type($uid);
$is_admin = get_user_permission($uid) & accAdmin;
$export = get_user_permission($uid) & accExport;
$passwordAdd = get_user_permission($uid) & accPasswordAdd;
$passwordEdit = get_user_permission($uid) & accPasswordEdit;
$passwordRemove = get_user_permission($uid) & accPasswordRemove;
$groupAdd = get_user_permission($uid) & accGroupAdd;
$groupEdit = get_user_permission($uid) & accGroupEdit;
$groupRemove = get_user_permission($uid) & accGroupRemove;
$groupSee = get_user_permission($uid) & accGroupSee;
?>

<div class="row">
	<!-- lefthand padding -->
	<div class="col-sm-3">&nbsp;</div>
	<div class="col-sm-6">
		<div class="panel panel-primary">
			<div class="panel-heading"><strong>Edit User</strong></div>
			<div class="panel-body">
				<form action="do_edit_user.php" method="POST" class="form-horizontal">
					<input type="hidden" name="uid" value="<?php echo $uid?>">
					<div class="form-group">
						<label class="control-label col-sm-3">Login:</label>
						<div class="col-sm-9"><p class="form-control-static"><?php echo $login; ?></p></div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Type:</label>
						<div class="col-sm-9"><p class="form-control-static"><?php echo $type; ?></p></div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Change Password:</label>
						<div class="col-sm-9">
							<p><input type="password" class="form-control" name="password" placeholder="New Password" maxlength="128" <?php if (($type == "ldap") OR ($type == "superuser")) { echo "disabled"; }?>></p>
							<p><input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" maxlength="128" <?php if (($type == "ldap") OR ($type == "superuser")) { echo "disabled"; }?>></p>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="admin1">Admin?</label>
						<div class="col-sm-9"><p class="form-control-static"><input type="checkbox" name="admin" <?php if ($is_admin == 1) { echo "checked"; }?>></p></div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="export">Export</label>
						<div class="col-sm-9"><p class="form-control-static"><input type="checkbox" name="export" <?php if ($export  != 0) { echo "checked"; }?>></p></div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="Password access">Password access</label>
						<div class="col-sm-9">
							<p class="form-control-static">
								<input type="checkbox" name="passwordAdd" <?php if ($passwordAdd != 0) { echo "checked"; }?>> Add &nbsp;&nbsp;&nbsp;
								<input type="checkbox" name="passwordEdit" <?php if ($passwordEdit != 0) { echo "checked"; }?>> Edit &nbsp;&nbsp;&nbsp;
								<input type="checkbox" name="passwordRemove" <?php if ($passwordRemove != 0) { echo "checked"; }?>> Delete &nbsp;&nbsp;&nbsp;
							</p>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="Group access">Group access</label>
						<div class="col-sm-9">
							<p class="form-control-static">
								<input type="checkbox" name="groupAdd" <?php if ($groupAdd != 0) { echo "checked"; }?>> Add &nbsp;&nbsp;&nbsp;
								<input type="checkbox" name="groupEdit" <?php if ($groupEdit != 0) { echo "checked"; }?>> Edit &nbsp;&nbsp;&nbsp;
								<input type="checkbox" name="groupRemove" <?php if ($groupRemove != 0) { echo "checked"; }?>> Delete &nbsp;&nbsp;&nbsp;
								<input type="checkbox" name="groupSee" <?php if ($groupSee != 0) { echo "checked"; }?>> See &nbsp;&nbsp;&nbsp;
							</p>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-3">&nbsp;</div>
						<div class="col-sm-9">
							<input type="submit" class="btn btn-sm btn-primary" value="Save Changes">
							<?php if ($type != "superuser") { ?><a href="delete_user.php?uid=<?php echo $uid;?>" class="btn btn-sm btn-danger">Delete User</a> <?php } ?>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php
require_once('footer.php');
?>
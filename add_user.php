<?php
require_once('header.php');
?>

<div class="row">
	<!-- lefthand padding -->
	<div class="col-sm-3">&nbsp;</div>
	<div class="col-sm-6">
		<div class="panel panel-primary">
			<div class="panel-heading">Add Local User</div>
			<div class="panel-body">
				<form action="do_add_user.php" method="POST" class="form-horizontal">
					<input type="hidden" name="type" value="local">
					<div class="form-group">
						<label for="local_login" class="control-label col-sm-3">Login:</label>
						<div class="col-sm-9"><input type="text" class="form-control" id="local_login" name="login" maxlength="32"></div>
					</div>
					<div class="form-group">
						<label for="local_password" class="col-sm-3 control-label">Password:</label>
						<div class="col-sm-9"><input type="password" class="form-control" id="local_password" name="password" maxlength="128"></div>
					</div>
					<div class="form-group">
						<div class="col-sm-3">&nbsp;</div>
						<div class="col-sm-9"><input type="checkbox" id="local_admin" name="admin"> Admin User</div>
					</div>
					<div class="form-group">
						<div class="col-sm-3">&nbsp;</div>
						<div class="col-sm-9"><input type="checkbox" id="local_force_password_change" name="force_password_change"> User must change password on next login</div>
					</div>
					<div class="form-group">
						<div class="col-sm-3">&nbsp;</div>
						<div class="col-sm-9"><input type="submit" class="btn btn-sm btn-primary" value="Add Local User"></div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<?php
require_once('footer.php');
?>
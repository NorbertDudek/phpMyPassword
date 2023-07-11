<?php
$requireadmin = true;
require_once('header.php');

$gid = $_GET['gid'];
$group_name = get_group_name($gid);
$group_description = get_group_description($gid);
$group_parent = get_group_parent($gid);
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
			</div>
		</div>
	</div>
</div>

<?php
require_once('footer.php');
?>
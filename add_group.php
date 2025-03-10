<?php
$requireadmin = false;
require_once('header.php');
?>

<div class="row">
	<!-- lefthand padding -->
	<div class="col-sm-3">&nbsp;</div>
	<div class="col-sm-6">
		<div class="panel panel-primary">
			<div class="panel-heading"><?php echo _("Add group"); ?></div>
			<div class="panel-body">
				<form action="do_add_group.php" method="POST" class="form-horizontal">
					<div class="form-group">
						<label for="group_name" class="control-label col-sm-3"><?php echo _("Name"); ?>:</label>
						<div class="col-sm-6"><input type="text" class="form-control" id="group_name" name="group_name" maxlength="128"></div>
					</div>
					<div class="form-group">
						<label for="parent" class="col-sm-3 control-label"><?php echo _("Parent"); ?>:</label>
						<div class="col-sm-6"><select class="form-control" name="parent">
						<option value="0"></option>';
						<?php
							echo get_group_options(0, $gid);
						?>
						</select></div>
					</div>
					<div class="form-group">
						<label for="description" class="col-sm-3 control-label"><?php echo _("Notes"); ?>:</label>
						<div class="col-sm-6"><textarea name="description" class="form-control" rows="4" cols="40" maxlength="1024"></textarea></div>
					</div>
					<div class="form-group">
						<div class="col-sm-3">&nbsp;</div>
						<div class="col-sm-6"><input type="submit" class="btn btn-xs btn-primary" value="Add Group"></div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php
require_once('footer.php');
?>
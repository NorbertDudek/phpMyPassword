<?php
$groups = get_permisiongroup_list(0);
?>

<form method="POST" action="do_share_with_group.php">
	<input type="hidden" name="id" value="<?php echo $id; ?>">
	
	<div class="row">
		<!-- lefthand padding -->
		<div class="col-sm-3">&nbsp;</div>
		<div class="col-sm-6">
			<div class="panel panel-primary">
				<div class="panel-heading"><strong><?php echo _("Share with group"); ?>:</strong> <?php echo get_object_name($id); ?></div>
				<div class="panel-body">
					<?php echo _("Select a group"); ?>:
					<select class="form-control input-lg" name="gid[]" multiple>
					<?php foreach ($groups as $group) { ?>
						<option value="<?php echo $group['gid'];?>"><?php echo $group['name'];?></option>
						<?php } ?>
					</select>
					<p><small><em><?php echo _("Use CTRL+Click to select multiple groups."); ?></em></small></p>
					<input type="submit" class="btn btn-sm btn-primary" value="<?php echo _("Share"); ?>">
				</div>
			</div>
		</div>
	</div>

</form>
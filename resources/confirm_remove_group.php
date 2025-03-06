<form action="do_remove_group.php" method="POST">
	<input type="hidden" name="id" value="<?php echo $id;?>">

	<div class="row">
		<!-- lefthand padding -->
		<div class="col-sm-3">&nbsp;</div>
		<div class="col-sm-6">
			<div class="panel panel-primary">
				<div class="panel-heading"><strong><?php echo _("Confirm removal"); ?></strong></div>
				<div class="panel-body">
					<p>
						<?php echo _("Please confirm you would like to remove the following group"); ?>:
						<ul>
							<?php foreach ($_POST['shared_groups'] as $gid) { ?>
							<li><?php echo get_permissiongroup_name($gid);?></li>
							<input type="hidden" name="gid[]" value="<?php echo $gid;?>">
							<?php } ?>
						</ul>
					</p>
					<p>
						<?php echo _("Users belonging to these groups will no longer be able to view the following object"); ?>:
						<ul>
							<li><?php echo get_group_name($id); ?>
						</ul>
					</p>
					<p><input type="submit" class="btn btn-sm btn-primary" value="<?php echo _("Confirm"); ?>"></p>
				</div>
			</div>
		</div>
	</div>
</form>
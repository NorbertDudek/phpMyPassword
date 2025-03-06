<?php
$requireadmin = false;
require_once('header.php');
require_once('resources/stack.php');
?>

<h2><?php echo _("Groups"); ?>:</h2>
<table class="table table-hover">
	<thead>
		<tr>
			<th><?php echo _("Group Name"); ?></th>
			<th><?php echo _("Notes"); ?></th>
			<th><?php echo _("Actions"); ?></th>
		</tr>
	</thead>
	<tbody>

<?php


function echoGroup($level, $name, $description, $gid)  {
	$myRights = user_rights();
?>
			<tr>
				<td><?php echo str_repeat('&nbsp;', $level *4); echo $name;?></td>
				<td><?php echo $description;?></td>
				<td>
					<?php if (($myRights & accGroupEdit) != 0) { ?>
						<a href="edit_group.php?gid=<?php echo $gid; ?>" class="btn btn-xs btn-primary"><?php echo _("Edit"); ?></a>
					<?php } ?>
					<?php if (($myRights & accGroupRemove) != 0) { ?>
						<a href="delete_group.php?gid=<?php echo $gid; ?>" class="btn btn-xs btn-danger"><?php echo _("Delete"); ?></a>
					<?php } ?>
				</td>
			</tr>
<?php

}

function list_groups($parent, $level = 0) {
	
	global $groupstack;
	
	if ($level >= 10)
		return;
		
	$groups = get_group_list($parent);
	
	if (!is_null($groups)) 
		foreach ($groups as $group) {
			if ((check_group_permissions($group['gid'], get_my_uid())) || am_i_admin())  {
				echoGroup($level, $group['name'], $group['description'], $group['gid'] );
				list_groups($group['gid'], $level +1);
				}
			}
}

list_groups(0);
	?>	




	</tbody>
</table>
<div><a href="add_group.php" class="btn btn-sm btn-primary"><?php echo _("Add new group"); ?></a></div>

<?php
require_once('footer.php');
?>
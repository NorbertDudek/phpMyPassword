<?php
$requireadmin = false;
require_once('header.php');
?>

<h2>Groups:</h2>
<table class="table table-hover">
	<thead>
		<tr>
			<th>Group Name</th>
			<th>Description</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>

<?php


function list_groups($parent, $level = 0) {
	
	if ($level >= 10)
		return;
		
	$groups = get_group_list($parent);
	foreach ($groups as $group) {
	?>
			<tr>
				<td><?php echo str_repeat('&nbsp;', $level *4); echo $group['name'];?></td>
				<td><?php echo $group['description'];?></td>
				<td><a href="edit_group.php?gid=<?php echo $group['gid']; ?>" class="btn btn-xs btn-primary">Edit</a>
					<a href="delete_group.php?gid=<?php echo $group['gid']; ?>" class="btn btn-xs btn-danger">Delete</a>
				</td>
			</tr>
	<?php
		list_groups($group['gid'], $level +1);
		}
}

list_groups(0);
	?>	




	</tbody>
</table>
<div><a href="add_group.php" class="btn btn-sm btn-primary">Add New Group</a></div>

<?php
require_once('footer.php');
?>
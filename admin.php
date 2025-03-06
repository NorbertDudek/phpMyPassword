<?php
$requireadmin = true;
require_once('header.php');
?>

<table class="table table-hover">
	<thead>
		<tr>
			<th rowspan="2" style="text-align:left;vertical-align:middle">User</th>
			<th rowspan="2" style="text-align:left;vertical-align:middle">Type</th>
			<th rowspan="2" style="text-align:center;vertical-align:middle">Admin</th>
			<th rowspan="2" style="text-align:center;vertical-align:middle">Export access</th>
			<th colspan="3" style="text-align:center;vertical-align:middle">Password access</th>
			<th colspan="4" style="text-align:center;vertical-align:middle">Group access</th>
			<th rowspan="2" align="center">Action</th>
		</tr>
		<tr>
			<th style="text-align:center;vertical-align:middle">Add</th>
			<th style="text-align:center;vertical-align:middle">Edit</th>
			<th style="text-align:center;vertical-align:middle">Remove</th>
			<th style="text-align:center;vertical-align:middle">See</th>
			<th style="text-align:center;vertical-align:middle">Add</th>
			<th style="text-align:center;vertical-align:middle">Edit</th>
			<th style="text-align:center;vertical-align:middle">Remove</th>
		</tr>
	</thead>
	<tbody>
	
<?php
$users = get_user_list();
foreach ($users as $user) {
?>

		<tr>
			<td><?php echo $user['login'];?></td>
			<td><?php echo $user['type'];?></td>
			<td align="center"><?php if (($user['admin'] & accAdmin) != 0) { echo "✓"; }?></td>
			<td align="center"><?php if (($user['admin'] & accExport) != 0) { echo "✓"; }?></td>
			<td align="center"><?php if (($user['admin'] & accPasswordAdd) != 0) { echo "✓"; }?></td>
			<td align="center"><?php if (($user['admin'] & accPasswordEdit) != 0) { echo "✓"; }?></td>
			<td align="center"><?php if (($user['admin'] & accPasswordRemove) != 0) { echo "✓"; }?></td>
			<td align="center"><?php if (($user['admin'] & accGroupSee) != 0) { echo "✓"; }?></td>
			<td align="center"><?php if (($user['admin'] & accGroupAdd) != 0) { echo "✓"; }?></td>
			<td align="center"><?php if (($user['admin'] & accGroupEdit) != 0) { echo "✓"; }?></td>
			<td align="center"><?php if (($user['admin'] & accGroupRemove) != 0) { echo "✓"; }?></td>
			
			<td><a href="edit_user.php?uid=<?php echo $user['uid'];?>" class="btn btn-xs btn-primary">Edit</a>
				<a href="delete_user.php?uid=<?php echo $user['uid'];?>" class="btn btn-xs btn-danger">Delete</a>
			</td>
		</tr>

<?php
	}
?>

	</tbody>
</table>
<div><a href="add_user.php" class="btn btn-sm btn-primary">Add New User</a></div>

<p>&nbsp;</p>

<?php
require_once('footer.php');
?>
<?php
$requireadmin = true;
require_once('header.php');
?>

<table class="table table-hover">
	<thead>
		<tr>
			<th>User</th>
			<th>Type</th>
			<th>Admin?</th>
			<th>Actions</th>
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
			<td><?php if ($user['admin']==1) { echo "Admin"; }?></td>
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
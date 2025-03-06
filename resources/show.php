<?php require_once('header.php'); ?>

<?php
$id = $_GET['id'];

// Check permissions
	// Permissions are good
$pass_entry = get_sql_results("SELECT * FROM data WHERE id=$id");

$name = $pass_entry[0]['name'];
$login = decrypt_string($pass_entry[0]['login']);
$password = $pass_entry[0]['password'];
$note = $pass_entry[0]['note'];
$group = $pass_entry[0]['group_id'];
$i_am_owner = ($owner == get_my_uid());

$placeholder = "(click to show for 10 seconds)";

?>

<!-- Fancy javascript stuff -->
<script>
$(document).ready(function() {
	$('#thepassword').click(function() {
		$('#thepassword').load('get_password.php?id=<?php echo $id;?>');
		
		window.setTimeout(function () {
			$('#thepassword').html('<?php echo $placeholder;?>');
			}, 10000);
		});
	});
</script>

<h2><?php echo $name; ?></h2>

<table class="table table-hover">
	<tr>
		<td>Name:</td>
		<td><?php echo $name; ?></td>
	</tr>
	<tr>
		<td>Group:</td>
		<td><?php echo get_group_path($group); ?></td>
	</tr>
	<tr>
		<td>Login:</td>
		<td><?php echo $login; ?></td>
	</tr>
	<tr>
		<td>Password:</td>
		<td>
			<code id="thepassword"><?php echo $placeholder;?></code>
		</td>
	</tr>
	<tr>
		<td>Note:</td>
		<td><?php echo string2html($note); ?></td>
	</tr>
</table>

<p>
	<a href="edit.php?id=<?php echo $id; ?>"><button class="btn btn-sm btn-primary">Edit this object</button></a>
	<a href="delete.php?id=<?php echo $id; ?>"><button class="btn btn-sm btn-danger">Delete this object</button></a>
</p>


<?php require_once('footer.php'); ?>
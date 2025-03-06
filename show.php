<?php require_once('header.php'); ?>

<?php
$id = $_GET['id'];

// Check permissions
if (!(check_object_permissions($id, get_my_uid()))) {
	// We don't have permissions!	?>
	<div class="bg-danger center-block">ACCESS DENIED</div>
<?php	}
else {
	// Permissions are good
	$pass_entry = get_sql_results("SELECT * FROM data WHERE id=$id");

	$name = $pass_entry[0]['name'];
	$login = decrypt_string($pass_entry[0]['login']);
	$password = $pass_entry[0]['password'];
	$note = $pass_entry[0]['note'];
	$group = $pass_entry[0]['group_id'];

	$placeholder = "(click to show for 10 seconds)";
	$myRights = user_rights();	
	
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
		
		
	function CopyPasswordToClibrd() {
	  // Get the text field
	  var copyText = document.getElementById("thepassword");

	   // Copy the text inside the text field
	  navigator.clipboard.writeText(copyText.textContent);

	  // Alert the copied text
	  alert("Copied password to the clipboard");
	}	

	function CopyLoginToClibrd() {
	  // Get the text field
	  var copyText = document.getElementById("thelogin");

	   // Copy the text inside the text field
	  navigator.clipboard.writeText(copyText.textContent);

	  // Alert the copied text
	  alert("Copied login to the clipboard");
	}	
	</script>

	<h2><?php echo $name; ?></h2>

	<table class="table table-hover">
		<tr>
			<td class="col-xs-2">Name:</td>
			<td class="col-xs-6"><?php echo $name; ?></td>
			<td class="col-xs-2"></td>
		</tr>
		<tr>
			<td>Group:</td>
			<td><?php echo get_group_path($group); ?></td>
			<td></td>
		</tr>
		<tr>
			<td>Login:</td>
			<td>
				<code id="thelogin"><?php echo $login; ?></code>
			</td>
			<td>
				<button onclick="CopyLoginToClibrd()" class="btn btn-sm btn-primary">Copy to clipboard</button>
			</td>
			
		</tr>
		<tr>
			<td>Password:</td>
			<td>
				<code id="thepassword"><?php echo $placeholder;?></code>
			</td>
			<td>
				<button onclick="CopyPasswordToClibrd()" class="btn btn-sm btn-primary">Copy to clipboard</button>
			</td>
		</tr>
		<tr>
			<td>Note:</td>
			<td><?php echo string2html($note); ?></td>
			<td></td>
		</tr>
	</table>

	<p>
		<?php if (($myRights & accPasswordEdit) != 0) { ?>
			<a href="edit.php?id=<?php echo $id; ?>"><button class="btn btn-sm btn-primary">Edit this object</button></a>
		<?php } ?>
		<?php if (($myRights & accPasswordRemove) != 0) { ?>
		<a href="delete.php?id=<?php echo $id; ?>"><button class="btn btn-sm btn-danger">Delete this object</button></a>
		<?php } ?>
	</p>

<?php } ?>

<?php require_once('footer.php'); ?>
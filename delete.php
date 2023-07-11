<?php require_once('header.php'); ?>

<?php
$id = $_GET['id'];
?>

<div class="row">
	<!-- lefthand padding -->
	<div class="col-sm-3">&nbsp;</div>
	<div class="col-sm-6">
		<div class="panel panel-primary">
			<div class="panel-heading"><strong>Confirm Deletion</strong></div>
			<div class="panel-body">

				<form action="do_delete_password.php" method="POST">
					<input type="hidden" name="id" value="<?php echo $id;?>">
					
					<div>Please confirm you would like to remove the password object:</div>
					<ul>
						<li><?php echo get_object_name($id); ?>
					</ul>
					<p class="bg-warning"><small><em>This will remove all user and group permissions associated with the object as well.</em></small></p>
					<div><input type="submit" class="btn btn-sm btn-primary" value="Confirm"></div>
				</form>
			</div>
		</div>
	</div>
</div>


<?php require_once('footer.php'); ?>
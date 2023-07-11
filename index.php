<?php require_once('header.php');

	$mode = "all";
	$title = "All Passwords Available To Me";
?>

				<table class="table table-hover">
				<table class="table table-hover">
					<thead>
						<tr>
							<th class="col-xs-2">Name</th>
							<th class="col-xs-3">User name</th>
							<th class="col-xs-5">Notes</th>
							<th class="col-xs-2">Actions</th>
						</tr>
					</thead>
					<tbody>
 <?php

function show_password($gid, $level) {
	$results = get_my_passwords($gid);
	foreach ($results as $entry) {
		$id =  $entry['id'];
		$name = $entry['name'];
		$login = decrypt_string($entry['login']);
		$notes = $entry['note'];
		?>
							<tr onclick="window.location='show.php?id=<?php echo $id;?>'">
								<td><?php echo str_repeat('&nbsp;', $level *4 +4).$name; ?></td>
								<td><?php echo $login; ?></td>
								<td><?php echo $notes; ?></td>
								<td>
									<a class="btn btn-primary btn-xs" href="show.php?id=<?php echo $id;?>">Show</a>
									<a class="btn btn-primary btn-xs" href="edit.php?id=<?php echo $id;?>">Edit</a>
									<a class="btn btn-danger btn-xs" href="delete.php?id=<?php echo $id;?>">Delete</a>
								</td>
							</tr>
		<?php
	}
}

function show_group($gid, $level = 0) {
	$groups = get_group_list($gid);
	foreach ($groups as $group) {
		$id = $group['gid'];
		$path = get_group_path($group['gid']);
		$description = get_group_description($group['gid']);
		
		echo "<tr style=\"background-color:#d6eaf8; padding:0\"><td style=\"padding-top: 0;padding-bottom: 0\"><h5 style \" margin-top:0\"><b>".str_repeat('&nbsp;', $level *4).$path."</b></h5></td><td></td><td>".$description."</td><td></td>";
		
		show_password($id, $level);
		
		show_group($id, $level +1);
	}
}

show_group(0);
?>
					</tbody>
				</table>
			<!-- END all passwords owned by me -->
			<?php  ?>


				<br>

<?php require_once('footer.php'); ?>
<?php
    $exportvisible = true; 
    require_once('header.php');

	$mode = "all";
	$title = "All Passwords Available To Me";
?>

				<table class="table table-hover">
				<table class="table table-hover">
					<thead>
						<tr>
							<th class="col-xs-2"><?php echo _("Name"); ?></th>
							<th class="col-xs-2"><?php echo _("User name"); ?></th>
							<th class="col-xs-3"><?php echo _("Notes"); ?></th>
							<th class="col-xs-3"><?php echo _("Actions"); ?></th>
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
		$myRights = user_rights();
		
		?>
							<tr onclick="window.location='show.php?id=<?php echo $id;?>'">
								<td><?php echo str_repeat('&nbsp;', $level *4 +4).$name; ?></td>
								<td><?php echo $login; ?></td>
								<td><?php echo $notes; ?></td>
								<td>
									<a class="btn btn-primary btn-xs" href="show.php?id=<?php echo $id;?>"><?php echo _("Show"); ?></a>
									<?php if (($myRights & accPasswordEdit) != 0) { ?>
										<a class="btn btn-primary btn-xs" href="edit.php?id=<?php echo $id;?>"><?php echo _("Edit"); ?></a>
									<?php } ?>
									<?php if (($myRights & accPasswordRemove) != 0) { ?>
										<a class="btn btn-danger btn-xs" href="delete.php?id=<?php echo $id;?>"><?php echo _("Delete"); ?></a>
									<?php } ?>
								</td>
							</tr>
		<?php
	}
}

function show_group($gid, $level = 0) {
	$groups = get_group_list($gid);
	foreach ($groups as $group) {
		if (check_group_permissions($group['gid'], get_my_uid()))  {
			$id = $group['gid'];
			$path = get_group_path($group['gid']);
			$description = get_group_description($group['gid']);
			
			echo "<tr style=\"background-color:#d6eaf8; padding:0\"><td style=\"padding-top: 0;padding-bottom: 0\"><h5 style \" margin-top:0\"><b>".str_repeat('&nbsp;', $level *4).$path."</b></h5></td><td></td><td>".$description."</td><td></td>";
			
			show_password($id, $level);
			
			show_group($id, $level +1);
		}
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
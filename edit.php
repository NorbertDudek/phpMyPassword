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
	$pass_entry = get_password_object($id);

	$name = $pass_entry['name'];
	$login = decrypt_string($pass_entry['login']);
	$password = decrypt_string($pass_entry['password']);
	$note = $pass_entry['note'];
	$group = $pass_entry['group_id'];
?>

<div class="row">
	<!-- lefthand padding -->
	<div class="col-sm-3">&nbsp;</div>
	<div class="col-sm-6">
		<div class="panel panel-primary">
			<div class="panel-heading"><strong><?php echo _("Edit object"); ?>:</strong> <?php echo $name;?></div>
			<div class="panel-body">

				<form action="do_edit.php" class="form-horizontal" method="POST">
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<div class="form-group">
						<label for="name" class="col-sm-3 control-label"><?php echo _("Name"); ?>:</label>
						<div class="col-sm-9"><input type="text" class="form-control" name="name" maxlength="256" value="<?php echo $name;?>"></div>
					</div>
					<div class="form-group">
						<label for="group" class="col-sm-3 control-label"><?php echo _("Group"); ?>:</label>
						<div class="col-sm-9"><select class="form-control" name="group">
						<?php
							echo get_group_options(0, $group);
						?>
						</select></div>
					</div>
					<div class="form-group">
						<label for="login" class="col-sm-3 control-label"><?php echo _("User name"); ?>:</label>
						<div class="col-sm-9"><input type="text" class="form-control" name="login" maxlength="256" value="<?php echo $login; ?>"></div>
					</div>
					<div class="form-group">
						<label for="password" class="col-sm-3 control-label"><?php echo _("Password"); ?>:</label>
						<div class="col-sm-9" style="vertical-align: middle;">
                            <span class="col-sm-9" style="padding-left: 0px;">
                                <input id="password" type="password" class="form-control" name="password" maxlength="256" value="<?php echo $password; ?>">
                            </span>
                            <a href="#" class="col-sm-1 btn btn-sm" role="button" id="showpassword"><span class="glyphicon glyphicon-eye-open"></span></a>
                            <a href="#" class="col-sm-1 btn btn-sm" role="button" id="genpassword"><span class="glyphicon glyphicon-lock"></span></span></a>
                            <a href="#" class="col-sm-1 btn btn-sm" role="button" id="copytocclip"><span class="glyphicon glyphicon-link"></span></span></a>
                        </div>
					</div>
					<div class="form-group">
						<label for="note" class="col-sm-3 control-label"><?php echo _("Notes"); ?>:</label>
						<div class="col-sm-9"><textarea class="form-control" name="note" rows="4"><?php echo $note; ?></textarea></div>
					</div>
					<div class="form-group">
						<div class="col-sm-3"></div>
						<div class="col-sm-9"><input type="submit" class="btn btn-sm btn-primary" value="<?php echo _("Save changes"); ?>"></div>
					</div>
					<div class="col-sm-12">&nbsp;</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Modal generatora hasla -->
<div class="modal fade" id="genPasswordModal" tabindex="-1" role="dialog" aria-labelledby="genPasswordModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="genPasswordModalLabel"><?php echo _("Generate password"); ?></h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="genLength"><?php echo _("Password length"); ?></label>
          <input type="number" class="form-control" id="genLength" min="1" max="256" value="12">
        </div>
        <div class="form-group">
          <label for="genDigits"><?php echo _("Number of digits"); ?></label>
          <input type="number" class="form-control" id="genDigits" min="0" value="1">
        </div>
        <div class="form-group">
          <label for="genSpecial"><?php echo _("Number of special characters"); ?></label>
          <input type="number" class="form-control" id="genSpecial" min="0" value="1">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _("Cancel"); ?></button>
        <button type="button" class="btn btn-primary" id="genPasswordSubmit"><?php echo _("Generate"); ?></button>
      </div>
    </div>
  </div>
</div>

<?php } ?>

<script>
document.getElementById('showpassword').addEventListener('click', function() {
  var pole = document.getElementById('password');
  if (pole.type === "password") {
    pole.type = "text";
  } else {
    pole.type = "password";
  }
});

function generatePasswordFromSyllables(length, digits, special) {
  return fetch('/genpassword.php?length=' + encodeURIComponent(length) + '&digits=' + encodeURIComponent(digits) + '&special=' + encodeURIComponent(special))
    .then(response => {
      if (!response.ok) throw new Error('Blad sieci');
      return response.text();
    })
    .then(data => {
      const element = document.getElementById('password');
      element.value = data;
      if (element.type === "password") {
        element.type = "text";
      }
    })
    .catch(error => {
      console.error('Blad pobierania:', error);
    });
}

document.getElementById('genpassword').addEventListener('click', function(e) {
  e.preventDefault();
  $('#genPasswordModal').modal('show');
});

document.getElementById('genPasswordSubmit').addEventListener('click', function() {
  var length = parseInt(document.getElementById('genLength').value, 10) || 12;
  var digits = parseInt(document.getElementById('genDigits').value, 10) || 0;
  var special = parseInt(document.getElementById('genSpecial').value, 10) || 0;
  generatePasswordFromSyllables(length, digits, special);
  $('#genPasswordModal').modal('hide');
});

document.getElementById('copytocclip').addEventListener('click', function() {
  var pole = document.getElementById('password');
  navigator.clipboard.writeText(pole.value);
});

</script>

<?php require_once('footer.php'); ?>


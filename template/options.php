<div class="wrap">
	<?php screen_icon('plugins'); ?>
	<h2>AdHerder configuration</h2>
	<form action="options.php" method="post">
		<?php
		settings_fields('adherder_options');
		do_settings_sections('edit.php?post_type=adherder_ad');
		?>
		<input name="Submit" type="submit" value="Save Changes" class="button-primary" />
	</form>
</div>

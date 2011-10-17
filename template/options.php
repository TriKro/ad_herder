<div class="wrap">
	<?php screen_icon('plugins'); ?>
	<h2>AdHerder configuration</h2>
	<form action="options.php" method="post">
		<?php
		settings_fields('adherder_options');
		do_settings_sections('adherder_options');
		?>
		<input name="Submit" type="submit" value="Save Changes" class="button-primary" />
	</form>
</div>

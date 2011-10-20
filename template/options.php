<?php
/*
Copyright 2011 Tristan Kromer, Peter Backx (email : tristan@grasshopperherder.com)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/
?>
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

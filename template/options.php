<h2>AdHerder configuration</h2>
<div class="wrap">
  <?php if($message) {
    echo '<p>' . $message . '</p>';
  } ?>
  <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
    <h3>Ad selection</h3>
    <p>The different weights (numeric and &gt;0) with which to select the calls. A higher value means they are more likely to be displayed. It is not suggested to put any of them at 0, but it is possible (they won't be displayed)</p>
    <table>
      <tr>
        <td><label for="ctopt_normalWeight">Normal/new Ad</label></td>
        <td><input name="ctopt_normalWeight" id="ctopt_normalWeight" type="text" value="<?php echo $options['normalWeight']; ?>" /></td>
      </tr>
      <tr>
        <td><label for="ctopt_convertedWeight">Ad for which a user has already converted</label></td>
        <td><input name="ctopt_convertedWeight" id="ctopt_convertedWeight" type="text" value="<?php echo $options['convertedWeight']; ?>" /></td>
      </tr>
      <tr>
        <td><label for="ctopt_seenWeight">Ad that has been seen (see below)</label></td>
        <td><input name="ctopt_seenWeight" id="ctopt_seenWeight" type="text" value="<?php echo $options['seenWeight']; ?>" /></td>
      </tr>
    </table>
    <h3>Display limit</h3>
    <p><label for="ctopt_seenLimit">Number of times an ad is displayed before it is considered "seen"</label></p>
    <p><input type="text" name="ctopt_seenLimit" id="ctopt_seenLimit" value="<?php echo $options['seenLimit']; ?>" /></p>

    <h3>Track logged in users?</h3>
    <p>Selecting "No" will not store tracking data or impressions/click counts for users that are logged in.</p>
	<p><label for="ctopt_trackLoggedIn_yes"><input type="radio" id="ctopt_trackLoggedIn_yes" name="ctopt_trackLoggedIn" value="true" <?php checked($options['trackLoggedIn'], "true"); ?> />Yes</label>
	   <label for="ctopt_trackLoggedIn_no"><input type="radio" id="ctopt_trackLoggedIn_no" name="ctopt_trackLoggedIn" value="false" <?php checked($options['trackLoggedIn'], "false"); ?> />No</label></p>

	<h3>Use Ajax to display widget?</h3>
	<p>This will load the widget's content via an Ajax call. If you are using any kind of caching plugin and want correct results, 
	you need to turn this on. But keep in mind that you might need to rewrite some ads that use JavaScript.</p>
	<p><label for="ctopt_ajaxWidget_yes"><input type="radio" id="ctopt_ajaxWidget_yes" name="ctopt_ajaxWidget" value="true" <?php checked($options['ajaxWidget'], "true"); ?> />Yes</label>
	   <label for="ctopt_ajaxWidget_no"><input type="radio" id="ctopt_ajaxWidget_no" name="ctopt_ajaxWidget" value="false" <?php checked($options['ajaxWidget'], "false"); ?> />No</label></p>

    <div class="submit">
      <input type="submit" name="ctopt_updateOptions" value="Update Settings" />
    </div>
  </form>
</div>

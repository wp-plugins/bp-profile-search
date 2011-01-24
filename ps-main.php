<?php
/*
Plugin Name: BP Profile Search
Plugin URI: http://www.blogsweek.com/bp-profile-search/
Description: Search BuddyPress extended profiles.
Version: 2.3
Author: Andrea Tarantini
Author URI: http://www.blogsweek.com/
*/

include 'ps-functions.php';

register_activation_hook (__FILE__, 'ps_activate');
function ps_activate ()
{
	ps_set_default_options ();
	return true;
}

add_action ('init', 'ps_init');
function ps_init ()
{
	global $ps_options;

	$ps_options = get_option ('ps_options');
	if ($ps_options == false)  ps_set_default_options ();
	return true;
}

add_action ('admin_init', 'ps_register_setting');
function ps_register_setting ()
{
	register_setting ('ps_options', 'ps_options');
	return true;
}

add_action ('admin_menu', 'ps_add_pages', 20);
function ps_add_pages ()
{
	add_submenu_page ('bp-general-settings', 'Profile Search Setup', 'Profile Search', 'manage_options', 'ps-settings', 'ps_admin');
	return true;
}

function ps_set_default_options ()
{
	global $ps_options;

	$ps_options['header'] = '<h4>Profile Search</h4><p>You can find site members searching their public profiles. Search by any or all of the fields below:</p>';
	$ps_options['show'] = array ('Enabled');
	$ps_options['message'] = 'Show Search Form';
	$ps_options['fields'] = array ();
	$ps_options['agerange'] = 0;
	$ps_options['agelabel'] = 'Age Range';
	$ps_options['agedesc'] = 'enter the minimum and maximum age for your search';
	$ps_options['searchmode'] = 'Partial Match';

	update_option ('ps_options', $ps_options);
	return true;
}

function ps_admin ()
{
	global $ps_options;
	global $updated;

	ps_get_vars (array ('updated'));
	if ($updated == 'true')
		$message = "Settings saved.";
?>

<div class="wrap">
  
  <h2><?php echo 'Profile Search Setup'; ?></h2>
  
<?php if ($message) : ?>
  <div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
<?php endif; ?>

  <form method="post" action="options.php">
	<?php settings_fields ('ps_options'); ?>
	
	<h3>Form Header and Fields</h3>

	<p>Customize your profile search form here. Select the header text and the profile fields to be included in the search form.</p>	

	<table class="form-table">
	<tr valign="top"><th scope="row">Search Form Header:</th><td>
		<textarea name="ps_options[header]" class="large-text code" rows="4"><?php echo $ps_options['header']; ?></textarea>
	</td></tr>
	<tr valign="top"><th scope="row">Show/Hide Form:</th><td>
		<label><input type="checkbox" name="ps_options[show][]" value="Enabled"<?php if (in_array ('Enabled', (array)$ps_options['show'])) echo ' checked="checked"'; ?> /> Enabled</label><br />
	</td></tr>
	<tr valign="top"><th scope="row">Show Form Message:</th><td>
		<input type="text" name="ps_options[message]" value="<?php echo $ps_options['message']; ?>"  />
	</td></tr>
	<tr valign="top"><th scope="row">Selected Profile Fields:</th><td>
		<?php ps_fields ('ps_options[fields]', $ps_options['fields']); ?>
	</td></tr>
	</table>
	
	<h3>Age Range Search</h3>

	<p>If your extended profiles include a birth date field, your search form can include the Age Range Search option. To enable this option, select the birth date field below.</p>	

	<table class="form-table">
	<tr valign="top"><th scope="row">Birth Date Field:</th><td>
		<?php ps_agerange ('ps_options[agerange]', $ps_options['agerange']); ?>
	</td></tr>
	<tr valign="top"><th scope="row">Search Field Label:</th><td>
		<input type="text" name="ps_options[agelabel]" value="<?php echo $ps_options['agelabel']; ?>"  />
	</td></tr>
	<tr valign="top"><th scope="row">Search Field Description:</th><td>
		<input type="text" name="ps_options[agedesc]" value="<?php echo $ps_options['agedesc']; ?>" class="large-text" />
	</td></tr>
	</table>
	
	<h3>Text Search Mode</h3>

	<p>Select your text search mode here. Choose between partial match (a search for <i>John</i> matches <i>John</i>, <i>Johnson</i>, <i>Long John Silver</i>, and so on) and exact match (a search for <i>John</i> matches <i>John</i> only). In both modes the wildcard characters <i>% (percent sign)</i>, matching zero or more characters, and <i>_ (underscore)</i>, matching exactly one character, may be used.</p>	

	<table class="form-table">
	<tr valign="top"><th scope="row">Text Search Mode:</th><td>
		<label><input type="radio" name="ps_options[searchmode]" value="Partial Match"<?php if ('Partial Match' == $ps_options['searchmode']) echo ' checked="checked"'; ?> /> Partial Match</label><br />
		<label><input type="radio" name="ps_options[searchmode]" value="Exact Match"<?php if ('Exact Match' == $ps_options['searchmode']) echo ' checked="checked"'; ?> /> Exact Match</label><br />
	</td></tr>
	</table>

	<p class="submit">
	  <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
  </form>
</div>

<?php
}

function ps_get_vars ($vars)
{
	foreach ($vars as $var)
	{
		global $$var;

		if (empty ($_POST["$var"]))
			$$var = empty ($_GET["$var"])? '': $_GET["$var"];
		else
			$$var = $_POST["$var"];
		
		$$var = stripslashes_deep ($$var);
	}
}
?>

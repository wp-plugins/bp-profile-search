<?php

function ps_add_pages ()
{
	add_submenu_page ('bp-general-settings', __('Profile Search Setup', 'buddypress'), 
		__('Profile Search', 'buddypress'), 'manage_options', 'search-settings', 'ps_admin_page');
	
	return true;
}

function ps_set_default_options ()
{
	global $ps_define;
	global $ps_options;

	$ps_options['header'] = '
<h4>Profile Search</h4>
<p>You can find site members searching their public profiles. Search by any or all of the fields below:</p>';
	$ps_options['show'] = array ('on');
	$ps_options['message'] = 'Show Search Form';

	$ps_options['fields'] = array ();
	$ps_options['agerange'] = 0;

	update_option ($ps_define->option_name, $ps_options);
	return true;
}

function ps_admin_page ()
{
	global $ps_define;
	global $updated;
	
	global $field;

	ps_get_vars (array ('updated'));
	if ($updated == 'true')
		$message = "Settings saved.";
?>

<div class="wrap">
  <h2>Profile Search Setup</h2>
  
<?php if ($message) : ?>
  <div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
<?php endif; ?>

  <form method="post" action="options.php">
	<?php settings_fields ($ps_define->option_group); ?>
	<?php $ps_options = get_option ($ps_define->option_name); ?>

	<p>Customize your profile search form here. Select the header text and
	the profile fields to be included in the search form.</p>

	<table class="form-table">

	<tr valign="top"><th scope="row">Search Form Header:</th>
	  <td><textarea name="ps-options[header]" rows="4" cols="50" class="large-text code"><?php echo $ps_options['header']; ?></textarea></td>
	</tr>
	<tr valign="top"><th scope="row">Show/Hide Form:</th>
	  <td><label><input type="checkbox" name="ps-options[show][]" value="on"<?php if (in_array ('on', (array)$ps_options['show'])) echo ' checked="checked"'; ?> /> Enabled</label></td>
	</tr>
	<tr valign="top"><th scope="row">Show Form Message:</th>
	  <td><input type="text" name="ps-options[message]" value="<?php echo $ps_options['message']; ?>"  /></td>
	</tr>

	<tr valign="top"><th scope="row">Selected Profile Fields:</th>
	  <td>

<?php if (bp_is_active ('xprofile')) : 
	if (function_exists ('bp_has_profile')) : 
		if (bp_has_profile ()) :
			$dateboxes = array ();
			$dateboxes[0] = '';

			while (bp_profile_groups ()) : 
				bp_the_profile_group (); 

				echo '<strong>'. bp_get_the_profile_group_name (). ':</strong><br />';

				while (bp_profile_fields ()) : 
					bp_the_profile_field(); 
					switch (bp_get_the_profile_field_type ())
					{
					case 'datebox':	
						$disabled = 'disabled="disabled"';
						$dateboxes[bp_get_the_profile_field_id ()] = bp_get_the_profile_field_name ();
						break;
					default:
						$disabled = '';
						break;
					}
?>
<label><input type="checkbox" name="ps-options[fields][]" value="<?php echo $field->id; ?>" <?php echo $disabled; ?>
<?php echo in_array ($field->id, (array)$ps_options['fields'])? ' checked="checked"': ''; ?> />
<?php bp_the_profile_field_name(); ?>
<?php if (bp_get_the_profile_field_is_required ()) 
	_e (' (required) ', 'buddypress');
else
	_e (' (optional) ', 'buddypress'); ?>
<?php bp_the_profile_field_description (); ?></label><br />

				<?php endwhile;
			endwhile; 
		endif;
	endif; 
endif; ?>

	  </td>
	</tr>

<?php if (defined ('ENABLE_AGE_RANGE') && count ($dateboxes) > 1)
	ps_select ('Age Range Field:', 'ps-options[agerange]', $dateboxes, $ps_options['agerange']);
?>

	</table>

	<p class="submit">
	  <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>

  </form>
</div>

<?php
}

function ps_select ($label, $name, $list, $selected, $attr='', $comment='')
{
	echo "<tr valign=\"top\"><th scope=\"row\">$label</th>\n";
	echo "<td><select name=\"$name\" $attr>\n";
	foreach ($list as $value => $caption)
		{
			echo "<option value=\"$value\"";
			if ($value == $selected) echo " selected=\"selected\"";
			echo "> $caption &nbsp; </option>\n";
		}
	echo "</select>\n";
		
	echo "$comment\n";
	echo "</td></tr>\n";

	return true;
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
//		unset ($_POST["$var"]);
	}
}
?>

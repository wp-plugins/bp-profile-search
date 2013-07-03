<?php

add_action ('bp_profile_search_form', 'bps_form');
function bps_form ($form_id)
{
	global $field;
	global $bps_options;
	global $bps_version;

	$action = bp_get_root_domain (). '/'. bp_get_members_root_slug (). '/';

echo "\n<!-- BP Profile Search $bps_version -->\n";
	if ($form_id == '')
	{
	$form_id = 'bps_action';
?>	
	<div class="item-list-tabs">
	<ul>
	<li><?php echo $bps_options['header']; ?></li>
<?php if (in_array ('Enabled', (array)$bps_options['show'])) { ?>
	<li class="last">
	<input id="bps_show" type="submit" value="<?php echo $bps_options['message']; ?>" />
	</li>
<?php } ?>
	</ul>
<?php if (in_array ('Enabled', (array)$bps_options['show'])) { ?>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('#bps_action').hide();
		$('#bps_show').click(function(){
			$('#bps_action').toggle();
		});
	});
</script>
<?php } ?>
</div>
<?php
	}

echo "<form action='$action' method='post' id='$form_id' class='standard-form'>";

	if (bp_has_profile ('hide_empty_fields=0'))  while (bp_profile_groups ())
	{
		bp_the_profile_group ();
		while (bp_profile_fields ())
		{
			bp_the_profile_field ();
			$fname = 'field_'. $field->id;
			$posted = $_POST[$fname];
			$posted_to = $_POST[$fname. '_to'];

			if ($field->id == $bps_options['agerange'])
			{
				$from = ($posted == '' && $posted_to == '')? '': (int)$posted;
				$to = ($posted_to == '')? $from: (int)$posted_to;
				if ($to < $from)  $to = $from;

echo '<div '. bp_get_field_css_class ('editfield'). '>';
echo "<label for='$fname'>{$bps_options['agelabel']}</label>";
echo "<input style='width: 10%;' type='text' name='$fname' value='$from' />";
echo '&nbsp;-&nbsp;';
echo "<input style='width: 10%;' type='text' name='{$fname}_to' value='$to' />";
echo "<p class='description'>{$bps_options['agedesc']}</p>";
echo '</div>';
				continue;
			}

			if ($field->id == $bps_options['numrange'])
			{
				$from = ($posted == '' && $posted_to == '')? '': (float)$posted;
				$to = ($posted_to == '')? $from: (float)$posted_to;
				if ($to < $from)  $to = $from;

echo '<div '. bp_get_field_css_class ('editfield'). '>';
echo "<label for='$fname'>{$bps_options['numlabel']}</label>";
echo "<input style='width: 10%;' type='text' name='$fname' value='$from' />";
echo '&nbsp;-&nbsp;';
echo "<input style='width: 10%;' type='text' name='{$fname}_to' value='$to' />";
echo "<p class='description'>{$bps_options['numdesc']}</p>";
echo '</div>';
				continue;
			}

			if (!in_array ($field->id, (array)$bps_options['fields']))  continue;

echo '<div '. bp_get_field_css_class ('editfield'). '>';

			if (!method_exists ($field, 'get_children'))
				$field = new BP_XProfile_Field ($field->id);
			$options = $field->get_children ();

			switch (bp_get_the_profile_field_type ())
			{
			case 'textbox':
			$value = esc_attr (stripslashes ($_POST[$fname]));
echo "<label for='$fname'>$field->name</label>";
echo "<input type='text' name='$fname' id='$fname' value='$value' />";
			break;

			case 'textarea':
			$value = esc_attr (stripslashes ($_POST[$fname]));
echo "<label for='$fname'>$field->name</label>";
echo "<textarea rows='5' cols='40' name='$fname' id='$fname'>$value</textarea>";
			break;

			case 'selectbox':
echo "<label for='$fname'>$field->name</label>";
echo "<select name='$fname' id='$fname'>";
echo "<option value=''></option>";

			foreach ($options as $option)
			{
				$option->name = trim ($option->name);
				$value = esc_attr (stripslashes ($option->name));
				$selected = ($option->name == $posted)? "selected='selected'": "";
echo "<option $selected value='$value'>$value</option>";
			}
echo "</select>";
			break;

			case 'multiselectbox':
echo "<label for='$fname'>$field->name</label>";
echo "<select name='{$fname}[]' id='$fname' multiple='multiple'>";

			foreach ($options as $option)
			{
				$option->name = trim ($option->name);
				$value = esc_attr (stripslashes ($option->name));
				$selected = (in_array ($option->name, (array)$posted))? "selected='selected'": "";
echo "<option $selected value='$value'>$value</option>";
			}
echo "</select>";
			break;

			case 'radio':
echo "<div class='radio'>";
echo "<span class='label'>$field->name</span>";
echo "<div id='$fname'>";

			foreach ($options as $option)
			{
				$option->name = trim ($option->name);
				$value = esc_attr (stripslashes ($option->name));
				$selected = ($option->name == $posted)? "checked='checked'": "";
echo "<label><input $selected type='radio' name='$fname' value='$value'>$value</label>";
			}
echo '</div>';
echo "<a class='clear-value' href='javascript:clear(\"$fname\");'>". __('Clear', 'buddypress'). "</a>";
echo '</div>';
			break;

			case 'checkbox':
echo "<div class='checkbox'>";
echo "<span class='label'>$field->name</span>";

			foreach ($options as $option)
			{
				$option->name = trim ($option->name);
				$value = esc_attr (stripslashes ($option->name));
				$selected = (in_array ($option->name, (array)$posted))? "checked='checked'": "";
echo "<label><input $selected type='checkbox' name='{$fname}[]' value='$value'>$value</label>";
			}
echo '</div>';
			break;
			}

echo '</div>';
		}
	}

	if (empty ($bps_options['agerange']) && count ((array)$bps_options['fields']) == 0)
	{
		$url = is_multisite ()? network_admin_url ('users.php'): admin_url ('users.php');
		$settings = add_query_arg (array ('page' => 'bp-profile-search'), $url);
echo "<p>Please <a href='$settings'>select your form fields</a>.</p>";
	}

echo "<div class='submit'>";
echo "<input type='submit' value='". __('Search', 'buddypress'). "' />";
echo '</div>';
echo "<input type='hidden' name='bp_profile_search' value='true' />";
echo '</form>';
echo "\n<!-- BP Profile Search $bps_version - end -->\n";
}

function bps_your_search ()
{
	global $field;
	global $bps_options;

	if (isset ($_POST['bp_profile_search']))
		$posted = $_POST;
	else if (isset ($_COOKIE['bp-profile-search']))
		$posted = unserialize (stripslashes ($_COOKIE['bp-profile-search']));

echo '<p>';
	if (bp_has_profile ('hide_empty_fields=0'))  while (bp_profile_groups ())
	{
		bp_the_profile_group ();
		while (bp_profile_fields ())
		{ 
			bp_the_profile_field ();
			$fname = 'field_'. $field->id;

			$value = $posted[$fname];
			$value_to = $posted[$fname. '_to'];
			if ($value == '' && $value_to == '')  continue;

			switch (bp_get_the_profile_field_type ())
			{
			case 'textbox':
				if ($field->id == $bps_options['numrange'])
				{
					$from = (float)$value;
					$to = ($value_to == '')? $from: (float)$value_to;
					if ($to < $from)  $to = $from;

echo "<strong>{$bps_options['numlabel']}:</strong> $from <strong>-</strong> $to<br/>";
					break;
				}
			case 'textarea':
				$value = esc_attr (stripslashes ($posted[$fname]));
echo "<strong>$field->name:</strong> $value<br/>";
				break;

			case 'selectbox':
				if ($field->id == $bps_options['numrange'])
				{
					$from = (float)$value;
					$to = ($value_to == '')? $from: (float)$value_to;
					if ($to < $from)  $to = $from;

echo "<strong>{$bps_options['numlabel']}:</strong> $from <strong>-</strong> $to<br/>";
					break;
				}
			case 'radio':
				$value = stripslashes ($posted[$fname]);
echo "<strong>$field->name:</strong> $value<br/>";
				break;

			case 'multiselectbox':
			case 'checkbox':
				$values = stripslashes_deep ($posted[$fname]);
echo "<strong>$field->name:</strong> ". implode ('<strong>,</strong> ', $values). "<br/>";
				break;

			case 'datebox':
				if ($field->id != $bps_options['agerange'])  continue;

				$from = (int)$value;
				$to = ($value_to == '')? $from: (int)$value_to;
				if ($to < $from)  $to = $from;

echo "<strong>{$bps_options['agelabel']}:</strong> $from <strong>-</strong> $to<br/>";
				break;
			}
		}
	}

echo '</p>';
}
?>

<?php

add_action ('bp_before_directory_members_tabs', 'bps_add_form');
function bps_add_form ()
{
	global $bps_options;

	if ($bps_options['directory'] == 'Yes')  bps_display_form (0, 'bps_auto');
}

add_action ('bp_profile_search_form', 'bps_display_form');
function bps_display_form ($name, $tag='bps_action')
{
	global $bps_options;

	if (empty ($bps_options['field_name']))
	{
		printf ('<p class="bps_error">'. __('%s: Error, you have not configured your search form.', 'bps'). '</p>',
			'<strong>BP Profile Search '. BPS_VERSION. '</strong>');
		return false;
	}

	$action = bp_get_root_domain (). '/'. bp_get_members_root_slug (). '/';

echo "\n<!-- BP Profile Search ". BPS_VERSION. " - start -->\n";
if ($tag != 'bps_auto')  echo "<div id='buddypress'>";

	if ($tag == 'bps_auto')
	{
?>
	<div class="item-list-tabs bps_header">
	<ul>
	<li><?php echo $bps_options['header']; ?></li>
<?php if (in_array ('Enabled', $bps_options['show'])) { ?>
	<li class="last">
	<input id="bps_show" type="submit" value="<?php echo $bps_options['message']; ?>" />
	</li>
<?php } ?>
	</ul>
<?php if (in_array ('Enabled', $bps_options['show'])) { ?>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('#<?php echo $tag; ?>').hide();
		$('#bps_show').click(function(){
			$('#<?php echo $tag; ?>').toggle();
		});
	});
</script>
<?php } ?>
	</div>
<?php
	}

	list ($x, $fields) = bps_get_fields ();

echo "<form action='$action' method='$bps_options[method]' id='$tag' class='standard-form'>";

	$j = 0;
	foreach ($bps_options['field_name'] as $k => $id)
	{
		if (empty ($fields[$id]))  continue;

		$field = $fields[$id];
		$field_type = apply_filters ('bps_field_html_type', $field->type, $field);

		$label = $bps_options['field_label'][$k];
		$desc = $bps_options['field_desc'][$k];
		$range = isset ($bps_options['field_range'][$k]);

		$fname = 'field_'. $id;
		$name = sanitize_title ($field->name);
		$alt = ($j++ % 2)? ' alt': '';

echo "<div class='editfield field_$id field_$name$alt'>";

		if (empty ($label))
			$label = $field->name;
		else
echo "<input type='hidden' name='label_$id' value='$label' />";

		if (empty ($desc))
			$desc = $field->description;

		if (bps_custom_field ($field_type))
		{
			$output = "<p>Your HTML code for the <em>$field_type</em> field type goes here</p>";
			$output = apply_filters ('bps_field_html', $output, $field, $label, $range);
echo $output;
		}
		else if ($range)
		{
			list ($min, $max) = bps_minmax ($_REQUEST, $id, $field_type);

echo "<label for='$fname'>$label</label>";
echo "<input style='width: 10%;' type='text' name='{$fname}_min' id='$fname' value='$min' />";
echo '&nbsp;-&nbsp;';
echo "<input style='width: 10%;' type='text' name='{$fname}_max' value='$max' />";
		}
		else switch ($field_type)
		{
		case 'textbox':
			$posted = isset ($_REQUEST[$fname])? $_REQUEST[$fname]: '';
			$value = esc_attr (stripslashes ($posted));
echo "<label for='$fname'>$label</label>";
echo "<input type='text' name='$fname' id='$fname' value='$value' />";
			break;

		case 'number':
			$posted = isset ($_REQUEST[$fname])? $_REQUEST[$fname]: '';
			$value = esc_attr (stripslashes ($posted));
echo "<label for='$fname'>$label</label>";
echo "<input type='number' name='$fname' id='$fname' value='$value' />";
			break;

		case 'textarea':
			$posted = isset ($_REQUEST[$fname])? $_REQUEST[$fname]: '';
			$value = esc_textarea (stripslashes ($posted));
echo "<label for='$fname'>$label</label>";
echo "<textarea rows='5' cols='40' name='$fname' id='$fname'>$value</textarea>";
			break;

		case 'selectbox':
echo "<label for='$fname'>$label</label>";
echo "<select name='$fname' id='$fname'>";
			$selectall = apply_filters ('bps_select_all', '', $field);
			if (is_string ($selectall))
echo "<option value='$selectall'></option>";

			$posted = isset ($_REQUEST[$fname])? $_REQUEST[$fname]: '';
			$options = bps_get_options ($id);
			foreach ($options as $option)
			{
				$option = trim ($option);
				$value = esc_attr (stripslashes ($option));
				$selected = ($option == $posted)? "selected='selected'": "";
echo "<option $selected value='$value'>$value</option>";
			}
echo "</select>";
			break;

		case 'multiselectbox':
echo "<label for='$fname'>$label</label>";
echo "<select name='{$fname}[]' id='$fname' multiple='multiple'>";

			$posted = isset ($_REQUEST[$fname])? $_REQUEST[$fname]: array ();
			$options = bps_get_options ($id);
			foreach ($options as $option)
			{
				$option = trim ($option);
				$value = esc_attr (stripslashes ($option));
				$selected = (in_array ($option, $posted))? "selected='selected'": "";
echo "<option $selected value='$value'>$value</option>";
			}
echo "</select>";
			break;

		case 'radio':
echo "<div class='radio'>";
echo "<span class='label'>$label</span>";
echo "<div id='$fname'>";

			$posted = isset ($_REQUEST[$fname])? $_REQUEST[$fname]: '';
			$options = bps_get_options ($id);
			foreach ($options as $option)
			{
				$option = trim ($option);
				$value = esc_attr (stripslashes ($option));
				$selected = ($option == $posted)? "checked='checked'": "";
echo "<label><input $selected type='radio' name='$fname' value='$value'>$value</label>";
			}
echo '</div>';
echo "<a class='clear-value' href='javascript:clear(\"$fname\");'>". __('Clear', 'buddypress'). "</a>";
echo '</div>';
			break;

		case 'checkbox':
echo "<div class='checkbox'>";
echo "<span class='label'>$label</span>";

			$posted = isset ($_REQUEST[$fname])? $_REQUEST[$fname]: array ();
			$options = bps_get_options ($id);
			foreach ($options as $option)
			{
				$option = trim ($option);
				$value = esc_attr (stripslashes ($option));
				$selected = (in_array ($option, $posted))? "checked='checked'": "";
echo "<label><input $selected type='checkbox' name='{$fname}[]' value='$value'>$value</label>";
			}
echo '</div>';
			break;
		}

	if ($desc != '-')
echo "<p class='description'>$desc</p>";
echo '</div>';
	}

echo "<div class='submit'>";
echo "<input type='submit' value='". __('Search', 'buddypress'). "' />";
echo '</div>';
	if ($bps_options['searchmode'] == 'Partial Match')
echo "<input type='hidden' name='options[]' value='partial_match' />";
echo "<input type='hidden' name='bp_profile_search' value='true' />";
echo '</form>';
if ($tag != 'bps_auto')  echo '</div>';
echo "\n<!-- BP Profile Search ". BPS_VERSION. " - end -->\n";

	return true;
}

function bps_filters ()
{
	$posted = $_REQUEST;
	$done = array ();
	$filters = '';
	$action = bp_get_root_domain (). '/'. bp_get_members_root_slug (). '/';

	list ($x, $fields) = bps_get_fields ();
	foreach ($posted as $key => $value)
	{
		if ($value === '')  continue;

		$split = explode ('_', $key);
		if ($split[0] != 'field')  continue;

		$id = $split[1];
		$op = isset ($split[2])? $split[2]: 'eq';
		if (isset ($done[$id]) || empty ($fields[$id]))  continue;
	
		$field = $fields[$id];
		$field_type = apply_filters ('bps_field_criteria_type', $field->type, $field);
		$field_label = isset ($posted['label_'. $id])? $posted['label_'. $id]: $field->name;

		if (bps_custom_field ($field_type))
		{
			$output = "The search criteria for the <em>$field_type</em> field type go here<br/>\n";
			$output = apply_filters ('bps_field_criteria', $output, $field, $key, $value, $field_label);
			$filters .= $output;
		}
		else if ($op == 'min' || $op == 'max')
		{
			if ($field_type == 'multiselectbox' || $field_type == 'checkbox')  continue;

			list ($min, $max) = bps_minmax ($posted, $id, $field_type);
			if ($min === '' && $max === '')  continue;

			$filters .= "<strong>$field_label:</strong>";
			if ($min !== '')
				$filters .= " <strong>". __('min', 'bps'). "</strong> $min";
			if ($max !== '')
				$filters .= " <strong>". __('max', 'bps'). "</strong> $max";
			$filters .= "<br/>\n";
		}
		else if ($op == 'eq')
		{
			if ($field_type == 'datebox')  continue;

			switch ($field_type)
			{
			case 'textbox':
			case 'number':
			case 'textarea':
			case 'selectbox':
			case 'radio':
				$filters .= "<strong>$field_label:</strong> ". esc_html (stripslashes ($value)). "<br/>\n";
				break;

			case 'multiselectbox':
			case 'checkbox':
				$values = $value;
				$filters .= "<strong>$field_label:</strong> ". esc_html (implode (', ', stripslashes_deep ($values))). "<br/>\n";
				break;
			}
		}
		else continue;

		$done [$id] = true;
	}

	if (count ($done) == 0)  return false;

	echo "\n";
	echo "<p class='bps_filters'>\n". $filters;
	echo "<a href='$action'>". __('Clear', 'buddypress'). "</a><br/>\n";
	echo "</p>\n";

	return true;
}
?>

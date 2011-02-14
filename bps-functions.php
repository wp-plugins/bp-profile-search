<?php
include 'bps-searchform.php';

function bps_fields ($name, $values)
{
	global $field;
	global $dateboxes;

	if (bp_is_active ('xprofile')) : 
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
<label><input type="checkbox" name="<?php echo $name; ?>[]" value="<?php echo $field->id; ?>" <?php echo $disabled; ?>
<?php if (in_array ($field->id, (array)$values))  echo ' checked="checked"'; ?> />
<?php bp_the_profile_field_name(); ?>
<?php if (bp_get_the_profile_field_is_required ()) 
	_e (' (required) ', 'buddypress');
else
	_e (' (optional) ', 'buddypress'); ?>
<?php bp_the_profile_field_description (); ?></label><br />

<?php 			endwhile;
			endwhile; 
		endif;
	endif; 
	endif;

	return true;
}

function bps_agerange ($name, $value)
{
	global $dateboxes;

	if (count ($dateboxes) > 1)
	{
		echo "<select name=\"$name\">\n";
		foreach ($dateboxes as $fid => $fname)
		{
			echo "<option value=\"$fid\"";
			if ($fid == $value)  echo " selected=\"selected\"";
			echo ">$fname &nbsp;</option>\n";
		}
		echo "</select>\n";
	}
	else
		echo 'There is no date field in your profile';

	return true;
}

add_filter ('bp_core_get_users', 'bps_search', 99, 2);
function bps_search ($results, $params)
{
	global $bp;
	global $wpdb;
	global $bps_list;
	global $bps_options;

	if ($_POST['bp_profile_search'] != true)  return $results;

	$bps_list += 1;
	if ($bps_list != $bps_options['filtered'])  return $results;

	$noresults['users'] = array ();
	$noresults['total'] = 0;

	$sql = "SELECT DISTINCT user_id from {$bp->profile->table_name_data}";
	$found = $wpdb->get_results ($sql);
	$userids = bps_conv ($found, 'user_id');
	$emptyform = true;

	if (bp_has_profile ()):
		while (bp_profile_groups ()):
			bp_the_profile_group ();
			while (bp_profile_fields ()): 
				bp_the_profile_field ();

				$id = bp_get_the_profile_field_id ();
				$value = $_POST["field_$id"];
				$to = $_POST["field_{$id}_to"];

				if ($value == '' && $to == '')  continue;

				switch (bp_get_the_profile_field_type ())
				{
				case 'textbox':
				case 'textarea':
					$sql = "SELECT user_id from {$bp->profile->table_name_data}";
					if ($bps_options['searchmode'] == 'Partial Match')
						$sql .= " WHERE field_id = $id AND value LIKE '%%$value%%'";
					else					
						$sql .= " WHERE field_id = $id AND value LIKE '$value'";
					break;

				case 'selectbox':
				case 'radio':
					$sql = "SELECT user_id from {$bp->profile->table_name_data}";
					$sql .= " WHERE field_id = $id AND value = '$value'";
					break;

				case 'multiselectbox':
				case 'checkbox':
					$sql = "SELECT user_id from {$bp->profile->table_name_data}";
					$sql .= " WHERE field_id = $id";
					$like = array ();
					foreach ($value as $curvalue)
						$like[] = "value LIKE '%\"$curvalue\"%'";
					$sql .= ' AND ('. implode (' OR ', $like). ')';	
					break;

				case 'datebox':
					if ($id != $bps_options['agerange'])  continue;
					
					$time = time ();
					$day = date ("j", $time);
					$month = date ("n", $time);
					$year = date ("Y", $time);
					$min = mktime (0, 0, 0, $month, $day+1, $year-$to-1);	
					$max = mktime (0, 0, 0, $month, $day, $year-$value);

					$sql = "SELECT user_id from {$bp->profile->table_name_data}";
					$sql .= " WHERE field_id = $id AND value BETWEEN $min AND $max";
					break;
				}

				$found = $wpdb->get_results ($sql);
				$userids = array_intersect ($userids, bps_conv ($found, 'user_id'));

				if (count ($userids) == 0)  return $noresults;
				$emptyform = false;

			endwhile;
		endwhile;
	endif;

	if ($emptyform == true)  return $noresults;

	remove_filter ('bp_core_get_users', 'bps_search', 99, 2);

	$params['per_page'] = count ($userids);
	$params['include'] = $wpdb->escape (implode (',', $userids));
	$results = bp_core_get_users ($params);

	return $results;
}

function bps_conv ($objects, $field)
{
	$array = array ();

	foreach ($objects as $object)
		$array[] = $object->$field;

	return $array;	
}

add_filter ('bp_get_the_profile_field_options_select', 'bps_field_options', 99, 2);
add_filter ('bp_get_the_profile_field_options_radio', 'bps_field_options', 99, 2);
add_filter ('bp_get_the_profile_field_options_checkbox', 'bps_field_options', 99, 2);

function bps_field_options ($html, $option)
{
	global $field;
	global $bps_search_form;
	
	if ($bps_search_form != true)  return $html;

	switch ($field->type)
	{
	case 'textbox':
	case 'textarea':
	case 'datebox':
		break;

	case 'selectbox':
		if ($option->name == $_POST["field_$field->id"])
			$selected = ' selected="selected"';
		else
			$selected = '';

		$html = '<option'. $selected. ' value="'. esc_attr ($option->name). '">'.
				esc_attr ($option->name). '</option>';
		break;

	case 'radio':
		if ($option->name == $_POST["field_$field->id"])
			$selected = ' checked="checked"';
		else
			$selected = '';

		$html = '<label><input'. $selected. ' type="radio" name="field_'. $field->id. '" value="'.
				esc_attr ($option->name). '"> '. esc_attr ($option->name). '</label>';
		break;

	case 'multiselectbox':
		if (is_array ($_POST["field_$field->id"]) && in_array ($option->name, $_POST["field_$field->id"]))
			$selected = ' selected="selected"';
		else
			$selected = '';

		$html = '<option'. $selected. ' value="'. esc_attr ($option->name). '">'.
				esc_attr ($option->name). '</option>';
		break;

	case 'checkbox':
		if (is_array ($_POST["field_$field->id"]) && in_array ($option->name, $_POST["field_$field->id"]))
			$selected = ' checked="checked"';
		else
			$selected = '';

		$html = '<label><input'. $selected. ' type="checkbox" name="field_'. $field->id. '[]" value="'.
				esc_attr ($option->name). '"> '. esc_attr ($option->name). '</label>';
		break;
	}

	return $html;
}
?>

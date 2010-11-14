<?php

/*
Plugin Name: BP Profile Search 
Plugin URI: http://www.blogsweek.com/bp-profile-search/
Description: Search BuddyPress extended profiles.
Version: 2.0
Author: Andrea Tarantini
Author URI: http://www.blogsweek.com/
*/

global $ps_define;
$ps_define = new stdClass;
$ps_define->option_group  	= 'ps-options';
$ps_define->option_name  	= 'ps-options';

include 'ps-admin.php';

function ps_activate ()
{
//	ps_set_default_options ();
	return true;
}

function ps_init ()
{
	global $ps_define;
	global $ps_options;

	$ps_options = get_option ($ps_define->option_name);
	if ($ps_options == false)  ps_set_default_options ();
	return true;
}

function ps_register_setting ()
{
	global $ps_define;

	register_setting ($ps_define->option_group, $ps_define->option_name);
	return true;
}

function ps_form ()
{
	global $bp;
	global $field;
	global $ps_options;
	global $ps_search_form;

	$ps_search_form = true;
?>

<form action="" method="post" id="profile-edit-form" class="standard-form">

<?php
	echo $ps_options['message'];

	if (bp_has_profile ()): while (bp_profile_groups ()):
		bp_the_profile_group ();

		$group_empty = true;
		while (bp_profile_fields ()):
			bp_the_profile_field ();

			if (bp_get_the_profile_field_id () == $ps_options['agerange']):
				$from = ($_POST["field_{$field->id}"] == '' && $_POST["field_{$field->id}_to"] == '')? $from = '': (int)$_POST["field_{$field->id}"];
				$to = ($_POST["field_{$field->id}_to"] == '')? $to = $from: (int)$_POST["field_{$field->id}_to"];
				if ($to < $from)  $to = $from;
				$_POST["field_{$field->id}"] = $from;
				$_POST["field_{$field->id}_to"] = $to;

?>				<div class="datebox">
					<label for="<?php bp_the_profile_field_input_name(); ?>">Age range</label>
					<input style="width: 10%;" type="text" name="<?php bp_the_profile_field_input_name(); ?>" value="<?php echo $from; ?>" />
					&nbsp;-&nbsp;
					<input style="width: 10%;" type="text" name="<?php bp_the_profile_field_input_name(); ?>_to" value="<?php echo $to; ?>" />
				</div>
<?php		endif;

			if (!in_array (bp_get_the_profile_field_id (), (array)$ps_options['fields']))  continue;

				if ($group_empty == true)
				{
					echo '<h5>'. bp_get_the_profile_group_name (). ':</h5>';
					$group_empty = false;
				}
?>
			<div <?php bp_field_css_class ('editfield'); ?>>

<?php			switch (bp_get_the_profile_field_type())
				{
				case 'textbox':
?>					<label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?></label>
					<input type="text" name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>" value="<?php bp_the_profile_field_edit_value(); ?>" />
<?php				break;

				case 'textarea':
?>					<label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?></label>
					<textarea rows="5" cols="40" name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_edit_value(); ?></textarea>
<?php				break;

				case 'selectbox':
?>					<label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?></label>
					<select name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>">
						<?php bp_the_profile_field_options(); ?>
					</select>
<?php				break;

				case 'multiselectbox':
?>					<label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?></label>
					<select name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>" multiple="multiple">
						<?php bp_the_profile_field_options(); ?>
					</select>
					<?php if (!bp_get_the_profile_field_is_required()): ?>
						<a class="clear-value" href="javascript:clear('<?php bp_the_profile_field_input_name(); ?>');"><?php _e('Clear', 'buddypress'); ?></a>
					<?php endif; ?>
<?php				break;

				case 'radio':
?>					<div class="radio">
						<span class="label"><?php bp_the_profile_field_name(); ?></span>
						<?php bp_the_profile_field_options(); ?>
						<?php if (!bp_get_the_profile_field_is_required()): ?>
							<a class="clear-value" href="javascript:clear('<?php bp_the_profile_field_input_name(); ?>');"><?php _e('Clear', 'buddypress'); ?></a>
						<?php endif; ?>
					</div>
<?php				break;

				case 'checkbox':
?>					<div class="checkbox">
						<span class="label"><?php bp_the_profile_field_name(); ?></span>
						<?php bp_the_profile_field_options(); ?>
					</div>
<?php				break;

				case 'datebox':
?>					<div class="datebox">
						<label for="<?php bp_the_profile_field_input_name(); ?>_day"><?php bp_the_profile_field_name(); ?></label>
						<select name="<?php bp_the_profile_field_input_name(); ?>_day" id="<?php bp_the_profile_field_input_name(); ?>_day">
							<?php bp_the_profile_field_options('type=day'); ?>
						</select>
						<select name="<?php bp_the_profile_field_input_name(); ?>_month" id="<?php bp_the_profile_field_input_name(); ?>_month">
							<?php bp_the_profile_field_options('type=month'); ?>
						</select>
						<select name="<?php bp_the_profile_field_input_name(); ?>_year" id="<?php bp_the_profile_field_input_name(); ?>_year">
							<?php bp_the_profile_field_options('type=year'); ?>
						</select>
					</div>
<?php				break;
				}
?>
				<p class="description"><?php bp_the_profile_field_description(); ?></p>
			</div>

<?php 	endwhile;
		if ($group_empty == false)  echo '<br />';

	endwhile; endif;
?>
	<div class="submit">
		<input type="submit" name="members_search_submit" id="members_search_submit" value="<?php _e('Search', 'buddypress'); ?>" />
		<?php echo '<a href="'. $bp->root_domain. '/'. BP_MEMBERS_SLUG. '/">'. __('Clear Form', 'buddypress'). '</a>'; ?>
	</div>

	<input type="hidden" name="bp_profile_search" value="true" />
	<?php wp_nonce_field ('bp_profile_search'); ?>

</form>

<?php
	if ($_POST['bp_profile_search'] == true)  $_REQUEST['num'] = 9999;
}

function ps_search ($results, $params)
{
	global $wpdb;
	global $field;
	global $ps_options;

	if ($_POST['bp_profile_search'] != true)  return $results;

	$noresults['users'] = array ();
	$noresults['total'] = 0;

	$sql = "SELECT DISTINCT user_id from {$wpdb->prefix}bp_xprofile_data";
	$found = $wpdb->get_results ($sql);
	$userids = ps_conv ($found, 'user_id');
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
					$sql = "SELECT user_id from {$wpdb->prefix}bp_xprofile_data";
					$sql .= " WHERE field_id = $id AND value LIKE '$value'";
					break;

				case 'selectbox':
				case 'radio':
					$sql = "SELECT user_id from {$wpdb->prefix}bp_xprofile_data";
					$sql .= " WHERE field_id = $id AND value = '$value'";
					break;

				case 'multiselectbox':
				case 'checkbox':
					$sql = "SELECT user_id from {$wpdb->prefix}bp_xprofile_data";
					$sql .= " WHERE field_id = $id";
					$like = array ();
					foreach ($value as $curvalue)
						$like[] = "value LIKE '%\"$curvalue\"%'";
					$sql .= ' AND ('. implode (' OR ', $like). ')';	
					break;

				case 'datebox':
					if ($id != $ps_options['agerange'])  continue;
					
					$time = time ();
					$day = date ("j", $time);
					$month = date ("n", $time);
					$year = date ("Y", $time);
					$min = mktime (0, 0, 0, $month, $day+1, $year-$to-1);	
					$max = mktime (0, 0, 0, $month, $day, $year-$value);

					$sql = "SELECT user_id from {$wpdb->prefix}bp_xprofile_data";
					$sql .= " WHERE field_id = $id AND value BETWEEN $min AND $max";
					break;
				}

				$found = $wpdb->get_results ($sql);
				$userids = array_intersect ($userids, ps_conv ($found, 'user_id'));

				if (count ($userids) == 0)  return $noresults;
				$emptyform = false;

			endwhile;
		endwhile;
	endif;

	if ($emptyform == true)  return $noresults;

	remove_filter ('bp_core_get_users', 'ps_search', 99, 2);

	$params['per_page'] = count ($userids);
	$params['include'] = $wpdb->escape (implode (',', $userids));
	$results = bp_core_get_users ($params);

	return $results;
}

function ps_conv ($objects, $field)
{
	$array = array ();

	foreach ($objects as $object)
		$array[] = $object->$field;

	return $array;	
}

register_activation_hook (__FILE__, 'ps_activate');
add_action ('init', 'ps_init');

add_action ('admin_init', 'ps_register_setting');
add_action ('admin_menu', 'ps_add_pages');

add_action ('bp_profile_search_form', 'ps_form');
add_filter ('bp_core_get_users', 'ps_search', 99, 2);

add_filter ('bp_get_the_profile_field_options_select', 'ps_field_options', 99, 2);
add_filter ('bp_get_the_profile_field_options_radio', 'ps_field_options', 99, 2);
add_filter ('bp_get_the_profile_field_options_checkbox', 'ps_field_options', 99, 2);

function ps_field_options ($html, $option)
{
	global $field;
	global $ps_search_form;
	
	if ($ps_search_form != true)  return $html;

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

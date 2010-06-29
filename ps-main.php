<?php

/*
Plugin Name: BP Profile Search 
Plugin URI: http://www.blogsweek.com/
Description: Search BuddyPress extended profiles.
Version: 1.0
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
	ps_set_default_options ();
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
	global $ps_options;
?>

<form action="" method="post" id="profile-edit-form" class="standard-form">

	<?php echo $ps_options['message']; ?>

	<?php if (bp_has_profile ()): while (bp_profile_groups()): bp_the_profile_group(); ?>

		<?php $group_empty = true; ?>
		<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>
			<?php 
				if (!in_array (bp_get_the_profile_field_id (), (array)$ps_options['fields']))  continue; 
				else if ($group_empty == true)
				{
					echo '<h5>'. bp_get_the_profile_group_name (). ':</h5>';
					$group_empty = false;
				}
			?>

			<div <?php bp_field_css_class( 'editfield' ) ?>>

				<?php if ( 'textbox' == bp_get_the_profile_field_type() ) : ?>

					<label for="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '', 'buddypress' ) ?><?php endif; ?></label>
					<input type="text" name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>" value="<?php bp_the_profile_field_edit_value() ?>" />

				<?php endif; ?>

				<?php if ( 'textarea' == bp_get_the_profile_field_type() ) : ?>

					<label for="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '', 'buddypress' ) ?><?php endif; ?></label>
					<textarea rows="5" cols="40" name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_edit_value() ?></textarea>

				<?php endif; ?>

				<?php if ( 'selectbox' == bp_get_the_profile_field_type() ) : ?>

					<label for="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '', 'buddypress' ) ?><?php endif; ?></label>
					<select name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>">
						<?php bp_the_profile_field_options() ?>
					</select>

				<?php endif; ?>

				<?php if ( 'multiselectbox' == bp_get_the_profile_field_type() ) : ?>

					<label for="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '', 'buddypress' ) ?><?php endif; ?></label>
					<select name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>" multiple="multiple">
						<?php bp_the_profile_field_options() ?>
					</select>

					<?php if ( !bp_get_the_profile_field_is_required() ) : ?>
						<a class="clear-value" href="javascript:clear( '<?php bp_the_profile_field_input_name() ?>' );"><?php _e( 'Clear', 'buddypress' ) ?></a>
					<?php endif; ?>

				<?php endif; ?>

				<?php if ( 'radio' == bp_get_the_profile_field_type() ) : ?>

					<div class="radio">
						<span class="label"><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '', 'buddypress' ) ?><?php endif; ?></span>

						<?php bp_the_profile_field_options() ?>

						<?php if ( !bp_get_the_profile_field_is_required() ) : ?>
							<a class="clear-value" href="javascript:clear( '<?php bp_the_profile_field_input_name() ?>' );"><?php _e( 'Clear', 'buddypress' ) ?></a>
						<?php endif; ?>
					</div>

				<?php endif; ?>

				<?php if ( 'checkbox' == bp_get_the_profile_field_type() ) : ?>

					<div class="checkbox">
						<span class="label"><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '', 'buddypress' ) ?><?php endif; ?></span>

						<?php bp_the_profile_field_options() ?>
					</div>

				<?php endif; ?>

				<?php if ( 'datebox' == bp_get_the_profile_field_type() ) : ?>

					<div class="datebox">
						<label for="<?php bp_the_profile_field_input_name() ?>_day"><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '', 'buddypress' ) ?><?php endif; ?></label>

						<select name="<?php bp_the_profile_field_input_name() ?>_day" id="<?php bp_the_profile_field_input_name() ?>_day">
							<?php bp_the_profile_field_options( 'type=day' ) ?>
						</select>

						<select name="<?php bp_the_profile_field_input_name() ?>_month" id="<?php bp_the_profile_field_input_name() ?>_month">
							<?php bp_the_profile_field_options( 'type=month' ) ?>
						</select>

						<select name="<?php bp_the_profile_field_input_name() ?>_year" id="<?php bp_the_profile_field_input_name() ?>_year">
							<?php bp_the_profile_field_options( 'type=year' ) ?>
						</select>
					</div>

				<?php endif; ?>

				<p class="description"><?php bp_the_profile_field_description() ?></p>
			</div>

		<?php endwhile; ?>
		<?php if ($group_empty == false)  echo '<br />'; ?>

	<?php endwhile; endif; ?>

	<div class="submit">
		<input type="submit" name="members_search_submit" id="members_search_submit" value="<?php _e( 'Search', 'buddypress' ) ?>" />
		<?php echo '<a href="'. $bp->root_domain. '/'. BP_MEMBERS_SLUG. '/">'. __('Clear Form', 'buddypress'). '</a>'; ?>
	</div>

	<input type="hidden" name="bp_profile_search" value="true" />
	<?php wp_nonce_field ('bp_profile_search'); ?>

</form>

<?php
	if ($_POST['bp_profile_search'] == true)  $_REQUEST['num'] = 99999;
}

function ps_search ($results, $params)
{
	global $wpdb;

	if ($_POST['bp_profile_search'] != true)  return $results;

	$noresults['users'] = array ();
	$noresults['total'] = 0;

	$fields = array ();
	foreach ($_POST as $key => $value)
		if ($value && preg_match ('/^field_([0-9]*)$/', $key, $matches))  
			$fields[] = array ($matches[1], $value);

	if (count ($fields) == 0)  return $noresults;

	$sql = "SELECT DISTINCT user_id from {$wpdb->prefix}bp_xprofile_data";
	$found = $wpdb->get_results ($sql);
	$userids = ps_conv ($found, 'user_id');

	foreach ($fields as $field)
	{
		$sql = "SELECT DISTINCT user_id from {$wpdb->prefix}bp_xprofile_data";
		$sql .= " WHERE field_id = '$field[0]' AND value = '$field[1]'";
		$found = $wpdb->get_results ($sql);
		$userids = array_intersect ($userids, ps_conv ($found, 'user_id'));

		if (count ($userids) == 0)  return $noresults;
	}

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

?>

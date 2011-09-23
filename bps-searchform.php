<?php

add_action ('bp_profile_search_form', 'bps_form');
function bps_form ()
{
	global $bp;
	global $field;
	global $bps_options;
	global $bps_search_form;
	global $bps_list;

	$bps_search_form = true;
?>

<form action="" method="post" id="profile-edit-form" class="standard-form">

<div class="item-list-tabs">
	<ul>
	<li><?php echo $bps_options['header']; ?><p></p></li>
<?php if (in_array ('Enabled', (array)$bps_options['show'])) { ?>
	<li class="last filter"><?php echo $bps_options['message']; ?>&nbsp;<input id="bps_Show" type="checkbox" onclick="javascript:bps_toggleForm()" /></li>
<?php } ?>
	</ul>
</div>

<div id="bps_Form">
<?php
	if (bp_has_profile ('hide_empty_fields=0')): while (bp_profile_groups ()):
		bp_the_profile_group ();

		$group_empty = true;
		while (bp_profile_fields ()):
			bp_the_profile_field ();

			if (bp_get_the_profile_field_id () == $bps_options['agerange']):
				$from = ($_POST["field_{$field->id}"] == '' && $_POST["field_{$field->id}_to"] == '')? $from = '': (int)$_POST["field_{$field->id}"];
				$to = ($_POST["field_{$field->id}_to"] == '')? $to = $from: (int)$_POST["field_{$field->id}_to"];
				if ($to < $from)  $to = $from;
				$_POST["field_{$field->id}"] = $from;
				$_POST["field_{$field->id}_to"] = $to;
?>				
			<div <?php bp_field_css_class ('editfield'); ?>>
				<label for="<?php bp_the_profile_field_input_name(); ?>"><?php echo $bps_options['agelabel']; ?></label>
				<input style="width: 10%;" type="text" name="<?php bp_the_profile_field_input_name(); ?>" value="<?php echo $from; ?>" />
				&nbsp;-&nbsp;
				<input style="width: 10%;" type="text" name="<?php bp_the_profile_field_input_name(); ?>_to" value="<?php echo $to; ?>" />
				<p class="description"><?php echo $bps_options['agedesc']; ?></p>
			</div>
<?php		endif;

			if (!in_array (bp_get_the_profile_field_id (), (array)$bps_options['fields']))  continue;

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

<?php if (in_array ('Enabled', (array)$bps_options['show'])) { ?>
<script type="text/javascript">
	function bps_toggleForm () {
		if (jQuery('#bps_Show').is(':checked'))
			jQuery('#bps_Form').show();
		else
			jQuery('#bps_Form').hide();
	}

	jQuery(document).ready(function() {
		bps_toggleForm ();
	});
</script>
<?php } ?>

</div>
<?php

	if ($_POST['bp_profile_search'] == true)
		if ($bps_list+1 == $bps_options['filtered'])  $_REQUEST['num'] = 9999;
}
?>

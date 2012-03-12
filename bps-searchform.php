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
	if (bp_has_profile ('hide_empty_fields=0'))  while (bp_profile_groups ())
	{
		bp_the_profile_group ();

		$group_empty = true;
		while (bp_profile_fields ())
		{
			bp_the_profile_field ();

			if (bp_get_the_profile_field_id () == $bps_options['agerange'])
			{
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
<?php		}

			if (!in_array (bp_get_the_profile_field_id (), (array)$bps_options['fields']))  continue;

			if ($group_empty == true)
			{
				echo '<h5>'. bp_get_the_profile_group_name (). ':</h5>';
				$group_empty = false;
			}

			echo '<div '. bp_get_field_css_class ('editfield'). '>';

			$field_input_name = 'field_'. $field->id;
			$posted = $_POST[$field_input_name];

			if (!method_exists ($field, 'get_children'))
				$field = new BP_XProfile_Field ($field->id);

			$options = $field->get_children ();

			switch (bp_get_the_profile_field_type ())
			{
			case 'textbox':
				echo "
<label for='$field_input_name'>$field->name</label>
<input type='text' name='$field_input_name' id='$field_input_name' value='$posted' />
";
				break;

			case 'textarea':
				echo "
<label for='$field_input_name'>$field->name</label>
<textarea rows='5' cols='40' name='$field_input_name' id='$field_input_name'>$posted</textarea>
";
				break;

			case 'selectbox':
				echo "
<label for='$field_input_name'>$field->name</label>
<select name='$field_input_name' id='$field_input_name'>
<option value=''></option>
";
				foreach ($options as $option)
				{
					$selected = ($option->name == $posted)? "selected='selected'": "";
					echo "
<option $selected value='$option->name'>$option->name</option>
";
				}
				echo "
</select>
";
				break;

			case 'multiselectbox':
				echo "
<label for='$field_input_name'>$field->name</label>
<select name='{$field_input_name}[]' id='$field_input_name' multiple='multiple'>
";
				foreach ($options as $option)
				{
					$selected = (in_array ($option->name, (array)$posted))? "selected='selected'": "";
					echo "
<option $selected value='$option->name'>$option->name</option>
";
				}
				echo "
</select>
";
				break;

			case 'radio':
				echo "
<div class='radio'>
<span class='label'>$field->name</span>
<div id='$field_input_name'>
";
				foreach ($options as $option)
				{
					$selected = ($option->name == $posted)? "checked='checked'": "";
					echo "
<label><input $selected type='radio' name='$field_input_name' value='$option->name'>$option->name</label>
";
				}
				echo "
</div>
<a class='clear-value' 
href='javascript:clear(\"$field_input_name\");'>". __('Clear', 'buddypress'). "</a>
</div>
";
				break;

			case 'checkbox':
				echo "
<div class='checkbox'>
<span class='label'>$field->name</span>
";
				foreach ($options as $option)
				{
					$selected = (in_array ($option->name, (array)$posted))? "checked='checked'": "";
					echo "
<label><input $selected type='checkbox' name='{$field_input_name}[]' value='$option->name'>$option->name</label>
";
				}
				echo "
</div>
";
				break;
			}

			echo '</div>';
		}

		if ($group_empty == false)  echo '<br />';
	}
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

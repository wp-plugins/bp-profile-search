=== BP Profile Search ===
Contributors: 
Tags: buddypress, profile, search
Requires at least: 3.0
Tested up to: 3.0
Stable tag: 1.0

Adds a customizable search form to your BuddyPress Members page, so visitors can find site members searching their public profiles.

== Description ==

BP Profile Search adds a customizable search form to your BuddyPress Members page, so visitors can find site members searching their public profiles.

== Installation ==

After the standard manual or automatic plugin installation procedure, add the profile search form to your BuddyPress theme.

If you are using the BuddyPress default theme, open *index.php* in the *buddypress/bp-themes/bp-default/members* folder, locate this line (around line 14):

`</div><!-- #members-dir-search -->`

and, right after that, insert the BP Profile Search form:

`<?php do_action ('bp_profile_search_form'); ?>`

To customize the profile search form, use the plugin admin page *BuddyPress -> Profile Search*. Since the *datebox*, *multiselectbox* and *checkbox* profile field types are not currently supported, those fields are not selectable in the plugin admin page.

Note: if you can't access the plugin admin page, try renaming your plugin folder name from *bp-profile-search* to *profile-search* and reactivating the plugin.

== Changelog ==

= 1.0 =
* First version released to the WordPress Plugin Directory

=== BP Profile Search ===
Tags: buddypress, profile, search
Requires at least: 3.0
Tested up to: 3.0.3
Stable tag: 2.2

Adds a customizable search form to your BuddyPress Members page, so visitors can find site members searching their public profiles.

== Description ==

BP Profile Search adds a customizable search form to your BuddyPress Members page, so visitors can find site members searching their public profiles.

Features:

BP Profile Search shows a collapsible search form in your Members page. The form header text and the profile fields to include in the search are configurable from the admin page.

BP Profile Search by default returns exact matches only, but users can request a fuzzy search using the wildcard characters *% (percent sign)*, matching zero or more characters, and *_ (underscore)*, matching exactly one character. So, for instance, a search for *%John%* matches *John*, *Johnson*, *Long John Silver* and so on.

If your extended profiles include a birth date field, BP Profile Search can also perform age range searches, allowing users to enter the minimum and maximum age for their search.

In the back end, BP Profile Search allows to customize the search form header text, select the profile fields to include in the search, enable the show/hide form feature, enable and customize the age range search feature.

== Installation ==

After the standard manual or automatic plugin installation procedure, add the profile search form to your BuddyPress Members page.

If you are using the BuddyPress default theme, open *index.php* in the *buddypress/bp-themes/bp-default/members* folder, locate this line (line 14):

`</div><!-- #members-dir-search -->`

and, right after that, insert the BP Profile Search form:

`<?php do_action ('bp_profile_search_form'); ?>`

If you are *not* using the BuddyPress default theme, you have to insert the BP Profile Search form somewhere in your theme Members page.

To customize the profile search form, use the plugin admin page *BuddyPress -> Profile Search*. Since the *datebox* profile fields are not currently supported, those fields are not selectable in the plugin admin page.

Note: if you can't access the plugin admin page, try renaming your plugin folder name from *bp-profile-search* to *profile-search* and reactivating the plugin.

== Changelog ==

= 2.2 =
* Added the Age Range Search option
= 2.1 =
* Added the option to show/hide the search form
* Fixed a bug where no results were found in some installations
= 2.0 =
* Added support for *multiselectbox* and *checkbox* profile fields
* Added support for % and _ wildcard characters in text searching
= 1.0 =
* First version released to the WordPress Plugin Directory

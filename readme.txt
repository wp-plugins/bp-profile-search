=== BP Profile Search ===
Contributors: dontdream
Tags: buddypress, member, members, friend, friends, user, users, profile, profiles, search, filter
Requires at least: BP 1.7
Tested up to: BP 1.7.2
Stable tag: 3.4.1

Adds a configurable search form to your BuddyPress site, so visitors can find site members searching their extended profiles.

== Description ==

BP Profile Search adds a configurable search form to your BuddyPress site, so visitors can find site members searching their extended profiles.

You can insert the search form in a post or page, or in a sidebar or widget area, or anywhere in your theme, e.g. in your Members Directory template file.

In all three cases when visitors click the 'Search' button, they are redirected to your Members Directory page showing their search results. The 'All Members' tab shows all the results, while the 'My Friends' tabs shows the results found among your visitor's friends.

== Installation ==

After the standard manual or automatic plugin installation procedure, you'll be able to access the plugin settings page *Users -> Profile Search*.

= Configuration =

In the plugin settings page you have the following options:

* Specify the HTML text for the form header and welcome message.

* Enable the toggle form feature.

* Select the profile fields to include in the search form (currently the *datebox* profile fields are not supported);

* If your extended profiles include a birth date field, enable the Age Range search, so your visitors can specify the minimum and maximum age for their search.

* If your extended profiles include a numerical *textbox* or *selectbox* field, enable the Value Range search, so your visitors can specify the minimum and maximum value for their search.

* Select the search mode for text fields, between *partial match*, where a search for *John* matches field values of *John*, *Johnson*, *Long John Silver*, and so on, and *exact match*, where a search for *John* matches the field value *John* only.

In both search modes the wildcard characters *% (percent sign)*, matching zero or more characters, and *_ (underscore)*, matching exactly one character, are available to your visitors to better specify their search.

= Displaying the search form =

There are three different ways to integrate your BP Profile Search form in your BuddyPress site. You can insert the form:

* In a post or page, using the shortcode **[bp_profile_search_form]**

* In a sidebar or widget area, using the ***BP Profile Search*** widget

* In your template files, e.g. in your Members Directory page, using the code  
**&lt;?php do_action ('bp_profile_search_form'); ?&gt;**

Shortcodes and widgets are standard WordPress features, so I won't explain their usage here. Adding the search form in your template files is also very simple, as shown in the following example.

= Example =

If you wish to add the search form to your Members Directory page, and you are using the BuddyPress default theme, open *index.php* in the *buddypress/bp-themes/bp-default/members* folder, locate this line:

`</div><!-- #members-dir-search -->`

and, right after that, insert the BP Profile Search form:

`<?php do_action ('bp_profile_search_form'); ?>`

If you are *not* using the BuddyPress default theme, the procedure might be different.

== Changelog ==

= 3.4.1 =
* Added *selectbox* profile fields as candidates for the Value Range Search feature
= 3.4 =
* Added the Value Range Search feature (Contributor: Florian Shießl)
= 3.3 =
* Added pagination for search results
* Added searching in the 'My Friends' tab of the Members Directory
* Removed the *Filtered Members List* option in the *Advanced Options* tab
* Requires BuddyPress 1.7 or higher
= 3.2 =
* Updated for BuddyPress 1.6
* Requires BuddyPress 1.6 or higher
= 3.1 =
* Fixed the search when field options contain trailing spaces
* Fixed the search when field type is changed after creation
= 3.0 =
* Added the BP Profile Search widget
* Added the [bp_profile_search_form] shortcode
= 2.8 =
* Fixed the Age Range Search feature
* Fixed the search form for required fields
* Removed field descriptions from the search form
* Requires BuddyPress 1.5 or higher
= 2.7 =
* Updated for BuddyPress 1.5 multisite
* Requires BuddyPress 1.2.8 or higher
= 2.6 =
* Updated for BuddyPress 1.5
= 2.5 =
* Updated for BuddyPress 1.2.8 multisite installations
= 2.4 =
* Changed the file names to allow activation in some installations
* Added the *Filtered Members List* option in the *Advanced Options* tab
= 2.3 =
* Added the choice between partial match and exact match for text searches
* Added a workaround so renaming the plugin folder is no longer required
= 2.2 =
* Added the Age Range Search feature
= 2.1 =
* Added the option to show/hide the search form
* Fixed a bug where no results were found in some installations
= 2.0 =
* Added support for *multiselectbox* and *checkbox* profile fields
* Added support for % and _ wildcard characters in text searches
= 1.0 =
* First version released to the WordPress Plugin Directory

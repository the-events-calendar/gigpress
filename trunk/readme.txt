=== GigPress ===
Contributors: mrherbivore
Donate link: http://gigpress.com/donate
Tags: concerts, bands, tours, shows, record labels, music, musicians, performers, artists
Requires at least: 3.0
Tested up to: 4.1

GigPress is a live performance listing and management plugin that's been serving musicians and performers since 2007.

== Description ==

GigPress is a powerful live performance listing and management plugin designed for musicians and other performers. Manage all of your upcoming and past performances right from within the WordPress admin, and display them on your site using simple shortcodes, PHP template tags, or the GigPress widget on your WordPress-powered website.

* GigPress is intuitive and easy-to-use. Add artists, venues, tours, and related posts on-the-fly, all saved in your database for re-use, all seamlessly within the WordPress admin.
* Manage multiple artists within GigPress, and display them either as a combined listing, or grouped by artist. Add an artist parameter to the shortcode and list only shows from a particular artist.
* GigPress features RSS and iCalendar feeds for your upcoming shows and for individual artists and tours, plus Google Calendar and iCal download links for each individual show. It can also output Schema.org/Event structured data for search engines.
* Advanced users can fully-customize the HTML and CSS used by GigPress to display your shows without altering any plugin files, making all changes upgrade-safe.
* Link up a related post for each show and your show's full details will appear within your post. Automatically create new related posts with customizable titles when entering new shows.
* No lock-in here. Import your shows from a CSV file, without fear of duplicate data. Export your shows database to CSV - filtered by artist, tour, and date.

== Changelog ==

= 2.3.8 =

* Fixed error which could result in GigPress' javascript being loaded too early

= 2.3.7 =

* Now using GMT -11 to determine expiry of shows to allow for longer display of shows in timezones earlier than the site timezone
* Updated .pot file so translators can add missing strings

= 2.3.6 = 

* Fixed bug where disabling JSON-LD output didn't in fact disable JSON-LD output
* Removed calls to mysql_real_escape_string during CSV import
* Fixed a bug where existing artists, venues, and tours were not identified and used during import in some cases
* Fixed potential PHP warnings during CSV import

= 2.3.5 = 

* Added the state and external link values to the RSS feed output

= 2.3.4 =

* Now always setting an event name in the JSON-LD markup
* Removed the requirement that a price be included to include offer information in the JSON-LD markup (h/t Alex Unger)

= 2.3.3 =

* Fix improper escaping of quotes in up_json_encode function (for PHP < 5.4) - h/t Alex Unger

= 2.3.2 =

* Now enabling JSON-LD output by default when upgrading
* Removed some extraneous markup from default templates
* Fixed PHP error related to custom menu order when inside Network Dashboard

= 2.3.1 =

* Added support for outputting schema.org/Event structured data under PHP < 5.4

= 2.3 =

* Added schema.org/Event structured data markup to support indexing of events by search engines (formatted with the new JSON-LD standard)
* Removed hCalendar markup elements from default templates

= 2.2.9.3 =

* Fixed alphabetical ordering of artists (ignoring definite/indefinite articles)
* Updated German translation

= 2.2.9.2 =

* Added setting for "Buy Tickets" language
* Updated admin menu with new icon for WordPress 3.8

= 2.2.9.1 = 

* Fixed fatal typo

= 2.2.9 =

* Replaced all instances of `WP_PLUGIN_URL` with `plugins_url()` function for compatibility with SSL-enabled sites (hat-tip: Jonathan Eiseman)

= 2.2.8 =

* Fixed debug notices in iCal and RSS feeds (`$wpdb->prepare()` arguments missing)
* Moved country list into /lib folder as external file for easier customization

= 2.2.7 =

* Fixed bug with boolean (checkbox) settings not sticking
* Improved logic for checking existence of boolean (checkbox) settings on admin screens and in templates
* Updated country list


= 2.2.6 =

* Fixes for artist and venue iCal links in default footer template (h/t Jeremiah Hester)
* Added Finnish, Japanese, and Slovak translations (thanks Niilo Sirola, @takipone, and Branco Radenovich)
* Various bug fixes

= 2.2.5 =

* Fixed glitch in shows list footer template when filtering by artist
* Added escaping of commas in iCalendar summaries; new `$showdata` parameter 'calendar_summary_ical'
* Added option to limit the number of shows in the RSS and iCal feeds

= 2.2.4 =

* Improved display logic in related post menu when adding/editing shows
* Added limit option for 300 shows in the admin

= 2.2.3 =

* Added link to add a similar show (copying all previous info) to the post-add success message - generously sponsored by Lee Eiseman
* Added more limit options to the Manage Shows screen
* Show scope is now sticky per-user on the Manage Shows screen
* Changed `.divider` CSS class to `.gigpress-divider` to prevent CSS conflicts with some themes

= 2.2.2 =

* The Related Post menu is no longer restricted to your Related Post category
* Fixed typo in CSV export which caused it to fail

= 2.2.1 =

* Updated Bulgarian translation (thanks Ivo Minchev)
* Fixed some straggling PHP notices
* More reliable conditional checking on `$showdata` variables

= 2.2 =

* Fixed PHP notices when debugging is on
* Added the function/template tag `gigpress_has_upcoming()` to check if there are upcoming shows
* Added an External Link field to shows (use for Facebook event links or what-have-you)
* Added an Artist URL field
* Added State and Postal Code fields to venues
* Added "limit" option to the Shows admin screen
* Venue and Admittance are now "sticky" (saving new defaults with each new show added)
* The "Related Post" menu is now restricted to the category chosen for Related Posts in your GigPress settings
* Increased the size of the "price" database column to allow for more data
* "the" is now ignored when sorting artists alphabetically
* Added Romanian and Belarusian translations (thanks to Web Hosting Geeks)

= 2.1.15 =

* Removed escaping of commas in Google Calendar links; added new `$showdata` parameters 'calendar_location_ical' and 'calendar_details_ical'

= 2.1.14 =

* Fixed some validation issues with the iCal formatting
* Removed calendar name from individual iCal events to fix poor behaviour in Outlook
* If the minutes portion of the time field is not selected, '00' is now assumed
* The "open links in new windows" setting is now ignored for links which point to the site GigPress is being served from

= 2.1.13 =

* Fixed formatting error when creating related post titles introduced in 2.1.11
* Added "past" scope to sidebar widget

= 2.1.12 =

* Allowing more HTML in the notes field again, while still stripping script tags (some tags were disallowed in last update)
* Added Danish localization (thanks to Rasmus Kern)

= 2.1.11 =

* Updated front-end javascript to fix an incompatibility with plugins that rewrite all hash links to include the full site URL
* Improved sanitation of data, especially in the Notes field
* Fixed default sorting when using "past" show scope to properly display in descending order

= 2.1.10 =

* Changed GIGPRESS_NOW constant to compensate for bug in WordPress' `current_time()` function which was causing shows to expire based on the server time rather than the WordPress localized time
* Fixed error with unquoted `init` action being interpreted as a constant
* Removed deprecated numeric user level indicators, replaced with appropriate role-based permissions
* Edited default templates to only display the "Tour" label if the "Tour" label setting is not empty
* Added Polish and Simplified Chinese translations (thanks to Krystian Buczak and Liang Chuan)

= 2.1.9.1 =

* Fix for future-dated posts not appearing in the related post menu

= 2.1.9 =

* Tested in WordPress 3.0
* Added "sort" as a valid argument to `gigpress_menu()` ("menu_sort" when used as part of the shortcode)
* Added "sort" and "scope" as valid arguments to the `[gigpress_related_shows]` shortcode
* Shows are now further ordered by their end date everywhere (for shows starting on the same day but ending on different days)
* You can now optionally maintain Related Post associations when importing from a GigPress-generated CSV file
* Fixed a bug where related posts weren't appearing in search results when the specified related posts category was excluded from normal listings
* Replaced now-deprecated `clean_url()` calls with `esc_url()`

= 2.1.8 =

* Fixed timezone issues with iCal feeds imported into Google Calendar (thanks to Michael Delaney)
* Now using WP's `clean_url()` function on inputted URLs in place of my far-less-robust version
* Added `[gigpress_related_shows]` shortcode for manual placement of related show info in posts
* Updates to German localization (thanks to Georg Fischer)

= 2.1.7 =

* Added "sort" as a valid argument to `gigpress_sidebar()` function options
* Some changes to the iCalendar date format for better timezone support
* Fixed GMT offset in RSS feeds
* Optimized "Shows" and "Venues" administration screens to reduce memory use
* Added Italian translation (thanks to Chiara Esposito)
* Added `$showdata['date_mysql']` and `$showdata['end_date_mysql']` variables to the `$showdata` array

= 2.1.6 =

* Added `$showdata['related_id']` to the list of available template variables, containing the ID of the show's related post
* Removed CDATA block from the title element of the GigPress RSS feed, as it was preventing character entities from being properly displayed in some readers
* Now assuming a "year" paremeter of "current" when only a "month" parameter is passed to the shortcode
* Added "past" and "all" as valid values for the "scope" argument to the `gigpress_sidebar()` function
* Corrected file name of the Swedish translation
* Small bug fix for the CSV import routine

= 2.1.5 =

* Fixed PHP error introduced in 2.1 caused by the Related Post menu when editing shows

= 2.1.4 =

* Fixed another bug introduced in 2.1.2 where the default scope for `gigpress_sidebar()` was "today" rather than "upcoming"

= 2.1.3 =

* Fixed bug introduced in 2.1.2 where widget listing was broken. Oops.

= 2.1.2 =

* Added new "today" option for the "scope" parameter of the `[gigpress_shows]` shortcode to display only shows happening today
* Added new "sort" parameter to the `[gigpress_shows]` shortcode to control date sorting
* Added option to the widget to display only shows happening today
* Fixed a bug where linebreaks and other characters in the "notes" field could break the iCalendar format
* Fixed a bug which allowed the entry of shows with no artist
* Fixed a bug introduced in 2.1 where server time was being used rather than WordPress GMT offset time
* Some small fixes for iCal feeds

= 2.1.1 =

* Fixed bug where widget title would not display

= 2.1 =

* **GigPress now requires WordPress 2.8 or newer**
* Overhauled the GigPress widget to use the new WordPress widget class for multiple-widget capability - **existing widgets will have their settings reset**
* Added options to restrict widget listing to a single artist, tour, or venue
* Made changes to the `gigpress_sidebar()` function to behave more like the `gigpress_shows()` function (arguments are now passed as an array, and the function must be echoed). **If you call `gigpress_sidebar()` from your template you must update your code - please see the docs for details**
* New template variables `$link` and `$show_feeds` for the *sidebar-list-footer* template - **update your customized template if neccessary** (see default template for example use)
* New `[gigpress_menu]` shortcode/function for displaying a monthly or yearly dropdown menu independent of the `[gigpress_shows]` shortcode
* New `show_menu` parameter for `[gigpress_shows]` shortcode to display a monthly or yearly dropdown menu for filtering the shows specified by the shortcode
* New `year` and `month` parameters for `[gigpress_shows]` shortcode for filtering shows by date
* Added *before-menu* and *after-menu* templates for surrounding the new dropdown menu
* Added bulk-deletion of shows
* Fixed issue where show->post relationships were lost when shows whose related post was outside of the last 100 posts
* Increased related post dropdown listing from 100 to 500 posts
* Updated default templates to not show gCal and iCal links when displaying past shows
* More fixes for certain strict MySQL configurations
* Now using the $wp_locale object for month name translations

= 2.0.3 =

* Fixed a bug (for real this time) where `$artist` and `$tour` parameters passed to the `gigpress_sidebar()` function were ignored when not grouping by artist
* Some fixes for people running MySQL in strict mode
* Updated German localization

= 2.0.2 =

* Fixed an issue with multi-day shows ending one day ahead of time in gCal and iCal
* Fixed a bug where `$artist` and `$tour` parameters passed to the `gigpress_sidebar()` function were ignored
* Updated CSV import to allow venues with the same name but in different cities and/or countries
* You can now customize the order in which artists are displayed when grouping by artist
* Added a new shortcode parameter `artist_order` - defaults to `custom` but can be set to `alphabetical` to override custom artist ordering
* Added new widget setting re: artist order
* Added `$total_shows` variable to related show template (useful for posts with multiple related shows)


= 2.0.1 =

* Updated jQuery script for better compatibility with other JS libraries
* Custom templates can now be stored in either `/wp-content/gigpress-templates/`, `/(active_theme_folder)/gigpress-templates/` or `/(child_theme_folder)/gigpress-templates/`
* Added some new  variables to the `$showdata` array for use in custom templates
* Added new `venue` shortcode parameter to display only shows from a specific venue
* Updated default templates to customize display when filtering by venue
* Added options to disable both the default style sheet and the default JavaScript
* Sort order on the "manage shows" screen is now persistent per user
* Added Spanish and Brazilian Portuguese localizations; minor updates to French, Bulgarian and Dutch

= 2.0 =

* Lost several translations due to the massive plugin overhaul and consequent deluge of new language. For now, 2.0 only includes Bulgarian, Dutch, French, German, Norwegian, Russian, and Swedish translations.
* WordPress 2.6.5 is now required
* GigPress now supports multiple artists - yay!
* Venues are now stored in the database for future editing and re-use
* Added Google Calendar and iCal download links for each show
* Added an iCalendar feed for all shows, and for individual artists and tours
* Added an RSS feed for individual artists and tours
* You can now add new artists, venues, and tours while entering a new show
* The title of newly-created related posts can now be customized using %placeholders% for your show data
* Newly-created related posts can optionally be future-published on the date of the show
* Changed the behaviour of tours, which are now grouped (with a heading) inline, within the chronological shows list
* As a result of the above, removed the "tour order" option on the Tour admin screen
* Changed the shortcode to `[gigpress_shows]` and added some new parameters - see docs for details (old shortcodes will still work!)
* Added new options to the widget
* Removed several and added a few options to the Settings screen
* All HTML output is now contained in modular templates, which can be customized without being overwritten during subsequent plugin updates - see the docs for the lowdown
* You can now import shows from a CSV file - see docs for specifications please
* CSV export has been improved, and is compatible with the new import routine
* The Age Restrictions field is now customizable
* You can now optionally display full country names instead of country codes
* Better error-checking for required fields, and better visual feedback
* You can no longer enter a date which doesn't exist (i.e. February 30th)
* Only administrators can see the GigPress Settings page now
* Added pagination to GigPress admin screens, and redesigned the Shows management screen
* Moved the GigPress plugin menu up between Comments and Appearance - it's better up there
* Rewrote most of the code, optimized queries, added `$wpdb->prepare` everywhere for improved security
* There's more I'm sure

= 1.4.9 =

* Added Belarusian localization (thanks to M.Comfi)
* Shows on the same day are now further ordered by show time
* Fixed venue information toggle under IE 7

= 1.4.8 =

* Added German (thanks to David Scott), Slovak (thanks to Igor Rjabinin) and Ukranian (thanks to Vladimir Agafonkin) localization
* Fixed a bug where cancelled shows were not appearing in the admin when not associated with a tour

= 1.4.7 =

* Fixed a missing closing tag on the related show list.
* Improved compatibility with child themes when loading a custom gigpress.css file.
* Expanded year list to 1900 through 2050.
* Updated country list to comply with the ISO 3166-1 list of countries, dependent territories, and special areas of geographical interest.
* Added Russian translation (thanks to Ravi).

= 1.4.6 =

* Fixed a bug which caused the "stickiness" of the fields on the entry screen to be delayed by one refresh.  Each new show entry now immediately loads the previous show's sticky fields (date, country, tour).
* Fixed RSS feed validation errors when using GigPress in languages other than English.
* Added the classes "upcoming" and "archive" to the respective shows tables.

= 1.4.5 =

* Fixed a bug which was preventing shows from displaying under WordPress 2.3.x.
* Fixed a bug where the SOLD OUT label was not displaying in the past shows listings.
* Added Dutch translation (thanks to Martin Teley)

= 1.4.4 =

* Fixed a typo in the database upgrade check that was leading to about 30 extra queries being performed on every page load throughout WordPress.  Oops?
* Added the missing "notes" field to the show listings on Related Post entries and in the RSS feed
* Added an "Add a show" link to the WordPress 2.7 favourites menu
* Added a new shortcode parameter "limit" that will display only a chosen number of shows (only works when *not* segmenting by tour, or when used in conjunction with displaying a specific tour using the "tour" shortcode parameter)
* Added Bulgarian (thanks to Ivo Minchev) and Danish (thanks to Michael Tysk-Andersen) translations

= 1.4.2 =

* Fixed a couple of bugs when using the `gigpress_upcoming()` and `gigpress_archive()` template tags - these functions now need to be echoed, e.g. `<?php echo gigpress_upcoming(); ?>`
* Removed vestigial hard-coded "Tour" label on Related Post entries
* Fixed minor character entity issue when loading/updating settings

= 1.4.1 =

* Fixed a bug where shortcodes were outputting before any other post content, regardless of where they appeared in the post

= 1.4 =

* Complete show info can now be displayed within a show's related post entry (before or after post content)
* Option to automatically create a new related post when entering a new show
* Date and City can now be optionally linked to related shows
* Shows can now be marked as CANCELLED or SOLD OUT
* Added fields for venue phone and box office
* Added ability to export all of your shows to a tab-separated CSV file
* You can now show a single tour using its ID in the gigpress_upcoming shortcode (see docs for more info)
* More language is now customizable through the Settings page
* Optional 24-hour clock display for show time on the entry screen
* Ability to enter archival shows back to 1960
* Added alternating class to sidebar listing
* Compatibility and styling updates for WordPress 2.7
* Dropped support for WordPress 2.2.3
* Probably some other stuff

= 1.3.4 =

* Fixed a bug that prevented language files from being loaded under WordPress 2.6
* Fixed an XHTML validation error in the upcoming/past shows table output

= 1.3.3 =

* Fixed a bug where past shows wouldn't appear in the admin in certain cases
* Revised and optimized the code that fetches recent posts for the "related post" drop-down when adding a show, as posts were being retrieved in the wrong order in some circumstances
* Added compatibility with WordPress 2.6's ability to relocate the wp-content directory

= 1.3.2 =

* Lowered the number of posts retrieved in the drop-down on the "Add a show" page to 100, as the previous 1000-post limit could cause PHP memory errors in some cases
* Fixed a bug where deleted shows were still appearing in the sidebar and RSS feed
* Added Basque, Hungarian and Norwegian translations

= 1.3.1 =

* Fixed a bug where the phrase "opens in a new window" that appears in the title attribute of certain links was not getting translated.
* Added German, Polish and Swedish translations

= 1.3 =

* New feature: associate each show with a post in WordPress
* New feature: copy any existing show to the "Add a show" screen for faster data entry
* Tours can now be reordered
* Added option to display tours before *or* after non-tour shows
* Added the option to *not* segment the tour listing into tours and individuals shows
* Added option to open Google maps, venue, and ticket-buy links in a new window
* The date, time, country and tour fields are now all "sticky," so their last-used values will be loaded into the "Add a show" form each time
* Added "undo" option immediately after deleting shows and tours
* Add visual cues for required fields on the "Add a show" screen
* GigPress will now look for a style sheet called gigpress.css in your current theme folder in order to load custom styles
* More styling fixes for visual compatibility with WordPress 2.5
* Dropped official support for Wordpress 2.1.3

= 1.2.7 =

* Added Spanish and French translation files
* Fixed a few text strings that weren't getting translated
* Add gettext() wrappers to month names, which will allow them to be translated by the core WordPress language file
* Removed some stray quotation marks in the welcome message
* Fixed a bug where under certain conditions the sidebar widget would not show the "no upcoming shows" message
* Added an extra span and class to the fields displayed in the "gigpress-info" cell of the shows table to allow for further styling flexibility
* The javascript used by GigPress in the WordPress admin will now *only* load on the GigPress "Add a show"  page to prevent potential conflict with other plugins' scripts
* Modified some of the markup and CSS in the admin area to better suit the forthcoming admin design in WordPress 2.5

= 1.2.6 =

* GigPress is now fully internationalized - language files for Italian, Hungarian, and Dutch included
* The 'show time' field can now be set to 'N/A' - if so it will not display
* Added option to choose your default country when adding new shows
* Fixed a bug where under certain configurations shows would move to the archive on the day of the show
* Changed default encoding of the database tables to UTF-8

= 1.2.2 =

* Fixed a bug where past shows would not display if there were no tours in the database
* Increased compatibility with certain configurations of MySQL 5

= 1.2.1 =

* The jQuery library used by GigPress was disabling the drag and drop on the WordPress widgets page.  GigPress now uses the jQuery version bundled with WordPress instead (but will load its own in WordPress versions prior to 2.2)

= 1.2 =

* Added a "time" field
* Added the ability to make shows span multiple days
* Added option to select a user-level required to use GigPress
* Add option to display a link to your upcoming shows page beneath the sidebar listing
* Fixed a bug where the sidebar listing wouldn't display any shows if the tour segmenting option was on, but there were no tours in the database
* The "Admittance" field will now not display if it's set to "Not sure"
* Fixed various issues in the countries list
* Display of the Country column can now be disabled
* Added element IDs to the header row of each tour in shows table (eg. #tour-2)
* Updated Options page to refelect new features
* Added a `<link>` element to each item in the RSS feed, linked to the page set on the options page

= 1.1.1 =

* Fixed a stray tag in the code that mangled the sidebar output when using it via the template tag with tour segmentation active

= 1.1 =

* Added RSS feed for upcoming shows
* Add option to split gigpress_sidebar output into tours
* Added filter on all output in the admin and in the template functions to strip slashes and encode HTML entities (oops!)

= 1.0 = 

* Initial release

== Installation ==

1. Upload the `gigpress` folder to the `/wp-content/plugins/` directory on your web server
2. Activate the plugin through the 'Plugins' admin menu in WordPress. This will create a new top-level menu called "GigPress".
3. To list upcoming shows, simply create a new page and put `[gigpress_shows]` in the page content. This shortcode accepts several parameters - [please refer to the documentation for details](http://gigpress.com/docs/).
4. GigPress also comes with a sidebar widget - simply drag the widget into your sidebar, set your options, and save.

== Frequently Asked Questions ==

Please check the [FAQ on the GigPress website](http://gigpress.com/faq/)

== For more info... ==

[Contribute on GitHub!](http://github.com/amphibian/gigpress)

[Please visit the GigPress website](http://gigpress.com/) for screenshots, full documentation, and the latest news about plugin updates, or to report bugs, suggest features, and the like.
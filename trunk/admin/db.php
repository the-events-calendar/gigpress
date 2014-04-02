<?php

// DB structure
$charset_collate = '';
if ( ! empty( $wpdb->charset ) )
	$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
if ( ! empty( $wpdb->collate ) )
	$charset_collate .= " COLLATE $wpdb->collate";

global $gp_db;
$gp_db = array();

// Note that the following columns are deprectated as of DB version 1.4,
// but cannot be dropped due to the neccessities of the upgrade process:
// show_address, show_locale, show_country, show_venue, show_venue_url, show_venue_phone
	
$gp_db[] = "CREATE TABLE " . GIGPRESS_SHOWS . " (
show_id INTEGER(4) AUTO_INCREMENT,
show_artist_id INTEGER(4) NOT NULL,
show_venue_id INTEGER(4) NOT NULL,
show_tour_id INTEGER(4) DEFAULT 0,
show_date DATE NOT NULL,
show_multi INTEGER(1),
show_time TIME NOT NULL,
show_expire DATE NOT NULL,
show_price VARCHAR(255),
show_tix_url VARCHAR(255),
show_tix_phone VARCHAR(255),
show_ages VARCHAR(255),
show_notes TEXT,
show_related BIGINT(20) DEFAULT 0,
show_status VARCHAR(32) DEFAULT 'active',
show_external_url VARCHAR(255),
show_tour_restore INTEGER(1) DEFAULT 0,
show_address VARCHAR(255),
show_locale VARCHAR(255),
show_country VARCHAR(2),
show_venue VARCHAR(255),
show_venue_url VARCHAR(255),
show_venue_phone VARCHAR(255),	
PRIMARY KEY  (show_id)
) $charset_collate";

$gp_db[] = "CREATE TABLE " . GIGPRESS_ARTISTS . " (
artist_id INTEGER(4) AUTO_INCREMENT,
artist_name VARCHAR(255) NOT NULL,
artist_alpha VARCHAR(255) NOT NULL,
artist_url VARCHAR(255),
artist_order INTEGER(4) DEFAULT 0,
PRIMARY KEY  (artist_id)
) $charset_collate";

$gp_db[] = "CREATE TABLE " . GIGPRESS_VENUES . " (
venue_id INTEGER(4) AUTO_INCREMENT,
venue_name VARCHAR(255) NOT NULL,
venue_address VARCHAR(255),
venue_city VARCHAR(255) NOT NULL,
venue_state VARCHAR(255),
venue_postal_code VARCHAR(32),
venue_country VARCHAR(2) NOT NULL,	
venue_url VARCHAR(255),
venue_phone VARCHAR(255),	
PRIMARY KEY  (venue_id)
) $charset_collate";

$gp_db[] = "CREATE TABLE " . GIGPRESS_TOURS . " (
tour_id INTEGER(4) AUTO_INCREMENT,
tour_name VARCHAR(255) NOT NULL,
tour_status VARCHAR(32) DEFAULT 'active',
PRIMARY KEY  (tour_id)
) $charset_collate";


// Default settings
global $default_settings;
$default_settings = array(
	'age_restrictions' => 'All Ages | All Ages/Licensed | No Minors',
	'alternate_clock' => 0,
	'artist_label' => 'Artist',		
	'artist_link' => 1,	
	'autocreate_post' => 0,
	'buy_tickets_label' => 'Buy Tickets',		
	'category_exclude' => 0,
	'country_view' => 'long',
	'date_format_long' => 'l, F jS Y',
	'date_format' => 'm/d/y',
	'db_version' => GIGPRESS_DB_VERSION,
	'default_country' => 'US',
	'default_date' => GIGPRESS_NOW,
	'default_time' => '00:00:01',
	'default_title' => '%artist% in %city% on %date%',
	'default_tour' => '',
	'disable_css' => 0,
	'disable_js' => 0,
	'display_subscriptions' => 1,
	'display_country' => 1,
	'external_link_label' => 'More information',
	'load_jquery' => 1,
	'nopast' => 'No shows in the archive yet.',
	'noupcoming' => 'No shows booked at the moment.',
	'output_schema_json' => 'y',
	'related_category' => 1,
	'related_heading' => 'Related show',
	'related_position' => 'after',
	'related' => 'Related post.',
	'related_date' => 'now',
	'relatedlink_city' => 0,
	'relatedlink_date' => 0,
	'relatedlink_notes' => 1,			
	'rss_head' => 1,
	'rss_limit' => 100,
	'rss_list' => 1,
	'rss_title' => 'Upcoming shows',
	'shows_page' => '',
	'sidebar_link' => 0,
	'target_blank' => 0,
	'time_format' => 'g:ia',
	'tour_label' => 'Tour',
	'user_level' => 'edit_posts',
	'welcome' => 'yes'
);

global $gpo;

if( ! $gpo = get_option('gigpress_settings') )
{
	$gpo = $default_settings;
}

if(empty($gpo['buy_tickets_label']))
{
	$gpo['buy_tickets_label'] = 'Buy Tickets';
	update_option('gigpress_settings', $gpo);
}

if(empty($gpo['output_schema_json']) || (!empty($gpo['output_schema_json']) && $gpo['output_schema_json'] == 1))
{
	$gpo['output_schema_json'] = 'y';
	update_option('gigpress_settings', $gpo);
}

function gigpress_install() {

	global $wpdb, $gp_db, $default_settings;
	
	if($wpdb->get_var("SHOW TABLES LIKE '" . GIGPRESS_SHOWS . "'") != GIGPRESS_SHOWS) {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($gp_db);
		add_option('gigpress_settings', $default_settings);
	}
}


// Upgrade checks and functions

if ( $gpo['db_version'] < GIGPRESS_DB_VERSION ) {
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');	
	dbDelta($gp_db);
	
	switch($gpo['db_version']) {
		case "1.0":
			gigpress_db_upgrade_110();
			gigpress_db_upgrade_120();
			gigpress_db_upgrade_130();
			gigpress_db_upgrade_140();
			gigpress_db_upgrade_160();
			break;		
		case "1.1":
			gigpress_db_upgrade_120();
			gigpress_db_upgrade_130();
			gigpress_db_upgrade_140();
			gigpress_db_upgrade_160();
			break;
		case "1.2":
			gigpress_db_upgrade_130();
			gigpress_db_upgrade_140();
			gigpress_db_upgrade_160();
			break;
		case "1.3":
			gigpress_db_upgrade_140();
			gigpress_db_upgrade_160();
			break;
		case "1.4":
			gigpress_db_upgrade_160();
			break;
		case "1.5":
			gigpress_db_upgrade_160();
			break;
	}
	
	$gpo['db_version'] = GIGPRESS_DB_VERSION;
	update_option('gigpress_settings', $gpo);

}


function gigpress_db_upgrade_110() {

	global $wpdb;

	// We need to make sure the current show_dates in the DB are cloned to the show_expire fields
	// Get all shows where the show_multi is NULL
	$getshows = $wpdb->get_results("
		SELECT * FROM " . GIGPRESS_SHOWS . " WHERE show_multi IS NULL
	");
	
	// Update each one's show_expire with its show_date
	if($getshows) {
		foreach($getshows as $show) {
			$wpdb->update(GIGPRESS_SHOWS, array('show_expire' => $show->show_date), array('show_id' => $show->show_id), array('%s'), array('%d'));	
		}
	};
	
	// Now set show_time to NA
	$settime = $wpdb->update(GIGPRESS_SHOWS, array('show_time' => '00:00:01'), array('show_time' => 
''));

}


function gigpress_db_upgrade_120() {

	global $wpdb;

	// Set status for all shows and tours
	$wpdb->update(GIGPRESS_SHOWS, array('show_status' => 'active'), array('show_status' => ''));	
	$wpdb->update(GIGPRESS_TOURS, array('tour_status' => 'active'), array('tour_status' => ''));	
	
}


function gigpress_db_upgrade_130() {
	
	global $gpo;
	$gpo['date_format_long'] = $gpo['date_format'];

}


function gigpress_db_upgrade_140() {

	global $wpdb, $gpo;

	// Add the first artist
	$artist_name = (!empty($gpo['band'])) ? strip_tags($gpo['band']) : get_bloginfo('name');
	$artist = array('artist_name' => $artist_name);
	$wpdb->insert(GIGPRESS_ARTISTS, $artist);
	
	$gpo['default_artist'] = $wpdb->insert_id;
	
	$wpdb->update(GIGPRESS_SHOWS, array('show_artist_id' => $wpdb->insert_id), array('show_artist_id' => 0));
		
	// Find all venues
	$venues = $wpdb->get_results("SELECT DISTINCT show_venue as venue_name, show_address as venue_address, show_locale as venue_city, show_country as venue_country, show_venue_phone as venue_phone, show_venue_url as venue_url FROM " . GIGPRESS_SHOWS . "", ARRAY_A);
	
	// Insert them into the database
	foreach($venues as $venue) {
		$wpdb->insert(GIGPRESS_VENUES, $venue);
		// Now re-associate the shows with their venues
		$where = array(
			"show_venue" => $venue['venue_name'],
			"show_locale" => $venue['venue_city'],
			"show_country" => $venue['venue_country']
		);
		$values = array("show_venue_id" => $wpdb->insert_id);
		$wpdb->update(GIGPRESS_SHOWS, $values, $where);	
	}
	
	$gpo['age_restrictions'] = 'All Ages | All Ages/Licensed | No Minors';
	$gpo['artist_label'] = 'Artist';				
	$gpo['country_view'] = 'short';
	$gpo['default_title'] = '%artist% at %venue% on %date%';
	$gpo['display_subscriptions'] = 1;
	$gpo['load_jquery'] = 1;
	$gpo['related_date'] = 'now';
	$gpo['widget_feeds'] = 1;
	$gpo['widget_group_by_artist'] = 0;
	
}

function gigpress_db_upgrade_160() {
	
	global $wpdb, $gpo;
	$gpo['artist_link'] = 1;
	$gpo['external_link_label'] = 'More information';
	
	// Add alpha values for all existing artists
	$artists = $wpdb->get_results(
		"SELECT * FROM " . GIGPRESS_ARTISTS
	);
	if($artists)
	{
		foreach($artists as $artist)
		{
			$alpha = preg_replace("/^the /uix", "", strtolower($artist->artist_name));
			$new_artist = array(
				'artist_alpha' => $alpha
			);
			$where = array('artist_id' => $artist->artist_id);
			$update = $wpdb->update(GIGPRESS_ARTISTS, $new_artist, $where, array('%s'), array('%d'));
		}
	}

	// Try our darndest to extract states from cities and put them in their own column
	$venues = $wpdb->get_results(
		"SELECT * FROM " . GIGPRESS_VENUES
	);
	if($venues)
	{
		foreach($venues as $venue)
		{
			preg_match("/,[ ]?([A-Z]{2})$/u", $venue->venue_city, $matches);
			if(is_array($matches))
			{
				$new_venue['venue_state'] = $matches[1];
				$new_venue['venue_city'] = preg_replace("/,[ ]?[A-Z]{2}$/u", '', $venue->venue_city);
				$where = array('venue_id' => $venue->venue_id);
				$update = $wpdb->update(GIGPRESS_VENUES, $new_venue, $where, array('%s', '%s'), array('%d'));
			}
		}
	}

}

function gigpress_uninstall() {

	delete_option('gigpress_settings');

	global $wpdb;	
	$wpdb->query('DROP TABLE IF EXISTS . '
	 . GIGPRESS_SHOWS . ', '
	 . GIGPRESS_TOURS . ', '
	 . GIGPRESS_VENUES . ', '
	 . GIGPRESS_ARTISTS);

}
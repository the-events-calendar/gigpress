<?php

// These two functions are for backwards-compatibility the shortcodes used in GigPress < 2.0
function gigpress_upcoming($filter = null, $content = null) {
	if(!is_array($filter)) $filter = array();
	$filter['scope'] = 'upcoming';
	return gigpress_shows($filter, $content);
}
function gigpress_archive($filter = null, $content = null) {
	if(!is_array($filter)) $filter = array();
	$filter['scope'] = 'past';
	return gigpress_shows($filter, $content);
}


function gigpress_shows($filter = null, $content = null) {
	
	global $wpdb, $gpo;
	$further_where = $limit = '';
	
	extract(shortcode_atts(array(
			'tour' => FALSE,
			'artist' => FALSE,
			'venue' => FALSE,
			'limit' => FALSE,
			'scope' => 'upcoming',
			'sort' => FALSE,
			'group_artists' => 'yes',
			'artist_order' => 'custom',
			'show_menu' => FALSE,
			'show_menu_count' => FALSE,
			'menu_sort' => FALSE,
			'menu_title' => FALSE,
			'year' => FALSE,
			'month' => FALSE
		), $filter)
	);
	
	$total_artists = $wpdb->get_var("SELECT count(*) from " . GIGPRESS_ARTISTS);
	
	// Date conditionals and sorting based on scope
	switch($scope) {
		case 'upcoming':
			$date_condition = "show_expire >= '" . GIGPRESS_NOW . "'";
			if(empty($sort)) $sort = 'asc';
			break;
		case 'past':
			$date_condition = "show_expire < '" . GIGPRESS_NOW . "'";
			if(empty($sort)) $sort = 'desc';
			break;
		case 'today':
			$date_condition = "show_expire >= '".GIGPRESS_NOW."' AND show_date <= '".GIGPRESS_NOW."'";
			if(empty($sort)) $sort = 'asc';
			break;
		case 'all':
			$date_condition = "show_expire != ''";
			if(empty($sort)) $sort = 'desc';
			break;
	}
	
	// Artist, tour and venue filtering
	if($artist) $further_where .= ' AND show_artist_id = ' . $wpdb->prepare('%d', $artist);
	if($tour) $further_where .= ' AND show_tour_id = ' . $wpdb->prepare('%d', $tour);
	if($venue) $further_where .= ' AND show_venue_id = ' . $wpdb->prepare('%d', $venue);
	
	// Date filtering
	
	// Query vars take precedence over function vars
	if(isset($_REQUEST['gpy'])) { 
		$year = $_REQUEST['gpy'];
	
		if(isset($_REQUEST['gpm'])) {
			$month = $_REQUEST['gpm'];
		} else {
			unset($month);
		}
		$no_limit = TRUE;
	}
	
	
	// Validate year and date parameters
	if($year || $month) {
	
		if($year) {
			if(is_numeric($year) && strlen($year) == 4) {
				$year = round($year);
			} else {
				$year = date('Y', current_time('timestamp'));
			}
		} else {
			// We've only specified a month, so we'll assume the year is current
			$year = date('Y', current_time('timestamp'));
		}
		
		if($month) {
			if($month == 'current') {
				$month = date('m', current_time('timestamp'));
			} elseif(round($month) == 0) {
				// Probably using a month name
				$month = date('m', strtotime($month));
			} elseif(round($month) < 10) {
				// Make sure the month is padded through 09
				$month = str_pad($month, 2, 0, STR_PAD_LEFT);
			} elseif(round($month) < 13) {
				// Between 10 and 12 we're OK
				$month = $month;
			} else {
				// Bogus month value (not a string and > 12)
				// Sorry, bailing out. Your "month" will be ignored. Dink.
				$month = FALSE;
			}
			$start_month = $end_month = $month;
		}
		
		if(!$month) {
			$start_month = '01';
			$end_month = '12';
		}
		
		$start = $year.'-'.$start_month.'-01';
		$end = $year.'-'.$end_month.'-31';
		$further_where .= ' AND show_date BETWEEN '.$wpdb->prepare('%s', $start).' AND '.$wpdb->prepare('%s', $end);
	}

	
	$limit = ($limit && !isset($no_limit)) ? ' LIMIT ' . $wpdb->prepare('%d', $limit) : '';
	$artist_order = ($artist_order == 'custom') ?  "artist_order ASC," : '';
	
	// With the new 'all' scope, we should probably have a third message option, but I'm too lazy
	// Really, there should just be one generic 'no shows' message. Oh well.
	$no_results_message = ($scope == 'upcoming') ? wptexturize($gpo['noupcoming']) : wptexturize($gpo['nopast']);
	
	ob_start();
	
	// Are we showing our menu?
	if($show_menu) {
		$menu_options = array();
		$menu_options['scope'] = $scope;
		$menu_options['type'] = $show_menu;
		if($menu_title) $menu_options['title'] = $menu_title;
		if($show_menu_count) $menu_options['show_count'] = $show_menu_count;
		if($menu_sort) $menu_options['sort'] = $menu_sort;
		if($artist) $menu_options['artist'] = $artist;
		if($tour) $menu_options['tour'] = $tour;
		if($venue) $menu_options['venue'] = $venue;

		include gigpress_template('before-menu');
		echo gigpress_menu($menu_options);
		include gigpress_template('after-menu');
	}
	
	// If we're grouping by artist, we'll unfortunately have to first get all artists
	// Then  make a query for each one. Looking for a better way to do this.
	
	if($group_artists == 'yes' && !$artist && $total_artists > 1) { 
		
		$artists = $wpdb->get_results("SELECT * FROM " . GIGPRESS_ARTISTS . " ORDER BY " . $artist_order . "artist_name ASC");
		
		foreach($artists as $artist_group) {
			$shows = $wpdb->get_results("SELECT * FROM " . GIGPRESS_ARTISTS . " AS a, " . GIGPRESS_VENUES . " as v, " . GIGPRESS_SHOWS ." AS s LEFT JOIN  " . GIGPRESS_TOURS . " AS t ON s.show_tour_id = t.tour_id WHERE " . $date_condition . " AND show_status != 'deleted' AND s.show_artist_id = " . $artist_group->artist_id . " AND s.show_artist_id = a.artist_id AND s.show_venue_id = v.venue_id " . $further_where . " ORDER BY s.show_date " . $sort . ",s.show_expire " . $sort . ",s.show_time ". $sort . $limit);
			
			if($shows) {
				// For each artist group
				
				$some_results = TRUE;
				$current_tour = '';
				$i = 0;
				$showdata = array(
					'artist' => (!empty($artist_group->artist_url) && isset($gpo['artist_link']) && $gpo['artist_link'] == 1) ? '<a href="' . esc_url($artist_group->artist_url) . '"' . gigpress_target($artist_group->artist_url) . '>' . wptexturize($artist_group->artist_name) . '</a>' : wptexturize($artist_group->artist_name),
					'artist_plain' => wptexturize($artist_group->artist_name),
					'artist_id' => $artist_group->artist_id,
					'artist_url' => (!empty($artist_group->artist_url)) ? esc_url($artist_group->artist_url) : '',
				);
			
				include gigpress_template('shows-artist-heading');
				include gigpress_template('shows-list-start');
											
				foreach($shows as $show) {
				
					// For each individual show
					
					$showdata = gigpress_prepare($show, 'public');
					
					if($showdata['tour'] && $showdata['tour'] != $current_tour && !$tour) {
						$current_tour = $showdata['tour'];
						include gigpress_template('shows-tour-heading');
					}
					
					$class = $showdata['status'];
					++ $i; $class .= ($i % 2) ? '' : ' gigpress-alt';
					if(!$showdata['tour'] && $current_tour) {
						$current_tour = '';
						$class .= ' gigpress-divider';
					}
					$class .= ($showdata['tour'] && !$tour) ? ' gigpress-tour' : '';
					
					include gigpress_template('shows-list');

				}
				
				include gigpress_template('shows-list-end');						
			}
		}
		
		if($some_results) {
		// After all artist groups
			include gigpress_template('shows-list-footer');
		} else {	
			// No shows from any artist
			include gigpress_template('shows-list-empty');
		}
		
	} else {

		// Not grouping by artists

		$shows = $wpdb->get_results("
			SELECT * FROM " . GIGPRESS_ARTISTS . " AS a, " . GIGPRESS_VENUES . " as v, " . GIGPRESS_SHOWS ." AS s LEFT JOIN  " . GIGPRESS_TOURS . " AS t ON s.show_tour_id = t.tour_id WHERE " . $date_condition . " AND show_status != 'deleted' AND s.show_artist_id = a.artist_id AND s.show_venue_id = v.venue_id " . $further_where . " ORDER BY s.show_date " . $sort . ",s.show_expire " . $sort . ",s.show_time " . $sort . $limit);
				
		if($shows) {
		
			$current_tour = '';
			$i = 0;

			include gigpress_template('shows-list-start');
			
			foreach($shows as $show) {
			
				// For each individual show
				$showdata = gigpress_prepare($show, 'public');
				
				if($showdata['tour'] && $showdata['tour'] != $current_tour && !$tour) {
					$current_tour = $showdata['tour'];
					include gigpress_template('shows-tour-heading');
				}
				
				$class = $showdata['status'];
				++ $i; $class .= ($i % 2) ? '' : ' gigpress-alt';
				if(!$showdata['tour'] && $current_tour) {
					$current_tour = '';
					$class .= ' gigpress-divider';
				}
				$class .= ($showdata['tour'] && !$tour) ? ' gigpress-tour' : '';
				
				include gigpress_template('shows-list');
			}
			
			include gigpress_template('shows-list-end');
			include gigpress_template('shows-list-footer');			
			
		} else {
			// No shows to display
			include gigpress_template('shows-list-empty');
		}	

	}
	
	echo('<!-- Generated by GigPress ' . GIGPRESS_VERSION . ' -->
	');
	return ob_get_clean();	
}


function gigpress_menu($options = null) {
	
	global $wpdb, $wp_locale, $gpo;	

	extract(shortcode_atts(array(
		'type' => 'monthly',
		'base' => get_permalink(),
		'scope' => 'upcoming',
		'title' => FALSE,
		'id' => 'gigpress_menu',
		'show_count' => FALSE,
		'artist' => FALSE,
		'tour' => FALSE,
		'venue' => FALSE,
		'sort' => 'desc'
	), $options));
	
	$base .= (strpos($base, '?') === FALSE) ? '?' : '&amp;';
		
	// Date conditionals based on scope
	switch($scope) {
		case 'upcoming':
			$date_condition = ">= '" . GIGPRESS_NOW . "'";
			break;
		case 'past':
			$date_condition = "< '" . GIGPRESS_NOW . "'";
			break;
		case 'all':
			$date_condition = "!= ''";
	}
	
	$further_where = '';
	
	// Artist, tour and venue filtering
	if($artist) $further_where .= ' AND show_artist_id = ' . $wpdb->prepare('%d', $artist);
	if($tour) $further_where .= ' AND show_tour_id = ' . $wpdb->prepare('%d', $tour);
	if($venue) $further_where .= ' AND show_venue_id = ' . $wpdb->prepare('%d', $venue);	
	
	// Variable operajigamarations based on monthly vs. yearly
	switch($type) {
		case 'monthly':
			$sql_select_extra = 'MONTH(show_date) AS month, ';
			$sql_group_extra = ', MONTH(show_date)';
			$title = ($title) ? wptexturize(strip_tags($title)) : __('Select Month');
			$current = (isset($_REQUEST['gpy']) && isset($_REQUEST['gpm'])) ? $_REQUEST['gpy'].$_REQUEST['gpm'] : '';
			break;
		case 'yearly':
			$sql_select_extra = $sql_group_extra = '';
			$title = ($title) ? wptexturize(strip_tags($title)) : __('Select Year');
			$current = (isset($_REQUEST['gpy'])) ? $_REQUEST['gpy'] : '';
	}
	
	// Build query
	$dates = $wpdb->get_results("
		SELECT YEAR(show_date) AS year, " . $sql_select_extra . " count(show_id) as shows 
		FROM ".GIGPRESS_SHOWS." 
		WHERE show_status != 'deleted' 
		AND show_date " . $date_condition . $further_where . " 
		GROUP BY YEAR(show_date)" . $sql_group_extra . " 
		ORDER BY show_date " . $sort);
	
	ob_start();
	
	if($dates) : ?>
			
			<select name="gigpress_menu" class="gigpress_menu" id="<?php echo $id; ?>">
				<option value="<?php echo $base; ?>"><?php echo $title; ?></option>
			<?php foreach($dates as $date) : ?>
				<?php $this_date = ($type == 'monthly') ? $date->year.$date->month : $date->year; ?>
				<option value="<?php echo $base.'gpy='.$date->year; if($type == 'monthly') echo '&amp;gpm='.$date->month; ?>"<?php if($this_date == $current) : ?> selected="selected"<?php endif; ?>>
					<?php if($type == 'monthly') echo $wp_locale->get_month($date->month).' '; echo $date->year; ?> 
					<?php if($show_count && $show_count == 'yes') : ?>(<?php echo $date->shows; ?>)<?php endif; ?>
				</option>
			<?php endforeach; ?>
			</select>
	
	<?php endif;
	
	return ob_get_clean();
}


function gigpress_has_upcoming($filter = null)
{
	global $wpdb;
	$further_where = '';
	extract(shortcode_atts(array(
			'tour' => FALSE,
			'artist' => FALSE,
			'venue' => FALSE
		), $filter)
	);
	
	// Artist, tour and venue filtering
	if($artist) $further_where .= ' AND show_artist_id = ' . $wpdb->prepare('%d', $artist);
	if($tour) $further_where .= ' AND show_tour_id = ' . $wpdb->prepare('%d', $tour);
	if($venue) $further_where .= ' AND show_venue_id = ' . $wpdb->prepare('%d', $venue);

	$shows = $wpdb->get_results("
			SELECT show_id 
			FROM " . GIGPRESS_SHOWS ." 
			WHERE show_expire >= '" . GIGPRESS_NOW . "' 
			AND show_status != 'deleted'" . $further_where . " 
			LIMIT 1
		");
	if($shows) return true;
	
	
}
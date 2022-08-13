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
	$and_where = $limit = '';
	$program_name = '';		//	artist == program, ?var for pastPerfs.html
	
	extract(shortcode_atts(array(
			'tour' => FALSE,
			'artist' => FALSE,
			'program_id' => FALSE,
			'venue' => FALSE,
			'limit' => '',
			'scope' => 'upcoming',
			'sort' => FALSE,
			'group_artists' => 'no',
			'artist_order' => 'alpha',
			'condensed' => 0,		// >0=hide gig-notes, >1= hide program descr.div also
			'show_menu' => FALSE,
			'show_menu_count' => FALSE,
			'menu_sort' => FALSE,
			'menu_title' => FALSE,
			'year' => FALSE,
			'month' => FALSE
		), $filter)
	);
		
	// Query vars take precedence over function vars
	if(isset($_REQUEST['condensed']))
		$condensed = $_REQUEST['condensed'];
	$condensed = 0 + $condensed;

	if(isset($_REQUEST['scope']))
		$scope = $_REQUEST['scope'];
		
	// Date conditionals and sorting based on scope
	switch($scope) 
	{
		case 'upcoming':
			$date_condition = "show_expire >= '" . GIGPRESS_NOW . "'";
			if(empty($sort)) $sort = 'asc';
			break;
		case 'past':
			$date_condition = "show_expire <  '" . GIGPRESS_NOW . "'";
			if(empty($sort)) $sort = 'desc';
			break;
		case 'today':
			$date_condition = "show_expire >= '" . GIGPRESS_NOW 
						. "' AND show_date <= '" . GIGPRESS_NOW . "'";
			if(empty($sort)) $sort = 'asc';
			break;
		case 'all':
		default:
			$date_condition = "show_expire != ''";
			if(empty($sort)) $sort = 'desc';
	}
	
	// Query vars take precedence over function vars
	if($artist)
		$program_id = $artist;
	if(isset($_REQUEST['artist_id']))
		$program_id = $_REQUEST['artist_id'];
	if(isset($_REQUEST['program_id']))
		$program_id = $_REQUEST['program_id'];
		
	$total_programs = ( $program_id
					   ? 1
					   : $wpdb->get_var("SELECT count(*) from " . GIGPRESS_ARTISTS));
		
	$and_atv_conditions = '';
	// program, tour and venue filtering
	if($program_id) $and_atv_conditions .= ' AND show_artist_id = ' . $wpdb->prepare('%d', $program_id);
	if($tour)   $and_atv_conditions .= ' AND show_tour_id = '   . $wpdb->prepare('%d', $tour);
	if($venue)  $and_atv_conditions .= ' AND show_venue_id = '  . $wpdb->prepare('%d', $venue);
	
	// Date filtering
	// Query vars take precedence over function vars
	if(isset($_REQUEST['year']))
		$_REQUEST['gpy'] = $_REQUEST['year'];
	if(isset($_REQUEST['month']))
		$_REQUEST['gpm'] = $_REQUEST['month'];
		
	if(isset($_REQUEST['gpy']))
	{ 
		$year = $_REQUEST['gpy'];
		$limit = '';
		unset($month);
	}
	if(isset($_REQUEST['gpm']))
	{ 
		$month = $_REQUEST['gpm'];
		$limit = '';
	}
	// Validate year and date parameters
	if($year || $month) {
	
		$dateRange = ' of this year';
		$thisYear = date('Y', current_time('timestamp'));
		if($year) 
		{
			if(is_numeric($year) && strlen($year) == 4) 
			{
				if( $year != $thisYear)
					$dateRange = ' in ' . $year;		
			}
			else 
				$year = $thisYear;
		} 
		else	// We've only specified a month, so we'll assume the year is current
			$year = $thisYear;

		$thisMonth = date('m', current_time('timestamp'));
		if($month) {
			if($month == 'current')
				$month = $thisMonth;
			elseif(round($month) == 0) 		// Probably using a month name
				$month = date('m', strtotime($month));
			elseif(round($month) < 13)		// Make sure the month is padded through 09
				$month = str_pad($month, 2, 0, STR_PAD_LEFT);
			else							// Bogus month value (not a string and > 12)
				$month = FALSE;
			$end_month = $month;
		}
		if(!$month) 
		{
			$month     = '01';
			$end_month = '12';
		}
		$start = $year.'-'.$month.'-01';
		$end   = date("Y-m-t", strtotime($year.'-'.$end_month.'-01'));
		$date_condition = 'show_date BETWEEN ' . $wpdb->prepare('%s', $start)
					 	 . ' AND ' . $wpdb->prepare('%s', $end);
		if($month == $end_month)
			$dateRange = (($month == $thisMonth) and  ($year == $thisYear))
							? ' this month'
							: ' in ' . date('F', strtotime($end)) . ' of ' . $year;
		$scope = 'dateRange';
	}

	$limit = (!empty($limit)) ? ' LIMIT ' . $wpdb->prepare('%d', $limit) : '';
	$artist_order = ($artist_order == 'custom') ?  "artist_order ASC," : '';
	
	ob_start();
	
	// Are we showing our menu?
	if($show_menu) {
		$menu_options = array();
		$menu_options['scope'] = $scope;
		$menu_options['type'] = $show_menu;
		if($menu_title) $menu_options['title'] = $menu_title;
		if($show_menu_count) $menu_options['show_count'] = $show_menu_count;
		if($menu_sort) $menu_options['sort'] = $menu_sort;
		if($program_id) $menu_options['artist'] = $program_id;
		if($tour) $menu_options['tour'] = $tour;
		if($venue) $menu_options['venue'] = $venue;

		include gigpress_template('before-menu');
		echo gigpress_menu($menu_options);
		include gigpress_template('after-menu');
	}
	
	$shows_markup = array();
	
	// If we're grouping by program, we'll unfortunately have to first get all programs
	// Then  make a query for each one. Looking for a better way to do this.
	// try sql group by program_id
	
	if($group_artists == 'yes' && !$program_id && $total_programs > 1) { 
		
		$programs = $wpdb->get_results("SELECT * FROM " . GIGPRESS_ARTISTS
										 . " ORDER BY " . $artist_order . " artist_alpha ASC");
		
		foreach($programs as $program_group) {
			$shows = $wpdb->get_results("SELECT * FROM " . GIGPRESS_ARTISTS . " AS a, " . GIGPRESS_VENUES . " as v, "
			 . GIGPRESS_SHOWS . " AS s LEFT JOIN  " . GIGPRESS_TOURS . " AS t ON s.show_tour_id = t.tour_id WHERE "
			  . $date_condition . $and_atv_conditions . " AND s.show_artist_id = " . $program_group->artist_id 
			   . " AND s.show_artist_id = a.artist_id AND s.show_venue_id = v.venue_id AND show_status != 'deleted'"
			    . " ORDER BY s.show_date " . $sort . ",s.show_expire " . $sort . ",s.show_time ". $sort
			     . $limit);
			
			if($shows) {
				// For each program group
				
				$some_results = TRUE;
				$current_tour = '';
				$i = 0;
				$wptexturized_program_name = wptexturize($program_group->artist_name);
				$showdata = array(
					'artist' => (!empty($program_group->artist_url) && !empty($gpo['artist_link'])
								? '<a href="' . esc_url($program_group->artist_url)
								 . '"' . gigpress_target($program_group->artist_url)
								  . '>' . $wptexturized_program_name . '</a>' 
								: $wptexturized_program_name),
					'artist_plain' => $wptexturized_program_name,
					'artist_id' => $program_group->artist_id,
					'program_notes' => (!empty($program_group-> program_notes)) ? $program_group->program_notes : '',
					'artist_url' => (!empty($program_group->artist_url)) ? esc_url($program_group->artist_url) : '',
				);

				include gigpress_template('shows-artist-heading');
				include gigpress_template('shows-list-start');
											
				foreach($shows as $show) // For each individual show
				{
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
					
					if($gpo['output_schema_json'] == 'y')
					{
						$show_markup = gigpress_json_ld($showdata);
						array_push($shows_markup,$show_markup);
					}
				}
				
				include gigpress_template('shows-list-end');						
			}
		}

		if ( $some_results ) {
			// After all artist groups
			include gigpress_template( 'shows-list-footer' );
			if ( ! empty( $shows_markup ) ) {
				echo '<script type="application/ld+json">';
				if ( ! defined( "JSON_UNESCAPED_SLASHES" ) ) {
					require_once( GIGPRESS_PLUGIN_DIR . 'lib/upgrade.php' );
					echo up_json_encode( $shows_markup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
				} else {
					echo json_encode( $shows_markup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
				}
				echo '</script>';
			}			
		} else {	
			// No shows from any program
			include gigpress_template('shows-list-empty');
		}
		
	} 
	else // Not grouping by programs or showing program or only 1 program
	{
		$shows = $wpdb->get_results("
			SELECT * FROM " . GIGPRESS_ARTISTS . " AS a, " . GIGPRESS_VENUES . " as v, "
			 . GIGPRESS_SHOWS ." AS s LEFT JOIN  " . GIGPRESS_TOURS 
			 . " AS t ON s.show_tour_id = t.tour_id WHERE " . $date_condition . $and_atv_conditions
			 . " AND show_status != 'deleted' AND s.show_artist_id = a.artist_id AND s.show_venue_id = v.venue_id "  
			 . " ORDER BY s.show_date " . $sort . ",s.show_expire " . $sort . ",s.show_time " . $sort
			 . $limit);
				
		if($shows) 
		{
			$current_tour = '';
			$i = 0;
			include gigpress_template('shows-list-start');
			
			foreach($shows as $show)
			{
				// For each individual show
				$showdata = gigpress_prepare($show, 'public');
				if($program_id AND ! $i )
				{
	 				$program_name = "?title=" . urlencode($showdata['artist_plain']); // for pastPerfs.html

				    echo "<h1 class='progtitle' id=prog-" . $showdata['artist_id'] . ">"
			                . "<a href=/the-programs/?program_id=" . $showdata['artist_id']
			                	 . "/#program-" . $showdata['artist_id'] . ">"
			                 . bc_bankhead($showdata['artist_plain'])
			                . "</a></h1>";
					if(!empty($showdata['program_notes'])) : ?>
						<div class="info-left prog-note" 
							 <?php echo ( 1 < $condensed ?  "style='display:none;'" : ""); ?>
						     id="prognote-<?php echo $showdata['artist_id']; ?>"> <!-- start prog-note gps -->
							<?php echo $showdata['program_notes']; ?>
						</div>
						<div class="prog-note-toggle"
					 		 style="display:<?php echo ( 1 < $condensed ?  "inline-block" : "none" ); ?>;">
						&nbsp;&nbsp;<a title='click to show/hide program description'
									   href="#prog-<?php echo $showdata['artist_id']; ?>"
									   onclick="return showInfo('prognote-<?php echo $showdata['artist_id']; ?>')">
							 program description</a>
							 <br>
						</div>
	    				<?php if(!empty($showdata['artist_url'])) :
	    	  				echo '<a class="info-right" href="'.$showdata['artist_url'].'" >read more...</a>';
	     				endif; ?>
					<?php endif; 
				}

				if($showdata['tour'] && $showdata['tour'] != $current_tour && !$tour) 
				{
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
				
				if($gpo['output_schema_json'] == 'y')
				{
					$show_markup = gigpress_json_ld($showdata);
					array_push($shows_markup,$show_markup);
				}
			}
			
			include gigpress_template('shows-list-end');
			include gigpress_template('shows-list-footer');

			if(!empty($shows_markup))
			{
				echo '<script type="application/ld+json">';
				if ( ! defined( "JSON_UNESCAPED_SLASHES" ) ) {
					require_once( GIGPRESS_PLUGIN_DIR . 'lib/upgrade.php' );
					echo up_json_encode( $shows_markup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
				} else {
					echo json_encode( $shows_markup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
				}
				echo '</script>';
			}
			
		} else {
			// No shows to display
			include gigpress_template('shows-list-empty');
		}	

	}
	if ($scope == 'past')
	{
	 	 // program_name set above or in shows-list-empty
	 	 echo "<a href='/pastPerfs.html". $program_name 
	 	 		."'><h2>Search Older Shows...</h2></a>";
	}

	echo('<!-- Generated by GigPress ' . GIGPRESS_VERSION . ' -->
	');
	return ob_get_clean() . $content;	
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
		'program_id' => FALSE,
		'tour' => FALSE,
		'venue' => FALSE,
		'sort' => 'desc'
	), $options));
	
	$base .= (strpos($base, '?') === FALSE) ? '?' : '&amp;';
		
	// Date conditionals based on scope
	switch($scope) {
		case 'upcoming':
			$date_condition = "show_date >= '" . GIGPRESS_NOW . "'";
			break;
		case 'past':
			$date_condition = "show_date < '" . GIGPRESS_NOW . "'";
			break;
		case 'today':
			$date_condition = "show_expire >= '" . GIGPRESS_NOW 
						. "' AND show_date <= '" . GIGPRESS_NOW . "'";
			break;
		case 'all':
		default:
			$date_condition = "show_date != ''";
	}

	// Query vars take precedence over function vars
	if($artist)
		$program_id = $artist;
	if(isset($_REQUEST['artist']))
		$program_id = $_REQUEST['artist'];
	if(isset($_REQUEST['program_id']))
		$program_id = $_REQUEST['program_id'];
		
	$and_atv_conditions = '';	// program, tour and venue filtering
	if($program_id) 
				$and_atv_conditions .= ' AND show_artist_id = ' . $wpdb->prepare('%d', $program_id);
	if($tour)   $and_atv_conditions .= ' AND show_tour_id = '   . $wpdb->prepare('%d', $tour);
	if($venue)  $and_atv_conditions .= ' AND show_venue_id = '  . $wpdb->prepare('%d', $venue);	
	
	// Variable operajigamarations based on monthly vs. yearly
	switch($type) {
		case 'monthly':
			$sql_select_extra = 'MONTH(show_date) AS month, ';
			$sql_group_extra = ', MONTH(show_date)';
			$title = ($title) ? wptexturize(strip_tags($title)) : __('Select Month', 'gigpress');
			$current = (isset($_REQUEST['gpy']) && isset($_REQUEST['gpm'])) ? $_REQUEST['gpy'].$_REQUEST['gpm'] : '';
			break;
		case 'yearly':
			$sql_select_extra = $sql_group_extra = '';
			$title = ($title) ? wptexturize(strip_tags($title)) : __('Select Year', 'gigpress');
			$current = (isset($_REQUEST['gpy'])) ? $_REQUEST['gpy'] : '';
	}
	
	// Build query
	$dates = $wpdb->get_results("
		SELECT YEAR(show_date) AS year, " . $sql_select_extra . " count(show_id) as shows 
				FROM " . GIGPRESS_SHOWS
				 . " WHERE show_status != 'deleted'"
				  . " AND " . $date_condition . $and_atv_conditions
				   . " GROUP BY YEAR(show_date)" . $sql_group_extra
				    . " ORDER BY show_date " . $sort);
	
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
	$and_where = '';
	extract(shortcode_atts(array(
			'tour' => FALSE,
			'artist' => FALSE,
			'program_id' => FALSE,
			'venue' => FALSE
		), $filter)
	);
	// Query vars take precedence over function vars
	if($artist)
		$program_id = $artist;
	if(isset($_REQUEST['artist']))
		$program_id = $_REQUEST['artist'];
	if(isset($_REQUEST['program_id']))
		$program_id = $_REQUEST['program_id'];
	
	// program, tour and venue filtering
	if($program_id) $and_where .= ' AND show_artist_id = ' . $wpdb->prepare('%d', $program_id);
	if($tour)       $and_where .= ' AND show_tour_id = '   . $wpdb->prepare('%d', $tour);
	if($venue)      $and_where .= ' AND show_venue_id = '  . $wpdb->prepare('%d', $venue);

	$shows = $wpdb->get_results("
			SELECT show_id 
			FROM " . GIGPRESS_SHOWS ." 
			WHERE show_expire >= '" . GIGPRESS_NOW . "' 
			AND show_status != 'deleted'" . $and_where . " 
			LIMIT 1
		");
	if($shows) return true;
	
	
}

function gigpress_json_ld($showdata)
{
	// Start array for single event
	$show_markup = array("@context" => "http://schema.org", "@type" => "Event");
	
	// Add show level attributes
	$show_markup['name'] = (!empty($showdata['tour'])) ? $showdata['tour'] : $showdata['artist_plain'];
	$show_markup['startDate'] = $showdata['iso_date'];
	if(!empty($showdata['related_url']))
	{
		$show_markup['url'] = $showdata['related_url'];
	}
	elseif(!empty($showdata['external_url']))
	{
		$show_markup['url'] = $showdata['external_url'];
	}
	if(!empty($showdata['iso_end_date']) && $showdata['iso_end_date'] != $showdata['iso_date']) { $show_markup['endDate'] = $showdata['iso_end_date']; }
	if(!empty($showdata['notes'])) { $show_markup['description'] = $showdata['notes']; }
	if(!empty($showdata['status']) && $showdata['status'] == "cancelled") { $show_markup['eventStatus'] = "EventCancelled"; }
	if(!empty($showdata['status']) && $showdata['status'] == "postponed") { $show_markup['eventStatus'] = "EventPostponed"; }
	if(!empty($showdata['admittance'])) { $show_markup['typicalAgeRange'] = $showdata['admittance']; }

	// Create performer
	$performer_markup = array("@type" => "Organization");
	
	// Add performer attributes
	$performer_markup['name'] = $showdata['artist_plain'];
	if(!empty($showdata['artist_url'])) { $performer_markup['url'] = $showdata['artist_url']; }
	
	// Merge performer into show
	$show_markup['performers'] = $performer_markup;

	// Create venue
	$location_markup = array("@type" => "Place");
	
	//Add venue attributes
	$location_markup['name'] = $showdata['venue_plain'];
	if(!empty($showdata['venue_url'])) { $location_markup['url'] = $showdata['venue_url']; }
	if(!empty($showdata['venue_phone'])) { $location_markup['telephone'] = $showdata['venue_phone']; }

	// Create venue address
	$address_markup = array("@type" => "PostalAddress");
	
	//Add address attributes
	if ( ! empty( $showdata['address_plain'] ) ) {
		$address_markup['streetAddress'] = $showdata['address_plain'];
	}
	$address_markup['addressLocality'] = $showdata['city_plain'];
	if ( ! empty( $showdata['state'] ) ) {
		$address_markup['addressRegion'] = $showdata['state'];
	}
	if ( ! empty( $showdata['postal_code'] ) ) {
		$address_markup['postalCode'] = $showdata['postal_code'];
	}
	if ( ! empty( $showdata['country'] ) ) {
		$address_markup['addressCountry'] = $showdata['country'];
	}

	// Merge address into venue
	$location_markup['address'] = $address_markup;

	// Merge venue into show
	$show_markup['location'] = $location_markup;

	// Create offer
	$offer_markup = array("@type" => "Offer");

	// Add offer attributes
	if ( ! empty( $showdata['price'] ) ) {
		// Filter out symbols like '$' per http://schema.org/PriceSpecification
		$offer_markup['price'] = preg_replace( '/[^0-9\.,]/', '', $showdata['price'] );
	}
	if ( ! empty( $showdata['ticket_url'] ) ) {
		$offer_markup['url'] = $showdata['ticket_url'];
	}
	if ( ! empty( $showdata['ticket_phone'] ) ) {
		$offer_markup['seller'] = array( "@type" => "Organization", "telephone" => $showdata['ticket_phone'] );
	}
	if ( ! empty( $showdata['status'] ) && $showdata['status'] == "soldout" ) {
		$offer_markup['availability'] = "SoldOut";
	}

	// Merge offer into show (if any fields were added)
	if(count($offer_markup) > 1) {
		$show_markup['offers'] = $offer_markup;
	}

	/**
	 * Provides an opportunity to customize and alter the JSON LD output for
	 * a specific show.
	 *
	 * @since 2.3.20
	 *
	 * @param array $show_markup
	 * @param array $showdata
	 */
	return apply_filters( 'gigpress_show_json_ld_markup', $show_markup, $showdata );
}

<?php

class Gigpress_widget extends WP_Widget
{

	function Gigpress_widget()
	{
		$widget_opts = array('description' => __("List upcoming GigPress shows", "gigpress") );
		$this->WP_Widget('gigpress', 'GigPress', $widget_opts);
	}
	
	function widget($args, $instance)
	{
		extract($args, EXTR_SKIP);

		echo $before_widget;
		if (!empty($instance['title'])) echo $before_title . $instance['title'] . $after_title;
		echo gigpress_sidebar($instance);
		echo $after_widget;
	}
	
   function update($new_instance, $old_instance)
   {
		$instance = array();
		$allowed = array(
			'title', 
			'limit',
			'scope',
			'show_tours', 
			'group_artists', 
			'artist_order',
			'artist', 
			'tour', 
			'venue',				
			'show_feeds', 
			'link_text'
		);
		foreach($new_instance as $option => $value)
		{
			if(in_array($option, $allowed))
			{
				if($option == 'limit' && (!is_numeric($value) || $value === 0))
				{
					$instance['limit'] = 5;
				}
				else
				{			
					$instance[$option] = gigpress_db_in($value);
				}
			}
		}
        return $instance;
    }
    
    function form($instance)
    {
		global $wpdb;
		
		$defaults = array(
			'title' => 'Upcoming shows', 
			'limit' => 5,
			'scope' => 'upcoming',
			'show_tours' => 'no',
			'group_artists' => 'no',
			'artist_order' => 'alphabetical', 
			'artist' => '', 
			'tour' => '', 
			'venue' => '', 
			'show_feeds' => 'no', 
			'link_text' => ''
		);			
		
		$instance = wp_parse_args($instance, $defaults);
		extract($instance);			
		
		?>
		
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?>: 
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</label>
		</p>
		
		<p>
			<select style="width:100%;" id="<?php echo $this->get_field_id('scope'); ?>" name="<?php echo $this->get_field_name('scope'); ?>">	
				<option value="upcoming"<?php if($scope == 'upcoming') echo ' selected="selected"'; ?>> 
					<?php _e('Display upcoming shows', 'gigpress'); ?>
				</option>
				<option value="today"<?php if($scope == 'today') echo ' selected="selected"'; ?>> 
					<?php _e("Display today's shows", 'gigpress'); ?>
				</option>
				<option value="past"<?php if($scope == 'past') echo ' selected="selected"'; ?>> 
					<?php _e("Display past shows", 'gigpress'); ?>
				</option>
			</select>
		</p>		

		<p>
			<label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Number of shows to list', 'gigpress'); ?>: 
				<input style="width: 25px; text-align: center;" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo $limit; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('artist'); ?>">
				<?php _e('Only display shows from this artist', 'gigpress'); ?>
			</label>
			<select style="width:100%;" id="<?php echo $this->get_field_id('artist'); ?>" name="<?php echo $this->get_field_name('artist'); ?>">
		  		<option value="">--</option>
		  	<?php
		  	$artists = fetch_gigpress_artists();
			if($artists != FALSE) :
			foreach($artists as $this_artist) : ?>
				<option value="<?php echo $this_artist->artist_id; ?>"<?php if($artist == $this_artist->artist_id) echo(' selected="selected"'); ?>><?php echo gigpress_db_out($this_artist->artist_name); ?></option>
			<?php endforeach; endif; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('tour'); ?>">
				<?php _e('Only display shows from this tour', 'gigpress'); ?>
			</label>
			<select style="width:100%;" id="<?php echo $this->get_field_id('tour'); ?>" name="<?php echo $this->get_field_name('tour'); ?>">
		  		<option value="">--</option>
		  	<?php
		  	$tours = fetch_gigpress_tours();
			if($tours != FALSE) :
			foreach($tours as $this_tour) : ?>
				<option value="<?php echo $this_tour->tour_id; ?>"<?php if($tour == $this_tour->tour_id) echo(' selected="selected"'); ?>><?php echo gigpress_db_out($this_tour->tour_name); ?></option>
			<?php endforeach; endif; ?>
			</select>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('venue'); ?>">
				<?php _e('Only display shows from this venue', 'gigpress'); ?>
			</label>
			<select style="width:100%;" id="<?php echo $this->get_field_id('venue'); ?>" name="<?php echo $this->get_field_name('venue'); ?>">
		  		<option value="">--</option>
		  	<?php
		  	$venues = fetch_gigpress_venues();
			if($venues != FALSE) :
			foreach($venues as $this_venue) : ?>
				<option value="<?php echo $this_venue->venue_id; ?>"<?php if($venue == $this_venue->venue_id) echo(' selected="selected"'); ?>><?php echo gigpress_db_out($this_venue->venue_name).' ('.gigpress_db_out($this_venue->venue_city); if(!empty($this_venue->venue_state)) echo ', '.gigpress_db_out($this_venue->venue_state); echo ')'; ?></option>
			<?php endforeach; endif; ?>
			</select>
		</p>			
			
		<p>
			<label>
				<input id="<?php echo $this->get_field_id('group_artists'); ?>" name="<?php echo $this->get_field_name('group_artists'); ?>" type="checkbox" value="yes"<?php if($group_artists == 'yes') echo ' checked="checked"'; ?> /> 
				<?php _e('Group by artist', 'gigpress'); ?><br />
				<small><?php _e('Ignored when filtering by artist, tour, or venue.', 'gigpress'); ?></small>
			</label>
		</p>
		
		<p>
			<select style="width:100%;" id="<?php echo $this->get_field_id('artist_order'); ?>" name="<?php echo $this->get_field_name('artist_order'); ?>">
				<option value="alphabetical"<?php if($artist_order == 'alphabetical') echo ' selected="selected"'; ?>>
					<?php _e("Order artists alphabetically", "gigpress"); ?>
				</option>
				<option value="custom"<?php if($artist_order == 'custom') echo ' selected="selected"'; ?>>
					<?php _e("Order artists by custom order", "gigpress"); ?>
				</option>
			</select><br />
			<small><?php _e('Ignored when not grouping by artist.', 'gigpress'); ?></small>
		</p>

		<p>
			<label>
				<input id="<?php echo $this->get_field_id('show_tours'); ?>" name="<?php echo $this->get_field_name('show_tours'); ?>" type="checkbox" value="yes"<?php if($show_tours == 'yes') echo ' checked="checked"'; ?> /> 
				<?php _e('Group by tour', 'gigpress'); ?>
			</label>
		</p>

		<p>
			<label>
				<input id="<?php echo $this->get_field_id('show_feeds'); ?>" name="<?php echo $this->get_field_name('show_feeds'); ?>" type="checkbox" value="yes"<?php if($show_feeds == 'yes') echo ' checked="checked"'; ?> /> 
				<?php _e('Show RSS and iCal feeds', 'gigpress'); ?>
			</label>
		</p>
										
		<p>
			<label for="<?php echo $this->get_field_id('link_text'); ?>"><?php _e('Link text'); ?>: 
				<input class="widefat" id="<?php echo $this->get_field_id('link_text'); ?>" name="<?php echo $this->get_field_name('link_text'); ?>" type="text" value="<?php echo $link_text; ?>" /><br />
				<small><?php _e('This phrase is used to link to the page specified in your GigPress settings. (Leave blank to disable this link.)', 'gigpress'); ?></small>
			</label>
		</p>
<?php }

}

// Register the widget
function gigpress_load_widgets() {
	register_widget('Gigpress_widget');
}


function gigpress_sidebar($filter = null) {

	global $wpdb, $gpo;
	$further_where = '';

	// Variables we need for conditionals
	
	// Check total number of artists
	$total_artists = $wpdb->get_var("SELECT count(*) from " . GIGPRESS_ARTISTS);
	
	// Check for sorting
	if(isset($filter['sort'])) $sort = $filter['sort'];
	
	// Scope
	switch($filter['scope']) {
		case 'today':
			$date_condition = "show_expire >= '".GIGPRESS_NOW."' AND show_date <= '".GIGPRESS_NOW."'";
			if(!isset($sort)) $sort = 'asc';
			break;
		case 'past':
			$date_condition = "show_expire < '".GIGPRESS_NOW."'";
			if(!isset($sort)) $sort = 'desc';
			break;
		case 'all':
			$date_condition = "show_date != ''";
			if(!isset($sort)) $sort = 'desc';
			break;
		default:
			$date_condition = "show_expire >= '".GIGPRESS_NOW."'";
			if(!isset($sort)) $sort = 'asc';
	}

	
	// Number of shows to list (per artist if grouping by artist)	
	$limit = (isset($filter['limit']) && is_numeric($filter['limit'])) ? $wpdb->prepare('%d', $filter['limit']) : 5;
	
	// Whether or not to display tour grouings
	$show_tours = (isset($filter['show_tours']) && $filter['show_tours'] == 'yes') ? 'yes' : FALSE;
	
	// Whether or not to group artists
	$group_artists = (isset($filter['group_artists']) && $filter['group_artists'] == 'yes') ? 'yes' : FALSE;
	
	// Order in which to display artists if grouping
	$artist_order = (isset($filter['artist_order']) && $filter['artist_order'] == 'custom') ? 'custom' : 'alphabetical';
	
	// Filtering by artist, tour, or venue?
	$artist = isset($filter['artist']) ? $filter['artist'] : FALSE;
	$tour = isset($filter['tour']) ? $filter['tour'] : FALSE;
	$venue = isset($filter['venue']) ? $filter['venue'] : FALSE;
	
	// Display feed links and link to more shows?
	$show_feeds = (isset($filter['show_feeds']) && $filter['show_feeds'] == 'yes') ? 'yes' : FALSE;
	$link = (isset($filter['link_text']) && !empty($gpo['shows_page'])) ? wptexturize($filter['link_text']) : FALSE;

	// Establish the variable parts of the query
	if($artist) $further_where .= ' AND show_artist_id IN(' . $wpdb->prepare('%s', $artist).')';
	if($tour) $further_where .= ' AND show_tour_id IN(' . $wpdb->prepare('%s', $tour).')';
	if($venue) $further_where .= ' AND show_venue_id IN(' . $wpdb->prepare('%s', $venue).')';
	$artist_order = ($artist_order == 'custom') ?  "artist_order ASC," : '';
		
	ob_start();
	
	// If we're grouping by artist, we'll unfortunately have to first get all artists
	// Then  make a query for each one. Looking for a better way to do this.
	
	if($group_artists && !$tour && !$artist && !$venue && $total_artists > 1) { 
		
		$artists = $wpdb->get_results("SELECT * FROM " . GIGPRESS_ARTISTS . " ORDER BY " . $artist_order . "artist_alpha ASC");
		
		foreach($artists as $artist_group) {
		
			$shows = $wpdb->get_results("SELECT * FROM " . GIGPRESS_ARTISTS . " AS a, " . GIGPRESS_VENUES . " as v, " . GIGPRESS_SHOWS ." AS s LEFT JOIN  " . GIGPRESS_TOURS . " AS t ON s.show_tour_id = t.tour_id WHERE " . $date_condition . " AND show_status != 'deleted' AND s.show_artist_id = " . $artist_group->artist_id . " AND s.show_artist_id = a.artist_id AND s.show_venue_id = v.venue_id " . $further_where . " ORDER BY s.show_date " . $sort . ",s.show_expire " . $sort . ",s.show_time " . $sort . " LIMIT " . $limit);
			
			if($shows) {
				// For each artist group
				
				$some_results = TRUE;
				$current_tour = '';
				$i = 0;
				
				// Data for artist heading
				$showdata = array(
					'artist' => wptexturize($artist_group->artist_name),
					'artist_id' => $artist_group->artist_id
				);
			
				include gigpress_template('sidebar-artist-heading');
				include gigpress_template('sidebar-list-start');
											
				foreach($shows as $show) {
				
					// For each individual show
					
					$showdata = gigpress_prepare($show, 'public');
					
					// Close the previous tour if needed
					if($show_tours && $current_tour && $showdata['tour'] != $current_tour) {
						include gigpress_template('sidebar-tour-end');					
					}
					
					// Open the current tour if needed
					if($show_tours && $showdata['tour'] && $showdata['tour'] != $current_tour && !$tour) {
						$current_tour = $showdata['tour'];
						include gigpress_template('sidebar-tour-heading');
					}
					
					// Zero-out $current_tour
					if(empty($showdata['tour'])) $current_tour = '';
					
					// Prepare the class
					$class = ($i % 2) ? 'gigpress-alt ' : ''; $i++;
					$class .= ($showdata['tour'] && $show_tours) ? 'gigpress-tour ' . $showdata['status'] : $showdata['status'];
					
					// Display the show
					include gigpress_template('sidebar-list');
				
				}
				
				// Close the current tour if needed
				if($show_tours && $current_tour) {
					include gigpress_template('sidebar-tour-end');					
				}
				
				// Close the list
				include gigpress_template('sidebar-list-end');
				
			}
		}
		
		if($some_results) {
		
		// After all artist groups
			
			// Display the list footer
			include gigpress_template('sidebar-list-footer');	

		} else {
			// No shows from any artist
			include gigpress_template('sidebar-list-empty');
		}	
			
	} else {

		// Not grouping by artists

		$shows = $wpdb->get_results("SELECT * FROM " . GIGPRESS_ARTISTS . " AS a, " . GIGPRESS_VENUES . " as v, " . GIGPRESS_SHOWS ." AS s LEFT JOIN  " . GIGPRESS_TOURS . " AS t ON s.show_tour_id = t.tour_id WHERE " . $date_condition . " AND show_status != 'deleted' AND s.show_artist_id = a.artist_id AND s.show_venue_id = v.venue_id " . $further_where . " ORDER BY s.show_date " . $sort . ",s.show_expire " . $sort . ",s.show_time " . $sort . " LIMIT " . $limit);
			
		if($shows) {
			
			$current_tour = '';
			$i = 0;
			
			include gigpress_template('sidebar-list-start');
										
			foreach($shows as $show) {
			
				// For each individual show
				
				$showdata = gigpress_prepare($show, 'public');
				
				// Close the previous tour if needed
				if($show_tours && $current_tour && $showdata['tour'] != $current_tour && !$tour) {
					include gigpress_template('sidebar-tour-end');						
				}
				
				// Open the current tour if needed
				if($show_tours && $showdata['tour'] && $showdata['tour'] != $current_tour && !$tour) {
					$current_tour = $showdata['tour'];
					include gigpress_template('sidebar-tour-heading');
				}
				
				if(!$showdata['tour']) $current_tour = '';
				
				// Prepare the class
				$class = ($i % 2) ? 'gigpress-alt ' : ''; $i++;
				$class .= ($showdata['tour'] && $show_tours) ? 'gigpress-tour ' . $showdata['status'] : $showdata['status'];
				
				// Display the show
				include gigpress_template('sidebar-list');
			}
			
			// Close the current tour if needed
			if($show_tours && $current_tour && !$tour) {
				include gigpress_template('sidebar-tour-end');						
			}
			
			// Close the list
			include gigpress_template('sidebar-list-end');
			
			// Display the list footer
			include gigpress_template('sidebar-list-footer');
												
		} else {
			// No shows from any artist
			include gigpress_template('sidebar-list-empty');
		}	
	}

	echo('<!-- Generated by GigPress ' . GIGPRESS_VERSION . ' -->
	');
	
	return ob_get_clean();
}
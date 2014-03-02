<?php

function gigpress_admin_shows() {
	
	if(isset($_REQUEST['gpaction']) && $_REQUEST['gpaction'] == "delete") {
		require_once('handlers.php');
		gigpress_delete_show();		
	}
	
	if(isset($_GET['gpaction']) && $_GET['gpaction'] == "undo") {
		require_once('handlers.php');
		gigpress_undo('show');		
	}
	
	if(isset($_POST['gpaction']) && $_POST['gpaction'] == "update") {
		require_once('handlers.php');
		gigpress_update_show();
	}
	
	if(isset($_GET['gpaction']) && $_GET['gpaction'] == "trash") {
		require_once('handlers.php');
		gigpress_empty_trash();		
	}	
	
	global $wpdb, $gpo;
		
	// Checks for filtering and pagination
	$url_args = '';
	$further_where = '';
	$pagination_args = array();

	global $current_user;
	get_currentuserinfo();
	
	if(isset($_GET['scope']))
	{
		$scope = $_GET['scope'];
		update_user_meta($current_user->ID, 'gigpress_scope', $scope);
	}
	else
	{
		if( ! $scope = get_user_meta($current_user->ID, 'gigpress_scope', true) ) {
			$scope = 'upcoming';
			update_user_meta($current_user->ID, 'gigpress_scope', $scope);
		}
	}
	
	switch($scope) {
		case 'upcoming':
			$condition = ">= '" . GIGPRESS_NOW . "'";
			break;
		case 'past':
			$condition = "< '" . GIGPRESS_NOW . "'";
			break;
		default:
			$condition = 'IS NOT NULL';
	}

	if(isset($_GET['sort']))
	{
		$sort = strtoupper($_GET['sort']);
		update_user_meta($current_user->ID, 'gigpress_sort', $sort);
	}
	else
	{
		if( ! $sort = get_user_meta($current_user->ID, 'gigpress_sort', true) ) {
			$sort = 'ASC';
			update_user_meta($current_user->ID, 'gigpress_sort', $sort);
		}
	}

	if(isset($_GET['limit']))
	{
		$limit = $_GET['limit'];
		update_user_meta($current_user->ID, 'gigpress_limit', $limit);
	}
	else
	{
		if( ! $limit = get_user_meta($current_user->ID, 'gigpress_limit', true) ) {
			$limit = 25;
			update_user_meta($current_user->ID, 'gigpress_limit', $limit);
		}
	}

		
	if(isset($_GET['gp-page'])) $url_args .= '&amp;gp-page=' . $_GET['gp-page'];
	
	if(isset($_GET['artist_id']) && $_GET['artist_id'] != '-1') {
		$further_where .= ' AND s.show_artist_id = ' . $wpdb->prepare('%d', $_GET['artist_id']) . ' ';
		$pagination_args['artist_id'] = $_GET['artist_id'];
		$url_args .= '&amp;artist_id=' . $_GET['artist_id'];
	}
	
	if(isset($_GET['tour_id']) && $_GET['tour_id'] != '-1') {
		$further_where .= ' AND s.show_tour_id = ' . $wpdb->prepare('%d', $_GET['tour_id']) . ' ';
		$pagination_args['tour_id'] = $_GET['tour_id'];		
		$url_args .= '&amp;tour_id=' . $_GET['tour_id'];
	}
	
	if(isset($_GET['venue_id']) && $_GET['venue_id'] != '-1') {
		$further_where .= ' AND s.show_venue_id = ' . $wpdb->prepare('%d', $_GET['venue_id']) . ' ';
		$pagination_args['venue_id'] = $_GET['venue_id'];		
		$url_args .= '&amp;venue_id=' . $_GET['venue_id'];
	}
		
	// Build pagination
	$show_count = $wpdb->get_var(
		"SELECT COUNT(*) FROM " . GIGPRESS_ARTISTS . " AS a, " . GIGPRESS_VENUES . " as v, " . GIGPRESS_SHOWS ." AS s LEFT JOIN  " . GIGPRESS_TOURS . " AS t ON s.show_tour_id = t.tour_id WHERE show_expire " . $condition . " AND show_status != 'deleted' AND s.show_artist_id = a.artist_id AND s.show_venue_id = v.venue_id " . $further_where . "ORDER BY show_date " . $sort . ",show_time " . $sort
		);
	if($show_count) {
		$pagination_args['page'] = 'gigpress-shows';
		$pagination = gigpress_admin_pagination($show_count, $limit, $pagination_args);			
	}

	$limit = (isset($_GET['gp-page'])) ? $pagination['offset'].','.$pagination['records_per_page'] : $limit;
	
	// Build the query	
	$shows = $wpdb->get_results("
		SELECT * FROM " . GIGPRESS_ARTISTS . " AS a, " . GIGPRESS_VENUES . " as v, " . GIGPRESS_SHOWS ." AS s LEFT JOIN  " . GIGPRESS_TOURS . " AS t ON s.show_tour_id = t.tour_id WHERE show_expire " . $condition . " AND show_status != 'deleted' AND s.show_artist_id = a.artist_id AND s.show_venue_id = v.venue_id " . $further_where . "ORDER BY show_date " . $sort . ",show_expire " . $sort . ",show_time " . $sort . " LIMIT " . $limit);

	?>
		
	<div class="wrap gigpress">

		<?php screen_icon('gigpress'); ?>		
		<h2><?php _e("Shows", "gigpress"); ?></h2>
		
		<ul class="subsubsub">
		<?php
			$all = $wpdb->get_var("SELECT COUNT(show_id) FROM " . GIGPRESS_SHOWS ." WHERE show_status != 'deleted'");
			$upcoming = $wpdb->get_var("SELECT count(show_id) FROM " . GIGPRESS_SHOWS . " WHERE show_expire >= '" . GIGPRESS_NOW . "' AND show_status != 'deleted'");
			$past = $wpdb->get_var("SELECT count(show_id) FROM " . GIGPRESS_SHOWS . " WHERE show_expire < '" . GIGPRESS_NOW . "' AND show_status != 'deleted'");
			echo('<li><a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=gigpress-shows&amp;scope=all"');
			if($scope == 'all') echo(' class="current"');
			echo('>' . __("All", "gigpress") . '</a> <span class="count">(' . $all	. ')</span> | </li>');
			echo('<li><a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=gigpress-shows&amp;scope=upcoming"');
			if($scope == 'upcoming') echo(' class="current"');
			echo('>' . __("Upcoming", "gigpress") . '</a> <span class="count">(' . $upcoming	. ')</span> | </li>');
			echo('<li><a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=gigpress-shows&amp;scope=past"');
			if($scope == 'past') echo(' class="current"');
			echo('>' . __("Past", "gigpress") . '</a> <span class="count">(' . $past	. ')</span></li>');
		?>
		</ul>
		
		<div class="tablenav">
			<div class="alignleft">
				<form action="" method="get">
					<div>
						<input type="hidden" name="page" value="gigpress-shows" />
						<select name="artist_id">
							<option value="-1"><?php _e("View all artists", "gigpress"); ?></option>
						<?php $artistdata = fetch_gigpress_artists();
						if($artistdata) {
							foreach($artistdata as $artist) {
								$selected = (isset($_GET['artist_id']) && $_GET['artist_id'] == $artist->artist_id) ? ' selected="selected"' : '';
								echo('<option value="' . $artist->artist_id . '"' . $selected . '>' . gigpress_db_out($artist->artist_name) . '</option>');
							}
						} else {
							echo('<option value="-1">' . __("No artists in the database", "gigpress") . '</option>');
						}
						?>
						</select>
						
						<select name="tour_id">
							<option value="-1"><?php _e("View all tours", "gigpress"); ?></option>
						<?php $tourdata = fetch_gigpress_tours();
						if($tourdata) {
							foreach($tourdata as $tour) {
								$selected = (isset($_GET['tour_id']) && $_GET['tour_id'] == $tour->tour_id) ? ' selected="selected"' : '';
								echo('<option value="' . $tour->tour_id . '"' . $selected . '>' . gigpress_db_out($tour->tour_name) . '</option>');
							}
						} else {
							echo('<option value="-1">' . __("No tours in the database", "gigpress") . '</option>');
						}
						?>
						</select>

						<select name="venue_id">
							<option value="-1"><?php _e("View all venues", "gigpress"); ?></option>
						<?php $venuedata = fetch_gigpress_venues();
						if($venuedata) {
							foreach($venuedata as $venue) {
								$selected = (isset($_GET['venue_id']) && $_GET['venue_id'] == $venue->venue_id) ? ' selected="selected"' : '';
								echo('<option value="' . $venue->venue_id . '"' . $selected . '>' . gigpress_db_out($venue->venue_name) . '</option>');
							}
						} else {
							echo('<option value="-1">' . __("No venues in the database", "gigpress") . '</option>');
						}
						?>
						</select>
								
						<select name="sort">
							<option value="desc"<?php if($sort == 'DESC') echo(' selected="selected"'); ?>><?php _e("Descending", "gigpress"); ?></option>
							<option value="asc"<?php if($sort == 'ASC') echo(' selected="selected"'); ?>><?php _e("Ascending", "gigpress"); ?></option>
						</select>

						<select name="limit">
						<?php
							$limits = array(10,25,50,100,150,200,250,300);
							foreach($limits as $limit_option) : ?>
							<option value="<?php echo $limit_option; ?>"<?php if($limit == $limit_option) echo(' selected="selected"'); ?>><?php echo $limit_option; ?></option>
						<?php endforeach; ?>
						</select>
						
						<input type="submit" value="Filter" class="button-secondary" />
					</div>
				</form>
			</div>
			<?php if(isset($pagination)) echo $pagination['output']; ?>
			<div class="clear"></div>
		</div>

		<form action="" method="post">
			<?php wp_nonce_field('gigpress-action') ?>
			<input type="hidden" name="gpaction" value="delete" />

		<table class="widefat">
			<thead>
				<tr>
					<th scope="col" class="column-cb check-column"><input type="checkbox" /></th>
					<th scope="col"><?php _e("Date", "gigpress"); ?></th>
					<th scope="col"><?php _e("Artist", "gigpress"); ?></th>
					<th scope="col"><?php _e("Venue", "gigpress"); ?></th>
					<th scope="col"><?php _e("City", "gigpress"); ?></th>
					<th scope="col"><?php _e("Country", "gigpress"); ?></th>
					<th scope="col"><?php _e("Tour", "gigpress") ?></th>					
					<th class="gp-centre" scope="col"><?php _e("Actions", "gigpress"); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th scope="col" class="column-cb check-column"><input type="checkbox" /></th>
					<th scope="col"><?php _e("Date", "gigpress"); ?></th>
					<th scope="col"><?php _e("Artist", "gigpress"); ?></th>
					<th scope="col"><?php _e("Venue", "gigpress"); ?></th>
					<th scope="col"><?php _e("City", "gigpress"); ?></th>
					<th scope="col"><?php _e("Country", "gigpress"); ?></th>
					<th scope="col"><?php _e("Tour", "gigpress") ?></th>					
					<th class="gp-centre" scope="col"><?php _e("Actions", "gigpress"); ?></th>
				</tr>
			</tfoot>			
			<tbody>
		<?php
		
		// Do we have dates?
		if($shows != FALSE) {
		
			foreach($shows as $show) {
		
				$showdata = gigpress_prepare($show, 'admin');

				?>
				<tr class="<?php echo 'gigpress-' . $showdata['status']; ?>">
					<th scope="row" class="check-column"><input type="checkbox" name="show_id[]" value="<?php echo $show->show_id; ?>" /></th>
					<td><span class="gigpress-date"><?php echo $showdata['date']; if($showdata['end_date']) { echo(' - ') . $showdata['end_date']; } ?></span>
					</td>
					<td><?php echo $showdata['artist']; ?></td>
					<td><?php echo $showdata['venue']; ?></td>
					<td><?php echo $showdata['city']; if(!empty($showdata['state'])) echo ', '.$showdata['state']; ?></td>
					<td><?php echo $showdata['country']; ?></td>
					<td><?php echo $showdata['tour']; ?></td>
					<td class="gp-centre">
						<a href="<?php bloginfo('wpurl') ?>/wp-admin/admin.php?page=gigpress/gigpress.php&amp;gpaction=edit&amp;show_id=<?php echo $show->show_id; ?>" class="edit" title="<?php _e("Edit", "gigpress"); ?>"><?php _e("Edit", "gigpress"); ?></a>&nbsp;|&nbsp;<a href="<?php bloginfo('wpurl') ?>/wp-admin/admin.php?page=gigpress/gigpress.php&amp;gpaction=copy&amp;show_id=<?php echo $show->show_id; ?>" class="edit" title="<?php _e("Copy", "gigpress"); ?>"><?php _e("Copy", "gigpress"); ?></a>
					</td>
				</tr>
				<tr class="<?php echo 'alternate' . ' gigpress-' . $showdata['status']; ?>">
					<td colspan="8"><small>
					<?php
						if($showdata['time']) echo $showdata['time'] . '. ';
						if($showdata['price']) echo __("Price", "gigpress") . ': ' . $showdata['price'] . '. ';
						if($showdata['admittance']) echo $showdata['admittance'] . '. ';
						if($showdata['ticket_link']) echo $showdata['ticket_link'] . '. ';
						if($showdata['external_link']) echo $showdata['external_link'] . '. ';
						if($showdata['ticket_phone']) echo __('Box office', "gigpress") . ': ' . $showdata['ticket_phone'] . '. ';
						echo $showdata['notes'] . ' ';
						echo $showdata['related_edit'];
					?>
					</small></td>
				</tr>	
			<?php } // end foreach			
		} else { // No results from the query
		?>
			<tr><td colspan="8"><?php _e("Sorry, no shows to display based on your criteria.", "gigpress"); ?></td></tr>
		<?php } ?>
			</tbody>
		</table>
		<div class="tablenav">
			<div class="alignleft">
				<input type="submit" value="<?php _e('Trash selected shows', 'gigpress'); ?>" class="button-secondary" /> &nbsp; 
				<?php
				if($tour_count = $wpdb->get_var("SELECT count(*) FROM ". GIGPRESS_TOURS ." WHERE tour_status = 'deleted'")) {
					$tours = $tour_count;
				} else {
					$tours = 0;
				}
				
				if($show_count = $wpdb->get_var("SELECT count(*) FROM ". GIGPRESS_SHOWS ." WHERE show_status = 'deleted'")) {
					$shows = $show_count;
				} else {
					$shows = 0;
				}
				if($tour_count || $show_count) {					
					echo('<small>'. __("You have", "gigpress"). ' <strong>'. $shows .' '. __("shows", "gigpress"). '</strong> '. __("and", "gigpress"). ' <strong>'. $tours .' '. __("tours", "gigpress") .'</strong> '. __("in your trash", "gigpress").'.');
					if($shows != 0 || $tours != 0) {
						echo(' <a href="'. wp_nonce_url(get_bloginfo('wpurl').'/wp-admin/admin.php?page=gigpress-shows&amp;gpaction=trash' . $url_args, 'gigpress-action') .'">'. __("Take out the trash now", "gigpress") .'</a>.');
					}
					echo('</small>');
				}
				?>
				</div>
	
			<?php if(isset($pagination)) echo $pagination['output']; ?>

		</div>
		</form>
	</div>
<?php } ?>
<?php

// HANDLER: ADD A SHOW
// ===================


function gigpress_prepare_show_fields($context = 'new') {

	global $wpdb;
	$gpo = get_option('gigpress_settings');
	$errors = array();
	$show = array();
	
	
	$show['show_date'] = $_POST['gp_yy'] . '-' . $_POST['gp_mm'] . '-' . $_POST['gp_dd'];
	if($_POST['gp_hh'] == "na") {
		$show['show_time'] = "00:00:01";
	} else {
		$min = ($_POST['gp_min'] == "na") ? '00' : $_POST['gp_min'];
		$show['show_time'] = $_POST['gp_hh'] . ':' . $min . ':00';
	}
	// If it's not a multi-day show, we need to set the expire date to match the show date
	if(!isset($_POST['show_multi']) || (isset($_POST['show_multi']) && empty($_POST['show_multi']) ) ) {
		$show['show_expire'] = $show['show_date'];
		$show['show_multi'] = 0;
	} else {
		$show['show_expire'] = $_POST['exp_yy'] . '-' . $_POST['exp_mm'] . '-' . $_POST['exp_dd'];
		$show['show_multi'] = 1;
	}
	$show['show_price'] = gigpress_db_in($_POST['show_price']);
	$show['show_tix_url'] = gigpress_db_in($_POST['show_tix_url'], FALSE);
	$show['show_tix_phone'] = gigpress_db_in($_POST['show_tix_phone']);
	$show['show_external_url'] = gigpress_db_in($_POST['show_external_url'], FALSE);
	$show['show_ages'] = $_POST['show_ages'];
	$show['show_notes'] = gigpress_db_in($_POST['show_notes'], FALSE);
	$show['show_status'] = $_POST['show_status'];
	
	// Create a new artist
	if($_POST['show_artist_id'] == 'new') {
		
		$alpha = preg_replace("/^the /uix", "", strtolower($_POST['artist_name']));
		$artist = array(
			'artist_name' => gigpress_db_in($_POST['artist_name']),
			'artist_alpha' => gigpress_db_in($alpha),
			'artist_url' => gigpress_db_in($_POST['artist_url'], FALSE)
		);
		$insert_artist = $wpdb->insert(GIGPRESS_ARTISTS, $artist);
		
		if($insert_artist) { 
			$show['show_artist_id'] = $wpdb->insert_id;
		} else {
			$errors[] = __("We had trouble creating your new artist. Sorry.", "gigpress");
		}
	} else {
		$show['show_artist_id'] = $_POST['show_artist_id'];
	}
	
	// Create a new venue
	if($_POST['show_venue_id'] == 'new') {
		
		$venue = gigpress_prepare_venue_fields();
		$insert_venue = $wpdb->insert(GIGPRESS_VENUES, $venue);
		
		if($insert_venue) { 
			$show['show_venue_id'] = $wpdb->insert_id;
		} else {
			$errors[] = __("We had trouble creating your new venue. Sorry.", "gigpress");
		}
		$gpo['default_country'] = $_POST['venue_country'];
	} else {
		$show['show_venue_id'] = $_POST['show_venue_id'];
	}
	
	// Create a new tour
	if($_POST['show_tour_id'] == 'new') {
		
		$tour = array('tour_name' => gigpress_db_in($_POST['tour_name']));
		
		$insert_tour = $wpdb->insert(GIGPRESS_TOURS, $tour);
		
		if($insert_tour) { 
			$show['show_tour_id'] = $wpdb->insert_id;
		} else {
			$errors[] = __("We had trouble creating your new tour. Sorry.", "gigpress");
		}
	} else {
		$show['show_tour_id'] = $_POST['show_tour_id'];
	}	
	
	// Create a new related post
	if ($_POST['show_related'] == "new")
	{
		
		// Find the variables we need for token replacement
		$artist = $wpdb->get_var("SELECT artist_name FROM " . GIGPRESS_ARTISTS . " WHERE artist_id = " . $show['show_artist_id'] . "");
		$venue = $wpdb->get_results("SELECT venue_name, venue_city FROM " . GIGPRESS_VENUES . " WHERE venue_id = " . $show['show_venue_id'] . "", ARRAY_A);
		
		// Prepare the post title
		$token_title = (isset($_POST['show_related_title'])) ? stripslashes(strip_tags(trim($_POST['show_related_title']))) : $gpo['default_title'];
		$find = array('%date%', '%long_date%', '%artist%', '%venue%', '%city%');
		$replace = array(
			mysql2date($gpo['date_format'], $show['show_date']),
			mysql2date($gpo['date_format_long'], $show['show_date']),
			gigpress_db_in($artist),
			gigpress_db_in($venue[0]['venue_name']),
			gigpress_db_in($venue[0]['venue_city'])
		);
		
		$post_date = ($_POST['show_related_date'] == 'show') ? $show['show_date'] . ' ' . $show['show_time'] : '';
		
		$related_post = array(
			'post_title' => str_replace($find, $replace, $token_title),
			'post_category' => array($gpo['related_category']),
			'post_date' => $post_date,
			'post_status' => "publish",
			'post_content' => ''
		);
		
		$insert = wp_insert_post($related_post);

		if ( $insert == 0 ) {
			$show['show_related'] = 0;
			$errors[] = __("We had trouble creating your Related Post. Sorry.", "gigpress");
		}
		else
		{
			$show['show_related'] = $insert;
		}
		
		$gpo['default_title'] = $token_title;
		$gpo['related_date'] = $_POST['show_related_date'];
	}
	else
	{
		$show['show_related'] = $_POST['show_related'];
	}
	
	if($context == 'new')
	{
		// Sticky stuff for the next entry
		$gpo['default_date'] = $show['show_date'];
		$gpo['default_time'] = $show['show_time'];
		$gpo['default_ages'] = $show['show_ages'];
		$gpo['default_artist'] = $show['show_artist_id'];
		$gpo['default_venue'] = $show['show_venue_id'];
		$gpo['default_tour'] = $show['show_tour_id'];
		update_option('gigpress_settings', $gpo);
	}
	
	// Not doing anything with $errors I suppose? Was I on crack when I wrote this?
	
	return $show;
}


function gigpress_prepare_venue_fields() {

	$venue = array(
		'venue_name' => gigpress_db_in($_POST['venue_name']),
		'venue_address' => gigpress_db_in($_POST['venue_address']),
		'venue_city' => gigpress_db_in($_POST['venue_city']),		
		'venue_state' => gigpress_db_in($_POST['venue_state']),		
		'venue_postal_code' => gigpress_db_in($_POST['venue_postal_code']),		
		'venue_country' => $_POST['venue_country'],
		'venue_url' => gigpress_db_in($_POST['venue_url'], FALSE),
		'venue_phone' => gigpress_db_in($_POST['venue_phone'])
	);
	return $venue;

}


function gigpress_error_checking($context) {
	
	$errors = array();
	
	switch($context) {
		case 'show':
			if(empty($_POST['show_venue_id']))
				$errors['show_venue_id'] = __("You must select a venue.", "gigpress");
			if(empty($_POST['show_artist_id']))
				$errors['artist_name'] = __("You must select an artist.", "gigpress");
			if($_POST['show_artist_id'] == 'new' && empty($_POST['artist_name']))
				$errors['artist_name'] = __("You must enter an artist name.", "gigpress");
			if($_POST['show_venue_id'] == 'new' && empty($_POST['venue_name']))
				$errors['venue_name'] = __("You must enter a venue name.", "gigpress");
			if($_POST['show_venue_id'] == 'new' && empty($_POST['venue_city']))
				$errors['venue_city'] = __("You must enter a city.", "gigpress");
			if($_POST['show_tour_id'] == 'new' && empty($_POST['tour_name']))
				$errors['tour_name'] = __("You must enter a tour name.", "gigpress");
			if(!checkdate($_POST['gp_mm'], $_POST['gp_dd'], $_POST['gp_yy']))
				$errors['show_date'] = __("That's not a valid date.", "gigpress");
			if(isset($_POST['show_multi']) && !checkdate($_POST['exp_mm'], $_POST['exp_dd'], $_POST['exp_yy']))
				$errors['expire_date'] = __("That's not a valid end date.", "gigpress");	
			break;
		case 'artist':
			if(empty($_POST['artist_name']))
				$errors['artist_name'] = __("You must enter an artist name.", "gigpress");
			break;
		case 'tour':
			if(empty($_POST['tour_name']))
				$errors['tour_name'] = __("You must enter a tour name.", "gigpress");
			break;	
		case 'venue':
			if(empty($_POST['venue_name']))
				$errors['venue_name'] = __("You must enter a venue name.", "gigpress");
			if(empty($_POST['venue_city']))
				$errors['venue_city'] = __("You must enter a city.", "gigpress");
			break;
	}

	return $errors;
}


function gigpress_add_show() {

	global $wpdb;
	
	$wpdb->show_errors();

	check_admin_referer('gigpress-action');
	
	$errors = gigpress_error_checking('show');
	
	if($errors) {
		echo('<div id="message" class="error fade">');
		foreach($errors as $error)
			echo("<p>".$error."</p>");
		echo("</div>");
		
		return $errors;
				
	} else {
	
		// Looks like we're all here, so let's add to the DB
		
		$show = gigpress_prepare_show_fields();
		$format = array('%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d');
		$addshow = $wpdb->insert(GIGPRESS_SHOWS, $show, $format);
		
		// Was the query successful?
		if($addshow != FALSE)
		{
			$gpo = get_option('gigpress_settings'); ?>
			
			<div id="message" class="updated fade">
				<p><?php echo __("Your show  on", "gigpress") . ' ' . mysql2date($gpo['date_format_long'], $show['show_date']) . ' ' . __("was successfully added.", "gigpress");
				echo(' <a href="' . get_bloginfo('wpurl') . '/wp-admin/?page=gigpress/gigpress.php&amp;gpaction=copy&amp;show_id=' . $wpdb->insert_id . '">' . __("Add a similar show", "gigpress"). '</a>');
				if($show['show_related']) echo(' | <a href="' . get_bloginfo('wpurl') . '/wp-admin/post.php?action=edit&amp;post=' . $show['show_related'] . '">' . __("Edit the related post", "gigpress"). '</a>');
				?></p>
		<?php
			global $errors; if($errors) {
				foreach($errors as $error) {
					echo('<p><strong>' . $error . '</strong></p>');
				}
			}
			unset($errors);
		?>
			</div>
			
	<?php } elseif($addshow === FALSE) { ?>
	
			<div id="message" class="error fade"><p><?php _e("Something ain't right - try again?", "gigpress"); ?></p></div>			
	
	<?php }
		unset($_POST, $show, $format);
	}
}


// HANDLER: EDIT A SHOW
// ====================


function gigpress_update_show() {

	global $wpdb, $gpo;
	$wpdb->show_errors();
	
	// Check the nonce
	check_admin_referer('gigpress-action');
			
	$errors = gigpress_error_checking('show');	
	
	if($errors) {
		echo('<div id="message" class="error fade">');
		foreach($errors as $error)
			echo("<p>".$error."</p>");
		echo("</div>");
		// We have to know that we're editing still, as we lose our previous query string
		$errors['editing'] = TRUE;
		return $errors;
		
	} else {
	
		// Looks like we're all here, so let's update the DB
		$show = gigpress_prepare_show_fields('edit');
		$where = array('show_id' => $_POST['show_id']);
		$format = array('%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d');
		$where_format = array('%d');
		$updateshow = $wpdb->update(GIGPRESS_SHOWS, $show, $where, $format, $where_format);
		
		// Was the query successful?
		if($updateshow != FALSE)
		{
			$gpo = get_option('gigpress_settings');
			?>	
			<div id="message" class="updated fade"><p><?php echo __("Your show  on", "gigpress") . ' ' . mysql2date($gpo['date_format_long'], $show['show_date']) . ' ' . __("was successfully updated.", "gigpress"); if($show['show_related']) echo(' <a href="' . get_bloginfo('wpurl') . '/wp-admin/post.php?action=edit&amp;post=' . $show['show_related'] . '">' . __("Edit the related post", "gigpress"). '.</a>'); ?></p>
			<?php
				global $errors; if($errors) {
					foreach($errors as $error) {
						echo('<p><strong>' . $error . '</strong></p>');
					}
				}
				unset($errors);
			?>
			</div>
		<?php } elseif($updateshow === FALSE) { ?>
			<div id="message" class="error fade"><p><?php _e("Something ain't right - try again?", "gigpress"); ?></p></div>
	<?php }
	unset($_POST, $show, $where, $format, $where_format, $updateshow);
	}
}


// HANDLER: DELETE A SHOW
// ======================


function gigpress_delete_show() {

	global $wpdb;
	$wpdb->show_errors();
	
	// Check the nonce
	check_admin_referer('gigpress-action');
	
	if(is_array($_REQUEST['show_id'])) {
		// We're deleting multiple shows, so we need to sanitize each id individually
		$shows = array();
		foreach($_REQUEST['show_id'] as $show) {
			$shows[] = $wpdb->prepare('%d', $show);
		}
		$shows = implode(',', $shows);
	
	} else {
		// Single show_id
		$shows = $wpdb->prepare('%d', $_REQUEST['show_id']);
	}
	
	$undo = wp_nonce_url(get_bloginfo('wpurl').'/wp-admin/admin.php?page=gigpress-shows&amp;gpaction=undo&amp;show_id='.$shows, 'gigpress-action');
		
	// Delete the show(s)
	$trashshow = $wpdb->query("UPDATE ".GIGPRESS_SHOWS." SET show_status = 'deleted' WHERE show_id IN($shows)");
	if($trashshow != FALSE) { ?>
			
		<div id="message" class="updated fade">
			<p><?php _e("Show(s) successfully deleted.", "gigpress"); ?> 
			<small>(<a href="<?php echo $undo; ?>"><?php _e("Undo", "gigpress"); ?></a>)</small></p>
		</div>
		
	<?php } elseif($trashshow === FALSE) { ?>
		
		<div id="message" class="error fade">
			<p><?php _e("We ran into some trouble deleting the show(s). Sorry.", "gigpress"); ?></p>
		</div>				
	<?php }
}


// HANDLER: ADD A VENUE
// ===================


function gigpress_add_venue() {

	global $wpdb, $gpo;	
	$errors = array();
	$wpdb->show_errors();
	
	check_admin_referer('gigpress-action');
	
	$errors = gigpress_error_checking('venue');
	
	if($errors) {
		echo('<div id="message" class="error fade">');
		foreach($errors as $error)
			echo("<p>".$error."</p>");
		echo("</div>");
		
		return $errors;
		
	} else {
	
		// Looks like we're all here, so let's add to the DB
		$venue = gigpress_prepare_venue_fields();
		$format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');
		$addvenue = $wpdb->insert(GIGPRESS_VENUES, $venue, $format);
		
		$gpo['default_country'] = $venue['venue_country'];
		update_option('gigpress_settings', $gpo);
		
		// Was the query successful?
		if($addvenue != FALSE) { ?>
			<div id="message" class="updated fade"><p><?php echo wptexturize($venue['venue_name']) .' '. __("was successfully added to the database.", "gigpress"); ?></p></div>
	<?php } elseif($addvenue === FALSE) { ?>
			<div id="message" class="error fade"><p><?php _e("Something ain't right - try again?", "gigpress"); ?></p></div>
	<?php }
		unset($venue);
	}
}


// HANDLER: UPDATE A VENUE
// ======================


function gigpress_update_venue() {

	global $wpdb;
	
	$wpdb->show_errors();
	
	check_admin_referer('gigpress-action');
			
	$errors = gigpress_error_checking('venue');
	
	if($errors) {
		echo('<div id="message" class="error fade">');
		foreach($errors as $error)
			echo("<p>".$error."</p>");
		echo("</div>");
		$errors['editing'] = TRUE;
		return $errors;

	} else {
	
		// Looks like we're all here, so let's add to the DB
		$venue = gigpress_prepare_venue_fields();
		$format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');
		$where = array('venue_id' => $_POST['venue_id']);
		$where_format = array('%d');
		$updatevenue = $wpdb->update(GIGPRESS_VENUES, $venue, $where, $format, $where_format);
		
		// Was the query successful?
		if($updatevenue != FALSE) { ?>
			<div id="message" class="updated fade"><p><?php echo wptexturize($venue['venue_name']) .' '. __("was successfully updated.", "gigpress"); ?></p></div>
	<?php } elseif($updatevenue === FALSE) { ?>
			<div id="message" class="error fade"><p><?php _e("Something ain't right - try again?", "gigpress"); ?></p></div>
	<?php }
		unset($venue,$where);
	}
}

// HANDLER: DELETE A VENUE
// ======================


function gigpress_delete_venue() {

	global $wpdb;
	
	$wpdb->show_errors();
	
	// Check the nonce
	check_admin_referer('gigpress-action');	
	
	// Delete the venue
	$trashvenue = $wpdb->query($wpdb->prepare("DELETE FROM ". GIGPRESS_VENUES ." WHERE venue_id = %d LIMIT 1", $_GET['venue_id']));
	if($trashvenue != FALSE) {	?>	
		<div id="message" class="updated fade"><p><?php _e("Venue successfully deleted.", "gigpress"); ?></p></div>	
	<?php } elseif($trashvenue === FALSE) { ?>
		<div id="message" class="error fade"><p><?php _e("We ran into some trouble deleting the venue. Sorry.", "gigpress"); ?></p></div>				
	<?php }
}


// HANDLER: ADD A TOUR
// ===================


function gigpress_add_tour() {

	global $wpdb;	
	
	$wpdb->show_errors();
	
	check_admin_referer('gigpress-action');
	
	$errors = gigpress_error_checking('tour');
	
	if($errors) {
		echo('<div id="message" class="error fade">');
		foreach($errors as $error)
			echo("<p>".$error."</p>");
		echo("</div>");
		
		return $errors;
		
	} else {
	
		// Looks like we're all here, so let's add to the DB
		
		$tour = array('tour_name' => gigpress_db_in($_POST['tour_name']));
		$addtour = $wpdb->insert(GIGPRESS_TOURS, $tour, array('%s','%d'));
		
		// Was the query successful?
		if($addtour != FALSE) { ?>
			<div id="message" class="updated fade"><p><?php echo wptexturize($tour['tour_name']) .' '. __("was successfully added to the database.", "gigpress"); ?></p></div>
	<?php } elseif($addtour === FALSE) { ?>
			<div id="message" class="error fade"><p><?php _e("Something ain't right - try again?", "gigpress"); ?></p></div>
	<?php }
		unset($tour);
	}
}


// HANDLER: UPDATE A TOUR
// ======================


function gigpress_update_tour() {

	global $wpdb;
	
	$wpdb->show_errors();
	
	check_admin_referer('gigpress-action');
			
	$errors = gigpress_error_checking('tour');
	
	if($errors) {
		echo('<div id="message" class="error fade">');
		foreach($errors as $error)
			echo("<p>".$error."</p>");
		echo("</div>");
		$errors['editing'] = TRUE;
		return $errors;

	} else {
			
		// Looks like we're all here, so let's update the DB
		$tour = array( 'tour_name' => gigpress_db_in($_POST['tour_name']) );
		$where = array('tour_id' => $_POST['tour_id']);	
		$updatetour = $wpdb->update(GIGPRESS_TOURS, $tour, $where, array('%s'), array('%d'));
		
		// Was the query successful?
		if($updatetour != FALSE) { ?>
			<div id="message" class="updated fade"><p><?php _e("Tour name successfully changed to", "gigpress"); echo ': ' . wptexturize($tour['tour_name']); ?></p></div>
	<?php } elseif($updatetour === FALSE) { ?>
			<div id="message" class="error fade"><p><?php _e("Something ain't right - try again?", "gigpress"); ?></p></div>
	<?php }
		unset($tour, $where);
	}
}


// HANDLER: DELETE A TOUR
// ======================


function gigpress_delete_tour() {

	global $wpdb;
	
	$undo = get_bloginfo('wpurl').'/wp-admin/admin.php?page=gigpress-tours&amp;gpaction=undo&amp;tour_id='.$_GET['tour_id'];
	$undo = wp_nonce_url($undo, 'gigpress-action');
	
	$wpdb->show_errors();
	
	// Check the nonce
	check_admin_referer('gigpress-action');	
	
	// Delete the tour
	$where = array('tour_id' => $_GET['tour_id']);
	$trashtour = $wpdb->update(GIGPRESS_TOURS, array('tour_status' => 'deleted'), $where, array('%s'), array('%s'));
	unset($where);
		
	if($trashtour != FALSE) {
	
		// Remove any previous shows marked to restore;
		// Find any shows associated with that tour, and mark them for restore;
		// Then then remove their foreign key
		
		$cleanup = $wpdb->query("UPDATE ".GIGPRESS_SHOWS." SET show_tour_restore = 0 WHERE show_tour_restore != 0");
		
		$where = array('show_tour_id' => $_GET['tour_id']);
		$restore = $wpdb->update(GIGPRESS_SHOWS, array('show_tour_id' => 0, 'show_tour_restore' => 1), $where, array('%d','%d'), array('%d'));
		unset($where);
		?>
		
		<div id="message" class="updated fade"><p><?php _e("Tour successfully deleted.", "gigpress"); ?> <small>(<a href="<?php echo $undo; ?>"><?php _e("Undo", "gigpress"); ?></a>)</small></p></div>
		
	<?php } elseif($trashtour === FALSE) { ?>
		
		<div id="message" class="error fade"><p><?php _e("We ran into some trouble deleting the tour. Sorry.", "gigpress"); ?></p></div>				
	<?php }
}



// HANDLER: ADD AN ARTIST
// ===================


function gigpress_add_artist() {

	global $wpdb;	
	
	$wpdb->show_errors();

	check_admin_referer('gigpress-action');
	
	$errors = gigpress_error_checking('artist');
	
	if($errors) {
		echo('<div id="message" class="error fade">');
		foreach($errors as $error)
			echo("<p>".$error."</p>");
		echo("</div>");
		
		return $errors;
		
	} else {
		
		$alpha = preg_replace("/^the /uix", "", strtolower($_POST['artist_name']));
		$artist = array(
			'artist_name' => gigpress_db_in($_POST['artist_name']),
			'artist_alpha' => gigpress_db_in($alpha),
			'artist_url' => gigpress_db_in($_POST['artist_url'], FALSE)
		);
		$format = array('%s', '%s', '%s');
		$addartist = $wpdb->insert(GIGPRESS_ARTISTS, $artist, $format);
		
		// Was the query successful?
		if($addartist != FALSE) { ?>
			<div id="message" class="updated fade"><p><?php echo wptexturize($artist['artist_name']) .' '. __("was successfully added to the database.", "gigpress"); ?></p></div>
	<?php } elseif($addartist === FALSE) { ?>
			<div id="message" class="error fade"><p><?php _e("Something ain't right - try again?", "gigpress"); ?></p></div>
	<?php }
		unset($artist);
	}
}


// HANDLER: UPDATE AN ARTIST
// ======================


function gigpress_update_artist() {

	global $wpdb;
	
	$wpdb->show_errors();
	
	check_admin_referer('gigpress-action');
			
	$errors = gigpress_error_checking('artist');
	
	if($errors) {
		echo('<div id="message" class="error fade">');
		foreach($errors as $error)
			echo("<p>".$error."</p>");
		echo("</div>");
		$errors['editing'] = TRUE;
		return $errors;

	} else {

		$alpha = preg_replace("/^the /uix", "", strtolower($_POST['artist_name']));
		$artist = array(
			'artist_name' => gigpress_db_in($_POST['artist_name']),
			'artist_alpha' => gigpress_db_in($alpha),
			'artist_url' => gigpress_db_in($_POST['artist_url'], FALSE)
		);
		$format = array('%s', '%s', '%s');
		$where = array('artist_id' => $_POST['artist_id']);
		$updateartist = $wpdb->update(GIGPRESS_ARTISTS, $artist, $where, $format, array('%d'));
		
		// Was the query successful?
		if($updateartist != FALSE) { ?>
			<div id="message" class="updated fade"><p><?php echo wptexturize($artist['artist_name']) .' '. __("successfully updated.", "gigpress"); ?></p></div>
	<?php } elseif($updateartist === FALSE) { ?>
			<div id="message" class="error fade"><p><?php _e("Something ain't right - try again?", "gigpress"); ?></p></div>
	<?php }
		unset($artist, $where);
	}
}


// HANDLER: DELETE AN ARTIST
// ======================


function gigpress_delete_artist() {

	global $wpdb;
		
	$wpdb->show_errors();
	
	// Check the nonce
	check_admin_referer('gigpress-action');	
	
	// Delete the artist
	$trashartist = $wpdb->query($wpdb->prepare("DELETE FROM ". GIGPRESS_ARTISTS ." WHERE artist_id = %d LIMIT 1", $_GET['artist_id']));
	if($trashartist != FALSE) {	?>	
		<div id="message" class="updated fade"><p><?php _e("Artist successfully deleted.", "gigpress"); ?></p></div>	
	<?php } elseif($trashartist === FALSE) { ?>
		
		<div id="message" class="error fade"><p><?php _e("We ran into some trouble deleting the artist. Sorry.", "gigpress"); ?></p></div>				
	<?php }
}



// HANDLER: UNDO DELETING SOMETHING
// ======================


function gigpress_undo($type) {

	global $wpdb;
	$wpdb->show_errors();
	
	check_admin_referer('gigpress-action');	
	
	if($type == "show") {
		
		$show_ids = explode(',', $_REQUEST['show_id']);
		
		if(count($show_ids) > 1) {
			// We're restoring multiple shows, so we santiize each show_id individually
			$shows = array();
			foreach($show_ids as $show) {
				$shows[] = $wpdb->prepare('%d', $show);
			}
			$shows = implode(',', $shows);
		} else {
			$shows = $wpdb->prepare('%d', $_REQUEST['show_id']);
		}
					
		// Restore the show(s)
		$undo = $wpdb->query("UPDATE ".GIGPRESS_SHOWS." SET show_status = 'active' WHERE show_id IN($shows)");		
		
		if($undo != FALSE) { ?>
			<div id="message" class="updated fade">
				<p><?php _e("Show(s) successfully restored.", "gigpress"); ?></p>
			</div>
		<?php } elseif($undo === FALSE) { ?>
			<div id="message" class="error fade">
				<p><?php _e("We ran into some trouble restoring your show(s). Sorry.", "gigpress"); ?></p>
			</div>				
		<?php }
	}
	
	if($type == "tour") {
		
		// Restore the tour
		$where = array('tour_id' => $_GET['tour_id']);
		$undo = $wpdb->update(GIGPRESS_TOURS, array('tour_status' => 'active'), $where, array('%s'), array('%d'));
		unset($where);
		
		// Update the shows that need it to associate with this tour
		$data = array('show_tour_id' => $_GET['tour_id'], 'show_tour_restore' => 0);
		$restore = $wpdb->update(GIGPRESS_SHOWS, $data, array('show_tour_restore' => 1), array('%d', '%d'), array('%d'));
		unset($data);
		
		if($undo != FALSE) { ?>
			<div id="message" class="updated fade"><p><?php _e("Tour successfully restored from the database.", "gigpress"); ?></p></div>
		<?php } elseif($undo === FALSE) { ?>
			<div id="message" class="error fade"><p><?php _e("We ran into some trouble restoring the tour. Sorry.", "gigpress"); ?></p></div>
		<?php }
	}
}


// HANDLER: IMPORT FROM CSV
// ========================

function gigpress_import() {

	// Deep breath
	
	global $wpdb, $gpo;
	
	// We've just uploaded a file to import
	check_admin_referer('gigpress-action');
	$upload = wp_upload_bits( $_FILES['gp_import']['name'], null, file_get_contents($_FILES['gp_import']['tmp_name']) );
	
	if (empty($upload['error'])) {
		// The file was uploaded, so let's try and parse the mofo
		require_once(WP_PLUGIN_DIR . '/gigpress/lib/parsecsv.lib.php');
		// This is under MIT license, which ain't GNU, but was else is new? Don't tell on me!
		$csv = new parseCSV();
		$csv->parse($upload['file']);
		
		if($csv->data) {
				
			// Looks like we parsed something
			$inserted = array(); $skipped = array(); $duplicates = array();
			
			foreach($csv->data as $key => $show) {				
				// Check to see if we have this artist
				$artist_exists = $wpdb->get_var(
					$wpdb->prepare("SELECT artist_id FROM " . GIGPRESS_ARTISTS . " WHERE artist_name = '%s'", $show['Artist'])
				);
								
				if(empty($artist_exists)) {
					// Can't find an artist with this name, so we'll have to create them
					$alpha = preg_replace("/^the /uix", "", strtolower($show['Artist']));
					$new_artist = array(
						'artist_name' => gigpress_db_in($show['Artist']),
						'artist_alpha' => gigpress_db_in($alpha),
						'artist_url' => gigpress_db_in(@$show['Artist URL'], FALSE)
					);
					$addartist = $wpdb->insert(GIGPRESS_ARTISTS, $new_artist, '%s');
					$show['artist_id'] = $wpdb->insert_id;
				} else {
					$show['artist_id'] = $artist_exists;
				}
				
				if(!empty($show['Tour'])) {
					// Check to see if we have this tour
					$tour_exists = $wpdb->get_var(
						$wpdb->prepare("SELECT tour_id FROM " . GIGPRESS_TOURS . " WHERE tour_name = '%s' AND tour_status = 'active'", $show['Tour'])
					);
					if(empty($tour_exists)) {
						// Can't find a tour with this name, so we'll have to create it
						$new_tour = array('tour_name' => gigpress_db_in($show['Tour']));
						$wpdb->insert(GIGPRESS_TOURS, $new_tour, '%s');
						$show['tour_id'] = $wpdb->insert_id;
					} else {
						$show['tour_id'] = $tour_exists;
					}
				}
				else
				{
					$show['tour_id'] = 0;
				}			

				// Check to see if we have this venue
				$venue_exists = $wpdb->get_var(
					$wpdb->prepare("SELECT venue_id FROM " . GIGPRESS_VENUES . " WHERE venue_name = '%s' AND venue_city = '%s' AND venue_country = '%s'", $show['Venue'], $show['City'], $show['Country'])
				);
				if(empty($venue_exists)) {
					// Can't find a venue with this name, so we'll have to create it
					$new_venue = array(
						'venue_name' => gigpress_db_in(@$show['Venue']),
						'venue_address' => gigpress_db_in(@$show['Address']),
						'venue_city' => gigpress_db_in(@$show['City']),
						'venue_state' => gigpress_db_in(@$show['State']),
						'venue_postal_code' => gigpress_db_in(@$show['Postal code']),
						'venue_country' => gigpress_db_in(@$show['Country']),
						'venue_url' => gigpress_db_in(@$show['Venue URL'], FALSE),
						'venue_phone' => gigpress_db_in(@$show['Venue phone'])
					);
					$wpdb->insert(GIGPRESS_VENUES, $new_venue, '%s');
					$show['venue_id'] = $wpdb->insert_id;
				} else {
					$show['venue_id'] = $venue_exists;
				}
							
				if($show['Time'] == FALSE) $show['Time'] = '00:00:01';
			
				if($wpdb->get_var("SELECT count(*) FROM " . GIGPRESS_SHOWS . " WHERE show_artist_id = " . $show['artist_id'] . " AND show_date = '" . $show['Date'] . "' AND show_time = '" . $show['Time'] . "' AND show_venue_id = " . $show['venue_id'] . " AND show_status != 'deleted'") > 0) {
					// It's a duplicate, so log it and move on
					$duplicates[] = $show;
				} else {
					if($show['End date'] == FALSE) {
						$show['show_multi'] = 0; $show['End date'] = $show['Date'];
					} else {
						$show['show_multi'] = 1;
					}
					
					$new_show = array(
						'show_date' => $show['Date'],
						'show_time' => $show['Time'],
						'show_multi' => $show['show_multi'],
						'show_expire' => $show['End date'],
						'show_artist_id' => $show['artist_id'],
						'show_venue_id' => $show['venue_id'],
						'show_tour_id' => $show['tour_id'],
						'show_ages' => gigpress_db_in(@$show['Admittance']),
						'show_price' => gigpress_db_in(@$show['Price']),
						'show_tix_url' => gigpress_db_in(@$show['Ticket URL'], FALSE),
						'show_tix_phone' => gigpress_db_in(@$show['Ticket phone']),
						'show_external_url' => gigpress_db_in(@$show['External URL']),
						'show_notes' => gigpress_db_in(@$show['Notes'], FALSE),
						'show_related' => '0'
					);
					
					// Are we importing related post IDs?
					if(isset($_POST['include_related']) && $_POST['include_related'] = 'y') {
						$new_show['show_related'] = @$show['Related ID'];
					}
					
					$format = array('%s','%s','%d','%s','%d','%d','%d','%s','%s','%s','%s','%s', '%s', '%d');
					
					$import = $wpdb->insert(GIGPRESS_SHOWS, $new_show, $format);
					
					if($import != FALSE) {
						$inserted[] = $show;
					} else {
						$skipped[] = $show;
					}
				}
			} // end foreach import

			if(!empty($skipped)) {
				echo('<h4 class="error">' . count($skipped) . ' ' . __("shows were skipped due to errors", "gigpress") . '.</h4>');
				echo('<ul class="ul-square">');
				foreach($skipped as $key => $show) {
					echo('<li>' . wptexturize($show['Artist']) . ' ' . __("in", "gigpress") . ' ' . wptexturize($show['City']) . ' ' . __("at", "gigpress") . ' ' . wptexturize($show['Venue']) . ' ' . __("on", "gigpress") . ' ' .  mysql2date($gpo['date_format'], $show['Date']) . '</li>'); 
				}
				echo('</ul>');
			}
			
			if(!empty($duplicates)) {
				echo('<h4 class="error">' . count($duplicates) . ' ' . __("shows were skipped as they were deemed duplicates", "gigpress") . '.</h4>');
				echo('<ul class="ul-square">');
				foreach($duplicates as $key => $show) {
					echo('<li>' . wptexturize($show['Artist']) . ' ' . __("in", "gigpress") . ' ' . wptexturize($show['City']) . ' ' . __("at", "gigpress") . ' ' . wptexturize($show['Venue']) . ' ' . __("on", "gigpress") . ' ' .  mysql2date($gpo['date_format'], $show['Date']) . '</li>'); 
				}
				echo('</ul>');
			}	
			
			if(!empty($inserted)) {
				echo('<h4 class="updated">' . count($inserted) . ' ' . __("shows were successfully imported", "gigpress") . '.</h4>');
				echo('<ul class="ul-square">');
				foreach($inserted as $key => $show) {
					echo('<li>' . wptexturize($show['Artist']) . ' ' . __("in", "gigpress") . ' ' . wptexturize($show['City']) . ' ' . __("at", "gigpress") . ' ' . wptexturize($show['Venue']) . ' ' . __("on", "gigpress") . ' ' .  mysql2date($gpo['date_format'], $show['Date']) . '</li>'); 
				}
				echo('</ul>');
			}								
			
		} else {
			// The file uploaded, but there were no results from the parse
			echo('<div id="message" class="error fade"><p>' . __("Sorry, but there was an error parsing your file. Maybe double-check your formatting and file type?", "gigpress") . '.</p></div>');
		
		}
		
		// Bye-bye
		unlink($upload['file']);
	
	} else {
		// The upload failed
		echo('<div id="message" class="error fade"><p>' . __("Sorry, but there was an error uploading", "gigpress") . ' <strong>' . $_FILES['gp_import']['name'] . '</strong>: ' . $upload['error'] . '.</p></div>');
	}

}


// HANDLER: EMPTY TRASH
// ======================

function gigpress_empty_trash() {

	global $wpdb;
	$wpdb->show_errors();
	check_admin_referer('gigpress-action');

	$trashshows = $wpdb->query("DELETE FROM ". GIGPRESS_SHOWS ." WHERE show_status = 'deleted'");
	$trashtours = $wpdb->query("DELETE FROM ". GIGPRESS_TOURS ." WHERE tour_status = 'deleted'");

	if($trashshows || $trashtours) { ?>
		<div id="message" class="updated fade"><p><?php _e("All shows and tours in the trash have been permanently deleted.", "gigpress"); ?></p></div>
	<?php } else { ?>
		<div id="message" class="error fade"><p><?php _e("We ran into some trouble emptying the trash. Sorry.", "gigpress"); ?></p></div>				
	<?php }
	
}


// MAP TOURS TO ARTISTS
// (useful for some migrations from 1.4.x to 2.0)
// ==============================================

function gigpress_map_tours_to_artists() {

	global $wpdb;

	$tours = $wpdb->get_results("SELECT tour_name, tour_id FROM " . GIGPRESS_TOURS . " WHERE tour_status = 'active'");
	if($tours) {
		foreach($tours as $tour) {
			$insert = $wpdb->insert(GIGPRESS_ARTISTS, array('artist_name' => $tour->tour_name));
			$update = $wpdb->update(GIGPRESS_SHOWS, array('show_artist_id' => $wpdb->insert_id, 'show_tour_id' => 0), array('show_tour_id' => $tour->tour_id));
			$delete = $wpdb->query("DELETE FROM " . GIGPRESS_TOURS . " WHERE tour_id = " . $tour->tour_id . " LIMIT 1");
		}
		if($insert && $update && $delete) {
			echo('<div id="message" class="updated fade"><p>' . __("All tours have been migrated into artists.", "gigpress") . '</p></div>');
		} else {
			echo('<div id="message" class="error fade"><p>' . __("There was an error migrating tours to artists. Sorry.", "gigpress") . '</p></div>');

		}
	} else {
		echo('<div id="message" class="error fade"><p>' . __("There were no tours to migrate.", "gigpress") . '</p></div>');
	}

}
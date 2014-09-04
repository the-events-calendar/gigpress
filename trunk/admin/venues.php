<?php

function gigpress_venues() {

	global $wpdb, $gpo;
	
	if(isset($_POST['gpaction']) && $_POST['gpaction'] == "add") {
		require_once('handlers.php');
		$result = gigpress_add_venue();		
	}
	
	if(isset($_POST['gpaction']) && $_POST['gpaction'] == "update") {
		require_once('handlers.php');
		$result = gigpress_update_venue();
	}
	
	if(isset($_GET['gpaction']) && $_GET['gpaction'] == "delete") {
		require_once('handlers.php');
		gigpress_delete_venue();		
	}
		
	$url_args = (isset($_GET['gp-page'])) ? '&amp;gp-page=' . $_GET['gp-page'] : '';

	?>

	<div class="wrap gigpress gp-venues">

	<?php screen_icon('gigpress'); ?>		
	<h2><?php _e("Venues", "gigpress"); ?></h2>	
	
	<?php
	
	$venue_id = (isset($_REQUEST['venue_id'])) ? $wpdb->prepare('%d', $_REQUEST['venue_id']) : '';
	
	// Check our context to determine what we're populating
	
	if(isset($result)) {
	
		// This means we $_POSTed and there were errors
		$venue_name = gigpress_db_out($_POST['venue_name']);		
		$venue_city = gigpress_db_out($_POST['venue_city']);		
		$venue_address = gigpress_db_out($_POST['venue_address']);
		$venue_state = gigpress_db_out($_POST['venue_state']);		
		$venue_postal_code = gigpress_db_out($_POST['venue_postal_code']);				
		$venue_country = $_POST['venue_country'];		
		$venue_url = gigpress_db_out($_POST['venue_url']);		
		$venue_phone = gigpress_db_out($_POST['venue_phone']);
	
	} elseif (isset($_GET['gpaction']) && $_GET['gpaction'] == "edit") {
	
		// Just loaded for editing
	
		$venuedata = $wpdb->get_results("
			SELECT * from ". GIGPRESS_VENUES ." WHERE venue_id = ". $venue_id ." LIMIT 1
		");
		if($venuedata) {
			
			foreach($venuedata as $venue) {
				$venue_name = gigpress_db_out($venue->venue_name);		
				$venue_city = gigpress_db_out($venue->venue_city);		
				$venue_address = gigpress_db_out($venue->venue_address);		
				$venue_state = gigpress_db_out($venue->venue_state);		
				$venue_postal_code = gigpress_db_out($venue->venue_postal_code);		
				$venue_country = $venue->venue_country;		
				$venue_url = gigpress_db_out($venue->venue_url);		
				$venue_phone = gigpress_db_out($venue->venue_phone);		
			}
			
		} else {
		
			$load_error = '<div id="message" class="error fade"><p>' . __("Sorry, but we had trouble loading that venue for editing.", "gigpress") . '</div>';
			
		}
	
	} else {
		
		// New venue entry
		$venue_country = $gpo['default_country'];
	
	}
	
	if(isset($_GET['gpaction']) && $_GET['gpaction'] == "edit" && $venuedata || isset($result) && isset($result['editing']) ) {
	?>
		<h3><?php _e("Edit this venue", "gigpress"); ?></h3>
	
		<form method="post" action="<?php echo get_bloginfo('wpurl')."/wp-admin/admin.php?page=gigpress-venues" . $url_args; ?>">
		<input type="hidden" name="gpaction" value="update" />
		<input type="hidden" name="venue_id" value="<?php echo $venue_id ?>" />

	<?php } else { ?>
	
		<?php if(isset($load_error)) echo $load_error; ?>

		<h3><?php _e("Add an venue", "gigpress"); ?></h3>
		
		<form method="post" action="<?php echo get_bloginfo('wpurl')."/wp-admin/admin.php?page=gigpress-venues" . $url_args; ?>">
		<input type="hidden" name="gpaction" value="add" />		
			
<?php } ?>
		<?php wp_nonce_field('gigpress-action') ?>
		<table class="form-table gp-table">
			<tr>
				<th scope="row"><label for="venue_name"><?php _e("Venue name", "gigpress") ?>:<span class="gp-required">*</span></label></th>
				<td><input type="text" size="48" name="venue_name" id="venue_name" value="<?php if(isset($venue_name)) echo $venue_name; ?>"<?php if(isset($result) && isset($result['venue_name'])) echo(' class="gigpress-error"'); ?> /></td>
			</tr>			  		
			<tr>
				<th scope="row"><label for="venue_address"><?php _e("Venue address", "gigpress") ?>:</label></th>
				<td><input type="text" size="48" name="venue_address" id="venue_address" value="<?php if(isset($venue_address)) echo $venue_address; ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="venue_city"><?php _e("Venue city", "gigpress") ?>:<span class="gp-required">*</span></label></th>
				<td><input type="text" size="48" name="venue_city" id="venue_city" value="<?php if(isset($venue_city)) echo $venue_city; ?>"<?php if(isset($result) && isset($result['venue_city'])) echo(' class="gigpress-error"'); ?> /></td>
			</tr>				
			<tr>
				<th scope="row"><label for="venue_state"><?php _e("Venue state/province", "gigpress") ?>:</label></th>
				<td><input type="text" size="48" name="venue_state" id="venue_state" value="<?php if(isset($venue_state)) echo $venue_state; ?>" /></td>
			  </tr>
			<tr>
				<th scope="row"><label for="venue_postal_code"><?php _e("Venue postal code", "gigpress") ?>:</label></th>
				<td><input type="text" size="48" name="venue_postal_code" id="venue_postal_code" value="<?php if(isset($venue_postal_code)) echo $venue_postal_code; ?>" /></td>
			  </tr>	
			<tr>
				<th scope="row"><label for="venue_country"><?php _e("Venue country", "gigpress") ?>:</label></th>
				<td>
					<select name="venue_country" id="venue_country">
					<?php global $gp_countries;
					foreach ($gp_countries as $code => $name) {
						$sel = ($code == $venue_country) ? ' selected="selected"' : '';
						echo('<option value="' . $code . '"' . $sel . '>' . $name . '</option>');
					} ?>
					</select>
				</td>
			</tr>	  
			<tr>
				<th scope="row"><label for="venue_url"><?php _e("Venue website", "gigpress") ?>:</label></th>
				<td><input type="text" size="48" name="venue_url" id="venue_url" value="<?php if(isset($venue_url)) echo $venue_url; ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="venue_phone"><?php _e("Venue phone", "gigpress") ?>:</label></th>
				<td><input type="text" size="48" name="venue_phone" id="venue_phone" value="<?php if(isset($venue_phone)) echo $venue_phone; ?>" /></td>
			</tr>			  
			<tr>
				<td>&nbsp;</td>
				<td>
		<?php if(isset($_GET['gpaction']) && $_GET['gpaction'] == "edit" || isset($result) && isset($result['editing']) ) { ?>
		
				<span class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php _e("Update venue", "gigpress") ?>" /></span> <?php _e("or", "gigpress"); ?> <a href="<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=gigpress-venues<?php echo $url_args; ?>"><?php _e("cancel", "gigpress"); ?></a>
		
		<?php } else { ?>
		
				<span class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php _e("Add venue", "gigpress") ?>" /></span>		
				
		<?php } ?>		
				</td>
			</tr>			
		</table>
		
		</form>
	
	<h3><?php _e("All venues", "gigpress"); ?></h3>
		
	<div class="tablenav">
		<div class="alignleft">
			<p><?php _e("Note that you cannot delete a venue while it has shows in the database.", "gigpress"); ?></p>
		</div>
	<?php
		$venue_count = $wpdb->get_var("SELECT COUNT(*) FROM ". GIGPRESS_VENUES);
		if($venue_count) {
			$pagination_args['page'] = 'gigpress-venues';
			$pagination = gigpress_admin_pagination($venue_count, 15, $pagination_args);
			if(isset($pagination)) echo $pagination['output'];
		}
		$limit = (isset($_GET['gp-page'])) ? $pagination['offset'].','.$pagination['records_per_page'] : 15;	
	?>
		<div class="clear"></div>
	</div>
	
	<table class="widefat">
		<thead>
			<tr>
				<th scope="col" class="gp-tiny">ID</th>
				<th scope="col"><?php _e("Name", "gigpress"); ?></th>
				<th scope="col"><?php _e("City", "gigpress"); ?></th>
				<th scope="col"><?php _e("Address", "gigpress"); ?></th>
				<th scope="col"><?php _e("Country", "gigpress"); ?></th>
				<th scope="col"><?php _e("Phone", "gigpress"); ?></th>
				<th scope="col" class="gp-centre"><?php _e("Number of shows", "gigpress"); ?></th>
				<th class="gp-centre" scope="col"><?php _e("Actions", "gigpress"); ?></th>
			</tr>
		</thead>
		<tbody>
	<?php
		$venues = $wpdb->get_results("SELECT * FROM ". GIGPRESS_VENUES ." ORDER BY venue_name ASC LIMIT ". $limit);
		if($venues) {
						
			$i = 0;
			foreach($venues as $venue) {
			
				if($n = $wpdb->get_var("SELECT count(*) FROM ". GIGPRESS_SHOWS ." WHERE show_venue_id = ". $venue->venue_id ." AND show_status != 'deleted'")) {
					$count = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=gigpress-shows&amp;venue_id=' . $venue->venue_id . '">' . $n . '</a>';
				} else {
					$count = 0;
				}
				
				$venuedata = gigpress_prepare($venue, 'venue');

				++ $i;
				$style = ($i % 2) ? '' : ' class="alternate"';
				// Print out our rows.
				?>
				<tr<?php echo $style; ?>>
					<td class="gp-tiny"><?php echo $venuedata['venue_id']; ?></td>
					<td><?php echo $venuedata['venue']; ?></td>
					<td><?php echo $venuedata['city']; if(!empty($venuedata['state'])) echo ', '.$venuedata['state']; ?></td>
					<td><?php echo $venuedata['address']; ?></td>
					<td><?php echo $venuedata['country']; ?></td>
					<td><?php echo $venuedata['venue_phone']; ?></td>
					<td class="gp-centre"><?php echo $count; ?></td>
					<td class="gp-centre">
						<a href="<?php echo get_bloginfo('wpurl').'/wp-admin/admin.php?page=gigpress-venues&amp;gpaction=edit&amp;venue_id=' . $venue->venue_id . $url_args; ?>" class="edit"><?php _e("Edit", "gigpress"); ?></a>
						<?php if(!$count) { ?> | <a href="<?php echo wp_nonce_url(get_bloginfo('wpurl').'/wp-admin/admin.php?page=gigpress-venues&amp;gpaction=delete&amp;venue_id='.$venue->venue_id . $url_args, 'gigpress-action'); ?>" class="delete"><?php _e("Delete", "gigpress"); ?></a><?php } ?>
					</td>
				</tr>
				<?php }
		} else {

			// We don't have any venues, so let's say so
			?>
			<tr><td colspan="8"><strong><?php _e("No venues in the database", "gigpress"); ?></strong></td></tr>
	<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<th scope="col" class="gp-tiny">ID</th>
				<th scope="col"><?php _e("Name", "gigpress"); ?></th>
				<th scope="col"><?php _e("City", "gigpress"); ?></th>
				<th scope="col"><?php _e("Address", "gigpress"); ?></th>
				<th scope="col"><?php _e("Country", "gigpress"); ?></th>
				<th scope="col"><?php _e("Phone", "gigpress"); ?></th>
				<th scope="col" class="gp-centre"><?php _e("Number of shows", "gigpress"); ?></th>
				<th class="gp-centre" scope="col"><?php _e("Actions", "gigpress"); ?></th>
			</tr>
		</tfoot>
	</table>
	
	<div class="tablenav">
	<?php if(isset($pagination)) echo $pagination['output']; ?>
	</div>
</div>
<?php }
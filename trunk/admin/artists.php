<?php

function gigpress_artists() {

	global $wpdb;
	
	if(isset($_POST['gpaction']) && $_POST['gpaction'] == "add") {
		require_once('handlers.php');
		$result = gigpress_add_artist();		
	}
	
	if(isset($_POST['gpaction']) && $_POST['gpaction'] == "update") {
		require_once('handlers.php');
		$result = gigpress_update_artist();
	}
	
	if(isset($_GET['gpaction']) && $_GET['gpaction'] == "delete") {
		require_once('handlers.php');
		gigpress_delete_artist();		
	}

	if(isset($_GET['gpaction']) && $_GET['gpaction'] == "import-tours") {
		require_once('handlers.php');
		gigpress_map_tours_to_artists();		
	}
	
	$url_args = (isset($_GET['gp-page'])) ? '&amp;gp-page=' . $_GET['gp-page'] : '';	
	
	?>

	<div class="wrap gigpress gp-artists">

	<?php screen_icon('gigpress'); ?>		
	<h2><?php _e("Artists", "gigpress"); ?></h2>	
	
	<?php
	if(isset($_GET['gpaction']) && $_GET['gpaction'] == "edit" || isset($result) && isset($result['editing']) ) {

		$artist_id = (isset($_REQUEST['artist_id'])) ? $wpdb->prepare('%d', $_REQUEST['artist_id']) : '';
	
		$artist = $wpdb->get_row("SELECT artist_name, artist_url FROM ". GIGPRESS_ARTISTS ." WHERE artist_id = ". $artist_id);
		if($artist) {
			
			$submit = '<span class="submit"><input type="submit" name="Submit" class="button-primary" value="' .  __("Update artist", "gigpress") . '" /></span> ' . __("or", "gigpress") . ' <a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=gigpress-artists' . $url_args . '">' . __("cancel", "gigpress") . '</a>'; ?>

			<h3><?php _e("Edit this artist", "gigpress"); ?></h3>
		
			<form method="post" action="<?php echo get_bloginfo('wpurl')."/wp-admin/admin.php?page=gigpress-artists" . $url_args; ?>">
			<input type="hidden" name="gpaction" value="update" />
			<input type="hidden" name="artist_id" value="<?php echo $artist_id; ?>" />
		
		<?php
		} else {
		?>
		
			<div id="message" class="error fade"><p><?php _e("Sorry, but we had trouble loading that artist for editing.", "gigpress"); ?></p></div>	
			
			<h3><?php _e("Add an artist", "gigpress"); ?></h3>
		
		<?php				
		}
		
	} else {
		
		$artist = array();
		$submit = '<span class="submit"><input type="submit" name="Submit" class="button-primary" value="' .  __("Add artist", "gigpress") . '" /></span>'; ?>

		<h3><?php _e("Add an artist", "gigpress"); ?></h3>
		
		<form method="post" action="<?php echo get_bloginfo('wpurl')."/wp-admin/admin.php?page=gigpress-artists" . $url_args; ?>">
		<input type="hidden" name="gpaction" value="add" />	
	
	<?php
	}
		wp_nonce_field('gigpress-action') ?>
		
		<table class="form-table gp-table">
			<tr>
				<th scope="row"><label for="artist_name"><?php _e("Artist name", "gigpress"); ?>:</label></th>
				<td>
					<input name="artist_name" id="artist_name" type="text" size="48" value="<?php if(isset($artist->artist_name)) echo gigpress_db_out($artist->artist_name); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="artist_url"><?php _e("Artist URL", "gigpress"); ?>:</label></th>
				<td>
					<input name="artist_url" id="artist_url" type="text" size="48" value="<?php if(isset($artist->artist_url)) echo gigpress_db_out($artist->artist_url); ?>" />
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<?php echo $submit; ?>
				</td>
			</tr>					
		</table>
		
		</form>

	<h3><?php _e("All artists", "gigpress"); ?></h3>
	
	<div class="tablenav">
		<div class="alignleft"><p><?php _e("Note that you cannot delete an artist while they have shows in the database.", "gigpress"); ?></p></div>
	<?php
		$artists = fetch_gigpress_artists();
/*		Removed pagination to allow for single-page AJAX reordering. Complaints might being it back?
		if($artists) {
			$pagination_args['page'] = 'gigpress-artists';
			$pagination = gigpress_admin_pagination(count($artists), 20, $pagination_args);
			if($pagination) {
				$artists = array_slice($artists, $pagination['offset'], $pagination['records_per_page']);
				echo $pagination['output'];
			}
		}
*/
	?>
	</div>
	
	<table class="widefat">
		<thead>
			<tr>
				<th scope="col" class="gp-tiny">&nbsp;</th>
				<th scope="col" class="gp-tiny">ID</th>
				<th scope="col"><?php _e("Artist name", "gigpress"); ?></th>
				<th scope="col" class="gp-centre"><?php _e("Number of shows", "gigpress"); ?></th>
				<th class="gp-centre" scope="col"><?php _e("Actions", "gigpress"); ?></th>
			</tr>
		</thead>
		<tbody class="gigpress-artist-sort">
	<?php

		if($artists) {
				
			$i = 0;
			foreach($artists as $artist) {
			
				if($n = $wpdb->get_var("SELECT count(*) FROM ". GIGPRESS_SHOWS ." WHERE show_artist_id = ". $artist->artist_id . " AND show_status != 'deleted'")) {
					$count = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=gigpress-shows&amp;artist_id=' . $artist->artist_id . '">' . $n . '</a>';
				} else {
					$count = 0;
				}

				$i++;
				$style = ($i % 2) ? '' : ' class="alternate"';
				// Print out our rows.
				?>
				<tr<?php echo $style; ?> id="artist_<?php echo $artist->artist_id; ?>">
					<td class="gp-tiny"><img src="<?php echo plugins_url('gigpress/images/sort.png'); ?>" alt="" class="gp-sort-handle" /></td>
					<td class="gp-tiny"><?php echo $artist->artist_id; ?></td>
					<td><?php if(!empty($artist->artist_url)) echo '<a href="'.esc_url($artist->artist_url).'">'; echo wptexturize($artist->artist_name); if(!empty($artist->artist_url)) echo '</a>';?></td>
					<td class="gp-centre"><?php echo $count; ?></td>
					<td class="gp-centre">
						<a href="<?php echo get_bloginfo('wpurl').'/wp-admin/admin.php?page=gigpress-artists&amp;gpaction=edit&amp;artist_id='.$artist->artist_id . $url_args; ?>" class="edit"><?php _e("Edit", "gigpress"); ?></a>
						<?php if(!$count) { ?> | <a href="<?php echo wp_nonce_url(get_bloginfo('wpurl').'/wp-admin/admin.php?page=gigpress-artists&amp;gpaction=delete&amp;artist_id='.$artist->artist_id . $url_args, 'gigpress-action'); ?>" class="delete"><?php _e("Delete", "gigpress"); ?></a><?php } ?>
					</td>
				</tr>
				<?php }
		} else {

			// We don't have any artists, so let's say so
			?>
			<tr><td colspan="5"><strong><?php _e("No artists in the database", "gigpress"); ?></strong></td></tr>
	<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<th scope="col" class="gp-tiny">&nbsp;</th>
				<th scope="col" class="gp-tiny">ID</th>
				<th scope="col"><?php _e("Artist name", "gigpress"); ?></th>
				<th scope="col" class="gp-centre"><?php _e("Number of shows", "gigpress"); ?></th>
				<th class="gp-centre" scope="col"><?php _e("Actions", "gigpress"); ?></th>
			</tr>
		</tfoot>
	</table>

<?php if(isset($pagination)) : ?>
	<div class="tablenav">
	<?php // echo $pagination['output']; ?>
	</div>
<?php endif; ?>
	
	<div id="artist-sort-update"></div>	
	
	</div>
<?php }
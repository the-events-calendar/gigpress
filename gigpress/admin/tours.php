<?php

function gigpress_tours() {

	global $wpdb;
	
	if(isset($_POST['gpaction']) && $_POST['gpaction'] == "add") {
		require_once('handlers.php');
		$result = gigpress_add_tour();		
	}
	
	if(isset($_POST['gpaction']) && $_POST['gpaction'] == "update") {
		require_once('handlers.php');
		$result = gigpress_update_tour();
	}
	
	if(isset($_GET['gpaction']) && $_GET['gpaction'] == "delete") {
		require_once('handlers.php');
		gigpress_delete_tour();		
	}

	if(isset($_GET['gpaction']) && $_GET['gpaction'] == "undo") {
		require_once('handlers.php');
		gigpress_undo('tour');		
	}

	$url_args = (isset($_GET['gp-page'])) ? '&amp;gp-page=' . $_GET['gp-page'] : '';	

	?>

	<div class="wrap gigpress gp-tours">

	<?php screen_icon('gigpress'); ?>		
	<h2><?php _e("Tours", "gigpress"); ?></h2>
	
	<p><?php _e("A tour is simply a named collection of shows that you want to group together.", "gigpress"); ?></p>
	
	<?php
	if(isset($_GET['gpaction']) && $_GET['gpaction'] == "edit" || isset($result) && isset($result['editing']) ) {
	
		// Load the previous show info into the edit form, and so forth 
	
		$tour_id = (isset($_REQUEST['tour_id'])) ? $wpdb->prepare('%d', $_REQUEST['tour_id']) : '';

		$tour_name = gigpress_db_out($wpdb->get_var("SELECT tour_name from ". GIGPRESS_TOURS ." WHERE tour_id = ". $tour_id));
		
		if($tour_name) {
			
			$submit = '<span class="submit"><input type="submit" name="Submit" class="button-primary" value="' .  __("Update tour", "gigpress") . '" /></span> ' . __("or", "gigpress") . ' <a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=gigpress-tours' . $url_args . '">' . __("cancel", "gigpress") . '</a>'; ?>
			
			<h3><?php _e("Edit this tour", "gigpress"); ?></h3>
		
			<form method="post" action="<?php bloginfo('wpurl') ?>/wp-admin/admin.php?page=gigpress-tours<?php echo $url_args; ?>">
				<input type="hidden" name="tour_id" value="<?php echo $tour_id; ?>" />			
				<input type="hidden" name="gpaction" value="update" />
			
		<?php
		} else {
		?>
			<div id="message" class="error fade"><p><?php _e("Sorry, but we had trouble loading that tour for editing.", "gigpress"); ?></p></div>	

			<h3><?php _e("Add a tour", "gigpress"); ?></h3>
			
		<?php
		}
	
	} else {
		
		$tour_name = '';
		$submit = '<span class="submit"><input type="submit" name="Submit" class="button-primary" value="' .  __("Add tour", "gigpress") . '" /></span>'; ?>
			
		<h3><?php _e("Add a tour", "gigpress"); ?></h3>

		<form method="post" action="<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=gigpress-tours<?php echo $url_args; ?>">
			<input type="hidden" name="gpaction" value="add" />
	<?php 
	}
			wp_nonce_field('gigpress-action'); ?>

			<table class="form-table gp-table">
				<tr>
					<th scope="row"><label for="tour_name"><?php _e("Tour name", "gigpress"); ?>:</label></th>
					<td><input name="tour_name" id="tour_name" type="text" size="48" value="<?php echo $tour_name; ?>" /> &nbsp; <?php echo $submit; ?></td>
				</tr>
			</table>		
		</form>

		<h3><?php _e("All tours", "gigpress"); ?></h3>

	<div class="tablenav">
		<div class="alignleft">
			<p><?php _e("Note that deleting a tour will <strong>NOT</strong> delete the shows associated with that tour.", "gigpress"); ?></p>
		</div>
		<?php // Get all tours from the DB
			$tours = fetch_gigpress_tours();
			
			if($tours) {
				$pagination_args['page'] = 'gigpress-tours';
				$pagination = gigpress_admin_pagination(count($tours), 20, $pagination_args);
				if($pagination) {
					$tours = array_slice($tours, $pagination['offset'], $pagination['records_per_page']);
					echo $pagination['output'];
				}
			}
		?>
	</div>
	
	<table class="widefat">
		<thead>
			<tr>
				<th scope="col" class="gp-tiny">ID</th>
				<th scope="col"><?php _e("Tour name", "gigpress"); ?></th>
				<th scope="col" class="gp-centre"><?php _e("Number of shows", "gigpress"); ?></th>
				<th class="gp-centre" scope="col"><?php _e("Actions", "gigpress"); ?></th>
			</tr>
		</thead>
		<tbody>
	<?php if($tours) {	
			$i = 0;
			foreach($tours as $tour) {
				if($n = $wpdb->get_var("SELECT count(*) FROM ". GIGPRESS_SHOWS ." WHERE show_tour_id = ". $tour->tour_id ." AND show_status != 'deleted'")) {
					$count = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=gigpress-shows&amp;tour_id=' . $tour->tour_id . '">' . $n . '</a>';
				} else {
					$count = 0;
				}

				$i++;
				$style = ($i % 2) ? '' : ' class="alternate"';
				?>

				<tr<?php echo $style; ?>>
					<td class="gp-tiny"><?php echo $tour->tour_id; ?></td>
					<td><?php echo wptexturize($tour->tour_name); ?></td>
					<td class="gp-centre"><?php echo $count; ?></td>
					<td class="gp-centre">
						<a href="<?php echo get_bloginfo('wpurl').'/wp-admin/admin.php?page=gigpress-tours&amp;gpaction=edit&amp;tour_id='.$tour->tour_id . $url_args; ?>" class="edit"><?php _e("Edit", "gigpress"); ?></a> | <a href="<?php echo wp_nonce_url(get_bloginfo('wpurl').'/wp-admin/admin.php?page=gigpress-tours&amp;gpaction=delete&amp;tour_id='.$tour->tour_id . $url_args, 'gigpress-action'); ?>" class="delete"><?php _e("Delete", "gigpress"); ?></a>
					</td>
				</tr>
				
			<?php
			}
		
		} else { ?>
			<tr><td colspan="4"><strong><?php _e("No tours in the database", "gigpress"); ?></strong></td></tr>
<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<th scope="col" class="gp-tiny">ID</th>
				<th scope="col"><?php _e("Tour name", "gigpress"); ?></th>
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
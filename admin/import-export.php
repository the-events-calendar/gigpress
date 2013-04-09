<?php

function gigpress_import_export() {

	global $wpdb, $gpo;
	
	?>

	<div class="wrap gigpress gp-options">

	<?php screen_icon('gigpress'); ?>			
	<h2><?php _e("Import/Export", "gigpress"); ?></h2>
	
	<div class="gp-import-panel">
	
		<h3>Import</h3>
		
		<p><?php _e("Upload a CSV (comma-separated values) file to import into your GigPress database.", "gigpress"); ?> <a href="http://gigpress.com/docs/#import"><strong><?php _e("Please review the formatting specifications to save yourself headaches!", "gigpress"); ?></strong></a></p>
		
		<div class="form-wrap">
			<form action="" enctype="multipart/form-data" method="POST">
				<div>
					<?php wp_nonce_field('gigpress-action') ?>
					<input type="hidden" name="gpaction" value="import" />
					<input type="file" name="gp_import" />
					<p>
						<label><input type="checkbox" name="include_related" value="y" /> <?php _e("Include Related Post associations?", "gigpress"); ?></label>
					</p>
					<input type="submit" class="button" value="<?php _e("Upload CSV", "gigpress"); ?>" />
				</div>
			</form>
		</div>
		
	
		<?php	
			if(isset($_POST['gpaction']) && $_POST['gpaction'] == "import") {
				require_once('handlers.php');
				gigpress_import();
			}
		?>

	</div>
	
	<div class="gp-export-panel">
		
		<h3>Export</h3>
		
		<p><?php _e("Download your complete show database as a CSV (comma-separated values) file, compatible with programs such as Microsoft Excel. This file is also suitable to import into another GigPress installation.", "gigpress"); ?></p>
		
		<form action="admin-post.php" method="post">
			<div>
				<?php wp_nonce_field('gigpress-action'); ?>
				<input type="hidden" name="action" value="gigpress_export" />
			</div>
			<div style="margin-bottom:5px;">
				<select name="artist_id">
					<option value="-1"><?php _e("Export all artists", "gigpress"); ?></option>
				<?php $artistdata = fetch_gigpress_artists();
				if($artistdata) {
					foreach($artistdata as $artist) {
						echo('<option value="' . $artist->artist_id . '">' . gigpress_db_out($artist->artist_name) . '</option>');
					}
				} else {
					echo('<option value="-1">' . __("No artists in the database", "gigpress") . '</option>');
				}
				?>
				</select>
			</div>
			<div style="margin-bottom:5px;">	
				<select name="tour_id">
					<option value="-1"><?php _e("Export all tours", "gigpress"); ?></option>
				<?php $tourdata = fetch_gigpress_tours();
				if($tourdata) {
					foreach($tourdata as $tour) {
						echo('<option value="' . $tour->tour_id . '">' . gigpress_db_out($tour->tour_name) . '</option>');
					}
				} else {
					echo('<option value="-1">' . __("No tours in the database", "gigpress") . '</option>');
				}
				?>
				</select>
			</div>
			<div style="margin-bottom:5px;">
				<select name="scope">
					<option value="-1"><?php _e("Export all dates", "gigpress"); ?></option>
					<option value="upcoming"><?php _e("Export upcoming dates", "gigpress"); ?></option>
					<option value="past"><?php _e("Export past dates", "gigpress"); ?></option>
				</select>
			</div>
			<div>							
				<input type="submit" value="<?php _e("Download CSV", "gigpress"); ?>" class="button-secondary" />
			</div>
		</form>
	
	</div>

	</div>
<?php }
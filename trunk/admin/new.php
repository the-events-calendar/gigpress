<?php

function gigpress_add() {

	global $wpdb, $wp_locale;
		
	if(isset($_POST['gpaction']) && $_POST['gpaction'] == "add") {
		// This is for when we've just POST-ed a new show ...
		require_once('handlers.php');
		$result = gigpress_add_show();
	}

	if(isset($_POST['gpaction']) && $_POST['gpaction'] == "update") {
		require_once('handlers.php');
		$result = gigpress_update_show();
	}

	$gpo = get_option('gigpress_settings');
		
	// If they're done with the welcome message, kill it
	if(isset($_GET['gpaction']) && $_GET['gpaction'] == "killwelcome") {
		$gpo['welcome'] = "no";
		update_option('gigpress_settings', $gpo);
		$gpo = get_option('gigpress_settings');
	}
	
	// If the welcome message is to be displayed, then do so
	if($gpo['welcome'] == "yes") { ?>
	
		<div id="message" class="updated">
			<p>
				<?php _e("<strong>Welcome to GigPress!</strong> Get started by adding your first show below. To display your shows, simply add the", "gigpress"); ?> [gigpress_shows] <?php _e("shortcode to any page or post.", "gigpress"); ?>
				<?php _e("Questions?  Please check out the", "gigpress"); ?> <a href="http://gigpress.com/docs"><?php _e("documentation", "gigpress"); ?></a> <?php _e("and", "gigpress"); ?> <a href="http://gigpress.com/faq"><?php _e("FAQ", "gigpress"); ?></a> <?php _e("on the GigPress website. Enjoy!", "gigpress"); ?> <small>(<a href="<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=gigpress/gigpress.php&amp;gpaction=killwelcome"><?php _e("Don't show this again", "gigpress"); ?>.</a>)</small>
			</p>
		</div>
		
	<?php } ?>
	
	<div class="wrap gigpress">
	
	<?php
		
		// Setup months
		$gp_months = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');		
		
		// Sanitize the show_id if we're editing or fixing errors
		$show_id = (isset($_REQUEST['show_id'])) ? $wpdb->prepare('%d', $_REQUEST['show_id']) : '';
	
		// If the handler returned an array ($result), it means there were errors.
		// This takes precedence over any other conditionals below.
		
		if(isset($result)) {
		
			$mm = $_POST['gp_mm'];
			$dd = $_POST['gp_dd'];
			$yy = $_POST['gp_yy'];
			
			$hh = $_POST['gp_hh'];
			$min = $_POST['gp_min'];
				
			$exp_mm = $_POST['exp_mm'];
			$exp_dd = $_POST['exp_dd'];
			$exp_yy = $_POST['exp_yy'];
			
			$show_multi = (isset($_POST['show_multi']) && !empty($_POST['show_multi'])) ? 1 : FALSE;
			$show_artist_id = $_POST['show_artist_id'];
			$show_venue_id = $_POST['show_venue_id'];
			$new_artist_name = gigpress_db_out($_POST['artist_name']);
			$artist_url = gigpress_db_out($_POST['artist_url']);
			$new_venue_name = gigpress_db_out($_POST['venue_name']);
			$venue_address = gigpress_db_out($_POST['venue_address']);
			$new_venue_city = gigpress_db_out($_POST['venue_city']);
			$venue_state = gigpress_db_out($_POST['venue_state']);		
			$venue_postal_code = gigpress_db_out($_POST['venue_postal_code']);					
			$venue_country = gigpress_db_out($_POST['venue_country']);
			$venue_url = gigpress_db_out($_POST['venue_url']);
			$venue_phone = gigpress_db_out($_POST['venue_phone']);
			$new_tour_name = gigpress_db_out($_POST['tour_name']);
			$show_ages = gigpress_db_out($_POST['show_ages']);
			$show_tix_url = gigpress_db_out($_POST['show_tix_url']);
			$show_tix_phone = gigpress_db_out($_POST['show_tix_phone']);
			$show_notes = gigpress_db_out($_POST['show_notes']);
			$show_price = gigpress_db_out($_POST['show_price']);
			$show_external_url = gigpress_db_out($_POST['show_external_url']);
			$show_tour_id = $_POST['show_tour_id'];
			$show_related = $_POST['show_related'];
			$show_related_title = gigpress_db_out($_POST['show_related_title']);
			$show_related_date = $_POST['show_related_date'];
			$show_status = $_POST['show_status'];
			
			$have_data = TRUE;
		
		} else if (isset($_GET['gpaction']) && ($_GET['gpaction'] == "edit" || $_GET['gpaction'] == "copy")) {
	
			// We're about to edit an existing show ...
			// Load the previous show info into the edit form, and so forth
			
			$show_edit = $wpdb->get_results("
			SELECT * from ". GIGPRESS_SHOWS ." WHERE show_id = ". $show_id ." LIMIT 1
			");
				if($show_edit) {
					// We got the goods from the DB
					foreach($show_edit as $show) {
						
						$show_date = explode('-', $show->show_date);
							$mm = $show_date[1];
							$dd = $show_date[2];
							$yy = $show_date[0];
						
						$show_time = explode(':', $show->show_time);
							$hh = $show_time[0];
							$min = $show_time[1];
							$ss = $show_time[2];
							
							if($ss == "01") {
								$hh = "na";
								$min = "na";
							}
							
						$show_expire = explode('-', $show->show_expire);
							$exp_mm = $show_expire[1];
							$exp_dd = $show_expire[2];
							$exp_yy = $show_expire[0];
						
						$show_multi = $show->show_multi;
						$show_artist_id = $show->show_artist_id;
						$show_venue_id = $show->show_venue_id;
						$show_ages = gigpress_db_out($show->show_ages);
						$show_tix_url = gigpress_db_out($show->show_tix_url);
						$show_tix_phone = gigpress_db_out($show->show_tix_phone);
						$show_notes = gigpress_db_out($show->show_notes);
						$show_price = gigpress_db_out($show->show_price);
						$show_external_url = gigpress_db_out($show->show_external_url);
						$show_tour_id = $show->show_tour_id;
						$show_related = $show->show_related;
						$show_related_title = $gpo['default_title'];	
						$show_related_date = $gpo['related_date'];
						$show_status = $show->show_status;			
					}
					
					$have_data = TRUE;
					
				} else {
				
					$have_data == FALSE;
					$load_error = '<div id="message" class="error fade"><p>' . __("Sorry, but we had trouble loading that show for editing.", "gigpress") . '</div>';
				
				}
		}
		
		if(!isset($have_data)) {
	
			// We're adding a new show, so get the defaults
			
			$show_date = explode('-', $gpo['default_date']);
				$mm = $show_date[1];
				$dd = $show_date[2];
				$yy = $show_date[0];
			
			$show_time = explode(':', $gpo['default_time']);
				$hh = $show_time[0];
				$min = $show_time[1];
				$ss = $show_time[2];
				
				if($ss == "01") {
					$hh = "na";
					$min = "na";
				}
				
			$show_expire = explode('-', $gpo['default_date']);
				$exp_mm = $show_expire[1];
				$exp_dd = $show_expire[2];
				$exp_yy = $show_expire[0];
			
			$show_multi = FALSE;	
			$show_artist_id = (isset($gpo['default_artist'])) ? $gpo['default_artist'] : '';
			$show_venue_id = (isset($gpo['default_venue'])) ? $gpo['default_venue'] : '';
			$show_ages = (isset($gpo['default_ages'])) ? $gpo['default_ages'] : '';
			$show_tour_id = $gpo['default_tour'];
			$venue_country = $gpo['default_country'];
			$show_related_title = $gpo['default_title'];
			$show_related_date = $gpo['related_date'];
		}
	
		screen_icon('gigpress');	
		
		// We're editing a show
		if(isset($_GET['gpaction']) && $_GET['gpaction'] == "edit" || (isset($result['editing']))) { ?>
		
			<h2><?php _e("Edit this show", "gigpress"); ?></h2>
		
			<form method="post" action="<?php bloginfo('wpurl') ?>/wp-admin/admin.php?page=gigpress/gigpress.php">
				<?php wp_nonce_field('gigpress-action') ?>
				<input type="hidden" name="gpaction" value="update" />
				<input type="hidden" name="show_id" value="<?php echo $show_id; ?>" />
		
		<?php } else { // We're adding a new show ?>
		
			<h2><?php _e("Add a show", "gigpress"); ?></h2>
			
			<?php if(isset($load_error)) echo $load_error; ?>
					
			<form method="post" action="<?php bloginfo('wpurl') ?>/wp-admin/admin.php?page=gigpress/gigpress.php">
				<?php wp_nonce_field('gigpress-action') ?>
				<input type="hidden" name="gpaction" value="add" />
				<input type="hidden" name="show_status" value="active" />
				
		<?php } ?>
			
			<table class="form-table gp-table" cellspacing="0">
			<tbody>
			  <tr>
				<th scope="row"><label for="gp_mm"><?php _e("Date", "gigpress") ?>:<span class="gp-required">*</span></label></th>
					<td>
					<?php if(isset($result['show_date'])) echo('<span class="gigpress-error">'); ?>				
					<select name="gp_mm" id="gp_mm">
					<?php foreach($gp_months as $month) : ?>
						<option value="<?php echo $month; ?>"<?php if($mm == $month) : ?> selected="selected"<?php endif; ?>>
							<?php echo $wp_locale->get_month($month); ?>
						</option>
					<?php endforeach; ?>
					</select>
					  <select name="gp_dd" id="gp_dd">
					  	<?php for($i = 1; $i <= 31; $i++) {
					  	$i = ($i < 10) ? '0' . $i : $i;
					  	echo('<option value="' . $i . '"');
					  	if($dd == $i) echo(' selected="selected"');
					  	echo('>' . $i . '</option>');
					  	} ?>
					  </select>
					  
					  <select name="gp_yy" id="gp_yy">
					  	<?php for($i = 1900; $i <= 2050; $i++) {
					  	echo('<option value="' . $i . '"');
					  	if($yy == $i) echo(' selected="selected"');
					  	echo('>' . $i . '</option>');
					  	} ?>
					</select>
					<?php if(isset($result['show_date'])) echo('</span>'); ?>
				
					&nbsp; <?php _e("at", "gigpress"); ?> &nbsp; 
					
					<?php if(!empty($gpo['alternate_clock'])) { ?>
					<select name="gp_hh" id="gp_hh" class="twentyfour">
						<option value="na"<?php if($hh == "na") echo(' selected="selected"'); ?>>--</option>
						<option value="00"<?php if($hh == "00") echo(' selected="selected"'); ?>>00</option>
						<option value="01"<?php if($hh == "01") echo(' selected="selected"'); ?>>01</option>
						<option value="02"<?php if($hh == "02") echo(' selected="selected"'); ?>>02</option>
						<option value="03"<?php if($hh == "03") echo(' selected="selected"'); ?>>03</option>
						<option value="04"<?php if($hh == "04") echo(' selected="selected"'); ?>>04</option>
						<option value="05"<?php if($hh == "05") echo(' selected="selected"'); ?>>05</option>
						<option value="06"<?php if($hh == "06") echo(' selected="selected"'); ?>>06</option>
						<option value="07"<?php if($hh == "07") echo(' selected="selected"'); ?>>07</option>
						<option value="08"<?php if($hh == "08") echo(' selected="selected"'); ?>>08</option>
						<option value="09"<?php if($hh == "09") echo(' selected="selected"'); ?>>09</option>
						<option value="10"<?php if($hh == "10") echo(' selected="selected"'); ?>>10</option>
						<option value="11"<?php if($hh == "11") echo(' selected="selected"'); ?>>11</option>
						<option value="12"<?php if($hh == "12") echo(' selected="selected"'); ?>>12</option>
						<option value="13"<?php if($hh == "13") echo(' selected="selected"'); ?>>13</option>
						<option value="14"<?php if($hh == "14") echo(' selected="selected"'); ?>>14</option>
						<option value="15"<?php if($hh == "15") echo(' selected="selected"'); ?>>15</option>
						<option value="16"<?php if($hh == "16") echo(' selected="selected"'); ?>>16</option>
						<option value="17"<?php if($hh == "17") echo(' selected="selected"'); ?>>17</option>
						<option value="18"<?php if($hh == "18") echo(' selected="selected"'); ?>>18</option>
						<option value="19"<?php if($hh == "19") echo(' selected="selected"'); ?>>19</option>
						<option value="20"<?php if($hh == "20") echo(' selected="selected"'); ?>>20</option>
						<option value="21"<?php if($hh == "21") echo(' selected="selected"'); ?>>21</option>
						<option value="22"<?php if($hh == "22") echo(' selected="selected"'); ?>>22</option>
						<option value="23"<?php if($hh == "23") echo(' selected="selected"'); ?>>23</option>
					</select>
					<?php } else { ?>
					<select name="gp_hh" id="gp_hh" class="twelve">
						<optgroup label="<?php _e("None", "gigpress"); ?>">
						  <option value="na"<?php if($hh == "na") echo(' selected="selected"'); ?>>--</option>
						</optgroup>
						<optgroup id="am" label="AM">
						  <option value="00"<?php if($hh == "00") echo(' selected="selected"'); ?>>12</option>
						  <option value="01"<?php if($hh == "01") echo(' selected="selected"'); ?>>01</option>
						  <option value="02"<?php if($hh == "02") echo(' selected="selected"'); ?>>02</option>
						  <option value="03"<?php if($hh == "03") echo(' selected="selected"'); ?>>03</option>
						  <option value="04"<?php if($hh == "04") echo(' selected="selected"'); ?>>04</option>
						  <option value="05"<?php if($hh == "05") echo(' selected="selected"'); ?>>05</option>
						  <option value="06"<?php if($hh == "06") echo(' selected="selected"'); ?>>06</option>
						  <option value="07"<?php if($hh == "07") echo(' selected="selected"'); ?>>07</option>
						  <option value="08"<?php if($hh == "08") echo(' selected="selected"'); ?>>08</option>
						  <option value="09"<?php if($hh == "09") echo(' selected="selected"'); ?>>09</option>
						  <option value="10"<?php if($hh == "10") echo(' selected="selected"'); ?>>10</option>
						  <option value="11"<?php if($hh == "11") echo(' selected="selected"'); ?>>11</option>
						</optgroup>
						<optgroup id="pm" label="PM">
						  <option value="12"<?php if($hh == "12") echo(' selected="selected"'); ?>>12</option>
						  <option value="13"<?php if($hh == "13") echo(' selected="selected"'); ?>>01</option>
						  <option value="14"<?php if($hh == "14") echo(' selected="selected"'); ?>>02</option>
						  <option value="15"<?php if($hh == "15") echo(' selected="selected"'); ?>>03</option>
						  <option value="16"<?php if($hh == "16") echo(' selected="selected"'); ?>>04</option>
						  <option value="17"<?php if($hh == "17") echo(' selected="selected"'); ?>>05</option>
						  <option value="18"<?php if($hh == "18") echo(' selected="selected"'); ?>>06</option>
						  <option value="19"<?php if($hh == "19") echo(' selected="selected"'); ?>>07</option>
						  <option value="20"<?php if($hh == "20") echo(' selected="selected"'); ?>>08</option>
						  <option value="21"<?php if($hh == "21") echo(' selected="selected"'); ?>>09</option>
						  <option value="22"<?php if($hh == "22") echo(' selected="selected"'); ?>>10</option>
						  <option value="23"<?php if($hh == "23") echo(' selected="selected"'); ?>>11</option>
						</optgroup>
					</select>
					<?php } ?>
					<select name="gp_min" id="gp_min">
						  	<option value="na"<?php if($min == "na") echo(' selected="selected"'); ?>>--</option>
							<option value="00"<?php if($min == "00") echo(' selected="selected"'); ?>>00</option>
							<option value="05"<?php if($min == "05") echo(' selected="selected"'); ?>>05</option>
							<option value="10"<?php if($min == "10") echo(' selected="selected"'); ?>>10</option>
							<option value="15"<?php if($min == "15") echo(' selected="selected"'); ?>>15</option>
							<option value="20"<?php if($min == "20") echo(' selected="selected"'); ?>>20</option>
							<option value="25"<?php if($min == "25") echo(' selected="selected"'); ?>>25</option>
							<option value="30"<?php if($min == "30") echo(' selected="selected"'); ?>>30</option>
							<option value="35"<?php if($min == "35") echo(' selected="selected"'); ?>>35</option>
							<option value="40"<?php if($min == "40") echo(' selected="selected"'); ?>>40</option>
							<option value="45"<?php if($min == "45") echo(' selected="selected"'); ?>>45</option>
							<option value="50"<?php if($min == "50") echo(' selected="selected"'); ?>>50</option>
							<option value="55"<?php if($min == "55") echo(' selected="selected"'); ?>>55</option>
					</select>
						<span id="ampm">&nbsp;</span>
						<p><span class="description">&nbsp;<label><input type="checkbox" value="1"<?php if(!empty($show_multi)) echo(' checked="checked"'); ?> id="show_multi" name="show_multi" />&nbsp;<?php _e("This is a multi-day event", "gigpress"); ?></label></span></p>
						</td>
					</tr>
					<!-- For multiple-day events -->
					<tr id="expire"<?php if(empty($show_multi)) echo(' class="gigpress-inactive"'); ?>>
						<th scope="row"><label for="exp_mm"><?php _e("End date", "gigpress") ?>:</label></th>
						<td>
						<?php if(isset($result['expire_date'])) echo('<span class="gigpress-error">'); ?>
						<select name="exp_mm" id="exp_mm">
						<?php foreach($gp_months as $month) : ?>
							<option value="<?php echo $month; ?>"<?php if($exp_mm == $month) : ?> selected="selected"<?php endif; ?>>
								<?php echo $wp_locale->get_month($month); ?>
							</option>
						<?php endforeach; ?>
						</select>
						
						  <select name="exp_dd" id="exp_dd">
						  	<?php for($i = 1; $i <= 31; $i++) {
						  	$i = ($i < 10) ? '0' . $i : $i;
						  	echo('<option value="' . $i . '"');
						  	if($exp_dd == $i) echo(' selected="selected"');
						  	echo('>' . $i . '</option>');
						  	} ?>
						  </select>
						  
						  <select name="exp_yy" id="exp_yy">
						  	<?php for($i = 1900; $i <= 2050; $i++) {
						  	echo('<option value="' . $i . '"');
						  	if($exp_yy == $i) echo(' selected="selected"');
						  	echo('>' . $i . '</option>');
						  	} ?>
					  	</select>
						<?php if(isset($result['expire_date'])) echo('</span>'); ?>
						</td>
					</tr>
				<tr>
					<th scope="row"><label for="show_artist_id"><?php _e("Artist", "gigpress") ?>:<span class="gp-required">*</span></label></th>
					<td>
					<select name="show_artist_id" id="show_artist_id" class="can-add-new">
						<option value="new"<?php if(isset($show_artist_id) && $show_artist_id == 'new') echo(' selected="selected"'); ?>><?php _e("Add a new artist", "gigpress"); ?></option>
						<option value="">------------------</option>
					  	<?php $artists = fetch_gigpress_artists();
							if($artists != FALSE) {
								foreach($artists as $artist) {
									$artist_name = gigpress_db_out($artist->artist_name);
									echo("<option value=\"$artist->artist_id\"");
									if($show_artist_id == $artist->artist_id) echo(' selected="selected"');
									echo(">$artist_name</option>\n\t\t\t");
								}
							} else {
								echo('<option value="0">' . __("No artists in the database", "gigpress") . '</option>');
								$no_artists = true;
							}
						?>
					  </select>
					</td>
				  </tr>
				 </tbody>
				 
				<tbody id="show_artist_id_new" class="gigpress-addition<?php if(!isset($show_artist_id) || (isset($show_artist_id) && $show_artist_id != 'new') && !isset($no_artists)) echo(' gigpress-inactive'); ?>">
				<tr>
					<th scope="row"><label for="artist_name"><?php _e("Artist name", "gigpress"); ?>:<span class="gp-required">*</span></label></th>
					<td><input type="text" size="48" name="artist_name" id="artist_name" value="<?php if(isset($new_artist_name)) echo $new_artist_name; ?>"<?php if(isset($result['artist_name'])) echo(' class="gigpress-error"'); ?> /></td>
				</tr>
				<tr>
					<th scope="row"><label for="artist_url"><?php _e("Artist URL", "gigpress"); ?>:</label></th>
					<td>
						<input name="artist_url" id="artist_url" type="text" size="48" value="<?php if(isset($artist_url)) echo $artist_url; ?>" />
					</td>
				</tr>				
				</tbody>
				
				<tbody>
				<tr>
					<th scope="row"><label for="show_venue_id"><?php _e("Venue", "gigpress") ?>:<span class="gp-required">*</span></label></th>
					<td>
					<select name="show_venue_id" id="show_venue_id" class="can-add-new">
						<option value="new"<?php if(isset($show_venue_id) && $show_venue_id == 'new') echo(' selected="selected"'); ?>><?php _e("Add a new venue", "gigpress"); ?></option>
						<option value="">------------------</option>
						<option value=""<?php if(!isset($show_venue_id) || (isset($show_venue_id) && $show_venue_id == '') ) echo(' selected="selected"'); ?>><?php _e("Select a venue", "gigpress"); ?></option>
						<option value="">------------------</option>
					  	<?php $venues = fetch_gigpress_venues();
							if($venues != FALSE) {
								foreach($venues as $venue) {
									$venue_name = gigpress_db_out($venue->venue_name);
									$venue_city = gigpress_db_out($venue->venue_city);
									if($venue->venue_state) $venue_city .= ', '.gigpress_db_out($venue->venue_state);
									echo("<option value=\"$venue->venue_id\"");
									if(isset($show_venue_id) && $show_venue_id == $venue->venue_id) echo(' selected="selected"');
									echo(">$venue_name ($venue_city)</option>\n\t\t\t");
								}
							} else {
								echo("<option value=\"0\">".__("No venues in the database", "gigpress")."</option>/n/t/t/t");
							}
						?>
					  </select>
					</td>
				  </tr>
				</tbody>
				
				<tbody id="show_venue_id_new" class="gigpress-addition<?php if(!isset($show_venue_id) || (isset($show_venue_id) && $show_venue_id != 'new')) echo(' gigpress-inactive'); ?>">
				  <tr>
					<th scope="row"><label for="venue_name"><?php _e("Venue name", "gigpress") ?>:<span class="gp-required">*</span></label>
					</th>
					<td><input type="text" size="48" name="venue_name" id="venue_name" value="<?php if(isset($new_venue_name)) echo $new_venue_name; ?>"<?php if(isset($result['venue_name'])) echo(' class="gigpress-error"'); ?> /></td>
				  </tr>	
				<tr>
					<th scope="row"><label for="venue_address"><?php _e("Venue address", "gigpress") ?>:</label></th>
					<td><input type="text" size="48" name="venue_address" id="venue_address" value="<?php if(isset($venue_address)) echo $venue_address; ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="venue_city"><?php _e("Venue city", "gigpress") ?>:<span class="gp-required">*</span></label></th>
					<td><input type="text" size="48" name="venue_city" id="venue_city" value="<?php if(isset($new_venue_city)) echo $new_venue_city; ?>"<?php if(isset($result['venue_city'])) echo(' class="gigpress-error"'); ?> class="required" /></td>
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
				</tbody>
				
				<tbody>
				<?php if(isset($_GET['gpaction']) && $_GET['gpaction'] == 'edit' || isset($result['editing'])) { ?>
				<tr>
					<th scope="row"><label for="show_status"><?php _e("Status", "gigpress") ?>:</label></th>
					<td><select name="show_status" id="show_status">
							<option value="active"<?php if($show_status == 'active') echo(' selected="selected"'); ?>><?php _e("Active", "gigpress"); ?></option>
							<option value="soldout"<?php if($show_status == 'soldout') echo(' selected="selected"'); ?>><?php _e("Sold Out", "gigpress"); ?></option>
							<option value="cancelled"<?php if($show_status == 'cancelled') echo(' selected="selected"'); ?>><?php _e("Cancelled", "gigpress"); ?></option>
						</select>
					</td>
				</tr>
				<?php } ?>			
				  <tr>
					<th scope="row"><label for="show_ages"><?php _e("Admittance", "gigpress") ?>:</label></th>
					<td><select name="show_ages" id="show_ages">
					  <option value="Not sure"<?php if(isset($show_ages) && $show_ages == "Not sure") echo(' selected="selected"'); ?>><?php _e("Not sure", "gigpress") ?></option>
					  <?php
					  	$ages = explode('|', $gpo['age_restrictions']);
					  	foreach($ages as $age) {
					  		$age = trim($age);
					  		$selected = (isset($show_ages) && $show_ages == $age) ? ' selected="selected"' : '';
					  		echo('<option value="' . $age . '"' . $selected . '>' . $age . '</option>
					  		');
					  	}	
					  ?>
					  </select>
					</td>
				  </tr>
				  <tr>
					<th scope="row"><label for="show_price"><?php _e("Price", "gigpress") ?>:</label></th>
					<td><input type="text" size="10" name="show_price" id="show_price" value="<?php if(isset($show_price)) echo $show_price; ?>" /> <span class="description">(<?php _e("include currency symbol", "gigpress"); ?>)</span></td>
				  </tr>
				  <tr>
					<th scope="row"><label for="show_tix_url"><?php _e("Ticket URL", "gigpress") ?>:</label></th>
					<td><input type="text" size="48" name="show_tix_url" id="show_tix_url" value="<?php if(isset($show_tix_url)) echo $show_tix_url; ?>" /></td>
				  </tr>
	 				<tr>
					<th scope="row"><label for="show_tix_phone"><?php _e("Ticket phone", "gigpress") ?>:</label></th>
					<td><input type="text" size="48" name="show_tix_phone" id="show_tix_phone" value="<?php if(isset($show_tix_phone)) echo $show_tix_phone; ?>" /></td>
				  </tr>
				  <tr>
					<th scope="row"><label for="show_external_url"><?php _e("External URL", "gigpress") ?>:</label></th>
					<td><input type="text" size="48" name="show_external_url" id="show_external_url" value="<?php if(isset($show_external_url)) echo $show_external_url; ?>" /></td>
				  </tr>				  
				  <tr>
					<th scope="row"><label for="show_notes"><?php _e("Notes", "gigpress") ?>:</label></th>
					<td>
						<textarea name="show_notes" id="show_notes" cols="45" rows="5"><?php if(isset($show_notes)) echo $show_notes; ?></textarea><br />
						<span class="description"><?php _e("Use this space to list other bands, 'presented by' info, etc", "gigpress"); ?></span>
					</td>
				  </tr>
				  <tr>
					<th scope="row"><label for="show_tour_id"><?php _e("Part of a tour?", "gigpress"); ?></label></th>
					<td>
						<select name="show_tour_id" id="show_tour_id" class="can-add-new">
						<option value="0"><?php _e("No", "gigpress"); ?></option>
						<option value="0">------------------</option>
						<option value="new"<?php if(isset($show_tour_id) && $show_tour_id == 'new') echo(' selected="selected"'); ?>><?php _e("Add a new tour", "gigpress"); ?></option>
						<option value="0">------------------</option>
					  	<?php $tours = fetch_gigpress_tours();
							if($tours != FALSE) {
								foreach($tours as $tour) {
									$tour_name = gigpress_db_out($tour->tour_name);
									echo("<option value=\"$tour->tour_id\"");
									if(isset($show_tour_id) && $show_tour_id == $tour->tour_id) echo(' selected="selected"');
									echo(">$tour_name</option>\n\t\t\t");
								}
							} else {
								echo("<option value=\"\">".__("No tours in the database", "gigpress")."</option>/n/t/t/t");
							}
						?>
					  </select>
					</td>
				  </tr>
			</tbody>
			
			<tbody id="show_tour_id_new" class="gigpress-addition<?php if(!isset($show_tour_id) || (isset($show_tour_id) && $show_tour_id != 'new')) echo(' gigpress-inactive'); ?>">
				<tr>
					<th scope="row"><label for="tour_name"><?php _e("Tour name", "gigpress"); ?>:</label></th>
					<td><input type="text" size="48" name="tour_name" id="tour_name" value="<?php if(isset($new_tour_name)) echo $new_tour_name; ?>"<?php if(isset($result['tour_name'])) echo(' class="gigpress-error"'); ?> /></td>
				</tr>
			</tbody>	  
			
			<tbody>
			<tr>
					<th scope="row"><label for="show_related"><?php _e("Related post", "gigpress") ?>:</label></th>
					<td>
						<select name="show_related" id="show_related" class="can-add-new">
					  		<option value="0"><?php _e("None", "gigpress"); ?></option>
							<option value="0">------------------</option>
					  		<option value="new"<?php if( ( (isset($show_related) && $show_related !== "0") || (!isset($show_related) ) && isset($gpo['autocreate_post']) && $gpo['autocreate_post'] == "1") || (isset($show_related) && $show_related == 'new') ) echo(' selected="selected"'); ?>><?php _e("Add a new post", "gigpress") ?></option>
							<option value="0">------------------</option>
							
					  	<?php 
					  	$entries = $wpdb->get_results("SELECT p.ID, p.post_title FROM " . $wpdb->prefix . "posts p WHERE (p.post_status = 'publish' OR p.post_status = 'future') AND p.post_type != 'page' ORDER BY p.post_date DESC LIMIT 500", ARRAY_A);
					  	if($entries != FALSE) {				  	
							foreach($entries as $entry) { ?>
								<option value="<?php echo $entry['ID']; ?>"<?php if(isset($show_related) && $entry['ID'] == $show_related) { echo(' selected="selected"'); $found_related = TRUE; } ?>><?php echo gigpress_db_out($entry['post_title']); ?></option>
						<?php }
						} ?>
						
						<?php if(isset($show_related) && !isset($found_related)) {
							$old_related = $wpdb->get_results("SELECT ID, post_title FROM " . $wpdb->prefix . "posts WHERE ID = ".$wpdb->prepare('%d', $show_related)." LIMIT 1", ARRAY_A);
							if($old_related != FALSE) {
								foreach($old_related as $entry) { ?>
									<option value="<?php echo $entry['ID']; ?>" selected="selected"><?php echo gigpress_db_out($entry['post_title']); ?></option>
						<?php }
							}
						} ?>
					  </select>
					</td>
				  </tr>
				 </tbody>
				 				 
				<tbody id="show_related_new" class="gigpress-addition<?php if( (isset($show_related) && $show_related != 'new') || (empty($show_related) && empty($gpo['autocreate_post'])) ) echo(' gigpress-inactive'); ?>">
				<tr>
					<th scope="row"><label for="show_related_title"><?php _e("Related post title", "gigpress"); ?>:</label></th>
					<td><input type="text" size="48" name="show_related_title" id="show_related_title" value="<?php if(isset($show_related_title)) echo $show_related_title; ?>" /><br />
					<span class="description"><?php _e("Available placeholders:", "gigpress"); ?> <code>%date%</code>, <code>%long_date%</code>, <code>%artist%</code>, <code>%city%</code>, <code>%venue%</code>.</span><br />
					<label><input type="radio" name="show_related_date" value="now"<?php if (isset($show_related_date) && $show_related_date == 'now') echo(' checked="checked"'); ?> /> <?php _e('Publish now', 'gigpress'); ?></label> &nbsp; 
					<label><input type="radio" name="show_related_date" value="show"<?php if (isset($show_related_date) && $show_related_date == 'show') echo(' checked="checked"'); ?> /> <?php _e('Publish on show date', 'gigpress'); ?></label>
					</td>
				</tr>
			</tbody>
			<tbody>  				 
				<tr>
					<td>&nbsp;</td>
					<td>
				<?php if(isset($_GET['gpaction']) && $_GET['gpaction'] == "edit" || isset($result['editing'])) { ?>
					<span class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php _e("Update show", "gigpress") ?>" /></span> <?php _e("or", "gigpress"); ?> <a href="<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=gigpress-shows"><?php _e("cancel", "gigpress"); ?></a>
				
				<?php } else { ?>
				
					<span class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php _e("Add show", "gigpress") ?>" /></span>
				
				<?php } ?>
				</td>
				</tr>
				</tbody>
			</table>		
		</form>
	</div>
	<?php unset($result);
}
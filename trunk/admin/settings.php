<?php

function gigpress_settings() {

	global $wpdb, $gpo;
	
	// This gives us the magic fading menu when we update.  Yes - magic.
	require(ABSPATH . 'wp-admin/options-head.php');
	
	?>

	<div class="wrap gigpress gp-options">

	<?php screen_icon('gigpress'); ?>			
	<h2><?php _e("Settings", "gigpress"); ?></h2>
	
	<form method="post" action="options.php">
	
	<table class="gp-table form-table">

		<tr>
			<th scope="row"><?php _e("Full URL to your 'Upcoming Shows' page", "gigpress") ?>:</th>
			<td>
				<input type="text" size="48" name="gigpress_settings[shows_page]" value="<?php echo $gpo['shows_page']; ?>" />				</td>
		</tr>	
		<tr>
			<th scope="row"><?php _e("No upcoming shows message", "gigpress") ?>:</th>
			<td>
				<input type="text" name="gigpress_settings[noupcoming]" size="48" value="<?php echo $gpo['noupcoming']; ?>" />
			</td>
		</tr>	
		<tr>
			<th scope="row"><?php _e("No past shows message", "gigpress") ?>:</th>
			<td>
				<input type="text" name="gigpress_settings[nopast]" size="48" value="<?php echo $gpo['nopast']; ?>" />
			</td>
		</tr>

		<tr>
			<th scope="row"><?php _e("User level required to use GigPress", "gigpress") ?>:</th>
			<td>
				<select name="gigpress_settings[user_level]">
					<option value="activate_plugins"<?php if ($gpo['user_level'] == 'activate_plugins') echo (' selected="selected"'); ?>><?php _e("Administrator", "gigpress"); ?></option>
					<option value="edit_published_posts"<?php if ($gpo['user_level'] == 'edit_published_posts') echo (' selected="selected"'); ?>><?php _e("Editor", "gigpress"); ?></option>
					<option value="publish_posts"<?php if ($gpo['user_level'] == 'publish_posts') echo (' selected="selected"'); ?>><?php _e("Author", "gigpress"); ?></option>
					<option value="edit_posts"<?php if ($gpo['user_level'] == 'edit_posts') echo (' selected="selected"'); ?>><?php _e("Contributor", "gigpress"); ?></option>
				</select>
			</td>
		</tr>
		
		<tr>
			<th scope="row"><?php _e("Short date format", "gigpress") ?>:</th>
			<td>
				<input type="text" name="gigpress_settings[date_format]" value="<?php echo $gpo['date_format']; ?>" />
				<span><?php _e("Output", "gigpress") ?>: <strong><?php echo mysql2date($gpo['date_format'], current_time('mysql')); ?></strong></span>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e("Long date format", "gigpress") ?>:</th>
			<td>
				<input type="text" name="gigpress_settings[date_format_long]" value="<?php echo $gpo['date_format_long']; ?>" />
				<span><?php _e("Output", "gigpress") ?>: <strong><?php echo mysql2date($gpo['date_format_long'], current_time('mysql')); ?></strong></span>
			</td>
		</tr>			
		<tr>
			<th scope="row"><?php _e("Time format", "gigpress") ?>:</th>
			<td>
				<input type="text" name="gigpress_settings[time_format]" value="<?php echo $gpo['time_format']; ?>" /> 
				<span><?php _e("Output", "gigpress") ?>: <strong><?php echo gmdate($gpo['time_format'], current_time('timestamp')); ?></strong></span><br />
				<a href="http://codex.wordpress.org/Formatting_Date_and_Time"><?php _e("Here's some documentation on date and time formatting", "gigpress") ?></a>.<br />
				<label><input type="checkbox" name="gigpress_settings[alternate_clock]" value="1"<?php if(!empty($gpo['alternate_clock'])) echo(' checked="checked"'); ?> />&nbsp;<?php _e("I use a 24 hour clock", "gigpress"); ?></label><br />
			</td>
		</tr>	
		<tr>
			<th scope="row"><?php _e("Artist label", "gigpress") ?>:</th>
			<td>
				<input type="text" name="gigpress_settings[artist_label]" size="48" value="<?php echo $gpo['artist_label']; ?>" />
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e("Tour label", "gigpress") ?>:</th>
			<td>
				<input type="text" name="gigpress_settings[tour_label]" size="48" value="<?php echo $gpo['tour_label']; ?>" />
			</td>
		</tr>				
		<tr>
			<th scope="row"><?php _e("External link label", "gigpress") ?>:</th>
			<td>
				<input type="text" name="gigpress_settings[external_link_label]" size="48" value="<?php echo $gpo['external_link_label']; ?>" />
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e("Buy tickets label", "gigpress") ?>:</th>
			<td>
				<input type="text" name="gigpress_settings[buy_tickets_label]" size="48" value="<?php echo $gpo['buy_tickets_label']; ?>" />
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e("Age restrictions", "gigpress") ?>:</th>
			<td>
				<input type="text" name="gigpress_settings[age_restrictions]" size="48" value="<?php echo $gpo['age_restrictions']; ?>" /> <span class="description"><?php _e("A pipe-separated list of available age restrictions.", "gigpress"); ?></span>
			</td>
		</tr>		<tr>
			<th scope="row"><?php _e("Related posts", "gigpress") ?></th>
			<td>
				<p><?php _e("Display gig info in related posts", "gigpress"); ?> &hellip;</p>
				<p><label><input type="radio" name="gigpress_settings[related_position]" value="before"<?php if($gpo['related_position'] == "before") echo(' checked="checked"'); ?> /> <?php _e("before the post content", "gigpress"); ?></label> &hellip; 
				<label><input type="radio" name="gigpress_settings[related_position]" value="after"<?php if($gpo['related_position'] == "after") echo(' checked="checked"'); ?> /> <?php _e("after the post content", "gigpress"); ?></label> &hellip; 
				<label><input type="radio" name="gigpress_settings[related_position]" value="nowhere"<?php if($gpo['related_position'] == "nowhere") echo(' checked="checked"'); ?> /> <?php _e("using <code>[gigpress_related_shows]</code>", "gigpress"); ?></label></p>
				<span class="description"><?php _e("If a gig has a related post, that gig's details will appear at the specified position in that post.", "gigpress"); ?></span>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e("Related show heading", "gigpress") ?>:</th>
			<td>
				<input type="text" name="gigpress_settings[related_heading]" size="48" value="<?php echo $gpo['related_heading']; ?>" />
				<span class="description"><?php _e("This appears before the gig details in your related post.", "gigpress") ?></span>
			</td>
		</tr>			
		<tr>
			<th scope="row"><?php _e("Related posts category", "gigpress") ?></th>
			<td>
				<p><label><input type="checkbox" name="gigpress_settings[autocreate_post]" value="1"<?php if(!empty($gpo['autocreate_post'])) echo(' checked="checked"'); ?> /> <?php _e("Automatically create a related post for every new show I enter.", "gigpress"); ?></label></p>
				<p><label><?php _e("When creating related posts, put them in this category", "gigpress"); ?>: &nbsp; 
				<select name="gigpress_settings[related_category]">
					<?php $categories = get_categories('hide_empty=0');
					foreach($categories as $cat) {
						$title = $cat->cat_name; ?>
						<option value="<?php echo $cat->cat_ID; ?>"<?php if(isset($gpo['related_category']) && $gpo['related_category'] == $cat->cat_ID) echo(' selected="selected"'); ?>><?php echo apply_filters("the_title", $title); ?></option>
					<?php } ?> 
				</select></label></p>
				<p><label><input type="checkbox" name="gigpress_settings[category_exclude]" value="1"<?php if(!empty($gpo['category_exclude'])) echo(' checked="checked"'); ?> /> <?php _e("Exclude this category from my normal post listings.", "gigpress"); ?></label></p>
			</td>
		</tr>
		
		<tr>
			<th scope="row"><?php _e("Related posts linking", "gigpress") ?></th>
			<td>
				<p><?php _e("Place a link to each show's related post in the following fields", "gigpress"); ?> &hellip;</p>
				<p><label><input type="checkbox" name="gigpress_settings[relatedlink_date]" value="1"<?php if(isset($gpo['relatedlink_date'] ) && $gpo['relatedlink_date'] == "1") echo(' checked="checked"'); ?> /> <?php _e("Date", "gigpress"); ?></label> &nbsp; 
				<label><input type="checkbox" name="gigpress_settings[relatedlink_city]" value="1"<?php if(isset($gpo['relatedlink_city'] ) && $gpo['relatedlink_city'] == "1") echo(' checked="checked"'); ?> /> <?php _e("City", "gigpress"); ?></label> &nbsp; 
				<label><input type="checkbox" name="gigpress_settings[relatedlink_notes]" value="1"<?php if(isset($gpo['relatedlink_notes']) && $gpo['relatedlink_notes'] == "1") echo(' checked="checked"'); ?> /> <?php _e("Notes", "gigpress"); ?></label> &nbsp; </p>
			</td>
		</tr>				
		<tr>
			<th scope="row"><?php _e("Related post phrase", "gigpress") ?>:</th>
			<td>
				<input type="text" name="gigpress_settings[related]" size="48" value="<?php echo $gpo['related']; ?>" />
				<span class="description"><?php _e("This appears in your shows listing.", "gigpress") ?></span>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e("Artist URLs", "gigpress") ?></th>
			<td><p><label><input type="checkbox" name="gigpress_settings[artist_link]" value="1" <?php if(!empty($gpo['artist_link'])) echo('checked="checked"'); ?> /> <?php _e("Link artist names to their URLs.", "gigpress") ?></label>
			</td>
		</tr>			
		<tr>
			<th scope="row"><?php _e("RSS/iCal", "gigpress") ?></th>
			<td>
				<p><label><input type="checkbox" name="gigpress_settings[rss_head]" value="1" <?php if(!empty($gpo['rss_head'])) echo('checked="checked"'); ?> /> <?php _e("Make the GigPress RSS feed auto-discoverable.", "gigpress"); ?></label></p>
				<p><label><input type="checkbox" name="gigpress_settings[display_subscriptions]" value="1" <?php if(!empty($gpo['display_subscriptions'])) echo('checked="checked"'); ?> /> <?php _e("Show RSS and iCal subscription links.", "gigpress"); ?></label></p>
				<span class="description"><?php _e("Note that the FeedBurner FeedSmith plugin will kill your GigPress RSS and iCal feeds, but the FD FeedBurner plugin will not.", "gigpress"); ?></span>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e("RSS/iCal feed title", "gigpress") ?>:</th>
			<td>
				<input type="text" name="gigpress_settings[rss_title]" size="48" value="<?php echo $gpo['rss_title']; ?>" />
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e("RSS/iCal feed limit", "gigpress") ?>:</th>
			<td>
				<input type="text" name="gigpress_settings[rss_limit]" size="48" value="<?php echo $gpo['rss_limit']; ?>" />
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e("Country display", "gigpress") ?></th>
			<td>	
				<p><label><input type="checkbox" name="gigpress_settings[display_country]" value="1" <?php if(!empty($gpo['display_country'])) echo('checked="checked"'); ?> /> <?php _e("Display country column.", "gigpress") ?></label> &nbsp; <label><input type="checkbox" name="gigpress_settings[country_view]" value="long" <?php if(isset($gpo['country_view']) && $gpo['country_view'] == 'long') echo('checked="checked"'); ?> /> <?php _e("Use full country names.", "gigpress") ?></label></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e("Link behaviour", "gigpress") ?></th>
			<td><p><label><input type="checkbox" name="gigpress_settings[target_blank]" value="1" <?php if(!empty($gpo['target_blank'])) echo('checked="checked"'); ?> /> <?php _e("Open external links in new windows.", "gigpress") ?></label>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e("Microdata", "gigpress") ?></th>
			<td>
				<p><?php _e("Include Schema.org/Event structured data as JSON-LD", "gigpress") ?></p>	
				<p>
					<label><input type="radio" name="gigpress_settings[output_schema_json]" value="y" <?php if($gpo['output_schema_json'] == 'y') echo('checked="checked"'); ?> /> <?php _e("Yes", "gigpress"); ?></label> &nbsp; 
					<label><input type="radio" name="gigpress_settings[output_schema_json]" value="n" <?php if($gpo['output_schema_json'] == 'n') echo('checked="checked"'); ?> /> <?php _e("No", "gigpress"); ?></label>
				</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e("jQuery", "gigpress") ?></th>
			<td>	
				<p><label><input type="checkbox" name="gigpress_settings[load_jquery]" value="1" <?php if(!empty($gpo['load_jquery'])) echo('checked="checked"'); ?> /> <?php _e("Load jQuery into my theme.", "gigpress") ?></label>
				<br /><span class="description"><?php _e("Uncheck this if you have a hard-coded link to the jQuery library in your theme.", "gigpress"); ?></span>
				</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e("Advanced", "gigpress"); ?></th>
			<td>	
				<p><label><input type="checkbox" name="gigpress_settings[disable_css]" value="1" <?php if(!empty($gpo['disable_css'])) echo('checked="checked"'); ?> /> <?php _e("Disable the default GigPress CSS.", "gigpress") ?></label> &nbsp; <label><input type="checkbox" name="gigpress_settings[disable_js]" value="1" <?php if(!empty($gpo['disable_js'])) echo('checked="checked"'); ?> /> <?php _e("Disable the default GigPress JavaScript.", "gigpress") ?></label></p>
			</td>
		</tr>		
		
	</table>
			
		<?php // We need to populate the form with the options not represented here, or else they'll get deleted ?>
		<input type="hidden" name="gigpress_settings[db_version]" value="<?php echo $gpo['db_version']; ?>" />
		<input type="hidden" name="gigpress_settings[default_country]" value="<?php echo $gpo['default_country']; ?>" />
		<input type="hidden" name="gigpress_settings[default_date]" value="<?php echo $gpo['default_date']; ?>" />
		<input type="hidden" name="gigpress_settings[default_time]" value="<?php echo $gpo['default_time']; ?>" />
		<input type="hidden" name="gigpress_settings[default_tour]" value="<?php echo $gpo['default_tour']; ?>" />
		<input type="hidden" name="gigpress_settings[default_artist]" value="<?php echo $gpo['default_artist']; ?>" />
		<input type="hidden" name="gigpress_settings[default_title]" value="<?php echo $gpo['default_title']; ?>" />
		<input type="hidden" name="gigpress_settings[related_date]" value="<?php echo $gpo['related_date']; ?>" />
		<input type="hidden" name="gigpress_settings[welcome]" value="<?php $gpo['welcome']; ?>" />
		
		<?php settings_fields('gigpress'); ?>
		
		<p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php _e("Save Changes", "gigpress") ?>" /></p>

	</form>

	</div>
<?php }
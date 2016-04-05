<?php

function gigpress_feed() {

	global $wpdb, $gpo;
	header('Content-type: text/xml; charset='.get_bloginfo('charset'));
	echo('<?xml version="1.0" encoding="'.get_bloginfo('charset').'"?>
	<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">');
	
	$filter = '';		
	$filter .= (isset($_GET['tour'])) ? $wpdb->prepare('AND s.show_tour_id = %d ',  $_GET['tour']) : '';
	$filter .= (isset($_GET['artist'])) ? $wpdb->prepare('AND s.show_artist_id = %d ', $_GET['artist']) : '';
	$filter .= (isset($_GET['venue'])) ? $wpdb->prepare('AND s.show_venue_id = %d ', $_GET['venue']) : '';
	$limit = (!empty($gpo['rss_limit'])) ? $gpo['rss_limit'] : 100;
	
	$shows = $wpdb->get_results(
		$wpdb->prepare("SELECT * FROM " . GIGPRESS_ARTISTS . " AS a, " . GIGPRESS_VENUES . " as v, " . GIGPRESS_SHOWS ." AS s LEFT JOIN  " . GIGPRESS_TOURS . " AS t ON s.show_tour_id = t.tour_id WHERE show_expire >= '" . GIGPRESS_NOW . "' AND show_status != 'deleted' AND s.show_artist_id = a.artist_id AND s.show_venue_id = v.venue_id " . $filter . "ORDER BY show_date ASC,show_time ASC LIMIT %d", $limit)
	);

	if($shows != FALSE) {
		$count = 1;
		$total = count($shows);
		foreach ($shows as $show) {
			$showdata = gigpress_prepare($show, 'feed');
			if($count == 1) : ?>
			
			<channel>
			<title><?php echo wptexturize($gpo['rss_title']); if(isset($_GET['artist'])) echo(': ' . $showdata['artist_plain']); if(isset($_GET['tour'])) echo(': ' . $showdata['tour']); if(isset($_GET['venue'])) echo(': ' . $showdata['venue_plain']); ?></title>
			<description><?php echo wptexturize($gpo['rss_title']); if(isset($_GET['artist'])) echo(': ' . $showdata['artist_plain']); if(isset($_GET['tour'])) echo(': ' . $showdata['tour']); if(isset($_GET['venue'])) echo(': ' . $showdata['venue_plain']); ?></description>
			<atom:link href="<?php echo GIGPRESS_RSS; if(isset($_GET['artist'])) echo('&amp;artist=' . $_GET['artist']); if(isset($_GET['tour'])) echo('&amp;tour=' . $_GET['tour']); if(isset($_GET['venue'])) echo('&amp;venue=' . $_GET['venue']); ?>" rel="self" type="application/rss+xml" />
			<link><?php echo GIGPRESS_URL; ?></link>
			
			<?php endif; ?>			
			
			<item>
				<title><?php echo $showdata['artist_plain'] . ' ' . __("in", "gigpress") . ' ' . $showdata['city'] . ' ' . __("on", "gigpress") . ' ' . $showdata['date']; ?></title>
				<description><?php echo('<![CDATA['); ?>
			<ul>
				<li><strong><?php echo wptexturize($gpo['artist_label']); ?>:</strong> <?php echo $showdata['artist_plain']; ?></li>
			<?php if($showdata['tour']) { ?>
				<li><strong><?php echo wptexturize($gpo['tour_label']); ?>:</strong> <?php echo $showdata['tour']; ?></li>
			<?php } ?>
				<li><strong><?php _e("Date", "gigpress"); ?>:</strong>
				<?php echo $showdata['date_long']; ?>
				<?php if($showdata['end_date']) { echo(' - ' . $showdata['end_date_long']); } ?>
				</li>
			<?php if($showdata['time']) { ?>
				<li><strong><?php _e("Time", "gigpress"); ?>:</strong> 
				<?php echo $showdata['time']; ?></li>
			<?php } ?>
				<li><strong><?php _e("City", "gigpress"); ?>:</strong> 
				<?php echo $showdata['city']; if(!empty($showdata['state'])) echo ', '.$showdata['state']; ?></li>
				<li><strong><?php _e("Venue", "gigpress"); ?>:</strong> 
				<?php echo $showdata['venue']; ?></li>
			<?php if($showdata['address']) { ?>
				<li><strong><?php _e("Address", "gigpress"); ?>:</strong> 
				<?php echo $showdata['address']; ?></li>
			<?php } ?>
			<?php if($showdata['venue_phone']) { ?>
				<li><strong><?php _e("Venue phone", "gigpress"); ?>:</strong> 
				<?php echo $showdata['venue_phone']; ?></li>
			<?php } ?>
				<li><strong><?php _e("Country", "gigpress"); ?>:</strong> 
				<?php echo $showdata['country']; ?></li>
			<?php if($showdata['price']) { ?>
				<li><strong><?php _e("Admission", "gigpress"); ?>:</strong> 
				<?php echo $showdata['price']; ?></li>
			<?php } ?>
			<?php if($showdata['admittance']) { ?>
				<li><strong><?php _e("Age restrictions", "gigpress"); ?>:</strong> 
				<?php echo $showdata['admittance']; ?></li>
			<?php } ?>
			<?php if($showdata['ticket_phone']) { ?>
				<li><strong><?php _e("Box office", "gigpress"); ?>:</strong> 
				<?php echo $showdata['ticket_phone']; ?></li>
			<?php } ?>
			<?php if($showdata['ticket_link']) { ?>
				<li><?php echo $showdata['ticket_link']; ?></li>
			<?php } ?>
			<?php if($showdata['external_link']) : ?>	
				<li><?php echo $showdata['external_link']; ?></li>
			<?php endif; ?>
			<?php if($showdata['notes']) { ?>
				<li><strong><?php _e("Notes", "gigpress"); ?>:</strong> 
				<?php echo $showdata['notes']; ?></li>
			<?php } ?>
			<?php if($showdata['related_link']) { ?>
				<li><?php echo $showdata['related_link']; ?></li>
			<?php } ?>
				<li>
					<?php echo $showdata['gcal']; ?> | <?php echo $showdata['ical']; ?> 
				</li>			
			</ul>
			]]></description>
				<link><?php echo $showdata['permalink']; ?></link>
				<guid isPermaLink="false">#show-<?php echo $showdata['id']; ?></guid>
				<pubDate><?php echo $showdata['rss_date']; ?></pubDate>
			</item>
		<?php
			if($count == $total) echo('</channel>');
			$count++;
		}
	}
	echo('</rss>');
}
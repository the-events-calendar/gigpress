<?php
	
// 	STOP! DO NOT MODIFY THIS FILE!
//	If you wish to customize the output, you can safely do so by COPYING this file
//	into a new folder called 'gigpress-templates' in your 'wp-content' directory
//	and then making your changes there. When in place, that file will load in place of this one.

// This template is displayed at the very end of our sidebar listing.
// By default, it displays links to RSS and iCal feeds for all upcoming shows,
// or, if we're filtering for a specific artist or tour, just for that specific artist or tour

?>

<?php // Show the "more" link if specified
if($link) : ?>
	<p class="gigpress-sidebar-more"><a href="<?php echo $gpo['shows_page']; ?>" title="<?php echo $link; ?>"><?php echo $link; ?></a></p>
<?php endif; ?>

<?php // Show the RSS/iCal links if specified
if($show_feeds) : ?>
	<p class="gigpress-subscribe"><?php _e("Subscribe", "gigpress") ;?>: 

	<?php if(!$artist && !$tour && !$venue) : ?>
		<a href="<?php echo GIGPRESS_RSS; ?>" title="<?php echo wptexturize($gpo['rss_title']); ?> RSS" class="gigpress-rss">RSS</a>&nbsp;<a href="<?php echo GIGPRESS_WEBCAL; ?>" title="<?php echo wptexturize($gpo['rss_title']); ?> iCalendar" class="gigpress-ical">iCal</a>
	<?php endif; ?>

	<?php if($artist) : ?>
		<a href="<?php echo GIGPRESS_RSS; ?>&amp;artist=<?php echo $showdata['artist_id']; ?>" title="<?php echo $showdata['artist_plain']; ?> RSS" class="gigpress-rss">RSS</a> | <a href="<?php echo GIGPRESS_WEBCAL; ?>&amp;artist=<?php echo $showdata['artist_id']; ?>" title="<?php echo $showdata['artist_plain']; ?> iCalendar" class="gigpress-ical">iCal</a>
	<?php endif; ?>	
		
	<?php if($tour) : ?>
		<a href="<?php echo GIGPRESS_RSS; ?>&amp;tour=<?php echo $showdata['tour_id']; ?>" title="<?php echo $showdata['tour_plain']; ?> RSS" class="gigpress-rss">RSS</a> | <a href="<?php echo GIGPRESS_WEBCAL . '&amp;tour=' . $showdata['tour_id']; ?>" title="<?php echo $showdata['tour_plain']; ?> iCalendar" class="gigpress-ical">iCal</a>
	<?php endif; ?>	

	<?php if($venue) : ?>
		<a href="<?php echo GIGPRESS_RSS; ?>&amp;venue=<?php echo $showdata['venue_id']; ?>" title="<?php echo $showdata['venue_plain']; ?> RSS" class="gigpress-rss">RSS</a> | <a href="<?php echo GIGPRESS_WEBCAL . '&amp;venue=' . $showdata['venue_id']; ?>" title="<?php echo $showdata['venue_plain']; ?> iCalendar" class="gigpress-ical">iCal</a>
	<?php endif; ?>	
					
	</p>

<?php endif; ?>	
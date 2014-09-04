<?php
	
// 	STOP! DO NOT MODIFY THIS FILE!
//	If you wish to customize the output, you can safely do so by COPYING this file
//	into a new folder called 'gigpress-templates' in your 'wp-content' directory
//	and then making your changes there. When in place, that file will load in place of this one.

// This template displays before each group of artist shows when grouping your shows by artist.

?>

<h3 class="gigpress-artist-heading" id="artist-<?php echo $showdata['artist_id']; ?>"><?php echo $showdata['artist']; ?>
<?php if(!empty($gpo['display_subscriptions'])) : ?>
	<span class="gigpress-artist-subscriptions">
		<a href="<?php echo GIGPRESS_RSS; ?>&amp;artist=<?php echo $showdata['artist_id']; ?>" title="<?php echo $showdata['artist_plain']; ?> RSS"><img src="<?php echo plugins_url('/gigpress/images/feed-icon-12x12.png'); ?>" alt="" /></a>
		&nbsp;
		<a href="<?php echo GIGPRESS_WEBCAL . '&amp;artist=' . $showdata['artist_id']; ?>" title="<?php echo $showdata['artist_plain']; ?> iCalendar"><img src="<?php echo plugins_url('/gigpress/images/icalendar-icon.gif'); ?>" alt="" /></a>
	</span>
<?php endif; ?>
</h3>
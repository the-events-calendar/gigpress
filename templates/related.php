<?php
	
// 	STOP! DO NOT MODIFY THIS FILE!
//	If you wish to customize the output, you can safely do so by COPYING this file
//	into a new folder called 'gigpress-templates' in your 'wp-content' directory
//	and then making your changes there. When in place, that file will load in place of this one.

//	If you're curious what all variables are available in the $showdata array,
//	have a look at the docs: http://gigpress.com/docs/

// Also available here:
// $count = current related show (if you have mutliple)
// $total_shows = total number of related shows for this post

// This is marked-up to be an hCalendar, so mess about at risk of munging that.
// See http://microformats.org/wiki/hcalendar for specs.

?>

<?php if ($gpo['related_heading'] && $count == 1) : ?>
	<h3 class="gigpress-related-heading"><?php echo wptexturize($gpo['related_heading']); ?></h3>
<?php endif; ?>

<ul class="gigpress-related-show <?php echo $showdata['status']; ?>">
	<li>
		<span class="gigpress-related-label"><?php echo wptexturize($gpo['artist_label']); ?>:</span> 
		<span class="gigpress-related-item"><?php echo $showdata['artist']; ?></span>
	</li>
<?php if($showdata['tour']) : ?>
	<li>
		<?php if($gpo['tour_label'] != '') : ?><span class="gigpress-related-label"><?php echo wptexturize($gpo['tour_label']); ?>:</span> <?php endif; ?>
		<span class="gigpress-related-item"><?php echo $showdata['tour']; ?></span>
	</li>
<?php endif; ?>
	<li>
		<span class="gigpress-related-label"><?php _e("Date", "gigpress"); ?>:</span>
		<span class="gigpress-related-item"><?php echo $showdata['date']; ?><?php if($showdata['end_date']) : ?> - <?php echo $showdata['end_date']; ?><?php endif; ?>
		</span>
	</li>
<?php if($showdata['time']) : ?>
	<li>
		<span class="gigpress-related-label"><?php _e("Time", "gigpress"); ?>:</span> 
		<span class="gigpress-related-item"><?php echo $showdata['time']; ?></span>
	</li>
<?php endif; ?>
	
	<li>
		<span class="gigpress-related-label"><?php _e("Venue", "gigpress"); ?>:</span> 
		<span class="gigpress-show-related"><?php echo $showdata['venue']; ?></span>
	</li>
	
	<li>
		<span class="gigpress-related-label"><?php _e("City", "gigpress"); ?>:</span> 
		<span class="gigpress-related-item"><?php echo $showdata['city']; ?><?php if(!empty($showdata['state'])) : ?> , <?php echo $showdata['state']; ?><?php endif; ?>
		</span>
	</li>			
	<?php if($showdata['address']) : ?>
		<li>
			<span class="gigpress-related-label"><?php _e("Address", "gigpress"); ?>:</span> 
			<span class="gigpress-related-item"><?php echo $showdata['address']; ?></span>
		</li>
	<?php endif; ?>
	<?php if($showdata['venue_phone']) : ?>	
		<li>
			<span class="gigpress-related-label"><?php _e("Venue phone", "gigpress"); ?>:</span> 
			<span class="gigpress-related-item"><?php echo $showdata['venue_phone']; ?></span>
		</li>
	<?php endif; ?>
		<li>
			<span class="gigpress-related-label"><?php _e("Country", "gigpress"); ?>:</span> 
			<span class="gigpress-related-item"><?php echo $showdata['country']; ?></span>
		</li>
	<?php if($showdata['price']) : ?>	
		<li>
			<span class="gigpress-related-label"><?php _e("Admission", "gigpress"); ?>:</span> 
			<span class="gigpress-related-item"><?php echo $showdata['price']; ?></span>
		</li>
	<?php endif; ?>	
	<?php if($showdata['admittance']) : ?>
		<li>
			<span class="gigpress-related-label"><?php _e("Age restrictions", "gigpress"); ?>:</span> 
			<span class="gigpress-related-item"><?php echo $showdata['admittance']; ?></span>
		</li>
	<?php endif; ?>
	
	<?php if($showdata['ticket_phone']) : ?>
		<li>
			<span class="gigpress-related-label"><?php _e("Box office", "gigpress"); ?>:</span> 
			<span class="gigpress-related-item"><?php echo $showdata['ticket_phone']; ?></span>
		</li>
	<?php endif; ?>
	
	<?php if($showdata['ticket_link']) : ?>	
		<li><?php echo $showdata['ticket_link']; ?></li>
	<?php endif; ?>
	
	<?php if($showdata['external_link']) : ?>	
		<li><?php echo $showdata['external_link']; ?></li>
	<?php endif; ?>
	
	<?php if($showdata['notes']) : ?>	
		<li>
			<span class="gigpress-related-label"><?php _e("Notes", "gigpress"); ?>:</span> 
			<span class="gigpress-related-item"><?php echo $showdata['notes']; ?></span>
		</li>
	<?php endif; ?>
	<li>
		<?php echo $showdata['gcal']; ?> | <?php echo $showdata['ical']; ?> 
	</li>
</ul>
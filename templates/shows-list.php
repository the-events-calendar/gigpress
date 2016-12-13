<?php
/*
	STOP! DO NOT MODIFY THIS FILE!
	If you wish to customize the output, you can safely do so by COPYING this file into a new folder called 'gigpress-templates' in your 'wp-content' directory	and then making your changes there. When in place, that file will load in place of this one.
	
	This template displays all of our individual show data in the main shows listing (upcoming and past).
	At the end of this template you can also modify the contents if the InformationWindow appearing on the overview map for each venue.

	If you're curious what all variables are available in the $showdata array, have a look at the docs: http://gigpress.com/docs/
*/
?>

<tbody>
	
	<tr id="<?php echo $showdata['id']; ?>" class="gigpress-row <?php echo $class; ?>">
	
		<td class="gigpress-date"><?php echo $showdata['date']; ?>
			<?php if($showdata['end_date']) : ?> - <?php echo $showdata['end_date']; ?><?php endif; ?>
		</td>
		
	<?php if((!$artist && $group_artists == 'no') && $total_artists > 1) : ?>
		<td class="gigpress-artist">
			<?php echo $showdata['artist']; ?>
		</td>
	<?php endif; ?>
	
		<td class="gigpress-city"><?php echo $showdata['city']; if(!empty($showdata['state'])) echo ', '.$showdata['state']; ?></td>
		
		<td class="gigpress-venue"><?php echo $showdata['venue']; ?></td>
		
	<?php if(!empty($gpo['display_country'])) : ?>
		<td class="gigpress-country"><?php echo $showdata['country']; ?></td>
	<?php endif; ?>
	
	</tr>
	
	<tr class="gigpress-info <?php echo $class; ?>">
	
		<td class="gigpress-links-cell">
			<?php
			// Only show these links if this show is in the future
			if($scope != 'past') : ?>
			<div class="gigpress-calendar-add">
				<a class="gigpress-links-toggle" href="#calendar-links-<?php echo $showdata['id']; ?>">Add</a>
				<div class="gigpress-calendar-links" id="calendar-links-<?php echo $showdata['id']; ?>">
					<div class="gigpress-calendar-links-inner">
						<span><?php echo $showdata['gcal']; ?></span>
						<span><?php echo $showdata['ical']; ?></span>
					</div>
				</div>
			</div>
			<?php endif; ?>
		</td>
		
		<td colspan="<?php echo $cols - 1; ?>">
		
			<?php if($showdata['time']) : ?>
				<span class="gigpress-info-item"><span class="gigpress-info-label"><?php _e("Time", "gigpress"); ?>:</span> <?php echo $showdata['time']; ?>.</span>
			<?php endif; ?>
			
			<?php if($showdata['price']) : ?>
				<span class="gigpress-info-item"><span class="gigpress-info-label"><?php _e("Admission", "gigpress"); ?>:</span> <?php echo $showdata['price']; ?>.</span>
			<?php endif; ?>
			
			<?php if($showdata['admittance']) : ?>
				<span class="gigpress-info-item"><span class="gigpress-info-label"><?php _e("Age restrictions", "gigpress"); ?>:</span> <?php echo $showdata['admittance']; ?>.</span>
			<?php endif; ?>
			
			<?php if($showdata['ticket_phone']) : ?>
				<span class="gigpress-info-item"><span class="gigpress-info-label"><?php _e("Box office", "gigpress"); ?>:</span> <?php echo $showdata['ticket_phone']; ?>.</span>
			<?php endif; ?>
			
			<?php if($showdata['address']) : ?> 
				<span class="gigpress-info-item"><span class="gigpress-info-label"><?php _e("Address", "gigpress"); ?>:</span> <?php echo $showdata['address']; ?>.</span>
			<?php endif; ?>
			
			<?php if($showdata['venue_phone']) : ?>
				<span class="gigpress-info-item"><span class="gigpress-info-label"><?php _e("Venue phone", "gigpress"); ?>:</span> <?php echo $showdata['venue_phone']; ?>.</span>
			<?php endif; ?>				
			
			<?php if($showdata['notes']) : ?>
				<span class="gigpress-info-item"><?php echo $showdata['notes']; ?></span>
			<?php endif; ?>
			
			<?php if($showdata['related_link'] && !empty($gpo['relatedlink_notes'])) : ?>
				<span class="gigpress-info-item"><?php echo $showdata['related_link']; ?></span> 
			<?php endif; ?>
			
			<?php if($showdata['ticket_link']) : ?>
				<span class="gigpress-info-item"><?php echo $showdata['ticket_link']; ?></span>
			<?php endif; ?>

			<?php if($showdata['external_link']) : ?>
				<span class="gigpress-info-item"><?php echo $showdata['external_link']; ?></span>
			<?php endif; ?>					
		
		</td>
	
	</tr>
</tbody>	
<?php
  if ($gigmap_thisGig!=null) { 
		//In this section you can set the contents of the markers tooltip and InfoWindow of each location in the overview map.
		//The marker's title will appear as a tooltip. Default: "City(State), Venue". May not contain HTML
		$citystate=$showdata['city_plain'];
		if (!empty($showdata['state'])) $citystate = $citystate.'('.$showdata['state'].')';  
		$gigmap_thisGig->setGigmapMarkerTooltip($citystate.', '.$showdata['venue_plain']);
		//
		//The InfoWindow appears when the user clicks on the marker. Here we define its contents
		//
		//The next statement is used to set the first <div ...> statemant in the InfoWindow of each location in the overview map. 
		//Example: to set the background-color to black use: 
		//$gigmap_thisGig->setGigmapInfoWdwDiv('<div style="background-color: #000";>');
		$gigmap_thisGig->setGigmapInfoWdwDiv('');
		//First and second line of the InfoWindow
		$gigmap_thisGig->setFirstLine('<h5 class="gigpress-heading">'.$showdata['city'].'</h5>');
		$gigmap_thisGig->setSecondLine('<span class="gigpress-info-item"><strong>' . $showdata['venue'] . '</strong> ____ ' . $showdata['address_plain'] . ', ' . $showdata['city_plain'] . '&ensp;</span>');
		//The next line sets the class for the appearance of the performance date(s)
		$gigmap_thisGig->setDateClass('"gigpress-date"');  
		//And this is the description of the performance itself:
		$gigmap_thisGig->setPerformanceText('<span class="gigpress-info-item">' . '&emsp;' . $showdata['artist_plain'] . '&emsp;|&emsp;' . $showdata['tour'] . '&ensp;</span>');
	}
?>
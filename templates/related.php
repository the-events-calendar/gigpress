 <!-- start related -->
<?php 
	$date_mysql_format = '%Y-%m-%d';
	$monthnames = array("0", "january", "february", "march", "april", "may", "june", "july", "august", "september", "october", "november", "december");
 $eventdtarray = explode("-", $showdata['date_mysql']);
 $yr = $eventdtarray['0'];
 $mo = intval($eventdtarray['1']);
 $day = intval($eventdtarray['2']);
 $monthname = $monthnames[$mo];
?>
<div class="gigpress-related-show <?php echo $showdata['status']; ?>">
<?php if ( $count == 1 ) : ?>
	   <a href=/the-programs?program_id= . <?php echo $showdata['artist_id']; ?>"><?php echo str_replace(':', ':<br>', $showdata['artist']); ?></a>
		 <?php  if($showdata['tour']) : ?>, <?php if($gpo['tour_label'] != '') : 
		  		echo wptexturize($gpo['tour_label']); ?>: <?php endif;
		     echo $showdata['tour']; 
		  endif; ?>
	<?php endif; ?>
	<hr class=cb>
	<div class="info-left gig-info" >
	  <span class=gig-date >
		<?php echo $day . ' ' . $monthname . ' ' . $yr; if($showdata['end_date']) : ?> - <?php echo $showdata['end_date'];  endif;  if($showdata['time']) : ?>,
		<?php echo $showdata['time']; endif;?>
	  </span>  <span class=gig-venue>
         <?php echo $showdata['venue']; ?>,
         <?php if(!empty($showdata['address'])) :  echo $showdata['address'].', '; endif; ?>
		 <?php echo $showdata['city'].', '.$showdata['state']; ?>
		 <?php if(!empty($gpo['display_country'])
		 		  | $showdata['country'] != 'United States' ) : 
		 			echo ', '.$showdata['country']; 
		 		endif; ?> 
<?php if ($gpo['related_heading'] && $count == 1) : ?>
   <?php if($showdata['notes']) : ?>
	<br class=cb><!-- start gig-note -->
		<div class="gig-note" style="display:block;" id="gignote-<?php echo $showdata['id']; ?>" >
			<?php echo $showdata['notes']; ?>
		</div>
	<br class=cb> <!-- end gig-note -->
   <?php endif; ?>
  <div class="info-right">
	<?php if($showdata['venue_phone']) : ?>	
			<?php _e("Venue phone", "gigpress"); ?>: <?php echo $showdata['venue_phone']; ?>
	<?php endif; ?>
	<?php if($showdata['price']) : ?>		<br>
			<?php _e("Admission", "gigpress"); ?>: <?php echo $showdata['price']; ?>
	<?php endif; ?>	
	<?php if($showdata['admittance']) : ?>	<br>
			<?php _e("Age restrictions", "gigpress"); ?>: <?php echo $showdata['admittance']; ?>
	<?php endif; ?>
	<?php if($showdata['ticket_phone']) : ?>	<br>
			<?php _e("Box office", "gigpress"); ?>: <?php echo $showdata['ticket_phone']; ?>
	<?php endif; ?>
	<?php if($showdata['external_link']) : ?>	
		<br><!-- start external_link -->
		<div class="info-right external-link">
			<?php echo $showdata['external_link']; ?>
		</div> <!-- end external_link -->
	<?php endif; ?>
  </div>
<?php endif; ?>
</div>
<?php if($showdata['date_mysql'] >= GIGPRESS_NOW) :  ?> <!--Only show these links if this show is in the future --- $scope != 'past' dont cut it -->
	<div class="gig-info info-right tc-links"  > <!-- start tix/cal links in right div -->
		<?php if($showdata['ticket_link']) : echo $showdata['ticket_link']; endif; ?>   <!-- ticket_link -->
		<?php echo $showdata['gcal']; ?>
		<?php echo $showdata['ical']; ?>
	</div> <!-- end tix/cal links right -->
<?php endif; ?> 
</div>
<br>


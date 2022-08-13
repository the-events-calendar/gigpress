<?php
/*
	This template displays all of our individual show data in the main shows listing (upcoming and past).
	If you're curious what all variables are available in the $showdata array, have a look at the docs: http://gigpress.com/docs/
*/

 $eventdtarray = explode("-", $showdata['date_mysql']);
 $yr = $eventdtarray['0'];
 $mo = intval($eventdtarray['1']);
 $day = intval($eventdtarray['2']);

if($yr != $current_year) :
	$current_year = $yr;
	$current_month = 0;
	$current_day = 0;
	$current_program = "";
	$current_venue   = "";
 ?>
<div class="postDivider"><img class="postDivider" src="/graphics/blogdivider.png"></div>
      <div class="year"><?php echo $yr; ?></div>
<?php endif; ?>
		 
<?php if($mo != $current_month) :
	$current_month = $mo;
	$current_day = 0;
	//$current_program = "";
	$current_venue   = "";
	$monthname = $monthnames[$mo];
?>
       <!-- start month -->
<div class="postDivider"><img class="postDivider" src="/graphics/blogdivider.png"></div>
       <div class="month">
           <img class="month" alt="<?php echo $monthname . $yr ; ?>" title="<?php echo $monthname . $yr ; ?>" src="/months/<?php echo $monthname; ?>.png" />
       </div>
<?php endif; ?>

    <div class="event" id="prog-<?php echo $showdata['id']; ?>">    <!-- start event -->

<?php if(!$program_id and $showdata['artist_plain'] != $current_program) :
	 	$current_program = $showdata['artist_plain'];
 ?>
<div class="postDivider"><img class="postDivider" src="/graphics/blogdivider.png"></div>
	   <a href="/the-programs?program_id=<?php echo $showdata['artist_id']; ?>"><h1 class="progtitle" ><?php echo bc_bankhead($current_program); ?></h1></a>

	<?php if(!empty($showdata['program_notes'])) : ?>
		<div class="info-left prog-note"
			 <?php echo ( 1 < $condensed ?  "style='display:none;'" : ""); ?>
			 id="prog-note-<?php echo $showdata['id']; ?>"> <!-- start prog-note -->
			<?php echo $showdata['program_notes']; ?>
		</div> <!-- end prog-note -->
			<div class="prog-note-toggle"
				 style="display:<?php echo ( 1 < $condensed ?  "inline-block" : "none" ); ?>;">
			&nbsp;&nbsp;<a title='click to show/hide program description'
						 href="#prog-<?php echo $showdata['id']; ?>"
						onclick="return showInfo('prog-note-<?php echo $showdata['id']; ?>')" >
								program description
						</a>
					<br>
			</div><!-- prog-note-toggle shown in wptouch -->
	<?php endif; ?>

    <?php if(!empty($showdata['artist_url'])) :
    	  echo '<a class="info-left" href="'.$showdata['artist_url'].'" >read more...</a>';
     endif; ?>

<?php endif; ?>
<br class=cb >
        <div class="date">
<?php if($day != $current_day) 
		{
        	$current_day = $day;
        	echo "<a href=#show-" . $showdata['id'] . " title='" . $current_day . $monthname . $current_year . "'>" . $current_day . "</a>";
        	if($showdata['end_date'])
        	{ 
        		echo '<br>-';
        		$end_date = explode("-", $showdata['end_date_mysql']);
        		$end_mo = intval($end_date[1]);
				if ($end_mo != $mo)
					echo ' '. ucfirst(substr($monthnames[$end_mo],0,3)) . '&nbsp;';
        		echo intval($end_date[2]);
        		$end_yr = intval($end_date[0]);
				if ($end_yr != $yr)
					echo ' ' . $end_yr;
        	} 
        	echo "<br>";
		} ?>
				<div class="gigpress-listing time"><?php echo $showdata['time']; ?></div>
		</div>  

        <div class="description">        <!-- start descr -->
<?php if($showdata['venue'] != $current_venue)
	  {
        	$current_venue = $showdata['venue'];
         	$loc = $showdata['venue'].', '; 
         	if(!empty($showdata['address'])) 
         		 $loc .= $showdata['address'].', '; 
			$loc .= $showdata['city']; 
            if(!empty($showdata['state'])) 
            	$loc .= ',&nbsp;' . $showdata['state'];
      		if(!empty($gpo['display_country'])
		 		| ($showdata['country'] != 'United States' ))
		 		$loc .= ', '.$showdata['country']; 
		 	echo $loc; 
	  }
	  if(!empty($showdata['notes'])) : ?>
				<br>
				<a title='event notes' onclick="return showInfo('gignote-<?php echo $showdata['id']; ?>')">* show info</a>
		 <?php endif; ?>

	<div class="info-right tc-links"  > 
<?php if(!empty($showdata['status']))
	{
		if( $showdata['status']== 'active' && ($scope != 'past') )  
	 	{ 			// Only show these links if this show is in the future 
		    if ($showdata['ticket_link'] ) 
				  echo $showdata['ticket_link'];
			echo $showdata['gcal'];
			echo $showdata['ical'];
		}
		else if( $showdata['status']== 'postponed' ) 
			echo "<h2>Postponed!</h2>";
		else if( $showdata['status']== 'cancelled' ) 
			echo "<h2>Cancelled!</h2>";
		else if( $showdata['status']== 'soldout' )
			echo "<h2>Sold Out!</h2>";
	}
?> 
	</div> 

	<?php if(!empty($showdata['notes'])) : ?>
		<br class=cb> <!-- start gig-note -->
		
		<div class="gig-note" 
		style="display:"<?php echo ( 0 < $condensed ?  "none" : "block" ); ?>;"
		id="gignote-<?php echo $showdata['id']; ?>" >
			<?php echo $showdata['notes']; ?>
			&nbsp;&nbsp;<a title='close notes' onclick="return showInfo('gignote-<?php echo $showdata['id']; ?>')" >close</a>
		</div>
	<?php endif; ?><!-- end gig-note -->

<?php if($showdata['related_link'] && !empty($gpo['relatedlink_notes'])) : ?>
			<br> <!-- start related_link -->
		<div class="info-right related-link">
			<?php echo $showdata['related_link']; ?>
		</div> <!-- end related_link -->
<?php endif; ?>			
       
<?php if($showdata['external_link']) : ?>
		<br><!-- start external_link -->
		<div class="info-right external-link">
			<?php echo $showdata['external_link']; ?>
		</div> <!-- end external_link -->
<?php endif; ?>					
      
    	</div>       <!-- end description -->
    	
    </div>       <!-- gigpress end show -->

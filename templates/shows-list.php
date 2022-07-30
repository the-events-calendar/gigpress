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
<?php endif; ?>
		 
<?php if($mo != $current_month) :
	$current_month = $mo;
	$current_day = 0;
	//$current_program = "";
	$current_venue   = "";
	$monthname = $monthnames[$mo];
?>
       <!-- start month -->
<?php endif; ?>

    <div class="event" id="prog-<?php echo $showdata['id']; ?>">    <!-- start event -->

<?php if($showdata['artist_plain'] != $current_program) :
	 	$current_program = $showdata['artist_plain'];
 ?>
		<a title='click to show/hide program description'
						 href="#prog-<?php echo $showdata['id']; ?>"
						onclick="return showInfo('prog-note-<?php echo $showdata['id']; ?>')" >
			<h2 class="progtitle" ><?php echo bc_bankhead($current_program); ?></h2></a>

	<?php if(!empty($showdata['program_notes'])) : ?>
		<div class="prog-note"
			 <?php echo ( 0 < $condensed ?  "style='display:none;'" : ""); ?>
			 id="prog-note-<?php echo $showdata['id']; ?>"> <!-- start prog-note -->
			<?php echo $showdata['program_notes']; ?>
			<div class="prog-note-toggle">&nbsp;
				<a title='click to hide program description'
					href="#prog-<?php echo $showdata['id']; ?>"
					onclick="return showInfo('prog-note-<?php echo $showdata['id']; ?>')" >
			    <h3 class="gig-pup">hide</h3></a> 
				&nbsp;
				<a title='open program description page'
					href="/programs-repertoire/?program_id=<?php echo $showdata['artist_id']; ?>">
			    <h3 class="gig-pup">program page</h3></a>
			</div><!-- prog-note-toggle shown in wptouch -->	
<p>&nbsp;</p>
		</div> <!-- end prog-note -->
	<?php endif; ?>
<?php endif; ?>	
		
        <div class="gig-date">
<?php   if($day != $current_day) 
		{
        	$current_day = $day;
        	echo ucwords(substr($monthname,0,3)) . "&nbsp;" . $current_day;

        	if($showdata['end_date'])
        	{ 
         		$end_date = explode("-", $showdata['end_date_mysql']);
        		$end_mo = intval($end_date[1]);
               	$end_yr = intval($end_date[0]);
				if ($end_yr != $yr)
					echo ',&nbsp;' . $yr . '&nbsp;';
   		        echo '-';
				if ($end_mo != $mo)
					echo '&nbsp;' . ucfirst(substr($monthnames[$end_mo],0,3)) . '&nbsp;';
        		echo intval($end_date[2]);
				echo ',&nbsp;' . $end_yr;
        	} 
        	else
				echo ',&nbsp;' . $yr . " ";

    		echo '<div class="gig-time">' .  $showdata['time'];
            if( $showdata['status']== 'active'
			  && ($scope != 'past') )  
	 	    { 			// Only show these links if this show is in the future 
			    echo "<br>";
			    echo $showdata['gcal'];
			    echo $showdata['ical'];
	        }
            echo "</div>";
	    } ?>
		</div>  

        <div class="gig-venue">        <!-- start descr -->
<?php if($showdata['venue'] != $current_venue)
	  {
        	$current_venue = $showdata['venue'];
         	$loc = $showdata['venue'].'<br>'; 
         	if(!empty($showdata['address'])) 
         		 $loc .= $showdata['address'].'<br>'; 
			$loc .= $showdata['city']; 
            if(!empty($showdata['state'])) 
            	$loc .= ',&nbsp;' . $showdata['state'];
      		if(!empty($gpo['display_country'])
		 		| ($showdata['country'] != 'United States' ))
		 		$loc .= ', '.$showdata['country']; 
		 	echo $loc; 
		} ?>
    </div>
    
	<div class="gig-tix"  > 
<?php if(!empty($showdata['status']))
	{
		if( $showdata['status']== 'active'
		  && ($scope != 'past') )  
	 	{ 			// Only show these links if this show is in the future 
		    if ($showdata['ticket_link'] ) 
				  echo $showdata['ticket_link'];
			if(!empty($showdata['price']))
				echo "<br>" , $showdata['price'];
		}
		else if( $showdata['status']== 'postponed' ) 
			echo "<h3>Postponed!</h3>";
		else if( $showdata['status']== 'cancelled' ) 
			echo "<h3>Cancelled!</h3>";
		else if( $showdata['status']== 'soldout' )
			echo "<h3>Sold Out!</h3>";
	}
?> 
	</div> 

	<?php if(!empty($showdata['notes'])
		  or !empty($showdata['artist_url'])) : ?>
		<br clear=both> <!-- start gig-note -->
		<div class="gig-note" 
		        id="gignote-<?php echo $showdata['id']; ?>" >
			<?php if(!empty($showdata['notes'])) echo $showdata['notes']; ?>
			<?php if(!empty($showdata['artist_url'])) : ?>
				<a class="more-info" href="<?php echo $showdata['artist_url']; ?>" >read more...</a>
			<?php endif; ?>	
		</div>
	<?php endif; ?><!-- end gig-note -->

<?php if($showdata['related_link']
		&& !empty($gpo['relatedlink_notes'])) : ?>
		<div class="info-right related-link">
			<?php echo $showdata['related_link']; ?>
		</div> <!-- end related_link -->
<?php endif; ?>			
       
<?php if($showdata['external_link']) : ?>
		<div class="info-right external-link">
			<?php echo $showdata['external_link']; ?>
		</div> <!-- end external_link -->
<?php endif; ?>	

    </div>       <!-- gigpress end show -->
<br clear=both>
<hr>
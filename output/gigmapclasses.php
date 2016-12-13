<?php

class Gigmap_Venue_Collection
{
  public $allVenues = array();
  
  public function getGigVenue($sort, $gigvenue, $giglat, $giglng) {
  	$mygigvenue = NULL;
  	
  	// find Object of Class Gigmap_Venue_Gigs, which contains $this venue
  	if ( !empty($this->allVenues) ) {  	
  		foreach($this->allVenues as $venue) {
  			if ($venue->getGigVenueVenue() == $gigvenue) {
  				$mygigvenue = $venue;
  				break;
  			}
  		}
  	}
  	// create it, if not found
  	if ($mygigvenue == NULL) 
  	{
  		$mygigvenue = new Gigmap_Venue_Gigs ($sort, $gigvenue, $giglat, $giglng);
  		$this->allVenues[] = $mygigvenue;
  	}
  	return $mygigvenue;
  }
  
  public function DisplayMap($artist_id){
  	//fb($this->allVenues);
  	if (count($this->allVenues) > 0) {
  		$json_allvenues = json_encode( $this->allVenues );
  		$markerIcons= json_encode(plugins_url('../images/gigmapmarkers.png', __FILE__));
		
			echo "
			<script type=\"text/javascript\"> 
		 	 gigmap_setmarkers($artist_id, $markerIcons, $json_allvenues);
			</script>";
		}
		return count($this->allVenues);
	}
}


class Gigmap_Venue_Gigs 
{
	
	const SAME_GIG_MAXDATEDIFF = 5;			 //if the new gig starts no later than SAME_GIG_MAXDATEDIFF days later than a previous one, it is treated as thee same multi-date event.  
	const SECONDS_PER_DAY      = 86400;  //seconds in a day
	private static $TheGigIaddedLast = null;
	
	
	//these attributes need to be public, otherwise we cannot transfer them to javascript via Gigmap_Venue_Collection / json_encode
  public $all_gigs_of_Venue = array();
  public $sort;
  public $venue;
  public $lat;
  public $lng;
  public $ux_closest_gig;
 
  
   public function __construct ($sort, $newvenue, $newgiglat, $newgiglng)
  {
    $this->sort  = $sort;
    $this->venue = $newvenue;
    $this->lat   = $newgiglat;
    $this->lng   = $newgiglng;
    $this->ux_closest_gig = 0;
  }
  
    
  public function setGigVenueGig($gigdate_from, $str_gigdate_from, $gigdate_to, $str_gigdate_to, $gigartist, $gigtour, $gigid) {
  	
  	if (empty($str_gigdate_to)) $str_gigdate_to = $str_gigdate_from;
  	$ux_gigdate_from = strtotime( $gigdate_from );
  	$ux_gigdate_to   = strtotime( $gigdate_to );
  	
  	if ($this->sort == 'asc') {  //we are in the future. Is this our next gig?
  		 if ($this->ux_closest_gig == 0 or $this->ux_closest_gig > $ux_gigdate_from) $this->ux_closest_gig = $ux_gigdate_from;
  	}
  	else {
  		if ($this->ux_closest_gig == 0 or $this->ux_closest_gig < $ux_gigdate_from) $this->ux_closest_gig = $ux_gigdate_from;	
  	}

		$mygig = NULL;
  	if ( !empty($this->all_gigs_of_Venue) ) { 
  		$lastindex = count($this->all_gigs_of_Venue)-1;
  		if(($this->all_gigs_of_Venue[$lastindex]->gigartist == $gigartist) and 
  			 ($this->all_gigs_of_Venue[$lastindex]->gigtour == $gigtour)) {
  			 	//Artist and tour are identical. If the time difference is not too big, we can collate. 
  			 	//Now check the time difference
   			if ($this->sort == 'asc') {
  				$timediff = $ux_gigdate_from - $this->all_gigs_of_Venue[$lastindex]->ux_gigdate_to;
  				if ($timediff	<= self::SAME_GIG_MAXDATEDIFF*self::SECONDS_PER_DAY) {
  					$mygig = $this->all_gigs_of_Venue[$lastindex];
  					$this->all_gigs_of_Venue[$lastindex]->ux_gigdate_to=$ux_gigdate_to;
  					$this->all_gigs_of_Venue[$lastindex]->str_gigdate_to=$str_gigdate_to;
  				}
  			}
  			else {
  				$timediff = $this->all_gigs_of_Venue[$lastindex]->ux_gigdate_from - $ux_gigdate_to;
  				if ($timediff	<= self::SAME_GIG_MAXDATEDIFF*self::SECONDS_PER_DAY) {
  					$mygig = $this->all_gigs_of_Venue[$lastindex];
  					$this->all_gigs_of_Venue[$lastindex]->ux_gigdate_from =$ux_gigdate_from;
  					$this->all_gigs_of_Venue[$lastindex]->str_gigdate_from=$str_gigdate_from;
  				}
  			}
  		}
  	}
 
  	if ($mygig == NULL){
				$mygig = new Gigmap_Venue_OneGig($ux_gigdate_from, $ux_gigdate_to, $str_gigdate_from, $str_gigdate_to, $gigartist, $gigtour, $gigid);
				$this->all_gigs_of_Venue[] = $mygig;
  	}
 
  	return $mygig;
  }
  
  public function getGigVenueVenue() {
  	return $this->venue;
  }

}


class Gigmap_Venue_OneGig
{
  	public $ux_gigdate_from;
  	public $ux_gigdate_to;
  	public $str_gigdate_from;
  	public $str_gigdate_to;
  	public $gigartist;
  	public $gigtour;
  	public $gigid;

  	public $tooltip;
  	public $infowdwdiv;
  	public $firstline;
  	public $secondline;
  	public $dateclass;
  	public $performancetext; 
  
  public function __construct ($ux_gigdate_from, $ux_gigdate_to, $str_gigdate_from, $str_gigdate_to, $gigartist, $gigtour, $gigid)
  {
  	$this->ux_gigdate_from	= $ux_gigdate_from;
  	$this->ux_gigdate_to  	= $ux_gigdate_to;
  	$this->str_gigdate_from	= $str_gigdate_from;
  	$this->str_gigdate_to		= $str_gigdate_to;
  	$this->gigartist				= $gigartist;
  	$this->gigtour					= $gigtour;
  	$this->gigid						= $gigid;

		$this->tooltip = '';
  	$this->infowdwdiv = '';
  	$this->firstline = '';
  	$this->secondline = '';
  	$this->dateclass = '';
  	$this->performancetext = '';
  }
  
	public function setGigmapMarkerTooltip($tooltip) {
   	$this->tooltip=$tooltip;
  } 
  public function setGigmapInfoWdwDiv($infowdwdiv) {
  	$this->infowdwdiv=$infowdwdiv;  
  }
  public function setFirstLine($firstline) {
  	$this->firstline=$firstline;  
  }
  public function setSecondLine($secondline) {
  	$this->secondline=$secondline;  
  }
  public function setDateClass($dateclass) {
  	$this->dateclass=$dateclass;  
  }
  public function setPerformanceText($performancetext) {
  	$this->performancetext=$performancetext;  
  }
}


?>
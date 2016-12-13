var CANVAS="gigmap";
var AllGigMaps  = Array();

jQuery(document).ready(function($) {
	$.fn.fadeToggle = function(speed, easing, callback) {
		return this.animate({opacity: "toggle"}, speed, easing, callback); 
	}; 	
	$("a.gigpress-links-toggle").click(function() {
		target = $(this).attr("href").replace(document.location.href,'');
		$(target).fadeToggle("fast");
		$(this).toggleClass("gigpress-link-active");
		return false;
	});
	
	$("select.gigpress_menu").change(function()
	{
		window.location = gigmap_getnewurl(); //window.location = $(this).val();
	});
	
	$("#mygmdistance").change(function()
	{
		//distance from my location has lost focus. reload th url with new parameters if my location is already given
		document.getElementById("mygmdist").value = $(this).val();
		if (document.getElementById("mygmplace").value) 
		{
			window.location = gigmap_getnewurl();
		}
	});	
	
	$("#kmiles").change(function()
	{
		document.getElementById("mygmkm").value = $(this).val();
		if (document.getElementById("mygmplace").value) 
		{
			window.location = gigmap_getnewurl();
		}
	});
	
});




jQuery(document).ready(function($) {
	//ID of input fields 
	var mygmlocation = "mygmlocation";
	var defaultBounds = null;
	
	/********* Here you can set boundarys for your preferred region of the world. These here roughly mark Austria, Germany and Switzerland
	defaultBounds = new google.maps.LatLngBounds(
  new google.maps.LatLng(45.7580539, 4.7650813),
  new google.maps.LatLng(55.891276, 17.600839));
	*********/ 
	
	var input = document.getElementById(mygmlocation);
	//verify the field
	if ( input != null ) {
		var options = {
    	types: ['(regions)'],    //(cities) does not allow some smaller places like 'Lavin', 'Ftan' etc.
    	bounds: defaultBounds
		};
		var autocomplete = new google.maps.places.Autocomplete(input, options);
		google.maps.event.addListener(autocomplete, 'place_changed', function(e) {
	
			var place = autocomplete.getPlace();
			if (place.geometry) 
			{
				var lat = place.geometry.location.lat();
				var lng = place.geometry.location.lng();
				//store the results in undisplayed input fields in the php
				document.getElementById("mygmplace").value = place.formatted_address;
				document.getElementById("mygmlat").value = lat;
				document.getElementById("mygmlng").value = lng;
			}
			else{  
				//user cleared my location input - remove restriction    
				document.getElementById("mygmplace").value = "";
			}
			//finally load a new url with new mylocation parameters
			window.location = gigmap_getnewurl();
		});
	};
});

//compose a new url with parameters for calendar and location restrictions
//mydate already contains url and month/year restrictions

function gigmap_getnewurl () {
	var e= document.getElementById("gigpress_menu");
	if (e) mydate = document.getElementById("gigpress_menu").value;
	else	 mydate = document.getElementById("mygmbase").value;

	//check the url string and append characters if necessary
	if (mydate.indexOf("?") < 0) mydate += "?";
	var lastchar = mydate.slice(-1);
	if (lastchar != "?" && lastchar != "&") mydate += "&";

	var e= document.getElementById("mygmplace");
	if (e) {		
		var myplace = document.getElementById("mygmplace").value;
		var mydistance = document.getElementById("mygmdist").value;
		var mykm = document.getElementById("mygmkm").value;
		var mylat = document.getElementById("mygmlat").value;
		var mylng = document.getElementById("mygmlng").value;
		
		if (myplace && mylat && mylng) {
			mydate += "gpmyp=" + myplace;
			mydate += "&gpmylat=" + mylat;
			mydate += "&gpmylng=" + mylng;
			
			if (mydistance) mydate += "&gpmyd=" + mydistance;
			if (mykm) mydate += "&gpmykm=" + mykm;
		}
	}

	return mydate;
}


function reset_mylocation() {
	//check, if something was there before	
	if (document.getElementById("mygmplace").value)	{
		document.getElementById("mygmplace").value ="";
		document.getElementById("mygmlocation").value ="";
		window.location = gigmap_getnewurl();
	}
	document.getElementById("mygmplace").value ="";
}

 
function gigmap_getMapcontext(gigid){
	var len = AllGigMaps.length;
	var thisisit = -1;

	for (var i = 0; i < len; i++) {
		if (AllGigMaps[i].map_id == gigid) thisisit = i; 
	}
	if (thisisit < 0) {
		AllGigMaps[len] = gigmap_initmap(gigid);
		thisisit = len;
	}
	return AllGigMaps[thisisit];
}

function isProbablyBlack(element){
		var mydiv = document.getElementById(element);   //returns rgb(1,2,3);
		var rgb = getComputedStyle(mydiv).getPropertyValue("color");
		var isblack = false;
		var r = -1;
		var g = -1;
		var b = -1;

		var start = rgb.indexOf("(");
		var end   = rgb.indexOf(",");
		if (end > start) {
			r = rgb.substring(start+1, end);
			rgb = rgb.substring(end+1);
		}

		end   = rgb.indexOf(",");
		if (end > 0) {
			g = rgb.substring(1, end);
			rgb = rgb.substring(end+1);
		}

		end   = rgb.indexOf(")");
		if (end > 0) {
			b = rgb.substring(1, end);
		}
		
		var total = parseInt(r)+parseInt(g)+parseInt(b);
 		if (total >= 450) isblack = true;
 		return isblack;
}


function gigmap_initmap (gigid) {
	var zoom = 5;	
	var firstOrigin = new google.maps.LatLng(64.6482781, -16.72); //Kverkfjöll in Iceland
	var mycanvas=CANVAS+gigid;

	var mymap = {
		map_id: gigid,
		width:  document.getElementById(mycanvas).offsetWidth,
		darkback: isProbablyBlack(mycanvas),
  	map:    null,
		bounds: null,
	};	

  var myOptions = {
    zoom:zoom,
    center: firstOrigin,
    panControl:true,
    zoomControl:true,
    mapTypeControl:true,
    scaleControl:true,
    streetViewControl:true,
    overviewMapControl:true,
    rotateControl:true,  
    mapTypeId: google.maps.MapTypeId.TERRAIN 
  };
   mymap.map = new google.maps.Map(document.getElementById(mycanvas), myOptions);
	 mymap.bounds = new google.maps.LatLngBounds();
	 
	 // finally open a window with full width
	var h = Math.round(window.innerHeight*.8);
	document.getElementById(mycanvas).style.height = h+'px';
	
	return mymap;
}  

function gigmapComposeInfoText(mymap, gigmap_venue){
	var numberofgigs = gigmap_venue.all_gigs_of_Venue.length;
	// We begin with a FIX for Bootstrap and Google Maps Info window styles problem (close control not visible)
	var iWcontents = '<style type="text/css">img[src*="gstatic.com/"], img[src*="googleapis.com/"] {max-width: none !important;}</style>';	
	 
	//set background or other style arguments - ifgiven by template. Otherwise set default background.
	if (gigmap_venue.all_gigs_of_Venue[0].infowdwdiv == ''){
		if (mymap.darkback) iWcontents += '<div style="background-color: #222";>';
		else iWcontents += '<div>';
	}
	else iWcontents += gigmap_venue.all_gigs_of_Venue[0].infowdwdiv;

 	iWcontents += gigmap_venue.all_gigs_of_Venue[0].firstline;
 	iWcontents += gigmap_venue.all_gigs_of_Venue[0].secondline;

 	iWcontents += '<ul>';
		 	
 	for (var j = 0; j < numberofgigs; j++){
 		iWcontents += '<li>';
 		iWcontents += '<span class =' + gigmap_venue.all_gigs_of_Venue[j].dateclass + '>'; 
 		iWcontents += '<a href="#' + gigmap_venue.all_gigs_of_Venue[j].gigid + '">';
 		//set the date for each gig-line...
 		iWcontents += gigmap_venue.all_gigs_of_Venue[j].str_gigdate_from;
 		if (gigmap_venue.all_gigs_of_Venue[j].str_gigdate_from != 
 				gigmap_venue.all_gigs_of_Venue[j].str_gigdate_to) {
 			iWcontents += ' - ' + gigmap_venue.all_gigs_of_Venue[j].str_gigdate_to;	
 		}	
 		iWcontents += '</a></span>';  //end of dateclass
 		iWcontents += gigmap_venue.all_gigs_of_Venue[j].performancetext;
 		iWcontents += '</li>';
 	}
 	iWcontents += '</ul>';

	iWcontents += '</div>';
	return iWcontents; 
}

function gigmap_setmarkers (gigid, markerIcons_path, gigmap_venue_collection)
{
	var today = new Date()/1000;   // get the local time in seconds
	today = today - today%86400;   // get the beginning of today in seconds (SECONDS_PER_DAY      = 86400)
	var homelocation=null;
	
	if (document.getElementById("mygmlat"))
	{
				lat=document.getElementById("mygmlat").value;
				lng=document.getElementById("mygmlng").value;
				if (lat) homelocation = new google.maps.LatLng(lat,lng);
	}

		var mymap = gigmap_getMapcontext(gigid);
    var infoWindow = new google.maps.InfoWindow({/*disableAutoPan: true*/});

    var marker;
    var markers = new Array();
		var markerWidth = 22;

    // Add the home marker - if it exists
    if (homelocation) {
    	
			var markX      = 7*markerWidth;   
		  var image = {
		    url: markerIcons_path,
		    // Our markers are 22 pixels wide by 39 pixels tall.
		    size: new google.maps.Size(markerWidth, 15),
		    origin: new google.maps.Point(markX,24),
		    anchor: new google.maps.Point(8, 8)
		  };
     	
      marker = new google.maps.Marker({
        position: homelocation,
        map:   mymap.map,
        icon:  image,
        optimized: false 
      });
      markers.push(marker);
    
      mymap.bounds.extend(homelocation);
    }


    // Add the markers and infoWindows to the map
    for (var i = 0; i < gigmap_venue_collection.length; i++) {
    	var thisvenue = gigmap_venue_collection[i];

    	var locat = new google.maps.LatLng(thisvenue.lat,thisvenue.lng);
    	
			//Marker's tooltip and InfoWindow-contents
			var tooltip    = thisvenue.all_gigs_of_Venue[0].tooltip;
    	var iWcontents = gigmapComposeInfoText(mymap, thisvenue, false);
    	
 
    	//define the right marker, depending on the time-distance of this gig
    	var timeDifference = (thisvenue.ux_closest_gig - today)/86400;   //gig begins in ... days


    	var markX  = -1;			//upper left X-coordinate of the marker (all 7 are in the same file)
    	var shapeIndex = -1;
    	var Zindex = gigmap_venue_collection.length-i;

    	var markerShapes = [[11,27, 17,20, 17,13, 11,9, 5,13, 5,20],            //small marker for the faraway gigs
    											[11,27, 17,17, 18, 8, 11,5, 4, 8, 4,17],						//medium marker
    											[11,28, 19,13, 15, 5, 11,1, 3, 5, 3,13]];						//large marker cor the close gigs.
    	

   		if (timeDifference == 0 || (timeDifference< 0 && thisvenue.sort == 'asc') || (timeDifference> 0 && thisvenue.sort == 'desc')) 
   		{		//today! - this is the most complex case because of the possible multiday events
				markX      = 2*markerWidth; 
				shapeIndex = 2; 									//big marker  
   		}
   		else if (timeDifference < -90) {		//a long time ago! small grey marker
				markX      = 0; 
				shapeIndex = 0;   		 
   		}
			else if (timeDifference < 0) {		//past gigs! medium grey marker
				markX      = markerWidth; 
				shapeIndex = 1;   
   		}
    	else if (timeDifference <=7) {		//this week! big orange marker
				markX      = 3*markerWidth; 
				shapeIndex = 2; 
   		}
    	else if (timeDifference <=30) {		//this month! big red marker
				markX      = 4*markerWidth; 
				shapeIndex = 2;
   		}
   		else if (timeDifference <=91) {		//in the next 3 months! medium pink marker
				markX      = 5*markerWidth; 
				shapeIndex = 1;   	
   		}
    	else {		//later. small blue marker
				markX      = 6*markerWidth; 
				shapeIndex = 0;  
   		}
  	
		  var image = {
		    url: markerIcons_path,
		    // Our markers are 22 pixels wide by 39 pixels tall.
		    size: new google.maps.Size(markerWidth, 39),
		    origin: new google.maps.Point(markX,0),
		    anchor: new google.maps.Point(11, 39)
		  };
		  // Shapes define the clickable region of the icon. The type defines an HTML &lt;area&gt; element 'poly' which
		  // traces out a polygon as a series of X,Y points. The final coordinate closes the poly by connecting to the first coordinate.
		  var shape = {
		      coords: markerShapes[shapeIndex],
//		      coords: [11,27, 17,20, 17,13, 11,9, 5,13, 5,20],
		      type: 'poly'
		  };    	
    	
     	
      marker = new google.maps.Marker({
        position: locat,
        map:   mymap.map,
        title: tooltip,
        icon:  image,
        shape: shape,
        zIndex: Zindex,
        optimized: false   //added 07.05.2016 to fix missing click reaction on mobile devices
      });
      markers.push(marker);

			var iWopen=0;
      google.maps.event.addListener(marker, 'click', (function(map, marker, htmlcontent, isOpen) {
        return function() {
        	if (isOpen==0){
          	infoWindow.setContent(htmlcontent);
          	infoWindow.open(map, marker);
          	isOpen = 1;
          }
          else {
          	infoWindow.close(map, marker);
          	isOpen = 0;
          }
        }
      })(mymap.map, marker, iWcontents, iWopen));
     
      mymap.bounds.extend(locat);
    }
    mymap.map.fitBounds(mymap.bounds);
    
    //do not zoom in too much initialy. We want an overview map. Thanks stackoverflow!
    var listener = google.maps.event.addListener(mymap.map, "idle", function() { 
  		if (mymap.map.getZoom() > 10) mymap.map.setZoom(10); 
  		google.maps.event.removeListener(listener); 
		});
}
<?php
	
// This template displays when you have no shows.
// all vars local to including php

	 echo '<p class="gigpress-empty">';
	 
	 $program_name = '';
	 $no_results_message = ($scope == 'upcoming' 
							? $gpo['noupcoming']
							: ($scope == 'past'
								? $gpo['nopast']
								: 'No performances'
									. ($scope == 'today'
										? ' today'
										: '')))
						 . (isset($dateRange)
								? $dateRange
								: "");							 
	 $no_results_message = wptexturize($no_results_message);
	 
	 if ($program_id)
	 {
		$programs = $wpdb->get_results("SELECT * FROM " . GIGPRESS_ARTISTS
	 			.  ' where artist_id = ' . $wpdb->prepare('%d', $program_id) );
	 	if ($programs)
	 		foreach($programs as $program)
		 	{
		 		$program_name = $program->artist_name;
		 		//$program_id   = $program->artist_id;
		 		echo $no_results_message
		 			 . wptexturize(" for ") . "<a href='"
		 			 . $program->artist_url . "'><h3 class=progtitle >"
		 			 . str_replace(':', ':<br>', wptexturize($program_name))
		 			 . "</h3>" . wptexturize("program info") . "</a><br>";

		 		if ($scope != 'all')
		 		{
		 			if (($scope != 'upcoming') or isset($dateRange) )
		 				echo "<a href=/performances?program_id=" 
		 				 . $program_id . ">"
		 				  . wptexturize('view upcoming performances')
		 				   .  "</a><br>";
		 			if ($scope != 'past')
			 			echo " <a href=/performances/past-performances?program_id="
		 				 . $program_id . ">"
		 				  . wptexturize('view past performances')
		 				   .  "</a><br>";
		 		}
		 		$program_name = "?title=" . urlencode($program_name); // for /pastPerfs.html
		 	}
	 	else
	 		echo "<span class=error>invalid program id: " . $program_id . "</span>";
	 }
	 else
		 echo $no_results_message;
?>
</p>
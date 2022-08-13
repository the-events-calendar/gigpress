<?php
/*
	bostoncamerata.org
*/

	echo '<div class="postDivider"><img class="postDivider" src="/graphics/blogdivider.png"></div>';
	echo '<div class="gigpress-artist" id="program-' . $showdata['artist_id']
		     . '"><h1 class=progtitle>'
		      . bc_bankhead($showdata['artist'])
		       . '</h1>';

		echo '<div class="more-info info-right">';
		echo 	'<a href="/performances/?program_id='
				  . $showdata['artist_id'] . '">Upcoming&nbsp;Performances</a> '
				  . '&nbsp;&nbsp;&nbsp; <a class=more-info href="/performances/past-performances/?program_id='
				  . $showdata['artist_id'] . '">Past&nbsp;Performances</a>';
		if(!empty($gpo['artist_link'])
		  && !empty($showdata['artist_url'])
		  && (strpos($showdata['artist_url'],"#program-" . $showdata['artist_id']) === false))
			echo '&nbsp;&nbsp;&nbsp;<a class=more-info href="' . esc_url($showdata['artist_url']) . '"'
				 . gigpress_target($showdata['artist_url']) . '>read more...</a>';

		if(!empty($gpo['display_subscriptions']))
			echo '<div class="gigpress-artist-subscriptions">'
				 . '<a href="'. GIGPRESS_RSS . '&amp;program_id=' . $showdata['artist_id']
				 . '" alt="subscribe to RSS feed" title="subscribe to RSS feed">'
				 . '<img src="' . plugins_url('/gigpress/images/feed-icon-12x12.png') . '" /></a>'
		    . '&nbsp;<a href="' . GIGPRESS_WEBCAL . '&amp;program_id=' . $showdata['artist_id'] 
				 . '" alt="subscribe to iCalendar" title="subscribe to iCalendar">'
			     . '<img src="'. plugins_url('/gigpress/images/icalendar-icon.gif') . '" /></a>'
			    . '</div>';
		echo '</div><br clear=both>';
			    
		if(!empty($showdata['program_notes'])) : ?>
			<div class="info-left prog-note" id="prognote-<?php echo $showdata['artist_id']; ?>"> <!-- start prog-note -->
			<?php echo $showdata['program_notes']; ?>
			</div>
			<div class="info-left prog-note-toggle" style="display:none;">&nbsp;&nbsp;<a title='click to show/hide program notes' onclick="return showInfo('prognote-<?php echo $showdata['artist_id']; ?>')" >program notes</a></div>
	<?php endif;
?>
	</div><!-- end prog-list -->

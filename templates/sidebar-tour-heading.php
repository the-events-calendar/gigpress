<?php
	
// 	STOP! DO NOT MODIFY THIS FILE!
//	If you wish to customize the output, you can safely do so by COPYING this file
//	into a new folder called 'gigpress-templates' in your 'wp-content' directory
//	and then making your changes there. When in place, that file will load in place of this one.

// This template displays prior to shows in our sidebar listing that are part of a tour.
// If several shows belonging to the same tour are displayed in sequence,
// this template only displays before the first show.
// By default, all shows within a tour have the 'gigpress-tour' class applied to their row as well
// (so that you can visually group them).

?>

<li>
	<span class="gigpress-list-tour-heading"><?php if(isset($gpo['tour_label']) && !empty($gpo['tour_label'])) echo wptexturize($gpo['tour_label']) . ': '; echo $showdata['tour']; ?></span>
	<ul>
	
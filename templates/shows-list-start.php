<!-- gigpress shows start-list -->
<?php
	$current_year  = ( $artist ? date('Y', current_time('timestamp')) : 0 );
	$current_month = 0;
	$current_day = 0;
	$current_program = "";
	$current_venue   = "";
	$date_mysql_format = '%Y-%m-%d';
	$monthnames = array("0", "january", "february", "march", "april", "may", "june", "july", "august", "september", "october", "november", "december");
	$title_out = FALSE;
 ?>

<!-- end shows start-list -->

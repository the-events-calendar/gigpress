<?php

function gigpress_ical() {
	
	global $wpdb, $gpo;
	$further_where = '';
	if(isset($_GET['show_id'])) {
		$further_where .= $wpdb->prepare(' AND s.show_id = %d', $_GET['show_id']);
	}
	if(isset($_GET['artist'])) {
		$further_where .= $wpdb->prepare(' AND s.show_artist_id = %d', $_GET['artist']);
	}
	if(isset($_GET['tour'])) {
		$further_where .= $wpdb->prepare(' AND s.show_tour_id = %d', $_GET['tour']);
	}
	if(isset($_GET['venue'])) {
		$further_where .= $wpdb->prepare(' AND s.show_venue_id = %d', $_GET['venue']);
	}
	$limit = (!empty($gpo['rss_limit'])) ? $gpo['rss_limit'] : 100;
	
	$shows = $wpdb->get_results(
		$wpdb->prepare("SELECT * FROM " . GIGPRESS_ARTISTS . " AS a, " . GIGPRESS_VENUES . " as v, " . GIGPRESS_SHOWS ." AS s LEFT JOIN  " . GIGPRESS_TOURS . " AS t ON s.show_tour_id = t.tour_id WHERE show_status != 'deleted' AND s.show_artist_id = a.artist_id AND s.show_venue_id = v.venue_id" . $further_where . " AND s.show_expire >= '" . GIGPRESS_NOW . "' ORDER BY s.show_date ASC, s.show_expire ASC, s.show_time ASC LIMIT %d", $limit)
	);
	if($shows) {
		$count = 1;
		$total = count($shows);
		foreach($shows as $show) {
			$showdata = gigpress_prepare($show, 'ical');
			if(isset($_GET['artist'])) {
				$filename = sanitize_title($showdata['artist_plain']) . '-icalendar';
				$title = $show->artist_name;
			} elseif(isset($_GET['tour'])) {
				$filename = sanitize_title($showdata['tour']) . '-icalendar';
				$title = $show->tour_name;
			} elseif(isset($_GET['venue'])) {
				$filename = sanitize_title($showdata['venue_plain']) . '-icalendar';
				$title = $show->venue_name;
			} elseif(isset($_GET['show_id'])) {
				$filename = sanitize_title($showdata['artist_plain']) . '-' . $show->show_date;
				$title = $show->artist_name . ' - ' . $showdata['date'];
			} else {
				$filename = sanitize_title(get_bloginfo('name')) . '-icalendar';
				$title = $gpo['rss_title'];
			}

			if($count == 1) {
				header('Content-type: text/calendar');
				header('Content-Disposition: attachment; filename="' . $filename . '.ics"');	
				echo("BEGIN:VCALENDAR\r\n" . 
				"VERSION:2.0\r\n");
				if($total > 1) {
					echo("X-WR-CALNAME: $title\r\n");
				}
				echo("PRODID:GIGPRESS 2.0 WORDPRESS PLUGIN\r\n".
				"CALSCALE:GREGORIAN\r\n".
				"X-WR-TIMEZONE:Etc/GMT\r\n".
				"METHOD:PUBLISH\r\n".
				"BEGIN:VTIMEZONE\r\n".
				"TZID:GMT\r\n".
				"BEGIN:STANDARD\r\n".
				"DTSTART:20071028T010000\r\n".
				"TZOFFSETTO:+0000\r\n".
				"TZOFFSETFROM:+0000\r\n".
				"END:STANDARD\r\n".
				"END:VTIMEZONE\r\n");
			}
				echo("BEGIN:VEVENT\r\n" . 
				"SUMMARY:" . $showdata['calendar_summary_ical'] . "\r\n" .
				"DESCRIPTION:" . $showdata['calendar_details_ical'] . "\r\n" . 
				"LOCATION:" . $showdata['calendar_location_ical'] . "\r\n" . 
				"UID:" . $showdata['calendar_start'] . '-' . $showdata['id'] . '-' . get_bloginfo('admin_email') . "\r\n" .
				"URL:" . $showdata['permalink'] . "\r\n");
				if(strlen($showdata['calendar_start']) == 8) {
					echo("DTSTART;VALUE=DATE;TZID=GMT:" . $showdata['calendar_start'] . 
					"\r\nDTEND;VALUE=DATE;TZID=GMT:" . $showdata['calendar_end'] . "\r\n");
				} else {
					echo("DTSTART;VALUE=DATE-TIME;TZID=GMT:" . $showdata['calendar_start'] . "\r\n" . 
					"DTEND;VALUE=DATE-TIME;TZID=GMT:" . $showdata['calendar_end'] . "\r\n");
				}
				echo("DTSTAMP:" . date('Ymd') . "T" . date('his') . "Z\r\n" . 
				"END:VEVENT\r\n");
				if($count == $total) {
					echo("END:VCALENDAR");
				}
			$count++;
		}
	} 
}
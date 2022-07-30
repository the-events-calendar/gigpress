<?php

function gigpress_programs($filter = null, $content = null) 
{

	global $wpdb, $gpo;
	$and_where = $limit = '';
	
	extract(shortcode_atts(array(
			'artist' => FALSE,
			'program' => FALSE,
			'exclude' => FALSE,
			'artist_order' => 'alpha'
		), $filter)
	);

	// Query vars take precedence over function vars
	if(isset($_REQUEST['artist_id']))
		$program_id = $_REQUEST['artist_id'];
	if(isset($_REQUEST['program_id']))
		$program_id = $_REQUEST['program_id'];
	if(isset($_REQUEST['exclude']))
		$exclude = $_REQUEST['exclude'];
	if(isset($_REQUEST['artist_order']))
		$artist_order = $_REQUEST['artist_order'];

	if($artist)
		$program_id = $artist;

	ob_start();
	
	if( $program_id )
	{
		$and_where = ' where artist_id = ' . $wpdb->prepare('%d', $program_id);
	}
	else
		echo $content;
			
	$programs = $wpdb->get_results("SELECT * FROM " . GIGPRESS_ARTISTS
	 			. $and_where
				 . " ORDER BY "
				  . (($artist_order == 'custom') 
					 ? "artist_order ASC" 
					 : "artist_alpha ASC"));
	if ($exclude)
		$exclude = explode(",",$exclude);
	else 
		$exclude = array();
		
	if ( count($programs) == 0 )
			include gigpress_template('artists-list-empty');
	else
	{
		include gigpress_template('artists-list-start');
		foreach($programs as $program) 
		{
			if (in_array($program->artist_id, $exclude))
				continue;
			$showdata = array();
			$showdata['artist']        = $program->artist_name;
			$showdata['artist_id']     = $program->artist_id;
			$showdata['artist_url']    = $program->artist_url;
			$showdata['program_notes'] = $program->program_notes;
	
			include gigpress_template('artists-list');
		}
		include gigpress_template('artists-list-end');
	}
	
	echo('<!-- Generated by GigPress ' . GIGPRESS_VERSION . ' gigpress_programs -->
	');
	
	return ob_get_clean();	
}
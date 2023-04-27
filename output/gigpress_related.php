<?php

function gigpress_show_related_auto( $content = '' ) {
	return gigpress_show_related( null, $content );
}

function gigpress_show_related( $args = [], $content = '' ) {

	global $is_excerpt, $wpdb, $gpo, $post;
	if ( $is_excerpt == true || ! is_object( $post ) ) {
		$is_excerpt = false;

		return $content;
	} else {
		$default_args = [
			'scope' => 'all',
			'sort'  => 'asc',
		];
		$arguments    = shortcode_atts( $default_args, $args );

		$sort = strtolower( sanitize_key( $arguments['sort'] ) );
		if ( ! in_array( $sort, [ 'asc', 'desc' ] ) ) {
			$sort = 'asc';
		}

		// Date conditionals based on scope
		switch ( $arguments['scope'] ) {
			case 'upcoming':
				$date_condition = ">= '" . GIGPRESS_NOW . "'";
				break;
			case 'past':
				$date_condition = "< '" . GIGPRESS_NOW . "'";
				break;
			case 'all':
				$date_condition = "IS NOT NULL";
		}

		$artists_table = GIGPRESS_ARTISTS;
		$venues_table  = GIGPRESS_VENUES;
		$shows_table   = GIGPRESS_SHOWS;
		$tours_table   = GIGPRESS_TOURS;

		$shows = $wpdb->get_results(
			$wpdb->prepare(
				"
				SELECT * 
					FROM {$artists_table} AS a,
					     {$venues_table} as v,
					     {$shows_table} AS s 
					     LEFT JOIN {$tours_table} AS t 
					         ON s.show_tour_id = t.tour_id 
					WHERE show_related = %d 
					  AND show_expire {$date_condition}
					  AND show_status != 'deleted' 
					  AND s.show_artist_id = a.artist_id 
					  AND s.show_venue_id = v.venue_id 
					  ORDER BY show_date {$sort}, show_expire {$sort}, show_time {$sort}",
				$post->ID
			)
		);

		if ( $shows != false ) {

			$shows_markup = [];
			ob_start();

			$count       = 1;
			$total_shows = count( $shows );
			foreach ( $shows as $show ) {
				$showdata = gigpress_prepare( $show, 'related' );
				include gigpress_template( 'related' );
				if ( $gpo['output_schema_json'] == 'y' ) {
					$show_markup = gigpress_json_ld( $showdata );
					array_push( $shows_markup, $show_markup );
				}
				$count ++;
			}

			$giginfo = ob_get_clean();

			if ( $gpo['related_position'] == "before" ) {
				$output = $giginfo . $content;
			} else {
				$output = $content . $giginfo;
			}

			if ( ! empty( $shows_markup ) ) {
				$output .= '<script type="application/ld+json">';
				if ( ! defined( "JSON_UNESCAPED_SLASHES" ) ) {
					require_once( GIGPRESS_PLUGIN_DIR . 'lib/upgrade.php' );
					$output .= up_json_encode( $shows_markup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
				} else {
					$output .= json_encode( $shows_markup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
				}
				$output .= '</script>';
			}

			return $output;

		} else {

			return $content;
		}
	}
}
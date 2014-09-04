<?php
	
// 	STOP! DO NOT MODIFY THIS FILE!
//	If you wish to customize the output, you can safely do so by COPYING this file
//	into a new folder called 'gigpress-templates' in your 'wp-content' directory
//	and then making your changes there. When in place, that file will load in place of this one.

// This template opens a list of shows - by default it opens a table,
// but it could also open a list, or be blank if you so desired

?>

<?php
	// Figure out how many columns this table has.  Base is 3 (date, city, venue).
	// If we're NOT grouping by artist, and we're NOT displaying just a single artist, add a column (for artist).
	// If we're displaying the country, add another.
	// We don't use this variable in this template, but we do need it in subsequent templates
	$cols = 3;
	$cols = ($total_artists == 1 || $artist || $group_artists == 'yes') ? $cols : $cols + 1;
	$cols = (!empty($gpo['display_country'])) ? $cols + 1 : $cols;
?>

<table class="gigpress-table <?php echo $scope; ?>" cellspacing="0">
	<tbody>
		<tr class="gigpress-header">
			<th scope="col" class="gigpress-date"><?php _e("Date", "gigpress"); ?></th>
		<?php if( (!$artist && $group_artists == 'no') && $total_artists > 1) : ?>
			<th scope="col" class="gigpress-artist"><?php echo wptexturize($gpo['artist_label']); ?></th>
		<?php endif; ?>
			<th scope="col" class="gigpress-city"><?php _e("City", "gigpress"); ?></th>
			<th scope="col" class="gigpress-venue"><?php _e("Venue", "gigpress"); ?></th>
		<?php if(!empty($gpo['display_country'])) : ?>
			<th scope="col" class="gigpress-country"><?php _e("Country", "gigpress"); ?></th>
		<?php endif; ?>
		</tr>
	</tbody>
	
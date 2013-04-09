<?php

function gigpress_debug() {

	global $gpo;
	
	?>

	<div class="wrap gigpress gp-options">

	<?php screen_icon('gigpress'); ?>			
	<h2>Debug</h2>
	
	<h3>GigPress Constants</h3>
	
	<ul>
		<li><code>GIGPRESS_SHOWS</code>: <?php echo GIGPRESS_SHOWS; ?></li>
		<li><code>GIGPRESS_ARTISTS</code>: <?php echo GIGPRESS_ARTISTS; ?></li>
		<li><code>GIGPRESS_VENUES</code>: <?php echo GIGPRESS_VENUES; ?></li>
		<li><code>GIGPRESS_TOURS</code>: <?php echo GIGPRESS_TOURS; ?></li>
		<li><code>GIGPRESS_VERSION</code>: <?php echo GIGPRESS_VERSION; ?></li>
		<li><code>GIGPRESS_DB_VERSION</code>: <?php echo GIGPRESS_DB_VERSION; ?></li>
		<li><code>GIGPRESS_RSS</code>: <?php echo GIGPRESS_RSS; ?></li>
		<li><code>GIGPRESS_ICAL</code>: <?php echo GIGPRESS_ICAL; ?></li>
		<li><code>GIGPRESS_WEBCAL</code>: <?php echo GIGPRESS_WEBCAL; ?></li>
		<li><code>GIGPRESS_URL</code>: <?php echo GIGPRESS_URL; ?></li>
		<li><code>GIGPRESS_NOW</code>: <?php echo GIGPRESS_NOW; ?></li>
	</ul>
	
	<h3>GigPress Settings</h3>
	
	<ul>
	<?php foreach($gpo as $setting => $value) { ?>
		<li><code><?php echo $setting; ?></code>: <?php echo $value; ?></li>
	<?php
	}	
	?>
	</ul>

	</div>
<?php }
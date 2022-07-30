<?php
//	bostoncamerata.org
?>
<!--gigpress prog-list-end start -->
<?php
	if( $program_id )
	{
		echo "<style type='text/css'>.hero-image { display: none; } </style>";
	 	echo "<p><a class=floatright href='/programs-repertoire/'>view all programs</a></p>";
	}
	else
	{
		echo "<p><a class=floatright href='/programs-repertoire/?artist_order=";
		if ( $artist_order == 'alpha')
			echo "custom'>list in preferred order";
		else
			echo "alpha'>list in alphabetical order";
		echo "</a></p>";
	}
?>

<div class=hide>
<script type="text/javascript">
	var currentInfo = null;
	function showInfo(id)
	{
		if(currentInfo != null)
		{
			currentInfo.style.display = "none";
		}
		if(id != null)
		{
			ci = document.getElementById(id);
			if(currentInfo == ci)
			{
				currentInfo = null;
			} else {
				currentInfo = ci;
				currentInfo.style.display = "block";
			}
			return true;
		}
		return false;
	}
</script></div><!--gigpress list-end end -->
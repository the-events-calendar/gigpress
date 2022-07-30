<?php
	 // This template closes  a list of shows. 
?>
<!--gigpress show-list-end  -->
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
	</script></div><!--gigpress show-list-end  -->


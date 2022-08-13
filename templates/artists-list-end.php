<?php
//	bostoncamerata.org
?>
<!--gigpress list-end start -->
<div class="postDivider"><img class="postDivider" src="/graphics/blogdivider.png"></div>
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
</script>
<!--gigpress list-end end -->
jQuery(function($) {
	$.fn.fadeToggle = function(speed, easing, callback) {
		return this.animate({opacity: "toggle"}, speed, easing, callback);
	};
	$("a.gigpress-links-toggle").on('click',function() {
		target = $(this).attr("href").replace(document.location.href,'');
		$(target).fadeToggle("fast");
		$(this).toggleClass("gigpress-link-active");
		return false;
	});
	$("select.gigpress_menu").on('change',function()
	{
		window.location = $(this).val();
	});
});
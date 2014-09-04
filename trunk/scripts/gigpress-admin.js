$gp=jQuery.noConflict();

$gp(document).ready(function()
	{
		// If we're using the 12-hour clock, then do this magic
		if ( $gp('select#gp_hh.twelve').length > 0 ) {
			var time = $gp('select#gp_hh option:selected').parent().attr('label');
			$gp('span#ampm').text(time);
			
			$gp('select#gp_hh.twelve').change(function()
				{
					var time = $gp('select#gp_hh option:selected').parent().attr('label');
					$gp('span#ampm').text(time);
				}
			);
		}
		
		$gp('tr.gigpress-inactive, tbody.gigpress-inactive').hide();
		
		$gp('input#show_multi').click(function()
			{
				// $gp('tr#expire').toggle();
				// Workaround for IE 8 nonsense
				$gp('tr#expire').toggle($gp('tr#expire').css('display') == 'none');
				this.blur();
			}
		);
		
		$gp('select.can-add-new').change(function()
			{
				var scope = $gp(this);
				var target = $gp(this).attr('id') + '_new';
				if ( $gp('option:selected', scope).val() == 'new') {
					$gp('tbody#' + target).fadeIn();
				} else {
					$gp('tbody#' + target).fadeOut();
				}
			}
		);
		
		// Return a helper with preserved width of cells
		var fixHelper = function(e, ui) {
			ui.children().each(function() {
				$gp(this).width($gp(this).width());
			});
			return ui;
		};
				
		// Sortable artist table
		$gp('img.gp-sort-handle').show();
		$gp('.gigpress-artist-sort').sortable({
			handle: '.gp-sort-handle', 
			axis: 'y',
			helper: fixHelper,
			update : function () { 
		      var order = $gp('.gigpress-artist-sort').sortable('serialize');
		      $gp("#artist-sort-update").load(ajaxurl, order + '&action=gigpress_reorder_artists&cachebuster=' + Math.floor(Math.random()*99999), function()
		      	{
		   			$gp("#artist-sort-update").fadeIn(100, function(){$gp(this).fadeOut(1500)});  
		      	}
		      ); 
		    } 
		});
					
	}
);
(function($) {
	jQuery.fn.displayFeedback = function() {
		$(this).find('li').each(function() {
			var li = $(this);
			li.find('.acknowledge').click(function() {
				li.slideUp('slow');
				return false;
			});
			li.slideDown('slow');
		});		
	};		
	// Add the onload code.
	$(document).ready(function() {
		$('#feedback').displayFeedback();
	});	
})(jQuery);
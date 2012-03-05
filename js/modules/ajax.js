var Celsus = (function(Celsus, $) {
	Celsus.Ajax = function($) {

		return {
			init: function() {

				// Ensure every AJAX call is made with a json header.
				$.ajaxSetup({
					'beforeSend': function(xhr) {
						xhr.setRequestHeader("X-Celsus-Format", "json")
					}
				});
			}
		};
	}($);

	return Celsus;
}(Celsus || {}, jQuery));
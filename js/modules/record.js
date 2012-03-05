(function($) {
	Celsus.Record = function(recordNode, options) {

		// Private members.

		// The record we are augmenting.
		var $record = $(recordNode);

		// The URL where this record can be edited.
		var _editLocation;

		// Public members.
		return {

			/**
			 * Setup function will be called when instance is created.
			 * 
			 * @returns void
			 */
			setup: function() {

				_editLocation = $('link[rel=edit]').attr('href');

				// Create a shortcut to editing the record when double-clicked.
				if (_editLocation) {
					$record.dblclick(function() {
						// For now, let's just do a full redirect.
						window.location = _editLocation;
					}).css( {
						"cursor": "pointer"
					})
				}
			}
		};
	};

	$.fn.record = function(options) {
		return $.fn.encapsulatedPlugin('record', Celsus.Record, this, options);
	};

	// Prepare all the forms on the page.
	$(document).ready(function() {
		$('.record').record();
	});

})(jQuery);
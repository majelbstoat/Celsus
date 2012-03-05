var Celsus = (function(Celsus, $) {

	// Displays feedback passed from the server as a dropdown bar in an element called #feedback.
	Celsus.DisplayFeedback = function($) {

		$.fn.displayFeedback = function(feedback) {
			container = this;
			count = 0;
			$.each(feedback, function() {
				var acknowledge;
				var li = $("<li></li>", {
					"class": this.type,
				}).appendTo(container);

				$("<span></span>", {
					text: this.message
				}).appendTo(li);

				if (this.callback) {
					// Messages that require an acknowledgement trigger a callback URL when clicked.
					acknowledge = $("<a></a>", {
						href: this.callback,
						"class": "acknowledge",
						title: "Acknowledge",
						text: "X",
						click: function() {
							li.slideUp('slow');
						}
					}).appendTo(li);

					li.slideDown('slow');
				} else {
					// For messages that don't require an acknowledgement, show the feedback and then have it disappear.
					count++;
					li.delay(500).slideDown('slow').delay(1000 + (count * 800)).slideUp('slow');
				}
			});
		};

		$.fn.displayFeedback.feedback = [];

		return {
			init: function() {
				$('#feedback').displayFeedback($.fn.displayFeedback.feedback);
			}
		};
	}($);

	return Celsus;
}(Celsus || {}, jQuery));
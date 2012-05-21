/**
 * Plugin generator plugin. Doesn't allow for chaining, but preserves public
 * methods and namespaces, and supports multiple elements.
 * 
 * @author Jamie Talbot
 */
(function($) {

	$.fn.encapsulatedPlugin = function(plugin, Definition, objects, options) {

		// Creates a function that calls the function of the same name on each member of the supplied set.
		function makeIteratorFunction(f, set) {
			return function() {
				var result = [];
				for ( var i = 0; i < set.length; i++) {
					result.push(set[i][f].apply(set[i][f], arguments));
				}
				return result;
			};
		}

		var result = [];
		objects.each(function() {
			if (!$.data(this, plugin)) {
				// Initialise
				var instance = new Definition(this, options);
				if (typeof (instance.setup) !== 'undefined' && $.isFunction(instance.setup)) {
					// If there is a setup function supplied, call it.
					instance.setup();
				}

				// Store the new functions in a validation data object.
				$.data(this, plugin, instance);
			}
			result.push($.data(this, plugin));
		});

		if (result.length > 1) {

			// We now have a set of plugin instances.
			result = $(result);

			// Take the public functions from the Definition and make them available across the set.
			var template = result[0];
			if (template) {
				for ( var i in template) {
					if (typeof (template[i]) !== 'undefined' && $.isFunction(template[i])) {
						result[i] = makeIteratorFunction(i, result);
					}
				}
			}
		} else {
			result = result[0];
		}

		// Finally mix-in a convenient reference back to the objects, to allow for chaining.
		result.$ = objects;

		return result;
	};
}(jQuery));

(function($) {
	Celsus.Validation = function(formNode, options) {

		// Private members.

		// The form to validate.
		var _form = $(formNode);

		// The rules to validate this form against.
		var _rules = {};

		// The elements under validation.
		var _elements = {};

		var _inputs = {};

		// Whether this form is valid or not.
		var _isValid = true;

		var _constraints = {};

		/**
		 * Turns the rule snippets into function definitions which can be called
		 * directly.
		 */
		function _generateConstraints(model) {
			$.each(_rules[model], function(field, definition) {
				var constraints = _rules[model][field].constraints;
				$.each(constraints, function(index, constraint) {
					_rules[model][field].constraints[index]._situation = function(model) {
						return eval(constraint.situation);
					};
					_rules[model][field].constraints[index]._condition = function(model) {
						return eval(constraint.condition);
					};
				});
			});
		}

		/**
		 * Validates the supplied input according to the rules specified on the
		 * form.
		 */
		function _validate(input, highlight) {

			// Whether or not to highlight invalid fields.
			if ('undefined' == typeof (highlight)) {
				highlight = true;
			}
			
			var valid = true;
			var element = $(input);
			var mandatory = false;
			var message;
			var elementName = element.attr('name');

			if (element.attr('disabled')) {
				// The field is disabled, so pass it for checks.
				return true;
			}
			
			var domainBoundary = input.id.indexOf('-');
			var normalisedElementId;
			var model;
			
			if (-1 != domainBoundary) {
				// This is subform element.
				model = input.id.substring(0, domainBoundary);
				normalisedElementId = input.id.substring(domainBoundary + 1); 
			} else {
				// This is a main form element.
				model = 'main';
				normalisedElementId = input.id
			}
			
			// Constraint definitions should have been loaded in a separate file.
			var constraints = _rules[model][normalisedElementId].constraints;

			// Test each constraint
			$.each(constraints, function() {
				if (this._situation(model)) {
					// Situation was met.

					if (this.mandatory) {
						// This situation demands that the field is mandatory.
						mandatory = true;
					}

					if (valid) {
						// Element has been valid to this point, so check the condition,
						// but only test the condition if there is a value, or the field
						// is mandatory.
						if ((mandatory || !element.blank()) && !this._condition(model)) {
							// The condition failed, so the target is invalid, but don't immediately break
							// because other situations might mean that this field is mandatory.
							valid = false;
							message = this.message;
						}
					}
				}
			});

			if (mandatory) {
				// At least one constraint has made this field mandatory.
				$("label[for=" + input.id + "]").addClass('required').removeClass('optional');
			} else {
				$("label[for=" + input.id + "]").addClass('optional').removeClass('required');
			}

			if (valid) {
				// Remove invalid styling and the bubble if it exists.
				element.removeClass('invalid').parent().find(".bubble").remove();
			} else {
				// At least one field failed a condition
				if (highlight) {
					element.addClass('invalid');

					var bubble = element.parent().find(".bubble");

					if (!bubble[0]) {
						var bubble = $("<div></div>", {
							"class": "bubble",
						}).appendTo(element.parent());
						var content = $("<div></div>", {
							"text": message,
							"class": "invalid bubble-contents drop-shadow round",
						}).appendTo(bubble);
						bubble.fadeIn('fast');
					} else {
						bubble.find('.bubble-contents').html(message);
					}
				}
				// Invalidate the form.
				_isValid = false;
			}

			return valid;
		}

		function _validateForm(highlight) {
			_isValid = $.inject(_elements, true, function(result) {
				return this.inject(result, function(result) {
					return _validate(this, highlight) && result;
				});
			});
			return _isValid;
		}
		
		// Public members.
		return {

			/**
			 * Setup function will be called when instance is created.
			 * 
			 * @returns void
			 */
			setup: function() {
			
				$.each(Celsus.Validation.Map, function(model, domain) {
					_rules[model] = Celsus.Validation.Rules[domain];
					
					var modelPrefixLength = ('main' == model) ? 0 : model.length + 1;
					
					// Get the elements of this form that have at least one rule set, as they are the
					// ones we have to validate.
					_elements[model] = _form.find(':input:not(:hidden):not(:submit)').select(function() {
						return (this.id.substring(modelPrefixLength) in _rules[model]);
					});
					
					_inputs[model] = {};
					_elements[model].each(function() {
						var element = $(this);
						_inputs[model][this.id.substring(modelPrefixLength)] = element;
					});

					_generateConstraints(model);

					// @todo Ensure we are generating the validation code for the subforms each time too.
					_elements[model].blur(function() {

						// Find the fields that have to be validated because of this change.
						var triggeredElements = _rules[model][this.id.substring(modelPrefixLength)].triggers;

						// Validate every input in the form that depends on the changed field.
						$.each(_elements[model].select(function() {
							var normalisedElementId = this.id.substring(modelPrefixLength);
							return (-1 != $.inArray(normalisedElementId, triggeredElements));
						}), function() {
							_validate(this);
						});

					});
										
				});
				
				// Register submit handler for the form.
				_form.submit(function() {
					return _validateForm();
				});
			},

			/**
			 * Determines whether the form is valid or not.
			 * 
			 * @returns boolean
			 */
			isValid: function() {
				return _isValid;
			},

			/**
			 * Validates a form.
			 * 
			 * @returns boolean
			 */
			validate: function(highlight) {
				return _validateForm(highlight);
			}			
		};
	};
	Celsus.Validation.Rules = {};
	Celsus.Validation.Map = {};
	Celsus.Validation.validateOnLoad = false;

	$.fn.validation = function(options) {
		if (!$(this).is('form')) {
			// We can only attach this behaviour to forms.
			return;
		}
		return $.fn.encapsulatedPlugin('validation', Celsus.Validation, this, options);
	};

	$.fn.blank = function() {
		return (this.hasClass('blank') || ('' == this.val()));
	};

	// Prepare all the forms on the page.
	$(document).ready(function() {
		$('form.validate').validation().validate(Celsus.Validation.validateOnLoad);
	});

})(jQuery);
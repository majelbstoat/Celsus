/*
	Formerize 0.1: Makes mediocre forms a little less mediocre
	By nodethirtythree design | http://nodethirtythree.com/
	Tested on IE6, IE7, IE8, Firefox 3.6, Opera 10, Safari 5, and Chrome 5.
	Dual licensed under the MIT or GPL license.
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	MIT LICENSE:
	Copyright (c) 2010 nodethirtythree design, http://nodethirtythree.com/
	Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation
	files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use,
	copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the
	Software is furnished to do so, subject to the following conditions:
	The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
	OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
	HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
	FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	GPL LICENSE:
	Copyright (c) 2010 nodethirtythree design, http://nodethirtythree.com/
	This program is free software: you can redistribute it and/or modify it	under the terms of the GNU General Public License as
	published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version. This program is
	distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY 
	or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of
	the GNU General Public License along with this program. If not, see <http://www.gnu.org/licenses/>. 
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
*/
(function($) {
	jQuery.fn.formerize = function() {
		// password fields
		jQuery(this)
			.find('input[type=password]')
				.each(function() {
					var e = jQuery(this);
					var x = jQuery(jQuery('<div>').append(e.clone()).remove().html().replace(/type="password"/i, 'type="text"').replace(/type=password/i, 'type=text'));
					if (e.attr('id') != '')
						x.attr('id', e.attr('id') + '_fakeformerizefield');
					if (e.attr('name') != '')
						x.attr('name', e.attr('name') + '_fakeformerizefield');
					x.addClass('blank').val(x.attr('title')).insertAfter(e);
					if (e.val() == '')
						e.hide();
					else
						x.hide();
					e.blur(function(event) {
						event.preventDefault();
						var e = jQuery(this);
						var x = e.parent().find('input[name=' + e.attr('name') + '_fakeformerizefield]');
						if (e.val() == '') {
							e.hide();
							x.show();
						}
					});
					x.focus(function(event) {
						event.preventDefault();
						var x = jQuery(this);
						var e = x.parent().find('input[name=' + x.attr('name').replace('_fakeformerizefield', '') + ']');
						x.hide();
						e.show().focus();
					});
					// just in case :P
					x.keypress(function(event) {
						event.preventDefault();
						x.val('');
					});
				});
		// text fields
		jQuery(this)
			.find('input[type=text],textarea')
				.each(function() {
					var e = jQuery(this);
					if (e.val() == '' || e.val() == e.attr('title')) {
						e.addClass('blank');
						e.val(e.attr('title'));
					}
				})
				.blur(function() {
					var e = jQuery(this);
					if (e.attr('name').match(/_fakeformerizefield$/))
						return;
					if (e.val() == '') {
						e.addClass('blank');
						e.val(e.attr('title'));
					}
				})
				.focus(function() {
					var e = jQuery(this);
					if (e.attr('name').match(/_fakeformerizefield$/))
						return;
					if (e.val() == e.attr('title')) {
						e.removeClass('blank');
						e.val('');
					}
				});
		// form events
		jQuery(this)
			// submit
			.submit(function() {
				jQuery(this)
					.find('input[type=text],textarea')
						.each(function(event) {
							var e = jQuery(this);
							if (e.attr('name').match(/_fakeformerizefield$/))
								e.attr('name', '');
							if (e.val() == e.attr('title')) {
								e.removeClass('blank');
								e.val('');
							}
						});
			})
			// reset
			.bind("reset", function(event) {
				event.preventDefault();
				// temporary: just set all SELECTs to their first options
				jQuery(this)
					.find('select')
						.val(jQuery('option:first').val());
				jQuery(this)
					.find('input,textarea')
						.each(function() {
							var e = jQuery(this);
							var x;
							e.removeClass('blank');
							switch (this.type) {
								case 'password':
									e.val(e.attr('defaultValue'));
									x = e.parent().find('input[name=' + e.attr('name') + '_fakeformerizefield]');
									if (e.val() == '') {
										e.hide();
										x.show();
									}
									else {
										e.show();
										x.hide();
									}
									break;
								case 'checkbox':
								case 'radio':
									e.attr('checked', e.attr('defaultValue'));
									break;
								case 'text':
								case 'textarea':
									e.val(e.attr('defaultValue'));
									if (e.val() == '') {
										e.addClass('blank');
										e.val(e.attr('title'));
									}
									break;
								default:
									e.val(e.attr('defaultValue'));
									break;
							}
						});
			});
	};	
	// Add the onload code.
	$(document).ready(function() {
		$('.formerize').formerize();
	});		
})(jQuery);
(function() {
    Monkey = window.Monkey || {}
    Monkey.Montage = function(node, options) {

        // Plugin version.
        var _version = '1.0'

        // Width of the montage.
        var _montageWidth = 700;

        // Width of the whitespace borders between images.
        var _borderWidth = 10;

        var _samples = {
            // Boat
            "2bc49532b57c6ba3a52b57db0fe88ee4": {
                w: 800,
                h: 444,
                x: 'jpg'
            },
            // Horses
            "2e0645b28c86093c7eeb773e8f696314": {
                w: 800,
                h: 485,
                x: 'jpg'
            },
            // Hut 
            "80f928be80cd8bb5219f9837d1b98a9c": {
                w: 800,
                h: 1067,
                x: 'jpg'
            },
            // Bike 
            "2d7b614ae79ce4942b489ccee0039bea": {
                w: 800,
                h: 444,
                x: 'jpg'
            },
            // Camel 
            "13719ad0adfd1006d03901d2ebfa0832": {
                w: 800,
                h: 533,
                x: 'jpg'
            },
            // Smile
            "f86106ff54cff8248c06fac6cb98e144": {
                w: 800,
                h: 533,
                x: 'jpg'
            }
        };
        
        // Store of original image dimensions.
        var _dimensions = {};

        /**
        * Makes an element draggable.
        */
        function _makeDraggable(element) {
            element.draggable({
                revert: 'invalid',
                cursor: 'move',
                cursorAt: {
                    left: 50,
                    top: 5
                },
                helper: function() {
                    // Create a semi-transparent clone that can be dragged.
                    clone = $(this).clone();
                    var thumbnailWidth = 100;
                    var width = clone.attr('width');
                    var height = clone.attr('height') * (thumbnailWidth / width);
                    $(clone[0]).css({
                        width: thumbnailWidth,
                        height: height,
                        opacity: 0.3,
                        left: 50,
                        zIndex: 5
                    });
                    return clone;
                },
                start: function(event, ui) {
                    $('.grid').toggle();
                },
                stop: function(event, ui) {
                    $('.grid').toggle();
                }
            });
        }


        function _recalculateGrid() {

            // Get rid of the old grid.
            $('.grid').remove();

            // Recreate the grid container.
            var container = $('.montage');
            container.prepend('<div class="grid" style="display:none;overflow:hidden;position:absolute;left:0;top:0;width:' + _montageWidth + 'px;height:100%;">');
            var grid = $('.grid');
            var totalHeight = 0;

            var montageRows = $('.rows li');

            // Iterate through each row of the montage, calculating cells as we go.
            montageRows.each(function(row, item) {

                var images = $('img', this); 
                var height = $(images[0]).attr('height');

                // Place the zone for the 'above row'.
                grid.append('<div class="cell" id="' + row + '" style="width:' + (_montageWidth - (2 * _borderWidth)) + 'px;height:' + (_borderWidth) + 'px;position:absolute;left:' + (_borderWidth) + 'px;top:' + (totalHeight) + 'px;"></div>');

                // Iterate through each image in the row.
                images.each(function(index) {
                
                    var image = $(this);

                    // Place the zone for 'to the left of' each image.
                    var width = image.attr('width');
                    var left = 0;
                    grid.append('<div class="cell" id="' + row + '_' + index + '" style="width:' + (_borderWidth) + 'px;height:' + (height) + 'px;position:relative;float:left;left:' + left + 'px;top:' + ((row + 1) * _borderWidth) + 'px"></div>');

                    // Place the zone for the 'left half of' each image
                    var half = Math.round(width / 2);
                    var remainder = width - half;
                    grid.append('<div class="cell" id="' + row + '_' + index + '_' + 'l' + '" style="width:' + half + 'px;height:' + (height) + 'px;position:relative;float:left;left:' + (left) + 'px;top:' + ((row + 1) * _borderWidth) + 'px;"></div>');

                    // Place the zone for the 'right half of' each image
                    grid.append('<div class="cell" id="' + row + '_' + index + '_' + 'r' + '" style="width:' + remainder + 'px;height:' + (height) + 'px;position:relative;float:left;left:' + (left) + 'px;top:' + ((row + 1) * _borderWidth) + 'px;"></div>');
                }); 

                // Place the zone for the 'right of row'.
                grid.append('<div class="cell" id="' + row + '_' + images.length + '"  style="width:' + (_borderWidth) + 'px;height:' + (height) + 'px;position:relative;float:left;top:' + ((row + 1) * _borderWidth) + 'px"></div>');

                totalHeight += parseInt(height) + parseInt(_borderWidth);
            });

            // Place the zone for 'after last row'.
            grid.append('<div class="cell" id="' + montageRows.length + '" style="width:' + (_montageWidth - (2 * _borderWidth)) + 'px;height:' + (_borderWidth) + 'px;position:absolute;left:' + (_borderWidth) + 'px;top:' + (totalHeight) + 'px;"></div>');

            // Now allow the grid cells to accept dropped images.
            $('.cell').droppable({
                activeClass: 'active-cell',
                hoverClass: 'hover-cell',
                tolerance: 'pointer',
                drop: function(event, ui) {
                    var image = ui.draggable;
                    if ((/^\d$/).test(this.id)) {
                        // We are dropping the image into a brand-new row.

                        var row = parseInt(this.id);
                        var numRows = $('.rows li').length; 
                    
                        // Fade the image out, then reinstate it in the new
                        // location.
                        image.fadeOut(function() {
                            if (row < numRows) {
                                // We are dropping before an existing row.
                                $('<li></li>').append(image).append('<div class="clear"></div>').insertBefore('.rows li:eq(' + row + ')');
                            } else {
                                // We are dropping at the end of the montage.
                                $('<li></li>').append(image).append('<div class="clear"></div>').appendTo('.rows');
                            }
                            
                            image.toggle();
                            _cleanUp();
                        });
                    } else if ((/^\d_\d.*/).test(this.id)) {
                        // We are dropping somewhere into an existing row.
                        var components = this.id.split("_");
                        var row = parseInt(components[0]);
                        var column = parseInt(components[1]);
                                
                        if (3 == components.length && "r" == components[2]) {
                            // If we are dropping on the right half of an
                            // image, that is like dropping to the left of the
                            // next image.
                            column++;
                        }
                            
                        // Fade the image out, then reinstate it in the new
                        // location.
                        image.fadeOut(function() {
                            
                            var targetRow = $('.rows li:eq(' + row + ')');
                            var numColumns = targetRow.find('img').length;
                                        
                            if ($('.rows li:eq(' + row + ') img:eq(' + column + ')').length) {
                                // We are inserting before an image.
                                image.insertBefore('.rows li:eq(' + row + ') img:eq(' + column + ')');
                            } else {
                                // We are inserting at the end of the row,
                                // before the clear div.
                                image.insertBefore('.rows li:eq(' + row + ') .clear');
                            }
                            image.toggle();
                            _cleanUp();
                        });
                    }
                }
            });
        }

        /**
         * Removes rows that have no images in them and recalculates the
         * width of the montage.
         */
        function _cleanUp() {
            $('.rows li').filter(function() {
                return $(this).find('img').length < 1;
            }).remove();
            _updateWidth();
        }

        /**
         * Updates the montage after the width has been changed.
         */
        function _updateWidth() {

            // Update the widths of the montage and the container.
            $('.rows').css({
                width: _montageWidth + 'px',
            });
            $('.montage').css({
                width: _montageWidth + 'px',
            });

            rows = $('.rows li');
            var minHeight = 10000;

            // Iterate each row and resize the images accordingly.
            rows.each(function(i) {

                // First, determine the minimum height for this row.
                var images = $('img', this);
                images.each(function(j) {
                    if ($(this).attr('height') < minHeight) {
                        minHeight = $(this).attr('height');
                    } 
                }); 

                var availableWidth = _montageWidth - _borderWidth;
                var totalWidth = 0;

                var partiallyScaled = {};

                // Now, scale all the images in the row to be the same height.
                images.each(function() {
                    var scaledImageWidth = _dimensions[this.id].w * (minHeight / _dimensions[this.id].h);

                    partiallyScaled[this.id] = scaledImageWidth;
                    totalWidth += scaledImageWidth;
                    availableWidth -= _borderWidth;
                });

                // Now, fit all the images into the specified width.
                var uniformScalingFactor = availableWidth / totalWidth;
                totalWidth = 0;
                images.each(function() {
                    $(this).attr('height', Math.round(minHeight * uniformScalingFactor));
                    var finalWidth = Math.floor(partiallyScaled[this.id] * uniformScalingFactor);
                    $(this).attr('width', finalWidth);
                    totalWidth += finalWidth;
                });

                // Now do a final stage of pixel correction if necessary, due to 
                // rounding errors.
                var difference = availableWidth - totalWidth;
                images.each(function() {
                    if (!difference) {
                        // break.
                        return false;
                    }
                    $(this).attr('width', parseInt($(this).attr('width')) + 1);
                    difference--;
                });
            });

            _recalculateGrid();
        }

        return {
            setup: function() {
                 
                // Allow the user to select the width of the final montage. 
                $('#width').change(function(element) {
                    var value = $(this).val();
                    if (/^\d+$/.test(value)) {
                        _montageWidth = value;
                        _updateWidth();
                    } else {
                        $(this).val(_montageWidth);
                    }

                });

                // Set up the AJAX file uploader.
                $('#image').fileUpload({
                    callback: function(json) {
                        result = $.parseJSON(json);
                        if (result && result.success) {

                            // Store the dimensions.
                            _dimensions[result.name] = {
                                w: result.width,
                                h: result.height,
                                x: result.filename.substring(result.filename.lastIndexOf('.') + 1)
                            };

                            if ($('.rows li').length) {
                                // There is at least one image in the montage,
                                // so put it in the waiting room.
                                $('.rows li:eq(0)').prepend('<img id="' + result.name + '" name="' + result.name + '" src="cache/' + result.filename + '">').show();
                            } else {
                                // This is the first image, so just put it
                                // straight into the montage.
                                $('<li></li>').append('<img id="' + result.name + '" name="' + result.name + '" src="cache/' + result.filename + '"><div class="clear">').appendTo('.rows');
                            }
                            _updateWidth();
                            _makeDraggable($('#' + result.name));

                            return true;
                        } else {
                            return false;
                        }
                    }

                });

                // Bind the sample data link.
                $('#load-samples').click(this.loadSamples);

                // Bind the download link.
                $('#hard-copy').click(this.getHardCopy);
            },

            /**
             * Allows the montage to be updated manually.
             */
            update: function() {
                _updateWidth();
            },

            /**
             * Loads some sample images and creates a montage from it.
             */
            loadSamples: function() {

                $('.rows li').remove();
                $('#width').val(700).change();

                // Set the dimensions.
                _dimensions = $.extend({}, _samples);

                var i = 0;
                for (image in _samples) {
                    if (i++ % 2 == 0) {
                        // Create a new row.
                        $('.rows').append('<li></li>');
                    }
                    $('.rows li:last-child').append('<img id="' + image + '" src="cache/' + image + '.jpg" width="' + _samples[image].w + '" height="' + _samples[image].h + '">');
                }
                _makeDraggable($('.rows li img'));
                $('.rows li').append('<div class="clear">');
                _updateWidth();
            },

            /**
             * Sends a request to the server for a hard-copy of the montage and
             * presents it to the user for download.
             */
            getHardCopy: function() {
                if (!$('.rows li').length) {
                    // No images to generate from.
                    return false;
                }

                var $button = $(this).hide();
                var $progress = $('#hard-copy-progress').show();

                // Create message.
                var montage = [];
                $('.rows li').each(function() {
                    var row = {};
                    row.images = [];
                    $('img', this).each(function() {
                        var id = $(this).attr('id');
                        row.height = $(this).attr('height');
                        var image = {
                            name: id,
                            width: $(this).attr('width'),
                            height: $(this).attr('height'),
                            extension: _dimensions[id].x
                        };
                        row.images.push(image);
                    });
                    montage.push(row);
                });

                var message = {
                    width: _montageWidth,
                    border: _borderWidth,
                    montage: montage
                };

                $.post('generate/', {
                    data: message
                }, function(response) {
                    $button.show();
                    $progress.hide();
                    var result = $.parseJSON(response);
                    if (result && result.success) {
                        window.location = 'generate/output/?file=' + result.name;
                    }
                });

            }

        }

    }

    $.fn.montage = function(options) {
        return $.fn.encapsulatedPlugin('montage', Monkey.Montage, this, options);
    };
})(jQuery);
    

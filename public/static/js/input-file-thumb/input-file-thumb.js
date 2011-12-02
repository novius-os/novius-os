/*
 * jQuery inputFileThumb Plugin
 *
 * Copyright (c) 2011 Novius Labs (Gilles FELIX & Antoine LEFEUVRE)
 *
 * version: 1.0.1 (30-JUN-2011)
 * @requires jQuery v1.4.4 or later
 * @optional bgiframe plugin to deal with IE z-index issues
 *
 * Examples and documentation at: http://www.novius-labs.com/contributions/jquery-plugin-inputfile/
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */
(function($) {
/*
    Usage Note:
    -----------
    Use only on an input type file

    $(document).ready(function() {
        $(':file').inputFileThumb({
            'file'                  : 'url', // URL of the existing file (the file which has been uploaded)
            'title'                 : 'Title', // The title of the file (appears in the details layer, the layer displayed when the mouse is over the thumbnail)
            'width'                 : 58, // Width in pixels of the thumbnail
            'height'                : 58, // Height in pixels of the thumbnail
            'description'           : 'More info about the file (appears in the details layer)',
            'extensions'            : [], // Array of allowed extensions for the file
            'deleteInput'           : false, // Give the name of a hidden input if you need one to catch the delete action
            'displayExtension'      : true, // Show the file extension (required or effective) in the thumbnail
            'orientation'           : '', // Orientation of the details layer. Leave blank for automatic detection (layer right of the thumbnail when possible). Set to 'rtl' to force the layer on the left.
            'classes'               : '', // You may create custom classes to change the thumbnail's default style. See CSS for further details.
            'addTitle'              : 'Add', // Label of the add link
            'editTitle'             : 'Edit', // Label of the edit link
            'delTitle'              : 'Delete', // Label of the delete link
            'wrongExtensionMessage' : 'This extension is not allowed', // Message returned when an unsupported file type is selected. Unused when cbCheckExt is a function.
            'cbCheckExt'            : false, // A callback function called when an unsupported file type is selected. This fonction must take two parameters: the wrong extension and the array of allowed extensions
            'cbVisualize'           : false // A callback function called when the user clicks on the thumbnail. The function's context is the link over the thumbnail.
        });
    });
*/

    // Constructor of the inputFileThumb. Builds the thumbnail and the details layer
    function InputFileThumb(input, options) {

        // The input has already been transformed. Used to modify the options.
        if (input.InputFileThumb) {
            input.InputFileThumb.options = $.extend(input.InputFileThumb.options, options || {});
            input.InputFileThumb.modify(true);
            return;
        }

        var object      = this;
        this.openchoose = false;
        this.input      = $(input).change(function(e) {
            object.modify();
            object.hide();
        }).click(function(e) {
            if ($.browser.webkit && !object.openchoose) {
                object.openchoose = setTimeout(function() {
                    $('body').one('mousemove', function() {
                        object.openchoose = false;
                        object.hide();
                    });
                }, 500);
            }
        });
        this.options = {};
        $.extend(this.options, this.defaultSettings, options || {});
        if (!$.isFunction(this.options.cbVisualize) && this.options.file) {
            this.options.cbVisualize = function() {
                window.open(object.options.file);
            };
        }

        var title = this.options.title;
        if (!title) {
            title = $(input).attr('title');
        }
        if (!title) {
            title = $(input).attr('name');
        }
        this.container   = $('<span class="inputFileThumb"></span>').insertAfter(this.input);
        this.thumb       = $('<span class="thumb"><span class="bg"></span></span>').mouseenter(function() {
            object.show();
        }).appendTo(this.container);
        this.hover       = $('<div class="hover"></div>').mouseleave(function() {
            if (!$.browser.webkit || !object.openchoose) {
                object.hide();
            }
        }).appendTo(this.container);
        this.fileThumb   = $('<span class="fileThumb"></span>').appendTo(this.hover);
        this.fileDesc    = $('<span class="fileDesc"><span class="fileTitle">' + title + '</span><span class="fileInfo">' + this.options.description + '</span></span>').appendTo(this.hover);
        this.fileActions = $('<div class="fileActions"></div>').appendTo(this.hover);
        this.addFile     = $('<span class="addFile">' + this.options.addTitle + '</span>').appendTo(this.fileActions);
        this.editFile    = $('<span class="editFile">' + this.options.editTitle + '</span>').appendTo(this.fileActions);
        if (this.options.deleteInput) {
            this.deleteInput = $('<input type="hidden" class="deleteInput" name="' + this.options.deleteInput + '" value="" />').appendTo(this.fileActions);
            this.deleteFile  = $('<span class="deleteFile">' + this.options.delTitle + '</span>').appendTo(this.fileActions).click(function(e) {
                e.stopImmediatePropagation();
                object.remove();
            });
        }
        this.modify(true);
        input.InputFileThumb = this;
        return;
    }

    $.extend(InputFileThumb.prototype, {
        // The default settings
        defaultSettings : {
            'file'                  : '',
            'title'                 : '',
            'width'                 : 58,
            'height'                : 58,
            'description'           : '',
            'extensions'            : [],
            'deleteInput'           : false,
            'displayExtension'      : true,
            'orientation'           : '', //ltr ou rtl
            'classes'               : '',
            'addTitle'              : 'Add',
            'editTitle'             : 'Edit',
            'delTitle'              : 'Delete',
            'wrongExtensionMessage' : 'This extension is not allowed',
            'cbCheckExt'            : false,
            'cbVisualize'           : false
        },
        // Predefined metatypes of files with associated extensions
        extensionsType : {
            'image'   : ['gif', 'jpg', 'jpeg', 'png', 'bmp']
        },
        // Modify the thumbnail depending on the selected file's extension
        iconByExtension : function(ext) {
            this.thumb.empty().append('<span class="bg"></span>').css({'text-align' : 'right', 'cursor' : 'default'});
            var object    = this;
            var classes = this.options.classes;
            if (!classes) {
                $.each(this.extensionsType, function(type, extensionsType) {
                    var ok = false;
                    if (object.options.extensions.length > 0) {
                        ok = true;
                        $.each(object.options.extensions, function(i, val) {
                            if ($.inArray(val, extensionsType) == -1) {
                                ok = false;
                                return true;
                            }
                            return false;
                        });
                    }
                    if (ok) {
                        classes = type;
                        return true;
                    } else {
                        if ($.inArray(ext, extensionsType) != -1) {
                            classes = type;
                            return true;
                        }
                    }
                    return false;
                });
            }
            if (ext) {
                var div = $('span.bg', this.thumb).addClass(classes + ' on');
                if (this.options.displayExtension && ext != '-') {
                    div.append('<span class="' + ext + '">' + ext + '</span>');
                }
            } else {
                var div = $('span.bg', this.thumb).addClass(classes);
                if (this.options.displayExtension && this.options.extensions.length == 1) {
                    div.append('<span class="' + this.options.extensions[0] + '">' + this.options.extensions[0] + '</span>');
                }
            }
        },
        // Called when the file input's value changes (init set to false)
        // or when the input file thumb is initialised (init set to true). The existing file is then taken into account.
        modify          : function(init) {
            init          = init || false;
            var ext       = false;
            var file      = this.input.val();
            if (init) {
                file      = this.options.file;
            }
            if (file) {
                ext       = '-';
                var found = file.match(/.+\.(\w{2,4})(\?|$)/gi);
                if ($.isArray(found) && found.length) {
                    ext = RegExp.$1;
                }
                if (this.options.extensions.length && $.inArray(ext, this.options.extensions) == -1) {
                    if ($.isFunction(this.options.cbCheckExt)) {
                        this.options.cbCheckExt(ext, this.options.extensions);
                    } else {
                        alert(this.options.wrongExtensionMessage);
                    }
                    this.input.val('');
                    this.modify();
                    return;
                }
                this.hover.css('cursor', 'default');
                this.input.appendTo(this.editFile);
                this.addFile.hide();
                this.editFile.show();
                if (this.deleteInput) {
                    this.deleteInput.val('');
                    this.deleteFile.show();
                }
            } else {
                this.input.appendTo(this.hover);
                this.hover.css('cursor', 'pointer');
                this.addFile.show();
                this.editFile.hide();
                if (this.deleteInput) {
                    this.deleteFile.hide();
                }
            }
            this.iconByExtension(ext);
            var self = this;
            if (init && file && $.inArray(ext, this.extensionsType.image) != -1) {
                this.thumb.empty().css({'text-align' : 'center', 'cursor' : 'pointer'}).width(this.options.width).height(this.options.height);
                var thumbSize = {
                    width  : this.thumb.innerWidth(),
                    height : this.thumb.innerHeight()
                };
                $('<img src="' + file + '" />').load(function() {
                    var width  = $(this).width();
                    var height = $(this).height();
                    if (height > width) {
                        if (width > thumbSize.width) {
                            $(this).width(thumbSize.width + 'px');
                        }
                    } else {
                        if (height > thumbSize.height) {
                            $(this).height(thumbSize.height + 'px');
                        } else {
                            $(this).css('margin-top', (thumbSize.height - height) / 2 + 'px');
                        }
                    }
                    $(this).css({display: ''}).appendTo(self.thumb);
                }).css({display: 'none'}).appendTo($('body'));
            } else {
                this.options.cbVisualize = false;
                $('span.bg', this.thumb).css('line-height', this.options.height + 'px').add(this.thumb).width(this.options.width).height(this.options.height);
            }
        },
        // Called when the user clicks on the delete action
        remove              : function() {
            this.deleteInput.val('1');
            this.options.file = '';
            this.input.val('');
            this.modify();
            this.hide();
        },
        // Called when the mouse is over the thumbnail to display the details layer
        show                : function() {
            this.hover.css({
                'top'     : 0,
                'left'    : 0,
                'opacity' : 0
            }).show();
            this.fileThumb.css('float', 'left');
            this.fileDesc.css('margin-left', '10px');
            this.fileActions.css('margin-left', (this.options.width + 10) + 'px');
            var clone = this.thumb.clone(false).appendTo(this.fileThumb);
            if ($.isFunction(this.options.cbVisualize)) {
                clone.bind('click', this.options.cbVisualize);
            }
            var ofst    = this.thumb.offset();
            var left    = ofst.left;
            var top     = ofst.top;
            var parent  = this.thumb.offsetParent().not('body');
            if (parent.size()) {
                ofst    = parent.offset();
                left    -= ofst.left;
                top     -= ofst.top;
            }
            var padding = {
                left  : this.hover.css('padding-left').replace('px', ''),
                top   : this.hover.css('padding-top').replace('px', ''),
                right : this.hover.css('padding-right').replace('px', '')
            };
            if (this.options.orientation == 'rtl' || (left - padding.left + this.hover.outerWidth()) > $(window).width()) {
                this.fileThumb.css('float', 'right');
                this.fileDesc.css({
                    'margin-right' : '10px',
                    'margin-left'  : '0px'
                });
                this.fileActions.css({
                    'margin-right' : (this.options.width + 10) + 'px',
                    'margin-left'  : '0px'
                });
                this.hover.css({'top' : top - padding.top, 'left' : left + padding.right + this.thumb.outerWidth() - this.hover.outerWidth()});
            } else {
                this.hover.css({'top' : top - padding.top, 'left' : left - padding.left});
            }
            if ($.fn.bgiframe) {
                this.hover.bgiframe();
            }
            this.hover.css('opacity', 1);
        },
        // Called when the mouse leaves the details layer to hide it
        hide                : function() {
            $('.thumb', this.hover).remove();
            this.hover.hide();
        }
    });

    /**
     * inputFileThumb() transforms a file input into an input file thumb
     */
    $.fn.inputFileThumb = function(options) {
        return this.each(function() {
            new InputFileThumb(this, options);
        });
    };
})(jQuery);

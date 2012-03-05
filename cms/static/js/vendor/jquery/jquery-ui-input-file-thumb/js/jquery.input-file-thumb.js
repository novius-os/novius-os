/*
 * jQuery inputFileThumb Plugin
 *
 * Copyright (c) 2011 Novius Labs (Gilles FELIX & Antoine LEFEUVRE & Julian ESPERAT)
 *
 * version: 2.0.1 (26-JAN-2012)
 * @requires jQuery v1.7.1 or later
 * @requires jQuery UI v1.8.16 or later (UI Core, UI Widget, UI Button)
 * @requires A jQuery UI Theme
 * @optional bgiframe plugin to deal with IE z-index issues  (v2.1.3-pre or later)
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
	            'width'                 : 58, // Width in pixels of the thumbnail
	            'height'                : 58, // Height in pixels of the thumbnail
	            'file'                  : 'url', // URL of the existing file (the file which has been uploaded)
	            'title'                 : 'Title', // The title of the file (appears in the details layer, the layer displayed when the mouse is over the thumbnail)
	            'description'           : 'More info about the file (appears in the details layer)',
	            'extensions'            : [], // Array of allowed extensions for the file, in lowercased
	            'deleteInput'           : false, // Give the name of a hidden input if you need one to catch the delete action
	            'allowDelete'           : false, // If true, the delete action will appear and will empty the value of the input
	            'displayExtension'      : true, // Show the file extension (required or effective) in the thumbnail
	            'orientation'           : '', // Orientation of the details layer. Leave blank for automatic detection (layer right of the thumbnail when possible). Set to 'rtl' to force the layer on the left.
	            'classes'               : '', // You may create custom classes to change the thumbnail's default style. See CSS for further details.
	            'texts'                 : {
		            'add'            : 'Add', // Label of the add link
		            'edit'           : 'Edit', // Label of the edit link
		            'delete'         : 'Delete', // Label of the delete link
		            'wrongExtension' : 'This extension is not allowed', // Message returned when an unsupported file type is selected. Unused when cbCheckExt is a function.
		        },
		        'extensionsType'        : { // Predefined metatypes of files with associated extensions
		            'image'   : ['gif', 'jpg', 'jpeg', 'png', 'bmp']
		        },
	            'wrongExtension'        : null, // A callback function called when an unsupported file type is selected. This fonction must take two parameters: the event and a json (extension : the extension of the file, authorized : array of authorized extensions, message : the message for wrong extension defined in options)
	            'visualize'             : null, // A callback function called when the user clicks on the thumbnail. This fonction must take two parameters: the event and a json (file : the url file)
	            'delete'                : null, // A callback function called when the user clicks on the Delete file. This fonction must take one parameters: the event
	            'changed'                : null // A callback function called when the input[type=file] change. This fonction must take one parameters: the event
	            'choose'                : null // A callback function called when the not :file and add or edit button click. This fonction must take one parameters: the event
	        });
	    });
	*/
	$.widget( "ui.inputFileThumb", {
		// The default settings
		options: {
			width : 58,
			height : 58,
			file : '',
			title : '',
			description : '',
			extensions : [],
			deleteInput : false,
			allowDelete : false,
			displayExtension : true,
			orientation : '', //ltr or rtl
			classes : '',
			texts : {
				add : 'Add',
				edit : 'Edit',
				'delete' : 'Delete',
				wrongExtension : 'This extension is not allowed'
			},

			// Predefined metatypes of files with associated extensions
			extensionsType : {
			  'image'   : ['gif', 'jpg', 'jpeg', 'png', 'bmp']
			},

			wrongExtension : function(event, ui) {
				alert(ui.message);
			},
			visualize : function(event, ui) {
				if (ui.file) {
			        window.open(ui.file);
				}
			},
			'delete' : null,
			changed : null,
			choose : null
		},

		popupOpened : false,
		created : false,

		_create: function() {
			var self = this,
				o = self.options;

			if (self.element.is(':input')) {
				if (self.element.is(':file')) {
					self.uiInputFile = self.element;
				} else {
					self.element.hide();
				}
				self.uiWidget = $('<span></span>')
					.insertAfter(self.element);

				self.element.appendTo(self.uiWidget);
			} else {
				self.uiWidget = self.element;
			}

			self.uiWidget.addClass('ui-widget ui-inputfilethumb');

			self._eventsHandlers();

			self.uiThumb = $('<span><span></span></span>')
				.addClass('ui-widget-content ui-corner-all ui-inputfilethumb-thumb')
				.mouseenter(function() {
				   self.show();
				})
				.appendTo(self.uiWidget)
				.find('span')
				.addClass('ui-inputfilethumb-thumb-bg ui-corner-all')
				.end();

			self.uiHover = $('<div></div>')
				.addClass('ui-widget-content ui-corner-all ui-inputfilethumb-hover')
				.mouseleave(function() {
				   if (!$.browser.webkit || !self.popupOpened) {
				       self.hide();
				   }
				})
				.appendTo(self.uiWidget);

			self.uiFileThumb = $('<span></span>')
				.addClass('ui-inputfilethumb-filethumb')
				.appendTo(self.uiHover);

			self.uiFileMeta = $('<span></span>')
				.addClass('ui-inputfilethumb-filemeta')
				.appendTo(self.uiHover);

			self.uiFileTitle = $('<span></span>')
				.addClass('ui-inputfilethumb-filetitle ui-widget-header ui-corner-all ')
				.appendTo(self.uiFileMeta);

			self.uiFileDescription = $('<span></span>')
				.addClass('ui-inputfilethumb-filedescription')
				.appendTo(self.uiFileMeta);

			self.uiFileActions = $('<div></div>')
				.addClass('ui-inputfilethumb-fileactions')
				.appendTo(self.uiHover);

			self.uiAddFile = $('<span></span>')
				.addClass('ui-inputfilethumb-fileaction')
				.appendTo(self.uiFileActions)
				.button({
					icons: {
						primary: "ui-icon-plusthick"
					}
				})
				.click(function(e) {
			        self.choose(e);
			    });


			self.uiEditFile = $('<span></span>')
				.addClass('ui-inputfilethumb-fileaction')
				.appendTo(self.uiFileActions)
				.button({
					icons: {
						primary: "ui-icon-pencil"
					}
				})
				.click(function(e) {
			        self.choose(e);
			    });

			self.uiDeleteInput = $('<input type="hidden" value="" />')
				.appendTo(self.uiFileActions);

			self.uiDeleteFile = $('<button type="button"></button>')
				.addClass('ui-inputfilethumb-fileaction')
				.appendTo(self.uiFileActions)
				.button({
					icons: {
						primary: "ui-icon-closethick"
					}
				})
			    .click(function(e) {
			        self['delete'](e);
			    });

			if (self.element.is(':input')) {
				self.element.appendTo(self.uiHover)
					.click(function(e) {
						self.choose(e);
					});
			}
		},

		_init: function() {
			var self = this,
				o = self.options,
				file = o.file;

			if (self.uiInputFile) {
				self.uiInputFile.appendTo(self.uiWidget);
				file = file || self.uiInputFile.val();
			}

			o.title = o.title || self.element.attr('title') || self.element.attr('name');

			self.uiFileTitle.text(o.title);
			self.uiFileDescription.text(o.description);
			self.uiAddFile.button('option', 'label', o.texts.add);
			self.uiEditFile.button('option', 'label', o.texts.edit);
			self.uiDeleteInput.attr('name', o.deleteInput || '');
			self.uiDeleteFile.button('option', 'label', o.texts['delete'])
				[o.deleteInput || o.allowDelete ? 'show' : 'hide']();

			self.uiWidget[o.disabled ? 'addClass' : 'removeClass']('ui-state-disabled');

			self._change(file);
		},

		destroy: function() {
			var self = this;

			if (self.element !== self.uiWidget) {
				self.element.insertBefore(self.uiWidget);
			}
			self._eventsHandlers(true);
			self.uiWidget.remove();

			return self;
		},

		_setOptions : function( options ) {
			var self = this;


			$.each(options, function(key, value) {
				$.Widget.prototype._setOption.call(self, key, value);
			});

			self._init();
		},

		_setOption : function(key, value){
			var self = this;

			$.Widget.prototype._setOption.apply(self, arguments);

			self._init();
		},

		_change : function(file) {
			var self = this,
				o = self.options,
				ext = false;

			if (file) {
				ext = self._checkExtension(file);
				if (ext === false) {
					self['delete']();
					return;
				}
				if (self.uiInputFile) {
					self.uiInputFile.appendTo(self.uiEditFile);
				}
				self.uiAddFile.hide();
				self.uiEditFile.show();
				if (o.deleteInput) {
					self.uiDeleteInput.val('');
				}
				if (o.deleteInput || o.allowDelete) {
					self.uiDeleteFile.show();
				}

			} else {
				if (self.uiInputFile) {
					self.uiInputFile.appendTo(self.uiAddFile);
				}
			    self.uiAddFile.show();
			    self.uiEditFile.hide();
				if (o.allowDelete) {
					self.element.val('');
				}
			    if (o.deleteInput || o.allowDelete) {
			        self.uiDeleteFile.hide();
			    }
			}
			self.uiFileActions[o.disabled ? 'hide' : 'show']();

			self._icon(ext, file);

			return self;
		},

		_checkExtension : function(file) {
			var self = this,
				o = self.options,
				ext = '';

			var found = file.match(/.+\.(\w{2,4})(\?|$)/gi);
			if ($.isArray(found) && found.length) {
			    ext = RegExp.$1;
			}
			if (o.extensions.length && $.inArray(ext.toLowerCase(), o.extensions) === -1) {
				self._trigger('wrongExtension', null, {
					extension : ext,
					authorized : o.extensions,
					message : o.texts.wrongExtension
				});
				return false;
			}

			return ext;
		},

		_icon : function(ext, file) {
			var self = this,
				o = self.options;

			if (file && $.inArray(ext, o.extensionsType.image) !== -1) {
			    $('<img src="' + file + '" />')
				    .error(function() {
					    $(this).remove();
					    self._iconByExtension(ext);
				    })
				    .load(function() {
					    self.uiThumb.empty().css({
								'text-align' : 'center',
								'cursor' : 'pointer'
							})
							.width(o.width)
							.height(o.height);

						var thumbSize = {
								width  : self.uiThumb.innerWidth(),
								height : self.uiThumb.innerHeight()
							},
							img = $(this),
					        width  = img.width(),
					        height = img.height();

					    if (height > width) {
							if (width > thumbSize.width) {
							    img.width(thumbSize.width + 'px');
							}
					    } else {
					        if (height > thumbSize.height) {
					            img.height(thumbSize.height + 'px');
					        } else {
					            img.css('margin-top', (thumbSize.height - height) / 2 + 'px');
					        }
					    }
					    img.css('display', 'inline')
							.appendTo(self.uiThumb);
					})
				    .hide()
			        .appendTo('body');
			} else {
				self._iconByExtension(ext);
			}

			return self;
		},

		_iconByExtension : function(ext) {
			var self = this,
				o = self.options,
				classes = o.classes;

			self.uiThumb.empty()
				.append('<span class="ui-inputfilethumb-thumb-bg ui-corner-all"></span>')
				.css({
					'text-align' : 'right',
					'cursor' : 'default'
				});

			if (!classes) {
				$.each(o.extensionsType, function (type, extensionsType) {
					var ok = false;
					if (o.extensions.length > 0) {
						ok = true;
						$.each(o.extensions, function (i, val) {
							if ($.inArray(val, extensionsType) === -1) {
								ok = false;
								return false;
							}
							return true;
						});
					}
					if (ok) {
						classes = type;
						return false;
					} else {
						if ($.inArray(ext, extensionsType) !== -1) {
							classes = type;
							return false;
						}
					}
					return true;
				});
			}

			var div = self.uiThumb.find('.ui-inputfilethumb-thumb-bg')
				.addClass(classes + (ext ? ' ui-inputfilethumb-extension-on' : ''));

			if (o.displayExtension) {
				if (ext !== false) {
					div.append('<span class="ui-corner-all ' + ext + '">' + ext + '</span>');
				} else if (o.extensions.length == 1) {
					div.append('<span class="ui-corner-all ' + o.extensions[0] + '">' + o.extensions[0] + '</span>');
				}
			}

			div.css('line-height', o.height + 'px')
				.add(self.uiThumb)
				.width(o.width)
				.height(o.height);

			return self;
		},

		_empty : function () {
			var self = this,
				o = self.options;

			if (self.uiInputFile) {
				var temp = $('<span></span>').insertAfter(self.uiInputFile);
				var form = $('<form></form>').appendTo('body');
				self.uiInputFile.appendTo(form);
				form[0].reset();
				self.uiInputFile.insertAfter(temp);
				temp.remove();
			}

			return self._change('');
		},

		_eventsHandlers : function(unbind) {
			unbind = unbind || false;

			var self = this;

			if (self.uiInputFile) {
				var mouseenter = function() {
						self.uiInputFile.parent().addClass('ui-state-hover');
					},
					mouseleave = function() {
						self.uiInputFile.parent().removeClass('ui-state-hover');
					},
					change = function(e) {
						self._change(self.uiInputFile.val())
							.hide()
							._trigger('changed');
					},
					click = function(e) {
						if ($.browser.webkit && !self.popupOpened) {
							self.popupOpened = setTimeout(function() {
								$('body').one('mousemove', function() {
									self.popupOpened = false;
									self.hide();
								});
							}, 500);
						}
					};

				self.uiInputFile[unbind ? 'off' : 'on']({
					mouseenter : mouseenter,
					mouseleave : mouseleave,
					change : change,
					click : click
				});
			}

			return self;
		},

		choose : function (event) {
			var self = this,
				o = self.options;

			self._trigger('choose', event);

			return self;
		},

		'delete' : function (event) {
			var self = this,
				o = self.options;

			if (false === self._trigger('delete', event)) {
				event.stopImmediatePropagation();
				return false;
			}

			self.uiDeleteInput.val('1');
			o.file = '';

			return self._empty()
				.hide();
		},

		show : function () {
			var self = this,
				o = self.options;

			self.uiHover.css({
					top : 0,
					left : 0,
					opacity : 0
				})
				.show();
			self.uiFileThumb.css('float', 'left');
			self.uiFileMeta.css('margin-left', '10px');
			self.uiFileActions.css('margin-left', (o.width + 10) + 'px');

			var clone = self.uiThumb
				.clone(false)
				.addClass('ui-state-active')
				.appendTo(self.uiFileThumb)
				.bind('click', function(e) {
					self._trigger('visualize', e, {file : o.file});
				}),
				ofst = self.uiThumb.offset(),
				left = ofst.left,
				top = ofst.top,
				parent = self.uiThumb.offsetParent().not('body');

			if (parent.size()) {
				ofst = parent.offset();
				left -= ofst.left;
				top -= ofst.top;
			}

			var padding = {
					left : parseInt(self.uiHover.css('padding-left').replace('px', '')) + parseInt(self.uiHover.css('border-left-width').replace('px', '')),
					top : parseInt(self.uiHover.css('padding-top').replace('px', '')) + parseInt(self.uiHover.css('border-top-width').replace('px', '')),
					right : parseInt(self.uiHover.css('padding-right').replace('px', '')) + parseInt(self.uiHover.css('border-right-width').replace('px', ''))
				};
			if (o.orientation == 'rtl' || (left - padding.left + self.uiHover.outerWidth()) > $(window).width()) {
				self.uiFileThumb.css('float', 'right');
				self.uiFileMeta.css({
						marginRight : '10px',
						marginLeft : '0px'
					});
				self.uiFileActions.css({
						marginRight : (o.width + 10) + 'px',
						marginLeft : '0px'
					});
				self.uiHover.css({
						top : top - padding.top,
						left : left + padding.right + self.uiThumb.outerWidth() - self.uiHover.outerWidth()
					});
			} else {
				self.uiHover.css({'top':top - padding.top, 'left':left - padding.left});
			}
			if ($.fn.bgiframe) {
				self.uiHover.bgiframe();
			}
			self.uiHover.css('opacity', 1);

			return self;
		},

		hide : function () {
			var self = this,
				o = self.options;

			self.uiHover.find('.ui-inputfilethumb-thumb')
				.remove()
				.end()
				.hide();

			return self;
		}
	});
})(jQuery);

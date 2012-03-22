/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

define([
        'jquery',
		'static/cms/js/vendor/wijmo/js/jquery.wijmo-open.all.2.0.3.min',
		'static/cms/js/vendor/wijmo/js/jquery.wijmo-complete.all.2.0.3.min'
	], function($) {
        var undefined = void(0);

        $.nos = {
            mp3Add: function(id, config) {
                var self = this;
                var onCustom = false;
                var jsonFile = "";

                if (config.selectedView == 'custom') {
                    if (config.custom) {
                        jsonFile = config.views[config.custom.from].json;
                        onCustom = true;
                    } else {
                        config.selectedView = 'default';
                    }
                }

                if (config.selectedView != 'custom') {
                    jsonFile = config.views[config.selectedView].json;
                }


                require([
                    'static/cms/js/jquery/jquery-ui-noviusos/js/jquery.nos.mp3grid.js',
                    'static/cms/js/jquery/jquery-ui-noviusos/js/jquery.nos.thumbnails.js',
                    'order!static/cms/js/jquery/jquery-ui-noviusos/js/jquery.nos.nosgrid.js',
                    'order!static/cms/js/jquery/jquery-ui-noviusos/js/jquery.nos.nostreegrid.js',
                    'static/cms/js/jquery/jquery-ui-noviusos/js/jquery.nos.inspector-preview.js'
                ], function( $ ) {

                    require(jsonFile, function () {
                        var mp3Grid = $.nos.mp3GridSetup();
                        $.extend(true, mp3Grid.i18nMessages, config.i18n);

                        // Extending mp3Grid with each of the different json files
                        for (var i = 0; i < arguments.length; i++) {
                            $.extend(true, mp3Grid, arguments[i](mp3Grid));
                        }

                        $.extend(true, mp3Grid.mp3grid, {
                            locales : config.locales,
                            views : config.views,
                            name  : config.configuration_id,
                            selectedView : config.selectedView,
                            selectedLang : config.selectedLang
                        });
                        if (onCustom) {
                            $.extend(true, mp3Grid.mp3grid, {
                                fromView : config.custom.from
                            }, config.custom.mp3grid);
                        }

                        var timeout,
                            div = $('div#' + id),
                            connector = div.closest('.nos-connector'),
                            params = mp3Grid.build();

                        if ($.isPlainObject(params.tab) && !$.isEmptyObject(params.tab)) {
                            try {
                                $.nos.tabs.update(div, params.tab);
                            } catch (e) {
                                log('Could not update current tab. Maybe your config file should not try to update it.');
                            }
                        }

                        div.removeAttr('id')
                            .mp3grid(params.mp3grid);

                        connector.on({
                            resizePanel : function() {
                                if (timeout) {
                                    window.clearTimeout(timeout);
                                }
                                timeout = window.setTimeout(function() {
                                    div.mp3grid('resize');
                                }, 200);
                            },
                            showPanel :  function() {
                                div.mp3grid('resize');
                            }
                        });

                        if (params.reload) {
                            connector.on('reload.' + params.reload, function() {
                                div.mp3grid('gridReload');
                            });
                        }

                        div.bind('reloadView', function(e, newConfig) {
                            $.extend(config, newConfig);
                            var newDiv = $('<div id="' + id + '"></div>');
                            newDiv.insertAfter(div);
                            div.remove();
                            self.mp3Add(id, config);
                        });

                    });

                });
            },


            mp3GridSetup : function() {
                var self = {};

                var objectToArray = function(val, i) {
                        val['setupkey'] = i;
                        return val;
                    },

                    keyToOrderedArray = function(object, key) {
                        if (object[key + 'Order']) {
                            var keys = object[key + 'Order'].split(',');
                            var ordered = [];
                            for (var i = 0; i < keys.length; i++) {
                                // Remove null values
                                if (object[key][keys[i]] != null) {
                                    object[key][keys[i]]['setupkey'] = keys[i];
                                    ordered.push(object[key][keys[i]]);
                                }
                            }
                            return ordered;
                        } else {
                            return $.map(object[key], objectToArray);
                        }
                    },

                    recursive = function(object) {
                        $.each(object, function(key, val) {
                            if ($.isPlainObject(val)) {
                                if ($.isFunction(val._)) {
                                    // Translate value
                                    object[key] = val._();
                                } else {
                                    recursive(val);
                                }
                            } else if ($.isArray(val)) {
                                recursive(val);
                            }

                            // Build actions columns if any, and translate columns properties
                            if (key === 'columns') {
                                object[key] = keyToOrderedArray(object, key);
                                for (var i = 0; i < object[key].length; i++) {
                                    if (object[key][i].lang) {
                                        object[key][i] = {
                                            headerText : 'Languages',
                                            dataKey    : 'lang',
                                            setupkey   : 'lang',
                                            showFilter : false,
                                            cellFormatter : function(args) {
                                                if (args.row.type & $.wijmo.wijgrid.rowType.data) {
                                                    args.$container.css("text-align", "center").html(args.row.data.lang);
                                                    return true;
                                                }
                                            },
                                            width : 1
                                        };
                                    }
                                    if (object[key][i].actions) {
                                        var actions = object[key][i].actions;
										var width;
                                        var showOnlyArrow = object[key][i].showOnlyArrow ? true : false;

                                        if (showOnlyArrow) {
                                            width = 20;
                                        } else {
                                            width = $.nos.grid.getActionWidth(actions);

                                            if (actions.length > 1) {
                                                // Reserve space for the dropdown actions menu
                                                //width -= 20;
                                            }
                                            // At least 80px wide
                                            //width = Math.max(width, 80);
                                        }

                                        // Make the drop-down actions columns
                                        object[key][i] = {
                                            headerText : showOnlyArrow ? '' : '',
                                            cellFormatter : function(args) {
                                                if ($.isPlainObject(args.row.data)) {

                                                    var buttons = $.nos.mp3gridActions(actions, args.row.data, {
                                                        showOnlyArrow : showOnlyArrow
                                                    });

													buttons.appendTo(args.$container);
													args.$container.parent().addClass('buttontd').css({width: width + 1});

                                                    return true;
                                                }
                                            },
                                            allowSizing : false,
                                            allowSort : false,
                                            width : width,
                                            ensurePxWidth : true,
                                            showFilter : false,
                                            setupkey: 'actions'
                                        };
                                    }
                                }
                            }
                        });
                    },

                    self = {
                        tab : null,
                        mp3grid : {
                            adds : {},
                            grid : {
                                proxyUrl : '',
                                columns : {}
                            },
                            thumbnails : null,
                            defaultView : 'grid',
                            inspectors : {},
                            splittersVertical : null,
                            splittersHorizontal : null
                        },

                        i18nMessages : {},

                        i18n : function(label) {
                            var o = {};
                            var self = this;

                            $.extend(o, {
                                label : label,
                                _ : function() {
                                    return self.i18nMessages[o.label] || o.label;
                                }
                            });

                            return o;
                        },

                        build : function() {
                            // Clone object
                            var params = $.extend(true, {
                                mp3grid : {
                                    texts : this.i18nMessages,
                                    splitters : {},
                                    slidersChange : function(e, rapport) {
                                        //$nos.saveUserConfiguration("'.$config['configuration_id'].'.ui.splitters", rapport)
                                    }
                                }
                            }, this);

                            if (params.mp3grid.splittersVertical) {
                                params.mp3grid.splitters.vertical = {splitterDistance : params.mp3grid.splittersVertical};
                            }
                            if (params.mp3grid.splittersHorizontal) {
                                params.mp3grid.splitters.horizontal = {splitterDistance : params.mp3grid.splittersHorizontal};
                            }
                            params.mp3grid.adds = $.map(params.mp3grid.adds, objectToArray);


                            params.mp3grid.inspectors = keyToOrderedArray(params.mp3grid, 'inspectors');

                            // 'actions' is an object containing all the possible actions
                            // 'mp3grid.grid.columns.actions.actions' references the actions we actually use (and are copied from 'actions')
                            if (params.actions) {
                                var gridActions = params.actions;
                                if (params.mp3grid.grid.columns.actions && params.mp3grid.grid.columns.actions.actions) {
                                    $.each(params.mp3grid.grid.columns.actions.actions, function(i, val) {
                                        if ($.type(val) == 'string') {
                                            params.mp3grid.grid.columns.actions.actions[i] = gridActions[val];
                                        }
                                    });
                                }
                                if (params.mp3grid.thumbnails && params.mp3grid.thumbnails.actions) {
                                    $.each(params.mp3grid.thumbnails.actions, function(i, val) {
                                        if ($.type(val) == 'string') {
                                            params.mp3grid.thumbnails.actions[i] = gridActions[val];
                                        }
                                    });
                                }
                                $.each(params.mp3grid.inspectors, function(i, inspector) {
                                    if (inspector.preview && inspector.options.actions) {
                                        $.each(inspector.options.actions, function(i, val) {
                                            if ($.type(val) == 'string') {
                                                inspector.options.actions[i] = gridActions[val];
                                            }
                                        });
                                    }
                                });
                            }

                            // Translate clone object
                            recursive(params);

                            // Build properties for preview inspector
                            for (var i = 0; i < params.mp3grid.inspectors.length; i++) {
                                if (params.mp3grid.inspectors[i].preview) {
                                    params.mp3grid.inspectors[i].url = function($li) {
                                        var inspectorData = $li.data('inspector'),
                                            widget = $('<div></div>')
                                                .appendTo($li)
                                                .inspectorPreview(inspectorData.options)
                                                .parent()
                                                .on({
                                                    widgetResize: function() {
                                                        widget.inspectorPreview('resize');
                                                    }
                                                })
                                                .end();
                                    };
                                }
                            }

                            return params;
                        }
                    };
                return self;
            },

			// Keep track of all created menus so we can hide them when
			mp3GridActionsList : [],
			mp3gridActions : function(actions, noParseData, options) {
                options = options || {};
				var container = $('<table><tr></tr></table>').addClass('buttontd wijgridtd');

                var actionsPrimary = [];
                var actionsSecondary = [];

                // Possibility to always hide everyting
                if (!options.showOnlyArrow) {
                    $.each(actions, function() {
                        if (this.primary) {
                            actionsPrimary.push(this);
                        } else {
                            actionsSecondary.push(this);
                        }
                    });

                    // If there is only 1 secondary action and it has an icon, don't show the dropdow, but show the action as a button
                    if (actionsSecondary.length == 1 && (actionsSecondary[0].icon || actionsSecondary[0].iconClasses)) {
                        actionsPrimary.push(actionsSecondary[0]);
                    }

                    $.each(actionsPrimary, function(i, action) {
                        var iconClass = false;
                        if (action.iconClasses) {
                            iconClass = action.iconClasses;
                        } else if (action.icon) {
                            iconClass = 'ui-icon ui-icon-' + action.icon;
                        }
                        var uiAction = $('<th></th>')
                            .css('white-space', 'nowrap')
                            .addClass("ui-state-default")
                            .attr('title', action.label)
                            .html( (iconClass ? '<span class="' + iconClass +'"></span>' : '') + (action.text || !iconClass ? '&nbsp;' + action.label + '&nbsp;' : ''));

                        // Check whether action name is disabled
                        if (action.name && noParseData && noParseData.actions && noParseData.actions[action.name] == false) {
                            uiAction.addClass('ui-state-disabled')
                            .click(function(e) {
                                e.stopImmediatePropagation();
                                e.preventDefault();
                            });
                        } else {
                            uiAction.click(function(e) {
                                e.stopImmediatePropagation();
                                e.preventDefault();
                                action.action.apply(this, [noParseData, uiAction]);
                            })
                            .hover(
                                function() {
                                    $(this).addClass("ui-state-hover");
                                },
                                function() {
                                    $(this).removeClass("ui-state-hover");
                                }
                            );
                        }

                        if (iconClass && !action.text) {
                            uiAction.css({
                                width : 20,
                                textAlign : 'center'
                            }).children().css({
                                margin : 'auto'
                            });
                        } else if (iconClass && action.text) {
                            uiAction.find('span').css('float', 'left');
                        }

                        uiAction.appendTo(container.find('tr'));
                    });
                }

                // Create the dropdown
				if (options.showOnlyArrow || actionsSecondary.length >= 2 || (actionsSecondary.length == 1 && !(actionsSecondary[0].icon || actionsSecondary[0].iconClasses))) {

					var dropDown = $('<th></th>')
						.addClass("ui-state-default")
						.css({
							width: '20px'
						})
						.hover(
							function() {
								$(this).addClass("ui-state-hover");
							},
							function() {
								$(this).removeClass("ui-state-hover");
							}
						);

					$("<span></span>")
						.addClass("ui-icon ui-icon-triangle-1-s")
						.appendTo(dropDown);

                    // Don't select the line when clicking the "more actions" arrow dropdown
					dropDown.appendTo(container.find('tr')).click(function(e) {

						$.each($.nos.mp3GridActionsList, function() {
							$(this).wijmenu('hideAllMenus');
						});

						if (!this.created) {
							var ul = $('<ul></ul>');
							$.each(actions, function(key, action) {
                                var iconClass;
                                if (action.iconClasses) {
                                    iconClass = action.iconClasses;
                                } else if (action.icon) {
                                    iconClass = 'ui-icon ui-icon-' + action.icon;
                                }
                                var text = '<span class="' + (iconClass ? iconClass : 'nos-icon16 nos-icon16-empty') + ' wijmo-wijmenu-icon-left"></span><span class="wijmo-wijmenu-text">'+action.label+'</span>';
								var li = $('<li><a href="#"></a></li>')
									.appendTo(ul)
									.find('a')
									.html(text);

                                // Check whether action name is disabled
                                if (action.name && noParseData.actions && noParseData.actions[action.name] == false) {
                                    li.addClass('ui-state-disabled')
                                    .click(function(e) {
                                        e.stopImmediatePropagation();
                                        e.preventDefault();
                                    });
                                } else {
									li.click(function(e) {
                                        e.stopImmediatePropagation();
                                        e.preventDefault();
                                        // Hide me
                                        ul.wijmenu('hideAllMenus');
                                        action.action.apply(this, [noParseData, li]);
                                    });
                                }
							});

							// Search the higher ancestor possible
							// @todo Review this, because when it's called from inspectors, the result is a <table>
							//       which is not convenient to add <ul>s or <div>s
							var containerActions = $.nos.$noviusos.ostabs
								? $.nos.$noviusos.ostabs('current').panel
								: dropDown.parentsUntil('.ui-widget, body').last();

							ul.appendTo(containerActions);

							ul.wijmenu({
								trigger : dropDown,
								triggerEvent : 'click',
								orientation : 'vertical',
								showAnimation : {Animated:"slide", duration: 50, easing: null},
								hideAnimation : {Animated:"hide", duration: 0, easing: null},
								position : {
									my        : 'right top',
									at        : 'right bottom',
									collision : 'flip',
									offset    : '0 0'
								}
							});

							$.nos.mp3GridActionsList.push(ul);

							this.created = true;

							// Now the menu is created, trigger the event to show it
							dropDown.triggerHandler('click');
						}

					});
					dropDown.click(false);
				}
				return container;
			},

            fireEvent : function(event) {
                if (window.parent != window && window.parent.$nos) {
                    return window.parent.$nos.nos.fireEvent(event);
                }
                if ($.nos.$noviusos) {
                    $.nos.$noviusos.ostabs('triggerPanels', event);
                }
            },

            dataStore : {},
            data : function (id, json) {
                if (window.parent != window && window.parent.$nos) {
                    return window.parent.$nos.nos.data(id, json);
                }

                if (id) {
                    if (json) {
                        this.dataStore[id] = json;
                    }
                    return this.dataStore[id];
                }
            },

            dialog : function(options) {

                if (options.destroyOnClose) {
                    var oldClose = options.close;
                    options.close = function() {
                        if ($.isFunction(oldClose)) {
                            oldClose.apply(this, arguments);
                        }
                        $(this).wijdialog('destroy')
                            .remove();
                    };
                }

                // Default options
                options = $.extend(true, {}, {
                    width: window.innerWidth - 200,
                    height: window.innerHeight - 100,
                    modal: true,
                    captionButtons: {
                        pin: {visible: false},
                        refresh: {visible: options.contentUrl != null && !options.ajax},
                        toggle: {visible: false},
                        minimize: {visible: false},
                        maximize: {visible: false}
                    }
                }, options);

				var where   = $.nos.$noviusos.ostabs ? $.nos.$noviusos.ostabs('current').panel : $('body');
				var $dialog = $(document.createElement('div')).appendTo(where);

				$.nos.data('dialog', $dialog);

                if (typeof options['content'] != 'undefined') {
                    $dialog.append(options.content);
                }

                var proceed = true;
                if (options.ajax) {
                    var contentUrl = options.contentUrl;
                    delete options.contentUrl;
                    options.autoOpen = false;
                    $dialog.wijdialog(options);

                    // Request the remote document
                    $.ajax({
                        url: contentUrl,
                        type: 'GET',
                        dataType: "html",
                        // Complete callback (responseText is used internally)
                        complete: function( jqXHR, status, responseText ) {
                            // Store the response as specified by the jqXHR object
                            responseText = jqXHR.responseText;
                            // If successful, inject the HTML into all the matched elements
                            if ( jqXHR.isResolved() ) {
                                // #4825: Get the actual response in case
                                // a dataFilter is present in ajaxSettings
                                jqXHR.done(function( r ) {
                                    responseText = r;
                                });



                                try {
                                    var json = $.parseJSON(responseText);
                                    // If the dialog ajax URL returns a valid JSON string, don't show the dialog
                                    proceed = false;
                                } catch (e) {}

                                if (proceed) {
                                    $dialog.wijdialog('open');
                                } else {
                                    $dialog.empty();
                                    $dialog.wijdialog('destroy');
                                    $dialog.remove();
                                    $.nos.ajax.success(json);
                                }

                                // inject the full result
                                $dialog.html( responseText );
                            }
                        }
                    });
                } else {
                    $dialog.wijdialog(options);
                }
                if (proceed) {
                    if ($.isFunction(options['onLoad'])) {
                        options['onLoad']();
                    }
                    $dialog.bind('wijdialogclose', function(event, ui) {
                        $dialog.closest('.ui-dialog').hide().appendTo(where);
                    });
                }

                return $dialog;
            },

            notify : function( options, type ) {

                if (window.parent != window && window.parent.$nos) {
                    return window.parent.$nos.nos.notify( options, type );
                }
                if ( !$.isPlainObject( options ) ) {
                    options = {title : options};
                }
                if ( type !== undefined ) {
                    $.extend(options, $.isPlainObject( type ) ? type : {type : type} );
                }
                if ( $.isPlainObject( options ) ) {
                    require([
                        'link!static/cms/js/vendor/jquery/pnotify/jquery.pnotify.default.css',
                        'static/cms/js/vendor/jquery/pnotify/jquery.pnotify.min'
                    ], function() {
                        var o = {
                            pnotify_history : false,
                            pnotify_addclass : 'nos-notification'
                        };
                        $.each( options, function(key, val) {
                            if ( key.substr( 0, 8 ) !== 'pnotify_' ) {
                                key = 'pnotify_' + key;
                            }
                            o[key] = val;
                        } );
                        return $.pnotify( o );
                    });
                }
                return false;
            },

            /** Execute an ajax request
             *
             * @param url
             * @param data
             */
            ajax : {
                request : function(options) {
                    options = $.extend({
                        dataType : 'json',
                        type     : 'POST',
                        data     : {}
                    }, options);

                    // Internal callbacks for JSON dataType
                    if (options.dataType == 'json') {
                        if ($.isFunction(options.success)) {
                            var old_success = options.success;
                            options.success = function(json) {
                                json.user_success = old_success;
                                $.nos.ajax.success(json);
                            }
                        } else {
                            options.success = $.nos.ajax.success;
                        }

                        if ($.isFunction(options.error)) {
                            var old_error = options.error;
                            options.error = function(json) {
                                $.nos.ajax.error(json);
                                old_error.apply(this, arguments);
                            }
                        } else {
                            options.error = $.nos.ajax.error;
                        }
                    }

                    $.ajax(options);
                },
                success : function(json) {
                    if (json.error) {
                        $.nos.notify(json.error, 'error');
                    }
                    if (json.notify) {
                        $.nos.notify(json.notify);
                    }
                    if (json.fireEvent) {
                        if ($.isArray(json.fireEvent)) {
                            $.each(json.fireEvent, function(i, event) {
                                $.nos.fireEvent(event);
                            });
                        } else {
                            $.nos.fireEvent(json.fireEvent);
                        }
                    }
                    // Call user callback
                    if ($.isFunction(json.user_success)) {
                        json.user_success.apply(this, arguments);
                    }

                    // Close at the end!
                    if (json.redirect) {
                        document.location = json.redirect;
                    }
                    if (json.closeTab) {
                        $.nos.tabs.close();
                    }
                    if (json.replaceTab) {
                        $.nos.tabs.replace(json.replaceTab);
                    }
                },
                error: function(x, e) {
					// http://www.maheshchari.com/jquery-ajax-error-handling/
                    if (x.status != 0) {
                        $.nos.notify('Connection error!', 'error');
                    } else if (e == 'parsererror') {
						$.nos.notify('Request seemed a success, but we could not read the answer.');
					} else if (e == 'timeout') {
						$.nos.notify('Time out (server is busy?). Please try again.');
					}
                }
            },

            grid : {
                getHeights : function() {
                    if (this.heights === undefined) {
                        var $div = $('<div></div>')
                            .appendTo('body');

                        var table = $('<table></table>')
							.addClass('nos-mp3grid')
                            .appendTo($div)
                            .nosgrid({
                                scrollMode : 'auto',
                                showFilter: true,
                                allowPaging : true,
                                data: [ ['test'] ]
                            });
                        this.heights = {
                            row : table.height(),
                            footer : $div.find('.wijmo-wijgrid-footer').outerHeight(),
                            header : $div.find('.wijmo-wijgrid-headerrow').outerHeight(),
                            filter : $div.find('.wijmo-wijgrid-filterrow').outerHeight()
                        };
                        table.nosgrid('destroy');
                        $div.remove();
                    }
                    return this.heights;
                },
				getActionWidth : function(actions) {

/*
					this.cache = {};
					if (null != this.cache[text]) {
						return this.cache[text];
					}*/

					var $div = $('<div></div>')
						.appendTo('body');

					var table = $('<table></table>')
						.addClass('nos-mp3grid')
						.appendTo($div)
						.nosgrid({
							scrollMode : 'none',
							showFilter: true,
							allowPaging : true,
							columns : [
								{
									headerText : 'Actions',
									cellFormatter : function(args) {
										if ($.isPlainObject(args.row.data)) {

											var buttons = $.nos.mp3gridActions(actions, []);

											buttons.appendTo(args.$container);
											args.$container.parent().addClass('buttontd');

											return true;
										}
									},
									allowSizing : true,
									showFilter : false,
									ensurePxWidth : true
								}
							],
							data: [
								{
									'key' : 'value'
								}
							]
						});
					$div.find('table.buttontd.wijgridtd').css({
						'font-size' : '1.05em',
						'width' : 'auto'
					});
					//this.cache[text] = $div.find('.buttontd .buttontd:first').outerWidth();
                    var width = $div.find('.buttontd .buttontd:first').outerWidth();
					table.nosgrid('destroy');
					$div.remove();
                    return width;
					//return this.cache[text];
				}
            },

            media : function(input, data) {

                var contentUrls = {
                    'all'   : '/admin/cms/media/list',
                    'image' : '/admin/cms/media/list?view=image_pick'
                };

				var dialog = null;

                var options = $.extend({
                    title: input.attr('title') || 'File',
					allowDelete : true,
                    choose: function(e) {
                        // Open the dialog to choose the file
						if (dialog == null) {
							dialog = $.nos.dialog({
								contentUrl: contentUrls[data.mode],
								ajax: true,
								title: 'Choose a media file'
							});
                            dialog.bind('select.media', function(e, item) {
                                input.inputFileThumb({
                                    file: item.thumbnail
                                });
                                input.val(item.id);
                                dialog.wijdialog('close');
                            });
						} else {
							dialog.wijdialog('open');
						}
                    }
                }, data.inputFileThumb);

                require([
                    'static/cms/js/vendor/jquery/jquery-ui-input-file-thumb/js/jquery.input-file-thumb',
                    'link!static/cms/js/vendor/jquery/jquery-ui-input-file-thumb/css/jquery.input-file-thumb.css'
                ], function() {
                    $(function() {
                        input.inputFileThumb(options);
                    });
                });
            },
			ui : {
				form : function(context) {
                    context = context || 'body';
					$(function() {
						var $container = $(context);
						$container.find(":input[type='text'],:input[type='password'],:input[type='email'],textarea").wijtextbox();
						$container.find(":input[type='submit'],button").each(function() {
							var options = {};
							var icon = $(this).data('icon');
							if (icon) {
								 options.icons = {
									 primary: 'ui-icon-' + icon
								 }
							}
							$(this).button(options);
						});
						$container.find("select").wijdropdown();
						$container.find(":input[type=checkbox]").wijcheckbox();
						$container.find('.expander').each(function() {
                            var $this = $(this);
                            $this.wijexpander($.extend({expanded: true}, $this.data('wijexpander-options')));
                        });
						$container.find('.accordion').wijaccordion({
							header: "h3"
						});
					});
				}
			}
        };
        window.$nos = $;

        $(function() {
            var noviusos = $('#noviusos');

            $.extend($.nos, {
                $noviusos : noviusos,

                saveUserConfiguration: function(key, configuration) {
                    this.ajax.request({
                        url: '/admin/cms/noviusos/save_user_configuration',
                        data: {
                            key: key,
                            configuration: configuration
                        }
                    });
                },

                initialize: function(configuration) {
                    var nosObject = this;
                    var fct = function(e) {
                        nosObject.tabs.save();
                    };
                    $.extend(configuration, {
                        add: fct,
                        pin: fct,
                        unpin: fct,
                        remove: fct,
                        select: fct,
                        show: fct,
                        drag: fct
                    });

                    if (configuration['user_configuration']['tabs']) {
                        if (!configuration['options']) {
                            configuration['options'] = {};
                        }
                        configuration['initTabs'] = configuration['user_configuration']['tabs']['tabs'];
                        configuration['selected'] = configuration['user_configuration']['tabs']['selected'];
                    }
                    noviusos.ostabs(configuration);
                    /*
                     if (configuration['user_configuration']['tabs']) {
                     noviusos.ostabs('setConfiguration', configuration['user_configuration']['tabs']);
                     }
                     */
                },

                tabs : {
                    current : function(index) {
                        if (window.parent != window && window.parent.$nos) {
                            return window.parent.$nos(window.frameElement).data('nos-ostabs-index');
                        }
                        if ($.isNumeric(index)) {
                            return index;
                        }
                        if ($.type(index) === 'object' && $.isFunction(index.parents)) {
                            return index.parents('.nos-ostabs-panel-content').data('nos-ostabs-index');
                        }
                        if (noviusos.length) {
                            return noviusos.ostabs('current').index;
                        }
                        return false;
                    },
                    add : function(tab, end) {
                        if (window.parent != window && window.parent.$nos) {
                            return window.parent.$nos.nos.tabs.add(tab, end);
                        }
                        var index;
                        if (end !== undefined && end !== true) {
                            index = this.current(end) + 1;
                        }
                        if (noviusos.length) {
                            index = noviusos.ostabs('add', tab, index);
                            return noviusos.ostabs('select', index);
                        } else if (tab.url) {
                            window.open(tab.url);
                        }
                        return false;
                    },
                    update : function(index, tab) {
                        if (window.parent != window && window.parent.$nos) {
                            return window.parent.$nos.nos.tabs.update(this.current(), index, tab);
                        }
						if (tab == null) {
							tab = index;
						}
                        index = this.current(index);
                        if (noviusos.length) {
                            noviusos.ostabs('update', index, tab);
                        }
                        return true;
                    },
                    close : function(index) {
                        if (window.parent != window && window.parent.$nos) {
                            return window.parent.$nos.nos.tabs.close(this.current());
                        }
                        index = this.current(index);
                        if (noviusos.length) {
                            noviusos.ostabs('remove', index);
                        }
                        return true;
                    },
                    replace : function(url) {
                        if (window.parent != window && window.parent.$nos) {
                            return window.parent.$nos.nos.tabs.replace(url);
                        }
                        var index = this.current();
                        this.add({url : url}, false);
                        this.close(index);
                        return true;
                    },
                    /** Save tabs in user configuration file
                     */
                    save: function() {
                        if (window.parent != window && window.parent.$nos) {
                            return window.parent.$nos.nos.tabs.save();
                        }
                        if (noviusos.length) {
                            $nos.nos.saveUserConfiguration('tabs', {selected: noviusos.ostabs('option', 'selected'), tabs: noviusos.ostabs('tabs')});
                        }
                    }
                }
            });
        });

        return $;
	});

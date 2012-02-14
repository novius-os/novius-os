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
		'static/cms/js/vendor/wijmo/js/jquery.wijmo-open.all.2.0.0b2.min',
		'static/cms/js/vendor/wijmo/js/jquery.wijmo-complete.all.2.0.0b2.min'
	], function($) {
        var undefined = void(0);

        $.nos = {
            mp3Add: function(id, config) {
                var self = this;
                var onCustom = false;
                var jsonFile = "";

                if (config['selectedView'] == 'custom') {
                    if (config['custom']) {
                        jsonFile = config['views'][config['custom']['from']].json;
                        onCustom = true;
                    } else {
                        config['selectedView'] = 'default';
                    }
                }

                if (config['selectedView'] != 'custom') {
                    jsonFile = config['views'][config['selectedView']].json;
                }


                require([
                    'static/cms/js/jquery/jquery-ui-noviusos/js/jquery.nos.mp3grid.js',
                    'static/cms/js/jquery/jquery-ui-noviusos/js/jquery.nos.thumbnails.js',
                    'static/cms/js/jquery/jquery-ui-noviusos/js/jquery.nos.nosgrid.js',
                    'static/cms/js/jquery/jquery-ui-noviusos/js/jquery.nos.inspector-preview.js'
                ], function( $ ) {

                    require(jsonFile, function () {
                        var mp3Grid = {};

                        // Extending mp3Grid with each of the different json files
                        for (var i = 0; i < arguments.length; i++) {
                            mp3Grid = $.extend(true, mp3Grid, arguments[i]);
                        }

                        $.extend(mp3Grid.i18nMessages, config['i18n']);
                        mp3Grid.mp3grid.views = config['views'];
                        mp3Grid.mp3grid.name = config['configuration_id'];
                        mp3Grid.mp3grid.selectedView = config['selectedView'];
                        if (onCustom) {
                            mp3Grid.mp3grid.fromView = config['custom']['from'];
                        }

                        if (onCustom) {
                            mp3Grid.mp3grid = $.extend(true, mp3Grid.mp3grid, config['custom'].mp3grid);
                        }

                        var timeout,
                            div = $('div#' + id),
                            container = div.parents('.nos-ostabs-panel, .ui-dialog'),
                            params = mp3Grid.build();

                        if ($.isPlainObject(params.tab)) {
                            try {
                                $.nos.tabs.update(div, params.tab);
                            } catch (e) {
                                log('Could not update current tab. Maybe your config file should not try to update it.');
                            }
                        }

                        $.nos.listener.add('mp3grid.' + id, true, function() {
                            div.removeAttr('id')
                            .mp3grid(params.mp3grid);
                            container
                            .bind({
                                'panelResize.ostabs' : function() {
                                    if (timeout) {
                                        window.clearTimeout(timeout);
                                    }
                                    timeout = window.setTimeout(function() {
                                        div.mp3grid('refresh');
                                    }, 200);
                                },
                                'showPanel.ostabs' :  function() {
                                    div.mp3grid('refresh');
                                }
                            });
                            $.nos.listener.remove('mp3grid.' + id, true, arguments.callee);
                        })

                        if (null == params.delayed || !params.delayed) {
                            $.nos.listener.fire('mp3grid.' + id, true, []);
                        }

                        if (params.refresh) {
                            container.bind('refresh.' + params.refresh, function() {
                                div.mp3grid('gridRefresh');
                            });
                        }

                        div.bind('reload', function(e, newConfig) {
                            config = $.extend(config, newConfig);
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
                                object[key][keys[i]]['setupkey'] = keys[i];
                                ordered.push(object[key][keys[i]]);
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
										var width = $.nos.grid.getActionWidth(actions[0].label);

										// At least 80px wide
										if (actions.length > 1) {
											// Reserve space for the dropdown actions menu
											width -= 20;
										}
										width = Math.max(width, 80);

                                        // Make the drop-down actions columns
                                        object[key][i] = {
                                            headerText : 'Actions',
                                            cellFormatter : function(args) {
                                                if ($.isPlainObject(args.row.data)) {

                                                    var buttons = $.nos.mp3gridActions(actions, args.row.data);

													buttons.appendTo(args.$container);
													args.$container.parent().addClass('buttontd').css({width: width + 1});

                                                    return true;
                                                };
                                            },
                                            allowSizing : false,
                                            allowSort : false,
                                            width : width,
                                            ensurePxWidth : true,
                                            showFilter : false,
                                            setupkey: 'actions'
                                        };
                                    }
                                };
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
                                                    inspectorResize: function() {
                                                        widget.inspectorPreview('refresh');
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
			mp3gridActions : function(actions, noParseData) {

				var container = $('<table><tr></tr></table>').addClass('buttontd wijgridtd');

				$.each(actions, function() {
					var action = this;
					action._action = function(e) {
						action.action(noParseData);
						e.stopImmediatePropagation();
						e.preventDefault();
					}
				});

				var action = $('<th></th>')
					.addClass("ui-state-default")
					.html(actions[0].label)
					.click(actions[0]._action)
					.hover(
						function() {
							$(this).addClass("ui-state-hover");
						},
						function() {
							$(this).removeClass("ui-state-hover");
						}
					);

				action.appendTo(container.find('tr'));

				if (actions.length > 1) {

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
							$.each(actions, function() {
								var action = this;
								$('<li><a href="#"></a></li>')
									.appendTo(ul)
									.find('a')
									.text(action.label)
									.click(action._action)
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

            listener : (function() {
                var _list = {};
                function _get(id) {
                    var listener = id && _list[id];

                    if ( !listener ) {
                        listener = $.Callbacks();
                        if ( id ) {
                            _list[id] = listener;
                        }
                    }
                    return listener;
                }

                return {
                    add: function(id, alltabs, fn) {
                        if (fn === undefined) {
                            fn = alltabs;
                            alltabs = true;
                        }
                        if (alltabs && window.parent != window && window.parent.$nos) {
                            $(window).unload(function() {
                                window.parent.$nos.nos.listener.remove(id, fn);
                            });
                            return window.parent.$nos.nos.listener.add(id, true, fn);
                        }
                        return _get(id).add(fn);
                    },
                    remove: function(id, alltabs, fn) {
                        if (fn === undefined) {
                            fn = alltabs
                            alltabs = true;
                        }
                        if (alltabs && window.parent != window && window.parent.$nos) {
                            return window.parent.$nos.nos.listener.remove(id, true, fn);
                        }
                        return _get(id).remove(fn);
                    },
                    fire: function(id, alltabs, args) {
                        if (args === undefined) {
                            args = alltabs
                            alltabs = true;
                        }
                        if (!$.isArray(args)) {
                            args = [args];
                        }
                        if (alltabs && window.parent != window && window.parent.$nos) {
                            return window.parent.$nos.nos.listener.fire(id, true, args);
                        }
                        if ($.nos.$noviusos) {
                            $.nos.$noviusos.ostabs('triggerPanels', id, args);
                            $('.ui-dialog').trigger(id, args);
                        }
                        //log('listener.fire.args', args);
                        if (id.substring(id.length - 1) == '!') {
                            triggerName = id.substring(0, id.length - 1);
                            //log('listener.fire', triggerName + '!', args, window);
                            _get(triggerName).fire.apply(null, args);
                            return;
                        }
                        var queue = id.split( "." );
                        var triggerName = "";
                        for (var i=0; i<queue.length ; i++) {
                            if (i > 0) {
                                triggerName += ".";
                            }
                            triggerName += queue[i];
                            //log('listener.fire', triggerName, args, window);
                            _get(triggerName).fire.apply(null, args);
                        }
                    }
                };
            })(),

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

            dialog : function(options, wijdialog_options) {

                // If only one argument is passed, then it's the wijdialog_options
                if (wijdialog_options == null) {
                    wijdialog_options = options;
                    options = {};
                }

                // Default options
                wijdialog_options = $.extend(true, {}, {
                    width: window.innerWidth - 200,
                    height: window.innerHeight - 100,
                    modal: true,
                    captionButtons: {
                        pin: {visible: false},
                        refresh: {visible: wijdialog_options.contentUrl != null && wijdialog_options.ajax != true},
                        toggle: {visible: false},
                        minimize: {visible: false},
                        maximize: {visible: false}
                    }
                }, wijdialog_options);


				var where   = $.nos.$noviusos.ostabs ? $.nos.$noviusos.ostabs('current').panel : $('body');
				var $dialog = $(document.createElement('div')).appendTo(where);

				$.nos.data('dialog_media', $dialog);

                if (typeof wijdialog_options['content'] != 'undefined') {
                    $dialog.append(wijdialog_options.content);
                }

                require([
                    //'link!static/cms/js/vendor/wijmo/css/jquery.wijmo-open.2.0.0b2.css',
                    //'static/cms/js/wijmo/wijmo/js/jquery.wijmo.wijutil',
                    'static/cms/js/vendor/wijmo/js/jquery.wijmo.wijdialog'
                ], function() {
					if (wijdialog_options.ajax) {
						$dialog.load(wijdialog_options.contentUrl, {}, function(responseText, textStatus, XMLHttpRequest){
							delete wijdialog_options.contentUrl;
							$dialog.wijdialog(wijdialog_options);
						});
					} else {
						$dialog.wijdialog(wijdialog_options);
					}
                    if ($.isFunction(wijdialog_options['onLoad'])) {
                        wijdialog_options['onLoad']();
                    }
					$dialog.bind('wijdialogclose', function(event, ui) {
						//log('Fermeture et destroyage');
						$dialog.closest('.ui-dialog').hide().appendTo(where);
					});
                });

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
                        var o = {};
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
                        }

                        if ($.isFunction(options.error)) {
                            var old_error = options.error;
                            options.error = function(json) {
                                $.nos.ajax.error(json);
                                old_error.apply(this, arguments);
                            }
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
                    if (json.listener_fire) {
                        if ($.isPlainObject(json.listener_fire)) {
                            $.each(json.listener_fire, function(listener_name, bubble) {
                                $.nos.listener.fire(listener_name, bubble);
                            });
                        } else {
                            $.nos.listener.fire(json.listener_fire);
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
				getActionWidth : function(text) {

					this.cache = {};
					if (null != this.cache[text]) {
						return this.cache[text];
					}

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

											var buttons = $.nos.mp3gridActions([{
												label : '&nbsp;' + text + '&nbsp;',
												action: function() {}
											}], []);

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
					this.cache[text] = $div.find('.buttontd .buttontd:first').outerWidth();
					table.nosgrid('destroy');
					$div.remove();
					return this.cache[text];
				}
            },

            media : function(input, data) {

                var contentUrls = {
                    'all'   : '/admin/media/list',
                    'image' : '/admin/media/list?view=image'
                };

				var dialog = null;

				var pick_media = function(item) {
					// Close the popup (if we have one)
					dialog && dialog.wijdialog('close');

					input.inputFileThumb({
						file: item.thumbnail
					});
					input.val(item.id);
				};

                var options = $.extend({
                    title: input.attr('title') || 'File',
					allowDelete : true,
                    choose: function(e) {
						// The popup will trigger this event when done
						$.nos.listener.remove('media.pick', true, pick_media);
						$.nos.listener.add('media.pick', true, pick_media);

                        // Open the dialog to choose the file
						if (dialog == null) {
							dialog = $.nos.dialog({
								contentUrl: contentUrls[data.mode],
								ajax: true,
								title: 'Choose a media file',
								close: function() {
									$.nos.listener.remove('media.pick', true, pick_media);
								}
							});
						} else {
							dialog.wijdialog('open');
						}
                    }
                }, data.inputFileThumb);

                require([
                    'static/cms/js/jquery/jquery-ui-input-file-thumb/js/jquery.input-file-thumb',
                    'link!static/cms/js/jquery/jquery-ui-input-file-thumb/css/jquery.input-file-thumb.css'
                ], function() {
                    $(function() {
                        input.inputFileThumb(options);
                    });
                });
            },
			ui : {
				form : function(context) {
					$(function() {
						context = $(context) || 'body';
						$(":input[type='text'],:input[type='password'],:input[type='email'],textarea", context).wijtextbox();
						$(":input[type='submit'],button", context).each(function() {
							var options = {};
							var icon = $(this).data('icon');
							if (icon) {
								 options.icons = {
									 primary: 'ui-icon-' + icon
								 }
							}
							$(this).button(options);
						});
						$("select", context).wijdropdown();
						$(":input[type=checkbox]", context).wijcheckbox();
						$('.expander', context).wijexpander({expanded: true});
						$('.accordion', context).wijaccordion({
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
                        url: '/admin/noviusos/noviusos/save_user_configuration',
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

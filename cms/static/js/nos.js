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
		'static/cms/js/jquery/globalize/globalize.min',
		'static/cms/js/jquery/mousewheel/jquery.mousewheel.min',
		'static/cms/js/jquery/wijmo/js/jquery.wijmo-open.1.5.0.min',
		'static/cms/js/jquery/wijmo/js/jquery.wijmo-complete.1.5.0.min'
	], function($) {
        var undefined = arguments[100];

        $.nos = {
            mp3GridSetup : function(params) {
                var messages = {},
                    self = {};

                objectToArray = function(val, i) {
                    return val;
                };

                $.extend(self, {
                    proxyUrl : '',
                    tab : null,
                    adds : {},
                    columns : {},
                    inspectors : {},
                    actions : {},
                    thumbnails : null,
                    defaultView : 'grid',
                    preview : null,
                    splittersVertical : null,
                    splittersHorizontal : null,

                    i18n : (function() {
                        return {
                            load : function(m) {
                                $.extend(messages, m);
                            },
                            _ : function(msgId) {
                                return messages[msgId] || msgId;
                            }
                        }
                    })(),
                    build : function() {
                        // Clone columns
                        var columns = $.extend({}, self.columns);

                        // Translate actions
                        $.each(self.actions, function() {
                            this.label = self.i18n._(this.label);
                        });
                        // Recover values in actions properties for actions columns / thumbnails / preview, if defined
                        $.each([columns, self.thumbnails ? self.thumbnails : {}, self.preview ? self.preview.options : {}], function(o, container) {
                            if (container.lang) {
                                container.lang = {
                                        headerText : 'Languages',
                                        dataKey   : 'lang',
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
                            if (container.actions && $.isPlainObject(self.actions)) {
                                var actions = [];
                                if ($.isArray(container.actions)) {
                                    $.each(container.actions, function(i, val) {
                                        if ($.type(val) === 'string' && self.actions[val]) {
                                            actions.push(self.actions[val]);
                                        }
                                    });
                                } else if ($.isPlainObject(container.actions)) {
                                    $.each(container.actions, function(key, param) {
                                        if ($.isPlainObject(param) && self.actions[key]) {
                                            actions.push($.extend({}, self.actions[key], param));
                                        } else {
                                            actions.push(self.actions[key]);
                                        }
                                    });
                                } else {
                                    actions = $.map(self.actions, objectToArray);
                                }
                                if (container === columns) {
                                    container.actions = {actions : actions};
                                } else {
                                    container.actions = actions;
                                }
                            }
                        });

                        var splitters = {};
                        if (self.splittersVertical) {
                            splitters.vertical = {splitterDistance : self.splittersVertical};
                        }
                        if (self.splittersHorizontal) {
                            splitters.horizontal = {splitterDistance : self.splittersHorizontal};
                        }

                        // Build the Json for return
                        var params = {
                            tab : self.tab,
                            mp3grid : {
                                texts : messages,
                                adds : $.map(self.adds, objectToArray),
                                defaultView : self.defaultView,
                                grid : {
                                    proxyurl : self.proxyUrl,
                                    columns : $.map(columns, objectToArray)
                                },
                                thumbnails : self.thumbnails,
                                preview : self.preview,
                                inspectors : $.map(self.inspectors, objectToArray),
                                slidersChange : function(e, rapport) {
                                    //$nos.saveUserConfiguration("'.$config['configuration_id'].'.ui.splitters", rapport)
                                },
                                splitters : splitters
                            }
                        };

                        // Translate tab property
                        if ($.isPlainObject(params.tab)) {
                            params.tab.label = self.i18n._(params.tab.label);
                        }
                        // Translate adds properties
                        $.each(params.mp3grid.adds, function() {
                            this.label = self.i18n._(this.label);
                        });
                        // Build actions columns if any, and translate columns properties
                        for (var i = 0; i < params.mp3grid.grid.columns.length; i++) {
                            if (params.mp3grid.grid.columns[i].actions) {
                                var actions = params.mp3grid.grid.columns[i].actions;
                                // Make the drop-down actions columns
                                params.mp3grid.grid.columns[i] = {
                                    headerText : '',
                                    cellFormatter : function(args) {
                                        if ($.isPlainObject(args.row.data)) {
                                            var dropDown = args.$container.parent()
                                                .addClass("buttontd ui-state-default")
                                                .hover(
                                                function() {
                                                    dropDown.parent().addClass("ui-state-hover");
                                                },
                                                function() {
                                                    dropDown.parent().removeClass("ui-state-hover");
                                                }
                                            )
                                                .find("div");

                                            $("<span></span>")
                                                .addClass("ui-icon ui-icon-triangle-1-s")
                                                .appendTo(dropDown);

                                            var ul = $("<ul></ul>").appendTo("body");

                                            $.each(actions, function() {
                                                var action = this;
                                                $("<li><a href=\"#\"></a></li>")
                                                    .appendTo(ul)
                                                    .find("a")
                                                    .text(action.label)
                                                    .click(function(e) {
                                                        e.preventDefault();
                                                        action.action(args);
                                                    })
                                            });

                                            ul.wijmenu({
                                                trigger : dropDown,
                                                triggerEvent : "mouseenter",
                                                orientation : "vertical",
                                                showAnimation : {Animated:"slide", duration: 50, easing: null},
                                                hideAnimation : {Animated:"hide", duration: 0, easing: null},
                                                position : {
                                                    my        : "right top",
                                                    at        : "right bottom",
                                                    collision : "flip",
                                                    offset    : "0 0"
                                                }
                                            });
                                            return true;
                                        }
                                    },
                                    allowSizing : false,
                                    width : 20,
                                    showFilter : false
                                };
                                // Make the default action columns
                                params.mp3grid.grid.columns.splice(i, 0, {
                                    headerText : '',
                                    cellFormatter : function(args) {
                                        if ($.isPlainObject(args.row.data)) {
                                            args.$container.parent()
                                                .addClass("buttontd ui-state-default")
                                                .hover(
                                                    function() {
                                                        args.$container.parent().addClass("ui-state-hover");
                                                    },
                                                    function() {
                                                        args.$container.parent().removeClass("ui-state-hover");
                                                    }
                                                )
                                                .click(function(e) {
                                                    e.preventDefault();
                                                    fct = actions[0].action;
                                                    fct(args);
                                                })
                                                .find("div")
                                                .text(actions[0].label);

                                            return true;
                                        }
                                    },
                                    allowSizing : false,
                                    width : actions[0].width ? actions[0].width : 60,
                                    showFilter: false
                                });
                            }
                            params.mp3grid.grid.columns[i].headerText = self.i18n._(params.mp3grid.grid.columns[i].headerText);
                        };
                        // Translate preview properties
                        if ($.isPlainObject(params.mp3grid.preview) && params.mp3grid.preview.meta) {
                            $.each(params.mp3grid.preview.meta, function() {
                                this.label = self.i18n._(this.label);
                            });
                        }
                        // Translate inspectors properties
                        $.each(params.mp3grid.inspectors, function() {
                            this.label = self.i18n._(this.label);
                        });
                        return params;
                    }
                }, params || {});
                return self;
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
                            fn = alltabs
                            alltabs = true;
                        }
                        if (alltabs && window.parent != window && window.parent.$nos) {
                            $(window).unload(function() {
                                window.parent.$nos.nos.listener.remove(id, fn);
                            });
                            return window.parent.$nos.nos.listener.add(id, true, fn);
                        }
                        _get(id).add(fn);
                    },
                    remove: function(id, alltabs, fn) {
                        if (fn === undefined) {
                            fn = alltabs
                            alltabs = true;
                        }
                        if (alltabs && window.parent != window && window.parent.$nos) {
                            return window.parent.$nos.nos.listener.remove(id, true, fn);
                        }
                        _get(id).remove(fn);
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
                        refresh: {visible: wijdialog_options.contentUrl != null},
                        toggle: {visible: false},
                        minimize: {visible: false},
                        maximize: {visible: false}
                    }
                }, wijdialog_options);

                var $dialog = $(document.createElement('div')).appendTo($('body'));

                require([
                    'link!static/cms/js/jquery/wijmo/css/jquery.wijmo-open.1.5.0.css',
                    //'static/cms/js/jquery/wijmo/js/jquery.wijmo.wijutil',
                    'static/cms/js/jquery/wijmo/js/jquery.wijmo.wijdialog'
                ], function() {
                    $dialog.wijdialog(wijdialog_options);
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
                        'link!static/cms/js/jquery/pnotify/jquery.pnotify.default.css',
                        'static/cms/js/jquery/pnotify/jquery.pnotify.min'
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
                error: function(e) {
                    if (e.status != 0) {
                        $.nos.notify('Connection error!', 'error');
                    }
                }
            },

            grid : {
                getHeights : function() {
                    if (this.heights === undefined) {
                        var div = $('<div></div>')
                            .appendTo('body');
                        table = $('<table></table>')
                            .appendTo(div)
                            .nosgrid({
                                scrollMode : 'auto',
                                showFilter: true,
                                allowPaging : true,
                                staticRowIndex : 0,
                                data: [ ['test'] ]
                            });
                        this.heights = {
                            row : table.height(),
                            footer : div.find('.wijmo-wijgrid-footer').outerHeight(),
                            header : div.find('.wijmo-wijgrid-headerrow').outerHeight(),
                            filter : div.find('.wijmo-wijgrid-filterrow').outerHeight()
                        };
                        table.nosgrid('destroy');
                        div.remove();
                    }
                    return this.heights;
                }
            },

            media : function(input, options) {

                var contentUrls = {
                    'all'   : '/admin/admin/media/list',
                    'image' : '/admin/admin/media/mode/image/index'
                };

                options = $.extend({
                    title: input.attr('title') || 'File',
                    choose: function(e) {

                        var dialog = null;

                        // The popup will trigger this event when done
                        $.nos.listener.add('media.pick', true, function(item) {

                            // Close the popup (if we have one)
                            dialog && dialog.wijdialog('close');

                            input.inputFileThumb({
                                file: item.thumbnail
                            });
                            input.val(item.id);

                            // And self-remove from the listener
                            $.nos.listener.remove('media.pick', true, arguments.callee);
                        });

                        // Open the dialog to choose the file
                        dialog = $.nos.dialog({
                            contentUrl: contentUrls[options.mode],
                            title: 'Choose a media file'
                        });
                    }
                }, options);

                if (input.data('selected-image')) {
                    options.file = input.data('selected-image');
                }

                require([
                    'static/cms/js/jquery/jquery-ui-input-file-thumb/js/jquery.input-file-thumb',
                    'link!static/cms/js/jquery/jquery-ui-input-file-thumb/css/jquery.input-file-thumb.css'
                ], function() {
                    $(function() {
                        input.inputFileThumb(options);
                    });
                });
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
                    nosObject = this;
                    fct = function(e) {
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
                    current : function() {
                        if (window.parent != window && window.parent.$nos) {
                            return window.parent.$nos(window.frameElement).data('nos-ostabs-index');
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
                        if (!end) {
                            index = this.current() + 1;
                        }
                        if (noviusos.length) {
                            index = noviusos.ostabs('add', tab, index);
                            $tabIt = noviusos.ostabs('select', index);
                            return $tabIt;
                        } else if (tab.url) {
                            window.open(tab.url);
                        }
                        return false;
                    },
                    update : function(index, tab) {
                        if (window.parent != window && window.parent.$nos) {
                            return window.parent.$nos.nos.tabs.update(this.current(), index);
                        }
                        if (!$.isNumeric(index)) {
                            index = this.current();
                        }
                        if (noviusos.length) {
                            noviusos.ostabs('update', index, tab);
                        }
                        return true;
                    },
                    close : function(index) {
                        if (window.parent != window && window.parent.$nos) {
                            return window.parent.$nos.nos.tabs.close(this.current());
                        }
                        if (!$.isNumeric(index)) {
                            index = this.current();
                        }
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

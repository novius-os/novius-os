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
            mp3GridSetup : function() {
                var self = {};

                var objectToArray = function(val, i) {
                        return val;
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
                                object[key] = $.map(val, objectToArray);

                                for (var i = 0; i < object[key].length; i++) {
                                    if (object[key][i].lang) {
                                        object[key][i] = {
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
                                    if (object[key][i].actions) {
                                        var actions = object[key][i].actions;
                                        // Make the drop-down actions columns
                                        object[key][i] = {
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
                                        object[key].splice(i, 0, {
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
                                    texts : self.i18nMessages,
                                    splitters : {},
                                    slidersChange : function(e, rapport) {
                                        //$nos.saveUserConfiguration("'.$config['configuration_id'].'.ui.splitters", rapport)
                                    }
                                }
                            }, self);

                            if (params.mp3grid.splittersVertical) {
                                params.mp3grid.splitters.vertical = {splitterDistance : params.mp3grid.splittersVertical};
                            }
                            if (params.mp3grid.splittersHorizontal) {
                                params.mp3grid.splitters.horizontal = {splitterDistance : params.mp3grid.splittersHorizontal};
                            }
                            params.mp3grid.adds = $.map(params.mp3grid.adds, objectToArray);
                            params.mp3grid.inspectors = $.map(params.mp3grid.inspectors, objectToArray);

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

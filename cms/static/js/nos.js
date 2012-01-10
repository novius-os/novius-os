/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

define([
		'static/cms/js/jquery/globalize/globalize.min',
		'static/cms/js/jquery/mousewheel/jquery.mousewheel.min',
		'static/cms/js/jquery/wijmo/js/jquery.wijmo-open.1.5.0.min',
		'static/cms/js/jquery/wijmo/js/jquery.wijmo-complete.1.5.0.min'
	], function() {

		return (function($, undefined) {
			$(function() {
				var noviusos = $('#noviusos');
				$.nos = {
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
									return window.parent.$nos.nos.listener.add(id, true, fn);
								}
								$(window).unload(function() {
									window.parent.$nos.nos.listener.remove(id, fn);
								});
								//log('listener.add', id, window);
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
								if (id.substring(id.length - 1) == '!') {
									triggerName = id.substring(0, id.length - 1);
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
									//log('listener.fire', triggerName, window);
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
								pin: { visible: false },
								refresh: { visible: wijdialog_options.contentUrl != null },
								toggle: { visible: false },
								minimize: { visible: false },
								maximize: { visible: false }
							}
						}, wijdialog_options);

						var $dialog = $(document.createElement('div')).appendTo($('body'));

						require([
							'link!static/cms/js/jquery/wijmo/css/jquery.wijmo-open.1.5.0.css',
							//'static/cms/js/jquery/wijmo/js/jquery.wijmo.wijutil',
							'static/cms/js/jquery/wijmo/js/jquery.wijmo.wijdialog',
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
							options = { title : options };
						}
						if ( type !== undefined ) {
							$.extend(options, $.isPlainObject( type ) ? type : { type : type } );
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
                    ajax : function(options) {
                        $.ajax({
                            url: options.url,
                            dataType: 'json',
                            data: options.data,
                            type: 'POST',
                            success: function(json) {
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
                                if (json.redirect) {
                                    document.location = json.redirect;
                                }
                                // Close at the end!
                                if (json.closeTab) {
                                    $.nos.tabs.close();
                                }

                                if (typeof options['success'] === 'function') {
                                    options['success'](json);
                                }
                            },
                            error: function(e) {
                                if (e.status != 0) {
                                    $.nos.notify("Connexion error !", "error");
                                }
                                if (typeof options['error'] === 'function') {
                                    options['error'](json);
                                }
                            }
                        });
                    },

                    saveUserConfiguration: function(key, configuration) {
                        this.ajax({
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
                            nosObject.tabs.saveTabs();
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
                        $('#noviusos').ostabs(configuration);
                        /*
                        if (configuration['user_configuration']['tabs']) {
                            noviusos.ostabs('setConfiguration', configuration['user_configuration']['tabs']);
                        }
                        */
                    },

					tabs : {
						index : function() {
							if (window.parent != window && window.parent.$nos) {
								return window.parent.$nos(window.frameElement).data('nos-ostabs-index');
							}
							return false;
						},
						link : function(event, container, data) {
							var data = data || {},
								a = container.find( 'a' );

							if ( a.length && !data.sameTab) {
								a.click(function(e) {
									var a = this;
									$.nos.tabs.openInNewTab({
										url : $(a).attr('href'),
										label : $(a).text()
									});
									e.preventDefault();
								});
							}
							return true;
						},
						openInNewTab: function(tab) {
							if (window.parent != window && window.parent.$nos) {
								this.add(tab, this.index() + 1);
							} else {
								window.open(tab.url);
							}
						},
						add : function(tab, index) {
							if (window.parent != window && window.parent.$nos) {
								return window.parent.$nos.nos.tabs.add(tab, index);
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
						updateTab : function(index, tab) {
							if (window.parent != window && window.parent.$nos) {
								return window.parent.$nos.nos.tabs.updateTab(this.index(), index);
							}
							if (noviusos.length) {
								noviusos.ostabs('update', index, tab);
							}
							return true;
						},
						close : function(index) {
							if (window.parent != window && window.parent.$nos) {
								return window.parent.$nos.nos.tabs.close(this.index());
							}
							if (noviusos.length) {
								noviusos.ostabs('remove', index);
							}
							return true;
						},
                        /** Save tabs in user configuration file
                         */
                        saveTabs: function() {
                            if (window.parent != window && window.parent.$nos) {
                                return window.parent.$nos.nos.tabs.updateTab(this.index(), index);
                            }
                            if (noviusos.length) {
                                $nos.nos.saveUserConfiguration('tabs', {selected: noviusos.ostabs('getSelected'), tabs: noviusos.ostabs('tabs')});
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
										.wijgrid({
											scrollMode : 'auto',
											showFilter: true,
											allowPaging : true,
											staticRowIndex : 0,
											data: [ ['<a class="ui-state-default" href="#" style="display:inline-block;"><span class="ui-icon ui-icon-pencil"></span></a>'] ]
										});
								this.heights = {
									row : table.height(),
									footer : div.find('.wijmo-wijgrid-footer').outerHeight(),
									header : div.find('.wijmo-wijgrid-headerrow').outerHeight(),
									filter : div.find('.wijmo-wijgrid-filterrow').outerHeight()
								};
								table.wijgrid('destroy');
								div.remove();
							}
							return this.heights;
						}
					}
				};
				$.nos.media = function(input, options) {

					var contentUrls = {
						'all'   : '/admin/cms_media/list',
						'image' : '/admin/cms_media/mode/image/index'
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
				};
				window.$nos = $;

			});
			return $;
		})(window.jQuery);
	});

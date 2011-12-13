/**
 * NOVIUS OS - Web OS for digital communication
 * 
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

define([
	'jquery-nos'
], function( $, undefined ) {
	$.widget( "nos.mp3grid", {
		options: {
			adds : [],
			inspectors : [],
			thumbnails : false,
			defaultView : 'grid',
			texts : {
				addDropDown : 'Select an action',
				columns : 'Columns',
				showFiltersColumns : 'Filters column header',
				visibility : 'Visibility',
				settings : 'Settings',
				vertical : 'Vertical',
				horizontal : 'Horizontal',
				hidden : 'Hidden',
				showNbItems : 'Showing {{x}} out of {{y}} items',
				showOneItem : 'Show 1 item',
				showNoItem : 'No item',
				showAll : 'Show all items',
				views : 'Views',
				viewGrid : 'Grid',
				viewThumbnails : 'Thumbnails'
			}
		},

		pageIndex : 0,
		menuSettings : {},
		showFilter : false,
		gridRendered : false,
		resizing : true,
		init : false,

		_create: function() {
			var self = this,
				o = self.options;

			self.element.addClass('nos-mp3grid');

			self.uiHeaderBar = $('<div></div>').addClass('nos-headerbar')
				.appendTo(self.element);

			self.uiAdds = $('<div></div>').addClass('nos-adds')
				.appendTo(self.uiHeaderBar);
			self.uiAddsButton = $('<button type="button"></button>').appendTo(self.uiAdds);
			self.uiAddsDropDown = $('<button type="button"></button>').text(o.texts.addDropDown)
				.appendTo(self.uiAdds);
			self.uiAddsMenu = $('<ul></ul>').appendTo(self.uiAdds);

			self.uiSettings = $('<div></div>').addClass('nos-settings')
				.appendTo(self.uiHeaderBar);
			self.uiSettingsButton = $('<button type="button"></button>').appendTo(self.uiSettings);
			self.uiSettingsMenu = $('<ul></ul>').appendTo(self.uiSettings);

			self.uiSplitterVertical = $('<div></div>').addClass('nos-splitter-v')
				.appendTo(self.element);
			self.uiSplitterVerticalRight = $('<div></div>').appendTo(self.uiSplitterVertical);
			self.uiInspectorsVertical = $('<ul></ul>').addClass('nos-inspectors nos-inspectors-v')
				.appendTo(self.uiSplitterVerticalRight);
			self.uiSplitterVerticalLeft = $('<div></div>').appendTo(self.uiSplitterVertical);

			self.uiSplitterHorizontal = $('<div></div>').appendTo(self.uiSplitterVerticalLeft);
			self.uiSplitterHorizontalTop = $('<div></div>').appendTo(self.uiSplitterHorizontal);
			self.uiInspectorsHorizontal = $('<ul></ul>').addClass('nos-inspectors nos-inspectors-h')
				.appendTo(self.uiSplitterHorizontalTop);
			self.uiSplitterHorizontalBottom = $('<div></div>').appendTo(self.uiSplitterHorizontal);

			self.uiSearchBar = $('<form></form>').addClass('nos-searchbar')
				.appendTo(self.uiSplitterHorizontalBottom);
			self.uiShowNbItems = $('<span></span>').addClass('nos-nbresult')
				.appendTo(self.uiSearchBar);
			self.uiSearchInput = $('<input type="search" name="search" placeholder="Search" value="" />').appendTo(self.uiSearchBar);
			self.uiInspectorsTags = $('<div></div>').addClass('nos-inspectorstag')
				.appendTo(self.uiSearchBar);
			self.uiShowAll = $('<a href="#"></a>').text(o.texts.showAll)
				.addClass('nos-inspectorsreset')
				.appendTo(self.uiSearchBar)
				.hide();

			self.uiGrid = $('<table></table>').appendTo(self.uiSplitterHorizontalBottom);

			self.uiThumbnail = $('<div></div>').appendTo(self.uiSplitterHorizontalBottom);
		},

		_init: function() {
			var self = this,
				o = self.options;

			if (!$.isPlainObject(o.thumbnails)) {
				o.thumbnails = false;
			} else {
				o.thumbnails = $.extend({
					thumbnailSize : 64
				}, o.thumbnails);
			}

			self._uiAdds()
				._uiSplitters()
				._uiInspectors()
				._uiSearchBar()
				._uiList()
				._uiSettings()
				._listeners();

			self.init = true;

			$(window).resize(function() {
				if ( self.timeoutResize ) {
					clearTimeout(self.timeoutResize);
				}
				self.timeoutResize = setTimeout(function() {
					if (self.resizing) {
					    self.uiSplitterVertical.add(self.uiSplitterHorizontal)
							.wijsplitter('refresh');
						self._resizeInspectorsV()
							._resizeInspectorsH()
							.gridRefresh();
					}
				}, 100)
			});

			$(window).bind({
				blur : function() {
					self.resizing = false;
				},
				focus : function() {
					$('html').focus();
					self.resizing = true;
				}
			});
			$(window).focus();
		},

		_uiAdds : function() {
			var self = this,
				o = self.options;

			if (!$.isArray(o.adds) || !o.adds.length) {
				self.uiAdds.hide();
				return self;
			}

			var first = o.adds.shift();

			self.uiAddsButton.button({
					label: first.label,
					icons : {
						primary: 'ui-icon ui-icon-circle-plus',
						secondary: null
					}
				})
				.click(function() {
					$.nos.tabs.openInNewTab({
						url : first.url,
						label : first.label
					});
				});

			self.uiAddsDropDown.button({
					text: false,
					icons: {
						primary: "ui-icon-triangle-1-s"
					}
				});

			self.uiAdds.buttonset();

			$.each(o.adds, function() {
				var li = $('<li></li>').appendTo(self.uiAddsMenu),
					a = $('<a href="#"></a>').click(function() {
							$.nos.tabs.openInNewTab({
								url : this.url,
								label : this.label
							});
						}).appendTo(li);

				$('<span></span>').text(this.label)
					.appendTo(a);
			});
			self.uiAddsMenu.wijmenu({
					trigger : self.uiAddsDropDown,
					triggerEvent : 'mouseenter',
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

			return self;
		},

		_uiSettings : function() {
			var self = this,
				o = self.options;

			self.uiSettingsButton.button({
				 label : o.texts.settings,
				 icons : {primary : 'ui-icon-gear'}
			});

			self._viewSettingsMenu()
				._inspectorsSettingsMenu()
				._uiSettingsMenu();

			return self;
		},

		_uiSettingsMenu : function() {
			var self = this;

			log('_uiSettingsMenu', self.menuSettings);

			$.each(self.menuSettings, function() {
				self._uiSettingsMenuAdd(this, self.uiSettingsMenu);
			});

			self.uiSettingsMenu.wijmenu({
					trigger : self.uiSettingsButton,
					triggerEvent : 'mouseenter',
					orientation : 'vertical',
					showAnimation : {Animated:"slide", duration: 50, easing: null},
					hideAnimation : {Animated:"hide", duration: 0, easing: null}
				});

			return self;
		},

		_uiSettingsMenuAdd : function(item, ul) {
			var self = this;

			li = $('<li></li>').appendTo(ul)
				.append(self._uiSettingsMenuRow(item.content));

			if ($.isArray(item.childs) || $.isPlainObject(item.childs)) {
				ul = $('<ul></ul>').appendTo(li);
				$.each(item.childs, function() {
					self._uiSettingsMenuAdd(this, ul);
				});
			}

			return self;
		},

		_uiSettingsMenuRow : function(args) {
			if (!$.isPlainObject(args)) {
				args = {
					label : args
				};
			}
			args = $.extend({
				name : '',
				id : '',
				value : '',
				checked : false,
				click : $.noop,
				label : '',
				radio : false
			}, args);

			var span = $('<span></span>');
			if (args.name) {
				var checked = args.checked;
				if ($.isFunction(checked)) {
					checked = checked();
				}

				$('<input type="' + (args.radio ? 'radio' : 'checkbox') + '" name="' + args.name + '" id="' + args.id + '" value="' + args.value + '" ' + (checked ? 'checked' : '') + ' />')
					.click(function(e) {
						args.click.call(this, e);
					})
					.appendTo(span);

				$('<label for="' + args.id + '"></label>').text(args.label)
					.appendTo(span);
			} else {
				$('<span></span>').text(args.label)
					.appendTo(span);
			}

			return span;
		},

		_viewSettingsMenu : function() {
			var self = this,
				o = self.options,
				views = {}
				nbViews = 0;

			self.menuSettings.grid = {
				content : o.label,
				childs : {}
			};

			if (o.grid) {
				nbViews++;
				views['grid'] = {
						content : {
							name : 'view',
							id : 'view_grid',
							checked : function() {
								return o.defaultView === 'grid';
							},
							click : function() {
								o.defaultView = 'grid';
								self.gridRefresh();
								self.uiSettingsMenu.wijmenu('hideAllMenus');
							},
							label : o.texts.viewGrid,
							radio : true
						}
					};
			}
			if (o.thumbnails) {
				var sizes = [32, 64];
				$.each(sizes, function(i, size) {
					nbViews++;
					views['thumbnails' + size] = {
							content : {
								name : 'view',
								id : 'view_thumbnails_' + size,
								checked : function() {
									return o.defaultView === 'thumbnails' && o.thumbnails.thumbnailSize === size;
								},
								click : function() {
									o.defaultView = 'thumbnails';
									o.thumbnails.thumbnailSize = size;
									self.gridRefresh();
									self.uiSettingsMenu.wijmenu('hideAllMenus');
									return true;
								},
								label : o.texts.viewThumbnails + ' ' + size + 'px',
								radio : true
							}
						};
				})
			}
			if (nbViews > 1) {
				self.menuSettings.grid.childs = {
					views : {
						content : o.texts.views,
						childs : views
					}
				};
			} else {
				delete self.menuSettings.grid;
			}

			return self;
		},

		_gridSettingsMenu : function() {
			var self = this,
				o = self.options,
				keys = ['columns', 'showFilters'];

			if (!self.menuSettings.grid) {
				self.menuSettings.grid = {
					content : o.label,
					childs : {}
				};
			}

			$.each(keys, function(i, key) {
				if (self.menuSettings.grid.childs[key]) {
					delete self.menuSettings.grid.childs[key];
				}
			})

			if (o.defaultView === 'thumbnails') {

			} else {
				var nbColumns = 0,
					columns = {},
					showFilter = self.showFilter;

			    o.grid.columns = self.uiGrid.wijgrid("option", "columns");

				$.each(o.grid.columns, function (index, column) {
					if (column.showFilter === undefined || column.showFilter) {
						showFilter = true;
					}
					nbColumns++;
					columns[index] = {
							content : {
								name : 'columnsGrid',
								id : 'columnsGrid_' + index,
								checked : column.visible,
								click : function() {
									o.grid.columns[index].visible = $(this).is(':checked');
									self.uiGrid.wijgrid('doRefresh');
									self.uiSettingsMenu.wijmenu('hideAllMenus');
								},
								label : column.headerText
							}
						};
	            });
				if (nbColumns > 1) {
					self.menuSettings.grid.childs['columns'] = {
							content : o.texts.columns,
							childs : columns
						};
				}
				if (showFilter) {
					self.menuSettings.grid.childs['showFilters'] = {
							content : {
								name : 'showFilterGrid',
								id : 'showFilterGrid',
								checked : self.showFilter,
								click : function() {
									self.showFilter = $(this).is(':checked');
									self.gridRefresh()
										.uiSettingsMenu.wijmenu('hideAllMenus');
								},
								label : o.texts.showFiltersColumns
							}
						};
				}
			}

			if ($.isEmptyObject(self.menuSettings.grid.childs)) {
				delete self.menuSettings.grid;
			}

			self._refreshSettingsMenu();

			return self;
		},

		_inspectorsSettingsMenu : function() {
			var self = this,
				o = self.options,
				states = ['v', 'h', ''];

			$.each(o.inspectors, function() {
				var inspector = this,
					childs = {};

				$.each(states, function(i, value) {
					var label,
						checked = false;

					switch (value) {
						case 'v' :
							label = o.texts.vertical;
							checked = function() {
								return !inspector.hide && inspector.vertical ? true : false;
							}
							break;
						case 'h' :
							label = o.texts.horizontal;
							checked = function() {
								return !inspector.hide && !inspector.vertical ? true : false;
							}
							break;
						case '' :
							label = o.texts.hidden;
							checked = function() {
								return inspector.hide || false;
							}
							break;
					}

					childs[label] = {
						content : {
							name : inspector.widget_id,
							id : 'radio' + inspector.widget_id + '_' + value,
							value : value,
							checked : checked,
							click : function() {
								var input = $(this),
									widget_id = input.attr('name'),
									orientation = input.val(),
									widget = $('#' + widget_id);

								if (!orientation) {
									inspector.hide = true;
									if (widget.length) {
										var menu = self.menuSettings[inspector.widget_id];
										if ($.isPlainObject(menu) && $.isPlainObject(menu.childs) && $.isPlainObject(menu.childs.visibility)) {
											menu.childs = menu.childs.visibility.childs;
											self._refreshSettingsMenu();
										}
										widget.closest('li.ui-widget-content')
											.remove();
										self._resizeInspectorsV()
											._resizeInspectorsH();
									}
								} else {
									inspector.hide = false;
									var target = orientation === 'v' ? self.uiInspectorsVertical : self.uiInspectorsHorizontal;
									inspector.vertical = orientation === 'v';
									if ( widget.length ) {
										if ( !target.has(widget).length ) {
											widget.closest('li.ui-widget-content')
												.find("script")
												.remove()
												.end()
												.css({ width: '100%', height: 'auto' })
												.appendTo(target);
											self._resizeInspectorsV()
												._resizeInspectorsH();
										}
									} else {
										var $li = $('<li></li>').addClass('ui-widget-content')
											.data('inspectorurl', inspector.url)
											.appendTo(target);
										self['_resizeInspectors' + orientation.toUpperCase()]()
											._loadInspector($li);
									}
								}
								self.uiSettingsMenu.wijmenu('hideAllMenus');
							},
							label : label,
							radio : true
						}
					};
				});


				self.menuSettings[inspector.widget_id] = {
					content : inspector.label,
					childs : childs
				};
			});

			return self;
		},

		_addSettingsInspectorMenu : function(widget_id, key, item) {
			var self = this,
				o = self.options,
				inspector = self.menuSettings[widget_id];

			if (inspector) {
				if (!inspector.childs.visibility) {
					self.menuSettings[widget_id].childs = {
						visibility : {
							content : o.texts.visibility,
							childs : inspector.childs
						}
					};
				}
				self.menuSettings[widget_id].childs[key] = item;
				self._refreshSettingsMenu();
			}

			return self;
		},

		_uiSplitters : function() {
			var self = this,
				refreshV = function() {
					self.uiSplitterHorizontal.wijsplitter("refresh");
					self._resizeInspectorsV()
						._resizeInspectorsH()
						.gridRefresh();
				},
				refreshH = function() {
					self._resizeInspectorsH()
						.gridRefresh();
				};

			self.uiSplitterVertical.wijsplitter({
					orientation: "vertical",
					splitterDistance: 300,
					showExpander: false,
					fullSplit: false,
					panel1 : {
						minSize : 150,
						scrollBars : 'hidden'
					},
					panel2 : {
						minSize : 200,
						scrollBars : 'hidden'
					},
					expanded: function () {
						refreshV();
					},
					collapsed: function () {
						refreshV();
					},
					sized: function () {
						self.resizing = true;
						refreshV();
					}
				})
				.find('.ui-resizable-handle')
				.mousedown(function() {
				    self.resizing = false;
				});

			self.uiSplitterHorizontal.wijsplitter({
					orientation: "horizontal",
					fullSplit: true,
					splitterDistance: 300,
					showExpander: false,
					panel1 : {
						minSize : 200,
						scrollBars : 'hidden'
					},
					panel2 : {
						minSize : 200,
						scrollBars : 'hidden'
					},
					expanded: function () {
						refreshH();
					},
					collapsed: function () {
						refreshH();
					},
					sized: function () {
						self.resizing = true;
						refreshH();
					}
				})
				.find('.ui-resizable-handle')
				.mousedown(function() {
				    self.resizing = false;
				});

			return self;
		},

		_uiInspectors : function() {
			var self = this,
				o = self.options;

			$.each(o.inspectors, function() {
				if (!this.hide) {
					$('<li></li>').addClass('ui-widget-content')
						.data('inspectorurl', this.url)
						.appendTo( this.vertical ? self.uiInspectorsVertical : self.uiInspectorsHorizontal );
				}
			});

			self._resizeInspectorsV()
				._resizeInspectorsH();

			self.uiInspectorsVertical.find('> li')
				.add(self.uiInspectorsHorizontal.find('> li'))
				.each(function() {
					self._loadInspector($(this));
				});

			self.uiInspectorsVertical.add(self.uiInspectorsHorizontal).sortable({
					connectWith: ".nos-inspectors",
					start : function() {
						self.resizing = false;
					},
					stop: function() {
						self.resizing = true;
						self._resizeInspectorsV()
							._resizeInspectorsH();
					}
				});

			return self;
		},

		_loadInspector : function($li) {
			var self = this;

			$.ajax({
				url: $li.data('inspectorurl'),
				dataType: 'html'
			})
			.done(function(data) {
				$(data).appendTo($li); // appendTo for embed javascript work
			})
			.fail(function(jqXHR, textStatus, errorThrown) {
				log('error');
				log(textStatus);
				log(errorThrown);
			});

			return self;
		},

		_uiSearchBar : function() {
			var self = this;

			self.uiSearchInput.bind("keypress", function( event ) {
					var keyCode = $.ui.keyCode;

					self.pageIndex = 0;

					if ( self.timeoutSearchInput ) {
						clearTimeout(self.timeoutSearchInput);
					}

					if ($.inArray(event.keyCode, [keyCode.ENTER, keyCode.NUMPAD_ENTER]) != -1) {
						self.gridRefresh();
						return false;
					}

					self.timeoutSearchInput = setTimeout(self.gridRefresh, 500);
				});

			self.uiShowAll.click(function(e) {
					e.preventDefault();
					self.uiSearchInput.val('');
					self.uiInspectorsTags.empty();
					self.gridRefresh();
				});

			return self;
		},

		_uiList : function() {
			var self = this,
				o = self.options;

			self.uiThumbnail.thumbnails('destroy')
				.empty()
				.hide();
			self.uiGrid.wijgrid('destroy')
				.empty()
				.hide();
			if (o.defaultView === 'thumbnails') {
				self.uiThumbnail.show();
				self._uiThumbnail();
			} else {
				self.uiGrid.show();
				self._uiGrid();
			}

			self._gridSettingsMenu();

			return self;
		},

		_uiGrid : function() {
			var self = this,
				o = self.options,
				position = self.uiGrid.offset(),
				height = $(window).height() - position.top,
				heights = $.nos.grid.getHeights();

			self.uiGrid.css('height', height)
				.wijgrid($.extend({
					columnsAutogenerationMode : 'none',
					showFilter: self.showFilter,
					allowSorting: true,
					scrollMode : 'auto',
					allowPaging : true,
					pageIndex : self.pageIndex,
					pageSize: Math.floor((height - heights.footer - heights.header - (self.showFilter ? heights.filter : 0)) / heights.row),
					allowColSizing : true,
					allowColMoving : true,
					staticRowIndex : 0,
					data: new wijdatasource({
						dynamic: true,
						proxy: new wijhttpproxy({
							url: o.grid.proxyurl,
							dataType: "json",
							error: function(jqXHR, textStatus, errorThrown) {
								log(jqXHR, textStatus, errorThrown);
							},
							data: {}
						}),
						loading: function (dataSource, userData) {
							var r = userData.data.paging;
							self.pageIndex = r.pageIndex;
							dataSource.proxy.options.data.inspectors = self._jsonInspectors();
							dataSource.proxy.options.data.offset = r.pageIndex * r.pageSize;
							dataSource.proxy.options.data.limit = r.pageSize;
						},
						loaded: function(dataSource, data) {
							if (dataSource.data.totalRows === 0) {
								self.uiShowNbItems.text(o.texts.showNoItem);
							} else if (dataSource.data.totalRows === 0) {
								self.uiShowNbItems.text(o.texts.showOneItem);
							} else {
								self.uiShowNbItems.text(o.texts.showNbItems.replace('{{x}}', dataSource.data.length).replace('{{y}}', dataSource.data.totalRows));
							}
							self.uiShowNbItems.show();

							self.uiShowAll[self.uiInspectorsTags.find('span').length ? 'show' : 'hide']();
						},
						reader: {
							read: function (dataSource) {
								var count = parseInt(dataSource.data.total, 10);
								dataSource.data = dataSource.data.items;
								dataSource.data.totalRows = count;
							}
						}
					}),
					currentCellChanged: function () {
						self.uiGrid.wijgrid("currentCell", -1, -1);
					},
					rendering : function() {
						self.gridRendered = false;
					},
					rendered : function() {
						self.gridRendered = true;
						self.uiGrid.css('height', 'auto');
					}
				}, o.grid));

			return self;
		},

		_uiThumbnail : function() {
			var self = this,
				o = self.options,
				position = self.uiThumbnail.offset(),
				height = $(window).height() - position.top,
				heights = $.nos.grid.getHeights();

			self.uiThumbnail.css('height', height)
				.thumbnails($.extend({
					pageIndex: 0,
					url: o.grid.proxyurl,
					loading: function (dataSource, userData) {
						var r = userData.data.paging;
						self.pageIndex = r.pageIndex;
						dataSource.proxy.options.data.inspectors = self._jsonInspectors();
						dataSource.proxy.options.data.offset = r.pageIndex * r.pageSize;
						dataSource.proxy.options.data.limit = r.pageSize;
					},
					loaded: function(dataSource, data) {
						if (dataSource.data.totalRows === 0) {
							self.uiShowNbItems.text(o.texts.showNoItem);
						} else if (dataSource.data.totalRows === 0) {
							self.uiShowNbItems.text(o.texts.showOneItem);
						} else {
							self.uiShowNbItems.text(o.texts.showNbItems.replace('{{x}}', dataSource.data.length).replace('{{y}}', dataSource.data.totalRows));
						}
						self.uiShowNbItems.show();

						self.uiShowAll[self.uiInspectorsTags.find('span').length ? 'show' : 'hide']();
					},
					reader: {
						read: function (dataSource) {
							var count = parseInt(dataSource.data.total, 10);
							dataSource.data = dataSource.data.items;
							dataSource.data.totalRows = count;
						}
					}
				}, o.thumbnails));

			return self;
		},

		_listeners : function() {
			var self = this,
				o = self.options;

			$nos.nos.listener.add('ostabs.show', function(index) {
				if ($.nos.tabs.index() === index) {
					$(window).focus();
				} else {
					$(window).blur();
				}
			});

			$nos.nos.listener.add('inspector.showFilter', false, function(widget_id, change, checked) {
				self._addSettingsInspectorMenu(widget_id, 'showFilters', {
						content : {
							name : 'showFilter' + widget_id,
							id : 'showFilter' + widget_id,
							checked : checked,
							click : function() {
								change($(this).is(':checked'));
								self.uiSettingsMenu.wijmenu('hideAllMenus');
							},
							label : o.texts.showFiltersColumns
						}
					})
			});

			$nos.nos.listener.add('inspector.declareColumns', false, function(widget_id, columns) {
				if (columns.length > 1) {
					var childs = {};

					$.each(columns, function(i) {
						var column = this;

						childs[i] = {
								content : {
									name : 'columns' + widget_id,
									id : 'column' + widget_id + '_' + i,
									checked : column.visible,
									click : function() {
										column.change($(this).is(':checked'));
										self.uiSettingsMenu.wijmenu('hideAllMenus');
									},
									label : column.label
								}
							};
					});

					self._addSettingsInspectorMenu(widget_id, 'column', {
							content : o.texts.columns,
							childs : childs
						});
				}
			});

			$nos.nos.listener.add('inspector.selectionChanged', false, function(input_name, value, label) {
				var multiple = false,
					name = input_name;

				if (input_name.substr(-2, 2) === '[]') {
					name = input_name.substr(0, input_name.length - 2);
					multiple = true;
				}

				if (!multiple) {
					self.uiInspectorsTags.find('span.' + name).remove();
				} else {
					var already = false;
					self.uiInspectorsTags.find('span.' + name).each(function() {
						if ($(this).find('input').val() === value) {
							already = true;
							return false;
						}
					});
					if (already) {
						return true;
					}
				}

				self.pageIndex = 0;

				var span = $('<span></span>').addClass('ui-state-default ui-corner-all ' + name)
					.text(label)
					.appendTo(self.uiInspectorsTags);

				$('<input type="hidden" name="' + input_name + '" />').val(value)
					.appendTo(span);

				$('<a href="#"></a>').addClass('ui-icon ui-icon-close')
					.click(function(e) {
						e.preventDefault();
						$(this).parent().remove();
						self.gridRefresh();
					})
					.appendTo(span);

				self.gridRefresh();
			});

			return self;
		},

		_jsonInspectors : function() {
			var self = this,
				inspectors = {};

			self.uiSearchBar.find('input').each(function() {
				var input = $(this),
					name = input.attr('name'),
					multiple = false;

				if (name.substr(-2, 2) === '[]') {
					name = name.substr(0, name.length - 2);
					multiple = true;
				}

				if (!multiple) {
					inspectors[name] = input.val();
				} else {
					if (!$.isArray(inspectors[name])) {
						inspectors[name] = [];
						inspectors[name].push( input.val() );
					} else if (-1 == $.inArray( input.val(), inspectors[name])) {
						inspectors[name].push( input.val() );
					}
				}
			});

			return inspectors;
		},

		_resizeInspectorsV : function() {
			var self = this;

		    if (self.resizing) {
				var inspectors = self.uiInspectorsVertical.find('> li').css({
						width: '100%',
						height: 'auto'
					});
	
				if (inspectors.length) {
					inspectors.css('height', ( self.uiInspectorsVertical.height() / inspectors.length )  + 'px')
						.trigger('inspectorResize');
				} else {
					self._hideSplitterV();
				}
			}

			return self;
		},

		_resizeInspectorsH : function() {
			var self = this;

		    if (self.resizing) {
				var inspectors = self.uiInspectorsHorizontal.find('> li').css({
						width: 'auto',
						height: '100%'
					});
	
				if (inspectors.length) {
					inspectors.css('width', ( self.uiInspectorsHorizontal.width() / inspectors.length )  + 'px')
						.trigger('inspectorResize');
				} else {
					self._hideSplitterH();
				}
			}

			return self;
		},

		_hideSplitterV : function() {
			var self = this,
				o = self.options,

				bar = $('.wijmo-wijsplitter-v-bar').hide();

			self.uiSplitterVertical.wijsplitter('option', 'panel1', {collapsed : true});

			return self;
		},

		_hideSplitterH : function() {
			var self = this,
				o = self.options,

				bar = $('.wijmo-wijsplitter-h-bar').hide();

			self.uiSplitterHorizontal.wijsplitter('option', 'panel1', {collapsed : true});

			return self;
		},

		_refreshSettingsMenu : function() {
			var self = this;

			self.uiSettingsMenu.wijmenu('destroy')
				.empty();
			self._uiSettingsMenu();

			return self;
		},

		gridRefresh : function() {
			var self = this,
				o = self.options;

			if (self.init) {
				self._uiList();
			}

			return self;
		}
	});
	return $;
});

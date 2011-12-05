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
			texts : {
				addDropDown : 'Select an action',
				columns : 'Columns',
				showFiltersColumns : 'Filters column header',
				visibility : 'Visibility',
				settings : 'Settings',
				vertical : 'Vertical',
				horizontal : 'Horizontal',
				hidden : 'Hidden',
				showNbItems : 'Show {{x}} items',
				showOneItem : 'Show 1 item',
				showNoItem : 'No item',
				showAll : 'Show all items'
			}
		},

		pageIndex : 0,
		menuInspectors : {},
		showFilter : false,
		gridRendered : false,
		resizing : true,

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
		},

		_init: function() {
			var self = this;

			self._uiAdds()
				._uiSplitters()
				._uiInspectors()
				._uiSearchBar()
				._uiGrid()
				._uiSettings()
				._listeners();

			$(window).resize(function() {
				if (self.resizing) {
				    self.uiSplitterVertical.add(self.uiSplitterHorizontal)
						.wijsplitter('refresh');
					self._resizeInspectorsV()
						._resizeInspectorsH()
						.gridRefresh();
				}
			});
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

			self._gridSettingsMenu()
				._inspectorsSettingsMenu()
				._uiSettingsMenu();

			return self;
		},

		_uiSettingsMenuAdd : function(item, ul) {
			var self = this,
				o = self.options;

			li = $('<li></li>').appendTo(ul)
				.append(item.content.clone(true));

			if ($.isArray(item.childs) || $.isPlainObject(item.childs)) {
				ul = $('<ul></ul>').appendTo(li);
				$.each(item.childs, function() {
					self._uiSettingsMenuAdd(this, ul);
				});
			}

			return self;
		},

		_uiSettingsMenuCheckbox : function(name, id, checked, click, label) {
			var self = this,
				span = $('<span></span>');
				
			$('<input type="checkbox" name="' + name + '" id="' + id + '" ' + (checked ? 'checked' : '') + ' />')
				.click(click)
				.appendTo(span);

			$('<label for="' + id + '"></label>').text(label)
				.appendTo(span);

			return span;
		},

		_uiSettingsMenu : function() {
			var self = this,
				o = self.options;

			$.each(self.menuInspectors, function() {
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

		_uiSplitters : function() {
			var self = this,
				o = self.options
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
					expanded: function (e) {
						refreshV();
					},
					collapsed: function (e) {
						refreshV();
					},
					sized: function (e) {
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
					expanded: function (e) {
						refreshH();
					},
					collapsed: function (e) {
						refreshH();
					},
					sized: function (e) {
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
					stop: function(e, ui) {
						self.resizing = true;
						self._resizeInspectorsV()
							._resizeInspectorsH();
					}
				});

			return self;
		},

		_loadInspector : function($li) {
			var self = this,
				o = self.options;

			$.ajax({
				url: $li.data('inspectorurl'),
				dataType: 'html'
			})
			.done(function(data, textStatus, jqXHR) {
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
			var self = this,
				o = self.options;

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

		_uiGrid : function() {
			var self = this,
				o = self.options,
				position = self.uiGrid.offset(),
				height = $(window).height() - position.top
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
								self.uiShowNbItems.text(o.texts.showNbItems.replace('{{x}}', dataSource.data.totalRows));
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
					currentCellChanged: function (e) {
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

		_listeners : function() {
			var self = this,
				o = self.options;

			$nos.nos.listener.add('ostabs.show', function(index) {
				if ($.nos.tabs.index() === index) {
				    setTimeout(function() {
						self.resizing = true;						
					}, 500);						
				} else {
				    self.resizing = false;
				}
			});

			$nos.nos.listener.add('inspector.showFilter', false, function(widget_id, change, checked) {
				var inspector;
				if ((inspector = self.menuInspectors[widget_id])) {
					self._addSettingsMenu(widget_id, 'showFilters', {
							content : self._uiSettingsMenuCheckbox('showFilter' + widget_id, 'showFilter' + widget_id, checked, function() {
									change($(this).is(':checked'));
									self.uiSettingsMenu.wijmenu('hideAllMenus');
								}, o.texts.showFiltersColumns)
						})
						._refreshSettingsMenu();
				}
			});

			$nos.nos.listener.add('inspector.declareColumns', false, function(widget_id, columns) {
				var inspector;
				if ((inspector = self.menuInspectors[widget_id]) && columns.length > 1) {
					var childs = [];

					$.each(columns, function(i) {
						var column = this;

						childs.push({
								content : self._uiSettingsMenuCheckbox('columns' + widget_id, 'column' + widget_id + '_' + i, column.visible, function() {
										column.change($(this).is(':checked'));
										self.uiSettingsMenu.wijmenu('hideAllMenus');
									}, column.label)
							});
					});

					self._addSettingsMenu(widget_id, 'column', {
							content : $('<span></span>').text(o.texts.columns),
							childs : childs
						})
						._refreshSettingsMenu();
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
				o = self.options,
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
			var self = this,
				o = self.options;

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
			var self = this,
				o = self.options;

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
			var self = this,
				o = self.options;

			self.uiSettingsMenu.wijmenu('destroy')
				.empty();
			self._uiSettingsMenu();

			return self;
		},

		_gridSettingsMenu : function() {
			var self = this,
				o = self.options,
				span = $('<span></span>'),
				columns = [],
				showFilter = self.showFilter;
				
		    o.grid.columns = self.uiGrid.wijgrid("option", "columns");

			$('<span></span>').text(o.label)
				.appendTo(span);
				
			self.menuInspectors['grid'] = {
				content : span,
				childs : []
			};
				
			$.each(o.grid.columns, function (index, column) {
				if (column.showFilter === undefined || column.showFilter) {
					showFilter = true;
				}
				columns.push({
					content : self._uiSettingsMenuCheckbox('columnsGrid', 'columnsGrid_' + index, column.visible, function() {
							o.grid.columns[index].visible = $(this).is(':checked');
							self.uiGrid.wijgrid('doRefresh');
							self.uiSettingsMenu.wijmenu('hideAllMenus');
						}, column.headerText)
					});
            });
			if (columns.length > 1) {
				self.menuInspectors['grid'].childs.push({
						content : $('<span></span>').text(o.texts.columns),
						childs : columns
					});
			}
			if (showFilter) {
				self.menuInspectors['grid'].childs.push({
						content : self._uiSettingsMenuCheckbox('showFilterGrid', 'showFilterGrid', self.showFilter, function() {
								self.showFilter = $(this).is(':checked');
								self.gridRefresh()
									.uiSettingsMenu.wijmenu('hideAllMenus');
							}, o.texts.showFiltersColumns)
					});
			}
			if (!showFilter && columns.length <= 1) {
				delete self.menuInspectors['grid'];
			}

			return self;
		},

		_inspectorsSettingsMenu : function() {
			var self = this,
				o = self.options,
				states = ['v', 'h', ''];

			$.each(o.inspectors, function() {
				var inspector = this,
					span = $('<span></span>'),
					childs = [];

				$('<span></span>').text(inspector.label)
					.appendTo(span);

				$.each(states, function(i, value) {
					var span = $('<span></span>'),
						label, checked;

					switch (value) {
						case 'v' :
							label = o.texts.vertical;
							checked = !inspector.hide && inspector.vertical ? 'checked' : '';
							break;
						case 'h' :
							label = o.texts.horizontal;
							checked = !inspector.hide && !inspector.vertical ? 'checked' : '';
							break;
						case '' :
							label = o.texts.hidden;
							checked = inspector.hide ? 'checked' : '';
							break;
					}

					$('<input type="radio" name="' + inspector.widget_id + '" id="radio' + inspector.widget_id + '_' + value + '" value="' + value + '" ' + checked + ' />')
						.click(function() {
							var input = $(this),
								widget_id = input.attr('name'),
								orientation = input.val(),
								widget = $('#' + widget_id);

							if (!orientation) {
								if (widget.length) {
									var menu = self.menuInspectors[inspector.widget_id];
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
								var target = orientation === 'v' ? self.uiInspectorsVertical : self.uiInspectorsHorizontal,
									refresh = self['_resizeInspectors' + orientation.toUpperCase()];
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
						})
						.appendTo(span);

					$('<label for="radio' + inspector.widget_id + '_' + value + '"></label>').text(label)
						.appendTo(span);

					childs.push({content : span});
				});


				self.menuInspectors[inspector.widget_id] = {
					content : span,
					childs : childs
				};
			});

			return self;
		},

		_addSettingsMenu : function(widget_id, key, item) {
			var self = this,
				o = self.options,
				inspector = self.menuInspectors[widget_id];

			if (inspector) {
				if (!inspector.childs.visibility) {
					self.menuInspectors[widget_id].childs = {
						visibility : {
							content : $('<span></span>').text(o.texts.visibility),
							childs : inspector.childs
						}
					};
				}
				self.menuInspectors[widget_id].childs[key] = item;
			}

			return self;
		},

		gridRefresh : function() {
			var self = this,
				o = self.options;

			self.uiGrid.wijgrid('destroy')
				.empty();
			self._uiGrid();

			return self;
		}
	});
	return $;
});

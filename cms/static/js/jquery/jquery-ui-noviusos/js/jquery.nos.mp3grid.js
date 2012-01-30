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
                save : 'Save',
                cancel: 'Cancel',
                mainView: 'Main view',
                item : 'item',
                items : 'items',
				showNbItems : 'Showing {{x}} items out of {{y}}',
				showOneItem : 'Show 1 item',
				showNoItem : 'No item',
				showAll : 'Show all items',
				views : 'Views',
				viewGrid : 'Grid',
				viewThumbnails : 'Thumbnails',
                loading : 'Loading...'
			},
			values: {},
            //callbabks
            columnVisibilityChange : null,
            slidersChange : null,
            splitters: {
                vertical: null,
                horizontal: null
            },
            views: {},
            selectedView: null,
            name: null
		},

		pageIndex : 0,
		menuSettings : {},
		showFilter : false,
		gridRendered : false,
		resizing : true,
		init : false,
		itemSelected : null,

		_create: function() {
			var self = this,
				o = self.options;

			self.element.addClass('nos-mp3grid');

			self.uiHeaderBar = $('<div></div>').addClass('nos-mp3grid-headerbar')
				.appendTo(self.element);

			self.uiAdds = $('<div></div>').addClass('nos-mp3grid-adds')
				.appendTo(self.uiHeaderBar);
			self.uiAddsButton = $('<button type="button"></button>').appendTo(self.uiAdds);
			self.uiAddsDropDown = $('<button type="button"></button>').text(o.texts.addDropDown)
				.appendTo(self.uiAdds);
			self.uiAddsMenu = $('<ul></ul>').appendTo(self.uiAdds);

			self.uiSettings = $('<div></div>').addClass('nos-mp3grid-settings')
				.appendTo(self.uiHeaderBar);
			self.uiSettingsDropDown = $('<select></select>').appendTo(self.uiSettings);
			//self.uiSettingsMenu = $('<ul></ul>').appendTo(self.uiSettings);

			self.uiSplitterVertical = $('<div></div>').addClass('nos-mp3grid-splitter-v')
				.appendTo(self.element);
			self.uiSplitterVerticalRight = $('<div></div>').appendTo(self.uiSplitterVertical);
			self.uiInspectorsVertical = $('<ul></ul>').addClass('nos-mp3grid-inspectors nos-mp3grid-inspectors-v')
				.appendTo(self.uiSplitterVerticalRight);
			self.uiSplitterVerticalLeft = $('<div></div>').appendTo(self.uiSplitterVertical);

			self.uiSplitterHorizontal = $('<div></div>').appendTo(self.uiSplitterVerticalLeft);
			self.uiSplitterHorizontalTop = $('<div></div>').appendTo(self.uiSplitterHorizontal);
			self.uiInspectorsHorizontal = $('<ul></ul>').addClass('nos-mp3grid-inspectors nos-mp3grid-inspectors-h')
				.appendTo(self.uiSplitterHorizontalTop);
			self.uiSplitterHorizontalBottom = $('<div></div>').appendTo(self.uiSplitterHorizontal);

			self.uiSearchBar = $('<div><form><div></div></form></div>')
                .addClass('nos-mp3grid-searchbar-container wijmo-wijgrid ui-widget ui-widget-header ui-state-default')
                .appendTo(self.uiSplitterHorizontalBottom)
                .find('form')
                .addClass('wijmo-wijgrid-headerrow wijmo-wijgrid-innercell')
                .find('div')
                .addClass('nos-mp3grid-searchbar wijmo-wijgrid-headertext');

			self.uiNbResult = $('<div></div>').addClass('nos-mp3grid-nbresult')
				.appendTo(self.uiSearchBar);
            self.uiInputContainer = $('<div></div>').addClass('nos-mp3grid-input-container ui-widget-content ui-corner-all')
                .appendTo(self.uiSearchBar);
            self.uiViewsButtons = $('<div></div>').addClass('nos-mp3grid-views-button')
                .appendTo(self.uiSearchBar);

            self.uiSearchIcon = $('<div></div>').addClass('nos-mp3grid-search-icon ui-icon ui-icon-search')
                .appendTo(self.uiInputContainer);
			self.uiSearchInput = $('<input type="search" name="search" placeholder="Search" value="" />')
                .addClass('nos-mp3grid-search-input ui-helper-reset')
                .appendTo(self.uiInputContainer);
			self.uiInspectorsTags = $('<div></div>').addClass('nos-mp3grid-inspectorstags')
				.appendTo(self.uiInputContainer);
			self.uiResetSearch = $('<a href="#"></a>').text(o.texts.showAll)
				.addClass('nos-mp3grid-reset-search')
				.appendTo(self.uiInputContainer);
            self.uiuiResetSearchIcon = $('<span></span>').text(o.texts.showAll)
                .addClass('ui-icon')
                .appendTo(self.uiResetSearch);

            self.uiGridTitle = $('<div></div>').addClass('nos-mp3grid-title')
                .appendTo(self.uiSearchBar);

            self.uiPaginationLabel = $('<span></span>').addClass('nos-mp3grid-pagination');

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

			self.menuSettings.grid = {
				content : o.label,
				childs : {}
			};

			self._css()
                ._uiAdds()
				._uiSplitters()
				._uiInspectors()
				._uiSearchBar()
				._uiList()
				._uiSettings();

			self.init = true;
		},

        _css : function() {
            var self = this,
                o = self.options;

            if (!$('style#inspectorsGrid').length) {
                var css = '';
                for (var u=0, numSheets = document.styleSheets.length; u<numSheets; u++) {
                    var sheet = document.styleSheets[u];
                    if (sheet.href && /wijmo/.test(sheet.href)) {
                        var rules = sheet.rules ? sheet.rules : sheet.cssRules;
                        for (var o=0, numRules = rules.length; o<numRules; o++) {
                            if (rules[o].selectorText === '.ui-widget-content') {
                                css += '.nos-mp3grid .nos-mp3grid-splitter-v .wijmo-wijsplitter-v-panel2 .wijmo-wijsplitter-h-panel1 .wijmo-wijgrid-alternatingrow {background:' + rules[o].style['background'] + ';}';
                                css += '.nos-mp3grid .nos-mp3grid-splitter-v .wijmo-wijsplitter-v-panel1 .wijmo-wijgrid-alternatingrow {background:' + rules[o].style['background'] + ';}';
                            }
                            if (rules[o].selectorText === '.wijmo-wijgrid tr.wijmo-wijgrid-row.ui-state-hover, .wijmo-wijgrid .wijmo-wijgrid-current-cell, .wijmo-wijgrid td.wijmo-wijgrid-rowheader.ui-state-active') {
                                css += '.nos-mp3grid .nos-mp3grid-splitter-v .wijmo-wijsplitter-v-panel2 .wijmo-wijsplitter-h-panel1 .wijmo-wijgrid-alternatingrow.ui-state-hover {background:' + rules[o].style['background'] + ';}';
                                css += '.nos-mp3grid .nos-mp3grid-splitter-v .wijmo-wijsplitter-v-panel1 .wijmo-wijgrid-alternatingrow.ui-state-hover {background:' + rules[o].style['background'] + ';}';
                            }
                        }
                    }
                }
                $('<style type="text/css" id="inspectorsGrid">' + css + '</style>').appendTo('head');
            }

            return self;
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
					$.nos.tabs.add({
                        iframe : true,
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
							$.nos.tabs.add({
                                iframe : true,
								url : this.url,
								label : this.label
							});
						}).appendTo(li);

				$('<span></span>').text(this.label)
					.appendTo(a);
			});
			self.uiAddsMenu.wijmenu({
					trigger : self.uiAddsDropDown,
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

			return self;
		},

		_uiSettings : function() {
			var self = this,
				o = self.options;

            for (var key in o.views) {
                self.uiSettingsDropDown.append(
                    $('<option></option>')
                        .attr({
                            'value': key,
                            'selected': (o.selectedView == key)
                        })
                        .append(o.views[key].name)
                );
            }
            self.uiSettingsDropDown.append(
                $('<option></option>')
                    .attr('value', 'edit_custom')
                    .append('Customize view')
            );

            self.uiSettingsDropDown.wijdropdown();
            /*
			self.uiSettingsButton.button({
				 label : o.texts.settings,
				 icons : {primary : 'ui-icon-gear'}
			});
            */

                        self.uiSettingsDropDown.change(function() {

                            if ($(this).val() == 'edit_custom') {
                                var $el = self._uiSettingsMenuPopup();
                                self.uiSettingsDialog = $.nos.dialog({title: 'Settings', contentUrl: null, content: $el, width: 500, height: 380});
                                            $el.wijtabs({
                                                alignment: 'left',
                                                scrollable: true,
                                                show: function(e, ui) {
                                                    $(ui.panel).find('.superpanel').wijsuperpanel({
                                                        autoRefresh: true,
                                                        hScroller: {
                                                            scrollMode: 'buttons'
                                                        }
                                                    });
                                        var $layout = $(ui.panel).find('#layout_settings');
                                                    $layout.find('.panels').sortable({
                                                            connectWith: ".panels",
                                                            update: function() {
                                                                self._uiSettingsMenuPopupRefreshLayout($layout);
                                                            },
                                                            change: function() {
                                                                self._uiSettingsMenuPopupRefreshLayout($layout);
                                                            },
                                                            start: function(event, ui) {
                                                                $(ui.item).addClass('moving');
                                                            },
                                                            stop: function(event, ui) {
                                                                $(ui.item).removeClass('moving');
                                                                self._uiSettingsMenuPopupRefreshLayout($layout);
                                                            },
                                                            placeholder: "droping"
                                                    });
                                                }
                                            });
                                            $el.after(
                                                $('<div></div>').css({
                                                    textAlign: 'right'
                                                }).append(
                                                    $('<button />').button({
                                                        label : o.texts.cancel,
                                                        icons : {primary : 'ui-icon-gear'}
                                                    }).click( function() {self.uiSettingsDialog.wijdialog('close');self.uiSettingsDialog.remove();} )
                                                ).append(
                                                    $('<button />').button({
                                                        label : o.texts.save,
                                                        icons : {primary : 'ui-icon-gear'}
                                                    }).click( function() {self._uiSettingsMenuPopupSave();self.uiSettingsDialog.wijdialog('close');self.uiSettingsDialog.remove();} )
                                                )
                                            );
                            } else {
                                $.nos.saveUserConfiguration(o.name, {selectedView: $(this).val()});
                                location.reload();
                            }
			});

			return self;
		},


		_uiSettingsMenuPopup : function() {
			var self = this,
				o = self.options;

			$el = $('<div><ul></ul></div>');
			self._uiSettingsMenuPopupAddMainViewTab($el);

			self._uiSettingsMenuPopupAddLayoutTab($el);

			self._uiSettingsMenuPopupAddInspectorsTab($el);


			return $el;
		},

		_uiSettingsMenuPopupAddMainViewTab: function($el) {
			var self = this,
				o = self.options;
			self._uiSettingsMenuPopupAddItem($el, o.texts.mainView, self._uiSettingsMenuPopupGetWidgetContentSettings('settings-main-view', o.texts.mainView, self.options));
		},

		_uiSettingsMenuPopupAddInspectorsTab: function($el) {
			var self = this,
				o = self.options;
			for (var i = 0; i < o.inspectors.length; i++) {
				self._uiSettingsMenuPopupAddItem($el, o.inspectors[i].label, self._uiSettingsMenuPopupGetWidgetContentSettings('settings-inspector-' + i, o.inspectors[i].label, o.inspectors[i]));
			}
		},

		_uiSettingsMenuPopupGetWidgetContentSettings: function(id, title, settings) {
			var self = this,
				o = self.options;

			var $contentSettings = $('<div class="content-settings"></div>')
									.attr({id: id});

			$contentSettings.append(
				$('<h1></h1>').append(
					title
				)
			);

			if (settings.grid) {
				$contentSettings.append(
					$('<h2></h2>').append(
						'Columns' // o.texts.columns ????
					)
				);

				var $columns = $('<ul class="widget-columns"></ul>');
				var columns = settings.grid.columns;

				for (var i = 0; i < columns.length; i++) {
					$columns.append(
						$('<li></li>')
							.data('column-id', i)
							.addClass(columns[i].visible !== false ? '' : 'invisible') //((typeof columns[i].visible == "undefined") ||
							.append(
								$('<div class="handle"></div>')
							)
							.append(
								$('<div class="title-zone"></div>')
									.append (
										columns[i].headerText
									)
							)
							.append(
								$('<div class="visibility-zone"></div>')
									.append (
										$('<input type="checkbox" />')
											.attr({checked: columns[i].visible !== false})
											.change(
												function() {
													var $column = $(this).closest('li');
													if ($(this).is(':checked')) {
														$column.removeClass('invisible');
													} else {
														$column.addClass('invisible');
													}
												}
											)
										)
									)
							);
				}

				$columns.sortable({
					handle: '.handle',
					placeholder: 'placeholder'
				});

				$contentSettings.append($columns);

			}

			return $contentSettings;
		},

		_uiSettingsMenuPopupAddLayoutTab : function($el) {
			var self = this,
		o = self.options;

			var $layout = $('<form id="layout_settings"></form>');

			$layout.append(
				$('<h1></h1>').append(
					'Layout'
				)
			);

			$layout.append(
				$('<div class="layout"></div>')
					.append(
						$('<ul class="left-panel panels"></ul>')
					)
					.append(
						$('<div class="right-side"></div>')
						.append(
							$('<ul class="top-panel panels"></ul>')
						)
						.append(
							$('<div class="content"></div>')
						)
					)
			);

			var $notLayout = $('<div class="not-layout superpanel"></div>')
					.append(
						'<ul class="invisible-panel panels"></ul>'
					);

            self._uiSettingsMenuPopupAddItem($el, "Layout", $layout);

            var $leftPanel = $layout.find('.left-panel');
            var $topPanel = $layout.find('.top-panel');
            var $invisiblePanel = $notLayout.find('.invisible-panel');

            $layout.find('.not-layout').wijsuperpanel({
                hScroller: {
                    scrollMode: 'buttons'
                }
            });

            for (var i = 0; i < o.inspectors.length; i++) {
                var visible = !o.inspectors[i].hide;
                var vertical = o.inspectors[i].vertical;
                var $inspectorEl = $('<li class="layout-inspector"></li>')
                                .data('inspector-id', i)
                                .append(
                                    $('<div></div>')
                                        .append(o.inspectors[i].label)
                                );
                if (visible) {
                    if (vertical) {
                        $leftPanel.append(
                            $inspectorEl
                        );
                    } else {
                        $topPanel.append(
                            $inspectorEl
                        );
                    }
                } else {
                    $invisiblePanel.append(
                        $inspectorEl
                    );
                }
            }

			$layout.append($notLayout);

			self._uiSettingsMenuPopupRefreshLayout($layout);
		},

		_uiSettingsMenuPopupRefreshLayout : function($layout) {
			var $leftPanel = $layout.find('.left-panel');
			var $topPanel = $layout.find('.top-panel');
			var $invisiblePanel = $layout.find('.invisible-panel');

			var $leftLis = $leftPanel.find('li').not('.moving');
			$leftLis.css({
				height: (200 - $leftLis.length) / $leftLis.length,
				width: "inherit"
			});
			$leftLis.removeClass('last');
			$($leftLis[$leftLis.length - 1]).addClass('last');


			var $topLis = $topPanel.find('li').not('.moving');

			$topLis.css({
				width: (200 - $topLis.length) / $topLis.length,
				height: "inherit"
			});
			$topLis.removeClass('last');
			$($topLis[$topLis.length - 1]).addClass('last');


			var $invisibleLis = $invisiblePanel.find('li').not('.moving');

			$invisibleLis.css({
				width: '',
				height: ''
			});
			$invisibleLis.removeClass('last');
			$($invisibleLis[$invisibleLis.length - 1]).addClass('last');
			$invisiblePanel.css({
				width: Math.max(($invisibleLis.length + 1) * ($invisibleLis.width() + 1), 100)
			});
		},

		_uiSettingsMenuPopupSave : function() {
			var self = this,
		        o = self.options;

			for (var j = 0; j < o.inspectors.length; j++) {
				if (o.inspectors[j].grid) {
					var gridColumns = o.inspectors[j].grid.columns;
					var newColumns = [];
                    self.uiSettingsDialog.find('#settings-inspector-' + j + ' .widget-columns > li').each(function(i, el) {
						var $this = $(this);
						var newColumn = gridColumns[$this.data('column-id')];

						newColumn.dataIndex = i;
						newColumn.leavesIdx = i;
						newColumn.linearIdx = i;
						newColumn.thX = i;
						newColumn.travIdx = i;
						newColumn.visLeavesIdx = i;
						newColumn.visible = !$this.hasClass('invisible');
						newColumns.push(newColumn);
					});
					o.inspectors[j].grid.columns = newColumns;
				}
			}

			var newInspectors = [];
			var layoutSettings = self.uiSettingsDialog.find('#layout_settings');
			layoutSettings.find('.layout-inspector').each(function() {
				var newInspector = self.options.inspectors[$(this).data('inspector-id')];
				var $panel = $(this).closest('.panels');
				newInspector.hide = $panel.hasClass('invisible-panel');
				newInspector.vertical = $panel.hasClass('left-panel');
				newInspectors.push(newInspector);
			});
			self.options.inspectors = newInspectors;

			newColumns = [];


            self.uiSettingsDialog.find('#settings-main-view .widget-columns > li').each(function(i, el) {
			    var $this = $(this),
			        newColumn = o.grid.columns[$this.data('column-id')];

			    newColumn.visible = !$this.hasClass('invisible');
			    newColumns.push(newColumn);
			});

			self.options.grid.columns = newColumns;

            self.element.find('.nos-mp3grid-inspector').remove();
			self._uiInspectors()
                ._uiList();
		},

		_uiSettingsMenuPopupAddItem : function(element, itemName, content) {
			if ( typeof this.idMenu == 'undefined' ) this.idMenu = 0;
			element.find('> ul').append('<li><a href="#settings_menu_popup_item_' + this.idMenu + '">' + itemName + '</a></li>');
			element.append($('<div id="settings_menu_popup_item_' + this.idMenu + '"></div>').append(content));
			this.idMenu++;
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

				var span2 = $('<span></span>').appendTo(span);

				$('<input type="' + (args.radio ? 'radio' : 'checkbox') + '" name="' + args.name + '" id="' + args.id + '" value="' + args.value + '" ' + (checked ? 'checked' : '') + ' />')
					.click(function(e) {
						args.click.call(this, e);
					})
					.appendTo(span2);

				$('<label for="' + args.id + '"></label>').text(args.label)
					.appendTo(span2);
			} else {
				$('<span></span>').text(args.label)
					.appendTo(span);
			}

			return span;
		},

		_gridSettingsMenu : function() {
			var self = this,
				o = self.options,
				nbColumn = 0,
				keys = ['columns', 'showFilters'];

			$.each(keys, function(i, key) {
				if (self.menuSettings.grid.childs[key]) {
					delete self.menuSettings.grid.childs[key];
				}
			})

			if (o.defaultView !== 'thumbnails') {
				var columns = {},
					showFilter = self.showFilter;

			    o.grid.columns = self.uiGrid.nosgrid("option", "columns");

				$.each(o.grid.columns, function (index, column) {
					if (column.showFilter === undefined || column.showFilter) {
						showFilter = true;
					}
					nbColumn++;
					columns[index] = {
							content : {
								name : 'columnsGrid',
								id : 'columnsGrid_' + index,
								checked : column.visible,
								click : function() {
									o.grid.columns[index].visible = $(this).is(':checked');
									self.uiGrid.nosgrid('doRefresh');
									self.uiSettingsMenu.wijmenu('hideAllMenus');
                                    self._trigger('columnVisibilityChange', null, {index : index, column : o.grid.columns[index]});
                                },
								label : column.headerText
							}
						};
	            });
				if (nbColumn > 1) {
					self.menuSettings.grid.childs.columns = {
							content : o.texts.columns,
							childs : columns
						};
				}
				if (showFilter) {
					self.menuSettings.grid.childs.showFilters = {
							content : {
								name : 'showFilterGrid',
								id : 'showFilterGrid',
								checked : self.showFilter,
								click : function() {
									self.showFilter = $(this).is(':checked');
									self._resizeList(true)
										.uiSettingsMenu.wijmenu('hideAllMenus');
								},
								label : o.texts.showFiltersColumns
							}
						};
				}
			}

			self._refreshSettingsMenu();

			return self;
		},

		_inspectorsSettingsMenu : function() {
			var self = this,
				o = self.options,
				states = ['v', 'h', ''],
				inspectors = o.inspectors;

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
										self._resizeInspectorsV(true)
											._resizeInspectorsH(true);
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
												.css({width: '100%', height: 'auto'})
												.appendTo(target);
											self._resizeInspectorsV(true)
												._resizeInspectorsH(true);
										}
									} else {
										var $li = $('<li></li>').addClass('ui-widget-content')
											.data('inspectorurl', inspector.url)
											.appendTo(target);
										self['_resizeInspectors' + orientation.toUpperCase()](true)
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
					self._resizeInspectorsV(true)
						._resizeInspectorsH(true)
						._resizeList(true);
				},
				refreshH = function() {
					self._resizeInspectorsH(true)
						._resizeList(true);
				},
                verticalSplitter = $.extend(true, {
                        orientation: "vertical",
                        splitterDistance: 200,
                        showExpander: false,
                        fullSplit: false,
                        panel1 : {
                            minSize : 150,
                            scrollBars : 'none'
                        },
                        panel2 : {
                            minSize : 200,
                            scrollBars : 'none'
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
                    }, self.options.splitters.vertical),
                horizontalSplitter = $.extend(true, {
                        orientation: "horizontal",
                        fullSplit: true,
                        splitterDistance: 200,
                        showExpander: false,
                        panel1 : {
                            minSize : 200,
                            scrollBars : 'none'
                        },
                        panel2 : {
                            minSize : 200,
                            scrollBars : 'none'
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
                    }, self.options.splitters.horizontal);

			self.uiSplitterVertical.wijsplitter(verticalSplitter)
				.find('.ui-resizable-handle')
				.mousedown(function() {
				    self.resizing = false;
				});

			self.uiSplitterHorizontal.wijsplitter(horizontalSplitter)
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
					$('<li></li>').addClass('nos-mp3grid-inspector ui-widget-content')
                        .data('inspector', this)
						.appendTo( this.vertical ? self.uiInspectorsVertical : self.uiInspectorsHorizontal );
				}
			});

			self._resizeInspectorsV(true)
				._resizeInspectorsH(true);

			self.uiInspectorsVertical.find('> li')
				.add(self.uiInspectorsHorizontal.find('> li'))
				.each(function() {
					self._loadInspector($(this));
				});

			self.uiInspectorsVertical.add(self.uiInspectorsHorizontal).sortable({
					connectWith: ".nos-mp3grid-inspectors",
					start : function() {
						self.resizing = false;
					},
					stop: function() {
						self.resizing = true;
						self._resizeInspectorsV(true)
							._resizeInspectorsH(true);
					}
				});

			return self;
		},

		_loadInspector : function($li) {
			var self = this,
				inspector = $li.data('inspector');

            inspector.selectionChanged = function(value, label) {
                    var multiple = false,
                        name = inspector.inputName;

                    if (inspector.inputName.substr(-2, 2) === '[]') {
                        name = inspector.inputName.substr(0, inspector.inputName.length - 2);
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
                    self.uiInspectorsTags.wijsuperpanel('destroy');

                    var span = $('<span></span>').addClass('nos-mp3grid-inspectorstag ui-state-default ui-corner-all ' + name)
                        .text(label)
                        .appendTo(self.uiInspectorsTags);

                    $('<input type="hidden" name="' + inspector.inputName + '" />').val(value)
                        .appendTo(span);

                    $('<a href="#"></a>').addClass('ui-icon ui-icon-close')
                        .click(function(e) {
                            e.preventDefault();
                            $(this).parent().remove();
                            self.gridRefresh();
                        })
                        .appendTo(span);

                    self.uiInspectorsTags.wijsuperpanel({
                        showRounder: false,
                        hScroller: {
                            scrollMode: 'buttons'
                        }
                    });

                    self.gridRefresh();
                };

            $li.data('inspector', inspector);

			if ($.isFunction(inspector.url)) {
                inspector.url.call(self, $li);
			} else {
				$.ajax({
					url: inspector.url,
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
			}

			return self;
		},

		_uiSearchBar : function() {
			var self = this,
                o = self.options;

            self.uiResetSearch.hide();

			self.uiSearchInput.on('keypress', function( event ) {
					var keyCode = $.ui.keyCode;

					self.pageIndex = 0;

					if ( self.timeoutSearchInput ) {
						clearTimeout(self.timeoutSearchInput);
					}

					if ($.inArray(event.keyCode, [keyCode.ENTER, keyCode.NUMPAD_ENTER]) != -1) {
						self.gridRefresh();
						return false;
					}

					self.timeoutSearchInput = setTimeout(function() {
                        self.gridRefresh();
                    }, 500);
				});

			self.uiResetSearch.click(function(e) {
					e.preventDefault();
					self.uiSearchInput.val('');
                    self.uiInspectorsTags.wijsuperpanel('destroy');
					self.uiInspectorsTags.empty();
					self.gridRefresh();
				});

            self.uiInspectorsTags.height(self.uiInputContainer.height())
                .width(parseInt(self.uiSearchBar.width() * 0.3))
                .wijsuperpanel({
                    showRounder: false,
                    hScroller: {
                        scrollMode: 'buttons'
                    }
                });

            if (o.grid) {
                $('<label for="view_grid"></label>')
                    .text(o.texts.viewGrid)
                    .appendTo(self.uiViewsButtons);
                $('<input type="radio" id="view_grid" name="view" checked="' + (o.defaultView === 'grid' ? 'checked="checked"' : '') + '" />')
                    .appendTo(self.uiViewsButtons)
                    .button({
                        text : false,
                        label: o.texts.viewGrid,
                        icons : {
                            primary: 'ui-icon view-list',
                            secondary: null
                        }
                    })
                    .click(function() {
                        if (o.defaultView !== 'grid') {
                            self.uiViewsButtons
                                .find('button')
                                .removeClass('ui-state-active');
                            $(this).addClass('ui-state-active');
                            o.defaultView = 'grid';
                            self._uiList();
                        }
                    });
            }
            if (o.thumbnails) {
                var sizes = [32, 64];
                $.each(sizes, function(i, size) {
                    $('<label for="view_thumbnails_' + size + '"></label>')
                        .text(o.texts.viewThumbnails + ' ' + size + 'px')
                        .appendTo(self.uiViewsButtons);
                    $('<input type="radio" id="view_thumbnails_' + size + '" name="view" ' + (o.defaultView === 'thumbnails' && o.thumbnails.thumbnailSize === size ? 'checked="checked"' : '') + ' />')
                        .appendTo(self.uiViewsButtons)
                        .button({
                            text : false,
                            label: o.texts.viewThumbnails + ' ' + size + 'px',
                            icons : {
                                primary: 'ui-icon ' + (size === 32 ? 'view-thumbs-small' : 'view-thumbs-big'),
                                secondary: null
                            }
                        })
                        .click(function() {
                            if (o.defaultView !== 'thumbnails' || o.thumbnails.thumbnailSize !== size) {
                                self.uiViewsButtons
                                    .find('button')
                                    .removeClass('ui-state-active');
                                $(this).addClass('ui-state-active');
                                o.defaultView = 'thumbnails';
                                o.thumbnails.thumbnailSize = size;
                                self._uiList();
                            }
                        });
                })
            }
            if (self.uiViewsButtons.find('input').length > 1) {
                self.uiViewsButtons.buttonset();
            } else {
                self.uiViewsButtons.hide();
            }

			return self;
		},

		_uiList : function() {
			var self = this,
				o = self.options;

			self.gridRendered = false;
            self.uiGridTitle.text(o.texts.item);

			self.uiThumbnail.thumbnails('destroy')
				.empty()
				.hide();
			self.uiGrid.nosgrid('destroy')
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
                positionContainer = self.element.offset(),
				height = self.element.height() - position.top + positionContainer.top,
				heights = $.nos.grid.getHeights();

			self.uiGrid.css({
                    height : height,
                    width : '100%'
                })
				.nosgrid($.extend({
					columnsAutogenerationMode : 'none',
					selectionMode: 'singleRow',
					showFilter: self.showFilter,
					allowSorting: true,
					scrollMode : 'auto',
					allowPaging : true,
					pageIndex : self.pageIndex,
					pageSize: Math.floor((height - heights.footer - heights.header - (self.showFilter ? heights.filter : 0)) / heights.row),
					allowColSizing : true,
					allowColMoving : true,
					data: new wijdatasource({
						dynamic: true,
						proxy: new wijhttpproxy({
							url: o.grid.proxyUrl,
							dataType: "json",
							error: function(jqXHR, textStatus, errorThrown) {
								log(jqXHR, textStatus, errorThrown);
							},
							data: {}
						}),
						loading: function (dataSource, userData) {
							var r = userData.data.paging;
							self.pageIndex = r.pageIndex;
							if (self.gridRendered) {
								self.uiGrid.nosgrid("currentCell", -1, -1);
							}
							dataSource.proxy.options.data.inspectors = self._jsonInspectors();
							dataSource.proxy.options.data.offset = r.pageIndex * r.pageSize;
							dataSource.proxy.options.data.limit = r.pageSize;
						},
						loaded: function(dataSource, data) {
							if (dataSource.data.totalRows === 0) {
								self.uiPaginationLabel.text(o.texts.showNoItem);
                                self.uiNbResult.text(o.texts.showNoItem);
							} else if (dataSource.data.totalRows === 0) {
								self.uiPaginationLabel.text(o.texts.showOneItem);
                                self.uiNbResult.text('1 ' + o.texts.item);
							} else {
								self.uiPaginationLabel.text(o.texts.showNbItems.replace('{{x}}', dataSource.data.length).replace('{{y}}', dataSource.data.totalRows));
                                self.uiNbResult.text(dataSource.data.totalRows + ' ' + o.texts.items);
							}
                            self.uiNbResult.show();

							self.uiResetSearch[self.uiInspectorsTags.find('.nos-mp3grid-inspectorstag').length || self.uiSearchInput.val() ? 'show' : 'hide']();
						},
						reader: {
							read: function (dataSource) {
								var count = parseInt(dataSource.data.total, 10);
								dataSource.data = dataSource.data.items;
								dataSource.data.totalRows = count;
							}
						}
					}),
					pageIndexChanging: function() {
                        self.element.trigger('selectionChanged.mp3grid', false);
					},
					cellStyleFormatter: function(args) {
						if (args.$cell.is('th')) {
			                args.$cell.removeClass("ui-state-active");
					    }
				        if (args.state & $.wijmo.wijgrid.renderState.selected && args.$cell.hasClass('ui-state-default')) {
				            args.$cell.removeClass("ui-state-highlight");
				        }
						if (args.state & $.wijmo.wijgrid.renderState.selected) {
			                args.$cell.removeClass("wijmo-wijgrid-current-cell");
					    }
				    },
					currentCellChanging : function () {
						return self.gridRendered;
					},
					currentCellChanged: function (e) {
						if (e) {
							var row = $(e.target).nosgrid("currentCell").row(),
								data = row ? row.data : false;

							if (data) {
								self.itemSelected = row.dataRowIndex;
                                self.element.trigger('selectionChanged.mp3grid', data);
							}
						}
						return true;
					},
					rendering : function() {
						self.gridRendered = false;
					},
					rendered : function() {
						self.gridRendered = true;
						self.uiGrid.css('height', 'auto');
						if (self.itemSelected !== null) {
							var sel = self.uiGrid.nosgrid("selection");
							sel.clear();
							sel.addRows(self.itemSelected);
						}
					},
                    dataLoading: function(e) {
                        self.uiPaginationLabel.detach();
                    },
                    loaded: function() {
                        self.uiSplitterHorizontalBottom.find('.wijmo-wijgrid-footer').prepend(self.uiPaginationLabel);
                    }
				}, o.grid));

			return self;
		},

		_uiThumbnail : function() {
			var self = this,
				o = self.options,
				position = self.uiThumbnail.offset(),
                positionContainer = self.element.offset(),
				height = self.element.height() - position.top + positionContainer.top,
				heights = $.nos.grid.getHeights();

			self.uiThumbnail.css('height', height)
				.thumbnails($.extend({
					pageIndex: 0,
					url: o.grid.proxyUrl,
					loading: function (dataSource, userData) {
						var r = userData.data.paging;
						self.pageIndex = r.pageIndex;
						dataSource.proxy.options.data.inspectors = self._jsonInspectors();
						dataSource.proxy.options.data.offset = r.pageIndex * r.pageSize;
						dataSource.proxy.options.data.limit = r.pageSize;
					},
					loaded: function(dataSource, data) {
                        if (dataSource.data.totalRows === 0) {
                            self.uiPaginationLabel.text(o.texts.showNoItem);
                            self.uiNbResult.text(o.texts.showNoItem);
                        } else if (dataSource.data.totalRows === 0) {
                            self.uiPaginationLabel.text(o.texts.showOneItem);
                            self.uiNbResult.text('1 ' + o.texts.item);
                        } else {
                            self.uiPaginationLabel.text(o.texts.showNbItems.replace('{{x}}', dataSource.data.length).replace('{{y}}', dataSource.data.totalRows));
                            self.uiNbResult.text(dataSource.data.totalRows + ' ' + o.texts.items);
                        }
                        self.uiNbResult.show();

						self.uiResetSearch[self.uiInspectorsTags.find('.nos-mp3grid-inspectorstag').length || self.uiSearchInput.val() ? 'show' : 'hide']();
					},
					rendered : function() {
                        self.uiSplitterHorizontalBottom.find('.wijmo-wijpager').prepend(self.uiPaginationLabel);

						if (self.itemSelected !== null) {
							if (!self.uiThumbnail.thumbnails('select', self.itemSelected)) {
								self.itemSelected = null;
							}
						}
					},
					reader: {
						read: function (dataSource) {
							var count = parseInt(dataSource.data.total, 10);
							dataSource.data = dataSource.data.items;
							dataSource.data.totalRows = count;
						}
					},
					pageIndexChanging: function() {
						self.itemSelected = null;
                        self.element.trigger('selectionChanged.mp3grid', false);
					},
					selectionChanged : function(e, data) {
						if (!data || $.isEmptyObject(data)) {
							self.itemSelected = null;
                            self.element.trigger('selectionChanged.mp3grid', false);
						} else {
							self.itemSelected = data.item.index;
                            self.element.trigger('selectionChanged.mp3grid', data.item.data.noParseData);
						}
					}
				}, o.thumbnails));

			return self;
		},

		_jsonInspectors : function() {
			var self = this,
				inspectors = this.options.values || {};

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

		_resizeInspectorsV : function(refresh) {
			var self = this;

		    if (self.resizing) {
				var inspectors = self.uiInspectorsVertical.find('.wijmo-wijsplitter-v-bar')
                    .css({
                        width : null,
                        borderRightWidth : null
                    })
                    .end()
                    .find('> li').css({
						width: '100%',
						height: 'auto'
					});

				if (inspectors.length) {
					inspectors.css('height', ( self.uiInspectorsVertical.height() / inspectors.length )  + 'px')
						.trigger('inspectorResize', {refresh : refresh || false});
				} else {
					self._hideSplitterV();
				}
			}

			return self;
		},

		_resizeInspectorsH : function(refresh) {
			var self = this;

		    if (self.resizing) {
				var inspectors = self.uiInspectorsHorizontal.find('.wijmo-wijsplitter-h-bar')
                    .css({
                        height : null,
                        borderTopWidth : null
                    })
                    .end()
                    .find('> li').css({
						width: 'auto',
						height: '100%'
					});

				if (inspectors.length) {
					inspectors.css('width', ( self.uiInspectorsHorizontal.width() / inspectors.length )  + 'px')
						.trigger('inspectorResize', {refresh : refresh || false});
				} else {
					self._hideSplitterH();
				}
			}

            this.uiSplitterVertical.wijsplitter('option', 'splitterDistance');

            self._trigger('slidersChange', null, this.slidersSettings());

			return self;
		},

        _resizeList : function(refresh) {
            var self = this,
                o = self.options;

            if (self.init) {
                var height = self.uiSplitterHorizontalBottom.height() - self.uiSearchBar.outerHeight(true);
                if (o.defaultView === 'thumbnails') {
                    if (refresh) {
                        self._uiList();
                    } else {
                        self.uiThumbnail.thumbnails('setSize', self.uiSplitterHorizontalBottom.width(), height);
                    }
                } else {
                    self.uiGrid.nosgrid('setSize', null, height);
                    if (refresh) {
                        var heights = $.nos.grid.getHeights();
                        self.uiGrid.nosgrid('option', 'pageSize', Math.floor((height - heights.footer - heights.header - (self.showFilter ? heights.filter : 0)) / heights.row));
                    }
                }
            }

            return self;
        },

		_hideSplitterV : function() {
			var self = this;

            self.uiSplitterVertical.find('.wijmo-wijsplitter-v-bar')
                .css({
                    width : '0px',
                    borderRightWidth : '0px'
                })
                .end()
                .wijsplitter('option', 'panel1', {collapsed : true})
                .wijsplitter('refresh', true, false);

			return self;
		},

		_hideSplitterH : function() {
			var self = this;

			self.uiSplitterHorizontal.find('.wijmo-wijsplitter-h-bar')
                .css({
                    height : '0px',
                    borderTopWidth : '0px'
                })
                .end()
                .wijsplitter('option', 'panel1', {collapsed : true})
                .wijsplitter('refresh', true, false);

			return self;
		},

		_refreshSettingsMenu : function() {
			var self = this;
			// @todo implement
			return self;
		},

		gridRefresh : function() {
			var self = this,
				o = self.options;

			if (self.init) {
                if (o.defaultView === 'thumbnails') {
                    self._uiList();
                } else {
                    self.uiGrid.nosgrid("ensureControl", true);
                }
			}

			return self;
		},

        refresh : function() {
            var self = this,
                o = self.options;

            self.uiSplitterVertical.wijsplitter('refresh', true, false);
            self.uiSplitterHorizontal.wijsplitter('refresh', true, false);

            self._resizeInspectorsV()
                ._resizeInspectorsH()
                ._resizeList();

            return self;
        },

        slidersSettings : function() {
            return {
                vertical: {
                    splitterDistance: this.uiSplitterVertical.wijsplitter('option', 'splitterDistance') / $(window).width()
                },
                horizontal: {
                    splitterDistance: this.uiSplitterHorizontal.wijsplitter('option', 'splitterDistance') / $(window).height()
                }
            }
        }


	});
	return $;
});

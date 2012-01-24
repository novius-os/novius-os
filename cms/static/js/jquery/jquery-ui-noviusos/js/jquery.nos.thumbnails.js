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
	$.widget( "nos.thumbnails", {
		options: {
			thumbnailSize : 64,
			pageIndex: 0,
			pageSize: null,
			url : '',
            texts : {
                loading : 'Loading...'
            },
            actions : [],
			loading : null,
			loaded : null,
			reader : null,
			dataParser : null,
			thumbFormatter : null
		},

		_create: function() {
			var self = this;

			self.uiPager = $('<div></div>').addClass('wijmo-wijsuperpanel-footer  ui-state-default')
				.appendTo(self.element);
		},


		destroy: function () {
			var self = this,
				o = self.options;

			self.uiPager.wijpager('destroy');
			self.uiPager.remove();

			self.element.removeClass('nos-thumbnails nos-thumbnails-size-' + o.thumbnailSize)
				.wijsuperpanel('destroy');

			$.Widget.prototype.destroy.apply(this, arguments);
		},

		_init: function() {
			var self = this,
				o = self.options;

			if ($.inArray(o.thumbnailSize, [32, 64]) === -1) {
				o.thumbnailSize = 64;
			}

			self.uiPager.wijpager({
				pageIndexChanging : function(sender, args) {
					self._trigger('pageIndexChanging', null, args);
					o.pageIndex = args.newPageIndex;
					self._load();
				},
				pageIndexChanged : function(sender, args) {
					self._trigger('pageIndexChanged');
					o.pageIndex = args.newPageIndex;
					self._load();
				}
			});
			self.element
				.addClass('nos-thumbnails nos-thumbnails-size-' + o.thumbnailSize)
				.wijsuperpanel({
					showRounder : false
				});
			self.uiContainer = self.element.find('.wijmo-wijsuperpanel-contentwrapper');

            self.uiOverlay = $('<div></div>')
                .addClass('nos-thumbnails-overlay ui-widget-overlay')
                .appendTo(self.element);
            self.uiOverlayText = $('<span><span></span>' + o.texts.loading + '</span>')
                .addClass('nos-thumbnails-loadingtext ui-widget-content ui-corner-all')
                .find('span')
                .addClass('ui-icon ui-icon-clock')
                .end()
                .appendTo(self.element);

            self.uiOverlayText.css({
                marginLeft : (self.uiOverlayText.width() * -1 / 2) + 'px',
                marginTop : (self.uiOverlayText.height() * -1 / 2) + 'px'
            });

			if (o.pageSize === null) {
				self._displayItem({
					title : 'Test'
				});

				var el = self.uiContainer.find('.nos-thumbnails-thumb');
				self.itemDimension = {
					width : el.outerWidth(true),
					height : el.outerHeight(true)
				};
				self.uiContainer.empty();

				o.pageSize = Math.floor(self.uiContainer.width() / self.itemDimension.width) * Math.max(1, Math.floor(self.uiContainer.height() / self.itemDimension.height));
			}

			self.dataSource = new wijdatasource({
				dynamic: true,
				proxy: new wijhttpproxy({
					url: o.url,
					dataType: "json",
					error: function(jqXHR, textStatus, errorThrown) {
						// Session lost, can't login
						if (jqXHR.status == 403) {

							var notify = {
								title: "You've been inactive for too long",
								text: "Please log-in again.",
								type: 'error'
							}

							try {
								var json = $.parseJSON(jqXHR.responseText);
								if (json.login_page) {
									notify.text = notify.text.replace('log-in again', "<a href=\"" + json.login_page + "\">log-in again</a>");
								}
							} catch (e) {}
							$.nos.notify(notify);
						}
						self.uiOverlay.hide();
						log(jqXHR, textStatus, errorThrown);
					},
					data: {}
				}),
				loading: function(dataSource, userData) {
					dataSource.proxy.options.data = $.extend(dataSource.proxy.options.data, userData.data);

                    self.uiOverlay.add(self.uiOverlayText)
                        .show();

					if ($.isFunction(o.loading)) {
						o.loading(dataSource, userData)
					}
				},
				loaded: function(dataSource, data) {
					if ($.isFunction(o.loaded)) {
						o.loaded(dataSource, data)
					}
					self.uiPager.wijpager('option', {
						pageCount : Math.ceil(dataSource.data.totalRows / data.data.paging.pageSize),
						pageIndex : data.data.paging.pageIndex
					});
					self._display(dataSource.data);

                    self.uiOverlay.add(self.uiOverlayText)
                        .hide();
				},
				reader: o.reader
			});

			self._load();
		},

		_load : function() {
			var self = this,
				o = self.options;

			self.dataSource.load({
					data: {
						paging: {
							pageIndex: o.pageIndex,
							pageSize: o.pageSize
						}
					},
					afterRefresh: null,
					beforeRefresh: null
				});

			return self;
		},

		_display : function(items) {
			var self = this,
				o = self.options,

			    parent = self.uiContainer.empty()
                    .parent();

            self.uiContainer.detach();
			$.each(items, function(i) {
				var data = this;

				if ($.isFunction(o.dataParser)) {
					data = {
						item : o.dataParser(o.thumbnailSize, data, i),
						noParseData : data
					};
				} else {
					data = {
						item : data,
						noParseData : data
					};
				}
				self._displayItem(data, i);
			});
            self.uiContainer.appendTo(parent);

			self._trigger('rendered');

			return self;
		},

		_displayItem : function(data, index) {
			var self = this,
				o = self.options,
				item = data.item,
                noParseData = data.noParseData;

			item = $.extend({
				title : '',
				thumbnail : null,
				thumbnailAlternate : null
			}, item);

			var container = $('<div></div>')
				.addClass('nos-thumbnails-thumb wijmo-wijgrid ui-widget-content')
				.data('thumbnail', {
					data : data,
					index : index
				})
				.appendTo(self.uiContainer),

				td = $('<table cellspacing="0" cellpadding="0" border="0"><tbody><tr><td></td></tr></tbody></table>')
					.addClass('nos-thumbnails-thumb-grid wijmo-wijgrid-root wijmo-wijgrid-table')
					.attr('title', item.title)
					.css({
						borderCollapse : 'separate',
						'-moz-user-select' : '-moz-none'
					})
					.hover(
						function() {
							td.parent().addClass('ui-state-hover');
						},
						function() {
							td.parent().removeClass('ui-state-hover');
						}
					)
					.click(function() {
						self.select(index);
					})
					.appendTo(container)
					.find('tbody')
					.addClass('ui-widget-content wijmo-wijgrid-data')
					.find('tr')
					.addClass('wijmo-wijgrid-row ui-widget-content wijmo-wijgrid-datarow')
					.find('td')
					.addClass('wijgridtd'),

				imgContainer = $('<div></div>')
					.addClass('nos-thumbnails-thumb-container-img wijmo-wijgrid-innercell')
					.appendTo(td),

				title = $('<div></div>')
					.addClass('nos-thumbnails-thumb-title wijmo-wijgrid-innercell')
					.text(item.title)
					.appendTo(td);


			self._itemThumbnail(imgContainer, item, index);

			if (o.actions.length > 0) {
				var tr = $('<table cellspacing="0" cellpadding="0" border="0"><tbody><tr></tr></tbody></table>')
						.addClass('nos-thumbnails-thumb-grid wijmo-wijgrid-root wijmo-wijgrid-table')
						.css({
							borderCollapse : 'separate',
							'-moz-user-select' : '-moz-none'
						})
						.appendTo(container)
						.find('tbody')
						.addClass('ui-widget-content wijmo-wijgrid-data')
						.find('tr')
						.addClass('wijmo-wijgrid-row ui-widget-content wijmo-wijgrid-datarow'),

					action = $('<th><div></div></th>')
						.addClass('nos-thumbnails-thumb-action wijgridtd ui-state-default')
						.hover(
							function() {
								action.parent().addClass('ui-state-hover');
							},
							function() {
								action.parent().removeClass('ui-state-hover');
							}
						)
						.click(function(e) {
							e.preventDefault();
							o.actions[0].action(noParseData);
						})
						.appendTo(tr)
						.find('div')
						.text(o.actions[0].label)
						.addClass('wijmo-wijgrid-innercell');

				if (o.actions.length > 1) {
					var dropDown = $('<th><div></div></th>')
						.css('width', '1px')
						.addClass('nos-thumbnails-thumb-dropdown wijgridtd ui-state-default')
						.hover(
							function() {
								dropDown.parent().addClass('ui-state-hover');
							},
							function() {
								dropDown.parent().removeClass('ui-state-hover');
							}
						)
						.appendTo(tr)
						.find('div')
						.addClass('wijmo-wijgrid-innercell');

					$('<span></span>')
						.addClass('ui-icon ui-icon-triangle-1-s')
						.appendTo(dropDown);

					var ul = $('<ul></ul>').appendTo(self.element);
					$.each(o.actions, function() {
						var action = this;
						$('<li><a href="#"></a></li>')
							.appendTo(ul)
							.find('a')
							.text(action.label)
							.click(function(e) {
								e.preventDefault();
								action.action(noParseData);
							})
					});
					ul.wijmenu({
						trigger : dropDown,
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
				}
			}

			if ($.isFunction(o.thumbFormatter)) {
				o.thumbFormatter({
					'$container' : container,
					item : {
						data : item,
						index : index
					}
				});
			}

			return self;
		},

		_itemThumbnail : function(container, item, index) {
			var self = this
				o = self.options,
				thumbnail = item.thumbnail || item.thumbnailAlternate;

			if (thumbnail) {
				self._loadImg(container, item, thumbnail);
			} else {
				self._loadImgDefault(container);
			}

			return self;
		},

		_loadImg : function(container, item, thumbnail) {
			var self = this
				o = self.options;

			$('<img />')
				.error(function() {
					if (thumbnail === item.thumbnail && item.thumbnailAlternate) {
						self._loadImg(container, item, item.thumbnailAlternate);
					} else {
						self._loadImgDefault(container);
					}
				})
				.load(function() {
					var img = $(this);
					img.prependTo(container)
						.css({
                            marginTop : '-' + (img.height() / 2) + 'px',
                            marginLeft : '-' + (img.width() / 2) + 'px'
                        });
				})
				.addClass('nos-thumbnails-thumb-img')
				.attr('src', thumbnail);

			return self;
		},

		_loadImgDefault : function(container) {
			var self = this
				o = self.options;

			$('<div></div>')
				.addClass('nos-thumbnails-thumb-img-default')
				.prependTo(container);

			return self;
		},

		select : function(index) {
			var self = this,
				o = self.options;

			if (index === undefined) {
				var sel = self.uiContainer.find('.nos-thumbnails-thumb:has(wijmo-wijgrid-current-cell)');
				if (sel.length) {
					var data = sel.data('thumbnail');
					return data.index;
				}

				return null;
			} else {
				self.uiContainer.find('td').removeClass('wijmo-wijgrid-current-cell ui-state-highlight');

				var sel = self.uiContainer.find('.nos-thumbnails-thumb').eq(index);
				if (sel.length) {
					var data = sel.data('thumbnail');

					sel.find('.wijgridtd')
						.eq(0)
						.addClass('wijmo-wijgrid-current-cell ui-state-highlight');

					self._trigger('selectionChanged', null, {item : data});
				} else {
					self._trigger('selectionChanged');
					return false
				}
			}

			return self;
		}
	});
	return $;
});

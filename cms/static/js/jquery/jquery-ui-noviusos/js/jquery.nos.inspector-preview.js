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
	$.widget( "nos.inspectorPreview", {
		options: {
            meta : {},
            actions : [],
			data : null,
			dataParser : null,
			texts : {
				headerDefault : 'Preview',
				selectItem : 'No item selected'
			}
		},

		data : null,

		_create: function() {
			var self = this,
				o = self.options;

			self.element.addClass('nos-inspector-preview ui-widget ui-widget-content wijmo-wijgrid')
                .parents('.nos-mp3grid')
                .bind('selectionChanged.mp3grid', function(e, data) {
                    if ($.isPlainObject(data)) {
                        self.select(data);
                    } else {
                        self.unselect();
                    }
                });
		},

		_init: function() {
			var self = this,
				o = self.options;

			self.data = self.data || o.data;

			if ($.isPlainObject(self.data)) {
				self.select(self.data);
			} else {
				self.unselect();
			}
		},

		_uiHeader : function(title) {
			var self = this,
				o = self.options;

			var table = $('<table cellspacing="0" cellpadding="0" border="0"><thead></thead></table>')
					.addClass('nos-inspector-preview-header wijmo-wijsuperpanel-header wijmo-wijgrid-root wijmo-wijgrid-table')
					.css({
						borderCollapse : 'separate',
						'-moz-user-select' : '-moz-none'
					})
					.appendTo(self.element);

			var tr = $('<tr></tr>').addClass('wijmo-wijgrid-headerrow')
				.appendTo(table);

			$('<th><div><span></span></div></th>').addClass('wijgridth ui-widget wijmo-c1basefield ui-state-default wijmo-c1field')
				.appendTo(tr)
				.find('div')
				.addClass('wijmo-wijgrid-innercell')
				.find('span')
				.addClass('wijmo-wijgrid-headertext')
				.text(title);

			return self;
		},

		_uiFooter : function() {
			var self = this,
				o = self.options;

			if (o.actions.length > 0) {

				self.uiFooter = $('<div></div>')
					.addClass('nos-inspector-preview-footer')
					.appendTo(self.uiContainer);

                $.each(o.actions, function() {
                    var action = this;
                    if (action.primary) {
                        var text = action.icon ? '<span class="ui-button-icon-primary ui-icon ui-icon-' + action.icon + ' wijmo-wijmenu-icon-left"></span>' : '';
                            text += '<span class="ui-button-text">' + action.label + '</span>';
                        $('<button></button>')
                            .addClass('ui-button ui-button-text' + (action.icon ? '-icon-primary' : '') + ' ui-widget ui-state-default ui-corner-all')
                            .appendTo(self.uiFooter)
                            .html(text)
                            .hover(function() {
                                $(this).addClass('ui-state-hover');
                            }, function() {
                                $(this).removeClass('ui-state-hover');
                            })
                            .click(function(e) {
                                e.preventDefault();
                                e.stopImmediatePropagation();
                                action.action.apply(this, [self.data]);
                            })
                    } else {
                        $('<a href="#"></a>')
                            .appendTo(self.uiFooter)
                            .text(action.label)
                            .click(function(e) {
                                e.preventDefault();
                                e.stopImmediatePropagation();
                                action.action.apply(this, [self.data]);
                            })
                    }
                });

			}

			return self;
		},

		_uiThumbnail : function(data) {
			var self = this,
				o = self.options,
				thumbnail = data.thumbnail.replace(/64-64/g, '256-256') || data.thumbnailAlternate;

			if (thumbnail) {
				self._loadImg(data, thumbnail);
			}

			return self;
		},

		_loadImg : function(item, thumbnail) {
			var self = this
				o = self.options;

			$('<img />')
				.error(function() {
					$(this).remove();
					if (thumbnail === item.thumbnail && item.thumbnailAlternate) {
						self._loadImg(item, item.thumbnailAlternate);
					}
				})
				.load(function() {
					var img = $(this),
						height = img.height();

					$('<div></div>')
						.addClass('nos-inspector-preview-thumb')
						.css({
							backgroundImage :'url("' + img.attr('src') +'")',
							height : (height <= 100 ? height : 100) + 'px'
						})
						.prependTo(self.uiContainer);
					img.remove();
				})
				.css({
					position : 'absolute',
					visibility : 'hidden'
				})
				.attr('src', thumbnail)
				.appendTo('body');

			return self;
		},

		_uiMetaData : function(data) {
			var self = this,
				o = self.options,
                i = 0;

			var table = $('<table cellspacing="0" cellpadding="0" border="0"><tbody></tbody></table>')
					.addClass('nos-inspector-preview-metadata wijmo-wijgrid-root wijmo-wijgrid-table')
					.css({
						borderCollapse : 'separate',
						'-moz-user-select' : '-moz-none'
					})
					.appendTo(self.uiContainer)
					.find('tbody')
					.addClass('ui-widget-content wijmo-wijgrid-data');

			$.each(o.meta, function(key, meta) {
				var tr = $('<tr></tr>').addClass('wijmo-wijgrid-row ui-widget-content wijmo-wijgrid-datarow' + (i%2 ? ' wijmo-wijgrid-alternatingrow' : ''))
					.appendTo(table);

				$('<td><div></div></td>').addClass('wijgridtd wijdata-type-string')
					.appendTo(tr)
					.find('div')
					.addClass('wijmo-wijgrid-innercell')
					.text(meta.label || '');

				$('<td><div></div></td>').addClass('wijgridtd wijdata-type-string')
					.appendTo(tr)
					.find('div')
					.addClass('wijmo-wijgrid-innercell')
					.text(data[key] || '');
                i++;
			});

			return self;
		},

		unselect : function() {
			var self = this,
				o = self.options;

			self.element.wijsuperpanel('destroy')
				.empty();

			self._uiHeader(o.texts.headerDefault);

			self.uiContainer = $('<div></div>')
				.addClass('nos-inspector-preview-noitem')
				.text(o.texts.selectItem)
				.appendTo(self.element);

			self.element.wijsuperpanel({
					showRounder : false
				});

			return self;
		},

		select : function(data) {
			var self = this,
				o = self.options;

			if (data === undefined) {
				return self.data;
			} else {
				self.data = data;

				if ($.isFunction(o.dataParser)) {
					data = o.dataParser(data);
				}

				self.element.wijsuperpanel('destroy')
					.empty()
					.css('height', '100%');

				self._uiHeader(data.title);

				self.uiContainer = $('<div></div>')
					.addClass('nos-inspector-preview-container')
					.appendTo(self.element);

				self._uiThumbnail(data)
					._uiMetaData(data.meta)
					._uiFooter();

				self.element.wijsuperpanel({
						showRounder : false,
						autoRefresh : true
					});
			}

			return self;
		},

		refresh : function() {
			var self = this;

			self._init();

			return self;
		}
	});
	return $;
});

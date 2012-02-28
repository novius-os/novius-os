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
	$.widget( "nos.nostreegrid", {
		options: {
            treeUrl : '',
            sortable : true,
            movable : true,
            texts : {
                moveConfirm : 'Are you sure you want to move \nxxx\ninto\nyyy'
            }
		},

        oldFirstColumn : null,
        treeData : {},
        arrayData : [],
        treeDataSource : null,
        nosGrid : null,

        dragged  : false,
        dragHelper : false,
        helperAdd : false,
        helperDenied : false,
        mousePressed : false,
        mouseMoved : false,
        timer : false,

		_create: function() {
			var self = this,
                o = self.options;

            if ($.isFunction(o.rendered)) {
                var old_rendered = o.rendered;
            }
            o.rendered = function() {
                if ($.isFunction(old_rendered)) {
                    old_rendered.apply(this, arguments);
                }

                self._dragInit();
            };

            o.allowPaging = false;
		},

		_init: function() {
			var self = this,
				o = self.options;

            self.treeDataSource = new wijdatasource({
                dynamic: true,
                proxy: new wijhttpproxy({
                    url: o.treeUrl,
                    dataType: "json",
                    error: function(jqXHR, textStatus, errorThrown) {
                        log(jqXHR, textStatus, errorThrown);
                    },
                    data: {}
                }),
                loaded: function(dataSource, data) {
                    var toObject = function(items) {
                            var oItems = {};
                            $.each(items, function() {
                                var item = this;
                                item.treeHash = item.treeType + '|' + item.treeId;
                                if ($.isArray(item.treeChilds)) {
                                    item.treeChilds = toObject(item.treeChilds);
                                }
                                if (!item.treeTitle) {
                                    item.treeTitle = item.title || item.name || item.label || item.treeHash;
                                }
                                oItems[item.treeHash] = item;
                            });
                            return oItems;
                        },
                        items = toObject(dataSource.items);

                    if (!$.isPlainObject(data)) {
                        self.treeData = items;
                    } else {
                        self._getTreeNode(data.node).treeChilds = items;
                    }
                    self._treeGrid();
                },
                reader: {
                    read: function (dataSource) {
                        dataSource.items = dataSource.data.items;
                    }
                }
            });

            self.oldFirstColumn = $.extend(true, {}, o.columns[0]);

            o.columns[0].cellFormatter = function(args) {
                if ($.isPlainObject(args.row.data)) {
                    var tr = args.$container.closest('tr'),
                        node = self._getTreeNode(args.row.data);
                    if (args.column.dataKey) {
                        $('<span></span>').text(args.row.data[args.column.dataKey])
                            .appendTo(args.$container);
                    } else if ($.isFunction(self.oldFirstColumn.cellFormatter)) {
                        self.oldFirstColumn.oldCellFormatter.call(args);
                    }
                    if (node.treeChilds) {
                        $('<div class="nostreegrid-toggle"></div>').css({
                                marginLeft : (node.treeLevel * 20) + 'px',
                                cursor : 'pointer'
                            })
                            .addClass('ui-icon')
                            .addClass($.isPlainObject(node.treeChilds) ? 'ui-icon-triangle-1-se' : 'ui-icon-triangle-1-e')
                            .click(function(e) {
                                e.stopImmediatePropagation();
                                if ($.isPlainObject(node.treeChilds)) {
                                    $(this).toggleClass('ui-icon-triangle-1-e ui-icon-triangle-1-se');
                                    node.treeChilds = true;
                                    tr.nextAll().each(function(i) {
                                        var tr = $(this),
                                            n = tr.data('treeNode');

                                        if (!$.isPlainObject(n) || n.treeLevel <= node.treeLevel) {
                                            return false;
                                        } else {
                                            tr.remove();
                                        }
                                    });
                                } else {
                                    $(this).toggleClass('ui-icon-triangle-1-e ui-icon-clock');
                                    self.treeDataSource.proxy.options.data.deep = 1;
                                    self.treeDataSource.proxy.options.data.id = node.treeId;
                                    self.treeDataSource.proxy.options.data.type = node.treeType;
                                    self.treeDataSource.load({
                                        node : node
                                    });
                                }
                            })
                            .prependTo(args.$container);
                    } else {
                        $('<div class="nostreegrid-toggle"></div>').css({
                                marginLeft : (node.treeLevel * 20) + 'px'
                            })
                            .addClass('nos-icon16 nos-icon16-empty')
                            .prependTo(args.$container);
                    }
                    tr.data('treeNode', node);
                    return true;
                };
            }

            o.columns.unshift({
                allowSort : false,
                allowMoving : false,
                allowSizing : false,
                width : 25,
                ensurePxWidth : true,
                cellFormatter : function(args) {
                    if ($.isPlainObject(args.row.data)) {

                        args.$container.append('<div class="ui-icon ui-icon-grip-dotted-vertical nostreegrid-move-handle"></div>');

                        return true;
                    };
                }
            });

            $.each(o.columns, function() {
                this.sortDirection = 'none';
            });

            self.treeDataSource.proxy.options.data.deep = 2;
            self.treeDataSource.load();
		},

        _dragInit : function() {
            var self = this,
                o = self.options;

            if (!o.movable) {
                return self;
            }

            self.element.find('tr.wijmo-wijgrid-row').each(function() {
                var $tr = $(this),
                    node = $tr.data('treeNode'),
                    $handle = $tr.find('.nostreegrid-move-handle');

                if (!self._trigger('movabled', null, {node : node, $tr : $tr})) {
                    return false;
                }

                $handle.mousedown(function(event){
                        if (event.which != 1) { // Not the left button
                            return true;
                        }
                        event.stopImmediatePropagation();
                        self.mousePressed = new Date().getTime();
                        cloneNode      = $tr.clone();

                        self.dragHelper = $('<div class="nostreegrid-drag-helper ui-state-highlight"></div>')
                            .hide()
                            .css('opacity', '0.8')
                            .append(cloneNode.find('td:eq(1)').find('>div'))
                            .appendTo('body');
                        self.helperInsert = $('<div class="nostreegrid-drag-inserthelper ui-state-highlight"></div>');
                        self.helperAdd = $('<div class="nostreegrid-drag-addhelper ui-icon ui-icon-circle-plus"></div>');
                        self.helperDenied = $('<div class="nostreegrid-drag-deniedhelper ui-icon ui-icon-circle-alert"></div>');
                        $(document).on('mousemove', {treeGrid : self, node : node, $tr : $tr}, self._dragStart)
                            .on('mouseup', {treeGrid : self}, self._dragDestroy);
                    });
                $tr.mouseup(function(){
                        if (self.mousePressed && self.mouseMoved && self.dragged) {
                            if (self._trigger('dropped', null, {dragged : self.dragged, dropped : $tr, notification : true})) {
                                var dragNode = self.dragged.data('treeNode');
                                if (confirm(o.texts.moveConfirm.replace('xxx', (dragNode.treeTitle + '').toUpperCase()).replace('yyy', node.treeTitle + '').toUpperCase())) {
                                    self._move(dragNode, node);
                                }
                            }
                        }
                        self._dragDestroy();
                    });
            });

            // happens when open a level during a drag
            if (self.draggedIndex) {
                var mousePressed = self.mousePressed,
                    $tr = self.element.find('tr.wijmo-wijgrid-row')
                        .eq(self.draggedIndex);

                self._dragDestroy();
                $tr.find('.nostreegrid-move-handle')
                    .trigger(jQuery.Event('mousedown', {which : 1}));
                self.mousePressed = mousePressed;
                self.dragOpen.event.target = self.element.find('tr.wijmo-wijgrid-row')[self.dragOpen.index];
                self.dragOpen.event.data.$tr = $tr;
                $tr.trigger(self.dragOpen.event);
            }

            return self;
        },

        _dragStart : function(event){
            var self = event.data.treeGrid,
                o = self.options,
                node = event.data.node,
                $tr = event.data.$tr;

            if (!o.movable) {
                return self;
            }
            if (self.mousePressed && (new Date().getTime() - self.mousePressed) > 500) {
                if (!self._scroller) {
                    self._scroller = {
                        superpanel : self.nosGrid._view()._scroller.data('wijsuperpanel')
                    };
                    var contentElement = self._scroller.superpanel.getContentElement(),
                        contentWrapper = contentElement.parent(),
                        os = contentWrapper.offset();

                    self._scroller.contentElement = self._scroller.superpanel.getContentElement();
                    self._scroller.contentWrapper = self._scroller.contentElement.parent();
                    $.extend(self._scroller, self._scroller.contentElement.offset(), {
                        bottom : os.top + contentWrapper.height()
                    });
                }
                if (event.pageY < self._scroller.top) {
                    self._scroller.superpanel._doScrolling('top', self._scroller.superpanel, true);
                } else if (event.pageY < (self._scroller.top + 20)) {
                    self._scroller.superpanel._doScrolling('top', self._scroller.superpanel);
                } else if (event.pageY > self._scroller.bottom) {
                    self._scroller.superpanel._doScrolling('bottom', self._scroller.superpanel, true);
                } else if (event.pageY > (self._scroller.bottom - 20)) {
                    self._scroller.superpanel._doScrolling('bottom', self._scroller.superpanel);
                }

                self.mouseMoved = true;
                if (self.timer) {
                    clearTimeout(self.timer);
                }
                if (!self.dragHelper.is(':visible')) {
                    self.dragHelper.show();
                    self.dragged = $tr;
                    self.draggedIndex = $tr.data('wijgriddataItemIndex');
                }
                self.dragHelper.css({
                    left : (event.pageX + 5),
                    top : (event.pageY + 15)
                });
                $tr.css('visibility', 'hidden');

                if ($(event.target).closest(self.element).size() && ($(event.target).is('tr.wijmo-wijgrid-row') || $(event.target).parents('tr.wijmo-wijgrid-row').size())) {
                    var hovered = $(event.target).parents('tr:first'),
                        dim = $.extend({
                                width : hovered.width(),
                                height : hovered.height()
                            }, hovered.offset()),
                        icon = hovered.find('.nostreegrid-toggle');

                    if (o.sortable && event.pageY < (dim.top + 5)) {
                        self.helperAdd.detach();
                        self.helperDenied.detach();
                        self.helperInsert.css('margin-left', icon.css('margin-left'))
                            .insertBefore(icon);
                    } else if (o.sortable && event.pageY > (dim.top + dim.height - 5)) {
                        self.helperAdd.detach();
                        self.helperDenied.detach();
                        self.helperInsert.css('margin-left', icon.css('margin-left'))
                            .appendTo(hovered.find('td:eq(1)').find('>div'));
                    } else {
                        self.helperInsert.detach();
                        if (!self._trigger('dropped', null, {dragged : $tr, dropped : hovered, notification : false})) {
                            self.helperAdd.detach();
                            self.helperDenied.insertAfter(icon);
                        } else {
                            self.helperDenied.detach();
                            if ($(event.target).hasClass('nostreegrid-toggle') && $(event.target).hasClass('ui-icon-triangle-1-e')) {
                                self.timer = setTimeout(function(){
                                    self.dragOpen = {
                                        event : event,
                                        index : hovered.data('wijgriddataItemIndex')
                                    };
                                    $(event.target).click();
                                }, 700);
                            }
                            self.helperAdd.insertAfter(icon);
                        }
                    }
                } else {
                    self.dragHelper.hide();
                    self.helperInsert.detach();
                    self.helperDenied.detach();
                    self.helperAdd.detach();
                }
            }
            return self;
        },

        _dragDestroy : function(event) {
            var self = event ? event.data.treeGrid : this,
                o = self.options;

            if (!o.movable) {
                return self;
            }
            if (self.timer) {
                clearTimeout(self.timer);
            }
            if (self.dragHelper) {
                $(document).off('mousemove', self._dragStart)
                    .off('mouseup')
                    .off('mousedown');
                self.dragHelper.remove();
                self.helperInsert.remove();
                self.helperAdd.remove();
                self.helperDenied.remove();
                if (self.dragged) {
                    self.dragged.css('visibility', 'visible');
                }
                self.dragged = self.mousePressed = self.mouseMoved = self.dragHelper = self.helperAdd = self.helperDenied = self.timer = self._scroller = false;
            }

            return self;
        },

        _getTreeNode : function(data) {
            var self = this,
                o = self.options,
                node = null,
                path = data.treePath.concat([data.treeHash]);

            $.each(path, function(i, id) {
                node = !node ? self.treeData[id] : node.treeChilds[id];
            });

            return node;
        },

        _treeGrid : function() {
            var self = this,
                o = self.options,
                recursive = function(parent, childs) {
                    $.each(childs, function(id, child) {
                        self.arrayData.push($.extend(child, {
                            treeLevel : !parent ? 0 : parent.treeLevel + 1,
                            treePath : !parent ? [] : parent.treePath.concat([parent.treeHash])
                        }));
                        if ($.isPlainObject(child.treeChilds)) {
                            recursive(child, child.treeChilds);
                        }
                    });
                };

            self.arrayData = [];
            recursive(null, self.treeData);

            if (self.nosGrid) {
                self.nosGrid.destroy();
            }
            self.element.empty();
            if ($.isFunction(o.beforeRefresh)) {
                o.beforeRefresh.call(self);
            }
            self.nosGrid = self.element.nosgrid($.extend(o, {
                    data : self.arrayData
                }))
                .data('nosgrid');

            return self;
        }
    });
	return $;
});

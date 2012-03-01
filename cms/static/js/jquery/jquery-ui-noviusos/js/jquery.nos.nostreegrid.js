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
	$.widget( "nos.nostreegrid", $.nos.nosgrid, {
		options: {
            treeUrl : '',
            sortable : true,
            movable : true,
            data : []
		},

        oldFirstColumn : null,
        treeData : {},
        treeDataSource : null,

        dragged  : false,
        dropTarget  : false,

        dragHelper : false,
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

            if ($.isFunction(o.rowStyleFormatter)) {
                var old_rowStyleFormatter = o.rowStyleFormatter;
            }
            o.rowStyleFormatter = function(args) {
                if ($.isFunction(old_rowStyleFormatter)) {
                    old_rowStyleFormatter.apply(this, arguments);
                }
                if (self.draggedIndex && args.state & $.wijmo.wijgrid.renderState.hovered) {
                    args.$rows.removeClass("ui-state-hover");
                }
            };

            o.allowPaging = false;

            $.nos.nosgrid.prototype._create.call(self);
		},

		_init: function() {
			var self = this,
				o = self.options;

            self.oldFirstColumn = $.extend(true, {}, o.columns[0]);

            o.columns[0].cellFormatter = function(args) {
                if ($.isPlainObject(args.row.data)) {
                    var tr = args.$container.closest('tr'),
                        node = args.row.data;
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
                                    self._toggle(tr, false);
                                } else {
                                    self._toggle(tr, true);
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

            $.nos.nosgrid.prototype._init.call(self);

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
                    var nosGridData = self.data(),
                        toArray = function(objets) {
                            return $.map(objets, function(el, key) {
                                if (key === 'length') {
                                    return null;
                                }
                                var r = el;
                                if ($.isPlainObject(el.treeChilds)) {
                                    r = [el].concat(toArray(el.treeChilds));
                                }
                                return r;
                            });
                        };

                    if (!$.isPlainObject(data)) {
                        self.treeData = self._completeChilds(null, dataSource.items);
                        $.merge(nosGridData, toArray(self.treeData));
                    } else {
                        var index = $.inArray(data.node, nosGridData);
                        self._removeNode(data.node);
                        if (data.close) {
                            data.node.treeChilds = dataSource.data.total;
                            Array.prototype.splice.apply(nosGridData, [index, 0, data.node]);
                        } else {
                            data.node.treeChilds = self._completeChilds(data.node, dataSource.items);
                            Array.prototype.splice.apply(nosGridData, [index, 0, data.node].concat(toArray(data.node.treeChilds)));
                        }
                    }
                    self.ensureControl(true);
                },
                reader: {
                    read: function (dataSource) {
                        dataSource.items = dataSource.data.items;
                    }
                }
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
                        $(document).on('mousemove', {treeGrid : self, node : node, $tr : $tr}, self._dragStart)
                            .on('mouseup', {treeGrid : self}, self._dragDestroy);
                    });
                $tr.mouseup(function(){
                        if (self.mousePressed && self.mouseMoved && self.dragged && self.dropTarget) {
                            if (self._trigger('dropped', null, {dragged : self.dragged, dropped : $tr, notification : true})) {
                                self._move($tr);
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
                        superpanel : self._view()._scroller.data('wijsuperpanel')
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
                if (!self.mouseMoved) {
                    var body = $('body');
                    if (body.css('cursor')) {
                        self._cursor = body.css("cursor");
                    }
                    body.css('cursor', 'move');
                    self.mouseMoved = true;
                }
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

                self.dropTarget = false;
                var hovered = $(event.target).parents('tr:first');
                if ($(event.target).closest(self.element).size() && ($(event.target).is('tr.wijmo-wijgrid-row') || $(event.target).parents('tr.wijmo-wijgrid-row').size())) {
                    var nodeHovered = hovered.data('treeNode'),
                        dim = $.extend({
                                width : hovered.width(),
                                height : hovered.height()
                            }, hovered.offset()),
                        icon = hovered.find('.nostreegrid-toggle'),
                        recursif = $.inArray(node.treeHash, nodeHovered.treePath) !== -1;

                    if (!recursif && o.sortable && event.pageY < (dim.top + 5)) {
                        self.dropTarget = 'before';
                        hovered.removeClass("ui-state-hover");
                        self.helperInsert.css({
                                marginLeft : (parseInt(icon.css('margin-left').replace('px', '')) + icon.width()) + 'px',
                                marginBottom :''
                            })
                            .insertBefore(icon);
                    } else if (!recursif && o.sortable && event.pageY > (dim.top + dim.height - 5)) {
                        self.dropTarget = 'after';
                        hovered.removeClass("ui-state-hover");
                        self.helperInsert.css({
                                marginLeft : (parseInt(icon.css('margin-left').replace('px', '')) + icon.width()) + 'px',
                                marginTop : ''
                            })
                            .appendTo(hovered.find('td:eq(1)').find('>div'));
                    } else {
                        self.helperInsert.detach();
                        if (recursif || !self._trigger('dropped', null, {dragged : $tr, dropped : hovered, notification : false})) {
                            hovered.removeClass("ui-state-hover");
                        } else {
                            self.dropTarget = 'in';
                            if ($(event.target).hasClass('nostreegrid-toggle') && $(event.target).hasClass('ui-icon-triangle-1-e')) {
                                self.timer = setTimeout(function(){
                                    self.dragOpen = {
                                        event : event,
                                        index : hovered.data('wijgriddataItemIndex')
                                    };
                                    $(event.target).click();
                                }, 700);
                            }
                            hovered.addClass("ui-state-hover");
                        }
                    }
                } else {
                    hovered.removeClass("ui-state-hover");
                    self.dragHelper.hide();
                    self.helperInsert.detach();
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
                $('body').css('cursor', self._cursor || 'auto');
                self.dragHelper.remove();
                self.helperInsert.remove();
                if (self.dragged) {
                    self.dragged.css('visibility', 'visible');
                }
                self.dragged = self.mousePressed = self.mouseMoved = self.dragHelper = self.timer = self._scroller = false;
            }

            return self;
        },

        _completeChilds : function(parent, childs) {
            var self = this,
                o = self.options,
                nb = 0,
                oItems = {};

            if (childs.length) {
                delete childs.length;
            }
            $.each(childs, function(id, child) {
                $.extend(child, {
                    treeHash : child._model + '|' + child._id,
                    treeLevel : !parent ? 0 : parent.treeLevel + 1,
                    treePath : !parent ? [] : parent.treePath.concat([parent.treeHash])
                });
                if ($.isArray(child.treeChilds) || $.isPlainObject(child.treeChilds)) {
                    child.treeChilds = self._completeChilds(child, child.treeChilds);
                }
                nb++;
                oItems[child.treeHash] = child;
            });

            return $.extend({length : nb}, oItems);
        },

        _getTreeNode : function(path) {
            var self = this,
                o = self.options,
                node = null;

            $.each(path, function(i, id) {
                node = !node ? self.treeData[id] : node.treeChilds[id];
            });

            return node;
        },

        _move : function(dropped) {
            var self = this,
                o = self.options,
                dragNode = self.dragged.data('treeNode'),
                dropNode = dropped.data('treeNode'),
                targets = {
                    'in' : 'moveInConfirm',
                    'after' : 'moveAfterConfirm',
                    'before' : 'moveBeforeConfirm'
                },
                text = o.texts[targets[self.dropTarget]];

            $.ajax({
                async : true,
                url : o.treeUrl,
                data : {
                    move : true,
                    itemModel : dragNode._model,
                    itemId : dragNode._id,
                    targetModel : dropNode._model,
                    targetId : dropNode._id,
                    targetType : self.dropTarget
                },
                dataType : 'json',
                success : function (data, textStatus) {
                    log('success', data);
                    self.draggedIndex = false;
                    self._removeNode(dragNode);
                    if (self.dropTarget === 'in') {
                        self._toggle(dropped, true);
                    } else {
                        var oldParent = self._getTreeNode(dragNode.treePath),
                            newParent = self._getTreeNode(dropNode.treePath),
                            index = $.inArray(newParent, self.data()),
                            $tr = self.element.find('tr.wijmo-wijgrid-row').eq(index);

                        delete oldParent.treeChilds[dragNode.treeHash];
                        delete oldParent.treeChilds.length;
                        oldParent.treeChilds = self._completeChilds(oldParent, oldParent.treeChilds);
                        self._toggle($tr, true);
                    }
                },
                error : function(jqXHR, textStatus, errorThrown) {
                    log('error', textStatus, errorThrown);
                }
            });

            return self;
        },

        _toggle : function($tr, open) {
            var self = this,
                o = self.options,
                node = $tr.data('treeNode'),
                open = open === undefined ? $tr.hasClass('ui-icon-triangle-1-e') : open;

            $tr.find('.nostreegrid-toggle').addClass(open ? 'ui-icon-clock' : 'ui-icon-triangle-1-e')
                .removeClass(open ? 'ui-icon-triangle-1-e' : 'ui-icon-triangle-1-se');
            self.treeDataSource.proxy.options.data.deep = open ? 1 : -1;
            self.treeDataSource.proxy.options.data.id = node._id;
            self.treeDataSource.proxy.options.data.model = node._model;
            self.treeDataSource.load({
                close : open ? false : true,
                node : node
            });

            return self;
        },

        _removeNode : function(node) {
            var self = this,
                o = self.options,
                removeIndex = false,
                removeLength = 0,
                data = self.data();
            $.each(data, function(i, item) {
                var remove = node.treeHash == item.treeHash || $.inArray(node.treeHash, item.treePath) !== -1;
                if (remove) {
                    if (!removeIndex) {
                        removeIndex = i;
                    }
                    removeLength++;
                }
                if (!remove && removeIndex) {
                    return false;
                }
            });
            if (removeIndex) {
                Array.prototype.splice.apply(data, [removeIndex, removeLength]);
            }

            return removeLength;
        }
    });
	return $;
});

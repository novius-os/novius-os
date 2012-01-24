<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */
    $id = uniqid('temp_');
?>
<table id="<?= $id ?>"></table>
<script type="text/javascript">
require([
		'jquery-nos'
	], function( $, table, undefined ) {
		$(function() {
			var inspector = $('#<?= $id ?>').removeAttr('id'),
				parent = inspector.parent().bind({
						inspectorResize: function() {
                            inspector.nosgrid('setSize', parent.width(), parent.height());
						}
					}),
                inspectorData = parent.data('inspector'),
				rendered = false,
                collapseIndentBehaviour = function(column, options) {
                        if ($.isFunction(column.cellFormatter)) {
                            column.oldCellFormatter = column.cellFormatter;
                        }

                        $.extend({
                            allExpanded: false,
                            allowCollapseExpand: true
                        }, options);

                        $.extend(column, {
                            allowMoving : false,
                            cellFormatter : function(args) {
                                if ($.isPlainObject(args.row.data)) {
                                    var tr = args.$container.closest('tr');
                                    if (args.column.dataKey) {
                                        $('<span></span>').text(args.row.data[args.column.dataKey])
                                            .appendTo(args.$container);
                                    } else if ($.isFunction(args.column.oldCellFormatter)) {
                                        args.column.oldCellFormatter.call(args);
                                    }
                                    if (args.row.data.hasChilds) {
                                        var toggle = $('<div></div>').css({
                                            float : 'left',
                                            marginLeft : (args.row.data.level * 20) + 'px'
                                        })
                                            .addClass('ui-icon').addClass(options.allExpanded ? 'ui-icon-triangle-1-se' : 'ui-icon-triangle-1-e');
                                        if (options.allowCollapseExpand) {
                                            toggle.css({
                                                cursor : 'pointer'
                                            }).click(function(e) {
                                                    tr.nextAll().each(function() {
                                                        var self = $(this),
                                                            level = self.data('tree-level');

                                                        if (level <= args.row.data.level) {
                                                            return false;
                                                        }
                                                        if (level === (args.row.data.level + 1)) {
                                                            self.toggle();
                                                        }
                                                    });
                                                    toggle.toggleClass('ui-icon-triangle-1-e ui-icon-triangle-1-se');
                                                    e.stopImmediatePropagation();
                                                });
                                        };
                                        toggle.prependTo(args.$container);
                                    } else {
                                        $('<div></div>').css({
                                            'float' : 'left',
                                            marginLeft : (args.row.data.level * 20) + 'px'
                                        })
                                            .addClass('ui-icon ui-icon-stop')
                                            .prependTo(args.$container);
                                    }
                                    if (args.row.data.level > 0 && !options.allExpanded) {
                                        tr.hide();
                                    }
                                    tr.data('tree-level', args.row.data.level);
                                    return true;
                                }
                            }
                        });
                    };

            inspector.css({
                    height : '100%',
                    width : '100%'
                })
                .nosgrid({
                    columnsAutogenerationMode : 'none',
                    scrollMode : 'auto',
                    allowColSizing : true,
                    allowColMoving : true,
                    currentCellChanged : function(e) {
                        var row = $(e.target).nosgrid("currentCell").row(),
                            data = row ? row.data : false;

                        if (data && rendered) {
                            inspectorData.selectionChanged(data.id, data.title);
                        }
                        inspector.nosgrid("currentCell", -1, -1);
                    },
                    rendering : function() {
                        rendered = false;
                    },
                    rendered : function() {
                        rendered = true;
                        inspector.css("height", "auto");
                    },
                    columns: inspectorData.grid.columns,
                    data: new wijdatasource({
                        proxy: new wijhttpproxy({
                            url: inspectorData.grid.urlJson,
                            dataType: "json",
                            error: function(jqXHR, textStatus, errorThrown) {
                                log(jqXHR, textStatus, errorThrown);
                            },
                            data: {}
                        }),
                        reader: {
                            read: function (dataSource) {
                                var count = parseInt(dataSource.data.total, 10);
                                dataSource.data = dataSource.data.items;
                                dataSource.data.totalRows = count;
                            }
                        }
                    })
                });

			collapseIndentBehaviour(inspectorData.grid.columns[0], {
                    allExpanded: true,
                    allowCollapseExpand: true
                });

			$nos.nos.listener.add(inspectorData.widget_id + '.refresh', true, function() {
                    parent.triggerHandler('inspectorResize');
                });
		});
	});
</script>
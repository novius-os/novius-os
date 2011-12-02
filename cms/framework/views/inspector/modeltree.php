<?php
/**
 * NOVIUS OS - Web OS for digital communication
 * 
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

?>
<table id="<?= $widget_id ?>"></table>
<script type="text/javascript">
require([
		'jquery-nos'
	], function( $, table, undefined ) {
		$(function() {
			var widget_id = "<?= $widget_id ?>",
				inspector = $('#' + widget_id),
				parent = inspector.parent().bind({
						inspectorResize: function() {
							inspector.wijgrid('destroy')
								.empty();
							init();
						}
					}),
				columns = <?= $columns ?>,
				rendered = false,
				init = function() {
					inspector.css({
							height : '100%',
							width : '100%'
						})
						.wijgrid({
							columnsAutogenerationMode : 'none',
							scrollMode : 'auto',
							allowColSizing : true,
							allowColMoving : true,
							staticRowIndex : 0,
							columns : columns,
							data: new wijdatasource({
								proxy: new wijhttpproxy({
									url: "<?= $urljson ?>",
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
							}),
							currentCellChanged: function (e) {
								var row = $(e.target).wijgrid("currentCell").row(),
									data = row ? row.data : false;
									
								if (data && rendered) {
									$nos.nos.listener.fire('inspector.selectionChanged.' + widget_id, false, ["<?= $input_name ?>", data.id, data.title]);
								}
								inspector.wijgrid("currentCell", -1, -1);
							},
							rendering : function() {
								rendered = false;
							},
							rendered : function() {
								rendered = true;
								inspector.css('height', 'auto');
							}
						});

				};

			if ($.isFunction(columns[0].cellFormatter)) {
				columns[0].oldCellFormatter = columns[0].cellFormatter;
			}
			$.extend(columns[0], {
				allowMoving : false,
				cellFormatter : function(args) {
					if ($.isPlainObject(args.row.data)) {
						var tr = args.$container.closest('tr');
						if (args.column.dataKey) {
							$('<span></span>').text(args.row.data[args.column.dataKey])
								.appendTo(args.$container);
						} else if ($.isfunction(args.column.oldCellFormatter)) {
							args.column.oldCellFormatter.call(args);
						}
						if (args.row.data.hasChilds) {
							var toggle = $('<div></div>').css({
									'float' : 'left',
									cursor : 'pointer',
									marginLeft : (args.row.data.level * 20) + 'px'
								})
								.addClass('ui-icon ui-icon-triangle-1-e')
								.click(function() {
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
								})
								.prependTo(args.$container);
						} else {
							$('<div></div>').css({
									'float' : 'left',
									marginLeft : (args.row.data.level * 20) + 'px'
								})
								.addClass('ui-icon ui-icon-stop')
								.prependTo(args.$container);
						}
						if (args.row.data.level > 0) {
							tr.hide();
						}
						tr.data('tree-level', args.row.data.level);
						return true;
					}
				}
			});
			init();
			var menuColumns = [];
			columns = inspector.wijgrid("option", "columns");
			$.each(columns, function (index, col) {
				menuColumns.push({
						label : col.headerText,
						visible : col.visible,
						change : function (visible) {
		                    columns[index].visible = visible;
		                    inspector.wijgrid('doRefresh');
		                }
					});
            });
			$nos.nos.listener.fire('inspector.declareColumns', false, [widget_id, menuColumns]);
		});
	});
</script>
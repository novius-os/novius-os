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
		'jquery-nos',
	], function( $, undefined ) {
		$(function() {
			var widget_id = "<?= $widget_id ?>",
				inspector = $('#' + widget_id),
				parent = inspector.parent()
					.bind({
						inspectorResize: function() {
							inspector.nosgrid('destroy')
								.empty();
							init();
						}
					}),
				table_heights = $.nos.grid.getHeights(),
				pageIndex = 0,
				showFilter = false,
				hasFilters = false,
				columns = <?= $columns ?>,
				rendered = false,
				init = function() {
					inspector.css({
							height : '100%',
							width : '100%'
						})
						.nosgrid({
							columnsAutogenerationMode : 'none',
							showFilter: showFilter,
							allowSorting: true,
							scrollMode : 'auto',
							allowPaging : true,
							pageIndex : pageIndex,
							pageSize: Math.floor((parent.height() - table_heights.footer - table_heights.header - (showFilter ? table_heights.filter : 0)) / table_heights.row),
							allowColSizing : true,
							allowColMoving : true,
							staticRowIndex : 0,
							columns : columns,
							data: new wijdatasource({
								dynamic: true,
								proxy: new wijhttpproxy({
									url: "<?= $urljson ?>",
									dataType: "json",
									error: function(jqXHR, textStatus, errorThrown) {
										log(jqXHR, textStatus, errorThrown);
									},
									data: {}
								}),
								loading: function (dataSource, userData) {
									var r = userData.data.paging;
									pageIndex = r.pageIndex;
									dataSource.proxy.options.data.offset = r.pageIndex * r.pageSize;
									dataSource.proxy.options.data.limit = r.pageSize;
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
								var row = $(e.target).nosgrid("currentCell").row(),
									data = row ? row.data : false;

								if (data && rendered) {
									$nos.nos.listener.fire('inspector.selectionChanged.' + widget_id, false, ["<?= $input_name ?>", data.id, data.title]);
								}
								inspector.nosgrid("currentCell", -1, -1);
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
			init();
			var menuColumns = [];
			columns = inspector.nosgrid("option", "columns"),
			showFilter = inspector.nosgrid("option", "showFilter");
			hasFilters = showFilter;
			$.each(columns, function (index, col) {
				if (col.showFilter === undefined || col.showFilter) {
					hasFilters = true;
				}
				menuColumns.push({
						label : col.headerText,
						visible : col.visible,
						change : function (visible) {
		                    columns[index].visible = visible;
		                    inspector.nosgrid('doRefresh');
		                }
					});
            });
			$nos.nos.listener.fire('inspector.declareColumns', false, [widget_id, menuColumns]);
			if (hasFilters) {
				$nos.nos.listener.fire('inspector.showFilter', false, [widget_id, function(visible) {
					showFilter = visible;
					inspector.nosgrid('destroy')
						.empty();
					init();
				}]);
			}

			$nos.nos.listener.add(widget_id + '.refresh', true, function() {
				parent.triggerHandler('inspectorResize');
			});
		});
	});
</script>

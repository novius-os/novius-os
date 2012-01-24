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
	], function( $, undefined ) {
		$(function() {
			var inspector = $('#<?= $id ?>').removeAttr('id'),
				parent = inspector.parent()
					.bind({
						inspectorResize: function() {
							inspector.nosgrid('destroy')
								.empty();
							init();
						}
					}),
                inspectorData = parent.data('inspector'),
				table_heights = $.nos.grid.getHeights(),
				pageIndex = 0,
				showFilter = inspectorData.grid.false,
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
							columns : inspectorData.grid.columns,
							data: new wijdatasource({
								dynamic: true,
								proxy: new wijhttpproxy({
									url: inspectorData.grid.urlJson,
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
                                    inspectorData.selectionChanged(data.id, data.title);
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

			$nos.nos.listener.add(inspectorData.widget_id + '.refresh', true, function() {
				parent.trigger('inspectorResize');
			});
		});
	});
</script>

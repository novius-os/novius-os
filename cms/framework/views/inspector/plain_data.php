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
				parent = inspector.parent()
					.bind({
						inspectorResize: function() {
							inspector.nosgrid('destroy')
								.empty();
							init();
						}
					}),
                inspectorData = parent.data('inspector'),
				rendered = false,
				init = function() {
					inspector.css({
							height : '100%',
							width : '100%'
						})
						.nosgrid({
							showFilter: false,
							allowSorting: false,
							scrollMode : 'auto',
							allowPaging : false,
							allowColSizing : false,
							allowColMoving : false,
							staticRowIndex : 0,
							columns : inspectorData.grid.columns,
							data: <?= $data ?>,
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
		});
	});
</script>
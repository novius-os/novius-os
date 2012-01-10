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
	], function( $, table, undefined ) {
		$(function() {
			var widget_id = "<?= $widget_id ?>",
				inspector = $('#' + widget_id),
				rendered = false,
				parent = inspector.parent()
					.bind({
						inspectorResize: function() {
							inspector.nosgrid('destroy')
								.empty();
							init();
						}
					}),
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
							columns : <?= $columns ?>,
							data: <?= $data ?>,
							currentCellChanged: function (e) {
								var row = $(e.target).nosgrid("currentCell").row(),
									data = row ? row.data : false;

								if (data && rendered) {
									$nos.nos.listener.fire('inspector.selectionChanged' + widget_id, false, ["<?= $input_name ?>", data.id, data.title]);
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
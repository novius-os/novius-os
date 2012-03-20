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
				connector = inspector.closest('.nos-inspector, body')
					.on('langChange', function() {
						if (inspectorData.langChange) {
							inspector.nostreegrid('option', 'treeOptions', {
								lang : connector.data('nosLang') || ''
							});
						}
					}),
				parent = inspector.parent()
					.on({
						widgetResize : function() {
                            inspector.nostreegrid('setSize', parent.width(), parent.height());
						},
						widgetReload : function() {
							inspector.nostreegrid('reload');
						}
					}),
                inspectorData = parent.data('inspector'),
				rendered = false;

            inspector.css({
                    height : '100%',
                    width : '100%'
                })
                .nostreegrid($.extend({
		            treeOptions : {
			            lang : connector.data('nosLang') || ''
		            },
                    columnsAutogenerationMode : 'none',
                    scrollMode : 'auto',
                    allowColSizing : true,
                    allowColMoving : true,
                    currentCellChanged : function(e) {
                        var row = $(e.target).nostreegrid("currentCell").row(),
                            data = row ? row.data : false;

                        if (data && rendered) {
                            inspectorData.selectionChanged(data.id, data.title);
                        }
                        inspector.nostreegrid("currentCell", -1, -1);
                    },
                    rendering : function() {
                        rendered = false;
                    },
                    rendered : function() {
                        rendered = true;
                        inspector.css("height", "auto");
                    }
                }, inspectorData.treeGrid))
	            .closest('.nos-connector')
	            .on('reload.' + inspectorData.widget_id, function() {
		            parent.trigger('widgetReload');
	            });
		});
	});
</script>
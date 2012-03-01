<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */
    $uniqid = uniqid('temp_');
?>
<table id="<?= $uniqid ?>" style="height:150px; width:100%"></table>
<?php
$folders = array();
foreach (Cms\Model_Media_Folder::find('all') as $folder) {
    $folders[] = array(
        'id'       => $folder->medif_id,
        'title'    => $folder->medif_title,
        'level'    => substr_count($folder->medif_path, '/'),
        'path'     => $folder->medif_path,
        'selected' => $folder->medif_id == $selected,
    );
}
?>
<script type="text/javascript">
require(['jquery-nos'], function ($) {
    var currentCell;
    var $table = $('#<?= $uniqid ?>');
    var $selected_folder = $('#<?= $input_id ?>');
    $selected_folder.hide();
    setTimeout(function() {
        $table.wijgrid({
            selectionMode : 'singleRow',
            highlightCurrentCell : true,
            showColHeader: true,
            scrollMode : 'auto',
            data : <?= \Format::forge()->to_json($folders) ?>,
            rowStyleFormatter : function(args) {
                if (args.type == $.wijmo.wijgrid.rowType.header) {
                    args.$rows.hide();
                }
            },
            currentCellChanged : function(e) {
				var grid = $table.data('wijgrid');
                // The 2 following lines are inspired / copied over from wijgrid _onCurrentCellChanged()
                var currentCell = grid._field("currentCell");
                if (currentCell.toString() != '-1:-1') {
                    grid._view().scrollTo(currentCell);
                    if ($selected_folder) {
                        $selected_folder.val(currentCell.row().data.id);
                        $selected_folder.triggerHandler('change', [currentCell.row().data]);
                    }
                } else if ($selected_folder) {
                    $selected_folder.val('', []);
                }
            },
            columnsAutogenerationMode : 'none',
            columns : [
                {
                    dataKey : 'title',
                    cellFormatter : function(args) {
                        if ($.isPlainObject(args.row.data)) {
                            if (args.row.data.selected) {
                                currentCell = [args.column.dataIndex, args.row.dataRowIndex];
                                log(currentCell);
                            }
                            var container = $(args.$container);
                            var level = args.row.data.level;
                            if (level) {
                                container.css({
                                    paddingLeft : (level * 20) + 'px'
                                });
                            }
                        }
                    }
                }
            ]
        });
        if (currentCell) {
            $table.wijgrid('currentCell', currentCell[0], currentCell[1]);
        } else {
            log('Clearing selection');
            $table.wijgrid('currentCell', -1, -1);
            $table.wijgrid('selection').clear();
        }
    }, 10);
});
</script>
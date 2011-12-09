<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Cms;

use Format;

class ConfigProcessor {
    /** Process the configuration files (replace language and actions column)
     *
     * @static
     * @param $config : configuration file content
     */
    static public function process($config) {
        $format = Format::forge();
        if ($config['ui']) {
            $columns = &$config['ui']['grid']['columns'];
        } else {
            $columns = &$config['columns'];
        }
        for ($i = 0; $i < count($columns); $i++) {
            if ($columns[$i] === 'lang') {
                $columns[$i] = array(
                    'headerText' => 'Languages',
                    'dataKey'   => 'lang',
                    'showFilter' => false,
                    'cellFormatter' => 'function(args) {
						if (args.row.type & $.wijmo.wijgrid.rowType.data) {
							args.$container.css("text-align", "center").html(args.row.data.lang);
							return true;
						}
					}',
                    'width' => 1,
                );
            }
            if (is_array($columns[$i]) && $columns[$i]['actions']) {
                $actions = $columns[$i]['actions'];
                $columns[$i] = array(
                    'headerText' => '',
                    'cellFormatter' => 'function(args) {
						if ($.isPlainObject(args.row.data)) {
							args.$container.parent().addClass("full-occupation");
							$(\'<div class="in_cell"></div>\')
							    .dropdownButton({
                                    items: '.$format->to_json($actions).',
                                    args: args
                                })
                                .appendTo(args.$container);

							return true;
						}
					}',
                    'allowSizing' => false,
                    'width' => 20,
                    'showFilter' => false,
                );
                $main_column = array(
                    'headerText' => $actions[0]['label'],
                    'cellFormatter' => 'function(args) {
  						if ($.isPlainObject(args.row.data)) {
  						    console.log(args.column);
  						    args.$container.parent()
  						    .addClass("full-occupation");
                            button = $(\'<button type="button" />\').button({
                                label: '.json_encode($actions[0]['label']).',
                            });
                            button.appendTo(args.$container);

                            return true;
                        }
                    }',
                    'allowSizing' => false,
                    'width' => 20,
                    'showFilter' => false,
                );
                //print_r($columns);
                array_splice($columns, $i, 0, array($main_column));
                //print_r($columns);
            }
            if (!$columns[$i]['dataType'] && is_array($config['dataset'][$columns[$i]['dataKey']]) && $config['dataset'][$columns[$i]['dataKey']]['dataType']) {
                $columns[$i]['dataType'] = $config['dataset'][$columns[$i]['dataKey']]['dataType'];
            }
            if (!$columns[$i]['dataType']) {
                //print_r($columns[$i]);
                $columns[$i]['dataType'] = 'string';
            }
        }
        return $config;
    }
}
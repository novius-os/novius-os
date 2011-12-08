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
        foreach ($columns as &$col) {
            if ($col === 'lang') {
                $col = array(
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
            if ($col['actions']) {
                $col = array(
                    'headerText' => 'Actions',
                    'cellFormatter' => 'function(args) {
						if ($.isPlainObject(args.row.data)) {
							args.$container.css("text-align", "center");
							$(\'<div></div>\')
							    .dropdownButton({
                                    items: '.$format->to_json($col['actions']).',
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
            }
            if (!$col['dataType'] && is_array($config['dataset'][$col['dataKey']]) && $config['dataset'][$col['dataKey']]['dataType']) {
                $col['dataType'] = $config['dataset'][$col['dataKey']]['dataType'];
            }
            if (!$col['dataType']) {
                $col['dataType'] = 'string';
            }
        }
        return $config;
    }
}
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
    /** Process the configuration files (process language and actions column)
     *
     * @static
     * @param $config : configuration file content
     */
    static public function process($config) {
        $format = Format::forge();
        if (isset($config['ui'])) {
            if (is_array($config['ui']['grid']['columns']) && is_array($config['ui']['actions'])) {
                $key = array_search('actions', $config['ui']['grid']['columns']);
                if ($key !== false) {
                    $config['ui']['grid']['columns'][$key] = array('actions' => $config['ui']['actions']);
                }
            }

            $columns = &$config['ui']['grid']['columns'];
            $config['ui']['slidersChange'] = 'function(e, rapport) { $.nos.saveUserConfiguration("'.$config['configuration_id'].'.ui.splitters", rapport) }';
            //$config['ui']['columnVisibilityChange'] = 'function() { console.log($("#mp3grid").mp3grid("exportSettings")); /*$.nos.saveUserConfiguration("cms_blog", {toto: "toto"});*/ }';
        } else {
            $columns = &$config['columns'];
            //$config['columnVisibilityChange'] = 'function() { console.log($("#mp3grid").mp3grid("exportSettings")); /*$.nos.saveUserConfiguration("cms_blog", {toto: "toto"});*/ }';
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
            if (is_array($columns[$i]) && isset($columns[$i]['actions'])) {
                $actions = $columns[$i]['actions'];
                $columns[$i] = array(
                    'headerText' => '',
                    'cellFormatter' => 'function(args) {
						if ($.isPlainObject(args.row.data)) {
						    var dropDown = args.$container.parent()
                            .addClass("buttontd ui-state-default")
                            .hover(
                                function() {
                                    dropDown.parent().addClass("ui-state-hover");
                                },
                                function() {
                                    dropDown.parent().removeClass("ui-state-hover");
                                }
                            )
                            .find("div");

                        $("<span></span>")
                            .addClass("ui-icon ui-icon-triangle-1-s")
                            .appendTo(dropDown);

                        var ul = $("<ul></ul>").appendTo("body");
                        items = '.$format->to_json($actions).';

                        $.each(items, function() {
                            var action = this;
                            $("<li><a href=\"#\"></a></li>")
                                .appendTo(ul)
                                .find("a")
                                .text(action.label)
                                .click(function(e) {
                                    e.preventDefault();
                                    action.action(args);
                                })
                        });

                        ul.wijmenu({
                            trigger : dropDown,
                            triggerEvent : "mouseenter",
                            orientation : "vertical",
                            showAnimation : {Animated:"slide", duration: 50, easing: null},
                            hideAnimation : {Animated:"hide", duration: 0, easing: null},
                            position : {
                                my        : "right top",
                                at        : "right bottom",
                                collision : "flip",
                                offset    : "0 0"
                            }
                        });
							return true;
						}
					}',
                    'allowSizing' => false,
                    'width' => 20,
                    'showFilter' => false,
                );
                $main_column = array(
                    'headerText' => '',
                    'cellFormatter' => 'function(args) {
  						if ($.isPlainObject(args.row.data)) {
  						    args.$container.parent()
  						    .addClass("buttontd ui-state-default")
                            .hover(
                                function() {
                                    args.$container.parent().addClass("ui-state-hover");
                                },
                                function() {
                                    args.$container.parent().removeClass("ui-state-hover");
                                }
                            )
                            .click(function(e) {
                                e.preventDefault();
                                fct = '.$actions[0]['action'].';
                                fct(args);
                            })
                            .find("div")
                            .text('.json_encode($actions[0]['label']).');

                            return true;
                        }
                    }',
                    'allowSizing' => false,
                    'width' => isset($actions[0]['width']) ? $actions[0]['width'] : 60,
                    'showFilter' => false,
                );
                array_splice($columns, $i, 0, array($main_column));
                /* It is possible to merge the two columns by using wijgrid bands...
                Disabled for ui choice...

                $columns[$i] = array(
                    'headerText' => 'Actions',
                    'height'     => '15',
                    'columns' => array(
                        $main_column,
                        $columns[$i],
                    )
                );
                */
            }
        }
        return $config;
    }
}
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

use \Security;

class Layout {
    public static function forge($content, $object, $fieldset, $globals = array()) {
        $ret = '';
        if ($content['type'] == 'main') {
            $attr = array(
                'class' => 'page myPage myBody',
            );
            if ($content['attributes']) {
                $attr = array_merge($attr, $content['attributes']);
            }

            $ret = '<div '.array_to_attr($attr).'>'.$fieldset->open('admin/cms_blog/form/edit/'.$object->blog_id);
            $ret .= static::getContent($content, $object, $fieldset, $globals);
            $ret .= $fieldset->close()
                    .'</div>';
        }

        if ($content['type'] == 'line') {
            $attr = array(
                'class' => 'line',
            );
            if ($content['attributes']) {
                $attr = array_merge($attr, $content['attributes']);
            }

            $ret = '<div '.array_to_attr($attr).'>';
            $ret .= static::getContent($content, $object, $fieldset, $globals);
            $ret .= '</div>';
        }

        if ($content['type'] == 'column') {
            $attr = array(
                'class' => 'unit col c'.$content['width'],
            );
            if ($content['attributes']) {
                $attr = array_merge($attr, $content['attributes']);
            }

            $ret = '<div '.array_to_attr($attr).'>';
            $ret .= static::getContent($content, $object, $fieldset, $globals);
            $ret .= '</div>';
        }

        if ($content['type'] == 'expander') {
            $attr = array(
                'class' => 'expander fieldset',
            );
            if ($content['attributes']) {
                $attr = array_merge($attr, $content['attributes']);
            }

            $ret = '<div '.array_to_attr($attr).'>';
            $ret .= '<h3>'.Security::xss_clean($content['title']).'</h3>';
            $ret .= '<div style="overflow: visible;">'.static::getContent($content, $object, $fieldset, $globals).'</div>';
            $ret .= '</div>';
        }

        if ($content['type'] == 'field') {
            $ret = $fieldset->field($content['name']);
            if ($content['template']) {
                $ret = $ret->set_template($content['template']);
            }
            if ($content['attributes']) {
                $ret = $ret->set_attribute($content['attributes']);
            }

            $ret = $ret->build();
        }

        if ($content['type'] == 'accordion') {
            $attr = array(
                'class' => 'accordion',
            );
            if ($content['attributes']) {
                $attr = array_merge($attr, $content['attributes']);
            }

            $ret = '<div '.array_to_attr($attr).'>';
            for ($i = 0; $i < count($content['items']); $i++) {
                $content['items'][$i]['type'] = 'accordion_item';
            }
            $ret .= static::getContent($content, $object, $fieldset, $globals);
            $ret .= '</div>';
        }

        if ($content['type'] == 'accordion_item') {
            $attr = array();
            if ($content['attributes']) {
                $attr = array_merge($attr, $content['attributes']);
            }

            $ret = '<div '.array_to_attr($attr).'>';
            if (!isset($content['items'])) {
                $content['items'] = array();
            }
            if (!isset($content['layout'])) {
                $content['layout'] = '<h3>{title}</h3><div>{content}</div>';
            }
            if ($content['title']) {
                $content['items']['title'] = array('type' => 'node', 'layout' => $content['title']);
            }
            if ($content['content']) {
                $content['items']['content'] = $content['content'];
            }



            $ret .= static::getContent($content, $object, $fieldset, $globals);
            $ret .= '</div>';
        }

        if ($content['type'] == 'node') {
            $ret = static::getContent($content, $object, $fieldset, $globals);
        }

        if ($content['type'] == 'standard_layout') {
            $menu_items = array();
            $menu_items[] = array(
                                'type' => 'line',
                                'layout' => '{save} &nbsp; or &nbsp; <a href="#" onclick="javascript:$.nos.tabs.close();return false;">Cancel</a>',
                                'attributes' => array(
                                    'style' => 'text-align: center;',
                                ),
                            );
            if ($content['menu']) {
                $menu_items[] = array(
                                    'type' => 'line',
                                    'attributes' => array(
                                        'style' => 'margin-top: 20px;'
                                    ),
                                    'items' => array(
                                        array(
                                            'type' => 'accordion',
                                            'items' => $content['menu'],
                                        )
                                    )
                                );
            }

            $tab = array(
                'type' => 'main',
                'items' => array(
                    array(
                        'type' => 'column',
                        'width' => 1,
                    ),
                    array(
                        'type' => 'column',
                        'width' => 7,
                        'items' => $content['content'],
                    ),
                    array(
                        'type' => 'column',
                        'width' => 3,
                        'items' => $menu_items,
                    ),
                ),
            );


            $ret = static::forge($tab, $object, $fieldset, $globals);
        }

        return $ret;
    }

    public static function getContent($content, $object, $fieldset, $globals) {
        if ($content['partial']) {
            return render($content['partial'], array('content' => $content, 'object' => $object, 'fieldset' => $fieldset, 'globals' => $globals, 'params' => $content['params'] ? $content['params'] : array()));
        }
        if ($content['items'] && $content['layout']) {
            return static::replaceLayout($content['layout'], $object, $fieldset, $globals, $content['items']);
        }
        if ($content['items']) {
            $ret = '';
            foreach ($content['items'] as $item) {
                $ret .= static::forge($item, $object, $fieldset, $globals);
            }
            return $ret;
        }
        if ($content['layout']) {
            return static::replaceLayout($content['layout'], $object, $fieldset, $globals);
        }
    }

    public static function replaceLayout($layout, $object, $fieldset, $globals, $items = array()) {
        if (is_string($layout)) {
            $pattern = '/\{([^\{]*)\}/';
            preg_match_all($pattern, $layout, $matches);

            for ($i = 0; $i < count($matches[1]); $i++) {
                $match = $matches[1][$i];
                if (isset($globals[$match])) {
                    $content = static::forge($globals[$match], $object, $fieldset, $globals);
                } else if (isset($items[$match])) {
                    $content = static::forge($items[$match], $object, $fieldset, $globals);
                } else {
                    $field = $fieldset->field($match);
                    if ($field) {
                        $content = $field->build();
                    } else {
                        $content = $object->{$match};
                    }
                }
                $layout = str_replace('{'.$match.'}', $content, $layout);
            }
        }

        return $layout;
    }
}
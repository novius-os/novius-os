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

class Widget {

    public static function date($field, $value = null, array $attributes = array()) {
        
        if (is_array($field))
        {
            $attributes = $field;
            ! array_key_exists('value', $attributes) and $attributes['value'] = '';
        }
        else
        {
            $attributes['name'] = (string) $field;
            $attributes['value'] = (string) $value;
        }
        
        $attrs  = $attributes;
        $output = '';
        
        $attrs['name'] = $attributes['name'].'_year';
        $attrs['value'] = substr($attributes['value'], 0, 4);
        $output .= html_tag('input', Form::attr_to_string($attrs));
        
        $attrs['name'] = $attributes['name'].'_month';
        $attrs['value'] = substr($attributes['value'], 5, 2);
        $output .= html_tag('input', Form::attr_to_string($attrs));
        
        $attrs['name'] = $attributes['name'].'_day';
        $attrs['value'] = substr($attributes['value'], 8, 2);
        $output .= html_tag('input', Form::attr_to_string($attrs));
      
        return $output;
    }
}

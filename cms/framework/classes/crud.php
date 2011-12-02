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

class Crud {


    /**
     * Set a Model's properties as fields on a Fieldset, which will be created with the Model's
     * classname if none is provided.
     *
     * @param   string
     * @param   Fieldset|null
     * @return  Fieldset
     */
    public static function set_fields($obj, $fieldset = null)
    {
        static $_generated = array();

        $class = is_object($obj) ? get_class($obj) : $obj;
        if (is_null($fieldset))
        {
            $fieldset = \Fieldset::instance($class);
            if ( ! $fieldset)
            {
                $fieldset = \Fieldset::forge($class);
            }
        }

        ! array_key_exists($class, $_generated) and $_generated[$class] = array();
        if (in_array($fieldset, $_generated[$class], true))
        {
            return $fieldset;
        }
        $_generated[$class][] = $fieldset;

        $properties = is_object($obj) ? $obj->properties() : $class::properties();
        foreach ($properties as $p => $settings)
        {
            if (isset($settings['form']['options']))
            {
                foreach ($settings['form']['options'] as $key => $value)
                {
                    $settings['form']['options'][$key] = __($value) ?: $value;
                }
            }

            $label       = isset($settings['label']) ? $settings['label'] : $p;
            $attributes  = isset($settings['form']) ? $settings['form'] : array();
            $field       = $fieldset->add($p, $label, $attributes);
            if ( ! empty($settings['validation']))
            {
                foreach ($settings['validation'] as $rule => $args)
                {
                    if (is_int($rule) and is_string($args))
                    {
                        $args = array($args);
                    }
                    else
                    {
                        array_unshift($args, $rule);
                    }

                    call_user_func_array(array($field, 'add_rule'), $args);
                }
            }
        }

        return $fieldset;
    }
}
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

use Event;
use Arr;

class Model extends \Orm\Model {

    protected static $_has_many = array();

    protected static $_has_wysiwygs = array();

    public static function _init() {
        Event::trigger(get_called_class().'.properties', get_called_class());

        if (static::$_has_wysiwygs) {
            static::$_has_many['wysiwygs'] = array(
                'key_from' => static::$_primary_key[0],
                'model_to' => 'Cms\Model_Wysiwyg',
                'key_to' => 'wysiwyg_foreign_id',
                'cascade_save' => true,
                'cascade_delete' => false,
                'conditions'     => array(
                    'where' => array(
                        array('wysiwyg_join_table', '=', \DB::expr('"'.static::$_table_name.'"') ),
                    ),
                )
            );
        }
    }
    
    public static function add_properties($properties) {
        static::$_properties = Arr::merge(static::$_properties, $properties);
    }

    /**
     * Method for use with Fieldset::add_model()
     *
     * @param   Fieldset     Fieldset instance to add fields to
     * @param   array|Model  Model instance or array for use to repopulate
     */
    public static function build_fieldset($form, $instance = null)
    {
        //Observer_Validation::set_fields($instance instanceof static ? $instance : get_called_class(), $form);
        //$instance and $form->repopulate($instance);
    }

    public function __set($name, $value) {
        $arr_name = explode('->', $name);
        if (count($arr_name) > 1) {
            if ($arr_name[0] == 'wysiwyg') {
                $key = $arr_name[1];
                $w_keys = array_keys($this->wysiwygs);
                for ($ii = 0; $ii < count($this->wysiwygs); $ii++) {
                    $i = $w_keys[$ii];
                    if ($this->wysiwygs[$i]->wysiwyg_key == $key) {
                        array_splice ($arr_name, 0, 2);
                        return $this->wysiwygs[$i]->{implode('->', $arr_name)} = $value;
                    }
                }
            }
            $obj = $this;
            for ($i = 0; $i < count($arr_name); $i++) {
                $obj = &$obj->{$arr_name[$i]};
            }
            return $obj = $value;
        }
        if ($name == 'id') {
            return $this->{static::$_primary_key[0]} = $value;
        }
        return parent::__set($name, $value);
    }

    public function & __get($name) {
        $arr_name = explode('->', $name);
        if (count($arr_name) > 1) {
            if ($arr_name[0] == 'wysiwyg') {
                $key = $arr_name[1];
                $w_keys = array_keys($this->wysiwygs);
                for ($ii = 0; $ii < count($this->wysiwygs); $ii++) {
                    $i = $w_keys[$ii];
                    if ($this->wysiwygs[$i]->wysiwyg_key == $key) {
                        array_splice ($arr_name, 0, 2);
                        return $this->wysiwygs[$i]->{implode('->', $arr_name)};
                    }
                }
                exit();
            }
            $obj = $this;
            for ($i = 0; $i < count($arr_name); $i++) {
                $obj = $obj->{$arr_name[$i]};
            }
            return $obj;
        }
        if ($name == 'id') {
            return $this->{static::$_primary_key[0]};
        }
        return parent::__get($name);
    }



}

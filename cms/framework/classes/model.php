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

    public static function _init() {
        Event::trigger(get_called_class().'.properties', get_called_class());
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

}

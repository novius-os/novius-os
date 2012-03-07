<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Cms\Blog;

class Controller_Admin_Form extends \Cms\Controller_Generic_Admin {


    public function action_edit($id = false) {
        if ($id === false) {
            $object = null;
        } else {
            $object = Model_Blog::find('first', array('where' => array('blog_id' => $id)));
        }
        return \View::forge($this->config['views']['edit'], array(
            'object'   => $object,
            'fieldset' => static::fieldset($this->config['fields']($object), $object)->set_config('field_template', '<tr><th>{label}{required}</th><td class="{error_class}">{field} {error_msg}</td></tr>'),
        ), false);
    }

    public static function fieldset($fields, $object) {

        \Config::load('app::templates', true);
        $templates = array();
        foreach (\Config::get('app::templates', array()) as $tpl_id => $template) {
            $templates[(int) substr($tpl_id, 3)] = $template['title'];
        }

        //$fields = $this->config['fields']($object);

        //$editable_fields = array_diff(array_keys(Model_Blog::properties()), Model_Blog::primary_key());

        $fieldset = \Fieldset::build_from_config($fields, $object, array(
            'save' => function($data) use ($object, $fields) {
                //print_r($object);
                $categories = \Input::post('categories');
                if ($categories == false) {
                    $categories = array();
                }
                $object->updateCategoriesById($categories);

            }
        ));

        $fieldset->js_validation();
        return $fieldset;
    }
}
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

    public function after($response) {

        \Asset::css('http://yui.yahooapis.com/3.3.0/build/cssreset/reset-min.css', array(), 'css');

        \Asset::add_path('static/cms/js/vendor/wijmo/');
        \Asset::css('aristo/jquery-wijmo.css', array(), 'css');
        \Asset::css('jquery.wijmo-complete.all.2.0.0b2.min.css', array(), 'css');

        \Asset::add_path('static/cms/');
        \Asset::css('base.css', array(), 'css');
        \Asset::css('laGrid.css', array(), 'css');
        \Asset::css('form.css', array(), 'css');

        return parent::after($response);
    }

    public function action_edit($id = false) {
        if ($id === false) {
            $object = new Model_Blog();
        } else {
            $object = Model_Blog::find('first', array('related' => array('wysiwygs'), 'where' => array('blog_id' => $id)));
        }
        $body = \View::forge('cms_blog::form/edit', array(
            'object'   => $object,
            'fieldset' => static::fieldset($object)->set_config('field_template', '<tr><th>{label}{required}</th><td class="{error_class}">{field} {error_msg}</td></tr>'),
        ), false);

        $this->template->set('body', $body, false);

        return $this->template;
    }

    public static function fieldset($object) {

        \Config::load('app::templates', true);
        $templates = array();
        foreach (\Config::get('app::templates', array()) as $tpl_id => $template) {
            $templates[(int) substr($tpl_id, 3)] = $template['title'];
        }

        $fields = array (
            'blog_id' => array (
                'label' => 'Id: ',
				'widget' => 'text',
				'editable' => false,
            ),
            'blog_date_debut_publication' => array (
				'label' => 'Published',
				'form' => array(
					'type' => 'checkbox',
					'value' => date('Y/m/d'),
				),
            ),
            'blog_titre' => array (
                'label' => 'Titre: ',
                'form' => array(
                    'type' => 'text',
                ),
            ),
            'blog_auteur' => array(
                'label' => 'Alias: ',
                'form' => array(
                    'type' => 'text',
                ),
            ),
            'author->user_fullname' => array(
                'label' => 'Auteur: ',
				'widget' => 'text',
				'editable' => false,
            ),
            'wysiwyg->content->wysiwyg_text' => array(
                'label' => 'Contenu',
                'widget' => 'wysiwyg',
                'form' => array(
                    'style' => 'width: 100%; height: 500px;',
                 ),
            ),
			'media->thumbnail->medil_media_id' => array(
				'label' => '',
				'widget' => 'media',
				'form' => array(
					'title' => 'Thumbnail',
				),
			),
            'blog_date_creation' => array(
                'label' => 'Created at:',
                'widget' => 'date_select',
            ),
            'blog_lu' => array(
                'label' => 'Read',
                'form' => array(
                    'type' => 'text',
					'size' => '4',
                ),
            ),
            'save' => array(
                'label' => '',
                'form' => array(
                    'type' => 'submit',
                    'value' => 'Save',
                ),
				'editable' => false,
            ),
        );

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
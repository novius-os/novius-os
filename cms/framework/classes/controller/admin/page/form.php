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

class Controller_Admin_Page_Form extends Controller {

    public function action_edit($id) {
        return \View::forge('cms::admin/page/form/edit', array(
			'page' => Model_Page_Page::find($id),
			'fieldset' => static::fieldset($id)->set_config('field_template', '<tr><th>{label}{required}</th><td class="{error_class}">{field} {error_msg}</td></tr>'),
		), false);
    }

	public static function fieldset($id) {

        \Config::load(APPPATH.'data'.DS.'config'.DS.'templates.php', 'templates');
		$templates = array();
		foreach (\Config::get('templates', array()) as $tpl_key => $template) {
			$templates[$tpl_key] = $template['title'];
		}

        $fields = array (
            'page_id' => array (
                'label' => 'ID: ',
                'widget' => 'text',
            ),
            'page_title' => array (
                'label' => 'Title: ',
				'form' => array(
					'type' => 'text',
				),
            ),
			'page_template' => array(
				'label' => 'Template: ',
				'form' => array(
					'type' => 'select',
					'options' => $templates,
				),
			),
			'page_published' => array(
				'label' => 'Published',
				'form' => array(
					'type' => 'checkbox',
					'value' => '1',
				),
			),
			'page_virtual_name' => array(
				'label' => 'Slug: ',
				'form' => array(
					'type' => 'text',
					'size' => 20,
				),
			),
			'page_meta_title' => array(
				'label' => 'SEO title: ',
				'form' => array(
					'type' => 'text',
					'size' => 26,
				),
			),
			'page_meta_description' => array(
				'label' => 'Description: ',
				'form' => array(
					'type' => 'textarea',
					'cols' => 26,
					'rows' => 6,
				),
			),
			'page_meta_keywords' => array(
				'label' => 'Keywords: ',
				'form' => array(
					'type' => 'textarea',
					'cols' => 26,
					'rows' => 3,
				),
			),
			'page_meta_noindex' => array(
				'label' => "Don't index on search engines",
				'form' => array(
					'type' => 'checkbox',
					'value' => '1',
				),
			),
			'page_menu' => array(
				'label' => "Show in the menu",
				'form' => array(
					'type' => 'checkbox',
					'value' => '1',
				),
			),
			'page_menu_title' => array(
				'label' => 'Menu title: ',
				'form' => array(
					'type' => 'text',
					'size' => 26,
				),
			),
			'page_external_link' => array(
				'label' => 'URL: ',
				'form' => array(
					'type' => 'text',
					'size' => 60
				),
			),
			'page_external_link_type' => array(
				'label' => 'Target: ',
				'form' => array(
					'type' => 'select',
					'options' => array(
						Model_Page_Page::EXTERNAL_TARGET_NEW   => 'New window',
						Model_Page_Page::EXTERNAL_TARGET_POPUP => 'Popup',
						Model_Page_Page::EXTERNAL_TARGET_SAME  => 'Same window',
					),
				),
			),
			'page_type' => array(
				'label' => 'Type: ',
				'form' => array(
					'type' => 'select',
					'options' => array(
						Model_Page_Page::TYPE_CLASSIC => 'Page',
                        Model_Page_Page::TYPE_FOLDER => 'Folder / Chapter',
                        Model_Page_Page::TYPE_INTERNAL_LINK => 'Internal link',
                        Model_Page_Page::TYPE_EXTERNAL_LINK => 'External link',
					),
				),
			),
			'page_lock' => array(
				'label' => 'Lock status: ',
				'form' => array(
					'type' => 'select',
					'options' => array(
						0 => 'Unlocked',
						1 => 'Deletion',
						2 => 'Modification',
					),
				),
			),
			'page_cache_duration' => array(
				'label' => 'Regenerate every',
				'form' => array(
					'type' => 'text',
					'size' => 4,
				),
			),
			'save' => array(
				'label' => '',
				'form' => array(
					'type' => 'submit',
					'value' => 'Save',
					'class' => 'primary',
					'data-icon' => 'check',
				),
			),
        );

        $page = Model_Page_Page::find($id);

		$editable_fields = array_diff(array_keys(Model_Page_Page::properties()), Model_Page_Page::primary_key());

		$template_key = \Input::post('page_template', $page->page_template);
		if (!empty($template_id)) {
            $templates = \Config::get('templates', array());
            $template_key and $data = \Config::get('templates.'.$template_key, array(
				'layout' => array(),
			));
		}

		$fieldset = \Fieldset::build_from_config($fields, $page, array(
			'complete' => function($data) use ($page, $fields, $editable_fields) {

				try {
					foreach ($data as $name => $value) {
						if (in_array($name, $editable_fields)) {
							$page->$name = $value;
						}
					}
					foreach ($fields as $name => $f) {
						if (empty($data[$name]) && \Arr::get($f, 'form.type', null) == 'checkbox') {
							$page->$name = 0;
						}
					}


					// Save wysiwyg after the page->save(), because we need page_id on creation too
					// @todo change this to use the provider from Cms\Model
					foreach (\Input::post('wysiwyg', array()) as $name => $content) {
						$page->{'wysiwyg->'.$name.'->wysiwyg_text'} = $content;
					}

                    $page->save();

					$body = array(
						'notify' => 'Page edited successfully.',
						'listener_fire' => array('cms_page.refresh' => true),
					);
				} catch (\Exception $e) {
					$body = array(
						'error' => \Fuel::$env == \Fuel::DEVELOPMENT ? $e->getMessage() : 'An error occured.',
					);
				}

				\Response::json($body);
			}
		));
		$fieldset->js_validation();
		return $fieldset;
	}
}
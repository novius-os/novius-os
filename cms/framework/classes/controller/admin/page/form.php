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
        return \View::forge('cms::admin/page/form_edit', array(
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
			'page_virtual_name' => array(
				'label' => 'URL: ',
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
				'label' => "Shows in the menu",
				'form' => array(
					'type' => 'checkbox',
					'value' => '1',
				),
			),
			'page_menu_title' => array(
				'label' => 'What\'s the page called in the menu: ',
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
						Model_Page_Page::LOCK_UNLOCKED => 'Unlocked',
						Model_Page_Page::LOCK_DELETION => 'Deletion',
						Model_Page_Page::LOCK_EDITION  => 'Modification',
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
                    'tag' => 'button',
					'value' => 'Save',
					'class' => 'primary',
					'data-icon' => 'check',
				),
			),
        );

        $page = Model_Page_Page::find($id);

		$fieldset = \Fieldset::build_from_config($fields, $page, array(
            'success' => function() {
                return array(
                    'notify' => 'Page sucessfully saved.',
                    'fireEvent' => array(
                        'event' => 'reload',
                        'target' => 'cms_page',
                    ),
                );
            }
        ));
		$fieldset->js_validation();
		return $fieldset;
	}

    protected static function  _get_page_with_permission($page_id, $permission) {
        if (empty($page_id)) {
            throw new \Exception('No page specified.');
        }
        $page = Model_Page_Page::find($page_id);
        if (empty($page)) {
            throw new \Exception('Page not found.');
        }
        if (!static::check_permission_action('delete', 'controller/admin/page/mp3grid/list', $page)) {
            throw new \Exception('Permission denied');
        }
        return $page;
    }

	public function action_delete_page($page_id = null) {
        try {
            $page = static::_get_page_with_permission($page_id, 'delete');
            return \View::forge('cms::admin/page/page_delete', array(
                'page' => $page,
            ));
        } catch (\Exception $e) {
            // Easy debug
            if (\Fuel::$env == \Fuel::DEVELOPMENT && !\Input::is_ajax()) {
                throw $e;
            }
			$body = array(
				'error' => $e->getMessage(),
			);
            \Response::json($body);
		}
    }

	public function action_delete_page_confirm() {
        try {
            $page_id = \Input::post('id');
            // Allow GET for easier dev
            if (empty($page_id) && \Fuel::$env == \Fuel::DEVELOPMENT) {
                $page_id = \Input::get('id');
            }

            $page = static::_get_page_with_permission($page_id, 'delete');
            // Delete all languages by default
            $lang = \Input::post('lang', 'all');

            // Delete children for all languages
            if ($lang == 'all') {
                // Children will be deleted recursively (with the 'after_delete' event from the Tree behaviour)
                // Optimised operation for deleting all languages
                $page->delete_all_lang();
            } else {
                // Search for the appropriate page
                if ($lang != 'all' && $page->get_lang() != $lang) {
                    $page = $page->find_lang($lang);
                }

                if (empty($page)) {
                    throw new \Exception(strtr(__('The page has not been found in the requested language {language}'), array(
                        '{language}' => $lang,
                    )));
                }

                // Reassigns common_id if this item is the main language (with the 'after_delete' event from the Translatable behaviour)
                // Children will be deleted recursively (with the 'after_delete' event from the Tree behaviour)
                $page->delete();
            }

			$body = array(
				'notify' => 'Page successfully deleted.',
                'fireEvent' => array(
	                'event' => 'reload',
                    'target' => 'cms_page',
                ),
			);

        } catch (\Exception $e) {
            // Easy debug
            if (\Fuel::$env == \Fuel::DEVELOPMENT && !\Input::is_ajax()) {
                throw $e;
            }
			$body = array(
				'error' => $e->getMessage(),
			);
		}

        \Response::json($body);
    }
}
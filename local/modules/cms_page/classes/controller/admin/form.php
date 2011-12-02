<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Cms\Page;

class Controller_Admin_Form extends \Cms\Controller_Generic_Admin {

	public function after($response) {

		\Asset::css('http://yui.yahooapis.com/3.3.0/build/cssreset/reset-min.css', array(), 'css');

		\Asset::add_path('static/cms/js/jquery/wijmo/');
		\Asset::css('aristo/jquery-wijmo.css', array(), 'css');
		\Asset::css('jquery.wijmo-complete.1.5.0.css', array(), 'css');
		\Asset::css('jquery.wijmo-open.1.5.0.css', array(), 'css');

		\Asset::add_path('static/cms/');
		\Asset::css('base.css', array(), 'css');
		\Asset::css('laGrid.css', array(), 'css');
		\Asset::css('mystyle.css', array(), 'css');

		return parent::after($response);
	}

    public function action_edit($id) {
        $body = \View::forge('cms_page::form/edit', array(
			'page' => Model_Page::find($id),
			'fieldset' => static::fieldset($id)->set_config('field_template', '<tr><th>{label}{required}</th><td class="{error_class}">{field} {error_msg}</td></tr>'),
		), false);

		$this->template->set('body', $body, false);

        return $this->template;
    }

	public static function fieldset($id) {

        $fields = array (
            'page_id' => array (
                'label' => 'ID',
                'widget' => 'text',
            ),
            'page_titre' => array (
                'label' => 'Title',
                'type' => 'text',
            ),
			'page_gab_id' => array(
				'label' => 'Template',
				'form' => array(
					'type' => 'select',
					'options' => array(
						1 => 'Gabarit 1',
						2 => 'Gabarit 2',
						3 => 'Gabarit 3',
						4 => 'Gabarit 4',
					),
				),
			),
			'page_publier' => array(
				'label' => "Published",
				'form' => array(
					'type' => 'checkbox',
					'value' => '1',
				),
			),
			'page_nom_virtuel' => array(
				'label' => 'Slug',
				'form' => array(
					'type' => 'text',
					'size' => 20,
				),
			),
			'page_titre_reference' => array(
				'label' => 'SEO title',
				'form' => array(
					'type' => 'text',
					'size' => 26,
				),
			),
			'page_description' => array(
				'label' => 'Description',
				'form' => array(
					'type' => 'textarea',
					'cols' => 26,
					'rows' => 6,
				),
			),
			'page_keywords' => array(
				'label' => 'Keywords',
				'form' => array(
					'type' => 'textarea',
					'cols' => 26,
					'rows' => 3,
				),
			),
			'page_noindex' => array(
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
			'page_titre_menu' => array(
				'label' => 'Menu title',
				'form' => array(
					'type' => 'text',
					'size' => 26,
				),
			),
			'save' => array(
				'label' => '',
				'form' => array(
					'type' => 'submit',
					'value' => 'Save',
				),
			),
        );

		$template_id = \Input::post('page_gab_id', null);
		\Config::load('templates', true);
		$template_id and $data = \Config::get('templates.id-'.$template_id, null);
		$data and Model_Page::set_wysiwyg(array_keys($data['layout']));

        $page = Model_Page::find($id);

		$fieldset = \Fieldset::build_from_config($fields, $page, array(
			'complete' => function($data) use ($page, $fields) {

				try {
					foreach ($data as $name => $value) {
						if (substr($name, 0, 5) == 'page_' && $name != 'page_id') {
							$page->$name = $value;
						}
					}
					foreach ($fields as $name => $f) {
						if (empty($data[$name]) && \Arr::get($f, 'form.type', null) == 'checkbox') {
							$page->$name = 0;
						}
					}
					foreach (\Input::post('wysiwyg', array()) as $name => $content) {
						$wysiwyg = $page->wysiwyg($name);
						$wysiwyg->wysiwyg_text = $content;
						$wysiwyg->save();
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

				$response = \Response::forge(\Format::forge()->to_json($body), 200, array(
					'Content-Type' => 'application/json',
				));
				$response->send(true);
				exit();
			}
		));
		$fieldset->js_validation();
		return $fieldset;
	}
}
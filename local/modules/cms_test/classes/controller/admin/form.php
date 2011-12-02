<?php
/**
 * NOVIUS OS - Web OS for digital communication
 * 
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Test;

class Controller_Admin_Form extends \Cms\Controller_Noviusos_Noviusos {

	public function action_index() {
		
		\Config::load('cms_test::form', true);
		$widgets = \Config::get('cms_test::form', array());
		
		$fieldset = \Fieldset::build_from_config($widgets);
				
		$body = \View::forge('form', array(
			'form' => $fieldset->build('/admin/cms_test/form'),
		), false);
		$this->template->set('body', $body, true);
		
		return $this->template;
	}
}
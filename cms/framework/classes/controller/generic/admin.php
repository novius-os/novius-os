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

class Controller_Generic_Admin extends \Fuel\Core\Controller_Template {

    public $template = 'cms::templates/html5';


    public function before($response = null) {
        return parent::before($response);
    }


	public function after($response) {
		foreach (array(
			'title' => 'Administration',
			'base' => \Uri::base(false),
			'require'  => 'static/cms/js/vendor/requirejs/require.js',
		) as $var => $default) {
			if (empty($this->template->$var)) {
				$this->template->$var = $default;
			}
		}
        $ret = parent::after($response);
		$this->template->set(array(
			'css' => \Asset::render('css'),
			'js'  => \Asset::render('js'),
		), false, false);
        return $ret;
	}
}
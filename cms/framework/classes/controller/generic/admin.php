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

class Controller_Generic_Admin extends Controller_Template_Extendable {

    public $template = 'cms::templates/html5';


    public function before($response = null) {
        $ret = parent::before($response);
        $location = $this->getLocation();
        list($module_name, $file_name) = $location;
        if ($module_name == 'cms' && isset($location[2])) {
            $module_name = 'nos_'.$location[2]; // this hack should be temporary until we figure out how to correctly implement native modules...
        }

        if ($module_name != 'cms' && !Permission::check($module_name, 'access')) {
            throw new \Exception('You don\'t have access to module '.$module_name.'!');
        }

        //\Debug::dump($module_name, Permission::check($module_name, 'access'));
        return $ret;
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
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

use Fuel\Core\Config;
use Fuel\Core\View;

class Controller_Overview_Plugins extends Controller_Generic_Admin {

    public function action_index() {
        $this->template->body = View::forge('overview/plugins');

        Config::load('modules', true);
        $this->template->body->set('plugins', Config::get('modules', array()));
    }
}
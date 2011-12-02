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

use Fuel\Core\Input;

use Fuel\Core\View;

use Date;

class Controller_Ui_Date extends \Fuel\Core\Controller_Template {

    public $template = 'ui/date';

    public function action_index($name, $value = '')
    {
        $this->template->set('name', $name, false);
        $this->template->set('value', $value);
    }
}
<?php
/**
 * NOVIUS OS - Web OS for digital communication
 * 
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Labs_Codesource;

use Cms\Controller;

class Controller_Front extends Controller {
    public function action_main($params) {
        $this->response->body = $params['codesource'];
    }
}

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

class Controller_404 extends \Controller {

    public function action_front() {
        $this->response->body = \View::forge('errors/404_front');
    }

    public function action_admin() {
        $this->response->body = \View::forge('errors/404_admin');
    }
}
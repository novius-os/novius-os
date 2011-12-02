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

use Fuel\Core\Uri;

class Model_Wysiwyg extends Model {
    protected static $_table_name = 'nov_contenu_wysiwyg';
    protected static $_primary_key = array('wys_id');

    public function content() {
        $current_controller = \Request::active()->controller_instance;
        return \Cms::parse_wysiwyg($this->wys_contenu, $current_controller);
    }
}

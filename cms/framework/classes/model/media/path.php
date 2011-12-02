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

class Model_Media_Path extends Model {
    protected static $_table_name = 'cms_media_path';
    protected static $_primary_key = array('medip_id');

    /**
     * Properties
     * medip_id
     * medip_parent_id
     * medip_path
     * medip_title
     */
}

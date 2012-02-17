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

class Model_Media_Folder extends \Orm\Model {
    protected static $_table_name = 'os_media_folder';
    protected static $_primary_key = array('medif_id');

	protected static $_has_many = array(
		'childrens' => array(
			'key_from'       => 'medif_id',
			'model_to'       => '\Cms\Model_Media_Folder',
			'key_to'         => 'medif_parent_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

	protected static $_belongs_to = array(
		'parent' => array(
			'key_from'       => 'medif_parent_id',
			'model_to'       => '\Cms\Model_Media_Folder',
			'key_to'         => 'medif_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

    public function path($file = '') {
        return APPPATH.'media/'.$this->medif_path.$file;
    }

    /**
     * Properties
     * medif_id
     * medif_parent_id
     * medif_path
     * medif_title
     */
}

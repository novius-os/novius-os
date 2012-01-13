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

class Model_Media_Link extends \Orm\Model {
    protected static $_table_name = 'os_media_link';
    protected static $_primary_key = array('medil_id');

	public static $_belongs_to = array(
		'media' => array(
			'key_from' => 'medil_media_id',
			'model_to' => 'Cms\Model_Media_Media',
			'key_to' => 'media_id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
	);
}

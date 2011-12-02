<?php
/**
 * NOVIUS OS - Web OS for digital communication
 * 
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Cms\Page;

class Model_Root extends \Orm\Model {
    protected static $_table_name = 'cms_racine';
    protected static $_primary_key = array('rac_id');

	protected static $_has_many = array(
		'pages' => array(
			'key_from'       => 'rac_id',
			'model_to'       => '\Cms\Page\Model_Page',
			'key_to'         => 'page_rac_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

    /**
     * Creates a new query with optional settings up front
     *
     * @param   array
     * @return  Query
     */
    public static function query($options = array())
    {
        return parent::query($options + array('order_by' => array('rac_rang')));
    }
}
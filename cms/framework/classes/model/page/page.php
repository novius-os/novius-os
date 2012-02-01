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

class Model_Page_Page extends \Cms\Model {
    protected static $_table_name = 'os_page';
    protected static $_primary_key = array('page_id');

	protected static $_has_many = array(
		'childrens' => array(
			'key_from'       => 'page_id',
			'model_to'       => '\Cms\Model_Page_Page',
			'key_to'         => 'page_parent_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

	protected static $_belongs_to = array(
		'parent' => array(
			'key_from'       => 'page_parent_id',
			'model_to'       => '\Cms\Model_Page_Page',
			'key_to'         => 'page_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'racine' => array(
			'key_from'       => 'page_root_id',
			'model_to'       => '\Cms\Model_Page_Root',
			'key_to'         => 'root_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

	const TYPE_CLASSIC       = 0;
	const TYPE_POPUP         = 1;
	const TYPE_FOLDER        = 2;
	const TYPE_EXTERNAL_LINK = 3;
	const TYPE_INTERNAL_LINK = 4;
	const TYPE_OTHER_PAGE    = 5;

    /**
     * Creates a new query with optional settings up front
     *
     * @param   array
     * @return  Query
     */
	/*
    public static function query($options = array())
    {
        return parent::query($options + array('order_by' => array('page_sort')));
    }*/

    public function get_link() {
        return 'href="'.$this->get_href().'"';
    }

    public static function get_url($params) {
        if (is_numeric($params)) {
            return self::find($params)->get_href();
        }
    }

    public static function get_url_absolute($params) {
        if (is_numeric($params)) {
            return self::find($params)->get_href(array(
                'absolute' => true,
            ));
        }
    }

    public function get_href($params = array()) {
        if ($this->page_type == self::TYPE_EXTERNAL_LINK) {
            return $this->page_external_link;
        }
        $url = !empty($params['absolute']) ? Uri::base(false) : '';

        if (!$this->page_home) {
            $url .= $this->page_virtual_url;
        }
        return $url;
    }
}

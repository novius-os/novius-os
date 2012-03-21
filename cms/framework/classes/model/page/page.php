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

class Model_Page_Page extends \Cms\Orm\Model {

    protected static $_table_name = 'os_page';
    protected static $_primary_key = array('page_id');

	protected static $_has_many = array(
		'children' => array(
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

	protected static $_behaviors = array(
		'Cms\Orm_Behaviour_Translatable' => array(
			'events' => array('before_insert', 'after_insert', 'before_save', 'after_delete', 'before_change_parent', 'after_change_parent'),
			'lang_property'      => 'page_lang',
			'common_id_property' => 'page_lang_common_id',
			'single_id_property' => 'page_lang_single_id',
            'invariant_fields'   => array(
                //'page_parent_id', // Depends on the lang, cannot be updated automagically
                'page_template',
                'page_level',
                'page_raw_html',
                'page_sort',
                'page_menu',
                'page_type',
                'page_lock',
                'page_entrance',
                'page_home',
                'page_cache_duration',
            ),
		),
		'Cms\Orm_Behaviour_Tree' => array(
			'events' => array('before_search', 'after_delete'),
			'parent_relation' => 'parent',
			'children_relation' => 'children',
		),
		'Cms\Orm_Behaviour_Sortable' => array(
			'events' => array('after_sort'),
			'sort_property' => 'page_sort',
		),
		'Cms\Orm_Behaviour_Publishable' => array(
			'publication_bool_property' => 'page_published',
		),
	);

	const TYPE_CLASSIC       = 0;
	const TYPE_POPUP         = 1;
	const TYPE_FOLDER        = 2;
	const TYPE_EXTERNAL_LINK = 3;
	const TYPE_INTERNAL_LINK = 4;
	const TYPE_OTHER_PAGE    = 5;

    const EXTERNAL_TARGET_NEW   = 0;
    const EXTERNAL_TARGET_POPUP = 1;
    const EXTERNAL_TARGET_SAME  = 2;

    const LOCK_UNLOCKED = 0;
    const LOCK_DELETION = 1;
    const LOCK_EDITION  = 2;

    /**
     * Alias to Model:find('all') with appropriate sort for Model_Page_Page
     *
     * @param array  $where
     * @param array  $order_by
     * @param array  $options   Additional options to pass on to the ::find() method
     * @return array of \Cms\Model_Page_Page
     */
    public static function search($where, $order_by = array(), $options = array()) {
        isset($order_by['page_sort']) or $order_by['page_sort'] = 'ASC';
        return parent::search($where, $order_by, $options);
    }

    /**
     * Returns the href and target attributes for an HTML link <a>
     *
     * @return string
     */
    public function get_link() {
        $attr = array(
            'href' => $this->get_href(),
        );
        if ($this->page_type == self::TYPE_EXTERNAL_LINK) {
            if ($this->page_external_link_type == self::EXTERNAL_TARGET_NEW) {
                $attr['target'] = '_blank';
            }
        }
        return array_to_attr($attr);
    }

    /**
     *
     * @param int $params Id of the page
     * @return type
     */
    public static function get_url($params) {
        if (is_numeric($params)) {
            return static::find($params)->get_href();
        }
    }

    public static function get_url_absolute($params) {
        if (is_numeric($params)) {
            return static::find($params)->get_href(array(
                'absolute' => true,
            ));
        }
    }

    /**
     *
     * @param   array   params
     * @return  string  the href of the page (external link or virtuak URL)
     */
    public function get_href($params = array()) {
        if ($this->page_type == self::TYPE_EXTERNAL_LINK) {
            $page_external_link = $this->page_external_link;
            if (empty($page_external_link) && !$this->is_main_lang()) {
                $page_external_link = $this->find_main_lang()->page_external_link;
            }
            return $page_external_link;
        }
        $url = !empty($params['absolute']) ? Uri::base(false) : '';

        if (!$this->page_home) {
            $url .= $this->page_virtual_url;
        }
        return $url;
    }

    public function get_possible_lang() {
        $parent = $this->find_parent();
        if (!empty($parent)) {
            return $parent->get_all_lang();
        }
        return array_keys(\Config::get('locales'));
    }
}

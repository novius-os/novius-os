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

use Fuel\Core\Uri;

class Model_Page extends \Cms\Model {
    protected static $_table_name = 'cms_page';
    protected static $_primary_key = array('page_id');

    protected static $_has_wysiwygs = true;

	protected static $_has_many = array(
		'childrens' => array(
			'key_from'       => 'page_id',
			'model_to'       => '\Cms\Page\Model_Page',
			'key_to'         => 'page_pere_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

	public static $_has_one = array();

	protected static $_belongs_to = array(
		'parent' => array(
			'key_from'       => 'page_pere_id',
			'model_to'       => '\Cms\Page\Model_Page',
			'key_to'         => 'page_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'racine' => array(
			'key_from'       => 'page_rac_id',
			'model_to'       => '\Cms\Page\Model_Root',
			'key_to'         => 'rac_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

	public $wysiwyg_key;

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
        return parent::query($options + array('order_by' => array('page_rang')));
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
            return $this->page_lien_externe;
        }
        $url = !empty($params['absolute']) ? Uri::base(false) : '';

        if (!$this->page_home) {
            $url .= $this->page_url_virtuel;
        }
        return $url;
    }

    /*

	public static function set_wysiwyg($names) {
		foreach ($names as $name) {
			$relation = 'wysiwyg_'.$name;
			static::$_has_one[$relation] = array(
				'key_from' => array('page_id', 'wysiwyg_key'),
				'model_to' => 'Cms\Page\Model_Wysiwyg',
				'key_to' => array('wysiwyg_foreign_id', 'wysiwyg_key'),
				'cascade_save' => false,
				'cascade_delete' => false,
				//'conditions' => array(
				//	'where' => array(
				//		'wysiwyg_key' => 'cms_page.'.$name,
				//	),
				//),
			);
		}
	}

	public function wysiwyg($wysiwyg_name) {
		$this->wysiwyg_key = 'cms_page.'.$wysiwyg_name;
		$relation = 'wysiwyg_'.$wysiwyg_name;
		if (empty($this->$relation)) {
			$this->$relation = new Model_Wysiwyg(array(
				'wysiwyg_text' => '',
				'wysiwyg_key' => $this->wysiwyg_key,
				'wysiwyg_foreign_id' => $this->page_id,
			), true);
		}
		return $this->$relation;
	}
    */



    protected static $_properties = array (
		'page_id' => array (
			'type' => 'int',
			'min' => '-2147483648',
			'max' => '2147483647',
			'name' => 'page_id',
			'default' => null,
			'data_type' => 'int',
			'null' => false,
			'ordinal_position' => 1,
			'display' => '11',
			'comment' => '',
			'extra' => 'auto_increment',
			'key' => 'PRI',
			'privileges' => 'select,insert,update,references',
		),
		'page_rac_id' => array (
			'type' => 'string',
			'exact' => true,
			'name' => 'page_rac_id',
			'default' => '',
			'data_type' => 'char',
			'null' => false,
			'ordinal_position' => 2,
			'character_maximum_length' => '2',
			'collation_name' => 'latin1_general_ci',
			'comment' => '',
			'extra' => '',
			'key' => 'MUL',
			'privileges' => 'select,insert,update,references',
		),
		'page_pere_id' => array (
			'type' => 'int',
			'min' => '-2147483648',
			'max' => '2147483647',
			'name' => 'page_pere_id',
			'default' => null,
			'data_type' => 'int',
			'null' => true,
			'ordinal_position' => 3,
			'display' => '11',
			'comment' => '',
			'extra' => '',
			'key' => 'MUL',
			'privileges' => 'select,insert,update,references',
		),
		'page_gab_id' => array (
			'type' => 'int',
			'min' => '-2147483648',
			'max' => '2147483647',
			'name' => 'page_gab_id',
			'default' => '0',
			'data_type' => 'int',
			'null' => false,
			'ordinal_position' => 4,
			'display' => '11',
			'comment' => '',
			'extra' => '',
			'key' => 'MUL',
			'privileges' => 'select,insert,update,references',
		),
		'page_niveau' => array (
			'type' => 'int',
			'min' => '-128',
			'max' => '127',
			'name' => 'page_niveau',
			'default' => '0',
			'data_type' => 'tinyint',
			'null' => false,
			'ordinal_position' => 5,
			'display' => '4',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_titre' => array (
			'type' => 'string',
			'name' => 'page_titre',
			'default' => '',
			'data_type' => 'varchar',
			'null' => false,
			'ordinal_position' => 6,
			'character_maximum_length' => '255',
			'collation_name' => 'latin1_general_ci',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_titre_menu' => array (
			'type' => 'string',
			'name' => 'page_titre_menu',
			'default' => null,
			'data_type' => 'varchar',
			'null' => true,
			'ordinal_position' => 7,
			'character_maximum_length' => '255',
			'collation_name' => 'latin1_general_ci',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_titre_reference' => array (
			'type' => 'string',
			'name' => 'page_titre_reference',
			'default' => null,
			'data_type' => 'varchar',
			'null' => true,
			'ordinal_position' => 8,
			'character_maximum_length' => '255',
			'collation_name' => 'latin1_general_ci',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_contenu' => array (
			'type' => 'string',
			'character_maximum_length' => '65535',
			'name' => 'page_contenu',
			'default' => null,
			'data_type' => 'text',
			'null' => true,
			'ordinal_position' => 9,
			'collation_name' => 'latin1_general_ci',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_contenu_text' => array (
			'type' => 'string',
			'character_maximum_length' => '65535',
			'name' => 'page_contenu_text',
			'default' => null,
			'data_type' => 'text',
			'null' => true,
			'ordinal_position' => 10,
			'collation_name' => 'latin1_general_ci',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_travail_contenu' => array (
			'type' => 'string',
			'character_maximum_length' => '65535',
			'name' => 'page_travail_contenu',
			'default' => null,
			'data_type' => 'text',
			'null' => true,
			'ordinal_position' => 11,
			'collation_name' => 'latin1_general_ci',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_html_brut' => array (
			'type' => 'int',
			'min' => '-128',
			'max' => '127',
			'name' => 'page_html_brut',
			'default' => '0',
			'data_type' => 'tinyint',
			'null' => true,
			'ordinal_position' => 12,
			'display' => '4',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_rang' => array (
			'type' => 'float',
			'name' => 'page_rang',
			'default' => null,
			'data_type' => 'float',
			'null' => true,
			'ordinal_position' => 13,
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_menu' => array (
			'type' => 'int',
			'min' => '-128',
			'max' => '127',
			'name' => 'page_menu',
			'default' => '0',
			'data_type' => 'tinyint',
			'null' => false,
			'ordinal_position' => 14,
			'display' => '4',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_type' => array (
			'type' => 'int',
			'min' => '-128',
			'max' => '127',
			'name' => 'page_type',
			'default' => '0',
			'data_type' => 'tinyint',
			'null' => false,
			'ordinal_position' => 15,
			'display' => '4',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_publier' => array (
			'type' => 'int',
			'min' => '-128',
			'max' => '127',
			'name' => 'page_publier',
			'default' => '0',
			'data_type' => 'tinyint',
			'null' => false,
			'ordinal_position' => 16,
			'display' => '4',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_a_publier' => array (
			'type' => 'string',
			'name' => 'page_a_publier',
			'default' => null,
			'data_type' => 'datetime',
			'null' => true,
			'ordinal_position' => 17,
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_noindex' => array (
			'type' => 'int',
			'min' => '0',
			'max' => '255',
			'name' => 'page_noindex',
			'default' => '0',
			'data_type' => 'tinyint unsigned',
			'null' => false,
			'ordinal_position' => 18,
			'display' => '1',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_demandeur_id' => array (
			'type' => 'int',
			'min' => '-2147483648',
			'max' => '2147483647',
			'name' => 'page_demandeur_id',
			'default' => null,
			'data_type' => 'int',
			'null' => true,
			'ordinal_position' => 19,
			'display' => '11',
			'comment' => '',
			'extra' => '',
			'key' => 'MUL',
			'privileges' => 'select,insert,update,references',
		),
		'page_verrou' => array (
			'type' => 'int',
			'min' => '-128',
			'max' => '127',
			'name' => 'page_verrou',
			'default' => '0',
			'data_type' => 'tinyint',
			'null' => false,
			'ordinal_position' => 20,
			'display' => '4',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_home' => array (
			'type' => 'int',
			'min' => '-128',
			'max' => '127',
			'name' => 'page_home',
			'default' => '0',
			'data_type' => 'tinyint',
			'null' => false,
			'ordinal_position' => 21,
			'display' => '4',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_carrefour' => array (
			'type' => 'int',
			'min' => '-128',
			'max' => '127',
			'name' => 'page_carrefour',
			'default' => '0',
			'data_type' => 'tinyint',
			'null' => false,
			'ordinal_position' => 22,
			'display' => '4',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_duree_vie' => array (
			'type' => 'int',
			'min' => '-2147483648',
			'max' => '2147483647',
			'name' => 'page_duree_vie',
			'default' => null,
			'data_type' => 'int',
			'null' => true,
			'ordinal_position' => 23,
			'display' => '11',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_nom_virtuel' => array (
			'type' => 'string',
			'name' => 'page_nom_virtuel',
			'default' => null,
			'data_type' => 'varchar',
			'null' => true,
			'ordinal_position' => 24,
			'character_maximum_length' => '50',
			'collation_name' => 'latin1_general_ci',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_url_virtuel' => array (
			'type' => 'string',
			'name' => 'page_url_virtuel',
			'default' => null,
			'data_type' => 'varchar',
			'null' => true,
			'ordinal_position' => 25,
			'character_maximum_length' => '255',
			'collation_name' => 'latin1_general_ci',
			'comment' => '',
			'extra' => '',
			'key' => 'UNI',
			'privileges' => 'select,insert,update,references',
		),
		'page_lien_externe' => array (
			'type' => 'string',
			'name' => 'page_lien_externe',
			'default' => null,
			'data_type' => 'varchar',
			'null' => true,
			'ordinal_position' => 26,
			'character_maximum_length' => '255',
			'collation_name' => 'latin1_general_ci',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_lien_externe_type' => array (
			'type' => 'int',
			'min' => '-128',
			'max' => '127',
			'name' => 'page_lien_externe_type',
			'default' => null,
			'data_type' => 'tinyint',
			'null' => true,
			'ordinal_position' => 27,
			'display' => '4',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_date_creation' => array (
			'type' => 'int',
			'min' => '-2147483648',
			'max' => '2147483647',
			'name' => 'page_date_creation',
			'default' => '0',
			'data_type' => 'int',
			'null' => false,
			'ordinal_position' => 28,
			'display' => '11',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_date_modif' => array (
			'type' => 'int',
			'min' => '-2147483648',
			'max' => '2147483647',
			'name' => 'page_date_modif',
			'default' => '0',
			'data_type' => 'int',
			'null' => false,
			'ordinal_position' => 29,
			'display' => '11',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_description' => array (
			'type' => 'string',
			'character_maximum_length' => '65535',
			'name' => 'page_description',
			'default' => null,
			'data_type' => 'text',
			'null' => true,
			'ordinal_position' => 30,
			'collation_name' => 'latin1_general_ci',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_keywords' => array (
			'type' => 'string',
			'character_maximum_length' => '65535',
			'name' => 'page_keywords',
			'default' => null,
			'data_type' => 'text',
			'null' => true,
			'ordinal_position' => 31,
			'collation_name' => 'latin1_general_ci',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'page_addheader' => array (
			'type' => 'string',
			'character_maximum_length' => '65535',
			'name' => 'page_addheader',
			'default' => null,
			'data_type' => 'text',
			'null' => true,
			'ordinal_position' => 32,
			'collation_name' => 'latin1_general_ci',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
	);
}

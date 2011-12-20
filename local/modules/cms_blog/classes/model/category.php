<?php
/**
 * NOVIUS OS - Web OS for digital communication
 * 
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Cms\Blog;

class Model_Category extends \Orm\Model {
    protected static $_table_name = 'cms_blog_categorie';
    protected static $_primary_key = array('blgc_id');

    protected static $_properties = array (
		'blgc_id' => array (
			'type' => 'int',
			'min' => '-2147483648',
			'max' => '2147483647',
			'name' => 'blgc_id',
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
		'blgc_parent_id' => array (
			'type' => 'int',
			'min' => '-2147483648',
			'max' => '2147483647',
			'name' => 'blgc_parent_id',
			'default' => null,
			'data_type' => 'int',
			'null' => true,
			'ordinal_position' => 2,
			'display' => '11',
			'comment' => '',
			'extra' => '',
			'key' => 'MUL',
			'privileges' => 'select,insert,update,references',
		),
		'blgc_titre' => array (
			'type' => 'string',
			'name' => 'blgc_titre',
			'default' => '',
			'data_type' => 'varchar',
			'null' => false,
			'ordinal_position' => 3,
			'character_maximum_length' => '255',
			'collation_name' => 'latin1_general_ci',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'blgc_niveau' => array (
			'type' => 'int',
			'min' => '-128',
			'max' => '127',
			'name' => 'blgc_niveau',
			'default' => '0',
			'data_type' => 'tinyint',
			'null' => false,
			'ordinal_position' => 4,
			'display' => '4',
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
		'blgc_rail' => array (
			'type' => 'string',
			'name' => 'blgc_rail',
			'default' => null,
			'data_type' => 'varchar',
			'null' => true,
			'ordinal_position' => 5,
			'character_maximum_length' => '255',
			'collation_name' => 'latin1_general_ci',
			'comment' => '',
			'extra' => '',
			'key' => 'MUL',
			'privileges' => 'select,insert,update,references',
		),
		'blgc_rang' => array (
			'type' => 'float',
			'name' => 'blgc_rang',
			'default' => null,
			'data_type' => 'float',
			'null' => true,
			'ordinal_position' => 6,
			'comment' => '',
			'extra' => '',
			'key' => '',
			'privileges' => 'select,insert,update,references',
		),
	);

	protected static $_has_many = array(
		'childrens' => array(
			'key_from'       => 'blgc_id',
			'model_to'       => '\Cms\Blog\Model_Category',
			'key_to'         => 'blgc_parent_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

	protected static $_belongs_to = array(
		'parent' => array(
			'key_from'       => 'blgc_parent_id',
			'model_to'       => '\Cms\Blog\Model_Category',
			'key_to'         => 'blgc_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	);

	protected static $_many_many = array(
		'blogs' => array(
			'key_from'         => 'blgc_id',
			'key_through_from' => 'blgc_id',
			'table_through'    => 'cms_blog_lien_categorie',
			'key_through_to'   => 'blog_id',
			'model_to'         => '\Cms\Blog\Model_Blog',
			'key_to'           => 'blog_id',
			'cascade_save'     => false,
			'cascade_delete'   => false,
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
        return parent::query($options + array('order_by' => array('blgc_rang')));
    }

    public static function findOrdered() {
        $objects = static::find('all', array('where' => array(array('blgc_parent_id', 'IS', \DB::expr('NULL')))));
        /*
        $obj_by_id = array();
        for ($i = 0; $i < count($objects); $i++) {
            $obj_by_id[$objects[$i]->id] = $i;
        }
        for ($i = 0; $i < count($objects); $i++) {
            if ($objects[$i]->blgc_parent_id) {
                $iParent = $obj_by_id[$objects[$i]->blgc_patent_id];
                if (!$objects[$iParent]->children) {
                    $objects[$iParent]->children = array();
                }
                $objects[$iParent]->children[] = $objects[$i];
                array_splice($objects, $i, 1);
                $i--;
            }
        }
        */
        return $objects;
    }
}
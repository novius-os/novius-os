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
    protected static $_table_name = 'os_blog_category';
    protected static $_primary_key = array('blgc_id');

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
			'table_through'    => 'os_blog_category_link',
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
        return parent::query($options + array('order_by' => array('blgc_sort')));
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
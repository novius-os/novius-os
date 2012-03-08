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

class Model_Blog extends \Cms\Orm\Model {
    protected static $_table_name = 'os_blog';
    protected static $_primary_key = array('blog_id');

    protected static $_has_one = array();

    protected static $_belongs_to = array(
        'author' => array(
            'key_from' => 'blog_author_id',
            'model_to' => 'Cms\Model_User_User',
            'key_to' => 'user_id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
    );

	protected static $_behaviors = array(
		'Cms\Orm_Translatable' => array(
			'events' => array('before_insert', 'after_insert', 'before_save'),
			'lang_property'      => 'blog_lang',
			'common_id_property' => 'blog_lang_common_id',
			'single_id_property' => 'blog_lang_single_id',
            'invariant_fields'   => array(),
		),
	);

    /*
    protected static $_has_many = array(
        'tags' => array(
            'key_from' => 'blog_id',
            'model_to' => 'Cms\Blog\Model_Tag',
            'key_to' => 'blgt_blog_id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
    );//*/

    protected static $_many_many = array(
        'categories' => array(
            'key_from' => 'blog_id',
            'key_through_from' => 'blog_id', // column 1 from the table in between, should match a posts.id
            'table_through' => 'os_blog_category_link', // both models plural without prefix in alphabetical order
            'key_through_to' => 'blgc_id', // column 2 from the table in between, should match a users.id
            'model_to' => 'Cms\Blog\Model_Category',
            'key_to' => 'blgc_id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
        'tags' => array(
            'key_from'         => 'blog_id',
            'key_through_from' => 'blgt_blog_id',
            'table_through'    => 'os_blog_tag',
            'key_through_to'   => 'blgt_tag_id',
            'model_to'         => '\Cms\Blog\Model_Tag',
            'key_to'           => 'tag_id',
            'cascade_save'     => false,
            'cascade_delete'   => false,
        ),
    );


    function updateCategoriesById($ids) {
        $deleteIds = array();
        for ($i = 0; $i < count($this->categories); $i++) {
            $searched = array_search($this->categories[$i]->blgc_id, $ids);
            if ($searched !== false) {
                array_splice($ids, $searched, 1);
            } else {
                array_splice($this->categories, $i, 1);
                $i--;
            }
        }
        foreach ($ids as $id) {
            $this->categories[] = Model_Category::find($id);
        }
    }

    public function toto() {
        return 'first toto';
    }
}


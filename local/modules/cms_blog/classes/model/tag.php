<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Cms_Blog;

class Model_Tag extends \Orm\Model {
    protected static $_table_name = 'os_tag';
    protected static $_primary_key = array('tag_id');

    protected static $_properties = array (
        'tag_id' => array (
            'type' => 'int',
            'min' => '-2147483648',
            'max' => '2147483647',
            'name' => 'tag_id',
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
        'tag_label' => array (
            'type' => 'string',
            'name' => 'tag_label',
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
            'ordinal_position' => 2,
            'character_maximum_length' => '255',
            'collation_name' => 'utf8_general_ci',
            'comment' => '',
            'extra' => '',
            'key' => 'UNI',
            'privileges' => 'select,insert,update,references',
        ),
    );

    protected static $_many_many = array(
        'blogs' => array(
            'key_from'         => 'tag_id',
            'key_through_from' => 'blgt_tag_id',
            'table_through'    => 'os_blog_tag',
            'key_through_to'   => 'blgt_blog_id',
            'model_to'         => '\Cms\Blog\Model_Blog',
            'key_to'           => 'blog_id',
            'cascade_save'     => false,
            'cascade_delete'   => false,
        ),
    );
}
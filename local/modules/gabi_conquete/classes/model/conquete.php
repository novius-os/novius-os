<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Gabi\Conquete;
use \Cms\Model;

class Model_Conquete extends Model {
    protected static $_table_name = 'conquete';
    protected static $_primary_key = array('conq_id');

    protected static $_has_one = array();

    protected static $_belongs_to = array();

    protected static $_observers = array();

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

    protected static $_many_many = array();
}


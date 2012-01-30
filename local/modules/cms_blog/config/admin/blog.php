<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */
use Cms\I18n;

I18n::load('cms_blog::blog');

return array(
	'query' => array(
		'model' => 'Cms\Blog\Model_Blog',
		'related' => array('author'),
		'limit' => 20,
	),
    'selectedView' => 'default',
    'views' => array(
        'default' => array(
            'name' => __('Default'),
            'json' => 'static/modules/cms_blog/js/admin/blog.js',
        ),
        'delete_first' => array(
            'name' => __('Delete first'),
            'json' => 'static/modules/cms_blog/js/admin/blog_1.js',
        )
    ),
    'i18n' => array(
        'Blog' => __('Blog'),
        'Add a post' => __('Add a post'),
        'Add a category' => __('Add a category'),
        'Title' => __('Title'),
        'Author' => __('Author'),
        'Date' => __('Date'),
        'Delete' => __('Delete'),
        'Edit' => __('Edit'),
        'Categories' => __('Categories'),
        'Tags' => __('Tags'),
        'Authors' => __('Authors'),
        'Publish date' => __('Publish date'),
        'Language' => __('Language'),

        'addDropDown' => __('Select an action'),
        'columns' => __('Columns'),
        'showFiltersColumns' => __('Filters column header'),
        'visibility' => __('Visibility'),
        'settings' => __('Settings'),
        'vertical' => __('Vertical'),
        'horizontal' => __('Horizontal'),
        'hidden' => __('Hidden'),
        'item' => __('post'),
        'items' => __('posts'),
        'showNbItems' => __('Showing {{x}} posts out of {{y}}'),
        'showOneItem' => __('Show 1 post'),
        'showNoItem' => __('No post'),
        'showAll' => __('Show all posts'),
        'views' => __('Views'),
        'viewGrid' => __('Grid'),
        'viewThumbnails' => __('Thumbnails'),
        'preview' => __('Preview'),
        'loading' => __('Loading...'),
    ),
	'dataset' => array(
        'id' => 'blog_id',
		'title' => 'blog_titre',
        'lang' => 'blog_lang',
		'author' => array(
            'search_relation' => 'author',
            'search_column'   => 'author.user_fullname',
            'value' =>  function($object) {
                            return $object->author->user_fullname;
                        },
        ),
		'date' => array(
            'search_column'    =>  'blog_date_creation',
            'dataType'         => 'datetime',
            'value'             => function($object) {
                            return \Date::create_from_string($object->blog_date_creation, 'mysql')->format('%m/%d/%Y %H:%M:%S'); //%m/%d/%Y %H:%i:%s
                        },

        ),
	),
	'inputs' => array(
		'blgc_id' => function($value, $query) {
			if ( is_array($value) && count($value) && $value[0]) {
				$query->related('categories');
				$query->where(array('categories.blgc_id', 'in', $value));
			}
			return $query;
		},
		'tag_id' => function($value, $query) {
			if ( is_array($value) && count($value) && $value[0]) {
				$query->related('tags', array(
					'where' => array(
						array('tags.tag_id', 'in', $value),
					),
				));
			}
			return $query;
		},
		'blog_auteur_id' => function($value, $query) {
			if ( is_array($value) && count($value) && $value[0]) {
				$query->where(array('blog_auteur_id', 'in', $value));
			}
			return $query;
		},
		'blog_date_creation' => function($value, $query) {
			list($begin, $end) = explode('|', $value.'|');
			if ($begin) {
				if ($begin = Date::create_from_string($begin, '%Y-%m-%d')) {
					$query->where(array('blog_date_creation', '>=', $begin->format('mysql')));
				}
			}
			if ($end) {
				if ($end = Date::create_from_string($end, '%Y-%m-%d')) {
					$query->where(array('blog_date_creation', '<=', $end->format('mysql')));
				}
			}
			return $query;
		},
	),
);
<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */


return array(
    'query' => array(
        'model' => 'Gabi\Conquete\Model_Conquete',
        'limit' => 20,
        'related' => array(),
    ),
    'selectedView' => 'default',
    'views' => array(
        'default' => array(
            'name' => __('Default'),
            'json' => array('static/modules/gabi_conquete/js/admin/conquete.js'),
        ),
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
        'id' => 'conq_id',
        'prenom' => 'conq_prenom',
        'nom'    => 'conq_nom',
    ),
    'inputs' => array(
        'prenoms' => function($value, $query) {
            if ( is_array($value) && count($value) && $value[0]) {
                $query->where(array('conq_prenom', 'in', $value));
            }
            return $query;
        },
        /*'blgc_id' => function($value, $query) {
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
        'blog_author_id' => function($value, $query) {
            if ( is_array($value) && count($value) && $value[0]) {
                $query->where(array('blog_author_id', 'in', $value));
            }
            return $query;
        },
        'blog_created_at' => function($value, $query) {
            list($begin, $end) = explode('|', $value.'|');
            if ($begin) {
                if ($begin = Date::create_from_string($begin, '%Y-%m-%d')) {
                    $query->where(array('blog_created_at', '>=', $begin->format('mysql')));
                }
            }
            if ($end) {
                if ($end = Date::create_from_string($end, '%Y-%m-%d')) {
                    $query->where(array('blog_created_at', '<=', $end->format('mysql')));
                }
            }
            return $query;
        },*/
    ),
);
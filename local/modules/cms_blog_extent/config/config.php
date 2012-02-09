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
    // Global
    'config' => array(
        'date_format'   => '%A %e %B %Y %H:%M',
        'link_on_title' => false,
        'item_per_page' => 10,
    ),

    // List view
    'display_list' => array(
        'order_by'    => array('blog_created_at' => 'DESC', 'blog_id' => 'DESC'),
    ),

    'display_list_main' => array(
        'list_view'   => 'front/list',
    ),

    'display_author' => array(
        'list_view'   => 'front/list_author',
    ),

    'display_category' => array(
        'list_view'   => 'front/list_category',
    ),

    'display_tag' => array(
        'list_view'   => 'front/list_tag',
    ),

    // Item view
    'display_list_item' => array(
        'fields'      => 'title author date categories thumbnail summary tags stats',
        'title_tag'   => 'h2',
        'item_view'   => 'front/item_list',
        'fields_view' => 'front/fields',
    ),

    'display_item' => array(
        'fields'      => 'title author date categories thumbnail summary tags stats wysiwyg',
        'title_tag'   => 'h1',
        'item_view'   => 'front/item',
        'fields_view' => 'front/fields',
    ),
);
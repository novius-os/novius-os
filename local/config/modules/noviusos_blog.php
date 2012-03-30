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
        'date_format' => '%A %e %B %Y',
    ),

    'display_list_item' => array(
        'fields'      => 'title author date categories thumbnail summary tags stats',
        'title_tag'   => 'h2',
        'item_view'   => 'front/item_list',
        'fields_view' => 'front/fields',
    ),

    //'display_list_item_following' => array(
    //    'fields'    => 'title',
    //    'title_tag' => 'h3',
    //    'item_view' => 'noviusos_blog/item_list_short',
    //),
);

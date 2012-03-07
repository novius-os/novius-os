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
    'name'    => 'Conquete',
    'version' => '0.9-alpha',
    'href' => 'admin/gabi_conquete/list',
    'icon64'  => 'static/modules/gabi_conquete/img/64/blog.png',
    'provider' => array(
        'name' => 'Novius OS',
    ),
    'launchers' => array(
        'conquete' => array(
            'name'    => 'Conquete',
            'url' => 'admin/gabi_conquete/list',
            'iconUrl' => 'static/modules/gabi_conquete/img/32/blog.png',
            'icon64'  => 'static/modules/gabi_conquete/img/64/blog.png',
        ),
    ),
    /*'wysiwyg_enhancers' => array(
        'gabi_conquete' => array(
            'title' => 'Blog',
            'id'    => 'gabi_conquete',
            'rewrite_prefix' => 'blog',
            'desc'  => '',
            'target' => 'gabi_conquete/front',
            'iconUrl' => 'static/modules/gabi_conquete/img/16/blog.png',
            'popupUrl' => 'admin/gabi_conquete/popup',
            'previewUrl' => 'admin/gabi_conquete/preview',
        ),
    ),*/
);

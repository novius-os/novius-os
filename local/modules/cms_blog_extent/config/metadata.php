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
    'name'    => 'Blog extent',
    'version' => '0.9-alpha',
    'extends'   => 'cms_blog',
	'icon64'  => 'static/modules/cms_blog/img/64/blog.png',
    'provider' => array(
        'name' => 'Novius OS',
    ),
    'launchers' => array(
        'blog_extent' => array(
            'name'    => 'Blog extent',
            'url' => 'admin/cms_blog_extent/list',
            'iconUrl' => 'static/modules/cms_blog/img/32/blog.png',
            'icon64'  => 'static/modules/cms_blog/img/64/blog.png',
        ),
    ),
    'wysiwyg_enhancers' => array(
        'cms_blog_extent' => array(
            'title' => 'Blog',
            'id'    => 'cms_blog_extent',
            'rewrite_prefix' => 'blog',
            'desc'  => '',
            'target' => 'cms_blog/front',
            'iconUrl' => 'static/modules/cms_blog/img/16/blog.png',
            'popupUrl' => 'admin/cms_blog/popup',
            'previewUrl' => 'admin/cms_blog/preview',
        ),
    ),
);

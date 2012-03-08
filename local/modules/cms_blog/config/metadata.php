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
    'name'    => 'Blog',
    'version' => '0.9-alpha',
	'href' => 'admin/cms_blog/list',
	'icon64'  => 'static/modules/cms_blog/img/64/blog.png',
    'provider' => array(
        'name' => 'Novius OS',
    ),
    'launchers' => array(
        'blog' => array(
            'name'    => 'Blog',
            'url' => 'admin/cms_blog/list',
            'iconUrl' => 'static/modules/cms_blog/img/32/blog.png',
            'icon64'  => 'static/modules/cms_blog/img/64/blog.png',
        ),
    ),
    'wysiwyg_enhancers' => array(
        'cms_blog' => array(
            'title' => 'Blog',
            'id'    => 'cms_blog',
            'rewrite_prefix' => 'blog',
            'desc'  => '',
            'target' => 'cms_blog/front',
            'iconUrl' => 'static/modules/cms_blog/img/16/blog.png',
            'previewUrl' => 'admin/cms_blog/preview',
	        'dialog' => array(
		        'contentUrl' => 'admin/cms_blog/popup',
		        'width' => 450,
		        'height' => 180,
		        'ajax' => true,
	        ),
        ),
    ),
);

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
	'views' => array(
		'default' => array(
			'name' => __('Default'),
			'json' => 'static/modules/cms_blog/js/admin/blog.js',
		),
		'delete_first' => array(
			'name' => 'Delete first Youhou',
			'json' => 'static/modules/cms_blog/js/admin/blog_1.js',
		)
	),
);
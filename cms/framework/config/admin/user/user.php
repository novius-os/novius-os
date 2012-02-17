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

I18n::load('user', 'cms_user');

return array(
	'query' => array(
		'model' => 'Cms\Model_User_User',
		'related' => array('groups'),
	),
    'selectedView' => 'default',
    'views' => array(
        'default' => array(
            'name' => __('Default'),
            'json' => 'static/cms/js/admin/user/user.js',
        )
    ),
    'i18n' => array(
        'Users' => __('Users'),
        'Add a user' => __('Add a user'),
        'User' => __('User'),
        'Email' => __('Email'),
        'Permissions' => __('Permissions'),

        'addDropDown' => __('Select an action'),
        'columns' => __('Columns'),
        'showFiltersColumns' => __('Filters column header'),
        'visibility' => __('Visibility'),
        'settings' => __('Settings'),
        'vertical' => __('Vertical'),
        'horizontal' => __('Horizontal'),
        'hidden' => __('Hidden'),
        'item' => __('user'),
        'items' => __('users'),
        'showNbItems' => __('Showing {{x}} users out of {{y}}'),
        'showOneItem' => __('Show 1 user'),
        'showNoItem' => __('No user'),
        'showAll' => __('Show all users'),
        'views' => __('Views'),
        'viewGrid' => __('Grid'),
        'viewThumbnails' => __('Thumbnails'),
        'preview' => __('Preview'),
        'loading' => __('Loading...'),
    ),
	'dataset' => array(
		'id' => 'user_id',
		'fullname' => function($object) {
			return $object->fullname();
		},
		'email' => 'user_email',
		'id_permission' => function($object) {
			return $object->groups && reset($object->groups)->group_id ?: $object->user_id;
		}
	),
	'inputs' => array(),
);
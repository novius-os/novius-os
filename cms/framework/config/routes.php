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
	'_root_' => 'cms/admin/dispatch/',
	'admin' => 'cms/admin/dispatch/',
	
	'admin/(:any)' => 'cms/admin/dispatch/$1',
	//'(:any)' => 'cms/admin/dispatch/$1',
	
	'_404_' => null,
);

Config::load('cloud', true);
// Not installed: Controller_Install
if (!Config::get('cloud.installed', false)) {
	return array(
		'_root_' => 'install/index',
		'_404_' => 'install/404',
	);
}

// No routes, everything's manual!
return array();
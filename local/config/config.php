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
    'profiling' => false,

    // Possible values: 'user' or 'group'
    'permission_mode' => 'user',

    'log_threshold'   => Fuel::L_WARNING,

	'locale' => 'fr_FR.utf8',
	'locales' => array(
		'fr_FR' => 'Français',
		'en_GB' => 'English',
	),

	'upload' => array(
		'disabled_extensions' => array('php'),
	),

	'allow_plugin_upload' => false,
);

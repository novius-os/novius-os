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

    'log_threshold'   => Fuel::$env === Fuel::DEVELOPMENT ? Fuel::L_WARNING : Fuel::L_ERROR,

    'locale' => 'en_GB.utf8',
    'locales' => array(
        'en_GB' => 'English',
        'fr_FR' => 'Français',
        //'de_DE' => 'Deutsch',
        //'es_ES' => 'Español
        //'it_IT' => 'Italiano',
    ),

    'upload' => array(
        'disabled_extensions' => array('php'),
    ),

    'assets_minified' => Fuel::$env !== Fuel::DEVELOPMENT,

    'allow_plugin_upload' => false,
);

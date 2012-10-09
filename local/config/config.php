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

    'sites' => array(
        'main' => array(
            'title' => 'Main site',
            'alias' => 'Main',
        ),
    ),

    'locales' => array(
        'fr_FR' => array(
            'title' => 'Français',
            'flag' => 'fr',
        ),
        'en_GB' => array(
            'title' => 'English',
            'flag' => 'gb',
        ),
        'ja_JP' => array(
            'title' => '日本語',
            'flag' => 'jp',
        ),
        /*'de_DE' => array(
            'title' => 'Deutsch',
            'flag' => 'de',
        ),
        'es_ES' => array(
            'title' => 'Español',
            'flag' => 'es',
        ),
        'it_IT' => array(
            'title' => 'Italiano',
            'flag' => 'it',
        ),*/
    ),

    'contexts' => array(
        'main::en_GB' => array(),
        'main::fr_FR' => array(),
        'main::ja_JP' => array(),
        /*'main::de_DE' => array(),
        'main::es_ES' => array(),
        'main::it_IT' => array(),*/
    ),

    'upload' => array(
        'disabled_extensions' => array('php'),
    ),

    'assets_minified' => Fuel::$env !== Fuel::DEVELOPMENT,

    'allow_plugin_upload' => false,
);

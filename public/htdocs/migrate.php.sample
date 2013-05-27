<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

$_SERVER['NOS_ROOT'] = realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..');

// Boot the app
require_once $_SERVER['NOS_ROOT'].DIRECTORY_SEPARATOR.'novius-os'.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'bootstrap.php';

define('NOVIUSOS_PATH', realpath(DOCROOT.'..'.DS.'novius-os').DS);


$migrations = \Nos\Application::migrateAll();


if (\Input::get('json', false)) {
    \Response::json(200, $migrations);
} else {
    echo \View::forge('nos::admin/migrations', array('migrations' => $migrations));
}
<?php
/**
 * NOVIUS OS - Web OS for digital communication
 * 
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

/**
 * Set error reporting and display errors settings.  You will want to change these when in production.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Use an anonymous function to keep the global namespace clean
call_user_func(function() {

    /**
     * Set all the paths here
     */
    $app_path       = '../../local/';
    $package_path   = '../packages/';
    $core_path      = '../fuel-core/';
    $novius_path    = '../framework/';


    /**
     * Website docroot
     */
    define('DOCROOT', __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR);

    ( ! is_dir($app_path) and is_dir(DOCROOT.$app_path)) and $app_path = DOCROOT.$app_path;
    ( ! is_dir($core_path) and is_dir(DOCROOT.$core_path)) and $core_path = DOCROOT.$core_path;
    ( ! is_dir($package_path) and is_dir(DOCROOT.$package_path)) and $package_path = DOCROOT.$package_path;
    ( ! is_dir($novius_path) and is_dir(DOCROOT.$novius_path)) and $novius_path = DOCROOT.$novius_path;

    define('APPPATH',  realpath($app_path).DIRECTORY_SEPARATOR);
    define('PKGPATH',  realpath($package_path).DIRECTORY_SEPARATOR);
    define('COREPATH', realpath($core_path).DIRECTORY_SEPARATOR);
    define('CMSPATH',  realpath($novius_path).DIRECTORY_SEPARATOR);

});

// Get the start time and memory for use later
defined('FUEL_START_TIME') or define('FUEL_START_TIME', microtime(true));
defined('FUEL_START_MEM') or define('FUEL_START_MEM', memory_get_usage());

// Boot the app
require_once CMSPATH.'bootstrap.php';
\Cms\PubliCache::delete('blog/category/3');
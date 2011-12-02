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

define('DOCROOT', $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR);

define('APPPATH',  realpath(DOCROOT.'../local/').DIRECTORY_SEPARATOR);
define('PKGPATH',  realpath(DOCROOT.'../cms/packages/').DIRECTORY_SEPARATOR);
define('COREPATH', realpath(DOCROOT.'../cms/fuel-core/').DIRECTORY_SEPARATOR);
define('CMSPATH',  realpath(DOCROOT.'../cms/framework/').DIRECTORY_SEPARATOR);

// Get the start time and memory for use later
defined('FUEL_START_TIME') or define('FUEL_START_TIME', microtime(true));
defined('FUEL_START_MEM') or define('FUEL_START_MEM', memory_get_usage());

// Boot the app
require_once CMSPATH.'bootstrap.php';

if (empty($_SERVER['REDIRECT_URL']) && !empty($_GET['URL'])) {
	$_SERVER['REDIRECT_URL'] = $_GET['URL'].'.html';
	if ($_SERVER['REDIRECT_URL'] == '/index.html')
	{
		$_SERVER['REDIRECT_URL'] = '/';
	}
}
// Generate the request, execute it and send the output.
$response = Request::forge('cms/front/index', false)->execute()->response();

// This will add the execution time and memory usage to the output.
// Comment this out if you don't use it.
$bm = Profiler::app_total();
$response->body(str_replace(array('{exec_time}', '{mem_usage}'), array(round($bm[0], 4), round($bm[1] / pow(1024, 2), 3)), $response->body()));

$response->send(true);

// Fire off the shutdown event
Event::shutdown();

// Make sure everything is flushed to the browser
ob_end_flush();

if (!empty($_GET['testing'])) {
    $fp = fopen('/tmp/'.$_GET['testing'].'.log', 'a+');
    $lock = flock($fp, LOCK_EX);
    fwrite($fp, round($bm[0], 4)."\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}
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

$resized = preg_match('`cache/media/(.+/(\d+)-(\d+)(?:-(\w+))?.([a-z]+))$`', $_SERVER['REDIRECT_URL'], $m);


if ($resized) {
    list(,$path, $max_width, $max_height, $verification, $extension) = $m;
    $media_url = str_replace("/$max_width-$max_height-$verification", '', $path);
    $media_url = str_replace("/$max_width-$max_height", '', $media_url);
} else {
	$redirect_url = Input::server('REDIRECT_SCRIPT_URL', Input::server('REDIRECT_URL'));
    $media_url    = str_replace('/media/', '', $redirect_url);
}

$media = false;

$res = \DB::select()->from(\Cms\Model_Media_Media::table())->where(array(
    array(DB::expr('CONCAT(media_path, media_file)'), '=', $media_url),
))->execute()->as_array();

if ($res) {
    $media = \Cms\Model_Media_Media::forge(reset($res));
    $media->freeze();
}

//$media = Cms\Model_Media::find('all', array(
//    'where' => array(
//        array(DB::expr('CONCAT(media_path, media_file)'), '=', $media_url),
//    ),
//));

//$media  = current($media);

if (false === $media) {
    $send_file = false;
} else {
    if ($resized) {
        $source = APPPATH.$media->get_public_path();
        $dest   = DOCROOT.$m[0];
        $dir    = dirname($dest);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        try {
            \Cms\Tools_Image::resize($source, $max_width, $max_height, $dest);
            $send_file = $dest;
        } catch(\Exception $e) {
            $send_file = false;
        }
    } else {
        $source = APPPATH.$media->get_public_path();
        $target = DOCROOT.$media->get_public_path();
        $dir    = dirname($target);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        //symlink($source, $target);
        $send_file = $source;
    }
}

if (false !== $send_file && is_file($send_file)) {
	//Cms\Tools_File::$use_xsendfile = false;
    Cms\Tools_File::send($send_file);
}

// TODO header 404
// real 404
//exit('test');



// Fire off the shutdown event
Event::shutdown();

// Make sure everything is flushed to the browser
ob_end_flush();

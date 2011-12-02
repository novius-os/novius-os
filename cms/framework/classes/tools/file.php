<?php
/**
 * NOVIUS OS - Web OS for digital communication
 * 
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Cms;

class Tools_File {

    public static $use_xsendfile = null;
    public static $xsendfile_header = 'X-Sendfile';

    public static function _init() {

        static::$use_xsendfile = \Config::get('use_xsendfile', null);
        if (null === static::$use_xsendfile) {
            // No config defined: auto detection
            static::$use_xsendfile = self::xsendfile_available();
        } else if (is_string(static::$use_xsendfile)) {
            static::$xsendfile_header = static::$use_xsendfile;
            static::$use_xsendfile    = true;
          }

        // Check availability
        if (static::$use_xsendfile && !static::xsendfile_available()) {
            \Fuel::$profiling && \Profiler::console('X-Sendfile enabled but not available on your installation.');
        } else if (!static::$use_xsendfile && static::xsendfile_available()) {
            \Fuel::$profiling && \Profiler::console('X-Sendfile available on your installation but not enabled.');
        }
    }

    public static function xsendfile_available() {
        // On Apache
        if (function_exists('apache_get_modules') && in_array('mod_xsendfile', apache_get_modules())) {
            // Doesn't mean it's configured properly but it's available
            // We consider that if it has benn installed, then it's also been configured
            return true;
        }

        // @todo Check xsendfile availability on others web servers (nginx, lighthttpd, etc.)
        return false;
    }

    /**
     *
     * @param type $file
     * @param type $url
     */
    public static function send($file) {
        // This is a 404 error handler, so force status 200
        header('HTTP/1.0 200 Ok');
        header('HTTP/1.1 200 Ok');

        // Send Content-Type
        header('Content-Type: '.static::content_type($file));

        // X-Sendfile is better when available
        if (static::$use_xsendfile) {
            header(static::$xsendfile_header.': '.$file);
        } else {
            readfile($file);
        }
        exit();
    }

    /**
     * Determines the content type of a file
     *
     * @param  string  $file  Path on the file system
     * @return string  The appropriate Content-type (eg. image/png)
     */
    public static function content_type($file) {
        // New way (default PHP 5.3)
        if (function_exists('finfo_file')) {
            return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file);
        }
        // Old way
        return static::_content_type_fallback($file);
    }

    protected static function _content_type_fallback($file) {
        static $content_types = array(
            'pdf'  => 'application/pdf',
            'exe'  => 'application/octet-stream',
            'zip'  => 'application/zip',
            'doc'  => 'application/msword',
            'xls'  => 'application/vnd.ms-excel',
            'ppt'  => 'application/vnd.ms-powerpoint',
            'gif'  => 'image/gif',
            'png'  => 'image/png',
            'jpeg' => 'image/jpg',
            'jpg'  => 'image/jpg',
            'mp3'  => 'audio/mpeg',
            'wav'  => 'audio/x-wav',
            'mpeg' => 'video/mpeg',
            'mpg'  => 'video/mpeg',
            'mpe'  => 'video/mpeg',
            'mov'  => 'video/quicktime',
            'avi'  => 'video/x-msvideo',
            'xml'  => 'text/xml',
            'htm'  => 'text/html',
            'html' => 'text/html',
            'txt'  => 'text/plain',
        );
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        return $content_types[$extension] ? : 'application/force-download';
    }
}
<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

if (empty($base_url)) {
    $base_url = '';
}
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Novius OS - Install wizard</title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="shortcut icon" href="<?php echo $base_url ?>static/novius-os/admin/novius-os/img/favicon/favicon.ico">

    <style type="text/css">
        html {
            height : 100%;
        }
        body {
            /* On step 1, this asset will probably return an HTTP status 404 Not Found */
            background: #ddd url("<?php echo $base_url ?>static/novius-os/admin/novius-os/img/wallpapers/default.jpg");
            background-size: cover;
            font-family: franklin gothic medium,arial,verdana,helvetica,sans-serif;
        }
        #header {
            position: absolute;
            top: 10px;
            left: 50px;
            right: 50px;
            height: 80px;
            padding: 0 20px;
            margin: 0 20px;
            line-height: 80px;
        }
        #header img {
            vertical-align: middle;
            display: inline-block;
            margin-right: 10px;
        }
        button a {
            color: inherit;
            text-decoration: inherit;
        }
        #blank_slate {
            background: rgba(255, 255, 255, 0.9);
            border: 1px outset #888888;
            border-radius: 10px;
            padding: 20px 40px;
            position: absolute;
            top: 110px;
            left: 50px;
            right: 50px;
            bottom: 30px;
            overflow: auto;
        }
        #blank_slate h2 {
            padding: 0 2em 0 0;
            color: #6C9DCF;
        }
        #blank_slate h2 .outof {
            color: #888;
        }
        #blank_slate h3 {
            color: #363636;
        }
        #version {
            position: absolute;
            right: 50px;
            bottom: 5px;
        }
        a {
            color: #6C9DCF;
        }

        table {
            border-collapse: collapse;
            margin: 1em auto;
        }
        th {
            text-align: left;
        }
        td, th {
            border: 1px solid #bbb;
            padding: 5px;
        }
        td.status {
            font-weight: bold;
            text-align: center;
            width: 7em;
        }
        tr.error td.status {
            background-color: #C40C12;
            color: #fff;
        }
        tr.warning td.status {
            background-color: #EF9000;
            color: #FFF;
        }
        tr.ok td.status {
            background-color: #52A500;
            color: #FFF;
        }
        tr.separator td {
            border:none;
        }
        tr.error td.description, tr.warning td.description {
            border-top: none;
        }
        code {
            background-color: #fff;
            border: 1px dashed #bbb;
            display:inline-block;
            padding: 2px;
        }
        p.description {
            font-style: italic;
            font-size: smaller;
        }


        input, button {
            background:#fff;
            -moz-box-shadow:inset 0 2px 2px rgba(143,143,143,0.50);
            -webkit-box-shadow:inset 0 2px 2px rgba(143,143,143,0.50);
            box-shadow:inset 0 2px 2px rgba(143,143,143,0.50);
            padding: 5px;
            -moz-border-radius: 3px;
            -webkit-border-radius: 3px;
            border-radius: 3px;
            border: 1px solid #a8a8a8;
            font-weight: bold;
            font-size: 1.1em;
            color: #4f4f4f;
            text-shadow: 0px 1px 0px rgba(255,255,255,0.7);
        }

        input:focus, input:active {
            border: solid 1px #8ab0c6;
            outline: none;
            -moz-box-shadow:0 0 5px #85b2cb, inset 0 2px 2px #8f8f8f;
            -webkit-box-shadow:0 0 5px #85b2cb, inset 0 2px 2px #8f8f8f;
            box-shadow:0 0 5px #85b2cb, inset 0 2px 2px #8f8f8f;
        }
        input[type=submit], button {
            padding: .4em 1em;
            cursor: pointer;
            color: #313131;
            border: 1px solid #a8a8a8;
            -moz-box-shadow: 0 0 3px #85b2cb;
            -webkit-box-shadow: 0px 0px 3px #85b2cb;
            box-shadow: 0px 0px 3px #85b2cb;
            background: #c4c4c4 linear-gradient(top, rgba(255,255,255,0.8), rgba(255,255,255,0));
            background: #c4c4c4 -webkit-gradient(linear, left top, left bottom, from(rgba(255,255,255,0.8)), to(rgba(255,255,255,0)));
            background: #c4c4c4 -moz-linear-gradient(top, rgba(255,255,255,0.8), rgba(255,255,255,0));
        }
        input[type=submit]:hover, button:hover {
            border: solid 1px #8ab0c6;
            background: #85b2cb linear-gradient(top, rgba(255,255,255,0.6), rgba(255,255,255,0));
            background: #85b2cb -webkit-gradient(linear, left top, left bottom, from(rgba(255,255,255,0.6)), to(rgba(255,255,255,0)));
            background: #85b2cb -moz-linear-gradient(top, rgba(255,255,255,0.6), rgba(255,255,255,0));
        }
        input[type=submit]:active, button:active {
            border: solid 1px #8ab0c6;
            background: #85b2cb linear-gradient(bottom, rgba(255,255,255,0.6), rgba(255,255,255,0));
            background: #85b2cb -webkit-gradient(linear, left bottom, left top, from(rgba(255,255,255,0.6)), to(rgba(255,255,255,0)));
            background: #85b2cb -moz-linear-gradient(bottom, rgba(255,255,255,0.6), rgba(255,255,255,0));
        }

        form label {
            display: inline-block;
            width: 180px;
            font-weight: bold;
            cursor: pointer;
            cursor: hand;
        }

        p.error {
            color: #ff0000;
        }

        #todo li {
            margin-bottom: 0.5em;
        }

        #todo em {
            color: #333;
            font-size: 0.8em;
        }
        #todo em a {
            color: #333;
            text-decoration: none;
        }
        #todo em a:hover {
            text-decoration: underline;
        }

        #languages {
            margin: 0 0 1em;
        }
        .languages_tip {
            font-style: italic;
        }
        #languages li {
            list-style-type: none;
            display: block;
            line-height: 24px;
            cursor: move;
            font-size: 1.1em;
        }
        #languages label {
            padding: 3px;
            cursor: pointer;
            cursor: hand;
            display: inline;
            font-weight: normal;
        }
        #languages li.checked label {
            font-weight: bold;
        }
        #languages input {
            padding: 2px;
            font-size: 1em;
        }
        #languages span.error {
            font-weight: normal;
            color: #ff0000;
        }
    </style>
</head>

<body>

<h1 id="header">
    <img src="<?php echo $base_url ?>logo-80x74.png" width="80" height="74" alt="Novius OS Logo"> Novius OS install wizard
</h1>
<div id="blank_slate">
<?php

define('NOS_ENTRY_POINT', 'install');

$_SERVER['NOS_ROOT'] = realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..');

// Boot the app
require_once $_SERVER['NOS_ROOT'].DIRECTORY_SEPARATOR.'novius-os'.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'bootstrap.php';

Fuel::$profiling = false;

define('NOVIUSOS_PATH', realpath(DOCROOT.'..'.DS.'novius-os').DS);
define('OS_WIN_XP', defined('PHP_WINDOWS_VERSION_MAJOR') && PHP_WINDOWS_VERSION_MAJOR < 6);

class Test
{
    public static $tests = array();
    public static $passed = true;
    protected static $run = array();

    public static function register(array $list)
    {
        static::$tests = static::$tests + $list;
    }

    public static function reset()
    {
        static::$run = array();
    }

    public static function run($name)
    {
        $test =& static::$tests[$name];
        static::$tests[$name]['is_error'] = false;

        if (!isset($test['warning'])) {
            $test['warning'] = false;
        }

        if (isset($test['run_only_if'])) {
            foreach ((array) $test['run_only_if'] as $s) {
                if (is_bool($s)) {
                    if (false === $s) {
                        return true;
                    }
                } elseif (!static::$tests[$s]['passed']) {
                    return true;
                }
            }
        }

        static::$run[] = $name;

        $class = $test['passed'] ? 'ok' : ($test['warning'] ? 'warning' : 'error');

        if ($class == 'ok' && !empty($test['hide_success_when'])) {
            return true;
        }

        if ($class == 'error' && isset($test['hide_error_when'])) {
            foreach ((array) $test['hide_error_when'] as $s) {
                if (is_bool($s)) {
                    if (true === $s) {
                        return true;
                    }
                } elseif (static::$tests[$s]['passed']) {
                    return true;
                }
            }
        }

        if ($class == 'ok') {
            return true;
        }

        static::$tests[$name]['is_error'] = true;
        static::$passed = false;
        return $class != 'error';
    }

    protected static function _format_test($test)
    {
        $class = $test['passed'] ? 'ok' : ($test['warning'] ? 'warning' : 'error');
        $return = array();
        $return[] = '<tr class="'.$class.'"><td class="status">'.($test['passed'] ? 'OK' : (!empty($test['warning']) ? 'Warning' : 'Fix me')).'</td>';
        $return[] = '<th>'.$test['title'].'</th></tr>';

        return implode('', $return);
    }

    public static function results($filter = 'all')
    {
        if ($filter == 'all') {
            // Show all status
            $filter = array('success', 'warning', 'error');
        }
        $filter = (array) $filter;

        $return = array('<table width="100%">');
        $last_separator = 0;
        foreach (static::$run as $name) {
            if ($name == 'separator') {
                if ($last_separator == 0) {
                    continue;
                }
                $last_separator = 0;
                $return[] = '<tr class="separator"><td colspan="2"></td></tr>';
            } else {
                $test = static::$tests[$name];
                $status = $test['passed'] ? 'success' : ($test['warning'] ? 'warning' : 'error');
                if (in_array($status, $filter)) {
                    $last_separator++;
                    $return[] = static::_format_test($test);
                }
            }
        }
        $return[] = '</table>';
        return implode('', $return);
    }

    public static function separator()
    {
        static::$run[] = 'separator';
    }

    public static function recap($command_line = true)
    {
        $recap = array();
        foreach (static::$tests as $data) {
            if (!empty($data['is_error'])) {
                if (isset($data['command_line_relative']) || !empty($data['command_line'])) {
                    if ($command_line) {
                        $cmd = (array) \Arr::get($data, 'command_line_relative', $data['command_line']);
                        if (!empty($cmd[1]) && $cmd[1] == '# or') {
                            $cmd = array_slice($cmd, 2);
                        }
                        foreach ($cmd as $c) {
                            $c = str_replace(NOSROOT, '', $c);
                            $recap[] = $c;
                        }
                    }
                } elseif (!$command_line && !empty($data['description'])) {
                    $recap[] = $data['description'];
                }
            }
        }
        if (!empty($recap) && $command_line) {
            return array_merge(array('cd '.NOSROOT, ''), $recap);
        }
        return $recap;
    }
}

if (file_exists(APPPATH.'config'.DS.'config.php')) {
    $config = \Fuel::load(APPPATH.'config'.DS.'config.php');
} else {
    $config = array();
}

$step = \Input::get('step', 0);

if ($step == 0) {
    ?>
    <h2>Thank you for downloading Novius OS</h2>
    <p>Welcome to the install wizard. You’ll be done within a few minutes, we’ll guide you through the process.</p>
    <p>The install process is divided into <strong>four easy steps</strong>:</p>
    <ol>
        <li>Test the server</li>
        <li>Enter the database details</li>
        <li>Create the first user account</li>
        <li>Select the languages</li>
    </ol>
    <p><strong>Before you start</strong>, make sure to have your <strong>database details at hand</strong>.<br />
       If you don’t have them, ask your hosting provider or system administrator for:</p>
    <ul>
        <li>MySQL server address</li>
        <li>MySQL username and password</li>
        <li>Database name</li>
    </ul>
    <a href="install.php?step=1"><button>I’m ready, proceed to step 1 ‘Test the server’</button></a>
    <?php
}

if ($step > 0) {
    $folder_data = is_dir(APPPATH.'data'.DS) ? realpath(APPPATH.'data').DS : APPPATH.'data'.DS;

    $session_save_path = \Arr::get(\Config::load('session', true), 'file.path');

    Test::register(array(
        'directive.htaccess_allow' => array(
            'title'        => 'Your server does not allow .htaccess file',
            'passed'       => empty($base_url) || !empty($_SERVER['HTACCESSALLOW']),
            'description'  => 'If your server uses Apache, check that configuration have '.
                '<code>AllowOverride All</code> for the Novius-OS directory.',
        ),
        'directive.rewrite_module' => array(
            'title'        => 'Server ‘rewrite_module’ must be enabled',
            'passed'       => $base_url == '',
            'description'  => 'Enable ‘rewrite_module’ in your server configuration (probably Apache).',
            'run_only_if'  => empty($base_url) || !empty($_SERVER['HTACCESSALLOW']),
        ),
        'requirements.gd_is_installed' => array(
            'title'        => 'GD is required',
            'passed'       => function_exists("gd_info"),
            'description'  => 'Install the <a href="http://php.net/manual/en/book.image.php" target="_blank">GD library</a>.',
            'run_only_if'  => empty($config['cmd_convert']),
        ),

        'session_path.writeable' => array(
            'title'        => 'Session directory must be writeable',
            'passed'       => is_writable($session_save_path),
            'description'  => OS_WIN ? 'Give write permission to all users on '.$session_save_path : null,
            'command_line' => 'chmod a+w '.$session_save_path,
        ),

        'directive.short_open_tag' => array(
            'title'        => 'PHP configuration directive ‘short_open_tag’ must be on',
            'passed'       => ini_get('short_open_tag') != false,
            'description'  => 'Set <code>short_open_tag = On</code> in <code>'.php_ini_loaded_file().
                '</code>.<br /><em>Why? Because <a href="http://php.net/manual/en/ini.core.php#ini.short-open-tag">'.
                'since PHP 5.4 short_open_tag is always on</a>.</em>',
            'run_only_if'  => version_compare(PHP_VERSION, '5.4.0', '<'),
        ),
        'directive.magic_quotes_gpc' => array(
            'title'        => 'PHP configuration directive ‘magic_quotes_gpc’ must be off',
            'passed'       => ini_get('magic_quotes_gpc') == false,
            'description'  => 'Set <code>magic_quotes_gpc = Off</code> in <code>'.php_ini_loaded_file().'</code>.'.
                '<br /><em>Why? Because <a href="http://php.net/manual/en/info.configuration.php#ini.magic-quotes-gpc">magic_quotes_gpc is deprecated in PHP 5.3 and removed in PHP 5.4</a>.</em>',
            'run_only_if'  => version_compare(PHP_VERSION, '5.4.0', '<'),
        ),
        'folder.config.writeable' => array(
            'title'        => 'APPPATH/config/ must be writeable (temporarily, to write the db.php and crypt.php config files)',
            'passed'       => is_writeable(APPPATH.'config'),
            'description'  => OS_WIN ? 'Give write permission to all users on APPPATH/config/' : null,
            'command_line' => 'chmod a+w '.APPPATH.'config',
            'run_only_if'  => !file_exists(APPPATH.'config'.DS.'db.config.php') or
                !file_exists(APPPATH.'config'.DS.'crypt.config.php'),
        ),

        'folder.cache.writeable' => array(
            'title'        => 'APPPATH/cache/ must be writeable',
            'passed'       => is_writeable(APPPATH.'cache'),
            'description'  => OS_WIN ? 'Give write permission to all users on APPPATH/cache/' : null,
            'command_line' => 'chmod a+w '.APPPATH.'cache',
        ),

        'folder.cache.media.writeable' => array(
            'title'        => 'APPPATH/cache/media/ must be writeable',
            'passed'       => is_writeable(APPPATH.'cache'.DS.'media'),
            'description'  => OS_WIN ? 'Give write permission to all users on APPPATH/cache/media/' : null,
            'command_line' => 'chmod a+w '.APPPATH.'cache'.DS.'media',
            'run_only_if'  => is_dir(APPPATH.'cache'.DS.'media'),
        ),

        'folder.cache.fuelphp.writeable' => array(
            'title'        => 'APPPATH/cache/fuelphp/ must be writeable',
            'passed'       => is_writeable(APPPATH.'cache'.DS.'fuelphp'),
            'description'  => OS_WIN ? 'Give write permission to all users on APPPATH/cache/fuelphp/' : null,
            'command_line' => 'chmod a+w '.APPPATH.'cache'.DS.'fuelphp',
            'run_only_if'  => is_dir(APPPATH.'cache'.DS.'fuelphp'),
        ),

        'folder.data.writeable' => array(
            'title'        => 'APPPATH/data/ must be writeable',
            'passed'       => is_writeable($folder_data),
            'description'  => OS_WIN ? 'Give write permission to all users on APPPATH/data/' : null,
            'command_line' => 'chmod a+w '.$folder_data,
        ),

        'folder.data.config.writeable' => array(
            'title'        => 'APPPATH/data/config/ must be writeable',
            'passed'       => is_writeable($folder_data.'config'),
            'description'  => OS_WIN ? 'Give write permission to all users on APPPATH/data/config/' : null,
            'command_line' => 'chmod a+w '.$folder_data.'config',
            'run_only_if'  => is_dir($folder_data.'config'),
        ),

        'folder.data.media.writeable' => array(
            'title'        => 'APPPATH/data/media/ must be writeable',
            'passed'       => is_writeable($folder_data.'media'),
            'description'  => OS_WIN ? 'Give write permission to all users on APPPATH/data/media/' : null,
            'command_line' => 'chmod a+w '.$folder_data.'media',
            'run_only_if'  => is_dir($folder_data.'media'),
        ),

        'folder.data.tmp' => array(
            'title'        => 'APPPATH/data/temp/ must exist',
            'passed'       => is_dir($folder_data.'temp'),
            'description'  => OS_WIN ? 'Create a directory APPPATH/data/temp/' : null,
            'command_line' => 'mkdir '.$folder_data.'temp',
            'run_only_if'  => !is_dir($folder_data),
        ),

        'folder.metadata.writeable' => array(
            'title'        => 'APPPATH/metadata/ must be writeable',
            'passed'       => is_writeable(APPPATH.'metadata'),
            'description'  => OS_WIN ? 'Give write permission to all users on APPPATH/metadata/' : null,
            'command_line' => 'chmod a+w '.APPPATH.'metadata',
        ),

        'public.htaccess.removed' => array(
            'title'        => 'DOCROOT/.htaccess must be removed',
            'passed'       => !is_file(DOCROOT.'.htaccess')
                || (is_file(NOSROOT.'.htaccess') && !rename(DOCROOT.'.htaccess', DOCROOT.'.htaccess.old')),
            'description'  => OS_WIN ? 'Rename '.DOCROOT.'.htaccess to '.DOCROOT.'.htaccess.old' : null,
            'command_line' => 'mv '.DOCROOT.'.htaccess '.DOCROOT.'.htaccess.old',
            'run_only_if'  => is_file(NOSROOT.'.htaccess'),
        ),

        'public.cache.writeable' => array(
            'title'        => 'DOCROOT/cache/ must be writeable',
            'passed'       => is_writeable(DOCROOT.'cache'),
            'description'  => OS_WIN ? 'Give write permission to all users on DOCROOT/cache/' : null,
            'command_line' => 'chmod a+w '.DOCROOT.'cache',
            'run_only_if'  => is_dir(DOCROOT.'cache'),
        ),

        'public.cache.media.writeable' => array(
            'title'        => 'DOCROOT/cache/media/ must be writeable',
            'passed'       => is_writeable(DOCROOT.'cache'.DS.'media'),
            'description'  => OS_WIN ? 'Give write permission to all users on DOCROOT/cache/media/' : null,
            'command_line' => 'chmod a+w '.DOCROOT.'cache'.DS.'media',
            'run_only_if'  => is_dir(DOCROOT.'cache'.DS.'media'),
        ),

        'public.htdocs.writeable' => array(
            'title'        => 'DOCROOT/htdocs/ must be writeable (to create the symbolic link htdocs/novius-os)',
            'passed'       => is_writeable(DOCROOT.'htdocs'),
            'description'  => OS_WIN ? 'Give write permission to all users on DOCROOT/htdocs/' : null,
            'command_line' => array(
                'chmod a+w '.DOCROOT.'htdocs',
                '# or',
                'ln -s '.Nos\Tools_File::relativePath(DOCROOT.'htdocs', NOVIUSOS_PATH.'htdocs ')
                .' '.DOCROOT.'htdocs'.DS.'novius-os'
            ),
            'run_only_if'  => is_dir(DOCROOT.'htdocs') && !file_exists(DOCROOT.'htdocs'.DS.'novius-os'),
        ),

        'public.media.writeable' => array(
            'title'        => 'DOCROOT/media/ must be writeable',
            'passed'       => is_writeable(DOCROOT.'media'),
            'description'  => OS_WIN ? 'Give write permission to all users on DOCROOT/media/' : null,
            'command_line' => 'chmod a+w '.DOCROOT.'media',
            'run_only_if'  => is_dir(DOCROOT.'media'),
        ),

        'public.data.writeable' => array(
            'title'        => 'DOCROOT/data/ must be writeable',
            'passed'       => is_writeable(DOCROOT.'data'),
            'description'  => OS_WIN ? 'Give write permission to all users on DOCROOT/data/' : null,
            'command_line' => 'chmod a+w '.DOCROOT.'data',
            'run_only_if'  => is_dir(DOCROOT.'data'),
        ),

        'public.htdocs.apps.writeable' => array(
            'title'        => 'DOCROOT/htdocs/apps/ must be writeable',
            'passed'       => is_writeable(DOCROOT.'htdocs'.DS.'apps'),
            'description'  => OS_WIN ? 'Give write permission to all users on DOCROOT/htdocs/apps/' : null,
            'command_line' => 'chmod a+w '.DOCROOT.'htdocs'.DS.'apps',
            'run_only_if'  => file_exists(DOCROOT.'htdocs'.DS.'apps'),
        ),

        'public.static.writeable' => array(
            'title'        => 'DOCROOT/static/ must be writeable (to create the symbolic link static/novius-os)',
            'passed'       => is_dir(DOCROOT.'static') && is_writeable(DOCROOT.'static'),
            'description'  => OS_WIN ? 'Give write permission to all users on DOCROOT/static/' : null,
            'command_line' => array(
                'chmod a+w '.DOCROOT.'static',
                '# or',
                'ln -s '.Nos\Tools_File::relativePath(DOCROOT.'static', NOVIUSOS_PATH.'static')
                .' '.DOCROOT.'static'.DS.'novius-os'
            ),
            'run_only_if'  => is_dir(DOCROOT.'static') && !file_exists(DOCROOT.'static'.DS.'novius-os'),
        ),

        'public.static.apps.writeable' => array(
            'title'        => 'DOCROOT/static/apps/ must be writeable',
            'passed'       => is_dir(DOCROOT.'static'.DS.'apps') && is_writeable(DOCROOT.'static'.DS.'apps'),
            'description'  => OS_WIN ? 'Give write permission to all users on DOCROOT/static/apps/' : null,
            'command_line' => 'chmod a+w '.DOCROOT.'static'.DS.'apps',
            'run_only_if'  => file_exists(DOCROOT.'static'.DS.'apps'),
        ),

        'public.htdocs.nos.valid' => array(
            'title'        => 'DOCROOT/htdocs/novius-os must link to NOSPATH/htdocs',
            'passed'       => \File::is_link(DOCROOT.'htdocs'.DS.'novius-os')
                && realpath(DOCROOT.'htdocs'.DS.'novius-os') == NOVIUSOS_PATH.'htdocs',
            'description'  => OS_WIN ? 'Change DOCROOT/htdocs/novius-os for linked to NOSPATH/htdocs' : null,
            'command_line' => 'ln -s '.Nos\Tools_File::relativePath(DOCROOT.'htdocs', NOVIUSOS_PATH.'htdocs')
                .' '.DOCROOT.'htdocs'.DS.'novius-os',
            'run_only_if'  => file_exists(DOCROOT.'htdocs'.DS.'novius-os'),
        ),

        'public.static.nos.valid' => array(
            'title'        => 'DOCROOT/static/novius-os must link to NOSPATH/static',
            'passed'       => \File::is_link(DOCROOT.'static'.DS.'novius-os')
                && realpath(DOCROOT.'static'.DS.'novius-os') == NOVIUSOS_PATH.'static',
            'description'  => OS_WIN ? 'Change DOCROOT/static/novius-os for linked to NOSPATH/static' : null,
            'command_line' => 'ln -s '.Nos\Tools_File::relativePath(DOCROOT.'static', NOVIUSOS_PATH.'static')
                .' '.DOCROOT.'static'.DS.'novius-os',
            'run_only_if'  => file_exists(DOCROOT.'static'.DS.'novius-os'),
        ),

        'public.static.nos.create' => array(
            'title'        => 'We can’t create the DOCROOT/static/novius-os symbolic link',
            'passed'       => !file_exists(DOCROOT.'static'.DS.'novius-os') && is_writeable(DOCROOT.'static')
                && \File::relativeSymlink(NOVIUSOS_PATH.'static', DOCROOT.'static'.DS.'novius-os'),
            'description'  =>
                OS_WIN_XP ?
                'Sorry, symlinks are not supported natively by your OS (Windows XP).' :
                'Please restart your server with the ‘Run as administrator’ option.',
            'run_only_if'  => !file_exists(DOCROOT.'static'.DS.'novius-os') && is_writeable(DOCROOT.'static'),
        ),
        'public.htdocs.nos.create' => array(
            'title'        => 'We can’t create the DOCROOT/htdocs/novius-os symbolic link',
            'passed'       => !file_exists(DOCROOT.'htdocs'.DS.'novius-os') && is_writeable(DOCROOT.'htdocs')
                && \File::relativeSymlink(NOVIUSOS_PATH.'htdocs', DOCROOT.'htdocs'.DS.'novius-os'),
            // No description because it would be a duplicate with
            // the public/static/novius-os symbolic link (which should have failed too).
            'run_only_if'  => !file_exists(DOCROOT.'htdocs'.DS.'novius-os') && is_writeable(DOCROOT.'htdocs'),
        ),

        'logs.fuel' => array(
            'title'        => 'logs/fuel/ must be writeable',
            'passed'       => is_writeable(NOSROOT.'logs/fuel'),
            'description'  => OS_WIN ? 'Give write permission to all users on logs/fuel/' : null,
            'command_line' => 'chmod a+w '.NOSROOT.'logs/fuel',
        ),
    ));

    ?><div><?php

    Test::reset();

    Test::run('directive.htaccess_allow');
    Test::run('directive.rewrite_module');

    Test::separator();

    Test::run('directive.short_open_tag');
    Test::run('directive.magic_quotes_gpc');

    Test::separator();

    Test::run('requirements.gd_is_installed');

    Test::separator();

    Test::run('public.htaccess.removed');
    Test::run('session_path.writeable');

    Test::separator();

    if (Test::run('folder.config.writeable') && $step == 1) {
        // Create the crypt.config.php file
        Crypt::_init();

        if (empty($config) && !file_exists(APPPATH.'config'.DS.'config.php')) {
            $url = str_replace(array('install.php', '?step=1'), '', ltrim($_SERVER['REQUEST_URI'], '/'));
            $base_url = \Uri::base(false).$url;
            if (!empty($url)) {
                $config['base_url'] = $base_url;
            }

            // Testing common imagick path
            foreach (array('convert', '/usr/bin/convert', '/usr/local/bin/convert') as $convert) {
                exec($convert, $output, $return_value);
                if ($return_value == 0) {
                    $config['cmd_convert'] = $convert;
                }
            }

            if (!empty($config)) {
                File::create(
                    APPPATH.'config'.DS,
                    'config.php',
                    '<?'."php \n\nreturn ".str_replace('  ', '    ', var_export($config, true)).";\n"
                );
            }
        }

    }

    Test::separator();

    if (Test::run('folder.data.writeable')) {
        $dir  = APPPATH.'data'.DS.'temp';
        if (!is_dir($dir)) {
            File::create_dir(APPPATH.'data', 'temp');
            clearstatcache(true, $dir);
        }

    }
    Test::run('folder.data.config.writeable');
    Test::run('folder.data.media.writeable');
    Test::run('folder.data.tmp');

    Test::separator();

    Test::run('folder.cache.writeable');
    Test::run('folder.cache.media.writeable');
    Test::run('folder.cache.fuelphp.writeable');

    if (Test::run('folder.metadata.writeable')) {
        $dir  = APPPATH.'metadata'.DS;
        $files = array(
            'app_installed.php',
            'templates.php',
            'enhancers.php',
            'launchers.php',
            'app_dependencies.php',
            'app_namespaces.php',
            'data_catchers.php'
        );
        foreach ($files as $file) {
            if (!is_file($dir.$file)) {
                File::create($dir, $file, '<?'.'php return array();');
            }
        }
    }

    Test::separator();

    Test::run('public.cache.writeable');
    Test::run('public.cache.media.writeable');

    Test::separator();

    Test::run('public.htdocs.writeable');
    Test::run('public.htdocs.apps.writeable');

    Test::separator();

    Test::run('public.data.writeable');
    Test::run('public.media.writeable');

    Test::separator();

    Test::run('public.static.writeable');
    Test::run('public.static.apps.writeable');

    Test::separator();

    Test::run('public.htdocs.nos.create');
    Test::run('public.static.nos.create');
    Test::separator();

    Test::run('public.htdocs.nos.valid');
    Test::run('public.static.nos.valid');

    Test::separator();

    Test::run('logs.fuel');

    if (!Test::$passed && $step > 1) {
        header('Location: install.php');
        exit();
    }
}

if ($step == 1) {
    ?>
    <h2>Step 1 <span class="outof">/ 4 -</span> Test the server</h2>
    <p>This step is to make sure your server is ready to run Novius OS.</p>
    <?php
    if (Test::$passed) {
        // Warnings validates but display informations
        ?>
        <h3>All tests passed. Your server is compatible with Novius OS.</h3>
        <p><a id="show_tests" href="#">Show the test results</a>.</p>
        <div id="tests" style="display:none;"><?php echo Test::results('success') ?></div>

        <a href="install.php?step=2"><button>Perfect, proceed to step 2 ‘Set up the database’</button></a>
        <?php
    } else {
        $errors = Test::results('error');
        ?>
        <h3>Some tests have failed</h3>
        <?php echo $errors ?>
        <p>All the other tests passed. <a id="show_tests" href="#">Show the full test results</a>.</p>
        <div id="tests" style="display:none;"><?php echo Test::results(array('warning', 'success')) ?></div>

        <h3 id="recap">Let’s fix this</h3>
        <p>Here is your to-do list:</p>
        <ul id="todo">
        <?php
        $recap_with_description = Test::recap(false);
        $recap_with_description = implode('</li><li>', $recap_with_description);
        if (!empty($recap_with_description)) {
            ?>
            <li><?php echo $recap_with_description ?></li>
            <?php
        }

        if (!OS_WIN) {
            $recap_with_command_line = Test::recap(true);
            $recap_with_command_line = implode("\n", $recap_with_command_line);
            if (!empty($recap_with_command_line)) {
                ?>
                <li>Open a terminal, copy and run the following commands:<br />
                <textarea style="width: 800px; height: 80px;"><?php echo $recap_with_command_line ?></textarea><br />
                <em>Unix commands. You may have to adapt them to your OS.</em>
                </li>
                <?php
            }
        }
        $recap_glossary = '';
        if (strpos($errors.$recap_with_description, 'APPPATH') !== -1) {
            $recap_glossary .= '<code>APPPATH</code><em>: '.APPPATH.'</em><br />';
        }
        if (strpos($errors.$recap_with_description, 'DOCROOT') !== -1) {
            $recap_glossary .= '<code>DOCROOT</code><em>: '.DOCROOT.'</em><br />';
        }
        if (!OS_WIN && strpos($recap_with_description, 'chmod a+w') !== -1) {
            $recap_glossary .= '<code>chmod a+w</code><em>: Write permission for all users</em><br />';
        }
        if (!empty($recap_glossary)) {
            ?>
            <li><?php echo $recap_glossary ?></li>
            <?php
        }
        ?>
        </ul>
        <p><a href="install.php?step=1"><button>I’m done, all problems fixed, re-run the tests</button></a></p>
        <?php
    }
    ?>
    <script type="text/javascript">
        var show_tests = document.getElementById('show_tests');
        var tests = document.getElementById('tests');
        show_tests.addEventListener('click', function(e) {
            tests.style.display = (tests.style.display == 'none' ? 'block' : 'none');
        }, false);
    </script>
    <?php
}

if ($step == 2) {
    Config::load('db', true);
    $active = Config::get('db.active');
    $db = Config::get('db.'.$active.'.connection', array());
    // Check database connection
    if (!empty($db['database'])) {
        try {
            $old_level = error_reporting(0);
            // Check credentials
            Database_Connection::instance()->connect();
            error_reporting($old_level);
            header('Location: install.php?step=3');
            exit();
        } catch (\Exception $e) {
            echo '<p class="error">Error : '.$e->getMessage().'</p>';
        }
    }

    if (Input::method() == 'POST') {
        $config = array(
            'active'          => Fuel::$env,
            Fuel::$env => array(
                'type'            => 'mysqli',
                'connection'    => array(
                    'hostname'   => \Input::post('hostname', ''),
                    'database'   => \Input::post('database', ''),
                    'username'   => \Input::post('username', ''),
                    'password'   => \Input::post('password', ''),
                    'persistent' => false,
                    'compress'   => false,
                ),
                'identifier'   => '`',
                'table_prefix' => '',
                'charset'      => 'utf8',
                'enable_cache' => true,
                'profiling'    => true,
            ),
        );

        try {
            Config::load($config, 'db'); // set config inside db and reload the cache
            \View::redirect('errors'.DS.'php_error', NOSPATH.'views/errors/empty.view.php');


            set_time_limit(60);

            // Check credentials
            $old_level = error_reporting(0);
            Migrate::latest('nos', 'package');
            error_reporting($old_level);

            // Install metadata
            Nos\Application::installNativeApplications();

            // Install templates
            \Module::load('noviusos_template_bootstrap');
            $application = Nos\Application::forge('noviusos_template_bootstrap');
            $application->install(false);

            Config::save('local::db', $config);

            $file = APPPATH.'config'.DS.'db.config.php';
            $handle = fopen($file, 'r+');
            if ($handle) {
                $content = fread($handle, filesize($file));
                $content = preg_replace(
                    "`'active' => '".Fuel::$env."'`Uu",
                    "'active' => Fuel::\$env",
                    $content
                );

                ftruncate($handle, 0);
                rewind($handle);
                fwrite($handle, $content);
                fclose($handle);
            }

            header('Location: install.php?step=3');
            exit();

        } catch (\Database_Exception $e) {
            \Log::error($e->getCode().' - '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine());
            $message = $e->getMessage();
            ?>
            <p class="error" title="<?php echo htmlspecialchars($message) ?>">
                <strong>There’s must be an error</strong> in the details you provided, as we can’t connect the database. Please double-check and try again.
            </p>
            <?php

        } catch (\Exception $e) {

            echo '<p class="error">Error : '.$e->getMessage().'</p>';
        }
    }
    ?>
    <h2>Step 2 <span class="outof">/ 4 -</span> Enter the database details</h2>
    <p>
        This step is <strong>not to create</strong> the database. At this stage, it must be ready and its details known. Ask your hosting provider or system administrator if it isn’t.
    </p>
    <form action="" method="POST">
        <p>
            <label for="hostname">MySQL server:</label>
            <input type="text" name="hostname" id="hostname" placeholder="Server address" value="<?php echo Input::post('hostname', \Arr::get($db, 'hostname', '')) ?>" />
            <em>A common server address is <a href="#" onclick="document.getElementById('hostname').value='localhost';">localhost</a>.</em>
        </p>
        <p>
            <label for="username">MySQL username:</label>
            <input type="text" name="username" id="username" placeholder="Username" value="<?php echo Input::post('username', \Arr::get($db, 'username', '')) ?>"  />
        </p>
        <p>
            <label for="password">MySQL password:</label>
            <input type="password" name="password" id="password" placeholder="Password" />
        </p>
        <p>
            <label for="database">Database name:</label>
            <input type="text" name="database" id="database" placeholder="Database" value="<?php echo Input::post('database', \Arr::get($db, 'database', '')) ?>"  />
        </p>
        <p><button type="submit">Save and proceed to step 3 ‘Create the first user account’</button></p>
    </form>
    <?php
}

if ($step == 3) {
    if (Nos\User\Model_User::count() > 0) {
        header('Location: install.php?step=4');
        exit();
    }
    if (\Input::method() == 'POST') {
        try {
            $password = \Input::post('password', '');
            if (empty($password)) {
                throw new Exception('You cannot leave the password blank.');
            }
            if (\Input::post('password', '') != \Input::post('password_confirmation', '')) {
                throw new Exception('The passwords don’t match.');
            }
            $user = new Nos\User\Model_User(array(
                'user_name'      => \Input::post('name', 'Admin name'),
                'user_firstname' => \Input::post('firstname', 'Firstname'),
                'user_email'     => \Input::post('email', ''),
                'user_password'  => \Input::post('password', ''),
                'user_last_connection'  => date('Y-m-d H:i:s'),
                'user_configuration' => serialize(array()),
            ), true);

            $user->save();

            // Authorize available apps
            $role = reset($user->roles);
            $permissions = array(
                'noviusos_page',
                'noviusos_media',
                'noviusos_user',
                'noviusos_appmanager',
                'noviusos_template_variation',
                'noviusos_template_bootstrap',
                'noviusos_menu',
            );
            foreach ($permissions as $app) {
                $access = Nos\User\Model_Permission::forge();
                $access->perm_role_id      = $role->role_id;
                $access->perm_name         = 'nos::access';
                $access->perm_category_key = $app;
                $access->save();
            }

            File::copy(APPPATH.'config'.DS.'contexts.config.php.sample', APPPATH.'config'.DS.'contexts.config.php');

            header('Location: install.php?step=4');
            exit();

        } catch (\Exception $e) {

            echo '<p class="error">'.$e->getMessage().'</p>';
        }
    }
    ?>
    <h2>Step 3 <span class="outof">/ 4 -</span> Create the first user account</h2>
    <p>
        We’re getting there, only two steps to go. Time to create the first administrator account:
    </p>
    <form action="" method="POST">
        <p>
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" placeholder="Name" size="30" value="<?php echo Input::post('name', '') ?>" />
            <em>If you’re on first name terms, you can leave this field blank…</em>
        </p>
        <p>
            <label for="firstname">First name:</label>
            <input type="text" name="firstname" id="firstname" placeholder="Firstname" size="30" value="<?php echo Input::post('firstname', '') ?>" />
            <em>… but do provide a first name.</em>
        </p>
        <p>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" placeholder="Email (used for login)" size="30" value="<?php echo Input::post('email', '') ?>" />
        </p>
        <p>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" placeholder="Password" size="30" />
        </p>
        <p>
            <label for="password_confirmation">Confirm the password:</label>
            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="One more time, just to be sure" size="30" />
        </p>
        <p><button type="submit">Save and proceed to the final step ‘Set up the website’</button></p>
    </form>

    <link rel="stylesheet" href="static/novius-os/admin/vendor/jquery/jquery-password_strength/jquery.password_strength.css" media="all" />
    <script type="text/javascript" src="static/novius-os/admin/vendor/jquery/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="static/novius-os/admin/vendor/jquery/jquery-password_strength/jquery.password_strength.min.js"></script>
    <script type="text/javascript">
        $(function() {
            var $password = $('#password');

            // Password strength
            var strength_id = 'password_strength';
            var $strength = $('<span id="' + strength_id + '"></span>');
            $password.after($strength);

            $password.password_strength({
                container : '#' + strength_id,
                texts : {
                    1 : ' <span class="color"></span><span class="box"></span><span class="box"></span><span class="box"></span> <span class="optional">Insufficient</span>',
                    2 : ' <span class="color"></span><span class="color"></span><span class="box"></span><span class="box"></span> <span class="optional">Weak</span>',
                    3 : ' <span class="color"></span><span class="color"></span><span class="color"></span><span class="box"></span> <span class="optional">Average</span>',
                    4 : ' <span class="color"></span><span class="color"></span><span class="color"></span><span class="color"></span> <span class="optional">Strong</span>',
                    5 : ' <span class="color"></span><span class="color"></span><span class="color"></span><span class="color"></span> <span class="optional">Outstanding</span>'
                }
            });
        });
    </script>
    <?php
}

if ($step == 4) {

    $available = array(
        'en_GB' => 'English',
        'fr_FR' => 'Français',
        'ja_JP' => '日本語',
        'de_DE' => 'Deutsch',
        'es_ES' => 'Español',
        'it_IT' => 'Italiano',
    );

    if (!is_file(APPPATH.'config/contexts.config.php')) {
        \File::copy(APPPATH.'config/contexts.config.php.sample', APPPATH.'config/contexts.config.php');
    }

    if (Input::method() == 'POST') {

        try {

            $languages = \Input::post('languages', array());
            if (empty($languages)) {
                throw new Exception('Please choose at least one language.');
            }

            $contexts = array(
                'sites' => array(
                    'main' => array(
                        'title' => 'Main site',
                        'alias' => 'Main',
                    ),
                ),
            );
            $locales = array();
            foreach ($languages as $locale) {
                list($lang, $country) = explode('_', $locale);
                $locales[$locale] = array(
                    'title' => $locale,
                    'flag' => strtolower($country),
                );
                $contexts['locales'][$locale] = array(
                    'title' => isset($available[$locale]) ? $available[$locale] : $locale,
                    'flag' => strtolower($country),
                );
                $contexts['contexts']['main::'.$locale] = array();
            }

            File::update(
                APPPATH.'config'.DS,
                'contexts.config.php',
                '<?'."php \n\nreturn ".str_replace('  ', '    ', var_export($contexts, true)).";\n"
            );
        } catch (\Exception $e) {
            echo '<p class="error">Error : '.$e->getMessage().'</p>';
        }
        header('Location: install.php?step=4');
        exit();
    }

    $locales = \Nos\Tools_Context::locales();
    ?>
    <h2>Step 4 <span class="outof">/ 4 -</span> Select the languages</h2>

    <p>
        Novius OS allows you to manage <strong>several websites in several languages</strong> out of the box, no plug-in required.
    </p>
    <p>
        Select the languages your content is available in:
    </p>

    <p class="languages_tip">
        Tip: Drag & drop the languages to order them. The first language in the list will be the default language.
    </p>

    <form action="" method="POST">
        <ul id="languages">
    <?php
    foreach (array_unique(array_merge(array_keys($locales), array_keys($available))) as $locale) {
        $flag = \Nos\Tools_Context::flag('main::'.$locale);
        ?>
                <li>
                    <input type="checkbox" name="languages[]" value="<?php echo $locale ?>" id="lang_<?php echo $locale ?>" <?php echo !empty($locales[$locale]) ? 'checked' : '' ?>>
                    <label for="lang_<?php echo $locale ?>"><?php echo $flag ?> <?php echo isset($available[$locale]) ? $available[$locale].' ('.$locale.')' : $locale ?></label>
                </li>
        <?php
    }
    ?>
            <li>
                <input type="checkbox" name="languages[]" value="" id="your_locale" />
                <label>Add another language:</label> <input size="5" id="your_locale_input" placeholder="en_GB" />
            </li>
        </ul>
        <button type="submit" style="font-size: 0.8em; margin-left: 3em;">Save your selection, add more languages</button>

        <p style="margin: 2em 0;">Not sure whether to add a language now? You may need more languages in the future? Don’t let this step stress you!<br />
           <strong>Languages configuration can be changed eventually</strong>. Edit, or ask a developer to edit, <code>local/config/contexts.config.php</code>.</p>

        <p><a href="install.php?step=5"><button type="button">I’m done, finish the installation</button></a></p>
    </form>

    <script type="text/javascript" src="static/novius-os/admin/vendor/jquery/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="static/novius-os/admin/vendor/jquery-ui/minified/jquery.ui.core.min.js"></script>
    <script type="text/javascript" src="static/novius-os/admin/vendor/jquery-ui/minified/jquery.ui.widget.min.js"></script>
    <script type="text/javascript" src="static/novius-os/admin/vendor/jquery-ui/minified/jquery.ui.mouse.min.js"></script>
    <script type="text/javascript" src="static/novius-os/admin/vendor/jquery-ui/minified/jquery.ui.sortable.min.js"></script>
    <script type="text/javascript">
        $(function() {
            var $languages = $('#languages');
            $languages.sortable();
            $languages.disableSelection();

            $languages.find(':checkbox').on('change', function() {
                $(this).closest('li')[$(this).is(':checked') ? 'addClass' : 'removeClass']('checked');
            }).trigger('change');

            $('#your_locale_input')
                .on('focus', function() {
                    $(this).closest('li').find(':checkbox').prop('checked', true).trigger('change');
                })
                .on('blur', function() {
                    var $this = $(this);
                    var val = $this.val();
                    if (val.length == 2) {
                        val = val.toLowerCase() + '_' + val.toUpperCase();
                    }
                    if (val.length !== 5 || val.substr(2, 1) != '_') {
                        $this.val('').trigger('change');
                        $this.nextAll().remove();
                        return;
                    }
                    $this.val(val).trigger('change');
                    var country = val.split('_');
                    var $img = $('<img src="static/novius-os/admin/novius-os/img/flags/' + country[1].toLowerCase() + '.png" />');
                    $img.on('error', function() {
                        $this.nextAll().remove();
                        $this.val('').trigger('change');
                        $this.after('<span class="error">We could not found this language (locale)</span>');
                    })
                    $this.nextAll().remove();
                    $this.after($img);
                }).on('change', function() {
                    var val = $(this).val();
                    $('#your_locale').val(val);
                    $(this).closest('li').find(':checkbox').prop('checked', val != '').trigger('change');
                });
        });
    </script>

    <?php
}

if ($step == 5) {
    ?>
    <h2>Congratulations!</h2>
    <p>Your now have a shiny new Novius OS to work with:</p>

    <p>
        <a href="admin/?tab=admin/noviusos_appmanager/appmanager"><button>Go to the back-office and sign-in<br /><span style="font-size: 0.8em;">You’ll be taken to the applications manager to select the applications you need</span></button></a>
    </p>

    <h3 style="margin-top: 4em;">Cleaning up</h3>
    <p>If you prefer to leave things clean and tidy, you may now:</p>
    <ul>
        <li>Remove or rename this install.php file,</li>
        <li>Remove writing permissions for the <code>local/config/</code> folder (if you changed it during step 1).</li>
    </ul>
    <p>These two operations as Unix commands:</p>
    <textarea style="width:800px; height: 60px;"><?php
    echo 'rm ', NOSROOT, "public/htdocs/install.php\n";
    echo 'chmod og-w ', NOSROOT, "local/config\n";
    ?>
    </textarea>

    <?php
}

?>
</div></div>
<div id="version">Version 5.0.1 (Elche) - July 30, 2014 </div>
</body>
</html>

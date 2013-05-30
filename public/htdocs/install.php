<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Novius OS - Installation</title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="shortcut icon" href="static/novius-os/admin/novius-os/img/noviusos.ico">

    <style type="text/css">
        html {
            height : 100%;
        }
        body {
            /* On step 1, this asset will probably return an HTTP status 404 Not Found */
            background: #ddd url("static/novius-os/admin/novius-os/img/wallpapers/default.jpg");
            background-size: cover;
            font-family: franklin gothic medium,arial,verdana,helvetica,sans-serif;
        }
        #blank_slate {
            background: rgba(255, 255, 255, 0.5);
            border: 1px outset rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            padding: 20px 40px;
            position: absolute;
            top: 50px;
            left: 50px;
            right: 50px;
            bottom: 50px;
            overflow: auto;
        }
        #blank_slate h1, #blank_slate img {
            vertical-align: middle;
            padding: 0 2em 0 1em;
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
            background-color: #fff;
            font-weight: bold;
            text-align: center;
        }
        tr.error td.status {
            color: #f00;
        }
        tr.warning td.status {
            color: #dd9700;
        }
        tr.ok td.status {
            color: #0b0;
        }
        table tr.error {
            background: #fff5f5;
        }
        table tr.warning {
            background: #fff9f0;
        }
        tr.error th, tr.warning th {
            border-bottom: none;
        }
        tr.separator td {
            border:none;
        }
        tr.error td.description, tr.warning td.description {
            border-top: none;
        }
        table tr.ok {
            background: #f5fff5;
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
    </style>
</head>

<body>
<div id="blank_slate">
<?php

define('NOS_ENTRY_POINT', 'install');

$_SERVER['NOS_ROOT'] = realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..');

// Boot the app
require_once $_SERVER['NOS_ROOT'].DIRECTORY_SEPARATOR.'novius-os'.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'bootstrap.php';

Fuel::$profiling = false;

define('NOVIUSOS_PATH', realpath(DOCROOT.'..'.DS.'novius-os').DS);

function run_test($name)
{
    static $results = array();
    $options = $GLOBALS['tests'][$name];
    $GLOBALS['tests'][$name]['is_error'] = false;

    if (!isset($options['warning'])) {
        $options['warning'] = false;
    }

    if (isset($options['run_only_if'])) {
        foreach ((array) $options['run_only_if'] as $s) {
            if (is_bool($s)) {
                if (false === $s) {
                    return true;
                }
            } elseif (!$GLOBALS['tests'][$s]['passed']) {
                return true;
            }
        }
    }

    $results[$name] = $options['passed'];

    $class = $options['passed'] ? 'ok' : ($options['warning'] ? 'warning' : 'error');

    if ($class == 'ok' && !empty($options['hide_success_when'])) {
        return true;
    }

    if ($class == 'error' && isset($options['hide_error_when'])) {
        foreach ((array) $options['hide_error_when'] as $s) {
            if (is_bool($s)) {
                if (true === $s) {
                    return true;
                }
            } elseif ($GLOBALS['tests'][$s]['passed']) {
                return true;
            }
        }
    }
    echo '<tr class="'.$class.'">
        <th>'.$options['title'].'</th>';

    if ($class == 'ok') {
        echo '<td class="status">OK</td>';
    } else {
        $GLOBALS['tests'][$name]['is_error'] = true;
        echo '<td class="status">'.($options['warning'] ? 'Warning' : 'Error').'</td></tr><tr class="'.$class.'"><td class="description" colspan="2">';
        if (!empty($options['description'])) {
            echo '<p class="description">'.$options['description'].'</p>';
        }
        if (!empty($options['command_line'])) {
            echo '<!--To solve this issue, you can execute this in a terminal : --><code>'.(is_array($options['command_line']) ? implode('<br />', $options['command_line']) : $options['command_line']).'</code>';
        }
        if (!empty($options['code'])) {
            echo '<code>'.(is_array($options['code']) ? implode('<br />', $options['code']) : $options['code']).'</code>';
        }
        echo '</td>';
    }
    echo '</tr>';

    return $class != 'error';
}

$folder_data = is_dir(APPPATH.'data'.DS) ? realpath(APPPATH.'data').DS : APPPATH.'data'.DS;

$session_save_path = \Arr::get(\Config::load('session'), 'file.path');

// @todo title_success and title_error?
$tests = array(
    'requirements.gd_is_installed' => array(
        'title'        => 'GD is installed',
        'passed'       => function_exists("gd_info"),
        'description'  => 'Novius OS requires the GD library. Please <a href="http://php.net/manual/en/book.image.php">install it</a>.',
        'warning'      => true,
    ),
    'requirements.is_not_on_windows' => array(
        'title'        => 'The OS is not Windows',
        'passed'       => !defined('PHP_WINDOWS_VERSION_PLATFORM'),
        'description'  => 'Sorry, Novius OS can\'t work on Windows for the moment.',
    ),

    'session_path.writeable' => array(
        'title'        => 'Session directory is writeable',
        'passed'       => is_writable($session_save_path),
        'description'  => 'Current session path : <strong>'.$session_save_path.'</strong>.<br />Please edit your configuration file (session.config.php : file.path key) or run <code>chmod a+w '.$session_save_path.'</code>. ',
    ),

    'directive.short_open_tag' => array(
        'title'        => 'PHP configuration directive short_open_tag = On',
        'passed'       => ini_get('short_open_tag') != false,
        'code'         => '# '.php_ini_loaded_file ()."\n<br />short_open_tag = On",
        'description'  => 'We use short_open_tag, since it\'ll be <a href="http://php.net/manual/en/ini.core.php#ini.short-open-tag">always enabled as of PHP 5.4</a>. Please edit your configuration file.',
        'run_only_if'  => version_compare(PHP_VERSION, '5.4.0', '<'),
    ),
    'directive.magic_quotes_gpc' => array(
        'title'        => 'PHP configuration directive magic_quotes_gpc = Off',
        'passed'       => ini_get('magic_quotes_gpc') == false,
        'code'         => '# '.php_ini_loaded_file ()."\n<br />magic_quotes_gpc = Off",
        'description'  => 'It\'s <a href="http://php.net/manual/en/info.configuration.php#ini.magic-quotes-gpc">deprecated in PHP 5.3 and has been removed in PHP 5.4</a>. Please edit your configuration file.',
        'run_only_if'  => version_compare(PHP_VERSION, '5.4.0', '<'),
    ),
    'folder.config.writeable' => array(
        'title'        => 'APPPATH/config/ is writeable ',
        'passed'       => is_writeable(APPPATH.'config'),
        'command_line' => 'chmod a+w '.APPPATH.'config',
        'description'  => 'This is required temporarly to write the db.php and crypt.php config files',
        'run_only_if'  => !file_exists(APPPATH.'config'.DS.'db.config.php') or  !file_exists(APPPATH.'config'.DS.'crypt.config.php'),
    ),

    'folder.cache.writeable' => array(
        'title'        => 'APPPATH/cache/ is writeable',
        'passed'       => is_writeable(APPPATH.'cache'),
        'command_line' => 'chmod a+w '.APPPATH.'cache',
    ),

    'folder.cache.media.writeable' => array(
        'title'        => 'APPPATH/cache/media is writeable',
        'passed'       => is_writeable(APPPATH.'cache'.DS.'media'),
        'command_line' => 'chmod a+w '.APPPATH.'cache'.DS.'media',
        'run_only_if'  => is_dir(APPPATH.'cache'.DS.'media'),
    ),

    'folder.cache.fuelphp.writeable' => array(
        'title'        => 'APPPATH/cache/fuelphp is writeable',
        'passed'       => is_writeable(APPPATH.'cache'.DS.'fuelphp'),
        'command_line' => 'chmod a+w '.APPPATH.'cache'.DS.'fuelphp',
        'run_only_if'  => is_dir(APPPATH.'cache'.DS.'fuelphp'),
    ),

    'folder.data.writeable' => array(
        'title'        => 'APPPATH/data/ is writeable',
        'passed'       => is_writeable($folder_data),
        'command_line' => 'chmod a+w '.$folder_data,
    ),

    'folder.data.config.writeable' => array(
        'title'           => 'APPPATH/data/config/ is writeable',
        'passed'          => is_writeable($folder_data.'config'),
        'command_line'	  => array('chmod a+w '.$folder_data.'config'),
        'run_only_if'     => is_dir($folder_data.'config'),
    ),

    'folder.data.media.writeable' => array(
        'title'           => 'APPPATH/data/media/ is writeable',
        'passed'          => is_writeable($folder_data.'media'),
        'command_line'	  => array('chmod a+w '.$folder_data.'media'),
        'run_only_if'     => is_dir($folder_data.'media'),
    ),

    'folder.metadata.writeable' => array(
        'title'           => 'APPPATH/metadata/ is writeable',
        'passed'          => is_writeable(APPPATH.'metadata'),
        'command_line'	  => 'chmod a+w '.APPPATH.'metadata',
    ),

    'public.htaccess.removed' => array(
        'title'        => 'DOCROOT/.htaccess is removed',
        'passed'       => !is_file(DOCROOT.'.htaccess'),
        'command_line' => 'mv '.DOCROOT.'.htaccess '.DOCROOT.'.htaccess.old',
        'run_only_if'  => is_file(NOSROOT.'.htaccess'),
    ),

    'public.cache.writeable' => array(
        'title'        => 'DOCROOT/cache/ is writeable',
        'passed'       => is_writeable(DOCROOT.'cache'),
        'command_line' => 'chmod a+w '.DOCROOT.'cache',
        'run_only_if'  => is_dir(DOCROOT.'cache'),
    ),

    'public.cache.media.writeable' => array(
        'title'        => 'DOCROOT/cache/media is writeable',
        'passed'       => is_writeable(DOCROOT.'cache'.DS.'media'),
        'command_line' => 'chmod a+w '.DOCROOT.'cache'.DS.'media',
        'run_only_if'  => is_dir(DOCROOT.'cache'.DS.'media'),
    ),

    'public.htdocs.writeable' => array(
        'title'        => 'DOCROOT/htdocs/ is writeable',
        'description'  => 'The symbolic link htdocs/novius-os doesn\'t exsists, so we need to be able to create it.',
        'passed'       => is_writeable(DOCROOT.'htdocs'),
        'command_line' => array('chmod a+w '.DOCROOT.'htdocs', '# or', 'ln -s '.Nos\Tools_File::relativePath(DOCROOT.'htdocs', NOVIUSOS_PATH.'htdocs ').' '.DOCROOT.'htdocs'.DS.'novius-os'),
        'run_only_if'  => is_dir(DOCROOT.'htdocs') && !file_exists(DOCROOT.'htdocs'.DS.'novius-os'),
    ),

    'public.media.writeable' => array(
        'title'        => 'DOCROOT/media/ is writeable',
        'passed'       => is_writeable(DOCROOT.'media'),
        'command_line' => 'chmod a+w '.DOCROOT.'media',
        'run_only_if'  => is_dir(DOCROOT.'media'),
    ),

    'public.data.writeable' => array(
        'title'        => 'DOCROOT/data/ is writeable',
        'passed'       => is_writeable(DOCROOT.'data'),
        'command_line' => 'chmod a+w '.DOCROOT.'data',
        'run_only_if'  => is_dir(DOCROOT.'data'),
    ),

    'public.htdocs.apps.writeable' => array(
        'title'        => 'DOCROOT/htdocs/apps is writeable',
        'passed'       => is_writeable(DOCROOT.'htdocs'.DS.'apps'),
        'command_line' => 'chmod a+w '.DOCROOT.'htdocs'.DS.'apps',
        'run_only_if'  => file_exists(DOCROOT.'htdocs'.DS.'apps'),
    ),

    'public.static.writeable' => array(
        'title'        => 'DOCROOT/static/ is writeable',
        'description'  => 'The symbolic link static/novius-os/ doesn\'t exsists, so we need to be able to create it.',
        'passed'       => is_dir(DOCROOT.'static') && is_writeable(DOCROOT.'static'),
        'command_line' => array('chmod a+w '.DOCROOT.'static', '# or', 'ln -s '.Nos\Tools_File::relativePath(DOCROOT.'static', NOVIUSOS_PATH.'static').' '.DOCROOT.'static'.DS.'novius-os'),
        'run_only_if'  => is_dir(DOCROOT.'static') && !file_exists(DOCROOT.'static'.DS.'novius-os'),
    ),

    'public.static.apps.writeable' => array(
        'title'        => 'DOCROOT/static/apps is writeable',
        'passed'       => is_dir(DOCROOT.'static'.DS.'apps') && is_writeable(DOCROOT.'static'.DS.'apps'),
        'command_line' => 'chmod a+w '.DOCROOT.'static'.DS.'apps',
        'run_only_if'  => file_exists(DOCROOT.'static'.DS.'apps'),
    ),

    'public.htdocs.nos.valid' => array(
        'title'        => 'DOCROOT/htdocs/novius-os links to NOSPATH/htdocs',
        'passed'       => is_link(DOCROOT.'htdocs'.DS.'novius-os') && realpath(DOCROOT.'htdocs'.DS.'novius-os') == NOVIUSOS_PATH.'htdocs',
        'command_line' => 'ln -s '.Nos\Tools_File::relativePath(DOCROOT.'htdocs', NOVIUSOS_PATH.'htdocs').' '.DOCROOT.'htdocs'.DS.'novius-os',
        'run_only_if'  => file_exists(DOCROOT.'htdocs'.DS.'novius-os'),
    ),

    'public.static.nos.valid' => array(
        'title'        => 'DOCROOT/static/novius-os links to NOSPATH/static',
        'passed'       => is_link(DOCROOT.'static'.DS.'novius-os') && realpath(DOCROOT.'static'.DS.'novius-os') == NOVIUSOS_PATH.'static',
        'command_line' => 'ln -s '.Nos\Tools_File::relativePath(DOCROOT.'static', NOVIUSOS_PATH.'static').' '.DOCROOT.'static'.DS.'novius-os',
        'run_only_if'  => file_exists(DOCROOT.'static'.DS.'novius-os'),
    ),

    'logs.fuel' => array(
        'title'        => 'logs/fuel exists and is writeable',
        'passed'       => is_writeable(NOSROOT.'logs/fuel'),
        'command_line' => 'chmod a+w '.NOSROOT.'logs/fuel',
    ),
);

$passed = true;
echo '<div style="width:800px;margin:auto;">';

ob_start();
echo '<table width="100%">';


$passed = run_test('requirements.gd_is_installed') && $passed;
$passed = run_test('requirements.is_not_on_windows') && $passed;

echo '<tr class="separator"><td colspan="2"></td></tr>';

$passed = run_test('directive.short_open_tag') && $passed;
$passed = run_test('directive.magic_quotes_gpc') && $passed;

echo '<tr class="separator"><td colspan="2"></td></tr>';

$passed = run_test('public.htaccess.removed') && $passed;
$passed = run_test('session_path.writeable') && $passed;

echo '<tr class="separator"><td colspan="2"></td></tr>';

$passed = run_test('folder.config.writeable') && $passed;

echo '<tr class="separator"><td colspan="2"></td></tr>';

$passed = run_test('folder.data.writeable') && $passed;
$passed = run_test('folder.data.config.writeable') && $passed;
$passed = run_test('folder.data.media.writeable') && $passed;

echo '<tr class="separator"><td colspan="2"></td></tr>';

$passed = run_test('folder.cache.writeable') && $passed;
$passed = run_test('folder.cache.media.writeable') && $passed;
$passed = run_test('folder.cache.fuelphp.writeable') && $passed;
$passed = run_test('folder.metadata.writeable') && $passed;

echo '<tr class="separator"><td colspan="2"></td></tr>';

$passed = run_test('public.cache.writeable') && $passed;
$passed = run_test('public.cache.media.writeable') && $passed;

echo '<tr class="separator"><td colspan="2"></td></tr>';

$passed = run_test('public.htdocs.writeable') && $passed;
$passed = run_test('public.htdocs.apps.writeable') && $passed;

echo '<tr class="separator"><td colspan="2"></td></tr>';

$passed = run_test('public.data.writeable') && $passed;
$passed = run_test('public.media.writeable') && $passed;

echo '<tr class="separator"><td colspan="2"></td></tr>';

$passed = run_test('public.static.writeable') && $passed;
$passed = run_test('public.static.apps.writeable') && $passed;

echo '<tr class="separator"><td colspan="2"></td></tr>';

$passed = run_test('public.htdocs.nos.valid') && $passed;
$passed = run_test('public.static.nos.valid') && $passed;

echo '<tr class="separator"><td colspan="2"></td></tr>';

$passed = run_test('logs.fuel') && $passed;

echo '</table>';

$step_1 = ob_get_clean();
$step = \Input::get('step', 1);

if (!$passed && $step != 1) {
    header('Location: install.php');
    exit();
}

if ($step == 1) {
    if (Input::method() == 'POST') {
        try {
            if (!file_exists(APPPATH.'config'.DS.'config.php')) {
                $url = str_replace(array('install.php', '?step=1'), '', ltrim($_SERVER['REQUEST_URI'], '/'));
                $base_url = \Uri::base(false).$url;
                if (!empty($url)) {
                    $config = <<<CONFIG
return array(
    'base_url' => '$base_url',
);
CONFIG;
                    File::create(APPPATH.'config'.DS, 'config.php', '<?'."php \n".$config);
                }
            }

            $dir  = APPPATH.'metadata'.DS;
            $files = array('app_installed.php', 'templates.php', 'enhancers.php', 'launchers.php', 'app_dependencies.php', 'app_namespaces.php', 'data_catchers.php');
            foreach ($files as $file) {
                if (!is_file($dir.$file)) {
                    File::create($dir, $file, '<?'.'php return array();');
                }
            }

            if (!file_exists(DOCROOT.'htdocs'.DS.'novius-os')) {
                \Nos\Tools_File::symlink(Nos\Tools_File::relativePath(DOCROOT.'htdocs', NOVIUSOS_PATH.'htdocs'), DOCROOT.'htdocs'.DS.'novius-os');
            }
            if (!file_exists(DOCROOT.'static'.DS.'novius-os')) {
                \Nos\Tools_File::symlink(Nos\Tools_File::relativePath(DOCROOT.'static', NOVIUSOS_PATH.'static'), DOCROOT.'static'.DS.'novius-os');
            }

            header('Location: install.php?step=2');
            exit();
        } catch (\Exception $e) {

            echo '<p>Error : '.$e->getMessage().'</p>';
        }
    }
    echo '<h2>Step 1 / 4 : checking  pre-requisite</h2>';
    if ($passed) {
        echo $step_1;
        echo '<form method="POST" action="">
            <input type="submit" value="Move on to the next step" />
            </form>';
    } else {
        echo '<p>Please note <a href="#summary">a summary</a> of the commands is available below</p>';
        echo $step_1;
        $summary = array('cd '.NOSROOT, '');
        foreach ($tests as $name => $data) {
            if ($data['is_error'] && (isset($data['command_line_relative']) || isset($data['command_line']))) {
                $cmd = (array) \Arr::get($data, 'command_line_relative', $data['command_line']);
                if (!empty($cmd[1]) && $cmd[1] == '# or') {
                    $cmd = array_slice($cmd, 2);
                }
                foreach ($cmd as $c) {
                    $c = str_replace(NOSROOT, '', $c);
                    $summary[] = $c;
                }
            }
        }
        echo '<h2 id="summary">Command summary</h2>';
        echo '<p>Relative to the root directory: <code>'.NOSROOT.'</code></p>';
        echo '<code style="width: 800px;">'.implode("<br />\n", $summary).'</code>';
        // Create everything missing except config/db.php
        echo '<p><a href="install.php?step=1">Re-run config check</a></p>';
    }
}

if ($step == 2) {
    Config::load('db', true);
    $active = Config::get('db.active');
    $db = Config::get('db.'.$active.'.connection', array());
    if (!empty($db['database'])) {
        try {
            $old_level = error_reporting(0);
            Database_Connection::instance()->connect();
            error_reporting($old_level);
            header('Location: install.php?step=3');
            exit();
        } catch (\Exception $e) {
            echo '<p>Error : '.$e->getMessage().'</p>';
        }
    }

    if (Input::method() == 'POST') {
        $config = array(
            'active'          => Fuel::DEVELOPMENT,
            Fuel::DEVELOPMENT => array(
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
            // Try to connect to the DB
            $old_level = error_reporting(0);
            \View::redirect('errors'.DS.'php_error', NOSPATH.'views/errors/empty.view.php');
            error_reporting($old_level);

            Migrate::latest('nos', 'package');
            Crypt::_init();

            // Install metadata
            Nos\Application::installNativeApplications();

            // Install templates
            \Module::load('noviusos_templates_basic');
            $application = Nos\Application::forge('noviusos_templates_basic');
            $application->install(false);

            Config::save('local::db', $config);

            $file = APPPATH.'config'.DS.'db.config.php';
            $handle = fopen($file, 'r+');
            if ($handle) {
                $content = fread($handle, filesize($file));
                $content = preg_replace(
                    "`'active' => 'development'`Uu",
                    "'active' => Fuel::\$env",
                    $content);

                ftruncate($handle, 0);
                rewind($handle);
                fwrite($handle, $content);
                fclose($handle);
            }

            header('Location: install.php?step=3');
            exit();

        } catch (\Database_Exception $e) {

            $message = $e->getMessage();
            echo '<p>Error : Wrong credentials '.($message ? '('.$message.')' : '').'</p>';

        } catch (\Exception $e) {

            echo '<p>Error : '.$e->getMessage().'</p>';
        }
    }
    ?>
    <h1><img src="static/novius-os/admin/novius-os/img/logo.png"> Step 2 / 4</h1>
    <h2>Configuring the MySQL database</h2>
    <form action="" method="POST">
        <p><label><input type="text" name="hostname" placeholder="Hostname" value="<?= Input::post('hostname', \Arr::get($db, 'hostname', '')) ?>" /></label></p>
        <p><label><input type="text" name="username" placeholder="Username" value="<?= Input::post('username', \Arr::get($db, 'username', '')) ?>"  /></label></p>
        <p><label><input type="password" name="password" placeholder="Password" /></label></p>
        <p><label><input type="text" name="database" placeholder="Database" value="<?= Input::post('database', \Arr::get($db, 'database', '')) ?>"  /></label></p>
        <p><input type="submit" value="Check and save DB config" /></p>
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
                throw new Exception('Empty password is not allowed.');
            }
            if (\Input::post('password', '') != \Input::post('password_confirmation', '')) {
                throw new Exception('The two password don\'t match.');
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
            foreach (array('noviusos_page', 'noviusos_media', 'noviusos_user', 'noviusos_help', 'noviusos_appmanager', 'noviusos_templates_basic') as $app) {
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

            echo '<p>Error : '.$e->getMessage().'</p>';
        }
    }
    ?>
    <h1><img src="static/novius-os/admin/novius-os/img/logo.png"> Step 3 / 4</h1>
    <h2>Create the first administrator account</h2>
    <form action="" method="POST">
        <p><label><input type="text" name="name" placeholder="Name" size="20" value="<?= Input::post('name', '') ?>" /></label></p>
        <p><label><input type="text" name="firstname" placeholder="Firstname" size="20" value="<?= Input::post('firstname', '') ?>" /></label></p>
        <p><label><input type="email" name="email" placeholder="Email / Login" size="30" value="<?= Input::post('email', '') ?>" /></label></p>
        <p><label><input type="password" name="password" placeholder="Password" /></label></p>
        <p><label><input type="password" name="password_confirmation" placeholder="Password confirmation" /></label></p>
        <p><input type="submit" value="Create the first account" /></p>
    </form>
    <?php
}

if ($step == 4) {
    ?>
    <h1><img src="static/novius-os/admin/novius-os/img/logo.png"> Step 4 / 4</h1>

    <h2>Setup contexts</h2>
    <p>
        You can edit your <strong>local/config/contexts.config.php</strong> file to configure the contexts.
    </p>
    <p>
        Currently, the following contexts are set:
    <ul>
    <?php
    foreach (Nos\Tools_Context::contexts() as $context => $domains) {
        echo '<li>'.$context.'</li>';
    }
    ?>
    </ul>
    </p>
    <p><a href="install.php?step=4">Refresh the list</a></p>


    <h2>Cleanup</h2>
    <p>You may want to remove write permissions on the <code>local/config/</code> folder if you set it in the first step.</p>
    <p>Please remove this <code>install.php</code> file.</p>
    <code style="width:800px;">
        rm <?= NOSROOT ?>public/htdocs/install.php<br />
        chmod og-w <?= NOSROOT ?>local/config
    </code>

    <h2>The end!</h2>
    <p><a href="admin/?tab=admin/noviusos_appmanager/appmanager"><button>Go to the administration panel</button></a></p>
    <?php
}

?>
</div>
</body>
</html>

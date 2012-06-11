<?php

define('DOCROOT', __DIR__.DIRECTORY_SEPARATOR);

define('APPPATH',   realpath(DOCROOT.'../local/').DIRECTORY_SEPARATOR);
define('PKGPATH',   realpath(DOCROOT.'../novius-os/packages/').DIRECTORY_SEPARATOR);
define('COREPATH',  realpath(DOCROOT.'../novius-os/fuel-core/').DIRECTORY_SEPARATOR);
define('NOSPATH',   realpath(DOCROOT.'../novius-os/framework/').DIRECTORY_SEPARATOR);


// Boot the app
require_once NOSPATH.'bootstrap.php';

define('ROOT',    realpath(DOCROOT.'../').DS);
define('NOSROOT', realpath(DOCROOT.'../novius-os/').DS);

?>
<style type="text/css">
body {
	font-family: Arial, Helvetica, sans-serif;
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
tr.ok td.status {
	color: #0b0;
}
table tr.error {
	background: #fff5f5;
}
tr.error th {
	border-bottom: none;
}
tr.separator td {
	border:none;
}
tr.error td.description {
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
</style>
<?php
function run_test($name) {

	static $results = array();
	$options = $GLOBALS['tests'][$name];
	$GLOBALS['tests'][$name]['is_error'] = false;

	if (isset($options['run_only_if'])) {
		foreach ((array) $options['run_only_if'] as $s) {
			if (is_bool($s)) {
				if (false === $s) {
					return true;
				}
			}
			else if (!$GLOBALS['tests'][$s]['passed']) {
				return true;
			}
		}
	}

	$results[$name] = $options['passed'];

	$class = $options['passed'] ? 'ok' : 'error';

	if ($class == 'ok' && !empty($options['hide_success_when'])) {
		return true;
	}

	if ($class == 'error' && isset($options['hide_error_when'])) {
		foreach ((array) $options['hide_error_when'] as $s) {
			if (is_bool($s)) {
				if (true === $s) {
					return true;
				}
			}
			else if ($GLOBALS['tests'][$s]['passed']) {
				return true;
			}
		}
	}
	echo '<tr class="'.$class.'">
		<th>'.$options['title'].'</th>';

	if ($class == 'ok')
	{
		echo '<td class="status">OK</td>';
	} else {
		$GLOBALS['tests'][$name]['is_error'] = true;
		echo '<td class="status">Error</td></tr><tr class="'.$class.'"><td class="description" colspan="2">';
		if (!empty($options['description'])) {
			echo '<p class="description">'.$options['description'].'</p>';
		}
		echo '<!--To solve this issue, you can execute this in a terminal : --><code>'.(is_array($options['command_line']) ? implode('<br />', $options['command_line']) : $options['command_line']).'</code>';
		echo '</td>';
	}
	echo '</tr>';
	return $class == 'ok';
}

$folder_data = is_dir(APPPATH.'data'.DS) ? realpath(APPPATH.'data').DS : APPPATH.'data'.DS;

// @todo title_success and title_error?
$tests = array(
	'folder.config.writeable' => array(
		'title'        => 'APPPATH/config/ is writeable  by the webserver',
		'passed'       => is_writeable(APPPATH.'config'),
		'command_line' => 'chmod a+w '.APPPATH.'config',
		'description'  => 'This is required temporarly to write the db.php and crypt.php config files',
        'run_only_if'  => !file_exists(APPPATH.'config'.DS.'db.php') or  !file_exists(APPPATH.'config'.DS.'crypt.php'),
	),

	'folder.data.writeable' => array(
		'title'        => 'APPPATH/data/ is writeable by the webserver',
		'passed'       => is_writeable($folder_data),
		'command_line' => 'chmod a+w '.$folder_data,
	),

	'folder.data.config.writeable' => array(
		'title'           => 'APPPATH/data/config/ is writeable by the webserver',
		'passed'          => is_writeable($folder_data.'config'),
		'command_line'	  => array('chmod a+w '.$folder_data.'config'),
		'run_only_if'     => is_dir($folder_data.'config'),
	),

	'file.data.config.app.exists.writeable' => array(
		'title'        => 'APPPATH/data/config/app_installed.php exists and is writeable',
		'passed'       => is_writeable($folder_data.'config'.DS.'app_installed.php'),
		'command_line' => 'echo "&lt;?php return array();" > '.$folder_data.'config'.DS.'app_installed.php',
		'run_only_if'  => file_exists($folder_data.'config'.DS.'app_installed.php'),
	),

	// htdocs needs to be writeable if htdocs or static doesn't exists
	'public.writeable' => array(
		'description'  => 'Either public/cache/, public/htdocs/ or public/static/ doesn\'t exists, so we need to be able to create them.',
		'title'        => 'DOCROOT is writeable',
		'passed'       => is_writeable(DOCROOT),
		'command_line' => array('chmod a+w '.DOCROOT),
		'run_only_if'  => !file_exists(DOCROOT.'htdocs') || !file_exists(DOCROOT.'static') || !file_exists(DOCROOT.'cache'),
	),


	'public.cache.writeable' => array(
		'title'        => 'DOCROOT/cache/ is writeable by the webserver',
		'passed'       => is_writeable(DOCROOT.'cache'),
		'command_line' => 'chmod a+w '.DOCROOT.'cache',
		'run_only_if'  => is_dir(DOCROOT.'cache'),
	),
	'public.cache.media.writeable' => array(
		'title'        => 'DOCROOT/cache/media is writeable by the webserver',
		'passed'       => is_writeable(DOCROOT.'cache'.DS.'media'),
		'command_line' => 'chmod a+w '.DOCROOT.'cache'.DS.'media',
		'run_only_if'  => is_dir(DOCROOT.'cache'.DS.'media'),
	),

	'public.htdocs.writeable' => array(
		'title'        => 'DOCROOT/htdocs/ is writeable by the webserver',
		'description'  => 'Either the symbolic link htdocs/novius-os or the folder htdocs/apps doesn\'t exsists, so we need to be able to create them.',
		'passed'       => is_dir(DOCROOT.'htdocs') && is_writeable(DOCROOT.'htdocs'),
		'command_line' => array('chmod a+w '.DOCROOT.'htdocs', '# or', 'ln -s '.NOSROOT.'htdocs '.DOCROOT.'htdocs'.DS.'novius-os', 'mkdir '.DOCROOT.'htdocs'.DS.'apps', 'chmod a+w '.DOCROOT.'htdocs'.DS.'apps'),
		'run_only_if'  => is_dir(DOCROOT.'htdocs') && (!file_exists(DOCROOT.'htdocs'.DS.'novius-os') || !file_exists(DOCROOT.'htdocs'.DS.'apps')),
	),

	'public.htdocs.modules.writeable' => array(
		'title'        => 'DOCROOT/htdocs/apps is writeable by the webserver',
		'passed'       => is_writeable(DOCROOT.'htdocs'.DS.'apps'),
		'command_line' => 'chmod a+w '.DOCROOT.'htdocs'.DS.'apps',
		'run_only_if'  => file_exists(DOCROOT.'htdocs'.DS.'apps'),
	),

	'public.static.writeable' => array(
		'title'        => 'DOCROOT/static/ is writeable by the webserver',
		'description'  => 'Either the symbolic link static/novius-os/ or the folder static/apps/ doesn\'t exsists, so we need to be able to create them.',
		'passed'       => is_dir(DOCROOT.'static') && is_writeable(DOCROOT.'static'),
		'command_line' => array('chmod a+w '.DOCROOT.'static', '# or', 'ln -s '.NOSROOT.'static '.DOCROOT.'static'.DS.'novius-os', 'mkdir '.DOCROOT.'static'.DS.'apps', 'chmod a+w '.DOCROOT.'static'.DS.'apps'),
		'run_only_if'  => is_dir(DOCROOT.'static') && (!file_exists(DOCROOT.'static'.DS.'novius-os') || !file_exists(DOCROOT.'static'.DS.'apps')),
	),

	'public.static.modules.writeable' => array(
		'title'        => 'DOCROOT/static/apps is writeable by the webserver',
		'passed'       => is_dir(DOCROOT.'static'.DS.'apps') && is_writeable(DOCROOT.'static'.DS.'apps'),
		'command_line' => 'chmod a+w '.DOCROOT.'static'.DS.'apps',
		'run_only_if'  => file_exists(DOCROOT.'static'.DS.'apps'),
	),

	'public.htdocs.cms.valid' => array(
		'title'        => 'DOCROOT/htdocs/novius-os links to NOSPATH/htdocs',
		'passed'       => is_link(DOCROOT.'htdocs'.DS.'novius-os') && realpath(DOCROOT.'htdocs'.DS.'novius-os') == NOSROOT.'htdocs',
		'command_line' => 'ln -s '.NOSROOT.'htdocs '.DOCROOT.'htdocs'.DS.'novius-os',
		'run_only_if'  => file_exists(DOCROOT.'htdocs'.DS.'novius-os'),
	),

	'public.static.cms.valid' => array(
		'title'        => 'DOCROOT/static/novius-os links to NOSPATH/static',
		'passed'       => is_link(DOCROOT.'static'.DS.'novius-os') && realpath(DOCROOT.'static'.DS.'novius-os') == NOSROOT.'static',
		'command_line' => 'ln -s '.NOSROOT.'static '.DOCROOT.'static'.DS.'novius-os',
		'run_only_if'  => file_exists(DOCROOT.'static'.DS.'novius-os'),
	),

	'logs.fuel' => array(
		'title'        => 'logs/fuel exists and is writeable by the webserver',
		'passed'       => is_dir(ROOT.'logs/fuel') && is_writeable(ROOT.'logs/fuel'),
		'command_line' => array('mkdir -p '.ROOT.'logs/fuel', 'chmod a+w '.ROOT.'logs/fuel'),
		'run_only_if'  => !is_writeable(ROOT.'logs'),
	),

	'folder.local.cache' => array(
		'title'           => 'APPPATH/cache/ exists and is writeable by the webserver',
		'passed'          => is_writeable(APPPATH.'cache'),
		'command_line'	  => array('mkdir '.APPPATH.'cache', 'chmod a+w '.APPPATH.'cache'),
		'run_only_if'     => !is_writeable(APPPATH),
	),

	'folder.local.media' => array(
		'title'           => 'APPPATH/data/media/ exists and is writeable by the webserver',
		'passed'          => is_writeable(APPPATH.'data'.DS.'media'),
		'command_line'	  => array('mkdir '.APPPATH.'data'.DS.'media', 'chmod a+w '.APPPATH.'data'.DS.'media'),
	),
);


$passed = true;
echo '<div style="width:800px;margin:auto;">';

ob_start();
echo '<table width="100%">';

$passed = run_test('folder.config.writeable') && $passed;

echo '<tr class="separator"><td colspan="2"></td></tr>';

$passed = run_test('folder.data.writeable') && $passed;

echo '<tr class="separator"><td colspan="2"></td></tr>';

$passed = run_test('folder.data.config.writeable') && $passed;
$passed = run_test('file.data.config.app.exists.writeable') && $passed;

echo '<tr class="separator"><td colspan="2"></td></tr>';

$passed = run_test('public.writeable') && $passed;

echo '<tr class="separator"><td colspan="2"></td></tr>';

$passed = run_test('public.cache.writeable') && $passed;
$passed = run_test('public.cache.media.writeable') && $passed;

echo '<tr class="separator"><td colspan="2"></td></tr>';

$passed = run_test('public.htdocs.writeable') && $passed;
$passed = run_test('public.htdocs.modules.writeable') && $passed;

echo '<tr class="separator"><td colspan="2"></td></tr>';

$passed = run_test('public.static.writeable') && $passed;
$passed = run_test('public.static.modules.writeable') && $passed;

echo '<tr class="separator"><td colspan="2"></td></tr>';

$passed = run_test('public.htdocs.cms.valid') && $passed;
$passed = run_test('public.static.cms.valid') && $passed;

echo '<tr class="separator"><td colspan="2"></td></tr>';

$passed = run_test('logs.fuel') && $passed;

echo '<tr class="separator"><td colspan="2"></td></tr>';

$passed = run_test('folder.local.cache') && $passed;
$passed = run_test('folder.local.media') && $passed;

// public/cache/media

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
			// local/data
			if (!is_dir(APPPATH.'data')) {
				File::create_dir(APPPATH, 'data');
			}

			if (!is_dir(APPPATH.'data'.DS.'config')) {
				File::create_dir(APPPATH.'data', 'config');
			}

			$dir  = APPPATH.'data'.DS.'config'.DS;
            $files = array('app_installed.php', 'templates.php', 'launchers.php', 'app_dependencies.php');
            foreach ($files as $file) {
                if (!is_file($dir.$file)) {
                    File::create($dir, $file, '<?'.'php return array();');
                }
            }

			// public/cache
			if (!is_dir(DOCROOT.'cache')) {
				File::create_dir(DOCROOT, 'cache');
			}
			if (!is_dir(DOCROOT.'cache'.DS.'media')) {
				File::create_dir(DOCROOT.'cache', 'media');
			}

			// public/htdocs
			if (!is_dir(DOCROOT.'htdocs')) {
				File::create_dir(DOCROOT, 'htdocs');
			}
			if (!is_dir(DOCROOT.'htdocs'.DS.'apps')) {
				File::create_dir(DOCROOT.'htdocs', 'apps');
			}
			if (!file_exists(DOCROOT.'htdocs'.DS.'novius-os')) {
				File::symlink(NOSROOT.'htdocs', DOCROOT.'htdocs'.DS.'novius-os', false);
			}

			// public/static
			if (!is_dir(DOCROOT.'static')) {
				File::create_dir(DOCROOT, 'static');
			}
			if (!is_dir(DOCROOT.'static'.DS.'apps')) {
				File::create_dir(DOCROOT.'static', 'apps');
			}
			if (!file_exists(DOCROOT.'static'.DS.'novius-os')) {
				File::symlink(NOSROOT.'static', DOCROOT.'static'.DS.'novius-os', false);
			}

			// local/cache
			if (!is_dir(APPPATH.'cache')) {
				File::create_dir(APPPATH, 'cache');
			}
			if (!is_dir(APPPATH.'cache'.DS.'media')) {
				File::create_dir(APPPATH.'cache', 'media');
			}

			// local/media
			if (!is_dir(APPPATH.'data'.DS.'media')) {
				File::create_dir(APPPATH, 'data'.DS.'media');
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
		$first = true;
		$summary = array('cd '.ROOT, '');
		foreach ($tests as $name => $data) {
			if ($data['is_error']) {
				$cmd = (array) $data['command_line'];
				if (!empty($cmd[1]) && $cmd[1] == '# or') {
					$cmd = array_slice($cmd, 2);
				}
				if ($first) {
					$first = false;
				} else {
					$summary[] = '';
				}
				foreach ($cmd as $c) {
					$p = strrpos($c, ROOT);
					if (!empty($p)) {
						$c = substr_replace($c, '', $p, strlen(ROOT));
					}
					$summary[] = $c;
				}
			}
		}
		echo '<h2 id="summary">Command summary</h2>';
		echo '<p>Relative to the root directory: <code>'.ROOT.'</code></p>';
		echo '<code style="width: 800px;">'.implode("<br />\n", $summary).'</code>';
		// Create everything missing except config/db.php
		echo '<p><a href="install.php?step=1">Re-run config check</a></p>';
	}
}

if ($step == 2) {
	if (is_file(APPPATH.'config'.DS.'db.php')) {
		$include = include APPPATH.'config'.DS.'db.php';
		if ($include != 1) {
			header('Location: install.php?step=3');
			exit();
		}
	}
	if (Input::method() == 'POST') {
		$config = array(
			'type'            => 'mysql',
			'connection'    => array(
				'hostname'   => \Input::post('hostname', ''),
				'database'   => \Input::post('database', ''),
				'username'   => \Input::post('username', ''),
				'password'   => \Input::post('password', ''),
				'persistent' => false,
			),
			'table_prefix' => '',
			'charset'      => 'utf8',
			'caching'      => false,
			'profiling'    => false,
		);
		try {
			$connection = \Database_Connection::instance(Fuel::DEVELOPMENT, $config);
			$connection->connect();

			$sql_create_tables = \File::read(APPPATH.'data'.DS.'install'.DS.'create_tables.sql', true );

			foreach(explode(';', $sql_create_tables) as $sql) {
				if (!empty($sql) && trim($sql) != '') {
					$connection->query(null, $sql, false);
				}
			}
			Config::save('db', array(
				'active'          => Fuel::DEVELOPMENT,
				Fuel::DEVELOPMENT => $config,
			));
			Crypt::_init();

			header('Location: install.php?step=3');
			exit();

		} catch (\Exception $e) {

			echo '<p>Error : '.$e->getMessage().'</p>';
		}
	}
	?>
	<h2>Step 2 / 4 : Configuring MySQL</h2>
	<form action="" method="POST">
		<p><label>Hostname: <input type="text" name="hostname" value="<?= Input::post('hostname', '') ?>" /></label></p>
		<p><label>Username: <input type="text" name="username" value="<?= Input::post('username', '') ?>"  /></label></p>
		<p><label>Password: <input type="password" name="password" /></label></p>
		<p><label>Database: <input type="text" name="database" value="<?= Input::post('database', '') ?>"  /></label></p>
		<p><input type="submit" value="Check and save DB config" /></p>
	</form>
	<?php
}


if ($step == 3) {
	if (Nos\Model_User::count() > 0) {
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
			$user = new Nos\Model_User(array(
				'user_name'      => \Input::post('name', 'Admin name'),
				'user_firstname' => \Input::post('firstname', 'Firstname'),
				'user_email'     => \Input::post('email', ''),
				'user_password'  => \Input::post('password', ''),
				'user_last_connection'  => date('Y-m-d H:i:s'),
                'user_configuration' => serialize(array(
                    'tabs'=> array(
                        'selected' => 1,
                    )
                )),
			), true);

			$user->save();

            // Authorize available apps
            $role = reset($user->roles);
            foreach (array('nos_page', 'nos_media', 'nos_user', 'nos_tray') as $app) {
                $access = Nos\Model_User_Permission::forge();
                $access->perm_role_id     = $role->role_id;
                $access->perm_application = 'access';
                $access->perm_identifier  = '';
                $access->perm_key         = $app;
                $access->save();
            }

			header('Location: install.php?step=4');
			exit();

		} catch (\Exception $e) {

			echo '<p>Error : '.$e->getMessage().'</p>';
		}
	}
	?>
	<h2>Step 3 / 4 : Create the first administrator account</h2>
	<form action="" method="POST">
		<p><label>Name: <input type="text" name="name" value="<?= Input::post('name', 'Admin name') ?>" /></label></p>
		<p><label>Firstname: <input type="text" name="firstname" value="<?= Input::post('firstname', 'Firstname') ?>" /></label></p>
		<p><label>Email (login): <input type="email" name="email" value="<?= Input::post('email', 'admin@'.Input::server('server_name', 'domain.com')) ?>" /></label></p>
		<p><label>Password: <input type="password" name="password" /></label></p>
		<p><label>Password (confirmation): <input type="password" name="password_confirmation" /></label></p>
		<p><input type="submit" value="Create the new account" /></p>
	</form>
	<?php
}

if ($step == 4) {
	?>
	<h2>Step 4 / 4 : Almost there...</h2>
	<p>You may want to remove write permissions on the <code>local/config/</code> folder if you set it in the first step.</p>
	<p>Please remove this <code>install.php</code> file.</p>
	<code style="width:800px;">
	rm <?= ROOT ?>public/install.php<br />
	chmod og-w <?= ROOT ?>local/config
	</code>
	<p>You can also edit <code>.htaccess</code> and remove the line containing <code>install.php</code>

	<h2>Setup languages</h2>
    <p>
        You can edit your <strong>local/config/config.php</strong> file to configure the locales.
    </p>
    <p>
        Currently, the following languages are set:
        <ul>
            <?php
            foreach (\Config::get('locales') as $lang) {
                echo '<li>'.$lang.'</li>';
            }
            ?>
        </ul>
    </p>
    <p><a href="install.php?step=4">Refresh the page and the list of languages</a></p>

	<h2>Installation is now complete!</h2>
	<p><a href="admin/">Go to the administration panel</a></p>
	<?php
}


echo '</div>';

<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

// Load in the Autoloader
require COREPATH.'classes'.DIRECTORY_SEPARATOR.'autoloader.php';
class_alias('Fuel\\Core\\Autoloader', 'Autoloader');

// Bootstrap the framework DO NOT edit this
require_once COREPATH.'bootstrap.php';

Autoloader::add_classes(array(
	// Add classes you want to override here
	// Example: 'View' => APPPATH.'classes/view.php',
	'Date'           => CMSPATH.'classes'.DIRECTORY_SEPARATOR.'fuel'.DIRECTORY_SEPARATOR.'date.php',
    'Config'         => CMSPATH.'classes'.DIRECTORY_SEPARATOR.'fuel'.DIRECTORY_SEPARATOR.'config.php',
    'Session'        => CMSPATH.'classes'.DIRECTORY_SEPARATOR.'fuel'.DIRECTORY_SEPARATOR.'session.php',
	'Fuel'           => CMSPATH.'classes'.DIRECTORY_SEPARATOR.'fuel'.DIRECTORY_SEPARATOR.'fuel.php',
	'Finder'         => CMSPATH.'classes'.DIRECTORY_SEPARATOR.'fuel'.DIRECTORY_SEPARATOR.'finder.php',
	'Fieldset'       => CMSPATH.'classes'.DIRECTORY_SEPARATOR.'fuel'.DIRECTORY_SEPARATOR.'fieldset.php',
	'Fieldset_Field' => CMSPATH.'classes'.DIRECTORY_SEPARATOR.'fuel'.DIRECTORY_SEPARATOR.'fieldset_field.php',
	'Format'         => CMSPATH.'classes'.DIRECTORY_SEPARATOR.'fuel'.DIRECTORY_SEPARATOR.'format.php',
	'Response'       => CMSPATH.'classes'.DIRECTORY_SEPARATOR.'fuel'.DIRECTORY_SEPARATOR.'response.php',
	'Cms\Orm\Query'  => CMSPATH.'classes'.DIRECTORY_SEPARATOR.'fuel'.DIRECTORY_SEPARATOR.'orm'.DIRECTORY_SEPARATOR.'query.php',
));

function __($_message, $default = null)
{
    return \Cms\I18n::get($_message, $default);
}

// Register the autoloader
Autoloader::register();

/**
 * Your environment.  Can be set to any of the following:
 *
 * Fuel::DEVELOPMENT
 * Fuel::TEST
 * Fuel::STAGE
 * Fuel::PRODUCTION
 */
if (\Input::server('SERVER_NAME') == 'os1.novius.fr') {
	$_SERVER['FUEL_ENV'] = Fuel::STAGE;
}
Fuel::$env = (isset($_SERVER['FUEL_ENV']) ? $_SERVER['FUEL_ENV'] : Fuel::DEVELOPMENT);

//* Register module autoloader
spl_autoload_register(function($class) {
	$class = ltrim($class, '\\');
	list($module, $whatever) = explode('\\', $class.'\\');
	$module = Inflector::words_to_upper($module);

	// A module can be put inside any namespace when properly configured
	if (!empty(Fuel::$namespace_aliases[$module])) {
		if (class_exists(Fuel::$namespace_aliases[$module].'\\'.$whatever)) {
			class_alias(Fuel::$namespace_aliases[$module].'\\'.$whatever, $class);
		}
		if (class_exists($class, false)) {
			return true;
		}
	}
	return false;
}, true, false);
//*/

// Initialize the framework with the config file.
$config_novius = include(CMSPATH.'config/config.php');
$routes_novius = include(CMSPATH.'config/routes.php');
$config_app    = include(APPPATH.'config/config.php');

Fuel::init(Arr::merge($config_novius, array('routes' => $routes_novius), $config_app));

Autoloader::add_namespace('Cms', CMSPATH.'classes'.DS);
Autoloader::add_namespace('App', APPPATH.'classes'.DS);

Config::load('namespaces', true);

foreach (Config::get('namespaces', array()) as $ns => $path) {
	Autoloader::add_namespace($ns, APPPATH.'..'.DS.$path.'classes'.DS);
}

chdir(DOCROOT);

define('URL_ADMIN', Uri::base(false).'admin/');
define('PHP_BEGIN', '<?php ');
define('PHP_END', ' ?>');

require_once CMSPATH.'classes'.DS.'cms.php';

// Site bootstrap
if (is_file(APPPATH.'bootstrap.php')) {
	require_once APPPATH.'bootstrap.php';
}

define('CACHE_DURATION_PAGE',     5);
define('CACHE_DURATION_FUNCTION', 10);

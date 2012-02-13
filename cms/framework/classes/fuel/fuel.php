<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

class Fuel extends Fuel\Core\Fuel {

	protected static $dependencies = array();

	public static $namespace_aliases = array();

	// We have a different base url because we changed the index.php
	protected static function generate_base_url()
	{
		$base_url = parent::generate_base_url();
		return str_replace('htdocs/cms/', '', $base_url);
	}

	/**
	 * Check the existence of a module
	 * @param   string  $name  Name ofthe module. Both news and module\news are valid
	 * @return  bool    Does the module exists?
	 */
	public static function module_exists($name) {

		if ($name == 'cms' || $name == 'app') {
			return true;
		}

		\Config::load(APPPATH.'data'.DS.'config'.DS.'app_installed.php', 'app_installed');
		$app_installed = \Config::get('app_installed', array());

		if (isset($app_installed[$name])) {
            return parent::module_exists($name);
        }
        return false;
	}

    public static function add_module($name) {

		if ($name == 'cms' || $name == 'app') {
			return;
		}


		static $added_modules = array();

		if ($path = parent::add_module($name)) {

			// Don't add twice
			if (!empty($added_modules[$name])) {
				return;
			}
			$added_modules[$name] = true;

			$namespace = Inflector::words_to_upper($name);

			Autoloader::add_namespaces(array(
				$namespace                  => $path.'classes'.DS,
				//strtolower($namespace)      => $path.'classes'.DS,
				//'\\'.$namespace             => $path.'classes'.DS,
				//'\\'.strtolower($namespace) => $path.'classes'.DS,
			), true);

			// Load the config (namespace + dependencies)
			Config::load("$name::config", true);
			$config = Config::get("$name::config", array());

			if (!empty($config['namespace'])) {
				Autoloader::add_namespaces(array(
					$config['namespace'] => $path.'classes'.DS,
				), true);
				// Allow autoloading from bootstrap to alias classes from this namespace
				self::$namespace_aliases[$namespace] = $config['namespace'];
			}
			Config::load("modules/$name", "$name::config");

			// Load the bootstrap if it exists
			if (is_file($path.'bootstrap.php')) {
				static::load($path.'bootstrap.php');
			}


			// Load dependent moduless
			Config::load(APPPATH.'data'.DS.'config'.DS.'modules_dependencies.php', true);
			$dependencies = Config::get('modules_dependencies', array());
			if (!empty($dependencies[$name])) {
				foreach ($dependencies[$name] as $module) {
					static::add_module($module);
				}
			}
		}
	}
}

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

		return isset($app_installed[$name]) && parent::module_exists($name);
	}


	public static function add_module($name, $already_extended = array()) {
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

            // If the module is extending an other one, we load the extended module and get its configuration
            if (isset($config['extends'])) {
                // When we add the extended module, we send the extended classes
                // so that he only combine the unextended class to the namespace
                static::add_module($config['extends'], $config['classes']);

                Config::load($config['extends']."::config", true);
                $config_extension = Config::get($config['extends']."::config", array());
            }


            // We try to resolve the namespace. We get :
            // - the defined namespace in configuration file
            // - or the defined namespace in the extended module configuration file
            $to_namespace = null;
            if (!empty($config['namespace'])) {
                $to_namespace = $config['namespace'];
            } else if (!empty($config['extends'])) {
                $to_namespace = $config_extension['namespace'];
            }

			if ($to_namespace != null) {
                /*
				Autoloader::add_namespaces(array(
					$config['namespace'] => $path.'classes'.DS,
				), true);
                 */
                // Instead of combining the entire module folder to the namespace, we alias each declared class
                // except those that have already been extended
                foreach($config['classes'] as $class) {
                    if (!in_array($class, $already_extended)) {
                        Autoloader::alias_to_namespace($namespace.'\\'.$class, $to_namespace);
                    }
                }
				// Allow autoloading from bootstrap to alias classes from this namespace
				self::$namespace_aliases[$namespace] = $to_namespace;
			}
			Config::load("modules/$name", "$name::config");

			// Load the bootstrap if it exists
			if (is_file($path.'bootstrap.php')) {
				static::load($path.'bootstrap.php');
			}

			// Load dependent moduless
			Config::load('modules_dependencies', true);
			$dependencies = Config::get('modules_dependencies', array());
			if (!empty($dependencies[$name])) {
				foreach ($dependencies[$name] as $module) {
					static::add_module($module);
				}
			}
		}
	}
}

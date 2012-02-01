<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

class Finder extends Fuel\Core\Finder {

	public static function instance()
	{
		if ( ! static::$instance)
		{
			static::$instance = static::forge(array(APPPATH, CMSPATH, COREPATH));
		}

		return static::$instance;
	}

	protected static function normalize_namespace($name) {
		return implode('\\', array_map(function($a) {
			return Inflector::words_to_upper($a);
		}, explode('\\', $name)));
	}

	/**
	 *
	 * @param   string  $directory  Directory to search into
	 * @param   string  $file       Base name of the file
	 * @param   string  $ext        .php
	 * @param   bool    $multiple   false
	 * @param   bool    $cache      true
	 * @return  string | array
	 */
	public function locate($directory, $file, $ext = '.php', $multiple = false, $cache = true)
	{
		list($section,) = explode('/', $directory,  2);

		// Do we need to override the default behaviour?
		if ($file[0] === '/' or $file[1] === ':' or !in_array($section, array('views', 'config', 'lang'))) {
			return parent::locate($directory, $file, $ext, $multiple, $cache);
		}

		$context = false;
		if ($directory == 'config') {
			// DEBUG_BACKTRACE_IGNORE_ARGS, 5
			$dbt = debug_backtrace();
			foreach ($dbt as $context) {
				if (!empty($context['class']) && $context['class'] == 'Fuel\Core\Config' && !empty($context['function'])) {
					if (in_array($context['function'], array('load', 'save'))) {
						$context = 'config.'.$context['function'];
					}
					break;
				}
			}
		}

		$search = array();
		$found  = array();

		// Init namespace and active module
		$is_namespaced = strripos($file, '::');

		if (false === $is_namespaced) {
			$request        = class_exists('Request', false) ? $request = Request::active() : false;
			$namespace      = false;
			$file_no_ns     = $file;
			$active_module  = $request ? $request->module : false;
			if ($active_module) {
				$namespace_path = \Autoloader::namespace_path(self::normalize_namespace($active_module));
			}
		} else {
			$namespace         = self::normalize_namespace(substr($file, 0, $is_namespaced));
			$file_no_ns        = substr($file, $is_namespaced + 2);
			$active_module     = false;
			\Fuel::add_module(strtolower($namespace));
			$namespace_path    = \Autoloader::namespace_path($namespace);
		}

		$local_config_path = APPPATH.$directory.DS;
		if ($is_namespaced) {
			$local_config_path .= ($active_module != 'cms' ? 'modules'.DS.$active_module : 'cms').DS;
		}
		if ($context == 'config.save') {
			$search = array($local_config_path);
		} else {

			if ($active_module == 'cms' && $directory == 'views') {
				$search[] = APPPATH.$directory.DS.'cms'.DS;
			}

			// -8 = strip the classes directory
			if (!empty($namespace_path)) {
				$search[] = substr($namespace_path, 0, -8).$directory.DS;
			}

			if ($active_module && $active_module != 'cms') {
				$search[] = CMSPATH.$directory.DS;
			}
			if ($context == 'config.load') {
				$search[] = $local_config_path;
			}
		}

		foreach ($search as $path) {
			// We now only have absolute paths, search through them
			if (is_file($path.$file_no_ns.$ext)) {
				$found[] = $path.$file_no_ns.$ext;
			}
		}

		// Fallback for standard search
		if (!$found) {
			// If a config has to be written it HAS to  be within the APPPATH
			if ($context == 'config.save') {
				if (!is_dir($search[0])) {
					File::create_dir(dirname($search[0]), basename($search[0]));
				}
				return $search[0].$file_no_ns.$ext;
			} else if (!$is_namespaced) {
				$found = parent::locate($directory, $file, $ext, $multiple, $cache);
			}
		}

		if (is_array($found) && !$multiple) {
			$found = isset($found[0]) ? $found[0] : false;
		}
/*
			echo '<pre>';
			print_r(array(
				func_get_args(),
				(int) $is_namespaced,
				array($directory, $file, $ext, $multiple, $cache),
				static::$_paths,
				$search,
				$found,
			));
			echo '</pre>';//*/

		return $found;
	}
}

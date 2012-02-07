<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

class Autoloader extends Fuel\Core\Autoloader {
    /*
    static $alreadyLoadedClasses = array();
    public static function load($class)
	{
        if (!(substr($class, 0, 3) == 'Cms' && static::$alreadyLoadedClasses[$class])) {
            $loaded = parent::load($class);
        }

        if (substr($class, 0, 3) == 'Cms' && static::$alreadyLoadedClasses[$class]) {
            $loaded = 1;
        }

        print_r(static::$alreadyLoadedClasses[$class]);

        if (substr($class, 0, 3) == 'Cms') {
            static::$alreadyLoadedClasses[$class] = true;
        }

        return $loaded;
    }
     *
     */
/*
    public static function load($class)
	{
        echo $class."\n";
		// deal with funny is_callable('static::classname') side-effect
		if (strpos($class, 'static::') === 0)
		{
			// is called from within the class, so it's already loaded
			return true;
		}

		$loaded = false;
		$class = ltrim($class, '\\');
		$namespaced = ($pos = strripos($class, '\\')) !== false;

		if (empty(static::$auto_initialize))
		{
			static::$auto_initialize = $class;
		}

		if (array_key_exists($class, static::$classes))
		{
			include_once str_replace('/', DS, static::$classes[$class]);
			static::init_class($class);
			$loaded = true;
		}
		elseif ($full_class = static::find_core_class($class))
		{
			if ( ! class_exists($full_class, false) and ! interface_exists($full_class, false))
			{
				include_once static::prep_path(static::$classes[$full_class]);
			}
			class_alias($full_class, $class);
			static::init_class($class);
			$loaded = true;
		}
		else
		{
			$full_ns = substr($class, 0, $pos);

			if ($full_ns)
			{
                echo ("FOREACH NAMESPACE\n");
				foreach (static::$namespaces as $ns => $path)
				{

                    echo($ns."\n");
					$ns = ltrim($ns, '\\');
					if (stripos($full_ns, $ns) === 0)
					{
						$path .= static::class_to_path(
							substr($class, strlen($ns) + 1),
							array_key_exists($ns, static::$psr_namespaces)
						);
						if (is_file($path))
						{
							require_once $path;
							static::init_class($class);
							$loaded = true;
							break;
						}
					}
				}
                echo ("END FOREACH NAMESPACE\n");
			}

			if ( ! $loaded)
			{
				$path = APPPATH.'classes/'.static::class_to_path($class);

				if (file_exists($path))
				{
					include_once $path;
					static::init_class($class);
					$loaded = true;
				}
			}
		}

		// Prevent failed load from keeping other classes from initializing
		if (static::$auto_initialize == $class)
		{
			static::$auto_initialize = null;
		}

		return $loaded;
	}
*/
}

?>

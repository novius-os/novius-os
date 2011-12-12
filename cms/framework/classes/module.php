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

class Module {

    public static $config_items = array();

    public static function load_config($module) {
        if (isset(static::$config_items[$module])) {
            return static::$config_items[$module];
        }



        static::$config_items[$module] = $config;
        return $config;
    }

    public static function save_config($module, $config) {

    }

    public static function generate_merged_config() {

    }

    protected static function read_config($module, $path = null) {
        $search = array($path.DS.'config', APPPATH.'config'.DS.'modules'.DS.$module);
        return static::load_config($module, true); // $reload ?
    }

    /**
     *
     * @param string $where :
     *  - not_installed
     *  - installed
     *  - site (local)
     *  - repository = not_installed + installed
     *  - available = site + installed
     *  - all =  site + installed + not_installed
     */
    public static function list_from($where, $hierarchy = false) {

        static $list = array();
        static $aliases = array(
            'all'        => array('site', 'installed', 'not_installed'),
            'repository' => array('not_installed', 'installed'),
            'available'  => array('installed', 'site'),
        );

        // Aliases
        if (isset($aliases[$where])) {
            $list = array();
            foreach ($aliases[$where] as $w) {
                if ($hierarchy) {
                    $list[$w] = static::list_from($w);
                } else {
                    $list = array_merge($list, static::list_from($w));
                }
            }
            return $list;
        } else if (isset($from[$where])) {
            return $from[$where];
        }

        // Fetch all the modules
        $list = array(
            'not_installed' => array(),
            'installed'     => array(),
            'site'          => array(),
        );

        $modules_path    = \Config::get('modules.path');
        $repository_path = \Config::get('modules.repository_path');

        $list = array();

        if ($modules_path) {
            foreach (array_keys(\File::read_dir($modules_path, 1)) as $module) {
                $list[$module] = array(
                    'path' => $modules_path.$module,
                    'type' => is_link($modules_path.$module) ? 'installed' : 'site',
                );
            }
        }

        if ($repository_path) {
            foreach (array_keys(\File::read_dir($repository_path, 1)) as $module) {
                // Ignore installed plugins
                if (!isset($list[$module])) {
                    $list[$module] = array(
                        'path' => $repository_path.$module,
                        'type' => 'not_installed',
                    );
                }
            }
        }

        foreach ($list as $module => $details) {
            $config = static::read_config($module, $details['path']);
            $list['module']['name']    = $config['name'];
            $list['module']['version'] = $config['version'];
        }

        foreach ($list as $module => $details) {
            $from[$module['type']][$module] = $details;
        }

        return $from[$where];
    }

	public static function forge($module_name) {
		return new static($module_name);
	}

	public $name;

	public function __construct($module_name) {
		$this->name = $module_name;
	}

	public function install() {
		return $this->check_install() ||
			($this->symlink('static') && $this->symlink('htdocs') && $this->symlink('data') && $this->symlink('cache'));
	}

	public function uninstall() {
		return $this->unsymlink('static')
		&& $this->unsymlink('htdocs')
		&& $this->unsymlink('data')
		&& $this->unsymlink('cache');
	}

	public function check_install() {
		return is_dir(APPPATH.'modules'.DS.$this->name)
		&& $this->is_link('static')
		&& $this->is_link('htdocs')
		&& $this->is_link('data')
		&& $this->is_link('cache');
	}

	protected function symlink($folder) {
		if (!$this->is_link($folder)) {
			$private = APPPATH.'modules'.DS.$this->name.DS.$folder;
			if (is_dir($private)) {
				$public = DOCROOT.$folder.DS.'modules'.DS.$this->name;
				\Debug::dump(array($private, $public));
				return symlink($private, $public);
			}
		}
		return true;
	}

	protected function unsymlink($folder) {
		$public = DOCROOT.$folder.DS.'modules'.DS.$this->name;
		if (file_exists($public)) {
			return unlink($public);
		}
		return true;
	}

	protected function is_link($folder) {
		$private = APPPATH.'modules'.DS.$this->name.DS.$folder;
		if (file_exists($private)) {
			$public = DOCROOT.$folder.DS.'modules'.DS.$this->name;
			return is_link($public);
		}
		return true;
	}
}
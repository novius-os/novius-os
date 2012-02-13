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

class Controller_Extendable extends \Controller {
    protected $config = array();

    public function before() {
        $this->config = \Arr::merge($this->config, $this->getConfiguration());
    }

    public function after($response) {
        if (isset($this->config['assets'])) {
            if (isset($this->config['assets']['paths'])) {
                foreach ($this->config['assets']['paths'] as $path) {
                    \Asset::add_path($path);
                }
            }
            if (isset($this->config['assets']['css'])) {
                foreach ($this->config['assets']['css'] as $css) {
                    \Asset::css($css, array(), 'css');
                }
            }
            if (isset($this->config['assets']['js'])) {
                foreach ($this->config['assets']['js'] as $js) {
                    \Asset::js($js, array(), 'js');
                }
            }
        }
        return parent::after($response);
    }

    protected function getConfiguration() {
        list($module_name, $file_name) = $this->getLocation();
        return static::loadConfiguration($module_name, $file_name);
    }

    protected function getLocation() {
        $class_name = get_class($this);
        $class_name = explode('\\', $class_name);
        $file_name = strtolower(implode('/', explode('_', $class_name[count($class_name) - 1])));

        array_splice($class_name, count($class_name) - 1, 1);
        $module_name = strtolower(implode('_', $class_name));
        return array($module_name, $file_name);
    }

    protected static function loadConfiguration($module_name, $file_name) {
        \Config::load($module_name.'::'.$file_name, true);
        $config = \Config::get($module_name.'::'.$file_name);
        $ret = \Config::load(APPPATH.'data'.DS.'config'.DS.'modules_dependencies.php', true);
        $dependencies = \Config::get(APPPATH.'data'.DS.'config'.DS.'modules_dependencies.php', array());
        if (!empty($dependencies[$module_name])) {
            foreach ($dependencies[$module_name] as $dependency) {
                \Config::load($dependency.'::'.$file_name, true);
                $config = \Arr::merge($config, \Config::get($dependency.'::'.$file_name));
            }
        }
        $config = \Arr::recursive_filter($config, function($var) { return $var !== null; });
        return $config;
    }
}
?>

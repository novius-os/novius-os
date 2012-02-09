<?php
class Controller extends Fuel\Core\Controller {
    protected $config = array();

    public function before() {
        $this->config = Arr::merge($this->config, $this->getConfiguration());
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
        \Config::load($module_name.'::'.$file_name, true);
        return \Config::get($module_name.'::'.$file_name);
    }

    protected function getLocation() {
        $class_name = get_class($this);
        $class_name = explode('\\', $class_name);
        $file_name = strtolower(implode('/', explode('_', $class_name[count($class_name) - 1])));

        array_splice($class_name, count($class_name) - 1, 1);
        $module_name = strtolower(implode('_', $class_name));
        return array($module_name, $file_name);
    }
}
?>

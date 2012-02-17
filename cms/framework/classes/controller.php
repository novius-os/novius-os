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

use Event;

class Controller extends Controller_Extendable {

    public $url;
    public $rewrite_url;
    public $rewrites;
    public $module;

    public $rewrite_prefix;

    public $cache;
    public $cache_cleanup;

    public $nesting_level;

    public $default_config = array();


    public function before() {
        $parent_request = $this->request->parent();
        if ($parent_request && $parent_request->controller_instance) {
            $this->nesting_level = $parent_request->controller_instance->nesting_level + 1;
        }
        $this->set_rewrite_prefix();
        if ($this->nesting_level > 3) {
            \Fuel::$profiling && \Console::logError(new Exception(), '3 levels of nesting reached. You need to stop now.');
        }
        return parent::before();
    }

    public function set_rewrite_prefix($prefix = null) {

        if (empty($prefix)) {
            $c =\Cms::main_controller();
            if (!empty($c->rewrite_prefix)) {
                $prefix    = $c->rewrite_prefix;
                $rewriting = \Arr::get($c->rewriting, $c->rewrite_prefix, array());
            }
        } else if ($prefix == -1) {
            $rewriting = $this->rewriting[-1];
        }

        if (!empty($rewriting)) {
            $this->rewrite_prefix = $prefix;
            $this->url            = $rewriting['url'];
            $this->rewrite_url    = $rewriting['rewrite_url'];
            $this->rewrites       = $rewriting['rewrites'];
        }
    }

    public function trigger($event) {
        Event::trigger(get_called_class().'.'.$event, $this, 'array');
    }

    public function save_cache() {
        return $this->cache;
    }

    public function rebuild_cache($cache) {
        $this->cache = $cache;
    }

    public function get_rewrite($name) {
        // @todo big tweak!!
        return implode(',', $this->rewrites);
    }

    protected function merge_config($mixed) {
        if (is_array($mixed)) {
            $this->config = \Arr::merge($this->config, $mixed);
            return;
        }
        if (!empty($this->default_config[$mixed]) && is_array($this->default_config[$mixed])) {
            return $this->merge_config($this->default_config[$mixed]);
        }
    }

    protected static function _compute_views($views = array()) {
        $views = array();
        foreach ($views as $view => $fields) {
            foreach ($fields as $field) {
                $views[$field] = $view;
            }
        }
        return $views;
    }

    public static function rewrite_url() {
        $args = func_get_args();
        $url = array_shift($args);
        return \Cms::rewrite_url($url, $args);
    }


}
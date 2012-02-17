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

class CacheNotFoundException extends \Exception {}
class CacheExpiredException extends \Exception {}

class PubliCache {

    /**
     * Loads any default caching settings when available
     */
    public static function _init()
    {
        \Config::load('cache', true);
    }

    public static function delete($path)
    {
        // Disabled
		// Experimental
        return;

        $dir = \Config::get('cache_dir').$path.'/';
        $files = \Fuel\Core\File::read_dir($dir, 1);
        echo '<pre>';
        $delete = array();
        foreach ($files as $file1 => $files1) {
            $link = substr($dir.$file1, 0, -1);
            print_r(array($link));
            if (is_link($link)) {
                $linked = readlink($link).'/';
                $files2 = \Fuel\Core\File::read_dir($linked, 1);
                foreach ($files2 as $file2 => $files3) {
                    unlink(substr($linked.$file2, 0, -1));
                }
                //$delete[] = $linked;
                rmdir($linked);
                @unlink($linked.'.php');
            }
            unlink($link);
        }
        foreach ($delete as $del) {
            //rmdir($del);
        }
        rmdir($dir);
    }

    public static function forge($path) {
        return new static($path);
    }

    public static function get($path, $params = array()) {

        if (empty($params['callback_func']) || !is_callable($params['callback_func'])) {
            \Fuel::$profiling && \Profiler::console($params);
            \Fuel::$profiling && \Console::logError(new \Exception(), "Invalid callback_func.");
            return;
        }

        $params = \Arr::merge($params, array(
            'callback_args' => array(),
            'duration'      => CACHE_DURATION_FUNCTION,
            'controller'    => null,
        ));

        $cache = new static($path);
        try {
            return $cache->execute_or_start($params['controller']);
        } catch (CacheNotFoundException $e) {
            call_user_func_array($params['callback_func'], $params['callback_args']);
            return $cache->save_and_execute($params['duration'], $params['controller']);
        }
    }

    protected $_path  = null;
    protected $_level = null;
    protected $_content = '';
    protected $_lock_fp = null;

    public function __construct($path = false) {

        if ($path == false) {
            $this->_path = false;
        } else {
            $this->_path = \Config::get('cache_dir').$path.'.php';
        }
    }

    public function execute($controller = null) {
         // Get an exclusive lock
         //$this->_lock_fp = fopen($this->_path, 'c');
         //flock($this->_lock_fp, LOCK_EX);
         if (!empty($this->_path) && is_file($this->_path)) {
            try {
                ob_start();
                include $this->_path;
                $this->content = ob_get_clean();
                //flock($this->_lock_fp, LOCK_UN);
                return $this->content;
            } catch (CacheExpiredException $e) {
                ob_end_clean();
                @unlink($this->_path);
            }
        }
        throw new CacheNotFoundException();
    }

    public function start() {
        ob_start();
        ob_implicit_flush(false);
        $this->_level = ob_get_level();
    }

    public static function check_expires($expires) {
        if ($expires > 0 && $expires <= time()) {
            throw new CacheExpiredException();
        }
		\Cms::main_controller()->expires = \Date::forge($expires)->format("%H:%M:%S");;
    }

    public function save($duration = -1, $controller = null) {
        if ($duration == -1) {
            //flock($this->_lock_fp, LOCK_UN);
            $expires = 0;
            $this->_path = \Config::get('tmp_dir', '/tmp/'.uniqid('page/').'.php');
            $this->_content = '';
        } else {
            $expires = time() + $duration;
            $this->_content = '<?php
            '.__CLASS__.'::check_expires('.$expires.');'."\n";

            if (!empty($controller)) {
                $this->_content .= '
                if (!empty($controller)) {
                    $controller->rebuild_cache('.var_export($controller->save_cache(), true).');
                }'."\n";
            }
            $this->_content .= '?>';
            \Fuel::$profiling && \Profiler::console('PubliCache:'.\Fuel::clean_path($this->_path).' saved for '.$duration.' s.');
        }

        while(ob_get_level() >= $this->_level) {
            $this->_content .= ob_get_clean();
        }
        if (!$this->store()) {
            trigger_error('Cache could not be written! (path = '.$this->_path.')', E_USER_WARNING);
        } else {
            /*
            if (!empty($controller->cache_cleanup)) {
                $cache_cleanup = $controller->cache_cleanup;
                if (!is_array($cache_cleanup)) {
                    $cache_cleanup = array($cache_cleanup);
                }
                foreach ($cache_cleanup as $cc) {
                    $path_cache1 = \Config::get('cache_dir').$cc;
                    $path_cache2 = substr($this->_path, 0, -4);
                    !is_dir($path_cache1) && mkdir($path_cache1, 0755, true);
                    !is_dir($path_cache2) && mkdir($path_cache2, 0755, true);
                    $uniqid = uniqid();
                    symlink($path_cache1, $path_cache2.'/'.$uniqid);
                    symlink($path_cache2, $path_cache1.'/'.$uniqid);
                }
            }
            //*/
        }
        //flock($this->_lock_fp, LOCK_UN);
    }

    public function save_and_execute($duration = -1, $controller = null) {
        $this->save($duration, $controller);
        return $this->execute($controller);
    }

    public function execute_or_start($controller = null) {
        try {
            return $this->execute($controller);
        } catch (CacheNotFoundException $e) {
            \Fuel::$profiling && \Profiler::console('Publicache:'.\Fuel::clean_path($this->_path).' has expired.');
            $this->start();
            throw $e;
        }
    }

    protected function store() {
        $dir = dirname($this->_path);
        // check if specified subdir exists
        if (!@is_dir($dir)) {
          // create non existing dir
            if (!@mkdir($dir, 0755, true)) {
              return false;
            }
        }
        file_put_contents($this->_path, $this->_content);
        return true;
    }

    public function get_path() {
        return $this->_path;
    }

    public function __toString() {
        return $this->content;
    }
}

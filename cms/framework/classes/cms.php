<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

class Cms {

    /**
     * Rewrites an URL according to conventions
     *
     * @param   array   $params
     *  - url   : the base URL to use for rewrite
     *  - title : the first part of the rewrited URL (slug)
     *  - ids   : a string or array of following parameters. A string won't be friendly_title'ized
     * @return  string  The rewrited URL
     */
    public static function rewrite_url($url = null, array $rewrites = array()) {
        // No URL provided, we use the one from the main page
        if ($url === null) {
            $url = self::main_controller()->page->get_href();
        }
        $url  = str_replace('.html', '/', $url);

        if (!empty($rewrites)) {
            if (is_array($rewrites)) {
                $rewrites[0] = Inflector::friendly_title($rewrites[0], '-', true);
                if (!empty($rewrites[1])) {
                    $rewrites[1] = Inflector::friendly_title($rewrites[1], '-', true);
                }
            } else {
                $rewrites = array((string) $rewrites);
            }
            $url .= implode(',', $rewrites);
        }
        $url .= '.html';
        return $url;
    }

    /**
     * Returns the controller instance from the main request
     *
     * @return \Cms\Controller
     */
    public static function main_controller() {
        return Request::main()->controller_instance;
    }

    /**
     *
     * @param  string   $where   Route for the request
     * @param  array    $args    The method parameters
     * @param  boolean  $inline  true  will execute the controller's action directly
     *                           false will writes the function call and include it
     * @return string
     */
    public static function hmvc($where, $args = null) {

        \Fuel::$profiling && \Profiler::console("HMVC $where");

        if (empty($args['args'])) {
            $args['args'] = array();
        }
        $module = !empty($args['module']) ? $args['module'] : '';

        ob_start();
        try {
            $request  = Request::forge($where);
            \Cms::main_controller()->rewrite_prefix = $module;
            $response = call_user_func_array(array($request, "execute"), array($args['args']));
            \Cms::main_controller()->rewrite_prefix = null;
            $cache_cleanup = $request->controller_instance->cache_cleanup;
            if (!empty($cache_cleanup)) {
                \Fuel::$profiling && \Profiler::console($cache_cleanup);
                Cms::main_controller()->cache_cleanup[] = $cache_cleanup;
            }
            echo $response;
            //echo $response->response();
            //$content = $response->response();
        } catch (\Exception $e) {
            $content = null;
            \Fuel::$profiling && Console::logError($e, "HMVC request '$where' failed.");
            if (\Fuel::$profiling) throw $e;
        }
        $content = ob_get_clean();
        return $content;
    }

    /**
     * Parse a wyiswyg
     *
     * @param  string           $content     Wysiwyg content to parse
     * @param  \Cms\Controller  $controller  Context for the execution
     * @param  boolean          $no_cache    Should cache be skipped?
     * @return string
     */
    public static function parse_wysiwyg($content, $controller, $inline = null) {

        // Fetch the available functions
        Config::load('front', true);

        \Fuel::$profiling && Profiler::mark('Recherche des fonctions dans la page');

		preg_match_all('`<(\w+)\s[^>]+data-module="([^"]+)" data-config="([^"]+)">.*?</\\1>`', $content, $matches);
        foreach ($matches[2] as $match_id => $fct_id) {
			$args = json_decode(strtr($matches[3][$match_id], array(
				'&quot;' => '"',
			)));

            // Check if the function exists
            $name   = $fct_id;
            $config = Config::get("front.$name", false);
            $found  = $config !== false;

            false && \Fuel::$profiling && Profiler::console(array(
                'function_id'   => $fct_id,
                'function_name' => $name,
                'controller'    => get_class($controller),
            ));

            if ($found) {
                $function_content = self::hmvc($config['target'].'/main', array(
                    'args'     => array($args),
                    'module'   => $config['rewrite_prefix'] ?: $name,
                    'inline'   => true,
                ));
            } else {
                $function_content = '';
                \Fuel::$profiling && Console::logError(new Exception(), 'Function '.$name.' not found in '.get_class($controller).'.');
            }

			$content = str_replace($matches[0][$match_id], $function_content, $content);
		}
        return strtr($content, array(
            'http://virtuel_url_data' => Uri::base(false).'data/',
        ));
    }
}

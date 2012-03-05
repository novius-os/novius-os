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
            $url = self::main_page()->get_href();
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
     * Returns the pagefrom the main request
     *
     * @return \Cms\Model_Page_Page
     */
    public static function main_page() {
        return static::main_controller()->page;
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

		Cms::_parse_enhancers($content);
		Cms::_parse_medias($content);
		Cms::_parse_internals($content);

		$content = strtr($content, array(
			'nos://anchor/' => \Cms::main_controller()->url,
		));

		foreach(Event::trigger('front.parse_wysiwyg', null, 'array') as $c) {
			is_callable($c) && call_user_func_array($c, array(&$content));
		}

		return $content;
    }

	protected static function _parse_enhancers(&$content) {

        // Fetch the available functions
        \Config::load(APPPATH.'data'.DS.'config'.DS.'wysiwyg_enhancers.php', 'wysiwyg_enhancers');

        \Fuel::$profiling && Profiler::mark('Recherche des fonctions dans la page');

		preg_match_all('`<(\w+)\s[^>]+data-module="([^"]+)" data-config="([^"]+)">.*?</\\1>`', $content, $matches);
        foreach ($matches[2] as $match_id => $fct_id) {

            $function_content = static::__parse_enhancers($fct_id, $matches[3][$match_id]);
			$content = str_replace($matches[0][$match_id], $function_content, $content);
		}

		preg_match_all('`<(\w+)\s[^>]+data-config="([^"]+)" data-module="([^"]+)">.*?</\\1>`', $content, $matches);
        foreach ($matches[3] as $match_id => $fct_id) {
            $function_content = static::__parse_enhancers($fct_id, $matches[2][$match_id]);
			$content = str_replace($matches[0][$match_id], $function_content, $content);
		}
	}

    protected static function __parse_enhancers($fct_id, $args) {
        $args = json_decode(strtr($args, array(
            '&quot;' => '"',
        )));

        // Check if the function exists
        $name   = $fct_id;
        $config = Config::get("wysiwyg_enhancers.$name", false);
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
            if (empty($function_content) && \Fuel::$env == \Fuel::DEVELOPMENT) {
                $function_content = 'Enhancer '.$name.' ('.$config['target'].') returned empty content.';
            }
        } else {
            $function_content = \Fuel::$env == \Fuel::DEVELOPMENT ? 'Function '.$name.' not found in '.get_class($controller).'.' : '';
            \Fuel::$profiling && Console::logError(new Exception(), 'Function '.$name.' not found in '.get_class($controller).'.');
        }
        return $function_content;
    }

	protected static function _parse_medias(&$content) {

		// Replace media URL
		preg_match_all('`nos://media/(\d+)(?:/(\d+)/(\d+))?`', $content, $matches);
		if (!empty($matches[0])) {
			$media_ids = array();
			foreach ($matches[1] as $match_id => $media_id)
			{
				$media_ids[] = $media_id;
			}
			$medias = Cms\Model_Media_Media::find('all', array('where' => array(array('media_id', 'IN', $media_ids))));
			foreach ($matches[1] as $match_id => $media_id)
			{
				if (!empty($matches[3][$match_id])) {
					$media_url = $medias[$media_id]->get_public_path_resized($matches[2][$match_id], $matches[3][$match_id]);
				} else {
					$media_url = $medias[$media_id]->get_public_path();
				}
				$content = str_replace($matches[0][$match_id], $media_url, $content);
			}
		}
	}

	protected static function _parse_internals(&$content) {

		// Replace internal links
		preg_match_all('`nos://page/(\d+)`', $content, $matches);
		if (!empty($matches[0])) {
			$page_ids = array();
			foreach ($matches[1] as $match_id => $page_id)
			{
				$page_ids[] = $page_id;
			}
			$pages = Cms\Model_Page_Page::find('all', array('where' => array(array('page_id', 'IN', $page_ids))));
			foreach ($matches[1] as $match_id => $page_id)
			{
				$content = str_replace($matches[0][$match_id], $pages[$page_id]->get_href(), $content);
			}
		}
	}
}

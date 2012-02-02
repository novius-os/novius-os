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

use Fuel\Core\Cache;
use Fuel\Core\Config;
use Fuel\Core\Request;
use Fuel\Core\View;


class Controller_Front extends Controller {

    public $page;

    public $template;

    protected $_view;

    public $full_url;
    public $rewriting;

    public $assets_css = array();
    public $assets_js  = array();
    public $raw_css    = array();
    public $raw_js     = array();

    public $page_title       = '';
    public $meta_description = '';
    public $meta_keywords    = '';
    public $meta_robots      = 'index,follow';
    public $metas            = array();


    //public function before() {
    //    return parent::before();
    //}

    // front entry point = action_index()

    public function router($action, $params) {

        $this->_prepare();

        $cache_path = str_replace(str_replace('.html', '', $this->rewrite_url), '', $this->full_url);
        $cache_path = (empty($this->url) ? 'index/' : $this->url).$cache_path;
        $cache_path = rtrim($cache_path, '/');

		if ($nocache = true) {
			$this->_generate_cache();
			ob_start();
            echo $this->_view->render();
			$content = ob_get_clean();
            return $this->_handle_head($content);
		}

        $publi_cache = PubliCache::forge('pages'.DS.$cache_path);
        try {
            $content = $publi_cache->execute($this);
            return $this->_handle_head($content);
        } catch (CacheNotFoundException $e) {
            $publi_cache->start();
            try {
                $this->_generate_cache();
            } catch (Exception $e) {
                // Cannot generate cache: fatal error...
                exit($e->getMessage());
            }
			ob_start();
            echo $this->_view->render();
			$content = ob_get_clean();
            $publi_cache->save(CACHE_DURATION_PAGE, $this);
            \Fuel\Core\Event::trigger('page_save_cache', $publi_cache->get_path());
            $content = $publi_cache->execute();
            return $this->_handle_head($content);
        }
    }

    protected function _handle_head($content) {
        $head = array();

        if (!empty($this->page_title)) {
            $head[] = '<title>'.$this->page_title.'</title>';
        }

        if (!empty($this->meta_robots)) {
            $head[] = '<meta name="robots" content="'.$this->meta_robots.'">';
        }
        if (!empty($this->meta_keywords)) {
            $head[] = '<meta name="keywords" content="'.$this->meta_keywords.'">';
        }
        if (!empty($this->meta_description)) {
            $head[] = '<meta name="description" content="'.$this->meta_description.'">';
        }

        foreach ($this->assets_css as $css) {
            $head[] = '<link href="'.$css.'" rel="stylesheet" type="text/css">';
        }
        foreach ($this->raw_css as $raw_css) {
            $head[] = '<style type="text/css">'.$raw_css.'</style>';
        }
        foreach ($this->assets_js as $js) {
            $head[] = '<script src="'.$js.'" type="text/javascript"></script>';
        }
        foreach ($this->raw_js as $raw_js) {
            $head[] = '<script type="text/javascript">'.$raw_js.'</script>';
        }
        foreach ($this->metas as $metas) {
            $head[] = $metas;
        }

        return str_ireplace('</head>', implode("\n", $head).'</head>', $content);
    }

    /**
     * Determine the URL of the page.
     * Computes all rewrites from all sub-pages.
     */
    protected function _prepare() {
        // Strip out leading / and trailing .html
        $url = substr($_SERVER['REDIRECT_URL'], 1, -5);
        $this->full_url = $url;
        $exploded_url = explode('/', $url);
        $rebuildt_url = array();
        $rewriting     = array();
        while(null !== ($fragment = array_shift($exploded_url))) {
            $fragments = explode(',', $fragment);
            if (count($fragments) == 1) {
                if ($fragment == $fragments[0]) {
                    $rebuildt_url[] = $fragment;
                    $url = implode('/', $rebuildt_url);
                    $url = $url.(empty($url) ? '' : '.html');
                    $rewriting[-1] = array(
                        'url'         => $url,
                        'rewrite_url' => $url,
                        'rewrites'    => array(),
                    );
                    continue;
                } else {
                    $fragments = array();
                }
            }

            $url = implode('/', $rebuildt_url);
            $rebuildt_url[] = $fragment;
            //$rewrite_url    = implode('/', $rebuildt_url);
            $rewrite_url    = $url;
            if (empty($rewriting[-1])) {
                $rewriting[-1] = array(
                    'url'         => $url,
                    'rewrite_url' => $url,
                    'rewrites'    => array(),
                );
            }
            $module         = array_shift($fragments);
            $rewriting[$module]      = array(
                'url'         => $url.(empty($url) ? '' : '.html'),
                'rewrite_url' => $rewrite_url.(empty($rewrite_url) ? '' : '.html'),
                'rewrites'    => $fragments,
            );
        }
        $this->rewriting     = $rewriting;
        $this->nesting_level = 0;
        $this->set_rewrite_prefix(-1);
    }

    /**
     * Generate the cache. Renders all wysiwyg and assign them to the view.
     */
    protected function _generate_cache() {

        $this->_find_page();
        $this->_find_template();

        \Fuel::$profiling && \Profiler::console('page_id = ' . $this->page->page_id);

        // Scan all wysiwyg
        foreach ($this->template['layout'] as $wysiwyg_name => $layout) {
            $content = \Cms::parse_wysiwyg($this->page->{'wysiwyg->'.$wysiwyg_name.'->wysiwyg_text'}, $this);

            $this->page_title = $this->page->page_title;

            $this->_view->set('wysiwyg_'.$wysiwyg_name, $content, false);
        }
    }

    /**
     * Find the page in the database and fill in the page variable.
     */
    protected function _find_page() {
        $where = array(array('page_published', 1));
        if (empty($this->url)) {
            $where[] = array('page_home', 1);
        } else {
            $where[] = array('page_virtual_url', $this->url);
        }

        // Liste toutes les pages ayant le bon nom
        $pages = Model_Page_Page::find('all', array(
            'where' => $where,
        ));

        if (empty($pages)) {
            var_dump($this->url);
            var_dump($this->rewrite_url);
            throw new \Exception('The requested page was not found.');
        }


        // Get the first page
        reset($pages);
        $this->page = current($pages);
    }

    protected function _find_template() {

        // Find the template
        Config::load(APPPATH.'data'.DS.'config'.DS.'templates.php', 'templates');
        $templates = Config::get('templates', array());

        if (!isset($templates[$this->page->page_template])) {
            throw new \Exception('The template '.$this->page->page_template.' cannot be found.');
        }

        $this->template = $templates[$this->page->page_template];
		if (empty($this->template['file'])) {
			throw new \Exception('The template file for '. ($this->template['title'] ?: $this->page->page_template ).' is not defined.');
		}

        try {
			// @todo : always load from the template directory?
            // Try normal loading
            $this->_view = View::forge($this->template['file']);
        } catch (\FuelException $e) {

            $path = array(rtrim(APPPATH, DS), $this->template['file'].'.php');

            array_splice($path, 1, 0, array('modules', $this->template['module'], 'templates'));

            $template_file = implode(DS, $path);

            if (!is_file($template_file)) {
                throw new \Exception('The template '.$this->template['file'].' cannot be found.');
            }
            $this->_view = View::forge($template_file);
        }
    }

    public function save_cache() {
        $page_fields = array('id', 'root_id', 'parent_id', 'level', 'title', 'menu_title', 'meta_title', 'type', 'meta_noindex', 'home', 'carrefour', 'virtual_name', 'virtual_url', 'external_link', 'external_link_type', 'meta_description', 'meta_keywords');
        $this->cache['page'] = array();
        foreach ($page_fields as $field) {
            $this->cache['page'][$field] = $this->page->{'page_'.$field};
        }
        return parent::save_cache();
    }

    public function rebuild_cache($cache) {
        $this->page = new Model_Page_Page();
        foreach ($cache['page'] as $field => $value) {
            $this->page->{'page_'.$field} = $value;
        }
        $this->page->freeze();
        unset($cache['page']);
        return parent::rebuild_cache($cache);
    }
}

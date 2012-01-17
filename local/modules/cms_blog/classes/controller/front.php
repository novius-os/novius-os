<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Cms\Blog;

use Cms\Controller;
use Cms\Model_Page_Page;

use Fuel\Core\Inflector;
use Fuel\Core\Str;
use Fuel\Core\View;

class Controller_Front extends Controller {

    /**
     * @var Cms\Pagination
     */
    public $pagination;
    public $current_page = 1;

    /**
     * @var Cms\Blog\Model_Category
     */
    public $category;

    /**
     * @var Cms\Model_User
     */
    public $author;

    /**
     * @var Cms\Blog\Model_Tag
     */
    public $tag;

    public static $blog_url = '';

    public function action_main($args = array()) {

        $this->default_config = \Arr::merge(\Config::get('cms_blog::config'), array(
			'config' => (array) $args,
		));

        $this->merge_config('config');

        $rewrites =& $this->rewrites;

        if (!empty($rewrites) && is_array($rewrites)) {

            if ($rewrites[1] == 'c' && is_numeric($rewrites[2])) {

                $this->cache_cleanup = "blog/category/{$rewrites[2]}";
                empty($rewrites[3]) && $rewrites[3] = 1;
                $this->init_pagination($rewrites[3]);
                return $this->display_list_category($args);

            } else if ($rewrites[1] == 'u' && is_numeric($rewrites[2])) {

                $this->cache_cleanup = "blog/author/{$rewrites[2]}";
                $this->init_pagination(!empty($rewrites[3]) ? $rewrites[3] : 1);
                return $this->display_list_author($args);

            } else if ($rewrites[0] == 'tag') {

                $this->cache_cleanup = "blog/tag/{$rewrites[1]}";
                $this->init_pagination(!empty($rewrites[2]) ? $rewrites[2] : 1);
                return $this->display_list_tag($args);

            } else if (is_numeric($rewrites[1])) {

                $this->cache_cleanup = "blog/post/{$rewrites[1]}";
                return $this->display_item($args);
            }
        }

        $this->cache_cleanup = "blog/list";
        $this->init_pagination(!empty($rewrites[0]) ? $rewrites[0] : 1);
        return $this->display_list_main($args);
    }

    protected function init_pagination($page) {
        $this->current_page = $page;
        $this->pagination   = new \Cms\Pagination();
    }

    public function display_list_main($params) {

        //$this->merge_config('config');

        $list = $this->_display_list('list_main');

        \Cms::main_controller()->page_title = 'Novius Labs';

        $self   = $this;
        $class = get_class($this);

        // Add surrounding stuff
        return View::forge($this->config['list_view'], array(
            'list'       => $list,
            'pagination' => $this->pagination->create_links(function($page) use ($class, $self) {
                if ($page == 1) {
                    return $class::rewrite_url($self->rewrite_url);
                }
                return $class::rewrite_url($self->rewrite_url, 'blog', $page);
            }),
        ), false);
    }

    public function display_list_category($params) {

        list(,,$cat_id, $page) = $this->rewrites;

        $this->category = Model_Category::find($cat_id);
        $list = $this->_display_list('category');

        $class = get_called_class();
        $self  = $this;
        $url   = $this->url;

        $link_to_category = function($category, $page = 1) use($class, $url) {
            return $class::rewrite_url_category($category, $page, $url);
        };
        $link_pagination = function($page) use ($link_to_category, $self) {
            return $link_to_category($self->category, $page);
        };

        // Add surrounding stuff
        return View::forge($this->config['list_view'], array(
            'list'             => $list,
            'pagination'       => $this->pagination->create_links($link_pagination),
            'category'         => $this->category,
            'link_to_category' => $link_to_category,
        ), false);
    }

    public function display_list_tag($params) {

        list(, $tag) = $this->rewrites;
        $this->tag = strtolower($tag);


        $class = get_called_class();
        $self  = $this;
        $url   = $this->url;

        $link_to_tag = function($tag, $page = 1) use($class, $url) {
            return $class::rewrite_url_tag($tag, $page, $url);
        };
        $link_pagination = function($page) use ($link_to_tag, $self) {
            return $link_to_tag($self->tag, $page);
        };

        $list = $this->_display_list('tag');

        // Add surrounding stuff
        return View::forge('front/list_tag', array(
            'list'        => $list,
            'pagination'  => $this->pagination->create_links($link_pagination),
            'tag'         => $this->tag,
            'link_to_tag' => $link_to_tag,
        ), false);
    }

    public function display_list_author($user_id) {

        list(,,$user_id, $page) = $this->rewrites;

        $this->author = \Cms\Model_User::find($user_id);
        $list = $this->_display_list('author');

        $class = get_called_class();
        $self  = $this;
        $url   = $this->url;

        $link_to_author = function($author, $page = 1) use($self, $url) {
            return $self::rewrite_url_author($author, '', $page, $url);
        };
        $link_pagination = function($page) use ($link_to_author, $self) {
            return $link_to_author($self->author, $page);
        };

        // Add surrounding stuff
        echo View::forge($this->config['list_view'], array(
            'list'           => $list,
            'pagination'     => $this->pagination->create_links($link_pagination),
            'author'         => $this->author,
            'link_to_author' => $link_to_author,
        ), false);
    }

    /**
     * Display several items (from a list context)
     *
     * @param   string  $context = list_main | list_author | list_category | list_tag
     */
    protected function _display_list($context = 'list_main') {

        // Allow events for each or all context
        $this->trigger('display_list');
        $this->trigger("display_{$context}");

        $this->config = \Arr::merge($this->config, $this->default_config['display_list'], $this->default_config["display_{$context}"]);

		\Fuel::add_module('cms_media');
        // Get the list of posts
        $query = Model_Blog::query()
                ->related('author')
                ->related('media_thumbnail')
                ->related('media_thumbnail.path');

		$query->where(array('blog_lang', 'fr'));

        if (!empty($this->category)) {
            $query->related('categories');
            $query->where(array('categories.blgc_id', $this->category->blgc_id));
        }
        if (!empty($this->author)) {
            $query->where(array('blog_auteur_id', $this->author->user_id));
        }
        if (!empty($this->tag)) {
            $query->related('tags');
            $query->where(array('tags.tag_label', $this->tag));
        }

        $this->pagination->set_config(array(
            'total_items'    => $query->count(),
            'per_page'       => $this->config['item_per_page'],
            'current_page'   => $this->current_page,
        ));

        $query->rows_limit($this->pagination->per_page);
        $query->rows_offset($this->pagination->offset);

        $query->order_by($this->config['order_by']);
        $posts = $query->get();

        // Display them
        return $this->_display_items($posts, $context);
    }

    /**
     * Display several items (from a list context)
     *
     * @param   array   $items
     * @param   string  $context = list_main | list_author | list_category | list_tag
     * @return  string  Rendered view
     */
    protected function _display_items($items, $context = 'list_main')  {

        $retrieve_stats = !empty($this->config['stats']) && $this->config['stats'];
        $comments_count = array();

        $ids = array();
        foreach ($items as $post) {
            $ids[] = $post->blog_id;
        }

        if ($retrieve_stats) {

            // Retrieve the comment counts for each post (1 request)
            $comments_count = \Db::select(\Db::expr('COUNT(comm_id) AS count_result'), 'comm_parent_id')
                    ->from(\Cms\Model_Comment::table())
                    ->and_where('comm_type', '=', 'blog')
                    ->and_where('comm_parent_id', 'in', $ids)
                    ->group_by('comm_parent_id')
                    ->execute()->as_array();
            $comments_count = \Arr::assoc_to_keyval($comments_count, 'comm_parent_id', 'count_result');

            /* Should look like this with the Orm, but that doesn't work...
            $t = \Cms\Model_Comment::query()
                    ->select(\Db::expr('COUNT(comm_id)', 'count_result'), 'comm_parent_id')
                    ->where(array(
                        array('comm_type', '=', 'blog'),
                        array('comm_parent_id', 'in', $ids),
                    ))
                    //->group_by('comm_parent_id')
                    ->get();
            //*/
        }

        // Loop meta-data
        $length = count($items);
        $index  = 1;
        $output = array();

        // Events based on current iteration
        $this->trigger('display_list_item');
        $this->trigger("display_{$context}_item");
        $this->merge_config('display_list_item');
        $this->merge_config("display_{$context}_item");
        if (!empty($this->config['fields_views'])) {
            $this->views = static::_compute_views($this->config['fields_views']);
        }

        // Render each news
        foreach ($items as $item) {
            $this->loop = array(
                'length' => $length,
                'current' => $index,
                'first'  => $index == 1,
                'last'   => $index++ == $length,
            );

            $this->trigger('display_list_item_loop');
            $this->trigger("display_{$context}_item_loop");

            if ($index == 2) {
                $this->merge_config('display_list_item_first');
                $this->merge_config("display_{$context}_item_first");
            } else {
                $this->merge_config('display_list_item_following');
                $this->merge_config("display_{$context}_item_following");
            }

            $output[] = $this->_display_item($item, array(
                'comment_count' => isset($comments_count[$item->blog_id]) ? $comments_count[$item->blog_id] : null,
            ));
        }
        return implode('', $output);
    }

    /**
     * Display a single item (outside a list context)
     *
     * @param   type  $item_id
     * @return  \Fuel\Core\View
     */
    public function display_item($args) {

        list(, $item_id) = $this->rewrites;

        $this->trigger('display_item');
        $this->merge_config('display_item');

        $post = Model_Blog::find($item_id);
        echo $this->_display_item($post);
    }

    /**
     *  Display a single item (from any context)
     *
     * @param   \Cms\Blog\Model_Blog  $item  An instance of the model
     * @param   array                 $data  Additionnal data to pass to the view
     *  - comment_count : the number of comment for this post
     * @return  \Fuel\Core\View
     */
    protected function _display_item($item, $data = array()) {

        $data['date_format'] = $this->config['date_format'];
        $data['title_tag']   = $this->config['title_tag'];

        // Main data from model, probably not needed thanks to Orm\Observers\Typing
        $data['created_at'] = strtotime($item['blog_date_creation']);

        // Additional data calculated per-item
        $data['link_to_author'] = self::rewrite_url_author($item->author, $item->blog_auteur, $this->url);
        $data['link_to_item']   = self::rewrite_url_item($item, $this->url);
        $data['link_on_title']  = $this->config['link_on_title'] ? $data['link_to_item'] : false;

        $self = get_called_class();
        $url  = $this->url;
        $data['link_to_category'] = function($category, $page = 1) use($self, $url) {
            return $self::rewrite_url_category($category, $page, $url);
        };
        $data['link_to_tag'] = function($tag, $page = 1) use($self, $url) {
            return $self::rewrite_url_tag($tag, $page, $url);
        };

        // Renders all the fields
        $fields = array();
        foreach (preg_split('/[\s,-]+/', $this->config['fields']) as $field) {
            $view = isset($this->views[$field]) ? $this->views[$field] : $this->config['fields_view'];
            $data['display'] = array($field => true);
            $data['item']    = $item;
            $view = static::get_view($view);
            $view->set($data);
            $fields[$field] = $view;
        }
        $view = static::get_view($this->config['item_view']);
        $view->set($data + $fields, null, false);
        return $view;
    }














    public static function get_view($which) {
        // Cache views
        static $views = array();
        if (empty($views[$which])) {
            $views[$which] = View::forge($which);
        }
        // Return empty views
        return clone $views[$which];
    }

    public function action_menu($dossier_menu = false) {

        return \Cms\PubliCache::get('cms_blog/menu', array(
            'callback_func' => array($this, 'action_menu_execute'),
        ));
    }

    public function action_menu_execute() {
        static::$blog_url = \Cms\Model_Page_Page::get_url(2);
        self::MenuBlog($dossier_menu);
    }

    public function action_links() {

        return \Cms\PubliCache::get('cms_blog/links', array(
            'callback_func' => array($this, 'action_links_execute'),
        ));
    }

    public function action_links_execute() {
        static::$blog_url = \Cms\Model_Page_Page::get_url(2);
        self::newLiens();
    }

    public function action_insert_tags() {

        return \Cms\PubliCache::get('cms_blog/tags', array(
            'callback_func' => array($this, 'action_insert_tags_execute'),
        ));
    }

    public function action_insert_tags_execute() {

        static::$blog_url = \Cms\Model_Page_Page::get_url(2);
        self::EncartTags();
    }


    static function EncartTags() {
        //$nb = \Cms\Blog\Model_Tag::query();
        //$nb->select(\Db::expr('COUNT(blgt_tag) as nb'));
        $query = \Db::select(\Db::expr('tag_label AS tag'), \Db::expr('COUNT(tag_label) AS sizeof'))
                ->distinct()
                ->from('cms_blog_tag')
				->join('cms_tag')
				->on('cms_blog_tag.blgt_tag_id', '=' , 'cms_tag.tag_id');


        $nb = $query->execute()->get('sizeof');
        $tags = $query
                ->group_by('blgt_tag_id')
                ->order_by('sizeof', 'desc')
                ->limit(20)
                ->as_object()
                ->execute()
                ->as_array();

        if (!count($tags)) {
            return;
        }
        $tags = (array) $tags;

        usort($tags, function($a, $b) {
            if ($a->tag == $b->tag) {
                return 0;
            }
            return $a->tag < $b->tag ? -1 : 1;
        });
?>
<div class="cadre_tag_top"></div>
<div class="cadre" style="padding:10px 10px; background: url(static/images/blog/cadre_tag_fond.png) bottom repeat-x;">
  <h3 align="left" style="margin:0 0 10px 0; text-transform: uppercase;" ><?= __('Tags') ?></h3>
  <div style="padding:0px 5px 8px;">
<?          self::cloud($tags, 5, 'tag_poids'); ?>
  </div>
<?      if ($nb > count($tags)) { ?>
 <div align="center" style="padding-bottom:5px;"><a href="<?= static::$blog_url ?>?todo=tags"><?= __('Tous les tags') ?></a></div>
<?      } ?>
</div>
<?
    }

    static function cloud($tags, $nb_paliers, $prefixeclass) {
        $nb_paliers = $nb_paliers ? $nb_paliers : 10;
        $max        = 1;
        foreach ($tags as $tag) {
            if ($tag->sizeof > $max) {
                $max = $tag->sizeof;
            }
        }
        $nb_paliers = 5;
        foreach ($tags as $tag) {
            for ($i = 1; $i <= $nb_paliers; $i++) {
                if ($tag->sizeof <= ($i * $max) / $nb_paliers) {
                    $poids = $i;
                    break;
                }
            }
?>
    <a class="tag <?= $prefixeclass.$poids ?>" href="<?= self::rewrite_url_tag($tag->tag, 1, static::$blog_url) ?>"><?= $tag->tag ?></a>
<?php
        }
    }




    static function MenuBlog($dossier_menu = false) {

        $page = \Cms\Model_Page_Page::query()
                ->where_open()
                    ->where(array('page_home', '=', '1'))
                    ->or_where(array('page_carrefour', '=', '1'))
                ->where_close()
                ->where(array('page_rac_id', '=', 'fr'));
        $accueil = current($page->get());

        $page_titre_menu = $accueil->page_titre_menu;
        if ($page_titre_menu == '') {
            $page_titre_menu = $accueil->page_titre;
        }
        $on = in_array($accueil->page_id, (array) $GLOBALS['page_rail']) && !is_array($_GET['rewrite_ids']);
?>
    <ul>
      <li><a <?= $accueil->get_link() ?> class="<?= $on ? 'on' : '' ?>"><?= $page_titre_menu ?></a></li>
<?      self::SousMenuCategorie(null);

        //-------Listage des pages du dossier Menu Header

        $list_page = \Cms\Model_Page_Page::query()
                ->where(array('page_pere_id', DOSSIER_MENU_HEADER))
                ->where(array('page_publier', 1))
                ->where(array('page_menu', 1))
                ->get();
        foreach ($list_page as $i => $page1) {
        $page_titre_menu = $page1->page_titre_menu;
        if ($page_titre_menu == '') {
            $page_titre_menu = $page1->page_titre;
        }
?>
                <li><a <?= $page1->get_link() ?>><?= $page_titre_menu ?></a></li>
<?      }
?>
    </ul>
<?
        }

    static function SousMenuCategorie($parent_id = null) {
        $categories = Model_Category::query()
                ->where(array('blgc_parent_id', '=', $parent_id))
                ->get();
        $nb_categorie = count($categories);
        $compteur = 1;
        foreach ($categories as $categorie) {

            $nbss = Model_Category::query()
                ->where(array('blgc_parent_id', '=', $categorie->blgc_id))->get();
            $nbss = count($nbss);

?>
      <li><a href="<?= self::rewrite_url_category($categorie, static::$blog_url) ?>"><?= $categorie->blgc_titre ?></a>
<?          if ($nbss) { ?>
        <ul>
<?              self::SousMenuCategorie($categorie->blgc_id); ?>
        </ul>
<?          } ?>
      </li>
<?
            $compteur++;
        }
    }



    static function newLiens() {
?>
<ul class="sf-menu" style="margin:0;">
  <!-- list-style-image pour IE 7 -->
  <?php
  $page_newsletters = \Cms\Model_Page_Page::find(PAGE_INSCRIPTION_NEWSLETTER);
  ?>
  <li style="list-style-type:none;list-style-image: none;"><a href="<?= $page_newsletters->page_url_virtuel ?>"><img src="static/images/abonner_actualites.png" border="0" alt="s'abonner aux actualités"  title="s'abonner aux actualités" /></a></li>
  <li style="list-style-type:none;list-style-image: none;"><a href="<?= static::$blog_url ?>?todo=rss" ><img src="static/images/abonner_rss.png" border="0" alt="s'abonner au flux RSS" title="s'abonner au flux RSS" /></a></li>
  <script src="http://widgets.twimg.com/j/2/widget.js"></script>
  <script>
  new TWTR.Widget({
    version: 2,
    type: 'profile',
    rpp: 4,
    interval: 6000,
    width: 204,
    height: 300,
    theme: {
      shell: {
        background: '#ebebeb',
        color: '#333333'
      },
      tweets: {
        background: '#ffffff',
        color: '#000000',
        links: '#474d4d'
      }
    },
    features: {
      scrollbar: false,
      loop: false,
      live: false,
      hashtags: true,
      timestamp: true,
      avatars: false,
      behavior: 'all'
    }
  }).render().setUser('NoviusInfo').start();
  </script>
</ul>
<?
    }

    static function rewrite_url_item($item, $url = null) {

        return self::rewrite_url($url, 'blog', $item->blog_titre, $item->blog_id);
    }

    static function rewrite_url_category($category, $page = 1, $url = null) {

        $args = array($url, 'blog', $category->blgc_titre, 'c', $category->blgc_id);
        if ($page > 1) {
            $args[] = $page;
        }
        return call_user_func_array('static::rewrite_url', $args);
    }

    static function rewrite_url_author($author, $fallback, $page, $url = null) {

        $args = array($url, 'blog', $author->user_fullname ?: $fallback, 'u', $author->user_id);
        if ($page > 1) {
            $args[] = $page;
        }
        return call_user_func_array('static::rewrite_url', $args);
    }

    static function rewrite_url_tag($tag, $page, $url = null) {

        $args = array($url, 'blog', 'tag', $tag);
        if ($page > 1) {
            $args[] = $page;
        }
        return call_user_func_array('static::rewrite_url', $args);
    }
}

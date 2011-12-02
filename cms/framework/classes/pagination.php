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

class Pagination {

    /**
     * @var    integer    The current page
     */
    public $current_page = null;

    /**
     * @var    integer    The offset that the current page starts at
     */
    public $offset = 0;

    /**
     * @var    integer    The number of items per page
     */
    public $per_page = 10;

    /**
     * @var    integer    The number of total pages
     */
    public $total_pages = 0;

    /**
     * @var array The HTML for the display
     */
    public $template = array(
        'wrapper_start'  => '<div class="pagination"> ',
        'wrapper_end'    => ' </div>',
        'page_start'     => '<span class="page-links"> ',
        'page_end'       => ' </span>',
        'previous_start' => '<span class="previous"> ',
        'previous_end'   => ' </span>',
        'previous_mark'  => '&laquo; ',
        'next_start'     => '<span class="next"> ',
        'next_end'       => ' </span>',
        'next_mark'      => ' &raquo;',
        'active_start'   => '<span class="active"> ',
        'active_end'     => ' </span>',
    );

    /**
     * @var    integer    The total number of items
     */
    protected $total_items = 0;

    /**
     * @var    integer    The total number of links to show
     */
    protected $num_links = 5;

    /**
     * @var	mixed	The pagination URL
     */
    protected $pagination_url;

    // --------------------------------------------------------------------

    public function __construct(array $config = array())
    {
        $config = \Arr::merge(\Config::get('pagination', array()), $config);

        $this->set_config($config);
    }

    /**
     * Set Config
     *
     * Sets the configuration for pagination
     *
     * @access public
     * @param array   $config The configuration array
     * @return void
     */
    public function set_config($config) {
        foreach ($config as $key => $value)
        {
            if ($key == 'template')
            {
                $this->template = array_merge($this->template, $config['template']);
                continue;
            }

            $this->{$key} = $value;
        }

        $this->initialize();
    }

    // --------------------------------------------------------------------

    /**
     * Prepares vars for creating links
     *
     * @access public
     * @return array    The pagination variables
     */
    protected function initialize()
    {
        $this->total_pages = ceil($this->total_items / $this->per_page) ?: 1;

        if ($this->current_page > $this->total_pages)
        {
            $this->current_page = $this->total_pages;
        }
        elseif ($this->current_page < 1)
        {
            $this->current_page = 1;
        }

        // The current page must be zero based so that the offset for page 1 is 0.
        $this->offset = ($this->current_page - 1) * $this->per_page;
    }

    // --------------------------------------------------------------------

    /**
     * Creates the pagination links
     *
     * @access public
     * @return mixed    The pagination links
     */
    public function create_links($pagination_url = null)
    {
        if ($this->total_pages == 1)
        {
            return '';
        }

        \Lang::load('pagination', true);

        if (!is_null($pagination_url)) {
            $old_pagination_url   = $this->pagination_url;
            $this->pagination_url = $pagination_url;
        }

        $pagination  = $this->template['wrapper_start'];
        $pagination .= $this->prev_link(\Lang::get('pagination.previous'));
        $pagination .= $this->page_links();
        $pagination .= $this->next_link(\Lang::get('pagination.next'));
        $pagination .= $this->template['wrapper_end'];

        if (!is_null($pagination_url)) {
            $this->pagination_url = $old_pagination_url;
        }

        return $pagination;
    }

    // --------------------------------------------------------------------

    /**
     * Pagination Page Number links
     *
     * @access public
     * @return mixed    Markup for page number links
     */
    public function page_links()
    {
        if ($this->total_pages == 1)
        {
            return '';
        }

        $pagination = '';

        // Let's get the starting page number, this is determined using num_links
        $start = (($this->current_page - $this->num_links) > 0) ? $this->current_page - ($this->num_links - 1) : 1;

        // Let's get the ending page number
        $end   = (($this->current_page + $this->num_links) < $this->total_pages) ? $this->current_page + $this->num_links : $this->total_pages;

        for($i = $start; $i <= $end; $i++)
        {
            if ($this->current_page == $i)
            {
                $pagination .= $this->template['active_start'].$i.$this->template['active_end'];
            }
            else
            {
                $pagination .= '<a href="'.call_user_func($this->pagination_url, $i).'">'.$i.'</a>';
            }
        }

        return $this->template['page_start'].$pagination.$this->template['page_end'];
    }

    // --------------------------------------------------------------------

    /**
     * Pagination "Next" link
     *
     * @access public
     * @param string $value The text displayed in link
     * @return mixed    The next link
     */
    public function next_link($value)
    {
        if ($this->total_pages == 1)
        {
            return '';
        }

        if ($this->current_page == $this->total_pages)
        {
            return $value.$this->template['next_mark'];
        }
        else
        {
            $next_page = $this->current_page + 1;
            $url = call_user_func($this->pagination_url, $next_page);
            return '<a href="'.$url.'">'.$value.$this->template['next_mark'].'</a>';
        }
    }

    // --------------------------------------------------------------------

    /**
     * Pagination "Previous" link
     *
     * @access public
     * @param string $value The text displayed in link
     * @return mixed    The previous link
     */
    public function prev_link($value)
    {
        if ($this->total_pages == 1)
        {
            return '';
        }

        if ($this->current_page == 1)
        {
            return $this->template['previous_mark'].$value;
        }
        else
        {
            $previous_page = $this->current_page - 1;
            $url = call_user_func($this->pagination_url, $previous_page);
            return '<a href="'.$url.'">'.$this->template['previous_mark'].$value.'</a>';
        }
    }
}



<?php
/**
 * NOVIUS OS - Web OS for digital communication
 * 
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

use Cms\I18n;

I18n::load('page', 'cms_page');

return array(
	'query' => array(
		'model' => 'Cms\Model_Page_Page',
		'related' => array(),
	),
    'urljson' => 'static/cms/js/admin/page/page.js',
    'i18n' => array(
        'Pages' => __('Pages'),
        'Add a Page' => __('Add a Page'),
        'Add a root' => __('Add a root'),
        'Title' => __('Title'),
        'Roots' => __('Roots'),
        'Directories' => __('Directories'),

        'addDropDown' => __('Select an action'),
        'columns' => __('Columns'),
        'showFiltersColumns' => __('Filters column header'),
        'visibility' => __('Visibility'),
        'settings' => __('Settings'),
        'vertical' => __('Vertical'),
        'horizontal' => __('Horizontal'),
        'hidden' => __('Hidden'),
        'item' => __('page'),
        'items' => __('pages'),
        'showNbItems' => __('Showing {{x}} pages out of {{y}}'),
        'showOneItem' => __('Show 1 page'),
        'showNoItem' => __('No page'),
        'showAll' => __('Show all pages'),
        'views' => __('Views'),
        'viewGrid' => __('Grid'),
        'viewThumbnails' => __('Thumbnails'),
        'preview' => __('Preview'),
        'loading' => __('Loading...'),
    ),
	'dataset' => array(
		'id' => 'page_id',
		'title' => 'page_titre',
        'url' => 'page_url_virtuel',
	),
	'inputs' => array(							
		'rac_id' => function($value, $query) {
			if ($value) {
				$query->where(array('page_rac_id', '=', $value));
				//$query->where(array('page_niveau', '=', 1));
				$query->order_by('page_titre');
			}
			return $query;
		},
		'directory_id' => function($value, $query) {
			$query->where(array('page_niveau', '>', 0));
			$query->where(array('page_type', '!=', Cms\Model_Page_Page::TYPE_FOLDER));
			if ($value) {
				$query->where(array('page_pere_id', '=', $value));
			}
			return $query;
		},
	),
);
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

use Fuel\Core\Request;
use Fuel\Core\View;
use Fuel\Core\Config;


class Controller_Inspector_Lang extends \Controller {

	protected $config = array(
		'label' => 'Language',
		'iconClasses' => 'cms_blog-icon16 cms_blog-icon16-date',
		'input'	     => 'lang',
		'input_name' => 'lang',
		'widget_id'  => 'filterlang',
		'columns' => array(
			array(
				'headerText' => 'Language',
				'dataKey' => 'title',
			),
			array(
				'visible' => false,
			),
		),
	);

	public function action_list()
	{
		$view = View::forge('inspector/lang');

		$content = array();
		foreach ($this->config['languages'] as $lang => $title) {
			$content[] = array(
				'id' => $lang,
				'title' => $title,
			);
		}

		$view->set('content', \Format::forge($content)->to_json($content), false);
		return $view;
	}
}
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

class Controller_Noviusos_Noviusos extends Controller_Generic_Admin {

	public function before() {
		parent::before();

		$logged_user = \Session::get('logged_user', false);
		if (empty($logged_user)) {
			\Response::redirect('/admin/login?redirect='.urlencode($_SERVER['REDIRECT_URL']));
			exit();
		}

		$this->auto_render = false;
	}

	public function after($response) {
		\Asset::add_path('static/cms/');
		\Asset::add_path('static/cms/js/jquery/wijmo/');
		\Asset::add_path('static/cms/js/jquery/jquery-ui-noviusos/');
		\Asset::css('aristo/jquery-wijmo.css', array(), 'css');
		\Asset::css('base.css', array(), 'css');
		\Asset::css('jquery.nos.ostabs.css', array(), 'css');

		return parent::after($response);
	}

	public function action_index()
	{
		$view = \View::forge('noviusos/index');

		$view->set('initTabs', \Format::forge(self::getTabs())->to_json());
		$view->set('trayTabs', \Format::forge(array(
			array(
				'url' => 'admin/tray/plugins',
				'iconClasses' => 'nos-icon24 nos-icon24-noviusstore',
				'label' => 'Novius store',
				'iconSize' => 24,
			),
			array(
				'url' => 'generator/model',
				'iconClasses' => 'nos-icon24 nos-icon24-help',
				'label' => 'Help',
				'iconSize' => 24,
			),
			array(
				'url' => 'generator/model',
				'iconClasses' => 'nos-icon24 nos-icon24-settings',
				'label' => 'Settings',
				'iconSize' => 24,
			),
			array(
				'url' => 'admin/tray/account',
				'iconClasses' => 'nos-icon24 nos-icon24-account',
				'label' => 'Account',
				'iconSize' => 24,
			),
		))->to_json());
		$view->set('appsTab', \Format::forge(array(
			'panelId' => 'noviusospanel',
			'url' => 'admin/noviusos/noviusos/appstab',
			'iconClasses' => 'nos-icon32',
			'iconSize' => 32,
			'label' => 'Novius OS',
			'ajax' => true,
		))->to_json());
		$view->set('newTab', \Format::forge(array(
			'panelId' => 'noviusospanel',
			'url' => 'admin/noviusos/noviusos/appstab',
			'iconClasses' => 'nos-icon16 nos-icon16-add',
			'iconSize' => 16,
			'ajax' => true,
		))->to_json());

		$this->template->body = $view;
		return $this->template;
	}

	public function action_appstab()
	{
		\Config::load(APPPATH.'data'.DS.'config'.DS.'app_installed.php', 'app_installed');
		$app_installed = \Config::get('app_installed', array());

		$apps = array();
		foreach ($app_installed as $app) {
			if (!empty($app['href']) && !empty($app['icon64'])) {
				$apps[] = $app;
			}
		}

		$view = \View::forge('noviusos/appstab', array(
			'apps' => $apps,
		));
		return $view;
	}

	protected static function getTabs() {
		return array(
			//array("url"=>"admin/cms_blog/list", "iconUrl" => "static/modules/cms_blog/img/32/blog.png", "label" => "Blog", "iconSize" => 32, 'labelDisplay'=> false, 'pined' => true),
			//array("url"=>"admin/generator/model", "iconClasses" => "ui-icon-16 ui-icon-settings", "label" => "Model générator", 'pined' => true),
			//array("url"=>"admin/user/list", "iconUrl" => "static/modules/cms_blog/img/32/author.png", "label" => "User management", "iconSize" => 32, 'labelDisplay'=> false, 'pined' => true),
		);
	}
}

/* End of file desktop.php */
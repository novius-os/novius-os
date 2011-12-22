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

        // Might be great to add some additional verifications here !
		$logged_user = \Session::get('logged_user', false);
		if (empty($logged_user)) {
			\Response::redirect('/admin/login?redirect='.urlencode($_SERVER['REDIRECT_URL']));
			exit();
		} else {
            $logged_user = Model_User::find_by_user_id($logged_user->id); // We reload the user
            \Session::set('logged_user', $logged_user);
        }

		$this->auto_render = false;
	}

	public function after($response) {
		\Asset::add_path('static/cms/');
		\Asset::add_path('static/cms/js/jquery/wijmo/');
		\Asset::add_path('static/cms/js/jquery/jquery-ui-noviusos/');
		\Asset::css('rocket/jquery-wijmo.css', array(), 'css');
		\Asset::css('base.css', array(), 'css');
		\Asset::css('jquery.nos.ostabs.css', array(), 'css');

		return parent::after($response);
	}

	public function action_index()
	{
		$view = \View::forge('noviusos/index');

        $user = \Session::get('logged_user', false);
		
		$ostabs = array(
			'initTabs' => self::getTabs(),
			'trayTabs' => array(
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
			),
			'appsTab' => array(
				'panelId' => 'noviusospanel',
				'url' => 'admin/noviusos/noviusos/appstab',
				'iconClasses' => 'nos-icon32',
				'iconSize' => 32,
				'label' => 'Novius OS',
				'ajax' => true,
			),
			'newTab' => array(
				'panelId' => 'noviusospanel',
				'url' => 'admin/noviusos/noviusos/appstab',
				'iconClasses' => 'nos-icon16 nos-icon16-add',
				'iconSize' => 16,
				'ajax' => true,
			),
			'show' => 'function(e, tab) {
				$nos.nos.listener.fire(\'ostabs.show\', false, [tab.index]);
			}',
            'user_configuration' => unserialize($user->user_configuration),
		);

		$view->set('ostabs', \Format::forge($ostabs)->to_json(), false);

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

    public function action_save_user_configuration() {
        $key            = \Input::post('key');
        $configuration  = \Input::post('configuration');
        if (!$configuration) {
            $configuration = array();
        }
        $configuration  = $this->convertFromPost($configuration);


        $json = array(
            'success'       => true,
        );

        $user = \Session::get('logged_user', false);
        if ($user) {
            if (!$user->user_configuration) {
                $user_configuration = array();
            } else {
                $user_configuration = unserialize($user->user_configuration);
            }
            $user_configuration[$key] = $configuration;
            $user->user_configuration = serialize($user_configuration);
            $user->save();
        }




        $response = \Response::forge(\Format::forge()->to_json($json), 200, array(
            'Content-Type' => 'application/json',
        ));
        $response->send(true);
        exit();
    }

    public function convertFromPost($arr) {
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                $arr[$key] = $this->convertFromPost($arr[$key]);
            } else {
                $arr[$key] = $arr[$key] == 'true' ? true : ($arr[$key] == 'false' ? false : $arr[$key]);
                $arr[$key] = is_numeric($arr[$key]) ? floatval($arr[$key]) : $arr[$key];
            }
        }
        return $arr;
    }
}

/* End of file desktop.php */
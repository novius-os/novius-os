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

	public function before($response = null) {
		parent::before($response);

		if (!\Cms\Auth::check()) {
			\Response::redirect('/admin/login' . ($_SERVER['REDIRECT_URL'] ? '?redirect='.urlencode($_SERVER['REDIRECT_URL']) : ''));
			exit();
		}

		$this->auto_render = false;
	}

	public function after($response) {

		// Yahoo CSS Reset
		//\Asset::css('http://yui.yahooapis.com/3.3.0/build/cssreset/reset-min.css', array(), 'css');

		\Asset::add_path('static/cms/');
		// laGrid before base
		\Asset::css('laGrid.css', array(), 'css');
		\Asset::css('base.css', array(), 'css');
		\Asset::css('form.css', array(), 'css');

		\Asset::add_path('static/cms/js/vendor/wijmo/');
        \Asset::css('aristo/jquery-wijmo.css', array(), 'css');
        \Asset::css('jquery.wijmo-complete.all.2.0.0b2.min.css', array(), 'css');

		\Asset::add_path('static/cms/js/jquery/jquery-ui-noviusos/');
        \Asset::css('jquery.nos.ostabs.css', array(), 'css');
        \Asset::css('jquery.nos.mp3grid.css', array(), 'css');

		return parent::after($response);
	}

	public function action_index()
	{
		$view = \View::forge('noviusos/index');

        $user = \Session::get('logged_user', false);

		$ostabs = array(
			'initTabs' => array(),
			'trayTabs' => array(
				array(
                    'iframe' => true,
					'url' => 'admin/tray/plugins',
					'iconClasses' => 'nos-icon24 nos-icon24-noviusstore',
					'label' => 'Novius store',
					'iconSize' => 24,
				),
				array(
                    'iframe' => true,
					'url' => 'generator/model',
					'iconClasses' => 'nos-icon24 nos-icon24-help',
					'label' => 'Help',
					'iconSize' => 24,
				),
				array(
                    'iframe' => true,
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
			),
			'newTab' => array(
				'panelId' => 'noviusospanel',
				'url' => 'admin/noviusos/noviusos/appstab',
				'iconClasses' => 'nos-icon16 nos-icon16-add',
				'iconSize' => 16,
			),
            'user_configuration' => unserialize($user->user_configuration),
		);

		$view->set('ostabs', \Format::forge($ostabs)->to_json(), false);

        $background_id = \Arr::get($user->getConfiguration(), 'misc.display.background');
        $background = $background_id ? Model_Media_Media::find($background_id) : false;
        $this->template->set('background', $background, false);
		$this->template->body = $view;
		return $this->template;
	}

	public function action_appstab()
	{
		\Config::load(APPPATH.'data'.DS.'config'.DS.'launchers.php', 'launchers');
		$launchers = \Config::get('launchers', array());

        \Config::load('cms::admin/launchers_default', true);
        $launchers_default = \Config::get('cms::admin/launchers_default', array());
        $launchers = array_merge($launchers, $launchers_default);
        //$app_installed = \Config::mergeWithUser('misc.apps', $app_installed);

        $apps = array();
        foreach ($launchers as $key => $app) {
            if (!empty($app['url']) && !empty($app['icon64'])) {
                $app['key'] = $key;
                $apps[] = $app;
            }
        }
        $apps = \Arr::sort($apps, 'order', 'asc');


		$view = \View::forge('noviusos/appstab', array(
			'apps'          => $apps,
		));
		return $view;
	}

    public function action_save_user_configuration() {
        $key            = \Input::post('key');
        $new_config     = \Input::post('configuration');

        if (!$new_config) {
            $new_config = array();
        }
        $new_config  = $this->convertFromPost($new_config);


        $json = array(
            'success' => true,
        );

        $user = \Session::get('logged_user', false);
        if ($user) {
            if (!$user->user_configuration) {
                $user_configuration = array();
            } else {
                $user_configuration = unserialize($user->user_configuration);
            }
            $configuration = &$user_configuration;
            \Arr::set($configuration, $key, $new_config);

            $user->user_configuration = serialize($user_configuration);
            $user->save();
            \Session::set('logged_user', $user);
        }


        \Response::json($json);
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

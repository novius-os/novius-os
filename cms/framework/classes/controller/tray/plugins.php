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

use Fuel\Core\File;
use Fuel\Core\View;

class Controller_Tray_Plugins extends Controller_Generic_Admin {

    public function action_index() {

        $LOCAL      = APPPATH.'modules'.DS;

        $plugins = array();
        $plugins['local'] = File::read_dir($LOCAL, 1);

        foreach ($plugins['local'] as $plugin => $foo) {
            $metadata = @include $LOCAL.$plugin.DS.'config'.DS.'metadata.php';
			unset($plugins['local'][$plugin]);
            $plugins['local'][trim($plugin, '/\\')] = $metadata;
        }

		\Config::load(APPPATH.'data'.DS.'config'.DS.'app_installed.php', 'app_installed');

		$app_installed = \Config::get('app_installed', array());
		$app_others = array();

		foreach ($plugins as $where => $list) {
			foreach ($list as $plugin => $metadata) {
				$plugin = trim($plugin, '/\\');
				if (isset($app_installed[$plugin])) {
					continue;
				}
				$app_others[$plugin] = $metadata;
			}
		}

		// Get the differences between the metadata files
		static::array_diff_key_assoc($app_installed, $plugins['local'], $diff);
		foreach ($app_installed as $app => &$metadata) {
			$instance = new \Cms\Module($app);
			if (!$instance->check_install()) {
				$metadata['dirty'] = true;
			}
			if (isset($diff[$app])) {
				$metadata['dirty'] = true;
			}
		}


        $this->template->body = View::forge('tray/plugins');

        $this->template->body->set('installed', $app_installed);
        $this->template->body->set('others', $app_others);

		return $this->template;
    }

	public function action_add($app) {
		$instance = new \Cms\Module($app);
		if ($instance->install()) {

			\Config::load(APPPATH.'data'.DS.'config'.DS.'app_installed.php', 'app_installed');
			$app_installed = \Config::get('app_installed', array());
            $metadata = @include APPPATH.'modules'.DS.$app.DS.'config'.DS.'metadata.php';
			$app_installed[$app] = $metadata;
			\Config::save(APPPATH.'data'.DS.'config'.DS.'app_installed.php', $app_installed);
		}

		\Response::redirect('admin/tray/plugins');
	}

	public function action_remove($app) {
		$instance = new \Cms\Module($app);

		if ($instance->uninstall()) {
			\Config::load(APPPATH.'data'.DS.'config'.DS.'app_installed.php', 'app_installed');
			$app_installed = \Config::get('app_installed', array());
			unset($app_installed[$app]);
			\Config::save(APPPATH.'data'.DS.'config'.DS.'app_installed.php', $app_installed);
		}

		\Response::redirect('admin/tray/plugins');
	}

	public function action_upload() {
		if (empty($_FILES['zip'])) {
			\Response::redirect('admin/tray/plugins');
		}

		if (!is_uploaded_file($_FILES['zip']['tmp_name'])) {
			\Session::forge()->set_flash('notification.plugins', array(
				'title' => 'Upload error.',
				'type' => 'error',
			));
			\Response::redirect('admin/tray/plugins');
		}

		if ($_FILES['zip']['error'] != UPLOAD_ERR_OK) {
			\Session::forge()->set_flash('notification.plugins', array(
				'title' => 'Upload error n°'.$_FILES['zip']['error'].'.',
				'type' => 'error',
			));
			\Response::redirect('admin/tray/plugins');
		}

		$files = array();
		$za = new \ZipArchive();
		$zip_file = $_FILES['zip']['tmp_name'];
		$za->open($zip_file);
		for ($i=0; $i<$za->numFiles;$i++) {
			$files[] = $za->getNameIndex($i);
		}

		$root_files = array();
		foreach ($files as $k => $f) {
			if (substr($f, -1) == '/' && substr_count($f, '/') <= 1) {
				$root_files[] = $f;
			}
		}

		$count = count($root_files);
		if ($count == 0) {
			\Session::forge()->set_flash('notification.plugins', array(
				'title' => $name.' already exists in you module directory.',
				'type' => 'error',
			));
			\Response::redirect('admin/tray/plugins');
		}
		$root = ($count == 1 ? $root_files[0] : '');

		$metadata_file = $root.'config/metadata.php';
		$metadata = \Fuel::load('zip://'.$zip_file.'#'.$metadata_file);

		if (empty($metadata['install_folder'])) {
			\Session::forge()->set_flash('notification.plugins', array(
				'title' => 'This is not a valid module archive.',
				'type' => 'error',
			));
			\Response::redirect('admin/tray/plugins');
		}

		$path = APPPATH.'modules'.DS.$metadata['install_folder'];
		if (is_dir($path.$name)) {
			\Session::forge()->set_flash('notification.plugins', array(
				'title' => $metadata['install_folder'].' already exists in you module directory.',
				'type' => 'error',
			));
			\Response::redirect('admin/tray/plugins');
		}

		usort($files, function($a, $b) {
			return strlen($a) > strlen($b);
		});

		// @todo better error handling ?
		// @todo skip stupid files ?
		// @todo appropriate chmod ?
		try {
			$old = umask(0);
			@mkdir($path, 0777);
			umask($old);

			$root_length = strlen($root);

			foreach ($files as $file) {
				$dest = $path.DS.substr($file, $root_length);
				if (substr($file, -1) == '/') {
					is_dir($dest) || @mkdir($dest, 0777);
				} else {
					copy('zip://'.$zip_file.'#'.$file, $dest);
				}
			}
		} catch (\Exception $e) {
			\Fuel\Core\File::delete_dir($path, true, true);
		}
		\Response::redirect('admin/tray/plugins');
	}

	/**
	 * Computes the diff between 2 arrays, bith on keys and values.
	 * @param type $arr1  First array to compare
	 * @param type $arr2  Second array to compare
	 * @param type $diff  Returns the diff between the 2 array
	 */
	protected static function array_diff_key_assoc($arr1, $arr2, &$diff = array()) {
		foreach ($arr1 as $k => $v) {
			if (!isset($arr2[$k])) {
				$diff[$k] = array($v, null);
			} else if (is_array($v)) {
				unset($subdiff);
				static::array_diff_key_assoc($v, $arr2[$k], $subdiff);
				if (!empty($subdiff)) {
					$diff[$k] = $subdiff;
				}
			} else if ($arr2[$k] !== $v) {
				$diff[$k] = array($v, $arr2[$k]);
			}
		}
		foreach ($arr2 as $k => $v) {
			if (!isset($arr1[$k])) {
				$diff[$k] = array(null, $v);
			}
		}
	}
}
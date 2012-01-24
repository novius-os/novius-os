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

use Fuel\Core\Config;

class Controller_Admin_Media_Mode_Image extends Controller_Admin_Media_List {

	public function action_index()
	{
		if (!\Cms\Auth::check()) {
			\Response::redirect('/admin/login?redirect='.urlencode($_SERVER['REDIRECT_URL']));
			exit();
		}
		
		$this->mp3grid['urljson'] = 'static/cms/js/admin/media/media_image.js';

		$view = \View::forge('admin/media/mp3table/widget');

        $view->set('urljson', $this->mp3grid['urljson'], false);
		$view->set('i18n', \Format::forge($this->mp3grid['i18n'])->to_json(), false);

		return $view;
	}
}

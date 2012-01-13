<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Cms\Media;

use Fuel\Core\Config;
use Cms\Controller_Mp3table_List;

class Controller_Admin_Mode_Tinymce extends Controller_Mp3table_List {

    public $template = 'cms::templates/html5';

	public function before() {
		Config::load('cms_media::admin/media', true);
		$this->config = Config::get('cms_media::admin/media', array());

		// Add the "Choose" action button
		if (isset($this->config['ui']['actions'])) {
			array_unshift($this->config['ui']['actions'], array(
				'label' => 'Choose',
				'action'   =>  'function(item) {
					console.log(item);
					$.nos.listener.fire("tinymce.image_select", true, [item]);
				}')
			);
		}

		// Remove the choices for the extension
		foreach ($this->config['ui']['inspectors'] as $id => $inspector) {
			if ($inspector['widget_id'] == 'inspector-extension') {
				unset($this->config['ui']['inspectors'][$id]);
			}
		}

		// Force only images to be displayed
		$this->config['ui']['values'] = array(
			'media_extension' => array('image'),
		);

		parent::before();
	}

	public function after($response) {
		\Asset::add_path('static/modules/cms_media/');
		\Asset::css('admin.css', array(), 'css');

		return parent::after($response);
	}
}

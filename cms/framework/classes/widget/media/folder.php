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

class Widget_Media_Folder extends \Fieldset_Field {

	protected $options = array(
		'mode' => 'image',
		'inputFileThumb' => array(),
	);

    public function __construct($name, $label = '', array $attributes = array(), array $rules = array(), \Fuel\Core\Fieldset $fieldset = null) {

		//$attributes['type']   = 'hidden';
		$attributes['class'] = (isset($attributes['class']) ? $attributes['class'] : '').' media';

		if (empty($attributes['id'])) {
			$attributes['id'] = uniqid('media_');
		}
		if (!empty($attributes['widget_options'])) {
			$this->options = \Arr::merge($this->options, $attributes['widget_options']);
		}
		unset($attributes['widget_options']);

        parent::__construct($name, $label, $attributes, $rules, $fieldset);
    }

    /**
     * How to display the field
     * @return type
     */
    public function build() {
		$folder_id = $this->get_value();
		if (!empty($folder_id)) {
			$folder = \Cms\Model_Media_Folder::find($folder_id);
			if (!empty($folder)) {
				$this->options['selected-folder'] = $folder_id;
			}
		}
		$this->set_attribute('data-widget-options', htmlspecialchars(\Format::forge()->to_json($this->options)));
        return (string) parent::build().\Request::forge('cms/admin/media/inspector/folder/list')->execute(array('widget/media_folder', array(
            'input_id' => $this->get_attribute('id'),
            'selected' => $folder_id,
        )))->response();
    }
}

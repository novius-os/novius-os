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

	protected $options = array();

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
        return (string) \Request::forge('cms/admin/media/inspector/folder/list')->execute(array('inspector/modeltree_radio', array(
	        'params' => array(
		        'treeUrl' => 'admin/cms/media/inspector/folder/json',
		        'widget_id' => 'cms_media_folders',
	            'input_id' => $this->get_attribute('id'),
	            'selected' => array(
		            'id' => $folder_id,
		            'model' => 'Cms\\Model_Media_Folder',
	            ),
		        'columns' => array(
			        array(
				        'dataKey' => 'title',
			        )
		        ),
		        'height' => '150px',
		    ),
        )))->response();
    }
}

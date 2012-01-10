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

class Widget_Media extends \Fieldset_Field {

	protected $media;

    public function __construct($name, $label = '', array $attributes = array(), array $rules = array(), \Fuel\Core\Fieldset $fieldset) {
        parent::__construct($name, $label, $attributes, $rules, $fieldset);

		$attributes['type'] = 'hidden';
		$attributes['class'] = 'media';
        $this->media = new \Fieldset_Field($name, $label, $attributes, $rules, $fieldset);
    }

	public function set_value($text) {
		$this->media->set_value((int) $text);
	}

    public function get_value() {
        return $this->media->get_value();
    }

    /**
     * How to display the field
     * @return type
     */
    public function build() {

	    static::form_append($this->fieldset());
		$media_id = $this->media->get_value();
		if (!empty($media_id)) {
			\Fuel::add_module('cms_media');
			$media = \Cms\Media\Model_Media::find($media_id);
			$this->media->set_attribute('data-selected-image', $media->get_public_path_resized(64, 64));
		}
        return (string) $this->media;
    }

	public function form_append($fieldset) {
		$fieldset->append(<<<JS
<script type="text/javascript">
require(['jquery-nos'], function ($) {
	$(function() {
		$(':hidden.media').each(function() {
			$.nos.media($(this), {
				mode: 'image'
			});
		});
	});
});
</script>
JS
		);
	}

}

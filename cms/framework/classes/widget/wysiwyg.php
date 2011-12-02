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

class Widget_Wysiwyg extends \Fieldset_Field {
	
	protected $wysiwyg;
    
    public function __construct($name, $label = '', array $attributes = array(), array $rules = array(), \Fuel\Core\Fieldset $fieldset) {
        parent::__construct($name, $label, $attributes, $rules, $fieldset);
        
		$attributes['type'] = 'textarea';
		$attributes['class'] = 'tinymce';
        $this->wysiwyg = new \Fieldset_Field($name, $label, $attributes, $rules, $fieldset);
    }
	
	public function set_value($text) {
		$this->wysiwyg->set_value(\Security::xss_clean($text));
	}
	
    /**
     * How to display the field
     * @return type 
     */
    public function build() {
        
		static::form_append($this->fieldset());
        return (string) $this->wysiwyg;
    }
	
	public function form_append($fieldset) {
		$fieldset->append(<<<JS
<script type="text/javascript">
	require([
	'static/cms/js/jquery/tinymce/jquery.tinymce_src',
	'static/cms/js/jquery/tinymce/jquery.wysiwyg'
	], function() {
		$('textarea.tinymce').wysiwyg({

		});
	});
</script>
JS
		);
	}
    
}

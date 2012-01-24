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

class Widget_Date_Picker extends \Fieldset_Field {

	protected $options = array(
		'showOn'            => 'both',
		'buttonImage'       => 'static/cms/img/icons/date-picker.png',
		'buttonImageOnly'   => true,
		'autoSize'          => true,
		'dateFormat'        => 'yy-mm-dd',
		'showButtonPanel'   => true,
		'changeMonth'       => true,
		'changeYear'        => true,
		'showOtherMonths'   => true,
		'selectOtherMonths' => true,
		'gotoCurrent'       => true,
		'firstDay'          => 1,
		'showAnim'          => 'slideDown',
	);

    public function __construct($name, $label = '', array $attributes = array(), array $rules = array(), \Fuel\Core\Fieldset $fieldset) {

        $attributes['type']  = 'text';
		$attributes['class'] = (isset($attributes['class']) ? $attributes['class'] : '').' datepicker';

		if (empty($attributes['id'])) {
			$attributes['id'] = uniqid();
		}
		if (!empty($attributes['widget_options'])) {
			$this->options = \Arr::merge($this->options, $attributes['widget_options']);
		}
		unset($attributes['date_picker']);

        parent::__construct($name, $label, $attributes, $rules, $fieldset);
    }

    /**
     * How to display the field
     * @return string
     */
    public function build() {

	    $this->fieldset()->append($this->js_init());

		$this->set_attribute('data-datepicker-options', htmlspecialchars(\Format::forge()->to_json($this->options)));
        return (string) parent::build();
    }

	public function js_init() {
		$id = $this->get_attribute('id');
		return <<<JS
<script type="text/javascript">
	require([
		'jquery-nos'
	], function( $, undefined ) {
		$(function() {
			$('input#$id').datepicker($('input#$id').data('datepicker-options'));
		});
	});
</script>
JS;
	}

}

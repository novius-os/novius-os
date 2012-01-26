<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

class Fieldset extends \Fuel\Core\Fieldset {

	protected $append = array();

	public function build($action = null) {
		return parent::build($action).implode('', $this->append);
	}

	public function append($content) {
		$this->append[] = $content;
	}

	public function open($action = null) {
		$attributes = $this->get_config('form_attributes');
		if ($action and ($this->fieldset_tag == 'form' or empty($this->fieldset_tag)))
		{
			$attributes['action'] = $action;
		}

		$open = ($this->fieldset_tag == 'form' or empty($this->fieldset_tag))
			? $this->form()->open($attributes).PHP_EOL
			: $this->form()->{$this->fieldset_tag.'_open'}($attributes);

		return $open;
	}

	public function close() {

		$close = ($this->fieldset_tag == 'form' or empty($this->fieldset_tag))
			? $this->form()->close().PHP_EOL
			: $this->form()->{$this->fieldset_tag.'_close'}();

		return $close.implode('', $this->append);
	}

	public function form_name($value) {
		if ($field = $this->field('form_name')) {
			return $field->get_value() == $value;
		}
		$this->add('form_name', '', array('type' => 'hidden', 'value' => $value));
	}

	/**
	 *
	 * @param   \Fuel\Core\Fieldset_Field  $field  A field instance
	 * @return  \Fuel\Core\Fieldset_Field
	 */
	public function add_field(\Fuel\Core\Fieldset_Field $field) {
		$name = $field->get_name();
		if (empty($name))
		{
			throw new \InvalidArgumentException('Cannot create field without name.');
		}

		// Check if it exists already, if so: return and give notice
		if ($existing = static::field($name))
		{
			\Error::notice('Field with this name "'.$name.'" exists already, cannot be overwritten through add().');
			return $existing;
		}

		// Make sure fieldset is current
		if ($field->get_fieldset() != $this) {
			\Error::notice('A field added through add() must have the correct parent fieldset.');
			return false;
		}
		$this->fields[$name] = $field;

		return $field;
	}

	/**
	 * Override default populate() to allow widgets populate themselves
	 * @param   array|object  The whole input array
	 * @param   bool          Also repopulate?
	 * @return  Fieldset this, to allow chaining
	 */
	public function populate($input, $repopulate = false) {
		foreach ($this->fields as $f) {
			if (substr(strtolower(\Inflector::denamespace(get_class($f))), 0, 6) == 'widget' && isset($input->{$f->name})) {
				$f->populate($input);
			}
		}
		return parent::populate($input, $repopulate);
	}

	/**
	 * Override default repopulate() to allow widgets populate themselves
	 *
	 * @param   array|object  input for initial population of fields, this is deprecated - you should use populate() instea
	 * @return  Fieldset  this, to allow chaining
	 */
	public function repopulate($repopulate = false) {

		$input = strtolower($this->form()->get_attribute('method', 'post')) == 'get' ? \Input::get() : \Input::post();

		foreach ($this->fields as $f) {

			// Don't repopulate the CSRF field
			if ($f->name === \Config::get('security.csrf_token_key', 'fuel_csrf_token'))
			{
				continue;
			}
			if (substr(strtolower(\Inflector::denamespace(get_class($f))), 0, 6) == 'widget')
			{
				// Widgets populates themselves
				$f->repopulate($input);
			}
		}

		return parent::populate($input, $repopulate);
	}

	/**
	 * Get populated values
	 *
	 * @param    string         null to fetch an array of all
	 * @return    array|false     returns false when field wasn't found
	 */
	public function value($name = null) {
		if ($name === null)
		{
            $values = \Input::post();
            foreach ($this->fields as $f)
			{
				$class = strtolower(\Inflector::denamespace(get_class($f)));
				if (substr($class, 0, 6) == 'widget') {
					$values[$f->name] = $f->get_value();
				}
            }
			return $values;
		}
		return $this->field($name)->get_value();
	}

	/**
	 * Set a Model's properties as fields on a Fieldset, which will be created with the Model's
	 * classname if none is provided.
	 *
	 * @param   string
	 * @param   Fieldset|null
	 * @return  Fieldset
	 */
	public function add_model_widgets($class, $instance = null, $options = array())
	{
		if (is_object($class)) {
			$instance = $class;
			$class = get_class($class);
			$options = $instance;
		}

		$properties = is_object($instance) ? $instance->properties() : $class::properties();
		$this->add_widgets($properties);

		$instance and $this->populate($instance);

		return $this;
	}

	public function add_widgets($properties, $options = array()) {
		foreach ($properties as $p => $settings)
		{
			if (!empty($options['action']) && isset($settings[$options['action']]) && false === $settings[$options['action']]) {
				continue;
			}
			//if (isset($settings['widget']['options']))
			//{
			//    foreach ($settings['widget']['options'] as $key => $value)
			//    {
			//        $settings['widget']['options'][$key] = __($value) ?: $value;
			//    }
			//}

			$label       = isset($settings['label']) ? $settings['label'] : $p;
			$attributes  = isset($settings['form']) ? $settings['form'] : array();
			if (!empty($settings['widget'])) {
				 $class = Inflector::words_to_upper('Cms\Widget_'.$settings['widget']);
				 $attributes['widget_options'] = isset($settings['widget_options']) ? $settings['widget_options'] : array();
				 $field = new $class($p, $label, $attributes, array(), $this);
				 $this->add_field($field);
			} else {
				$field = $this->add($p, $label, $attributes);
			}
			if ( ! empty($settings['validation']))
			{
				foreach ($settings['validation'] as $rule => $args)
				{
					if (is_int($rule) and is_string($args))
					{
						$args = array($args);
					}
					else
					{
						array_unshift($args, $rule);
					}

					call_user_func_array(array($field, 'add_rule'), $args);
				}
			}
		}
	}

	public function format_js_validation($name, $args) {

		static $i = 1;

		if ($name == 'required') {
			return array('required', true);
		}

		if ($name == 'min_length') {
			return array('minlength', $args[0]);
		}

		if ($name == 'max_length') {
			return array('maxlength', $args[0]);
		}

		if ($name == 'exact_length') {
			return array('length', $args[0]);
		}

		if ($name == 'match_field') {
			$field_id = $this->field($args[0])->get_attribute('id');
			if (empty($field_id)) {
				$field_id = 'field_id_'.$i++;
				$this->field($args[0])->set_attribute('id', $field_id);
			}
			return array('equalTo', '#'.$field_id);
		}

		if ($name == 'valid_email') {
			return array('email', true);
		}

		return false;
		return array($name, $args);
	}

	public function js_validation() {

		static $i = 1;

		$form_attributes = $this->get_config('form_attributes', array());
		if (empty($form_attributes['id'])) {
			$form_attributes['id'] = 'form_id_'.$i++;
		}
		$this->set_config('form_attributes', $form_attributes);

		$json = array();
		foreach ($this->fields as $f) {

			$rules = $f->js_validation();

			if (empty($rules)) {
				continue;
			}

			foreach ($rules as $rule) {
				if (empty($rule)) {
					continue;
				}

				list($name, $args) = $rule;
				is_array($name) and $name = reset($name);

				list($js_name, $js_args) = $this->format_js_validation($name, $args);
				if (empty($js_name)) {
					continue;
				}
				$json['rules'][$f->name][$js_name] = $js_args;

				// Computes the error message, replacing :args placeholders with {n}
				$error = new \Validation_Error($f, '', array($name => ''), array());
				$error = $error->get_message();
				preg_match_all('`:param:(\d+)`', $error, $m);
				foreach ($m[1] as $int) {
					$error = str_replace(':param:'.$int, '{' . ($int - 1).'}', $error);
				}
				$json['messages'][$f->name][$js_name] = $error;
			}
		}
		//\Debug::dump($json);
		$validate = \Format::forge()->to_json($json);
		$this->append(<<<JS
<script type="text/javascript">
require(['jquery', 'static/cms/js/jquery/jquery-validation/jquery.validate.min'], function($) {
	var json = $validate;
	//console.log($validate);
	$('#{$form_attributes['id']}').validate($.extend({}, json, {
		submitHandler: function(form) {
			require(['jquery-nos', 'static/cms/js/jquery/jquery-form/jquery.form.min'], function($) {
				$(form).ajaxSubmit({
					dataType: 'json',
					success: function(json) {
						$.nos.ajax.success(json);
					},
					error: function() {
						$.nos.notify('An error occured', 'error');
					},
				});
			});
		}
	}));
	require(['static/cms/js/jquery/jquery-form/jquery.form.min', 'jquery-nos']);
});
</script>
JS
		);
	}

	public static function build_from_config($config, $model = null, $options = array()) {



		if (is_object($model)) {
			$instance = $model;
			$class = get_class($instance);
			empty($options['action']) && $options['action'] = 'edit';
		} else if (is_string($model)) {
			$instance = null;
			$class = $model;
			empty($options['action']) && $options['action'] = 'add';
		} else if (is_array($model)) {
			$options = $model;
			$class = null;
			$instance = null;
		}

		$fieldset = \Fieldset::forge(uniqid(), array(
			'inline_errors'  => true,
			'auto_id'		 => true,
			'required_mark'  => ' *',
			'error_template' => '{error_msg}',
			'error_class'    => 'error',
		));

		if (!empty($options['form_name'])) {
			$fieldset->form_name($options['form_name']);
		}
		$fieldset->add_widgets($config, $options);

		if (!empty($options['extend']) && is_callable($options['extend'])) {
			call_user_func($options['extend'], $fieldset);
		}

		$instance && $fieldset->populate($instance);
		if (\Input::method() == 'POST' && (empty($options['form_name']) || \Input::post('form_name') == $options['form_name'])) {
			$fieldset->repopulate();
			if ($fieldset->validation()->run($fieldset->value())) {
				$data = $fieldset->validated();
				if (!empty($options['complete']) && is_callable($options['complete'])) {
					call_user_func($options['complete'], $data);
				} else {
                    self::defaultComplete($data, $model, $config, $options);
                }
			} else {
				 \Response::json(array(
					'error' => (string) current($fieldset->error()),
					'_error' => $fieldset->error(),
					'config' => $config,
				));
			}
		}
		return $fieldset;
	}

    public static function defaultComplete ($data, $object, $fields, $options) {

		if (!is_object($object)) {
			return;
		}

		if (empty($options['error'])) {
			$options['error'] = function(\Exception $e, $object, $data) {
				return array(
					'error' => \Fuel::$env == \Fuel::DEVELOPMENT ? $e->getMessage() : 'An error occured.',
				);
			};
		}

		foreach ($fields as $name => $config)
		{
			$type = \Arr::get($config, 'form.type', null);

			if (!empty($config['widget']) && in_array($config['widget'], array('widget_text', 'widget_empty'))) {
				continue;
			}

			switch($type) {
				case 'checkbox' :
					if (empty($data[$name])) {
						$object->$name = null;
					}
					break;

				// Skip submit fields
				case 'submit' :
					continue 2;
					break;

				default :
					if (isset($data[$name])) {
						try {
							$object->$name = $data[$name];
						} catch (\Exception $e) {
							$body = array(
								'error' => $e->getMessage(),
							);
						}
					}
			}
		}

		if (!empty($options['before_save']) && is_callable($options['before_save']))
		{
			call_user_func($options['before_save'], $object, $data);
		}

		// Will trigger cascade_save for media and wysiwyg
		try {
			$object->save();

			if (!empty($options['success']) && is_callable($options['success']))
			{
				$body = call_user_func($options['success'], $object, $data);
			} else {
				$body = array(
					'notify' => 'Operation completed successfully.',
				);
			}
		} catch (Exception $e) {
			if (empty($options['error']) && is_callable($options['error'])) {
				$body = call_user_func($options['error'], $e, $object, $data);
			} else {
				$body = array(
					'error' => $e->getMessage(),
				);
			}
		}

		\Response::json($body);
	}
}

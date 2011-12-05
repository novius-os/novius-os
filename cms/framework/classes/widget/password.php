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

class Widget_Password extends \Fieldset_Field {

    /**
     * @var \Cms\Fieldset_Field
     */
    protected $password;

    /**
     * @var \Cms\Fieldset_Field
     */
    protected $verification;

    protected $display = array();

    /**
     *
     * @param  string  $name
     * @param  string  $label
     * @param  array   $attributes
     * @param  array   $rules
     * @param  \Fuel\Core\Fieldset  $fieldset
     * @return  Cms\Widget_Date
     */
    public function __construct($name, $label = '', array $attributes = array(), array $rules = array(), \Fuel\Core\Fieldset $fieldset) {
        parent::__construct(uniqid(), '', array(), array(), $fieldset);

        isset($attributes['type']) or $attributes['type'] = 'password';

		// We need the id for the match_field rule
		static $i = 1;
        isset($attributes['id']) or $attributes['id'] = 'field_password_'.$i++;

        $this->display[] = new \Fieldset_Field($name, $label, $attributes, $rules, $fieldset);
        // Purpose of empty widget is validation!
        $this->password = $fieldset->add_field(new Widget_Empty($name, $label, $attributes, $rules, $fieldset));

		unset($attributes['id']);
		$label = $label.' (confirmation)';
		$this->display[] = new \Fieldset_Field('verification_'.$name, $label, $attributes, $rules, $fieldset);
        $this->verification = $fieldset->add_field(new Widget_Empty('verification_'.$name, $label, array(), array(), $fieldset))
                                       ->add_rule('match_field', $name);

		$this->display[1]->add_rule('match_field', $name);
	}

    public function add_rule($callback) {
        // Relay all validation rules to the password
        call_user_func_array(array($this->password, 'add_rule'), func_get_args());
        if ($callback == 'required') {
            call_user_func_array(array($this->verification, 'add_rule'), func_get_args());
            call_user_func_array(array($this->display[0], 'add_rule'), func_get_args());
            call_user_func_array(array($this->display[1], 'add_rule'), func_get_args());
        }
    }

    /**
     * How to display the field
     * @return type
     */
    public function build() {
        return implode('', $this->display);
    }

    public function repopulate(array $input) {
        list($password, $verification) = array($input[$this->password->name], $input[$this->verification->name]);

        // Remember previous entered values
        if (!empty($password)) {
            $this->password->set_value($password);
            $this->display[0]->set_value($password);
        }
        if (!empty($verification)) {
            $this->verification->set_value($verification);
            $this->display[1]->set_value($verification);
        }
    }

    public function get_name() {
        return $this->password->name;
    }

    public function get_value() {
        return $this->password->value;
    }

}

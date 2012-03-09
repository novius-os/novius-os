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

class Widget_Date_Select extends \Fieldset_Field {

    /**
     * @var \Cms\Fieldset_Field
     */
    protected $year;

    /**
     * @var \Cms\Fieldset_Field
     */
    protected $month;

    /**
     * @var \Cms\Fieldset_Field
     */
    protected $day;


    /**
     * @var \Cms\Fieldset_Field
     */
    protected $time;

    /**
     * @var \Cms\Fieldset_Field
     */
    protected $parts;

    /**
     *
     * @param  string  $name
     * @param  string  $label
     * @param  array   $attributes
     * @param  array   $rules
     * @param  \Fuel\Core\Fieldset  $fieldset
     * @return  Cms\Widget_Date
     */
    public function __construct($name, $label = '', array $attributes = array(), array $rules = array(), \Fuel\Core\Fieldset $fieldset = null) {
        parent::__construct($name, $label, $attributes, $rules, $fieldset);

		$attributes = \Arr::merge($attributes, array(
			'day' => array(
				'size' => '2',
				'style' => 'width: 20px;'
			),
			'month' => array(
				'type'    => 'select',
			),
			'year' => array(
				'size' => '6',
				'style' => 'width: 40px;'
			),
			'time' => array(
				'type' => 'hidden',
			),
		));

		// Can't merge this key without messing up
		if (empty($attributes['month']['options'])) {
			$attributes['month']['options'] = static::_get_month_names();
		}

        // Build the fields used by the widget
        $this->parts = \Fieldset::forge($name.uniqid());

        $this->day   = $this->parts->add($name.'_day',   '', $attributes['day']);
        $this->month = $this->parts->add($name.'_month', '', $attributes['month']);
        $this->year  = $this->parts->add($name.'_year',  '', $attributes['year']);
        $this->time  = $this->parts->add($name.'_time',  '', $attributes['time']);

        $this->add_rule(array('valid_date' => function($value) {
            list($date, $time) = explode(' ', $value.' ');
            list($year, $month ,$day) = explode('-', $date);
            return empty($value) or !empty($year) and !empty($month) and !empty($day) and checkdate((int) $month, (int) $day, (int) $year);
        }));
    }

	/*
	public function js_validation() {
		return array(
			 array('valid_date', array($this->name)),
		);
	}
	*/

    protected static function _get_month_names() {
        static $months = null;
        empty($months) and $months = array(
            1  => \Date::create_from_string('01/01/2011', 'eu')->format('%B'),
            2  => \Date::create_from_string('01/02/2011', 'eu')->format('%B'),
            3  => \Date::create_from_string('01/03/2011', 'eu')->format('%B'),
            4  => \Date::create_from_string('01/04/2011', 'eu')->format('%B'),
            5  => \Date::create_from_string('01/05/2011', 'eu')->format('%B'),
            6  => \Date::create_from_string('01/06/2011', 'eu')->format('%B'),
            7  => \Date::create_from_string('01/07/2011', 'eu')->format('%B'),
            8  => \Date::create_from_string('01/08/2011', 'eu')->format('%B'),
            9  => \Date::create_from_string('01/09/2011', 'eu')->format('%B'),
            10 => \Date::create_from_string('01/10/2011', 'eu')->format('%B'),
            11 => \Date::create_from_string('01/11/2011', 'eu')->format('%B'),
            12 => \Date::create_from_string('01/12/2011', 'eu')->format('%B'),
        );
        return $months;
    }

	/**
	 *
	 * @param string  $value       YYYY-MM-DD time
	 * @param bool    $repopulate
	 */
    public function set_value($value, $repopulate = false) {

        list($date, $time) = explode(' ', $value.' ');
        list($year, $month ,$day) = explode('-', $date);

        $this->year->set_value($year, $repopulate);
        $this->month->set_value($month, $repopulate);
        $this->day->set_value($day, $repopulate);
        $this->time->set_value($time, $repopulate);

        parent::set_value($value, $repopulate);
    }

    /**
     * How to display the field
     * @return string HTML output
     */
    public function build() {

        if (!empty($this->attributes['readonly'])) {
            if (empty($this->attributes['date_format'])) {
                $html = $this->value;
            } else {
                try {
                    $html = (string) \Date::create_from_string($this->value, 'mysql')->format($this->attributes['date_format']);
                } catch (\Exception $e) {
                    // Input pattern not recognized
                    $html = $this->value;
                }
            }
        } else {
            $this->parts->set_config('field_template', '{label} {field}');
            $html = (string) $this->day . (string) $this->month . (string) $this->year . (string) $this->time;
        }
		return $this->template($html);
    }

	public static function field($args = array()) {
		$args = \Arr::merge(array(
			'name' => 'date',
			'readonly' => false,
			'date_format' => 'eu_full',
			'value' => '',
			'day' => array(
				'attributes' => array(
					'size' => '2',
					'style' => 'width: 20px;'
				),
			),
			'month' => array(
				'attributes' => array(
					'type'    => 'select',
				),
			),
			'year' => array(
				'attributes' => array(
					'size' => '6',
					'style' => 'width: 40px;'
				),
			),
			'time' => array(
				'attributes' => array(
					'type' => 'hidden',
				),
			),
		), $args);


		// Can't merge this key without messing up
		if (empty($args['month']['attributes']['options'])) {
			$args['month']['attributes']['options'] = static::_get_month_names();
		}

		$fields = array(
			\Form::input($name.'_day',    '', $args['day']['attributes']),
			\Form::select($name.'_month', '', $args['month']['attributes']['options']),
			\Form::input($name.'_year',   '', $args['year']['attributes']),
			\Form::input($name.'_time',   '', $args['time']['attributes']),
		);
		return implode('', $fields);
	}

    /**
     * repopulate() takes the whole input array as parameter
     * @param array $input
     */
    public function repopulate(array $input) {
        list($year, $month, $day, $time) = array(
			isset($input[$this->name.'_year'])  ? $input[$this->name.'_year']  : null,
			isset($input[$this->name.'_month']) ? $input[$this->name.'_month'] : null,
			isset($input[$this->name.'_day'])   ? $input[$this->name.'_day']   : null,
			isset($input[$this->name.'_time'])  ? $input[$this->name.'_time']  : null
		);

        // Remember previous entered values
        empty($year)  or $this->year->set_value($year);
        empty($month) or $this->month->set_value($month);
        empty($day)   or $this->day->set_value($day);
        empty($time)  or $this->time->set_value($time);

        // If day or year is set, set the value for validation
        if (!empty($year) || !empty($day)) {
            $this->set_value(sprintf('%s-%s-%s %s', $year, $month, $day, $time));
        }
    }
}

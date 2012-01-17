<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

return array(
	'test_id' => array(
		'label' => 'Id.',
		'widget' => 'text',
	),
	'test_text' => array(
		'label' => 'Textfield',
		'validation' => array(
			'required',
		),
	),
	'test_textarea' => array(
		'label' => 'Textarea field',
		'form' => array(
			'type' => 'textarea',
			'cols' => 50,
			'rows' => 4,
		),
	),
	'test_date' => array(
		'label' => 'Date field',
		'widget' => 'date_select',
	),
	'test_wysiwyg' => array(
		'label' => 'Tiny MCE',
		'widget' => 'wysiwyg',
	),
	'test_select' => array(
		'label' => 'Dropdown field',
		'form' => array(
			'type' => 'select',
			'options' => array(
				'julian' => 'Julian',
				'antoine' => 'Antoine',
				'gilles' => 'Gillou',
				'seb' => 'Sébastien',
			),
		),
	),
	'test_checkbox' => array(
		'label' => 'Checkbox label',
		'form' => array(
			'type' => 'checkbox',
		),
	),
	'test_radio' => array(
		'label' => 'Radio field',
		'form' => array(
			'type' => 'radio',
			'options' => array(
				'julian' => 'Julian',
				'antoine' => 'Antoine',
				'gilles' => 'Gillou',
				'seb' => 'Sébastien',
			),
		),
	),
	'test_picker' => array(
		'label' => 'Date picker jQUery UI',
		'widget' => 'date_picker',
		'widget_options' => array(
			'firstDay' => 1,
		),
		'form' => array(
			'name' => 'toto_picker',
		),
	),
);

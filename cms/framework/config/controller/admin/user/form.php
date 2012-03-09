<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

return array (
    'fields' => array(
        /*'user_id' => array (
            'label' => 'ID',
            'add' => false,
            'widget' => 'text',
        ),*/
        'user_name' => array (
            'label' => 'Full name',
            'widget' => '',
            'validation' => array(
                'required',
            ),
        ),
        'user_firstname' => array (
            'label' => 'First name',
            'widget' => '',
            'validation' => array(
                'required',
            ),
        ),
        'user_email' => array(
            'label' => 'Email',
            'widget' => '',
            'validation' => array(
                'required',
                'valid_email',
            ),
        ),
        'user_password' => array (
            'label' => 'Password',
            'edit' => false,
			'form' => array(
				'type' => 'password',
			),
            'validation' => array(
                'required',
                'min_length' => array(6),
            ),
        ),
        'password_confirmation' => array (
            'label' => 'Password (confirmation)',
            'edit' => false,
			'form' => array(
				'type' => 'password',
			),
            'validation' => array(
                'required', // To show the little star
                'match_field' => array('user_password'),
            ),
        ),
        'user_last_connection' => array (
            'label' => 'Last login',
            'add' => false,
            'widget' => 'date_select',
			'form' => array(
				'readonly' => true,
				'date_format' => 'eu_full',
			),
        ),
		'save' => array(
			'label' => '',
			'form' => array(
				'type' => 'submit',
                'tag' => 'button',
                'data-icon' => 'check',
				'value' => 'Save',
			),
		),
    ),
);
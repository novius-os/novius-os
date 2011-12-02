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
        'user_fullname' => array (
            'label' => 'Full name',
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
            'widget' => 'password',
            'validation' => array(
                'required',
                'min_length' => array(6),
            ),
        ),
        'user_last_connection' => array (
            'label' => 'Last login',
            'add' => false,
            'widget' => 'date_select',
            'attributes' => array(
                'readonly' => false,
                'date_format' => 'eu_full',
            ),
        ),
    ),
);
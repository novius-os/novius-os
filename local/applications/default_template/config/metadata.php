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
    'name'    => 'Default template',
    'version' => '0.9-alpha',
    'provider' => array(
        'name' => 'Novius OS',
    ),
    'launchers' => array(
    ),
    'enhancers' => array(
    ),
    'templates' => array(
        'default_template_html5' => array(
            'file' => 'default_template::html5',
            'title' => 'Default HTML 5 template',
            'cols' => 1,
            'rows' => 1,
            'layout' => array(
                'content' => '0,0,1,1',
            ),
            'module' => '',
        ),
    ),
);

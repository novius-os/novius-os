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
    'namespace' => 'Cms\TplDefault',
    'display' => false,
    'templates' => array(
        'blog' => array(
            'file' => 'blog',
            'title' => 'Main template',
            'cols' => 3,
            'rows' => 1,
            'layout' => array(
                'content' => '0,0,2,1',
                'right' => '2,0,1,1',
            ),
        ),
    ),
);
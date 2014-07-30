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
    'name'    => 'Simple Twitter share',
    'version' => '5.0.1 (Elche)',
    'provider' => array(
        'name' => 'Novius OS',
    ),
    'i18n_file' => 'noviusos_simpletwitter::metadata',
    'data_catchers' => array(
        'noviusos_simpletwitter' => array(
            'title' => 'Twitter',
            'description'  => '',
            'iconUrl' => 'static/apps/noviusos_simpletwitter/img/twitter.png',
            'action' => array(
                'action' => 'window.open',
                'url' => 'https://twitter.com/intent/tweet?text={{urlencode:'.\Nos\DataCatcher::TYPE_TITLE.'}}&url={{urlencode:absolute_url}}',
            ),
            'onDemand' => true,
            'specified_models' => false,
            'required_data' => array(
                \Nos\DataCatcher::TYPE_TITLE,
            ),
            'optional_data' => array(
                \Nos\DataCatcher::TYPE_URL,
            ),
        ),
    ),
    'icons' => array(
        16  => 'static/apps/noviusos_simpletwitter/img/twitter.png',
    ),
);

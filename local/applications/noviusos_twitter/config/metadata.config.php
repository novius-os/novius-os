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
    'name'    => 'Twitter',
    'version' => '0.9-alpha',
    'provider' => array(
        'name' => 'Novius OS',
    ),
    'icon16' => 'static/apps/noviusos_twitter/img/twitter.png',
    'namespace' => 'Nos\Twitter',
    'data_catchers' => array(
        'noviusos_twitter_intent' => array(
            'title' => 'Twitter',
            'description'  => '',
            'iconUrl' => 'static/apps/noviusos_twitter/img/twitter.png',
            'action' => array(
                'action' => 'window.open',
                'url' => 'https://twitter.com/intent/tweet?text={{'.\Nos\DataCatcher::TYPE_TITLE.'}}&url={{'.\Nos\DataCatcher::TYPE_URL.'}}',
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
);

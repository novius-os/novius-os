<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Nos\Twitter;

class Controller_Admin_Share extends \Nos\Controller
{
    public function action_index()
    {
        return \Nos\Controller_Admin_DataCatcher::catcher_form(array(
            'catcher_name' => 'noviusos_twitter_intent',
            'view'         => 'noviusos_twitter::simple_form',
        ));
    }

    public function action_save()
    {
        try {
            list($item, $catcher_name) = \Nos\Controller_Admin_DataCatcher::save_catcher_nugget();

            $this->response(array(
                'notify' => strtr(__('Catcher "{catcher_name}" saved successfully.'), array(
                    '{catcher_name}' => \Arr::get($item->data_catchers(), $catcher_name.'.title'),
                )),
                'intent_url' => $this->intent_url($item),
            ));
        } catch (\Exception $e) {
            \Response::json(array(
                'error' => $e->getMessage(),
            ));
        }
    }

    public function intent_url($item)
    {
        $nugget_intent  = $item->get_catcher_nuggets('noviusos_twitter_intent')->content_data;
        $nugget_default = $item->get_default_nuggets();

        // The plus operator allows a merge without reindexing
        $nugget = $nugget_intent + $nugget_default;

        return 'https://twitter.com/intent/tweet?'.http_build_query(array(
            'text' => $nugget[\Nos\DataCatcher::TYPE_TITLE],
            'url' => $nugget[\Nos\DataCatcher::TYPE_URL],
        ), '', '&');
    }
}
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
        $model = \Input::get('model', null);
        $id = \Input::get('id', null);

        if (empty($model) or empty($id))
        {
            \Response::json(array(
                'error' => 'Insufficient parameters.',
            ));
        }

        try {
            $object = $model::find($id);
        } catch (\Exception $e) {
            \Response::json(array(
                'error' => 'Wrong parameters.',
            ));
        }
        return \View::forge('noviusos_twitter::simple_form', array(
            'object' => $object,
        ), false);
    }

    public function action_save()
    {
        try {
            $item = \Nos\Controller_Admin_DataCatcher::save_catcher_nugget('twitter_intent', array(
                \Nos\DataCatcher::TYPE_TITLE,
                \Nos\DataCatcher::TYPE_URL,
            ));

            $this->response(array(
                'notify' => 'testing',
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
        $nugget_intent  = $item->get_catcher_nuggets('twitter_intent')->content_data;
        $nugget_default = $item->get_default_nuggets();

        // The plus operator allow a merge without reindexing
        $nugget = $nugget_intent + $nugget_default;

        return 'https://twitter.com/intent/tweet?'.http_build_query(array(
            'text' => $nugget[\Nos\DataCatcher::TYPE_TITLE],
            'url' => $nugget[\Nos\DataCatcher::TYPE_URL],
        ), '', '&');
    }
}
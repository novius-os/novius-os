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

class Orm_Behaviour_Publishable extends Orm_Behavior
{
	protected $_class = null;

	/**
	 * publication_bool_property
	 * publication_start_property
	 * publication_end_property
	 */
	protected $_properties = array();

	public function __construct($class)
	{
		$this->_class = $class;
		$this->_properties = call_user_func($class . '::observers', get_class($this));
	}

    public static function dataset(&$dataset) {
        $dataset['publication_status'] = array(__CLASS__, 'publication_status');
    }

    public static function publication_status($item) {
        $published = $item->published();
        if ($published === true) {
            return '<img class="publication_status" src="static/cms/img/icons/status-green.png"> '.__('Published');
        }
        if ($published === false) {
            return '<img class="publication_status" src="static/cms/img/icons/status-red.png"> '.__('Not published');
        }
        return '<img class="publication_status" src="static/cms/img/icons/status-schedule.png"> '.strtr(__('From {date}'), array(
            '{date}' => \Date::create_from_string($published)->format('local'),
        ));
    }

	/**
	 * Returns the locale of the current object
	 *
	 * @return string
	 */
	public function published($object) {
        $bool = $this->_properties['publication_bool_property'];
        if (!empty($bool)) {
            return (bool) $object->get($bool);
        }
        // @todo publication start / end
	}

	public function before_search(&$where, &$order_by = array(), &$options = array()) {
		foreach ($where as $k => $w) {
			if ($w[0] == 'published') {
                $bool = $this->_properties['publication_bool_property'];
				if ($w[1] === true) {
					$where[$k] = array($bool, 1);
				} else if ($w[1] === false) {
					$where[$k] = array($bool, 0);
				} else {
                    unset($where[$k]);
                }
			}
		}
	}

    public function form_processing($item, $data, $response_json) {
        $props = $item->behaviors(__CLASS__);
        $publishable = $props['publication_bool_property'];
        // $data[$publishable] can possibly be filled with the data (see multi-line comment below)
        $item->set($publishable, (string) (int) (bool) \Input::post($publishable));
        $response_json['publication_initial_status'] = $item->get($publishable);
    }

    /*
    // This is only needed if we want the $data variable from the above function to be filled with the publishable attribute

    public function form_fieldset_fields($item, &$fieldset) {
        $props = $item->behaviors(__CLASS__);
        $publishable = $props['publication_bool_property'];
        // Empty array just so the data are retrieved from the input
        $fieldset[$publishable] = array();
    }
    */

}
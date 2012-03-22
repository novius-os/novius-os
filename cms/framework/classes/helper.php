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

class Helper {

    static $locales = array();

    public function _init() {

        \Config::load('locales', true);
        static::$locales = \Config::get('locales', array());
    }

	public static function flag($locale) {
        // Convert lang_LOCALE to locale
        list($lang, $country) = explode('_', $locale.'_');
        if (!empty($country)) {
            $lang = strtolower($country);
        }
        switch($lang) {
            case 'en':
                $lang = 'gb';
                break;
        }
        return '<img src="static/cms/img/flags/'.$lang.'.png" title="'.\Arr::get(static::$locales, $locale, $locale).'" /> ';
	}

    public static function flag_empty() {
        return '<span style="display:inline-block; width:16px;"></span> ';
    }
}

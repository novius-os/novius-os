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

class I18n
{

	private static $_messages = array();

    private static $_group;

    private static $_locale;

    private static $_locale_stack = array();

    private static $_loaded_files = array();

	public static $fallback;

	public static function _init()
	{
		static::$fallback = (array) \Config::get('language_fallback', 'en');
        static::setLocale(\Fuel::$locale);
	}

    public static function setLocale($locale)
    {
        if (static::$_locale) {
            static::$_locale_stack[] = static::$_locale;
        }

        list($remaining, $variant) = explode('@', $locale.'@');
        list($remaining, $encoding) = explode('.', $remaining.'.');
        list($language, $country) = explode('_', $remaining.'_');
        if (!$country) {
            $country = strtoupper($language);
        }
        static::$_locale = $language.'_'.$country;
    }

    public static function restoreLocale()
    {
        static::$_locale = array_pop(static::$_locale_stack);
    }

	public static function load($file, $group = null)
	{
		$languages = static::$fallback;
		array_unshift($languages, static::$_locale, substr(static::$_locale, 0, 2));

		$_messages = array();
		foreach ($languages as $lang)
		{
			if ($path = \Finder::search('lang/'.$lang, $file, '.php', true))
			{
				foreach ($path as $p)
				{
                    if (array_key_exists($p, static::$_loaded_files))
                    {
                        break;
                    }
					$_messages = \Arr::merge(\Fuel::load($p), $_messages);
				}
                static::$_loaded_files[$p] = true;
				break;
			}
		}

        if (count($_messages)) {
            if ( ! isset(static::$_messages[static::$_locale]))
            {
                static::$_messages[static::$_locale] = array();
            }
            $group = ($group === null) ? $file : $group;
            static::$_group = $group;
            if ( ! isset(static::$_messages[$group]))
            {
                static::$_messages[static::$_locale][$group] = array();
            }
            static::$_messages[static::$_locale][$group] = \Arr::merge($_messages, static::$_messages[static::$_locale][$group]);
        }
	}

	public static function get($_message, $default = null)
	{
        return static::gget(static::$_group, $_message, $default);
	}

    public static function gget($group, $_message, $default = null)
    {
        $result = isset(static::$_messages[static::$_locale][$group][$_message]) ? static::$_messages[static::$_locale][$group][$_message] : $default;
        $result = $result ? : $_message;
        return $result;
    }
}



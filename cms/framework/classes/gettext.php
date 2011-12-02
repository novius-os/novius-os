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

\Autoloader::add_class('Gettext\CachedFileReader', CMSPATH.'vendor'.DS.'gettext'.DS.'streams.php');
\Autoloader::add_class('gettext_reader', CMSPATH.'vendor'.DS.'gettext'.DS.'gettext.php');


class Gettext {
	
	public static $locale;
	public static $readers = array();
	
	public static function _init() {
		list($remaining, $variant) = explode('@', \Fuel::$locale);
		list($remaining, $encoding) = explode('.', $remaining);
		list($language, $country) = explode('_', $remaining);
		static::setlocale($language, array($country));
	}
	
	public static function bindtextdomain($domain, $path) {
		list($ns, $name) = explode('::', $domain);
		if (empty($name)) {
			$name = $ns;
		}
		
		static::$readers[$domain] = $path.'%s'.DS.'LC_MESSAGES'.DS.$name.'.mo';
	}
	
	protected static function get_reader($name, $locale = null) {
		static $readers = array();
		if (empty($locale)) {
			$locale = static::$locale;
		}
		if (!isset($readers[$name][$locale])) {
			list($remaining, $variant) = explode('@', $locale);
			list($remaining, $encoding) = explode('.', $remaining);
			list($language, $country) = explode('_', $remaining);
			$files = array();
			if (!empty($variant)) {
				if (!empty($country)) {
					if (!empty($encoding)) {
						$files[] = sprintf(static::$readers[$name], "{$language}_$country.$encoding@$variant");
					}
					$files[] = sprintf(static::$readers[$name], "{$language}_$country@$variant");
				} else if (!empty($encoding)) {
					$files[] = sprintf(static::$readers[$name], "$language.$encoding@$variant");
				}
				$files[] = sprintf(static::$readers[$name], "$language@$variant");
			}
			if (!empty($country)) {
				if (!empty($encoding)) {
					$files[] = sprintf(static::$readers[$name], "{$language}_$country.$encoding");
				}
				$files[] = sprintf(static::$readers[$name], "{$language}_$country");
			} else if (!empty($encoding)) {
				$files[] = sprintf(static::$readers[$name], "$language.$encoding");
			}
			$files[] = sprintf(static::$readers[$name], "$language");
			
			foreach ($files as $file) {
				if (is_file($file)) {
					$readers[$name][$locale][] = new \gettext_reader(new \Gettext\CachedFileReader($file));
				}
			}
		}
		return $readers[$name][$locale];
	}
	
	public static function d($domain, $str) {
		foreach ((array) static::get_reader($domain) as $reader) {
			$trans = $reader->translate($str);
			if ($trans != $str) {
				break;
			}
		}
		// If translation not found, return the original string
		return $trans ?: $str;
	}
	
	public static function app($str) {
		return static::d('App::local', $str);
	}
	
	public static function cms($str) {
		return static::d('Cms::cms', $str);
	}
	
	public static function setlocale($lang, $locales = null) {
		if (is_null($locales)) {
			$locales = $lang;
		}
		if (!is_array($locales)) {
			$locales = array($locales);
		}
		$args = array(LC_MESSAGES, $lang);
		foreach ($locales as $locale) {
			$try = $lang.'_'.strtoupper($locale);
			$args[] = $try;
			$args[] = $try.'.'.str_replace('-', '', strtolower(\Fuel::$encoding));
		}
		static::$locale = call_user_func_array('\setlocale', $args);		
		return static::$locale;
	}
}


/*
// Native gettext version
class Gettext {
	
	public static function _init() {
		bindtextdomain('local', APPPATH.'gettext/');
		bindtextdomain('cms',   CMSPATH.'gettext/');
	}
	
	public static function local($str) {
		return dgettext('local', $str);
	}
	
	public static function cms($str) {
		return dgettext('cms', $str);
	}
	
	public static function setlocale($lang, $locales = null) {
		if (is_null($locales)) {
			$locales = $lang;
		}
		if (!is_array($locales)) {
			$locales = array($locales);
		}
		$args = array(LC_MESSAGES, $lang);
		foreach ($locales as $locale) {
			$try = $lang.'_'.strtoupper($locale);
			$args[] = $try;
			$args[] = $try.'.'.str_replace('-', '', strtolower(\Fuel::$encoding));
		}
		$return = call_user_func_array('\setlocale', $args);		
		return $return;
	}
}
//*/
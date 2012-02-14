<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */


class Config extends \Fuel\Core\Config {

    public static function getFromUser($item, $default = null) {
        return static::mergeWithUser($item, static::get($item, $default));
    }

    public static function mergeWithUser($item, $config) {
        $user = Session::user();

        Arr::set($config, 'configuration_id', static::getBDDName($item));

        return \Arr::merge($config, \Arr::get($user->getConfiguration(), static::getBDDName($item), array()));
    }

    public static function getBDDName($item) {
        $item = str_replace('::', '/config/', $item);
        $item = str_replace('/', '.', $item);
        return $item;
    }

}

/* End of file config.php */

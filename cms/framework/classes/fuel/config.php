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



        $item = str_replace('::', '/', $item);
        $item = explode('/', $item);

        array_splice($item, 1, 0, 'config');

        $item = implode('.', $item);

        //Arr::set($config, 'configuration_id', $item);

        return \Arr::merge($config, \Arr::get($user->getConfiguration(), $item, array()));
    }

}

/* End of file config.php */

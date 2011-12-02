<?php
/**
 * NOVIUS OS - Web OS for digital communication
 * 
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Novius;

class Config extends \Fuel\Core\Config {

    public static function load($file, $group = null, $reload = false)
    {
        return parent::load($file, $group, $reload);
        print_r(func_get_args());
        print_r($paths = \Fuel::find_file('config', $file, '.php', true));
        $load = parent::load($file, $group, $reload);
        print_r($load);
        exit();
    }

    public static function save($file, $config)
    {
        return parent::save($file, $config);
    }
}

/* End of file config.php */

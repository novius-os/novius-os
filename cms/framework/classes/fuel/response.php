<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

class Response extends \Fuel\Core\Response {

	public static function json($data) {

		static::forge(\Format::forge()->to_json($data), 200, array(
			'Content-Type' => 'application/json',
		))->send(true);
		exit();
	}
}

/* End of file date.php */

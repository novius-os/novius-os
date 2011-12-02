<?php
/**
 * NOVIUS OS - Web OS for digital communication
 * 
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Cms\Orm;

class Query extends \Orm\Query {
	public function alias() {
		return $this->alias;
	}
	public function connection() {
		return $this->connection;
	}
}
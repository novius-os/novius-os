<?php
/**
 * NOVIUS OS - Web OS for digital communication
 * 
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Test;

class Controller_Admin_Wijdialog extends \Cms\Controller_Noviusos_Noviusos
{
	public function action_index()
	{
		$this->template->body = \View::forge('cms_test::wijdialog');
		return $this->template;
	}
	public function action_first()
	{
		$this->template->body = \View::forge('cms_test::wijdialog_first');
		return $this->template;
	}
	public function action_second()
	{
		$this->template->body = \View::forge('cms_test::wijdialog_second');
		return $this->template;
	}
}
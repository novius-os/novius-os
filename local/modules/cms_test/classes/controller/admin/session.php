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

class Controller_Admin_Session extends \Cms\Controller_Noviusos_Noviusos
{
	
	public function action_test_1()
	{
		\Debug::dump(\Session::get());
		\Session::set('test1', 'foo');
		//sleep(3);
		return 'test 1';
	}
	
	public function action_test_2()
	{
		\Session::set('test2', 'foo');
		sleep(3);
		return 'test 2';
	}
	
	public function action_read()
	{
		\Debug::dump(\Session::get());
		return '';
	}
	
	public function action_destroy()
	{
		\Session::destroy();
		return 'destroyed';
	}
}
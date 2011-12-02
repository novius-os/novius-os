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

class Controller_Admin_Upload extends \Cms\Controller
{
	public function action_index()
	{
		return '
			<form method="POST" action="/admin/cms_test/upload/do" enctype="multipart/form-data">
				<input type="file" name="file" />
				<input type="submit" />
			</form>';
	}
	
	public function action_do()
	{
		if (!empty($_FILES['file']))
		{
			$dir = APPPATH.'media/cms_blog';
			$file = $_FILES['file']['name'];
			is_dir($dir) || mkdir($dir, 0755, true);
			move_uploaded_file($_FILES['file']['tmp_name'], $dir.'/'.$file);
			return 'ok ?';
		}
		return 'not ok';
	}
}
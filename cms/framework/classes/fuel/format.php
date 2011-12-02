<?php
/**
 * NOVIUS OS - Web OS for digital communication
 * 
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

class Format extends \Fuel\Core\Format {

	private function json_encode_jsfunc($input = array(), $funcs = array(), $level = 0)
	{
		foreach($input as $key => $value)
		{
			if (is_array($value))
			{
				$ret = $this->json_encode_jsfunc($value, $funcs, 1);
				$input[$key]=$ret[0];
				$funcs=$ret[1];
			}
			else
			{
				if (substr($value,0,9) == 'function(')
				{
					$func_key="#".uniqid()."#";
					$funcs[$func_key]=$value;
					$input[$key]=$func_key;
				}
			}
		}
		if ($level == 1)
		{
			return array($input, $funcs);
		}
		else
		{
			$input_json = json_encode($input);
			foreach($funcs as $key=>$value)
			{
				$input_json = str_replace('"'.$key.'"', $value, $input_json);
			}
			return $input_json;
		}
	}

	public function to_json($data = null)
	{
		if ($data == null)
		{
			$data = $this->_data;
		}
		
		if ($data == null)
		{
			return json_encode($data);
		}

		// To allow exporting ArrayAccess objects like Orm\Model instances they need to be
		// converted to an array first
		$data = (is_array($data) or is_object($data)) ? $this->to_array($data) : $data;
		return $this->json_encode_jsfunc($data);
	}
}

/* End of file format.php */

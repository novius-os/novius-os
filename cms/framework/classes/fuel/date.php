<?php
/**
 * NOVIUS OS - Web OS for digital communication
 * 
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

class Date extends \Fuel\Core\Date {

	/**
	 * Compare two dates without regard to hours.
	 *
	 * @param   int|Date    first date to compare.
	 * @param   int|Date    seconda date to compare.
	 * @return  int			-1 If the first date is earlier, 1 if it's after, 0 if both dates are equal
	 */
	public static function compare($date1, $date2)
	{
		$date1 = ( ! $date1 instanceof Date) ? static::forge($date1) : $date1;
		$date2 = ( ! $date2 instanceof Date) ? static::forge($date2) : $date2;

		$date1 = $date1->format('%Y-%m-%d');
		$date2 = $date2->format('%Y-%m-%d');
		if ($date1 < $date2) {
			return -1;
		}
		elseif ($date1 > $date2)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}


	/**
	 * Modify the timestamp of the date, by incrementing or decrementing in a format accepted by strtotime().
	 *
	 * @param   string   a date/time string. Valid formats are explained in PHP Manual chapter's "Date and Time Formats".
	 * @return  Date
	 */
	public function modify($modify)
	{
		// Temporarily change timezone when different from default
		if (\Fuel::$timezone != $this->timezone)
		{
			date_default_timezone_set($this->timezone);
		}

		// Modify the timestamp
		$timestamp = strtotime($modify, $this->timestamp);

		if ($timestamp === false)
		{
			\Error::notice('Invalid input for modify given.');
			return false;
		}
		else
		{
			$this->timestamp = $timestamp;
		}

		// Change timezone back to default if changed previously
		if (\Fuel::$timezone != $this->timezone)
		{
			date_default_timezone_set(\Fuel::$timezone);
		}

		return $this;
	}
}

/* End of file date.php */

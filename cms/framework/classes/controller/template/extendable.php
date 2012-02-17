<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Cms;

/**
 * Template Controller class
 *
 * A base controller for easily creating templated output.
 *
 * @package   Fuel
 * @category  Core
 * @author    Fuel Development Team
 */
abstract class Controller_Template_Extendable extends Controller_Extendable
{

	/**
	* @var string page template
	*/
	public $template = 'template';

	/**
	 * Load the template and create the $this->template object
	 */
	public function before()
	{
		if ( ! empty($this->template) and is_string($this->template))
		{
			// Load the template
			$this->template = \View::forge($this->template);
		}

		return parent::before();
	}

	/**
	 * After controller method has run output the template
	 *
	 * @param  Response  $response
	 */
	public function after($response)
	{
		// If nothing was returned default to the template
		if (empty($response))
		{
			$response = $this->template;
		}

		// If the response isn't a Response object, embed in the available one for BC
		// @deprecated  can be removed when $this->response is removed
		if ( ! $response instanceof Response)
		{
			$this->response->body = $response;
			$response = $this->response;
		}

		return parent::after($response);
	}

}

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

class Controller_Admin_Gettext extends \Cms\Controller_Admin_Noviusos {

	public function action_index() {

		// Use the appropriate locale
		//\Debug::dump(\Cms\Gettext::setlocale('fr', array('FR')));

		// Set the domains
		\Cms\Gettext::bindtextdomain('Cms::cms', CMSPATH.'gettext'.DS);
		\Cms\Gettext::bindtextdomain('App::local', APPPATH.'gettext'.DS);

		// Translate some strings
		\Debug::dump(array(
			'local' => \Cms\Gettext::d('App::local', 'Website'),
			'cms'   => \Cms\Gettext::d('Cms::cms', 'User'),
			'fr'   => \Cms\Gettext::d('Cms::cms', 'fr'),
			'fr_FR'   => \Cms\Gettext::d('Cms::cms', 'fr_FR'),
			'fr_CA'   => \Cms\Gettext::d('Cms::cms', 'fr_CA'),
			'fr_FR_fr'   => \Cms\Gettext::d('Cms::cms', 'fr_FR_fr'),
			'fr_CA_fr'   => \Cms\Gettext::d('Cms::cms', 'fr_CA_fr'),
		));
		return '';
	}
}
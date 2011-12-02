
*NOVIUS OS - Web OS for digital communication*

*@copyright  2011 Novius<br />
@license    GNU Affero General Public License v3 or (at your option) any later version
            http://www.gnu.org/licenses/agpl-3.0.html<br />
@link http://www.novius-os.org*


Gettext
=======

*[Back to index](/admin/doc)*

Caching problem
---------------

* Gettext caches the message files in memory
* It doesn't seem to have an expiration
* We cannot flush gettext cache (invalidation)

### Implications

* New files are properly loaded
* Updated files are loaded sometimes (when the process doesn't know them already)


Locales
-------

* Full qualified locales (fr_FR, en_GB) contains the string to be translated
* Simple locales are also used as fallback (fr, en)

Not be the best solution?
-------------------------

From this [topic on Stack Overflow](http://stackoverflow.com/questions/7931021/gettext-caching-annoyance/7940710#7940710):

Gettext was originally developed for desktop apps, which you wouldn't expect to change while running it.

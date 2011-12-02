
*NOVIUS OS - Web OS for digital communication*

*@copyright  2011 Novius<br />
@license    GNU Affero General Public License v3 or (at your option) any later version
            http://www.gnu.org/licenses/agpl-3.0.html<br />
@link http://www.novius-os.org*


Configuration
==============

Ecriture des fichiers de config initiaux
----------------------------------------

* db.php
* crypt.php
* Remettre le dossier local/config en read-only


Installation
============


Médiathèque : X-Sendfile
------------------------

* Test réel avec image htdocs/xsendfile (http://cloud.julian.lyon.novius.fr/htdocs/cms/xsendfile.php)
* Apache : détection de l'installation de mod_xsendfile
* Apache : explication de l'activation avec `XSendFile On` et `XSendFileAllowAbove on`

Médiathèque : Librairie convert
-------------------------------

* Test de redimensionnement d'une image avec convert

Modules : Librairie zip
-------------------------------

* Librarie Zip nécessaire pour installer un module :
 * Classe `ZipArchive` pour lire le contenu (fichier de config / metadata + répertoire racine)
 * Protocole `zip://` pour l'extraction des fichiers dans `~/local/modules`


Gettext
=======

* Vérifier la présence de la / des locales appropriées

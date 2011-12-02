
*NOVIUS OS - Web OS for digital communication*

*@copyright  2011 Novius<br />
@license    GNU Affero General Public License v3 or (at your option) any later version
            http://www.gnu.org/licenses/agpl-3.0.html<br />
@link http://www.novius-os.org*


<style type="text/css">
code {
    white-space: pre;
}
</style>

Folder organization
===================

*[Back to index](/admin/doc)*


Root folders
------------

* `~/cms/` : internal files of the framework
* `~/local/` : your website
* `~/public/` : document root


Cms folder organization
-----------------------

* `~/cms/framework/` : the Novius OS framework
* `~/cms/fuel-core/` : FuelPHP, the low-level framework used by Novius OS
* `~/cms/packages/` : FuelPHP packages


Application folder organization
-----------------------------------

* `~/local/classes/` : Controllers and models from your app
* `~/local/config/` : Configuration files
* `~/local/media/` : [Media library](/admin/doc/media_library) files
* `~/local/modules/` : Novius OS modules
* `~/local/gettext/` : i18n files
* `~/local/views/` : Views and front-end templates

Note: the `classes` and `views` folders should not contain a lot of files, because most of your application should consists of modules.


Public folder organization
--------------------------

A file can either be:

1. executable or non-executable
2. provided by the developer or provided by the application

This leads to 4 possibles usage, and each of them is reflected in the public folder:

* `~/public/static/` : equivalent to assets. Non-executable files provided by the developer.
* `~/public/data/` : Non-executable files created by the application.
* `~/public/htdocs/` : Executable files provided by the developer.
* `~/public/cache/` : Executable files created by the application.

Where the application can write the developer cannot, and vice-versa.

Each of these 4 folders has the same sub-directories structure :

* `~/cms/` : Novius OS files
* `~/modules/<module_name>/` : module files

There is also an additional `~/public/media/` directory which is used by the [media library](/admin/doc/media_library).




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


Differences with FuelPHP
========================

*[Back to index](/admin/doc)*

Paths constants
---------------

* `APPPATH` still links to `~/local/`
* `PKGPATH` still links to `~/cms/packages/`
* `COREPATH` still links to `~/cms/fue-core/`
* Novius OS introduces a new constant`CMSPATH`, which links to `~/cms/`

Autoloader
----------

Two additionnal namespaces are registered by Novius OS:

* `cms` links to `CMSPATH`
* `app` links to `APPPATH`

Bootstrap & entry points
------------------------

In a Novius OS application / website, the front-office and the back-office are two separated areas.

Thus, Novius OS can be used to run a back-end application without a front-office.

Although the front-office is optional, Novius OS provides a Content Management System for that.

If you use it, you can't route an URL from the front-office to a controller. Everything is handeld by Novius OS.

So instead of a unique `index.php` entry point from FuelPHP, we have two entry points:

* `~/cms/htdocs/admin.php`: back-office entry point. Matches every URL starting with `/admin/`
* `~/cms/htdocs/front.php`: front-office entry point. Matches every URL ending with `.html`


### Admin entry point

Every URL from the backend starts with `/admin/`.

Novius OS uses his special admin controller to dispatch them, as follow:

1. The `/admin/` prefix is removed
2. Novius OS tries to route the URL to a module
3. If it fails, Novius OS tries to route the URL to an internal action

#### Routing example

We'll use the URL `/admin/user/form/add` for the explanations.

The dispatcher try the following:

1. Use the `admin/form/add` action of the `user` module
2. If that fails try the internal, `user/form/add` action
3. If that fails, show the 404 page



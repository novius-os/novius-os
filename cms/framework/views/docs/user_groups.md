
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

 Documentation - Form generator
================================

*[Back to index](/admin/doc)*

**2 mechanism:**

1. Without user groups
1. With user groups

 1. Permission on users
------------------------

* **Default**
* Simplest way of operate

Principles:

* The permissions are set independently for each user
* There is no way to share a set of permissions between them

Internally:

* The permissions are attached to an unique hidden group for each user.


 2. Permission on groups
-------------------------

* **Optional**
* More complex/powerful way of operate

Principles:

* Every permission are attached to groups instead of users
* Users are assigned to one or several groups
* Groups share a set of permissions between every user it contains

### How to set permission on users individually?

* A dedicated group containing only one user can be created

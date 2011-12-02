
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

Media library
=============

*[Back to index](/admin/doc)*

General informations
--------------------

The media library is the place where all the files managed by the application will be stored. It can contains images, documents, videos and any other file.

* All the files are stored in the APPPATH/media directory (non public)
* They are accessed using http://your.website.com/**media**/folder/ressource.ext

When a user want to access a media for the first time, the 404 handler is invoked.

* If the media is **public**: a symbolic link is created in public/media, so subsequent requests don't need to use the 404 handler anymore
* If the media is **private**, either an http 401 error (authorization required) is returned or the file is sent on the output (no symbolic link is created, the access still has to be checked on every request)

How is the file sent on the output?
-----------------------------------

`X-Sendfile` will be used if available or configured in the application.

The file is not sent from the PHP script with `readfile()`, we delegate this work to the web server.
Instead of sending the file, the script returns a special header:


* `X-Sendfile` is appropriate for **Apache** and others
* `X-Accel-Redirect` is appropriate for **nginx**

This special header is caught by the web server which will process it accordingly.

It allows the PHP process to be freed and become available again instantly.


### Configuring X-Sendfile on **Apache**
`mod_xsendfile` needs to be installed and properly configured:

```
[httpd.conf or .htaccess]
XSendFile On
XSendFileAllowAbove on
```

### Configuring X-Accel-Redirect for **nginx**

Nothing to do: nginx understands it natively.

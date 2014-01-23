<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

$document_root = rtrim(realpath($_SERVER['DOCUMENT_ROOT']), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
$nos_dir = trim(substr(realpath($_SERVER['SCRIPT_FILENAME']), strlen($document_root), -12), DIRECTORY_SEPARATOR);
if (!empty($nos_dir)) {
    $nos_dir .= DIRECTORY_SEPARATOR;
}

$htaccess = '';
$htaccess_save = false;
$htaccess_move = false;

$public = $document_root.$nos_dir.'public'.DIRECTORY_SEPARATOR;
$file = $public.'.htaccess';
$htaccess_move = !file_exists($file);
if (!$htaccess_move && is_writable($file) && is_writable($public)) {
    $htaccess_move = rename($file, $public.'.htaccess.old');
}

// Don't create the new .htaccess file if the rename failed
if ($htaccess_move) {
    $file = $document_root.$nos_dir.'.htaccess.shared-hosting';
    $handle = fopen($file, 'r');
    if ($handle) {
        $content = fread($handle, filesize($file));
        fclose($handle);

        $htaccess = str_replace('novius-os-install-dir/', str_replace('\\', '/', $nos_dir), $content);

        if (file_exists($document_root.$nos_dir.'.htaccess')) {
            $file = $document_root.$nos_dir.'.htaccess';
            $handle = fopen($file, 'r');
            if ($handle) {
                $content = fread($handle, filesize($file));
                fclose($handle);

                $htaccess_save = trim($htaccess) == trim($content);
            }
        } else {
            $file = $document_root.$nos_dir.'.htaccess';
            if (is_writable($document_root.$nos_dir)) {
                $handle = fopen($file, 'w');
                if ($handle) {
                    $htaccess_save = fwrite($handle, $htaccess);
                    fclose($handle);
                }
            }
        }
    }
}

if ($htaccess_save && $htaccess_move) {
    // Just emulate what rewrite_module would do.
    // The installation includes a test which requires it to be enabled to continue.
    $base_url = 'public/htdocs/';

    include 'public/htdocs/install.php';
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Novius OS - Installation</title>
    <meta name="robots" content="noindex,nofollow">

    <style type="text/css">
        html {
            height : 100%;
        }
        body {
            /* On step 1, this asset will probably return an HTTP status 404 Not Found */
            background: #ddd;
            background-size: cover;
            font-family: franklin gothic medium,arial,verdana,helvetica,sans-serif;
        }
        #blank_slate {
            background: rgba(255, 255, 255, 0.5);
            border: 1px outset rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            padding: 20px 40px;
            position: absolute;
            top: 50px;
            left: 50px;
            right: 50px;
            bottom: 50px;
            overflow: auto;
        }
        #blank_slate h1, #blank_slate img {
            vertical-align: middle;
            padding: 0 2em 0 1em;
        }

        table {
            border-collapse: collapse;
            margin: 1em auto;
        }
        th {
            text-align: left;
        }
        td, th {
            border: 1px solid #bbb;
            padding: 5px;
        }
        td.status {
            background-color: #fff;
            font-weight: bold;
            text-align: center;
        }
        tr.error td.status {
            color: #f00;
        }
        tr.ok td.status {
            color: #0b0;
        }
        table tr.error {
            background: #fff5f5;
        }
        tr.error th {
            border-bottom: none;
        }
        tr.separator td {
            border:none;
        }
        tr.error td.description {
            border-top: none;
        }
        table tr.ok {
            background: #f5fff5;
        }
        code {
            background-color: #fff;
            border: 1px dashed #bbb;
            display:inline-block;
            padding: 2px;
        }
        p.description {
            font-style: italic;
            font-size: smaller;
        }


        input, button {
            background:#fff;
            -moz-box-shadow:inset 0 2px 2px rgba(143,143,143,0.50);
            -webkit-box-shadow:inset 0 2px 2px rgba(143,143,143,0.50);
            box-shadow:inset 0 2px 2px rgba(143,143,143,0.50);
            padding: 5px;
            -moz-border-radius: 3px;
            -webkit-border-radius: 3px;
            border-radius: 3px;
            border: 1px solid #a8a8a8;
            font-weight: bold;
            font-size: 1.1em;
            color: #4f4f4f;
            text-shadow: 0px 1px 0px rgba(255,255,255,0.7);
        }

        input:focus, input:active {
            border: solid 1px #8ab0c6;
            outline: none;
            -moz-box-shadow:0 0 5px #85b2cb, inset 0 2px 2px #8f8f8f;
            -webkit-box-shadow:0 0 5px #85b2cb, inset 0 2px 2px #8f8f8f;
            box-shadow:0 0 5px #85b2cb, inset 0 2px 2px #8f8f8f;
        }
        input[type=submit], button {
            padding: .4em 1em;
            cursor: pointer;
            color: #313131;
            border: 1px solid #a8a8a8;
            -moz-box-shadow: 0 0 3px #85b2cb;
            -webkit-box-shadow: 0px 0px 3px #85b2cb;
            box-shadow: 0px 0px 3px #85b2cb;
            background: #c4c4c4 linear-gradient(top, rgba(255,255,255,0.8), rgba(255,255,255,0));
            background: #c4c4c4 -webkit-gradient(linear, left top, left bottom, from(rgba(255,255,255,0.8)), to(rgba(255,255,255,0)));
            background: #c4c4c4 -moz-linear-gradient(top, rgba(255,255,255,0.8), rgba(255,255,255,0));
        }
        input[type=submit]:hover, button:hover {
            border: solid 1px #8ab0c6;
            background: #85b2cb linear-gradient(top, rgba(255,255,255,0.6), rgba(255,255,255,0));
            background: #85b2cb -webkit-gradient(linear, left top, left bottom, from(rgba(255,255,255,0.6)), to(rgba(255,255,255,0)));
            background: #85b2cb -moz-linear-gradient(top, rgba(255,255,255,0.6), rgba(255,255,255,0));
        }
        input[type=submit]:active, button:active {
            border: solid 1px #8ab0c6;
            background: #85b2cb linear-gradient(bottom, rgba(255,255,255,0.6), rgba(255,255,255,0));
            background: #85b2cb -webkit-gradient(linear, left bottom, left top, from(rgba(255,255,255,0.6)), to(rgba(255,255,255,0)));
            background: #85b2cb -moz-linear-gradient(bottom, rgba(255,255,255,0.6), rgba(255,255,255,0));
        }
    </style>
</head>

<body>
<div id="blank_slate">
    <h1>Novius OS Shared Hosting</h1>
    <?php
    if ($htaccess_move && !$htaccess_save) {
        echo '<h2>Create a .htaccess file in the Novius OS\'s directory</h2>';

        echo '<p>Create a file name <code>.htaccess</code> and write this code in it :</p>';
        echo '<pre><code style="width:800px;">', htmlspecialchars($htaccess), '</code></pre>';
        echo '<p>Upload this file in the Novius OS\'s directory of your hosting server (<code>', $nos_dir, '</code>, beside CHANGELOG.md file).</p>';
    }

    if (!$htaccess_move) {

        echo '<h2>Rename invalid .htaccess file in the Novius OS\'s public directory</h2>';
        echo '<p>Rename <code>', 'public'.DIRECTORY_SEPARATOR.'.htaccess</code> file by <code>', 'public'.DIRECTORY_SEPARATOR.'.htaccess.old</code>.</p>';
    }
    ?>
    <p><a href="install.php"><button>It's done, refresh this page</button></a></p>
</div>
</body>
</html>

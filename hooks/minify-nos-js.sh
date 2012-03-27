#!/bin/bash

JAVA_CMD='java'
export JAVA_CMD

# directories of the JS and CSS files
CUR_DIR=`pwd`
JS_DIR="$CUR_DIR/../cms/static/admin/novius-os/js"
JS_MINIFY_DIR="$CUR_DIR/../cms/static/admin/novius-os/js/minified"

#these are the paths to the final combined files that you want to have in the end
JS_COMBINED_FILE="$JS_DIR/novius-os.min.js"

# These files are your CSS and JS files you want to combine together
JS_FILES=( jquery.novius-os jquery.novius-os.loadspinner jquery.novius-os.ostabs jquery.novius-os.preview jquery.novius-os.listgrid jquery.novius-os.treegrid jquery.novius-os.thumbnailsgrid jquery.novius-os.appdesk )

#clear the files
> $JS_COMBINED_FILE

echo '/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */' >> $JS_COMBINED_FILE
#run thru the JS files
for F in ${JS_FILES[@]}; do
  yui-compressor -o "$JS_DIR/$F.min.js" "$JS_DIR/$F.js"
  cat "$JS_DIR/$F.min.js" >> $JS_COMBINED_FILE
  echo '' >> $JS_COMBINED_FILE
  rm "$JS_DIR/$F.min.js"
done

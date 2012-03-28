#!/bin/bash

#kill compass watch

COMPASS_WATCH=`ps -ef | grep compass | grep watch | awk '{ print $2 }'`
kill -STOP $COMPASS_WATCH

JAVA_CMD='java'
export JAVA_CMD

# directories of the JS and CSS files
CUR_DIR=`pwd`
CSS_DIR="$CUR_DIR/../cms/static/admin/novius-os/css"

cd $CSS_DIR

#these are the paths to the final combined files that you want to have in the end
CSS_COMBINED_FILE="$CSS_DIR/novius-os.min.css"

# These files are your CSS and JS files you want to combine together
CSS_FILES=( laGrid novius-os jquery.novius-os.appdesk jquery.novius-os.listgrid jquery.novius-os.ostabs jquery.novius-os.treegrid jquery.novius-os.preview jquery.novius-os.thumbnailsgrid )

#clear the files
> $CSS_COMBINED_FILE

#run thru the CSS files
for F in ${CSS_FILES[@]}; do
  if [ -e "$F.scss" ]
  then
    touch "$F.scss"
  fi
done
compass compile -s compressed

echo '/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */' >> $CSS_COMBINED_FILE
for F in ${CSS_FILES[@]}; do
  if [ $F = laGrid ]
  then
    yui-compressor -o "$CSS_DIR/$F.min.css" "$CSS_DIR/$F.css"
    cat "$CSS_DIR/$F.min.css" >> $CSS_COMBINED_FILE
    echo '' >> $CSS_COMBINED_FILE
  else
    cat "$CSS_DIR/$F.css" >> $CSS_COMBINED_FILE
  fi
done

#add the file to the git base
#git add $CSS_COMBINED_FILE
#git add $JS_COMBINED_FILE

kill -CONT $COMPASS_WATCH
cd public/htdocs
ln -s ../../cms/htdocs cms
cd ../
cd static
ln -s ../../cms/static cms
cd ../../

chmod 777 local/config

mkdir local/data
mkdir local/data/config
echo "<?php

return array();" > local/data/config/app_installed.php 


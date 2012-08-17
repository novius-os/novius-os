<?php

namespace Fuel\Migrations;

class Remove_page_root
{
    public function up()
    {
        \DB::query("ALTER TABLE `nos_media_folder` ADD `medif_dir_name` VARCHAR( 50 ) NULL AFTER `medif_path`;")->execute();
        \DB::query("UPDATE `nos_media_folder` SET `medif_dir_name` = REPLACE(SUBSTRING_INDEX(`medif_path`, '/', -2), '/', '');")->execute();
        \DB::query("UPDATE `nos_media` SET `media_file` = REPLACE(`media_file`, CONCAT('.', `media_ext`), '');")->execute();
        \DB::query("UPDATE `nos_media` SET `media_path` = CONCAT(`media_path`, `media_file`, '.', `media_ext`);")->execute();
   }

    public function down()
    {

    }
}
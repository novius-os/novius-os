<?php

namespace Fuel\Migrations;

class Created_and_updated_at
{
    public function up()
    {
        \DB::query("ALTER TABLE `nos_blog_category` ADD `cat_created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
          ADD `cat_updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00';")->execute();

        \DB::query("ALTER TABLE `nos_news_category` ADD `cat_created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
          ADD `cat_updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00';")->execute();

        \DB::query("ALTER TABLE `nos_page` CHANGE `page_created_at` `page_created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
          CHANGE `page_updated_at` `page_updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00';")->execute();

        \DB::query("ALTER TABLE `nos_user` ADD `user_created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
          ADD `user_updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00';")->execute();

        \DB::query("ALTER TABLE `nos_media` ADD `media_created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
          ADD `media_updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00';")->execute();

        \DB::query("ALTER TABLE `nos_media_folder` ADD `medif_created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
          ADD `medif_updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00';")->execute();
    }
    public function down()
    {

    }
}
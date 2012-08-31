<?php

namespace Fuel\Migrations;

class Content_nuggets
{
    public function up()
    {
        \DB::query("CREATE TABLE IF NOT EXISTS `nos_content_nuggets` (
          `content_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `content_catcher` varchar(25) DEFAULT NULL,
          `content_model_name` varchar(100) DEFAULT NULL,
          `content_model_id` int(10) unsigned DEFAULT NULL,
          `content_data` text,
          PRIMARY KEY (`content_id`),
          UNIQUE KEY `catcher_model` (`content_catcher`,`content_model_name`,`content_model_id`)
        ) DEFAULT CHARSET=utf8;")->execute();
    }
    public function down()
    {

    }
}

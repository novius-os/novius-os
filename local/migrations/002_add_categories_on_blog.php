<?php

namespace Fuel\Migrations;

class Add_categories_on_blog
{
    public function up()
    {
        \DB::query("DROP TABLE IF EXISTS `nos_blog_category`;")->execute();
        \DB::query("DROP TABLE IF EXISTS `nos_blog_category_post`;")->execute();
        \DB::query("DROP TABLE IF EXISTS `nos_blog_category_link`;")->execute();

        \DB::query("CREATE TABLE `nos_blog_category` (
  `blgc_id` int(11) NOT NULL AUTO_INCREMENT,
  `blgc_parent_id` int(11) DEFAULT NULL,
  `blgc_title` varchar(255) NOT NULL DEFAULT '',
  `blgc_lang_id` varchar(5) NOT NULL,
  `blgc_single_id` int(10) NOT NULL,
  `blgc_common_id` int(10) NOT NULL,
  `blgc_virtual_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`blgc_id`),
  UNIQUE KEY `blgc_virtual_name` (`blgc_virtual_name`),
  KEY `blgc_parent_id` (`blgc_parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;")->execute();

        \DB::query("CREATE TABLE `nos_blog_category_post` (
  `blog_id` int(11) NOT NULL DEFAULT '0',
  `blgc_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`blog_id`,`blgc_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;")->execute();


    }

    public function down()
    {

    }
}
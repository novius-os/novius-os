<?php

namespace Fuel\Migrations;

class Blog_news
{
    public function up()
    {
        \DB::query("DROP TABLE IF EXISTS `nos_blog_category`;")->execute();
        \DB::query("DROP TABLE IF EXISTS `nos_blog_category_post`;")->execute();

        \DB::query("CREATE TABLE IF NOT EXISTS `nos_blog_category` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_title` varchar(255) NOT NULL,
  `cat_virtual_name` varchar(255) NOT NULL,
  `cat_lang` varchar(5) NOT NULL,
  `cat_lang_common_id` int(11) NOT NULL,
  `cat_lang_single_id` int(11) DEFAULT NULL,
  `cat_parent_id` int(11) DEFAULT NULL,
  `cat_sort` float DEFAULT NULL,
  PRIMARY KEY (`cat_id`),
  KEY `cat_lang` (`cat_lang`,`cat_lang_common_id`,`cat_lang_single_id`)
) DEFAULT CHARSET=utf8 ;")->execute();

        \DB::query("CREATE TABLE IF NOT EXISTS `nos_blog_category_post` (
  `post_id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  PRIMARY KEY (`post_id`, `cat_id`)
) DEFAULT CHARSET=utf8;")->execute();

        \DB::query("CREATE TABLE IF NOT EXISTS `nos_blog_post` (
  `post_id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `post_title` varchar(250) NOT NULL,
  `post_summary` text NOT NULL,
  `post_author_alias` varchar(255) DEFAULT NULL,
  `post_author_id` int(10) unsigned DEFAULT NULL,
  `post_created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `post_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_lang` varchar(5) NOT NULL,
  `post_lang_common_id` int(11) NOT NULL,
  `post_lang_single_id` int(11) DEFAULT NULL,
  `post_published` tinyint(1) NOT NULL,
  `post_read` int(10) unsigned NOT NULL,
  `post_virtual_name` varchar(255) NOT NULL,
  PRIMARY KEY (`post_id`),
  UNIQUE KEY `news_virtual_name` (`post_virtual_name`),
  KEY `news_lang` (`post_lang`,`post_lang_common_id`,`post_lang_single_id`)
) DEFAULT CHARSET=utf8 ;")->execute();

        \DB::query("CREATE TABLE IF NOT EXISTS `nos_blog_tag` (
  `tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag_label` varchar(255) NOT NULL,
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `tag_label` (`tag_label`)
) DEFAULT CHARSET=utf8 ;")->execute();

        \DB::query("RENAME TABLE `nos_blog_tag` TO `nos_blog_tag_post` ;")->execute();

        \DB::query("ALTER TABLE `nos_blog_tag_post` CHANGE `blgt_blog_id` `post_id` INT( 10 ) UNSIGNED NOT NULL ,
CHANGE `blgt_tag_id` `tag_id` INT( 10 ) UNSIGNED NOT NULL;")->execute();

        \DB::query("ALTER TABLE `nos_blog_tag_post` DROP INDEX `blgt_blog_id`;")->execute();
        \DB::query("ALTER TABLE `nos_blog_tag_post` ADD PRIMARY KEY ( `post_id` , `tag_id` );")->execute();
        \DB::query("ALTER TABLE `nos_blog_tag_post` ADD INDEX ( `post_id` );")->execute();
        \DB::query("ALTER TABLE `nos_blog_tag_post` ADD INDEX ( `tag_id` );")->execute();

        \DB::query("RENAME TABLE `nos_tag` TO `nos_blog_tag` ;")->execute();

        \DB::query("INSERT INTO `nos_blog_post` (`post_id`, `post_title`, `post_summary`, `post_author_alias`, `post_author_id`, `post_created_at`, `post_updated_at`, `post_lang`, `post_lang_common_id`, `post_lang_single_id`, `post_published`, `post_read`, `post_virtual_name`)
SELECT `blog_id`, `blog_title`, `blog_summary`, `blog_author`, `blog_author_id`, `blog_created_at`, `blog_created_at`, `blog_lang`, `blog_lang_common_id`, `blog_lang_single_id`, `blog_published`, `blog_read`, `blog_virtual_name` FROM `nos_blog`;")->execute();

        \DB::query("DROP TABLE IF EXISTS `nos_blog`;")->execute();


        \DB::query("CREATE TABLE IF NOT EXISTS `nos_news_category` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_title` varchar(255) NOT NULL,
  `cat_virtual_name` varchar(255) NOT NULL,
  `cat_lang` varchar(5) NOT NULL,
  `cat_lang_common_id` int(11) NOT NULL,
  `cat_lang_single_id` int(11) DEFAULT NULL,
  `cat_parent_id` int(11) DEFAULT NULL,
  `cat_sort` float DEFAULT NULL,
  PRIMARY KEY (`cat_id`),
  KEY `cat_lang` (`cat_lang`,`cat_lang_common_id`,`cat_lang_single_id`)
) DEFAULT CHARSET=utf8;")->execute();

        \DB::query("CREATE TABLE IF NOT EXISTS `nos_news_category_post` (
  `post_id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  PRIMARY KEY (`post_id`, `cat_id`)
) DEFAULT CHARSET=utf8;")->execute();

        \DB::query("CREATE TABLE IF NOT EXISTS `nos_news_post` (
  `post_id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `post_title` varchar(250) NOT NULL,
  `post_summary` text NOT NULL,
  `post_author_alias` varchar(255) DEFAULT NULL,
  `post_author_id` int(10) unsigned DEFAULT NULL,
  `post_created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `post_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_lang` varchar(5) NOT NULL,
  `post_lang_common_id` int(11) NOT NULL,
  `post_lang_single_id` int(11) DEFAULT NULL,
  `post_published` tinyint(1) NOT NULL,
  `post_read` int(10) unsigned NOT NULL,
  `post_virtual_name` varchar(255) NOT NULL,
  PRIMARY KEY (`post_id`),
  UNIQUE KEY `news_virtual_name` (`post_virtual_name`),
  KEY `news_lang` (`post_lang`,`post_lang_common_id`,`post_lang_single_id`)
) DEFAULT CHARSET=utf8;")->execute();

        \DB::query("CREATE TABLE IF NOT EXISTS `nos_news_tag` (
  `tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag_label` varchar(255) NOT NULL,
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `tag_label` (`tag_label`)
) DEFAULT CHARSET=utf8;")->execute();

        \DB::query("CREATE TABLE IF NOT EXISTS `nos_news_tag_post` (
  `post_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`tag_id`, `post_id`),
  KEY `tag_id` (`tag_id`),
  KEY `post_id` (`post_id`)
) DEFAULT CHARSET=utf8;")->execute();

        \DB::query("ALTER TABLE `nos_comment` CHANGE `comm_type` `comm_from_table` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
CHANGE `comm_parent_id` `comm_foreign_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';")->execute();

        \DB::query("ALTER TABLE `nos_comment`
  DROP `comm_parent_title`,
  DROP `comm_parent_url`,
  DROP `comm_blacklist`;")->execute();

        \DB::query("ALTER TABLE `nos_comment` DROP INDEX `comm_type` ,
ADD INDEX `comm_from_table` ( `comm_from_table` , `comm_foreign_id` );")->execute();

        \DB::query("ALTER TABLE `nos_comment` DROP INDEX `comm_etat`;")->execute();
        \DB::query("ALTER TABLE `nos_comment` ADD INDEX ( `comm_created_at` );")->execute();

        \DB::query("UPDATE `nos_comment` SET `comm_from_table` = 'nos_blog_post' WHERE `comm_from_table` = 'blog';")->execute();
    }

    public function down()
    {

    }
}
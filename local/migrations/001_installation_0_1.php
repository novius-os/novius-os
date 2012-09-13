<?php

namespace Fuel\Migrations;

class Installation_0_1
{
    public function up()
    {
        //Table structure for table `nos_blog_category`
        \DB::query("CREATE TABLE IF NOT EXISTS `nos_blog_category` (
          `cat_id` int(11) NOT NULL AUTO_INCREMENT,
          `cat_title` varchar(255) NOT NULL,
          `cat_virtual_name` varchar(255) NOT NULL,
          `cat_lang` varchar(5) NOT NULL,
          `cat_lang_common_id` int(11) NOT NULL,
          `cat_lang_is_main` tinyint(1) NOT NULL DEFAULT '0',
          `cat_parent_id` int(11) DEFAULT NULL,
          `cat_sort` float DEFAULT NULL,
          `cat_created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
          `cat_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`cat_id`),
          KEY `cat_lang` (`cat_lang`),
          KEY `cat_lang_common_id` (`cat_lang_common_id`, `cat_lang_is_main`),
          KEY `cat_lang_is_main` (`cat_lang_is_main`),
          KEY `cat_virtual_name` (`cat_virtual_name`),
          KEY `cat_parent_id` (`cat_parent_id`)
        ) DEFAULT CHARSET=utf8;")->execute();

        //Table structure for table `nos_blog_category_post`
        \DB::query("CREATE TABLE IF NOT EXISTS `nos_blog_category_post` (
          `post_id` int(11) NOT NULL,
          `cat_id` int(11) NOT NULL,
          PRIMARY KEY (`post_id`,`cat_id`)
        ) DEFAULT CHARSET=utf8;")->execute();

        //Table structure for table `nos_blog_post`
        \DB::query("CREATE TABLE IF NOT EXISTS `nos_blog_post` (
          `post_id` int(8) unsigned NOT NULL AUTO_INCREMENT,
          `post_title` varchar(250) NOT NULL,
          `post_summary` text NOT NULL,
          `post_author_alias` varchar(255) DEFAULT NULL,
          `post_author_id` int(10) unsigned DEFAULT NULL,
          `post_created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
          `post_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
          `post_lang` varchar(5) NOT NULL,
          `post_lang_common_id` int(11) NOT NULL,
          `post_lang_is_main` tinyint(1) NOT NULL DEFAULT '0',
          `post_published` tinyint(1) NOT NULL,
          `post_read` int(10) unsigned NOT NULL,
          `post_virtual_name` varchar(255) NOT NULL,
          PRIMARY KEY (`post_id`),
          KEY `post_lang` (`post_lang`),
          KEY `post_lang_common_id` (`post_lang_common_id`, `post_lang_is_main`),
          KEY `post_lang_is_main` (`post_lang_is_main`),
          KEY `post_virtual_name` (`post_virtual_name`),
          KEY `post_author_id` (`post_author_id`)
        )  DEFAULT CHARSET=utf8;")->execute();

        //Table structure for table `nos_blog_tag`
        \DB::query("CREATE TABLE IF NOT EXISTS `nos_blog_tag` (
          `tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `tag_label` varchar(255) NOT NULL,
          PRIMARY KEY (`tag_id`),
          UNIQUE KEY `tag_label` (`tag_label`)
        )  DEFAULT CHARSET=utf8;")->execute();

        //Table structure for table `nos_blog_tag_post`
        \DB::query("CREATE TABLE IF NOT EXISTS `nos_blog_tag_post` (
          `post_id` int(10) unsigned NOT NULL,
          `tag_id` int(10) unsigned NOT NULL,
          KEY `tag_id` (`tag_id`),
          KEY `post_id` (`post_id`)
        ) DEFAULT CHARSET=utf8;")->execute();

        //Table structure for table `nos_comment`
        \DB::query("CREATE TABLE IF NOT EXISTS `nos_comment` (
          `comm_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `comm_from_table` varchar(255) NOT NULL,
          `comm_foreign_id` int(10) unsigned NOT NULL,
          `comm_email` varchar(255) NOT NULL,
          `comm_author` varchar(255) NOT NULL,
          `comm_content` text NOT NULL,
          `comm_created_at` datetime NOT NULL,
          `comm_ip` varchar(15) NOT NULL,
          `comm_state` enum('published','pending','refused') NOT NULL,
          PRIMARY KEY (`comm_id`),
          KEY `comm_created_at` (`comm_created_at`),
          KEY `comm_parent_id` (`comm_foreign_id`),
          KEY `comm_from_table` (`comm_from_table`,`comm_foreign_id`)
        ) DEFAULT CHARSET=utf8;")->execute();

        //Table structure for table `nos_content_nuggets`
        \DB::query("CREATE TABLE IF NOT EXISTS `nos_content_nuggets` (
          `content_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `content_catcher` varchar(25) DEFAULT NULL,
          `content_model_name` varchar(100) DEFAULT NULL,
          `content_model_id` int(10) unsigned DEFAULT NULL,
          `content_data` text,
          PRIMARY KEY (`content_id`),
          UNIQUE KEY `content_catcher` (`content_catcher`,`content_model_name`,`content_model_id`)
        )  DEFAULT CHARSET=utf8;")->execute();

        //Table structure for table `nos_media`
        \DB::query("CREATE TABLE IF NOT EXISTS `nos_media` (
          `media_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `media_folder_id` int(10) unsigned NOT NULL,
          `media_path` varchar(255) NOT NULL,
          `media_file` varchar(100) NOT NULL,
          `media_ext` varchar(4) NOT NULL,
          `media_title` varchar(50) NOT NULL,
          `media_application` varchar(30) DEFAULT NULL,
          `media_protected` tinyint(1) NOT NULL DEFAULT '0',
          `media_width` smallint(5) unsigned DEFAULT NULL,
          `media_height` smallint(5) unsigned DEFAULT NULL,
          `media_created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
          `media_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`media_id`),
          KEY `media_folder_id` (`media_folder_id`)
        )  DEFAULT CHARSET=utf8;")->execute();

        //Table structure for table `nos_media_folder`
        \DB::query("CREATE TABLE IF NOT EXISTS `nos_media_folder` (
          `medif_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `medif_parent_id` int(10) unsigned DEFAULT NULL,
          `medif_path` varchar(255) NOT NULL,
          `medif_dir_name` varchar(50) DEFAULT NULL,
          `medif_title` varchar(50) NOT NULL,
          `medif_created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
          `medif_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`medif_id`),
          KEY `medip_parent_id` (`medif_parent_id`)
        )  DEFAULT CHARSET=utf8;")->execute();

        //Dumping data for table `nos_media_folder`
        \DB::query("INSERT INTO `nos_media_folder` (`medif_id`, `medif_parent_id`, `medif_path`, `medif_dir_name`, `medif_title`, `medif_created_at`, `medif_updated_at`) VALUES
          (1, NULL, '/', NULL, 'Media centre', '0000-00-00 00:00:00', '0000-00-00 00:00:00');")->execute();

        //Table structure for table `nos_media_link`
        \DB::query("CREATE TABLE IF NOT EXISTS `nos_media_link` (
          `medil_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `medil_from_table` varchar(255) NOT NULL,
          `medil_foreign_id` int(10) unsigned NOT NULL,
          `medil_key` varchar(30) NOT NULL,
          `medil_media_id` int(10) unsigned NOT NULL,
          PRIMARY KEY (`medil_id`)
        )  DEFAULT CHARSET=utf8;")->execute();

        //Table structure for table `nos_news_category`
        \DB::query("CREATE TABLE IF NOT EXISTS `nos_news_category` (
          `cat_id` int(11) NOT NULL AUTO_INCREMENT,
          `cat_title` varchar(255) NOT NULL,
          `cat_virtual_name` varchar(255) NOT NULL,
          `cat_lang` varchar(5) NOT NULL,
          `cat_lang_common_id` int(11) NOT NULL,
          `cat_lang_is_main` tinyint(1) NOT NULL DEFAULT '0',
          `cat_parent_id` int(11) DEFAULT NULL,
          `cat_sort` float DEFAULT NULL,
          `cat_created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
          `cat_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`cat_id`),
          KEY `cat_lang` (`cat_lang`),
          KEY `cat_lang_common_id` (`cat_lang_common_id`, `cat_lang_is_main`),
          KEY `cat_lang_is_main` (`cat_lang_is_main`),
          KEY `cat_virtual_name` (`cat_virtual_name`),
          KEY `cat_parent_id` (`cat_parent_id`)
        )  DEFAULT CHARSET=utf8;")->execute();

        //Table structure for table `nos_news_category_post`
        \DB::query("CREATE TABLE IF NOT EXISTS `nos_news_category_post` (
          `post_id` int(11) NOT NULL,
          `cat_id` int(11) NOT NULL
        ) DEFAULT CHARSET=utf8;")->execute();

        //Table structure for table `nos_news_post`
        \DB::query("CREATE TABLE IF NOT EXISTS `nos_news_post` (
          `post_id` int(8) unsigned NOT NULL AUTO_INCREMENT,
          `post_title` varchar(250) NOT NULL,
          `post_summary` text NOT NULL,
          `post_author_alias` varchar(255) DEFAULT NULL,
          `post_author_id` int(10) unsigned DEFAULT NULL,
          `post_created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
          `post_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
          `post_lang` varchar(5) NOT NULL,
          `post_lang_common_id` int(11) NOT NULL,
          `post_lang_is_main` tinyint(1) NOT NULL DEFAULT '0',
          `post_published` tinyint(1) NOT NULL,
          `post_read` int(10) unsigned NOT NULL,
          `post_virtual_name` varchar(255) NOT NULL,
          PRIMARY KEY (`post_id`),
          KEY `post_lang` (`post_lang`),
          KEY `post_lang_common_id` (`post_lang_common_id`, `post_lang_is_main`),
          KEY `post_lang_is_main` (`post_lang_is_main`),
          KEY `post_virtual_name` (`post_virtual_name`),
          KEY `post_author_id` (`post_author_id`)
        )  DEFAULT CHARSET=utf8;")->execute();

        //Table structure for table `nos_news_tag`
        \DB::query("CREATE TABLE IF NOT EXISTS `nos_news_tag` (
          `tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `tag_label` varchar(255) NOT NULL,
          PRIMARY KEY (`tag_id`),
          UNIQUE KEY `tag_label` (`tag_label`)
        ) DEFAULT CHARSET=utf8;")->execute();

        //Table structure for table `nos_news_tag_post`
        \DB::query("CREATE TABLE IF NOT EXISTS `nos_news_tag_post` (
          `post_id` int(10) unsigned NOT NULL,
          `tag_id` int(10) unsigned NOT NULL,
          KEY `tag_id` (`tag_id`),
          KEY `post_id` (`post_id`)
        ) DEFAULT CHARSET=utf8;")->execute();

        //Table structure for table `nos_page`
        \DB::query("CREATE TABLE IF NOT EXISTS `nos_page` (
          `page_id` int(11) NOT NULL AUTO_INCREMENT,
          `page_parent_id` int(11) DEFAULT NULL,
          `page_template` varchar(255) DEFAULT NULL,
          `page_level` tinyint(4) NOT NULL DEFAULT '0',
          `page_title` varchar(255) NOT NULL DEFAULT '',
          `page_lang` varchar(5) NOT NULL,
          `page_lang_common_id` int(11) NOT NULL,
          `page_lang_is_main` tinyint(1) NOT NULL DEFAULT '0',
          `page_menu_title` varchar(255) DEFAULT NULL,
          `page_meta_title` varchar(255) DEFAULT NULL,
          `page_search_words` text,
          `page_raw_html` tinyint(4) DEFAULT '0',
          `page_sort` float DEFAULT NULL,
          `page_menu` tinyint(4) NOT NULL DEFAULT '0',
          `page_type` tinyint(4) NOT NULL DEFAULT '0',
          `page_published` tinyint(4) NOT NULL DEFAULT '0',
          `page_publication_start` datetime DEFAULT NULL,
          `page_meta_noindex` tinyint(1) unsigned NOT NULL DEFAULT '0',
          `page_requested_by_user_id` int(11) DEFAULT NULL,
          `page_lock` tinyint(4) NOT NULL DEFAULT '0',
          `page_entrance` tinyint(4) NOT NULL DEFAULT '0',
          `page_home` tinyint(4) NOT NULL DEFAULT '0',
          `page_cache_duration` int(11) DEFAULT NULL,
          `page_virtual_name` varchar(50) DEFAULT NULL,
          `page_virtual_url` varchar(255) DEFAULT NULL,
          `page_external_link` varchar(255) DEFAULT NULL,
          `page_external_link_type` tinyint(4) DEFAULT NULL,
          `page_created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
          `page_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
          `page_meta_description` text,
          `page_meta_keywords` text,
          `page_head_additional` text,
          PRIMARY KEY (`page_id`),
          UNIQUE KEY `page_virtual_url` (`page_virtual_url`, `page_lang`),
          KEY `page_parent_id` (`page_parent_id`),
          KEY `page_lang` (`page_lang`),
          KEY `page_lang_common_id` (`page_lang_common_id`, `page_lang_is_main`),
          KEY `page_lang_is_main` (`page_lang_is_main`),
          KEY `page_requested_by_user_id` (`page_requested_by_user_id`)
        )  DEFAULT CHARSET=utf8;")->execute();

        //Table structure for table `nos_role`
        \DB::query("CREATE TABLE IF NOT EXISTS `nos_role` (
          `role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `role_name` varchar(50) NOT NULL,
          `role_user_id` int(10) unsigned NOT NULL,
          PRIMARY KEY (`role_id`)
        )  DEFAULT CHARSET=utf8;")->execute();

        //Table structure for table `nos_role_permission`
        \DB::query("CREATE TABLE IF NOT EXISTS `nos_role_permission` (
          `perm_role_id` int(10) unsigned NOT NULL,
          `perm_application` varchar(30) NOT NULL,
          `perm_identifier` varchar(30) NOT NULL,
          `perm_key` varchar(30) NOT NULL,
          UNIQUE KEY `perm_group_id` (`perm_role_id`,`perm_application`,`perm_key`)
        ) DEFAULT CHARSET=utf8;")->execute();

        //Table structure for table `nos_user`
        \DB::query("CREATE TABLE IF NOT EXISTS `nos_user` (
          `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `user_md5` varchar(32) DEFAULT NULL,
          `user_name` varchar(100) NOT NULL,
          `user_firstname` varchar(100) DEFAULT NULL,
          `user_email` varchar(100) NOT NULL,
          `user_password` varchar(64) NOT NULL,
          `user_last_connection` datetime DEFAULT NULL,
          `user_configuration` text,
          `user_created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
          `user_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`user_id`),
          UNIQUE KEY `user_email` (`user_email`),
          KEY `user_md5` (`user_md5`)
        )  DEFAULT CHARSET=utf8;")->execute();

        //Table structure for table `nos_user_role`
        \DB::query("CREATE TABLE IF NOT EXISTS `nos_user_role` (
          `user_id` int(11) NOT NULL,
          `role_id` int(11) NOT NULL
        ) DEFAULT CHARSET=utf8;")->execute();

        //Table structure for table `nos_wysiwyg`
        \DB::query("CREATE TABLE IF NOT EXISTS `nos_wysiwyg` (
          `wysiwyg_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `wysiwyg_text` text NOT NULL,
          `wysiwyg_join_table` varchar(255) NOT NULL,
          `wysiwyg_key` varchar(30) NOT NULL,
          `wysiwyg_foreign_id` int(10) unsigned NOT NULL,
          PRIMARY KEY (`wysiwyg_id`)
        )  DEFAULT CHARSET=utf8;")->execute();
    }

    public function down()
    {

    }
}

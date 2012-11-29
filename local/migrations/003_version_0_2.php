<?php

namespace Fuel\Migrations;

class Version_0_2
{
    public function up()
    {
        // Rename lang, lang_common_id, lan_is_main columns. Replace lang by context. Resize lang columns.
        // Update context's columns with site::locale
        $alters = <<<SQL
ALTER TABLE `nos_blog_category` CHANGE `cat_lang` `cat_context` VARCHAR( 25 ) NOT NULL, CHANGE `cat_lang_common_id` `cat_context_common_id` INT( 11 ) NOT NULL, CHANGE `cat_lang_is_main` `cat_context_is_main` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `nos_blog_post` CHANGE `post_lang` `post_context` VARCHAR( 25 ) NOT NULL, CHANGE `post_lang_common_id` `post_context_common_id` INT( 11 ) NOT NULL, CHANGE `post_lang_is_main` `post_context_is_main` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `nos_news_category` CHANGE `cat_lang` `cat_context` VARCHAR( 25 ) NOT NULL, CHANGE `cat_lang_common_id` `cat_context_common_id` INT( 11 ) NOT NULL, CHANGE `cat_lang_is_main` `cat_context_is_main` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `nos_news_post` CHANGE `post_lang` `post_context` VARCHAR( 25 ) NOT NULL, CHANGE `post_lang_common_id` `post_context_common_id` INT( 11 ) NOT NULL, CHANGE `post_lang_is_main` `post_context_is_main` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `nos_page` CHANGE `page_lang` `page_context` VARCHAR( 25 ) NOT NULL, CHANGE `page_lang_common_id` `page_context_common_id` INT( 11 ) NOT NULL, CHANGE `page_lang_is_main` `page_context_is_main` TINYINT( 1 ) NOT NULL DEFAULT '0';

UPDATE `nos_blog_category` SET `cat_context` = CONCAT('main::', `cat_context`);
UPDATE `nos_blog_post` SET `post_context` = CONCAT('main::', `post_context`);
UPDATE `nos_news_category` SET `cat_context` = CONCAT('main::', `cat_context`);
UPDATE `nos_news_post` SET `post_context` = CONCAT('main::', `post_context`);
UPDATE `nos_page` SET `page_context` = CONCAT('main::', `page_context`);

ALTER TABLE `nos_user_role` ADD PRIMARY KEY ( `user_id` , `role_id` );

ALTER TABLE `nos_user` ADD `user_expert` tinyint(1) NOT NULL DEFAULT '0' AFTER `user_configuration`;

SQL;
        foreach (explode(PHP_EOL, $alters) as $alter) {
            if (!empty($alter)) {
                \DB::query($alter)->execute();
            }
        }

        // Clear pages cache, now cache use domain
        if (file_exists(\Config::get('cache_dir').'pages')) {
            \File::delete_dir(\Config::get('cache_dir').'pages', true, false);
        }


        // Update url_enhanced config file, integrate contexts
        \Config::load(APPPATH.'data'.DS.'config'.DS.'url_enhanced.php', 'data::url_enhanced');

        $url_enhanced_old = \Config::get("data::url_enhanced", array());
        $url_enhanced_new = array();
        foreach ($url_enhanced_old as $page_id) {
            $page = \Nos\Model_Page::find($page_id);
            if (!empty($page)) {
                $url_enhanced_new[$page_id] = array(
                    'url' => $page->page_entrance ? '' : $page->virtual_path(true),
                    'context' => $page->page_context,
                );
            }
        }
        \Config::save(APPPATH.'data'.DS.'config'.DS.'url_enhanced.php', $url_enhanced_new);
    }

    public function down()
    {

    }
}

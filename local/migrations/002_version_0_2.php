<?php

namespace Fuel\Migrations;

class Version_0_2
{
    public function up()
    {
        // Rename lang, lang_common_id, lan_is_main columns. Replace lang by site. Resize lang columns.
        $alters = <<<SQL
ALTER TABLE `nos_blog_category` CHANGE `cat_lang` `cat_site` VARCHAR( 25 ) NOT NULL, CHANGE `cat_lang_common_id` `cat_site_common_id` INT( 11 ) NOT NULL, CHANGE `cat_lang_is_main` `cat_site_is_main` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `nos_blog_post` CHANGE `post_lang` `post_site` VARCHAR( 25 ) NOT NULL, CHANGE `post_lang_common_id` `post_site_common_id` INT( 11 ) NOT NULL, CHANGE `post_lang_is_main` `post_site_is_main` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `nos_news_category` CHANGE `cat_lang` `cat_site` VARCHAR( 25 ) NOT NULL, CHANGE `cat_lang_common_id` `cat_site_common_id` INT( 11 ) NOT NULL, CHANGE `cat_lang_is_main` `cat_site_is_main` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `nos_news_post` CHANGE `post_lang` `post_site` VARCHAR( 25 ) NOT NULL, CHANGE `post_lang_common_id` `post_site_common_id` INT( 11 ) NOT NULL, CHANGE `post_lang_is_main` `post_site_is_main` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `nos_page` CHANGE `page_lang` `page_site` VARCHAR( 25 ) NOT NULL, CHANGE `page_lang_common_id` `page_site_common_id` INT( 11 ) NOT NULL, CHANGE `page_lang_is_main` `page_site_is_main` TINYINT( 1 ) NOT NULL DEFAULT '0';
SQL;
        foreach (explode(PHP_EOL, $alters) as $alter) {
            \DB::query($alter)->execute();
        }
    }

    public function down()
    {

    }
}

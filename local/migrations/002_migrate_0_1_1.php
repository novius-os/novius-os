<?php

namespace Fuel\Migrations;

class Migrate_0_1_1
{
    public function up()
    {
        $queries = '
        ALTER TABLE `nos_news_category_post` ADD PRIMARY KEY ( `post_id` , `cat_id` );

        ALTER TABLE `nos_blog_tag_post` DROP INDEX `post_id`;
        ALTER TABLE `nos_blog_tag_post` ADD PRIMARY KEY ( `post_id` , `tag_id` );

        ALTER TABLE `nos_news_tag_post` DROP INDEX `post_id`;
        ALTER TABLE `nos_news_tag_post` ADD PRIMARY KEY ( `post_id` , `tag_id` );

        ALTER TABLE `nos_page`
            DROP `page_search_words`,
            DROP `page_raw_html`,
            DROP `page_publication_start`,
            DROP `page_requested_by_user_id`,
            DROP `page_head_additional`;
        ';

        foreach (explode(';', $queries) as $query) {
            \DB::query($query)->execute();
        }
    }

    public function down()
    {

    }
}
